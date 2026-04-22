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
        Schema::table('adaptive_actions', function (Blueprint $table) {
            $table->string('variant')->nullable()->after('description'); // result, acceleration, certificate, intervention, backtrack
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adaptive_actions', function (Blueprint $table) {
            $table->dropColumn('variant');
        });
    }
};
