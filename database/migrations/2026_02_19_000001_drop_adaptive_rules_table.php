<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the adaptive_rules table.
     * The adaptive engine is fully implemented as hardcoded PHP rule classes
     * in app/Rules/Adaptive/ and does not use this DB-driven table.
     */
    public function up(): void
    {
        Schema::dropIfExists('adaptive_rules');
    }

    public function down(): void
    {
        Schema::create('adaptive_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('material_id')->nullable()->constrained()->onDelete('cascade');
            $table->json('conditions')->nullable();
            $table->json('actions')->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }
};
