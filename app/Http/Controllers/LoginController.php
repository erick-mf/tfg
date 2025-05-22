<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Login');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|size:6|alpha_num:ascii',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.size' => 'La longitud de la contraseña no es correcta',
            'password.alpha_num' => 'La contraseña debe ser alfanumérica',
        ]);

        $validated['password'] = strtoupper(trim($validated['password']));
        if ($validated['password'] == '12345A') {
            return redirect()->route('admin.dashboard')->with('toast', ['type' => 'success', 'message' => 'Bienvenido']);
        } else {
            return back()->withErrors(['password' => 'La contraseña es incorrecta'])->withInput($request->only('password'));
        }
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
