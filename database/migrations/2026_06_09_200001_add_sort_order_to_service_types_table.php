<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->nullable()->default(0)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
