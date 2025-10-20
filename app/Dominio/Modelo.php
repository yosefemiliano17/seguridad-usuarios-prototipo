<?php

namespace App\Dominio;

use App\Servicios\BaseDeDatos;
use Illuminate\Support\Facades\Hash;

class Modelo {

    public function __construct() {}

    public function iniciarSesion(UsuarioDominio $usuario) : UsuarioDominio {

        $usuarioDominio = BaseDeDatos::getInstancia()->obtenerUsuario($usuario);
        if ($usuarioDominio === null) {
            throw new \Exception(message: "Usuario no encontrado");
        }

        //dd($usuarioDominio);
        
               //throw new \Exception(message: "El correo no existe");
            //creamos usarios opcionalmente por ahora para verificaciones
            /*$usuarioNuevo= new UsuarioDominio(
                email: $usuario->getEmail(),
                password: Hash::make($usuario->getPassword()),
                status: false,
                failedAttempts: 0,
                lockUntil: null
            );
            BaseDeDatos::getInstancia()->crearUsuario($usuarioNuevo);
            return $usuarioNuevo;*/
            

        BaseDeDatos::getInstancia()->beginTransaction();
        try {
            $usuarioDominio = BaseDeDatos::getInstancia()->obtenerUsuario($usuario);
            
            if ($usuarioDominio->isStatus()) {
                throw new \Exception(message: "Ya has iniciado sesión");
            }
            $fechaActual = new \DateTime();
            if ($usuarioDominio->getLockUntil() > $fechaActual->format('Y-m-d H:i:s')) {
                throw new \Exception(message: "Tu cuenta está bloqueada hasta " . $usuarioDominio->getLockUntil());
            }
            
            if(!Hash::check($usuario->getPassword(), $usuarioDominio->getPassword())) {
                $intentosFallidos = $usuarioDominio->getFailedAttempts() + 1;
                $usuarioDominio->setFailedAttempts($intentosFallidos);
                if ($intentosFallidos >= 3) {
                    $fechaBloqueo = $fechaActual->modify('+30 minutes')->format('Y-m-d H:i:s');
                    $usuarioDominio->setLockUntil(lockUntil: $fechaBloqueo);
                    BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
                    BaseDeDatos::getInstancia()->commit();
                    throw new \Exception(message: "Tu cuenta ha sido bloqueada por 30 minutos");
                } else {
                    BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
                    BaseDeDatos::getInstancia()->commit();
                    throw new \Exception("Contraseña incorrecta");
                }
            }
            // Si llegamos aquí, la contraseña es correcta
            $usuarioDominio->setStatus(true);
            $usuarioDominio->setFailedAttempts(0);
            $usuarioDominio->setLockUntil(lockUntil: null);

            BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
            BaseDeDatos::getInstancia()->commit();
            return $usuarioDominio;
        }catch (\Exception $e) {
            throw $e;
        }
    }

    public function cerrarSesion(UsuarioDominio $usuario) : void {
        $usuarioDominio = BaseDeDatos::getInstancia()->obtenerUsuario($usuario);
        $usuarioDominio->setStatus(false);
        BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
    }
}









