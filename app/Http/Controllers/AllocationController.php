<?php

namespace App\Http\Controllers;

use App\Models\Allocation;
use App\Models\Exam;
use App\Models\Venue;
use App\Services\AllocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AllocationController extends Controller
{
    protected AllocationService $allocationService;

    public function __construct(AllocationService $allocationService)
    {
        $this->allocationService = $allocationService;
    }

    /**
     * Display a listing of allocations.
     */
    public function index(Request $request)
    {
        return redirect()->route('exams.index');
    }

    /**
     * Show page to run allocation for an exam.
     */
    public function showRunAllocation(string $examId)
    {
        $exam = Exam::with(['course', 'academicSession'])->findOrFail($examId);
        $studentsCount = $exam->students()->count();
        $isAllocated = $exam->is_allocated;

        return Inertia::render('Action/allocation', [
            'exam' => $exam,
            'stats' => [
                'totalStudents' => $studentsCount,
            ],
            'isAllocated' => $isAllocated,
        ]);
    }

    /**
     * Allocate students to venues and seats for an exam.
     */
    public function runAllocation(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
        ]);

        $exam = Exam::findOrFail($validated['exam_id']);

        // Check if already allocated
        if ($exam->is_allocated) {
            return redirect()->back()
                ->with('error', 'This exam has already been allocated.');
        }

        // Get the authenticated user ID (exam officer or admin)
        $allocatedBy = auth()->id();

        if (!$allocatedBy) {
            return redirect()->back()
                ->with('error', 'User not authenticated.');
        }

        try {
            $result = $this->allocationService->allocate($exam, $allocatedBy);

            if ($result['success']) {
                return redirect()->route('allocations.show', $exam->id)
                    ->with('success', $result['message']);
            }

            return redirect()->back()
                ->with('error', $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Allocation failed: ' . $e->getMessage());
        }
    }

    /**
     * Show allocation results for an exam.
     */
    public function show(string $examId)
    {
        $exam = Exam::with(['course', 'academicSession'])->findOrFail($examId);
        
        if (!$exam->is_allocated) {
            return redirect()->route('allocations.run', $examId)
                ->with('info', 'This exam has not been allocated yet.');
        }

        $summary = $this->allocationService->getAllocationSummary($exam);

        return Inertia::render('Action/allocation', [
            'exam' => $exam,
            'allocation' => [
                'id' => $examId,
                'is_allocated' => true,
            ],
            'venues' => $summary['venues'],
            'stats' => [
                'totalAllocated' => $summary['total_allocated'],
                'avgOccupancy' => $summary['average_occupancy_percentage'],
                'unallocated' => $summary['unallocated'],
                'venueUtilization' => $summary['average_occupancy_percentage'],
            ],
            'isAllocated' => true,
        ]);
    }

    /**
     * Show attendance sheet for a venue.
     */
    public function attendance(string $examId, Request $request)
    {
        $exam = Exam::with(['course', 'academicSession'])->findOrFail($examId);
        $venueId = $request->query('venue_id');
        
        if (!$venueId) {
            return redirect()->back()->with('error', 'Venue not specified');
        }

        $venue = Venue::findOrFail($venueId);
        
        $allocations = Allocation::where('exam_id', $examId)
            ->where('venue_id', $venueId)
            ->with(['student'])
            ->orderBy('seat_number')
            ->get();

        return Inertia::render('Action/attendance', [
            'exam' => $exam,
            'venue' => $venue,
            'allocations' => $allocations,
            'date' => $exam->date,
        ]);
    }

    /**
     * Get venue icon based on type.
     */
    private function getVenueIcon(string $type): string
    {
        return match ($type) {
            'lecture_hall' => 'stadium',
            'hall' => 'meeting_room',
            'classroom' => 'school',
            default => 'school',
        };
    }

    /**
     * Deallocate an exam (remove all allocations).
     */
    public function deallocate(string $examId)
    {
        $exam = Exam::findOrFail($examId);

        $result = $this->allocationService->deallocate($exam);

        if ($result['success']) {
            return redirect()->route('allocations.run', $examId)
                ->with('success', 'Allocation removed successfully.');
        }

        return redirect()->back()
            ->withErrors(['allocation' => $result['message']]);
    }

    /**
     * Download allocation results as CSV.
     */
    public function download(string $examId)
    {
        $exam = Exam::findOrFail($examId);
        $allocations = Allocation::where('exam_id', $examId)
            ->with(['student', 'venue'])
            ->orderBy('venue_id')
            ->orderBy('seat_number')
            ->get();

        $filename = "allocation_{$exam->course->code}_{$exam->date->format('Y-m-d')}.csv";

        return response()->stream(function () use ($allocations) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['Seat Number', 'Registration Number', 'Student Name', 'Venue', 'Department']);
            
            // Data
            foreach ($allocations as $allocation) {
                fputcsv($file, [
                    $allocation->seat_number,
                    $allocation->student->registration_number,
                    $allocation->student->name,
                    $allocation->venue->name,
                    $allocation->student->department?->name ?? 'N/A',
                ]);
            }
            
            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    /**
     * Print seating arrangement for a venue.
     */
    public function printSeatingArrangement(string $examId, string $venueId)
    {
        $exam = Exam::with(['course', 'academicSession'])->findOrFail($examId);
        $venue = Venue::findOrFail($venueId);

        $allocations = Allocation::where('exam_id', $examId)
            ->where('venue_id', $venueId)
            ->with(['student'])
            ->orderBy('seat_number')
            ->get();

        return view('exports.seating-arrangement', [
            'exam' => $exam,
            'venue' => $venue,
            'allocations' => $allocations,
        ]);
    }

    /**
     * Get student's allocation by exam.
     */
    public function studentAllocation(string $examId, string $studentId)
    {
        $allocation = Allocation::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->with(['venue', 'exam.course'])
            ->first();

        if (!$allocation) {
            return response()->json([
                'message' => 'No allocation found for this student.',
            ], 404);
        }

        return response()->json([
            'allocation' => $allocation,
        ]);
    }
}
