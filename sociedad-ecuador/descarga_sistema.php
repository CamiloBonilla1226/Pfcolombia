<?php
/*
 * descarga_sistema.php
 * Sirve los archivos de archivos/sistema/ de forma segura.
 * Solo usuarios autenticados con acceso al sistema pueden descargar.
 */
session_start();

// Sin sesión: denegar
if(!isset($_SESSION["id"]) || $_SESSION["id"] == ""){
    http_response_code(403);
    die("<h1>No autorizado</h1>");
}

// Perfiles sin acceso al sistema: denegar
if($_SESSION["perfil"] == 3 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 160){
    http_response_code(403);
    die("<h1>No está autorizado para ver esta información</h1>");
}

if(!isset($_GET["archivo"]) || trim($_GET["archivo"]) == ""){
    http_response_code(400);
    die("<h1>Archivo no especificado</h1>");
}

// Sanitizar: solo el nombre base, sin rutas relativas ni caracteres peligrosos
$archivo = basename($_GET["archivo"]);
$archivo = preg_replace('/[^a-zA-Z0-9._\-]/', '', $archivo);

if($archivo == ""){
    http_response_code(400);
    die("<h1>Nombre de archivo inválido</h1>");
}

$ruta = "archivos/sistema/" . $archivo;

if(!file_exists($ruta)){
    http_response_code(404);
    die("<h1>Archivo no encontrado</h1>");
}

// Tipo MIME por extensión
$extension = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
$mimeTypes = array(
    "pdf"  => "application/pdf",
    "doc"  => "application/msword",
    "docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    "xls"  => "application/vnd.ms-excel",
    "xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
    "mp4"  => "video/mp4",
    "avi"  => "video/x-msvideo",
    "mov"  => "video/quicktime",
    "webm" => "video/webm",
);
$mime = isset($mimeTypes[$extension]) ? $mimeTypes[$extension] : "application/octet-stream";

// PDF y videos: abrir en el navegador. Resto: forzar descarga
$esVideo     = in_array($extension, array("mp4","avi","mov","webm"));
$disposicion = in_array($extension, array("pdf","mp4","avi","mov","webm")) ? "inline" : "attachment";

$tamano = filesize($ruta);

// ── Soporte de Range requests (necesario para streaming de video) ──
if($esVideo && isset($_SERVER["HTTP_RANGE"])){
    // Parsear el header: bytes=inicio-fin
    if(!preg_match('/bytes=(\d*)-(\d*)/i', $_SERVER["HTTP_RANGE"], $m)){
        http_response_code(416); // Range Not Satisfiable
        header("Content-Range: bytes */" . $tamano);
        exit;
    }

    $inicio = ($m[1] !== "") ? (int)$m[1] : 0;
    $fin    = ($m[2] !== "") ? (int)$m[2] : $tamano - 1;

    if($fin >= $tamano) $fin = $tamano - 1;

    if($inicio > $fin || $inicio < 0){
        http_response_code(416);
        header("Content-Range: bytes */" . $tamano);
        exit;
    }

    $longitud = $fin - $inicio + 1;

    http_response_code(206); // Partial Content
    header("Content-Type: "     . $mime);
    header("Content-Length: "   . $longitud);
    header("Content-Range: bytes " . $inicio . "-" . $fin . "/" . $tamano);
    header("Content-Disposition: " . $disposicion . '; filename="' . $archivo . '"');
    header("Accept-Ranges: bytes");
    header("Cache-Control: private, max-age=3600");

    $fp = fopen($ruta, "rb");
    fseek($fp, $inicio);
    $restante = $longitud;
    while(!feof($fp) && $restante > 0 && !connection_aborted()){
        $chunk = min(8192, $restante);
        echo fread($fp, $chunk);
        $restante -= $chunk;
        flush();
    }
    fclose($fp);
    exit;
}

// ── Respuesta normal (sin Range) ──
header("Content-Type: "        . $mime);
header("Content-Length: "      . $tamano);
header("Content-Disposition: " . $disposicion . '; filename="' . $archivo . '"');
header("Cache-Control: private, max-age=3600");
if($esVideo){
    header("Accept-Ranges: bytes"); // Le dice al navegador que puede pedir rangos
}
readfile($ruta);
exit;