<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use PioneerDynamics\LaravelPasskey\Traits\HasPasskeys;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PioneerDynamics\LaravelPasskey\Contracts\PasskeyUser;

/**
 * @property bool $is_two_factor_enabled
 */
class User extends Authenticatable implements PasskeyUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasPasskeys;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_token',
        'backup_codes',
    ];

    protected $appends = [
        'is_two_factor_enabled',
    ];

    public function getUserIcon()
    {
        return null;
    }


    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'backup_codes'=> 'encrypted:array',
            'two_factor_token' => 'encrypted',
        ];
    }

    public function isTwoFactorEnabled(): Attribute
    {
        return Attribute::make(
            get: fn() => !blank($this->two_factor_token),
        );
    }

    public function disable2FA()
    {
        $this->forceFill([
            'two_factor_token' => null,
            'backup_codes' => null
        ])->save();
    }
}
