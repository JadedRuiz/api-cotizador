<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Utils\Errores;
use App\Models\Cotizaciones;
use App\Models\Lote;
use App\Models\Plazo;
use Illuminate\Support\Facades\DB;
use App\Exports\CotizacionExport as PDF;
use GuzzleHttp\Client;
use Log;

class CotizacionController extends Controller
{
    /*  [Guardar Cotización]
        Descripción: Método que guarda la cotización, notifica al administrador
        al ciudadano e inserta el formulario en Hubspot
        Desarrollo: Jaded Ruiz 
    */
    public function guardarCotizacion(Request $data) {
        try {
            //Iniciamos la transacciones a BD
            DB::beginTransaction();

            //Varibales
            $cotizacion = new Cotizaciones();
            $precio_total = 0;
            $data_view = [
                "info_ciudadano" => $cotizacion,
                "info_lote" => [],
                "info_plazo" => json_decode(json_encode([
                    "sPlazo" => "CONTADO",
                    "iNoPlazo" => 1,
                    "iInteres" => 100, 
                ]))
            ];
            $notifica_hubspot = in_array($data["iIdPlan"],[2,3]) ? true : false;

            #region [Validaciones]
                //Validamos si existe una cotizacion de la persona sobre el mismo lote
                $valida_existencia = Cotizaciones::where("sCorreo",$data["sCorreo"])->where("iIdLote",$data["iIdLote"])->first();
                if($valida_existencia){
                    return $this->crearRespuesta(2,Errores::getError("CE004"),201);
                }
            #endregion

            #region [Consultas e Inserción de Cotización]
                //Insertamos la cotización
                $cotizacion->iIdLote= $data['iIdLote'];
                $cotizacion->iIdPlazo= $data['iIdPlazo'];
                $cotizacion->sNombre= $data['sNombre'];
                $cotizacion->sApellidos= $data['sApellidos'];
                $cotizacion->sCorreo= $data['sCorreo'];
                $cotizacion->sTelefono= $data['iTelefono'];
                $cotizacion->sCiudad= $data['sCiudad'];
                $cotizacion->iEnganche= $data['iEnganche'];
                $cotizacion->dtCreacion = date('Y-m-d h:i:s');            
                $cotizacion->save();
                //Buscamos la información del lote seleccionado
                $data_view["info_lote"]= Lote::select('tblL.iLote','tblL.sTipoLote','tbE.sEtapa',"tbE.iEtapa","tblL.iSuperficie","tblL.iPrecioM2Contado")
                ->join("tbl_etapa as tbE","tbE.iIdEtapa","=","tblL.iIdEtapa")
                ->where("iIdLote",$data["iIdLote"])
                ->first();
                //Buscams la informacion de los plazos de la etapa seleccionada, solo si no es de contado
                $precio_total = floatval($data_view["info_lote"]->iSuperficie) * floatval($data_view["info_lote"]->iPrecioM2Contado);
                if($data['iIdPlazo'] > 0) {
                    $data_view["info_plazo"]= Plazo::select("sPlazo","iNoPlazo","iInteres")
                    ->where("iIdPlazo",$data["iIdPlazo"])
                    ->first();
                    $precio_total = floatval($data_view["info_lote"]->iSuperficie) * (($data_view["info_plazo"]->iInteres / 100)  * floatval($data_view["info_lote"]->iPrecioM2Contado));
                }
            #endregion

            #region [Generación de PDF]
                try {
                    $pdf_b64= PDF::generarPDF($data_view);
                }catch(\Exception $e) {
                    Log::error("'Error al generar el PDF', error: [".$e->getMessage()."], data: ".json_encode($cotizacion));
                    DB::rollBack();
                    return $this->crearRespuesta(2,Errores::getError("CE003",$e->getMessage()),201);
                }
            #endregion

            #region [Envios de Corres Admin & Ciudadano]
                //Enviar correo al admin
                Mail::send('correo_admin', compact('data_view'), function ($message) use ($data){
                    $message->subject('Cotizador San Jeronimo');
                    $message->to(getenv('MAIL_ADMIN'),'Administrador San Jerononimo');     
                });
                //Enviar Correo al interesado
                Mail::send('correo_cotizacion', compact('data_view'), function ($message) use ($data_view, $data, $pdf_b64){
                    $message->subject('Cotización de '.$data_view["info_lote"]->iLote."-".$data_view['info_lote']->iEtapa.' | San Jerónimo');
                    $message->to($data["sCorreo"], $data["sNombre"]); 
                    $message->attachData(base64_decode($pdf_b64),date('d/m/Y')."-Cotizacion|SanJeronimo.pdf");    
                });
            #endregion
                
            #region [Notifica a HubSpot]
                try {
                    if($notifica_hubspot) {
                        $conexion = new Client(['headers'=>['Content-Type' => 'application/json'],'verify' => false]);
                        $fields = [];
                        //Creamos el json de los campos
                        foreach($data->all() as $parameter => $value) {
                            $name = null;
                            switch($parameter) {
                                case "sNombre": 
                                    $name = "firstname";
                                    break;
                                case "sApellidos": 
                                    $name = "lastname";
                                    break;
                                case "sTelefono":
                                    $name = "phone";
                                    break;
                                case "sCorreo":
                                    $name = "email";
                                    break;
                                case "sCiudad":
                                    $name = "city";
                                    break;
                            }
                            
                            if($name != null) {
                                $fields[] = [
                                    "objectTypeId" => "0-1",
                                    "name" => $name,
                                    "value" => $value
                                ];
                            }
                        }
                        $enganche = floatval(intval($data["iEnganche"]) / 100) * $precio_total;
                        $fields[] = [
                            "objectTypeId" => "0-1",
                            "name" => "cotizacion",
                            "value" => $data_view["info_lote"]->sEtapa.", Lote ".$data_view["info_lote"]->iLote.", Cotización: ".
                            $data_view["info_plazo"]->sPlazo." - Enganche: ".$data["iEnganche"]."%, $".number_format($enganche,2,".")
                        ];
                        date_default_timezone_set('America/Mexico_City');
                        $parametros = [
                            "fields" => $fields
                        ];
                        $response = $conexion->post(getenv("URL_SERVICE")."/".getenv("ID_PLATFORM")."/".getenv("ID_FORM"),["json" => $parametros]);
                        $response = json_decode($response->getBody());
                        if (isset($response->errors)) {
                            Log::error("'Error de de respuesta', error: [".$response->errors."], data: ".json_encode($parametros));
                        }
                    }
                }catch(\GuzzleHttp\Exception\RequestException $e) {
                    Log::error("'Error al enviar formulario', error: [".$e->getMessage()."], data: ".json_encode($parametros));
                }
            #endregion
            
            DB::commit();
            return $this->crearRespuesta(1,"Muchas gracias por contactarnos, un asesor se contactará con usted.",200);
        }catch(\Error | \Exception $e) {
            Log::error("'Error General', error: [".$e->getMessage()."], data: ".json_encode($data->all()));
            $this->resetearId("tbl_cotizaciones");
            DB::rollBack();
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
        ->join("tbl_lote as tblL","tblL.iIdLote","=","tbl_cotizaciones.iIdLote")
        ->leftJoin("tbl_plazo as tblP","tblP.iIdPlazo","=","tbl_cotizaciones.iIdPlazo")
        ->where("tbl_cotizaciones.iIdLote",$request["iIdLote"])
        ->orderBy("tbl_cotizaciones.dtCreacion","DESC")
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

        $data_view["info_lote"]= Lote::select('iLote','sTipoLote','tbE.sEtapa',"tbE.iEtapa","iSuperficie","iPrecioM2Contado")
        ->join("tbl_etapa as tbE","tbE.iIdEtapa","=","tbl_lote.iIdEtapa")
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
