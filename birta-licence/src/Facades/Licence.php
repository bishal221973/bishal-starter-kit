<?php

namespace Birta\Licence\Facades;

use Illuminate\Support\Facades\Facade;

class Licence extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'licence';
    }
}