<?php
namespace App\Facades;

use App\Contracts\QRCodeGenerator as QRCodeGeneratorContractor;
use Illuminate\Support\Facades\Facade;

/**
 * @method static QRCodeGenerator size(int $size = 400)
 * @method static string writeUrl(string $url)
 */
class QRCodeGenerator extends Facade
{
    public static function getFacadeAccessor()
    {
        return QRCodeGeneratorContractor::class;
    }
}