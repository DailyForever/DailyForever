# ZK Encryption Integration (snarkjs)

This folder contains the thin integration layer for snarkjs used by the client to generate and verify zkSNARK proofs that bind encrypted data (ciphertext) to public commitments (hashes, IV, etc.) without revealing plaintext.

## Components

- `encryption-proof.js` — loader and helpers for snarkjs:
  - `SnarkJSLoader.load({ cdn?, preferModule? })`: dynamically loads snarkjs in the browser (CDN by default) or via ESM import when `preferModule=true`.
  - `EncryptionProof.generateProof({ input, wasmUrl, zkeyUrl })`: `groth16.fullProve` wrapper.
  - `EncryptionProof.verifyProof({ vkeyJson|vkeyUrl, proof, publicSignals })`: verification wrapper.

- `SecureEncryption.encryptWithZK(...)` in `resources/js/crypto.js`:
  - Performs AES-GCM encryption client-side, computes commitments (`plaintextHash`, `ciphertextHash`, `iv`, `algorithm`, `timestamp`, and `additionalDataHash` if provided), and optionally generates a zkSNARK proof using provided circuit artifacts.

## Usage

```js
import { SecureEncryption } from '@/resources/js/crypto.js';

// 1) Generate a key (non-extractable by default)
const key = await SecureEncryption.generateSecureKey();

// 2) Encrypt + generate commitments (and optional proof if artifacts provided)
const data = new TextEncoder().encode('secret');
const aad = new TextEncoder().encode('policy:public');

const { encrypted, commitments, zk } = await SecureEncryption.encryptWithZK(
  data,
  key,
  'AES-GCM',
  { additionalData: aad },
  {
    wasmUrl: '/circuits/policy.wasm',
    zkeyUrl: '/circuits/policy.zkey',
    // Optional: map commitments to circuit input if your circuit expects a specific shape
    // buildInput: ({ plaintextHash, ciphertextHash, iv, additionalDataHash }) => ({ ... })
  }
);

// 3) Verify (client-side quick check)
const ok = await SecureEncryption.verifyEncryptionProof({
  vkeyUrl: '/circuits/policy.vkey.json',
  proof: zk?.proof,
  publicSignals: zk?.publicSignals
});

// 4) Send to server (example JSON API — large files should use multipart and include hashes alongside the blob)
await fetch('/api/zk/encryption/submit', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ encrypted, commitments, zk }),
});
```

## Circuits

This repo does not include Circom circuits or artifacts. To build your own:

1. Author a Circom circuit that proves the property you care about while binding to the public commitments you’ll store:
   - Keep it practical: proving AES-GCM inside the circuit is heavy. A common approach is to prove knowledge of a plaintext that satisfies a policy and whose hash equals a public `plaintextHash`. The server binds the proof to the stored ciphertext using `ciphertextHash` and relies on AEAD for integrity.
2. Compile with `circom`, set up powers of tau, and generate a Groth16 `.zkey` and `verification_key.json`.
3. Export the WASM and ZKey, host them at stable URLs (e.g. `/public/circuits/policy.wasm`, `/public/circuits/policy.zkey`, `/public/circuits/policy.vkey.json`).
4. Provide those URLs to `encryptWithZK()`.

## Design principles

- Zero-knowledge of content: server stores only ciphertext and non-sensitive metadata (hashes/commitments, IV, algorithm, timestamp) and never sees plaintext.
- Client-side crypto: WebCrypto + snarkjs (WASM) run in-browser.
- Minimal-trust server: verifies proofs and enforces access control but cannot read content.
- Recoverability & usability: add key-wrapping or social recovery outside of this module.
- Auditability: sign logs and metadata to make tampering evident.
