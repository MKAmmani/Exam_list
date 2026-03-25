<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'course_id',
        'academic_session_id',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'is_allocated',
        'allocated_by',
        'allocated_at',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'is_allocated' => 'boolean',
        'allocated_at' => 'datetime',
    ];

    /**
     * Get the course for this exam.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the academic session for this exam.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the allocations for this exam.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    /**
     * Get the user who allocated this exam.
     */
    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    /**
     * Get the students for this exam (via course enrollment).
     */
    public function students()
    {
        return $this->course->students()
            ->wherePivot('academic_session_id', $this->academic_session_id);
    }

    /**
     * Get the count of students for this exam.
     */
    public function getStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }

    /**
     * Check if exam is fully allocated.
     */
    public function getIsFullyAllocatedAttribute(): bool
    {
        return $this->is_allocated && 
               $this->allocations()->count() === $this->students_count;
    }
}
