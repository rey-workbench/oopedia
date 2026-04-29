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
            if (Schema::hasColumn('adaptive_rules', 'domain')) {
                $table->renameColumn('domain', 'recommendation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            if (Schema::hasColumn('adaptive_rules', 'recommendation')) {
                $table->renameColumn('recommendation', 'domain');
            }
        });
    }
};
