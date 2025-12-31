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
        'condition_type',
        'condition_operator',
        'condition_value',
        'action_type',
        'action_value',
        'priority',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'priority' => 'integer'
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
}
