<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\Lms\AssessmentType;
use App\Contracts\Services\SusResultServiceInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class SusSurveyController extends Controller
{
    public function __construct(private readonly SusResultServiceInterface $susResultService)
    {
    }

    public function index(Request $request): Response
    {
        $type       = $request->query('type') ? AssessmentType::tryFrom($request->query('type')) : null;
        $results    = $this->susResultService->getAllResults($type);
        $types      = $this->susResultService->getDistinctAssessmentTypes();
        $metrics    = $this->susResultService->calculateGlobalMetrics($results);

        $type1    = $request->query('type1') ? AssessmentType::tryFrom($request->query('type1')) : $type;
        $type2    = $request->query('type2') ? AssessmentType::tryFrom($request->query('type2')) : null;
        $analysis = $this->susResultService->calculateStatisticalAnalysis($type1, $type2);

        return $this->render('Admin/Sus/Index', [
            'results'  => $results,
            'averages' => [
                'total' => $metrics['average_score'],
                'items' => $metrics['items'],
            ],
            'grading'  => [
                'score'         => $metrics['average_score'],
                'adjective'     => $metrics['adjective'],
                'grade'         => $metrics['grade'],
                'acceptability' => $metrics['acceptability'],
            ],
            'types'       => $types,
            'activeType'  => $type?->value ?? '',
            'analysis'    => $analysis,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $type    = $request->query('type') ? AssessmentType::tryFrom($request->query('type')) : null;
        $results = $this->susResultService->getAllResults($type);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sus-survey-results.csv"',
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
                    'Nama Pengguna',
                    'Email',
                    'Tipe Asesmen',
                    'Tanggal Pengisian',
                    'Q1', 'Q2', 'Q3', 'Q4', 'Q5', 'Q6', 'Q7', 'Q8', 'Q9', 'Q10',
                    'Total Score',
                    'Komentar',
                    'Saran',
                ],
                escape: '\\',
            );

            foreach ($results as $result) {
                fputcsv(
                    $file,
                    [
                        $result['id'],
                        $result['user']['name']  ?? 'Tidak ada',
                        $result['user']['email'] ?? 'Tidak ada',
                        $result['assessment_type'] ?? '',
                        $result['created_at'],
                        $result['q1'], $result['q2'], $result['q3'], $result['q4'], $result['q5'],
                        $result['q6'], $result['q7'], $result['q8'], $result['q9'], $result['q10'],
                        $result['total_score'],
                        $result['comments']    ?? '',
                        $result['suggestions'] ?? '',
                    ],
                    escape: '\\',
                );
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(string $resultId): Response
    {
        $result = $this->susResultService->getStudentDetail($resultId);

        if (! $result) {
            abort(404);
        }

        $user = $result['user'];

        $calculation = [
            'item_scores' => $this->susResultService->calculateItemScores($result),
            'total_score' => $result['total_score'],
        ];

        return $this->render('Admin/Sus/Detail/Index', ['result' => $result, 'user' => $user, 'calculation' => $calculation]);
    }
}
