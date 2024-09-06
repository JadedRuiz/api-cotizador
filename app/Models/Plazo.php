<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plazo extends Model
{
    protected $table = 'tbl_plazo as tblP';

    protected $fillable = [
        'iIdEtapa', 
        'sPlazo', 
        'iNoPlazo', 
        'iInteres', 
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
