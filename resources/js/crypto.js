import { EncryptionProof } from './zkp/encryption-proof.js';

export class SecureEncryption {
    // Algorithm configuration - removed AES-CBC for security
    static ALGORITHMS = {
        'AES-GCM': {
            name: 'AES-GCM',
            keyLength: 256,
            ivLength: 12, // 96 bits for GCM
            tagLength: 16,
            authenticated: true,
            supported: true
        },
        'ChaCha20-Poly1305': {
            name: 'ChaCha20-Poly1305',
            keyLength: 256,
            ivLength: 12,
            tagLength: 16,
            authenticated: true,
            supported: false // Detected dynamically
        }
    };

    static DEFAULT_ALGORITHM = 'AES-GCM';
    static CURRENT_VERSION = 1; // For future format upgrades
    static MIN_PBKDF2_ITERATIONS = 1000000; // Increased security (>= 1M per OWASP 2024)

    // Browser compatibility detection
    static _browserCompatibility = null;
    static _ivCounters = new Map(); // Track IV usage per key: { set: Set<string>, order: string[] }

    static async _checkBrowserCompatibility() {
        if (this._browserCompatibility !== null) {
            return this._browserCompatibility;
        }

        const compatibility = {
            'AES-GCM': true,
            'ChaCha20-Poly1305': false
        };

        // Check ChaCha20-Poly1305 support
        try {
            await window.crypto.subtle.generateKey(
                { name: 'ChaCha20-Poly1305', length: 256 },
                false,
                ['encrypt', 'decrypt']
            );
            compatibility['ChaCha20-Poly1305'] = true;
            this.ALGORITHMS['ChaCha20-Poly1305'].supported = true;
        } catch (e) {
            // ChaCha20-Poly1305 not supported
        }

        this._browserCompatibility = compatibility;
        return compatibility;
    }

    // Secure algorithm validation
    static _validateAlgorithm(algorithm) {
        if (!algorithm) {
            return this.DEFAULT_ALGORITHM;
        }
        if (!(algorithm in this.ALGORITHMS)) {
            throw new Error(`Unsupported algorithm: ${algorithm}. Supported algorithms: ${Object.keys(this.ALGORITHMS).join(', ')}`);
        }
        
        const config = this.ALGORITHMS[algorithm];
        if (!config.supported) {
            throw new Error(`Algorithm ${algorithm} is not supported in this browser`);
        }
        if (!config.authenticated) {
            throw new Error(`Algorithm ${algorithm} is not authenticated and therefore not secure`);
        }
        
        return algorithm;
    }

    static _getAlgorithmConfig(algorithm) {
        const validatedAlgorithm = this._validateAlgorithm(algorithm);
        return this.ALGORITHMS[validatedAlgorithm];
    }

    // Secure IV generation with uniqueness tracking and validation
    static _generateSecureIV(algorithm, keyId = null) {
        const config = this._getAlgorithmConfig(algorithm);
        let iv;
        let attempts = 0;
        const maxAttempts = 3;
        
        // Generate and validate IV
        while (attempts < maxAttempts) {
            iv = window.crypto.getRandomValues(new Uint8Array(config.ivLength));
            
            // Validate randomness quality
            const validation = this._validateRandomBytes(iv);
            if (validation.valid) {
                break;
            }
            
            console.warn(`IV generation attempt ${attempts + 1} failed validation:`, validation.issues);
            attempts++;
        }
        
        if (attempts >= maxAttempts) {
            console.error('Failed to generate high-quality IV after multiple attempts');
            // Continue with last generated IV but log the issue
            this._logSecurityEvent('low_quality_iv', { algorithm, attempts });
        }
        
        // Track IV usage to detect collisions (basic protection)
        if (keyId) {
            const ivHex = Array.from(iv, b => b.toString(16).padStart(2, '0')).join('');
            const tracker = this._ivCounters.get(keyId) || { set: new Set(), order: [] };

            if (tracker.set.has(ivHex)) {
                console.warn('IV collision detected - consider key rotation');
                // Generate new IV (extremely unlikely when IVs are secure random)
                return this._generateSecureIV(algorithm, keyId);
            }

            tracker.set.add(ivHex);
            tracker.order.push(ivHex);

            // Enforce a bounded LRU window to prevent memory exhaustion
            const MAX_IV_TRACK = 10000;
            if (tracker.order.length > MAX_IV_TRACK) {
                const oldest = tracker.order.shift();
                if (oldest) tracker.set.delete(oldest);
            }
            this._ivCounters.set(keyId, tracker);
        }
        
        return iv;
    }

    // Key generation with proper entropy
    static async generateKey(algorithm = this.DEFAULT_ALGORITHM) {
        await this._checkBrowserCompatibility();
        const algConfig = this._getAlgorithmConfig(algorithm);
        
        return await window.crypto.subtle.generateKey(
            { name: algConfig.name, length: algConfig.keyLength },
            true,
            ['encrypt', 'decrypt']
        );
    }

    // Secure encryption with proper format versioning
    static async encrypt(data, key, algorithm = this.DEFAULT_ALGORITHM, options = {}) {
        await this._checkBrowserCompatibility();
        const algConfig = this._getAlgorithmConfig(algorithm);
        
        // Input validation
        if (!data) {
            throw new Error('Data is required for encryption');
        }
        if (!key) {
            throw new Error('Key is required for encryption');
        }

        // Generate key ID for IV tracking
        const keyId = options.keyId || await this._generateKeyId(key);
        
        // Generate secure IV
        const iv = this._generateSecureIV(algorithm, keyId);
        
        // Prepare data - NO CUSTOM PADDING
        const plaintext = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        
        // Create algorithm parameters
        const algorithmParams = { 
            name: algConfig.name, 
            iv,
            tagLength: 128 // 128 bits for compatibility (AES-GCM)
        };
        if (options.additionalData) {
            const aad = options.additionalData instanceof Uint8Array
                ? options.additionalData
                : (typeof options.additionalData === 'string'
                    ? new TextEncoder().encode(options.additionalData)
                    : new Uint8Array(options.additionalData));
            algorithmParams.additionalData = aad;
        }
        
        // Encrypt with authenticated encryption
        const ciphertext = await window.crypto.subtle.encrypt(algorithmParams, key, plaintext);
        
        // Create secure format with version and algorithm binding
        const result = {
            version: this.CURRENT_VERSION,
            algorithm: algorithm, // Algorithm is bound to ciphertext
            ciphertext: new Uint8Array(ciphertext),
            iv: iv,
            timestamp: Date.now() // For key rotation policies
        };

        return result;
    }

    // Secure decryption with algorithm verification
    static async decrypt(encryptedData, key, options = {}) {
        await this._checkBrowserCompatibility();
        
        // Input validation
        if (!encryptedData || typeof encryptedData !== 'object') {
            throw new Error('Invalid encrypted data format');
        }
        
        const { version, algorithm, ciphertext, iv, timestamp } = encryptedData;
        
        // Validate format version
        if (version !== this.CURRENT_VERSION) {
            throw new Error(`Unsupported format version: ${version}`);
        }
        
        // Validate required fields
        if (!algorithm || !ciphertext || !iv) {
            throw new Error('Missing required fields in encrypted data');
        }
        
        // Get algorithm config (this validates the algorithm)
        const algConfig = this._getAlgorithmConfig(algorithm);
        
        // Validate IV length
        if (iv.length !== algConfig.ivLength) {
            throw new Error(`Invalid IV length for ${algorithm}`);
        }
        
        // Check data age for key rotation policies
        if (options.maxAge && timestamp) {
            const age = Date.now() - timestamp;
            if (age > options.maxAge) {
                throw new Error('Encrypted data is too old - key rotation required');
            }
        }
        
        // Prepare algorithm parameters
        const algorithmParams = { 
            name: algConfig.name, 
            iv: iv,
            tagLength: 128
        };
        if (options.additionalData) {
            const aad = options.additionalData instanceof Uint8Array
                ? options.additionalData
                : (typeof options.additionalData === 'string'
                    ? new TextEncoder().encode(options.additionalData)
                    : new Uint8Array(options.additionalData));
            algorithmParams.additionalData = aad;
        }
        
        try {
            // Decrypt - authentication is verified automatically by authenticated algorithms
            const decrypted = await window.crypto.subtle.decrypt(algorithmParams, key, ciphertext);
            return new Uint8Array(decrypted);
        } catch (error) {
            // Provide consistent error message to prevent oracle attacks
            throw new Error('Decryption failed - invalid ciphertext or key');
        }
    }

    // Key export/import with validation
    static async exportKey(key) {
        if (!key) {
            throw new Error('Key is required for export');
        }
        return new Uint8Array(await window.crypto.subtle.exportKey('raw', key));
    }

    // Generate key ID for IV tracking
    static async _generateKeyId(key) {
        const keyBytes = await this.exportKey(key);
        const hash = await this.hash(keyBytes, 'SHA-256');
        return Array.from(hash.slice(0, 8), b => b.toString(16).padStart(2, '0')).join('');
    }

    // Step 21: Add key import/export for raw bytes
    static async importKey(keyBytes, algorithm = this.DEFAULT_ALGORITHM) {
        // Check browser compatibility first
        await this._checkBrowserCompatibility();
        
        const algConfig = this._getAlgorithmConfig(algorithm);
        
        if (!(keyBytes instanceof Uint8Array)) {
            keyBytes = new Uint8Array(keyBytes);
        }
        
        // Validate key length
        const expectedKeyLength = algConfig.keyLength / 8; // Convert bits to bytes
        if (keyBytes.length !== expectedKeyLength) {
            throw new Error(`Invalid key length for ${algorithm}. Expected ${expectedKeyLength} bytes, got ${keyBytes.length}`);
        }
        
        return await window.crypto.subtle.importKey(
            'raw',
            keyBytes,
            { name: algConfig.name },
            false,
            ['encrypt', 'decrypt']
        );
    }

    // Step 25: Add key format validation and conversion
    static validateKeyFormat(keyBytes, algorithm = this.DEFAULT_ALGORITHM) {
        const algConfig = this._getAlgorithmConfig(algorithm);
        const expectedKeyLength = algConfig.keyLength / 8;
        
        if (!(keyBytes instanceof Uint8Array)) {
            return { valid: false, error: 'Key must be a Uint8Array' };
        }
        
        if (keyBytes.length !== expectedKeyLength) {
            return { 
                valid: false, 
                error: `Invalid key length for ${algorithm}. Expected ${expectedKeyLength} bytes, got ${keyBytes.length}` 
            };
        }
        
        return { valid: true };
    }

    static convertKeyFormat(keyBytes, fromFormat = 'raw', toFormat = 'hex') {
        if (!(keyBytes instanceof Uint8Array)) {
            throw new Error('Key must be a Uint8Array');
        }
        
        switch (toFormat.toLowerCase()) {
            case 'hex':
                return Array.from(keyBytes, byte => byte.toString(16).padStart(2, '0')).join('');
            case 'base64':
                return btoa(String.fromCharCode(...keyBytes));
            case 'base64url':
                return btoa(String.fromCharCode(...keyBytes))
                    .replace(/\+/g, '-')
                    .replace(/\//g, '_')
                    .replace(/=/g, '');
            case 'array':
                return Array.from(keyBytes);
            case 'raw':
            case 'uint8array':
                return keyBytes;
            default:
                throw new Error(`Unsupported output format: ${toFormat}`);
        }
    }

    static parseKeyFormat(keyString, fromFormat = 'hex', algorithm = this.DEFAULT_ALGORITHM) {
        const algConfig = this._getAlgorithmConfig(algorithm);
        const expectedKeyLength = algConfig.keyLength / 8;
        let keyBytes;
        
        switch (fromFormat.toLowerCase()) {
            case 'hex':
                if (!/^[0-9a-fA-F]+$/.test(keyString)) {
                    throw new Error('Invalid hex string');
                }
                if (keyString.length !== expectedKeyLength * 2) {
                    throw new Error(`Invalid hex length. Expected ${expectedKeyLength * 2} characters, got ${keyString.length}`);
                }
                keyBytes = new Uint8Array(keyString.match(/.{2}/g).map(byte => parseInt(byte, 16)));
                break;
            case 'base64':
                try {
                    const binaryString = atob(keyString);
                    keyBytes = new Uint8Array(binaryString.length);
                    for (let i = 0; i < binaryString.length; i++) {
                        keyBytes[i] = binaryString.charCodeAt(i);
                    }
                } catch (e) {
                    throw new Error('Invalid base64 string');
                }
                break;
            case 'base64url':
                try {
                    const base64 = keyString.replace(/-/g, '+').replace(/_/g, '/');
                    const padded = base64 + '='.repeat((4 - base64.length % 4) % 4);
                    const binaryString = atob(padded);
                    keyBytes = new Uint8Array(binaryString.length);
                    for (let i = 0; i < binaryString.length; i++) {
                        keyBytes[i] = binaryString.charCodeAt(i);
                    }
                } catch (e) {
                    throw new Error('Invalid base64url string');
                }
                break;
            case 'array':
                if (!Array.isArray(keyString)) {
                    throw new Error('Input must be an array for array format');
                }
                keyBytes = new Uint8Array(keyString);
                break;
            default:
                throw new Error(`Unsupported input format: ${fromFormat}`);
        }
        
        if (keyBytes.length !== expectedKeyLength) {
            throw new Error(`Invalid key length for ${algorithm}. Expected ${expectedKeyLength} bytes, got ${keyBytes.length}`);
        }
        
        return keyBytes;
    }

    // Step 9: Algorithm selection helper methods (continued)
    static getSupportedAlgorithms() {
        return Object.keys(this.ALGORITHMS).filter(alg => this.ALGORITHMS[alg].supported);
    }

    static isAlgorithmSupported(algorithm) {
        return algorithm in this.ALGORITHMS && this.ALGORITHMS[algorithm].supported;
    }

    static getAlgorithmInfo(algorithm) {
        return this.ALGORITHMS[algorithm] || null;
    }

    // Additional key management utilities
    static async generateKeyPair(algorithm = 'RSA-OAEP', options = {}) {
        // Check browser compatibility first
        await this._checkBrowserCompatibility();
        
        let algorithmParams;
        
        switch (algorithm) {
            case 'RSA-OAEP':
                algorithmParams = {
                    name: 'RSA-OAEP',
                    modulusLength: options.modulusLength || 2048,
                    publicExponent: new Uint8Array([1, 0, 1]),
                    hash: options.hash || 'SHA-256'
                };
                break;
            case 'ECDH':
                algorithmParams = {
                    name: 'ECDH',
                    namedCurve: options.curve || 'P-256'
                };
                break;
            case 'ECDSA':
                algorithmParams = {
                    name: 'ECDSA',
                    namedCurve: options.curve || 'P-256'
                };
                break;
            default:
                throw new Error(`Unsupported asymmetric algorithm: ${algorithm}`);
        }

        // Set correct usages for each algorithm type
        let usages;
        switch (algorithm) {
            case 'RSA-OAEP':
                usages = ['encrypt', 'decrypt'];
                break;
            case 'ECDH':
                usages = ['deriveKey', 'deriveBits'];
                break;
            case 'ECDSA':
                usages = ['sign', 'verify'];
                break;
            default:
                throw new Error(`Unsupported asymmetric algorithm: ${algorithm}`);
        }

        return await window.crypto.subtle.generateKey(
            algorithmParams,
            true,
            usages
        );
    }

    static async generateRandomBytes(length) {
        return window.crypto.getRandomValues(new Uint8Array(length));
    }

    static async hash(data, algorithm = 'SHA-256') {
        const encoded = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        const hashBuffer = await window.crypto.subtle.digest(algorithm, encoded);
        return new Uint8Array(hashBuffer);
    }

    static async importHMACKey(keyBytes, hash = 'SHA-256') {
        return await window.crypto.subtle.importKey(
            'raw', 
            keyBytes, 
            { name: 'HMAC', hash: { name: hash } }, 
            false, 
            ['sign', 'verify']
        );
    }

    static async createHMAC(data, key) {
        const encoded = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        const signature = await window.crypto.subtle.sign({ name: 'HMAC' }, key, encoded);
        return new Uint8Array(signature);
    }

    // Secure key derivation with higher iterations
    static async deriveKeyFromPassword(password, salt, algorithm = this.DEFAULT_ALGORITHM, iterations = this.MIN_PBKDF2_ITERATIONS) {
        // Check browser compatibility first
        await this._checkBrowserCompatibility();
        
        const algConfig = this._getAlgorithmConfig(algorithm);
        
        if (typeof password !== 'string') {
            throw new Error('Password must be a string');
        }
        
        if (!(salt instanceof Uint8Array)) {
            throw new Error('Salt must be a Uint8Array');
        }
        
        if (iterations < this.MIN_PBKDF2_ITERATIONS) {
            throw new Error(`Iterations must be at least ${this.MIN_PBKDF2_ITERATIONS} for security`);
        }
        
        // Import password as key material
        const passwordKey = await window.crypto.subtle.importKey(
            'raw',
            new TextEncoder().encode(password),
            'PBKDF2',
            false,
            ['deriveBits', 'deriveKey']
        );
        
        // Derive key using PBKDF2
        return await window.crypto.subtle.deriveKey(
            {
                name: 'PBKDF2',
                salt: salt,
                iterations: iterations,
                hash: 'SHA-256'
            },
            passwordKey,
            { name: algConfig.name, length: algConfig.keyLength },
            true, // Make extractable for better usability
            ['encrypt', 'decrypt']
        );
    }

    // Step 23: Add key stretching with configurable iterations
    static async generateSalt(length = 16) {
        return window.crypto.getRandomValues(new Uint8Array(length));
    }

    static async deriveKeyWithStretching(password, salt = null, algorithm = this.DEFAULT_ALGORITHM, options = {}) {
        const {
            saltLength = 16,
            memoryUsage = 64 * 1024 * 1024, // 64MB (for future Argon2 implementation)
            parallelism = 1
        } = options;
        // Align with security minimum by default
        const iterations = options.iterations ?? this.MIN_PBKDF2_ITERATIONS;
        
        // Use provided salt or generate new one
        const actualSalt = salt || await this.generateSalt(saltLength);
        
        // For now, use PBKDF2. In the future, could add Argon2 support
        return {
            key: await this.deriveKeyFromPassword(password, actualSalt, algorithm, iterations),
            salt: actualSalt,
            iterations: iterations,
            algorithm: algorithm
        };
    }

    // Step 24: Implement secure key generation for each algorithm
    static async generateSecureKey(algorithm = this.DEFAULT_ALGORITHM, options = {}) {
        // Check browser compatibility first
        await this._checkBrowserCompatibility();
        
        const algConfig = this._getAlgorithmConfig(algorithm);
        
        const {
            extractable = false,
            usages = ['encrypt', 'decrypt'],
            additionalEntropy = null
        } = options;
        
        // Generate base key
        const key = await window.crypto.subtle.generateKey(
            { name: algConfig.name, length: algConfig.keyLength },
            extractable,
            usages
        );
        
        // If additional entropy is provided, mix it in
        if (additionalEntropy && additionalEntropy instanceof Uint8Array) {
            if (!extractable) {
                throw new Error('Cannot mix additionalEntropy when extractable is false');
            }
            
            const exportedKey = await this.exportKey(key);
            const mixedKey = new Uint8Array(exportedKey.length);
            
            for (let i = 0; i < exportedKey.length; i++) {
                mixedKey[i] = exportedKey[i] ^ (additionalEntropy[i % additionalEntropy.length] || 0);
            }
            
            return await this.importKey(mixedKey, algorithm);
        }
        
        return key;
    }

    // Key rotation utilities
    static shouldRotateKey(encryptedData, maxAge = 30 * 24 * 60 * 60 * 1000) { // 30 days default
        if (!encryptedData.timestamp) {
            return true; // Rotate if no timestamp
        }
        return (Date.now() - encryptedData.timestamp) > maxAge;
    }

    // Validate random bytes quality
    static _validateRandomBytes(bytes) {
        const length = bytes.length;
        if (length < 8) {
            return { valid: false, issues: ['Too short for validation'] };
        }
        
        const issues = [];
        
        // Check for all same value
        const firstByte = bytes[0];
        const allSame = bytes.every(b => b === firstByte);
        if (allSame) {
            issues.push('All bytes have same value');
            return { valid: false, issues, critical: true };
        }
        
        // Calculate entropy estimate
        const byteFreq = new Map();
        for (const byte of bytes) {
            byteFreq.set(byte, (byteFreq.get(byte) || 0) + 1);
        }
        
        const uniqueRatio = byteFreq.size / Math.min(length, 256);
        if (uniqueRatio < 0.5) {
            issues.push(`Poor byte distribution: ${uniqueRatio.toFixed(2)} unique ratio`);
        }
        
        // Check for obvious patterns
        let increasingCount = 0;
        let decreasingCount = 0;
        for (let i = 1; i < length; i++) {
            if (bytes[i] === bytes[i-1] + 1) increasingCount++;
            if (bytes[i] === bytes[i-1] - 1) decreasingCount++;
        }
        
        const sequentialRatio = Math.max(increasingCount, decreasingCount) / (length - 1);
        if (sequentialRatio > 0.5) {
            issues.push(`Sequential pattern detected: ${(sequentialRatio * 100).toFixed(1)}%`);
        }
        
        return {
            valid: issues.length === 0,
            issues,
            entropy: {
                uniqueBytes: byteFreq.size,
                uniqueRatio,
                sequentialRatio
            }
        };
    }
    
    // Log security events for monitoring
    static _logSecurityEvent(event, data) {
        const timestamp = new Date().toISOString();
        const logEntry = { event, data, timestamp };
        
        // Store in session storage for debugging
        try {
            const logs = JSON.parse(sessionStorage.getItem('crypto_security_logs') || '[]');
            logs.push(logEntry);
            // Keep only last 100 entries
            if (logs.length > 100) logs.shift();
            sessionStorage.setItem('crypto_security_logs', JSON.stringify(logs));
        } catch (e) {
            console.error('Failed to log security event:', e);
        }
        
        // In production, send critical events to server
        if (event === 'low_quality_iv' || event === 'key_rotation_needed') {
            // TODO: Implement server reporting
        }
    }
    
    // Clear sensitive data from memory (best effort in JavaScript)
    static clearSensitiveData(data) {
        if (data instanceof Uint8Array) {
            data.fill(0);
        }
    }

    // Version and security info
    static getSecurityInfo() {
        return {
            version: this.CURRENT_VERSION,
            supportedAlgorithms: this.getSupportedAlgorithms(),
            minPBKDF2Iterations: this.MIN_PBKDF2_ITERATIONS,
            securityFeatures: [
                'Authenticated encryption only',
                'IV collision detection',
                'Algorithm binding',
                'Key rotation support',
                'Secure key derivation',
                'Input validation'
            ]
        };
    }

    // Encrypt and produce a ZK proof (client-side) using snarkjs
    static async encryptWithZK(data, key, algorithm = this.DEFAULT_ALGORITHM, options = {}, zkOptions = {}) {
        // 1) Perform authenticated encryption
        const encrypted = await this.encrypt(data, key, algorithm, options);
        const plaintext = typeof data === 'string' ? new TextEncoder().encode(data) : data;
        const plaintextHash = await this.hash(plaintext, 'SHA-256');
        const ciphertextHash = await this.hash(encrypted.ciphertext, 'SHA-256');
        const aad = options.additionalData || null;
        let aadBytes = null;
        if (aad) {
            aadBytes = (aad instanceof Uint8Array)
                ? aad
                : (typeof aad === 'string'
                    ? new TextEncoder().encode(aad)
                    : new Uint8Array(aad));
        }
        const additionalDataHash = aadBytes ? await this.hash(aadBytes, 'SHA-256') : null;

        // 2) Build circuit input for Poseidon preimage commit
        const isCommitOnly = !!(zkOptions && zkOptions.wasmUrl && zkOptions.wasmUrl.toLowerCase().includes('commit_only'));
        const builder = zkOptions.buildInput || ((ctx) => {
            const N = 15;
            const pt = Array.from(ctx.plaintext);
            const plainArr = pt.slice(0, N);
            while (plainArr.length < N) plainArr.push(0);
            if (isCommitOnly) {
                // commit_only circuit expects ONLY plaintext and nonce
                return {
                    plaintext: plainArr,
                    nonce: ctx.nonce,
                };
            }
            // Extended circuits may mirror metadata; provide full shape
            return {
                plaintext: plainArr,
                nonce: ctx.nonce,
                ciphertextHash: Array.from(ctx.ciphertextHash || []),
                iv: Array.from(ctx.iv || []),
                plaintextLength: ctx.plaintext.length,
                // Always provide 32 items (zeros) when AAD is absent to match circuit input shape
                additionalDataHash: ctx.additionalDataHash ? Array.from(ctx.additionalDataHash) : new Array(32).fill(0),
            };
        });

        // Generate a random nonce for the commitment
        const nonceBytes = new Uint8Array(4);
        window.crypto.getRandomValues(nonceBytes);
        const nonce = (nonceBytes[0] | (nonceBytes[1] << 8) | (nonceBytes[2] << 16) | (nonceBytes[3] << 24)) >>> 0;

        const circuitInput = zkOptions.input || builder({
            plaintext,
            ciphertext: encrypted.ciphertext,
            ciphertextHash,
            iv: encrypted.iv,
            algorithm,
            additionalDataHash,
            nonce,
        });

        // 3) Generate ZK proof if artifacts provided (non-fatal on failure)
        let zk = null;
        if (zkOptions.wasmUrl && zkOptions.zkeyUrl) {
            try {
                console.log('[ZK] Starting proof generation with:', {
                    wasmUrl: zkOptions.wasmUrl,
                    zkeyUrl: zkOptions.zkeyUrl,
                    inputKeys: Object.keys(circuitInput),
                    loaderOptions: zkOptions.loaderOptions
                });
                
                const { proof, publicSignals } = await EncryptionProof.generateProof({
                    input: circuitInput,
                    wasmUrl: zkOptions.wasmUrl,
                    zkeyUrl: zkOptions.zkeyUrl,
                    loaderOptions: zkOptions.loaderOptions || {},
                });
                
                console.log('[ZK] Proof generated successfully:', {
                    proofKeys: proof ? Object.keys(proof) : null,
                    publicSignalsLength: publicSignals ? publicSignals.length : 0,
                    firstSignal: publicSignals ? publicSignals[0] : null
                });
                
                zk = { proof, publicSignals };
            } catch (e) {
                console.error('[ZK] Proof generation failed:', e);
                console.error('[ZK] Error details:', {
                    message: e.message,
                    stack: e.stack,
                    wasmUrl: zkOptions.wasmUrl,
                    zkeyUrl: zkOptions.zkeyUrl
                });
                zk = null;
            }
        } else {
            console.log('[ZK] Skipping proof generation - missing artifacts:', {
                hasWasmUrl: !!zkOptions.wasmUrl,
                hasZkeyUrl: !!zkOptions.zkeyUrl
            });
        }

        return {
            encrypted,
            commitments: {
                plaintextHash,
                ciphertextHash,
                iv: encrypted.iv,
                algorithm,
                timestamp: encrypted.timestamp,
                additionalDataHash,
                // If zk present, first publicSignal is the Poseidon commit
                commit: zk && zk.publicSignals ? zk.publicSignals[0] : null,
            },
            zk,
        };
    }

    // Verify a provided ZK proof (client-side helper)
    static async verifyEncryptionProof({ vkeyJson, vkeyUrl, proof, publicSignals, loaderOptions = {} }) {
        if (!proof || !publicSignals) throw new Error('Missing proof or publicSignals');
        return EncryptionProof.verifyProof({ vkeyJson, vkeyUrl, proof, publicSignals, loaderOptions });
    }

    // Generate a ZK proof for an EXISTING ciphertext/iv using the provided plaintext
    // without re-encrypting. Useful to attach proofs retroactively on show pages.
    static async generateProofForCiphertext({
        plaintext,
        ciphertext,
        iv,
        algorithm = this.DEFAULT_ALGORITHM,
        additionalData = null,
        zkOptions = {},
    }) {
        if (!(plaintext instanceof Uint8Array)) plaintext = new Uint8Array(plaintext);
        if (!(ciphertext instanceof Uint8Array)) ciphertext = new Uint8Array(ciphertext);
        if (!(iv instanceof Uint8Array)) iv = new Uint8Array(iv);

        const plaintextHash = await this.hash(plaintext, 'SHA-256');
        const ciphertextHash = await this.hash(ciphertext, 'SHA-256');
        let additionalDataHash = null;
        if (additionalData) {
            const aad = additionalData instanceof Uint8Array
                ? additionalData
                : (typeof additionalData === 'string'
                    ? new TextEncoder().encode(additionalData)
                    : new Uint8Array(additionalData));
            additionalDataHash = await this.hash(aad, 'SHA-256');
        }

        const isCommitOnly = !!(zkOptions && zkOptions.wasmUrl && zkOptions.wasmUrl.toLowerCase().includes('commit_only'));
        const builder = zkOptions.buildInput || ((ctx) => {
            const N = 15;
            const pt = Array.from(ctx.plaintext);
            const plainArr = pt.slice(0, N);
            while (plainArr.length < N) plainArr.push(0);
            if (isCommitOnly) {
                // commit_only circuit expects ONLY plaintext and nonce
                return {
                    plaintext: plainArr,
                    nonce: ctx.nonce,
                };
            }
            // Extended circuits may mirror metadata; provide full shape
            return {
                plaintext: plainArr,
                nonce: ctx.nonce,
                ciphertextHash: Array.from(ctx.ciphertextHash || []),
                iv: Array.from(ctx.iv || []),
                plaintextLength: ctx.plaintext.length,
                // Always provide 32 items (zeros) when AAD is absent to match circuit input shape
                additionalDataHash: ctx.additionalDataHash ? Array.from(ctx.additionalDataHash) : new Array(32).fill(0),
            };
        });

        // Random nonce for commitment
        const nonceBytes = new Uint8Array(4);
        window.crypto.getRandomValues(nonceBytes);
        const nonce = (nonceBytes[0] | (nonceBytes[1] << 8) | (nonceBytes[2] << 16) | (nonceBytes[3] << 24)) >>> 0;

        const circuitInput = zkOptions.input || builder({
            plaintext,
            plaintextHash,
            ciphertext,
            ciphertextHash,
            iv,
            algorithm,
            additionalDataHash,
            nonce,
        });

        if (!zkOptions.wasmUrl || !zkOptions.zkeyUrl) {
            return { zk: null, commitments: { plaintextHash, ciphertextHash, iv, algorithm, additionalDataHash } };
        }

        const { proof, publicSignals } = await EncryptionProof.generateProof({
            input: circuitInput,
            wasmUrl: zkOptions.wasmUrl,
            zkeyUrl: zkOptions.zkeyUrl,
            loaderOptions: zkOptions.loaderOptions || {},
        });

        return {
            zk: { proof, publicSignals },
            commitments: { plaintextHash, ciphertextHash, iv, algorithm, additionalDataHash, commit: publicSignals && publicSignals[0] ? publicSignals[0] : null },
        };
    }

    // Backward compatibility layer
    static async encryptLegacy(data, key) {
        // Maintain exact same behavior as before for backward compatibility
        return this.encrypt(data, key, 'AES-GCM');
    }

    static async decryptLegacy(encryptedData, key) {
        // Maintain exact same behavior as before for backward compatibility
        return this.decrypt(encryptedData, key);
    }
}

// Bind to window for blade inline usage until fully modularized
if (typeof window !== 'undefined') {
    window.SecureEncryption = SecureEncryption;
}


