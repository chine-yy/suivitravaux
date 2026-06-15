<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Satisfaction;
use App\Models\Projet;
use Illuminate\Http\Request;

class SatisfactionController extends Controller
{
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
        $partenaires = \App\Models\User::where('type_compte', 'partenaire')->orderBy('name')->get();

        return view('super-admin.satisfaction.index', compact('satisfactions', 'partenaires'));
    }

    public function show($id)
    {
        $satisfaction = Satisfaction::with(['partenaire', 'projet'])->findOrFail($id);
        return view('super-admin.satisfaction.show', compact('satisfaction'));
    }
}
