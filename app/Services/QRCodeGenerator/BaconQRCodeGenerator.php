<?php
namespace App\Services\QRCodeGenerator;

use App\Contracts\QRCodeGenerator;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class BaconQRCodeGenerator extends QRCodeEngine implements QRCodeGenerator
{
    public function writeUrl(string $url): string
    {
        $writer = new Writer(
            new ImageRenderer(
                new RendererStyle($this->size),
                new ImagickImageBackEnd()
            )
        );
        
        return 'data:image/png;base64, ' . base64_encode($writer->writeString($url));
    }
}