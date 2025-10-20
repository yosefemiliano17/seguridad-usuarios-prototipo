<?php

namespace App\Http\Controllers;

use App\Dominio\Modelo; 
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException; 
use App\Dominio\UsuarioDominio;
use Illuminate\Routing\Controller;


class ControladorIniciarSesionLayer extends Controller
{

    private $modeloUsuarios;
    public function __construct(Modelo $modelo)
    {
        $this->modeloUsuarios = $modelo;
    }
    public function create()
    {
        return view('welcome'); 
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'nip' => ['required', 'string'],
        ]);

        try {
            //crear objeto del dominio con los datos del request
            $usuarioDominio = new UsuarioDominio($credentials['email'], $credentials['nip'],false,0,null);
            $usuario = $this->modeloUsuarios->iniciarSesion($usuarioDominio);

            $request->session()->put('usuario', [
                'email' => $usuario->getEmail(),
                'id' => method_exists($usuario, 'getId') ? $usuario->getId() : null,
            ]);
            $request->session()->regenerate();
            // ---------------------------------------------

            return redirect()->route('dashboard')
                ->with('success', '¡Bienvenido de nuevo, ' . $usuario->getEmail() . '!');

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => $e->getMessage(),
            ]);
        }
    }
    
    public function destroy(Request $request) {
        $usuarioSesion = $request->session()->get('usuario');
        if ($usuarioSesion && isset($usuarioSesion['email'])) {
            $usuarioDominio = new UsuarioDominio($usuarioSesion['email'], '', false, 0, null);
            $this->modeloUsuarios->cerrarSesion($usuarioDominio);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Has cerrado sesión correctamente.');
    }

    public function dashboard() {
        if (!session('usuario')) {
            return redirect()->route('login')->with('error', 'Necesitas iniciar sesión');
        }
        return view('dashboard');
    }
}