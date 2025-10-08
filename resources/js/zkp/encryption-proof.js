/**
 * Zero-Knowledge Encryption Proof utilities (snarkjs integration)
 *
 * This module provides thin wrappers around snarkjs for browser usage.
 * It does NOT define a specific circuit. You must provide a circuit wasm/zkey
 * and, optionally, a verification key JSON. Use the inputBuilder to map your
 * plaintext/ciphertext/bindings to your circuit's expected input.
 */

export class SnarkJSLoader {
  static _loaded = null;

  static async load(options = {}) {
    if (this._loaded) return this._loaded;

    // If snarkjs already present (global from bundler or script tag)
    if (typeof window !== 'undefined' && window.snarkjs) {
      this._loaded = window.snarkjs;
      return this._loaded;
    }

    const preferModule = !!options.preferModule;

    // Helper to import as ESM via bundler
    const importModule = async () => {
      const mod = await import(/* @vite-ignore */ 'snarkjs');
      return mod.default || mod;
    };

    // Helper to inject script: try local -> configured -> cdn fallbacks
    const importCdn = async () => {
      if (typeof document === 'undefined') {
        throw new Error('CDN not available in non-browser environment');
      }
      const candidates = [];
      // Allow explicit override via loader options
      if (options.url) candidates.push(options.url);
      // Global config from layout
      if (typeof window !== 'undefined' && window.SNARKJS_URL) candidates.push(window.SNARKJS_URL);
      // Local public asset default
      candidates.push('/js/snarkjs.min.js');
      // Known CDNs
      const primaryCdn = options.cdn || 'https://cdn.jsdelivr.net/npm/snarkjs@0.7.4/dist/snarkjs.min.js';
      candidates.push(primaryCdn);
      candidates.push('https://unpkg.com/snarkjs@0.7.4/dist/snarkjs.min.js');

      let lastErr = null;
      for (const url of candidates) {
        try {
          await new Promise((resolve, reject) => {
            const s = document.createElement('script');
            s.src = url;
            s.async = true;
            s.onload = () => resolve();
            s.onerror = () => reject(new Error(`Failed to load snarkjs from ${url}`));
            document.head.appendChild(s);
          });
          if (window.snarkjs) return window.snarkjs;
        } catch (e) {
          lastErr = e;
        }
      }
      throw lastErr || new Error('Failed to load snarkjs from known URLs');
    };

    try {
      if (preferModule) {
        this._loaded = await importModule();
      } else {
        // Default: try local/CDN first, then module fallback
        try {
          this._loaded = await importCdn();
        } catch (e) {
          this._loaded = await importModule();
        }
      }
    } catch (e) {
      // Final fallback: try the other path
      try {
        this._loaded = preferModule ? await importCdn() : await importModule();
      } catch (e2) {
        throw e2;
      }
    }

    return this._loaded;
  }
}

export class EncryptionProof {
  /**
   * Generate a ZK proof using groth16.fullProve.
   * @param {Object} params
   * @param {Object} params.input - Circuit input object
   * @param {string} params.wasmUrl - URL to circuit.wasm
   * @param {string} params.zkeyUrl - URL to circuit_final.zkey
   * @param {Object} [params.loaderOptions] - Snark loader options
   * @returns {Promise<{ proof: any, publicSignals: any }>} - The snark proof bundle
   */
  static async generateProof({ input, wasmUrl, zkeyUrl, loaderOptions = {} }) {
    const snarkjs = await SnarkJSLoader.load(loaderOptions);
    if (!wasmUrl || !zkeyUrl) throw new Error('wasmUrl and zkeyUrl are required to generate a proof');
    let effectiveWasmUrl = wasmUrl;
    let revoke = null;
    try {
      try { console.debug('[ZK] loaderOptions', loaderOptions, 'wasmUrl', wasmUrl, 'zkeyUrl', zkeyUrl); } catch (_) {}
      if (loaderOptions.forceBlob) {
        const res = await fetch(wasmUrl, { cache: 'no-store' });
        if (!res.ok) throw new Error(`Failed to fetch wasm: ${res.status}`);
        const buf = await res.arrayBuffer();
        try { console.debug('[ZK] fetched wasm bytes:', buf.byteLength); } catch (_) {}
        if (buf.byteLength < 4) throw new Error('WASM too small to be valid');
        const u8 = new Uint8Array(buf);
        if (!(u8[0] === 0x00 && u8[1] === 0x61 && u8[2] === 0x73 && u8[3] === 0x6d)) {
          throw new Error('Invalid WASM magic header');
        }
        const blob = new Blob([buf], { type: 'application/wasm' });
        effectiveWasmUrl = URL.createObjectURL(blob);
        revoke = () => URL.revokeObjectURL(effectiveWasmUrl);
      }
      const { proof, publicSignals } = await snarkjs.groth16.fullProve(input, effectiveWasmUrl, zkeyUrl);
      return { proof, publicSignals };
    } finally {
      if (revoke) {
        try { revoke(); } catch (_) {}
      }
    }
  }

  /**
   * Verify a ZK proof.
   * @param {Object} params
   * @param {Object} [params.vkeyJson] - Verification key JSON (object)
   * @param {string} [params.vkeyUrl] - Alternatively, URL to fetch verification key JSON
   * @param {any} params.proof - Proof object
   * @param {any} params.publicSignals - Public signals
   * @param {Object} [params.loaderOptions] - Snark loader options
   * @returns {Promise<boolean>} - True if valid
   */
  static async verifyProof({ vkeyJson, vkeyUrl, proof, publicSignals, loaderOptions = {} }) {
    const snarkjs = await SnarkJSLoader.load(loaderOptions);
    let vkey = vkeyJson;
    if (!vkey) {
      if (!vkeyUrl) throw new Error('Either vkeyJson or vkeyUrl is required for verification');
      const res = await fetch(vkeyUrl);
      if (!res.ok) throw new Error(`Failed to fetch verification key: ${res.status}`);
      vkey = await res.json();
    }
    return snarkjs.groth16.verify(vkey, publicSignals, proof);
  }
}

// Global binding for convenience (optional)
if (typeof window !== 'undefined') {
  window.SnarkJSLoader = SnarkJSLoader;
  window.EncryptionProof = EncryptionProof;
}
