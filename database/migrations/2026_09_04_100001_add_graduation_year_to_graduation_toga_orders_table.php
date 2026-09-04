<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('graduation_toga_orders')) {
            return;
        }

        if (Schema::hasColumn('graduation_toga_orders', 'graduation_year')) {
            return;
        }

        Schema::table('graduation_toga_orders', function (Blueprint $table) {
            $table->unsignedSmallInteger('graduation_year')->nullable()->after('study_program_name');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('graduation_toga_orders')) {
            return;
        }

        if (! Schema::hasColumn('graduation_toga_orders', 'graduation_year')) {
            return;
        }

        Schema::table('graduation_toga_orders', function (Blueprint $table) {
            $table->dropColumn('graduation_year');
        });
    }
};
