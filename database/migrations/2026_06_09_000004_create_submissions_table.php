<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->string('progress_token', 64)->unique();
            $table->foreignId('service_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('study_program_id')->constrained()->cascadeOnDelete();
            $table->string('nim');
            $table->string('full_name');
            $table->string('nik', 16);
            $table->string('email');
            $table->string('whatsapp');
            $table->string('semester')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('request_type')->nullable();
            $table->text('corrected_data')->nullable();
            $table->text('purpose')->nullable();
            $table->string('issue_type')->nullable();
            $table->text('leave_reason')->nullable();
            $table->string('entry_year')->nullable();
            $table->string('graduation_year')->nullable();
            $table->string('alumni_letter_type')->nullable();
            $table->text('additional_note')->nullable();
            $table->string('status')->default('Diajukan');
            $table->text('admin_note')->nullable();
            $table->boolean('ready_to_send_email')->default(false);
            $table->timestamp('email_sent_at')->nullable();
            $table->text('email_error')->nullable();
            $table->boolean('data_confirmed')->default(false);
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('nim');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
