<?php
namespace App\Contracts;

interface Filterable
{
    /**
     * @return array<int,string>
     */
    public static function getFilterableArray(): array;
}