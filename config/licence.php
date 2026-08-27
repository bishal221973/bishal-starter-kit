<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Licence Key
    |--------------------------------------------------------------------------
    |
    | The licence key used by the current application.
    |
    */

    'key' => env('LICENCE_KEY'),

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
    | Allow Multiple Branches
    |--------------------------------------------------------------------------
    |
    | When true, the licence is checked against the current branch ID.
    |
    | When false, branch_id is ignored during licence validation.
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
    | Number of days a newly created licence remains valid.
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
    |
    | Licence database table.
    |
    */

    'table' => 'licences',

];