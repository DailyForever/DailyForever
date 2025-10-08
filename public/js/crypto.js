// Delegate to the unified SecureEncryption if available. Fallback to safe AES-GCM.
const Unified = (typeof window !== 'undefined' && window.SecureEncryption) ? window.SecureEncryption : (class {
    static CURRENT_VERSION = 1;
    static DEFAULT_ALGORITHM = 'AES-GCM';

    static async generateKey() {
        return window.crypto.subtle.generateKey(
            { name: 'AES-GCM', length: 256 },
            true,
            ['encrypt', 'decrypt']
        );
    }

    static async encrypt(data, key, options = {}) {
        if (!data) throw new Error('Data to encrypt cannot be empty');
        if (!key) throw new Error('Encryption key is required');
        const iv = window.crypto.getRandomValues(new Uint8Array(12));
        const plaintext = (typeof data === 'string') ? new TextEncoder().encode(data) : data;
        const params = { name: 'AES-GCM', iv, tagLength: 128 };
        if (options.additionalData) {
            const aad = options.additionalData instanceof Uint8Array ? options.additionalData
                : (typeof options.additionalData === 'string' ? new TextEncoder().encode(options.additionalData) : new Uint8Array(options.additionalData));
            params.additionalData = aad;
        }
        const ct = await window.crypto.subtle.encrypt(params, key, plaintext);
        return {
            version: this.CURRENT_VERSION,
            algorithm: this.DEFAULT_ALGORITHM,
            ciphertext: new Uint8Array(ct),
            iv,
            timestamp: Date.now()
        };
    }

    static async decrypt(encryptedData, key, options = {}) {
        if (!encryptedData || typeof encryptedData !== 'object') throw new Error('Invalid encrypted data');
        const { version, algorithm, ciphertext, iv } = encryptedData;
        if (version !== this.CURRENT_VERSION) throw new Error('Unsupported format version');
        if (algorithm !== this.DEFAULT_ALGORITHM) throw new Error('Unsupported algorithm');
        const params = { name: 'AES-GCM', iv, tagLength: 128 };
        if (options.additionalData) {
            const aad = options.additionalData instanceof Uint8Array ? options.additionalData
                : (typeof options.additionalData === 'string' ? new TextEncoder().encode(options.additionalData) : new Uint8Array(options.additionalData));
            params.additionalData = aad;
        }
        const pt = await window.crypto.subtle.decrypt(params, key, ciphertext);
        return new Uint8Array(pt);
    }

    static async exportKey(key) { return new Uint8Array(await window.crypto.subtle.exportKey('raw', key)); }
    static async importKey(keyArray) {
        const bytes = (keyArray instanceof Uint8Array) ? keyArray : new Uint8Array(keyArray);
        if (bytes.length !== 32) throw new Error('Invalid key format: must be 32 bytes');
        return window.crypto.subtle.importKey('raw', bytes, { name: 'AES-GCM', length: 256 }, false, ['encrypt','decrypt']);
    }

    static keyToHex(arr) { const b = (arr instanceof Uint8Array) ? arr : new Uint8Array(arr); return Array.from(b, x => x.toString(16).padStart(2,'0')).join(''); }
});

export class SecureEncryption extends Unified {}

if (typeof window !== 'undefined') {
    window.SecureEncryption = SecureEncryption;
}


