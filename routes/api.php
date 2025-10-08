<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KeypairController;
use App\Http\Controllers\Api\ZkProofController;
use App\Http\Controllers\Api\KeyRotationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Post-Quantum KEM Keypair Management
Route::middleware(['auth'])->group(function () {
    Route::post('/keypairs/generate', [KeypairController::class, 'generate']);
    Route::put('/keypairs/update', [KeypairController::class, 'update']);
    Route::get('/keypairs', [KeypairController::class, 'list']);
    Route::delete('/keypairs/revoke', [KeypairController::class, 'revoke']);
    
    // Key Rotation Management
    Route::get('/keys/rotation-status', [KeyRotationController::class, 'checkRotationStatus']);
    Route::post('/keys/rotate', [KeyRotationController::class, 'rotateKeys']);
    Route::post('/keys/emergency-rotate', [KeyRotationController::class, 'emergencyRotation']);
    Route::get('/keys/rotation-history', [KeyRotationController::class, 'rotationHistory']);
});

// Public key lookup (no auth required)
Route::get('/keypairs/public/{username}', [KeypairController::class, 'getPublicKey']);

// ZK Encryption submission (minimal trust server)
Route::post('/zk/encryption/submit', [ZkProofController::class, 'verifyAndStore'])->middleware('throttle:30,1');
Route::get('/zk/encryption/by-ref', [ZkProofController::class, 'byRef'])->middleware('throttle:120,1');
Route::get('/zk/encryption/{id}/metadata', [ZkProofController::class, 'metadata'])->middleware('throttle:120,1');

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/rotation-stats', [KeyRotationController::class, 'getRotationStats']);
});
