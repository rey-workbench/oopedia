<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeDefinition extends Model
{
    protected $fillable = [
        'key', 'label', 'type', 'is_computed', 'formula_id',
        'validation_rules', 'default_value', 'category', 
        'description', 'is_active', 'sort_order'
    ];
    
    protected $casts = [
        'validation_rules' => 'array',
        'is_active' => 'boolean',
        'is_computed' => 'boolean',
        'sort_order' => 'integer'
    ];
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function formula()
    {
        return $this->belongsTo(Formula::class);
    }

    public function getCastedDefaultValue()
    {
        return match($this->type) {
            'integer' => (int) $this->default_value,
            'float' => (float) $this->default_value,
            'boolean' => filter_var($this->default_value, FILTER_VALIDATE_BOOLEAN),
            'array' => json_decode($this->default_value, true) ?? [],
            default => $this->default_value,
        };
    }
}
