<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Config;

class DynamicMailService
{
    public function configure(): void
    {
        $settings = MailSetting::first();

        if (!$settings) {
            return;
        }

        Config::set('mail.default', $settings->mailer);

        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $settings->host,
            'port' => $settings->port,
            'encryption' => $settings->encryption,
            'username' => $settings->username,
            'password' => $settings->password,
            'timeout' => null,
            'local_domain' => null,
        ]);

        Config::set('mail.from.address', $settings->from_address);
        Config::set('mail.from.name', $settings->from_name);
    }
}
