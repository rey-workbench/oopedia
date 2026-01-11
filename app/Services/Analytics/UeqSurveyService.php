<?php

namespace App\Services\Analytics;

use App\Repositories\UeqSurveyRepository;

class UeqSurveyService
{
    protected $ueqRepo;

    public function __construct(UeqSurveyRepository $ueqRepo)
    {
        $this->ueqRepo = $ueqRepo;
    }

    public function getAllSurveys($class = null)
    {
        return $this->ueqRepo->getAllWithUser($class);
    }

    public function getDistinctClasses()
    {
        return $this->ueqRepo->getDistinctClasses();
    }

    public function getStudentDetail($userId)
    {
        return $this->ueqRepo->findByUserId($userId);
    }

    public function hasUserSubmitted($userId)
    {
        return $this->ueqRepo->findSurveyByUser($userId) !== null;
    }

    public function createSurvey(array $data)
    {
        return $this->ueqRepo->create($data);
    }

    public function calculateAverages($surveys)
    {
        if ($surveys->isEmpty()) {
            return [];
        }
        
        // Inisialisasi array untuk menyimpan total nilai
        $totals = [
            'attractiveness' => 0,
            'perspicuity' => 0,
            'efficiency' => 0,
            'dependability' => 0,
            'stimulation' => 0,
            'novelty' => 0
        ];
        
        foreach ($surveys as $survey) {
            // Attractiveness
            $totals['attractiveness'] += (
                $survey->annoying_enjoyable + 
                $survey->good_bad + 
                $survey->unlikable_pleasing + 
                $survey->unpleasant_pleasant + 
                $survey->attractive_unattractive + 
                $survey->friendly_unfriendly
            ) / 6;
            
            // Perspicuity
            $totals['perspicuity'] += (
                $survey->not_understandable_understandable + 
                $survey->easy_difficult + 
                $survey->complicated_easy + 
                $survey->clear_confusing
            ) / 4;
            
            // Efficiency
            $totals['efficiency'] += (
                $survey->fast_slow + 
                $survey->inefficient_efficient + 
                $survey->impractical_practical + 
                $survey->organized_cluttered
            ) / 4;
            
            // Dependability
            $totals['dependability'] += (
                $survey->unpredictable_predictable + 
                $survey->obstructive_supportive + 
                $survey->secure_not_secure + 
                $survey->meets_expectations_does_not_meet
            ) / 4;
            
            // Stimulation
            $totals['stimulation'] += (
                $survey->valuable_inferior + 
                $survey->boring_exciting + 
                $survey->not_interesting_interesting + 
                $survey->motivating_demotivating
            ) / 4;
            
            // Novelty
            $totals['novelty'] += (
                $survey->creative_dull + 
                $survey->inventive_conventional + 
                $survey->usual_leading_edge + 
                $survey->conservative_innovative
            ) / 4;
        }
        
        // Hitung rata-rata
        $count = $surveys->count();
        $averages = [];
        
        foreach ($totals as $key => $total) {
            $averages[$key] = $total / $count;
        }
        
        return $averages;
    }
}
