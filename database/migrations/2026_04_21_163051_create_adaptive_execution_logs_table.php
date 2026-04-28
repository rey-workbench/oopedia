<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adaptive_execution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->string('code')->index();
            $table->string('action_code')->nullable()->index();
            $table->json('trigger_facts')->nullable();
            $table->json('state_deltas')->nullable();
            $table->json('new_state')->nullable();
            $table->json('execution_context')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_execution_logs');
    }
};
