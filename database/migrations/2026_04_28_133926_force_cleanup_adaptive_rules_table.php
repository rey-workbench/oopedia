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
        Schema::table('adaptive_rules', function (Blueprint $table) {
            // Force drop the "useless" code-based columns
            $colsToDrop = [];
            if (Schema::hasColumn('adaptive_rules', 'action_codes')) {
                $colsToDrop[] = 'action_codes';
            }
            if (Schema::hasColumn('adaptive_rules', 'required_facts')) {
                $colsToDrop[] = 'required_facts';
            }
            if (Schema::hasColumn('adaptive_rules', 'deduced_facts')) {
                $colsToDrop[] = 'deduced_facts';
            }
            if (Schema::hasColumn('adaptive_rules', 'action_id')) {
                $colsToDrop[] = 'action_id';
            }

            if (! empty($colsToDrop)) {
                $table->dropColumn($colsToDrop);
            }

            // Ensure the correct ID-based columns exist
            if (! Schema::hasColumn('adaptive_rules', 'action_ids')) {
                $table->json('action_ids')->nullable()->after('priority');
            }
            if (! Schema::hasColumn('adaptive_rules', 'required_fact_ids')) {
                $table->json('required_fact_ids')->nullable()->after('action_ids');
            }
            if (! Schema::hasColumn('adaptive_rules', 'deduced_fact_ids')) {
                $table->json('deduced_fact_ids')->nullable()->after('required_fact_ids');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            //
        });
    }
};
