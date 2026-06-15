<div class="cp-card-body p-0">
    <div class="table-responsive">
        <table class="table table-hover mb-0" id="rapportsTable">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" class="form-check-input" id="selectAll"></th>
                    <th>Date</th>
                    <th>Titre</th>
                    <th>Projet</th>
                    <th>Auteur</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $rapport)
                <tr data-statut="{{ $rapport->statut }}">
                    <td><input type="checkbox" class="form-check-input"></td>
                    <td>{{ \Carbon\Carbon::parse($rapport->created_at)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $rapport->titre }}</strong>
                        @if($rapport->description)
                        <br><small class="text-muted text-truncate" style="max-width: 200px;">{{ Str::limit($rapport->description, 50) }}</small>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-secondary">{{ $rapport->projet->nom ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-3 bg-light text-green border border-green-subtle shadow-sm">
                                {{ strtoupper(substr($rapport->auteur->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="d-flex flex-column text-start">
                                <span class="fw-bold text-dark">{{ $rapport->auteur->prenom ?? '' }} {{ $rapport->auteur->name ?? 'N/A' }}</span>
                                <small class="text-green fw-medium"><i class="bi bi-shield-lock me-1"></i>{{ $rapport->auteur->role->nom ?? 'Auteur' }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($rapport->statut === 'valide')
                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Validé</span>
                        @elseif($rapport->statut === 'rejete')
                        <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Rejeté</span>
                        @else
                        <span class="badge bg-primary"><i class="bi bi-hourglass-split me-1"></i>En attente</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-sm btn-icon btn-outline-info" title="Voir le détail" data-bs-toggle="modal"
                                data-bs-target="#viewRapportModal{{ $rapport->id }}">
                                <i class="bi bi-eye"></i>
                            </button>
                            @if($canPermission('exporter-pdf-rapports'))
                            <a href="{{ route('role-dynamique.export.pdf.direct', ['type' => 'rapport', 'id' => $rapport->id]) }}" class="btn btn-sm btn-icon btn-outline-primary" title="Telecharger en PDF">
                                <i class="bi bi-file-earmark-pdf"></i>
                            </a>
                            @endif
                            @php
                                $user = auth()->user();
                                $isAdminOrSuper = $user && ($user->isAdminEntreprise() || $user->isSuperAdmin());
                                $isNonOwnerReport = $rapport->auteur_id != auth()->id();
                                $canQuickStatusEdit = $isAdminOrSuper && $isNonOwnerReport;
                                $currentStatut = $rapport->statut;
                                $approuveStatuts = ['valide', 'approuve'];
                                $soumisStatuts = ['soumis', 'en_revision', 'en_revue'];
                            @endphp

                            @if($canPermission('edit-rapports'))
                                @if($canQuickStatusEdit)
                                <button type="button" class="btn btn-sm btn-icon btn-outline-warning" title="Modifier le statut" data-bs-toggle="modal" data-bs-target="#statusOnlyModal{{ $rapport->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @else
                                <a href="{{ route('role-dynamique.rapports.edit', $rapport->id) }}" class="btn btn-sm btn-icon btn-outline-warning" title="Modifier le rapport">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                            @endif
                            @if($canPermission('delete-rapports'))
                            <form action="{{ route('role-dynamique.rapports.destroy', $rapport->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Voulez-vous vraiment supprimer ce rapport ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Supprimer le rapport">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endif
                            @if($canPermission('envoyer-partenaire-rapports') && !$rapport->est_envoye)
                            <form action="{{ route('role-dynamique.rapports.envoyer-partenaire', $rapport->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-icon btn-outline-success" title="Envoyer au(x) partenaire(s)" onclick="return confirm('Envoyer ce rapport aux partenaires du projet ?')">
                                    <i class="bi bi-send"></i>
                                </button>
                            </form>
                            @elseif($canPermission('envoyer-partenaire-rapports') && $rapport->est_envoye)
                            <span class="btn btn-sm btn-icon btn-outline-secondary" title="Deja envoye aux partenaires">
                                <i class="bi bi-send-check-fill text-success"></i>
                            </span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <p class="mt-3 text-muted">Aucun rapport trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($items as $rapport)
@php
    $user = auth()->user();
    $isAdminOrSuper = $user && ($user->isAdminEntreprise() || $user->isSuperAdmin());
    $isNonOwnerReport = $rapport->auteur_id != auth()->id();
    $canQuickStatusEdit = $isAdminOrSuper && $isNonOwnerReport;
    $currentStatut = $rapport->statut;
    $approuveStatuts = ['valide', 'approuve'];
    $soumisStatuts = ['soumis', 'en_revision', 'en_revue'];
@endphp

@if($canQuickStatusEdit)
<div class="modal fade js-rapport-modal" id="statusOnlyModal{{ $rapport->id }}" tabindex="-1" aria-labelledby="statusOnlyModalLabel{{ $rapport->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <form action="{{ route('role-dynamique.rapports.update', $rapport->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="statusOnlyModalLabel{{ $rapport->id }}">
                        Modifier le statut du rapport #{{ $rapport->id }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Ce rapport ne vous appartient pas. Vous pouvez uniquement modifier son statut.
                    </p>

                    <label for="statut_{{ $rapport->id }}" class="form-label">Statut du Rapport <span class="text-danger">*</span></label>
                    <select name="statut" id="statut_{{ $rapport->id }}" class="form-select" required>
                        <option value="soumis" {{ in_array($currentStatut, $soumisStatuts) ? 'selected' : '' }}>Soumis / En révision</option>
                        <option value="valide" {{ in_array($currentStatut, $approuveStatuts) ? 'selected' : '' }}>Validé</option>
                        <option value="rejete" {{ $currentStatut == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                        <option value="brouillon" {{ $currentStatut == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-green">
                        <i class="bi bi-check-circle me-2"></i>Enregistrer le statut
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade js-rapport-modal" id="viewRapportModal{{ $rapport->id }}" tabindex="-1"
    aria-labelledby="viewRapportModalLabel{{ $rapport->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewRapportModalLabel{{ $rapport->id }}">
                    <i class="bi bi-file-earmark-text me-2"></i>{{ $rapport->titre }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Projet:</strong> <span class="badge bg-light text-dark border">{{ $rapport->projet->nom ?? 'N/A' }}</span></p>
                        <p><strong>Auteur:</strong> <span class="text-green fw-bold">{{ $rapport->auteur->prenom ?? '' }} {{ $rapport->auteur->name ?? 'N/A' }}</span> <small class="text-muted">({{ $rapport->auteur->role->nom ?? 'Rôle' }})</small></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Date de création:</strong> {{ \Carbon\Carbon::parse($rapport->created_at)->format('d/m/Y H:i') }}</p>
                        <p><strong>Statut:</strong>
                            @if($rapport->statut === 'valide')
                            <span class="badge bg-success">Validé</span>
                            @elseif($rapport->statut === 'rejete')
                            <span class="badge bg-danger">Rejeté</span>
                            @else
                            <span class="badge bg-primary">En attente</span>
                            @endif
                        </p>
                    </div>
                </div>
                @if($rapport->description)
                <div class="mb-3">
                    <strong>Description:</strong>
                    <p class="mt-2">{{ $rapport->description }}</p>
                </div>
                @endif
                @if($rapport->contenu)
                <div>
                    <strong>Contenu:</strong>
                    <div class="bg-light p-3 rounded mt-2">{{ $rapport->contenu }}</div>
                </div>
                @endif
            </div>
            <div class="modal-footer bg-light border-top-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                @if($canPermission('exporter-pdf-rapports'))
                <a href="{{ route('role-dynamique.export.pdf.direct', ['type' => 'rapport', 'id' => $rapport->id]) }}" class="btn btn-green">
                    <i class="bi bi-file-earmark-pdf me-2"></i>Télécharger PDF
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

@if($items instanceof \Illuminate\Contracts\Pagination\Paginator || $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
<div class="cp-card-footer d-flex justify-content-between align-items-center">
    <div class="text-muted">
        Affichage de {{ $items->firstItem() }} à {{ $items->lastItem() }} sur {{ $items->total() }} résultats
    </div>
    {{ $items->links() }}
</div>
@endif
