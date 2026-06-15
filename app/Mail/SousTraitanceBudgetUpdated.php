<?php

namespace App\Mail;

use App\Models\SousTraitance;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SousTraitanceBudgetUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $sousTraitance;
    public $amount;
    public $isNew;

    /**
     * Create a new message instance.
     *
     * @param SousTraitance $sousTraitance
     * @param float|null $amount
     * @param bool $isNew
     */
    public function __construct(SousTraitance $sousTraitance, $amount = null, $isNew = false)
    {
        $this->sousTraitance = $sousTraitance;
        $this->amount = $amount ?? $sousTraitance->montant_contrat;
        $this->isNew = $isNew;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->isNew 
            ? 'Nouvelle Sous-Traitance - Projet : ' . (optional($this->sousTraitance->projet)->nom ?? 'N/A')
            : 'Mise à jour du budget de sous-traitance - Projet : ' . (optional($this->sousTraitance->projet)->nom ?? 'N/A');

        return $this->subject($subject)
                    ->view('emails.sous-traitance-budget-updated');
    }
}
