<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NouvelleSousTacheMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $assignedBy;
    public $sousTache;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $assignedBy, $sousTache)
    {
        $this->user = $user;
        $this->assignedBy = $assignedBy;
        $this->sousTache = $sousTache;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nouvelle mission assignée - ' . config('app.name'))
                    ->view('emails.notification-soustache');
    }
}
