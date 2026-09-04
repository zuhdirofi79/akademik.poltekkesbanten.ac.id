<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('graduation_toga_orders')) {
            return;
        }

        Schema::create('graduation_toga_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->string('student_name');
            $table->string('nim');
            $table->string('nik')->nullable();
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->nullOnDelete();
            $table->string('study_program_name')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('gender');
            $table->string('toga_size');
            $table->text('size_note')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('status')->default('pending_payment');
            $table->string('payment_status')->default('unpaid');
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->string('payment_proof_original_name')->nullable();
            $table->string('payment_proof_mime')->nullable();
            $table->unsignedInteger('payment_proof_size')->nullable();
            $table->timestamp('payment_uploaded_at')->nullable();
            $table->timestamp('payment_verified_at')->nullable();
            $table->foreignId('payment_verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('payment_rejected_at')->nullable();
            $table->foreignId('payment_rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_note')->nullable();
            $table->text('rejection_note')->nullable();
            $table->string('public_token')->nullable()->unique();
            $table->timestamps();

            $table->index('status');
            $table->index('payment_status');
            $table->index('nim');
            $table->index('email');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('graduation_toga_orders');
    }
};
