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
        Schema::dropIfExists('student_progress_states');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create table if rolled back (simplified, as exact recreation is verbose)
        Schema::create('student_progress_states', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
};
