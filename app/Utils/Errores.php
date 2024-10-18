<?php

namespace App\Utils;

class Errores 
{
    public static function getError($codeError, $message="") {
        $errores=[
            //Errores genericos
            "G001" => "Lo sentimos en este momento estamos presentando problemas con nuestro servicio.
                        Si el problema persiste por favor contactese con el administrador '".getenv("MAIL_ADMIN")."'",
            "G002" => "No se han podido recuperar la información",
            //Errores cotizador
            "CE001" => "No se ha podido encontrar lotes para esta etapa",
            "CE002" => "Error al consultar en la base de datos",
            "CE003" => "Los sentimos, ha ocurrido un problema al generar su cotización.
                        Intentelo de nuevo, si el problema persiste contacte con el administrador '".getenv("MAIL_ADMIN")."'",
            "CE004" => "Lo sentimos, ya hemos recibido la información de su cotización acerca de este lote.
                        Intente con otro lote, o ingrese un nuevo correo electrónico.
                        Si no recibio el correo con la cotización, contacte con el administrador '".getenv("MAIL_ADMIN")."'",
            //Errores Usuario
            "USR001" => "Nombre de Usuario no disponible",
            "USR002" => "El nombre de usuario no existe",
            "USR003" => "La contraseña no coincide con nuestros registros",
            //Errores AUTH
            "AU001" => "El token proporcionado no es valido",
            //Errores Admin
            "AD001" => "No se pudieron recuperar los lotes de está etapa",
            "AD002" => "No se pudo actualizar el status",
            "AD003" => "Errror al actualizar: ".$message
        ];
        return $errores[$codeError];
    }
}