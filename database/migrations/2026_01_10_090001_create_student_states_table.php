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
        Schema::dropIfExists('student_states');

        Schema::create('student_states', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();

            // Gamification
            $table->unsignedInteger('xp')->default(0);
            $table->string('level')->default('Pemula');
            $table->integer('streak')->default(0);
            $table->integer('max_streak')->default(0);

            // Learning profile
            $table->string('learning_style')->default('visual');
            $table->json('certifications')->nullable();
            $table->json('unlocked_modules')->nullable();
            $table->json('time_distribution')->nullable();

            // Performance metrics
            $table->unsignedInteger('total_answered')->default(0);
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('wrong_count')->default(0);
            $table->unsignedInteger('wrong_streak')->default(0);
            $table->unsignedInteger('hints_used')->default(0);
            $table->unsignedInteger('hints_available')->default(3);

            // Navigation
            $table->string('current_material_id')->nullable();
            $table->string('target_difficulty')->nullable();

            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->index('xp');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_states');
    }
};
