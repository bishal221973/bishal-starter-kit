<?php

namespace App\Http\Controllers;

use App\Facades\AppConfig;
use App\Models\Configuration;
use App\Models\MailSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigurationController extends Controller
{
    public function index()
    {
        // return AppConfig::hello('Bishal');
        $config = Configuration::first();
        $mailConfig=MailSetting::first();
        return Inertia::render('Settings/Configuration', [
            'config' => $config,
            'mailConfig'=>$mailConfig
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'enable_password_policy' => ['nullable'],
            'minimum_password_length' => ['nullable'],
            'require_uppercase' => ['nullable'],
            'require_lowercase' => ['nullable'],
            'require_number' => ['nullable'],
            'require_special_character' => ['nullable'],
            'password_expiry_days' => ['nullable'],

            // Backup
            'enable_auto_backup' => 'nullable',
            'backup_frequency' => 'nullable',
            'backup_retention_days' => 'nullable',

            // Screen Saver
            'enable_screen_saver' => 'nullable',
            'screen_saver_timeout' => 'nullable',
            'screen_saver_type' => 'nullable',
            'screen_saver_images' => 'nullable',
            'screen_saver_video' => 'nullable',
            'screen_saver_show_clock' => 'nullable',
            'screen_saver_show_date' => 'nullable',

            // Date time
            'date_type' => 'nullable',
            'date_format' => 'nullable',
            'time_format' => 'nullable',

            // Login Security
            'enable_login_attempt_limit' => 'nullable',
            'max_login_attempts' => 'nullable',
            'login_lockout_duration' => 'nullable',

            // Auto Logout
            'enable_auto_logout' => 'nullable',
            'auto_logout_time' => 'nullable',
            'show_logout_warning' => 'nullable',
            'logout_warning_time' => 'nullable',

            // Registration
            'enable_registration' => 'nullable',
            'enable_email_verification' => 'nullable',
            'enable_2fa' => 'nullable',
            'enable_multiple_branch' => 'nullable',

            // Password Change
            'force_logout_on_password_change' => 'nullable',
            'invalidate_other_sessions' => 'nullable',

            // Ip Config
            'enable_ip_blacklist' => ['nullable'],
            'blacklisted_ips' => [
                'nullable',
                'array',
            ],
            'blacklisted_ips.*' => [
                'string',
                'ip',
            ],
            'log_blocked_ip_attempts' => ['nullable'],

            // Footer Text
            'footer_text' => 'nullable',

            // Footer Text
            'auto_disable_inactive_users' => 'nullable',
            'inactive_user_days' => 'nullable',
            'enable_delete_account' => 'nullable',
            'force_single_device_login' => 'nullable',

            // Licence
            'license_key' => 'nullable'

        ]);

        if ($request->hasFile('screen_saver_images')) {

            $files = $request->file('screen_saver_images');

            // Always convert single upload to array
            $files = is_array($files)
                ? $files
                : [$files];

            $images = [];

            foreach ($files as $image) {

                if (!$image->isValid()) {
                    continue;
                }

                $images[] = $image->store(
                    'screen_saver',
                    'public'
                );
            }

            $data['screen_saver_images'] = $images;
        }
        if ($request->has('blacklisted_ips')) {

            $data['blacklisted_ips'] =
                $request->input('blacklisted_ips', []);
        }
        $setting = Configuration::first();

        if (! $setting) {
            $setting = Configuration::create($data);
        } else {
            $setting->update($data);
        }

        return back()->with('success', 'Theme settings updated successfully.');
    }
}
