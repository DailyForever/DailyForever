pragma circom 2.1.4;

include "circomlib/circuits/poseidon.circom";
include "circomlib/circuits/comparators.circom";

// Simple commitment circuit for encrypted content
// Proves knowledge of plaintext that hashes to a public commitment
template EncryptionCommitment() {
    // Private inputs
    signal input plaintext[15];  // 15 bytes of plaintext
    signal input nonce;           // Random nonce for commitment
    
    // Public outputs
    signal output commitment;     // Poseidon hash of plaintext + nonce
    
    // Compute Poseidon hash of plaintext and nonce
    component hasher = Poseidon(16);
    for (var i = 0; i < 15; i++) {
        hasher.inputs[i] <== plaintext[i];
    }
    hasher.inputs[15] <== nonce;
    
    commitment <== hasher.out;
}

component main = EncryptionCommitment();
