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
            $table->string('id')->primary();
            $table->string('name');
            $table->string('recommendation')->nullable()->index();
            $table->integer('priority')->default(0);
            $table->json('actions')->nullable();
            $table->json('required_fact_ids');
            $table->json('deduced_fact_ids')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_rules');
    }
};
