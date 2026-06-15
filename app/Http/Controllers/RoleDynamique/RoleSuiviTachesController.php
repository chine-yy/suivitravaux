<?php

namespace App\Http\Controllers\RoleDynamique;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleSuiviTachesController extends Controller
{
    public function index()
    {
        return view('role-dynamique.suivi-taches.index');
    }

    public function create() { return redirect()->back(); }
    public function store(Request $request) { return redirect()->back(); }
    public function show($id) { return redirect()->back(); }
    public function edit($id) { return redirect()->back(); }
    public function update(Request $request, $id) { return redirect()->back(); }
    public function destroy($id) { return redirect()->back(); }
}
