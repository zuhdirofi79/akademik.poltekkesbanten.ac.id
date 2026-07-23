<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('academic_satisfaction_surveys')) {
            return;
        }

        if (! Schema::hasColumn('academic_satisfaction_surveys', 'submission_id')) {
            Schema::table('academic_satisfaction_surveys', function (Blueprint $table) {
                $table->foreignId('submission_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('submissions')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('academic_satisfaction_surveys')) {
            return;
        }

        if (Schema::hasColumn('academic_satisfaction_surveys', 'submission_id')) {
            Schema::table('academic_satisfaction_surveys', function (Blueprint $table) {
                $table->dropConstrainedForeignId('submission_id');
            });
        }
    }
};
