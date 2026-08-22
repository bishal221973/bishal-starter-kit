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

    public static function loginSecurity(): array
    {
        $config = static::config();

        if (!$config) {
            return [
                'enabled' => false,
                'max_attempts' => 5,
                'lockout_duration' => 15,
            ];
        }

        return [
            'enabled' => (bool) $config->enable_login_attempt_limit,
            'max_attempts' => (int) ($config->max_login_attempts ?? 5),
            'lockout_duration' => (int) ($config->login_lockout_duration ?? 15),
        ];
    }

    public static function autoLogout(): array
    {
        $config = static::config();

        if (!$config) {
            return [
                'enabled' => false,
                'timeout' => 30,
                'show_warning' => true,
                'warning_time' => 1,
            ];
        }

        return [
            'enabled' => (bool) $config->enable_auto_logout,

            'timeout' => (int) (
                $config->auto_logout_time ?? 30
            ),

            'show_warning' => (bool) (
                $config->show_logout_warning ?? true
            ),

            'warning_time' => (int) (
                $config->logout_warning_time ?? 1
            ),
        ];
    }

    public static function registration(): ?array
    {
        $config = static::config();

        if (!$config) {
            return null;
        }

        return [
            'enabled' => true,

            'email_verification' => (bool) (
                $config->enable_email_verification
            ),

            'two_factor' => (bool) (
                $config->enable_2fa
            ),

            'multiple_branch' => (bool) (
                $config->enable_multiple_branch
            ),
        ];
    }

    public static function passwordChange(): array
    {
        $config = static::config();

        return [
            'force_logout' => (bool) (
                $config?->force_logout_on_password_change ?? false
            ),

            'invalidate_other_sessions' => (bool) (
                $config?->invalidate_other_sessions ?? false
            ),
        ];
    }

    public static function ipSecurity(): array
    {
        $config = static::config();

        return [
            'enabled' => (bool) (
                $config?->ip_blocking_enabled ?? false
            ),

            'blocked_ips' => $config?->blacklisted_ips
                ? $config->blacklisted_ips
                : [],
        ];
    }
}
