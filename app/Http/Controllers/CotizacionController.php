<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Utils\Errores;
use App\Models\Cotizaciones;
use App\Models\Lote;
use App\Models\Plazo;
use App\Exports\CotizacionExport as PDF;

class CotizacionController extends Controller
{
    public function guardarCotizacion(Request $data) {
        try {
            $cotizacion = new Cotizaciones();
            $cotizacion->iIdLote= $data['iIdLote'];
            $cotizacion->iIdPlazo= $data['iIdPlazo'];
            $cotizacion->sNombre= $data['sNombre'];
            $cotizacion->sCorreo= $data['sCorreo'];
            $cotizacion->sTelefono= $data['iTelefono'];
            $cotizacion->sCiudad= $data['sCiudad'];
            $cotizacion->iEnganche= $data['iEnganche'];
            $cotizacion->dtCreacion = date('Y-m-d');
            $cotizacion->save();
            $data_view = [
                "info_ciudadano" => $cotizacion,
                "info_lote" => [],
                "info_plazo" => json_decode(json_encode([
                    "sPlazo" => "CONTADO",
                    "iNoPlazo" => 1,
                    "iInteres" => 100, 
                ]))
            ];
            $data_view["info_lote"]= Lote::select('tblL.iLote','tblL.sTipoLote','tbE.sEtapa',"tbE.iEtapa","tblL.iSuperficie","tblL.iPrecioM2Contado")
            ->join("tbl_etapa as tbE","tbE.iIdEtapa","=","tblL.iIdEtapa")
            ->where("iIdLote",$data["iIdLote"])
            ->first();

            if($data['iIdPlazo'] > 0) {
                $data_view["info_plazo"]= Plazo::select("sPlazo","iNoPlazo","iInteres")
                ->where("iIdPlazo",$data["iIdPlazo"])
                ->first();
            }

            //Enviar correo al admin
            Mail::send('correo_admin', compact('data_view'), function ($message) use ($data){
                $message->subject('Cotizador San Jeronimo');
                $message->to(getenv('MAIL_ADMIN'),'Administrador San Jerononimo');     
            });

            //Generamos el PDF en B64
            try {
                $pdf_b64= PDF::generarPDF($data_view);
            }catch(\Exception $e) {
                return $this->crearRespuesta(2,Errores::getError("CE003",$e->getMessage()),201);
            }

            //Enviar Correo al interesado
            Mail::send('correo_cotizacion', compact('data_view'), function ($message) use ($data_view, $data, $pdf_b64){
                $message->subject('Cotización de '.$data_view["info_lote"]->iLote."-".$data_view['info_lote']->iEtapa.' | San Jerónimo');
                $message->to($data["sCorreo"], $data["sNombre"]); 
                $message->attachData(base64_decode($pdf_b64),date('d/m/Y')."-Cotizacion|SanJeronimo.pdf");    
            });

            return $this->crearRespuesta(1,"Muchas gracias por contactarnos, un asesor se contactará con usted.",200);
        }catch(\Exception $e) {
            return $this->crearRespuesta(2,Errores::getError("G001",$e->getMessage()),201);
        }      

    }

    function obtenerCotizacionesLote(Request $request) {
        try{
            $usuario = json_decode($this->decode_json($request["token"]));
        }catch(\Exceptiion $e) {
            return $this->crearRespuesta(2,Errores::getError("AU001"),201);
        }

        $interesados = Cotizaciones::select("sNombre","sCorreo","sTelefono","sCiudad","iEnganche","tblL.iPrecioM2Contado","tblL.iSuperficie","tblP.iInteres","tblP.iNoPlazo","tblP.sPlazo")
        ->join("tbl_lote as tblL","tblL.iIdLote","=","tblC.iIdLote")
        ->leftJoin("tbl_plazo as tblP","tblP.iIdPlazo","=","tblC.iIdPlazo")
        ->where("tblC.iIdLote",$request["iIdLote"])
        ->orderBy("tblC.dtCreacion","DESC")
        ->get();
        
        foreach($interesados as $index => $interesado) {
            $interesado->collapse = false;
            if($index == 0) {
                $interesado->collapse = true;
            }
            if($interesado->iNoPlazo != null) {
                $interesado->iPrecioSubTotal= ((($interesado->iInteres/100)*$interesado->iPrecioM2Contado)+$interesado->iPrecioM2Contado) * $interesado->iSuperficie;
                $interesado->iPrecioEnganche = ($interesado->iEnganche/100) * $interesado->iPrecioSubTotal;
                $interesado->iPrecioTotal = ($interesado->iPrecioSubTotal - $interesado->iPrecioEnganche);
                $interesado->iMensualidad = "$ ".number_format($interesado->iPrecioTotal / $interesado->iNoPlazo,2);
                $interesado->iPrecioSubTotal = "$ ". number_format($interesado->iPrecioSubTotal,2);
                $interesado->iPrecioEnganche = "$ ". number_format($interesado->iPrecioEnganche,2);
                $interesado->iPrecioTotal = "$ ". number_format($interesado->iPrecioTotal,2);
                $interesado->sPlazo= strtoupper($interesado->sPlazo);
            }else{
                $interesado->sPlazo= "CONTADO";
                $interesado->iPrecioSubTotal=0;
                $interesado->iMensualidad=0;
                $interesado->iPrecioTotal= "$ ".number_format($interesado->iSuperficie * $interesado->iPrecioM2Contado);
                $interesado->iPrecioEnganche = 0;
            }
        }
        return $interesados;
    }

    function generarCotizacionAdmin(Request $data) {
        $cotizacion = new Cotizaciones();
        $cotizacion->iIdLote= $data['iIdLote'];
        $cotizacion->iIdPlazo= $data['iIdPlazo'];
        $cotizacion->sNombre= "Aministrador";
        $cotizacion->sCorreo= "";
        $cotizacion->sTelefono= "";
        $cotizacion->sCiudad= "";
        $cotizacion->iEnganche= $data['iEnganche'];
        $cotizacion->dtCreacion = date('Y-m-d');

        $data_view = [
            "info_ciudadano" => $cotizacion,
            "info_lote" => [],
            "info_plazo" => json_decode(json_encode([
                "sPlazo" => "CONTADO",
                "iNoPlazo" => 1,
                "iInteres" => 100, 
            ]))
        ];

        $data_view["info_lote"]= Lote::select('tblL.iLote','tblL.sTipoLote','tbE.sEtapa',"tbE.iEtapa","tblL.iSuperficie","tblL.iPrecioM2Contado")
        ->join("tbl_etapa as tbE","tbE.iIdEtapa","=","tblL.iIdEtapa")
        ->where("iIdLote",$data["iIdLote"])
        ->first();

        if($data['iIdPlazo'] > 0) {
            $data_view["info_plazo"]= Plazo::select("sPlazo","iNoPlazo","iInteres")
            ->where("iIdPlazo",$data["iIdPlazo"])
            ->first();
        }

        try {
            $pdf_b64= PDF::generarPDF($data_view);
            return $this->crearRespuesta(1,$pdf_b64,200);
        }catch(\Exception $e) {
            return $this->crearRespuesta(2,Errores::getError("CE003",$e->getMessage()),201);
        }
    }
}
