<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RemovedFromChefRoleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $equipe;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $equipe)
    {
        $this->user = $user;
        $this->equipe = $equipe;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Changement de Chef d\'Équipe')
                    ->view('emails.removed-chef-equipe');
    }
}
