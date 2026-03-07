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
    use HasFactory, HasUlids;

    protected $fillable = [
        'title',
        'content',
        'module_id',
        'created_by',
        'is_final_project',
    ];

    protected $casts = [
        'is_final_project' => 'boolean',
        'module_id' => 'string',
        'created_by' => 'string',
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
        return $this->belongsTo(User::class , 'created_by');
    }

    // ==================== SCOPES ====================

    public function scopeByModule($query, int $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    // ==================== METHODS ====================

    public function getNextMaterial(): ?self
    {
        return self::where('module_id', '>', $this->module_id)
            ->orderBy('module_id', 'asc')
            ->first();
    }

    public function getPreviousMaterial(): ?self
    {
        return self::where('module_id', '<', $this->module_id)
            ->orderBy('module_id', 'desc')
            ->first();
    }
}
