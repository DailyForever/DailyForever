<?php

namespace App\Services;

class TwoFactorService
{
    private $google2fa;

    public function __construct()
    {
        if (class_exists('PragmaRX\Google2FA\Google2FA')) {
            $this->google2fa = new \PragmaRX\Google2FA\Google2FA();
        } else {
            throw new \Exception('Google2FA package not installed. Run: composer require pragmarx/google2fa');
        }
    }

    /**
     * Generate a new 2FA secret
     */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate QR code URL for 2FA setup
     */
    public function getQRCodeUrl(string $companyName, string $companyEmail, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secret
        );
    }

    /**
     * Verify a 2FA code
     */
    public function verifyCode(string $secret, string $code): bool
    {
        try {
            return $this->google2fa->verifyKey($secret, $code);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get current TOTP code for a secret (for testing)
     */
    public function getCurrentCode(string $secret): string
    {
        return $this->google2fa->getCurrentOtp($secret);
    }
}
