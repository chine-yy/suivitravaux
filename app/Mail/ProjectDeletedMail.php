<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProjectDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $prenom;
    public $projet_nom;
    public $role_label;
    public $projet_budget;
    public $salaire;
    public $chef_projet_nom;
    public $chef_projet_prenom;
    public $chef_projet_email;
    public $chef_projet_telephone;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->prenom = $data['prenom'] ?? '';
        $this->projet_nom = $data['projet_nom'] ?? '';
        $this->role_label = $data['role_label'] ?? 'Membre';
        $this->projet_budget = $data['projet_budget'] ?? 0;
        $this->salaire = $data['salaire'] ?? 0;
        $this->chef_projet_nom = $data['chef_projet_nom'] ?? '';
        $this->chef_projet_prenom = $data['chef_projet_prenom'] ?? '';
        $this->chef_projet_email = $data['chef_projet_email'] ?? '';
        $this->chef_projet_telephone = $data['chef_projet_telephone'] ?? '';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Information : Suppression du projet ' . $this->projet_nom)
                    ->view('emails.suppression_projet');
    }
}
