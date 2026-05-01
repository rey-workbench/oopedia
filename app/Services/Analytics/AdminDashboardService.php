<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Helpers\ProgressHelper;
use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;

final readonly class AdminDashboardService implements AdminDashboardServiceInterface
{
    public function __construct(
        public UserRepositoryInterface $userRepo,
        public MaterialRepositoryInterface $materialRepo,
        public ProgressRepositoryInterface $progressRepo,
        public QuestionRepositoryInterface $questionRepo,
    ) {}

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

    public function getRecentProgress(int $limit = 10): EloquentCollection
    {
        return $this->progressRepo->getRecentSystemProgress($limit);
    }

    /** @return array<int, array<string, mixed>> */
    public function getStudentProgressOverview(int $limit = 5): array
    {
        return Cache::remember('admin_student_progress_overview_' . $limit, 600, function () use ($limit) {
            $students                                  = $this->userRepo->getStudentProgressOverview($limit);
            $allWithQuestionsAndConfigs                = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $totalConfiguredQuestions                  = ProgressHelper::calculateTotalQuestions($allWithQuestionsAndConfigs);

            return $students->map(function ($student) use ($totalConfiguredQuestions): Model {
                $uniqueCorrectQuestions = $student->quizAttempts
                    ->where('is_correct', true)
                    ->pluck('question_id')
                    ->unique()
                    ->count();

                $student->materials_progress = ProgressHelper::calculateProgressPercentage(
                    $uniqueCorrectQuestions,
                    $totalConfiguredQuestions,
                );

                $lastActivity         = $student->quizAttempts->max('created_at');
                $student->last_active = $lastActivity ? Date::parse($lastActivity) : null;

                return $student;
            })->all();
        });
    }

    public function getMaterialStatistics(): Collection
    {
        return Cache::remember('admin_material_statistics', 600, function () {
            $allWithQuestionsAndConfigs    = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $materialPerformanceStats      = $this->progressRepo->getMaterialPerformanceStats();

            return $allWithQuestionsAndConfigs->map(function ($material) use ($materialPerformanceStats) {
                $totalConfiguredQuestions   = $material->questions->count();
                $materialProgress           = $materialPerformanceStats->where('material_id', $material->id);
                $activeStudents             = $materialProgress->pluck('user_id')->unique()->count();
                $correctlyAnsweredQuestions = $materialProgress->pluck('question_id')->unique()->count();

                $completionRate = $totalConfiguredQuestions > 0
                    ? round(($correctlyAnsweredQuestions / $totalConfiguredQuestions) * 100, 1)
                    : 0;

                return (object) [
                    'id'              => $material->id,
                    'title'           => $material->title,
                    'questions_count' => $totalConfiguredQuestions,
                    'active_students' => $activeStudents,
                    'completion_rate' => $completionRate,
                ];
            });
        });
    }

    public function getPopularMaterials(int $limit = 5): Collection
    {
        return $this->progressRepo->getPopularMaterials($limit);
    }

    /** @return array<string, mixed> */
    public function getStudentAnalytics(): array
    {
        return Cache::remember('admin_student_analytics', 600, function (): array {
            $allStudents                               = $this->userRepo->getUsersByRoleAndApproval('mahasiswa', true, null, null);
            $allWithQuestionsAndConfigs                = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $totalConfiguredQuestions                  = ProgressHelper::calculateTotalQuestions($allWithQuestionsAndConfigs);

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

    public function getStudentsNeedingAttention(): EloquentCollection
    {
        return User::whereHas('role', function (Builder $q): void {
            $q->where('role_name', 'mahasiswa');
        })
            ->whereHas('studentState', function ($q): void {
                $q->whereJsonContains('adaptive_state->notify_teacher', true);
            })
            ->with(['studentState'])
            ->get();
    }
}
