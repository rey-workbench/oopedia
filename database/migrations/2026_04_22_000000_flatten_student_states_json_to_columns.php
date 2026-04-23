<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add flat columns (nullable so existing rows survive)
        Schema::table('student_states', function (Blueprint $table) {
            // Gamification
            $table->unsignedInteger('xp')->default(0)->after('user_id');
            $table->string('level')->default('Pemula')->after('xp');
            $table->unsignedInteger('streak')->default(0)->after('level');
            $table->unsignedInteger('max_streak')->default(0)->after('streak');
            $table->json('badges')->nullable()->after('max_streak');

            // Learning profile
            $table->string('learning_style')->default('visual')->after('badges');
            $table->json('unlocked_modules')->nullable()->after('learning_style');
            $table->json('certifications')->nullable()->after('unlocked_modules');
            $table->json('time_distribution')->nullable()->after('certifications');

            // Performance metrics
            $table->unsignedInteger('total_answered')->default(0)->after('time_distribution');
            $table->unsignedInteger('correct_count')->default(0)->after('total_answered');
            $table->unsignedInteger('wrong_count')->default(0)->after('correct_count');
            $table->unsignedInteger('wrong_streak')->default(0)->after('wrong_count');
            $table->unsignedInteger('hints_used')->default(0)->after('wrong_streak');
            $table->unsignedInteger('hints_available')->default(3)->after('hints_used');

            // Navigation
            $table->string('current_material_id')->nullable()->after('hints_available');
            $table->string('target_difficulty')->nullable()->after('current_material_id');
        });

        // 2. Migrate existing JSON data into flat columns
        DB::table('student_states')->orderBy('id')->each(function ($row) {
            $g = (array) json_decode($row->gamification_data ?? '{}', true);
            $p = (array) json_decode($row->performance_metrics ?? '{}', true);
            $l = (array) json_decode($row->learning_profile ?? '{}', true);
            $n = (array) json_decode($row->navigation ?? '{}', true);

            DB::table('student_states')->where('id', $row->id)->update([
                // Gamification
                'xp'          => $g['global_xp']        ?? 0,
                'level'       => $g['current_level']    ?? 'Pemula',
                'streak'      => $g['current_streak']   ?? 0,
                'max_streak'  => $g['max_streak']       ?? 0,
                'badges'      => json_encode($g['badges'] ?? []),

                // Learning profile
                'learning_style'    => $l['learning_style']    ?? 'visual',
                'unlocked_modules'  => json_encode($l['unlocked_modules'] ?? ['1']),
                'certifications'    => json_encode($l['certifications']   ?? []),
                'time_distribution' => json_encode($l['time_distribution'] ?? []),

                // Performance
                'total_answered'  => $p['total_questions_answered']  ?? 0,
                'correct_count'   => $p['correct_count']             ?? 0,
                'wrong_count'     => $p['wrong_count']               ?? 0,
                'wrong_streak'    => $p['wrong_streak']              ?? 0,
                'hints_used'      => $p['hints_used_count']          ?? 0,
                'hints_available' => $p['hints_available']           ?? 3,

                // Navigation
                'current_material_id' => $n['current_material_id']  ?? null,
                'target_difficulty'   => $n['target_difficulty']    ?? null,
            ]);
        });

        // 3. Drop old JSON blob columns
        Schema::table('student_states', function (Blueprint $table) {
            $table->dropColumn([
                'gamification_data',
                'learning_profile',
                'performance_metrics',
                'navigation',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('student_states', function (Blueprint $table) {
            $table->json('gamification_data')->nullable();
            $table->json('learning_profile')->nullable();
            $table->json('performance_metrics')->nullable();
            $table->json('navigation')->nullable();

            $table->dropColumn([
                'xp', 'level', 'streak', 'max_streak', 'badges',
                'learning_style', 'unlocked_modules', 'certifications', 'time_distribution',
                'total_answered', 'correct_count', 'wrong_count', 'wrong_streak',
                'hints_used', 'hints_available',
                'current_material_id', 'target_difficulty',
            ]);
        });
    }
};
