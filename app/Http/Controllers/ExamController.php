<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Course;
use App\Models\AcademicSession;
use App\Models\Venue;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExamController extends Controller
{
    /**
     * Display a listing of exams.
     */
    public function index(Request $request)
    {
        $query = Exam::with(['course', 'academicSession']);

        // Filter by academic session
        if ($request->has('academic_session_id') && $request->academic_session_id) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        // Filter by course
        if ($request->has('course_id') && $request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by date
        if ($request->has('date') && $request->date) {
            $query->whereDate('date', $request->date);
        }

        // Filter by allocation status
        if ($request->has('is_allocated')) {
            $query->where('is_allocated', $request->boolean('is_allocated'));
        }

        $exams = $query->orderBy('date')->orderBy('start_time')->get();
        
        // Load student counts for each exam
        foreach ($exams as $exam) {
            try {
                if ($exam->course && $exam->academic_session_id) {
                    $exam->students_count = $exam->course->students()
                        ->wherePivot('academic_session_id', $exam->academic_session_id)
                        ->count();
                } else {
                    $exam->students_count = 0;
                }
            } catch (\Exception $e) {
                $exam->students_count = 0;
            }
        }

        $courses = Course::all();
        $sessions = AcademicSession::all();
        $venues = Venue::where('is_active', true)->get();

        return Inertia::render('Action/exam', [
            'exams' => $exams,
            'courses' => $courses,
            'sessions' => $sessions,
            'venues' => $venues,
            'filters' => [
                'academic_session_id' => $request->academic_session_id,
                'course_id' => $request->course_id,
                'date' => $request->date,
                'is_allocated' => $request->is_allocated,
            ],
        ]);
    }

    /**
     * Store a newly created exam.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration_minutes' => 'required|integer|min:30',
        ]);

        Exam::create($validated);

        return redirect()->route('exams.index')
            ->with('success', 'Exam schedule created successfully.');
    }

    /**
     * Display the specified exam.
     */
    public function show(string $id)
    {
        $exam = Exam::with([
            'course',
            'academicSession',
            'allocations.venue.student',
            'allocatedBy'
        ])->findOrFail($id);

        return redirect()->route('allocations.show', $exam->id);
    }

    /**
     * Remove the specified exam.
     */
    public function destroy(string $id)
    {
        $exam = Exam::findOrFail($id);
        $exam->delete();

        return redirect()->route('exams.index')
            ->with('success', 'Exam schedule deleted successfully.');
    }

    /**
     * Get students for an exam.
     */
    public function getStudents(string $id)
    {
        $exam = Exam::with(['course.students'])->findOrFail($id);

        return response()->json([
            'exam' => $exam,
            'students' => $exam->students,
        ]);
    }
}
