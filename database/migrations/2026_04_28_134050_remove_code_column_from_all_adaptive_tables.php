<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = ['adaptive_actions', 'adaptive_facts', 'adaptive_rules'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                // Drop primary key first if necessary (for MySQL)
                // $table->dropPrimary(); // Seringkali tidak bisa langsung drop jika auto-increment
            });

            // Cara paling aman di Laravel untuk mengubah Primary Key dari Int ke String
            DB::statement("ALTER TABLE {$tableName} MODIFY id VARCHAR(255) NOT NULL");

            Schema::table($tableName, function (Blueprint $table) {
                if (Schema::hasColumn($table->getTable(), 'code')) {
                    $table->dropColumn('code');
                }
            });
        }

        Schema::table('adaptive_execution_logs', function (Blueprint $table) {
            if (Schema::hasColumn('adaptive_execution_logs', 'code')) {
                $table->renameColumn('code', 'rule_id');
            }
            if (Schema::hasColumn('adaptive_execution_logs', 'action_code')) {
                $table->renameColumn('action_code', 'action_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('all_adaptive_tables', function (Blueprint $table) {
            //
        });
    }
};
