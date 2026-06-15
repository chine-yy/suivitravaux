<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NouvelleEquipeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $chef;
    public $equipe;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($chef, $equipe)
    {
        $this->chef = $chef;
        $this->equipe = $equipe;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Vous avez été désigné(e) Chef d\'Équipe')
                    ->view('emails.nouvelle-equipe');
    }
}
