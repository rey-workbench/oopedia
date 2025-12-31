<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adaptive_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('cascade');
            
            // Kondisi (IF)
            $table->string('condition_type'); // 'score_range', 'consecutive_correct', 'consecutive_wrong', 'time_spent'
            $table->string('condition_operator'); // '>', '<', '>=', '<=', '==', 'between'
            $table->string('condition_value'); // nilai atau range (misal: "70" atau "60-80")
            
            // Aksi (THEN)
            $table->string('action_type'); // 'change_difficulty', 'show_hint', 'skip_questions', 'recommend_material'
            $table->string('action_value'); // nilai aksi (misal: "hard", "medium", "beginner")
            
            $table->integer('priority')->default(0); // urutan eksekusi rule
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_rules');
    }
};
