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
        Schema::table('ueq_surveys', function (Blueprint $table) {
            $table->string('assessment_type')->default('pre')->after('user_id');
            $table->dropColumn(['nim', 'class']);
        });
    }

    public function down(): void
    {
        Schema::table('ueq_surveys', function (Blueprint $table) {
            $table->dropColumn('assessment_type');
            $table->string('nim')->nullable();
            $table->string('class')->nullable();
        });
    }
};
