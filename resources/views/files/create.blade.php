@extends('layouts.app')

@section('title', 'Upload Secure File - DailyForever')

@section('content')
<div id="filesCreateRoot" class="w-full">
    <div class="content-card">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-yt-text mb-3" data-i18n="files.create.section_title">Share Files Securely</h1>
            <p class="text-yt-text-secondary text-base" data-i18n="files.create.section_desc">Drop any file below — we'll encrypt it instantly before anyone (including us) can see it.</p>
        </div>

        <form id="fileForm" class="space-y-5" data-store-url="{{ route('files.store', [], false) }}">
            @csrf
            <!-- Quick Mode (Default) -->
            <div id="quickMode" class="space-y-6">
                <!-- Simple Drop Zone -->
                <div id="dropzone" class="relative border-3 border-dashed border-emerald-500/30 rounded-2xl p-8 sm:p-12 bg-gradient-to-br from-yt-elevated/40 to-emerald-500/5 hover:from-yt-elevated/60 hover:to-emerald-500/10 cursor-pointer transition-all">
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="w-16 h-16 rounded-2xl grid place-items-center bg-emerald-500/15 border-2 border-emerald-400/40">
                            <svg viewBox="0 0 24 24" class="w-8 h-8 text-emerald-400">
                                <path fill="currentColor" d="M9 16h6v-6h4l-7-7-7 7h4v6zm-4 2h14v2H5v-2z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-lg font-medium text-yt-text mb-2" data-i18n="files.create.dropzone.simple">Drop your file here or click to browse</div>
                            <div class="text-sm text-yt-text-secondary" data-i18n="files.create.dropzone.simple_desc">Any file up to 160 MB • Encrypted instantly</div>
                        </div>
                        <button type="button" class="btn-primary px-6 py-3 text-base rounded-xl" onclick="document.getElementById('fileInput').click()" data-i18n="files.create.dropzone.choose">Choose File</button>
                    </div>
                    <div id="selectedFile" class="mt-4 hidden">
                        <div class="flex items-center justify-center gap-3 p-3 rounded-lg bg-emerald-500/10 border border-emerald-400/30">
                            <svg class="w-5 h-5 text-emerald-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9 16.2l-3.5-3.5 1.4-1.4L9 13.4l7.1-7.1 1.4 1.4z"/>
                            </svg>
                            <span class="font-medium text-emerald-400" id="selectedFileName"></span>
                            <span class="text-sm text-emerald-300" id="selectedFileMeta"></span>
                        </div>
                    </div>
                    <input 
                        type="file" 
                        id="fileInput" 
                        name="file" 
                        class="absolute inset-0 w-px h-px opacity-0 pointer-events-none"
                        required
                        accept="*/*"
                    >
                </div>

                <div id="progressWrap" class="hidden">
                    <div class="mt-4">
                        <div class="w-full h-3 rounded-full bg-yt-bg border border-yt-border overflow-hidden">
                            <div id="progressBar" class="h-full bg-emerald-500 transition-all" style="width: 0%"></div>
                        </div>
                        <div id="progressLabel" class="text-sm text-yt-text-secondary mt-2 text-center" data-i18n="common.progress.preparing">Preparing…</div>
                    </div>
                </div>
                
                <!-- Simple Options -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.expires_simple">How long should we keep it?</label>
                        <select 
                            id="expires_in" 
                            name="expires_in" 
                            class="input-field w-full px-4 py-3 text-base"
                        >
                            <option value="1hour" data-i18n="common.options.1hour_simple">1 Hour</option>
                            <option value="1day" selected data-i18n="common.options.1day_simple">24 Hours (recommended)</option>
                            <option value="1week" data-i18n="common.options.1week_simple">1 Week</option>
                            <option value="1month" data-i18n="common.options.1month_simple">1 Month</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.password_simple">Add password protection?</label>
                        <input 
                            id="password" 
                            type="password" 
                            class="input-field w-full px-4 py-3 text-base" 
                            data-i18n-attr="placeholder" 
                            data-i18n-placeholder="common.password.placeholder_simple" 
                            placeholder="Optional password..."
                        />
                    </div>
                </div>
            </div>
            
            <!-- Advanced Mode (Hidden by default) -->
            <div id="advancedMode" class="hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
                    <div class="lg:col-span-2">
                        <label for="fileInputAdvanced" class="block text-sm font-medium text-yt-text mb-2" data-i18n="files.create.field.file">File</label>
                        <div id="dropzoneAdvanced" class="relative border-2 border-dashed border-yt-border rounded-xl p-4 sm:p-6 bg-yt-elevated/40 hover:bg-yt-elevated/55 cursor-pointer transition-colors">
                            <div class="flex flex-col items-center text-center gap-3 py-1">
                                <div class="w-12 h-12 rounded-full grid place-items-center bg-emerald-500/10 border border-emerald-400/30 text-emerald-300">
                                    <svg viewBox="0 0 24 24" class="w-6 h-6"><path fill="currentColor" d="M12 16q-.425 0-.713-.288T11 15v-3.175l-.9.9q-.3.3-.712.288T8.7 12.9q-.3-.3-.3-.7t.3-.7l2.6-2.6q.15-.15.325-.213T12 8.55q.2 0 .375.062t.325.213l2.6 2.6q.3.3.288.713T15.3 12.9q-.3.3-.7.3t-.7-.3l-.9-.9V15q0 .425-.288.713T12 16ZM6 20q-.825 0-1.412-.587T4 18V8q0-.825.588-1.413T6 6h5q.2-.6.675-1t1.125-.4t1.125.4t.675 1h5q.825 0 1.413.588T22 8v10q0 .825-.587 1.413T20 20H6Z"/></svg>
                                </div>
                                <div class="text-sm" data-i18n="files.create.dropzone.drag">Drag a file here, or browse from your device</div>
                                <div class="text-xs text-yt-text-secondary" data-i18n="files.create.dropzone.limit">Up to 160 MB — Client‑side encrypted • Zero‑knowledge</div>
                                <div class="mt-1">
                                    <button type="button" class="btn-secondary px-3 py-1 text-xs" onclick="document.getElementById('fileInputAdvanced').click()" data-i18n="files.create.dropzone.browse">Browse files</button>
                                </div>
                            </div>
                            <div id="selectedFileAdvanced" class="mt-3 hidden">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="px-2 py-1 rounded-md border border-yt-border bg-yt-bg font-mono" id="selectedFileNameAdvanced"></span>
                                    <span class="text-yt-text-secondary" id="selectedFileMetaAdvanced"></span>
                                </div>
                            </div>
                            <input 
                                type="file" 
                                id="fileInputAdvanced" 
                                name="fileAdvanced" 
                                class="absolute inset-0 w-px h-px opacity-0 pointer-events-none"
                                accept="*/*"
                            >
                        </div>

                        <div id="progressWrapAdvanced" class="hidden">
                            <div class="mt-4">
                                <div class="w-full h-2 rounded bg-yt-bg border border-yt-border overflow-hidden">
                                    <div id="progressBarAdvanced" class="h-full bg-emerald-500" style="width: 0%"></div>
                                </div>
                                <div id="progressLabelAdvanced" class="text-xs text-yt-text-secondary mt-2" data-i18n="common.progress.preparing">Preparing…</div>
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-1">
                        <div class="rounded-lg border border-yt-border bg-yt-elevated/40 p-3 sm:p-4">
                            <h3 class="text-sm font-medium text-yt-text mb-3" data-i18n="common.options.title">Options</h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="expires_in_advanced" class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.options.expiration">Expiration</label>
                                    <select
                                        id="expires_in_advanced"
                                        name="expires_in_advanced"
                                        class="input-field w-full px-3 py-2 text-sm"
                                    >
                                        <option value="1hour" data-i18n="common.options.1hour">1 Hour</option>
                                        <option value="1day" selected data-i18n="common.options.1day">1 Day</option>
                                        <option value="1week" data-i18n="common.options.1week">1 Week</option>
                                        <option value="1month" data-i18n="common.options.1month">1 Month</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="view_limit" class="block text-sm font-medium text-yt-text mb-2" data-i18n="files.create.view_limit.label">View limit (optional)</label>
                                    <input 
                                        id="view_limit" 
                                        type="number" 
                                        min="1" 
                                        max="1000000" 
                                        class="input-field w-full px-3 py-2 text-sm" 
                                        data-i18n-attr="placeholder" data-i18n-placeholder="files.create.view_limit.placeholder" placeholder="Maximum number of downloads"
                                    />
                                    <p class="text-xs text-yt-text-secondary mt-1" data-i18n="files.create.view_limit.note">File will be deleted after this many downloads</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-yt-text mb-2" data-i18n="common.password.label_optional">Password (optional)</label>
                                    <input id="password_advanced" type="password" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="common.password.placeholder_optional" placeholder="Protect with a password (optional)" />
                                    <input id="password_hint" class="input-field w-full px-3 py-2 text-sm mt-2" data-i18n-attr="placeholder" data-i18n-placeholder="common.password.hint_optional" placeholder="Password hint (optional)" />
                                    <p class="text-xs text-yt-text-secondary mt-1" data-i18n="files.create.section_desc">If set, the password is verified server-side (Argon2id). File remains end‑to‑end encrypted.</p>
                                </div>
                                <div>
                                    <label for="recipient_username" class="block text-sm font-medium text-yt-text mb-2" data-i18n="files.create.recipient_username">Recipient username (optional)</label>
                                    <input id="recipient_username" class="input-field w-full px-3 py-2 text-sm" data-i18n-attr="placeholder" data-i18n-placeholder="files.create.username_placeholder" placeholder="username" />
                                    <p class="text-xs text-yt-text-secondary mt-1" data-i18n="files.create.recipient_note">If set, the file key is wrapped to the recipient using their ML‑KEM prekey so they can download without the URL key.</p>
                                </div>
                                @auth
                                <div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" id="is_private_toggle" role="switch" aria-checked="false" class="private-toggle">
                                            <span id="is_private_knob" class="private-knob"></span>
                                        </button>
                                        <input type="checkbox" id="is_private" name="is_private" class="hidden" />
                                        <span class="text-sm text-yt-text private-label" data-i18n="common.private_label">Private (visible only to you when logged in)</span>
                                    </div>
                                </div>
                                @endauth
                                @guest
                                <div class="text-xs text-yt-text-secondary" data-i18n="files.create.login_private">Login to create private files.</div>
                                @endguest
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Toggle Advanced Mode -->
            <div class="flex justify-center">
                <button 
                    type="button" 
                    id="toggleMode"
                    class="text-sm text-yt-text-secondary hover:text-yt-text transition-colors flex items-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span id="modeToggleText" data-i18n="common.advanced_settings">Advanced Settings</span>
                </button>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center pt-4 gap-4">
                <button 
                    type="submit" 
                    id="submitBtn"
                    class="btn-primary px-8 py-4 text-lg font-medium w-full sm:w-auto rounded-xl shadow-lg hover:shadow-xl transition-all"
                >
                    <span class="flex items-center justify-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM9 6c0-1.66 1.34-3 3-3s3 1.34 3 3v2H9V6zm9 14H6V10h12v10zm-6-3c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                        </svg>
                        <span data-i18n="files.create.cta_simple">Encrypt & Upload File</span>
                    </span>
                </button>
            </div>
            <div class="text-center text-sm text-yt-text-secondary">
                <div data-i18n="common.security_simple">🔒 Your file is encrypted before leaving your device</div>
            </div>
        </form>

        <div id="result" class="mt-6 hidden">
            <div class="rounded-xl border border-yt-success/60 bg-yt-elevated/60 p-5 sm:p-6">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 mt-0.5">
                        <div class="w-7 h-7 rounded-full grid place-items-center bg-emerald-500/15 border border-emerald-400/30">
                            <svg viewBox="0 0 24 24" class="w-4 h-4 text-emerald-400"><path fill="currentColor" d="M9 16.2l-3.5-3.5l1.4-1.4L9 13.4l7.1-7.1l1.4 1.4z"/></svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base sm:text-lg font-medium text-yt-success" data-i18n="files.create.result.title">File Uploaded Successfully</h3>
                        <p class="text-xs text-yt-text-secondary mt-1" data-i18n="common.share_link_never_leaves">Your share link contains the decryption key after the # and never leaves your browser.</p>
                        <div class="mt-4 space-y-3">
                            <div>
                                <label class="block text-xs font-medium text-yt-text mb-2" data-i18n="files.create.download_url">Download URL</label>
                                <div class="flex flex-col sm:flex-row gap-2">
                                    <input 
                                        type="text" 
                                        id="downloadUrl" 
                                        readonly 
                                        class="flex-1 px-3 py-2 bg-yt-bg border border-yt-border rounded text-yt-text font-mono text-xs sm:text-sm focus:outline-none"
                                    >
                                    <button 
                                        type="button" 
                                        onclick="copyToClipboard(event)"
                                        class="btn-primary px-4 py-2 rounded text-xs sm:text-sm font-medium" data-i18n="common.buttons.copy"
                                    >Copy</button>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 pt-1">
                                <button type="button" class="btn-secondary px-3 py-2 text-xs sm:text-sm" onclick="shareLink()" data-i18n="common.buttons.share">Share</button>
                                <button type="button" class="btn-secondary px-3 py-2 text-xs sm:text-sm" onclick="showQr()" data-i18n="common.buttons.show_qr">Show QR</button>
                            </div>
                            <div id="identifierWrapper" class="pt-2 hidden space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-yt-text-secondary" data-i18n="common.identifier">Identifier:</span>
                                    <a id="createdIdentifier" href="#" target="_blank" class="text-link font-mono text-xs sm:text-sm"></a>
                                    <input type="hidden" id="identifierValue" />
                                    <button type="button" class="btn-secondary px-2 py-1 text-xs" onclick="copyIdentifier()" data-i18n="common.buttons.copy_id">Copy ID</button>
                                </div>
                                <p class="text-xs text-yt-text-secondary" data-i18n="files.create.result.identifier_note">Share this short ID with support/admins for moderation. Never share the key if you want to keep the file private.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Modal -->
        <div id="qrModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60">
            <div class="relative content-card p-0 max-w-md w-full mx-4 overflow-hidden">
                <div class="px-5 py-4 border-b border-yt-border flex items-center justify-between bg-yt-elevated/60">
                    <div>
                        <h4 class="text-base font-medium" data-i18n="common.qr.scan_to_download">Scan to download</h4>
                        <p class="text-xs text-yt-text-secondary" data-i18n="common.qr.use_camera">Use your phone camera to open the link</p>
                    </div>
                    <button class="btn-secondary px-2 py-1 text-xs" onclick="hideQr()" data-i18n="common.buttons.close">Close</button>
                </div>
                <div class="p-6">
                    <div class="mx-auto rounded-xl p-4 bg-yt-bg border border-yt-border w-fit">
                        <canvas id="qrCanvas" class="block"></canvas>
                    </div>
                    <div class="mt-4 flex items-center gap-2">
                        <input id="qrUrl" class="flex-1 px-3 py-2 bg-yt-bg border border-yt-border rounded text-yt-text font-mono text-xs" readonly />
                        <button type="button" class="btn-primary px-3 py-2 text-xs" onclick="copyQrUrl()" data-i18n="common.buttons.copy">Copy</button>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                        <button type="button" class="btn-secondary px-3 py-2" onclick="downloadQrPng()" data-i18n="common.buttons.download_png">Download PNG</button>
                        <button type="button" class="btn-secondary px-3 py-2" onclick="openInNewTab()" data-i18n="common.buttons.open_link">Open Link</button>
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
 function toHex(u8) { return Array.from(u8).map(b => b.toString(16).padStart(2,'0')).join(''); }
 function u8ToB64(u8) {
   const arr = (u8 instanceof Uint8Array) ? u8 : new Uint8Array(u8);
   if (window.WebCryptoWrapper?.arrayBufferToBase64) return window.WebCryptoWrapper.arrayBufferToBase64(arr);
   let bin = '';
   for (let i = 0; i < arr.length; i += 0x8000) bin += String.fromCharCode.apply(null, arr.subarray(i, i + 0x8000));
   return btoa(bin);
 }
 function b64ToU8(b64) {
   try {
     if (window.WebCryptoWrapper?.base64ToArrayBuffer) return new Uint8Array(window.WebCryptoWrapper.base64ToArrayBuffer(b64));
     const bin = atob(b64); const out = new Uint8Array(bin.length);
     for (let i = 0; i < bin.length; i++) out[i] = bin.charCodeAt(i);
     return out;
   } catch(_) { return null; }
 }
 document.addEventListener('DOMContentLoaded', function() {
   if (window.__filesCreateBound) return; window.__filesCreateBound = true;
   const formEl = document.getElementById('fileForm');
   const submitBtn = document.getElementById('submitBtn');
  const IS_AUTH = Boolean(document.getElementById('is_private'));
   if (!formEl || !submitBtn) { console.warn('files/create: #fileForm or #submitBtn not found'); return; }
   // Prevent Enter key from submitting the form
   formEl.addEventListener('submit', function(e) { e.preventDefault(); });

   // Dropzone + selection preview + progress
   const dropzone = document.getElementById('dropzone');
   const fileInput = document.getElementById('fileInput');
   const selectedFileWrap = document.getElementById('selectedFile');
   const selectedFileName = document.getElementById('selectedFileName');
   const selectedFileMeta = document.getElementById('selectedFileMeta');
   const progressWrap = document.getElementById('progressWrap');
   const progressBar = document.getElementById('progressBar');
   const progressLabel = document.getElementById('progressLabel');
   let currentFile = null;

   function setSelectedFile(f) {
     currentFile = f || null;
     if (!selectedFileWrap) return;
     if (currentFile) {
       selectedFileWrap.classList.remove('hidden');
       selectedFileName.textContent = currentFile.name || 'file';
       const kb = (currentFile.size/1024).toFixed(1);
       selectedFileMeta.textContent = `${kb} KB` + (currentFile.type ? ` • ${currentFile.type}` : '');
     } else {
       selectedFileWrap.classList.add('hidden');
       selectedFileName.textContent = '';
       selectedFileMeta.textContent = '';
     }
   }

   function setProgress(pct, label) {
     if (!progressWrap || !progressBar || !progressLabel) return;
     progressWrap.classList.remove('hidden');
     const clamped = Math.max(0, Math.min(100, pct|0));
     progressBar.style.width = clamped + '%';
     if (label) progressLabel.textContent = label;
   }

   if (dropzone) {
     dropzone.addEventListener('click', () => { fileInput && fileInput.click(); });
     ['dragenter','dragover'].forEach(ev => dropzone.addEventListener(ev, (e) => { e.preventDefault(); dropzone.classList.add('bg-yt-elevated/55'); }));
     ['dragleave','dragend','drop'].forEach(ev => dropzone.addEventListener(ev, () => { dropzone.classList.remove('bg-yt-elevated/55'); }));
     dropzone.addEventListener('drop', (e) => {
       e.preventDefault();
       const f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
       if (f) setSelectedFile(f);
     });
   }

   if (fileInput) {
     fileInput.addEventListener('change', () => {
       const f = fileInput.files && fileInput.files[0];
       if (f) setSelectedFile(f);
     });
   }

   submitBtn.addEventListener('click', async function() {
     const file = currentFile || (fileInput && fileInput.files ? fileInput.files[0] : null);
     
     if (!file) {
         alert('Please select a file');
         return;
     }
     
     if (file.size > 160 * 1024 * 1024) {
         alert('File too large (max 160MB)');
         return;
     }
     
     const expiresIn = document.getElementById('expires_in').value;
     const viewLimit = document.getElementById('view_limit').value;
     const isPrivate = document.getElementById('is_private') ? document.getElementById('is_private').checked : false;
     const password = document.getElementById('password')?.value || '';
     const passwordHint = document.getElementById('password_hint')?.value || '';

     submitBtn.disabled = true;
     submitBtn.textContent = 'Encrypting...';
     setProgress(10, 'Encrypting…');

     try {
         const arrayBuffer = await file.arrayBuffer();
         const fileData = new Uint8Array(arrayBuffer);
         let key, encrypted, commitments = {}, zk = null;
         if (window.SecureEncryption && typeof window.SecureEncryption.generateSecureKey === 'function') {
             key = await window.SecureEncryption.generateSecureKey('AES-GCM', { extractable: true });
             const zkArtifacts = (window.ZK_ARTIFACTS && (window.ZK_ARTIFACTS.file || window.ZK_ARTIFACTS.common)) || null;
             ({ encrypted, commitments, zk } = await window.SecureEncryption.encryptWithZK(
                 fileData,
                 key,
                 'AES-GCM',
                 {},
                 zkArtifacts ? { wasmUrl: zkArtifacts.wasmUrl, zkeyUrl: zkArtifacts.zkeyUrl, loaderOptions: zkArtifacts.loaderOptions || {} } : {}
             ));
         } else {
             // Fallback to native WebCrypto AES-GCM if SecureEncryption is unavailable
             key = await crypto.subtle.generateKey({ name: 'AES-GCM', length: 256 }, true, ['encrypt','decrypt']);
             const iv = crypto.getRandomValues(new Uint8Array(12));
             const ctBuf = await crypto.subtle.encrypt({ name: 'AES-GCM', iv }, key, fileData);
             encrypted = { algorithm: 'AES-GCM', iv, ciphertext: new Uint8Array(ctBuf), timestamp: Date.now() };
             // Optional commitment hash for diagnostics
             try {
                 const hashBuf = await crypto.subtle.digest('SHA-256', encrypted.ciphertext);
                 commitments.ciphertextHash = new Uint8Array(hashBuf);
             } catch (_) {}
         }
         // Export raw AES key for KEM wrapping (if recipient set)
         let rawKey;
         try {
             rawKey = window.SecureEncryption && typeof window.SecureEncryption.exportKey === 'function'
                 ? await window.SecureEncryption.exportKey(key)
                 : new Uint8Array(await crypto.subtle.exportKey('raw', key));
         } catch (_) { rawKey = null; }

         // Attempt KEM wrapping for recipient (optional)
         const recipient = (document.getElementById('recipient_username')?.value || '').trim();
         let kem = null; let usedPrekeyKid = null;
         if (recipient && rawKey) {
             try {
                 // fetch prekey
                 const prekeyResp = await fetch(`/api/users/${encodeURIComponent(recipient)}/prekey`);
                 if (prekeyResp.ok) {
                     const pk = await prekeyResp.json();
                     // Ensure kyber is loaded (PostQuantumKEM)
                     if (!window.PostQuantumKEM) { /* kyber is loaded by app.js on files pages */ }
                     const pkBytes = b64ToU8(pk.public_key);
                     if (window.PostQuantumKEM && pkBytes) {
                         const { ciphertext, sharedSecret } = await window.PostQuantumKEM.encapsulate(pkBytes, pk.alg || 'ML-KEM-512');
                         const wrapped = await window.PostQuantumKEM.encryptWithSharedSecret(rawKey, sharedSecret);
                         kem = {
                             alg: pk.alg || 'ML-KEM-512',
                             kid: pk.kid || null,
                             ctB64: u8ToB64(ciphertext),
                             wrappedB64: u8ToB64(wrapped)
                         };
                         if (pk.kid) usedPrekeyKid = pk.kid;
                     }
                 }
             } catch (_) { kem = null; }
         }

         const formData = new FormData();
         formData.append('original_filename', file.name);
         formData.append('mime_type', file.type || 'application/octet-stream');
         formData.append('iv', JSON.stringify(Array.from(encrypted.iv)));
         formData.append('cipher_file', new Blob([encrypted.ciphertext], { type: 'application/octet-stream' }), file.name + '.bin');
         formData.append('expires_in', expiresIn);
         formData.append('is_private', isPrivate ? '1' : '0');
         if (viewLimit) formData.append('view_limit', viewLimit);
         if (password) formData.append('password', password);
         if (passwordHint) formData.append('password_hint', passwordHint);
         if (kem && recipient) formData.append('recipient_username', recipient);
         if (kem) {
             formData.append('kem_alg', kem.alg);
             if (kem.kid) formData.append('kem_kid', kem.kid);
             if (kem.ctB64) formData.append('kem_ct', kem.ctB64);
             if (kem.wrappedB64) formData.append('kem_wrapped_key', kem.wrappedB64);
         }
         try {
             const ctHash = commitments.ciphertextHash || (await (async () => {
                 try { const h = await crypto.subtle.digest('SHA-256', encrypted.ciphertext); return new Uint8Array(h); } catch (_) { return null; }
             })());
             if (ctHash) formData.append('ciphertext_hash', Array.from(ctHash).map(b => b.toString(16).padStart(2,'0')).join(''));
         } catch (_) {}
         
         const response = await fetch('{{ route('files.store', [], false) }}', {
             method: 'POST',
             headers: {
                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
             },
             body: formData
         });

         const result = await response.json();
         
         if (result.success) {
             const identifier = result.identifier;
             const rawKey = window.SecureEncryption && typeof window.SecureEncryption.exportKey === 'function'
                 ? await window.SecureEncryption.exportKey(key)
                 : new Uint8Array(await crypto.subtle.exportKey('raw', key));
             const keyHex = Array.from(rawKey, b => b.toString(16).padStart(2, '0')).join('');
             const downloadUrl = result.url + '#' + keyHex;
             
             document.getElementById('downloadUrl').value = downloadUrl;
             document.getElementById('result').classList.remove('hidden');
             const idEl = document.getElementById('createdIdentifier');
             if (idEl && identifier) {
                 idEl.textContent = identifier;
                 idEl.href = result.url;
                 document.getElementById('identifierWrapper')?.classList.remove('hidden');
                 document.getElementById('identifierValue').value = identifier;
             }
             
             setProgress(100, 'Done');
            formEl.reset();
            try { setSelectedFile(null); } catch (_) {}
            // Mark prekey used if we used one (auth-only)
            try {
              if (IS_AUTH && usedPrekeyKid && kem && kem.ctB64) {
                fetch('/api/prekeys/mark-used', { method:'POST', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: JSON.stringify({ kid: usedPrekeyKid }) });
              }
            } catch (_) {}
             
             try {
                const payload = {
                    encrypted: { algorithm: encrypted.algorithm, iv: Array.from(encrypted.iv), timestamp: encrypted.timestamp },
                    commitments: { ciphertextHash: toHex(commitments.ciphertextHash || new Uint8Array()), additionalDataHash: null },
                    zk: zk || null,
                    ref: { type: 'file', identifier },
                    recipient: document.getElementById('recipient_username')?.value || null
                };
                fetch('/api/zk/encryption/submit', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) }).catch(() => {});
            } catch (e) { console.warn('ZK submit failed:', e); }
        } else {
            alert('Error uploading file: ' + (result.message || 'Unknown error'));
        }
     } catch (error) {
         console.error('Encryption error:', error);
         alert('Encryption or upload failed: ' + (error?.message || String(error)));
     } finally {
         submitBtn.disabled = false;
     }
 });
  });

 function copyToClipboard(e) {
     const urlInput = document.getElementById('downloadUrl');
     if (!urlInput) return;
     urlInput.select();
     urlInput.setSelectionRange(0, 99999);
     try {
         navigator.clipboard.writeText(urlInput.value);
         const button = e && e.target;
         if (button) {
             const originalText = button.textContent;
             button.textContent = 'Copied!';
             setTimeout(() => { button.textContent = originalText; }, 2000);
         }
     } catch (err) {
         console.error('Failed to copy: ', err);
     }
 }

 function copyIdentifier() {
     const idValue = document.getElementById('identifierValue')?.value;
     if (!idValue) return;
     navigator.clipboard.writeText(idValue).then(() => {
         const copyBtn = document.querySelector('#identifierWrapper button');
         const original = copyBtn.textContent;
         copyBtn.textContent = 'Copied!';
         setTimeout(() => copyBtn.textContent = original, 2000);
     }).catch(() => alert('Could not copy identifier.'));
 }

 const privateToggle = document.getElementById('is_private_toggle');
 const knob = document.getElementById('is_private_knob');
 const hiddenPrivateInput = document.getElementById('is_private');
 const privateLabel = privateToggle ? privateToggle.parentElement?.querySelector('.private-label') : null;

 if (privateToggle && knob && hiddenPrivateInput) {
     const setPrivateState = (next) => {
         privateToggle.setAttribute('aria-checked', String(next));
         privateToggle.classList.toggle('active', next);
         hiddenPrivateInput.checked = next;
         if (privateLabel) {
             privateLabel.classList.toggle('text-yt-accent', next);
             privateLabel.classList.toggle('font-medium', next);
         }
     };

     // Initialize toggle based on hidden input state
     setPrivateState(hiddenPrivateInput.checked);

     privateToggle.addEventListener('click', (e) => {
         e.preventDefault();
         const next = !(privateToggle.getAttribute('aria-checked') === 'true');
         setPrivateState(next);

         privateToggle.classList.add('private-pulse');
         privateToggle.style.transform = 'scale(0.95)';
         setTimeout(() => {
             privateToggle.style.transform = '';
             privateToggle.classList.remove('private-pulse');
         }, 300);

         if (navigator.vibrate) {
             try { navigator.vibrate(50); } catch (_) {}
         }
     });

     privateToggle.addEventListener('keydown', (e) => {
         if (e.key === ' ' || e.key === 'Enter') {
             e.preventDefault();
             privateToggle.click();
         }
     });

     formEl.addEventListener('reset', () => {
         setPrivateState(false);
     });
 }

 async function shareLink() {
     const url = document.getElementById('downloadUrl').value;
     if (navigator.share) {
         try {
             await navigator.share({ title: 'DailyForever', text: 'Download this secure file', url });
         } catch (_) {}
     } else {
         copyToClipboard({ target: { textContent: 'Copy' } });
         alert('Link copied to clipboard');
     }
 }

 function showQr() {
     const url = document.getElementById('downloadUrl').value;
     const modal = document.getElementById('qrModal');
     const canvas = document.getElementById('qrCanvas');
     const qrUrl = document.getElementById('qrUrl');
     qrUrl.value = url;
     modal.classList.remove('hidden');
     modal.classList.add('flex');
     if (window.QRCode) {
         QRCode.toCanvas(canvas, url, { width: 240, margin: 1, color: { dark: '#ffffff', light: '#0b0b0c' } });
     }
 }

 function hideQr() {
     const modal = document.getElementById('qrModal');
     modal.classList.add('hidden');
     modal.classList.remove('flex');
 }

 function copyQrUrl() {
     const input = document.getElementById('qrUrl');
     input.select();
     input.setSelectionRange(0, 99999);
     try { navigator.clipboard.writeText(input.value); } catch (_) {}
 }

 function downloadQrPng() {
     const canvas = document.getElementById('qrCanvas');
     const link = document.createElement('a');
     link.href = canvas.toDataURL('image/png');
     link.download = 'qr.png';
     link.click();
 }

 function openInNewTab() {
     const url = document.getElementById('qrUrl').value;
     window.open(url, '_blank');
 }
</script>
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
@endsection