<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class MailConfig extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mailFacade';
    }
}