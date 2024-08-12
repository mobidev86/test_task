<?php

namespace App\Console\Commands;

use App\Models\ScheduleSession;
use Illuminate\Console\Command;
use App\Models\Session; // Update with your model path
use App\Notifications\SessionReminder;
use App\Notifications\StudentSessionReminder;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendSessionReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sessions:remind';
    

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send session reminders 5 minutes before the session starts';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();
        $reminderTime = $now->copy()->addMinutes(5)->format('Y-m-d H:i:00');
        $sessions = ScheduleSession::whereRaw('CONCAT(session_date, " ", start_time) = ?',[$reminderTime])->get();
        
        foreach ($sessions as $session) {
            $student = $session->student;
            $user = $student->creator;
            Notification::send($student, new StudentSessionReminder($session));
            Notification::send($user, new SessionReminder($session));
        }
        $this->info('Session reminders sent.');
    }
}
