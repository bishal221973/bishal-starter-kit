<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function theme(){
        $setting=Setting::first();
        return Inertia::render('Settings/Theme',[
            'setting'=>$setting
        ]);
    }
    
    public function themeUpdate(Request $request)
    {
        $data = $request->validate([
            'primary_color' => ['nullable', 'string'],
            'secondary_color' => ['nullable', 'string'],
            'accent_color' => ['nullable', 'string'],
            'success_color' => ['nullable', 'string'],
            'warning_color' => ['nullable', 'string'],
            'danger_color' => ['nullable', 'string'],
            'info_color' => ['nullable', 'string'],

            'background_color' => ['nullable', 'string'],
            'text_color' => ['nullable', 'string'],
            'border_color' => ['nullable', 'string'],

            'sidebar_position' => ['nullable', 'string'],
            'sidebar_width' => ['nullable', 'integer'],
            'sidebar_bg_color' => ['nullable', 'string'],
            'sidebar_text_color' => ['nullable', 'string'],
            'sidebar_hover_color' => ['nullable', 'string'],
            'sidebar_hover_text_color' => ['nullable', 'string'],
            'sidebar_active_color' => ['nullable', 'string'],
            'sidebar_icon_color' => ['nullable', 'string'],
            'sidebar_collapsed' => ['boolean'],

            'navbar_bg_color' => ['nullable', 'string'],
            'navbar_text_color' => ['nullable', 'string'],
            'navbar_border_color' => ['nullable', 'string'],
            'navbar_height' => ['nullable', 'integer'],

            'layout_mode' => ['nullable', 'string'],
            'theme_mode' => ['nullable', 'string'],
            'rtl' => ['boolean'],

            'card_border_radius' => ['nullable', 'integer'],
            'card_shadow' => ['boolean'],
            'card_header_color' => ['nullable'],
            'card_footer_color' => ['nullable'],
            'card_bg_color' => ['nullable'],

            'button_border_radius' => ['nullable', 'integer'],

            'table_striped' => ['boolean'],
            'table_bordered' => ['boolean'],

            'font_family' => ['nullable', 'string'],
            'font_size' => ['nullable', 'integer'],

            'footer_text' => ['nullable', 'string'],
            'show_footer' => ['boolean'],

            'enable_animations' => ['boolean'],
            'enable_breadcrumbs' => ['boolean'],
            'enable_notifications' => ['boolean'],
        ]);

        $setting = Setting::first();

        if (! $setting) {
            $setting = Setting::create($data);
        } else {
            $setting->update($data);
        }

        return back()->with('success', 'Theme settings updated successfully.');
    }
}
