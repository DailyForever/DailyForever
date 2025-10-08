@extends('layouts.app')

@section('title', 'Test Backup Code Modal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="content-card p-8">
        <h1 class="text-2xl font-bold text-yt-text mb-4">Test Backup Code Modal</h1>
        <p class="text-yt-text-secondary mb-6">This page tests if the backup code modal works correctly.</p>
        
        <button onclick="showBackupCodeModal('TEST1234567890AB')" class="btn-primary">
            Show Test Modal
        </button>
        
        <div class="mt-4">
            <h3 class="text-lg font-semibold text-yt-text mb-2">Debug Info:</h3>
            <p class="text-sm text-yt-text-secondary">Session backup_code: {{ session('backup_code') ?? 'Not set' }}</p>
            <p class="text-sm text-yt-text-secondary">Backup code exists: {{ session('backup_code') ? 'Yes' : 'No' }}</p>
        </div>
    </div>
</div>
@endsection
