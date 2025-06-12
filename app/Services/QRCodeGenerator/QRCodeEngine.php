<?php
namespace App\Services\QRCodeGenerator;

use App\Contracts\QRCodeGenerator;

abstract class QRCodeEngine implements QRCodeGenerator
{
    protected int $size = 400;

    public function size(int $size = 400): QRCodeGenerator
    {
        $this->size = $size;

        return $this;
    }
}