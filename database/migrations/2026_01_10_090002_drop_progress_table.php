<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('progress');
    }

    public function down(): void
    {
        // Recreating the table in down() is complex due to dependent foreign keys usually.
        // But for completeness, we provide the basic schema.
        Schema::create('progress', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->bigInteger('material_id')->unsigned();
            $table->bigInteger('question_id')->unsigned();
            $table->boolean('is_answered')->default(false);
            $table->boolean('is_correct')->default(false);
            $table->bigInteger('answer_id')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamps();
        });
    }
};
