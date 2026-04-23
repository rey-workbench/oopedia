<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clean up adaptive_rules:
 * - Drop legacy string columns (condition_fact, action_code, action_instructions, fact_code, h_action_code)
 * - Add proper integer FK columns (fact_id, action_id)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            // Drop legacy FK string indexes first
            $table->dropForeign(['fact_code']);
            $table->dropForeign(['h_action_code']);

            // Drop all legacy/redundant columns
            $table->dropColumn([
                'condition_fact',
                'action_code',
                'action_instructions',
                'fact_code',
                'h_action_code',
            ]);

            // Add clean integer FK columns
            $table->unsignedBigInteger('fact_id')->nullable()->after('node_type');
            $table->unsignedBigInteger('action_id')->nullable()->after('condition_value');

            $table->foreign('fact_id')->references('id')->on('adaptive_facts')->onDelete('set null');
            $table->foreign('action_id')->references('id')->on('adaptive_actions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->dropForeign(['fact_id']);
            $table->dropForeign(['action_id']);
            $table->dropColumn(['fact_id', 'action_id']);

            // Restore legacy columns
            $table->string('condition_fact')->nullable();
            $table->string('fact_code')->nullable();
            $table->string('action_code')->nullable();
            $table->string('h_action_code')->nullable();
            $table->json('action_instructions')->nullable();
        });
    }
};
