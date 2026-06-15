<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Détails Fournisseur - {{ $fournisseur->nom }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 25px; }

        .stats-grid { width: 100%; margin-bottom: 25px; border-collapse: separate; border-spacing: 12px 0; margin-left: -12px; }
        .stat-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; text-align: center; width: 25%; }
        .stat-value { font-size: 18px; font-weight: bold; color: #0f172a; margin-bottom: 4px; }
        .stat-label { font-size: 9px; color: #64748b; text-transform: uppercase; font-weight: 800; }

        .main-container { width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-left: -20px; }
        .left-col { width: 66%; vertical-align: top; }
        .right-col { width: 34%; vertical-align: top; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-header-icon { color: #009A44; margin-right: 8px; }
        .card-body { padding: 20px; }

        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 5px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 15px; display: block; }

        .badge { padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 10px; display: inline-block; }
        .badge-success { background: #c8e6c9; color: #005a28; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-secondary { background: #f1f5f9; color: #475569; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td><div class="logo">CNRST</div></td>
                <td style="text-align: right; color: #94a3b8; font-size: 9px;">Généré le {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
        <div class="page-title">{{ $fournisseur->nom }}</div>
        <div class="page-subtitle">Fiche détaillée du fournisseur</div>
    </div>

    @php
    $statutClass = [
        'actif' => 'badge-success',
        'inactif' => 'badge-danger'
    ];
    $statutText = [
        'actif' => 'Actif',
        'inactif' => 'Inactif'
    ];
    @endphp

    <table class="stats-grid">
        <tr>
            <td class="stat-card">
                <div class="stat-value">
                    <span class="badge {{ $statutClass[$fournisseur->statut] ?? 'badge-secondary' }}">
                        {{ $statutText[$fournisseur->statut] ?? $fournisseur->statut }}
                    </span>
                </div>
                <div class="stat-label">Statut</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $fournisseur->stocks_count ?? 0 }}</div>
                <div class="stat-label">Articles fournis</div>
            </td>
            <td class="stat-card">
                <div class="stat-value">{{ $fournisseur->categorie ?? '-' }}</div>
                <div class="stat-label">Catégorie</div>
            </td>
            <td class="stat-card" style="border-top: 3px solid #009A44;">
                <div class="stat-value">{{ $fournisseur->conditions_paiement ?? 'Standard' }}</div>
                <div class="stat-label">Paiement</div>
            </td>
        </tr>
    </table>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header"> Informations générales</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">Nom</span>
                                    <span class="value">{{ $fournisseur->nom }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Email</span>
                                    <span class="value">{{ $fournisseur->email ?? 'Non renseigné' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Téléphone</span>
                                    <span class="value">{{ $fournisseur->telephone ?? 'Non renseigné' }}</span>
                                </td>
                                <td>
                                    <span class="label">Site Web</span>
                                    <span class="value">{{ $fournisseur->site_web ?? 'Non renseigné' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Adresse</span>
                                    <span class="value">{{ $fournisseur->adresse ?? 'Non renseignée' }}</span>
                                </td>
                                <td>
                                    <span class="label">SIRET / ICE</span>
                                    <span class="value">{{ $fournisseur->siret ?? 'Non renseigné' }}</span>
                                </td>
                            </tr>
                        </table>

                        @if($fournisseur->description)
                        <span class="label">Description / Notes</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 60px;">
                            {!! nl2br(e($fournisseur->description)) !!}
                        </div>
                        @endif
                    </div>
                </div>

                @if($fournisseur->contact_nom)
                <div class="card">
                    <div class="card-header"> Contact référent</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">Nom & Prénom</span>
                                    <span class="value">{{ $fournisseur->contact_prenom }} {{ $fournisseur->contact_nom }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Fonction</span>
                                    <span class="value">{{ $fournisseur->contact_fonction ?? 'Non renseignée' }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Email contact</span>
                                    <span class="value">{{ $fournisseur->contact_email ?? 'Non renseigné' }}</span>
                                </td>
                                <td>
                                    <span class="label">Téléphone contact</span>
                                    <span class="value">{{ $fournisseur->contact_telephone ?? 'Non renseigné' }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                @endif
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"> Informations bancaires</div>
                    <div class="card-body">
                        <span class="label">Banque</span>
                        <div class="value">{{ $fournisseur->banque ?? 'Non renseignée' }}</div>
                        <span class="label">IBAN</span>
                        <div class="value">{{ $fournisseur->iban ?? 'Non renseigné' }}</div>
                        <span class="label">BIC / SWIFT</span>
                        <div class="value">{{ $fournisseur->bic ?? 'Non renseigné' }}</div>
                        <span class="label">Conditions de paiement</span>
                        <div class="value">{{ $fournisseur->conditions_paiement ?? 'Standard' }}</div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value">{{ $fournisseur->created_at->format('d/m/Y à H:i') }}</span>
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $fournisseur->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document généré par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
