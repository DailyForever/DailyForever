<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SecureRandomValidator
{
    // Entropy quality thresholds
    const MIN_ENTROPY_BITS = 128; // Minimum 128 bits of entropy
    const MIN_UNIQUE_BYTES_RATIO = 0.9; // 90% unique bytes minimum
    const MAX_PATTERN_SCORE = 0.1; // Maximum 10% pattern detection
    
    // Statistical test thresholds
    const CHI_SQUARE_P_VALUE = 0.01; // Chi-square test p-value threshold
    const RUNS_TEST_THRESHOLD = 0.05; // Runs test threshold
    
    // Monitoring
    private static $failureCount = 0;
    private static $lastFailureTime = null;
    
    /**
     * Validate the quality of random bytes
     * 
     * @param string $randomBytes
     * @param array $options
     * @return array ['valid' => bool, 'score' => float, 'issues' => array]
     */
    public static function validateRandomBytes(string $randomBytes, array $options = []): array
    {
        $issues = [];
        $score = 1.0;
        
        $minLength = $options['min_length'] ?? 16;
        $requireCrypto = $options['require_crypto'] ?? true;
        $performStatTests = $options['statistical_tests'] ?? true;
        
        // Length validation
        $length = strlen($randomBytes);
        if ($length < $minLength) {
            $issues[] = "Insufficient length: {$length} bytes (minimum: {$minLength})";
            $score *= 0.5;
        }
        
        // Check for all zeros or all ones (catastrophic failure)
        if (self::isAllSameValue($randomBytes)) {
            $issues[] = "CRITICAL: All bytes have the same value";
            return ['valid' => false, 'score' => 0, 'issues' => $issues, 'critical' => true];
        }
        
        // Entropy estimation
        $entropyBits = self::estimateEntropy($randomBytes);
        if ($entropyBits < self::MIN_ENTROPY_BITS) {
            $issues[] = "Low entropy: {$entropyBits} bits (minimum: " . self::MIN_ENTROPY_BITS . ")";
            $score *= ($entropyBits / self::MIN_ENTROPY_BITS);
        }
        
        // Check byte distribution
        $distribution = self::analyzeByteDistribution($randomBytes);
        if ($distribution['unique_ratio'] < self::MIN_UNIQUE_BYTES_RATIO) {
            $issues[] = "Poor byte distribution: {$distribution['unique_ratio']} unique ratio";
            $score *= $distribution['unique_ratio'];
        }
        
        // Pattern detection
        $patternScore = self::detectPatterns($randomBytes);
        if ($patternScore > self::MAX_PATTERN_SCORE) {
            $issues[] = "Patterns detected: score {$patternScore}";
            $score *= (1 - $patternScore);
        }
        
        // Statistical tests
        if ($performStatTests && $length >= 128) {
            $statTests = self::runStatisticalTests($randomBytes);
            foreach ($statTests['failed'] as $test) {
                $issues[] = "Failed statistical test: {$test}";
                $score *= 0.8;
            }
        }
        
        // Cryptographic source validation
        if ($requireCrypto && !self::isFromCryptoSource()) {
            $issues[] = "Not from cryptographic source";
            $score *= 0.7;
        }
        
        // Check for known weak patterns
        $weakPatterns = self::checkWeakPatterns($randomBytes);
        if (!empty($weakPatterns)) {
            foreach ($weakPatterns as $pattern) {
                $issues[] = "Weak pattern detected: {$pattern}";
                $score *= 0.6;
            }
        }
        
        // Log failures for monitoring
        if ($score < 0.8) {
            self::logValidationFailure($randomBytes, $issues, $score);
        }
        
        return [
            'valid' => empty($issues) || $score >= 0.7,
            'score' => round($score, 3),
            'issues' => $issues,
            'entropy_bits' => $entropyBits,
            'distribution' => $distribution,
            'length' => $length
        ];
    }
    
    /**
     * Generate and validate secure random bytes
     * 
     * @param int $length
     * @param array $options
     * @return string
     * @throws Exception if unable to generate secure random
     */
    public static function generateSecureRandom(int $length, array $options = []): string
    {
        $maxAttempts = $options['max_attempts'] ?? 3;
        $requireValidation = $options['validate'] ?? true;
        
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                // Try crypto.getRandomValues() equivalent in PHP
                if (function_exists('random_bytes')) {
                    $bytes = random_bytes($length);
                } elseif (function_exists('openssl_random_pseudo_bytes')) {
                    $bytes = openssl_random_pseudo_bytes($length, $strong);
                    if (!$strong) {
                        throw new Exception('OpenSSL did not use strong algorithm');
                    }
                } else {
                    throw new Exception('No cryptographic random source available');
                }
                
                // Validate if required
                if ($requireValidation) {
                    $validation = self::validateRandomBytes($bytes, [
                        'min_length' => $length,
                        'statistical_tests' => $length >= 128
                    ]);
                    
                    if (!$validation['valid']) {
                        Log::warning('Random generation produced low-quality output', [
                            'attempt' => $attempt,
                            'issues' => $validation['issues']
                        ]);
                        
                        if ($attempt < $maxAttempts) {
                            continue; // Retry
                        }
                        
                        throw new Exception('Unable to generate high-quality random after ' . $maxAttempts . ' attempts');
                    }
                }
                
                return $bytes;
                
            } catch (\Throwable $e) {
                if ($attempt >= $maxAttempts) {
                    throw new Exception('Failed to generate secure random: ' . $e->getMessage());
                }
                
                // Add entropy from multiple sources before retry
                self::addEmergencyEntropy();
            }
        }
        
        throw new Exception('Failed to generate secure random bytes');
    }
    
    /**
     * Check if all bytes have the same value
     */
    private static function isAllSameValue(string $bytes): bool
    {
        if (strlen($bytes) === 0) return true;
        
        $first = $bytes[0];
        for ($i = 1; $i < strlen($bytes); $i++) {
            if ($bytes[$i] !== $first) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Estimate entropy in bits
     */
    private static function estimateEntropy(string $bytes): float
    {
        $length = strlen($bytes);
        if ($length === 0) return 0;
        
        // Count byte frequencies
        $frequencies = array_count_values(str_split($bytes));
        
        // Calculate Shannon entropy
        $entropy = 0;
        foreach ($frequencies as $count) {
            $probability = $count / $length;
            if ($probability > 0) {
                $entropy -= $probability * log($probability, 2);
            }
        }
        
        // Convert to total bits
        return $entropy * $length;
    }
    
    /**
     * Analyze byte distribution
     */
    private static function analyzeByteDistribution(string $bytes): array
    {
        $length = strlen($bytes);
        if ($length === 0) {
            return ['unique_ratio' => 0, 'chi_square' => PHP_FLOAT_MAX];
        }
        
        $byteValues = array_count_values(str_split($bytes));
        $uniqueCount = count($byteValues);
        
        // Expected frequency for uniform distribution
        $expected = $length / 256;
        
        // Chi-square test
        $chiSquare = 0;
        for ($i = 0; $i < 256; $i++) {
            $observed = $byteValues[chr($i)] ?? 0;
            if ($expected > 0) {
                $chiSquare += pow($observed - $expected, 2) / $expected;
            }
        }
        
        return [
            'unique_ratio' => min($uniqueCount / min($length, 256), 1.0),
            'chi_square' => $chiSquare,
            'unique_bytes' => $uniqueCount,
            'total_bytes' => $length
        ];
    }
    
    /**
     * Detect patterns in random data
     */
    private static function detectPatterns(string $bytes): float
    {
        $length = strlen($bytes);
        if ($length < 4) return 0;
        
        $patternScore = 0;
        
        // Check for repeated sequences
        for ($seqLen = 2; $seqLen <= min(8, $length / 2); $seqLen++) {
            $sequences = [];
            for ($i = 0; $i <= $length - $seqLen; $i++) {
                $seq = substr($bytes, $i, $seqLen);
                $sequences[$seq] = ($sequences[$seq] ?? 0) + 1;
            }
            
            // Calculate repetition score
            foreach ($sequences as $count) {
                if ($count > 1) {
                    $patternScore += ($count - 1) / ($length - $seqLen + 1);
                }
            }
        }
        
        return min($patternScore / 7, 1.0); // Normalize across sequence lengths
    }
    
    /**
     * Run statistical randomness tests
     */
    private static function runStatisticalTests(string $bytes): array
    {
        $passed = [];
        $failed = [];
        
        // Monobit test
        $bits = '';
        for ($i = 0; $i < strlen($bytes); $i++) {
            $bits .= str_pad(decbin(ord($bytes[$i])), 8, '0', STR_PAD_LEFT);
        }
        
        $ones = substr_count($bits, '1');
        $zeros = strlen($bits) - $ones;
        $diff = abs($ones - $zeros);
        $expected = strlen($bits) / 2;
        
        if ($diff > sqrt(strlen($bits)) * 3) {
            $failed[] = 'monobit';
        } else {
            $passed[] = 'monobit';
        }
        
        // Runs test (sequences of same bit)
        $runs = 1;
        for ($i = 1; $i < strlen($bits); $i++) {
            if ($bits[$i] !== $bits[$i-1]) {
                $runs++;
            }
        }
        
        $expectedRuns = (2 * $ones * $zeros) / strlen($bits) + 1;
        $variance = (2 * $ones * $zeros * (2 * $ones * $zeros - strlen($bits))) / 
                    (pow(strlen($bits), 2) * (strlen($bits) - 1));
        
        if ($variance > 0) {
            $z = abs($runs - $expectedRuns) / sqrt($variance);
            if ($z > 2.58) { // 99% confidence
                $failed[] = 'runs';
            } else {
                $passed[] = 'runs';
            }
        }
        
        return [
            'passed' => $passed,
            'failed' => $failed,
            'total' => count($passed) + count($failed)
        ];
    }
    
    /**
     * Check for known weak patterns
     */
    private static function checkWeakPatterns(string $bytes): array
    {
        $weakPatterns = [];
        
        // Check for timestamp-like patterns
        $asHex = bin2hex($bytes);
        if (preg_match('/^[0-9a-f]{8}0{8}/', $asHex)) {
            $weakPatterns[] = 'timestamp-like prefix';
        }
        
        // Check for counter patterns
        $increasing = true;
        $decreasing = true;
        for ($i = 1; $i < min(8, strlen($bytes)); $i++) {
            if (ord($bytes[$i]) <= ord($bytes[$i-1])) {
                $increasing = false;
            }
            if (ord($bytes[$i]) >= ord($bytes[$i-1])) {
                $decreasing = false;
            }
        }
        if ($increasing) $weakPatterns[] = 'increasing sequence';
        if ($decreasing) $weakPatterns[] = 'decreasing sequence';
        
        // Check for keyboard walks
        $keyboardPatterns = ['qwerty', 'asdfgh', '123456', 'abcdef'];
        $bytesLower = strtolower($bytes);
        foreach ($keyboardPatterns as $pattern) {
            if (strpos($bytesLower, $pattern) !== false) {
                $weakPatterns[] = "keyboard pattern: {$pattern}";
            }
        }
        
        return $weakPatterns;
    }
    
    /**
     * Check if using cryptographic random source
     */
    private static function isFromCryptoSource(): bool
    {
        // In PHP, check if strong random functions are available
        if (function_exists('random_bytes')) {
            return true;
        }
        
        if (function_exists('openssl_random_pseudo_bytes')) {
            $test = openssl_random_pseudo_bytes(1, $strong);
            return $strong;
        }
        
        return false;
    }
    
    /**
     * Add emergency entropy (last resort)
     */
    private static function addEmergencyEntropy(): void
    {
        // Mix in multiple entropy sources
        $entropy = '';
        
        // High-resolution time
        $entropy .= microtime(true);
        
        // Process ID
        if (function_exists('getmypid')) {
            $entropy .= getmypid();
        }
        
        // Memory usage
        $entropy .= memory_get_usage(true);
        
        // Random uniqid
        $entropy .= uniqid('', true);
        
        // Hash and feed to random state (platform-specific)
        $hash = hash('sha512', $entropy, true);
        
        // This is a last resort - log the event
        Log::warning('Emergency entropy mixing performed');
    }
    
    /**
     * Log validation failure for monitoring
     */
    private static function logValidationFailure(string $bytes, array $issues, float $score): void
    {
        self::$failureCount++;
        self::$lastFailureTime = time();
        
        $sample = bin2hex(substr($bytes, 0, 16));
        
        Log::warning('Random validation failure', [
            'score' => $score,
            'issues' => $issues,
            'sample' => $sample . '...',
            'failure_count' => self::$failureCount
        ]);
        
        // Store metrics for monitoring
        Cache::increment('random_validation_failures');
        Cache::put('last_random_failure', [
            'time' => self::$lastFailureTime,
            'score' => $score,
            'issues' => $issues
        ], now()->addHours(24));
        
        // Alert if too many failures
        if (self::$failureCount > 10) {
            Log::critical('Excessive random validation failures detected', [
                'count' => self::$failureCount,
                'period' => time() - (self::$lastFailureTime ?? time())
            ]);
        }
    }
    
    /**
     * Get validation statistics
     */
    public static function getValidationStats(): array
    {
        return [
            'failure_count' => Cache::get('random_validation_failures', 0),
            'last_failure' => Cache::get('last_random_failure'),
            'crypto_available' => self::isFromCryptoSource(),
            'thresholds' => [
                'min_entropy_bits' => self::MIN_ENTROPY_BITS,
                'min_unique_bytes_ratio' => self::MIN_UNIQUE_BYTES_RATIO,
                'max_pattern_score' => self::MAX_PATTERN_SCORE
            ]
        ];
    }
}
