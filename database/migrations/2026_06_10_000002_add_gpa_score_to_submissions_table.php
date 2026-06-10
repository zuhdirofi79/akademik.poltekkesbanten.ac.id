<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submissions', 'gpa_score')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->decimal('gpa_score', 3, 2)->nullable()->after('semester');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('submissions', 'gpa_score')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->dropColumn('gpa_score');
            });
        }
    }
};
