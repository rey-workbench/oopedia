<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop old tables
        Schema::dropIfExists('adaptive_rule_actions');
        Schema::dropIfExists('adaptive_rule_conditions');
        Schema::dropIfExists('adaptive_rules');

        // 2. Create single unified tree table
        Schema::create('adaptive_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('adaptive_rules')->nullOnDelete();
            $table->string('rule_code')->nullable(); // For flat IDs like RULE_01
            $table->string('name');
            $table->string('node_type')->default('branch'); // branch, leaf

            // Condition (Unified)
            $table->string('condition_fact')->nullable(); // e.g. G01
            $table->string('condition_value')->nullable(); // e.g. true

            // Action (For Leaf Nodes)
            $table->string('action_code')->nullable(); // e.g. H01
            $table->json('action_params')->nullable(); // e.g. {"difficulty": "hard"}

            $table->integer('priority')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_rules');
    }
};
