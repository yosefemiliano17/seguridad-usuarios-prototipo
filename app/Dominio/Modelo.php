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
            // Verificar si ya está logueado
            $usuarioDominio = BaseDeDatos::getInstancia()->obtenerUsuario($usuario);
            if ($usuarioDominio->isStatus()) {
                //BaseDeDatos::getInstancia()->rollback();
                throw new \Exception(message: "Ya has iniciado sesión");
            }
            
            // Verificar si la cuenta está bloqueada
            $fechaActual = new \DateTime();
            if ($usuarioDominio->getLockUntil() !== null && !empty($usuarioDominio->getLockUntil())) {
                $fechaBloqueo = new \DateTime($usuarioDominio->getLockUntil());
                if ($fechaBloqueo > $fechaActual) {
                    $diferencia = $fechaActual->diff($fechaBloqueo);
                    $minutosRestantes = ($diferencia->h * 60) + $diferencia->i;
                    
                    if ($minutosRestantes < 1) {
                        $mensaje = "Tu cuenta está bloqueada. Podrás intentar de nuevo en menos de 1 minuto";
                    } else {
                        $mensaje = "Tu cuenta está bloqueada. Podrás intentar de nuevo en $minutosRestantes minutos";
                    }
                    
                   // BaseDeDatos::getInstancia()->rollback();
                    throw new \Exception(message: $mensaje);
                }
                $usuarioDominio->setLockUntil(null);
                $usuarioDominio->setFailedAttempts(0);
            }
            
            // Verificar la contraseña
            if(!Hash::check($usuario->getPassword(), $usuarioDominio->getPassword())) {
                $intentosFallidos = $usuarioDominio->getFailedAttempts() + 1;
                $usuarioDominio->setFailedAttempts($intentosFallidos);
                
                if ($intentosFallidos >= 3) {
                    $fechaBloqueo = (new \DateTime())->modify('+30 minutes');
                    $usuarioDominio->setLockUntil($fechaBloqueo->format('Y-m-d H:i:s'));
                    BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
                    BaseDeDatos::getInstancia()->commit();
                    throw new \Exception(message: "Tu cuenta ha sido bloqueada por 30 minutos debido a múltiples intentos fallidos");
                } else {
                    BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
                    BaseDeDatos::getInstancia()->commit();
                    throw new \Exception(message: "Contraseña incorrecta. Intentos restantes: " . (3 - $intentosFallidos));
                }
            }
            
            // Si llegamos aquí, la contraseña es correcta
            $usuarioDominio->setStatus(true);
            $usuarioDominio->setFailedAttempts(0);
            $usuarioDominio->setLockUntil(null);

            BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
            BaseDeDatos::getInstancia()->commit();
            return $usuarioDominio;
            
        } catch (\Exception $e) {
            BaseDeDatos::getInstancia()->rollback();
            throw $e;
        }
    }

    public function cerrarSesion(UsuarioDominio $usuario) : void {
        $usuarioDominio = BaseDeDatos::getInstancia()->obtenerUsuario($usuario);
        $usuarioDominio->setStatus(false);
        BaseDeDatos::getInstancia()->actualizarValores($usuarioDominio);
    }
}









