<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licences', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('branch_id')
                ->nullable()
                ->index();

            $table->string('key')
                ->unique();

            $table->enum('status', [
                'active',
                'expired',
                'revoked',
                'suspended',
            ])->default('active')->index();

            $table->unsignedInteger('max_users')
                ->nullable();

            $table->timestamp('issued_at')
                ->nullable();

            $table->timestamp('starts_at')
                ->nullable();

            $table->timestamp('expires_at')
                ->nullable()
                ->index();

            $table->timestamp('activated_at')
                ->nullable();

            $table->timestamp('revoked_at')
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licences');
    }
};