<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAvailabilityController;
use App\Http\Controllers\ScheduleSessionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// Route::get('/test-mail', function () {
//     $toAddress = 'pravin_acq@yopmail.com'; // Replace with your recipient's email address
//     $subject = 'Test Email';
//     $message = 'This is a test email sent from Laravel without using a Mailable class.';

//     Mail::raw($message, function ($message) use ($toAddress, $subject) {
//         $message->to($toAddress)
//                 ->subject($subject);
//     });
   
//     return 'Test email sent!';
// });

Route::get('/', function () {
    if (Auth::check()) {
       // return redirect()->route('students.index');
       return view('dashboard');
    } else {
        return view('welcome');
    }
})->name('dashboard');

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Students Crud
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');


    Route::get('/student-availabilities/{id}', [StudentAvailabilityController::class, 'index']);
    Route::post('/student-availabilities', [StudentAvailabilityController::class, 'store']);

    Route::get('/sessions', [ScheduleSessionController::class, 'index']);
    Route::post('/sessions', [ScheduleSessionController::class, 'schedule']);
    Route::get('/session-details/{id}', [ScheduleSessionController::class, 'show']);
    Route::post('/session/submit-rating', [ScheduleSessionController::class, 'updateRating']);
    

    Route::get('/report-template', [ReportController::class, 'show']);
    Route::post('/report-template', [ReportController::class, 'updateReportTemplate']);

    Route::post('/upload-docx', [ReportController::class, 'uploadDocx']);
    Route::post('/generate-report', [ReportController::class, 'generateReport']);
    

});

require __DIR__.'/auth.php';


// Route::get('/{any}', function () {
//     return view('welcome');
// })->where('any', '.*');
