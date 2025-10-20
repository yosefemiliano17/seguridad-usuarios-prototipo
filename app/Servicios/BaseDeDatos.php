<?php

namespace App\Servicios;

use App\Models\User;
use App\Dominio\UsuarioDominio;
use Illuminate\Support\Facades\DB;

class BaseDeDatos {

    private static ?BaseDeDatos $instancia = null;

    private function __construct() {}

    public static function getInstancia(): BaseDeDatos{
        if (self::$instancia === null) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    public function actualizarValores(UsuarioDominio $usuarioDominio) : void {
        $usuarioModelo = User::where('email', $usuarioDominio->getEmail())->lockForUpdate()->first();
        if ($usuarioModelo !== null) {
            $usuarioModelo->session_status = $usuarioDominio->isStatus();
            $usuarioModelo->failure_attempts = $usuarioDominio->getFailedAttempts();
            $usuarioModelo->lock_until = $usuarioDominio->getLockUntil();
            $usuarioModelo->save();
        }
    }
    public function obtenerUsuario(UsuarioDominio $usuarioDominio) : ?UsuarioDominio {
        $usuario = User::where('email', $usuarioDominio->getEmail())->lockForUpdate()->first();
        if ($usuario) {
            return new UsuarioDominio(
                $usuario->email,
                $usuario->password,
                $usuario->session_status,
                $usuario->failure_attempts,
                $usuario->lock_until
            );
        }
        return null;
    }
    public function crearUsuario(UsuarioDominio $usuarioDominio) : void {
        $usuarioModelo = new User();
        $usuarioModelo->email = $usuarioDominio->getEmail();
        $usuarioModelo->password = $usuarioDominio->getPassword();
        $usuarioModelo->session_status = $usuarioDominio->isStatus();
        $usuarioModelo->failure_attempts = $usuarioDominio->getFailedAttempts();
        $usuarioModelo->lock_until = $usuarioDominio->getLockUntil();
        $usuarioModelo->save();
    }
    public function beginTransaction(): void {
        DB::beginTransaction();
    }
    public function commit(): void {
        DB::commit();
    }
    public function rollBack(): void {
        DB::rollBack(); 
    }

    
}