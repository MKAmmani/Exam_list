<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class StudentRegisterController extends Controller
{
    /**
     * Display the student registration view.
     */
    public function showRegistrationForm(): Response
    {
        return Inertia::render('Student/Register');
    }

    /**
     * Handle an incoming student registration request.
     */
    public function register(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:students'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Student::create([
            'Name' => $credentials['name'],
            'Email' => $credentials['email'],
            'password' => Hash::make($credentials['password']),
        ]);

        return redirect()->route('student.login')->with('status', 'Registration successful! Please login.');
    }
}
