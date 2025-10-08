<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\File as FileModel;
use App\Models\Paste;
use App\Models\Prekey;

class FilesController extends Controller
{
    // ---------- Standalone files: Pages ----------
    public function create()
    {
        // Accessible to guests as well
        return view('files.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'original_filename' => 'required|string|max:255',
            'mime_type' => 'nullable|string|max:127',
            'iv' => 'required|string',
            'cipher_file' => 'required|file|max:163840',
            'expires_in' => 'nullable|in:1hour,1day,1week,1month',
            'is_private' => 'nullable|boolean',
            'view_limit' => 'nullable|integer|min:1|max:1000000',
            'password' => 'nullable|string|max:128',
            'password_hint' => 'nullable|string|max:128',
            // Optional addressed recipient + KEM metadata
            'recipient_username' => 'nullable|string|max:64',
            'kem_alg' => 'nullable|string|max:32',
            'kem_kid' => 'nullable|string|max:64',
            'kem_ct' => 'nullable|string', // base64
            'kem_wrapped_key' => 'nullable|string', // base64 ([salt][iv][ct])
        ]);

        $expiresAt = null;
        switch ($request->expires_in) {
            case '1hour': $expiresAt = Carbon::now()->addHour(); break;
            case '1day': $expiresAt = Carbon::now()->addDay(); break;
            case '1week': $expiresAt = Carbon::now()->addWeek(); break;
            case '1month': $expiresAt = Carbon::now()->addMonth(); break;
        }

        // Validate IV JSON: must be an array of 12 integers 0..255
        $ivNorm = null;
        try {
            $ivArr = json_decode((string) $request->iv, true, 512, JSON_THROW_ON_ERROR);
            if (!\is_array($ivArr) || \count($ivArr) !== 12) {
                return response()->json(['error' => 'Invalid IV'], 422);
            }
            foreach ($ivArr as $v) {
                if (!\is_int($v) && !ctype_digit((string)$v)) return response()->json(['error' => 'Invalid IV bytes'], 422);
                $i = (int)$v; if ($i < 0 || $i > 255) return response()->json(['error' => 'Invalid IV byte range'], 422);
            }
            $ivNorm = json_encode(array_map('intval', $ivArr));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Invalid IV'], 422);
        }

        // Normalize IV JSON
        try {
            $ivArr = json_decode((string) $request->iv, true, 512, JSON_THROW_ON_ERROR);
            if (!\is_array($ivArr) || \count($ivArr) !== 12) return response()->json(['error' => 'Invalid IV'], 422);
            foreach ($ivArr as $v) { if (!\is_int($v) && !ctype_digit((string)$v)) return response()->json(['error' => 'Invalid IV bytes'], 422); $i=(int)$v; if ($i<0||$i>255) return response()->json(['error'=>'Invalid IV byte range'],422);}            
            $request->merge(['iv' => json_encode(array_map('intval', $ivArr))]);
        } catch (\Throwable $e) { return response()->json(['error' => 'Invalid IV'], 422); }

        $uploaded = $request->file('cipher_file');
        $uniqueName = bin2hex(random_bytes(16)) . '.bin';
        $storagePath = 'private/files/' . $uniqueName;

        // Ensure storage directory exists, then move the uploaded file into place
        $fullStoragePath = storage_path('app/' . $storagePath);
        $storageDir = \dirname($fullStoragePath);
        if (!\is_dir($storageDir)) { @mkdir($storageDir, 0755, true); }
        try {
            // Use storeAs on local disk to avoid memory copies
            $uploaded->storeAs('private/files', $uniqueName, 'local');
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to save file to storage'], 500);
        }

        if (!Storage::disk('local')->exists($storagePath)) {
            return response()->json(['error' => 'File save verification failed'], 500);
        }
        $savedSize = (int) Storage::disk('local')->size($storagePath);
        if ($savedSize <= 0) {
            Storage::disk('local')->delete($storagePath);
            return response()->json(['error' => 'File size verification failed'], 500);
        }


        // Resolve addressed recipient (optional)
        $recipientId = null;
        if ($request->filled('recipient_username')) {
            $recipient = \App\Models\User::where('username', $request->recipient_username)->first();
            $recipientId = $recipient?->id;
        }
        // Validate optional KEM metadata when present
        $kemAlg = $request->input('kem_alg');
        $kemKid = $request->input('kem_kid');
        $kemCtRaw = null; $kemWrappedRaw = null;
        if ($kemAlg || $kemKid || $request->filled('kem_ct') || $request->filled('kem_wrapped_key')) {
            // Require minimum fields when any is present
            if (!$kemAlg || !$kemKid || !$request->filled('kem_ct') || !$request->filled('kem_wrapped_key')) {
                return response()->json(['error' => 'Invalid KEM metadata'], 422);
            }
            if (!$recipientId) {
                return response()->json(['error' => 'Recipient required for addressed files'], 422);
            }
            $allowedAlgs = ['ML-KEM-512','ML-KEM-768','ML-KEM-1024'];
            if (!in_array($kemAlg, $allowedAlgs, true)) {
                return response()->json(['error' => 'Unsupported KEM algorithm'], 422);
            }
            $kemCtRaw = base64_decode($request->kem_ct, true);
            $kemWrappedRaw = base64_decode($request->kem_wrapped_key, true);
            if ($kemCtRaw === false || $kemWrappedRaw === false) {
                return response()->json(['error' => 'Invalid KEM data encoding'], 422);
            }
            // Reasonable size guards
            if (strlen($kemCtRaw) < 16 || strlen($kemCtRaw) > 8192) {
                return response()->json(['error' => 'KEM ciphertext size invalid'], 422);
            }
            if (strlen($kemWrappedRaw) < (32 + 12 + 16) || strlen($kemWrappedRaw) > (32 + 12 + 8192)) {
                return response()->json(['error' => 'Wrapped key size invalid'], 422);
            }
        }

        $file = FileModel::create([
            'identifier' => FileModel::generateIdentifier(),
            'user_id' => Auth::id(),
            'recipient_id' => $recipientId,
            'original_filename' => $request->original_filename,
            'mime_type' => $request->mime_type,
            'size_bytes' => $savedSize,
            'views' => 0,
            'view_limit' => $request->input('view_limit'),
            // Guests cannot create private files; force false when not authenticated
            'is_private' => Auth::check() ? (bool) $request->boolean('is_private') : false,
            'expires_at' => $expiresAt,
            'storage_path' => $storagePath,
            'iv' => $ivNorm,
            'password_hash' => $request->filled('password') ? password_hash($request->password, PASSWORD_ARGON2ID) : null,
            'password_hint' => $request->input('password_hint'),
            // Optional KEM metadata
            'kem_alg' => $kemAlg,
            'kem_kid' => $kemKid,
            'kem_ct' => $kemCtRaw,
            'kem_wrapped_key' => $kemWrappedRaw,
        ]);

        // Server-side authoritative mark-used for recipient's prekey when addressed
        try {
            if ($recipientId && $kemKid) {
                Prekey::where('user_id', $recipientId)
                    ->where('kid', $kemKid)
                    ->update(['used_at' => now()]);
            }
        } catch (\Throwable $e) { /* non-fatal */ }

        return response()->json([
            'success' => true,
            'url' => route('files.show', $file->identifier),
            'identifier' => $file->identifier,
        ]);
    }

    public function show($identifier)
    {
        $file = FileModel::where('identifier', $identifier)->first();
        if (!$file || ($file->expires_at && Carbon::now()->greaterThan($file->expires_at))) {
            return view('paste.not-found');
        }
        // Enforce owner-only for private; enforce addressed-only for recipient addressed files
        if ($file->is_private && (!Auth::check() || Auth::id() !== $file->user_id)) {
            abort(403);
        }
        if (!is_null($file->recipient_id) && (!Auth::check() || Auth::id() !== $file->recipient_id)) {
            abort(403);
        }
        // Don't increment views on page load - only on actual download
        return view('files.show', compact('file'));
    }

    public function downloadStandalone($identifier)
    {
        return $this->streamFileByIdentifier($identifier);
    }

    public function mine()
    {
        if (!Auth::check()) return redirect()->route('auth.login.show');
        $files = FileModel::where('user_id', Auth::id())->orderByDesc('id')->paginate(30);
        return view('files.mine', compact('files'));
    }

    // ---------- API: Paste attachments + shared download ----------
    public function upload(Request $request)
    {
        $request->validate([
            'paste_identifier' => 'required|string',
            'cipher_file' => 'required|file|max:163840', // 160MB in KB
            'original_filename' => 'required|string|max:255',
            'mime_type' => 'nullable|string|max:127',
            'iv' => 'required|string',
        ]);

        $paste = Paste::where('identifier', $request->paste_identifier)->firstOrFail();
        if ($paste->isExpired() || $paste->is_removed) abort(410);
        if ($paste->is_private) {
            if (!Auth::check() || Auth::id() !== $paste->user_id) abort(403);
        }

        $uploaded = $request->file('cipher_file');
        $uniqueName = bin2hex(random_bytes(16)) . '.bin';
        $storagePath = 'private/files/' . $uniqueName;

        $fullStoragePath = storage_path('app/' . $storagePath);
        $storageDir = \dirname($fullStoragePath);
        if (!\is_dir($storageDir)) { @mkdir($storageDir, 0755, true); }
        try {
            $uploaded->storeAs('private/files', $uniqueName, 'local');
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to save file to storage'], 500);
        }

        if (!Storage::disk('local')->exists($storagePath)) {
            return response()->json(['error' => 'File save verification failed'], 500);
        }
        $savedSize = (int) Storage::disk('local')->size($storagePath);
        if ($savedSize <= 0) {
            Storage::disk('local')->delete($storagePath);
            return response()->json(['error' => 'File size verification failed'], 500);
        }


        $file = FileModel::create([
            'identifier' => FileModel::generateIdentifier(),
            'paste_id' => $paste->id,
            'user_id' => Auth::id(),
            'original_filename' => $request->original_filename,
            'mime_type' => $request->mime_type,
            'size_bytes' => $uploaded->getSize(),
            'storage_path' => $storagePath,
            'iv' => $request->iv,
        ]);

        return response()->json([
            'success' => true,
            'file_id' => $file->id,
        ]);
    }

    public function apiDownload(Request $request, $identifier)
    {
        return $this->streamFileByIdentifier($identifier, $request);
    }

    // ---------- API: Chunked uploads ----------
    public function startChunked(Request $request)
    {
        $request->validate([
            'original_filename' => 'required|string|max:255',
            'mime_type' => 'nullable|string|max:127',
            'iv' => 'required|string',
            'expires_in' => 'nullable|in:1hour,1day,1week,1month',
            'is_private' => 'nullable|boolean',
            'view_limit' => 'nullable|integer|min:1|max:1000000',
        ]);
        // Normalize IV JSON
        try {
            $ivArr = json_decode((string) $request->iv, true, 512, JSON_THROW_ON_ERROR);
            if (!\is_array($ivArr) || \count($ivArr) !== 12) return response()->json(['success'=>false,'error' => 'invalid_iv'], 422);
            foreach ($ivArr as $v) { if (!\is_int($v) && !ctype_digit((string)$v)) return response()->json(['success'=>false,'error' => 'invalid_iv_bytes'], 422); $i=(int)$v; if ($i<0||$i>255) return response()->json(['success'=>false,'error'=>'invalid_iv_range'],422);}            
            $request->merge(['iv' => json_encode(array_map('intval', $ivArr))]);
        } catch (\Throwable $e) { return response()->json(['success'=>false,'error' => 'invalid_iv'], 422); }
        $uploadId = Str::uuid()->toString();
        $dir = storage_path('app/private/chunks/'.$uploadId);
        if (!\is_dir($dir)) { mkdir($dir, 0775, true); }
        Storage::disk('local')->put("private/chunks/$uploadId/meta.json", json_encode([
            'original_filename' => $request->original_filename,
            'mime_type' => $request->mime_type,
            'iv' => $request->iv,
            'expires_in' => $request->input('expires_in'),
            'is_private' => $request->boolean('is_private'),
            'view_limit' => $request->input('view_limit'),
            'user_id' => Auth::id(),
            'size' => 0,
            'created_at' => time(),
        ]));
        return response()->json(['success' => true, 'upload_id' => $uploadId]);
    }

    public function appendChunk(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|string',
            'index' => 'required|integer|min:0',
            'chunk' => 'required|file|max:1536',
        ]);
        $uploadId = $request->upload_id;
        $dir = storage_path('app/private/chunks/'.$uploadId);
        if (!\is_dir($dir)) abort(404);
        $path = "private/chunks/$uploadId/".$request->index;
        $bytes = file_get_contents($request->file('chunk')->getRealPath());
        $ok = Storage::disk('local')->put($path, $bytes);
        $exists = Storage::disk('local')->exists($path);
        $savedSize = $exists ? Storage::disk('local')->size($path) : 0;
        if (!$ok || !$exists || $savedSize <= 0) {
            return response()->json(['success' => false, 'error' => 'save_failed', 'index' => (int)$request->index], 500);
        }
        return response()->json(['success' => true, 'index' => (int)$request->index, 'size' => \strlen($bytes)]);
    }

    public function finishChunked(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|string',
            'total_chunks' => 'required|integer|min:1|max:10000',
        ]);
        $uploadId = $request->upload_id;
        $base = 'private/chunks/'.$uploadId;
        if (!Storage::disk('local')->exists("$base/meta.json")) abort(404);
        $meta = json_decode(Storage::disk('local')->get("$base/meta.json"), true);

        $uploadsDir = storage_path('app/private/files');
        if (!\is_dir($uploadsDir)) { @mkdir($uploadsDir, 0775, true); }
        $uniqueName = bin2hex(random_bytes(16)) . '.bin';
        $storagePath = 'private/files/' . $uniqueName;
        $stream = fopen(storage_path('app/'.$storagePath), 'w');
        for ($i = 0; $i < (int)$request->total_chunks; $i++) {
            $chunkPath = "$base/$i";
            if (!Storage::disk('local')->exists($chunkPath)) {
                fclose($stream);
                return response()->json(['success' => false, 'error' => 'missing_chunk', 'index' => $i], 400);
            }
            $data = Storage::disk('local')->get($chunkPath);
            fwrite($stream, $data);
        }
        fclose($stream);
        Storage::disk('local')->deleteDirectory($base);

        $file = FileModel::create([
            'identifier' => FileModel::generateIdentifier(),
            'user_id' => $meta['user_id'] ?? null,
            'original_filename' => $meta['original_filename'],
            'mime_type' => $meta['mime_type'],
            'size_bytes' => filesize(storage_path('app/'.$storagePath)),
            'views' => 0,
            'view_limit' => $meta['view_limit'] ?? null,
            'is_private' => ($meta['user_id'] ? (bool)$meta['is_private'] : false),
            'expires_at' => null,
            'storage_path' => $storagePath,
            'iv' => $meta['iv'],
        ]);

        return response()->json(['success' => true, 'url' => route('files.show', $file->identifier)]);
    }

    // ---------- Shared streaming logic ----------
    private function streamFileByIdentifier(string $identifier, Request $request = null)
    {
        
        $file = FileModel::where('identifier', $identifier)->first();
        if (!$file) { abort(404, 'File not found'); }

        // Password gate for standalone files
        if (is_null($file->paste_id) && $file->password_hash) {
            // Throttle password attempts (per file + IP)
            try {
                $ip = $request?->ip();
                $key = 'pw_check:file:' . $file->id . ':' . ($ip ?: 'unknown');
                if (RateLimiter::tooManyAttempts($key, 30)) {
                    return response()->json(['error' => 'Too many attempts'], 429);
                }
                RateLimiter::hit($key, 60); // decay after 60 seconds
            } catch (\Throwable $e) {
                // If RateLimiter fails, continue without blocking
            }

            $pwd = $request?->header('X-Download-Password') ?? $request?->query('password');
            if (!$pwd || !password_verify($pwd, $file->password_hash)) {
                return response()->json(['error' => 'password_required', 'hint' => $file->password_hint], 401);
            }
            
        }

        // Privacy checks
        if (!is_null($file->paste_id)) {
            $paste = Paste::findOrFail($file->paste_id);
            if ($paste->is_private) { if (!Auth::check() || Auth::id() !== $paste->user_id) abort(403); }
        } else {
            if ($file->is_private) { if (!Auth::check() || Auth::id() !== $file->user_id) abort(403); }
        }
        // Addressed files: only intended recipient may download
        if (!is_null($file->recipient_id)) {
            if (!Auth::check() || Auth::id() !== $file->recipient_id) abort(403);
        }

        // Expiry check
        if ($file->expires_at && now()->greaterThan($file->expires_at)) { abort(404); }

        $storagePath = $file->storage_path;
        if (!Storage::disk('local')->exists($storagePath)) {
            // Backward-compat: if record points to private/uploads, try private/files
            $altStoragePath = preg_replace('/^private\/uploads\//', 'private/files/', $storagePath);
            if ($altStoragePath !== $storagePath && Storage::disk('local')->exists($altStoragePath)) {
                $file->storage_path = $altStoragePath; $file->save();
                $storagePath = $altStoragePath;
            } else {
                return response()->json([
                    'error' => 'File not found on disk',
                    'message' => 'The file exists in our database but the actual file is missing from storage.',
                    'file_id' => $file->id,
                    'identifier' => $file->identifier
                ], 404);
            }
        }

        // Increment download count
        $file->increment('views');
        if (!is_null($file->view_limit) && $file->views >= $file->view_limit) {
            register_shutdown_function(function () use ($file) { try { $file->delete(); } catch (\Throwable $e) {} });
        }

        $stream = Storage::disk('local')->readStream($storagePath);
        // Sanitize download name to mitigate header injection / weird chars
        $baseName = (string) ($file->original_filename ?? 'file');
        $baseName = preg_replace('/[\r\n\t]+/', ' ', $baseName);
        $baseName = preg_replace('/[^A-Za-z0-9._-]+/', '_', $baseName);
        if ($baseName === '' || $baseName === '.' || $baseName === '..') { $baseName = 'file'; }
        $downloadName = $baseName . '.bin';
        $disposition = 'attachment; filename="'.str_replace('"','',$downloadName).'"; filename*=UTF-8\'\'' . rawurlencode($downloadName);
        return response()->stream(function() use ($stream) { fpassthru($stream); }, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => $disposition,
            'X-File-IV' => $file->iv,
        ]);
    }

    public function destroy($identifier)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }
        $file = FileModel::where('identifier', $identifier)->first();
        if (!$file) {
            return back()->withErrors(['file' => 'File not found']);
        }
        if ($file->user_id !== Auth::id()) {
            abort(403);
        }
        // Delete from storage if present
        if ($file->storage_path && Storage::disk('local')->exists($file->storage_path)) {
            try { Storage::disk('local')->delete($file->storage_path); } catch (\Throwable $e) {}
        }
        $file->delete();
        return back()->with('status', 'File deleted successfully');
    }
}
