<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN difficulty ENUM('beginner', 'medium', 'hard', 'final') DEFAULT 'beginner'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE questions MODIFY COLUMN difficulty ENUM('beginner', 'medium', 'hard') DEFAULT 'beginner'");
    }
};
