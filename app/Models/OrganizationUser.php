<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class OrganizationUser extends Model
{
    protected $table = "organization_user";

    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($organizationUser) {

            if (empty($organizationUser->employee_code)) {
                do {
                    $code = 'EMP-' . strtoupper(Str::random(8));
                } while (
                    self::where('employee_code', $code)->exists()
                );

                $organizationUser->employee_code = $code;
            }
        });
    }
}
