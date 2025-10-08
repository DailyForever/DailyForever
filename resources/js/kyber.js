import { MlKem512, MlKem768, MlKem1024 } from 'mlkem';

// Internal helpers
function toU8(input, name = 'data') {
    if (input instanceof Uint8Array) return input;
    if (ArrayBuffer.isView(input)) return new Uint8Array(input.buffer, input.byteOffset, input.byteLength);
    if (input instanceof ArrayBuffer) return new Uint8Array(input);
    if (Array.isArray(input)) return new Uint8Array(input);
    throw new TypeError(`${name} must be a Uint8Array, ArrayBufferView, ArrayBuffer or number[]`);
}

const HKDF_INFO = new TextEncoder().encode('kyber-aes-key');

export class PostQuantumKEM {
    static _getKem(alg) {
        const s = String(alg || 'ML-KEM-512').toUpperCase();
        if (s.includes('1024')) return MlKem1024;
        if (s.includes('768')) return MlKem768;
        return MlKem512;
    }

    static async generateKeypair(alg = 'ML-KEM-512') {
        const Klass = this._getKem(alg);
        const kem = new Klass();
        const kp = await kem.generateKeyPair();
        let publicKey, secretKey;
        if (Array.isArray(kp)) {
            [publicKey, secretKey] = kp;
        } else {
            publicKey = kp.publicKey || kp.pk || kp[0];
            secretKey = kp.secretKey || kp.sk || kp[1];
        }
        return { publicKey, secretKey };
    }

    static async encapsulate(publicKey, alg = 'ML-KEM-512') {
        const Klass = this._getKem(alg);
        const kem = new Klass();
        const out = await kem.encap(toU8(publicKey, 'publicKey'));
        let ciphertext, sharedSecret;
        if (Array.isArray(out)) {
            [ciphertext, sharedSecret] = out;
        } else {
            ciphertext = out.ciphertext || out.ct || out[0];
            sharedSecret = out.sharedSecret || out.shared || out.ss || out[1];
        }
        return { ciphertext: toU8(ciphertext, 'ciphertext'), sharedSecret: toU8(sharedSecret, 'sharedSecret') };
    }

    static async decapsulate(ciphertext, secretKey, alg = 'ML-KEM-512') {
        const Klass = this._getKem(alg);
        const kem = new Klass();
        return await kem.decap(toU8(ciphertext, 'ciphertext'), toU8(secretKey, 'secretKey'));
    }

    static async encryptForRecipient(content, recipientPublicKey, alg = 'ML-KEM-512') {
        // Generate ephemeral AES key
        const aesKey = await window.crypto.subtle.generateKey(
            { name: 'AES-GCM', length: 256 },
            true,
            ['encrypt', 'decrypt']
        );

        // Encrypt content with AES
        const iv = window.crypto.getRandomValues(new Uint8Array(12));
        const encodedContent = new TextEncoder().encode(content);
        const encryptedContent = await window.crypto.subtle.encrypt(
            { name: 'AES-GCM', iv },
            aesKey,
            encodedContent
        );

        // Encapsulate AES key with recipient's Kyber public key
        const { ciphertext: kemCiphertext, sharedSecret } = await this.encapsulate(toU8(recipientPublicKey, 'recipientPublicKey'), alg);
        
        // Use shared secret to encrypt the AES key
        const aesKeyBytes = await window.crypto.subtle.exportKey('raw', aesKey);
        const encryptedAesKey = await this.encryptWithSharedSecret(aesKeyBytes, sharedSecret);

        return {
            encryptedContent: Array.from(new Uint8Array(encryptedContent)),
            iv: Array.from(iv),
            kemCiphertext: Array.from(kemCiphertext),
            encryptedAesKey: Array.from(encryptedAesKey),
        };
    }

    static async decryptFromSender(encryptedData, secretKey, alg = 'ML-KEM-512') {
        const { encryptedContent, iv, kemCiphertext, encryptedAesKey } = encryptedData;
        
        // Decapsulate to get shared secret
        const sharedSecret = await this.decapsulate(toU8(kemCiphertext, 'kemCiphertext'), toU8(secretKey, 'secretKey'), alg);
        
        // Decrypt AES key with shared secret
        const aesKeyBytes = await this.decryptWithSharedSecret(toU8(encryptedAesKey, 'encryptedAesKey'), sharedSecret);
        
        // Import AES key
        const aesKey = await window.crypto.subtle.importKey(
            'raw',
            aesKeyBytes,
            { name: 'AES-GCM', length: 256 },
            false,
            ['decrypt']
        );

        // Decrypt content
        const decryptedContent = await window.crypto.subtle.decrypt(
            { name: 'AES-GCM', iv: toU8(iv, 'iv') },
            aesKey,
            toU8(encryptedContent, 'encryptedContent')
        );
        return new TextDecoder().decode(decryptedContent);
    }

    static async encryptWithSharedSecret(data, sharedSecret) {
        // Use HKDF to derive encryption key from shared secret with random salt (embedded)
        const hkdfSalt = window.crypto.getRandomValues(new Uint8Array(32));
        const key = await window.crypto.subtle.importKey(
            'raw',
            sharedSecret,
            { name: 'HKDF' },
            false,
            ['deriveBits']
        );

        const derivedKey = await window.crypto.subtle.deriveBits(
            {
                name: 'HKDF',
                hash: 'SHA-256',
                salt: hkdfSalt,
                info: HKDF_INFO,
            },
            key,
            256
        );

        const encryptionKey = await window.crypto.subtle.importKey(
            'raw',
            derivedKey,
            { name: 'AES-GCM', length: 256 },
            false,
            ['encrypt']
        );

        const iv = window.crypto.getRandomValues(new Uint8Array(12));
        const encrypted = await window.crypto.subtle.encrypt(
            { name: 'AES-GCM', iv },
            encryptionKey,
            data
        );

        // Embed salt (32) + iv (12) + ciphertext for backward-compatible single field
        return new Uint8Array([...hkdfSalt, ...iv, ...new Uint8Array(encrypted)]);
    }

    static async decryptWithSharedSecret(encryptedDataIn, sharedSecretIn) {
        const encryptedData = toU8(encryptedDataIn, 'encryptedData');
        const sharedSecret = toU8(sharedSecretIn, 'sharedSecret');

        // Try new format first: [32 salt][12 iv][ciphertext]
        const tryNew = async () => {
            if (encryptedData.length < 32 + 12 + 16) throw new Error('ciphertext too short');
            const hkdfSalt = encryptedData.slice(0, 32);
            const iv = encryptedData.slice(32, 44);
            const ciphertext = encryptedData.slice(44);

            const key = await window.crypto.subtle.importKey('raw', sharedSecret, { name: 'HKDF' }, false, ['deriveBits']);
            const derivedKey = await window.crypto.subtle.deriveBits({ name: 'HKDF', hash: 'SHA-256', salt: hkdfSalt, info: HKDF_INFO }, key, 256);
            const decryptionKey = await window.crypto.subtle.importKey('raw', derivedKey, { name: 'AES-GCM', length: 256 }, false, ['decrypt']);
            const decrypted = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv }, decryptionKey, ciphertext);
            return new Uint8Array(decrypted);
        };

        // Legacy format: [12 iv][ciphertext], salt is zeros
        const tryLegacy = async () => {
            if (encryptedData.length < 12 + 16) throw new Error('legacy ciphertext too short');
            const hkdfSalt = new Uint8Array(32); // zeros
            const iv = encryptedData.slice(0, 12);
            const ciphertext = encryptedData.slice(12);

            const key = await window.crypto.subtle.importKey('raw', sharedSecret, { name: 'HKDF' }, false, ['deriveBits']);
            const derivedKey = await window.crypto.subtle.deriveBits({ name: 'HKDF', hash: 'SHA-256', salt: hkdfSalt, info: HKDF_INFO }, key, 256);
            const decryptionKey = await window.crypto.subtle.importKey('raw', derivedKey, { name: 'AES-GCM', length: 256 }, false, ['decrypt']);
            const decrypted = await window.crypto.subtle.decrypt({ name: 'AES-GCM', iv }, decryptionKey, ciphertext);
            return new Uint8Array(decrypted);
        };

        try {
            return await tryNew();
        } catch (eNew) {
            try {
                return await tryLegacy();
            } catch (eLegacy) {
                // Preserve new-format error for better diagnostics
                throw eNew;
            }
        }
    }
}

// Bind to window for blade usage
if (typeof window !== 'undefined' && !('PostQuantumKEM' in window)) {
    try {
        Object.defineProperty(window, 'PostQuantumKEM', {
            value: Object.freeze(PostQuantumKEM),
            writable: false,
            configurable: false,
            enumerable: false,
        });
    } catch (_) {
        // Fallback assignment (older browsers)
        window.PostQuantumKEM = PostQuantumKEM;
    }
}
