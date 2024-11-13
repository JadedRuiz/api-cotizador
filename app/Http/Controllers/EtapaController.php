<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Utils\Errores;
use App\Models\Etapa;
use App\Models\Lote;
use App\Models\Plazo;
use Illuminate\Support\Facades\Storage;

class EtapaController extends Controller
{
    public function getEtapas($iIdProyecto) {
        $etapas = Etapa::select("iIdEtapa","sEtapa","sPath")
        ->leftJoin("tbl_adjunto as tblA","tblA.iIdAdjunto","=","tblE.iIdAdjunto")
        ->where('iIdProyecto',$iIdProyecto)
        ->where('tblE.bActivo',1)
        ->orderBy('iOrden','ASC')
        ->get();
        return $this->crearRespuesta(1,$etapas,200);
    }

    public function obtenerPlazosPorEtapa($iIdEtapa) {
        $plazos = Plazo::select('iIdPlazo', 'sPlazo', 'iNoPlazo', 'iInteres')
        ->where('iIdEtapa',$iIdEtapa)
        ->where('bActivo',1)
        ->get();
        return $this->crearRespuesta(1,$plazos,200);
    }

    //Admin

    public function getEtapasAdmin(Request $request) {
        try{
            $usuario = json_decode($this->decode_json($request["token"]));
        }catch(\Exceptiion $e) {
            return $this->crearRespuesta(2,Errores::getError("AU001"),201);
        }
        
        try{
            $objEtapas = DB::table("tbl_proyecto as tblP")
            ->select("tblE.iIdEtapa","tblE.iEtapa","tblE.sEtapa","tblE.bActivo")
            ->join("tbl_etapa as tblE","tblE.iIdProyecto","=","tblP.iIdProyecto")
            ->where("iIdEmpresa",$usuario->iIdEmpresa)
            ->orderBy("iOrden","ASC")
            ->get();
            foreach($objEtapas as $index => $etapa) {
                $etapa->iTotalLotes= Lote::where("iIdEtapa","=",$etapa->iIdEtapa)->count();
                $etapa->bActive= 0;
                if($index == 0) {
                    $etapa->bActive= 1;
                }
                $etapa->sSvg= "/assets/Imagenes/img-default.png";
            }
            return $this->crearRespuesta(1,$objEtapas,200);
        }catch(\Exception $e) {
            return $this->crearRespuesta(2,Errores::getError("G002"),201);
        }
    }
}
