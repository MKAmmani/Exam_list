<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\AcademicSession;
use App\Models\Student;
use App\Exports\CoursesExport;
use App\Imports\CoursesImport;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class CourseController extends Controller
{
    protected ImportService $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display a listing of courses.
     */
    public function index(Request $request)
    {
        $query = Course::with(['academicSession']);

        // Filter by academic session
        if ($request->has('academic_session_id') && $request->academic_session_id) {
            $query->where('academic_session_id', $request->academic_session_id);
        }

        // Filter by semester
        if ($request->has('semester') && $request->semester) {
            $query->where('semester', $request->semester);
        }

        // Search by code or title
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%");
            });
        }

        $courses = $query->withCount('students as students_count')->orderBy('code')->get();
        $sessions = AcademicSession::all();

        return Inertia::render('Action/course', [
            'courses' => $courses,
            'sessions' => $sessions,
            'filters' => [
                'search' => $request->search,
                'academic_session_id' => $request->academic_session_id,
                'semester' => $request->semester,
            ],
        ]);
    }

    /**
     * Show form to create a new course.
     */
    public function create()
    {
        $sessions = AcademicSession::all();

        return Inertia::render('ExamOfficer/Courses/Create', [
            'sessions' => $sessions,
        ]);
    }

    /**
     * Store a newly created course.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:courses,code',
            'title' => 'required|string|max:255',
            'credit_hours' => 'integer|min:1|max:6',
            'semester' => 'integer|in:1,2',
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        Course::create($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course.
     */
    public function show(string $id)
    {
        $course = Course::with(['academicSession', 'students', 'exams'])->findOrFail($id);

        return Inertia::render('ExamOfficer/Courses/Show', [
            'course' => $course,
        ]);
    }

    /**
     * Show form to edit the specified course.
     */
    public function edit(string $id)
    {
        $course = Course::findOrFail($id);
        $sessions = AcademicSession::all();

        return Inertia::render('ExamOfficer/Courses/Edit', [
            'course' => $course,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Update the specified course.
     */
    public function update(Request $request, string $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|required|string|max:20|unique:courses,code,' . $id,
            'title' => 'sometimes|required|string|max:255',
            'credit_hours' => 'integer|min:1|max:6',
            'semester' => 'integer|in:1,2',
            'academic_session_id' => 'sometimes|required|exists:academic_sessions,id',
        ]);

        $course->update($validated);

        return redirect()->route('courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course.
     */
    public function destroy(string $id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Import courses from Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        $filePath = $request->file('file')->getRealPath();
        $result = $this->importService->importCourses($filePath, $request->academic_session_id);

        if ($result['success']) {
            return redirect()->route('courses.index')
                ->with('success', "{$result['imported']} courses imported successfully.");
        }

        return redirect()->back()
            ->with('error', $result['message']);
    }

    /**
     * Export courses to Excel file.
     */
    public function export(Request $request)
    {
        $academicSessionId = $request->query('academic_session_id');

        if (!$academicSessionId) {
            return redirect()->back()->with('error', 'Please select an academic session to export');
        }

        return Excel::download(
            new CoursesExport($academicSessionId),
            'courses_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Download course import template.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new CoursesExport(null),
            'course_import_template.xlsx'
        );
    }

    /**
     * Show form to enroll students in a course.
     */
    public function showEnrollStudents(string $id)
    {
        $course = Course::findOrFail($id);
        $sessions = AcademicSession::all();

        return Inertia::render('ExamOfficer/Courses/EnrollStudents', [
            'course' => $course,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Enroll students in a course.
     */
    public function enrollStudents(Request $request, string $id)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,max:10240',
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        $filePath = $request->file('file')->getRealPath();
        $result = $this->importService->enrollStudentsInCourse(
            $filePath,
            $id,
            $request->academic_session_id
        );

        if ($result['success']) {
            return redirect()->route('courses.show', $id)
                ->with('success', "{$result['enrolled']} students enrolled successfully.");
        }

        return redirect()->back()
            ->withErrors(['file' => $result['message']])
            ->withInput();
    }

    /**
     * Manually add students to a course.
     */
    public function addStudents(Request $request, string $id)
    {
        $request->validate([
            'students' => 'required|array|min:1',
            'students.*.registration_number' => 'required|string|max:50',
            'students.*.name' => 'required|string|max:255',
            'students.*.department' => 'nullable|string|max:255',
            'students.*.level' => 'nullable|string|max:10',
            'academic_session_id' => 'required|exists:academic_sessions,id',
        ]);

        $course = Course::findOrFail($id);
        $academicSessionId = $request->academic_session_id;

        $added = 0;
        $failed = 0;

        foreach ($request->students as $studentData) {
            // Find student by registration number
            $student = Student::where('registration_number', $studentData['registration_number'])->first();

            if (!$student) {
                // Create new student
                $student = Student::create([
                    'registration_number' => $studentData['registration_number'],
                    'name' => $studentData['name'],
                    'department_id' => null,
                    'level' => !empty($studentData['level']) ? (int) $studentData['level'] : null,
                ]);
            }

            // Check if already enrolled in this course for this session
            $existingEnrollment = $course->students()
                ->wherePivot('academic_session_id', $academicSessionId)
                ->where('student_id', $student->id)
                ->exists();

            if (!$existingEnrollment) {
                $course->students()->attach($student->id, [
                    'academic_session_id' => $academicSessionId,
                ]);
                $added++;
            } else {
                $failed++;
            }
        }

        $message = "{$added} student(s) enrolled successfully.";
        if ($failed > 0) {
            $message .= " {$failed} already enrolled.";
        }

        return redirect()->route('courses.index')
            ->with('success', $message);
    }

    /**
     * Get students enrolled in a course.
     */
    public function getStudents(string $id)
    {
        $course = Course::findOrFail($id);
        $academicSessionId = request()->query('academic_session_id');

        if ($academicSessionId) {
            $students = $course->students()
                ->wherePivot('academic_session_id', $academicSessionId)
                ->withPivot('academic_session_id')
                ->get();
        } else {
            $students = $course->students;
        }

        return Inertia::render('ExamOfficer/Courses/Students', [
            'course' => $course,
            'students' => $students,
        ]);
    }
}
