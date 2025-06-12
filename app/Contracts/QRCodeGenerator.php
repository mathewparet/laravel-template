<?php
namespace App\Contracts;

interface QRCodeGenerator
{
    public function size(int $size = 400): self;

    public function writeUrl(string $url): string;
}