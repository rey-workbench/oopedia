<?php

use App\Enums\Lms\MediaType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('material_id');
            $table->enum('media_type', array_map(fn ($case) => $case->value, MediaType::cases()));
            $table->string('media_url');
            $table->text('media_description')->nullable();
            $table->timestamps();

            $table->foreign('material_id')->references('id')->on('materials')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
}
