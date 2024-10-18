<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>San Jeronimo | Notificacion</title>
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,600,700" rel="stylesheet">
    <style type="text/css">
        table{
            border: 0px solid transparent;
            border-width: 0px;
        }
        /* CLIENT-SPECIFIC STYLES */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        /* RESET STYLES */
        img { border: 0; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        /* iOS BLUE LINKS */
        a[x-apple-data-detectors] {
        color: inherit !important;
        text-decoration: none !important;
        font-size: inherit !important;
        font-family: inherit !important;
        font-weight: inherit !important;
        line-height: inherit !important;
        }
        /* ANDROID CENTER FIX */
        div[style*="margin: 16px 0;"] { margin: 0 !important; }
        /* MEDIA QUERIES */
        @media all and (max-width:639px){
        .wrapper{ width:320px!important; padding: 0 !important; }
        .container{ width:300px!important;  padding: 0 !important; }
        .mobile{ width:300px!important; display:block!important; padding: 0 !important; }
        .img{ width:100% !important; height:auto !important; }
        *[class="mobileOff"] { width: 0px !important; display: none !important; }
        *[class*="mobileOn"] { display: block !important; max-height:none !important; }
        }
    </style>
</head>
<body>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff">
        <tr>
            <td align="center" valign="top">
                <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                <tr>
                                    <td width="600" class="mobile" align="left" valign="middle">
                                        <font face="Arial" color="#0E888E" size="4" style="font-family: 'Raleway', sans-serif; font-weight: 400;">
                                            Hola, {{$data_view["info_ciudadano"]["sNombre"]}}
                                        </font>
                                    </td>
                                </tr>
                            </table>
                            <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                                <tr>
                                    <td height="20" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                            <tr>
                                                <td class="mobile" align="center" valign="top">
                                                    <font face="Arial" color="#000000" size="3" style="font-family: 'Raleway', sans-serif; font-weight: 200;">
                                                        Recibimos tu solicitud, en breve un asesor te informará sobre las promociones
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
						    </table>
                            <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                                <tr>
                                    <td height="20" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <table width="400" cellpadding="0" cellspacing="0" border="0" class="container">
                                            <tr>
                                                <td bgcolor="#d3d4d4" width="200" class="mobile" align="left" valign="middle" style="padding: 10px;">
                                                    <font face="Arial" color="#000000" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 600;">
                                                        Etapa / Lote:
                                                    </font>
                                                </td>
                                                <td bgcolor="#d3d4d4" width="200" class="mobile" align="left" valign="middle" style="padding: 10px;">
                                                    <font face="Arial" color="#000000" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 200;">
                                                        {{ $data_view["info_lote"]["sEtapa"] }} / LOTE-{{ $data_view["info_lote"]["iLote"] }}
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="#f2f2f2" width="200" class="mobile" align="left" valign="middle" style="padding: 10px;">
                                                    <font face="Arial" color="#000000" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 600;">
                                                        Financiamiento:
                                                    </font>
                                                </td>
                                                <td bgcolor="#f2f2f2" width="200" class="mobile" align="left" valign="middle" style="padding: 10px;">
                                                    <font face="Arial" color="#000000" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 200;">
                                                        {{ $data_view["info_plazo"]["sPlazo"] }}
                                                    </font>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td bgcolor="#d3d4d4" width="200" class="mobile" align="left" valign="middle" style="padding: 10px;">
                                                    <font face="Arial" color="#000000" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 600;">
                                                        Enganche:
                                                    </font>
                                                </td>
                                                <td bgcolor="#d3d4d4" width="200" class="mobile" align="left" valign="middle" style="padding: 10px;">
                                                    <font face="Arial" color="#000000" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 200;">
                                                        {{ $data_view["info_ciudadano"]["iEnganche"] }}
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
						    </table>
                            <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#FFFFFF">
                                <tr>
                                    <td height="20" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <table width="600" cellpadding="0" cellspacing="0" border="0" class="container">
                                            <tr>
                                                <td class="mobile" align="center" valign="top">
                                                    <font face="Arial" color="#000000" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 600;">
                                                        En el archivo adjunto puedes visualizar o descargar tu corrida financiera en formato PDF
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
						    </table>
                            <table width="640" cellpadding="0" cellspacing="0" border="0" class="wrapper" bgcolor="#0E888E">
                                <tr>
                                    <td height="20" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td align="center" valign="top">
                                        <table width="500" cellpadding="0" cellspacing="0" border="0" class="container">
                                            <tr>
                                                <td align="center" valign="top">

                                                    <font face="Arial" color="#FFFFFF" size="2" style="font-family: 'Raleway', sans-serif; font-weight: 200;">
                                                    San Jerónimo. Copyright © {{ date('Y') }}
                                                    </font>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="20" style="font-size:10px; line-height:10px;">&nbsp;</td>
                                </tr>
						    </table>
                        </td>
                    </tr>
                    <tr>
                        <td height="10" style="font-size:10px; line-height:10px;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>