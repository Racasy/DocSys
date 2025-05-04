<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\DocumentRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\RequestNotificationEmail;

class CreateMonthlyRequests extends Command
{
    protected $signature = 'requests:create-monthly';
    protected $description = 'Create monthly document requests for all users';

    public function handle()
    {
        $users = User::all();
        $monthName = Carbon::now()->format('F'); // Get current month name
        $deadline = Carbon::now()->day(10); // Set deadline to 10th of the month

        foreach ($users as $user) {
            $request = DocumentRequest::create([
                'user_id' => $user->id,
                'title' => "Par {$monthName}",
                'description' => "Monthly document request for {$monthName}",
                'deadline' => $deadline,
                'status' => 'pending',
            ]);

            Mail::to($user->email)->send(new RequestNotificationEmail($user, "Par {$monthName}", $deadline));

            $this->info("Request created for user: {$user->name} and email sent to {$user->email}");
        }

        $this->info('Monthly document requests created successfully!');
    }
}