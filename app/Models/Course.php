<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'code',
        'title',
        'credit_hours',
        'semester',
        'academic_session_id',
    ];

    protected $casts = [
        'credit_hours' => 'integer',
        'semester' => 'integer',
    ];

    /**
     * Get the academic session that owns the course.
     */
    public function academicSession(): BelongsTo
    {
        return $this->belongsTo(AcademicSession::class);
    }

    /**
     * Get the students enrolled in this course.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'course_student')
                    ->withPivot('academic_session_id')
                    ->withTimestamps();
    }

    /**
     * Get the exams for this course.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get the allocations for this course through exams.
     */
    public function allocations(): HasManyThrough
    {
        return $this->hasManyThrough(Allocation::class, Exam::class);
    }

    /**
     * Get the count of enrolled students.
     */
    public function getEnrolledStudentsCountAttribute(): int
    {
        $session = AcademicSession::getActive();
        if (!$session) {
            return 0;
        }
        
        return $this->students()
                    ->wherePivot('academic_session_id', $session->id)
                    ->count();
    }
}
