<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('academic_satisfaction_surveys')) {
            return;
        }

        Schema::create('academic_satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->string('satisfaction_level');
            $table->foreignId('service_type_id')->nullable()->constrained('service_types')->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->string('reason')->nullable();
            $table->text('feedback')->nullable();
            $table->string('respondent_name')->nullable();
            $table->string('respondent_email')->nullable();
            $table->string('ticket_number', 100)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index('satisfaction_level');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_satisfaction_surveys');
    }
};
