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
        Schema::create('mslq_results', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->onDelete('cascade');
            $table->string('assessment_type')->default('post');
            $table->string('nim')->nullable();
            $table->string('class')->nullable();
            $table->json('scores_by_scale'); // { "intrinsic_goal_orientation": 5.4, ... }
            $table->float('total_motivation');
            $table->float('total_strategy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mslq_results');
    }
};
