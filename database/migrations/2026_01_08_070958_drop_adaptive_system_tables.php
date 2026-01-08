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
        // Drop foreign keys first to avoid constraint violations
        Schema::table('attribute_definitions', function (Blueprint $table) {
            $table->dropForeign(['formula_id']);
        });

        // Drop adaptive system tables - no longer needed with code-based rules
        Schema::dropIfExists('attribute_definitions');
        Schema::dropIfExists('adaptive_rules');
        Schema::dropIfExists('formulas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: We don't recreate these tables in down() because the schema
        // is complex and the old system is being completely replaced.
        // If rollback is needed, restore from backup or use older migrations.
    }
};
