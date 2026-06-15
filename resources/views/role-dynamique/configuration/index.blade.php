@extends('layouts.role-dynamique')

@section('title', 'Configuration Système')

@section('breadcrumb')
    <span class="text-muted">Configuration</span>
@endsection

@section('content')
<div class="cp-configuration">
    <div class="cp-content">
        <!-- Page Header -->
        <div class="cp-page-header">
            <div>
                <h1 class="cp-page-title">Configuration Système</h1>
                <p class="cp-page-subtitle">Gérer les paramètres globaux de l'application</p>
            </div>
        </div>


        <!-- Quick Stats / System Status -->
        <div class="cp-stats-grid mb-4">
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-info"><i class="bi bi-clock-history"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ now()->format('H:i') }}</div>
                    <div class="cp-stat-label">Heure du serveur</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon cp-bg-success"><i class="bi bi-check-circle"></i></div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $configurations['app_env'] === 'production' ? 'Production' : 'Développement' }}</div>
                    <div class="cp-stat-label">Environnement</div>
                </div>
            </div>
            <div class="cp-stat-card">
                <div class="cp-stat-icon {{ $configurations['app_debug'] ? 'cp-bg-green' : 'cp-bg-success' }}">
                    <i class="bi {{ $configurations['app_debug'] ? 'bi-bug' : 'bi-shield-check' }}"></i>
                </div>
                <div class="cp-stat-content">
                    <div class="cp-stat-value">{{ $configurations['app_debug'] ? 'Activé' : 'Désactivé' }}</div>
                    <div class="cp-stat-label">Mode Debug</div>
                </div>
            </div>
        </div>

        <!-- Configuration Sections -->
        <div class="row">
            <!-- System Information -->
            <div class="col-lg-6 mb-4">
                <div class="cp-card">
                    <div class="cp-card-header">
                        <h5 class="cp-card-title">
                            <i class="bi bi-info-circle me-2"></i>Informations Système
                        </h5>
                    </div>
                    <div class="cp-card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless table-config">
                                <tbody>
                                    @if($canPermission('view-base-de-donnees'))
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-database me-2"></i>Base de données</td>
                                        <td><span class="badge bg-info">{{ $configurations['db_driver'] }}</span></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-hdd me-2"></i>Hôte DB</td>
                                        <td><code>{{ $configurations['db_host'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-envelope me-2"></i>Mail Driver</td>
                                        <td><span class="badge bg-secondary">{{ $configurations['mail_driver'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-collection me-2"></i>Session</td>
                                        <td><span class="badge bg-primary">{{ $configurations['session_driver'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-lightning me-2"></i>Cache</td>
                                        <td><span class="badge bg-success">{{ $configurations['cache_driver'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-folder me-2"></i>Storage</td>
                                        <td><span class="badge bg-primary">{{ $configurations['filesystems_default'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-list-task me-2"></i>Queue</td>
                                        <td><span class="badge bg-info">{{ $configurations['queue_default'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted"><i class="bi bi-file-earmark-text me-2"></i>Log Channel</td>
                                        <td><span class="badge bg-secondary">{{ $configurations['log_channel'] }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Management -->
            @if($canAnyPermission([
                'view-base-de-donnees',
                'clear-base-de-donnees',
                'sauvegarde-base-de-donnees',
            ]))
            <div class="col-lg-6 mb-4">
                <div class="cp-card mb-4">
                    <div class="cp-card-header">
                        <h5 class="cp-card-title">
                            <i class="bi bi-database me-2"></i>Base de Données
                        </h5>
                    </div>
                    <div class="cp-card-body">
                        <div class="d-grid gap-2">
                                <a href="{{ route('role-dynamique.database.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-database me-2"></i>Gérer la base de données
                                </a>
                        </div>
                    </div>
                </div>

                <!-- Logs System -->
                @if($canPermission('view-logs'))
                <div class="cp-card">
                    <div class="cp-card-header">
                        <h5 class="cp-card-title">
                            <i class="bi bi-file-earmark-text me-2"></i>Logs Système
                        </h5>
                    </div>
                    <div class="cp-card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('role-dynamique.configuration.logs') }}" class="btn btn-outline-green">
                                <i class="bi bi-file-earmark-text me-2"></i>Voir les logs système
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>

        <!-- System Resources -->
        <div class="cp-card mb-4">
            <div class="cp-card-header">
                <h5 class="cp-card-title">
                    <i class="bi bi-pie-chart me-2"></i>Ressources Système
                </h5>
            </div>
            <div class="cp-card-body">
                <div class="row text-center">
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-cpu fs-1 text-primary"></i>
                            <div class="mt-2"><strong>PHP</strong></div>
                            <small class="text-muted">{{ phpversion() }}</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-gem fs-1 text-danger"></i>
                            <div class="mt-2"><strong>Laravel</strong></div>
                            <small class="text-muted">{{ app()->version() }}</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-git fs-1 text-primary"></i>
                            <div class="mt-2"><strong>Version</strong></div>
                            <small class="text-muted">1.0.0</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <i class="bi bi-calendar-check fs-1 text-success"></i>
                            <div class="mt-2"><strong>Dernière mise à jour</strong></div>
                            <small class="text-muted">{{ now()->format('d/m/Y') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
