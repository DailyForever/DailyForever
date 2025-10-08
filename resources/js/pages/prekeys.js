// Rebuilt client for /prekeys with full feature set
// - Local Kyber (ML-KEM) keypair generation
// - Secret key storage in localStorage
// - JSON bundle compose, copy, download, clear, drag&drop
// - Upload bundle to server
// - Export/import secrets via modal

'use strict';
(function initPrekeysPage() {
  const root = document.getElementById('prekeysRoot');
  if (!root) return; // run only on /prekeys

  // DOM refs
  const algSel = document.getElementById('prekeyAlg');
  const cntInput = document.getElementById('prekeyCount');
  const genBtn = document.getElementById('genPrekeysBtn');
  const bundleTa = document.getElementById('bundle');
  const bundleStats = document.getElementById('bundleStats');
  const copyBtn = document.getElementById('copyBundleBtn');
  const downloadBtn = document.getElementById('downloadBundleBtn');
  const clearBtn = document.getElementById('clearBundleBtn');
  const quickCountBtns = Array.from(document.querySelectorAll('[data-quick-count]'));
  const dropzone = document.getElementById('dropzone');
  const uploadForm = document.getElementById('uploadForm');
  const bundleField = document.getElementById('bundleField');

  // Secrets import/export elements
  const exportSecretsBtn = document.getElementById('exportSecretsBtn');
  const importSecretsBtn = document.getElementById('importSecretsBtn');
  const importSecretsBtnHero = document.getElementById('importSecretsBtnHero');
  const secretsModal = document.getElementById('secretsModal');
  const secretsClose = document.getElementById('closeSecretsBtn');
  const secretsTa = document.getElementById('secretsJsonTa');
  const doImportSecretsBtn = document.getElementById('doImportSecretsBtn');

  // Policy constants
  const ALLOWED_ALGS = new Set(['ML-KEM-512','ML-KEM-768','ML-KEM-1024']);
  const DEFAULT_ALG = 'ML-KEM-768';
  const MAX_BUNDLE_BYTES = 1024 * 1024; // 1 MiB
  const MAX_UPLOAD_KEYS = 1000; // client-side guard; server also validates
  const MAX_IMPORT_SECRETS = 1000;
  let isGenerating = false;

  // Utils
  function u8ToB64(u8) {
    try {
      if (window.WebCryptoWrapper && window.WebCryptoWrapper.arrayBufferToBase64) {
        return window.WebCryptoWrapper.arrayBufferToBase64(u8 instanceof Uint8Array ? u8 : new Uint8Array(u8));
      }
      const arr = (u8 instanceof Uint8Array ? u8 : new Uint8Array(u8));
      let bin = '';
      for (let i = 0; i < arr.length; i += 0x8000) {
        bin += String.fromCharCode.apply(null, arr.subarray(i, i + 0x8000));
      }
      return btoa(bin);
    } catch (_) { return null; }
  }

  async function ensureKyber() {
    if (!window.PostQuantumKEM) {
      try { await import('../kyber.js'); } catch (e) { console.warn('Kyber module load failed', e); }
    }
    return !!window.PostQuantumKEM;
  }

  function randKid(prefix = 'k') {
    const r = new Uint8Array(8);
    crypto.getRandomValues(r);
    const hex = Array.from(r, b => b.toString(16).padStart(2, '0')).join('');
    return `${prefix}-${Date.now().toString(36)}-${hex}`;
  }

  function storeSecret(kid, skB64) {
    try { localStorage.setItem(`pq.prekeys.${kid}.sk`, skB64); return true; } catch (_) { return false; }
  }

  function setGenerating(state) {
    try {
      if (!genBtn) return;
      const spinner = genBtn.querySelector('.gen-spinner');
      const label = genBtn.querySelector('.gen-label');
      genBtn.disabled = !!state;
      if (algSel) algSel.disabled = !!state;
      if (cntInput) cntInput.disabled = !!state;
      if (spinner) spinner.classList.toggle('hidden', !state);
      if (label) label.classList.toggle('opacity-50', !!state);
    } catch (_) {}
    isGenerating = !!state;
  }

  function updateBundleStats() {
    if (!bundleTa || !bundleStats) return;
    const t = (bundleTa.value || '').trim();
    if (!t) { bundleStats.textContent = '0 keys in bundle'; return; }
    try {
      const arr = JSON.parse(t);
      const n = Array.isArray(arr) ? arr.length : 0;
      bundleStats.textContent = `${n} ${n === 1 ? 'key' : 'keys'} in bundle`;
    } catch {
      bundleStats.textContent = 'Invalid JSON';
    }
  }

  async function copyText(text) {
    try { await navigator.clipboard.writeText(text); return true; } catch { return false; }
  }

  function downloadFile(filename, content, type = 'application/json') {
    const blob = new Blob([content], { type });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url; a.download = filename; a.click();
    setTimeout(() => URL.revokeObjectURL(url), 1200);
  }

  function safeAlg() {
    const sel = (algSel?.value || DEFAULT_ALG);
    return ALLOWED_ALGS.has(sel) ? sel : DEFAULT_ALG;
  }

  async function generate() {
    if (isGenerating) return; // guard concurrent clicks
    if (!window.crypto || !window.crypto.getRandomValues || !window.crypto.subtle) {
      alert('Web Crypto API is not available in this browser');
      return;
    }
    const alg = safeAlg();
    let count = parseInt(cntInput?.value || '10', 10);
    if (!Number.isFinite(count) || count < 1) count = 1; if (count > 100) count = 100;
    if (!(await ensureKyber())) { alert('Kyber module not available'); return; }

    setGenerating(true);
    const out = [];
    for (let i = 0; i < count; i++) {
      try {
        const { publicKey, secretKey } = await window.PostQuantumKEM.generateKeypair(alg);
        const kid = randKid('k');
        const pkB64 = u8ToB64(publicKey);
        const skB64 = u8ToB64(secretKey);
        if (!pkB64 || !skB64) { console.warn('Key encode failed'); continue; }
        if (!storeSecret(kid, skB64)) { console.warn('Could not store secret key locally for', kid); }
        out.push({ kid, alg, public_key: pkB64 });
      } catch (e) {
        console.warn('Generate prekey failed:', e);
      }
      // Yield to event loop periodically to keep UI responsive
      if (i % 10 === 9) { try { await new Promise(r => setTimeout(r, 0)); } catch (_) {} }
    }
    setGenerating(false);

    if (!out.length) { alert('No prekeys generated'); return; }
    if (bundleTa) {
      bundleTa.value = JSON.stringify(out, null, 2);
      updateBundleStats();
      try { bundleTa.scrollIntoView({ behavior: 'smooth', block: 'center' }); } catch (_) {}
    }
  }

  // Wire up events
  if (genBtn) genBtn.addEventListener('click', generate);

  quickCountBtns.forEach(btn => btn.addEventListener('click', () => {
    const n = parseInt(btn.getAttribute('data-quick-count') || '0', 10);
    if (Number.isFinite(n) && n > 0 && cntInput) {
      cntInput.value = String(n);
      cntInput.dispatchEvent(new Event('change'));
    }
  }));

  copyBtn?.addEventListener('click', async () => {
    const text = bundleTa?.value || '';
    if (!text) return;
    const ok = await copyText(text);
    const prev = copyBtn.textContent;
    copyBtn.textContent = ok ? 'Copied' : 'Copy failed';
    setTimeout(() => copyBtn.textContent = prev || 'Copy JSON', 1500);
  });

  downloadBtn?.addEventListener('click', () => {
    const text = bundleTa?.value || '';
    if (!text) return;
    const ts = new Date().toISOString().replace(/[:.]/g, '-');
    downloadFile(`prekeys-bundle-${ts}.json`, text);
  });

  clearBtn?.addEventListener('click', () => {
    if (bundleTa) { bundleTa.value = ''; updateBundleStats(); }
  });

  bundleTa?.addEventListener('input', updateBundleStats);
  updateBundleStats();

  // Drag & drop JSON into area
  if (dropzone) {
    dropzone.addEventListener('dragover', (e) => { e.preventDefault(); dropzone.classList.add('ring-1','ring-yt-accent'); });
    dropzone.addEventListener('dragleave', () => dropzone.classList.remove('ring-1','ring-yt-accent'));
    dropzone.addEventListener('drop', (e) => {
      e.preventDefault(); dropzone.classList.remove('ring-1','ring-yt-accent');
      const f = e.dataTransfer?.files?.[0]; if (!f) return;
      if (typeof f.size === 'number' && f.size > MAX_BUNDLE_BYTES) { alert('File is too large'); return; }
      const reader = new FileReader();
      reader.onload = () => { if (bundleTa) { bundleTa.value = String(reader.result || ''); updateBundleStats(); } };
      reader.readAsText(f);
    });
  }

  // Upload form safety: ensure JSON in hidden field and validate schema/limits
  uploadForm?.addEventListener('submit', (e) => {
    const t = (bundleTa?.value || '').trim();
    if (!t) { e.preventDefault(); alert('Bundle is empty'); return; }
    if (new Blob([t]).size > MAX_BUNDLE_BYTES) { e.preventDefault(); alert('Bundle JSON is too large'); return; }
    let arr;
    try { arr = JSON.parse(t); } catch { e.preventDefault(); alert('Invalid JSON in bundle'); return; }
    if (!Array.isArray(arr)) { e.preventDefault(); alert('Bundle must be an array'); return; }
    if (arr.length === 0) { e.preventDefault(); alert('Bundle is empty'); return; }
    if (arr.length > MAX_UPLOAD_KEYS) { e.preventDefault(); alert('Too many keys in bundle'); return; }
    // Validate shape
    for (let i = 0; i < arr.length; i++) {
      const it = arr[i] || {};
      if (typeof it.kid !== 'string' || it.kid.length < 1 || it.kid.length > 64) { e.preventDefault(); alert(`Invalid kid at index ${i}`); return; }
      if (typeof it.alg !== 'string' || !ALLOWED_ALGS.has(it.alg)) { e.preventDefault(); alert(`Invalid alg at index ${i}`); return; }
      if (typeof it.public_key !== 'string' || it.public_key.length < 16) { e.preventDefault(); alert(`Invalid public_key at index ${i}`); return; }
    }
    if (bundleField) bundleField.value = JSON.stringify(arr);
  });

  // Export secrets (localStorage -> file)
  exportSecretsBtn?.addEventListener('click', () => {
    try {
      const secrets = {};
      for (let i = 0; i < localStorage.length; i++) {
        const k = localStorage.key(i);
        if (k && k.startsWith('pq.prekeys.') && k.endsWith('.sk')) {
          const kid = k.slice('pq.prekeys.'.length, -3);
          secrets[kid] = localStorage.getItem(k);
        }
      }
      const payload = JSON.stringify({ type: 'prekey-secrets', version: 1, secrets }, null, 2);
      const ts = new Date().toISOString().replace(/[:.]/g, '-');
      downloadFile(`prekey-secrets-${ts}.json`, payload);
    } catch (e) {
      alert('Export failed');
    }
  });

  // Import secrets modal
  const showImportModal = () => { if (secretsModal) secretsModal.classList.remove('hidden'); };
  importSecretsBtn?.addEventListener('click', showImportModal);
  importSecretsBtnHero?.addEventListener('click', showImportModal);
  secretsClose?.addEventListener('click', () => secretsModal?.classList.add('hidden'));

  doImportSecretsBtn?.addEventListener('click', () => {
    try {
      const text = (secretsTa?.value || '').trim(); if (!text) return;
      const obj = JSON.parse(text);
      let count = 0;
      if (obj && obj.secrets && typeof obj.secrets === 'object') {
        const entries = Object.entries(obj.secrets);
        for (let i = 0; i < entries.length && count < MAX_IMPORT_SECRETS; i++) {
          const [kid, b64] = entries[i];
          if (typeof kid === 'string' && typeof b64 === 'string') {
            try { localStorage.setItem(`pq.prekeys.${kid}.sk`, b64); count++; } catch (_) {}
          }
        }
      } else if (Array.isArray(obj)) {
        for (let i = 0; i < obj.length && count < MAX_IMPORT_SECRETS; i++) {
          const item = obj[i];
          const kid = item?.kid; const b64 = item?.secret || item?.sk || item?.secret_key;
          if (typeof kid === 'string' && typeof b64 === 'string') {
            try { localStorage.setItem(`pq.prekeys.${kid}.sk`, b64); count++; } catch (_) {}
          }
        }
      }
      secretsModal?.classList.add('hidden');
      alert(`Imported ${count} secret ${count === 1 ? 'key' : 'keys'}`);
    } catch { alert('Invalid JSON'); }
  });
})();
