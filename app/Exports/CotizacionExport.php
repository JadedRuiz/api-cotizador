<?php

namespace App\Exports;
use Codedge\Fpdf\Fpdf\Fpdf;

class CotizacionExport {

    public static function generarPDF($data) {
        setlocale(LC_MONETARY,'ex_MX');
        $pdf = new Fpdf('P','mm','A4');
        $pdf->AddPage();
        #region [Cabecera]
            $pdf->SetXY(0,0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(60,30,"",0,0,"",1);
            $pdf->Image(public_path('assets/Imagenes')."/logo.png",5,5,50,20,'PNG','');
            $pdf->AddFont('Montserrat-Bold','','Montserrat-Bold.php', public_path('assets/fonts/Montserrat'));
            $pdf->AddFont('Montserrat-Regular','','Montserrat-Regular.php', public_path('assets/fonts/Montserrat'));
            $pdf->SetFont('Montserrat-Regular', 'U', 10);
            $pdf->SetFillColor(6, 93, 98);
            $pdf->SetTextColor(255,255,255);
            $pdf->Cell(150,5,"sanjeronimo.com.mx",0,0,"C",1,"https://sanjeronimo.com.mx/");
            //Titulo
            $pdf->SetXY(80,9);
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Montserrat-Bold', '', 17);
            $pdf->Cell(100,5,"COTIZACION A ".utf8_decode(strtoupper($data["info_plazo"]->sPlazo)),0,0,"C");
            $pdf->SetXY(80,16);
            $pdf->SetFont('Montserrat-Regular', '', 10);
            $pdf->Cell(100,5,utf8_decode("* Este documento cuenta con una validez de 15 días"),0,0,"C");
            $pdf->SetXY(80,21);
            $pdf->Cell(100,5,utf8_decode("* Precios sujetos a cambios sin previo aviso"),0,0,"C");
            $pdf->SetXY(80,27);
            $pdf->Cell(100,5,utf8_decode("PREGUNTA POR LA PROMOCIÓN DEL MES"),0,0,"C");
        #endregion
        #region [Division]
            $pdf->SetXY(0,32);
            $pdf->SetFillColor(15, 136, 142);
            $pdf->Cell(210,1.5,"",0,0,"",1);
        #endregion
        #region [Información Seleccionada y de Contacto]
            $pdf->SetXY(4, 36);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->SetDrawColor(6, 93, 98);
            $pdf->Cell(132,5,utf8_decode("INFORMACION SELECCIONADA"),"B");
            $pdf->SetX($pdf->GetX()+5);
            $pdf->Cell(65,5,utf8_decode("CONTACTO"),"B"); 
            //Fecha
            $pdf->SetXY(5, 44);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(25,6,"Fecha",1,0,"L",1);
            $pdf->SetFont('Montserrat-Regular', '', 11);            
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(35,6,date('d-m-Y'),1,0,"C",1);
            //Precio M2            
            $iCInteres= $data["info_lote"]->iPrecioM2Contado + (($data["info_plazo"]->iInteres/100) * $data["info_lote"]->iPrecioM2Contado);
            $pdf->SetX($pdf->GetX()+5);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(31,6,utf8_decode("Precio M2"),1,0,"L",1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Montserrat-Regular', '', 11);
            $pdf->Cell(35,6,"$ ".number_format($iCInteres,2),1,0,"C",1);
            //Telefono
            $pdf->SetX($pdf->GetX()+5);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(28,6,utf8_decode("Teléfono"),1,0,"L",1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Montserrat-Regular', '', 11);
            $pdf->Cell(38,6,"",1,0,"C",1);
            //Etapa
            $pdf->SetXY(5, 50);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(25,6,"Etapa",1,0,"L",1);
            $pdf->SetFont('Montserrat-Regular', '', 11);            
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(35,6,utf8_decode($data["info_lote"]->sEtapa),1,0,"C",1);
            //Subtotal
            // $iCInteres= ($data["info_plazo"]->iInteres/100) * $data["info_lote"]->iPrecioM2Contado;
            $iCTotal= $iCInteres * $data["info_lote"]->iSuperficie;
            $pdf->SetX($pdf->GetX()+5);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(31,6,utf8_decode("Subtotal"),1,0,"L",1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Montserrat-Regular', '', 11);
            $pdf->Cell(35,6,"$ ".number_format($iCTotal,2),1,0,"C",1);
            //Correo
            $pdf->SetX($pdf->GetX()+5);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(66,6,utf8_decode("Correo Electrónico"),1,0,"C",1);
            //Unidad
            $pdf->SetXY(5, 56);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(25,6,"Unidad",1,0,"L",1);
            $pdf->SetFont('Montserrat-Regular', '', 11);            
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(35,6,"LOTE ".$data["info_lote"]->iLote."-".$data["info_lote"]->iEtapa,1,0,"C",1);
            //Enganche
            $iEngancheTotal=0;
            if($data["info_plazo"]->iNoPlazo > 1) {
                $iEngancheTotal= ($data["info_ciudadano"]->iEnganche/100) * $iCTotal;
            }            
            $pdf->SetX($pdf->GetX()+5);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(31,6,utf8_decode("Enganche"),1,0,"L",1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Montserrat-Regular', '', 11);
            $pdf->SetTextColor(211, 13, 14);
            $pdf->Cell(35,6,"- $ ".number_format($iEngancheTotal,2),1,0,"C",1);
            //Correo Valor
            $pdf->SetX($pdf->GetX()+5);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('Montserrat-Regular', '', 11);
            $pdf->MultiCell(66,6,"contacto@sanjeronimo.com.mx",1,"C",1);
            //Superficie
            $pdf->SetXY(5, 62);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(25,6,"Superficie",1,0,"L",1);
            $pdf->SetFont('Montserrat-Regular', '', 11);            
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(35,6,number_format($data["info_lote"]->iSuperficie,2)." M2",1,0,"C",1);
            //Costo Total
            $iCostoTotal=$iCTotal - $iEngancheTotal;
            $pdf->SetX($pdf->GetX()+5);
            $pdf->SetFillColor(244, 244, 244);
            $pdf->SetFont('Montserrat-Bold', '', 12);
            $pdf->Cell(31,6,utf8_decode("Costo Total"),1,0,"L",1);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont('Montserrat-Regular', '', 11);
            $pdf->SetTextColor(68, 143, 153);
            $pdf->Cell(35,6,"$ ".number_format($iCostoTotal,2),1,0,"C",1);
        #endregion
        #region [Mensualidades]
            $pdf->SetXY(5,73);
            $pdf->SetFillColor(6, 93, 98);
            $pdf->SetTextColor(255,255,255);
            $pdf->SetDrawColor(6, 93, 98);
            $pdf->SetFont('Montserrat-Bold', '', 15);
            $pdf->Cell(100,8,"MES","TL",0,"C",1);
            $pdf->Cell(100,8,"COSTO","TR",0,"C",1);
            //Pintar Mensualidades
            if($data["info_plazo"]->iNoPlazo > 1) {
                $mensualidad = $iCostoTotal / $data["info_plazo"]->iNoPlazo;
                $pdf->SetXY(5,81);
                for($i=0; $i<$data["info_plazo"]->iNoPlazo; $i++) {
                    $pdf->SetFillColor(255, 255, 255);            
                    $pdf->SetTextColor(0,0,0);            
                    $pdf->SetDrawColor(0, 0, 0);
                    $pdf->SetFont('Montserrat-Regular', '', 15); 
                    $pdf->Cell(100,7,$i+1,"RLB",0,"C",1);
                    $pdf->Cell(100,7,"$ ".number_format($mensualidad,2),"RB",0,"C",1);
                    $pdf->SetXY(5,$pdf->GetY()+7.1);

                    if($pdf->GetY()+7.1 >= 272.7) {
                        $pdf->AddPage();
                        $pdf->SetXY(5,10);
                        $pdf->SetFillColor(6, 93, 98);
                        $pdf->SetTextColor(255,255,255);
                        $pdf->SetDrawColor(6, 93, 98);
                        $pdf->SetFont('Montserrat-Bold', '', 15);
                        $pdf->Cell(100,8,"MES","TL",0,"C",1);
                        $pdf->Cell(100,8,"COSTO","TR",0,"C",1);                    
                        $pdf->SetXY(5,17.1);
                    }
                }
            }
            
        #endregion
        return base64_encode($pdf->Output('S','cotizacion.pdf'));
    }
}