<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserKeypair;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KeypairController extends Controller
{
    public function generate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'expires_in_days' => 'nullable|integer|min:1|max:365',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input', 'details' => $validator->errors()], 400);
        }

        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $expiresInDays = $request->input('expires_in_days', 30);
        $expiresAt = now()->addDays($expiresInDays);

        // Deactivate old keypairs for this user
        UserKeypair::where('user_id', Auth::id())
            ->where('is_active', true)
            ->update(['is_active' => false]);

        $keypair = UserKeypair::create([
            'user_id' => Auth::id(),
            'key_id' => UserKeypair::generateKeyId(),
            'public_key' => '', // Will be set by client
            'secret_key' => '', // Will be set by client
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);

        return response()->json([
            'key_id' => $keypair->key_id,
            'expires_at' => $keypair->expires_at->toISOString(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key_id' => 'required|string|max:64',
            'public_key' => 'required|string',
            'secret_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input', 'details' => $validator->errors()], 400);
        }

        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $keypair = UserKeypair::where('user_id', Auth::id())
            ->where('key_id', $request->key_id)
            ->where('is_active', true)
            ->first();

        if (!$keypair) {
            return response()->json(['error' => 'Keypair not found'], 404);
        }

        $keypair->update([
            'public_key' => base64_decode($request->public_key),
            'secret_key' => base64_decode($request->secret_key),
        ]);

        return response()->json(['success' => true]);
    }

    public function getPublicKey(Request $request, string $username): JsonResponse
    {
        $user = \App\Models\User::whereRaw('LOWER(username) = ?', [strtolower($username)])->first();
        
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $keypair = UserKeypair::where('user_id', $user->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$keypair) {
            return response()->json(['error' => 'No active keypair found'], 404);
        }

        return response()->json([
            'key_id' => $keypair->key_id,
            'public_key' => base64_encode($keypair->getPublicKeyBytes()),
            'expires_at' => $keypair->expires_at?->toISOString(),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $keypairs = UserKeypair::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get(['key_id', 'is_active', 'created_at', 'expires_at']);

        return response()->json(['keypairs' => $keypairs]);
    }

    public function revoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key_id' => 'required|string|max:64',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input', 'details' => $validator->errors()], 400);
        }

        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $keypair = UserKeypair::where('user_id', Auth::id())
            ->where('key_id', $request->key_id)
            ->first();

        if (!$keypair) {
            return response()->json(['error' => 'Keypair not found'], 404);
        }

        $keypair->update(['is_active' => false]);

        return response()->json(['success' => true]);
    }
}