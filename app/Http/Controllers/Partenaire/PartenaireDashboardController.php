<?php

namespace App\Http\Controllers\Partenaire;

use App\Http\Controllers\Controller;
use App\Models\Partenaire;
use App\Models\Projet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Rapport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PartenaireDashboardController extends Controller
{
    public function index()
    {
        $partenaireId = auth()->id();
        $partenaire = \App\Models\User::with('projet')->findOrFail($partenaireId);
        $projet = $this->getProjetForPartenaire($partenaire);

        if (!$projet) {
            abort(403, 'Aucun projet assigné pour le moment.');
        }

        // Latest reports for dashboard
        $partenaire_rapports = \App\Models\Rapport::where('projet_id', $projet->id)
            ->latest()
            ->take(5)
            ->get();

        // Statistics
        $totalPersonnes = 1; // Chef de Projet (admin)
        $totalPersonnes += $projet->membresCount();

        $totalPartenaires = \App\Models\User::where('projet_id', $projet->id)
            ->whereHas('role', fn($q) => $q->where('nom', 'Partenaire'))
            ->count();

        // Advancement statistics
        $statsTaches = [
            'total' => $projet->taches()->count(),
            'terminees' => $projet->taches()->where('statut', 'terminee')->count(),
            'en_cours' => $projet->taches()->where('statut', 'en_cours')->count(),
            'a_faire' => $projet->taches()->where('statut', 'a_faire')->count(),
            'bloquees' => $projet->taches()->where('statut', 'bloquee')->count(),
        ];

        // Data for graphs
        $phasedata = $projet->phases()->withCount('taches')->get();
        $labels = $phasedata->pluck('nom');
        $data = $phasedata->pluck('avancement');

        $existingSatisfaction = \App\Models\Satisfaction::where('partenaire_id', $partenaireId)
            ->where('projet_id', $projet->id)
            ->first();

        return view('partenaire.dashboard', compact(
            'partenaire',
            'projet',
            'totalPersonnes',
            'totalPartenaires',
            'statsTaches',
            'labels',
            'data',
            'partenaire_rapports',
            'existingSatisfaction'
        ));
    }

    public function equipe()
    {
        $partenaireId = auth()->id();
        $partenaire = \App\Models\User::findOrFail($partenaireId);
        $projet = $this->getProjetForPartenaire($partenaire);

        if (!$projet) {
            abort(403, 'Aucun projet assigné.');
        }

        $projet->load('equipes');

        return view('partenaire.equipe.equipe', compact('partenaire', 'projet'));
    }

    public function rapports()
    {
        $partenaireId = auth()->id();
        $partenaire = \App\Models\User::findOrFail($partenaireId);
        $projet = $this->getProjetForPartenaire($partenaire);

        if (!$projet) {
            abort(403, 'Aucun projet assigné.');
        }

        $rapports = \App\Models\Rapport::where('projet_id', $projet->id)
            ->with(['auteur.role', 'envoyePar.role'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('partenaire.rapport.rapports', compact('partenaire', 'rapports'));
    }

    public function factures()
    {
        $partenaireId = auth()->id();
        $partenaire = \App\Models\User::findOrFail($partenaireId);
        $projet = $this->getProjetForPartenaire($partenaire);

        if (!$projet) {
            abort(403, 'Aucun projet assigné.');
        }

        $factures = \App\Models\Facture::where('partenaire_id', $partenaireId)
            ->orWhere('projet_id', $projet->id)
            ->orderBy('date_emission', 'desc')
            ->get();

        return view('partenaire.facture.factures', compact('partenaire', 'factures', 'projet'));
    }

    public function profil()
    {
        $partenaire = \App\Models\User::findOrFail(auth()->id());
        return view('partenaire.profil.index', compact('partenaire'));
    }

    public function updateProfil(Request $request)
    {
        $partenaireId = auth()->id();
        $partenaire = \App\Models\User::findOrFail($partenaireId);

        $request->validate([
            'name' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $partenaireId,
            'telephone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'email.unique' => 'Cette adresse email est déjà utilisée par un autre compte.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'photo.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        $data = $request->only(['name', 'prenom', 'email', 'telephone']);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $photo = $request->file('photo');

            // Delete old photo if exists
            if ($partenaire->photo) {
                $oldPhotoPath = $partenaire->photo;
                if (strpos($oldPhotoPath, 'uploads/profil-images/') !== 0) {
                    $oldPhotoPath = 'uploads/profil-images/' . $oldPhotoPath;
                }

                // Delete from storage
                Storage::disk('public')->delete($oldPhotoPath);

                // Also check and delete from public folder if exists
                if (file_exists(public_path($oldPhotoPath))) {
                    @unlink(public_path($oldPhotoPath));
                }
            }

            $filename = \Illuminate\Support\Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('uploads/profil-images', $filename, 'public');
            $data['photo'] = $photoPath;
        }

        // Handle password update
        $passwordChanged = $request->filled('password');
        if ($passwordChanged) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $partenaire->update(['password' => bcrypt($request->password)]);
        }

        $photoChanged = isset($data['photo']);
        $partenaire->update($data);

        if ($passwordChanged) {
            $message = 'Mot de passe mis à jour avec succès.';
        } elseif ($photoChanged) {
            $message = 'Photo de profil mise à jour avec succès.';
        } else {
            $message = 'Vos informations ont été enregistrées avec succès.';
        }

        return redirect()->route('partenaire.parametres')->with('success', $message);
    }

    public function destroyPhoto()
    {
        $partenaire = \App\Models\User::findOrFail(auth()->id());

        if ($partenaire->photo) {
            $oldPhotoPath = $partenaire->photo;
            if (strpos($oldPhotoPath, 'uploads/profil-images/') !== 0) {
                $oldPhotoPath = 'uploads/profil-images/' . $oldPhotoPath;
            }

            Storage::disk('public')->delete($oldPhotoPath);

            if (file_exists(public_path($oldPhotoPath))) {
                @unlink(public_path($oldPhotoPath));
            }
        }

        $partenaire->update(['photo' => null]);

        return redirect()->route('partenaire.parametres')->with('success', 'Photo de profil supprimée avec succès.');
    }

    /**
     * View report PDF in browser.
     */
    public function voirPdf($id)
    {
        $user = auth()->user();
        $projet = $this->getProjetForPartenaire($user);

        if (!$projet) {
            abort(403, 'Projet non trouvé.');
        }

        $rapport = Rapport::with(['projet', 'auteur.role'])
            ->where('projet_id', $projet->id)
            ->findOrFail($id);

        $pdf = Pdf::loadView('partials.pdf-rapport', compact('rapport'));
        return $pdf->stream();
    }

    /**
     * Download report PDF.
     */
    public function telechargerPdf($id)
    {
        $partenaireId = auth()->id();
        $partenaire = \App\Models\User::findOrFail($partenaireId);
        $projet = $this->getProjetForPartenaire($partenaire);

        if (!$projet) {
            abort(403, 'Projet non trouvé.');
        }

        $rapport = Rapport::where('projet_id', $projet->id)->with(['projet', 'auteur.role'])->findOrFail($id);

        $pdf = Pdf::loadView('partials.pdf-rapport', compact('rapport'));

        $filename = 'rapport_' . str_replace(' ', '_', $rapport->projet->nom ?? 'projet') . '_' . Carbon::parse($rapport->created_at)->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }
    /**
     * Store satisfaction rating from partenaire.
     */
    public function storeSatisfaction(Request $request)
    {
        $partenaireId = auth()->id();
        $partenaire = \App\Models\User::findOrFail($partenaireId);
        $projet = $this->getProjetForPartenaire($partenaire);

        if (!$projet) {
            return redirect()->back()->with('error', 'Aucun projet assigné.');
        }

        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        \App\Models\Satisfaction::create([
            'partenaire_id' => $partenaireId,
            'projet_id' => $projet->id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date_envoi' => now(),
            'date_reponse' => now(),
            'statut' => 'repondu',
        ]);

        return redirect()->back()->with('success', 'Merci pour votre retour ! Votre avis nous aide à nous améliorer.');
    }

    private function getProjetForPartenaire($user)
    {
        if ($user->projet_id) {
            return $user->projet;
        }

        return \App\Models\Projet::where('partenaire_id', $user->id)
            ->orWhereHas('partenaires', fn($q) => $q->where('user_id', $user->id))
            ->first();
    }

    /**
     * Update satisfaction rating from partenaire.
     */
    public function updateSatisfaction(Request $request, $id)
    {
        $partenaireId = auth()->id();

        $satisfaction = \App\Models\Satisfaction::where('id', $id)
            ->where('partenaire_id', $partenaireId)
            ->firstOrFail();

        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $satisfaction->update([
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date_reponse' => now(),
        ]);

        return redirect()->back()->with('success', 'Votre avis a été mis à jour avec succès.');
    }

    /**
     * Delete partenaire satisfaction.
     */
    public function destroySatisfaction($id)
    {
        $partenaireId = auth()->id();

        $satisfaction = \App\Models\Satisfaction::where('id', $id)
            ->where('partenaire_id', $partenaireId)
            ->firstOrFail();

        $satisfaction->delete();

        return redirect()->back()->with('success', 'Votre avis a été supprimé.');
    }
}
