<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigurationController extends Controller
{
    public function index()
    {
        $config = Configuration::first();
        return Inertia::render('Settings/Configuration', [
            'config' => $config
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
        ]);

        if ($request->hasFile('screen_saver_images')) {
            $images = [];

            foreach ($request->file('screen_saver_images') as $image) {
                $images[] = $image->store('screen_saver', 'public');
            }

            $data['screen_saver_images'] = $images;
        }
        // return "Hello";
        $setting = Configuration::first();

        if (! $setting) {
            $setting = Configuration::create($data);
        } else {
            $setting->update($data);
        }

        return back()->with('success', 'Theme settings updated successfully.');
    }
}
