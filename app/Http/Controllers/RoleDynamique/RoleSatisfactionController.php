<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use App\Models\Satisfaction;
use App\Models\User;
use App\Models\Projet;
use Illuminate\Http\Request;

class RoleSatisfactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Satisfaction::with(['partenaire', 'projet']);

        if ($request->filled('partenaire_id')) {
            $query->where('partenaire_id', $request->partenaire_id);
        }

        if ($request->filled('note')) {
            $query->where('note', $request->note);
        }

        $satisfactions = $query->latest()->get();
        $partenaires = User::where('type_compte', 'partenaire')->orderBy('name')->get();

        return view('role-dynamique.satisfaction.index', compact('satisfactions', 'partenaires'));
    }

    public function show($id)
    {
        $satisfaction = Satisfaction::with(['partenaire', 'projet'])->findOrFail($id);
        return view('role-dynamique.satisfaction.show', compact('satisfaction'));
    }
}
