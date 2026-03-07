<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('material_id');
            $table->text('question_text');
            $table->enum('question_type', ['radio_button', 'drag_and_drop', 'fill_in_the_blank']);
            $table->enum('type', ['teori', 'sintaks', 'mixed'])->default('teori');
            $table->enum('difficulty', ['beginner', 'medium', 'hard'])->default('beginner');
            $table->text('hint')->nullable();
            $table->ulid('created_by');
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
