<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfigurationController extends Controller
{
    public function index(){
        $config=Configuration::first();
        return Inertia::render('Settings/Configuration',[
            'config'=>$config
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'enable_password_policy' => ['required'],
            'minimum_password_length' => ['nullable'],
            'require_uppercase' => ['nullable'],
            'require_lowercase' => ['nullable'],
            'require_number' => ['nullable'],
            'require_special_character' => ['nullable'],
            'password_expiry_days' => ['nullable'],
        ]);

        $setting = Configuration::first();

        if (! $setting) {
            $setting = Configuration::create($data);
        } else {
            $setting->update($data);
        }

        return back()->with('success', 'Theme settings updated successfully.');
    }
}
