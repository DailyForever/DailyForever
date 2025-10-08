@extends('layouts.app')

@section('title', 'View Paste - DailyForever')

@section('content')
<div id="pasteShowRoot" class="max-w-7xl mx-auto" data-encrypted-content="{{ e($paste->encrypted_content) }}" data-iv="{{ e($paste->iv) }}" data-password-protected="{{ $paste->password_hash ? '1' : '0' }}" data-is-owner="{{ $isOwner ? '1' : '0' }}" data-owner-key="{{ $isOwner && $paste->encryption_key ? e($paste->encryption_key) : '' }}" data-kem-alg="{{ $paste->kem_alg ?? '' }}" data-kem-kid="{{ $paste->kem_kid ?? '' }}" data-kem-ct="{{ $paste->kem_ct ? base64_encode($paste->kem_ct) : '' }}" data-kem-wrapped-key="{{ $paste->kem_wrapped_key ? base64_encode($paste->kem_wrapped_key) : '' }}">
    <div class="content-card p-6">
        <div class="flex justify-between items-start mb-5">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-xl font-medium text-yt-text" data-i18n="paste.show.title">Secure Paste</h1>
                    <span class="text-xs text-yt-text-secondary" data-i18n="common.id_label">ID:</span>
                    <code class="font-mono text-sm text-yt-accent" id="pasteIdentifier">{{ $paste->identifier }}</code>
                    <button type="button" class="btn-secondary px-2 py-1 text-xs" data-action="copyIdentifier" data-i18n="common.buttons.copy_id">Copy ID</button>
                </div>
                <div class="flex items-center space-x-4 text-sm text-yt-text-secondary">
                    <span>{{ $paste->views }} views</span>
                    @if(!is_null($paste->view_limit))
                        <span>Max views: {{ $paste->view_limit }}</span>
                    @endif
                    @if($paste->expires_at)
                        <span>Expires {{ $paste->expires_at->format('M j, Y g:i A') }}</span>
                    @else
                        <span>Never expires</span>
                    @endif
                    <span>Created {{ $paste->created_at->format('M j, Y g:i A') }}</span>
                </div>
            </div>
            <div class="flex space-x-3">
                <button 
                    data-action="copyContent"
                    class="btn-secondary px-4 py-2 text-sm"
                >
                    <span data-i18n="common.buttons.copy">Copy</span>
                </button>
                <a 
                    href="{{ route('paste.create') }}"
                    class="btn-primary px-4 py-2 text-sm font-medium"
                >
                    <span data-i18n="paste.show.new_paste">New Paste</span>
                </a>
            </div>
        </div>

        @if($isOwner)
        <!-- Owner view - show decrypted content if key is available -->
        <div class="bg-yt-bg border border-yt-border rounded-lg overflow-hidden">
            <div class="flex justify-between items-center px-4 py-3 bg-yt-surface border-b border-yt-border">
                <span class="text-sm font-medium text-yt-text">
                    @if($paste->encryption_key)
                        <span data-i18n="paste.show.owner_decrypted">Decrypted Content (Owner View)</span>
                    @else
                        <span data-i18n="paste.show.owner_encrypted">Encrypted Content (Owner View)</span>
                    @endif
                </span>
                <div class="flex space-x-2 items-center">
                    <select id="langSelectOwner" class="px-2 py-1 text-[11px] bg-yt-border/40 border border-yt-border rounded">
                        <option value="auto" selected>Auto</option>
                        <option>plaintext</option>
                        <option>markdown</option>
                        <option>json</option>
                        <option>javascript</option>
                        <option>typescript</option>
                        <option>python</option>
                        <option>php</option>
                        <option>go</option>
                        <option>rust</option>
                        <option>java</option>
                        <option>c</option>
                        <option>cpp</option>
                        <option>bash</option>
                        <option>sql</option>
                        <option>html</option>
                        <option>css</option>
                        <option>yaml</option>
                    </select>
                    <button 
                        data-action="toggleWrap"
                        id="wrapBtn"
                        class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors"
                    >
                        <span data-i18n="editor.wrap">Wrap</span>
                    </button>
                    <button 
                        data-action="toggleHighlight"
                        id="highlightBtn"
                        class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors"
                    >
                        <span data-i18n="editor.highlight">Highlight</span>
                    </button>
                    <button 
                        type="button"
                        class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors"
                        onclick="copyFrom('owner')"
                    ><span data-i18n="common.buttons.copy">Copy</span></button>
                    <button 
                        type="button"
                        class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors"
                        onclick="downloadRaw('owner')"
                    ><span data-i18n="common.buttons.download">Download</span></button>
                    @if(!$paste->encryption_key)
                    <button 
                        data-action="decryptForOwner"
                        id="decryptBtn"
                        class="text-xs px-3 py-1 bg-yt-accent text-white rounded hover:bg-yt-accent/80 transition-colors"
                    >
                        <span data-i18n="common.buttons.decrypt">Decrypt</span>
                    </button>
                    @endif
                </div>
            </div>
            <div class="editor-grid">
                <pre id="lineGutterOwner" class="line-gutter">1\n</pre>
                <pre class="p-4 text-sm font-mono text-yt-text whitespace-pre-wrap break-words overflow-x-auto"><code id="decrypted-content">{{ $paste->encrypted_content }}</code></pre>
            </div>
            @if(!$paste->encryption_key)
            <div class="px-4 py-2 bg-yt-surface border-t border-yt-border text-xs text-yt-text-secondary">
                <strong data-i18n="paste.show.owner_note_label">Owner Note:</strong> <span data-i18n="paste.show.owner_note_text">This is the encrypted content. To decrypt it, you need the encryption key from the share URL (the part after #).</span>
                <a href="#" data-action="showKeyInput" class="text-yt-accent hover:underline" data-i18n="paste.show.enter_key_manually">Enter key manually</a> 
                <span>or</span>
                <a href="{{ route('paste.show', $paste->identifier) }}#YOUR_KEY_HERE" class="text-yt-accent hover:underline" data-i18n="paste.show.use_share_url">use the share URL with key</a>.
            </div>
            @endif
        </div>
        @else
        <!-- Regular view - requires decryption -->
        @if($paste->password_hash)
        <div id="password-gate" class="text-center py-6">
            <div class="text-sm text-yt-text-secondary" data-i18n="paste.show.password_gate.protected">This paste is protected by a password.</div>
            @if($paste->password_hint)
            <div class="text-xs text-yt-text-secondary mt-1">Hint: {{ $paste->password_hint }}</div>
            @endif
            <div class="mt-3 flex items-center justify-center gap-2">
                <input id="pastePwd" type="password" class="input-field px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="paste.show.password_gate.placeholder" placeholder="Enter password" />
                <button id="unlockBtn" class="btn-primary px-3 py-2 text-sm" data-action="submitPassword" data-i18n="common.buttons.unlock">Unlock</button>
            </div>
        </div>
        @endif

        <div id="decrypting" class="text-center py-12 {{ $paste->password_hash ? 'hidden' : '' }}">
            <div class="text-yt-accent text-base font-medium" data-i18n="paste.show.decrypting.title">Decrypting content...</div>
            <p class="text-yt-text-secondary mt-2 text-sm" data-i18n="paste.show.decrypting.desc">Please wait while we decrypt your content client-side.</p>
            <div class="text-yt-text-disabled text-xs mt-1" data-i18n="paste.show.decrypting.note">Zero-knowledge: the key stays in your URL fragment and is never sent to the server.</div>
        </div>
        @if(!$isOwner && $paste->kem_alg)
        <div id="kemKeyImport" class="hidden mt-4">
            <div class="bg-yt-bg border border-yt-border rounded-lg p-4">
                <div class="text-sm font-medium text-yt-text mb-2">Recipient Key Required</div>
                <div class="text-xs text-yt-text-secondary mb-3">This paste is addressed using {{ $paste->kem_alg }}. Import your ML‑KEM secret key (base64) for key ID <code class="font-mono">{{ $paste->kem_kid }}</code> to decrypt without a URL key.</div>
                <div class="flex items-center gap-2">
                    <input id="kemSecretB64" class="input-field flex-1 px-3 py-2 text-sm font-mono" placeholder="Paste your ML-KEM secret key (base64)" />
                    <button type="button" class="btn-primary px-3 py-2 text-sm" onclick="saveKemSecret()">Save Key</button>
                </div>
            </div>
        </div>
        @endif
        @endif

        <div id="content-container" class="hidden">
            <div class="bg-yt-bg border border-yt-border rounded-lg overflow-hidden">
                <div class="flex justify-between items-center px-4 py-3 bg-yt-surface border-b border-yt-border">
                    <span class="text-sm font-medium text-yt-text" data-i18n="common.content">Content</span>
                    <div class="flex space-x-2 items-center">
                        <select id="langSelectViewer" class="px-2 py-1 text-[11px] bg-yt-border/40 border border-yt-border rounded">
                            <option value="auto" selected>Auto</option>
                            <option>plaintext</option>
                            <option>markdown</option>
                            <option>json</option>
                            <option>javascript</option>
                            <option>typescript</option>
                            <option>python</option>
                            <option>php</option>
                            <option>go</option>
                            <option>rust</option>
                            <option>java</option>
                            <option>c</option>
                            <option>cpp</option>
                            <option>bash</option>
                            <option>sql</option>
                            <option>html</option>
                            <option>css</option>
                            <option>yaml</option>
                        </select>
                        <button 
                            data-action="toggleWrap"
                            id="wrapBtn"
                            class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors"
                        >
                            <span data-i18n="editor.wrap">Wrap</span>
                        </button>
                        <button 
                            data-action="toggleHighlight"
                            id="highlightBtn"
                            class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors"
                        >
                            <span data-i18n="editor.highlight">Highlight</span>
                        </button>
                        <button type="button" class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors" onclick="copyFrom('viewer')" data-i18n="common.buttons.copy">Copy</button>
                        <button type="button" class="text-xs px-3 py-1 bg-yt-border text-yt-text rounded hover:bg-yt-elevated transition-colors" onclick="downloadRaw('viewer')" data-i18n="common.buttons.download">Download</button>
                    </div>
                </div>
                <div class="editor-grid">
                    <pre id="lineGutterShow" class="line-gutter">1\n</pre>
                    <pre id="decrypted-content" class="p-4 text-sm font-mono text-yt-text overflow-x-auto"><code id="content-code"></code></pre>
                </div>
            </div>
        </div>

        @if($paste->files && $paste->files->count())
        <div class="bg-yt-bg border border-yt-border rounded-lg overflow-hidden mt-6">
            <div class="flex justify-between items-center px-4 py-3 bg-yt-surface border-b border-yt-border">
                <span class="text-sm font-medium text-yt-text" data-i18n="paste.show.attachments.title">Encrypted Files</span>
            </div>
            <ul class="p-4 space-y-2">
                @foreach($paste->files as $f)
                <li class="flex items-center justify-between">
                    <span class="text-sm">{{ $f->original_filename }} ({{ number_format($f->size_bytes/1024, 1) }} KB)</span>
                    @if($isOwner)
                        <a href="{{ route('files.show', $f->identifier) }}" class="btn-secondary px-3 py-1 text-xs" data-i18n="paste.show.attachments.view_file">View File</a>
                    @else
                        <button class="btn-secondary px-3 py-1 text-xs" data-action="downloadAndDecrypt" data-file-id="{{ $f->identifier }}" data-filename="{{ $f->original_filename }}" data-i18n="paste.show.attachments.download_decrypt">Download & Decrypt</button>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif

        <div id="error-container" class="hidden">
            <div class="bg-yt-surface border border-yt-error rounded-lg p-6 text-center">
                <div class="text-yt-error text-base font-medium mb-2" data-i18n="paste.show.error.title">Decryption Failed</div>
                <p class="text-yt-text" data-i18n="paste.show.error.desc1">Unable to decrypt this paste. The encryption key may be missing or invalid.</p>
                <p class="text-yt-text-secondary text-sm mt-2" data-i18n="paste.show.error.desc2">Make sure you're using the complete URL including the fragment after #</p>
                <div class="mt-4 max-w-md mx-auto">
                    <label class="block text-sm mb-2 text-yt-text" data-i18n="paste.show.error.label">Paste key (hex) to decrypt</label>
                    <div class="flex">
                        <input id="manualKey" class="input-field flex-1 px-3 py-2 text-sm font-mono" placeholder="e.g. 8f2a..." />
                        <button data-action="setManualKey" class="btn-primary ml-2 px-3 py-2 text-sm" data-i18n="common.buttons.decrypt">Decrypt</button>
                    </div>
                    <p class="text-xs text-yt-text-secondary mt-2" data-i18n="paste.show.error.note">This is the part after # in the share URL.</p>
                </div>
            </div>
        </div>
    </div>
    <div id="zkProofPanelPaste" class="bg-yt-bg border border-yt-border rounded-lg p-4 mt-3">
        <div class="text-sm font-medium" data-i18n="files.show.zkp.title">Zero-Knowledge Proof</div>
        <div id="zkProofStatusPaste" class="text-xs text-yt-text-secondary mt-1" data-i18n="common.status.checking">Checking…</div>
        <div class="mt-2 flex items-center gap-2">
            <button type="button" class="btn-secondary px-3 py-1 text-xs" data-action="refreshZkProofPaste" data-i18n="common.buttons.refresh">Refresh</button>
            <button type="button" class="btn-secondary px-3 py-1 text-xs" data-action="verifyZkProofPaste" data-i18n="common.buttons.verify">Verify</button>
            <button type="button" class="btn-secondary px-3 py-1 text-xs" data-action="generateZkProofForPaste" data-i18n="common.buttons.generate_proof">Generate Proof</button>
            <button type="button" class="btn-secondary px-3 py-1 text-xs" data-action="toggleViewZkProofPaste" data-i18n="common.buttons.view">View</button>
        </div>
        <div class="mt-2 text-xs">
            <span class="text-yt-text-secondary" data-i18n="common.zkp.commitment">Commitment:</span>
            <code id="zkCommitValue" class="font-mono text-yt-accent break-all"></code>
            <button type="button" class="btn-secondary px-2 py-1 text-xs ml-2" data-action="copyZkCommitPaste" data-i18n="common.buttons.copy">Copy</button>
        </div>
        <div id="zkProofViewerPaste" class="mt-2 hidden">
            <div class="flex items-center gap-2 mb-2">
                <button type="button" class="btn-secondary px-2 py-1 text-xs" data-action="copyZkProofPaste" data-i18n="common.buttons.copy_json">Copy JSON</button>
                <button type="button" class="btn-secondary px-2 py-1 text-xs" data-action="downloadZkProofPaste" data-i18n="common.buttons.download">Download JSON</button>
            </div>
            <pre id="zkProofJsonPaste" class="text-xs overflow-auto max-h-64 bg-yt-bg border border-yt-border rounded p-2"></pre>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
async function isValidWasmPaste(url) {
    try {
        if (!url) return false;
        const res = await fetch(url, { cache: 'no-store' });
        if (!res.ok) return false;
        const buf = await res.arrayBuffer();
        const u8 = new Uint8Array(buf);
        return u8.length >= 4 && u8[0] === 0x00 && u8[1] === 0x61 && u8[2] === 0x73 && u8[3] === 0x6d;
    } catch (_) { return false; }
}
// ---- ZK Proof viewer (Paste) ----
function toggleViewZkProofPaste() {
    const viewer = document.getElementById('zkProofViewerPaste');
    if (viewer.classList.contains('hidden')) {
        populateZkProofJsonPaste();
        viewer.classList.remove('hidden');
    } else {
        viewer.classList.add('hidden');
    }
}

async function populateZkProofJsonPaste() {
    try {
        if (!_zkMetaPaste) {
            await refreshZkProofPaste();
        }
        const pre = document.getElementById('zkProofJsonPaste');
        if (!_zkMetaPaste || !_zkMetaPaste.zk) {
            pre.textContent = 'No proof available to display.';
            return;
        }
        // Only display commitment (do not expose raw proof/publicSignals)
        const commit = (_zkMetaPaste.commitments && _zkMetaPaste.commitments.commit)
            ? _zkMetaPaste.commitments.commit
            : (_zkMetaPaste.zk.publicSignals && _zkMetaPaste.zk.publicSignals[0]) || null;
        if (!commit) { pre.textContent = 'No proof available to display.'; return; }
        const encryptedView = _zkMetaPaste.encrypted || {
            algorithm: _zkMetaPaste.algorithm ?? null,
            iv: _zkMetaPaste.iv ?? null,
            timestamp: _zkMetaPaste.timestamp ?? null,
        };
        const payload = { encrypted: encryptedView, commitments: _zkMetaPaste.commitments || null, zk: { commit } };
        pre.textContent = JSON.stringify(payload, null, 2);
    } catch (e) {
        console.warn(e);
        const pre = document.getElementById('zkProofJsonPaste');
        pre.textContent = 'Error loading proof JSON';
    }
}

function copyZkProofPaste() {
    const pre = document.getElementById('zkProofJsonPaste');
    if (!pre || !pre.textContent) return;
    try {
        navigator.clipboard.writeText(pre.textContent);
    } catch (_) {}
}

function downloadZkProofPaste() {
    const pre = document.getElementById('zkProofJsonPaste');
    if (!pre || !pre.textContent) return;
    const id = document.getElementById('pasteIdentifier')?.textContent?.trim() || 'paste';
    const blob = new Blob([pre.textContent], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${id}-zk-proof.json`;
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
}

function setZkCommitPaste(val) {
    const el = document.getElementById('zkCommitValue');
    if (!el) return;
    el.textContent = val ? String(val) : '—';
}

function copyZkCommitPaste() {
    const el = document.getElementById('zkCommitValue');
    if (!el || !el.textContent) return;
    try { navigator.clipboard.writeText(el.textContent); } catch (_) {}
}
const pasteData = {
    encrypted_content: {!! json_encode($paste->encrypted_content) !!},
    iv: {!! json_encode($paste->iv) !!}
};
class SecureDecryption {
    static async importKey(keyArray) {
        const keyBuffer = new Uint8Array(keyArray);
        return await window.crypto.subtle.importKey(
            "raw",
            keyBuffer,
            { name: "AES-GCM", length: 256 },
            false,
            ["decrypt"]
        );
    }

    static async decrypt(encryptedData, iv, key) {
        const ciphertext = new Uint8Array(encryptedData);
        const ivArray = new Uint8Array(iv);
        
        const decrypted = await window.crypto.subtle.decrypt(
            { name: "AES-GCM", iv: ivArray },
            key,
            ciphertext
        );
        const bytes = new Uint8Array(decrypted);
        // Backward-compat: old format used 4-byte length header. Use it only if plausible.
        if (bytes.length >= 4) {
            const len = (((bytes[0] << 24) | (bytes[1] << 16) | (bytes[2] << 8) | bytes[3]) >>> 0);
            if (len <= bytes.length - 4) {
                return new TextDecoder().decode(bytes.slice(4, 4 + len));
            }
        }
        return new TextDecoder().decode(bytes);
    }
}

function updateLineGutterFromText(gutterId, text) {
    try {
        const gutter = document.getElementById(gutterId);
        if (!gutter) return;
        const lines = Math.max(1, (String(text).match(/\n/g) || []).length + 1);
        let buf = '';
        for (let i = 1; i <= lines; i++) buf += i + "\n";
        gutter.textContent = buf;
    } catch (_) {}
}

async function decryptAndDisplay() {
    if (isDecrypting) {
        console.log('Decryption already in progress, ignoring duplicate request');
        return;
    }
    
    isDecrypting = true;
    
    try {
        // If content already visible (e.g., decrypted by external module), skip
        const shown = document.getElementById('content-container');
        if (shown && !shown.classList.contains('hidden')) { isDecrypting = false; return; }
        console.log('Starting decryption process...');
        
        // Get key from URL fragment
        const fragment = window.location.hash.substring(1);
        if (!fragment) {
            // If no key in URL, check if KEM secret import UI is available
            const kemImportUI = document.getElementById('kemKeyImport');
            if (kemImportUI) {
                kemImportUI.classList.remove('hidden');
                isDecrypting = false;
                return;
            }
            throw new Error('No encryption key found in URL');
        }

        console.log('Found encryption key in URL fragment');

        // Convert hex key to array
        const keyArray = [];
        for (let i = 0; i < fragment.length; i += 2) {
            keyArray.push(parseInt(fragment.substr(i, 2), 16));
        }

        // If server requires password, check if password gate is still visible
        @if($paste->password_hash)
        const passwordGate = document.getElementById('password-gate');
        if (passwordGate && !passwordGate.classList.contains('hidden')) {
            console.log('Password gate is still visible, waiting for password verification');
            // Password gate is still visible, don't proceed with decryption
            isDecrypting = false;
            return;
        }
        console.log('Password gate is hidden, proceeding with decryption');
        @endif
        // Import key
        const key = await SecureDecryption.importKey(keyArray);
        
        // Decrypt content
        const encryptedContent = JSON.parse(pasteData.encrypted_content);
        const iv = JSON.parse(pasteData.iv);
        
        const decryptedText = await SecureDecryption.decrypt(encryptedContent, iv, key);
        
        // Display content
        document.getElementById('content-code').textContent = decryptedText;
        updateLineGutterFromText('lineGutterShow', decryptedText);
        document.getElementById('decrypting').classList.add('hidden');
        document.getElementById('content-container').classList.remove('hidden');
        
        // Initialize syntax highlighting using viewer language selector
        try {
            const sel = document.getElementById('langSelectViewer');
            applyLanguage(document.getElementById('content-code'), sel?.value || 'auto');
        } catch (_) {}
        
    } catch (error) {
        console.error('Decryption error:', error);
        document.getElementById('decrypting').classList.add('hidden');
        document.getElementById('error-container').classList.remove('hidden');
    } finally {
        isDecrypting = false;
    }
}
async function verifyPassword() {
    const gate = document.getElementById('password-gate');
    if (!gate) {
        console.log('No password gate found, assuming no password required');
        return true; // No password gate means no password required
    }
    
    // Check if password gate is already hidden (password already verified)
    if (gate.classList.contains('hidden')) {
        console.log('Password gate is already hidden, password already verified');
        return true;
    }
    
    const pwd = (document.getElementById('pastePwd').value || '').trim();
    if (!pwd) { 
        alert('Enter password'); 
        return false; 
    }
    
    console.log('Verifying password for paste...', {
        passwordLength: pwd.length,
        passwordPreview: pwd.substring(0, 3) + '***'
    });
    
    try {
        // Construct URL without fragment for password check
        const baseUrl = window.location.origin + window.location.pathname;
        const checkUrl = baseUrl + '?pw_check=1';
        
        console.log('Making request to:', checkUrl);
        
        const resp = await fetch(checkUrl, { 
            method: 'GET',
            headers: { 
                'X-Paste-Password': pwd,
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            } 
        });
        
        console.log('Password check response:', {
            status: resp.status,
            statusText: resp.statusText,
            ok: resp.ok
        });
        
        if (resp.status === 204) { 
            console.log('Password verification successful');
            gate.classList.add('hidden'); 
            document.getElementById('decrypting').classList.remove('hidden'); 
            return true; 
        }
        
        const errorData = await resp.json().catch(() => ({}));
        console.log('Password check error:', errorData);
        
        alert('Incorrect password');
        return false;
    } catch (error) {
        console.error('Password verification error:', error);
        alert('Error verifying password');
        return false;
    }
}

let isVerifyingPassword = false;
let isDecrypting = false;

async function submitPassword() { 
    if (isVerifyingPassword) {
        console.log('Password verification already in progress, ignoring duplicate request');
        return;
    }
    
    if (isDecrypting) {
        console.log('Decryption already in progress, ignoring duplicate request');
        return;
    }
    
    // Disable the button and input during verification
    const unlockBtn = document.getElementById('unlockBtn');
    const passwordInput = document.getElementById('pastePwd');
    
    if (unlockBtn) {
        unlockBtn.disabled = true;
        unlockBtn.textContent = 'Verifying...';
    }
    if (passwordInput) {
        passwordInput.disabled = true;
    }
    
    isVerifyingPassword = true;
    try {
        const ok = await verifyPassword();
        if (ok) {
            // Password verified, now proceed with decryption
            await decryptAndDisplay();
        } else {
            // Re-enable the button and input on failure
            if (unlockBtn) {
                unlockBtn.disabled = false;
                unlockBtn.textContent = 'Unlock';
            }
            if (passwordInput) {
                passwordInput.disabled = false;
                passwordInput.focus();
            }
        }
    } finally {
        isVerifyingPassword = false;
    }
}

function copyContent(e) {
    const content = document.getElementById('content-code').textContent;
    navigator.clipboard.writeText(content).then(() => {
        const button = e.target;
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        setTimeout(() => {
            button.textContent = originalText;
        }, 2000);
    });
}

function copyIdentifier() {
    const id = document.getElementById('pasteIdentifier')?.textContent?.trim();
    if (!id) return;
    navigator.clipboard.writeText(id).then(() => {
        const btn = event?.target;
        if (!btn || btn.tagName !== 'BUTTON') return;
        const label = btn.textContent;
        btn.textContent = 'Copied!';
        setTimeout(() => btn.textContent = label, 2000);
    }).catch(() => alert('Unable to copy identifier.'));
}

function activeContext() {
    const viewerVisible = !document.getElementById('content-container')?.classList.contains('hidden');
    if (viewerVisible) {
        return {
            mode: 'viewer',
            pre: document.getElementById('decrypted-content'),
            code: document.getElementById('content-code'),
            langSel: document.getElementById('langSelectViewer')
        };
    } else {
        const ownerCode = document.querySelector('code#decrypted-content');
        return {
            mode: 'owner',
            pre: ownerCode ? ownerCode.parentElement : null,
            code: ownerCode,
            langSel: document.getElementById('langSelectOwner')
        };
    }
}

function setWrapButtons(label) {
    document.querySelectorAll('button#wrapBtn').forEach(b => { b.textContent = label; });
}

function toggleWrap() {
    const ctx = activeContext(); if (!ctx || !ctx.pre) return;
    const pre = ctx.pre;
    if (pre.classList.contains('whitespace-pre-wrap')) {
        pre.classList.remove('whitespace-pre-wrap');
        pre.classList.add('overflow-x-auto');
        setWrapButtons('Wrap');
    } else {
        pre.classList.add('whitespace-pre-wrap');
        pre.classList.remove('overflow-x-auto');
        setWrapButtons('No Wrap');
    }
}

function setHighlightButtons(label) {
    document.querySelectorAll('button#highlightBtn').forEach(b => { b.textContent = label; });
}

function applyLanguage(codeEl, lang) {
    if (!codeEl) return;
    codeEl.className = '';
    if (lang && lang !== 'auto' && lang !== 'plaintext') {
        codeEl.classList.add('language-' + lang);
    }
    if (typeof hljs !== 'undefined') {
        try { hljs.highlightElement(codeEl); } catch (_) {}
    }
}

function toggleHighlight() {
    const ctx = activeContext(); if (!ctx || !ctx.code) return;
    const code = ctx.code;
    if (code.classList.contains('hljs')) {
        code.className = '';
        setHighlightButtons('Highlight');
    } else {
        applyLanguage(code, ctx.langSel?.value || 'auto');
        setHighlightButtons('Plain');
    }
}

function copyFrom(which) {
    try {
        const ctx = activeContext();
        const text = (ctx && ctx.code) ? ctx.code.textContent : '';
        if (!text) return;
        navigator.clipboard.writeText(text);
    } catch (_) {}
}

function downloadRaw(which) {
    try {
        const ctx = activeContext();
        const text = (ctx && ctx.code) ? ctx.code.textContent : '';
        if (!text) return;
        const id = document.getElementById('pasteIdentifier')?.textContent?.trim() || 'paste';
        const blob = new Blob([text], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = `${id}.txt`;
        document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
    } catch (_) {}
}

// Decrypt content for owner view
async function decryptForOwner(encryptionKey = null) {
    try {
        console.log('Starting owner decryption...');
        console.log('pasteData:', pasteData);
        
        let keySource = 'URL fragment';
        let keyHex = null;
        
        // Get key from parameter, URL fragment, or stored key
        if (encryptionKey) {
            keyHex = encryptionKey;
            keySource = 'stored key';
        } else {
            keyHex = window.location.hash.substring(1);
            keySource = 'URL fragment';
        }
        
        console.log('Key source:', keySource);
        console.log('Key hex:', keyHex ? keyHex.substring(0, 20) + '...' : 'none');
        
        if (!keyHex) {
            console.log('No encryption key found for owner view');
            document.getElementById('decrypted-content').textContent = 'No encryption key found';
            return;
        }

        console.log('Decrypting content for owner view...');

        // Convert hex key to array
        const keyArray = [];
        for (let i = 0; i < keyHex.length; i += 2) {
            keyArray.push(parseInt(keyHex.substr(i, 2), 16));
        }
        console.log('Key array length:', keyArray.length);

        // Check if SecureDecryption is available
        if (typeof SecureDecryption === 'undefined') {
            console.error('SecureDecryption class not found');
            document.getElementById('decrypted-content').textContent = 'Decryption library not loaded';
            return;
        }

        // Import key
        const key = await SecureDecryption.importKey(keyArray);
        console.log('Key imported successfully');
        
        // Decrypt content
        const encryptedContent = JSON.parse(pasteData.encrypted_content);
        const iv = JSON.parse(pasteData.iv);
        console.log('Encrypted content length:', encryptedContent.length);
        console.log('IV length:', iv.length);
        
        const decryptedText = await SecureDecryption.decrypt(encryptedContent, iv, key);
        console.log('Decryption successful, text length:', decryptedText.length);
        
        // Display decrypted content
        document.getElementById('decrypted-content').textContent = decryptedText;
        updateLineGutterFromText('lineGutterOwner', decryptedText);
        
        // Initialize syntax highlighting using owner language selector
        try {
            const sel = document.getElementById('langSelectOwner');
            applyLanguage(document.getElementById('decrypted-content'), sel?.value || 'auto');
        } catch (_) {}
        
        console.log('Owner view decryption completed');
    } catch (error) {
        console.error('Owner view decryption failed:', error);
        console.error('Error details:', error.message, error.stack);
        // If decryption fails, show error message
        document.getElementById('decrypted-content').textContent = 'Decryption failed: ' + error.message;
    }
}

// Show manual key input for owners
function showKeyInput() {
    const key = prompt('Enter the encryption key (hex string from the share URL):');
    if (key) {
        // Set the URL fragment and try to decrypt
        window.location.hash = key;
        decryptForOwner();
    }
}

// Start decryption when page loads (only for non-password protected pastes)
document.addEventListener('DOMContentLoaded', function() {
    @if($isOwner)
    // Owner view - auto-decrypt if key is stored
    @if($paste->encryption_key)
    console.log('Owner view - auto-decrypting with stored key');
    decryptForOwner('{{ $paste->encryption_key }}');
    @else
    console.log('Owner view - showing encrypted content (no stored key)');
    // Initialize line gutter based on currently shown (encrypted) content
    try {
        const txt = document.getElementById('decrypted-content')?.textContent || '';
        updateLineGutterFromText('lineGutterOwner', txt);
    } catch (_) {}
    @endif
    @else
    @if($paste->password_hash)
    // For password-protected pastes, don't auto-decrypt
    // User must enter password first
    console.log('Password-protected paste detected, waiting for user input');
    @else
    // For non-password protected pastes, decrypt immediately
    console.log('Non-password protected paste, starting decryption');
    decryptAndDisplay();
    @endif
    @endif
});

// Re-highlight on language selection changes
document.addEventListener('DOMContentLoaded', function() {
    const ownerSel = document.getElementById('langSelectOwner');
    if (ownerSel) {
        ownerSel.addEventListener('change', () => {
            const code = document.querySelector('code#decrypted-content');
            applyLanguage(code, ownerSel.value);
            setHighlightButtons('Plain');
        });
    }
    const viewerSel = document.getElementById('langSelectViewer');
    if (viewerSel) {
        // Initialize from URL ?lang=
        try {
            const params = new URLSearchParams(window.location.search);
            const qLang = params.get('lang');
            if (qLang) viewerSel.value = qLang;
        } catch (_) {}
        viewerSel.addEventListener('change', () => {
            const code = document.getElementById('content-code');
            applyLanguage(code, viewerSel.value);
            setHighlightButtons('Plain');
        });
    }
});

function setManualKey() {
    const input = document.getElementById('manualKey').value.trim();
    if (!input || input.length % 2 !== 0 || /[^0-9a-fA-F]/.test(input)) {
        alert('Please enter a valid hex key.');
        return;
    }
    window.location.hash = '#' + input.toLowerCase();
    document.getElementById('error-container').classList.add('hidden');
    document.getElementById('decrypting').classList.remove('hidden');
    decryptAndDisplay();
}

async function downloadAndDecrypt(fileIdentifier, filename) {
    // Ask user for the file key
    const keyHex = prompt('Enter file key (hex) provided at upload time:');
    if (!keyHex) return;
    const keyBytes = new Uint8Array((keyHex.match(/.{1,2}/g) || []).map(byte => parseInt(byte, 16)));
    const key = await SecureDecryption.importKey(keyBytes);
    const resp = await fetch(`{{ url('/api/files') }}/${fileIdentifier}/download`);
    if (!resp.ok) { alert('Download failed'); return; }
    const ivHeader = resp.headers.get('X-File-IV');
    const iv = JSON.parse(ivHeader || '[]');
    const cipher = new Uint8Array(await resp.arrayBuffer());
    const plain = await crypto.subtle.decrypt({ name: 'AES-GCM', iv: new Uint8Array(iv) }, key, cipher);
    const blob = new Blob([plain], { type: 'application/octet-stream' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    URL.revokeObjectURL(url);
    a.remove();
}

// ---- ZK Proof status + verification (Paste) ----
let _zkMetaPaste = null;
function setZkStatusPaste(text, isError = false) {
    const el = document.getElementById('zkProofStatusPaste');
    if (!el) return;
    el.textContent = text;
    el.classList.toggle('text-yt-error', !!isError);
}

async function refreshZkProofPaste() {
    try {
        setZkStatusPaste('Checking…');
        const id = document.getElementById('pasteIdentifier')?.textContent?.trim();
        if (!id) { setZkStatusPaste('Missing paste identifier', true); return; }
        const resp = await fetch(`/api/zk/encryption/by-ref?type=paste&identifier=${encodeURIComponent(id)}&_t=${Date.now()}` , { cache: 'no-store' });
        if (!resp.ok) { setZkStatusPaste('No proof found'); return; }
        const data = await resp.json();
        _zkMetaPaste = data.meta || null;
        if (!_zkMetaPaste) { setZkStatusPaste('No proof metadata'); return; }
        const present = _zkMetaPaste.zk?.present ? 'yes' : 'no';
        const verified = _zkMetaPaste.zk?.verified === true ? 'yes' : (_zkMetaPaste.zk?.verified === false ? 'no' : 'unknown');
        setZkStatusPaste(`Proof present: ${present} • Verified: ${verified}`);
        const commit = (_zkMetaPaste?.commitments?.commit) || (_zkMetaPaste?.zk?.publicSignals?.[0]) || null;
        setZkCommitPaste(commit);
    } catch (e) {
        setZkStatusPaste('Error loading proof metadata', true);
        console.warn(e);
    }
}

async function verifyZkProofPaste() {
    try {
        // Always refresh latest metadata before verifying
        if (!_zkMetaPaste) {
            await refreshZkProofPaste();
        }
        // If still missing, try one more refresh
        if (!_zkMetaPaste) {
            await refreshZkProofPaste();
        }
        if (!_zkMetaPaste || !_zkMetaPaste.zk) {
            setZkStatusPaste('No proof available to verify');
            return;
        }
        // Prepare proof/publicSignals from plain or encrypted envelope
        let proofObj = null;
        if (_zkMetaPaste.zk.proof) {
            proofObj = { proof: _zkMetaPaste.zk.proof, publicSignals: _zkMetaPaste.zk.publicSignals };
        } else if (_zkMetaPaste.zk.enc) {
            try {
                setZkStatusPaste('Decrypting proof…');
                const ok = await ensureDecryptedForProofPaste();
                if (!ok || !_pKey) { setZkStatusPaste('Missing key to decrypt proof', true); return; }
                const enc = _zkMetaPaste.zk.enc;
                const iv = new Uint8Array(enc.iv || []);
                const ad = enc.ad ? new TextEncoder().encode(enc.ad) : undefined;
                const ctBuf = (window.WebCryptoWrapper && window.WebCryptoWrapper.base64ToArrayBuffer)
                    ? window.WebCryptoWrapper.base64ToArrayBuffer(enc.ct)
                    : Uint8Array.from(atob(enc.ct), c => c.charCodeAt(0)).buffer;
                const decrypted = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv, additionalData: ad }, _pKey, ctBuf);
                const text = new TextDecoder().decode(new Uint8Array(decrypted));
                proofObj = JSON.parse(text);
            } catch (e) {
                setZkStatusPaste('Encrypted proof present but could not be decrypted', true);
                return;
            }
        }
        if (!proofObj || !proofObj.proof || !proofObj.publicSignals) {
            setZkStatusPaste('No proof available to verify');
            return;
        }
        // 1) Bind proof to actually served ciphertext by checking hash
        try {
            const enc = new Uint8Array(JSON.parse(pasteData.encrypted_content));
            const localHash = await window.SecureEncryption.hash(enc, 'SHA-256');
            const localHex = toHexPaste(localHash);
            const claimedHex = (_zkMetaPaste.commitments?.ciphertextHash || '').toLowerCase();
            if (!claimedHex || localHex !== claimedHex) {
                setZkStatusPaste('Ciphertext hash mismatch — served content not bound to proof', true);
                return;
            }
        } catch (e) {
            console.warn(e);
            setZkStatusPaste('Failed to compute ciphertext hash locally', true);
            return;
        }
        // Optional: ensure commitment matches first public signal when present
        if (_zkMetaPaste.commitments?.commit && proofObj.publicSignals?.[0]) {
            const commit = String(_zkMetaPaste.commitments.commit);
            if (String(proofObj.publicSignals[0]) !== commit) {
                setZkStatusPaste('Commitment mismatch — proof not for this ciphertext', true);
                return;
            }
        }
        const art = window.ZK_ARTIFACTS || {};
        const vkeyUrl = (art.paste?.vkeyUrl || art.common?.vkeyUrl || art.vkeyUrl) || window.ZK_VKEY_URL;
        if (!vkeyUrl) {
            setZkStatusPaste('vkey not configured on client');
            return;
        }
        setZkStatusPaste('Verifying…');
        if (!window.SecureEncryption) {
            setZkStatusPaste('Crypto library not loaded', true);
            return;
        }
        const ok = await window.SecureEncryption.verifyEncryptionProof({
            vkeyUrl,
            proof: proofObj.proof,
            publicSignals: proofObj.publicSignals,
            loaderOptions: art.paste?.loaderOptions || art.common?.loaderOptions || {}
        });
        setZkStatusPaste(`Proof present: yes • Hash bound: yes • Verified: ${ok ? 'yes' : 'no'}`);
    } catch (e) {
        setZkStatusPaste('Verification error', true);
        console.warn(e);
    }
}

// Retroactive proof generation for pastes
function toHexPaste(u8) { return Array.from(u8).map(b => b.toString(16).padStart(2,'0')).join(''); }
let _pCipher = null, _pIv = null, _pPlain = null, _pKey = null;

async function ensureDecryptedForProofPaste() {
    try {
        // Ensure key is imported
        if (!_pKey) {
            const fragment = window.location.hash.substring(1);
            if (!fragment) { setZkStatusPaste('Missing key in link.'); return false; }
            const keyArray = [];
            for (let i = 0; i < fragment.length; i += 2) keyArray.push(parseInt(fragment.substr(i, 2), 16));
            _pKey = await window.crypto.subtle.importKey('raw', new Uint8Array(keyArray), { name: 'AES-GCM' }, false, ['decrypt','encrypt']);
        }
        // Decrypt content once to populate caches
        if (!_pCipher || !_pIv || !_pPlain) {
            const encryptedContent = JSON.parse(pasteData.encrypted_content);
            const iv = JSON.parse(pasteData.iv);
            const decrypted = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv: new Uint8Array(iv) }, _pKey, new Uint8Array(encryptedContent));
            _pCipher = new Uint8Array(encryptedContent);
            _pIv = new Uint8Array(iv);
            _pPlain = new Uint8Array(decrypted);
        } else {
            // If already decrypted, just return true
            return true;
        }
        return true;
    } catch (e) {
        console.warn(e);
        setZkStatusPaste('Decryption failed', true);
        return false;
    }
}

async function generateZkProofForPaste() {
    try {
        if (!window.SecureEncryption) { setZkStatusPaste('Crypto library not loaded', true); return; }
        const ok = await ensureDecryptedForProofPaste();
        if (!ok) return;
        const art = (window.ZK_ARTIFACTS && (window.ZK_ARTIFACTS.paste || window.ZK_ARTIFACTS.common)) || null;
        if (!art || !art.wasmUrl || !art.zkeyUrl) { setZkStatusPaste('Artifacts not configured'); return; }
        if (!(await isValidWasmPaste(art.wasmUrl))) { setZkStatusPaste('WASM invalid or not reachable', true); return; }
        setZkStatusPaste('Generating proof…');
        const { zk, commitments } = await window.SecureEncryption.generateProofForCiphertext({
            plaintext: _pPlain,
            ciphertext: _pCipher,
            iv: _pIv,
            algorithm: 'AES-GCM',
            additionalData: null,
            zkOptions: { wasmUrl: art.wasmUrl, zkeyUrl: art.zkeyUrl, loaderOptions: art.loaderOptions || {} }
        });
        const id = document.getElementById('pasteIdentifier')?.textContent?.trim();
        // Build encrypted proof envelope (omit plaintext proof in payload)
        let zkEnc = null;
        if (zk && zk.proof) {
            const iv = crypto.getRandomValues(new Uint8Array(12));
            const ad = new TextEncoder().encode('zkp:proof:v1');
            const body = new TextEncoder().encode(JSON.stringify({ proof: zk.proof, publicSignals: zk.publicSignals }));
            const ct = await crypto.subtle.encrypt({ name: 'AES-GCM', iv, additionalData: ad }, _pKey, body);
            const ctB64 = (window.WebCryptoWrapper && window.WebCryptoWrapper.arrayBufferToBase64)
                ? window.WebCryptoWrapper.arrayBufferToBase64(ct)
                : btoa(String.fromCharCode(...new Uint8Array(ct)));
            zkEnc = { alg: 'AES-GCM', iv: Array.from(iv), ad: 'zkp:proof:v1', ct: ctB64 };
        }
        const payload = {
            encrypted: { algorithm: 'AES-GCM', iv: Array.from(_pIv), timestamp: Date.now() },
            commitments: {
                ciphertextHash: toHexPaste(commitments.ciphertextHash),
                additionalDataHash: null,
                commit: commitments.commit || (zk && zk.publicSignals ? zk.publicSignals[0] : null)
            },
            zk: zkEnc ? { proof: null, publicSignals: null, enc: zkEnc } : (zk || null),
            ref: { type: 'paste', identifier: id }
        };
        await fetch('/api/zk/encryption/submit', { method:'POST', headers:{ 'Content-Type':'application/json' }, body: JSON.stringify(payload) });
        setZkStatusPaste('Proof submitted. Refreshing…');
        setZkCommitPaste(payload.commitments.commit || null);
        await refreshZkProofPaste();
    } catch (e) {
        console.warn(e);
        setZkStatusPaste('Error generating proof', true);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    refreshZkProofPaste();
});
</script>
@endsection
