<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Department;
use App\Models\Allocation;
use App\Exports\StudentsExport;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Inertia\Inertia;

class StudentController extends Controller
{
    protected ImportService $importService;

    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        $query = Student::with(['department']);

        // Filter by department
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Search by name, email, or registration number
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20);
        $departments = Department::all();

        return Inertia::render('ExamOfficer/Students/Index', [
            'students' => $students,
            'departments' => $departments,
            'filters' => [
                'search' => $request->search,
                'department_id' => $request->department_id,
                'level' => $request->level,
            ],
        ]);
    }

    /**
     * Show form to create a new student.
     */
    public function create()
    {
        $departments = Department::all();

        return Inertia::render('ExamOfficer/Students/Create', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:students,email',
            'registration_number' => 'required|string|max:50|unique:students,registration_number',
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'integer|in:100,200,300,400,500',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show(string $id)
    {
        $student = Student::with(['department', 'allocations.exam.course', 'allocations.venue'])->findOrFail($id);

        return Inertia::render('ExamOfficer/Students/Show', [
            'student' => $student,
        ]);
    }

    /**
     * Show form to edit the specified student.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        $departments = Department::all();

        return Inertia::render('ExamOfficer/Students/Edit', [
            'student' => $student,
            'departments' => $departments,
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:students,email,' . $id,
            'registration_number' => 'sometimes|required|string|max:50|unique:students,registration_number,' . $id,
            'department_id' => 'nullable|exists:departments,id',
            'level' => 'integer|in:100,200,300,400,500',
            'phone' => 'nullable|string|max:20',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Show import form for students.
     */
    public function showImport()
    {
        $departments = Department::all();

        return Inertia::render('ExamOfficer/Students/Import', [
            'departments' => $departments,
        ]);
    }

    /**
     * Import students from Excel/CSV file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,max:10240',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $filePath = $request->file('file')->getRealPath();
        $result = $this->importService->importStudents($filePath, $request->department_id);

        if ($result['success']) {
            return redirect()->route('students.index')
                ->with('success', "{$result['imported']} students imported successfully.");
        }

        return redirect()->back()
            ->withErrors(['file' => $result['message']])
            ->withInput();
    }

    /**
     * Export students to Excel file.
     */
    public function export(Request $request)
    {
        $departmentId = $request->query('department_id');
        $level = $request->query('level');

        return Excel::download(
            new StudentsExport(null, null, $departmentId, $level),
            'students_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Download student import template.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new StudentsExport(),
            'student_import_template.xlsx'
        );
    }

    /**
     * Search student by registration number (for public venue search).
     */
    public function searchVenue()
    {
        return Inertia::render('Student/SearchVenue');
    }

    /**
     * Search student by registration number.
     */
    public function searchByRegNumber(Request $request)
    {
        $validated = $request->validate([
            'registration_number' => 'required|string|max:50',
        ]);

        $student = Student::with(['currentSessionAllocations'])
            ->where('registration_number', $validated['registration_number'])
            ->first();

        if (!$student) {
            return redirect()->back()
                ->withErrors(['registration_number' => 'Student not found with the provided registration number.']);
        }

        $allocations = $student->currentSessionAllocations->map(function ($allocation) {
            return [
                'course_code' => $allocation->exam->course->code,
                'course_title' => $allocation->exam->course->title,
                'exam_date' => $allocation->exam->date->format('Y-m-d'),
                'exam_time' => $allocation->exam->start_time,
                'venue' => $allocation->venue->name,
                'seat_number' => $allocation->seat_number,
            ];
        });

        return Inertia::render('Student/SearchVenue', [
            'student' => $student,
            'allocations' => $allocations,
        ]);
    }
}
