<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizaciones extends Model
{
    protected $table = 'tbl_cotizaciones';

    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'iIdLote',
        'iIdPlazo',  
        'sNombre', 
        'sCorreo', 
        'sTelefono', 
        'sCiudad',
        'iEnganche',
        'dtCreacion'
    ];
}
