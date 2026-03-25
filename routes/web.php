<?php

use App\Http\Controllers\AcademicSessionController;
use App\Http\Controllers\AllocationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\StudentLoginController;
use App\Http\Controllers\Auth\StudentRegisterController;
use App\Http\Controllers\ExamOfficer\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

// Student Public Search Page
Route::get('/students/search', function () {
    return Inertia::render('Student/search');
})->name('students.search');

// Exam Officer Dashboard
Route::middleware(['auth', 'exam.officer'])->group(function () {
    Route::get('/exam-officer', [DashboardController::class, 'index'])->name('exam-officer.dashboard');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Dashboard', [
            'userType' => 'admin',
        ]);
    })->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Exam Officer Routes
    |--------------------------------------------------------------------------
    */

    // Academic Sessions
    Route::resource('academic-sessions', AcademicSessionController::class);
    Route::post('academic-sessions/{id}/set-active', [AcademicSessionController::class, 'setActive'])
        ->name('academic-sessions.set-active');

    // Courses
    Route::resource('courses', CourseController::class);
    Route::get('courses/import', [CourseController::class, 'showImport'])->name('courses.import.show');
    Route::post('courses/import', [CourseController::class, 'import'])->name('courses.import');
    Route::get('courses/export', [CourseController::class, 'export'])->name('courses.export');
    Route::get('courses/template', [CourseController::class, 'downloadTemplate'])->name('courses.template');
    Route::get('courses/{id}/students', [CourseController::class, 'getStudents'])->name('courses.students');
    Route::post('courses/{id}/students', [CourseController::class, 'addStudents'])->name('courses.students.add');
    Route::get('courses/{id}/enroll-students', [CourseController::class, 'showEnrollStudents'])->name('courses.enroll.show');
    Route::post('courses/{id}/enroll-students', [CourseController::class, 'enrollStudents'])->name('courses.enroll');

    // Students
    Route::resource('students', StudentController::class);
    Route::get('students/import', [StudentController::class, 'showImport'])->name('students.import.show');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');
    Route::get('students/template', [StudentController::class, 'downloadTemplate'])->name('students.template');

    // Exams
    Route::resource('exams', ExamController::class);
    Route::get('exams/{id}/students', [ExamController::class, 'getStudents'])->name('exams.students');
    Route::get('exams/{id}/run-allocation', [ExamController::class, 'showRunAllocation'])->name('exams.run-allocation');

    // Allocations
    Route::get('allocations', [AllocationController::class, 'index'])->name('allocations.index');
    Route::get('allocations/{exam}/run', [AllocationController::class, 'showRunAllocation'])->name('allocations.run');
    Route::post('allocations/run', [AllocationController::class, 'runAllocation'])->name('allocations.run.post');
    Route::get('allocations/{exam}', [AllocationController::class, 'show'])->name('allocations.show');
    Route::get('allocations/{exam}/attendance', [AllocationController::class, 'attendance'])->name('allocations.attendance');
    Route::post('allocations/{exam}/deallocate', [AllocationController::class, 'deallocate'])->name('allocations.deallocate');
    Route::get('allocations/{exam}/download', [AllocationController::class, 'download'])->name('allocations.download');
    Route::get('allocations/{exam}/venues/{venue}/print', [AllocationController::class, 'printSeatingArrangement'])
        ->name('allocations.print');
});

// Public student venue search (no auth required)
Route::get('student/venue-search', [StudentController::class, 'searchVenue'])->name('student.venue-search');
Route::post('student/venue-search', [StudentController::class, 'searchByRegNumber']);

// Student Registration
Route::get('student/register', [StudentRegisterController::class, 'showRegistrationForm'])->name('student.register');
Route::post('student/register', [StudentRegisterController::class, 'register']);

// Student Login
Route::get('student/login', [StudentLoginController::class, 'showLoginForm'])->name('student.login');
Route::post('student/login', [StudentLoginController::class, 'login']);
Route::post('student/logout', [StudentLoginController::class, 'logout'])->name('student.logout');

// Student Dashboard
Route::get('student/dashboard', function () {
    return Inertia::render('Student/Dashboard', [
        'student' => Auth::guard('student')->user(),
    ]);
})->middleware('auth:student')->name('student.dashboard');

require __DIR__.'/auth.php';
