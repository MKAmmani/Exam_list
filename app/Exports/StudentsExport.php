<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $courseId;
    protected $academicSessionId;

    public function __construct($courseId = null, $academicSessionId = null)
    {
        $this->courseId = $courseId;
        $this->academicSessionId = $academicSessionId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Student::with(['department', 'courses']);

        if ($this->courseId && $this->academicSessionId) {
            $query->whereHas('courses', function ($q) {
                $q->where('course_id', $this->courseId)
                  ->wherePivot('academic_session_id', $this->academicSessionId);
            });
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Registration Number',
            'Department',
            'Level',
            'Phone',
            'Created At',
        ];
    }

    /**
     * @param mixed $student
     * @return array
     */
    public function map($student): array
    {
        return [
            $student->id,
            $student->name,
            $student->email,
            $student->registration_number,
            $student->department?->name ?? 'N/A',
            $student->level,
            $student->phone ?? '',
            $student->created_at?->format('Y-m-d H:i:s') ?? '',
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
