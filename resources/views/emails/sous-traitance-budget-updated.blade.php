<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 8px; }
        .header { background-color: #009A44; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 20px; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
        .budget-box { background-color: #e8f5e9; border: 1px hide #81c784; padding: 15px; border-radius: 4px; margin: 20px 0; text-align: center; }
        .budget-amount { font-size: 24px; font-weight: bold; color: #007a35; }
        .details { margin-top: 20px; }
        .details-item { margin-bottom: 10px; }
        .label { font-weight: bold; color: #666; width: 140px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Suivi de Travaux - Notification de Sous-Traitance</h2>
        </div>
        <div class="content">
            <p>Bonjour {{ $sousTraitance->contact_prenom }} {{ $sousTraitance->contact_nom }},</p>
            
            @if($isNew)
                <p>Nous avons le plaisir de vous informer qu'une nouvelle sous-traitance a été enregistrée pour votre entreprise <strong>{{ $sousTraitance->nom_entreprise }}</strong>.</p>
            @else
                <p>Nous vous informons qu'une mise à jour a été effectuée concernant le budget alloué à votre prestation.</p>
            @endif

            <div class="budget-box">
                <div class="label">Montant Alloué</div>
                <div class="budget-amount">{{ number_format($amount, 0, ',', ' ') }} FCFA</div>
            </div>

            <div class="details">
                <h3>Détails de l'intervention :</h3>
                <div class="details-item">
                    <span class="label">Projet :</span> {{ $sousTraitance->projet?->nom ?? 'N/A' }}
                </div>
                <div class="details-item">
                    <span class="label">Description :</span> {{ $sousTraitance->description_tache ?? 'N/A' }}
                </div>
                <div class="details-item">
                    <span class="label">Date de début :</span> {{ $sousTraitance->date_debut ? $sousTraitance->date_debut->format('d/m/Y') : 'N/A' }}
                </div>
                <div class="details-item">
                    <span class="label">Date de fin :</span> {{ $sousTraitance->date_fin ? $sousTraitance->date_fin->format('d/m/Y') : 'N/A' }}
                </div>
            </div>

            <p>Nous restons à votre disposition pour toute information complémentaire.</p>
            
            <p>Cordialement,<br>L'administration de Suivi de Travaux</p>
        </div>
        <div class="footer">
            Ceci est un mail automatique, merci de ne pas y répondre directement.
        </div>
    </div>
</body>
</html>
