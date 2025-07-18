<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function handle(Login $event)
    {
        AuditLog::create([
            'user_id' => $event->user->id,
            'action' => 'logged_in',
            'details' => ['ip_address' => $this->request->ip()],
        ]);
    }
}