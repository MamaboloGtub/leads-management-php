<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'lead_status',
        'lead_source',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'lead_status',
        'lead_source',
        'created_at',
        'updated_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'leadStatus',
        'leadSource',
        'createdAt',
        'updatedAt',
    ];

    /**
     * Get the lead status in camelCase.
     */
    public function getLeadStatusAttribute()
    {
        return $this->attributes['lead_status'] ?? null;
    }

    /**
     * Get the lead source in camelCase.
     */
    public function getLeadSourceAttribute()
    {
        return $this->attributes['lead_source'] ?? null;
    }

    /**
     * Get the created at timestamp in camelCase with ISO format.
     */
    public function getCreatedAtAttribute($value)
    {
        return $value ? $this->asDateTime($value)->toISOString() : null;
    }

    /**
     * Get the updated at timestamp in camelCase with ISO format.
     */
    public function getUpdatedAtAttribute($value)
    {
        return $value ? $this->asDateTime($value)->toISOString() : null;
    }

    /**
     * Override the toArray method to customize serialization
     */
    public function toArray()
    {
        $array = parent::toArray();

        // Remove snake_case versions and add camelCase versions
        unset($array['lead_status'], $array['lead_source'], $array['created_at'], $array['updated_at']);

        $array['leadStatus'] = $this->attributes['lead_status'] ?? null;
        $array['leadSource'] = $this->attributes['lead_source'] ?? null;

        if (isset($this->attributes['created_at'])) {
            $array['createdAt'] = $this->asDateTime($this->attributes['created_at'])->toISOString();
        }

        if (isset($this->attributes['updated_at'])) {
            $array['updatedAt'] = $this->asDateTime($this->attributes['updated_at'])->toISOString();
        }

        return $array;
    }
}
