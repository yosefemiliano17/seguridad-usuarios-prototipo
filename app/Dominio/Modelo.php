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
            

        BaseDeDatos::getInstancia()->beginTransaction();
        try {
        
            $usuarioDominio = BaseDeDatos::getInstancia()->obtenerUsuario($usuario);
            if ($usuarioDominio->isStatus()) {
                throw new \Exception(message: "Ya has iniciado sesión");
            }
            
           
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
                    
                  
                    throw new \Exception(message: $mensaje);
                }
                $usuarioDominio->setLockUntil(null);
                $usuarioDominio->setFailedAttempts(0);
            }
            
           
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









