<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('admin')->after('password');
            $table->boolean('must_change_password')->default(true)->after('role');
            $table->boolean('is_active')->default(true)->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'must_change_password', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin'])->default('admin')->after('password');
        });
    }
};
