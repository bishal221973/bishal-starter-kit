<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class OrganizationConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'organizationFacade';
    }
}
