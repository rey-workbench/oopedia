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
}