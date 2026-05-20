<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Contracts\Services\MslqServiceInterface;
use App\Enums\Lms\AssessmentType;
use App\Enums\Lms\MslqScale;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class MslqController extends Controller
{
    public function __construct(
        private readonly MslqServiceInterface $mslqService,
    ) {
    }

    public function index(Request $request): Response
    {
        $type = $request->query('type') ? AssessmentType::tryFrom($request->query('type')) : null;

        $lengthAwarePaginator     = $this->mslqService->getAdminResults($type);
        $distinctTypes            = $this->mslqService->getDistinctAssessmentTypes();
        $metricsData              = $this->mslqService->calculateGlobalMetrics($type);

        $type1      = $request->query('type1') ? AssessmentType::tryFrom($request->query('type1')) : $type;
        $type2      = $request->query('type2') ? AssessmentType::tryFrom($request->query('type2')) : null;
        $analysis    = $this->mslqService->calculateStatisticalAnalysis($type1, $type2);

        return $this->render('Admin/Mslq/Index', [
            'results'     => $lengthAwarePaginator,
            'types'       => $distinctTypes,
            'activeType'  => $type?->value ?? '',
            'metrics'     => array_merge($metricsData, [
                'total_responses' => $lengthAwarePaginator->total(),
            ]),
            'analysis'    => $analysis,
        ]);
    }

    public function show(string $id): Response
    {
        $mslqResult = $this->mslqService->getResultDetail($id);

        return $this->render('Admin/Mslq/Detail/Index', [
            'result' => $mslqResult,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $type    = $request->input('type') ? AssessmentType::tryFrom($request->input('type')) : null;
        $results = $this->mslqService->getResultsForExport($type);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="mslq-results.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($results): void {
            $file = fopen('php://output', 'w');

            fputcsv(
                $file,
                [
                    'ID',
                    'Nama Mahasiswa',
                    'Email',
                    'Tipe Asesmen',
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
                ],
                escape: '\\',
            );

            foreach ($results as $result) {
                $scores = $result['scores_by_scale'];
                fputcsv(
                    $file,
                    [
                        $result['id'],
                        $result['user']['name']  ?? 'Tidak ada',
                        $result['user']['email'] ?? 'Tidak ada',
                        $result['assessment_type'],
                        $result['created_at'],
                        $result['total_motivation'],
                        $result['total_strategy'],
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
                    ],
                    escape: '\\',
                );
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
