@extends('layouts.app')

@section('title', 'Edit Paste - DailyForever')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="content-card p-6">
        <h1 class="text-2xl font-medium text-yt-text mb-3" data-i18n="paste.edit.title" data-i18n-doc-title="paste.edit.doc_title">Edit Paste</h1>
        <p class="text-yt-text-secondary mb-6 text-sm" data-i18n="paste.edit.desc">Edit your encrypted paste. Changes will be encrypted client-side before saving.</p>

        <form id="pasteForm" class="space-y-5">
            @csrf
            @method('PUT')
            <div>
                <label for="content" class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.content">Content</label>
                <textarea 
                    id="content" 
                    name="content" 
                    rows="18" 
                    class="input-field w-full px-3 py-3 font-mono text-sm resize-vertical"
                    data-i18n-attr="placeholder" data-i18n-placeholder="paste.edit.placeholder" placeholder="Paste your content here... It will be encrypted before sending."
                    required
                ></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="expires_in" class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.options.expiration">Expiration</label>
                    <select 
                        id="expires_in" 
                        name="expires_in" 
                        class="input-field w-full px-3 py-2 text-sm"
                    >
                        <option value="never" data-i18n="common.options.never">Never</option>
                        <option value="1hour" data-i18n="common.options.1hour">1 Hour</option>
                        <option value="1day" data-i18n="common.options.1day">1 Day</option>
                        <option value="1week" data-i18n="common.options.1week">1 Week</option>
                        <option value="1month" data-i18n="common.options.1month">1 Month</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.password.label_optional">Password (optional)</label>
                    <input id="password" type="password" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="common.password.placeholder_optional" placeholder="Protect with a password (optional)" />
                    <input id="password_hint" class="input-field w-full px-3 py-2 text-sm mt-2" data-i18n-attr="placeholder" data-i18n-placeholder="common.password.hint_optional" placeholder="Password hint (optional)" />
                    <p class="text-xs text-yt-text-secondary mt-1" data-i18n="paste.edit.password_note">If set, password is verified server-side (Argon2id). Content remains end‑to‑end encrypted.</p>
                </div>
                <div>
                    <label for="view_limit" class="block text-sm font-medium text-yt-text mb-2" data-i18n="paste.edit.view_limit.label">View Limit</label>
                    <input 
                        id="view_limit" 
                        type="number" 
                        min="1" 
                        max="1000000" 
                        class="input-field w-full px-3 py-2 text-sm" 
                        data-i18n-attr="placeholder" data-i18n-placeholder="paste.edit.view_limit.placeholder" placeholder="Maximum views (optional)"
                    />
                </div>
                <div>
                    <label class="flex items-center space-x-2">
                        <input id="is_private" type="checkbox" class="rounded border-yt-border text-yt-accent focus:ring-yt-accent" />
                        <span class="text-sm text-yt-text" data-i18n="paste.edit.private.label">Make this paste private</span>
                    </label>
                    <p class="text-xs text-yt-text-secondary mt-1" data-i18n="paste.edit.private.help">Private pastes are only visible to you</p>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('pastes.mine') }}" class="text-yt-text-secondary hover:text-yt-text" data-i18n="paste.edit.back_to_mine">← Back to My Pastes</a>
                <div class="flex space-x-3">
                    <button type="button" id="cancelBtn" class="btn-secondary px-6 py-2" data-i18n="common.buttons.cancel">Cancel</button>
                    <button type="submit" id="submitBtn" class="btn-primary px-6 py-2">
                        <span id="submitText" data-i18n="paste.edit.buttons.update">Update Paste</span>
                        <span id="loadingText" class="hidden" data-i18n="paste.edit.buttons.updating">Updating...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pasteForm');
    const contentTextarea = document.getElementById('content');
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingText = document.getElementById('loadingText');
    const cancelBtn = document.getElementById('cancelBtn');

    // Load existing paste data
    loadPasteData();
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!contentTextarea.value.trim()) {
            const msg = window.I18N ? I18N.t('paste.edit.errors.empty') : 'Please enter some content.';
            alert(msg);
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
{{ ... }}
    // Load existing paste data
    async function loadPasteData() {
        try {
            // Fetch the raw paste data
            const response = await fetch('{{ route("paste.raw", $paste->identifier) }}');
            const data = await response.json();
            const ok = await verifyPassword();

            if (data.encrypted_content && data.iv) {
                // Decrypt the content
                const decryptedContent = await decryptContent(data.encrypted_content, data.iv);
                contentTextarea.value = decryptedContent;
{{ ... }}

            document.getElementById('view_limit').value = '{{ $paste->view_limit ?? "" }}';
            document.getElementById('is_private').checked = {{ $paste->is_private ? 'true' : 'false' }};
        } catch (error) {
            console.error('Error loading paste data:', error);
            alert(window.I18N ? I18N.t('paste.edit.errors.load_failed') : 'Error loading paste data. You may need to refresh the page.');
        }
    }

    // Encryption function (same as create page)
    async function encryptContent(content) {
{{ ... }}
            { name: 'AES-GCM', length: 256 },
            true,
            ['encrypt', 'decrypt']
        );

        const iv = crypto.getRandomValues(new Uint8Array(12));
        const encodedContent = new TextEncoder().encode(content);

        const encrypted = await crypto.subtle.encrypt(
            { name: 'AES-GCM', iv: iv },
            key,
            encodedContent
        );

        const exportedKey = await crypto.subtle.exportKey('raw', key);
        const keyBytes = new Uint8Array(exportedKey);
        const keyHex = Array.from(keyBytes, b => b.toString(16).padStart(2, '0')).join('');

        return {
            encrypted: Array.from(new Uint8Array(encrypted)),
            iv: Array.from(iv),
            keyHex
        };
    }

    // Decryption function
    async function decryptContent(encryptedArray, ivArray) {
        // This is a simplified version - in practice, you'd need the original key
        // For now, we'll just show a placeholder
        return 'Content loaded (decryption requires original key)';
    }
});
</script>
@endsection
