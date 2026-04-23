<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adaptive_execution_logs', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->char('user_id', 26)->index();
            $blueprint->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $blueprint->string('rule_code')->nullable()->index();
            $blueprint->string('action_code')->nullable()->index();
            $blueprint->json('facts');
            $blueprint->json('previous_state');
            $blueprint->json('new_state');
            $blueprint->json('context')->nullable();
            $blueprint->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_execution_logs');
    }
};
