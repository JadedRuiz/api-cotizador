<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etapa extends Model
{
    protected $table = 'tbl_etapa as tblE';

    protected $primaryKey= 'iIdEtapa';

    protected $fillable = [
        'iIdProyecto',
        'sEtapa',
        'iEtapa',
        'iMinEnganche',
        'iIdUsuario',
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
