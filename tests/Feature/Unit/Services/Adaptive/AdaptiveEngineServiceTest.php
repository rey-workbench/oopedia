<?php

namespace Tests\Feature\Unit\Services\Adaptive;

use App\Models\Role;
use App\Models\User;
use App\Rules\Adaptive\Constants\AdaptiveConstants as AC;
use App\Services\Adaptive\AdaptiveEngineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdaptiveEngineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AdaptiveEngineService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AdaptiveEngineService;
    }

    public function test_it_evaluates_rules_successfully(): void
    {
        // 1. Setup Role & User
        $role = Role::create(['role_name' => 'mahasiswa', 'description' => 'Student']);
        $user = User::create([
            'id'          => (string) Str::ulid(),
            'name'        => 'Test Student',
            'email'       => 'test@oopedia.com',
            'password'    => bcrypt('password'),
            'role_id'     => $role->id,
            'is_approved' => true,
        ]);

        Auth::login($user);

        // 2. Evaluation Context
        $facts        = [AC::FACT_SCORE_PERFECT, AC::FACT_DIFF_BEGINNER];
        $currentState = [
            'current_material_id' => 'mat-123',
            'target_difficulty'   => AC::DIFFICULTY_BEGINNER,
        ];
        $context = [
            'material_id' => 'mat-123',
            'is_correct'  => true,
        ];

        // 3. Run Service
        $result = $this->service->evaluate($facts, $currentState, $context);

        // 4. Assertions
        $this->assertIsArray($result);
        $this->assertArrayHasKey('triggered_rule', $result);
        $this->assertArrayHasKey('new_state', $result);
    }
}
