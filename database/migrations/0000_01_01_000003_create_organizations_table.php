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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            // Parent branch
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('organizations')
                ->nullOnDelete();
            // Basic information
            $table->string('name');
            $table->string('slug')->unique();

            // Contact information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('vat')->nullable();

            // Organization branding
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            // Organization status
            $table->boolean('is_active')->default(true);

            // Trial
            $table->timestamp('trial_ends_at')->nullable();

            // Subscription
            $table->string('subscription_status')->default('trial');
            $table->timestamp('subscription_ends_at')->nullable();

            // Soft delete
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
