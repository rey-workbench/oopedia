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
        Schema::table('attribute_definitions', function (Blueprint $table) {
            $table->boolean('is_computed')->default(false)->after('type');
            $table->foreignId('formula_id')->nullable()->constrained()->onDelete('set null')->after('is_computed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attribute_definitions', function (Blueprint $table) {
            $table->dropForeign(['formula_id']);
            $table->dropColumn(['is_computed', 'formula_id']);
        });
    }
};
