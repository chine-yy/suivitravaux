<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Entreprise;
use App\Models\Role;
use App\Services\PhpMailerService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     */
    public function showRegistrationForm()
    {
        if (Entreprise::hasRegisteredAccount()) {
            return redirect()->route('login')->with('error', 'Un compte entreprise existe deja.');
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request to the application.
     * Creates both an entreprise and its administrator with proper relationship.
     * Sends a confirmation email - if email fails, registration is rolled back.
     */
    public function register(Request $request)
    {
        if (Entreprise::hasRegisteredAccount()) {
            return redirect()->route('login')->with('error', 'Un compte entreprise existe deja.');
        }

        // Validate all form fields
        $validated = $request->validate([
            // User fields
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

            // Entreprise fields
            'company_name' => ['required', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:20'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'industry' => ['nullable', 'string', 'max:255'],
            'custom_industry' => ['nullable', 'string', 'max:255'],
            'siret' => ['nullable', 'string', 'max:50'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['nullable', 'string', 'max:255'],
            'site_web' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            // Administrator fields
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
            'accept_terms' => ['accepted'],
        ], [
            // Custom validation messages in French
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez saisir une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée. Veuillez en utiliser une autre.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'company_name.required' => 'Le nom de l\'entreprise est obligatoire.',
            'first_name.required' => 'Le prénom de l\'administrateur est obligatoire.',
            'last_name.required' => 'Le nom de l\'administrateur est obligatoire.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.mimes' => 'Le fichier doit être au format JPEG, PNG, JPG ou GIF.',
            'photo.max' => 'Le fichier ne doit pas dépasser 10 Mo.',
            'photo.uploaded' => 'Le téléchargement de la photo a échoué. Veuillez vérifier que sa taille ne dépasse pas 10 Mo.',
            'accept_terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
            'company_phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'company_address.max' => 'L\'adresse ne peut pas dépasser 500 caractères.',
            'siret.max' => 'Le numéro SIRET/RC ne peut pas dépasser 50 caractères.',
        ]);

        // Check if email already exists in users table
        $emailExistsInUsers = User::where('email', $validated['email'])->exists();
        if ($emailExistsInUsers) {
            return back()->withErrors([
                'email' => 'Cette adresse email est déjà utilisée. Veuillez en utiliser une autre.'
            ])->withInput();
        }

        // Check if company name already exists
        $companyExists = Entreprise::where('nom_entreprise', $validated['company_name'])->exists();
        if ($companyExists) {
            return back()->withErrors([
                'company_name' => 'Une entreprise avec ce nom existe déjà. Veuillez choisir un autre nom.'
            ])->withInput();
        }

        try {
            // Step 1: Send confirmation email FIRST (before saving to DB)
            $mailer = app(PhpMailerService::class);
            $emailBody = View::make('emails.welcome', [
                'userName' => $validated['first_name'] . ' ' . $validated['last_name'],
                'companyName' => $validated['company_name'],
                'email' => $validated['email'],
            ])->render();

            $emailSent = $mailer->send([
                'to' => $validated['email'],
                'to_name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'subject' => 'Bienvenue sur ' . config('app.name') . ' - Votre compte a été créé en tant qu\'administrateur',
                'body' => $emailBody,
                'alt_body' => 'Bienvenue sur ' . config('app.name') . ' ! Votre compte a été créé avec succès. Connectez-vous en tant qu\'administrateur sur ' . url('/login'),
                'is_html' => true,
            ]);

            // If email sending fails, do NOT save to database
            if (!$emailSent) {
                return back()->with('error', 'Impossible d\'envoyer l\'email de confirmation à l\'adresse fournie. Veuillez vérifier votre adresse email et réessayer.')->withInput();
            }

            // Step 2: Email sent successfully, now save to database
            $result = DB::transaction(function () use ($validated, $request) {
                // Create the entreprise
                $industryValue = $validated['industry'] === 'autre'
                    ? ($validated['custom_industry'] ?? null)
                    : ($validated['industry'] ?? null);

                $entreprise = Entreprise::create([
                    'id_entreprise' => Entreprise::generateEntrepriseId(),
                    'nom_entreprise' => $validated['company_name'],
                    'adresse' => $validated['company_address'] ?? null,
                    'telephone' => $validated['company_phone'] ?? null,
                    'email' => $validated['email'],
                    'ville' => $validated['ville'] ?? null,
                    'pays' => $validated['pays'] ?? null,
                    'site_web' => $validated['site_web'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'industry' => $industryValue,
                    'statut' => true,
                ]);

                // Handle photo upload
                $photoPath = null;
                if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                    $photo = $request->file('photo');
                    $extension = $photo->getClientOriginalExtension();
                    $photoName = 'admin_' . $entreprise->id . '_' . time() . '.' . $extension;

                    // Store in storage/app/public/uploads/profil-images
                    $path = $photo->storeAs('uploads/profil-images', $photoName, 'public');
                    $photoPath = 'uploads/profil-images/' . $photoName;
                }

                // Create "Administrateur Entreprise" role without permissions
                // L'admin doit configure les permissions manuellement via le panneau d'admin
                $role = Role::create([
                    'nom' => 'Administrateur Entreprise',
                    'slug' => Str::slug('Administrateur Entreprise-' . $entreprise->id),
                    'statut' => true
                ]);

                // Ne pas attribuer de permissions par défaut - l'admin décidera lui-même

                // Create the user linked to the entreprise and role
                $user = User::create([
                    'entreprise_id' => $entreprise->id,
                    'role_id' => $role->id,
                    'name' => $validated['last_name'],
                    'prenom' => $validated['first_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'telephone' => $validated['phone'] ?? null,
                    'photo' => $photoPath,
                    'is_active' => true,
                ]);

                return [
                    'entreprise' => $entreprise,
                    'user' => $user
                ];
            });

            // Redirect to login after successful registration
            return redirect()->route('login')->with('success', 'Inscription réussie ! Un email de confirmation a été envoyé à ' . $validated['email'] . '. Vous pouvez maintenant vous connecter.');

        }
        catch (\Illuminate\Database\QueryException $e) {
            Log::error('Erreur de base de données lors de l\'inscription: ' . $e->getMessage());

            $errorMessage = 'Une erreur de base de données est survenue. Veuillez vérifier vos données et réessayer.';
            if (app()->hasDebugModeEnabled() && app()->environment('local')) {
                $errorMessage .= ' Détail: ' . $e->getMessage();
            }
            return back()->with('error', $errorMessage)->withInput();
        }
        catch (\Exception $e) {
            Log::error('Erreur lors de l\'inscription: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            $errorMessage = 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.';
            if (app()->hasDebugModeEnabled() && app()->environment('local')) {
                $errorMessage .= ' Détail: ' . $e->getMessage();
            }
            return back()->with('error', $errorMessage)->withInput();
        }
    }
}
