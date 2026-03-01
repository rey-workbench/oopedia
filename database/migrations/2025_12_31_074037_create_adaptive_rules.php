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
            $table->json('conditions')->nullable();

            // Aksi (THEN)
            $table->json('actions')->nullable();

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
