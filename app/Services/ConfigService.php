<?php

namespace App\Services;

use App\Models\Configuration;
use Illuminate\Validation\Rules\Password;

class ConfigService
{

    protected static function config(): ?Configuration
    {
        return Configuration::query()->first();
    }

    public static function password(): array
    {
        $config = static::config();

        /*
        |--------------------------------------------------------------------------
        | Password Policy Disabled
        |--------------------------------------------------------------------------
        */

        if (!$config || !$config->enable_password_policy) {
            return [
                'required',
                'string',
                'confirmed',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Password Policy Enabled
        |--------------------------------------------------------------------------
        */

        $password = Password::min(
            (int) $config->minimum_password_length
        );

        /*
        | Require uppercase + lowercase
        */
        if ($config->require_uppercase || $config->require_lowercase) {
            $password->mixedCase();
        }

        /*
        | Require number
        */
        if ($config->require_number) {
            $password->numbers();
        }

        /*
        | Require special character
        */
        if ($config->require_special_character) {
            $password->symbols();
        }

        return [
            'required',
            'string',
            'confirmed',
            $password,
        ];
    }

    public static function passwordExpiryDays(): ?int
    {
        $config = static::config();

        if (!$config || !$config->enable_password_policy) {
            return null;
        }

        return (int) $config->password_expiry_days;
    }
    public static function screenSaver(): ?array
    {
        $config = static::config();

        if (!$config || !$config->enable_screen_saver) {
            return null;
        }

        return [
            'enabled' => true,

            'timeout' => (int) $config->screen_saver_timeout,

            'type' => $config->screen_saver_type,

            'images' => $config->screen_saver_images ?? [],

            'video' => $config->screen_saver_video,

            'show_clock' => (bool) $config->screen_saver_show_clock,

            'show_date' => (bool) $config->screen_saver_show_date,
        ];
    }
}
