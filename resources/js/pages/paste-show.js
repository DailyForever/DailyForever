// Page script for resources/views/paste/show.blade.php
// Extracted from inline <script> to enable strict CSP

(function initPasteShow() {
  const root = document.getElementById('pasteShowRoot');
  if (!root) return; // run only on show page

  const pasteData = {
    encrypted_content: root.dataset.encryptedContent,
    iv: root.dataset.iv,
  };
  // KEM metadata (if addressed paste)
  const kemMeta = {
    alg: (root.dataset.kemAlg || '').trim(),
    kid: (root.dataset.kemKid || '').trim(),
    ctB64: (root.dataset.kemCt || '').trim(),
    wrappedB64: (root.dataset.kemWrappedKey || '').trim(),
  };
  const IS_OWNER = root.dataset.isOwner === '1';
  const OWNER_KEY = root.dataset.ownerKey || '';
  const PASSWORD_PROTECTED = root.dataset.passwordProtected === '1';

  // Helpers
  function toHexPaste(u8) { return Array.from(u8).map(b => b.toString(16).padStart(2, '0')).join(''); }
  function setZkStatusPaste(text, isError = false) {
    const el = document.getElementById('zkProofStatusPaste');
    if (!el) return;
    el.textContent = text;
    el.classList.toggle('text-yt-error', !!isError);
  }

  // ================================
  // Post-Quantum KEM decrypt (recipient)
  // ================================
  function b64ToU8(b64) {
    try {
      if (window.WebCryptoWrapper && window.WebCryptoWrapper.base64ToArrayBuffer) {
        return new Uint8Array(window.WebCryptoWrapper.base64ToArrayBuffer(b64));
      }
      const bin = atob(b64);
      const u8 = new Uint8Array(bin.length);
      for (let i = 0; i < bin.length; i++) u8[i] = bin.charCodeAt(i);
      return u8;
    } catch (_) { return null; }
  }

  async function loadKyber() {
    if (!window.PostQuantumKEM) {
      try { await import('../kyber.js'); } catch (_) {}
    }
    return !!window.PostQuantumKEM;
  }

  function loadRecipientSecret(kid) {
    if (!kid) return null;
    // Primary: prekey storage
    let b64 = null;
    try { b64 = localStorage.getItem(`pq.prekeys.${kid}.sk`) || null; } catch (_) {}
    // Fallback: long-term keypair stash
    if (!b64) { try { b64 = localStorage.getItem(`pq.keypairs.${kid}.sk`) || null; } catch (_) {}
    }
    return b64 ? b64ToU8(b64) : null;
  }

  function saveRecipientSecret(kid, b64) {
    if (!kid || !b64) return false;
    try { localStorage.setItem(`pq.prekeys.${kid}.sk`, b64); return true; } catch (_) { return false; }
  }

  async function tryKemDecryptAndDisplay() {
    try {
      if (!kemMeta || !kemMeta.alg || !kemMeta.wrappedB64 || !kemMeta.ctB64) return false;
      // Respect password gate
      if (PASSWORD_PROTECTED) {
        const gate = document.getElementById('password-gate');
        if (gate && !gate.classList.contains('hidden')) return false;
      }
      if (!(await loadKyber())) return false;
      const sk = loadRecipientSecret(kemMeta.kid);
      if (!sk) {
        const importCard = document.getElementById('kemKeyImport');
        if (importCard) importCard.classList.remove('hidden');
        return false;
      }
      const ct = b64ToU8(kemMeta.ctB64);
      const wrapped = b64ToU8(kemMeta.wrappedB64);
      if (!ct || !wrapped) return false;
      // Derive shared secret and unwrap AES key
      const shared = await window.PostQuantumKEM.decapsulate(ct, sk, kemMeta.alg || 'ML-KEM-512');
      const aesRaw = await window.PostQuantumKEM.decryptWithSharedSecret(wrapped, shared);
      const aesKey = await window.crypto.subtle.importKey('raw', aesRaw, { name: 'AES-GCM', length: 256 }, false, ['decrypt']);
      // Decrypt content
      const encryptedContent = JSON.parse(pasteData.encrypted_content);
      const iv = JSON.parse(pasteData.iv);
      const decryptedText = await SecureDecryption.decrypt(encryptedContent, iv, aesKey);
      document.getElementById('content-code').textContent = decryptedText;
      updateLineGutterFromText('lineGutterShow', decryptedText);
      document.getElementById('decrypting').classList.add('hidden');
      document.getElementById('content-container').classList.remove('hidden');
      try {
        const sel = document.getElementById('langSelectViewer');
        applyLanguage(document.getElementById('content-code'), sel?.value || 'auto');
      } catch (_) {}
      return true;
    } catch (e) {
      console.warn('KEM decrypt failed:', e);
      return false;
    }
  }

  async function saveKemSecret() {
    try {
      const kid = kemMeta?.kid || '';
      const input = document.getElementById('kemSecretB64');
      const b64 = (input?.value || '').trim();
      if (!kid || !b64) { alert('Enter secret key (base64)'); return; }
      if (!saveRecipientSecret(kid, b64)) { alert('Could not save key'); return; }
      document.getElementById('kemKeyImport')?.classList.add('hidden');
      // If password gate is not present or already unlocked, attempt decrypt
      const ok = await tryKemDecryptAndDisplay();
      if (!ok) alert('Saved key, but decryption still failed');
    } catch (e) { console.warn(e); }
  }
  function setZkCommitPaste(val) {
    const el = document.getElementById('zkCommitValue');
    if (!el) return;
    el.textContent = val ? String(val) : '—';
  }
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

  // Clipboard + UI actions
  async function copyContent(e) {
    const content = document.getElementById('content-code')?.textContent || '';
    let ok = false;
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(content);
        ok = true;
      }
    } catch (_) {}
    if (!ok) {
      try {
        const ta = document.createElement('textarea');
        ta.value = content;
        ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.focus(); ta.select();
        ok = document.execCommand && document.execCommand('copy');
        ta.remove();
      } catch (_) { ok = false; }
    }
    const button = e?.target?.closest('button');
    if (button) {
      const originalText = button.textContent;
      button.textContent = ok ? 'Copied!' : 'Copy failed';
      setTimeout(() => { button.textContent = originalText; }, 2000);
    }
  }
  async function copyIdentifier(e) {
    const id = document.getElementById('pasteIdentifier')?.textContent?.trim();
    if (!id) return;
    let ok = false;
    try {
      if (navigator.clipboard && navigator.clipboard.writeText) {
        await navigator.clipboard.writeText(id);
        ok = true;
      }
    } catch (_) {}
    if (!ok) {
      try {
        const ta = document.createElement('textarea');
        ta.value = id; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.focus(); ta.select();
        ok = document.execCommand && document.execCommand('copy');
        ta.remove();
      } catch (_) { ok = false; }
    }
    const btn = e?.target?.closest('button') || document.querySelector('button[data-action="copyIdentifier"]');
    if (btn) {
      const label = btn.textContent;
      btn.textContent = ok ? 'Copied!' : 'Copy failed';
      setTimeout(() => { btn.textContent = label; }, 2000);
    }
    if (!ok) alert('Unable to copy identifier.');
  }
  function toggleWrap() {
    const pre = document.getElementById('decrypted-content');
    const btn = document.getElementById('wrapBtn');
    if (!pre || !btn) return;
    if (pre.classList.contains('whitespace-pre-wrap')) {
      pre.classList.remove('whitespace-pre-wrap');
      pre.classList.add('overflow-x-auto');
      btn.textContent = 'Wrap';
    } else {
      pre.classList.add('whitespace-pre-wrap');
      pre.classList.remove('overflow-x-auto');
      btn.textContent = 'No Wrap';
    }
  }
  function toggleHighlight() {
    const code = document.getElementById('content-code');
    const btn = document.getElementById('highlightBtn');
    if (!code || !btn) return;
    if (code.classList.contains('hljs')) {
      code.className = '';
      btn.textContent = 'Highlight';
    } else {
      if (window.hljs) window.hljs.highlightElement(code);
      btn.textContent = 'Plain';
    }
  }

  // Decryption helpers
  class SecureDecryption {
    static async importKey(keyArray) {
      const keyBuffer = new Uint8Array(keyArray);
      return await window.crypto.subtle.importKey(
        'raw',
        keyBuffer,
        { name: 'AES-GCM', length: 256 },
        false,
        ['decrypt']
      );
    }
    static async decrypt(encryptedData, iv, key) {
      const ciphertext = new Uint8Array(encryptedData);
      const ivArray = new Uint8Array(iv);
      const decrypted = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv: ivArray }, key, ciphertext);
      const bytes = new Uint8Array(decrypted);
      if (bytes.length >= 4) {
        const len = (((bytes[0] << 24) | (bytes[1] << 16) | (bytes[2] << 8) | bytes[3]) >>> 0);
        if (len <= bytes.length - 4) {
          return new TextDecoder().decode(bytes.slice(4, 4 + len));
        }
      }
      return new TextDecoder().decode(bytes);
    }
  }

  let isVerifyingPassword = false;
  let isDecrypting = false;

  async function decryptAndDisplay() {
    if (isDecrypting) return;
    isDecrypting = true;
    try {
      // First try KEM path if metadata is present
      if (kemMeta && kemMeta.alg && kemMeta.wrappedB64 && kemMeta.ctB64) {
        const ok = await tryKemDecryptAndDisplay();
        if (ok) { return; }
        // If KEM path failed due to missing key and no URL key, we will fall back below (show error if missing)
      }
      const fragment = window.location.hash.substring(1);
      if (!fragment) throw new Error('No encryption key found in URL');
      const keyArray = [];
      for (let i = 0; i < fragment.length; i += 2) keyArray.push(parseInt(fragment.substr(i, 2), 16));
      if (PASSWORD_PROTECTED) {
        const passwordGate = document.getElementById('password-gate');
        if (passwordGate && !passwordGate.classList.contains('hidden')) { isDecrypting = false; return; }
      }
      const key = await SecureDecryption.importKey(keyArray);
      const encryptedContent = JSON.parse(pasteData.encrypted_content);
      const iv = JSON.parse(pasteData.iv);
      const decryptedText = await SecureDecryption.decrypt(encryptedContent, iv, key);
      document.getElementById('content-code').textContent = decryptedText;
      document.getElementById('decrypting').classList.add('hidden');
      document.getElementById('content-container').classList.remove('hidden');
      if (window.hljs) window.hljs.highlightElement(document.getElementById('content-code'));
    } catch (error) {
      console.warn('Decryption error:', error);
      document.getElementById('decrypting').classList.add('hidden');
      document.getElementById('error-container').classList.remove('hidden');
    } finally { isDecrypting = false; }
  }

  async function verifyPassword() {
    const gate = document.getElementById('password-gate');
    if (!gate) return true;
    if (gate.classList.contains('hidden')) return true;
    const pwd = (document.getElementById('pastePwd').value || '').trim();
    if (!pwd) { alert('Enter password'); return false; }
    try {
      const baseUrl = window.location.origin + window.location.pathname;
      const checkUrl = baseUrl + '?pw_check=1';
      const resp = await fetch(checkUrl, { method: 'GET', headers: { 'X-Paste-Password': pwd, 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/json' } });
      if (resp.status === 204) { gate.classList.add('hidden'); document.getElementById('decrypting').classList.remove('hidden'); return true; }
      await resp.json().catch(() => ({}));
      alert('Incorrect password');
      return false;
    } catch (e) {
      console.warn('Password verification error:', e);
      alert('Error verifying password');
      return false;
    }
  }

  async function submitPassword() {
    if (isVerifyingPassword || isDecrypting) return;
    const unlockBtn = document.getElementById('unlockBtn');
    const passwordInput = document.getElementById('pastePwd');
    if (unlockBtn) { unlockBtn.disabled = true; unlockBtn.textContent = 'Verifying...'; }
    if (passwordInput) passwordInput.disabled = true;
    isVerifyingPassword = true;
    try {
      const ok = await verifyPassword();
      if (ok) await decryptAndDisplay();
      else {
        if (unlockBtn) { unlockBtn.disabled = false; unlockBtn.textContent = 'Unlock'; }
        if (passwordInput) { passwordInput.disabled = false; passwordInput.focus(); }
      }
    } finally { isVerifyingPassword = false; }
  }

  async function decryptForOwner(encryptionKey = null) {
    try {
      let keyHex = encryptionKey || window.location.hash.substring(1) || OWNER_KEY || '';
      if (!keyHex) { document.getElementById('decrypted-content').textContent = 'No encryption key found'; return; }
      const keyArray = []; for (let i = 0; i < keyHex.length; i += 2) keyArray.push(parseInt(keyHex.substr(i, 2), 16));
      const key = await SecureDecryption.importKey(keyArray);
      const encryptedContent = JSON.parse(pasteData.encrypted_content);
      const iv = JSON.parse(pasteData.iv);
      const decryptedText = await SecureDecryption.decrypt(encryptedContent, iv, key);
      document.getElementById('decrypted-content').textContent = decryptedText;
      if (window.hljs) window.hljs.highlightElement(document.getElementById('decrypted-content'));
    } catch (error) {
      console.warn('Owner view decryption failed:', error);
      document.getElementById('decrypted-content').textContent = 'Decryption failed: ' + (error?.message || 'error');
    }
  }

  function showKeyInput() {
    const key = prompt('Enter the encryption key (hex string from the share URL):');
    if (key) { window.location.hash = key; decryptForOwner(); }
  }

  function setManualKey() {
    const input = document.getElementById('manualKey').value.trim();
    if (!input || input.length % 2 !== 0 || /[^0-9a-fA-F]/.test(input)) { alert('Please enter a valid hex key.'); return; }
    window.location.hash = '#' + input.toLowerCase();
    document.getElementById('error-container').classList.add('hidden');
    document.getElementById('decrypting').classList.remove('hidden');
    decryptAndDisplay();
  }

  async function downloadAndDecrypt(fileIdentifier, filename) {
    const keyHex = prompt('Enter file key (hex) provided at upload time:');
    if (!keyHex) return;
    const keyBytes = new Uint8Array((keyHex.match(/.{1,2}/g) || []).map(byte => parseInt(byte, 16)));
    const key = await SecureDecryption.importKey(keyBytes);
    const resp = await fetch(`${window.location.origin}/api/files/${fileIdentifier}/download`);
    if (!resp.ok) { alert('Download failed'); return; }
    const ivHeader = resp.headers.get('X-File-IV');
    const iv = JSON.parse(ivHeader || '[]');
    const cipher = new Uint8Array(await resp.arrayBuffer());
    const plain = await crypto.subtle.decrypt({ name: 'AES-GCM', iv: new Uint8Array(iv) }, key, cipher);
    const blob = new Blob([plain], { type: 'application/octet-stream' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = filename; document.body.appendChild(a); a.click(); URL.revokeObjectURL(url); a.remove();
  }

  // ZK Proof UI
  let _zkMetaPaste = null;
  function toggleViewZkProofPaste() {
    const viewer = document.getElementById('zkProofViewerPaste');
    if (!viewer) return;
    if (viewer.classList.contains('hidden')) { populateZkProofJsonPaste(); viewer.classList.remove('hidden'); }
    else { viewer.classList.add('hidden'); }
  }
  async function populateZkProofJsonPaste() {
    try {
      if (!_zkMetaPaste) await refreshZkProofPaste();
      const pre = document.getElementById('zkProofJsonPaste');
      if (!pre || !_zkMetaPaste || !_zkMetaPaste.zk) { if (pre) pre.textContent = 'No proof available to display.'; return; }
      const commit = (_zkMetaPaste.commitments && _zkMetaPaste.commitments.commit)
        ? _zkMetaPaste.commitments.commit
        : (_zkMetaPaste.zk.publicSignals && _zkMetaPaste.zk.publicSignals[0]) || null;
      if (!commit && !_zkMetaPaste.zk?.proof) { pre.textContent = 'No proof available to display.'; return; }
      const encryptedView = _zkMetaPaste.encrypted || { algorithm: _zkMetaPaste.algorithm ?? null, iv: _zkMetaPaste.iv ?? null, timestamp: _zkMetaPaste.timestamp ?? null };
      
      // Build the full ZK object with all available proof data
      let zkDisplay = { 
        commit,
        // Always include verified field (null, true, or false)
        verified: _zkMetaPaste.zk?.verified !== undefined ? _zkMetaPaste.zk.verified : null,
        // Include present field
        present: _zkMetaPaste.zk?.present !== undefined ? _zkMetaPaste.zk.present : false
      };
      
      // Add optional fields if they exist
      if (_zkMetaPaste.zk?.proof) {
        zkDisplay.proof = _zkMetaPaste.zk.proof;
      }
      if (_zkMetaPaste.zk?.publicSignals) {
        zkDisplay.publicSignals = _zkMetaPaste.zk.publicSignals;
      }
      if (_zkMetaPaste.zk?.enc) {
        zkDisplay.enc = _zkMetaPaste.zk.enc;
      }
      
      const payload = { encrypted: encryptedView, commitments: _zkMetaPaste.commitments || null, zk: zkDisplay };
      pre.textContent = JSON.stringify(payload, null, 2);
    } catch (e) { console.warn(e); const pre = document.getElementById('zkProofJsonPaste'); if (pre) pre.textContent = 'Error loading proof JSON'; }
  }
  async function refreshZkProofPaste() {
    try {
      setZkStatusPaste('Checking…');
      const id = document.getElementById('pasteIdentifier')?.textContent?.trim();
      if (!id) { setZkStatusPaste('Missing paste identifier', true); return; }
      const resp = await fetch(`/api/zk/encryption/by-ref?type=paste&identifier=${encodeURIComponent(id)}&_t=${Date.now()}`, { cache: 'no-store' });
      if (!resp.ok) { setZkStatusPaste('No proof found'); return; }
      const data = await resp.json();
      _zkMetaPaste = data.meta || null;
      if (!_zkMetaPaste) { setZkStatusPaste('No proof metadata'); return; }
      const present = _zkMetaPaste.zk?.present ? 'yes' : 'no';
      const verified = _zkMetaPaste.zk?.verified === true ? 'yes' : (_zkMetaPaste.zk?.verified === false ? 'no' : 'unknown');
      setZkStatusPaste(`Proof present: ${present} • Verified: ${verified}`);
      const commit = (_zkMetaPaste?.commitments?.commit) || (_zkMetaPaste?.zk?.publicSignals?.[0]) || null;
      setZkCommitPaste(commit);
    } catch (e) { setZkStatusPaste('Error loading proof metadata', true); console.warn(e); }
  }
  let _pCipher = null, _pIv = null, _pPlain = null, _pKey = null;
  async function ensureDecryptedForProofPaste() {
    try {
      if (!_pKey) {
        const fragment = window.location.hash.substring(1);
        if (!fragment) { setZkStatusPaste('Missing key in link.'); return false; }
        const keyArray = []; for (let i = 0; i < fragment.length; i += 2) keyArray.push(parseInt(fragment.substr(i, 2), 16));
        _pKey = await window.crypto.subtle.importKey('raw', new Uint8Array(keyArray), { name: 'AES-GCM' }, false, ['decrypt','encrypt']);
      }
      if (!_pCipher || !_pIv || !_pPlain) {
        const encryptedContent = JSON.parse(pasteData.encrypted_content);
        const iv = JSON.parse(pasteData.iv);
        const decrypted = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv: new Uint8Array(iv) }, _pKey, new Uint8Array(encryptedContent));
        _pCipher = new Uint8Array(encryptedContent); _pIv = new Uint8Array(iv); _pPlain = new Uint8Array(decrypted);
      }
      return true;
    } catch (e) { console.warn(e); setZkStatusPaste('Decryption failed', true); return false; }
  }
  async function verifyZkProofPaste() {
    try {
      if (!_zkMetaPaste) await refreshZkProofPaste();
      if (!_zkMetaPaste) await refreshZkProofPaste();
      if (!_zkMetaPaste || !_zkMetaPaste.zk) { setZkStatusPaste('No proof available to verify'); return; }
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
        } catch (e) { setZkStatusPaste('Encrypted proof present but could not be decrypted', true); return; }
      }
      if (!proofObj || !proofObj.proof || !proofObj.publicSignals) { setZkStatusPaste('No proof available to verify'); return; }
      try {
        const enc = new Uint8Array(JSON.parse(pasteData.encrypted_content));
        const localHash = await window.SecureEncryption.hash(enc, 'SHA-256');
        const localHex = toHexPaste(localHash);
        const claimedHex = (_zkMetaPaste.commitments?.ciphertextHash || '').toLowerCase();
        if (!claimedHex || localHex !== claimedHex) { setZkStatusPaste('Ciphertext hash mismatch — served content not bound to proof', true); return; }
      } catch (e) { console.warn(e); setZkStatusPaste('Failed to compute ciphertext hash locally', true); return; }
      if (_zkMetaPaste.commitments?.commit && proofObj.publicSignals?.[0]) {
        const commit = String(_zkMetaPaste.commitments.commit);
        const sig0 = String(proofObj.publicSignals[0]);
        if (commit && sig0 && commit !== sig0) { setZkStatusPaste('Commitment mismatch — proof not for this ciphertext', true); return; }
      }
      const art = window.ZK_ARTIFACTS || {};
      const vkeyUrl = (art.paste?.vkeyUrl || art.common?.vkeyUrl || art.vkeyUrl) || window.ZK_VKEY_URL;
      if (!vkeyUrl) { setZkStatusPaste('vkey not configured on client'); return; }
      setZkStatusPaste('Verifying…');
      if (!window.SecureEncryption) { setZkStatusPaste('Crypto library not loaded', true); return; }
      const ok = await window.SecureEncryption.verifyEncryptionProof({ vkeyUrl, proof: proofObj.proof, publicSignals: proofObj.publicSignals, loaderOptions: art.paste?.loaderOptions || art.common?.loaderOptions || {} });
      setZkStatusPaste(`Proof present: yes • Hash bound: yes • Verified: ${ok ? 'yes' : 'no'}`);
    } catch (e) { setZkStatusPaste('Verification error', true); console.warn(e); }
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
      const { zk, commitments } = await window.SecureEncryption.generateProofForCiphertext({ plaintext: _pPlain, ciphertext: _pCipher, iv: _pIv, algorithm: 'AES-GCM', additionalData: null, zkOptions: { wasmUrl: art.wasmUrl, zkeyUrl: art.zkeyUrl, loaderOptions: art.loaderOptions || {} } });
      const id = document.getElementById('pasteIdentifier')?.textContent?.trim();
      let zkEnc = null;
      if (zk && zk.proof) {
        const iv = crypto.getRandomValues(new Uint8Array(12));
        const ad = new TextEncoder().encode('zkp:proof:v1');
        const body = new TextEncoder().encode(JSON.stringify({ proof: zk.proof, publicSignals: zk.publicSignals }));
        const ct = await crypto.subtle.encrypt({ name: 'AES-GCM', iv, additionalData: ad }, _pKey, body);
        const ctB64 = (window.WebCryptoWrapper && window.WebCryptoWrapper.arrayBufferToBase64) ? window.WebCryptoWrapper.arrayBufferToBase64(ct) : btoa(String.fromCharCode(...new Uint8Array(ct)));
        zkEnc = { alg: 'AES-GCM', iv: Array.from(iv), ad: 'zkp:proof:v1', ct: ctB64 };
      }
      const payload = { encrypted: { algorithm: 'AES-GCM', iv: Array.from(_pIv), timestamp: Date.now() }, commitments: { ciphertextHash: toHexPaste(commitments.ciphertextHash), additionalDataHash: null, commit: commitments.commit || (zk && zk.publicSignals ? zk.publicSignals[0] : null) }, zk: zkEnc ? { proof: null, publicSignals: null, enc: zkEnc } : (zk || null), ref: { type: 'paste', identifier: id } };
      await fetch('/api/zk/encryption/submit', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
      setZkStatusPaste('Proof submitted. Refreshing…');
      setZkCommitPaste(payload.commitments.commit || null);
      await refreshZkProofPaste();
    } catch (e) { console.warn(e); setZkStatusPaste('Error generating proof', true); }
  }
  function copyZkCommitPaste() {
    const el = document.getElementById('zkCommitValue');
    if (!el || !el.textContent) return;
    try { navigator.clipboard.writeText(el.textContent); } catch (_) {}
  }
  function copyZkProofPaste() {
    const pre = document.getElementById('zkProofJsonPaste');
    if (!pre || !pre.textContent) return;
    try { navigator.clipboard.writeText(pre.textContent); } catch (_) {}
  }
  function downloadZkProofPaste() {
    const pre = document.getElementById('zkProofJsonPaste');
    if (!pre || !pre.textContent) return;
    const id = document.getElementById('pasteIdentifier')?.textContent?.trim() || 'paste';
    const blob = new Blob([pre.textContent], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a'); a.href = url; a.download = `${id}-zk-proof.json`; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
  }

  // Initial flow
  document.addEventListener('DOMContentLoaded', () => {
    if (IS_OWNER) {
      if (OWNER_KEY) decryptForOwner(OWNER_KEY);
    } else {
      if (!PASSWORD_PROTECTED) decryptAndDisplay();
    }
    refreshZkProofPaste();
  });

  // Expose actions for paste-actions.js
  Object.assign(window, {
    copyContent,
    copyIdentifier,
    toggleWrap,
    toggleHighlight,
    decryptForOwner,
    showKeyInput,
    submitPassword,
    setManualKey,
    downloadAndDecrypt,
    toggleViewZkProofPaste,
    populateZkProofJsonPaste,
    refreshZkProofPaste,
    verifyZkProofPaste,
    generateZkProofForPaste,
    copyZkCommitPaste,
    copyZkProofPaste,
    saveKemSecret,
  });
})();
