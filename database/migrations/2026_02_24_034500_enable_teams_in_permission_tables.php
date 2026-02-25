<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add unit_id to activity_log for tenant-scoped activity logs
        if (!Schema::hasColumn('activity_log', 'unit_id')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->foreignId('unit_id')->nullable()->constrained()->nullOnDelete()->after('batch_uuid');
                $table->index('unit_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('activity_log', 'unit_id')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->dropForeign(['unit_id']);
                $table->dropColumn('unit_id');
            });
        }
    }
};
