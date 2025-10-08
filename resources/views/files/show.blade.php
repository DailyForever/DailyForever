@extends('layouts.app')

@section('title', 'Encrypted File - DailyForever')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="content-card p-6 space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg grid place-items-center bg-emerald-500/10 border border-emerald-400/30 text-emerald-300">
                        <svg viewBox="0 0 24 24" class="w-4 h-4"><path fill="currentColor" d="M12 16q-.425 0-.713-.288T11 15v-3.175l-.9.9q-.3.3-.712.288T8.7 12.9q-.3-.3-.3-.7t.3-.7l2.6-2.6q.15-.15.325-.213T12 8.55q.2 0 .375.062t.325.213l2.6 2.6q.3.3.288.713T15.3 12.9q-.3.3-.7.3t-.7-.3l-.9-.9V15q0 .425-.288.713T12 16Z"/></svg>
                    </div>
                    <h1 class="text-xl font-medium" data-i18n="files.show.title">Encrypted File</h1>
                </div>
                <div class="text-xs text-yt-text-secondary" data-i18n="paste.show.decrypting.note">Zero-knowledge: the key stays in your URL fragment and is never sent to the server.</div>
            </div>
            <div class="hidden sm:flex flex-col items-end gap-1">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-yt-text-secondary" data-i18n="common.id_label">ID:</span>
                    <code id="fileIdentifier" class="font-mono text-sm text-yt-accent">{{ $file->identifier }}</code>
                    <button type="button" class="btn-secondary px-2 py-1 text-xs" onclick="copyFileIdentifier()" data-i18n="common.buttons.copy_id">Copy ID</button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="md:col-span-2 space-y-3">
                <div class="text-sm"><span data-i18n="files.show.info.filename">Filename:</span> <span class="font-mono">{{ $file->original_filename }}</span></div>
                <div class="text-sm"><span data-i18n="files.show.info.size">Size:</span> {{ number_format($file->size_bytes/1024,1) }} KB</div>
                <div class="text-sm"><span data-i18n="files.show.info.downloads">Downloads:</span> {{ $file->views }} @if(!is_null($file->view_limit)) / <span data-i18n="files.show.info.max">Max</span> {{ $file->view_limit }} @endif</div>
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    @if($file->password_hash)
                        <input id="filePwd" type="password" class="input-field px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="login.hint_password" placeholder="Password" />
                    @endif
                    <button class="btn-primary px-4 py-2 text-sm" onclick="downloadFileSmart()" data-i18n="common.buttons.download">Download</button>
                    <button class="btn-secondary px-4 py-2 text-xs" onclick="copyCurrentLink()" data-i18n="common.buttons.copy">Copy Link</button>
                </div>
            </div>
        </div>

        @if($file->kem_alg)
        <div id="kemKeyImportFile" class="hidden mt-3">
            <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                <div class="text-sm font-medium text-yt-text mb-2" data-i18n="files.show.kem.title">Recipient Key Required</div>
                <div class="text-xs text-yt-text-secondary mb-3">
                    <span data-i18n="files.show.kem.desc_part1">This file is addressed using</span> <span class="font-mono">{{ $file->kem_alg }}</span>. 
                    <span data-i18n="files.show.kem.desc_part2">Import your ML‑KEM secret key (base64) for key ID</span> 
                    <code class="font-mono">{{ $file->kem_kid }}</code> 
                    <span data-i18n="files.show.kem.desc_part3">to download without a URL key.</span>
                </div>
                <div class="flex items-center gap-2">
                    <input id="kemSecretB64File" class="input-field flex-1 px-3 py-2 text-sm font-mono" data-i18n-attr="placeholder" data-i18n-placeholder="files.show.kem.placeholder" placeholder="Paste your ML-KEM secret key (base64)" />
                    <button type="button" class="btn-primary px-3 py-2 text-sm" onclick="saveKemSecretFile()" data-i18n="files.show.kem.save">Save Key</button>
                </div>
            </div>
        </div>
        @endif

        <div id="zkProofPanel" class="bg-yt-bg border border-yt-border rounded-lg p-4 mt-4">
            <div class="text-sm font-medium" data-i18n="files.show.zkp.title">Zero-Knowledge Proof</div>
            <div id="zkProofStatus" class="text-xs text-yt-text-secondary mt-1" data-i18n="common.status.checking">Checking…</div>
            <div class="mt-2 flex items-center gap-2">
                <button type="button" class="btn-secondary px-3 py-1 text-xs" onclick="refreshZkProof()" data-i18n="common.buttons.refresh">Refresh</button>
                <button type="button" class="btn-secondary px-3 py-1 text-xs" onclick="verifyZkProof()" data-i18n="common.buttons.verify">Verify</button>
                <button type="button" class="btn-secondary px-3 py-1 text-xs" onclick="generateZkProofForFile()" data-i18n="common.buttons.generate_proof">Generate Proof</button>
            </div>
            <div class="mt-2 text-xs">
                <span class="text-yt-text-secondary" data-i18n="common.zkp.commitment">Commitment:</span>
                <code id="zkCommitValue" class="font-mono text-yt-accent break-all"></code>
                <button type="button" class="btn-secondary px-2 py-1 text-xs ml-2" onclick="copyZkCommit()" data-i18n="common.buttons.copy">Copy</button>
            </div>
            <div class="mt-2">
                <div class="flex items-center gap-2">
                    <button type="button" class="btn-secondary px-3 py-1 text-xs" onclick="toggleZkJson()" data-i18n="common.buttons.show_json">Show JSON</button>
                    <button type="button" class="btn-secondary px-3 py-1 text-xs" onclick="copyZkJson()" data-i18n="common.buttons.copy_json">Copy JSON</button>
                </div>
                <pre id="zkProofJson" class="mt-2 bg-yt-bg border border-yt-border rounded p-3 text-xs overflow-x-auto hidden"></pre>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Helpers and state (minimal logs)
function toHex(u8) { return Array.from(u8).map(b => b.toString(16).padStart(2,'0')).join(''); }
function b64ToU8(b64) {
    try {
        if (window.WebCryptoWrapper?.base64ToArrayBuffer) return new Uint8Array(window.WebCryptoWrapper.base64ToArrayBuffer(b64));
        const bin = atob(b64); const out = new Uint8Array(bin.length); for (let i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i); return out;
    } catch (_) { return null; }
}

const KEM_META_FILE = {
    alg: "{{ $file->kem_alg ?? '' }}",
    kid: "{{ $file->kem_kid ?? '' }}",
    ctB64: "{{ $file->kem_ct ? base64_encode($file->kem_ct) : '' }}",
    wrappedB64: "{{ $file->kem_wrapped_key ? base64_encode($file->kem_wrapped_key) : '' }}",
};

function loadRecipientSecretFile(kid) {
    if (!kid) return null; let b64 = null; try { b64 = localStorage.getItem(`pq.prekeys.${kid}.sk`) || null; } catch (_) {}
    if (!b64) { try { b64 = localStorage.getItem(`pq.keypairs.${kid}.sk`) || null; } catch (_) {} }
    return b64 ? b64ToU8(b64) : null;
}
function saveKemSecretFile() {
    try {
        const kid = KEM_META_FILE.kid || '';
        const input = document.getElementById('kemSecretB64File');
        const b64 = (input?.value || '').trim();
        if (!kid || !b64) { alert('Enter secret key (base64)'); return; }
        try { localStorage.setItem(`pq.prekeys.${kid}.sk`, b64); } catch (_) { alert('Could not save key'); return; }
        document.getElementById('kemKeyImportFile')?.classList.add('hidden');
        alert('Key saved. Try Download again.');
    } catch (_) {}
}

function setZkCommit(val) {
    const el = document.getElementById('zkCommitValue');
    if (!el) return; el.textContent = val ? String(val) : '—';
}

function copyZkCommit() {
    const el = document.getElementById('zkCommitValue');
    if (!el || !el.textContent) return;
    try { navigator.clipboard.writeText(el.textContent); } catch (_) {}
}

function copyFileIdentifier() {
    const el = document.getElementById('fileIdentifier');
    const id = el?.textContent?.trim();
    if (!id) return;
    try { navigator.clipboard.writeText(id); } catch (_) {}
}

function copyCurrentLink() {
    try { navigator.clipboard.writeText(window.location.href); } catch (_) {}
}
let _lastCipher = null, _lastIv = null, _lastPlain = null, _zkMetaFile = null;

async function downloadFileSmart() {
    try {
        // If KEM metadata present, try addressed download first
        if (KEM_META_FILE && KEM_META_FILE.alg && KEM_META_FILE.kid && KEM_META_FILE.ctB64 && KEM_META_FILE.wrappedB64) {
            const ok = await downloadViaKEM();
            if (ok) return;
            // If failed due to missing key, UI will be shown; otherwise fallback to fragment
        }
    } catch (_) {}
    // Fallback to URL fragment flow
    return downloadWithFragment();
}

async function downloadViaKEM() {
    try {
        if (!window.PostQuantumKEM) return false;
        const sk = loadRecipientSecretFile(KEM_META_FILE.kid);
        if (!sk) {
            const ui = document.getElementById('kemKeyImportFile'); if (ui) ui.classList.remove('hidden');
            return false;
        }
        const ct = b64ToU8(KEM_META_FILE.ctB64);
        const wrapped = b64ToU8(KEM_META_FILE.wrappedB64);
        if (!ct || !wrapped) return false;
        const shared = await window.PostQuantumKEM.decapsulate(ct, sk, KEM_META_FILE.alg || 'ML-KEM-512');
        const aesRaw = await window.PostQuantumKEM.decryptWithSharedSecret(wrapped, shared);
        const aesKey = await crypto.subtle.importKey('raw', aesRaw, { name:'AES-GCM', length:256 }, false, ['decrypt']);

        // Prepare headers (password if required)
        const headers = {};
        const pwdInput = document.getElementById('filePwd');
        if (pwdInput && pwdInput.value) headers['X-Download-Password'] = pwdInput.value;

        const resp = await fetch('{{ route('files.standalone.download', $file->identifier) }}', { headers });
        if (!resp.ok) return false;
        const iv = JSON.parse(resp.headers.get('X-File-IV') || '[]');
        const cipher = new Uint8Array(await resp.arrayBuffer());
        const plain = await crypto.subtle.decrypt({ name:'AES-GCM', iv: new Uint8Array(iv) }, aesKey, cipher);
        _lastIv = new Uint8Array(iv); _lastCipher = cipher; _lastPlain = new Uint8Array(plain);
        const blob = new Blob([plain], { type: '{{ $file->mime_type ?: 'application/octet-stream' }}' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = '{{ $file->original_filename }}';
        document.body.appendChild(a); a.click(); URL.revokeObjectURL(url); a.remove();
        return true;
    } catch (e) { console.warn('downloadViaKEM failed', e); return false; }
}

async function downloadWithFragment() {
    const fragment = location.hash.slice(1);
    if (!fragment) { alert('Missing key in link.'); return; }
    const keyBytes = new Uint8Array((fragment.match(/.{1,2}/g) || []).map(h => parseInt(h,16)));
    const key = await crypto.subtle.importKey('raw', keyBytes, { name:'AES-GCM' }, false, ['decrypt']);
    const headers = {};
    const pwdInput = document.getElementById('filePwd');
    if (pwdInput && pwdInput.value) headers['X-Download-Password'] = pwdInput.value;
    const resp = await fetch('{{ route('files.standalone.download', $file->identifier) }}', { headers });
    if (!resp.ok) {
        let msg = 'Server error';
        try {
            if ((resp.headers.get('content-type')||'').includes('application/json')) {
                const j = await resp.json(); msg = j.error || j.message || msg;
            }
        } catch(_) {}
        alert('Download failed ('+resp.status+'): '+msg);
        return;
    }
    const iv = JSON.parse(resp.headers.get('X-File-IV') || '[]');
    const cipher = new Uint8Array(await resp.arrayBuffer());
    try {
        const plain = await crypto.subtle.decrypt({ name:'AES-GCM', iv: new Uint8Array(iv) }, key, cipher);
        _lastIv = new Uint8Array(iv); _lastCipher = cipher; _lastPlain = new Uint8Array(plain);
        const blob = new Blob([plain], { type: '{{ $file->mime_type ?: 'application/octet-stream' }}' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = '{{ $file->original_filename }}';
        document.body.appendChild(a); a.click(); URL.revokeObjectURL(url); a.remove();
    } catch (error) {
        alert('Decryption failed: ' + error.message);
    }
}

function setZkStatus(text, isError = false) {
    const el = document.getElementById('zkProofStatus');
    if (!el) return; el.textContent = text; el.classList.toggle('text-yt-error', !!isError);
}

async function ensureDecryptedForProof() {
    if (_lastCipher && _lastIv && _lastPlain) return true;
    const fragment = location.hash.slice(1); if (!fragment) { setZkStatus('Missing key'); return false; }
    const keyBytes = new Uint8Array((fragment.match(/.{1,2}/g) || []).map(h => parseInt(h,16)));
    const key = await crypto.subtle.importKey('raw', keyBytes, { name:'AES-GCM' }, false, ['decrypt']);
    const headers = {}; const pwdInput = document.getElementById('filePwd');
    if (pwdInput && pwdInput.value) headers['X-Download-Password'] = pwdInput.value;
    const resp = await fetch('{{ route('files.standalone.download', $file->identifier) }}', { headers });
    if (!resp.ok) { setZkStatus('Cannot fetch ciphertext', true); return false; }
    const iv = JSON.parse(resp.headers.get('X-File-IV') || '[]');
    const cipher = new Uint8Array(await resp.arrayBuffer());
    try {
        const plain = await crypto.subtle.decrypt({ name:'AES-GCM', iv: new Uint8Array(iv) }, key, cipher);
        _lastIv = new Uint8Array(iv); _lastCipher = cipher; _lastPlain = new Uint8Array(plain);
        return true;
    } catch { setZkStatus('Decryption failed for proof', true); return false; }
}

async function generateZkProofForFile() {
    try {
        if (!window.SecureEncryption) { setZkStatus('Crypto not loaded', true); return; }
        const ok = await ensureDecryptedForProof(); if (!ok) return;
        const art = (window.ZK_ARTIFACTS && (window.ZK_ARTIFACTS.file || window.ZK_ARTIFACTS.common)) || null;
        if (!art || !art.wasmUrl || !art.zkeyUrl) { setZkStatus('Artifacts not configured'); return; }
        setZkStatus('Generating proof…');
        const { zk, commitments } = await window.SecureEncryption.generateProofForCiphertext({
            plaintext: _lastPlain, ciphertext: _lastCipher, iv: _lastIv, algorithm: 'AES-GCM', additionalData: null,
            zkOptions: { wasmUrl: art.wasmUrl, zkeyUrl: art.zkeyUrl, loaderOptions: art.loaderOptions || {} }
        });
        const payload = { encrypted: { algorithm: 'AES-GCM', iv: Array.from(_lastIv), timestamp: Date.now() },
            commitments: { ciphertextHash: toHex(commitments.ciphertextHash), additionalDataHash: null }, zk,
            ref: { type: 'file', identifier: '{{ $file->identifier }}' } };
        await fetch('/api/zk/encryption/submit', { method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(payload) });
        setZkStatus('Proof submitted. Refreshing…'); await refreshZkProof();
    } catch { setZkStatus('Error generating proof', true); }
}

async function refreshZkProof() {
    try {
        setZkStatus('Checking…');
        const resp = await fetch(`/api/zk/encryption/by-ref?type=file&identifier={{ $file->identifier }}`);
        if (!resp.ok) { setZkStatus('No proof found'); return; }
        const data = await resp.json(); _zkMetaFile = data.meta || null;
        if (!_zkMetaFile) { setZkStatus('No proof metadata'); return; }
        const present = _zkMetaFile.zk?.present ? 'yes' : 'no';
        const verified = _zkMetaFile.zk?.verified === true ? 'yes' : (_zkMetaFile.zk?.verified === false ? 'no' : 'unknown');
        setZkStatus(`Proof present: ${present} • Verified: ${verified}`);
        // Update commitment display if available
        try {
            const commit = (_zkMetaFile?.commitments?.commit) || (_zkMetaFile?.zk?.publicSignals?.[0]) || null;
            setZkCommit(commit);
        } catch (_) {}
        // If JSON panel is visible, keep it in sync
        const pre = document.getElementById('zkProofJson');
        if (pre && !pre.classList.contains('hidden')) updateZkJson();
    } catch { setZkStatus('Error loading proof metadata', true); }
}

async function verifyZkProof() {
    try {
        if (!_zkMetaFile || !_zkMetaFile.zk || !_zkMetaFile.zk.proof) { setZkStatus('No proof available'); return; }
        const art = window.ZK_ARTIFACTS || {};
        const vkeyUrl = (art.file?.vkeyUrl || art.common?.vkeyUrl || art.vkeyUrl) || window.ZK_VKEY_URL;
        if (!vkeyUrl) { setZkStatus('vkey not configured'); return; }
        setZkStatus('Verifying…');
        if (!window.SecureEncryption) { setZkStatus('Crypto not loaded', true); return; }
        const ok = await window.SecureEncryption.verifyEncryptionProof({ vkeyUrl, proof: _zkMetaFile.zk.proof, publicSignals: _zkMetaFile.zk.publicSignals, loaderOptions: art.file?.loaderOptions || art.common?.loaderOptions || {} });
        setZkStatus(`Proof present: yes • Verified: ${ok ? 'yes' : 'no'}`);
    } catch { setZkStatus('Verification error', true); }
}

document.addEventListener('DOMContentLoaded', () => { refreshZkProof(); });

function updateZkJson() {
    const pre = document.getElementById('zkProofJson');
    if (!pre) return;
    if (!_zkMetaFile) { pre.textContent = '// No proof metadata available'; return; }
    try {
        pre.textContent = JSON.stringify(_zkMetaFile, null, 2);
    } catch (_) {
        pre.textContent = '// Error serializing proof metadata';
    }
}

function toggleZkJson() {
    const pre = document.getElementById('zkProofJson');
    if (!pre) return;
    const isHidden = pre.classList.contains('hidden');
    if (isHidden) { updateZkJson(); pre.classList.remove('hidden'); }
    else { pre.classList.add('hidden'); }
}

async function copyZkJson() {
    try {
        let text = '';
        if (_zkMetaFile) text = JSON.stringify(_zkMetaFile, null, 2); else text = '';
        await navigator.clipboard.writeText(text);
        alert('ZK JSON copied');
    } catch (_) { alert('Failed to copy JSON'); }
}
</script>
@endsection


