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
$disposicion = in_array($extension, array("pdf","mp4","avi","mov","webm")) ? "inline" : "attachment";

header("Content-Type: " . $mime);
header("Content-Length: " . filesize($ruta));
header('Content-Disposition: ' . $disposicion . '; filename="' . $archivo . '"');
header("Cache-Control: private, max-age=0, must-revalidate");
readfile($ruta);
exit;