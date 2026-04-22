<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Redesign adaptive_rules as a flat forward-chaining table.
 * Drop all tree/orchestrator structure. Each row = one rule.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('adaptive_rules');

        Schema::create('adaptive_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code')->unique();          // R01, R02 … R15, RE1, RE2
            $table->string('name');                         // Human readable name
            $table->integer('priority')->default(50);       // Lower = higher priority
            $table->json('required_facts');                 // G-codes yang HARUS ada: ["G18","G22","G07"]
            $table->json('forbidden_facts')->nullable();    // G-codes yang TIDAK boleh ada (optional)
            $table->unsignedBigInteger('action_id');        // FK → adaptive_actions.id
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('action_id')->references('id')->on('adaptive_actions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_rules');
    }
};
