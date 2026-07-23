<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submissions', 'jurusan_verified_at')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->timestamp('jurusan_verified_at')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'jurusan_verified_by')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->foreignId('jurusan_verified_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('submissions', 'jurusan_verification_note')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->text('jurusan_verification_note')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'central_verified_at')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->timestamp('central_verified_at')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'central_verified_by')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->foreignId('central_verified_by')->nullable()->constrained('users')->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('submissions', 'central_verification_note')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->text('central_verification_note')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('submissions', 'jurusan_verified_by')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('jurusan_verified_by');
            });
        }

        if (Schema::hasColumn('submissions', 'central_verified_by')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('central_verified_by');
            });
        }

        foreach (['jurusan_verified_at', 'jurusan_verification_note', 'central_verified_at', 'central_verification_note'] as $column) {
            if (Schema::hasColumn('submissions', $column)) {
                Schema::table('submissions', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
