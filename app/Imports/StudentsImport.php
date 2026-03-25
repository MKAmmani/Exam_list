<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Hash;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, WithMapping
{
    protected $defaultDepartmentId;

    public function __construct($departmentId = null)
    {
        $this->defaultDepartmentId = $departmentId;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function map(array $row): array
    {
        // Try to find department by name or code
        $departmentId = $this->defaultDepartmentId;
        if (isset($row['department']) || isset($row['dept'])) {
            $deptValue = $row['department'] ?? $row['dept'] ?? null;
            $department = Department::where('name', $deptValue)
                ->orWhere('code', $deptValue)
                ->first();
            if ($department) {
                $departmentId = $department->id;
            }
        }

        return [
            'name' => $row['name'] ?? $row['student_name'] ?? null,
            'email' => $row['email'] ?? null,
            'registration_number' => $row['registration_number'] ?? $row['reg_number'] ?? $row['matric_number'] ?? $row['reg_no'] ?? null,
            'department_id' => $departmentId,
            'level' => $row['level'] ?? $row['year'] ?? 100,
            'phone' => $row['phone'] ?? $row['telephone'] ?? null,
            'password' => Hash::make('password'), // Default password
        ];
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $data = $this->map($row);

        // Skip if required fields are missing
        if (empty($data['name']) || empty($data['registration_number'])) {
            return null;
        }

        // Check if student already exists
        $existingStudent = Student::where('registration_number', $data['registration_number'])->first();
        if ($existingStudent) {
            return null; // Skip existing students
        }

        return new Student($data);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.registration_number' => 'required|string|max:50|unique:students,registration_number',
            '*.email' => 'nullable|email|max:255|unique:students,email',
            '*.level' => 'nullable|integer|in:100,200,300,400,500',
            '*.phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.name.required' => 'Student name is required.',
            '*.registration_number.required' => 'Registration number is required.',
            '*.registration_number.unique' => 'Registration number ":input" already exists.',
            '*.email.unique' => 'Email ":input" already exists.',
        ];
    }
}
