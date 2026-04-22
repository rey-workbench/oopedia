<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Standardize adaptive schema names to be more intuitive and "frozen".
     */
    public function up(): void
    {
        // 1. student_states
        Schema::table('student_states', function (Blueprint $table) {
            $table->renameColumn('adaptive_state', 'navigation');
        });

        // 2. adaptive_execution_logs
        Schema::table('adaptive_execution_logs', function (Blueprint $table) {
            $table->renameColumn('facts', 'trigger_facts');
            $table->renameColumn('previous_state', 'state_deltas');
            $table->renameColumn('context', 'execution_context');
        });

        // 3. adaptive_rules
        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->renameColumn('action_params', 'action_instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_states', function (Blueprint $table) {
            $table->renameColumn('navigation', 'adaptive_state');
        });

        Schema::table('adaptive_execution_logs', function (Blueprint $table) {
            $table->renameColumn('trigger_facts', 'facts');
            $table->renameColumn('state_deltas', 'previous_state');
            $table->renameColumn('execution_context', 'context');
        });

        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->renameColumn('action_instructions', 'action_params');
        });
    }
};
