<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('adaptive_rule_actions');
        Schema::dropIfExists('adaptive_rule_conditions');
        Schema::dropIfExists('adaptive_rules');

        Schema::create('adaptive_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('domain')->nullable()->index();
            $table->integer('priority')->default(0);
            $table->json('required_facts');
            $table->json('deduced_facts')->nullable();
            $table->json('action_codes')->nullable();
            $table->foreignId('action_id')->nullable()->constrained('adaptive_actions')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_rules');
    }
};
