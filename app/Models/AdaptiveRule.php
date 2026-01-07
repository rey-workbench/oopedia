<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdaptiveRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'material_id',
        'conditions', // New JSON column
        'actions',    // New JSON column for multiple actions
        'action_type',
        'action_value',
        'priority',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer',
        'conditions' => 'array',
        'actions' => 'array'
    ];

    // Relasi
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Konstanta untuk tipe kondisi
    const CONDITION_TYPES = [
        'score_range' => 'Rentang Skor (%)',
        'consecutive_correct' => 'Jawaban Benar Berturut-turut',
        'consecutive_wrong' => 'Jawaban Salah Berturut-turut',
        'time_spent' => 'Waktu yang Dihabiskan (detik)',
        'accuracy_rate' => 'Tingkat Akurasi (%)'
    ];

    // Konstanta untuk operator
    const OPERATORS = [
        '>' => 'Lebih dari',
        '<' => 'Kurang dari',
        '>=' => 'Lebih dari atau sama dengan',
        '<=' => 'Kurang dari atau sama dengan',
        '==' => 'Sama dengan',
        'between' => 'Antara'
    ];

    // Konstanta untuk tipe aksi
    const ACTION_TYPES = [
        'change_difficulty' => 'Ubah Tingkat Kesulitan',
        'show_hint' => 'Tampilkan Petunjuk',
        'skip_questions' => 'Lewati Beberapa Soal',
        'recommend_material' => 'Rekomendasikan Materi',
        'end_quiz' => 'Akhiri Kuis'
    ];

    // Konstanta untuk nilai aksi kesulitan
    const DIFFICULTY_LEVELS = [
        'beginner' => 'Beginner',
        'medium' => 'Medium',
        'hard' => 'Hard'
    ];

    // Method untuk evaluasi kondisi
    public function evaluateCondition($userStats)
    {
        $value = $userStats[$this->condition_type] ?? 0;
        
        switch ($this->condition_operator) {
            case '>':
                return $value > floatval($this->condition_value);
            case '<':
                return $value < floatval($this->condition_value);
            case '>=':
                return $value >= floatval($this->condition_value);
            case '<=':
                return $value <= floatval($this->condition_value);
            case '==':
                return $value == floatval($this->condition_value);
            case 'between':
                $range = explode('-', $this->condition_value);
                return $value >= floatval($range[0]) && $value <= floatval($range[1]);
            default:
                return false;
        }
    }

    /**
     * Get formatted actions (supports both new and legacy format)
     */
    public function getFormattedActions()
    {
        // If new format exists, use it
        if (!empty($this->actions)) {
            return $this->actions;
        }
        
        // Convert legacy format to new format
        return $this->convertLegacyAction();
    }

    /**
     * Convert legacy action format to new actions array
     */
    private function convertLegacyAction()
    {
        $actions = [];
        
        if ($this->action_type === 'change_difficulty') {
            $actions[] = [
                'type' => 'update_attribute',
                'key' => 'current_level',
                'operator' => '=',
                'value' => $this->action_value
            ];
            
            // Reset counters on level change
            $actions[] = ['type' => 'update_attribute', 'key' => 'current_streak', 'operator' => '=', 'value' => 0];
            $actions[] = ['type' => 'update_attribute', 'key' => 'wrong_streak', 'operator' => '=', 'value' => 0];
            $actions[] = ['type' => 'update_attribute', 'key' => 'level_correct_count', 'operator' => '=', 'value' => 0];
            $actions[] = ['type' => 'update_attribute', 'key' => 'level_attempt_count', 'operator' => '=', 'value' => 0];
        } 
        elseif ($this->action_type === 'add_bonus') {
            // Parse bonus type
            $bonusMap = [
                'high_accuracy_85' => ['xp' => 50, 'points' => 25],
                'medium_accuracy_75' => ['xp' => 30, 'points' => 15],
                'basic_accuracy_60' => ['xp' => 20, 'points' => 10],
                'base_reward_correct' => ['xp' => 10, 'points' => 5]
            ];
            
            if (isset($bonusMap[$this->action_value])) {
                foreach ($bonusMap[$this->action_value] as $key => $value) {
                    $actions[] = [
                        'type' => 'update_attribute',
                        'key' => $key,
                        'operator' => '+',
                        'value' => $value
                    ];
                }
            }
        }
        
        return $actions;
    }
}
