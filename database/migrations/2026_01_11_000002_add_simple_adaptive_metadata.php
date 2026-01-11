<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Add simple metadata for adaptive system.
     */
    public function up(): void
    {
        // 1. Add question_type enum to questions (for G09-G10 error detection)
        DB::statement("ALTER TABLE questions ADD COLUMN type ENUM('teori', 'sintaks') DEFAULT 'teori' AFTER difficulty");
        
        // 2. Modify difficulty enum to include 'final' for final projects (for G18)
        DB::statement("ALTER TABLE questions MODIFY COLUMN difficulty ENUM('beginner', 'medium', 'hard', 'final') DEFAULT 'beginner'");
        
        // 3. Add module_id to materials (for G13-G25 module facts)
        Schema::table('materials', function (Blueprint $table) {
            $table->integer('module_id')->nullable()->after('content')
                ->comment('1=Foundation, 2=Encapsulation, 3=Inheritance, 4=Polymorphism, 5=Abstraction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added columns
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('module_id');
        });
        
        // Restore original difficulty enum
        DB::statement("ALTER TABLE questions MODIFY COLUMN difficulty ENUM('beginner', 'medium', 'hard') DEFAULT 'beginner'");
    }
};
