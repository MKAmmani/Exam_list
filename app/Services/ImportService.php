<?php

namespace App\Services;

use App\Imports\CoursesImport;
use App\Imports\StudentsImport;
use App\Models\Course;
use App\Models\Student;
use App\Models\AcademicSession;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException;

class ImportService
{
    /**
     * Import courses from Excel/CSV file.
     * 
     * @param string $filePath Path to the uploaded file
     * @param int|null $academicSessionId Academic session ID
     * @return array Result with success status and messages
     */
    public function importCourses(string $filePath, $academicSessionId = null): array
    {
        try {
            $import = new CoursesImport($academicSessionId);
            
            // Import and handle validation
            \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);
            
            return [
                'success' => true,
                'message' => 'Courses imported successfully.',
                'imported_count' => Course::where('academic_session_id', $academicSessionId ?? AcademicSession::getActive()?->id)
                    ->whereDate('created_at', today())
                    ->count(),
            ];
            
        } catch (ExcelValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Validation failed. Please fix the errors and try again.',
                'errors' => $errors,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Import students from Excel/CSV file.
     * 
     * @param string $filePath Path to the uploaded file
     * @param int|null $departmentId Default department ID
     * @return array Result with success status and messages
     */
    public function importStudents(string $filePath, $departmentId = null): array
    {
        try {
            $import = new StudentsImport($departmentId);
            
            // Import and handle validation
            \Maatwebsite\Excel\Facades\Excel::import($import, $filePath);
            
            return [
                'success' => true,
                'message' => 'Students imported successfully.',
                'imported_count' => Student::whereDate('created_at', today())->count(),
            ];
            
        } catch (ExcelValidationException $e) {
            $failures = $e->failures();
            $errors = [];
            
            foreach ($failures as $failure) {
                $errors[] = [
                    'row' => $failure->row(),
                    'attribute' => $failure->attribute(),
                    'errors' => $failure->errors(),
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Validation failed. Please fix the errors and try again.',
                'errors' => $errors,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Enroll students in a course from Excel/CSV file.
     * 
     * @param string $filePath Path to the uploaded file
     * @param int $courseId Course ID
     * @param int $academicSessionId Academic session ID
     * @return array Result with success status and messages
     */
    public function enrollStudentsInCourse(string $filePath, int $courseId, int $academicSessionId): array
    {
        try {
            $course = Course::findOrFail($courseId);
            $enrolledCount = 0;
            $skippedCount = 0;
            $errors = [];

            \Maatwebsite\Excel\Facades\Excel::import($import = new StudentsImport(), $filePath);
            
            // Get students from the import
            $data = \Maatwebsite\Excel\Facades\Excel::toArray(new StudentsImport(), $filePath)[0];
            
            // Skip header row
            array_shift($data);
            
            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // Account for header and 0-based index
                
                // Get registration number from row
                $regNumber = $row['registration_number'] 
                           ?? $row['reg_number'] 
                           ?? $row['matric_number'] 
                           ?? $row['reg_no'] 
                           ?? null;
                
                if (!$regNumber) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'message' => 'Registration number not found in row',
                    ];
                    continue;
                }
                
                // Find student
                $student = Student::where('registration_number', $regNumber)->first();
                
                if (!$student) {
                    $errors[] = [
                        'row' => $rowNumber,
                        'message' => "Student with registration number '{$regNumber}' not found",
                    ];
                    continue;
                }
                
                // Check if already enrolled
                $exists = $course->students()
                    ->wherePivot('academic_session_id', $academicSessionId)
                    ->where('student_id', $student->id)
                    ->exists();
                
                if ($exists) {
                    $skippedCount++;
                    continue;
                }
                
                // Enroll student
                $course->students()->attach($student->id, [
                    'academic_session_id' => $academicSessionId,
                ]);
                
                $enrolledCount++;
            }
            
            return [
                'success' => true,
                'message' => "Successfully enrolled {$enrolledCount} students.",
                'enrolled_count' => $enrolledCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors,
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Enrollment failed: ' . $e->getMessage(),
            ];
        }
    }
}
