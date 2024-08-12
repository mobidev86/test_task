<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentAvailability;
use Illuminate\Support\Facades\Validator;
use Exception;

class StudentAvailabilityController extends Controller
{
    /**
     * Display a listing of Student Availability.
     *
     * @return \Illuminate\Http\Response
     */

     public function index($id)
    {
        try {
            $availabilities = StudentAvailability::with('student')
                ->where('student_id', $id)
                ->get();

            if ($availabilities->isEmpty()) {
                return response()->json([
                    'error' => 'No availability found for this student.'
                ], 404);
            }

            return response()->json($availabilities, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not retrieve Student Availability.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
     

    /**
     * Store a newly created Student Availability in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     public function store(Request $request)
     {
         try {
            
             $validator = Validator::make($request->all(), [
                 'student_id' => 'required|exists:students,id',
                 'days_of_week' => 'required|array',
                 'days_of_week.*' => 'required|string|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
             ]);
     
             
             if ($validator->fails()) {
                 return response()->json([
                     'error' => 'Validation failed.',
                     'messages' => $validator->errors()
                 ], 422);
             }
     
             $studentId = $request->input('student_id');
             $daysOfWeek = $request->input('days_of_week');
     
             
             $existingAvailabilities = StudentAvailability::where('student_id', $studentId)->get();
     
             
             $daysToCreateOrUpdate = [];
            
             $daysToDelete = $existingAvailabilities->pluck('day_of_week')->diff($daysOfWeek);
     
             foreach ($daysOfWeek as $day) {
                 $existing = $existingAvailabilities->where('day_of_week', $day)->first();
                 if (!$existing) {
                    
                     $daysToCreateOrUpdate[] = ['student_id' => $studentId, 'day_of_week' => $day];
                 }
                
             }
     
             
             foreach ($daysToCreateOrUpdate as $availability) {
                 StudentAvailability::updateOrCreate(
                     ['student_id' => $availability['student_id'], 'day_of_week' => $availability['day_of_week']],
                     $availability
                 );
             }
     
             
             StudentAvailability::where('student_id', $studentId)
                 ->whereIn('day_of_week', $daysToDelete)
                 ->delete();
     
             return response()->json([
                 'message' => 'Student availabilities updated successfully.',
             ], 200);
     
         } catch (Exception $e) {
             return response()->json([
                 'error' => 'Could not update student availabilities.',
                 'message' => $e->getMessage()
             ], 500);
         }
     }
     
}
