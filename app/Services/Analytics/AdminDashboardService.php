<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Helpers\ProgressHelper;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\RecentProgressResource;
use App\Http\Resources\StudentProgressResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

final readonly class AdminDashboardService implements AdminDashboardServiceInterface
{
    public function __construct(
        public UserRepositoryInterface $userRepo,
        public MaterialRepositoryInterface $materialRepo,
        public ProgressRepositoryInterface $progressRepo,
        public QuestionRepositoryInterface $questionRepo,
    ) {
    }

    /** @return array<string, int> */
    public function getDashboardStats(): array
    {
        return Cache::remember('admin_dashboard_stats', 600, fn (): array => [
            'total_students'  => $this->userRepo->countByRole('mahasiswa'),
            'total_materials' => $this->materialRepo->countAll(),
            'total_questions' => $this->questionRepo->countAll(),
            'active_students' => $this->userRepo->getActiveStudentsCount(7),
        ]);
    }

    /** @return SupportCollection<int, array{user_name: string, material_name: string, action_name: string, created_at: string, created_at_diff: string}> */
    public function getRecentProgress(int $limit = 10): SupportCollection
    {
        return collect(RecentProgressResource::collection(
            $this->progressRepo->getRecentSystemProgress($limit),
        )->resolve());
    }

    public function getStudentProgressOverview(int $limit = 5): SupportCollection
    {
        return Cache::remember('admin_student_progress_overview_' . $limit, 600, function () use ($limit): SupportCollection {
            $students                   = $this->userRepo->getStudentProgressOverview($limit);
            $allWithQuestionsAndConfigs = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $totalConfiguredQuestions   = ProgressHelper::calculateTotalQuestions($allWithQuestionsAndConfigs);

            return collect(StudentProgressResource::collection($students)->additional([
                'totalConfiguredQuestions' => $totalConfiguredQuestions,
            ])->resolve());
        });
    }

    public function getMaterialStatistics(): SupportCollection
    {
        return Cache::remember('admin_material_statistics', 600, function (): SupportCollection {
            $allWithQuestionsAndConfigs = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $materialPerformanceStats   = $this->progressRepo->getMaterialPerformanceStats();

            $materials = $allWithQuestionsAndConfigs->map(function ($material) use ($materialPerformanceStats): Model {
                $totalConfiguredQuestions   = $material->questions->count();
                $materialProgress           = $materialPerformanceStats->where('material_id', $material->id);
                $activeStudents             = $materialProgress->pluck('user_id')->unique()->count();
                $correctlyAnsweredQuestions = $materialProgress->pluck('question_id')->unique()->count();

                $material->completion_rate = $totalConfiguredQuestions > 0
                    ? round(($correctlyAnsweredQuestions / $totalConfiguredQuestions) * 100, 1)
                    : 0;

                $material->active_students = $activeStudents;
                $material->questions_count = $totalConfiguredQuestions;

                return $material;
            });

            return collect(MaterialResource::collection($materials)->resolve());
        });
    }

    public function getPopularMaterials(int $limit = 5): SupportCollection
    {
        $materials = $this->progressRepo->getPopularMaterials($limit);

        return collect(MaterialResource::collection($materials)->resolve());
    }

    /** @return array<string, mixed> */
    public function getStudentAnalytics(): array
    {
        return Cache::remember('admin_student_analytics', 600, function (): array {
            $allStudents                = $this->userRepo->getUsersByRoleAndApproval('mahasiswa', true, null, null);
            $allWithQuestionsAndConfigs = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $totalConfiguredQuestions   = ProgressHelper::calculateTotalQuestions($allWithQuestionsAndConfigs);

            $distribution = [
                '0%'      => 0,
                '1-25%'   => 0,
                '26-50%'  => 0,
                '51-75%'  => 0,
                '76-100%' => 0,
            ];

            foreach ($allStudents as $allStudent) {
                $correctCount = $allStudent->quizAttempts
                    ->where('is_correct', true)
                    ->pluck('question_id')
                    ->unique()
                    ->count();

                $progress = ProgressHelper::calculateProgressPercentage(
                    $correctCount,
                    $totalConfiguredQuestions,
                );

                if ($progress === 0) {
                    $distribution['0%']++;
                } elseif ($progress <= 25) {
                    $distribution['1-25%']++;
                } elseif ($progress <= 50) {
                    $distribution['26-50%']++;
                } elseif ($progress <= 75) {
                    $distribution['51-75%']++;
                } else {
                    $distribution['76-100%']++;
                }
            }

            $materialStatistics = $this->getMaterialStatistics();

            return [
                'distribution'       => $distribution,
                'module_performance' => [
                    'labels' => $materialStatistics->pluck('title'),
                    'data'   => $materialStatistics->pluck('completion_rate'),
                ],
            ];
        });
    }

    public function getStudentsNeedingAttention(): SupportCollection
    {
        $students = User::whereHas('role', function (Builder $q): void {
            $q->where('role_name', 'mahasiswa');
        })
            ->whereHas('studentState', function ($q): void {
                $q->whereJsonContains('adaptive_state->notify_teacher', true);
            })
            ->with(['role', 'studentState'])
            ->get();

        return $students->map(fn ($student): array => [
            'id'              => $student->id,
            'user'            => new UserResource($student)->resolve(),
            'low_score_count' => 0,
            'last_activity'   => $student->studentState?->last_active_at?->toIso8601String() ?? '-',
            'student_state'   => [
                'adaptive_state' => $student->studentState?->adaptive_state ?? [],
            ],
        ]);
    }
}
