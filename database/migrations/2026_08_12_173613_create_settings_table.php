<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        /**
         * Run the migrations.
         */
        public function up(): void
        {
                Schema::create('settings', function (Blueprint $table) {
                        $table->id();
                        /*
    
    |--------------------------------------------------------------------------
    | Theme Colors
    |--------------------------------------------------------------------------
    */
                        $table->string('primary_color')->default('#3d98aa');
                        $table->string('secondary_color')->default('#2f7f8f');
                        $table->string('accent_color')->default('#4fb6c8');
                        $table->string('success_color')->default('#22c55e');
                        $table->string('warning_color')->default('#f59e0b');
                        $table->string('danger_color')->default('#ef4444');
                        $table->string('info_color')->default('#06b6d4');

                        $table->string('background_color')->default('#ffffff');
                        $table->string('text_color')->default('#1e293b');
                        $table->string('border_color')->default('#e2e8f0');

                        /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    */
                        $table->enum('sidebar_position', [
                                'left',
                                'right'
                        ])->default('left');

                        $table->integer('sidebar_width')->default(280);

                        $table->string('sidebar_bg_color')->default('#0f172a');
                        $table->string('sidebar_text_color')->default('#ffffff');
                        $table->string('sidebar_hover_color')->default('#1e293b');
                        $table->string('sidebar_hover_text_color')->default('#1e293b');
                        $table->string('sidebar_active_color')->default('#3d98aa');
                        $table->string('sidebar_icon_color')->default('#cbd5e1');
                        $table->boolean('sidebar_collapsed')->default(false);

                        /*
    |--------------------------------------------------------------------------
    | Navbar / Header
    |--------------------------------------------------------------------------
    */
                        $table->string('navbar_bg_color')->default('#ffffff');
                        $table->string('navbar_text_color')->default('#1e293b');
                        $table->string('navbar_border_color')->default('#e2e8f0');

                        $table->integer('navbar_height')->default(70);

                        /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    */
                        $table->enum('layout_mode', [
                                'boxed',
                                'full'
                        ])->default('full');

                        $table->enum('theme_mode', [
                                'light',
                                'dark',
                                'system'
                        ])->default('light');

                        $table->boolean('rtl')->default(false);

                        /*
    |--------------------------------------------------------------------------
    | Cards
    |--------------------------------------------------------------------------
    */
                        $table->integer('card_border_radius')->default(16);
                        $table->boolean('card_shadow')->default(true);

                        /*
    |--------------------------------------------------------------------------
    | Buttons
    |--------------------------------------------------------------------------
    */
                        $table->integer('button_border_radius')->default(10);

                        /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    */
                        $table->boolean('table_striped')->default(true);
                        $table->boolean('table_bordered')->default(false);

                        /*
    |--------------------------------------------------------------------------
    | Typography
    |--------------------------------------------------------------------------
    */
                        $table->string('font_family')->default('Inter');
                        $table->integer('font_size')->default(14);

                        /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */
                        $table->string('footer_text')->nullable();
                        $table->boolean('show_footer')->default(true);

                        /*
    |--------------------------------------------------------------------------
    | Misc
    |--------------------------------------------------------------------------
    */
                        $table->boolean('enable_animations')->default(true);
                        $table->boolean('enable_breadcrumbs')->default(true);
                        $table->boolean('enable_notifications')->default(true);
                        $table->timestamps();
                });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
                Schema::dropIfExists('settings');
        }
};
