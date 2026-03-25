<?php

namespace App\Services;

use App\Models\Allocation;
use App\Models\Exam;
use App\Models\Venue;
use Illuminate\Support\Facades\DB;

class AllocationService
{
    /**
     * Allocate students to venues and seat numbers for an exam.
     * 
     * @param Exam $exam The exam to allocate
     * @param int $allocatedBy User ID of the exam officer
     * @return array Result of the allocation
     */
    public function allocate(Exam $exam, int $allocatedBy): array
    {
        // Check if already allocated
        if ($exam->is_allocated) {
            return [
                'success' => false,
                'message' => 'Exam has already been allocated.',
            ];
        }

        // Get all students for this exam (randomized for fair allocation)
        $students = $exam->students()->inRandomOrder()->get();
        $totalStudents = $students->count();

        if ($totalStudents === 0) {
            return [
                'success' => false,
                'message' => 'No students enrolled for this exam.',
            ];
        }

        // Get available venues for the exam date and time (only active venues)
        $venues = Venue::where('is_active', true)
            ->orderBy('capacity', 'desc')
            ->get();

        if ($venues->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No venues available for allocation.',
            ];
        }

        // Calculate total capacity (50% of each venue)
        $totalCapacity = $venues->sum(function ($venue) {
            return (int) ($venue->capacity * 0.5);
        });

        if ($totalCapacity < $totalStudents) {
            return [
                'success' => false,
                'message' => "Insufficient venue capacity. Need {$totalStudents} seats, but only {$totalCapacity} available (50% rule applied).",
            ];
        }

        DB::beginTransaction();

        try {
            $allocations = [];
            $studentIndex = 0;
            $seatLetter = 'A';
            $seatNumber = 1;

            foreach ($venues as $venue) {
                // Skip if all students are allocated
                if ($studentIndex >= $totalStudents) {
                    break;
                }

                // Use only 50% of venue capacity
                $venueCapacity = (int) ($venue->capacity * 0.5);
                $seatsAllocatedInVenue = 0;

                // Allocate students to this venue
                while ($seatsAllocatedInVenue < $venueCapacity && $studentIndex < $totalStudents) {
                    $student = $students[$studentIndex];

                    // Generate seat number (e.g., A-001, B-015)
                    $seatNumberFormatted = $seatLetter . '-' . str_pad($seatsAllocatedInVenue + 1, 3, '0', STR_PAD_LEFT);

                    // Create allocation
                    $allocation = Allocation::create([
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                        'venue_id' => $venue->id,
                        'seat_number' => $seatNumberFormatted,
                        'allocated_by' => $allocatedBy,
                    ]);

                    $allocations[] = $allocation;

                    $studentIndex++;
                    $seatsAllocatedInVenue++;

                    // Update seat letter after every 26 seats (A-Z)
                    if ($seatsAllocatedInVenue > 0 && $seatsAllocatedInVenue % 26 === 0) {
                        $seatLetter = chr(ord($seatLetter) + 1);
                        $seatNumber = 1;
                    } else {
                        $seatNumber++;
                    }
                }
            }

            // Mark exam as allocated
            $exam->update([
                'is_allocated' => true,
                'allocated_at' => now(),
                'allocated_by' => $allocatedBy,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => "Successfully allocated {$totalStudents} students to venues.",
                'allocations_count' => count($allocations),
                'venues_used' => collect($allocations)->unique('venue_id')->count(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Allocation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Deallocate an exam (remove all allocations).
     * 
     * @param Exam $exam The exam to deallocate
     * @return array Result of the deallocation
     */
    public function deallocate(Exam $exam): array
    {
        DB::beginTransaction();

        try {
            // Delete all allocations for this exam
            $exam->allocations()->delete();

            // Mark exam as not allocated
            $exam->update([
                'is_allocated' => false,
                'allocated_at' => null,
                'allocated_by' => null,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Exam deallocated successfully.',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'Deallocation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get allocation summary for an exam.
     *
     * @param Exam $exam The exam to get summary for
     * @return array Summary of allocations
     */
    public function getAllocationSummary(Exam $exam): array
    {
        $allocations = $exam->allocations()
            ->with(['student', 'venue'])
            ->get();

        $venueSummary = [];
        foreach ($allocations as $allocation) {
            $venueId = $allocation->venue_id;
            if (!isset($venueSummary[$venueId])) {
                $venueSummary[$venueId] = [
                    'venue' => [
                        'id' => $allocation->venue->id,
                        'name' => $allocation->venue->name,
                        'location' => $allocation->venue->location,
                        'capacity' => $allocation->venue->capacity,
                        'type' => $allocation->venue->type,
                    ],
                    'allocated_count' => 0,
                    'seat_range' => '',
                    'students' => [],
                ];
            }
            $venueSummary[$venueId]['allocated_count']++;
            $venueSummary[$venueId]['seat_range'] = $this->calculateSeatRange($venueSummary[$venueId]['seat_range'], $allocation->seat_number);
            $venueSummary[$venueId]['students'][] = $allocation;
        }

        $totalAllocated = $allocations->count();
        $totalCapacity = 0;
        foreach ($venueSummary as $venueData) {
            $totalCapacity += $venueData['venue']['capacity'] * 0.5; // 50% capacity
        }
        $avgOccupancy = $totalCapacity > 0 ? round(($totalAllocated / $totalCapacity) * 100) : 0;

        return [
            'success' => true,
            'total_allocated' => $totalAllocated,
            'venues_used' => count($venueSummary),
            'venues' => collect(array_values($venueSummary)),
            'average_occupancy_percentage' => $avgOccupancy,
            'unallocated' => 0,
        ];
    }

    /**
     * Calculate seat range string.
     */
    private function calculateSeatRange(string $currentRange, string $newSeat): string
    {
        if (empty($currentRange)) {
            return $newSeat;
        }
        
        // Extract the letter and number parts
        $parts = explode('-', $newSeat);
        if (count($parts) !== 2) {
            return $currentRange;
        }
        
        $letter = $parts[0];
        $number = (int) $parts[1];
        
        // Get the last seat in the current range
        $lastParts = explode('-', $currentRange);
        if (count($lastParts) !== 2) {
            return $currentRange;
        }
        
        $lastLetter = $lastParts[0];
        $lastNumber = (int) $lastParts[1];
        
        // If same letter, update the range
        if ($letter === $lastLetter) {
            return $lastLetter . '-' . str_pad(max($lastNumber, $number), 3, '0', STR_PAD_LEFT);
        }
        
        // Different letter, just return the new seat
        return $newSeat;
    }
}
