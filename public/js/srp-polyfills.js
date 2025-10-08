'use strict';
(function(){
  try {
    // Map msCrypto for older Edge/IE to window.crypto
    if (typeof window !== 'undefined') {
      if (!window.crypto && window.msCrypto) {
        window.crypto = window.msCrypto;
      }
      // getRandomValues shim via msCrypto if present
      if (window.crypto && !window.crypto.getRandomValues && window.msCrypto && window.msCrypto.getRandomValues) {
        window.crypto.getRandomValues = function(arr){ return window.msCrypto.getRandomValues(arr); };
      }

      // Minimal TextEncoder polyfill (UTF-8)
      if (typeof window.TextEncoder === 'undefined') {
        window.TextEncoder = class {
          encode(str) {
            str = String(str || '');
            const out = [];
            for (let i = 0; i < str.length; i++) {
              let codePoint = str.codePointAt(i);
              if (codePoint > 0xFFFF) i++; // surrogate pair consumed
              if (codePoint <= 0x7F) {
                out.push(codePoint);
              } else if (codePoint <= 0x7FF) {
                out.push(0xC0 | (codePoint >> 6));
                out.push(0x80 | (codePoint & 0x3F));
              } else if (codePoint <= 0xFFFF) {
                out.push(0xE0 | (codePoint >> 12));
                out.push(0x80 | ((codePoint >> 6) & 0x3F));
                out.push(0x80 | (codePoint & 0x3F));
              } else {
                out.push(0xF0 | (codePoint >> 18));
                out.push(0x80 | ((codePoint >> 12) & 0x3F));
                out.push(0x80 | ((codePoint >> 6) & 0x3F));
                out.push(0x80 | (codePoint & 0x3F));
              }
            }
            return new Uint8Array(out);
          }
        };
      }

      // SubtleCrypto.digest polyfill using js-sha256 if needed
      if (window.crypto) {
        if (!window.crypto.subtle) window.crypto.subtle = {};
        if (typeof window.crypto.subtle.digest !== 'function') {
          window.crypto.subtle.digest = async function(algorithm, data) {
            const name = (typeof algorithm === 'string' ? algorithm : (algorithm && algorithm.name)) || '';
            if (name.toUpperCase() !== 'SHA-256') throw new Error('Unsupported digest algorithm');
            if (typeof window.sha256 === 'undefined' || typeof window.sha256.arrayBuffer !== 'function') {
              throw new Error('SHA-256 polyfill not available');
            }
            // data can be ArrayBuffer or TypedArray
            const buf = data && data.buffer ? data.buffer : data;
            return window.sha256.arrayBuffer(buf);
          };
        }
      }
    }
  } catch (e) {
    // fail closed; SRP init will check feature presence
  }
})();
