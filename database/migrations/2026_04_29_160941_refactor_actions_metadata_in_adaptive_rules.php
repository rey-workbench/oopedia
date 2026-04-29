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
            $table->renameColumn('action_ids', 'actions');
            $table->dropColumn('metadata');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->renameColumn('actions', 'action_ids');
            $table->json('metadata')->nullable();
        });
    }
};
