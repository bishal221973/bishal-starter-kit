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
        Schema::create('organization_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Employee information
            $table->string('employee_code')->nullable();

            $table->enum('gender', [
                'male',
                'female',
                'other',
            ])->nullable();

            $table->date('date_of_birth')->nullable();

            // Contact
            $table->string('personal_phone')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();

            // Employment
            $table->string('employee_type')->default('full_time');
            // full_time, part_time, contract, intern

            $table->string('department')->nullable();
            $table->string('designation')->nullable();

            $table->date('joined_at')->nullable();
            $table->date('probation_ends_at')->nullable();
            $table->date('employment_ends_at')->nullable();

            // Salary
            $table->decimal('salary', 12, 2)->nullable();
            $table->string('salary_type')->default('monthly');
            // monthly, yearly, hourly


            // Identification
            $table->string('tax_number')->nullable();

            // Status
            $table->boolean('is_active')->default(true);

            $table->boolean('can_login')->default(true);


            $table->unique([
                'organization_id',
                'user_id',
            ]);

            $table->unique([
                'organization_id',
                'employee_code',
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_user');
    }
};
