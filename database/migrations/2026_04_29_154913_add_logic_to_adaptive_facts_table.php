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
        Schema::table('adaptive_facts', function (Blueprint $table) {
            $table->renameColumn('description', 'logic');
        });
    }

    public function down(): void
    {
        Schema::table('adaptive_facts', function (Blueprint $table) {
            $table->renameColumn('logic', 'description');
        });
    }
};
