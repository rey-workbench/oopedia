<?php

use App\Enums\Lms\MslqCategory;
use App\Enums\Lms\MslqScale;
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
        Schema::create('mslq_questions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('text');
            $table->enum('category', array_map(fn ($case) => $case->value, MslqCategory::cases()));
            $table->enum('scale', array_map(fn ($case) => $case->value, MslqScale::cases()));
            $table->boolean('is_reverse')->default(false);
            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mslq_questions');
    }
};
