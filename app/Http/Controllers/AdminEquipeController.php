<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Equipe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminEquipeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index(Request $request)
    {
        $entrepriseId = auth()->user()->entreprise_id;
        $activeTab = $request->get('tab', 'actives');

        $equipesActives = Equipe::where('entreprise_id', $entrepriseId)
                                ->whereNull('deleted_at')
                                ->with('membres')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        $equipesSupprimees = Equipe::onlyTrashed()
                                   ->where('entreprise_id', $entrepriseId)
                                   ->with('membres')
                                   ->orderBy('deleted_at', 'desc')
                                   ->get();

        return view('admin.equipes.index', compact('activeTab', 'equipesActives', 'equipesSupprimees'));
    }

    public function create()
    {
        $entrepriseId = auth()->user()->entreprise_id;
        $membres = User::with('role')->where('entreprise_id', $entrepriseId)->nonSuperAdmins()->get();
        return view('admin.equipes.create', compact('membres'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'membres' => 'nullable|array',
        ]);

        $equipe = Equipe::create([
            'entreprise_id' => auth()->user()->entreprise_id,
            'nom' => $request->nom,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        if ($request->membres) {
            $equipe->membres()->attach($request->membres);
        }

        return redirect()->route('admin.equipes.index')->with('success', 'Équipe créée avec succès');
    }

    public function show($id)
    {
        $equipe = Equipe::withTrashed()->findOrFail($id);
        return view('admin.equipes.show', compact('equipe'));
    }

    public function edit($id)
    {
        $equipe = Equipe::findOrFail($id);
        $membres = User::with('role')->where('entreprise_id', auth()->user()->entreprise_id)->nonSuperAdmins()->get();
        return view('admin.equipes.edit', compact('equipe', 'membres'));
    }

    public function update(Request $request, $id)
    {
        $equipe = Equipe::findOrFail($id);
        
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'membres' => 'nullable|array',
        ]);

        $equipe->update([
            'nom' => $request->nom,
            'description' => $request->description,
        ]);

        if ($request->has('membres')) {
            $equipe->membres()->sync($request->membres);
        }

        return redirect()->route('admin.equipes.index')->with('success', 'Équipe mise à jour');
    }

    public function destroy($id)
    {
        $equipe = Equipe::findOrFail($id);
        $equipe->delete();
        return redirect()->route('admin.equipes.index')->with('success', 'Équipe supprimée (soft delete)');
    }

    public function restore($id)
    {
        $equipe = Equipe::withTrashed()->findOrFail($id);
        $equipe->restore();
        return redirect()->route('admin.equipes.index')->with('success', 'Équipe restaurée');
    }

    public function forceDelete($id)
    {
        $equipe = Equipe::withTrashed()->findOrFail($id);
        $equipe->forceDelete();
        return redirect()->route('admin.equipes.index')->with('success', 'Équipe définitivement supprimée');
    }
}
