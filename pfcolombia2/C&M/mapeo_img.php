<?php
error_reporting(0);
ini_set('display_errors', '0');

include("funciones.php");
$PSN1 = new DBbase_Sql;

$campos_mapeo = array(
    "mapeo_comprometido", "mapeo_oracion", "mapeo_companerismo", "mapeo_adoracion",
    "mapeo_biblia", "mapeo_evangelizar", "mapeo_cena", "mapeo_dar",
    "mapeo_bautizar", "mapeo_trabajadores"
);

// Valores por defecto (reporte nuevo sin guardar: todo en 0/sin seleccionar)
foreach ($campos_mapeo as $campo) {
    $$campo = 0;
}

$idReporteActual = isset($_REQUEST["id"]) ? soloNumeros($_REQUEST["id"]) : 0;

// Si hay un reporte ya guardado, cargar sus valores desde reporte_cm
if ($idReporteActual > 0) {
    $sql = "SELECT * FROM reporte_cm ";
    $sql.=" WHERE id_cm = '".$idReporteActual."'";
    $PSN1->query($sql);
    if ($PSN1->num_rows() > 0 && $PSN1->next_record()) {
        foreach ($campos_mapeo as $campo) {
            $$campo = $PSN1->f($campo);
        }
    }
}

// Modo preview en vivo: si llegan valores directos por GET (reporte nuevo, aun sin id_cm),
// estos sobrescriben lo que se haya cargado de la BD.
foreach ($campos_mapeo as $campo) {
    if (isset($_REQUEST[$campo]) && $_REQUEST[$campo] !== '') {
        $$campo = soloNumeros($_REQUEST[$campo]);
    }
}

define('MAPEO_IMG_DIR', '/home/pfcoiied/public_html/mapeo_img/');

function cargarPng($nombreArchivo){
    $ruta = MAPEO_IMG_DIR . $nombreArchivo;
    if (!is_file($ruta)) {
        return false;
    }
    $img = @imagecreatefrompng($ruta);
    return $img !== false ? $img : false;
}

function superponerIcono(&$output_image, $nombreArchivo, $x, $y, $w = 150, $h = 150){
    $imagen_actual = cargarPng($nombreArchivo);
    if ($imagen_actual === false) {
        return;
    }
    $tmp = imagecreatetruecolor($w, $h);
    imagecopyresampled($tmp, $imagen_actual, 0, 0, 0, 0, $w, $h, imagesx($imagen_actual), imagesy($imagen_actual));
    imagecopymerge($output_image, $tmp, $x, $y, 0, 0, $w, $h, 100);
    imagedestroy($imagen_actual);
    imagedestroy($tmp);
}

$y_inicial = 100;
$width = 1024; // image width
$height = 1024; // image height

$background = imagecreatetruecolor($width, $height); // setting canvas size
// set background to white
$white = imagecolorallocate($background, 255, 255, 255);
imagefill($background, 0, 0, $white);
$output_image = $background;

$nombreCompromiso = ($mapeo_comprometido == 3) ? 'compromiso_no.png' : 'compromiso_si.png';
$imagen_actual = cargarPng($nombreCompromiso);
if ($imagen_actual !== false) {
    $tmp = imagecreatetruecolor($width, $height);
    imagecopyresampled($tmp, $imagen_actual, 0, 0, 0, 0, $width, $height, imagesx($imagen_actual), imagesy($imagen_actual));
    imagecopymerge($output_image, $tmp, 0, 0, 0, 0, $width, $height, 100);
    imagedestroy($imagen_actual);
    imagedestroy($tmp);
}

//EVANGELIZAR SUPERIOR MEDIO
if($mapeo_evangelizar > 0){
    $actual = "mapeo_evangelizar".$mapeo_evangelizar.".png";
    if($mapeo_evangelizar == 4){
        $actual = "mapeo_evangelizar2.png";
    }
    if($mapeo_evangelizar == 2){
        $actual = "mapeo_evangelizar1.png";
    }
    superponerIcono($output_image, $actual, 430, 35+$y_inicial);
}

//BIBLIA SUPERIOR IZQ
/*
mapeo_oracion
mapeo_companerismo
mapeo_adoracion
mapeo_biblia
mapeo_evangelizar
mapeo_cena
mapeo_dar
mapeo_bautizar
mapeo_trabajadores
*/

if($mapeo_biblia > 0){
    $actual = "mapeo_biblia".$mapeo_biblia.".png";
    if($mapeo_biblia == 4){
        $actual = "mapeo_biblia2.png";
    }
    if($mapeo_biblia == 2){
        $actual = "mapeo_biblia1.png";
    }
    superponerIcono($output_image, $actual, 200, 185+$y_inicial);
}

//CENA SUPERIOR DER
if($mapeo_cena > 0){
    $actual = "mapeo_cena".$mapeo_cena.".png";
    if($mapeo_cena == 4){
        $actual = "mapeo_cena2.png";
    }
    if($mapeo_cena == 2){
        $actual = "mapeo_cena1.png";
    }
    superponerIcono($output_image, $actual, 650, 185+$y_inicial);
}

//ADORACION MEDIO IZQ
if($mapeo_adoracion > 0){
    $actual = "mapeo_adoracion".$mapeo_adoracion.".png";
    if($mapeo_adoracion == 4){
        $actual = "mapeo_adoracion2.png";
    }
    if($mapeo_adoracion == 2){
        $actual = "mapeo_adoracion1.png";
    }
    superponerIcono($output_image, $actual, 50, 355+$y_inicial);
}

//trabajadores MEDIO CENTRAL
if($mapeo_trabajadores > 0){
    $actual = "mapeo_trabajadores".$mapeo_trabajadores.".png";
    if($mapeo_trabajadores == 4){
        $actual = "mapeo_trabajadores2.png";
    }
    if($mapeo_trabajadores == 2){
        $actual = "mapeo_trabajadores1.png";
    }
    superponerIcono($output_image, $actual, 430, 355+$y_inicial);
}

//DAR MEDIO DER
if($mapeo_dar > 0){
    $actual = "mapeo_dar".$mapeo_dar.".png";
    if($mapeo_dar == 4){
        $actual = "mapeo_dar2.png";
    }
    if($mapeo_dar == 2){
        $actual = "mapeo_dar1.png";
    }
    superponerIcono($output_image, $actual, 800, 355+$y_inicial);
}

//CIMPAÑERISMO BAJO IZQ
if($mapeo_companerismo > 0){
    $actual = "mapeo_companerismo".$mapeo_companerismo.".png";
    if($mapeo_companerismo == 4){
        $actual = "mapeo_companerismo2.png";
    }
    if($mapeo_companerismo == 2){
        $actual = "mapeo_companerismo1.png";
    }
    superponerIcono($output_image, $actual, 200, 520+$y_inicial);
}

//BAUTIZAR BAJO DER
if($mapeo_bautizar > 0){
    $actual = "mapeo_bautizar".$mapeo_bautizar.".png";
    if($mapeo_bautizar == 4){
        $actual = "mapeo_bautizar2.png";
    }
    if($mapeo_bautizar == 2){
        $actual = "mapeo_bautizar1.png";
    }
    superponerIcono($output_image, $actual, 650, 520+$y_inicial);
}

//ORACIÓN INFERIOR MEDIO
if($mapeo_oracion > 0){
    $actual = "mapeo_oracion".$mapeo_oracion.".png";
    if($mapeo_oracion == 4){
        $actual = "mapeo_oracion2.png";
    }
    if($mapeo_oracion == 2){
        $actual = "mapeo_oracion1.png";
    }
    superponerIcono($output_image, $actual, 430, 670+$y_inicial);
}

$imagen_actual = $output_image;

$y_inicial = -25;
$width = 1100; // image width
$height = 1100; // image height

$background = imagecreatetruecolor($width, $height); // setting canvas size
// set background to white
$white = imagecolorallocate($background, 255, 255, 255);
imagefill($background, 0, 0, $white);
$output_image = $background;

$tmp = imagecreatetruecolor($width, $height);
imagecopyresampled($tmp, $imagen_actual, 0, 0, 0, 0, 800, 800, 1024, 1024);
imagecopymerge($output_image, $tmp, 150, 160, 0, 0, 800, 800, 100);
imagedestroy($imagen_actual);
imagedestroy($tmp);

//EVANGELIZAR SUPERIOR MEDIO
if($mapeo_evangelizar > 0){
    $actual = ($mapeo_evangelizar == 2) ? "mapeo_evangelizar2.png" : "mapeo_evangelizar1.png";
    superponerIcono($output_image, $actual, 470, 35+$y_inicial);
}

if($mapeo_biblia > 0){
    $actual = ($mapeo_biblia == 2) ? "mapeo_biblia2.png" : "mapeo_biblia1.png";
    superponerIcono($output_image, $actual, 90, 185+$y_inicial);
}

//CENA SUPERIOR DER
if($mapeo_cena > 0){
    $actual = ($mapeo_cena == 2) ? "mapeo_cena2.png" : "mapeo_cena1.png";
    superponerIcono($output_image, $actual, 850, 185+$y_inicial);
}

//ADORACION MEDIO IZQ
if($mapeo_adoracion > 0){
    $actual = ($mapeo_adoracion == 2) ? "mapeo_adoracion2.png" : "mapeo_adoracion1.png";
    superponerIcono($output_image, $actual, 0, 385+$y_inicial);
}

//trabajadores MEDIO CENTRAL
if($mapeo_trabajadores > 0){
    $actual = ($mapeo_trabajadores == 2) ? "mapeo_trabajadores2.png" : "mapeo_trabajadores1.png";
    superponerIcono($output_image, $actual, 0, 630+$y_inicial);
}

//DAR MEDIO DER
if($mapeo_dar > 0){
    $actual = ($mapeo_dar == 2) ? "mapeo_dar2.png" : "mapeo_dar1.png";
    superponerIcono($output_image, $actual, 950, 585+$y_inicial);
}

//CIMPAÑERISMO BAJO IZQ
if($mapeo_companerismo > 0){
    $actual = ($mapeo_companerismo == 2) ? "mapeo_companerismo2.png" : "mapeo_companerismo1.png";
    superponerIcono($output_image, $actual, 90, 830+$y_inicial);
}

//BAUTIZAR BAJO DER
if($mapeo_bautizar > 0){
    $actual = ($mapeo_bautizar == 2) ? "mapeo_bautizar2.png" : "mapeo_bautizar1.png";
    superponerIcono($output_image, $actual, 850, 840+$y_inicial);
}

//ORACIÓN INFERIOR MEDIO
if($mapeo_oracion > 0){
    $actual = ($mapeo_oracion == 2) ? "mapeo_oracion2.png" : "mapeo_oracion1.png";
    superponerIcono($output_image, $actual, 470, 980+$y_inicial);
}

//
header('Content-Type: image/png');
imagepng($output_image);
imagedestroy($output_image);
exit;
