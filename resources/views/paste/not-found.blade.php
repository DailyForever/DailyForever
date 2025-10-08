@extends('layouts.app')

@section('title', 'Paste Not Found - DailyForever')

@section('content')
<div class="max-w-2xl mx-auto text-center">
    <div class="content-card p-12">
        <h1 class="text-2xl font-medium text-yt-text mb-4" data-i18n="paste.not_found.title" data-i18n-doc-title="paste.not_found.doc_title">Paste Not Found</h1>
        <p class="text-base text-yt-text-secondary mb-8" data-i18n="paste.not_found.desc">
            This paste either doesn't exist, has expired, or the URL is incomplete.
        </p>
        
        <div class="bg-yt-bg border border-yt-border rounded-lg p-6 mb-8 text-left">
            <h3 class="text-base font-medium text-yt-text mb-3" data-i18n="paste.not_found.common">Common Issues:</h3>
            <ul class="space-y-2 text-yt-text-secondary text-sm">
                <li data-i18n="paste.not_found.issue_expired">• The paste has expired and been automatically deleted</li>
                <li data-i18n="paste.not_found.issue_missing_key">• The URL is missing the encryption key fragment (part after #)</li>
                <li data-i18n="paste.not_found.issue_mistyped">• The paste ID was mistyped or corrupted</li>
                <li data-i18n="paste.not_found.issue_never_created">• The paste was never created successfully</li>
            </ul>
        </div>

        <div class="space-y-4">
            <a 
                href="{{ route('paste.create') }}"
                class="btn-primary inline-block px-6 py-2 text-sm font-medium"
            >
                <span data-i18n="paste.not_found.create">Create New Paste</span>
            </a>
            <div>
                <button 
                    onclick="history.back()"
                    class="text-yt-accent hover:underline text-sm"
                >
                    <span data-i18n="paste.not_found.go_back">← Go Back</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection