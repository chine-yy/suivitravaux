<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartenaireInscribedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nom;
    public $prenom;
    public $email;
    public $projet_nom;
    public $mot_de_passe;
    public $chef_equipe_nom;
    public $chef_equipe_prenom;
    public $chef_projet_nom;
    public $chef_projet_prenom;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->nom = $data['nom'] ?? '';
        $this->prenom = $data['prenom'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->projet_nom = $data['projet_nom'] ?? '';
        $this->mot_de_passe = $data['mot_de_passe'] ?? '';
        $this->chef_equipe_nom = $data['chef_equipe_nom'] ?? '';
        $this->chef_equipe_prenom = $data['chef_equipe_prenom'] ?? '';
        $this->chef_projet_nom = $data['chef_projet_nom'] ?? '';
        $this->chef_projet_prenom = $data['chef_projet_prenom'] ?? '';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Vos accès partenaire ' . config('app.name'))
                    ->view('emails.inscription-partenaire');
    }
}
