// Minimal commitment-only circuit
// Proves knowledge of a bounded-length plaintext (N field elements) and a nonce
// such that MiMC Sponge over [plaintext..., nonce] equals a public commitment.

include "./lib/circomlib/mimcsponge.circom";

template CommitOnly(N) {
    // Private inputs (do not expose as public outputs)
    signal input plaintext[N]; // bytes coerced to field elements 0..255 client-side
    signal input nonce;

    // Public output: commitment only
    signal output outCommit;

    // Compute MiMC Sponge commitment
    component sponge = MiMCSponge(N + 1, 220, 1);
    for (var i = 0; i < N; i++) {
        sponge.ins[i] <== plaintext[i];
    }
    sponge.ins[N] <== nonce;
    sponge.k <== 0;

    outCommit <== sponge.outs[0];
}

// Choose N = 15 (aligned with current client input builder)
component main = CommitOnly(15);
