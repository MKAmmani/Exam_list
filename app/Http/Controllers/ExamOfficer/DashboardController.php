<?php

namespace App\Http\Controllers\ExamOfficer;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\Course;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Venue;
use App\Models\Allocation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the Exam Officer Dashboard.
     */
    public function index()
    {
        // Get active academic session
        $academicSession = AcademicSession::getActive();
        
        // If no active session, create a default one for demo
        if (!$academicSession) {
            $academicSession = AcademicSession::create([
                'name' => '2024/2025',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth()->addMonths(11),
                'is_active' => true,
            ]);
        }

        // Get statistics
        $stats = [
            'pendingAllocations' => Exam::where('academic_session_id', $academicSession->id)
                ->where('is_allocated', false)
                ->where('date', '>=', now())
                ->count(),
            'totalCourses' => Course::where('academic_session_id', $academicSession->id)->count(),
            'totalStudents' => Student::count(),
            'upcomingExams' => Exam::where('academic_session_id', $academicSession->id)
                ->where('date', '>=', now())
                ->where('date', '<=', now()->addWeek())
                ->count(),
            'venueUtilization' => $this->calculateVenueUtilization($academicSession->id),
        ];

        // Get upcoming exams
        $upcomingExams = Exam::where('academic_session_id', $academicSession->id)
            ->where('date', '>=', now())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get()
            ->map(function ($exam) {
                return [
                    'id' => $exam->id,
                    'course_code' => $exam->course->code,
                    'course_title' => $exam->course->title,
                    'date' => $exam->date->format('M d, Y'),
                    'time' => $exam->start_time,
                    'students_count' => $exam->students_count,
                    'allocated' => $exam->is_allocated,
                ];
            });

        // Get current user
        $user = Auth::user();

        return Inertia::render('Exam_officer/index', [
            'academicSession' => $academicSession,
            'stats' => $stats,
            'upcomingExams' => $upcomingExams,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Calculate venue utilization percentage.
     */
    private function calculateVenueUtilization($sessionId)
    {
        $totalCapacity = Venue::active()->sum('capacity');
        
        $allocatedSeats = Allocation::whereHas('exam', function ($query) use ($sessionId) {
            $query->where('academic_session_id', $sessionId);
        })->count();

        if ($totalCapacity === 0) {
            return 0;
        }

        return min(100, round(($allocatedSeats / $totalCapacity) * 100));
    }
}
