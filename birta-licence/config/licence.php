<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Licence Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix used when generating licence keys.
    |
    */

    'prefix' => env(
        'LICENCE_PREFIX',
        'BL'
    ),

    /*
    |--------------------------------------------------------------------------
    | Multiple Branches
    |--------------------------------------------------------------------------
    |
    | When enabled, the middleware checks the licence against
    | the current branch ID.
    |
    */

    'allow_multiple_branches' => env(
        'LICENCE_ALLOW_MULTIPLE_BRANCHES',
        false
    ),

    /*
    |--------------------------------------------------------------------------
    | Default Duration
    |--------------------------------------------------------------------------
    |
    | Number of days for a newly generated licence.
    |
    */

    'duration' => env(
        'LICENCE_DURATION',
        365
    ),

    /*
    |--------------------------------------------------------------------------
    | Database Table
    |--------------------------------------------------------------------------
    */

    'table' => 'licences',

];