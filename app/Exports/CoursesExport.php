<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CoursesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $academicSessionId;

    public function __construct($academicSessionId = null)
    {
        $this->academicSessionId = $academicSessionId;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->academicSessionId) {
            return Course::where('academic_session_id', $this->academicSessionId)
                ->with('academicSession')
                ->get();
        }

        return Course::with('academicSession')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Course Code',
            'Course Title',
            'Credit Hours',
            'Semester',
            'Academic Session',
            'Created At',
        ];
    }

    /**
     * @param mixed $course
     * @return array
     */
    public function map($course): array
    {
        return [
            $course->id,
            $course->code,
            $course->title,
            $course->credit_hours,
            $course->semester,
            $course->academicSession?->name ?? 'N/A',
            $course->created_at?->format('Y-m-d H:i:s') ?? '',
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
