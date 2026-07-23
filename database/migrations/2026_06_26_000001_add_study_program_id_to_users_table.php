<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'study_program_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('study_program_id')
                    ->nullable()
                    ->after('role')
                    ->constrained('study_programs')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'study_program_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('study_program_id');
            });
        }
    }
};
