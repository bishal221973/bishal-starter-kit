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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
           $table->foreignId('user_id')
        ->constrained()
        ->cascadeOnDelete();
            // Document information
            $table->string('document_type');
            // citizenship, passport, driving_license, contract,
            // qualification, experience_letter, etc.

            $table->string('title');

            $table->string('document_number')->nullable();

            // File
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('file_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();

            // Dates
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();


            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_documents');
    }
};
