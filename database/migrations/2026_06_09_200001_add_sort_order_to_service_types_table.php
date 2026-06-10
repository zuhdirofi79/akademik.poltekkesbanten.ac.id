<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('service_types', 'sort_order')) {
            Schema::table('service_types', function (Blueprint $table) {
                if (Schema::hasColumn('service_types', 'is_active')) {
                    $table->unsignedInteger('sort_order')->nullable()->default(0)->after('is_active');
                } else {
                    $table->unsignedInteger('sort_order')->nullable()->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('service_types', 'sort_order')) {
            Schema::table('service_types', function (Blueprint $table) {
                $table->dropColumn('sort_order');
            });
        }
    }
};