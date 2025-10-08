<?php

namespace App\Http\Controllers;

use App\Models\Prekey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrekeyController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }
        $prekeys = Prekey::where('user_id', Auth::id())
            ->orderByDesc('id')->get();
        return view('prekeys.index', compact('prekeys'));
    }

    public function store(Request $request)
    {
        // Require authentication for uploading prekeys
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }
        // Accept JSON string from hidden field and decode
        $raw = $request->input('bundle');
        // Guard: raw JSON size (up to 2 MiB)
        if (is_string($raw)) {
            $rawLen = strlen($raw);
            if ($rawLen > 2 * 1024 * 1024) {
                return back()->withErrors(['bundle' => 'Bundle JSON too large']);
            }
        }
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
        } else {
            $decoded = $raw; // in case it's already an array
        }
        if (!is_array($decoded)) {
            return back()->withErrors(['bundle' => 'Invalid JSON bundle']);
        }

        // Guard: limit number of entries (max 1000)
        if (count($decoded) === 0) {
            return back()->withErrors(['bundle' => 'Empty bundle']);
        }
        if (count($decoded) > 1000) {
            return back()->withErrors(['bundle' => 'Too many keys in bundle']);
        }

        // Basic shape validation
        foreach ($decoded as $idx => $item) {
            if (!is_array($item)
                || empty($item['kid']) || !is_string($item['kid']) || strlen($item['kid']) > 64
                || empty($item['alg']) || !is_string($item['alg']) || strlen($item['alg']) > 32
                || !isset($item['public_key']) || !is_string($item['public_key'])) {
                return back()->withErrors(['bundle' => "Invalid entry at index {$idx}"]);
            }
            // Allowed algorithms only
            $allowedAlgs = ['ML-KEM-512','ML-KEM-768','ML-KEM-1024'];
            if (!in_array($item['alg'], $allowedAlgs, true)) {
                return back()->withErrors(['bundle' => "Unsupported algorithm for {$item['kid']}"]);
            }
            // Strict base64 check
            $bin = base64_decode($item['public_key'], true);
            if ($bin === false) {
                return back()->withErrors(['bundle' => "Invalid base64 for entry {$item['kid']}"]);
            }
            // Reasonable size guard (1..8 KB)
            $len = strlen($bin);
            if ($len < 16 || $len > 8192) {
                return back()->withErrors(['bundle' => "Public key size out of range for {$item['kid']}"]);
            }
        }

        // Upsert all
        foreach ($decoded as $item) {
            Prekey::updateOrCreate(
                ['user_id' => Auth::id(), 'kid' => $item['kid']],
                [
                    'alg' => $item['alg'],
                    'public_key' => base64_decode($item['public_key'], true),
                    'used_at' => null,
                ]
            );
        }

        return back()->with('status', 'Prekey bundle uploaded');
    }

    public function fetch($user)
    {
        // Allow lookup by numeric ID or username (case-insensitive)
        $userModel = null;
        if (is_numeric($user)) {
            $userModel = User::find($user);
        } else {
            $userModel = User::whereRaw('LOWER(username) = ?', [strtolower($user)])->first();
        }
        if (!$userModel) {
            return response()->json(['error' => 'user_not_found'], 404);
        }
        $prekey = Prekey::where('user_id', $userModel->id)->whereNull('used_at')->orderBy('id')->first();
        if (!$prekey) {
            return response()->json(['error' => 'no_prekey'], 404);
        }
        return response()->json([
            'kid' => $prekey->kid,
            'alg' => $prekey->alg,
            'public_key' => base64_encode($prekey->public_key),
        ]);
    }

    public function markUsed(Request $request)
    {
        $request->validate([
            'kid' => 'required|string',
        ]);
        if (!Auth::check()) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }
        Prekey::where('user_id', Auth::id())
            ->where('kid', $request->kid)
            ->update(['used_at' => now()]);
        return response()->noContent();
    }
    
    public function all(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }
        $filters = [
            'status' => $request->query('status', 'all'),
            'alg' => $request->query('alg', 'all'),
            'q' => trim((string)$request->query('q', '')),
        ];
        $query = Prekey::where('user_id', Auth::id());
        if ($filters['status'] === 'available') {
            $query->whereNull('used_at');
        } elseif ($filters['status'] === 'used') {
            $query->whereNotNull('used_at');
        }
        if (in_array($filters['alg'], ['ML-KEM-512','ML-KEM-768','ML-KEM-1024'], true)) {
            $query->where('alg', $filters['alg']);
        }
        if ($filters['q'] !== '') {
            $q = str_replace(['%','_'], ['\\%','\\_'], $filters['q']);
            $query->where('kid', 'like', "%$q%");
        }
        $prekeys = $query->orderByDesc('id')->paginate(25)->withQueryString();
        return view('prekeys.all', compact('prekeys', 'filters'));
    }

    public function bulk(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login.show');
        }
        $data = $request->validate([
            'action' => 'required|string|in:delete,mark_used,mark_available',
            'kids' => 'required|array',
            'kids.*' => 'string|max:64',
        ]);
        $kids = array_values(array_unique(array_filter($data['kids'], fn($v) => is_string($v) && $v !== '')));
        if (empty($kids)) {
            return back()->withErrors(['kids' => 'No keys selected']);
        }
        if (count($kids) > 1000) {
            return back()->withErrors(['kids' => 'Too many keys selected']);
        }
        $affected = 0;
        $builder = Prekey::where('user_id', Auth::id())->whereIn('kid', $kids);
        switch ($data['action']) {
            case 'delete':
                $affected = (clone $builder)->delete();
                break;
            case 'mark_used':
                $affected = (clone $builder)->update(['used_at' => now()]);
                break;
            case 'mark_available':
                $affected = (clone $builder)->update(['used_at' => null]);
                break;
        }
        return back()->with('status', $affected . ' key' . ($affected === 1 ? '' : 's') . ' updated');
    }
}


