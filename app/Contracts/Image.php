<?php
namespace App\Contracts;

interface Image
{
    public function getImageAttributeName(): string;

    public function getImagePath(): string;

    public function getImageDisk(): string;
}