<?php
namespace App\Contracts;

interface TwoFactorAuthentication
{
    public function setUser(string $name, string $email): self;

    public function generateSecret(): self;
    
    public function register(?string $name = null, ?string $email = null): self;

    public function verifyRegistration(string $otp): bool;

    public function getRegistrationSecret(): string;

    public function getSecret(): string;

    public function setSecret(?string $secret = null): self;

    public function verifyOtp(string $otp, ?string $secret = null): bool;

    public function generateQRCodeUrl(?string $secret = null, ?string $name = null, ?string $email = null): string;
}