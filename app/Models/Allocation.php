<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Allocation extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'venue_id',
        'seat_number',
        'allocated_by',
        'allocated_at',
    ];

    protected $casts = [
        'allocated_at' => 'datetime',
    ];

    /**
     * Get the exam for this allocation.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the student for this allocation.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the venue for this allocation.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the user who made this allocation.
     */
    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    /**
     * Get the course for this allocation through exam.
     */
    public function getCourseAttribute()
    {
        return $this->exam->course;
    }
}
