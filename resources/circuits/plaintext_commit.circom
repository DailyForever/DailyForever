// Local circomlib includes (MiMC Sponge)
include "./lib/circomlib/mimcsponge.circom";

// Preimage-of-commitment circuit (Option A)
// Proves knowledge of a plaintext (bounded to N field elements) and a nonce
// such that Poseidon(plaintext[0..N-1], nonce) = commit. Also mirrors
// ciphertextHash/iv/length/AAD to public outputs to bind UI metadata.
// values in 0..255 per position from the first N bytes (pad with zeros).

template PlaintextCommit(N) {
    // Private inputs
    signal input plaintext[N];      // typically bytes (0..255)
    signal input nonce;             // field element

    // Public inputs mirrored to outputs (UI bindings)
    signal input ciphertextHash[32];
    signal input iv[12];
    signal input plaintextLength;
    signal input additionalDataHash[32];

    // Public outputs
    signal output outCommit;                  // Poseidon(plaintext..., nonce)
    signal output outCiphertextHash[32];
    signal output outIv[12];
    signal output outPlaintextLength;
    signal output outAdditionalDataHash[32];

    // Compute MiMC Sponge commitment over plaintext[0..N-1] and nonce (k=0)
    // nRounds=220 as suggested in circomlib comments
    component sponge = MiMCSponge(N + 1, 220, 1);
    for (var i = 0; i < N; i++) {
        sponge.ins[i] <== plaintext[i];
    }
    sponge.ins[N] <== nonce;
    sponge.k <== 0;
    outCommit <== sponge.outs[0];

    // Mirror metadata to outputs
    for (var j = 0; j < 32; j++) {
        outCiphertextHash[j] <== ciphertextHash[j];
        outAdditionalDataHash[j] <== additionalDataHash[j];
    }
    for (var k = 0; k < 12; k++) {
        outIv[k] <== iv[k];
    }
    outPlaintextLength <== plaintextLength;
}

// Choose N = 15 field elements; +1 input is nonce => 16 inputs total
component main = PlaintextCommit(15);
