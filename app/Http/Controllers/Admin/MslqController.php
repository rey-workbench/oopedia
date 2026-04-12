<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\Lms\MslqServiceInterface;
use App\Enums\Lms\MslqScale;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MslqController extends Controller
{
    public function __construct(
        protected MslqServiceInterface $mslqService,
    ) {}

    public function index(Request $request): Response
    {
        $class    = $request->query('class');
        $results  = $this->mslqService->getAdminResults($class);
        $classes  = $this->mslqService->getDistinctClasses();
        $averages = $this->mslqService->calculateGlobalAverages($class);

        return $this->render('Admin/Mslq/Index', [
            'results'     => $results,
            'classes'     => $classes,
            'activeClass' => $class ?? '',
            'averages'    => $averages,
        ]);
    }

    public function show(string $id): Response
    {
        $result = $this->mslqService->getResultDetail($id);

        return $this->render('Admin/Mslq/Detail/Index', [
            'result' => $result,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $class   = $request->input('class');
        $results = $this->mslqService->getResultsForExport($class);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mslq-results.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($results): void {
            $file = fopen('php://output', 'w');

            // Header for CSV
            fputcsv($file, [
                'ID',
                'NIM',
                'Nama Mahasiswa',
                'Email',
                'Kelas',
                'Tanggal Pengisian',
                'Total Motivasi',
                'Total Strategi',
                'Intrinsic Value',
                'Extrinsic Value',
                'Task Value',
                'Control Beliefs',
                'Self-Efficacy',
                'Test Anxiety',
                'Rehearsal',
                'Elaboration',
                'Organization',
                'Critical Thinking',
                'Metacognitive Self-Regulation',
                'Time and Study Environment',
                'Effort Regulation',
                'Peer Learning',
                'Help Seeking',
            ]);

            foreach ($results as $result) {
                $scores = $result->scores_by_scale;
                fputcsv($file, [
                    $result->id,
                    $result->nim,
                    $result->user?->name  ?? 'Tidak ada',
                    $result->user?->email ?? 'Tidak ada',
                    $result->class,
                    $result->created_at->format('d/m/Y H:i'),
                    $result->total_motivation,
                    $result->total_strategy,
                    $scores[MslqScale::INTRINSIC_GOAL_ORIENTATION->value]             ?? 0,
                    $scores[MslqScale::EXTRINSIC_GOAL_ORIENTATION->value]             ?? 0,
                    $scores[MslqScale::TASK_VALUE->value]                             ?? 0,
                    $scores[MslqScale::CONTROL_OF_LEARNING_BELIEFS->value]            ?? 0,
                    $scores[MslqScale::SELF_EFFICACY_FOR_LEARNING_PERFORMANCE->value] ?? 0,
                    $scores[MslqScale::TEST_ANXIETY->value]                           ?? 0,
                    $scores[MslqScale::REHEARSAL->value]                              ?? 0,
                    $scores[MslqScale::ELABORATION->value]                            ?? 0,
                    $scores[MslqScale::ORGANIZATION->value]                           ?? 0,
                    $scores[MslqScale::CRITICAL_THINKING->value]                      ?? 0,
                    $scores[MslqScale::METACOGNITIVE_SELF_REGULATION->value]          ?? 0,
                    $scores[MslqScale::TIME_STUDY_ENVIRONMENT_MANAGEMENT->value]      ?? 0,
                    $scores[MslqScale::EFFORT_REGULATION->value]                      ?? 0,
                    $scores[MslqScale::PEER_LEARNING->value]                          ?? 0,
                    $scores[MslqScale::HELP_SEEKING->value]                           ?? 0,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
