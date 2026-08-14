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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->string('application_version')->default('1.0.0');
            $table->string('default_language')->default('en'); //en or np
            $table->string('timezone')->default('en'); //Nepal
            $table->unsignedTinyInteger('decimal_places')->default(2);
            $table->boolean('maintenance_mode')->default(false);
            $table->json('maintenance_mode_allowed_ips')->nullable();
            $table->string('session_timeout')->default(2); //in days
            $table->string('data_table_source')->default('server'); //server side or client side
            $table->string('default_pagination_size')->default(20);



            $table->boolean('enable_registration')->default(false);
            $table->boolean('enable_email_verification')->default(false);
            $table->boolean('enable_2fa')->default(false);
            $table->boolean('enable_multiple_branch')->default(false);


            $table->boolean('force_logout_on_password_change')->default(true);
            $table->boolean('invalidate_other_sessions')->default(true);
            // 
            $table->string('cache_lifetime')->default(20);
            // $table->boolean('enable_2fa')->default(false); 
            // 
            // date/time
            $table->string('date_type')->default('ad'); //bs or ad
            $table->string('date_format')->default('Y-m-d');
            $table->string('time_format')->default('24hour');

            // Screen Saver
            $table->boolean('enable_screen_saver')->default(false);
            $table->unsignedInteger('screen_saver_timeout')->default(300); // seconds (5 min)
            $table->enum('screen_saver_type', ['image', 'slider', 'video'])->default('image');
            $table->json('screen_saver_images')->nullable();
            $table->string('screen_saver_video')->nullable();
            $table->boolean('screen_saver_show_clock')->default(true);
            $table->boolean('screen_saver_show_date')->default(true);

            // Auto Logout
            $table->boolean('enable_auto_logout')->default(false);
            $table->unsignedInteger('auto_logout_time')->default(30); // minutes
            $table->boolean('show_logout_warning')->default(true);
            $table->unsignedInteger('logout_warning_time')->default(1); // minutes before logout


            // IP Security
            $table->boolean('enable_ip_blacklist')->default(false);
            $table->json('blacklisted_ips')->nullable();
            $table->boolean('log_blocked_ip_attempts')->default(true);

            // Login Security
            $table->boolean('enable_login_attempt_limit')->default(true);
            $table->unsignedTinyInteger('max_login_attempts')->default(5);
            $table->unsignedInteger('login_lockout_duration')->default(15); // minutes

            $table->text('footer_text')->nullable();

            // Backup
            $table->boolean('enable_auto_backup')->default(false);
            $table->string('backup_frequency')->default('daily');
            $table->unsignedInteger('backup_retention_days')->default(30);

            // Password_policy
            $table->boolean('enable_password_policy')->default(false);
            $table->unsignedTinyInteger('minimum_password_length')->default(8);
            $table->boolean('require_uppercase')->default(true);
            $table->boolean('require_lowercase')->default(true);
            $table->boolean('require_number')->default(true);
            $table->boolean('require_special_character')->default(false);
            $table->string('password_expiry_days')->default(90);

            // Users
            $table->boolean('auto_disable_inactive_users')->default(false);
            $table->unsignedInteger('inactive_user_days')->default(90);
            $table->boolean('enable_delete_account')->default(false);
            $table->boolean('force_single_device_login')->default(false);

            // licence
            $table->string('license_key')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configurations');
    }
};
