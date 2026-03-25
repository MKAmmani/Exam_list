<?php

namespace App\Imports;

use App\Models\Course;
use App\Models\AcademicSession;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMapping;

class CoursesImport implements ToModel, WithHeadingRow, WithValidation, WithMapping
{
    protected $academicSessionId;

    public function __construct($academicSessionId = null)
    {
        $this->academicSessionId = $academicSessionId ?? AcademicSession::getActive()?->id;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function map(array $row): array
    {
        return [
            'code' => $row['code'] ?? $row['course_code'] ?? null,
            'title' => $row['title'] ?? $row['course_title'] ?? $row['name'] ?? null,
            'credit_hours' => $row['credit_hours'] ?? $row['credits'] ?? $row['credit'] ?? 3,
            'semester' => $row['semester'] ?? $row['sem'] ?? 1,
            'academic_session_id' => $this->academicSessionId,
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
        if (empty($data['code']) || empty($data['title'])) {
            return null;
        }

        return new Course($data);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.code' => 'required|string|max:20|unique:courses,code',
            '*.title' => 'required|string|max:255',
            '*.credit_hours' => 'nullable|integer|min:1|max:6',
            '*.semester' => 'nullable|in:1,2',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.code.required' => 'Course code is required.',
            '*.code.unique' => 'Course code ":input" already exists.',
            '*.title.required' => 'Course title is required.',
        ];
    }
}
