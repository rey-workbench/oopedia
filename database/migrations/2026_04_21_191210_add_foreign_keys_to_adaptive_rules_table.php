<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            // Relasi ke Kamus Gejala
            $table->string('fact_code')->nullable()->after('condition_fact');
            $table->foreign('fact_code')->references('code')->on('adaptive_facts')->onDelete('cascade');

            // Relasi ke Kamus Hasil
            $table->string('h_action_code')->nullable()->after('action_code');
            $table->foreign('h_action_code')->references('code')->on('adaptive_actions')->onDelete('cascade');

            // Kolom metadata tambahan
            $table->integer('weight')->default(1)->after('priority');
        });
    }

    public function down(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->dropForeign(['fact_code']);
            $table->dropForeign(['h_action_code']);
            $table->dropColumn(['fact_code', 'h_action_code', 'weight']);
        });
    }
};
