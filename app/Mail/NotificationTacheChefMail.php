<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationTacheChefMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chefEquipe;
    public $tache;
    public $projet;

    public function __construct($chefEquipe, $tache, $projet)
    {
        $this->chefEquipe = $chefEquipe;
        $this->tache = $tache;
        $this->projet = $projet;
    }

    public function build()
    {
        return $this->subject('Nouvelle tâche assignée - ' . config('app.name'))
            ->view('emails.notification-tache-chef');
    }
}
