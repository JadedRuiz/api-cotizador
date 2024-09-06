<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    
    protected $table = 'tbl_lote as tblL';

    protected $primaryKey = 'iIdLote';

    protected $fillable = [
        'iIdEtapa', 
        'iLote', 
        'sTipoLote', 
        'iSuperficie', 
        'bIrregular', 
        'iAncho', 
        'iLargo', 
        'iStatus',
        'iUsuario', 
        'dtCreacion', 
        'dtMod', 
        'bActivo'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id'
    ];
}
