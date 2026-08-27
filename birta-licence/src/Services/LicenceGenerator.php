<?php

namespace Birta\Licence\Services;

use Illuminate\Support\Str;

class LicenceGenerator
{
    public function generate(): string
    {
        $prefix = config(
            'licence.prefix',
            'BL'
        );

        $segments = [];

        for ($i = 0; $i < 4; $i++) {
            $segments[] = strtoupper(
                Str::random(5)
            );
        }

        return $prefix . '-' . implode('-', $segments);
    }
}