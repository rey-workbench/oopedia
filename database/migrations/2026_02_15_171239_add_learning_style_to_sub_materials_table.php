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
        Schema::table('sub_materials', function (Blueprint $table) {
            $table->enum('learning_style', ['visual', 'textual', 'mixed'])->default('mixed')->after('jenis_konten')
                ->comment('Learning style suitability: visual, textual, or mixed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_materials', function (Blueprint $table) {
            $table->dropColumn('learning_style');
        });
    }
};
