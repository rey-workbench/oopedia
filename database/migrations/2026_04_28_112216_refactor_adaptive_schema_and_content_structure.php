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
        // 1. Remove SubMaterial dependencies (Already handled if table doesn't exist, but safe to keep)
        if (Schema::hasTable('questions') && Schema::hasColumn('questions', 'sub_material_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->dropForeign(['sub_material_id']);
                $table->dropColumn('sub_material_id');
            });
        }

        Schema::dropIfExists('sub_materials');

        // 2. Refactor StudentState for Rule R01-R15
        Schema::table('student_states', function (Blueprint $table) {
            // Remove ALL legacy/obsolete fields
            $table->dropColumn([
                'learning_style',
                'unlocked_modules',
                'certifications',
                'time_distribution',
                'wrong_count',
                'wrong_streak',
            ]);

            // Add/Ensure Adaptive Engine required fields
            $table->decimal('accuracy', 5, 2)->default(0)->after('correct_count');

            $table->json('session_history')->nullable()->after('accuracy')
                ->comment('Last 5 session accuracies for trend analysis');

            $table->json('current_session')->nullable()->after('session_history')
                ->comment('Live session data: [correct, total, hints, time_spent]');

            $table->json('performance_metrics')->nullable()->after('current_session')
                ->comment('Aggregated analysis: trend, speed, stagnant_count');

            $table->json('adaptive_state')->nullable()->after('performance_metrics')
                ->comment('Stateful engine data for complex rules');
        });

        // 3. Overhaul AdaptiveRules for Multi-Action (Rule R01-R15)
        Schema::table('adaptive_rules', function (Blueprint $table) {
            // Drop legacy singular action if it exists
            if (Schema::hasColumn('adaptive_rules', 'action_id')) {
                $table->dropForeign(['action_id']);
                $table->dropColumn('action_id');
            }

            // Drop ALL redundant code/string based columns
            $table->dropColumn([
                'action_codes',
                'required_facts',
                'deduced_facts',
            ]);

            // Add ID-centric JSON columns
            $table->json('action_ids')->nullable()->after('priority');
            $table->json('required_fact_ids')->nullable()->after('action_ids');
            $table->json('deduced_fact_ids')->nullable()->after('required_fact_ids');
        });

        Schema::dropIfExists('adaptive_rule_actions');
    }

    public function down(): void
    {
        Schema::table('student_states', function (Blueprint $table) {
            $table->string('learning_style')->default('visual');
            $table->json('unlocked_modules')->nullable();
            $table->json('certifications')->nullable();
            $table->json('time_distribution')->nullable();
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('wrong_streak')->default(0);

            $table->dropColumn(['accuracy', 'session_history', 'current_session', 'performance_metrics', 'adaptive_state']);
        });

        Schema::create('sub_materials', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('material_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('jenis_konten')->default('teori');
            $table->string('learning_style')->default('mixed');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->ulid('sub_material_id')->nullable()->after('material_id');
        });
    }
};
