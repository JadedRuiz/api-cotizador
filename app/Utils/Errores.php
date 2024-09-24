<?php

namespace App\Utils;

class Errores 
{
    public static function getError($codeError, $message="") {
        $errores=[
            //Errores genericos
            "G001" => "Ha ocurrido un error: ".$message,
            "G002" => "No se han podido recuperar la información",
            //Errores cotizador
            "CE001" => "No se ha podido encontrar lotes para esta etapa",
            "CE002" => "Error al consultar en la base de datos",
            "CE003" => "Ha ocurrido un error al generar el PDF: ".$message,
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