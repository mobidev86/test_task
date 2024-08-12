<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ScheduleSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Exception;

class ScheduleSessionController extends Controller
{
     /**
     * Display a listing of Schedule Session.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        try {
            $scheduleSession = ScheduleSession::with('student')->orderBy('created_at', 'desc')->get();
            return response()->json($scheduleSession, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not retrieve Schedule Session',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of Student Availability.
     *
     * @return \Illuminate\Http\Response
     */

     public function show($id)
    {
        try {
            $scheduleSession = ScheduleSession::with('student')
                ->where('id', $id)
                ->first();
            return response()->json($scheduleSession, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not retrieve Schedule Session.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function schedule(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
        ]);

        $student = Student::findOrFail($validated['student_id']);
        $session_date = new Carbon($validated['session_date']);
        $start_time = Carbon::createFromFormat('H:i', $validated['start_time']);
       
        $end_time = isset($request->end_time)  ? Carbon::createFromFormat('H:i', $request->end_time) : $start_time->copy()->addMinutes(15);

      
        $availability = $student->availabilities()
            ->where('day_of_week', $session_date->format('l'))
            ->first();

            if (!$availability) {
                return response()->json([ 'errors' => ['student_id' => ['This student is not available at this time']]], 400);
            }
            
       
        $overlapping = ScheduleSession::where('student_id', $student->id)
            ->whereDate('session_date', $session_date->format('Y-m-d'))
            ->where(function ($query) use ($start_time, $end_time) {
                $query->whereBetween('start_time', [$start_time->format('H:i:s'), $end_time->format('H:i:s')])
                    ->orWhereBetween('end_time', [$start_time->format('H:i:s'), $end_time->format('H:i:s')])
                    ->orWhere(function ($query) use ($start_time, $end_time) {
                        $query->where('start_time', '<=', $start_time->format('H:i:s'))
                                ->where('end_time', '>=', $end_time->format('H:i:s'));
                    });
            })
            ->exists();

            if ($overlapping) {
                return response()->json([ 'errors' => ['session_date' => ['A session already exists for the specified date and time']]], 400);
            }

        ScheduleSession::create([
            'student_id' => $student->id,
            'session_date' => $session_date->format('Y-m-d'),
            'start_time' => $start_time->format('H:i:s'),
            'end_time' => $end_time->format('H:i:s'),
            'is_repeated' => $request->is_repeated ?? false,
        ]);

        return response()->json(['success' => 'Session scheduled successfully']);
    }


    public function updateRating(Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:0,10',
            'sessionId' => 'required|integer',
        ]);

        $session = ScheduleSession::findOrFail($validated['sessionId']);
        $session->update(['rating' => $validated['rating']]);

        return response()->json(['success' => 'Rating updated']);
    }
}
