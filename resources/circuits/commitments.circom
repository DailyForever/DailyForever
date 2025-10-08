pragma circom 2.1.6;

template Commitments() {
    // Inputs
    signal input plaintextHash[32];
    signal input ciphertextHash[32];
    signal input iv[12];
    signal input plaintextLength;
    signal input additionalDataHash[32];

    // Public outputs (mirror the inputs)
    signal output outPlaintextHash[32];
    signal output outCiphertextHash[32];
    signal output outIv[12];
    signal output outPlaintextLength;
    signal output outAdditionalDataHash[32];

    // Constrain outputs to equal inputs
    for (var i = 0; i < 32; i++) {
        outPlaintextHash[i] <== plaintextHash[i];
        outCiphertextHash[i] <== ciphertextHash[i];
        outAdditionalDataHash[i] <== additionalDataHash[i];
    }
    for (var j = 0; j < 12; j++) {
        outIv[j] <== iv[j];
    }
    outPlaintextLength <== plaintextLength;
}

component main = Commitments();