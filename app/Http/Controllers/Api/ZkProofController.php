<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ZkProofController extends Controller
{
    /**
     * POST /api/zk/encryption/submit
     * Accepts { encrypted, commitments, zk } and verifies minimal commitments.
     * Optionally verifies zk proof (pluggable). Stores ciphertext + metadata.
     */
    public function verifyAndStore(Request $request)
    {
        $data = $request->json()->all();
        if (!$data) {
            return response()->json(['error' => 'Invalid JSON'], 400);
        }

        $encrypted = $data['encrypted'] ?? null;
        $commitments = $data['commitments'] ?? null;
        $zk = $data['zk'] ?? null;
        $requireProof = (bool) env('ZK_REQUIRE_PROOF', false);
        $ref = $data['ref'] ?? null; // { type: 'file'|'paste', identifier?: string, id?: number }

        if (!$encrypted || !$commitments) {
            return response()->json(['error' => 'Missing required fields: encrypted, commitments'], 400);
        }

        // Extract fields
        $algorithm = $encrypted['algorithm'] ?? 'AES-GCM';
        $ciphertextIn = $encrypted['ciphertext'] ?? null; // may be null when only commitments provided
        $ivIn = $encrypted['iv'] ?? null;
        $timestamp = $encrypted['timestamp'] ?? null;

        if (!$ivIn) {
            return response()->json(['error' => 'Missing iv'], 400);
        }

        // Decode helpers
        $decodeBytes = function ($val) {
            if (is_string($val)) {
                // Detect hex vs base64
                if (preg_match('/^[0-9a-fA-F]+$/', $val) && (strlen($val) % 2 === 0)) {
                    return hex2bin($val);
                }
                $bin = base64_decode($val, true);
                if ($bin !== false) return $bin;
                // Fallback: treat as UTF-8
                return $val;
            } elseif (is_array($val)) {
                return pack('C*', ...array_map(function ($x) { return (int)$x; }, $val));
            }
            return $val; // assume binary string
        };

        $ciphertext = $ciphertextIn !== null ? $decodeBytes($ciphertextIn) : null;
        $iv = $decodeBytes($ivIn);

        if ($ciphertextIn !== null && (!is_string($ciphertext) || strlen($ciphertext) === 0)) {
            return response()->json(['error' => 'Invalid ciphertext format'], 400);
        }
        if (!is_string($iv) || (strlen($iv) !== 12 && strlen($iv) !== 16)) { // 12 for GCM, 16 for CBC/CTR
            return response()->json(['error' => 'Invalid IV length'], 400);
        }

        // Recompute or accept provided ciphertext hash and compare to commitment
        $commitCt = $commitments['ciphertextHash'] ?? null;
        if (!$commitCt) {
            return response()->json(['error' => 'Missing commitments.ciphertextHash'], 400);
        }
        $commitCtBin = $decodeBytes($commitCt);
        if (!is_string($commitCtBin) || strlen($commitCtBin) !== 32) {
            return response()->json(['error' => 'Invalid commitments.ciphertextHash'], 400);
        }
        $ctHash = null;
        if ($ciphertext !== null) {
            $ctHash = hash('sha256', $ciphertext, true); // binary
            if (!hash_equals($ctHash, $commitCtBin)) {
                return response()->json(['error' => 'Ciphertext commitment mismatch'], 400);
            }
        }

        // Optionally verify ZK proof (pluggable)
        $proofVerified = null;
        if ($zk && isset($zk['proof']) && isset($zk['publicSignals'])) {
            try {
                $proofVerified = $this->verifyProofPluggable($zk);
            } catch (\Throwable $e) {
                $proofVerified = false;
            }
        }
        
        // Also check if we have just a commitment (minimal proof)
        if (!$proofVerified && $zk && isset($zk['commit']) && !isset($zk['proof'])) {
            // This is a commitment-only proof (simplified display format)
            // Extract the actual proof from commitments if available
            if (isset($commitments['commit'])) {
                $zk = [
                    'commit' => $commitments['commit'],
                    'present' => true,
                    'verified' => null  // Cannot verify without full proof
                ];
            }
        }

        // If proof is required, enforce presence and validity
        if ($requireProof) {
            if (!$zk || !isset($zk['proof'], $zk['publicSignals'])) {
                return response()->json(['error' => 'Zero-knowledge proof required'], 400);
            }
            if ($proofVerified !== true) {
                return response()->json(['error' => 'Zero-knowledge proof invalid or unverifiable'], 400);
            }
        }

        // Persist minimal data (ciphertext and metadata) without plaintext
        $id = bin2hex(random_bytes(16));
        $dir = 'zk/'.$id;
        if ($ciphertext !== null) {
            Storage::disk('local')->put($dir.'/ciphertext.bin', $ciphertext);
        }

        // Sanity caps (very generous to avoid breaking legitimate clients)
        $maxCtHashLen = 64; // hex
        $maxEncCtLen = 10 * 1024 * 1024; // 10MB base64
        if (isset($zk['enc']['ct']) && is_string($zk['enc']['ct']) && strlen($zk['enc']['ct']) > $maxEncCtLen) {
            return response()->json(['error' => 'Encrypted proof too large'], 413);
        }

        $meta = [
            'id' => $id,
            'algorithm' => $algorithm,
            'iv' => base64_encode($iv),
            'timestamp' => $timestamp,
            'commitments' => [
                'ciphertextHash' => is_string($commitCt) ? $commitCt : bin2hex($commitCtBin),
                'additionalDataHash' => isset($commitments['additionalDataHash']) ? (is_string($commitments['additionalDataHash']) ? $commitments['additionalDataHash'] : bin2hex($decodeBytes($commitments['additionalDataHash']))) : null,
                'commit' => $commitments['commit'] ?? null,
            ],
            'zk' => [
                'present' => $zk ? true : false,
                // Explicitly set verified to null if not determined, false if failed, true if passed
                'verified' => $proofVerified === true ? true : ($proofVerified === false ? false : null),
                // Avoid storing raw proof/publicSignals when encrypted envelope is provided
                'proof' => isset($zk['enc']) ? null : ($zk['proof'] ?? null),
                'publicSignals' => isset($zk['enc']) ? null : ($zk['publicSignals'] ?? null),
                // Optional encrypted proof envelope { alg, iv, salt, ct }
                'enc' => $zk['enc'] ?? null,
                // Store the commit from zk if available
                'commit' => isset($zk['commit']) ? $zk['commit'] : null,
            ],
            'ref' => is_array($ref) ? $ref : null,
            'receivedAt' => time(),
        ];
        Storage::disk('local')->put($dir.'/metadata.json', json_encode($meta, JSON_PRETTY_PRINT));

        // Maintain a simple reference index for lookup by resource
        if (is_array($ref) && isset($ref['type'], $ref['identifier'])) {
            $refPath = 'zk/_refs/'.preg_replace('/[^a-z]/i','',strtolower($ref['type'])).'/'.preg_replace('/[^A-Za-z0-9_-]/','',$ref['identifier']).'.json';
            $index = [ 'latest' => $id, 'ids' => [$id], 'updatedAt' => time() ];
            if (Storage::disk('local')->exists($refPath)) {
                $prev = json_decode(Storage::disk('local')->get($refPath), true) ?: [];
                $ids = isset($prev['ids']) && is_array($prev['ids']) ? $prev['ids'] : [];
                $ids[] = $id;
                $index['ids'] = array_values(array_unique($ids));
            }
            Storage::disk('local')->put($refPath, json_encode($index, JSON_PRETTY_PRINT));
        }

        return response()->json([
            'ok' => true,
            'id' => $id,
            'zkVerified' => $proofVerified,
        ], 201);
    }

    /**
     * Pluggable proof verification.
     * Default: returns null (no verification). You can integrate a Node service here.
     */
    protected function verifyProofPluggable(array $zk)
    {
        // Integrate external verification service here if desired.
        // Example (pseudo-code):
        // $resp = Http::post(env('ZK_VERIFIER_URL'), $zk);
        // return $resp->ok() ? (bool)$resp->json('valid') : false;
        return null; // not verified by default
    }

    /**
     * GET /api/zk/encryption/by-ref?type=file|paste&identifier=...
     * Returns latest proof metadata for a resource reference.
     */
    public function byRef(Request $request)
    {
        $type = strtolower((string)$request->query('type'));
        $identifier = (string)$request->query('identifier');
        if (!in_array($type, ['file','paste'], true) || $identifier === '') {
            return response()->json(['error' => 'Invalid ref'], 400);
        }
        $refPath = 'zk/_refs/'.$type.'/'.preg_replace('/[^A-Za-z0-9_-]/','',$identifier).'.json';
        if (!Storage::disk('local')->exists($refPath)) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $index = json_decode(Storage::disk('local')->get($refPath), true) ?: [];
        $latest = $index['latest'] ?? null;
        if (!$latest) return response()->json(['error' => 'No entries'], 404);
        $metaPath = 'zk/'.$latest.'/metadata.json';
        if (!Storage::disk('local')->exists($metaPath)) {
            return response()->json(['error' => 'Missing metadata'], 404);
        }
        $meta = json_decode(Storage::disk('local')->get($metaPath), true) ?: [];
        return response()->json(['ok' => true, 'meta' => $meta, 'index' => $index]);
    }

    /**
     * GET /api/zk/encryption/{id}/metadata
     */
    public function metadata(Request $request, string $id)
    {
        $metaPath = 'zk/'.preg_replace('/[^A-Za-z0-9]/','',$id).'/metadata.json';
        if (!Storage::disk('local')->exists($metaPath)) {
            return response()->json(['error' => 'Not found'], 404);
        }
        $meta = json_decode(Storage::disk('local')->get($metaPath), true) ?: [];
        return response()->json(['ok' => true, 'meta' => $meta]);
    }
}
