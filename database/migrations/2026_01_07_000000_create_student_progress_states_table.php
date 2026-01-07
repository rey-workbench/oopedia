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
        Schema::create('student_progress_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('material_id')->constrained()->onDelete('cascade');
            
            // Level Tracking
            $table->enum('current_level', ['beginner', 'medium', 'hard'])->default('beginner');
            
            // Streak & Performance Tracking
            $table->integer('current_streak')->default(0); // Correct answers in a row
            $table->integer('wrong_streak')->default(0); // Wrong answers in a row
            
            // Rewards
            $table->integer('total_xp')->default(0);
            $table->integer('total_points')->default(0);
            $table->integer('hints_remaining')->default(0);
            
            // Level Dynamics
            $table->integer('retry_count')->default(0); // Retries on current level
            
            // Accuracy Calculation for current level session
            $table->integer('level_correct_count')->default(0); 
            $table->integer('level_attempt_count')->default(0);
            
            // Achievements
            $table->json('badges')->nullable(); // Arrays of badge codes e.g. ["ACCURATE", "FAST"]
            
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            // Unique constraint to ensure one state per user per material
            $table->unique(['user_id', 'material_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress_states');
    }
};
