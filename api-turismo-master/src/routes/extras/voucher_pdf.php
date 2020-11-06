<?php
require('../../config/pdf/fpdf/fpdf.php');




$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$mes = $meses[date("m") - 1];
$dia = date("d");
$anio = date("Y");
function ffecha($fecha)
{
    $res = explode("-", $fecha);
    return $res[2] . "/" . $res[1] . "/" . $res[0];
}
?>
<style>
    div {
        margin-bottom: 20px;
    }

    .container {
        width: 800px;
        margin: 10px auto;
    }

    .titulo {
        padding: 10px;
        background: #67223D;
        color: #fff;
        text-align: center;
        text-transform: uppercase;
        /*font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-weight: 800;
        font-size: 1.4em;
    }

    .subtitulo {
        color: #fff;
        font-weight: 600;
        background: #67223D;
        padding: 8px;
    }

    .txt-info {
        font-size: 1.1em;
        border-bottom: 1px solid #ccc;
    }

    .txt-info span {
        padding: 6px;
        font-weight: 800;
        font-style: italic;
        color: #04050B;
    }

    .txt-info-td {
        font-size: 1.1em;
        padding: 6px;
        border-bottom: 1px solid #ccc;
    }

    .table-container {
        font-size: 1.1em;
        border-bottom: 1px solid #ccc;
    }

    .table-container .t-dato-titulo {
        background-color: #141B3D;
        text-align: center;
        color: #fff;
        padding: 8px;
    }

    .table-container table {
        width: 100%;
    }

    .fondo {
        background-image: 'http://turismo.sanluis.gov.ar/api-turismo/public/recursos/image.png';

    }
</style>
<page backtop="10mm" backbottom="17mm" backleft="10mm" backright="10mm">
    <page_header>
        <div class="header">
            <table style="width: 100%;">
                <colgroup>
                    <col style="width: 50%">
                    <col style="width: 50%">
                </colgroup>
                <tbody>
                    <tr>
                        <td>Argentina / San Luis / Secretaría de Turismo</td>
                        <td align="right"><?php echo ($dia); ?> de <?php echo ($mes); ?> de <?php echo ($anio); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </page_header>
    <page_footer>SisTur &copy; <?php echo ($anio); ?></page_footer>
    <!-- Datos Fiscales -->
    <div class="fondo">
        <table style="width: 100%;">
            <colgroup>
                <col style="width: 70%">
                <col style="width: 30%">
            </colgroup>
            <tbody>
                <tr>
                    <td>
                        <div class="txt-info"><span>Departamento:</span> <?php echo ($guia->departamento); ?> (<?php echo ($guia->cp); ?>)</div>
                        <div class="txt-info"><span>Ciudad:</span> <?php echo ($guia->ciudad); ?></div>
                        <div class="txt-info"><span>Tipo:</span> <?php echo ($guia->tipo); ?></div>
                        <div class="txt-info"><span>Tipo de Categoría:</span> <?php echo ($guia->tipocategorianombre); ?></div>
                        <div class="txt-info"><span>Categoría:</span> <?php echo ($guia->valortipcatdescripcion); ?></div>
                        <div class="txt-info"><span>Domicilio:</span> <?php echo ($guia->domicilio); ?></div>
                        <div class="txt-info"><span>Teléfono:</span> (<?php echo ($guia->caracteristica); ?>) <?php echo ($guia->telefono); ?></div>
                        <div class="txt-info"><span>EMail:</span> <?php echo ($guia->mail); ?></div>
                        <div class="txt-info"><span>Web:</span> http://<?php echo ($guia->web); ?></div>
                    </td>
                    <td style="vertical-align: top; text-align: right;">
                        <img class="logo" src='<?php echo ($directory . DIRECTORY_SEPARATOR . $guia->logo); ?>' height="150" width="150" alt="Logo" />
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</page>