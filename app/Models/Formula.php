<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'description',
        'expression',
        'dependencies',
        'return_type',
        'scope',
        'is_active',
        'sort_order'
    ];
    
    protected $casts = [
        'dependencies' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];
    
    // Available functions in formula
    const FUNCTIONS = [
        'PERCENTAGE' => 'Persentase (a/b)*100',
        'IF' => 'Kondisi IF-THEN-ELSE',
        'ROUND' => 'Pembulatan',
        'ABS' => 'Nilai absolut',
        'MIN' => 'Nilai minimum',
        'MAX' => 'Nilai maksimum',
        'SUM' => 'Jumlahkan nilai',
        'AVG' => 'Rata-rata',
        'COUNT' => 'Hitung jumlah'
    ];
    
    // Available operators
    const OPERATORS = [
        '+' => 'Tambah',
        '-' => 'Kurang',
        '*' => 'Kali',
        '/' => 'Bagi',
        '%' => 'Modulo',
        '>' => 'Lebih besar',
        '<' => 'Lebih kecil',
        '>=' => 'Lebih besar sama dengan',
        '<=' => 'Lebih kecil sama dengan',
        '==' => 'Sama dengan',
        '!=' => 'Tidak sama dengan',
        '&&' => 'DAN (AND)',
        '||' => 'ATAU (OR)'
    ];

    // Scope types
    const SCOPES = [
        'material' => 'Per Materi',
        'global' => 'Global (Semua Materi)',
        'session' => 'Session Saat Ini'
    ];

    // Return types
    const RETURN_TYPES = [
        'integer' => 'Integer (Angka Bulat)',
        'float' => 'Float (Angka Desimal)',
        'string' => 'String (Teks)',
        'boolean' => 'Boolean (True/False)'
    ];
    
    public function attributeDefinitions()
    {
        return $this->hasMany(AttributeDefinition::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }
}
