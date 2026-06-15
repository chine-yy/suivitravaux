<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Equipe;
use App\Models\Projet;
use App\Models\User;
use App\Models\Role;
use App\Services\EquipeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SuperAdminEquipeController extends Controller
{
    protected $equipeService;

    public function __construct(EquipeService $equipeService)
    {
        $this->middleware(['auth']);
        $this->equipeService = $equipeService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['projet_id', 'search']);
        $equipes = $this->equipeService->getTeams($filters);
        $projets = Projet::all();
        
        return view('super-admin.equipes.index', compact('equipes', 'projets'));
    }

    public function show($id)
    {
        $equipe = Equipe::with(['projet', 'users', 'role', 'chef'])->findOrFail($id);
        return view('super-admin.equipes.show', compact('equipe'));
    }

    public function create()
    {
        $projets = Projet::where('statut', '!=', 'termine')
            ->get();
        $users = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();
        $roles = Role::whereNotIn('nom', ['Administration', 'Super Admin', 'Partenaire'])->get();
        return view('super-admin.equipes.create', compact('projets', 'users', 'roles'));
    }

    public function store(Request $request)
    {
        try {
            $this->equipeService->createTeam($request->all());
            return redirect()->route('super-admin.equipes.index')
                ->with('success', 'L\'équipe a été créée avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function edit($id)
    {
        $equipe = Equipe::with('users')->findOrFail($id);
        $projets = Projet::where(function ($query) use ($equipe) {
                $query->doesntHave('equipes')->orWhere('id', $equipe->projet_id);
            })
            ->where('statut', '!=', 'termine')
            ->get();
        $users = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();
        $roles = Role::whereNotIn('nom', ['Administration', 'Super Admin', 'Partenaire'])->get();
        return view('super-admin.equipes.edit', compact('equipe', 'projets', 'users', 'roles'));
    }

    public function update(Request $request, $id)
    {
        try {
            $equipe = Equipe::findOrFail($id);
            $this->equipeService->updateTeam($equipe, $request->all());
            return redirect()->route('super-admin.equipes.index')
                ->with('success', 'L\'équipe a été modifiée avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function destroy($id)
    {
        $equipe = Equipe::findOrFail($id);
        $this->equipeService = $equipeService ?? app(EquipeService::class);
        $this->equipeService->deleteTeam($equipe);
        return redirect()->route('super-admin.equipes.index')
            ->with('success', 'L\'équipe a été supprimée avec succès.');
    }

    public function exportAllPdf(Request $request)
    {
        $filters = $request->only(['projet_id', 'search']);
        $equipes = $this->equipeService->getTeams($filters);
        
        $pdf = \PDF::loadView('partials.pdf-equipes', compact('equipes'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('equipes_' . date('Y-m-d') . '.pdf');
    }
}
