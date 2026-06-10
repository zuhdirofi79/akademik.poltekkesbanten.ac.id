<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->string('file_category');
            $table->string('original_name');
            $table->string('stored_path');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->enum('uploaded_by', ['applicant', 'admin'])->default('applicant');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_files');
    }
};
