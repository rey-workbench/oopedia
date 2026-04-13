<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop media_description from media table.
     * The column is written on upload (auto-generated value) but never
     * read back by any controller, service, or frontend view.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('media_description');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->text('media_description')->nullable()->after('media_url');
        });
    }
};
