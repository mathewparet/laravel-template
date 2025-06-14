<?php
namespace App\Contracts;

interface MaskedException
{
    public function getMaskedMessage(): array;
}