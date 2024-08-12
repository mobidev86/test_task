<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $students = Student::orderBy('created_at', 'desc')->get();
            return response()->json($students, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not retrieve students.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Store a newly created student in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'date_of_birth' => 'required|date|before:today',
                'email' => 'required|email|unique:students,email',
            ]);

            
            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed.',
                    'messages' => $validator->errors()
                ], 422);
            }
           
             
             $student = Student::create(array_merge($request->all(), ['created_by' => Auth::id()]));

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not create student.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
