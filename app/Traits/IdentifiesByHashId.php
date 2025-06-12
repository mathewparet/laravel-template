<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait IdentifiesByHashId
{
    private function identify(string|Model $model, $object): int
    {
        return $object instanceof $model ? $model->id : $model::getIdFromHashId($object);
    }
}