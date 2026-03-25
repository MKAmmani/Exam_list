<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'type',
        'capacity',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the allocations for this venue.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    /**
     * Scope for active venues.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get available venues with sufficient capacity.
     */
    public static function getAvailable($requiredCapacity, $date, $time)
    {
        return self::active()
            ->where('capacity', '>=', $requiredCapacity)
            ->whereDoesntHave('allocations', function ($query) use ($date, $time) {
                $query->whereHas('exam', function ($q) use ($date, $time) {
                    $q->where('date', $date)
                      ->where('start_time', $time);
                });
            })
            ->orderBy('capacity', 'desc')
            ->get();
    }
}
