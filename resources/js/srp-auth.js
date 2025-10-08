/**
 * SRP (Secure Remote Password) Authentication Client
 * 
 * This implements the client-side of SRP-6a protocol for zero-knowledge authentication.
 * Uses the artisansdk/srp library for cryptographic operations.
 * 
 * SECURITY NOTE: This is a true zero-knowledge proof system where:
 * - Password is never transmitted to the server
 * - Server never learns the password
 * - Authentication is based on mathematical proofs
 * - Even if server is compromised, passwords remain secure
 */

import srpClient from 'secure-remote-password/client';
import SRPInteger from 'secure-remote-password/lib/srp-integer';

// Fallback: compute SHA-256 over bytes using js-sha256 (loaded globally) when SubtleCrypto is unavailable
function __bytesToHex(arr) {
    const b = (arr instanceof Uint8Array) ? arr : new Uint8Array(arr || []);
    let s = '';
    for (let i = 0; i < b.length; i++) s += b[i].toString(16).padStart(2, '0');
    return s;
}
async function sha256BytesFallback(bytes) {
    try {
        if (typeof window !== 'undefined' && window.sha256 && typeof window.sha256.arrayBuffer === 'function') {
            const buf = window.sha256.arrayBuffer(bytes && bytes.buffer ? bytes.buffer : bytes);
            const out = new Uint8Array(buf);
            return __bytesToHex(out);
        }
    } catch (_) {}
    throw new Error('SHA-256 not available');
}

export class SRPAuthentication {
    static SUPPORTED = false;
    static CLIENT = null;
    static CONFIG = null; // filled from /api/srp/support

    // RFC5054 prime groups (hex)
    static N_HEX_1024 = (
        'EEAF0AB9ADB38DD69C33F80AFA8FC5E86072618775FF3C0B9EA2314C'
      + '9C256576D674DF7496EA81D3383B4813D692C6E0E0D5D8E250B98BE4'
      + '8E495C1D6089DAD15DC7D7B46154D6B6CE8EF4AD69B15D4982559B29'
      + '7BCF1885C529F566660E57EC68EDBC3C05726CC02FD4CBF4976EAA9A'
      + 'FD5138FE8376435B9FC61D2FC0EB06E3'
    ).toUpperCase();
    static N_HEX_2048 = (
        'AC6BDB41324A9A9BF166DE5E1389582FAF72B6651987EE07FC319294'
      + '3DB56050A37329CBB4A099ED8193E0757767A13DD52312AB4B03310D'
      + 'CD7F48A9DA04FD50E8083969EDB767B0CF6095179A163AB3661A05FB'
      + 'D5FAAAE82918A9962F0B93B855F97993EC975EEAA80D740ADBF4FF74'
      + '7359D041D5C33EA71D281E446B14773BCA97B43A23FB801676BD207A'
      + '436C6481F1D2B9078717461A5B9D32E688F87748544523B524B0D57D'
      + '5EA77A2775D2ECFA032CFBDBF52FB3786160279004E57AE6AF874E73'
      + '03CE53299CCC041C7BC308D82A5698F3A8D0C38271AE35F8E9DBFBB6'
      + '94B5C803D89F7AE435DE236D525F54759B65E372FCD68EF20FA7111F'
      + '9E4AFF73'
    ).toUpperCase();
    static G_HEX = '02';

    static padHex(hex, targetLen) {
        return (hex || '').replace(/^0+/, '').padStart(targetLen, '0').toUpperCase();
    }

    static isHexString(s) {
        return typeof s === 'string' && /^[0-9a-fA-F]+$/.test(s);
    }

    static isDecString(s) {
        return typeof s === 'string' && /^[0-9]+$/.test(s);
    }

    static decToHex(decStr) {
        // Convert decimal string to hex string without BigInt for compatibility
        if (typeof decStr !== 'string' || !/^[0-9]+$/.test(decStr)) return '';
        if (decStr === '0') return '0';
        let s = decStr;
        let out = '';
        const div16 = (numStr) => {
            let q = '';
            let rem = 0;
            for (let i = 0; i < numStr.length; i++) {
                const n = rem * 10 + (numStr.charCodeAt(i) - 48);
                const d = Math.floor(n / 16);
                rem = n % 16;
                if (q.length || d) q += String.fromCharCode(48 + d);
            }
            return { q: q || '0', r: rem };
        };
        while (s !== '0') {
            const { q, r } = div16(s);
            out = (r < 10 ? String.fromCharCode(48 + r) : String.fromCharCode(55 + r)) + out;
            s = q;
        }
        return out.toUpperCase();
    }

    static async computeKHex(Nhex = this.N_HEX_1024, gHex = this.G_HEX) {
        const gPadded = gHex.padStart(Nhex.length, '0');
        return await this.sha256Hex(Nhex + gPadded);
    }

    static async srpParams(sessionCfg = null) {
        const cfg = this.CONFIG || {};
        const sc = sessionCfg || {};
        const Nhex = (
            (typeof sc.N_hex === 'string' && sc.N_hex.length > 0 && sc.N_hex) ||
            (typeof cfg.N_hex === 'string' && cfg.N_hex.length > 0 && cfg.N_hex) ||
            this.N_HEX_1024
        ).toUpperCase();
        const gHex = (
            (typeof sc.g_hex === 'string' && sc.g_hex.length > 0 && sc.g_hex) ||
            (typeof cfg.g_hex === 'string' && cfg.g_hex.length > 0 && cfg.g_hex) ||
            this.G_HEX
        ).toUpperCase();
        const N = SRPInteger.fromHex(Nhex);
        const g = SRPInteger.fromHex(gHex);
        const kHex = await this.computeKHex(Nhex, gHex);
        const k = SRPInteger.fromHex(kHex);
        return { N, g, k, kHex, NHex: Nhex, NHexLen: Nhex.length };
    }

    /**
     * Initialize SRP client
     */
    static async initialize() {
        try {
            // Check if SRP is supported on server
            const response = await fetch('/api/srp/support');
            const data = await response.json();
            
            if (!data.supported) {
                console.warn('SRP not supported on server');
                return false;
            }

            // Check if we have required primitives
            // We require a CSPRNG (getRandomValues). SHA-256 can fall back to JS implementation.
            if (typeof crypto === 'undefined' || typeof crypto.getRandomValues !== 'function') {
                console.warn('CSPRNG (crypto.getRandomValues) not available');
                return false;
            }

            this.CONFIG = data.config || {};
            this.SUPPORTED = true;
            console.log('SRP Authentication initialized successfully');
            return true;
            
        } catch (error) {
            console.error('SRP initialization failed:', error);
            return false;
        }
    }

    /**
     * Register a new user with SRP
     * @param {string} username - The username
     * @param {string} password - The password (never sent to server)
     * @param {string} pin - The PIN
     * @returns {Promise<Object>} Registration result
     */
    static async register(username, password, pin) {
        try {
            if (!this.SUPPORTED) {
                throw new Error('SRP not supported');
            }

            // Zero-knowledge registration flow:
            // 1) Generate salt (hex), normalize to uppercase (server style)
            const saltLower = srpClient.generateSalt();
            const saltUpper = saltLower.toUpperCase();

            // 2) Optional Argon2id prehash if server config enables it (requires re-enrollment)
            const passwordPrime = await this.prehashIfEnabled(username, password);

            // 3) Compute x the same way the server does (with possibly prehashed password)
            // inner = sha256Ascii(username+':'+passwordPrime), then x = sha256Ascii( UPPER(salt + unpad(inner)) )
            const inner = await this.sha256Ascii(`${username}:${passwordPrime}`);
            const innerUnpadded = this.unpadHex(inner);
            const xHex = await this.sha256Ascii((saltUpper + innerUnpadded.toUpperCase()));

            // 4) Compute verifier v = g^x mod N (use server config from /api/srp/support or fallback)
            const sp = await this.srpParams();
            const xInt = SRPInteger.fromHex(xHex);
            const vHexPadded = sp.g.modPow(xInt, sp.N).toHex();
            const verifier = this.unpadHex(vHexPadded);

            // 5) Send username, pin, salt, verifier (no password is sent)
            const formData = new FormData();
            formData.append('username', username);
            formData.append('pin', pin);
            formData.append('salt', saltUpper);
            formData.append('verifier', verifier);
            formData.append('_token', this.getCSRFToken());

            const response = await fetch('/api/srp/register', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: formData,
                credentials: 'same-origin'
            });

            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const result = await response.json();
                if (response.ok) {
                    return { success: true, data: result };
                }
                // Prefer detailed validation messages if present
                if (result.messages && typeof result.messages === 'object') {
                    try {
                        const parts = [];
                        for (const [field, arr] of Object.entries(result.messages)) {
                            if (Array.isArray(arr) && arr.length) {
                                parts.push(`${field}: ${arr.join(' ')}`);
                            }
                        }
                        const detail = parts.join('; ');
                        const base = result.error || 'Validation failed';
                        return { success: false, error: `${base}: ${detail}` };
                    } catch (_) { /* fallthrough */ }
                }
                const serverErr = result.error_detail ? `${result.error} (${result.error_detail})` : (result.error || 'Registration failed');
                return { success: false, error: serverErr };
            } else {
                const text = await response.text();
                return { success: false, error: `Server returned non-JSON (${response.status}). ${text.slice(0,180)}` };
            }

        } catch (error) {
            console.error('SRP registration failed:', error);
            return {
                success: false,
                error: 'Registration failed: ' + error.message
            };
        }
    }

    /**
     * Initiate SRP login
     * @param {string} username - The username
     * @returns {Promise<Object>} Challenge data
     */
    static async initiateLogin(username) {
        try {
            if (!this.SUPPORTED) {
                throw new Error('SRP not supported');
            }

            const response = await fetch('/api/srp/initiate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({ username }),
                credentials: 'same-origin'
            });

            const contentType = response.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                const result = await response.json();
                if (response.ok) return { success: true, data: result };
                return { success: false, error: result.error || 'Authentication initiation failed' };
            } else {
                const text = await response.text();
                return { success: false, error: `Server returned non-JSON (${response.status}). ${text.slice(0,180)}` };
            }

        } catch (error) {
            console.error('SRP login initiation failed:', error);
            return {
                success: false,
                error: 'Authentication initiation failed: ' + error.message
            };
        }
    }

    /**
     * Complete SRP login with password
     * @param {string} username - The username
     * @param {string} password - The password (used locally only)
     * @param {Object} challengeData - Challenge data from server
     * @returns {Promise<Object>} Login result
     */
    static async completeLogin(username, password, challengeData) {
        try {
            if (!this.SUPPORTED) {
                throw new Error('SRP not supported');
            }

            // Use secure-remote-password (SRP-6a) to compute client ephemeral and proof
            if (!challengeData || !challengeData.salt || !challengeData.B) {
                throw new Error('Invalid SRP challenge from server');
            }

            const { salt, B } = challengeData;

            // 1) Generate client ephemeral (a, A)
            const clientEphemeral = srpClient.generateEphemeral();

            // 2) Optional Argon2id prehash if server config enables it (requires re-enrollment)
            const passwordPrime = await this.prehashIfEnabled(username, password);

            // 3) Derive private key x from salt, username, and (possibly prehashed) password (server-compatible)
            // Server uses: x = H( UPPER(salt + unpad(H(I+":"+P'))) )
            const inner = await this.sha256Ascii(`${username}:${passwordPrime}`);
            const innerUnpadded = this.unpadHex(inner);
            const saltUpper = (salt || '').toUpperCase();
            const privateKey = await this.sha256Ascii((saltUpper + innerUnpadded.toUpperCase()));

            // 4) Derive S explicitly (server-matching params and hashing)
            const sp = await this.srpParams({ N_hex: challengeData.N_hex, g_hex: challengeData.g_hex });
            const N = sp.N;
            const g = sp.g;
            const k = sp.k;
            const aInt = SRPInteger.fromHex(clientEphemeral.secret);
            const xInt = SRPInteger.fromHex(privateKey);
            const Aint = g.modPow(aInt, N);
            const AhexRaw = this.unpadHex(Aint.toHex());
            // Normalize server B: accept hex or decimal
            let Bclean = (B || '').toString().trim();
            if (Bclean.startsWith('0x') || Bclean.startsWith('0X')) Bclean = Bclean.slice(2);
            let BhexRaw;
            if (this.isHexString(Bclean)) {
                BhexRaw = this.unpadHex(Bclean);
            } else if (this.isDecString(Bclean)) {
                const hx = this.decToHex(Bclean);
                if (!hx) throw new Error('Invalid server B value');
                BhexRaw = this.unpadHex(hx);
            } else {
                throw new Error('Unsupported B format from server');
            }
            const Ahex = AhexRaw.toLowerCase();
            const Bhex = BhexRaw.toLowerCase();
            // u = H(A||B) over ASCII hex (server style)
            const uHex = await this.sha256Ascii(Ahex + Bhex);
            const uInt = SRPInteger.fromHex(uHex);
            // S = (B - k * g^x) ^ (a + u * x) mod N
            const gx = g.modPow(xInt, N);
            const kgx = k.multiply(gx);
            const Bint = SRPInteger.fromHex(Bhex);
            const base = Bint.subtract(kgx);
            const exp = aInt.add(uInt.multiply(xInt));
            const Sint = base.modPow(exp, N);
            const Shex = this.unpadHex(Sint.toHex()).toLowerCase();
            // M1 = H(A||B||S) over ASCII hex (server style) — keep full 64-char hex digest
            const M1 = (await this.sha256Ascii(Ahex + Bhex + Shex)).toLowerCase();

            const clientProof = { A: Ahex, M1 };

            const response = await fetch('/api/srp/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.getCSRFToken()
                },
                body: JSON.stringify({
                    A: clientProof.A,
                    M1: clientProof.M1,
                    challenge_id: challengeData.challenge_id || null
                }),
                credentials: 'same-origin'
            });

            const contentType = response.headers.get('content-type') || '';
            if (!contentType.includes('application/json')) {
                const text = await response.text();
                return { success: false, error: `Server returned non-JSON (${response.status}). ${text.slice(0,180)}` };
            }
            const result = await response.json();
            if (!response.ok) {
                return { success: false, error: result.error || 'Authentication failed' };
            }
            // Mutual authentication: verify server proof M2 = H(A || M1 || S)
            try {
                const expectedM2 = (await this.sha256Ascii(Ahex + M1 + Shex)).toLowerCase();
                const serverM2 = String(result.M2 || '').toLowerCase();
                if (!serverM2 || serverM2 !== expectedM2) {
                    return { success: false, error: 'Server proof verification failed' };
                }
            } catch (_) {
                return { success: false, error: 'Server proof verification failed' };
            }
            return { success: true, data: result };

        } catch (error) {
            console.error('SRP login completion failed:', error);
            return {
                success: false,
                error: 'Authentication failed: ' + error.message
            };
        }
    }

    /**
     * Generate client proof for SRP
     * This is a simplified implementation - in production, use proper SRP library
     * @param {string} username - The username
     * @param {string} password - The password
     * @param {Object} challengeData - Challenge data from server
     * @returns {Promise<Object>} Client proof
     */
    // Deprecated placeholder removed; real SRP proof generation is in completeLogin()

    /**
     * Generate random hex string
     * @param {number} length - Length in characters
     * @returns {Promise<string>} Random hex string
     */
    static async generateRandomHex(length) {
        const array = new Uint8Array(length / 2);
        crypto.getRandomValues(array);
        return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
    }

    /**
     * Compute SHA-256 over a hex-encoded string and return hex digest
     * @param {string} hexInput concatenated lowercase hex (no 0x)
     * @returns {Promise<string>} lowercase hex digest
     */
    static async sha256Hex(hexInput) {
        const bytes = new Uint8Array((hexInput || '').match(/.{1,2}/g).map(b => parseInt(b, 16)));
        if (crypto && crypto.subtle && typeof crypto.subtle.digest === 'function') {
            const digest = await crypto.subtle.digest('SHA-256', bytes);
            const out = new Uint8Array(digest);
            return Array.from(out, b => b.toString(16).padStart(2, '0')).join('');
        }
        return sha256BytesFallback(bytes);
    }

    /**
     * Compute SHA-256 over ASCII string content and return lowercase hex digest
     * @param {string} asciiText
     * @returns {Promise<string>}
     */
    static async sha256Ascii(asciiText) {
        const encoder = new TextEncoder();
        const bytes = encoder.encode(asciiText || '');
        if (crypto && crypto.subtle && typeof crypto.subtle.digest === 'function') {
            const digest = await crypto.subtle.digest('SHA-256', bytes);
            const out = new Uint8Array(digest);
            return Array.from(out, b => b.toString(16).padStart(2, '0')).join('');
        }
        return sha256BytesFallback(bytes);
    }

    /**
     * Optionally prehash the password with Argon2id if server enabled it.
     * This requires re-enrollment and the argon2 library available at runtime (window.argon2).
     * Falls back to raw password if disabled or unavailable. Non-breaking default.
     */
    static async prehashIfEnabled(username, password) {
        try {
            const enabled = !!(this.CONFIG && this.CONFIG.prehash && this.CONFIG.prehash.enabled);
            if (!enabled) return password;
            if (typeof window === 'undefined' || !window.argon2) {
                // When server requires prehash, do not silently fall back — enforce consistency
                throw new Error('SRP prehash (Argon2id) is required but the Argon2 library is not loaded');
            }
            const saltBytes = new TextEncoder().encode(username || '');
            const res = await window.argon2.hash({
                pass: password,
                salt: saltBytes,
                time: 3,
                mem: 65536, // 64MB
                hashLen: 32,
                parallelism: 1,
                type: window.argon2.ArgonType.Argon2id
            });
            // Return hex string
            return res && res.hashHex ? res.hashHex.toLowerCase() : password;
        } catch (e) {
            // Surface the error to caller so UI can inform the user and avoid mismatched verifiers
            throw e instanceof Error ? e : new Error('Argon2 prehash failed');
        }
    }

    /**
     * Remove leading zeros from a hex string
     * @param {string} hex
     * @returns {string}
     */
    static unpadHex(hex) {
        if (!hex) return hex;
        return hex.replace(/^0+/, '');
    }

    /**
     * Get CSRF token
     * @returns {string} CSRF token
     */
    static getCSRFToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * Check if SRP is supported
     * @returns {boolean} Whether SRP is supported
     */
    static isSupported() {
        return this.SUPPORTED;
    }

    /**
     * Get SRP configuration info
     * @returns {Promise<Object>} Configuration info
     */
    static async getConfigInfo() {
        try {
            const response = await fetch('/api/srp/support');
            const data = await response.json();
            return data.config || {};
        } catch (error) {
            console.error('Failed to get SRP config:', error);
            return {};
        }
    }
}

// Initialize SRP when the module loads
if (typeof window !== 'undefined') {
    SRPAuthentication.initialize().then(supported => {
        if (supported) {
            console.log('SRP Authentication ready');
        }
    });
}

// Bind to window for global access
if (typeof window !== 'undefined') {
    window.SRPAuthentication = SRPAuthentication;
}
