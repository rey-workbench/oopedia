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
        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->json('conditions')->nullable()->after('material_id');
            
            // Make old columns nullable
            $table->string('condition_type')->nullable()->change();
            $table->string('condition_operator')->nullable()->change();
            $table->string('condition_value')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->dropColumn('conditions');
            
            // Revert nullable changes (warning: this might fail if null values exist)
            $table->string('condition_type')->nullable(false)->change();
            $table->string('condition_operator')->nullable(false)->change();
            $table->string('condition_value')->nullable(false)->change();
        });
    }
};
