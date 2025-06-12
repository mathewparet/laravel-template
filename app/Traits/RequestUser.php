<?php

namespace App\Traits;

use App\Models\User;

trait RequestUser
{
    private function requestUser(): User
    {
        /**
         * @var \App\Models\User $user
         */
        $user = request()->user();

        return $user;
    }
}
