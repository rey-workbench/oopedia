<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UeqSurveyExport;
use Inertia\Inertia;

class UeqSurveyController extends Controller
{
    public function __construct(
        protected \App\Services\Analytics\UeqSurveyService $ueqService,
        protected \App\Contracts\Services\MaterialServiceInterface $materialService
    ) {}

    public function index(Request $request)
    {
        $class = $request->input('class');
        
        $surveys = $this->ueqService->getAllSurveys($class);
        
        $classes = $this->ueqService->getDistinctClasses();
        
        // Hitung rata-rata untuk setiap dimensi UEQ
        $averages = $this->ueqService->calculateAverages($surveys);
        
        // Untuk sidebar materials dropdown
        $materials = $this->materialService->getAllMaterials();
        
        return Inertia::render('Admin/Ueq/Index', [
            'surveys' => $surveys,
            'averages' => $averages,
            'classes' => $classes,
            'activeClass' => $class
        ]);
    }

    /**
     * Export UEQ Survey results filtered by class
     */
    public function export(Request $request)
    {
        $class = $request->input('class');
        
        // Query data
        $surveys = $this->ueqService->getAllSurveys($class);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ueq-survey-results.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];
        
        $callback = function() use ($surveys) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID', 'NIM', 'Nama Pengguna', 'Email', 'Kelas', 'Tanggal Pengisian',
                // 26 aspek UEQ
                'Annoying - Enjoyable',
                'Not Understandable - Understandable',
                'Creative - Dull',
                'Easy - Difficult',
                'Valuable - Inferior',
                'Boring - Exciting',
                'Not Interesting - Interesting',
                'Unpredictable - Predictable',
                'Fast - Slow',
                'Inventive - Conventional',
                'Obstructive - Supportive',
                'Good - Bad',
                'Complicated - Easy',
                'Unlikable - Pleasing',
                'Usual - Leading Edge',
                'Unpleasant - Pleasant',
                'Secure - Not Secure',
                'Motivating - Demotivating',
                'Meets Expectations - Does Not Meet',
                'Inefficient - Efficient',
                'Clear - Confusing',
                'Impractical - Practical',
                'Organized - Cluttered',
                'Attractive - Unattractive',
                'Friendly - Unfriendly',
                'Conservative - Innovative',
                'Komentar', 'Saran'
            ]);
            
            // Add data rows
            foreach ($surveys as $survey) {
                fputcsv($file, [
                    $survey->id,
                    $survey->nim ?? '',
                    optional($survey->user)->name ?? 'Tidak ada',
                    optional($survey->user)->email ?? 'Tidak ada',
                    $survey->class ?? '',
                    $survey->created_at->format('d/m/Y H:i'),
                    // 26 aspek UEQ
                    $survey->annoying_enjoyable,
                    $survey->not_understandable_understandable,
                    $survey->creative_dull,
                    $survey->easy_difficult,
                    $survey->valuable_inferior,
                    $survey->boring_exciting,
                    $survey->not_interesting_interesting,
                    $survey->unpredictable_predictable,
                    $survey->fast_slow,
                    $survey->inventive_conventional,
                    $survey->obstructive_supportive,
                    $survey->good_bad,
                    $survey->complicated_easy,
                    $survey->unlikable_pleasing,
                    $survey->usual_leading_edge,
                    $survey->unpleasant_pleasant,
                    $survey->secure_not_secure,
                    $survey->motivating_demotivating,
                    $survey->meets_expectations_does_not_meet,
                    $survey->inefficient_efficient,
                    $survey->clear_confusing,
                    $survey->impractical_practical,
                    $survey->organized_cluttered,
                    $survey->attractive_unattractive,
                    $survey->friendly_unfriendly,
                    $survey->conservative_innovative,
                    $survey->comments ?? '',
                    $survey->suggestions ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return Response::stream($callback, 200, $headers);
    }

    public function detail($userId)
    {
        $survey = $this->ueqService->getStudentDetail($userId);
        $user = $survey->user;

        return Inertia::render('Admin/Ueq/Detail/Index', compact('survey', 'user'));
    }
} 