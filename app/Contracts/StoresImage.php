<?php
namespace App\Contracts;

interface StoresImage
{
    public function getImageAttributeName(): string;

    public function getImagePath(): string;

    public function getImageDisk(): string;
}