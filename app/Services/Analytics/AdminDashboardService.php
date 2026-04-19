<?php

declare(strict_types=1);

namespace App\Services\Analytics;

use App\Contracts\Repositories\MaterialRepositoryInterface;
use App\Contracts\Repositories\ProgressRepositoryInterface;
use App\Contracts\Repositories\QuestionRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\Services\AdminDashboardServiceInterface;
use App\Helpers\ProgressHelper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class AdminDashboardService implements AdminDashboardServiceInterface
{
    public function __construct(
        public readonly UserRepositoryInterface $userRepo,
        public readonly MaterialRepositoryInterface $materialRepo,
        public readonly ProgressRepositoryInterface $progressRepo,
        public readonly QuestionRepositoryInterface $questionRepo,
    ) {}

    /** @return array<string, int> */
    public function getDashboardStats(): array
    {
        return Cache::remember('admin_dashboard_stats', 600, function () {
            return [
                'totalStudents'  => $this->userRepo->countByRole('mahasiswa'),
                'totalMaterials' => $this->materialRepo->countAll(),
                'totalQuestions' => $this->questionRepo->countAll(),
                'activeStudents' => $this->userRepo->getActiveStudentsCount(7),
            ];
        });
    }

    public function getRecentProgress(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->progressRepo->getRecentSystemProgress($limit);
    }

    /** @return array<int, array<string, mixed>> */
    public function getStudentProgressOverview(int $limit = 5): array
    {
        return Cache::remember("admin_student_progress_overview_{$limit}", 600, function () use ($limit) {
            $students                 = $this->userRepo->getStudentProgressOverview($limit);
            $materials                = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $totalConfiguredQuestions = ProgressHelper::calculateTotalQuestions($materials);

            return $students->map(function ($student) use ($totalConfiguredQuestions) {
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
                $student->last_active = $lastActivity ? Carbon::parse($lastActivity) : null;

                return $student;
            })->all();
        });
    }

    public function getMaterialStatistics(): Collection
    {
        return Cache::remember('admin_material_statistics', 600, function () {
            $materials    = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $progressData = $this->progressRepo->getMaterialPerformanceStats();

            return $materials->map(function ($material) use ($progressData) {
                $totalConfiguredQuestions   = $material->questions->count();
                $materialProgress           = $progressData->where('material_id', $material->id);
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
        return Cache::remember('admin_student_analytics', 600, function () {
            $allStudents              = $this->userRepo->getUsersByRoleAndApproval('mahasiswa', true, null, null);
            $materials                = $this->materialRepo->getAllWithQuestionsAndConfigs();
            $totalConfiguredQuestions = ProgressHelper::calculateTotalQuestions($materials);

            $distribution = [
                '0%'      => 0,
                '1-25%'   => 0,
                '26-50%'  => 0,
                '51-75%'  => 0,
                '76-100%' => 0,
            ];

            foreach ($allStudents as $student) {
                $correctCount = $student->quizAttempts
                    ->where('is_correct', true)
                    ->pluck('question_id')
                    ->unique()
                    ->count();

                $progress = ProgressHelper::calculateProgressPercentage(
                    $correctCount,
                    $totalConfiguredQuestions,
                );

                if ($progress == 0) {
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

            $moduleStats = $this->getMaterialStatistics();

            return [
                'distribution'      => $distribution,
                'modulePerformance' => [
                    'labels' => $moduleStats->pluck('title'),
                    'data'   => $moduleStats->pluck('completion_rate'),
                ],
            ];
        });
    }
}
