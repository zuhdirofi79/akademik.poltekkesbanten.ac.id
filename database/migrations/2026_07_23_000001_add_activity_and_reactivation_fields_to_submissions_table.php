<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('submissions', 'activity_name')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('activity_name')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'activity_date')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->date('activity_date')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'activity_location')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('activity_location')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'participant_list')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->text('participant_list')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'previous_status')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('previous_status')->nullable();
            });
        }

        if (! Schema::hasColumn('submissions', 'reactivation_reason')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->text('reactivation_reason')->nullable();
            });
        }

        if (Schema::hasColumn('submissions', 'nik')) {
            $driver = Schema::getConnection()->getDriverName();
            if ($driver === 'mysql') {
                DB::statement('ALTER TABLE submissions MODIFY nik VARCHAR(16) NULL');
            } elseif ($driver === 'pgsql') {
                DB::statement('ALTER TABLE submissions ALTER COLUMN nik DROP NOT NULL');
            } else {
                Schema::table('submissions', function (Blueprint $table) {
                    $table->string('nik', 16)->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        $columns = [
            'activity_name',
            'activity_date',
            'activity_location',
            'participant_list',
            'previous_status',
            'reactivation_reason',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('submissions', $column)) {
                Schema::table('submissions', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
