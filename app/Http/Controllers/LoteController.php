<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Utils\Errores;
use App\Models\Lote;
use App\Models\Etapa;
use Log;

class LoteController extends Controller
{
    //Metodos Genericos
    public function getLotesPorEtapaId($iIdEtapa) {
        try{
            $lote= Lote::select('iIdLote','iLote','sTipoLote','iSuperficie','bIrregular','iAncho','iLargo', 'iStatus', 'iPrecioM2Contado')
            ->where('iIdEtapa',$iIdEtapa)
            ->where('bActivo',1)
            ->get();


            return $this->crearRespuesta(1,$lote,200);
        }catch(\Exception $e) {
            return $this->crearRespuesta(2,Errores::getError("G001",$e->getMessage()),201);
        }
    }

    public function obtenerLotesEtapa(Request $request) {
        try{
            $usuario = json_decode($this->decode_json($request["token"]));
        }catch(\Exceptiion $e) {
            return $this->crearRespuesta(2,Errores::getError("AU001"),401);
        }

        $respuesta= [
            "lotes" => [],
            "plazos" => []
        ];
        try {
            $respuesta["lotes"]= Lote::select("iIdLote","tbl_lote.iIdEtapa","iIdFoto",DB::raw("CONCAT('LOTE ',iLote,'-',tblE.iEtapa) as sLote"),"iLote","sTipoLote","iSuperficie","iAncho","iLargo","iPrecioM2Contado","iStatus","tbl_lote.bActivo",DB::raw("ROUND(iPrecioM2Contado * iSuperficie,0) as iPrecioTotal"),"tblE.iMinEnganche",DB::raw("(SELECT COUNT(*) FROM tbl_cotizaciones WHERE iIdLote = tbl_lote.iIdLote) as iNotificacion"))
            ->join("tbl_etapa as tblE","tblE.iIdEtapa","=","tbl_lote.iIdEtapa")
            ->where("tbl_lote.iIdEtapa",$request["iIdEtapa"])
            ->orderBy("iLote","ASC")
            ->get();

            foreach($respuesta["lotes"] as $lote) {
                $lote->iIdPlazo = 0;
                $lote->iEnganche = $lote->iMinEnganche;
                $lote->bIrregular = $lote->iAncho == null && $lote->iLargo == null ? true : false;
            }
            return $this->crearRespuesta(1,$respuesta,200);
        } catch(\Exception $e) {
            Log::error("Error en método [obtenerLotesEtapa]: ".$e->getMessage().", Linea: ".$e->getLine());
            return $this->crearRespuesta(2,Errores::getError("AD001"),201);
        }        
    }

    // Método: [guardarLote]
    // Desc: Esto es un comentario
    // Ult. mod: 28/10/2024
    public function guardarLote(Request $request) {
        $foto = [
            "url" => asset("Imagenes\img-default.png"),
            "path" => "Imagenes\img-default.png"
        ];
        #region [Validaciones]
            try {
                $this->validate($request, [
                    'iIdEtapa' => 'required|numeric',
                    'iLote' => 'required|numeric',
                    'sTipoLote' => 'max:300',
                    'iSuperficie' => 'required',
                    'bIrregular' => 'required',
                    'iAncho' => 'required',
                    'iLargo' => 'required',
                    'iPrecioM2Contado' => 'required',
                    'image' => 'nullable|image|mimes:png,jpg,jpeg|max:5000',
                    'token' => 'required'
                ], [
                    'iIdEtapa.required' => "El campo 'iIdEtapa' es requerido",
                    'iIdEtapa.numeric' => "El campo 'iIdEtapa' solo acepta enteros",
                    'iLote.required' => "El campo 'iLote' es requerido",
                    'sTipoLote.max' => "El campo 'sTipoLote' solo acepta 300 caracteres",
                    'iSuperficie' => "El campo 'iSuperficie' es requerido",
                    'bIrregular.required' => "El campo 'bIrregular' es requerido",
                    'bIrregular.boolean' => "El campo 'bIrregular' solo acepta booleanos",
                    'iAncho.required' => "El campo 'iAncho' es requerido",
                    'iLargo.required' => "El campo 'iLargo' es requerido",
                    'iPrecioM2Contado.required' => "El campo 'iPrecioM2Contado' es requerido",
                    'image.image' => "El archivo adjuntado no es una imagen",
                    'image.mimes' => "Solo se aceptan imagenes jpg, jpeg & png",
                    "image.max" => "La imagen ha sobrepasado el tamaño maximo",
                    "token.required" => "El token es requerido",
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
            //Validamos el token
            try{
                $user = json_decode($this->decode_json($request["token"]));
            }catch(\Exceptiion $e) {
                return $this->crearRespuesta(2,Errores::getError("AU001"),401);
            }
            //Validamos si la variable imagen tiene data
            if($request->image != null && $request->hasFile("image")) {
                $image= time().'.'.$request->image->extension();
                $request->image->move(public_path('assets/Imagenes/Empresas/'.$user->sEmpresa."/Lotes/Lote-".$request->iLote),$image);
            }
        #endregion
        return $this->crearRespuesta(1,$usuario,200); 
        #region [Insercción de datos]
            try {
                DB::beginTransaction(); 
                $request = json_decode(json_encode($request->all()));
                $request->dtCreacion = date('Y-m-d h:i:s');
                $request->bActivo = 1;    
                $request = json_decode(json_encode($request),true);           
                Lote::create($request);
                DB::commit();
                return $this->crearRespuesta(1,"Datos del lote almacenado con exito",200); 

            } catch(\PdoException | \Error | \Exception $e) {
                DB::rollback();
                Log::error("Error en método [guardarLote]: ".$e->getMessage().", Linea: ".$e->getLine());
                return $this->crearRespuesta(2,Errores::getError("LOT001"),201);
            }
        #endregion
    }

    public function editarLotes(Request $request) {
        try {
            Lote::whereId($request["iIdLote"])->update($request->all());
        }catch(\Exception | \PDOException $e){
            return $this->crearRespuesta(2,Errores::getError("AD003"),201);
        }
    }

    public function cambiarStatusLote(Request $request) {
        try{
            $usuario = json_decode($this->decode_json($request["token"]));
        }catch(\Exceptiion $e) {
            return $this->crearRespuesta(2,Errores::getError("AU001"),201);
        }

        try{

            $lote = Lote::where("iIdLote",$request["iIdLote"])->update(["iStatus" => $request["iStatus"]]);
            return $this->crearRespuesta(1,"El status se ha actualizado",200);

        } catch(\Exception $e) {
            return $this->crearRespuesta(2,Errores::getError("G001",$e->getMessage()),201);
        }   
        return $lote;
    }
}
