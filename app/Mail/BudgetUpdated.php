<?php

namespace App\Mail;

use App\Models\Projet;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BudgetUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $projet;
    public $amount;
    public $user;

    public function __construct(Projet $projet, $amount, $user)
    {
        $this->projet = $projet;
        $this->amount = $amount;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Mise à jour du budget - Projet : ' . $this->projet->nom)
                    ->view('emails.budget-updated');
    }
}
