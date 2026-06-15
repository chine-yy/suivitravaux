<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Document - {{ $document->nom }}</title>
    <style>
        @page { margin: 0; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; background-color: #f8fafc; margin: 0; padding: 40px; font-size: 11px; }
        .header { margin-bottom: 25px; }
        .logo { font-size: 24px; font-weight: bold; color: #009A44; margin-bottom: 5px; }
        .page-title { font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 2px; }
        .page-subtitle { color: #64748b; font-size: 13px; margin-bottom: 25px; }

        .card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; margin-bottom: 20px; overflow: hidden; }
        .card-header { background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 12px 20px; font-weight: 700; color: #334155; font-size: 12px; }
        .card-body { padding: 20px; }

        .label { color: #64748b; font-weight: 700; font-size: 9px; text-transform: uppercase; margin-bottom: 5px; display: block; }
        .value { color: #0f172a; font-weight: 600; font-size: 11px; margin-bottom: 15px; display: block; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: 600; display: inline-block; }
        .bg-success { background-color: #009A44; color: #fff; }
        .bg-secondary { background-color: #64748b; color: #fff; }

        .main-container { width: 100%; border-collapse: separate; border-spacing: 20px 0; margin-left: -20px; }
        .left-col { width: 66%; vertical-align: top; }
        .right-col { width: 34%; vertical-align: top; }

        .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .icon-circle { display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; border-radius: 50%; background-color: #009A44; color: #fff; font-size: 24px; margin-bottom: 10px; }
        .text-center { text-align: center; }
        .mb-2 { margin-bottom: 10px; }
        .text-truncate { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
        <div class="page-title">{{ $document->nom }}</div>
        <div class="page-subtitle">Informations détaillées du document</div>
    </div>

    <table class="main-container">
        <tr>
            <td class="left-col">
                <div class="card">
                    <div class="card-header"> Aperçu des informations</div>
                    <div class="card-body">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width: 50%;">
                                    <span class="label">Nom du Fichier</span>
                                    <span class="value">{{ $document->nom }}</span>
                                </td>
                                <td style="width: 50%;">
                                    <span class="label">Type</span>
                                    <span class="value">
                                        @php
                                            $types = ['contrat' => 'Contrat', 'facture' => 'Facture', 'rapport' => 'Rapport', 'photo' => 'Photo', 'plan' => 'Plan', 'autre' => 'Autre'];
                                            $displayType = $document->type === 'autre' && $document->type_personnalise
                                                ? $document->type_personnalise
                                                : ($types[$document->type] ?? ucfirst($document->type));
                                        @endphp
                                        {{ $displayType }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="label">Catégorie</span>
                                    <span class="value">{{ $document->categorie ?? 'Non spécifiée' }}</span>
                                </td>
                                <td>
                                    <span class="label">Projet Associé</span>
                                    <span class="value">{{ $document->projet->nom ?? 'Aucun projet' }}</span>
                                </td>
                            </tr>
                        </table>
                        <span class="label">Description</span>
                        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; color: #475569; line-height: 1.6; min-height: 80px;">
                            {!! nl2br(e($document->description ?? 'Aucune description disponible pour ce document.')) !!}
                        </div>
                    </div>
                </div>
            </td>

            <td class="right-col">
                <div class="card">
                    <div class="card-header"> Statut</div>
                    <div class="card-body text-center">
                        <span class="badge {{ $document->statut == 'actif' ? 'bg-success' : 'bg-secondary' }}" style="font-size: 14px; padding: 8px 16px;">
                            {{ $document->statut == 'actif' ? 'Actif' : 'Archivé' }}
                        </span>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"> Méta-informations</div>
                    <div class="card-body">
                        <span class="label">Ajouté par</span>
                        <span class="value">{{ $document->user->name ?? 'Système' }}</span>
                        <span class="label">Date d'ajout</span>
                        <span class="value">{{ $document->created_at->format('d/m/Y H:i') }}</span>
                        <span class="label">Dernière modification</span>
                        <span class="value">{{ $document->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                @if($document->fichier)
                <div class="card">
                    <div class="card-header"> Fichier</div>
                    <div class="card-body">
                        <span class="label">Nom du fichier</span>
                        <span class="value" style="word-break: break-all;">{{ basename($document->fichier) }}</span>
                    </div>
                </div>
                @endif
            </td>
        </tr>
    </table>

    <div class="footer">
        Document généré par CNRST Suivi Travaux - &copy; {{ date('Y') }}
    </div>
</body>
</html>
