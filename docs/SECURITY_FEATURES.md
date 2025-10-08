# Security Features Documentation

## Overview
This document describes the enhanced security features implemented in the application, including Zero-Knowledge Proofs, Key Rotation, and Secure Random Validation.

## 1. Zero-Knowledge Proofs (ZK-SNARKs)

### What It Does
Allows users to prove they know the plaintext without revealing it, providing cryptographic proof of encryption correctness.

### Implementation
- **Circuit**: Poseidon hash-based commitment circuit (`/circuits/encryption_commitment.circom`)
- **Proving Key**: `/public/zkp/encryption_commitment.zkey`
- **Verification Key**: `/public/zkp/verification_key.json`

### Usage

#### Client-Side (JavaScript)
```javascript
import { SecureEncryption } from './crypto.js';

// Encrypt with ZK proof
const result = await SecureEncryption.encryptWithZK(
    plaintext,
    key,
    'AES-GCM',
    {},
    {
        wasmUrl: '/zkp/encryption_commitment.wasm',
        zkeyUrl: '/zkp/encryption_commitment.zkey'
    }
);

// Verify proof
const isValid = await SecureEncryption.verifyEncryptionProof({
    vkeyUrl: '/zkp/verification_key.json',
    proof: result.zk.proof,
    publicSignals: result.zk.publicSignals
});
```

#### Server-Side (API)
```bash
POST /api/zk/encryption/submit
{
    "encrypted": { "algorithm": "AES-GCM", "ciphertext": "...", "iv": "..." },
    "commitments": { "ciphertextHash": "...", "commit": "..." },
    "zk": { "proof": {...}, "publicSignals": [...] }
}
```

### Security Properties
- **Zero-Knowledge**: Server learns nothing about plaintext
- **Non-Malleability**: Proof is bound to specific ciphertext
- **Soundness**: Cannot create valid proof without knowing plaintext

## 2. Key Rotation Mechanism

### What It Does
Automatically rotates encryption keys based on age, usage, and security events to maintain cryptographic hygiene.

### Rotation Triggers
- **Time-Based**: Keys older than 90 days (critical)
- **Usage-Based**: Keys used more than 10,000 times (critical)
- **IV Collisions**: More than 100 IV collisions detected (critical)
- **Manual**: User-initiated or admin-forced rotation
- **Emergency**: Immediate rotation for suspected compromise

### API Endpoints

#### Check Rotation Status
```bash
GET /api/keys/rotation-status
```
Response:
```json
{
    "status": {
        "needs_rotation": true,
        "urgency": "critical|urgent|recommended|none",
        "reasons": ["Key age exceeds 90 days"]
    },
    "current_key_id": "abc123...",
    "key_age_days": 95
}
```

#### Perform Rotation
```bash
POST /api/keys/rotate
```
Response:
```json
{
    "success": true,
    "new_key_id": "def456...",
    "old_key_id": "abc123...",
    "grace_period_ends": "2025-10-12T00:00:00Z"
}
```

#### Emergency Rotation
```bash
POST /api/keys/emergency-rotate
{
    "reason": "Suspected compromise",
    "confirm": true
}
```

### Client-Side Integration
```javascript
import keyRotationManager from './key-rotation.js';

// Initialize rotation monitoring
await keyRotationManager.initialize();

// Listen for rotation events
keyRotationManager.onRotationEvent((event) => {
    switch(event.type) {
        case 'rotation_needed':
            console.log('Rotation needed:', event.reasons);
            break;
        case 'rotation_completed':
            console.log('New key:', event.newKeyId);
            break;
    }
});

// Manual rotation
await keyRotationManager.performRotation('manual', 'User requested');
```

### Grace Period
- Old keys remain valid for 7 days after rotation
- Allows time for re-encryption of existing content
- Automatic cleanup after grace period expires

## 3. Secure Random Validation

### What It Does
Validates the quality of random bytes used for cryptographic operations to prevent weak randomness vulnerabilities.

### Validation Checks
1. **Length Validation**: Minimum required bytes
2. **Entropy Estimation**: Shannon entropy calculation
3. **Distribution Analysis**: Chi-square test for uniformity
4. **Pattern Detection**: Sequential, repetitive patterns
5. **Statistical Tests**: Monobit, runs tests
6. **Weak Pattern Detection**: Timestamps, counters, keyboard patterns

### PHP Usage
```php
use App\Services\SecureRandomValidator;

// Generate validated random
$random = SecureRandomValidator::generateSecureRandom(32, [
    'validate' => true,
    'max_attempts' => 3
]);

// Validate existing random
$validation = SecureRandomValidator::validateRandomBytes($bytes);
if (!$validation['valid']) {
    throw new Exception('Low quality random: ' . implode(', ', $validation['issues']));
}
```

### JavaScript Usage
```javascript
// Automatic validation in crypto operations
const iv = SecureEncryption._generateSecureIV('AES-GCM');
// Internally validates and retries if quality is low

// Manual validation
const validation = SecureEncryption._validateRandomBytes(randomBytes);
if (!validation.valid) {
    console.error('Random validation failed:', validation.issues);
}
```

### Thresholds
- **Minimum Entropy**: 128 bits
- **Unique Bytes Ratio**: > 90%
- **Pattern Score**: < 10%
- **Chi-Square P-Value**: > 0.01

## 4. Security Event Logging

### Monitored Events
- Key rotation triggers
- Random validation failures
- IV collisions
- Failed decryption attempts
- Emergency rotations

### Accessing Logs
```php
// Server-side
$stats = SecureRandomValidator::getValidationStats();
$rotationStats = KeyRotationService::getRotationStats();
```

```javascript
// Client-side (stored in session storage)
const logs = JSON.parse(sessionStorage.getItem('crypto_security_logs'));
```

## 5. Database Schema Updates

### New Tables
- `key_rotation_audits`: Audit trail for all rotation events
- `random_validation_metrics`: Quality metrics for random generation

### Updated Tables
- `user_keypairs`: Added rotation tracking fields
  - `rotation_from`: Previous key ID
  - `rotation_to`: Next key ID
  - `rotation_status`: Current rotation status
  - `rotation_initiated_at`: Rotation timestamp

## 6. Testing

### Run Security Tests
```bash
php artisan test --filter=KeyRotationTest
php artisan test --filter=SecurityTest
```

### Test Coverage
- Key age detection
- Usage limit detection
- Random quality validation
- Pattern detection
- API authentication
- Emergency rotation

## 7. Configuration

### Environment Variables
```env
# ZK Proof Settings
ZK_REQUIRE_PROOF=false  # Set to true to enforce ZK proofs

# Key Rotation Settings
KEY_MAX_AGE_DAYS=90
KEY_MAX_USES=10000
KEY_ROTATION_GRACE_DAYS=7

# Random Validation
RANDOM_MIN_ENTROPY_BITS=128
RANDOM_VALIDATION_ENABLED=true
```

## 8. Best Practices

### For Developers
1. Always use `SecureRandomValidator::generateSecureRandom()` for cryptographic randomness
2. Monitor key rotation status regularly
3. Handle rotation events gracefully in the UI
4. Test with low-quality random to ensure validation works

### For Operations
1. Monitor rotation statistics dashboard
2. Set up alerts for critical rotations
3. Regularly review audit logs
4. Ensure backup of keys during rotation

### For Security Auditors
1. Review `/api/admin/rotation-stats` for system-wide metrics
2. Check `key_rotation_audits` table for rotation history
3. Verify ZK proof generation and verification
4. Test random validation with known weak patterns

## 9. Troubleshooting

### Common Issues

#### "Failed to generate high-quality IV"
- **Cause**: System randomness source may be depleted
- **Solution**: Check `/dev/urandom` availability, add entropy sources

#### "Key rotation already in progress"
- **Cause**: Previous rotation didn't complete
- **Solution**: Wait 5 minutes or use emergency rotation

#### "ZK proof generation failed"
- **Cause**: Missing WASM/ZKEY files or browser incompatibility
- **Solution**: Ensure circuit files are accessible, use supported browser

## 10. Future Improvements

### Planned Enhancements
- [ ] Automated re-encryption queue with progress tracking
- [ ] Hardware Security Module (HSM) integration
- [ ] Distributed key generation for multi-party computation
- [ ] Threshold encryption for team features
- [ ] Post-quantum signature schemes (Dilithium, Falcon)

### Research Areas
- Homomorphic encryption for server-side operations
- Secure multi-party computation for collaborative editing
- Ring signatures for anonymous posting
- Verifiable delay functions for time-locked encryption
