<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSession extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the courses for this academic session.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the exams for this academic session.
     */
    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get the course-student enrollments for this session.
     */
    public function courseStudents(): HasMany
    {
        return $this->hasMany(CourseStudent::class);
    }

    /**
     * Get the active academic session.
     */
    public static function getActive(): ?self
    {
        return self::where('is_active', true)->first();
    }
}
