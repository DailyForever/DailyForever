// Page script for resources/views/paste/create.blade.php
// Extracted from inline <script> to enable strict CSP

(function initPasteCreate() {
  const root = document.getElementById('pasteCreateRoot');
  if (!root) return; // run only on create page

  const PASTE_STORE_URL = root.dataset.pasteStoreUrl;
  const IS_AUTH = root.dataset.auth === '1';

  function toHex(u8) { return Array.from(u8).map(b => b.toString(16).padStart(2, '0')).join(''); }

  async function isValidWasm(url) {
    try {
      if (!url) return false;
      const res = await fetch(url, { cache: 'no-store' });
      if (!res.ok) return false;
      const buf = await res.arrayBuffer();
      const u8 = new Uint8Array(buf);
      return u8.length >= 4 && u8[0] === 0x00 && u8[1] === 0x61 && u8[2] === 0x73 && u8[3] === 0x6d;
    } catch (_) {
      return false;
    }
  }

  const form = document.getElementById('pasteForm');
  // Editor enhancements: line numbers, tab-to-spaces, submit shortcut
  const contentEl = document.getElementById('content');
  const gutterEl = document.getElementById('lineGutter');
  const charCountEl = document.getElementById('charCount');
  const wrapToggleBtn = document.getElementById('wrapToggle');
  const tabSizeSel = document.getElementById('tabSize');
  function updateGutter() {
    if (!contentEl || !gutterEl) return;
    const val = contentEl.value || '';
    const lines = 1 + (val.match(/\n/g) || []).length;
    let buf = '';
    for (let i = 1; i <= lines; i++) buf += i + "\n";
    gutterEl.textContent = buf;
    gutterEl.scrollTop = contentEl.scrollTop;
  }
  if (contentEl && gutterEl) {
    contentEl.addEventListener('input', () => {
      updateGutter();
      if (charCountEl) charCountEl.textContent = (contentEl.value.length || 0) + ' chars';
    });
    contentEl.addEventListener('scroll', () => { gutterEl.scrollTop = contentEl.scrollTop; });
    // Tab inserts 2 spaces (common pastebin UX)
    contentEl.addEventListener('keydown', (e) => {
      if (e.key === 'Tab') {
        e.preventDefault();
        const el = e.target;
        const start = el.selectionStart, end = el.selectionEnd;
        const before = el.value.substring(0, start);
        const after = el.value.substring(end);
        const size = parseInt(tabSizeSel?.value || '2', 10);
        const insert = ' '.repeat(Math.max(1, Math.min(8, size)));
        el.value = before + insert + after;
        el.selectionStart = el.selectionEnd = start + insert.length;
        updateGutter();
      } else if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        // Submit shortcut
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn && !submitBtn.disabled) submitBtn.click();
      }
    });
    // Initial
    setTimeout(updateGutter, 0);
    if (charCountEl) setTimeout(() => { charCountEl.textContent = (contentEl.value.length || 0) + ' chars'; }, 0);
  }

  // Wrap toggle for textarea (soft vs off)
  if (wrapToggleBtn && contentEl) {
    let wrapped = true; // default soft wrap
    wrapToggleBtn.addEventListener('click', () => {
      wrapped = !wrapped;
      if (wrapped) {
        contentEl.setAttribute('wrap', 'soft');
        wrapToggleBtn.textContent = 'No Wrap';
      } else {
        contentEl.setAttribute('wrap', 'off');
        wrapToggleBtn.textContent = 'Wrap';
      }
      contentEl.focus();
    });
    // set default
    contentEl.setAttribute('wrap', 'soft');
    wrapToggleBtn.textContent = 'No Wrap';
  }
  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const submitBtn = document.getElementById('submitBtn');
      const content = document.getElementById('content').value;
      const expiresIn = document.getElementById('expires_in').value;
      const viewOnce = document.getElementById('view_once').value === 'yes';
      const isPrivate = document.getElementById('is_private') ? document.getElementById('is_private').checked : false;
      const recipient = document.getElementById('recipient_username') ? document.getElementById('recipient_username').value.trim() : '';

      if (!content.trim()) { alert('Please enter some content'); return; }

      submitBtn.disabled = true;
      submitBtn.textContent = 'Encrypting...';

      try {
        // Generate encryption key (extractable for share URL)
        const key = await window.SecureEncryption.generateSecureKey('AES-GCM', { extractable: true });

        // Encrypt content and optionally produce a ZK proof
        const plaintext = new TextEncoder().encode(content);
        let zkArtifacts = (window.ZK_ARTIFACTS && (window.ZK_ARTIFACTS.paste || window.ZK_ARTIFACTS.common)) || null;
        if (zkArtifacts && !(await isValidWasm(zkArtifacts.wasmUrl))) {
          console.warn('WASM invalid or not reachable, skipping proof generation');
          zkArtifacts = null;
        }
        let encrypted, commitments, zk;
        try {
          ({ encrypted, commitments, zk } = await window.SecureEncryption.encryptWithZK(
            plaintext,
            key,
            'AES-GCM',
            {},
            zkArtifacts ? { wasmUrl: zkArtifacts.wasmUrl, zkeyUrl: zkArtifacts.zkeyUrl, loaderOptions: zkArtifacts.loaderOptions || {} } : {}
          ));
        } catch (zkErr) {
          console.warn('encryptWithZK failed, falling back to plain encrypt:', zkErr);
          encrypted = await window.SecureEncryption.encrypt(plaintext, key, 'AES-GCM', {});
          const plaintextHash = await window.SecureEncryption.hash(plaintext, 'SHA-256');
          const ciphertextHash = await window.SecureEncryption.hash(encrypted.ciphertext, 'SHA-256');
          commitments = {
            plaintextHash,
            ciphertextHash,
            iv: encrypted.iv,
            algorithm: 'AES-GCM',
            timestamp: encrypted.timestamp,
            additionalDataHash: null,
          };
          zk = null;
        }

        // Optional PQ addressed mode
        let kem = null;
        let usedPrekeyKid = null;
        const exportedKey = await window.SecureEncryption.exportKey(key);
        if (recipient) {
          try {
            let pk = null;
            let fromPrekey = false;
            // Try prekey first
            const prekeyResp = await fetch(`/api/users/${encodeURIComponent(recipient)}/prekey`);
            if (prekeyResp.ok) { pk = await prekeyResp.json(); fromPrekey = true; }
            // Fallback to long-term keypair public key
            if (!pk) {
              const kpResp = await fetch(`/api/keypairs/public/${encodeURIComponent(recipient)}`);
              if (kpResp.ok) {
                const kp = await kpResp.json();
                pk = { kid: kp.key_id, alg: 'ML-KEM-512', public_key: kp.public_key };
              }
            }
            if (pk) {
              // Dynamically load PostQuantumKEM (Kyber/ML-KEM) only when needed
              if (!window.PostQuantumKEM) {
                try { await import('../kyber.js'); } catch (_) {}
              }
              // Decode base64 public key to bytes
              let pkBytes;
              try {
                if (window.WebCryptoWrapper && window.WebCryptoWrapper.base64ToArrayBuffer) {
                  pkBytes = new Uint8Array(window.WebCryptoWrapper.base64ToArrayBuffer(pk.public_key));
                } else {
                  const bin = atob(pk.public_key);
                  const arr = new Uint8Array(bin.length);
                  for (let i = 0; i < bin.length; i++) arr[i] = bin.charCodeAt(i);
                  pkBytes = arr;
                }
              } catch (_) { pkBytes = null; }
              // Encapsulate and wrap AES key
              if (window.PostQuantumKEM && pkBytes) {
                try {
                  const { ciphertext, sharedSecret } = await window.PostQuantumKEM.encapsulate(pkBytes, pk.alg || 'ML-KEM-512');
                  const ctU8 = ciphertext instanceof Uint8Array ? ciphertext : new Uint8Array(ciphertext);
                  const ctB64 = (window.WebCryptoWrapper && window.WebCryptoWrapper.arrayBufferToBase64)
                    ? window.WebCryptoWrapper.arrayBufferToBase64(ctU8)
                    : btoa(String.fromCharCode(...ctU8));
                  const wrapped = await window.PostQuantumKEM.encryptWithSharedSecret(exportedKey, sharedSecret);
                  const wrappedB64 = (window.WebCryptoWrapper && window.WebCryptoWrapper.arrayBufferToBase64)
                    ? window.WebCryptoWrapper.arrayBufferToBase64(wrapped)
                    : btoa(String.fromCharCode(...new Uint8Array(wrapped)));
                  kem = { alg: pk.alg || 'ML-KEM-512', kid: pk.kid || null, ct: ctB64, wrappedKey: wrappedB64 };
                  if (fromPrekey && pk.kid) usedPrekeyKid = pk.kid;
                } catch (_) {
                  kem = { alg: pk.alg || 'ML-KEM-512', kid: pk.kid || null, ct: null, wrappedKey: null };
                }
              } else {
                kem = { alg: pk.alg || 'ML-KEM-512', kid: pk.kid || null, ct: null, wrappedKey: null };
              }
            }
          } catch (_) { }
        }

        // Build payload
        const payload = {
          encrypted_content: JSON.stringify(Array.from(encrypted.ciphertext)),
          iv: JSON.stringify(Array.from(encrypted.iv)),
          expires_in: expiresIn,
          view_limit: viewOnce ? 1 : null,
          is_private: isPrivate ? 1 : 0,
          recipient_username: recipient || null,
          kem_alg: kem ? kem.alg : null,
          kem_kid: kem ? kem.kid : null,
          kem_ct: kem && kem.ct ? kem.ct : null,
          kem_wrapped_key: kem && kem.wrappedKey ? kem.wrappedKey : null,
          password: document.getElementById('password')?.value || '',
          password_hint: document.getElementById('password_hint')?.value || null,
          encryption_key: IS_AUTH ? Array.from(exportedKey, b => b.toString(16).padStart(2, '0')).join('') : null
        };

        // Send to server
        const response = await fetch(PASTE_STORE_URL, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();
        if (result.success) {
          const identifier = result.identifier || result.url.split('/paste/')[1]?.split('#')[0]?.split('?')[0];
          // Add key to URL fragment
          const keyHex = Array.from(exportedKey, b => b.toString(16).padStart(2, '0')).join('');
          const langSel = document.getElementById('syntax_select');
          const langVal = (langSel && langSel.value && langSel.value !== 'auto') ? langSel.value : '';
          const baseUrl = langVal ? (result.url + '?lang=' + encodeURIComponent(langVal)) : result.url;
          const shareUrl = baseUrl + '#' + keyHex;

          const shareUrlEl = document.getElementById('shareUrl');
          if (shareUrlEl) shareUrlEl.value = shareUrl;
          document.getElementById('result')?.classList.remove('hidden');
          const idEl = document.getElementById('createdIdentifier');
          if (idEl && identifier) {
            idEl.textContent = identifier;
            idEl.href = result.url;
            document.getElementById('identifierWrapper')?.classList.remove('hidden');
            const idHidden = document.getElementById('identifierValue');
            if (idHidden) idHidden.value = identifier;
          }

          // Reset form
          this.reset();

          // Mark prekey used (fire and forget, auth-only)
          if (IS_AUTH && usedPrekeyKid && kem && kem.ct) {
            try {
              fetch('/api/prekeys/mark-used', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ kid: usedPrekeyKid })
              });
            } catch (_) {}
          }

          // Submit ZK bundle (non-blocking) to minimal-trust endpoint, linking to paste identifier
          try {
            const zkSubmit = async () => {
              try {
                console.log('[ZK Submit] Starting with zk:', {
                  hasZk: !!zk,
                  hasProof: !!(zk && zk.proof),
                  hasPublicSignals: !!(zk && zk.publicSignals),
                  publicSignalsCount: zk && zk.publicSignals ? zk.publicSignals.length : 0
                });
                
                // Encrypt ZK proof envelope so server only sees ciphertext
                let zkEnc = null;
                if (zk && zk.proof) {
                  console.log('[ZK Submit] Encrypting proof envelope');
                  const envIv = crypto.getRandomValues(new Uint8Array(12));
                  const envAd = new TextEncoder().encode('zkp:proof:v1');
                  const envBody = new TextEncoder().encode(JSON.stringify({ proof: zk.proof, publicSignals: zk.publicSignals }));
                  const envCt = await crypto.subtle.encrypt({ name: 'AES-GCM', iv: envIv, additionalData: envAd }, key, envBody);
                  const envCtB64 = (window.WebCryptoWrapper && window.WebCryptoWrapper.arrayBufferToBase64)
                    ? window.WebCryptoWrapper.arrayBufferToBase64(envCt)
                    : btoa(String.fromCharCode(...new Uint8Array(envCt)));
                  zkEnc = { alg: 'AES-GCM', iv: Array.from(envIv), ad: 'zkp:proof:v1', ct: envCtB64 };
                  console.log('[ZK Submit] Proof envelope encrypted');
                }
                
                const payload = {
                  encrypted: {
                    algorithm: encrypted.algorithm,
                    iv: Array.from(encrypted.iv),
                    timestamp: encrypted.timestamp,
                  },
                  commitments: {
                    ciphertextHash: toHex(commitments.ciphertextHash),
                    additionalDataHash: commitments.additionalDataHash ? toHex(commitments.additionalDataHash) : null,
                    commit: commitments.commit || (zk && zk.publicSignals ? zk.publicSignals[0] : null),
                  },
                  zk: zkEnc ? { proof: null, publicSignals: null, enc: zkEnc } : (zk || null),
                  ref: { type: 'paste', identifier }
                };
                
                console.log('[ZK Submit] Sending payload with zk field:', {
                  hasZkField: !!payload.zk,
                  zkFieldType: payload.zk ? (payload.zk.enc ? 'encrypted' : 'plain') : 'none',
                  hasCommit: !!payload.commitments.commit
                });
                
                const response = await fetch('/api/zk/encryption/submit', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify(payload)
                });
                
                console.log('[ZK Submit] Response:', {
                  ok: response.ok,
                  status: response.status,
                  statusText: response.statusText
                });
                
                if (!response.ok) {
                  const text = await response.text();
                  console.error('[ZK Submit] Server error:', text);
                }
              } catch (e) {
                console.error('[ZK Submit] Failed:', e);
              }
            };
            zkSubmit();
          } catch (e) {
            console.warn('ZK submit failed:', e);
          }
        } else {
          alert('Error creating paste');
        }
      } catch (error) {
        console.error('Encryption error:', error);
        alert('Error encrypting content');
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Create Secure Paste';
      }
    });
  }


  // Private toggle switch behavior with enhanced animations
  const privateToggle = document.getElementById('is_private_toggle');
  const knob = document.getElementById('is_private_knob');
  if (privateToggle && knob) {
    privateToggle.addEventListener('click', (e) => {
      e.preventDefault();
      const checked = privateToggle.getAttribute('aria-checked') === 'true';
      const next = !checked;
      privateToggle.setAttribute('aria-checked', String(next));
      const cb = document.getElementById('is_private');
      if (cb) cb.checked = next;
      // Remove any scaling/enlarging on click for a stable UI
      privateToggle.classList.remove('private-pulse');
      privateToggle.style.transform = '';
      if (navigator.vibrate) { try { navigator.vibrate(30); } catch (_) {} }
    });
    privateToggle.addEventListener('keydown', (e) => {
      if (e.key === ' ' || e.key === 'Enter') {
        e.preventDefault();
        privateToggle.click();
      }
    });
  }

  // Actions used by data-action buttons (exposed on window for paste-actions.js)
  async function copyToClipboard(e) {
    const urlInput = document.getElementById('shareUrl');
    if (!urlInput) return;
    urlInput.select();
    urlInput.setSelectionRange(0, 99999);
    let ok = false;
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(urlInput.value);
        ok = true;
      }
    } catch (_) { /* fallback below */ }
    if (!ok) {
      try {
        ok = document.execCommand && document.execCommand('copy');
      } catch (_) { ok = false; }
    }
    const button = e?.target?.closest('button');
    if (button) {
      const originalText = button.textContent;
      button.textContent = ok ? 'Copied!' : 'Copy failed';
      setTimeout(() => { button.textContent = originalText; }, 2000);
    }
    if (!ok) {
      console.error('Copy to clipboard failed');
    }
  }

  function copyIdentifier() {
    const idValue = document.getElementById('identifierValue')?.value;
    if (!idValue) return;
    navigator.clipboard.writeText(idValue).then(() => {
      const copyBtn = document.querySelector('#identifierWrapper button');
      if (!copyBtn) return;
      const original = copyBtn.textContent;
      copyBtn.textContent = 'Copied!';
      setTimeout(() => copyBtn.textContent = original, 2000);
    }).catch(() => alert('Could not copy identifier.'));
  }

  async function shareLink() {
    const url = document.getElementById('shareUrl')?.value || '';
    if (!url) return;
    if (navigator.share) {
      try { await navigator.share({ title: 'DailyForever', text: 'Open this secure paste', url }); } catch (_) {}
    } else {
      copyToClipboard({ target: { textContent: 'Copy' } });
      alert('Link copied to clipboard');
    }
  }

  function showQr() {
    const url = document.getElementById('shareUrl')?.value || '';
    const modal = document.getElementById('qrModal');
    const canvas = document.getElementById('qrCanvas');
    const qrUrl = document.getElementById('qrUrl');
    if (qrUrl) qrUrl.value = url;
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
    if (window.QRCode && canvas) {
      window.QRCode.toCanvas(canvas, url, { width: 240, margin: 1, color: { dark: '#ffffff', light: '#0b0b0c' } });
    }
  }

  function hideQr() {
    const modal = document.getElementById('qrModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  function copyQrUrl() {
    const input = document.getElementById('qrUrl');
    if (!input) return;
    input.select();
    input.setSelectionRange(0, 99999);
    try { navigator.clipboard.writeText(input.value); } catch (_) { }
  }

  function downloadQrPng() {
    const canvas = document.getElementById('qrCanvas');
    if (!canvas) return;
    const link = document.createElement('a');
    link.href = canvas.toDataURL('image/png');
    link.download = 'qr.png';
    link.click();
  }

  function openInNewTab() {
    const url = document.getElementById('qrUrl')?.value || '';
    if (url) window.open(url, '_blank');
  }

  Object.assign(window, {
    copyToClipboard,
    copyIdentifier,
    shareLink,
    showQr,
    hideQr,
    copyQrUrl,
    downloadQrPng,
    openInNewTab,
  });
})();
