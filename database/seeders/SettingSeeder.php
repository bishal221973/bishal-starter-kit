<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::create([
            'sidebar_bg_color'=>'#3d98aa'
        ]);

        Configuration::create([
            'application_version'=>'1.0.0'
        ]);
    }
}
