<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PasteController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Middleware\AdminOnly;
use App\Http\Controllers\PrekeyController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SRPAuthController;

Route::get('/', [PasteController::class, 'index'])->name('paste.create');
// SEO
Route::get('/robots.txt', function() {
    return response()->view('seo.robots')
        ->header('Content-Type', 'text/plain');
});
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-pastes.xml', [\App\Http\Controllers\SitemapController::class, 'pastes'])->name('sitemap.pastes');
Route::get('/sitemap-files.xml', [\App\Http\Controllers\SitemapController::class, 'files'])->name('sitemap.files');
Route::get('/sitemap-blog.xml', [\App\Http\Controllers\SitemapController::class, 'blog'])->name('sitemap.blog');

// Google Search Console verification files
Route::get('/google{verification}.html', function($verification) {
    return response('google-site-verification: google' . $verification . '.html')
        ->header('Content-Type', 'text/plain');
})->where('verification', '[a-zA-Z0-9]+');

// Crawler-friendly content discovery
Route::get('/crawl', function() {
    $content = [
        'site' => config('app.name'),
        'description' => 'DailyForever - Secure Encrypted Pastebin and File Sharing',
        'url' => url('/'),
        'sitemaps' => [
            url('/sitemap.xml'),
            url('/sitemap-blog.xml'),
            url('/sitemap-pastes.xml'),
            url('/sitemap-files.xml')
        ],
        'robots' => url('/robots.txt'),
        'ads' => url('/ads.txt'),
        'last_updated' => now()->toISOString()
    ];
    
    return response()->json($content)
        ->header('Content-Type', 'application/json')
        ->header('Cache-Control', 'public, max-age=3600');
});

// Crawler test endpoint
if (app()->environment('local', 'testing')) {
    Route::get('/crawler-test', [\App\Http\Controllers\CrawlerTestController::class, 'test']);
}
// Public blog
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [PostController::class, 'show'])->name('blog.show');
Route::post('/paste', [PasteController::class, 'store'])->middleware('throttle:30,1')->name('paste.store');
Route::get('/paste/{identifier}', [PasteController::class, 'show'])->name('paste.show');
Route::get('/api/paste/{identifier}', [PasteController::class, 'raw'])->name('paste.raw');
Route::get('/my/pastes', [PasteController::class, 'mine'])->middleware('auth')->name('pastes.mine');
Route::get('/paste/{identifier}/edit', [PasteController::class, 'edit'])->name('paste.edit');
Route::put('/paste/{identifier}', [PasteController::class, 'update'])->name('paste.update');
Route::delete('/paste/{identifier}', [PasteController::class, 'destroy'])->name('paste.destroy');

// File API (used by paste attachments and standalone flows)
Route::post('/api/files/upload', [FilesController::class, 'upload'])->middleware('throttle:60,1')->name('files.upload');
Route::get('/api/files/{identifier}/download', [FilesController::class, 'apiDownload'])->name('files.download');
// Chunked uploads for big files
Route::post('/api/files/chunk/start', [FilesController::class, 'startChunked'])->middleware('throttle:60,1')->name('files.chunk.start');
Route::post('/api/files/chunk/append', [FilesController::class, 'appendChunk'])->middleware('throttle:240,1')->name('files.chunk.append');
Route::post('/api/files/chunk/finish', [FilesController::class, 'finishChunked'])->middleware('throttle:60,1')->name('files.chunk.finish');

// Dedicated files pages (standalone files)
Route::get('/files/new', [FilesController::class, 'create'])->name('files.create');
Route::post('/files', [FilesController::class, 'store'])->middleware('throttle:30,1')->name('files.store');
Route::get('/files/{identifier}', [FilesController::class, 'show'])->name('files.show');
Route::get('/files/{identifier}/download', [FilesController::class, 'downloadStandalone'])->name('files.standalone.download');
Route::get('/myfiles', [FilesController::class, 'mine'])->middleware('auth')->name('user.files');
Route::delete('/files/{identifier}', [FilesController::class, 'destroy'])->name('files.destroy');

    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support.index');
    Route::post('/support', [SupportController::class, 'submit'])->name('support.submit');
    
    // Legal pages
    Route::get('/faq', function () {
        return view('legal.faq');
    })->name('legal.faq');
    
    Route::get('/cookies', function () {
        return view('legal.cookies');
    })->name('legal.cookies');
    
    // How Encryption Works (public explainer)
    Route::get('/how-it-works', function () {
        return view('legal.how-it-works');
    })->name('legal.how-it-works');
    
    Route::get('/roadmap', function () {
        return view('legal.roadmap');
    })->name('legal.roadmap');
    // Test routes only available in non-production environments
    if (app()->environment('local', 'testing')) {
        // Test route for backup code modal
        Route::get('/test-backup-modal', function () {
            return view('test-modal')->with('backup_code', 'TEST1234567890AB');
        })->name('test.backup.modal');
        
        // Test route for backup code debugging
        Route::get('/test-backup-debug', function () {
            if (!auth()->check()) {
                return 'Please log in first';
            }
            
            $user = auth()->user();
            return response()->json([
                'username' => $user->username,
                'has_backup_code' => !is_null($user->backup_code_hash),
                'backup_code_hash' => $user->backup_code_hash,
                'test_code' => 'TEST1234567890AB',
                'hash_check' => $user->backup_code_hash ? \Illuminate\Support\Facades\Hash::check('TEST1234567890AB', $user->backup_code_hash) : false
            ]);
        })->name('test.backup.debug');
    }

// Prekeys (auth required for UI and upload)
Route::get('/prekeys', [PrekeyController::class, 'index'])
    ->middleware('auth')
    ->name('prekeys.index');
Route::post('/prekeys', [PrekeyController::class, 'store'])
    ->middleware(['auth','throttle:30,1'])
    ->name('prekeys.store');
Route::get('/prekeys/all', [PrekeyController::class, 'all'])
    ->middleware('auth')
    ->name('prekeys.all');
Route::post('/prekeys/bulk', [PrekeyController::class, 'bulk'])
    ->middleware(['auth','throttle:30,1'])
    ->name('prekeys.bulk');
// Fetch recipient prekey (public)
Route::get('/api/users/{user}/prekey', [PrekeyController::class, 'fetch'])
    ->middleware('throttle:120,1')
    ->name('prekeys.fetch');
// Mark used (auth)
Route::post('/api/prekeys/mark-used', [PrekeyController::class, 'markUsed'])
    ->middleware(['auth','throttle:60,1'])
    ->name('prekeys.markUsed');

// Legal pages
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/dmca', [LegalController::class, 'dmca'])->name('legal.dmca');
Route::get('/acceptable-use', [LegalController::class, 'acceptableUse'])->name('legal.acceptable-use');
Route::get('/no-logs', [LegalController::class, 'noLogs'])->name('legal.no-logs');
Route::get('/philosophy', [LegalController::class, 'philosophy'])->name('legal.philosophy');

// Auth (no email, username + password + PIN recovery)
Route::get('/register', [AuthController::class, 'showRegister'])->name('auth.register.show');
Route::post('/register', [AuthController::class, 'register'])->name('auth.register')->middleware('throttle:5,1');
Route::get('/login', [AuthController::class, 'showLogin'])->name('auth.login.show');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login')->middleware('throttle:10,1');
Route::get('/login/2fa', [AuthController::class, 'showTwoFactor'])->name('auth.2fa.show');
Route::post('/login/2fa', [AuthController::class, 'verifyTwoFactor'])->name('auth.2fa.verify')->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');


// SRP Authentication routes (rate-limited)
Route::prefix('api/srp')->middleware('throttle:30,1')->group(function () {
    Route::post('initiate', [SRPAuthController::class, 'initiate'])->name('auth.srp.initiate');
    Route::post('verify', [SRPAuthController::class, 'verify'])->name('auth.srp.verify');
    Route::post('register', [SRPAuthController::class, 'register'])->name('auth.srp.register');
    Route::get('support', [SRPAuthController::class, 'checkSupport'])->name('auth.srp.support');
});
Route::get('/recover', [AuthController::class, 'showPinRecovery'])->name('auth.recover.show');
Route::post('/recover/start', [AuthController::class, 'startRecovery'])->name('auth.recover.start')->middleware('throttle:5,1');
Route::post('/recover/complete', [AuthController::class, 'completeRecovery'])->name('auth.recover.complete')->middleware('throttle:5,1');
Route::post('/recover/verify-password', [AuthController::class, 'verifyPasswordAndCompleteRecovery'])->name('auth.recover.verify-password')->middleware('throttle:5,1');

// Enhanced Recovery System
Route::get('/recovery', [App\Http\Controllers\RecoveryController::class, 'showRecoveryOptions'])->name('auth.recovery.options');
Route::get('/recovery/email', [App\Http\Controllers\RecoveryController::class, 'showEmailRecovery'])->name('auth.recovery.email');
Route::post('/recovery/email', [App\Http\Controllers\RecoveryController::class, 'sendRecoveryEmail'])->name('auth.recovery.email.send');
Route::get('/recovery/reset', [App\Http\Controllers\RecoveryController::class, 'showRecoveryForm'])->name('auth.recovery.reset');
Route::post('/recovery/reset', [App\Http\Controllers\RecoveryController::class, 'resetPinWithEmail'])->name('auth.recovery.reset.submit');
Route::get('/recovery/security-questions', [App\Http\Controllers\RecoveryController::class, 'showSecurityQuestions'])->name('auth.recovery.security-questions');
Route::post('/recovery/security-questions', [App\Http\Controllers\RecoveryController::class, 'verifySecurityQuestions'])->name('auth.recovery.security-questions.submit');
Route::get('/recovery/backup-code', [App\Http\Controllers\RecoveryController::class, 'showBackupCodeRecovery'])->name('auth.recovery.backup-code');
Route::post('/recovery/backup-code', [App\Http\Controllers\RecoveryController::class, 'verifyBackupCode'])->name('auth.recovery.backup-code.submit');

Route::get('/dashboard', [AuthController::class, 'dashboard'])->middleware('auth')->name('user.dashboard');

// Admin dashboard and abuse controls
Route::middleware(['web', AdminOnly::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics');
    Route::post('/admin/paste/{paste}/takedown', [AdminController::class, 'takedown'])->name('admin.takedown');
    Route::post('/admin/paste/{paste}/view-limit', [AdminController::class, 'setViewLimit'])->name('admin.viewlimit');
    Route::get('/admin/pastes', [AdminController::class, 'pastes'])->name('admin.pastes.index');
    Route::get('/admin/files', [AdminController::class, 'files'])->name('admin.files.index');
    Route::post('/admin/files/{file}/view-limit', [AdminController::class, 'setFileViewLimit'])->name('admin.files.viewlimit');
    Route::post('/admin/files/{file}/delete', [AdminController::class, 'deleteFile'])->name('admin.files.delete');
    
    // Admin User Management
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [AdminController::class, 'userShow'])->name('admin.users.show');
    Route::post('/admin/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('admin.users.toggle-admin');
    Route::post('/admin/users/{user}/toggle-2fa', [AdminController::class, 'toggle2FA'])->name('admin.users.toggle-2fa');
    Route::post('/admin/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('admin.users.reset-password');
    Route::post('/admin/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('admin.users.suspend');
    Route::post('/admin/users/{user}/delete', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/admin/users/{user}/activity', [AdminController::class, 'userActivity'])->name('admin.users.activity');
    
    // Admin Support
    Route::get('/admin/support', [AdminController::class, 'support'])->name('admin.support.index');
    Route::get('/admin/support/{report}', [AdminController::class, 'supportShow'])->name('admin.support.show');
    Route::post('/admin/support/{report}', [AdminController::class, 'supportUpdate'])->name('admin.support.update');
    // Admin blog - using resource routes
    Route::get('/admin/posts', [PostController::class, 'adminIndex'])->name('admin.posts.index');
    Route::resource('admin/posts', PostController::class)->except(['index'])->names([
        'create' => 'admin.posts.create',
        'store' => 'admin.posts.store',
        'show' => 'admin.posts.show',
        'edit' => 'admin.posts.edit',
        'update' => 'admin.posts.update',
        'destroy' => 'admin.posts.destroy',
    ]);
});

// Sitemaps
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/sitemap-pastes.xml', [SitemapController::class, 'pastes']);
Route::get('/sitemap-files.xml', [SitemapController::class, 'files']);

// SEO text files
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/ads.txt', [SeoController::class, 'ads']);

// Circuits diagnostics: list available artifacts across known roots
Route::get('/circuits', function() {
    $roots = [
        'public' => public_path('circuits'),
        'storage' => storage_path('app/circuits'),
        'base' => base_path('circuits'),
        'resources' => resource_path('circuits'),
    ];
    $listing = [];
    foreach ($roots as $name => $root) {
        if (is_dir($root)) {
            $paths = glob($root . '/*');
            $files = [];
            if ($paths) {
                foreach ($paths as $p) {
                    if (is_file($p)) { $files[] = basename($p); }
                }
            }
            $listing[$name] = [ 'path' => $root, 'files' => $files ];
        } else {
            $listing[$name] = [ 'path' => $root, 'exists' => false ];
        }
    }
    return response()->json($listing);
});

// Serve Circom artifacts for ZK proofs in dev and prod without web server rewrites
Route::get('/circuits/{path}', function(string $path) {
    $candidates = [
        public_path('circuits/' . $path),
        storage_path('app/circuits/' . $path),
        base_path('circuits/' . $path),
        resource_path('circuits/' . $path),
    ];
    foreach ($candidates as $file) {
        if (is_file($file)) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'wasm' => 'application/wasm',
                'json' => 'application/json',
                'zkey' => 'application/octet-stream',
                default => 'application/octet-stream',
            };
            return response()->file($file, [ 'Content-Type' => $mime, 'Cache-Control' => 'public, max-age=3600' ]);
        }
    }
    abort(404);
})->where('path', '.*')->name('circuits.serve');

// User settings (auth required)
Route::middleware(['web','auth'])->group(function () {
    Route::get('/settings', [UserSettingsController::class, 'show'])->name('settings.index');
    Route::post('/settings/password', [UserSettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::post('/settings/email', [UserSettingsController::class, 'updateEmail'])->name('settings.email.update');
    Route::post('/settings/encryption-keys', [UserSettingsController::class, 'updateEncryptionKeyPreference'])->name('settings.encryption-keys.update');
    Route::post('/settings/delete-account', [UserSettingsController::class, 'deleteAccount'])->name('settings.delete-account');
    Route::get('/settings/2fa/qr', [UserSettingsController::class, 'qr'])->name('settings.2fa.qr');
    Route::post('/settings/2fa/enable', [UserSettingsController::class, 'enable2fa'])->name('settings.2fa.enable');
    Route::post('/settings/2fa/disable', [UserSettingsController::class, 'disable2fa'])->name('settings.2fa.disable');
    Route::get('/settings/2fa/otpauth', [UserSettingsController::class, 'otpauth'])->name('settings.2fa.otpauth');
});
