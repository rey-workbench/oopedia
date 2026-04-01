<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index('is_correct', 'quiz_attempts_is_correct_index');
            $table->index(['user_id', 'is_correct'], 'quiz_attempts_user_correct_index');
            $table->index(['user_id', 'created_at'], 'quiz_attempts_user_created_index');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->index('difficulty', 'questions_difficulty_index');
            $table->index(['material_id', 'difficulty'], 'questions_material_difficulty_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('is_approved', 'users_is_approved_index');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->index('is_correct', 'answers_is_correct_index');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex('quiz_attempts_is_correct_index');
            $table->dropIndex('quiz_attempts_user_correct_index');
            $table->dropIndex('quiz_attempts_user_created_index');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('questions_difficulty_index');
            $table->dropIndex('questions_material_difficulty_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_is_approved_index');
        });

        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex('answers_is_correct_index');
        });
    }
};
