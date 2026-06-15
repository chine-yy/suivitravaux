<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationSousTacheMenbreMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $sousTache;
    public $assignedBy;

    public function __construct($user, $sousTache, $assignedBy)
    {
        $this->user = $user;
        $this->sousTache = $sousTache;
        $this->assignedBy = $assignedBy;
    }

    public function build()
    {
        return $this->subject('Nouvelle sous-tâche assignée - ' . config('app.name'))
            ->view('emails.notification-soustache');
    }
}
