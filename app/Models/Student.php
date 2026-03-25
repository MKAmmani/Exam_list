<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string, string>
     */
    protected $fillable = [
        'name',
        'email',
        'registration_number',
        'department_id',
        'level',
        'phone',
    ];

    /**
     * Get the courses this student is enrolled in.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_student')
                    ->withPivot('academic_session_id')
                    ->withTimestamps();
    }

    /**
     * Get the allocations for this student.
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(Allocation::class);
    }

    /**
     * Get the department for this student.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the current session allocations.
     */
    public function currentSessionAllocations()
    {
        $session = AcademicSession::getActive();
        if (!$session) {
            return collect();
        }

        return $this->allocations()
            ->whereHas('exam', function ($query) use ($session) {
                $query->where('academic_session_id', $session->id);
            })
            ->with(['exam.course', 'venue'])
            ->get();
    }
}
