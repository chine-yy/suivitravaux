<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Models\User;
use App\Models\Projet;
use App\Models\Phase;
use App\Models\Tache;
use App\Models\Incident;

Route::get('/', function () {
    $projetCount = Projet::count();
    $phaseCount = Phase::count();
    $tacheCount = Tache::count();
    $userCount = User::count();
    $incidentCount = Incident::count();

    return view('accueil', compact('projetCount', 'phaseCount', 'tacheCount', 'userCount', 'incidentCount'));
});

// Login routes
use App\Http\Controllers\LoginController;
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/logout/{sessionKey}', [LoginController::class, 'logout'])->name('logout.session');
Route::get('/switch-session/{sessionKey}', [LoginController::class, 'switchSessionGet'])->name('switch.session');

// Register routes - only if no company exists
use App\Http\Controllers\RegisterController;
Route::get('/register', fn() => redirect()->to('/inscription-entreprise'));
Route::get('/inscription-entreprise', [RegisterController::class, 'showRegistrationForm'])
    ->name('entreprise.register');
Route::post('/inscription-entreprise', [RegisterController::class, 'register'])
    ->name('register');

// Entreprise routes
use App\Http\Controllers\EntrepriseController;
Route::middleware(['auth'])->group(function () {
    Route::resource('entreprises', EntrepriseController::class);
    Route::post('/entreprises/{entreprise}/administrateurs', [EntrepriseController::class, 'addAdministrateur'])->name('entreprises.administrateurs.store');
});

// Contact form submission
use App\Http\Controllers\ContactController;
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// Password Reset with OTP
use App\Http\Controllers\PasswordResetController;
Route::get('/password/reset', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/password/send-otp', [PasswordResetController::class, 'sendOtp'])->name('password.send-otp');
Route::get('/password/verify-otp', [PasswordResetController::class, 'showVerifyForm'])->name('password.verify-otp');
Route::post('/password/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verify-otp.submit');
Route::get('/password/reset-form', [PasswordResetController::class, 'showResetForm'])->name('password.reset-form');
Route::post('/password/update', [PasswordResetController::class, 'resetPassword'])->name('password.update');

// Dashboard redirect based on role
use App\Helpers\SessionHelper;

Route::get('/dashboard', function () {
    // Prefer the active session (when multiple sessions are present)
    $currentType = SessionHelper::getCurrentType();

    $user = auth()->user();
    if ($currentType === 'Partenaire' || ($user && ($user->isPartenaire() || $user->type_compte === 'partenaire'))) {
        return redirect()->route('partenaire.dashboard');
    }

    if ($currentType === 'Admin') {
        return redirect()->route('admin-entreprise.dashboard');
    }

    if ($currentType === 'RolePersonnalise') {
        return redirect()->route('role-dynamique.dashboard');
    }

    // Fallback: check authenticated user
    if ($user) {
        if ($user->isSuperAdmin() || $user->type_compte === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }
        if ($user->isAdminEntreprise() || $user->type_compte === 'admin') {
            return redirect()->route('admin-entreprise.dashboard');
        }
        if ($user->isPartenaire() || $user->type_compte === 'partenaire') {
            return redirect()->route('partenaire.dashboard');
        }
        if ($user->type_compte === 'role_personnalise') {
            return redirect()->route('role-dynamique.dashboard');
        }
    }

    return redirect('/login');
})->name('dashboard');



// ROLE DYNAMIQUE Routes (remplace chef-equipe + membres)
Route::prefix('role-dynamique')
    ->name('role-dynamique.')
    ->middleware(['auth', 'check.budget.completion'])
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\RoleDynamique\RoleDashboardController::class, 'index'])->name('dashboard');

        // Chat pour les utilisateurs à rôle dynamique
        Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/store', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');

        // IA Chat pour les utilisateurs à rôle dynamique
        Route::middleware(['permission:activer-ia-chat-box'])->group(function () {
            Route::get('/ia-chat', [App\Http\Controllers\IaChat\IaChatController::class, 'index'])->name('ia-chat.index');
            Route::post('/ia-chat/store', [App\Http\Controllers\IaChat\IaChatController::class, 'store'])->name('ia-chat.store');
            Route::post('/ia-chat/create', [App\Http\Controllers\IaChat\IaChatController::class, 'createConversation'])->name('ia-chat.create');
            Route::delete('/ia-chat/{id}', [App\Http\Controllers\IaChat\IaChatController::class, 'deleteConversation'])->name('ia-chat.destroy');
        });

        Route::post('/export/pdf', [App\Http\Controllers\ExportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/pdf/{type}/{id}', [App\Http\Controllers\ExportController::class, 'directExportPdf'])->name('export.pdf.direct');
        Route::get('/export/users', [App\Http\Controllers\ExportController::class, 'exportUsersPdf'])->name('export.users.pdf');
        Route::post('/export/voir-pdf', [App\Http\Controllers\ExportController::class, 'voirPdf'])->name('export.voir-pdf');

        // Fonctionnalités selon permissions
        Route::get('/projets', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'index'])->name('projets.index');
        Route::get('/projets/create', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'create'])->name('projets.create');
        Route::post('/projets', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'store'])->name('projets.store');
        Route::get('/projets/{projet}', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'show'])->name('projets.show');
        Route::get('/projets/{projet}/edit', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'edit'])->name('projets.edit');
        Route::put('/projets/{projet}', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'update'])->name('projets.update');
        Route::delete('/projets/{projet}', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'destroy'])->name('projets.destroy');
        Route::get('/taches', [App\Http\Controllers\RoleDynamique\RoleTachesController::class, 'index'])->name('taches.index');
        Route::get('/taches/create', [App\Http\Controllers\RoleDynamique\RoleTachesController::class, 'create'])->name('taches.create');
        Route::post('/taches', [App\Http\Controllers\RoleDynamique\RoleTachesController::class, 'store'])->name('taches.store');
        Route::get('/taches/{tache}', [App\Http\Controllers\RoleDynamique\RoleTachesController::class, 'show'])->name('taches.show');
        Route::get('/taches/{tache}/edit', [App\Http\Controllers\RoleDynamique\RoleTachesController::class, 'edit'])->name('taches.edit');
        Route::put('/taches/{tache}', [App\Http\Controllers\RoleDynamique\RoleTachesController::class, 'update'])->name('taches.update');
        Route::delete('/taches/{tache}', [App\Http\Controllers\RoleDynamique\RoleTachesController::class, 'destroy'])->name('taches.destroy');
        Route::get('/sous-taches', [App\Http\Controllers\RoleDynamique\RoleSousTachesController::class, 'index'])->name('sous-taches.index');
        Route::get('/sous-taches/create', [App\Http\Controllers\RoleDynamique\RoleSousTachesController::class, 'create'])->name('sous-taches.create');
        Route::post('/sous-taches', [App\Http\Controllers\RoleDynamique\RoleSousTachesController::class, 'store'])->name('sous-taches.store');
        Route::get('/sous-taches/{id}', [App\Http\Controllers\RoleDynamique\RoleSousTachesController::class, 'show'])->name('sous-taches.show');
        Route::get('/sous-taches/{id}/edit', [App\Http\Controllers\RoleDynamique\RoleSousTachesController::class, 'edit'])->name('sous-taches.edit');
        Route::put('/sous-taches/{id}', [App\Http\Controllers\RoleDynamique\RoleSousTachesController::class, 'update'])->name('sous-taches.update');
        Route::delete('/sous-taches/{id}', [App\Http\Controllers\RoleDynamique\RoleSousTachesController::class, 'destroy'])->name('sous-taches.destroy');
        Route::get('/rapports', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'index'])->name('rapports.index');
        Route::get('/rapports/create', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'create'])->name('rapports.create');
        Route::post('/rapports', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'store'])->name('rapports.store');
        Route::get('/rapports/{id}/pdf', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'telechargerPdf'])->name('rapports.pdf');
        Route::get('/rapports/{id}/voir-pdf', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'voirPdf'])->name('rapports.voir-pdf');
        Route::get('/rapports/{id}', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'show'])->name('rapports.show');
        Route::get('/rapports/{id}/edit', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'edit'])->name('rapports.edit');
        Route::put('/rapports/{id}', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'update'])->name('rapports.update');
        Route::delete('/rapports/{id}', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'destroy'])->name('rapports.destroy');
        Route::post('/rapports/{id}/envoyer-partenaire', [App\Http\Controllers\RoleDynamique\RoleRapportsController::class, 'envoyerPartenaire'])->name('rapports.envoyer-partenaire');
        Route::get('/incidents', [App\Http\Controllers\RoleDynamique\RoleIncidentsController::class, 'index'])->name('incidents.index');
        Route::get('/incidents/create', [App\Http\Controllers\RoleDynamique\RoleIncidentsController::class, 'create'])->name('incidents.create');
        Route::post('/incidents', [App\Http\Controllers\RoleDynamique\RoleIncidentsController::class, 'store'])->name('incidents.store');
        Route::get('/incidents/{incident}', [App\Http\Controllers\RoleDynamique\RoleIncidentsController::class, 'show'])->name('incidents.show');
        Route::get('/incidents/{incident}/edit', [App\Http\Controllers\RoleDynamique\RoleIncidentsController::class, 'edit'])->name('incidents.edit');
        Route::put('/incidents/{incident}', [App\Http\Controllers\RoleDynamique\RoleIncidentsController::class, 'update'])->name('incidents.update');
        Route::delete('/incidents/{incident}', [App\Http\Controllers\RoleDynamique\RoleIncidentsController::class, 'destroy'])->name('incidents.destroy');
        Route::get('/phases', [App\Http\Controllers\RoleDynamique\RolePhasesController::class, 'index'])->name('phases.index');
        Route::get('/phases/create', [App\Http\Controllers\RoleDynamique\RolePhasesController::class, 'create'])->name('phases.create');
        Route::post('/phases', [App\Http\Controllers\RoleDynamique\RolePhasesController::class, 'store'])->name('phases.store');
        Route::get('/phases/{id}/edit', [App\Http\Controllers\RoleDynamique\RolePhasesController::class, 'edit'])->name('phases.edit');
        Route::put('/phases/{id}', [App\Http\Controllers\RoleDynamique\RolePhasesController::class, 'update'])->name('phases.update');
        Route::delete('/phases/{id}', [App\Http\Controllers\RoleDynamique\RolePhasesController::class, 'destroy'])->name('phases.destroy');
        Route::get('/phases/{id}', [App\Http\Controllers\RoleDynamique\RolePhasesController::class, 'show'])->name('phases.show');
        Route::get('/equipes/export-pdf', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'exportAllPdf'])->name('equipes.export-pdf');
        Route::get('/equipes', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'index'])->name('equipes.index');
        Route::get('/equipes/create', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'create'])->name('equipes.create');
        Route::post('/equipes', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'store'])->name('equipes.store');
        Route::get('/equipes/{equipe}', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'show'])->name('equipes.show');
        Route::get('/equipes/{equipe}/edit', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'edit'])->name('equipes.edit');
        Route::put('/equipes/{equipe}', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'update'])->name('equipes.update');
        Route::delete('/equipes/{equipe}', [App\Http\Controllers\RoleDynamique\RoleEquipesController::class, 'destroy'])->name('equipes.destroy');
        Route::get('/partenaires', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'index'])->name('partenaires.index');
        Route::get('/partenaires/create', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'create'])->name('partenaires.create');
        Route::post('/partenaires', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'store'])->name('partenaires.store');
        Route::get('/partenaires/{partenaire}', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'show'])->name('partenaires.show');
        Route::get('/partenaires/{partenaire}/edit', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'edit'])->name('partenaires.edit');
        Route::put('/partenaires/{partenaire}', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'update'])->name('partenaires.update');
        Route::post('/partenaires/{partenaire}/reset-password', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'resetPassword'])->name('partenaires.reset-password');
        Route::delete('/partenaires/{partenaire}', [App\Http\Controllers\RoleDynamique\RolePartenairesController::class, 'destroy'])->name('partenaires.destroy');
        Route::resource('budget', App\Http\Controllers\RoleDynamique\RoleBudgetsController::class)->where(['budget' => '[0-9]+']);
        Route::get('/budget/gestion-depenses', fn() => redirect()->route('role-dynamique.depenses.index'))->name('budget.gestion-depenses');

        // Allocation par Projet (separate view)
        Route::get('/allocation-projet', [App\Http\Controllers\RoleDynamique\RoleAllocationProjetsController::class, 'index'])->name('allocation-projet.index');
        Route::post('/allocation-projet', [App\Http\Controllers\RoleDynamique\RoleAllocationProjetsController::class, 'store'])->name('allocation-projet.store');
        Route::delete('/allocation-projet/{id}', [App\Http\Controllers\RoleDynamique\RoleAllocationProjetsController::class, 'destroy'])->name('allocation-projet.destroy');

        // Allocation par Sous-Traitance (separate view)
        Route::get('/allocation-sous-traitance', [App\Http\Controllers\RoleDynamique\RoleAllocationSousTraitanceController::class, 'index'])->name('allocation-sous-traitance.index');
        Route::post('/allocation-sous-traitance', [App\Http\Controllers\RoleDynamique\RoleAllocationSousTraitanceController::class, 'store'])->name('allocation-sous-traitance.store');
        Route::delete('/allocation-sous-traitance/{id}', [App\Http\Controllers\RoleDynamique\RoleAllocationSousTraitanceController::class, 'destroy'])->name('allocation-sous-traitance.destroy');
        Route::post('/budget/depenses', [App\Http\Controllers\RoleDynamique\RoleBudgetsController::class, 'storeDepense'])->name('budget.depenses.store');
        Route::put('/budget/depenses/{depense}', [App\Http\Controllers\RoleDynamique\RoleBudgetsController::class, 'updateDepense'])->name('budget.depenses.update');
        Route::delete('/budget/depenses/{depense}', [App\Http\Controllers\RoleDynamique\RoleBudgetsController::class, 'destroyDepense'])->name('budget.depenses.destroy');
        Route::get('/depenses', [App\Http\Controllers\RoleDynamique\RoleDepensesController::class, 'index'])->name('depenses.index');
        Route::get('/users', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'index'])->name('users.index');
        Route::get('/users/create', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'usersCreate'])->name('users.create');
        Route::post('/users', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'usersStore'])->name('users.store');
        Route::get('/users/{user}', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'usersShow'])->name('users.show');
        Route::get('/users/{user}/edit', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'usersEdit'])->name('users.edit');
        Route::put('/users/{user}', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'usersUpdate'])->name('users.update');
        Route::delete('/users/{user}', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'usersDestroy'])->name('users.destroy');
        Route::post('/users/{user}/reset-password', [App\Http\Controllers\RoleDynamique\RoleMembresController::class, 'usersResetPassword'])->name('users.reset-password');
        Route::post('/roles/clone', [App\Http\Controllers\RoleDynamique\RoleRolesController::class, 'clone'])->name('roles.clone');
        Route::post('/roles/quick', [App\Http\Controllers\RoleDynamique\RoleRolesController::class, 'quickRole'])->name('roles.quick');
        Route::resource('roles', App\Http\Controllers\RoleDynamique\RoleRolesController::class);
        Route::get('/contrats/export-pdf', [App\Http\Controllers\RoleDynamique\RoleContratsController::class, 'exportAllPdf'])->name('contrats.export-pdf');
        Route::resource('contrats', App\Http\Controllers\RoleDynamique\RoleContratsController::class);
        Route::post('/contrats/{id}/envoyer-partenaire', [App\Http\Controllers\RoleDynamique\RoleContratsController::class, 'envoyerPartenaire'])->name('contrats.envoyer-partenaire');
        Route::get('/factures', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'index'])->name('factures.index');
        Route::get('/factures/create', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'create'])->name('factures.create');
        Route::post('/factures', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'store'])->name('factures.store');
        Route::get('/factures/{id}', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'show'])->name('factures.show');
        Route::get('/factures/{id}/edit', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'edit'])->name('factures.edit');
        Route::put('/factures/{id}', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'update'])->name('factures.update');
        Route::delete('/factures/{id}', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'destroy'])->name('factures.destroy');
        Route::post('/factures/{id}/envoyer-partenaire', [App\Http\Controllers\RoleDynamique\RoleFacturesController::class, 'envoyerPartenaire'])->name('factures.envoyer-partenaire');
        Route::get('/interventions', [App\Http\Controllers\RoleDynamique\RoleInterventionsController::class, 'index'])->name('interventions.index');
        Route::get('/interventions/create', [App\Http\Controllers\RoleDynamique\RoleInterventionsController::class, 'create'])->name('interventions.create');
        Route::post('/interventions', [App\Http\Controllers\RoleDynamique\RoleInterventionsController::class, 'store'])->name('interventions.store');
        Route::get('/interventions/{id}', [App\Http\Controllers\RoleDynamique\RoleInterventionsController::class, 'show'])->name('interventions.show');
        Route::get('/interventions/{id}/edit', [App\Http\Controllers\RoleDynamique\RoleInterventionsController::class, 'edit'])->name('interventions.edit');
        Route::put('/interventions/{id}', [App\Http\Controllers\RoleDynamique\RoleInterventionsController::class, 'update'])->name('interventions.update');
        Route::delete('/interventions/{id}', [App\Http\Controllers\RoleDynamique\RoleInterventionsController::class, 'destroy'])->name('interventions.destroy');
        Route::get('/fournisseurs/export', [App\Http\Controllers\RoleDynamique\RoleFournisseursController::class, 'export'])->name('fournisseurs.export');
        Route::resource('fournisseurs', App\Http\Controllers\RoleDynamique\RoleFournisseursController::class);
        Route::get('/stocks/export', [App\Http\Controllers\RoleDynamique\RoleStocksController::class, 'export'])->name('stocks.export');
        Route::resource('stocks', App\Http\Controllers\RoleDynamique\RoleStocksController::class);
        Route::get('/rendezvous', [App\Http\Controllers\RoleDynamique\RoleRendezvousController::class, 'index'])->name('rendezvous.index');
        Route::get('/rendezvous/create', [App\Http\Controllers\RoleDynamique\RoleRendezvousController::class, 'create'])->name('rendezvous.create');
        Route::post('/rendezvous', [App\Http\Controllers\RoleDynamique\RoleRendezvousController::class, 'store'])->name('rendezvous.store');
        Route::get('/rendezvous/{id}', [App\Http\Controllers\RoleDynamique\RoleRendezvousController::class, 'show'])->name('rendezvous.show');
        Route::get('/rendezvous/{id}/edit', [App\Http\Controllers\RoleDynamique\RoleRendezvousController::class, 'edit'])->name('rendezvous.edit');
        Route::put('/rendezvous/{id}', [App\Http\Controllers\RoleDynamique\RoleRendezvousController::class, 'update'])->name('rendezvous.update');
        Route::delete('/rendezvous/{id}', [App\Http\Controllers\RoleDynamique\RoleRendezvousController::class, 'destroy'])->name('rendezvous.destroy');
        Route::get('/documents', [App\Http\Controllers\RoleDynamique\RoleDocumentsController::class, 'index'])->name('documents.index');
        Route::get('/documents/create', [App\Http\Controllers\RoleDynamique\RoleDocumentsController::class, 'create'])->name('documents.create');
        Route::post('/documents', [App\Http\Controllers\RoleDynamique\RoleDocumentsController::class, 'store'])->name('documents.store');
        Route::get('/documents/{id}', [App\Http\Controllers\RoleDynamique\RoleDocumentsController::class, 'show'])->name('documents.show');
        Route::get('/documents/{id}/edit', [App\Http\Controllers\RoleDynamique\RoleDocumentsController::class, 'edit'])->name('documents.edit');
        Route::put('/documents/{id}', [App\Http\Controllers\RoleDynamique\RoleDocumentsController::class, 'update'])->name('documents.update');
        Route::delete('/documents/{id}', [App\Http\Controllers\RoleDynamique\RoleDocumentsController::class, 'destroy'])->name('documents.destroy');
        Route::get('/satisfaction', [App\Http\Controllers\RoleDynamique\RoleSatisfactionController::class, 'index'])->name('satisfaction.index');
        Route::get('/satisfaction/{id}', [App\Http\Controllers\RoleDynamique\RoleSatisfactionController::class, 'show'])->name('satisfaction.show');
        Route::get('/rh', [App\Http\Controllers\RoleDynamique\RoleProjetsController::class, 'index'])->name('rh.index');

        // Sous-traitances
        Route::resource('sous-traitances', App\Http\Controllers\RoleDynamique\RoleSousTraitancesController::class);

        // Historique
        Route::get('/historique', [App\Http\Controllers\RoleDynamique\RoleDynamiqueHistoriqueController::class, 'index'])->name('historique.index');
        Route::get('/historique/{annee}', [App\Http\Controllers\RoleDynamique\RoleDynamiqueHistoriqueController::class, 'show'])->name('historique.show');
        Route::get('/historique/{annee}/pdf', [App\Http\Controllers\RoleDynamique\RoleDynamiqueHistoriqueController::class, 'exportPdf'])->name('historique.pdf');
        Route::get('/historique/{annee}/voir-pdf', [App\Http\Controllers\RoleDynamique\RoleDynamiqueHistoriqueController::class, 'voirPdf'])->name('historique.voir-pdf');

        // Profil
        Route::get('/parametres', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'show'])->name('parametres');
        Route::put('/parametres', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'update'])->name('parametres.update');
        Route::delete('/parametres/photo', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'destroyPhoto'])->name('parametres.photo.destroy');

        // Permissions
        Route::get('/parametres/permissions', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'editPermissions'])->name('permissions.edit');
        Route::put('/parametres/permissions', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'updatePermissions'])->name('permissions.update');

        // Configuration
        Route::get('/configuration', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'configuration'])->name('configuration.index');
        Route::get('/configuration/logs', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'viewLogs'])->name('configuration.logs');
        Route::post('/configuration/logs/clear', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'clearLogs'])->name('configuration.logs.clear');
        Route::get('/configuration/logs/export-pdf', [App\Http\Controllers\RoleDynamique\RoleDynamiqueProfileController::class, 'exportLogsPdf'])->name('configuration.logs.export-pdf');

        // Base de données
        Route::get('/database', [App\Http\Controllers\RoleDynamique\RoleDatabaseController::class, 'index'])->name('database.index');
        Route::post('/database/clear-table', [App\Http\Controllers\RoleDynamique\RoleDatabaseController::class, 'clearTable'])->name('database.clear-table');
        Route::post('/database/clear-all', [App\Http\Controllers\RoleDynamique\RoleDatabaseController::class, 'clearAll'])->name('database.clear-all');
        Route::get('/database/backup', [App\Http\Controllers\RoleDynamique\RoleDatabaseController::class, 'backup'])->name('database.backup');
        Route::get('/database/export-table', [App\Http\Controllers\RoleDynamique\RoleDatabaseController::class, 'exportTable'])->name('database.export-table');
    });

// CHEF D'EQUIPE & MEMBRE Routes retirés (remplacés par rôles dynamiques gérés via admin/permissions).

// SUPER ADMIN Routes
Route::prefix('super-admin')
    ->name('super-admin.')
    ->middleware(['auth', 'check.budget.completion'])
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'index'])->name('dashboard');

        // Budget Management
        Route::get('/budget', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'index'])->name('budget.index');
        Route::get('/budget/create', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'create'])->name('budget.create');
        Route::post('/budget', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'store'])->name('budget.store');
        Route::get('/budget/{budget}/edit', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'edit'])->name('budget.edit');
        Route::put('/budget/{budget}', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'update'])->name('budget.update');
        Route::post('/budget/assign', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'assignProjectBudget'])->name('budget.assign');
        Route::post('/budget/assign-st', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'assignSousTraitanceBudget'])->name('budget.assign-st');

        // Allocation par Projet (separate view)
        Route::get('/allocation-projet', [App\Http\Controllers\SuperAdmin\AllocationProjetsController::class, 'index'])->name('allocation-projet.index');
        Route::post('/allocation-projet', [App\Http\Controllers\SuperAdmin\AllocationProjetsController::class, 'store'])->name('allocation-projet.store');
        Route::delete('/allocation-projet/{id}', [App\Http\Controllers\SuperAdmin\AllocationProjetsController::class, 'destroy'])->name('allocation-projet.destroy');

        // Allocation par Sous-Traitance (separate view)
        Route::get('/allocation-sous-traitance', [App\Http\Controllers\SuperAdmin\AllocationSousTraitanceController::class, 'index'])->name('allocation-sous-traitance.index');
        Route::post('/allocation-sous-traitance', [App\Http\Controllers\SuperAdmin\AllocationSousTraitanceController::class, 'store'])->name('allocation-sous-traitance.store');
        Route::delete('/allocation-sous-traitance/{id}', [App\Http\Controllers\SuperAdmin\AllocationSousTraitanceController::class, 'destroy'])->name('allocation-sous-traitance.destroy');

        // Depenses (separate view)
        Route::get('/depenses', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'depenses'])->name('depenses.index');

        // Depense CRUD
        Route::post('/budget/depenses', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'storeDepense'])->name('budget.depenses.store');
        Route::put('/budget/depenses/{depense}', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'updateDepense'])->name('budget.depenses.update');
        Route::delete('/budget/depenses/{depense}', [App\Http\Controllers\SuperAdmin\SuperAdminBudgetController::class, 'destroyDepense'])->name('budget.depenses.destroy');

        Route::get('/rapports', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'rapports'])->name('rapports.index');
        Route::get('/rapports/{id}', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'showRapport'])->name('rapports.show');
        Route::put('/rapports/{id}/statut', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'updateStatutRapport'])->name('rapports.update-statut');
        Route::post('/rapports/{id}/envoyer-partenaire', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'envoyerRapportPartenaire'])->name('rapports.envoyer-partenaire');

        // Gestion des Rôles
        Route::resource('roles', App\Http\Controllers\SuperAdmin\SuperAdminRoleController::class);

        // Gestion des Utilisateurs (reset-password avant resource pour éviter conflit)
        Route::post('/users/{user}/reset-password', [App\Http\Controllers\SuperAdmin\SuperAdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::delete('/admins/{id}', [App\Http\Controllers\SuperAdmin\SuperAdminUserController::class, 'destroyAdmin'])->name('admins.destroy');
        Route::post('/admins/{id}/reset-password', [App\Http\Controllers\SuperAdmin\SuperAdminUserController::class, 'resetAdminPassword'])->name('admins.reset-password');
        Route::post('/partenaires/{partenaire}/reset-password', [App\Http\Controllers\SuperAdmin\SuperAdminPartenaireController::class, 'resetPassword'])->name('partenaires.reset-password');

        // Gestion des fonctionnalités des Admins Entreprise
        Route::get('/admins/{admin}/permissions', [App\Http\Controllers\SuperAdmin\SuperAdminUserController::class, 'editAdminPermissions'])->name('admins.permissions.edit');
        Route::post('/admins/{admin}/permissions', [App\Http\Controllers\SuperAdmin\SuperAdminUserController::class, 'updateAdminPermissions'])->name('admins.permissions.update');

        Route::resource('users', App\Http\Controllers\SuperAdmin\SuperAdminUserController::class);

        // Gestion des Permissions
        Route::post('/permissions/{role}/assign', [App\Http\Controllers\SuperAdmin\SuperAdminPermissionController::class, 'assignToRole'])->name('permissions.assign');
        Route::resource('permissions', App\Http\Controllers\SuperAdmin\SuperAdminPermissionController::class)->only(['index', 'store', 'show', 'update', 'destroy']);

        // Ressources globales (projets, taches, etc.)
        Route::resource('projets', App\Http\Controllers\SuperAdmin\SuperAdminProjetController::class);
        Route::resource('partenaires', App\Http\Controllers\SuperAdmin\SuperAdminPartenaireController::class);
        Route::resource('chef-projets', App\Http\Controllers\SuperAdmin\SuperAdminChefProjetController::class);
        Route::resource('chef-equipes', App\Http\Controllers\SuperAdmin\SuperAdminChefEquipeController::class);
        Route::resource('membres', App\Http\Controllers\SuperAdmin\SuperAdminMembreController::class);
        Route::resource('taches', App\Http\Controllers\SuperAdmin\SuperAdminTacheController::class);
        Route::resource('sous-taches', App\Http\Controllers\SuperAdmin\SuperAdminSousTacheController::class);
        Route::resource('phases', App\Http\Controllers\SuperAdmin\PhasesController::class);
        Route::resource('incidents', App\Http\Controllers\SuperAdmin\IncidentsController::class);
        Route::get('/equipes/export-pdf', [App\Http\Controllers\SuperAdmin\SuperAdminEquipeController::class, 'exportAllPdf'])->name('equipes.export-pdf');
        Route::resource('equipes', App\Http\Controllers\SuperAdmin\SuperAdminEquipeController::class);

        // Autres modules
        Route::get('/contrats/export-pdf', [App\Http\Controllers\SuperAdmin\ContratsController::class, 'exportAllPdf'])->name('contrats.export-pdf');
        Route::resource('contrats', App\Http\Controllers\SuperAdmin\ContratsController::class);
        Route::post('/contrats/{id}/envoyer-partenaire', [App\Http\Controllers\SuperAdmin\ContratsController::class, 'envoyerPartenaire'])->name('contrats.envoyer-partenaire');
        Route::resource('factures', App\Http\Controllers\SuperAdmin\FacturesController::class);
        Route::post('/factures/{id}/envoyer-partenaire', [App\Http\Controllers\SuperAdmin\FacturesController::class, 'envoyerPartenaire'])->name('factures.envoyer-partenaire');
        Route::resource('interventions', App\Http\Controllers\SuperAdmin\InterventionsController::class);
        Route::get('/fournisseurs/export', [App\Http\Controllers\SuperAdmin\FournisseursController::class, 'export'])->name('fournisseurs.export');
        Route::resource('fournisseurs', App\Http\Controllers\SuperAdmin\FournisseursController::class);
        Route::get('/stocks/export', [App\Http\Controllers\SuperAdmin\StocksController::class, 'export'])->name('stocks.export');
        Route::resource('stocks', App\Http\Controllers\SuperAdmin\StocksController::class);
        Route::resource('rendezvous', App\Http\Controllers\SuperAdmin\RendezvousController::class);
        Route::resource('documents', App\Http\Controllers\SuperAdmin\DocumentsController::class);
        Route::resource('satisfaction', App\Http\Controllers\SuperAdmin\SatisfactionController::class)->only(['index', 'show']);
        Route::resource('sous-traitances', App\Http\Controllers\SuperAdmin\SuperAdminSousTraitanceController::class);
        Route::get('/sous-traitances/{id}/pdf', [App\Http\Controllers\SuperAdmin\SuperAdminSousTraitanceController::class, 'exportPdf'])->name('sous-traitances.export-pdf');
        Route::get('/sous-traitances/export/all', [App\Http\Controllers\SuperAdmin\SuperAdminSousTraitanceController::class, 'exportAllPdf'])->name('sous-traitances.export');


        // Base de donnees
        Route::get('/database', [App\Http\Controllers\SuperAdmin\SuperAdminDatabaseController::class, 'index'])->name('database.index');
        Route::get('/database/backup', [App\Http\Controllers\SuperAdmin\SuperAdminDatabaseController::class, 'backup'])->name('database.backup');
        Route::post('/database/clear-table', [App\Http\Controllers\SuperAdmin\SuperAdminDatabaseController::class, 'clearTable'])->name('database.clear-table');
        Route::post('/database/clear-all', [App\Http\Controllers\SuperAdmin\SuperAdminDatabaseController::class, 'clearAll'])->name('database.clear-all');
        Route::get('/database/export-table', [App\Http\Controllers\SuperAdmin\SuperAdminDatabaseController::class, 'exportTable'])->name('database.export-table');

        // Profil
        Route::get('/parametres', [App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'show'])->name('parametres');
        Route::put('/parametres', [App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'update'])->name('parametres.update');
        Route::delete('/parametres/photo', [App\Http\Controllers\SuperAdmin\SuperAdminProfileController::class, 'resetPhoto'])->name('parametres.photo.destroy');

        // Chat
        Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/store', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');

        // IA Chat
        Route::get('/ia-chat', [App\Http\Controllers\IaChat\IaChatController::class, 'index'])->name('ia-chat.index');
        Route::post('/ia-chat/store', [App\Http\Controllers\IaChat\IaChatController::class, 'store'])->name('ia-chat.store');
        Route::post('/ia-chat/create', [App\Http\Controllers\IaChat\IaChatController::class, 'createConversation'])->name('ia-chat.create');
        Route::delete('/ia-chat/{id}', [App\Http\Controllers\IaChat\IaChatController::class, 'deleteConversation'])->name('ia-chat.destroy');

        Route::post('/export/pdf', [App\Http\Controllers\ExportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/pdf/{type}/{id}', [App\Http\Controllers\ExportController::class, 'directExportPdf'])->name('export.pdf.direct');
        Route::get('/export/users', [App\Http\Controllers\ExportController::class, 'exportUsersPdf'])->name('export.users.pdf');
        Route::post('/export/voir-pdf', [App\Http\Controllers\ExportController::class, 'voirPdf'])->name('export.voir-pdf');

        // Historique par année
        Route::get('/historique', [App\Http\Controllers\SuperAdmin\SuperAdminHistoriqueController::class, 'index'])->name('historique.index');
        Route::get('/historique/{annee}', [App\Http\Controllers\SuperAdmin\SuperAdminHistoriqueController::class, 'show'])->name('historique.show');
        Route::get('/historique/{annee}/pdf', [App\Http\Controllers\SuperAdmin\SuperAdminHistoriqueController::class, 'exportPdf'])->name('historique.pdf');
        Route::get('/historique/{annee}/voir-pdf', [App\Http\Controllers\SuperAdmin\SuperAdminHistoriqueController::class, 'voirPdf'])->name('historique.voir-pdf');

        // Configuration Système
        Route::get('/configuration', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'configuration'])->name('configuration.index');
        Route::post('/configuration/maintenance', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'toggleMaintenance'])->name('configuration.maintenance.toggle');
        Route::get('/configuration/logs', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'viewLogs'])->name('configuration.logs');
        Route::post('/configuration/logs/clear', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'clearLogs'])->name('configuration.logs.clear');
        Route::get('/configuration/logs/export-pdf', [App\Http\Controllers\SuperAdmin\SuperAdminDashboardController::class, 'exportLogsPdf'])->name('configuration.logs.export-pdf');
    });

// ADMIN ENTREPRISE Routes
Route::prefix('admin-entreprise')
    ->name('admin-entreprise.')
    ->middleware(['auth', 'check.budget.completion'])
    ->group(function () {
        Route::get('/dashboard', function () {
            session(['admin_entreprise_session' => true]);
            return redirect()->route('role-dynamique.dashboard');
        })->name('dashboard');

        // Alias pour toutes les routes role-dynamique
        Route::get('/projets', fn() => redirect()->route('role-dynamique.projets.index'))->name('projets.index');
        Route::get('/projets/create', fn() => redirect()->route('role-dynamique.projets.create'))->name('projets.create');
        Route::get('/phases', fn() => redirect()->route('role-dynamique.phases.index'))->name('phases.index');
        Route::get('/taches', fn() => redirect()->route('role-dynamique.taches.index'))->name('taches.index');
        Route::get('/sous-taches', fn() => redirect()->route('role-dynamique.sous-taches.index'))->name('sous-taches.index');
        Route::get('/rapports', fn() => redirect()->route('role-dynamique.rapports.index'))->name('rapports.index');
        Route::get('/incidents', fn() => redirect()->route('role-dynamique.incidents.index'))->name('incidents.index');
        Route::get('/roles', fn() => redirect()->route('role-dynamique.roles.index'))->name('roles.index');
        Route::get('/users', fn() => redirect()->route('role-dynamique.users.index'))->name('users.index');
        Route::get('/equipes', fn() => redirect()->route('role-dynamique.equipes.index'))->name('equipes.index');
        Route::get('/partenaires', fn() => redirect()->route('role-dynamique.partenaires.index'))->name('partenaires.index');
        Route::get('/contrats', fn() => redirect()->route('role-dynamique.contrats.index'))->name('contrats.index');
        Route::get('/factures', fn() => redirect()->route('role-dynamique.factures.index'))->name('factures.index');
        Route::get('/budget', fn() => redirect()->route('role-dynamique.budget.index'))->name('budget.index');
        Route::get('/stocks', fn() => redirect()->route('role-dynamique.stocks.index'))->name('stocks.index');
        Route::get('/fournisseurs', fn() => redirect()->route('role-dynamique.fournisseurs.index'))->name('fournisseurs.index');
        Route::get('/documents', fn() => redirect()->route('role-dynamique.documents.index'))->name('documents.index');
        Route::get('/parametres', fn() => redirect()->route('role-dynamique.parametres'))->name('parametres');
        Route::get('/chat', fn() => redirect()->route('role-dynamique.chat.index'))->name('chat.index');
        Route::get('/ia-chat', fn() => redirect()->route('role-dynamique.ia-chat.index'))->name('ia-chat.index');
    });

// CLIENT Routes
Route::prefix('partenaire')
    ->name('partenaire.')
    ->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'index'])->name('dashboard');
        Route::get('/equipe', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'equipe'])->name('equipe');
        Route::get('/rapports', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'rapports'])->name('rapports');
        Route::get('/factures', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'factures'])->name('factures');

        Route::post('/export/pdf', [App\Http\Controllers\ExportController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/pdf/{type}/{id}', [App\Http\Controllers\ExportController::class, 'directExportPdf'])->name('export.pdf.direct');
        Route::get('/export/users', [App\Http\Controllers\ExportController::class, 'exportUsersPdf'])->name('export.users.pdf');
        Route::post('/export/voir-pdf', [App\Http\Controllers\ExportController::class, 'voirPdf'])->name('export.voir-pdf');

        // Rapports PDF
        Route::get('/rapports/{id}/pdf', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'telechargerPdf'])->name('rapports.pdf');
        Route::get('/rapports/{id}/voir-pdf', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'voirPdf'])->name('rapports.voir-pdf');

        // Mon Profil
        Route::get('/parametres', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'profil'])->name('parametres');
        Route::put('/parametres', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'updateProfil'])->name('parametres.update');
        Route::delete('/parametres/photo', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'destroyPhoto'])->name('parametres.photo.destroy');

        // Chat
        Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
        Route::post('/chat/store', [App\Http\Controllers\ChatController::class, 'store'])->name('chat.store');

        // Satisfaction
        Route::post('/satisfaction', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'storeSatisfaction'])->name('satisfaction.store');
        Route::put('/satisfaction/{id}', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'updateSatisfaction'])->name('satisfaction.update');
        Route::delete('/satisfaction/{id}', [App\Http\Controllers\Partenaire\PartenaireDashboardController::class, 'destroySatisfaction'])->name('satisfaction.destroy');
    });
