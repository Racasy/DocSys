<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class DeniedRequestEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $requestTitle;
    public $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $requestTitle, string $reason)
    {
        $this->user = $user;
        $this->requestTitle = $requestTitle;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.denied')
                    ->subject('Jūsu pieprasījums tika noraidīts')
                    ->with([
                        'user' => $this->user,
                        'requestTitle' => $this->requestTitle,
                        'reason' => $this->reason,
                    ]);
    }
}