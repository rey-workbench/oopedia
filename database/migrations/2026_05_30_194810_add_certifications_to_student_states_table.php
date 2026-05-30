<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add column
        Schema::table('student_states', function (Blueprint $table) {
            $table->json('certifications')->nullable()->after('max_streak');
        });

        // 2. Data migration from adaptive_state to certifications
        DB::table('student_states')
            ->whereNotNull('adaptive_state')
            ->orderBy('id')
            ->chunk(100, function ($states) {
                foreach ($states as $state) {
                    $adaptiveState = json_decode($state->adaptive_state, true);
                    
                    if (isset($adaptiveState['certifications'])) {
                        $certs = $adaptiveState['certifications'];
                        unset($adaptiveState['certifications']);
                        
                        DB::table('student_states')
                            ->where('id', $state->id)
                            ->update([
                                'certifications' => json_encode($certs),
                                'adaptive_state' => json_encode($adaptiveState)
                            ]);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Data migration from certifications to adaptive_state
        DB::table('student_states')
            ->whereNotNull('certifications')
            ->orderBy('id')
            ->chunk(100, function ($states) {
                foreach ($states as $state) {
                    $adaptiveState = json_decode($state->adaptive_state, true) ?? [];
                    $certs = json_decode($state->certifications, true) ?? [];
                    
                    if (!empty($certs)) {
                        $adaptiveState['certifications'] = $certs;
                        
                        DB::table('student_states')
                            ->where('id', $state->id)
                            ->update([
                                'adaptive_state' => json_encode($adaptiveState)
                            ]);
                    }
                }
            });

        // Drop column
        Schema::table('student_states', function (Blueprint $table) {
            $table->dropColumn('certifications');
        });
    }
};
