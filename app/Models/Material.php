<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'module_id',
        'created_by'
    ];

    protected $casts = [
        'module_id' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Material has many SubMaterials.
     */
    public function subMaterials()
    {
        return $this->hasMany(SubMaterial::class)->ordered();
    }

    /**
     * Material has many Questions (legacy, prefer subMaterials->questions).
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Material has many Media.
     */
    public function media()
    {
        return $this->hasMany(Media::class);
    }

    /**
     * Material belongs to User (creator).
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by module.
     */
    public function scopeByModule($query, int $moduleId)
    {
        return $query->where('module_id', $moduleId);
    }

    // ==================== METHODS ====================

    /**
     * Get the next material in sequence (based on module_id).
     */
    public function getNextMaterial()
    {
        return self::where('module_id', '>', $this->module_id)
            ->orderBy('module_id', 'asc')
            ->first();
    }

    /**
     * Get the previous material in sequence (based on module_id).
     */
    public function getPreviousMaterial()
    {
        return self::where('module_id', '<', $this->module_id)
            ->orderBy('module_id', 'desc')
            ->first();
    }
}