<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create sub_materials table for better content organization.
     * Structure: Module → Material → SubMaterial → Question → Answer
     */
    public function up(): void
    {
        // Create sub_materials table
        Schema::create('sub_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->enum('jenis_konten', ['teori', 'sintaks', 'mixed'])->default('teori')
                ->comment('Jenis konten: teori (konsep), sintaks (kode), atau mixed');
            $table->integer('order')->default(0)->comment('Urutan tampilan dalam material');
            $table->timestamps();
            
            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
        
        // Add sub_material_id to questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->unsignedBigInteger('sub_material_id')->nullable()->after('material_id');
            $table->foreign('sub_material_id')->references('id')->on('sub_materials')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['sub_material_id']);
            $table->dropColumn('sub_material_id');
        });
        
        Schema::dropIfExists('sub_materials');
    }
};
