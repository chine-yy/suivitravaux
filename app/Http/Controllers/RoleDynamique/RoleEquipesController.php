<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Equipe;
use App\Models\Projet;
use App\Models\User;
use App\Services\EquipeService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RoleEquipesController extends Controller
{
    protected $equipeService;

    public function __construct(EquipeService $equipeService)
    {
        $this->middleware(['auth']);
        $this->equipeService = $equipeService;
    }

    public function index(Request $request)
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403, 'Action non autorisée.');
        }

        $filters = $request->only(['projet_id', 'search']);
        $equipes = $this->equipeService->getTeams($filters);
        $projets = Projet::all();

        return view('role-dynamique.equipes.index', compact('equipes', 'projets'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403);
        }

        $projets = Projet::where('statut', '!=', 'termine')
            ->get();
        $users = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();

        return view('role-dynamique.equipes.create', compact('projets', 'users'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403);
        }

        try {
            $this->equipeService->createTeam($request->all());
            return redirect()->route('role-dynamique.equipes.index')
                ->with('success', 'L\'équipe a été créée avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }
    }

    public function show($id)
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403);
        }

        $equipe = Equipe::with('users', 'projet', 'role')->findOrFail($id);

        return view('role-dynamique.equipes.show', compact('equipe'));
    }

    public function edit($id)
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403);
        }

        $equipe = Equipe::with('users')->findOrFail($id);

        $projets = Projet::where(function ($query) use ($equipe) {
                $query->doesntHave('equipes')->orWhere('id', $equipe->projet_id);
            })
            ->where('statut', '!=', 'termine')
            ->get();
        $users = User::with('role')->nonSuperAdmins()->nonPartenaires()->get();

        return view('role-dynamique.equipes.edit', compact('equipe', 'projets', 'users'));
    }

    public function update(Request $request, $id)
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403);
        }

        try {
            $equipe = Equipe::findOrFail($id);
            $this->equipeService->updateTeam($equipe, $request->all());
            return redirect()->route('role-dynamique.equipes.index')
                ->with('success', 'L\'équipe a été modifiée avec succès.');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403);
        }

        try {
            $equipe = Equipe::findOrFail($id);
            $this->equipeService->deleteTeam($equipe);
            return redirect()->route('role-dynamique.equipes.index')
                ->with('success', 'L\'équipe a été supprimée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function exportAllPdf(Request $request)
    {
        if (!auth()->user()->hasPermission('view-equipes')) {
            abort(403);
        }

        $filters = $request->only(['projet_id', 'search']);
        $equipes = $this->equipeService->getTeams($filters);

        $pdf = \PDF::loadView('partials.pdf-equipes', compact('equipes'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('equipes_' . date('Y-m-d') . '.pdf');
    }
}
