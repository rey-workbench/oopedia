<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Material extends Model
{
    use HasFactory;
    use HasUlids;

    #[\Override]
    protected $fillable = [
        'title',
        'content',
        'module_id',
        'created_by',
        'is_final_project',
    ];

    #[\Override]
    protected function casts(): array
    {
        return [
            'is_final_project' => 'boolean',
            'module_id'        => 'string',
            'created_by'       => 'string',
        ];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
