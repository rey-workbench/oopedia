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
        Schema::table('progress', function (Blueprint $table) {
            $table->integer('xp_earned')->default(0)->after('attempt_number');
            $table->integer('points_earned')->default(0)->after('xp_earned');
            $table->boolean('used_hint')->default(false)->after('points_earned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropColumn(['xp_earned', 'points_earned', 'used_hint']);
        });
    }
};
