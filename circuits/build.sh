#!/bin/bash

# ZK Circuit Build Script
# Compiles Circom circuits and generates proving/verification keys

set -e

echo "Building ZK circuits..."

# Check if circom is installed
if ! command -v circom &> /dev/null; then
    echo "Error: circom is not installed. Please install circom first."
    echo "Visit: https://docs.circom.io/getting-started/installation/"
    exit 1
fi

# Check if snarkjs is installed
if ! command -v snarkjs &> /dev/null; then
    echo "Error: snarkjs is not installed. Please install snarkjs first."
    echo "Run: npm install -g snarkjs"
    exit 1
fi

# Create build directory
mkdir -p build

# Compile the circuit
echo "Compiling encryption_commitment.circom..."
circom encryption_commitment.circom --r1cs --wasm --sym -o build/

# Generate trusted setup
echo "Starting trusted setup ceremony..."
cd build

# Generate powers of tau
echo "Generating powers of tau..."
snarkjs powersoftau new bn128 12 pot12_0000.ptau -v
snarkjs powersoftau contribute pot12_0000.ptau pot12_0001.ptau --name="First contribution" -v -e="random entropy"
snarkjs powersoftau contribute pot12_0001.ptau pot12_0002.ptau --name="Second contribution" -v -e="more random entropy"

# Prepare phase 2
snarkjs powersoftau prepare phase2 pot12_0002.ptau pot12_final.ptau -v

# Generate zkey
echo "Generating zkey..."
snarkjs groth16 setup encryption_commitment.r1cs pot12_final.ptau encryption_commitment_0000.zkey

# Contribute to phase 2
snarkjs zkey contribute encryption_commitment_0000.zkey encryption_commitment_0001.zkey --name="First contribution" -v -e="random entropy"

# Export verification key
echo "Exporting verification key..."
snarkjs zkey export verificationkey encryption_commitment_0001.zkey verification_key.json

# Create simplified versions for web
echo "Creating web-compatible files..."
cp encryption_commitment_js/encryption_commitment.wasm ../../public/zkp/encryption_commitment.wasm
cp encryption_commitment_0001.zkey ../../public/zkp/encryption_commitment.zkey
cp verification_key.json ../../public/zkp/verification_key.json

echo "ZK circuit build complete!"
echo "Files generated:"
echo "  - public/zkp/encryption_commitment.wasm"
echo "  - public/zkp/encryption_commitment.zkey"
echo "  - public/zkp/verification_key.json"
