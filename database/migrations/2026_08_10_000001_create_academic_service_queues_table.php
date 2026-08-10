<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('academic_service_queues')) {
            return;
        }

        Schema::create('academic_service_queues', function (Blueprint $table) {
            $table->id();
            $table->date('queue_date');
            $table->unsignedInteger('queue_number');
            $table->string('queue_code', 20);
            $table->string('visitor_name')->nullable();
            $table->string('visitor_identity')->nullable();
            $table->string('visitor_phone', 30)->nullable();
            $table->string('visitor_type', 50)->nullable();
            $table->foreignId('service_type_id')->nullable()->constrained('service_types')->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->string('purpose')->nullable();
            $table->string('status', 30)->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->foreignId('called_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('served_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('skipped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('finished_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->unique(['queue_date', 'queue_number']);
            $table->unique(['queue_date', 'queue_code']);
            $table->index('queue_date');
            $table->index('status');
            $table->index('queue_number');
            $table->index('service_type_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_service_queues');
    }
};
