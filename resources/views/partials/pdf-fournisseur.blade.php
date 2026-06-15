<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fiche Fournisseur - {{ $fournisseur->nom }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; display: flex; align-items: center; gap: 15px; }
        .header-icon { color: #009A44; font-size: 28px; flex-shrink: 0; }
        .header-text { flex-grow: 1; }
        .logo { font-size: 20px; font-weight: bold; color: #009A44; margin-bottom: 2px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 0; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 2px solid #009A44; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-header-icon { color: #009A44; margin-right: 8px; }
        .card-body { padding: 20px; }

        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 5px; display: block; letter-spacing: 0.5px; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 15px; display: block; }

        .grid-2col { display: table; width: 100%; border-collapse: collapse; }
        .grid-2col-cell { display: table-cell; width: 50%; padding-right: 20px; }
        .grid-2col-cell:last-child { padding-right: 0; }

        .main-container { width: 100%; border-collapse: collapse; }
        .left-col { width: 65%; vertical-align: top; padding-right: 20px; }
        .right-col { width: 35%; vertical-align: top; }

        .status-badge { display: inline-block; padding: 8px 12px; border-radius: 4px; font-size: 10px; font-weight: bold; color: white; }
        .status-actif { background-color: #009A44; }
        .status-inactif { background-color: #6c757d; }

        .description-box { background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 80px; border-left: 4px solid #009A44; white-space: pre-wrap; word-wrap: break-word; }

        .contact-card { background: #f8fafc; padding: 15px; border-radius: 8px; border-left: 4px solid #009A44; text-align: center; margin-bottom: 15px; }
        .contact-item { margin-bottom: 12px; display: flex; align-items: flex-start; }
        .contact-icon { width: 28px; height: 28px; background: white; border: 1px solid #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #009A44; margin-right: 10px; flex-shrink: 0; font-size: 12px; }
        .contact-data { flex-grow: 1; }
        .contact-label { font-size: 9px; color: #64748b; font-weight: 700; text-transform: uppercase; margin-bottom: 3px; }
        .contact-value { font-size: 10px; color: #334155; font-weight: 600; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-icon"><i class="bi bi-truck" style="font-size: 28px; color: #009A44;"></i></div>
        <div class="header-text">
            <div class="logo">CNRST</div>
            <div class="page-title">{{ $fournisseur->nom }}</div>
            <div class="page-subtitle">Fiche Fournisseur</div>
        </div>
    </div>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header">
                         Informations Générales
                        @php
                        $statutClass = $fournisseur->statut == 'actif' ? 'status-actif' : 'status-inactif';
                        $statutText = $fournisseur->statut == 'actif' ? 'Actif' : 'Inactif';
                        @endphp
                        <span style="float: right;"><span class="status-badge {{ $statutClass }}">{{ $statutText }}</span></span>
                    </div>
                    <div class="card-body">
                        <table class="grid-2col">
                            <tr>
                                <td class="grid-2col-cell">
                                    <span class="label">Nom / Raison Sociale</span>
                                    <span class="value">{{ $fournisseur->nom }}</span>
                                </td>
                                <td class="grid-2col-cell">
                                    <span class="label">Catégorie</span>
                                    <span class="value">{{ $fournisseur->categorie ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </table>
                        <table class="grid-2col">
                            <tr>
                                <td class="grid-2col-cell">
                                    <span class="label">Email</span>
                                    <span class="value">{{ $fournisseur->email ?? 'N/A' }}</span>
                                </td>
                                <td class="grid-2col-cell">
                                    <span class="label">Téléphone</span>
                                    <span class="value">{{ $fournisseur->telephone ?? 'N/A' }}</span>
                                </td>
                            </tr>
                        </table>
                        @if($fournisseur->adresse)
                        <span class="label">Adresse</span>
                        <div class="description-box">{{ $fournisseur->adresse }}</div>
                        @endif
                        @if($fournisseur->site_web)
                        <table class="grid-2col">
                            <tr>
                                <td class="grid-2col-cell">
                                    <span class="label">Site Web</span>
                                    <span class="value">{{ $fournisseur->site_web }}</span>
                                </td>
                            </tr>
                        </table>
                        @endif
                    </div>
                </div>

                @if($fournisseur->notes)
                <div class="card">
                    <div class="card-header"><i class="card-header-icon bi bi-journal-text"></i> Notes / Observations</div>
                    <div class="card-body">
                        <div class="description-box">{{ $fournisseur->notes }}</div>
                    </div>
                </div>
                @endif
            </td>

            <td class="right-col">
                @if($fournisseur->contact_nom || $fournisseur->contact_email || $fournisseur->contact_telephone)
                <div class="card">
                    <div class="card-header"><i class="card-header-icon bi bi-person-badge"></i> Contact Principal</div>
                    <div class="card-body">
                        <div class="contact-card">
                            @if($fournisseur->contact_nom)
                            <div style="font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 15px;">
                                {{ $fournisseur->contact_prenom }} {{ $fournisseur->contact_nom }}
                            </div>
                            @endif

                            @if($fournisseur->contact_email)
                            <div class="contact-item">
                                <div class="contact-icon"><i class="bi bi-envelope"></i></div>
                                <div class="contact-data">
                                    <div class="contact-label">Email</div>
                                    <div class="contact-value">{{ $fournisseur->contact_email }}</div>
                                </div>
                            </div>
                            @endif

                            @if($fournisseur->contact_telephone)
                            <div class="contact-item">
                                <div class="contact-icon"><i class="bi bi-telephone"></i></div>
                                <div class="contact-data">
                                    <div class="contact-label">Téléphone</div>
                                    <div class="contact-value">{{ $fournisseur->contact_telephone }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endif

                <div class="card">
                    <div class="card-header"><i class="card-header-icon bi bi-clock-history"></i> Historique</div>
                    <div class="card-body">
                        <span class="label">Créé le</span>
                        <span class="value">{{ $fournisseur->created_at?->format('d/m/Y à H:i') ?? 'N/A' }}</span>
                        @if($fournisseur->updated_at && $fournisseur->updated_at != $fournisseur->created_at)
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $fournisseur->updated_at->format('d/m/Y à H:i') }}</span>
                        @endif
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        Document confidentiel - Service de Suivi des Travaux | Généré le {{ now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>