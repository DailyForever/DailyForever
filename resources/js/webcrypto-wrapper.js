/**
 * Enhanced Web Crypto API Wrapper
 * Provides additional functionality and utilities for Web Crypto API
 */

export class WebCryptoWrapper {
    static SUPPORTED_ALGORITHMS = {
        // Symmetric encryption
        'AES-GCM': { name: 'AES-GCM', keyLengths: [128, 192, 256], ivLength: 12, defaultUsages: ['encrypt', 'decrypt'], authenticated: true },
        'AES-CBC': { name: 'AES-CBC', keyLengths: [128, 192, 256], ivLength: 16, defaultUsages: ['encrypt', 'decrypt'], authenticated: false, deprecated: true },
        'AES-CTR': { name: 'AES-CTR', keyLengths: [128, 192, 256], ivLength: 16, defaultUsages: ['encrypt', 'decrypt'], authenticated: false, deprecated: true },
        'ChaCha20-Poly1305': { name: 'ChaCha20-Poly1305', keyLengths: [256], ivLength: 12, defaultUsages: ['encrypt', 'decrypt'], experimental: true },
        
        // Asymmetric encryption
        'RSA-OAEP': { name: 'RSA-OAEP', keyLengths: [2048, 3072, 4096], defaultUsages: ['encrypt', 'decrypt'], minKeyLength: 2048 },
        'RSA-PSS': { name: 'RSA-PSS', keyLengths: [2048, 3072, 4096], defaultUsages: ['sign', 'verify'], minKeyLength: 2048 },
        
        // Key agreement
        'ECDH': { name: 'ECDH', curves: ['P-256', 'P-384', 'P-521'], defaultUsages: ['deriveKey', 'deriveBits'] },
        'X25519': { name: 'X25519', defaultUsages: ['deriveKey', 'deriveBits'], experimental: true },
        'X448': { name: 'X448', defaultUsages: ['deriveKey', 'deriveBits'], experimental: true },
        
        // Digital signatures
        'ECDSA': { name: 'ECDSA', curves: ['P-256', 'P-384', 'P-521'], defaultUsages: ['sign', 'verify'] },
        'Ed25519': { name: 'Ed25519', defaultUsages: ['sign', 'verify'], experimental: true },
        'Ed448': { name: 'Ed448', defaultUsages: ['sign', 'verify'], experimental: true },
        
        // Hashing (removed SHA-1 for security)
        'SHA-256': { name: 'SHA-256' },
        'SHA-384': { name: 'SHA-384' },
        'SHA-512': { name: 'SHA-512' },
        
        // Key derivation
        'PBKDF2': { name: 'PBKDF2', defaultUsages: ['deriveBits', 'deriveKey'], minIterations: 1000000 },
        'HKDF': { name: 'HKDF', defaultUsages: ['deriveBits', 'deriveKey'] },
        
        // MAC
        'HMAC': { name: 'HMAC', defaultUsages: ['sign', 'verify'] }
    };

    static async checkSupport() {
        const support = {};
        const tests = Object.entries(this.SUPPORTED_ALGORITHMS).map(async ([algName, config]) => {
            try {
                if (['AES-GCM', 'AES-CBC', 'AES-CTR', 'ChaCha20-Poly1305'].includes(algName)) {
                    const length = (config.keyLengths && config.keyLengths[0]) || 256;
                    await crypto.subtle.generateKey({ name: config.name, length }, false, config.defaultUsages);
                } else if (algName === 'HMAC') {
                    await crypto.subtle.generateKey({ name: 'HMAC', hash: 'SHA-256', length: 256 }, false, ['sign', 'verify']);
                } else if (algName === 'PBKDF2') {
                    const base = await crypto.subtle.importKey('raw', new Uint8Array([1,2,3]), 'PBKDF2', false, ['deriveBits']);
                    await crypto.subtle.deriveBits({ name: 'PBKDF2', salt: new Uint8Array(8), iterations: 100000, hash: 'SHA-256' }, base, 128);
                } else if (algName === 'HKDF') {
                    const base = await crypto.subtle.importKey('raw', new Uint8Array([4,5,6]), 'HKDF', false, ['deriveBits']);
                    await crypto.subtle.deriveBits({ name: 'HKDF', salt: new Uint8Array(8), info: new Uint8Array(0), hash: 'SHA-256' }, base, 128);
                } else if (algName === 'RSA-OAEP' || algName === 'RSA-PSS') {
                    const usages = algName === 'RSA-OAEP' ? ['encrypt', 'decrypt'] : ['sign', 'verify'];
                    await crypto.subtle.generateKey({ name: config.name, modulusLength: 2048, publicExponent: new Uint8Array([1,0,1]), hash: 'SHA-256' }, false, usages);
                } else if (algName === 'ECDH' || algName === 'ECDSA') {
                    const usages = algName === 'ECDH' ? ['deriveKey', 'deriveBits'] : ['sign', 'verify'];
                    await crypto.subtle.generateKey({ name: config.name, namedCurve: (config.curves && config.curves[0]) || 'P-256' }, false, usages);
                } else if (algName.startsWith('SHA-')) {
                    await crypto.subtle.digest(config.name, new Uint8Array([1,2,3]));
                } else {
                    // Experimental/unsupported (Ed25519, X25519, etc.)
                    await crypto.subtle.generateKey({ name: config.name }, false, config.defaultUsages || []);
                }
                return [algName, true];
            } catch {
                return [algName, false];
            }
        });
        const results = await Promise.allSettled(tests);
        for (const r of results) {
            if (r.status === 'fulfilled') {
                const [name, ok] = r.value;
                support[name] = ok;
            }
        }
        return support;
    }

    // Enhanced key generation with more options
    static async generateKey(algorithm, options = {}) {
        const config = this.SUPPORTED_ALGORITHMS[algorithm];
        if (!config) {
            throw new Error(`Unsupported algorithm: ${algorithm}`);
        }

        const {
            extractable = false, // Changed to false for security
            usages = config.defaultUsages || ['encrypt', 'decrypt'],
            keyLength = null,
            namedCurve = null,
            hash = 'SHA-256'
        } = options;

        // Validate key length
        if (keyLength && config.minKeyLength && keyLength < config.minKeyLength) {
            throw new Error(`Key length ${keyLength} is below minimum ${config.minKeyLength} for ${algorithm}`);
        }

        let keyParams = { name: algorithm };
        
        if (keyLength) {
            keyParams.length = keyLength;
        }
        
        if (namedCurve) {
            keyParams.namedCurve = namedCurve;
        }
        
        if (hash && ['RSA-OAEP', 'RSA-PSS', 'ECDSA', 'HMAC'].includes(algorithm)) {
            keyParams.hash = hash;
        }

        // Fix for RSA algorithms - ensure modulusLength is set
        if (algorithm === 'RSA-OAEP' || algorithm === 'RSA-PSS') {
            keyParams.modulusLength = keyLength || 2048;
            keyParams.publicExponent = new Uint8Array([1, 0, 1]);
        }

        return await crypto.subtle.generateKey(keyParams, extractable, usages);
    }

    // Enhanced encryption with more algorithms
    static async encrypt(algorithm, key, data, options = {}) {
        const {
            iv = null,
            additionalData = null,
            tagLength = null
        } = options;

        const config = this.SUPPORTED_ALGORITHMS[algorithm];
        if (!config) throw new Error(`Unsupported algorithm: ${algorithm}`);
        if (config.authenticated === false && !options.allowUnauthenticated) {
            throw new Error(`${algorithm} is unauthenticated and not allowed by default. Use AES-GCM or set allowUnauthenticated=true with a separate MAC.`);
        }

        let algorithmParams = { name: algorithm };
        
        if (iv) {
            algorithmParams.iv = iv;
        }
        
        if (additionalData) {
            algorithmParams.additionalData = additionalData;
        }
        
        if (tagLength) {
            algorithmParams.tagLength = tagLength;
        }

        const plaintext = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        return await crypto.subtle.encrypt(algorithmParams, key, plaintext);
    }

    // Enhanced decryption
    static async decrypt(algorithm, key, ciphertext, options = {}) {
        const {
            iv = null,
            additionalData = null,
            tagLength = null
        } = options;

        const config = this.SUPPORTED_ALGORITHMS[algorithm];
        if (!config) throw new Error(`Unsupported algorithm: ${algorithm}`);
        if (config.authenticated === false && !options.allowUnauthenticated) {
            throw new Error(`${algorithm} is unauthenticated and not allowed by default. Use AES-GCM or set allowUnauthenticated=true with a separate MAC.`);
        }

        let algorithmParams = { name: algorithm };
        
        if (iv) {
            algorithmParams.iv = iv;
        }
        
        if (additionalData) {
            algorithmParams.additionalData = additionalData;
        }
        
        if (tagLength) {
            algorithmParams.tagLength = tagLength;
        }

        return await crypto.subtle.decrypt(algorithmParams, key, ciphertext);
    }

    // Enhanced key import with more formats
    static async importKey(format, keyData, algorithm, options = {}) {
        const {
            extractable = true,
            usages = ['encrypt', 'decrypt']
        } = options;

        let algorithmParams = { name: algorithm };
        
        if (algorithm === 'HMAC' && options.hash) {
            algorithmParams.hash = options.hash;
        }
        
        if (algorithm === 'RSA-OAEP' && options.hash) {
            algorithmParams.hash = options.hash;
        }

        // Fix for PBKDF2 - make it non-extractable
        if (algorithm === 'PBKDF2') {
            return await crypto.subtle.importKey(format, keyData, algorithmParams, false, ['deriveBits', 'deriveKey']);
        }

        return await crypto.subtle.importKey(format, keyData, algorithmParams, extractable, usages);
    }

    // Enhanced key export
    static async exportKey(format, key) {
        return await crypto.subtle.exportKey(format, key);
    }

    // Key derivation with more algorithms
    static async deriveKey(algorithm, baseKey, derivedKeyType, options = {}) {
        const config = this.SUPPORTED_ALGORITHMS[algorithm];
        if (!config) {
            throw new Error(`Unsupported algorithm: ${algorithm}`);
        }

        const {
            extractable = false, // Changed to false for security
            usages = config.defaultUsages || ['encrypt', 'decrypt']
        } = options;

        let algorithmParams = { name: algorithm };
        
        if (algorithm === 'PBKDF2') {
            if (!options.salt) {
                throw new Error('Salt is required for PBKDF2');
            }
            if (!options.iterations) {
                throw new Error('Iterations is required for PBKDF2');
            }
            if (config.minIterations && options.iterations < config.minIterations) {
                throw new Error(`Iterations ${options.iterations} is below minimum ${config.minIterations} for PBKDF2`);
            }
            algorithmParams.salt = options.salt;
            algorithmParams.iterations = options.iterations;
            algorithmParams.hash = options.hash || 'SHA-256';
        } else if (algorithm === 'HKDF') {
            if (!options.salt) {
                throw new Error('Salt is required for HKDF');
            }
            algorithmParams.salt = options.salt;
            algorithmParams.info = options.info;
            algorithmParams.hash = options.hash || 'SHA-256';
        } else if (algorithm === 'ECDH') {
            if (!options.publicKey) {
                throw new Error('Public key is required for ECDH');
            }
            algorithmParams.public = options.publicKey;
        }

        return await crypto.subtle.deriveKey(algorithmParams, baseKey, derivedKeyType, extractable, usages);
    }

    // Digital signatures
    static async sign(algorithm, key, data, options = {}) {
        let algorithmParams = { name: algorithm };
        
        if (algorithm === 'RSA-PSS') {
            algorithmParams.saltLength = options.saltLength || 32;
        }
        
        if (algorithm === 'ECDSA') {
            algorithmParams.hash = options.hash || 'SHA-256';
        }

        const message = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        
        // FIXED: ECDSA already hashes internally, no manual pre-hash needed
        return await crypto.subtle.sign(algorithmParams, key, message);
    }

    // Signature verification
    static async verify(algorithm, key, signature, data, options = {}) {
        let algorithmParams = { name: algorithm };
        
        if (algorithm === 'RSA-PSS') {
            algorithmParams.saltLength = options.saltLength || 32;
        }
        
        if (algorithm === 'ECDSA') {
            algorithmParams.hash = options.hash || 'SHA-256';
        }

        const message = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        
        // FIXED: ECDSA already hashes internally, no manual pre-hash needed
        return await crypto.subtle.verify(algorithmParams, key, signature, message);
    }

    // Hashing
    static async digest(algorithm, data) {
        const message = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        return await crypto.subtle.digest(algorithm, message);
    }

    // Random number generation
    static getRandomValues(array) {
        return crypto.getRandomValues(array);
    }

    // Utility functions
    static async generateRandomBytes(length) {
        return crypto.getRandomValues(new Uint8Array(length));
    }

    static async generateRandomIV(algorithm) {
        const config = this.SUPPORTED_ALGORITHMS[algorithm];
        if (!config || !config.ivLength) {
            throw new Error(`Algorithm ${algorithm} does not support IV generation`);
        }
        return crypto.getRandomValues(new Uint8Array(config.ivLength));
    }

    // IV safety helper - automatically generates fresh IV and returns both ciphertext and IV
    static async encryptWithFreshIV(algorithm, key, data, options = {}) {
        const config = this.SUPPORTED_ALGORITHMS[algorithm];
        if (!config || !config.ivLength) {
            throw new Error(`Algorithm ${algorithm} does not support IV generation`);
        }

        const iv = await this.generateRandomIV(algorithm);
        const ciphertext = await this.encrypt(algorithm, key, data, { ...options, iv });
        
        return {
            ciphertext,
            iv,
            algorithm
        };
    }

    // Helper to decrypt with IV from encryptWithFreshIV result
    static async decryptWithIV(encryptedData, key) {
        const { ciphertext, iv, algorithm } = encryptedData;
        return await this.decrypt(algorithm, key, ciphertext, { iv });
    }

    // Key wrapping/unwrapping
    static async wrapKey(format, key, wrappingKey, algorithm) {
        return await crypto.subtle.wrapKey(format, key, wrappingKey, algorithm);
    }

    static async unwrapKey(format, wrappedKey, unwrappingKey, algorithm, unwrappedKeyType, options = {}) {
        const {
            extractable = true,
            usages = ['encrypt', 'decrypt']
        } = options;

        return await crypto.subtle.unwrapKey(
            format, wrappedKey, unwrappingKey, algorithm, unwrappedKeyType, extractable, usages
        );
    }

    // Utility for converting between formats
    static arrayBufferToBase64(buffer) {
        const bytes = new Uint8Array(buffer);
        // FIXED: Use chunked approach for better performance with large buffers
        const CHUNK_SIZE = 0x8000; // 32KB chunks
        let binary = '';
        
        for (let i = 0; i < bytes.length; i += CHUNK_SIZE) {
            const chunk = bytes.subarray(i, i + CHUNK_SIZE);
            binary += String.fromCharCode.apply(null, chunk);
        }
        
        return btoa(binary);
    }

    static base64ToArrayBuffer(base64) {
        const binary = atob(base64);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes.buffer;
    }

    static arrayBufferToHex(buffer) {
        const bytes = new Uint8Array(buffer);
        return Array.from(bytes, byte => byte.toString(16).padStart(2, '0')).join('');
    }

    static hexToArrayBuffer(hex) {
        const bytes = new Uint8Array(hex.length / 2);
        for (let i = 0; i < hex.length; i += 2) {
            bytes[i / 2] = parseInt(hex.substr(i, 2), 16);
        }
        return bytes.buffer;
    }

    // Performance testing utilities
    static async benchmarkEncryption(algorithm, keyLength, dataSize, iterations = 100) {
        const key = await this.generateKey(algorithm, { keyLength });
        const data = new Uint8Array(dataSize);
        crypto.getRandomValues(data);
        
        const start = performance.now();
        
        for (let i = 0; i < iterations; i++) {
            const iv = await this.generateRandomIV(algorithm);
            await this.encrypt(algorithm, key, data, { iv });
        }
        
        const end = performance.now();
        const totalTime = end - start;
        const avgTime = totalTime / iterations;
        const throughput = (dataSize * iterations) / (totalTime / 1000); // bytes per second
        
        return {
            algorithm,
            keyLength,
            dataSize,
            iterations,
            totalTime,
            avgTime,
            throughput
        };
    }

    // Security utilities
    static async constantTimeEquals(a, b) {
        if (a.length !== b.length) {
            return false;
        }
        
        let result = 0;
        for (let i = 0; i < a.length; i++) {
            result |= a[i] ^ b[i];
        }
        return result === 0;
    }

    static async secureCompare(a, b) {
        const aBuffer = typeof a === 'string' ? new TextEncoder().encode(a) : a;
        const bBuffer = typeof b === 'string' ? new TextEncoder().encode(b) : b;
        return this.constantTimeEquals(aBuffer, bBuffer);
    }

    // Get algorithm information and security warnings
    static getAlgorithmInfo(algorithm) {
        const config = this.SUPPORTED_ALGORITHMS[algorithm];
        if (!config) {
            return { supported: false, error: `Algorithm ${algorithm} is not supported` };
        }

        const info = {
            name: config.name,
            supported: true,
            experimental: config.experimental || false,
            defaultUsages: config.defaultUsages,
            keyLengths: config.keyLengths,
            curves: config.curves,
            ivLength: config.ivLength,
            minKeyLength: config.minKeyLength,
            minIterations: config.minIterations,
            warnings: []
        };

        // Add security warnings
        if (config.experimental) {
            info.warnings.push('This algorithm is experimental and may not be supported in all environments');
        }

        if (config.minKeyLength && config.minKeyLength < 2048) {
            info.warnings.push(`Key length should be at least ${config.minKeyLength} bits for security`);
        }

        if (config.minIterations && config.minIterations < 100000) {
            info.warnings.push(`PBKDF2 iterations should be at least ${config.minIterations} for security`);
        }

        if (config.authenticated === false) {
            info.warnings.push(`${algorithm} is not an authenticated cipher; use AES-GCM instead or pair with a secure MAC (encrypt-then-MAC).`);
        }

        if (config.deprecated) {
            info.warnings.push(`${algorithm} is deprecated in this wrapper and may be blocked by default.`);
        }

        return info;
    }

    // Get all supported algorithms with their information
    static getAllAlgorithms() {
        const algorithms = {};
        for (const [name, config] of Object.entries(this.SUPPORTED_ALGORITHMS)) {
            algorithms[name] = this.getAlgorithmInfo(name);
        }
        return algorithms;
    }
}

// Bind to window for global access
if (typeof window !== 'undefined') {
    window.WebCryptoWrapper = WebCryptoWrapper;
}
