<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adaptive_actions', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // H01, H02...
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('variant')->nullable(); // result, acceleration, certificate, intervention, backtrack
            $table->json('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adaptive_actions');
    }
};
