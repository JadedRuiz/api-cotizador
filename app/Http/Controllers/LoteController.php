<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Utils\Errores;
use App\Models\Lote;
use App\Models\Etapa;

class LoteController extends Controller
{
    //Metodos Genericos
    public function obtenerLotesEtapa($iIdEtapa) {
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

    public function obtenerLotesEtapaAdmin(Request $request) {
        try{
            $usuario = json_decode($this->decode_json($request["token"]));
        }catch(\Exceptiion $e) {
            return $this->crearRespuesta(2,Errores::getError("AU001"),201);
        }

        $respuesta= [
            "lotes" => [],
            "plazos" => []
        ];
        try {
            $respuesta["lotes"]= Lote::select("tblL.iIdLote",DB::raw("CONCAT('LOTE ',tblL.iLote,'-',tblE.iEtapa) as sLote"),"iLote","sTipoLote","iSuperficie","iAncho","iLargo","iPrecioM2Contado","iStatus","tblL.bActivo",DB::raw("ROUND(tblL.iPrecioM2Contado * tblL.iSuperficie,0) as iPrecioTotal"),"tblE.iMinEnganche",DB::raw("(SELECT COUNT(*) FROM tbl_cotizaciones WHERE iIdLote = tblL.iIdLote) as iNotificacion"))
            ->join("tbl_etapa as tblE","tblE.iIdEtapa","=","tblL.iIdEtapa")
            ->where("tblL.iIdEtapa",$request["iIdEtapa"])
            ->orderBy("tblL.iLote","ASC")
            ->get();

            foreach($respuesta["lotes"] as $lote) {
                $lote->iIdPlazo = 0;
                $lote->iEnganche = $lote->iMinEnganche;
            }
            return $this->crearRespuesta(1,$respuesta,200);
        } catch(\Exception $e) {
            return $this->crearRespuesta(2,Errores::getError("AD001"),201);
        }        
    }

    public function editarLotesAdmin(Request $request) {
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
