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
        // Drop if exists to ensure fresh start for this refactor
        Schema::dropIfExists('student_states');

        Schema::create('student_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            
            // Gamification
            $table->integer('global_xp')->default(0);
            $table->string('current_level')->default('Pemula');
            $table->integer('current_streak')->default(0);
            $table->integer('max_streak')->default(0);
            
            // Learning Profile
            $table->string('learning_style')->nullable(); // visual, auditory, kinesthetic
            $table->json('mastery_levels')->nullable(); // {"topic_A": 80, "topic_B": 40}
            
            // Adaptive State
            $table->json('adaptive_variables')->nullable(); // Temporary storage for adaptive engine
                
            $table->json('badges')->nullable(); // ["badge_1", "badge_2"]
            $table->json('unlocked_modules')->nullable(); // [1, 2, 3]            
            $table->integer('total_questions_answered')->default(0);
            $table->integer('correct_count')->default(0);
            $table->integer('wrong_count')->default(0);
            $table->integer('wrong_streak')->default(0);
            $table->integer('hints_used_count')->default(0);
            $table->integer('hints_available')->default(3); // Number of hints available to use

            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();
            
            $table->index('user_id');
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
