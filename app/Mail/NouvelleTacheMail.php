<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NouvelleTacheMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $assignedBy;
    public $tache;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $assignedBy, $tache)
    {
        $this->user = $user;
        $this->assignedBy = $assignedBy;
        $this->tache = $tache;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nouvelle tâche assignée - ' . config('app.name'))
                    ->view('emails.notification-tache');
    }
}
