<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'screen_saver_images' => 'array',
            'blacklisted_ips' => 'array',
        ];
    }

    
}
