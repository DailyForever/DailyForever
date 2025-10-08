<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'email_verified_at',
        'password',
        'pin_hash',
        'is_admin',
        'recovery_token',
        'recovery_token_expires_at',
        'store_encryption_keys',
        'security_question_1',
        'security_answer_1_hash',
        'security_question_2',
        'security_answer_2_hash',
        'backup_code_hash',
        'suspended_at',
        'suspended_until',
        'suspension_reason',
        'suspended_by',
        // ZKP fields
        'zkp_proof',
        'zkp_enabled',
        'zkp_proof_created_at',
        'zkp_last_login_at',
        // SRP fields
        'srp_salt',
        'srp_verifier',
        'srp_group_bits',
        'srp_enabled',
        'srp_enabled_at',
        'srp_last_login_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'recovery_token_expires_at' => 'datetime',
            'two_factor_enabled' => 'boolean',
            'two_factor_confirmed_at' => 'datetime',
            'store_encryption_keys' => 'boolean',
            'suspended_at' => 'datetime',
            'suspended_until' => 'datetime',
            // ZKP fields
            'zkp_proof' => 'array',
            'zkp_enabled' => 'boolean',
            'zkp_proof_created_at' => 'datetime',
            'zkp_last_login_at' => 'datetime',
            // SRP fields
            'srp_group_bits' => 'integer',
            'srp_enabled' => 'boolean',
            'srp_enabled_at' => 'datetime',
            'srp_last_login_at' => 'datetime',
        ];
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin_hash',
        'recovery_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
    ];

    /**
     * Get the pastes for the user.
     */
    public function pastes()
    {
        return $this->hasMany(Paste::class);
    }

    /**
     * Get the files for the user.
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Check if user is suspended
     */
    public function isSuspended(): bool
    {
        if (!$this->suspended_at) {
            return false;
        }

        // If suspended_until is null, it's a permanent suspension
        if (!$this->suspended_until) {
            return true;
        }

        // Check if suspension has expired
        return $this->suspended_until->isFuture();
    }

    /**
     * Get suspension status
     */
    public function getSuspensionStatus(): string
    {
        if (!$this->suspended_at) {
            return 'active';
        }

        if ($this->isSuspended()) {
            return 'suspended';
        }

        return 'expired';
    }

    /**
     * Get the user who suspended this user
     */
    public function suspendedBy()
    {
        return $this->belongsTo(User::class, 'suspended_by');
    }

    /**
     * Check if ZKP authentication is enabled for this user
     */
    public function hasZKPEnabled(): bool
    {
        return $this->zkp_enabled && !empty($this->zkp_proof);
    }

    /**
     * Enable ZKP authentication for this user
     */
    public function enableZKP(array $proofData): bool
    {
        try {
            $this->update([
                'zkp_proof' => $proofData,
                'zkp_enabled' => true,
                'zkp_proof_created_at' => now(),
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Disable ZKP authentication for this user
     */
    public function disableZKP(): bool
    {
        try {
            $this->update([
                'zkp_proof' => null,
                'zkp_enabled' => false,
                'zkp_proof_created_at' => null,
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update last ZKP login timestamp
     */
    public function updateZKPLastLogin(): bool
    {
        try {
            $this->update(['zkp_last_login_at' => now()]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get ZKP proof data
     */
    public function getZKPProof(): ?array
    {
        return $this->zkp_proof;
    }

    // SRP Helper Methods

    /**
     * Check if SRP is enabled for this user
     */
    public function hasSRPEnabled(): bool
    {
        return $this->srp_enabled && $this->srp_salt && $this->srp_verifier;
    }

    /**
     * Enable SRP for this user
     */
    public function enableSRP(string $username, string $password): bool
    {
        try {
            return \App\Services\SRPService::enableSRP($this, $username, $password);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Disable SRP for this user
     */
    public function disableSRP(): bool
    {
        try {
            return \App\Services\SRPService::disableSRP($this);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Update last SRP login timestamp
     */
    public function updateSRPLastLogin(): bool
    {
        try {
            $this->update(['srp_last_login_at' => now()]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get SRP salt
     */
    public function getSRPSalt(): ?string
    {
        return $this->srp_salt;
    }

    /**
     * Get SRP verifier
     */
    public function getSRPVerifier(): ?string
    {
        return $this->srp_verifier;
    }
}
