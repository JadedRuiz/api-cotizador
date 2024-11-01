<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\DB;
use App\Utils\Errores;
use Log;

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
            $validate= Usuario::where("sUsuario",$usuario["sUsuario"])->first();
            if(!$validate) {
                $usuario= Usuario::create([
                    "iIdEmpresa" => $usuario["iIdEmpresa"],
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
            Log::error("Error en método [createUsuario]: ".$e->getMessage().", Linea: ".$e->getLine());
            return $this->crearRespuesta(2,Errores::getError("G001",$e->getMessage()),201); 
        }
        
    }

    public function login(Request $credentials) {
        try {
            $this->validate($credentials, [
                'sContra' => 'required',
                'sUsuario' => 'required',
            ], [
                'sUsuario.required' => "El campo 'Usuario' es requerido",
                'sContra.required' => "El campo 'Contraseña' es requerido",
            ]);
        } catch(\Illuminate\Validation\ValidationException $e) {
            $total_errores=-1;
            foreach($e->errors() as $error) {
                $error_message = $error[0];
                $total_errores += count($error);
            }
            $error_total_message = $total_errores > 1 ? " (".$total_errores." errores más)" : " (".$total_errores." error más)";
            
            return response()->json(["message" => $error_message.$error_total_message], 422);
        }
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
