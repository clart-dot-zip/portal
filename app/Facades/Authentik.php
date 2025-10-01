<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Authentik extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'authentik';
    }
}