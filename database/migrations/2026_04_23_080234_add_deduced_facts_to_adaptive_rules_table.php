<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add deduced_facts column and remove deprecated forbidden_facts.
 * This completes the Pure Forward Chaining (Detective Model) migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            // Virtual facts yang dihasilkan oleh rule ini untuk chaining inference
            $table->json('deduced_facts')->nullable()->after('required_facts');

            // Hapus forbidden_facts – pure positive logic model
            if (Schema::hasColumn('adaptive_rules', 'forbidden_facts')) {
                $table->dropColumn('forbidden_facts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('adaptive_rules', function (Blueprint $table) {
            $table->dropColumn('deduced_facts');
            $table->json('forbidden_facts')->nullable()->after('required_facts');
        });
    }
};
