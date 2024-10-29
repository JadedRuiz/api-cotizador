<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use App\Utils\Errores;

class UsuarioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function create(Request $usuario)
    {
        try {
            $validate= Usuario::where("sUsuario",$credentials["sUsuario"])->fisrt();
            if(!$validate) {
                $usuario= Usuario::create([
                    "iIdEmpresa" => 1,
                    "sNombre" => $usuario["sNombre"],
                    "sUsuario" => $usuario["sUsuario"],
                    "sContra" => $this->encode_json($usuario["sContra"]),
                    "dtCreacion" => date("Y-m-d"),
                    "bActivo" => 1
                ]);
                return $this->crearRespuesta(1,$usuario,200);
            }
            return $this->crearRespuesta(2,Errores::getError("USR001"),201);            
        }catch(\Exception $e) {
            return $this->crearRespuesta(2,Errores::getError("G001",$e->getMessage()),201); 
        }
        
    }

    public function login(Request $credentials) {
        $usuario= Usuario::where("sUsuario",$credentials["sUsuario"])->first();

        if($usuario){
            if($this->decode_json($usuario->sContra) == $credentials["sContra"]) {
                $empresa = DB::table("tbl_empresa")->where("iIdEmpresa",$usuario->iIdEmpresa)->first();
                $usuario->empresa = $empresa->sEmpresa;
                return $this->crearRespuesta(1,$this->encode_json($usuario),200);
            }
            return $this->crearRespuesta(2,Errores::getError("USR003"),201); 
        }
        return $this->crearRespuesta(2,Errores::getError("USR002"),201); 
    }
}
