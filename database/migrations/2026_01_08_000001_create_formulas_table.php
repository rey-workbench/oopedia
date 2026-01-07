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
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // "Calculate Accuracy"
            $table->string('key')->unique(); // "accuracy"
            $table->text('description')->nullable();
            $table->text('expression'); // "(correct_count / total_count) * 100"
            $table->json('dependencies')->nullable(); // ["correct_count", "total_count"]
            $table->string('return_type')->default('float'); // integer, float, string, boolean
            $table->string('scope')->default('material'); // material, global, session
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formulas');
    }
};
