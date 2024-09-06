<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model 
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    
    protected $table= 'tbl_usuario';

    protected $fillable = [
        'iIdEmpresa', 'iIdPerfil', 'sNombre', 'sUsuario', 'sContra', 'dtCreacion', 'dtMod', 'bActivo'
    ];

}
