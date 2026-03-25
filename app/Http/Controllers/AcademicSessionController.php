<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AcademicSessionController extends Controller
{
    /**
     * Display a listing of academic sessions.
     */
    public function index()
    {
        $sessions = AcademicSession::withCount(['courses', 'exams'])
            ->orderBy('start_date', 'desc')
            ->get();

        return Inertia::render('Action/academic_session', [
            'sessions' => $sessions,
        ]);
    }

    /**
     * Store a newly created academic session.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        // If setting as active, deactivate other sessions
        if ($validated['is_active'] ?? false) {
            AcademicSession::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicSession::create($validated);

        return redirect()->route('academic-sessions.index')
            ->with('success', 'Academic session created successfully.');
    }

    /**
     * Update the specified academic session.
     */
    public function update(Request $request, string $id)
    {
        $session = AcademicSession::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        // If setting as active, deactivate other sessions
        if (isset($validated['is_active']) && $validated['is_active']) {
            AcademicSession::where('id', '!=', $id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }

        $session->update($validated);

        return redirect()->route('academic-sessions.index')
            ->with('success', 'Academic session updated successfully.');
    }

    /**
     * Remove the specified academic session.
     */
    public function destroy(string $id)
    {
        $session = AcademicSession::findOrFail($id);
        $session->delete();

        return redirect()->route('academic-sessions.index')
            ->with('success', 'Academic session deleted successfully.');
    }

    /**
     * Set a session as active.
     */
    public function setActive(string $id)
    {
        AcademicSession::where('is_active', true)->update(['is_active' => false]);

        $session = AcademicSession::findOrFail($id);
        $session->update(['is_active' => true]);

        return redirect()->route('academic-sessions.index')
            ->with('success', 'Active session updated successfully.');
    }
}
