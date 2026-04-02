<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $title
 * @property string|null $content
 * @property string|null $module_id
 * @property string|null $created_by
 */
class Material extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'title',
        'content',
        'module_id',
        'created_by',
        'is_final_project',
    ];

    protected $casts = [
        'is_final_project' => 'boolean',
        'module_id'        => 'string',
        'created_by'       => 'string',
    ];

    // ==================== RELATIONSHIPS ====================

    public function subMaterials(): HasMany
    {
        return $this->hasMany(SubMaterial::class)->ordered();
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

    public function getNextMaterial(): ?self
    {
        return static::where(function ($query) {
            $query->where('created_at', '>', $this->created_at)
                ->orWhere(function ($q) {
                    $q->where('created_at', '=', $this->created_at)
                        ->where('id', '>', $this->id);
                });
        })
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->first();
    }

    public function getPreviousMaterial(): ?self
    {
        return static::where(function ($query) {
            $query->where('created_at', '<', $this->created_at)
                ->orWhere(function ($q) {
                    $q->where('created_at', '=', $this->created_at)
                        ->where('id', '<', $this->id);
                });
        })
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }
}
