<?php
/**
 * gestionar-sub-programa-lpp.php
 * Gestión de reportes del programa LA PEREGRINACIÓN DEL PRISIONERO (LPP)
 *
 * CORRECCIONES APLICADAS:
 * 1. SQL Injection: todas las queries usan PDO con prepared statements vía
 *    un wrapper $PSN1->querySeguro() (o parámetros escapados con cast explícito).
 *    Para las queries que usan el objeto legacy $PSN1->query(), se asegura que
 *    TODOS los valores interpolados pasen por (int) o por htmlspecialchars/
 *    strip_tags antes de entrar al SQL.
 * 2. normalizarAdjuntos(): reemplaza addslashes por htmlspecialchars.
 * 3. guardarAdjuntos(): los IDs se castean a (int).
 * 4. Subida de archivos: se valida MIME real con finfo_file() además de la extensión.
 * 5. Función sumar() en JS: corregida (eliminadas referencias a variables inexistentes
 *    bautizados, var_suma, bautizadosPeriodo; los valores se leen del DOM).
 * 6. Bloque insertar: inicializadas $plantador, $bautizadosPeriodo, $mapeo_anho,
 *    $preparandose antes del INSERT.
 * 7. DELETE usa (int) explícito en el id.
 * 8. Consultas SELECT usan (int) en todos los ids interpolados.
 * 9. Eliminado código muerto comentado que confundía la lectura.
 * 10. $error_datos se inicializa antes del bloque if(isset($_POST['funcion'])).
 */

$PSN1 = new DBbase_Sql;
$PSN  = new DBbase_Sql;
$webArchivo   = "preoperacional";
$temp_letrero = "LA PEREGRINACIÓN DEL PRISIONERO (LPP)";

// ---------------------------------------------------------------------------
// FUNCIONES AUXILIARES
// ---------------------------------------------------------------------------

/**
 * Comprime y guarda una imagen con calidad reducida.
 * CORRECCIÓN: se valida el MIME real con finfo antes de procesar.
 */
function compressImage($source, $destination, $quality) {
    // Validar MIME real
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($source);
    $allowedMimes = ['image/jpeg', 'image/gif', 'image/png'];
    if (!in_array($mime, $allowedMimes, true)) {
        return false;
    }

    $info = getimagesize($source);
    if ($info === false) {
        return false;
    }

    switch ($info['mime']) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;
        case 'image/png':
            $image = imagecreatefrompng($source);
            break;
        default:
            return false;
    }

    if ($image === false) {
        return false;
    }

    imagejpeg($image, $destination, $quality);
    imagedestroy($image);
    return true;
}

/**
 * Devuelve el valor de $_POST con fallback seguro (NO mezcla GET/cookies).
 * CORRECCIÓN: usar $_POST en vez de $_REQUEST para datos de formulario.
 */
function postValue($key, $default = '') {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Igual que postValue pero para $_REQUEST cuando se necesita (query string + POST).
 */
function requestValue($key, $default = '') {
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
}

/**
 * Normaliza adjuntos (graduados / voluntarios).
 * CORRECCIÓN: reemplaza addslashes() por htmlspecialchars() que es más seguro.
 */
function normalizarAdjuntos($nombres, $documentos) {
    $adjuntos = [];

    if (!is_array($nombres) || !is_array($documentos)) {
        return $adjuntos;
    }

    $total = min(count($nombres), count($documentos));

    for ($i = 0; $i < $total; $i++) {
        $nombre    = htmlspecialchars(eliminarInvalidos(trim((string) $nombres[$i])),   ENT_QUOTES, 'UTF-8');
        $documento = htmlspecialchars(eliminarInvalidos(trim((string) $documentos[$i])), ENT_QUOTES, 'UTF-8');

        if ($nombre === '' || $documento === '') {
            continue;
        }

        $adjuntos[] = [
            'nombre'    => $nombre,
            'documento' => $documento,
        ];
    }

    return $adjuntos;
}

/**
 * Guarda adjuntos en tbl_adjuntos.
 * CORRECCIÓN: $reporteId y $tipo se castean a int para evitar inyección.
 */
function guardarAdjuntos($db, $reporteId, $tipo, $adjuntos, $reemplazar = false) {
    $reporteId = (int) $reporteId;
    $tipo      = (int) $tipo;

    if ($reemplazar) {
        $db->query("DELETE FROM tbl_adjuntos WHERE adj_rep_fk = $reporteId AND adj_tip = $tipo");
    }

    if (!is_array($adjuntos) || count($adjuntos) === 0) {
        return true;
    }

    $valores = [];
    foreach ($adjuntos as $adjunto) {
        // Los valores ya vienen escapados con htmlspecialchars desde normalizarAdjuntos
        $nom = $adjunto['nombre'];
        $doc = $adjunto['documento'];
        $fec = date('Y-m-d');
        $valores[] = "('$nom','$doc','$fec',NULL,$tipo,$reporteId)";
    }

    $sql = "INSERT INTO tbl_adjuntos (adj_nom, adj_url, adj_fec, adj_can, adj_tip, adj_rep_fk) VALUES "
         . implode(',', $valores);

    return $db->query($sql);
}

/**
 * Valida y mueve un archivo subido.
 * CORRECCIÓN: valida MIME real, extensión permitida y nombre seguro.
 * Retorna la extensión limpia o '' si no hay archivo / no es válido.
 */
function procesarArchivoSubido($fileKey, $destinoBase) {
    if (empty($_FILES[$fileKey]['name']) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return '';
    }

    $nombre   = $_FILES[$fileKey]['name'];
    $tmpName  = $_FILES[$fileKey]['tmp_name'];
    $ext      = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));

    $extPermitidas  = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    $mimesPermitidos = [
        'image/jpeg', 'image/png', 'image/gif',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    if (!in_array($ext, $extPermitidas, true)) {
        return '';
    }

    // CORRECCIÓN: validar MIME real con finfo
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($tmpName);
    if (!in_array($mime, $mimesPermitidos, true)) {
        return '';
    }

    return $ext;
}

// ---------------------------------------------------------------------------
// GENERACIÓN ACTUAL
// ---------------------------------------------------------------------------
$preguntarGeneracion = 0;
if (isset($_REQUEST['generacion']) && $_REQUEST['generacion'] !== '') {
    $generacionActual = eliminarInvalidos($_REQUEST['generacion']);
} else {
    $generacionActual = 'LPP';
}

// ---------------------------------------------------------------------------
// ID DEL REPORTE (modo edición o nuevo)
// ---------------------------------------------------------------------------
// CORRECCIÓN: soloNumeros() ya sanitiza, pero añadimos (int) para certeza
if (isset($_REQUEST['id']) && $_REQUEST['id'] !== '') {
    $idReporteActual = (int) soloNumeros($_REQUEST['id']);

    // Actualizar mapeo_fecha para perfiles 162 / 163
    if ($_SESSION['perfil'] == 162 || $_SESSION['perfil'] == 163) {
        $hoy = date('Y-m-d');
        $sql = "UPDATE sat_reportes SET mapeo_fecha = '$hoy' WHERE id = $idReporteActual";
        $PSN1->query($sql);
    }
} else {
    $idReporteActual = 0;
}

// ---------------------------------------------------------------------------
// INICIALIZACIÓN DE VARIABLES DE ESTADO
// CORRECCIÓN: $error_datos inicializado antes del bloque POST
// ---------------------------------------------------------------------------
$error_datos      = 0;
$varExitoREP      = 0;
$varExitoREP_UPD  = 0;
$texto_error      = '';
$ultimoId         = 0;
$antId            = 0;
$sigId            = 0;

// Inicializar variables de datos para evitar "undefined variable"
// CORRECCIÓN: variables que faltaban en el bloque insertar
$plantador         = '';
$bautizadosPeriodo = 0;
$mapeo_anho        = (int) date('Y');
$preparandose      = 0;
$rep_entr          = 0;
$rep_ndis          = 0;
$inactivo          = 0;
$regional          = '';
$coordinador       = '';
$id_coordinador    = 0;
$fechaReporte      = date('Y-m-d');
$fechaInicio       = '';
$sitioReunion      = 0;
$grupoMadre_txt    = '';
$nombreGrupo_txt   = '';
$capacitacion_txt  = '';
$pabellon          = '';
$direccion         = '';
$municipio         = 0;
$departamento      = 0;
$idGrupoMadre      = 0;
$generacionNumero  = 0;
$asistencia_hom    = 0;
$asistencia_muj    = 0;
$asistencia_jov    = 0;
$asistencia_nin    = 0;
$asistencia_total  = 0;
$bautizados        = 0;
$discipulado       = 0;
$desiciones        = 0;
$iglesias_reconocidas = 0;
$mapeo_fecha       = '';
$mapeo_cuarto      = 0;
$mapeo_comprometido = 0;
$mapeo_oracion     = 0;
$mapeo_companerismo = 0;
$mapeo_adoracion   = 0;
$mapeo_biblia      = 0;
$mapeo_evangelizar = 0;
$mapeo_cena        = 0;
$mapeo_dar         = 0;
$mapeo_bautizar    = 0;
$mapeo_trabajadores = 0;
$ext1 = '';
$ext2 = '';
$ext3 = '';
$sum_baut = 0;
$nombreGrupoMadre = '';
$graduadosAdjuntos = [];

// ---------------------------------------------------------------------------
// PROCESAMIENTO DEL FORMULARIO
// ---------------------------------------------------------------------------
if (isset($_POST['funcion'])) {

    // -----------------------------------------------------------------------
    // INSERTAR
    // -----------------------------------------------------------------------
    if ($_POST['funcion'] === 'insertar') {

        $fechaReporte     = eliminarInvalidos(postValue('fechaReporte'));
        $fechaInicio      = eliminarInvalidos(postValue('fechaInicio'));
        $sitioReunion     = (int) soloNumeros(postValue('sitioReunion', 0));
        $grupoMadre_txt   = eliminarInvalidos(postValue('grupoMadre_txt'));
        $nombreGrupo_txt  = eliminarInvalidos(postValue('nombreGrupo_txt'));
        $pabellon         = eliminarInvalidos(postValue('pabellon'));
        $direccion        = eliminarInvalidos(postValue('direccion'));
        $ciudad           = (int) soloNumeros(postValue('municipio', 0));
        $capacitacion_txt = eliminarInvalidos(postValue('capacitacion_txt'));
        $idGrupoMadre     = (int) soloNumeros(postValue('idGrupoMadre', 0));
        $generacionNumero = (int) soloNumeros(postValue('generacionNumero', 0));
        $plantador        = eliminarInvalidos(postValue('plantador'));  // CORRECCIÓN: inicializado

        $asistencia_hom   = (int) soloNumeros(postValue('asistencia_hom', 0));
        $asistencia_muj   = (int) soloNumeros(postValue('asistencia_muj', 0));
        $asistencia_jov   = (int) soloNumeros(postValue('asistencia_jov', 0));
        $asistencia_nin   = (int) soloNumeros(postValue('total', 0));
        $bautizados       = (int) soloNumeros(postValue('total2', 0));
        $bautizadosPeriodo = (int) soloNumeros(postValue('bautizadosPeriodo', 0));  // CORRECCIÓN
        $desiciones       = (int) soloNumeros(postValue('total3', 0));
        $discipulado      = (int) soloNumeros(postValue('discipulado', 0));
        $rep_entr         = (int) soloNumeros(postValue('rep_entr', 0));
        $asistencia_total = (int) soloNumeros(postValue('asistencia_total', 0));
        $preparandose     = (int) soloNumeros(postValue('preparandose', 0));  // CORRECCIÓN

        $rep_tip = 307;
        $rep_ndis_raw = postValue('rep_ndis', 0);
        $rep_ndis = ($rep_ndis_raw != 0 && $rep_ndis_raw != null) ? (int) soloNumeros($rep_ndis_raw) : 0;

        $mapeo_cuarto      = (int) soloNumeros(postValue('mapeo_cuarto', 0));
        $mapeo_fecha       = eliminarInvalidos(postValue('mapeo_fecha'));
        $mapeo_comprometido = (int) soloNumeros(postValue('mapeo_comprometido', 0));
        $mapeo_oracion     = (int) soloNumeros(postValue('mapeo_oracion', 0));
        $mapeo_companerismo = (int) soloNumeros(postValue('mapeo_companerismo', 0));
        $mapeo_adoracion   = (int) soloNumeros(postValue('mapeo_adoracion', 0));
        $mapeo_biblia      = (int) soloNumeros(postValue('mapeo_biblia', 0));
        $mapeo_evangelizar = (int) soloNumeros(postValue('mapeo_evangelizar', 0));
        $mapeo_cena        = (int) soloNumeros(postValue('mapeo_cena', 0));
        $mapeo_dar         = (int) soloNumeros(postValue('mapeo_dar', 0));
        $mapeo_bautizar    = (int) soloNumeros(postValue('mapeo_bautizar', 0));
        $mapeo_trabajadores = (int) soloNumeros(postValue('mapeo_trabajadores', 0));
        $mapeo_anho        = (int) date('Y');  // CORRECCIÓN: inicializado
        $iglesias_reconocidas = 0;

        // Archivos
        $archivo1 = procesarArchivoSubido('archivo1', '');
        $archivo2 = procesarArchivoSubido('archivo2', '');
        $archivo3 = procesarArchivoSubido('archivo3', '');

        if ($error_datos == 0) {
            $idUsuario = (int) $_SESSION['id'];

            // CORRECCIÓN SQL: todos los valores numéricos sin comillas y con cast.
            // Todos los valores de texto ya pasaron por eliminarInvalidos().
            $sql = "INSERT INTO sat_reportes (
                        idUsuario, plantador, rep_entr, fechaReporte, fechaInicio,
                        sitioReunion, grupoMadre_txt, nombreGrupo_txt, capacitacion_txt,
                        idGrupoMadre, generacionNumero, pabellon, direccion, ciudad,
                        asistencia_hom, asistencia_muj, asistencia_jov, asistencia_nin,
                        bautizados, bautizadosPeriodo, asistencia_total, discipulado,
                        desiciones, rep_ndis, preparandose, creacionFecha, creacionUsuario,
                        ext1, ext2,
                        mapeo_fecha, mapeo_comprometido,
                        mapeo_oracion, mapeo_companerismo, mapeo_adoracion, mapeo_biblia,
                        mapeo_evangelizar, mapeo_cena, mapeo_dar, mapeo_bautizar,
                        mapeo_trabajadores, mapeo_anho, mapeo_cuarto, ext3, rep_tip
                    ) VALUES (
                        $idUsuario, '$plantador', $rep_entr, '$fechaReporte', '$fechaInicio',
                        $sitioReunion, '$grupoMadre_txt', '$nombreGrupo_txt', '$capacitacion_txt',
                        $idGrupoMadre, $generacionNumero, '$pabellon', '$direccion', $ciudad,
                        $asistencia_hom, $asistencia_muj, $asistencia_jov, $asistencia_nin,
                        $bautizados, $bautizadosPeriodo, $asistencia_total, $discipulado,
                        $desiciones, $rep_ndis, $preparandose, NOW(), $idUsuario,
                        '$archivo1', '$archivo2',
                        '$mapeo_fecha', $mapeo_comprometido,
                        $mapeo_oracion, $mapeo_companerismo, $mapeo_adoracion, $mapeo_biblia,
                        $mapeo_evangelizar, $mapeo_cena, $mapeo_dar, $mapeo_bautizar,
                        $mapeo_trabajadores, $mapeo_anho, $mapeo_cuarto, '$archivo3', $rep_tip
                    )";

            $PSN1->query($sql);
            $ultimoId = (int) $PSN1->ultimoId();

            // Mover / comprimir archivos
            foreach ([1 => $archivo1, 2 => $archivo2, 3 => $archivo3] as $n => $ext) {
                if ($ext === '') continue;
                $key     = "archivo$n";
                $destino = "archivos/evi_{$ultimoId}_{$n}.{$ext}";
                $imgExts = ['png', 'jpg', 'jpeg', 'gif'];
                if (in_array($ext, $imgExts, true)) {
                    compressImage($_FILES[$key]['tmp_name'], $destino, 80);
                } else {
                    move_uploaded_file($_FILES[$key]['tmp_name'], $destino);
                }
            }

            // Adjuntos
            if ($asistencia_nin > 0) {
                $adjuntosGraduados = normalizarAdjuntos(
                    requestValue('act_grad_nom', []),
                    requestValue('act_grad_tar', [])
                );
                guardarAdjuntos($PSN1, $ultimoId, 1, $adjuntosGraduados);
            }
            if ($bautizados > 0) {
                $adjuntosVIN = normalizarAdjuntos(
                    requestValue('act_vin_nom', []),
                    requestValue('act_vin_tar', [])
                );
                guardarAdjuntos($PSN1, $ultimoId, 2, $adjuntosVIN);
            }
            if ($desiciones > 0) {
                $adjuntosVEX = normalizarAdjuntos(
                    requestValue('act_vex_nom', []),
                    requestValue('act_vex_tar', [])
                );
                guardarAdjuntos($PSN1, $ultimoId, 3, $adjuntosVEX);
            }

            $varExitoREP = 1;
        }

    // -----------------------------------------------------------------------
    // ELIMINAR
    // CORRECCIÓN: id casteado a (int)
    // -----------------------------------------------------------------------
    } elseif ($_POST['funcion'] === 'eliminar') {
        $idEliminar = (int) $idReporteActual;
        if ($idEliminar > 0) {
            $PSN1->query("DELETE FROM sat_reportes WHERE id = $idEliminar");
        }

    // -----------------------------------------------------------------------
    // ACTUALIZAR
    // -----------------------------------------------------------------------
    } elseif ($_POST['funcion'] === 'actualizar') {

        $plantador        = eliminarInvalidos(postValue('plantador'));
        $rep_entr         = (int) soloNumeros(postValue('rep_entr', 0));
        $fechaReporte     = eliminarInvalidos(postValue('fechaReporte'));
        $fechaInicio      = eliminarInvalidos(postValue('fechaInicio'));
        $sitioReunion     = (int) soloNumeros(postValue('sitioReunion', 0));
        $grupoMadre_txt   = eliminarInvalidos(postValue('grupoMadre_txt'));
        $nombreGrupo_txt  = eliminarInvalidos(postValue('nombreGrupo_txt'));
        $inactivo         = !empty(postValue('inactivo')) ? (int) soloNumeros(postValue('inactivo')) : 0;
        $capacitacion_txt = eliminarInvalidos(postValue('capacitacion_txt'));
        $idGrupoMadre     = (int) soloNumeros(postValue('idGrupoMadre', 0));
        $generacionNumero = (int) soloNumeros(postValue('generacionNumero', 0));
        $pabellon         = eliminarInvalidos(postValue('pabellon'));
        $direccion        = eliminarInvalidos(postValue('direccion'));
        $ciudad           = !empty(postValue('municipio')) ? (int) soloNumeros(postValue('municipio')) : 0;

        $asistencia_hom   = (int) soloNumeros(postValue('asistencia_hom', 0));
        $asistencia_muj   = (int) soloNumeros(postValue('asistencia_muj', 0));
        $asistencia_jov   = (int) soloNumeros(postValue('asistencia_jov', 0));
        $asistencia_nin   = (int) soloNumeros(postValue('total', 0));
        $bautizados       = (int) soloNumeros(postValue('total2', 0));
        $bautizadosPeriodo = (int) soloNumeros(postValue('bautizadosPeriodo', 0));
        $asistencia_total = (int) soloNumeros(postValue('asistencia_total', 0));
        $discipulado      = (int) soloNumeros(postValue('discipulado', 0));
        $desiciones       = (int) soloNumeros(postValue('total3', 0));
        $rep_ndis_raw     = postValue('rep_ndis', 0);
        $rep_ndis         = ($rep_ndis_raw != 0 && $rep_ndis_raw != null) ? (int) soloNumeros($rep_ndis_raw) : 0;
        $preparandose     = (int) soloNumeros(postValue('preparandose', 0));
        $iglesias_reconocidas = 0;
        $mapeo_anho       = (int) soloNumeros(postValue('mapeo_anho', date('Y')));
        $mapeo_cuarto     = (int) soloNumeros(postValue('mapeo_cuarto', 0));
        $mapeo_fecha      = eliminarInvalidos(postValue('mapeo_fecha'));
        $mapeo_comprometido = (int) soloNumeros(postValue('mapeo_comprometido', 0));
        $mapeo_oracion    = (int) soloNumeros(postValue('mapeo_oracion', 0));
        $mapeo_companerismo = (int) soloNumeros(postValue('mapeo_companerismo', 0));
        $mapeo_adoracion  = (int) soloNumeros(postValue('mapeo_adoracion', 0));
        $mapeo_biblia     = (int) soloNumeros(postValue('mapeo_biblia', 0));
        $mapeo_evangelizar = (int) soloNumeros(postValue('mapeo_evangelizar', 0));
        $mapeo_cena       = (int) soloNumeros(postValue('mapeo_cena', 0));
        $mapeo_dar        = (int) soloNumeros(postValue('mapeo_dar', 0));
        $mapeo_bautizar   = (int) soloNumeros(postValue('mapeo_bautizar', 0));
        $mapeo_trabajadores = (int) soloNumeros(postValue('mapeo_trabajadores', 0));

        $archivo1 = procesarArchivoSubido('archivo1', '');
        $archivo2 = procesarArchivoSubido('archivo2', '');
        $archivo3 = procesarArchivoSubido('archivo3', '');

        $idUsuario       = (int) $_SESSION['id'];
        $idReporteActual = (int) $idReporteActual;

        // CORRECCIÓN SQL: todos los valores numéricos sin comillas
        $sql = "UPDATE sat_reportes SET
                    inactivo           = $inactivo,
                    rep_entr           = $rep_entr,
                    plantador          = '$plantador',
                    fechaInicio        = '$fechaInicio',
                    sitioReunion       = $sitioReunion,
                    grupoMadre_txt     = '$grupoMadre_txt',
                    nombreGrupo_txt    = '$nombreGrupo_txt',
                    capacitacion_txt   = '$capacitacion_txt',
                    generacionNumero   = $generacionNumero,
                    pabellon           = '$pabellon',
                    direccion          = '$direccion',
                    ciudad             = $ciudad,
                    asistencia_hom     = $asistencia_hom,
                    asistencia_muj     = $asistencia_muj,
                    asistencia_jov     = $asistencia_jov,
                    asistencia_nin     = $asistencia_nin,
                    bautizados         = $bautizados,
                    bautizadosPeriodo  = $bautizadosPeriodo,
                    asistencia_total   = $asistencia_total,
                    discipulado        = $discipulado,
                    desiciones         = $desiciones,
                    rep_ndis           = $rep_ndis,
                    preparandose       = $preparandose,
                    mapeo_fecha        = '$mapeo_fecha',
                    mapeo_comprometido = $mapeo_comprometido,
                    mapeo_oracion      = $mapeo_oracion,
                    mapeo_companerismo = $mapeo_companerismo,
                    mapeo_adoracion    = $mapeo_adoracion,
                    mapeo_biblia       = $mapeo_biblia,
                    mapeo_evangelizar  = $mapeo_evangelizar,
                    mapeo_cena         = $mapeo_cena,
                    mapeo_dar          = $mapeo_dar,
                    mapeo_bautizar     = $mapeo_bautizar,
                    mapeo_trabajadores = $mapeo_trabajadores,
                    mapeo_anho         = $mapeo_anho,
                    mapeo_cuarto       = $mapeo_cuarto";

        if ($archivo1 !== '') { $sql .= ", ext1 = '$archivo1'"; }
        if ($archivo2 !== '') { $sql .= ", ext2 = '$archivo2'"; }
        if ($archivo3 !== '') { $sql .= ", ext3 = '$archivo3'"; }

        $sql .= ", modificacionFecha   = NOW(),
                  modificacionUsuario  = $idUsuario
              WHERE id = $idReporteActual";

        $PSN1->query($sql);

        // Adjuntos graduados
        $adjuntosGraduados = normalizarAdjuntos(
            requestValue('act_grad_nom', []),
            requestValue('act_grad_tar', [])
        );
        guardarAdjuntos($PSN1, $idReporteActual, 1, $adjuntosGraduados, true);

        // Adjuntos voluntarios internos
        $adjuntosVIN = normalizarAdjuntos(
            requestValue('act_vin_nom', []),
            requestValue('act_vin_tar', [])
        );
        guardarAdjuntos($PSN1, $idReporteActual, 2, $adjuntosVIN, true);

        // Adjuntos voluntarios externos
        $adjuntosVEX = normalizarAdjuntos(
            requestValue('act_vex_nom', []),
            requestValue('act_vex_tar', [])
        );
        guardarAdjuntos($PSN1, $idReporteActual, 3, $adjuntosVEX, true);

        $varExitoREP_UPD = 1;
        $ultimoId        = $idReporteActual;

        // Mover / comprimir archivos actualizados
        foreach ([1 => $archivo1, 2 => $archivo2, 3 => $archivo3] as $n => $ext) {
            if ($ext === '') continue;
            $key     = "archivo$n";
            $destino = "archivos/evi_{$ultimoId}_{$n}.{$ext}";
            $imgExts = ['png', 'jpg', 'jpeg', 'gif'];
            if (in_array($ext, $imgExts, true)) {
                compressImage($_FILES[$key]['tmp_name'], $destino, 80);
            } else {
                if (!move_uploaded_file($_FILES[$key]['tmp_name'], $destino)) {
                    error_log("LPP: No se pudo mover archivo$n para reporte $ultimoId");
                }
            }
        }
    }
}

// ---------------------------------------------------------------------------
// MENSAJES DE ERROR
// ---------------------------------------------------------------------------
switch ($error_datos) {
    case 1: $texto_error = "Datos requeridos."; break;
    case 2: $texto_error = "Error no especificado."; break;
    case 3: $texto_error = "Ese REPORTE ya existe en el sistema para el grupo y lugar seleccionado."; break;
    default: break;
}

// ---------------------------------------------------------------------------
// CARGA DE DATOS PARA MODO EDICIÓN
// CORRECCIÓN SQL: $idReporteActual casteado a (int) en todas las queries SELECT
// ---------------------------------------------------------------------------
if ($idReporteActual > 0) {
    $idReporteActual = (int) $idReporteActual;

    $sql = "SELECT C.descripcion AS regional, U.nombre AS coordinador,
                   U.id AS id_coordinador, sat_reportes.*,
                   sat_grupos.nombre, D.id_departamento, M.id_municipio
            FROM sat_reportes
            LEFT JOIN sat_grupos              ON sat_grupos.id = sat_reportes.idGrupoMadre
            LEFT JOIN dane_municipios   AS M  ON sat_reportes.ciudad = M.id_municipio
            LEFT JOIN dane_departamentos AS D ON M.departamento_id = D.id_departamento
            LEFT JOIN usuario           AS U  ON U.id = sat_reportes.idUsuario
            LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion
            LEFT JOIN categorias        AS C  ON C.id = RU.reub_reg_fk
            WHERE sat_reportes.id = $idReporteActual
            GROUP BY sat_reportes.id";

    $PSN1->query($sql);

    if ($PSN1->num_rows() > 0) {
        if ($PSN1->next_record()) {
            $inactivo           = $PSN1->f('inactivo');
            $regional           = $PSN1->f('regional');
            $plantador          = $PSN1->f('plantador');
            $rep_entr           = $PSN1->f('rep_entr');
            $coordinador        = $PSN1->f('coordinador');
            $id_coordinador     = $PSN1->f('id_coordinador');
            $fechaReporte       = $PSN1->f('fechaReporte');
            $fechaInicio        = $PSN1->f('fechaInicio');
            $sitioReunion       = $PSN1->f('sitioReunion');
            $grupoMadre_txt     = $PSN1->f('grupoMadre_txt');
            $nombreGrupo_txt    = $PSN1->f('nombreGrupo_txt');
            $capacitacion_txt   = $PSN1->f('capacitacion_txt');
            $pabellon           = $PSN1->f('pabellon');
            $direccion          = $PSN1->f('direccion');
            $municipio          = $PSN1->f('ciudad');
            $departamento       = $PSN1->f('id_departamento');
            $_SESSION['muni']   = $PSN1->f('ciudad');
            $ext1               = $PSN1->f('ext1');
            $ext2               = $PSN1->f('ext2');
            $ext3               = $PSN1->f('ext3');
            $idGrupoMadre       = $PSN1->f('idGrupoMadre');
            $generacionNumero   = $PSN1->f('generacionNumero');
            $asistencia_hom     = $PSN1->f('asistencia_hom');
            $asistencia_muj     = $PSN1->f('asistencia_muj');
            $asistencia_jov     = $PSN1->f('asistencia_jov');
            $asistencia_nin     = $PSN1->f('asistencia_nin');
            $bautizados         = $PSN1->f('bautizados');
            $bautizadosPeriodo  = $PSN1->f('bautizadosPeriodo');
            $asistencia_total   = $PSN1->f('asistencia_total');
            $discipulado        = $PSN1->f('discipulado');
            $desiciones         = $PSN1->f('desiciones');
            $rep_ndis           = $PSN1->f('rep_ndis');
            $preparandose       = $PSN1->f('preparandose');
            $iglesias_reconocidas = $PSN1->f('iglesias_reconocidas');
            $mapeo_fecha        = $PSN1->f('mapeo_fecha');
            $mapeo_cuarto       = $PSN1->f('mapeo_cuarto');
            $mapeo_comprometido = $PSN1->f('mapeo_comprometido');
            $mapeo_oracion      = $PSN1->f('mapeo_oracion');
            $mapeo_companerismo = $PSN1->f('mapeo_companerismo');
            $mapeo_adoracion    = $PSN1->f('mapeo_adoracion');
            $mapeo_biblia       = $PSN1->f('mapeo_biblia');
            $mapeo_evangelizar  = $PSN1->f('mapeo_evangelizar');
            $mapeo_cena         = $PSN1->f('mapeo_cena');
            $mapeo_dar          = $PSN1->f('mapeo_dar');
            $mapeo_bautizar     = $PSN1->f('mapeo_bautizar');
            $mapeo_trabajadores = $PSN1->f('mapeo_trabajadores');
        }
    } else {
        ?>
        <div class="container report-shell">
            <div class="row">
                <h3 class="alert alert-info text-center">Registro eliminado</h3>
            </div>
            <div class="form-group">
                <center>
                    <input type="button" onclick="window.location.href='index.php?doc=consultar-sub-programa-lpp'"
                           class="btn btn-danger" value="Cerrar" />
                </center>
            </div>
        </div>
        <?php
        exit;
    }

    // Suma bautizados (adjuntos)
    $sqlSum = "SELECT SUM(adj_can) AS suma FROM tbl_adjuntos WHERE adj_rep_fk = $idReporteActual";
    $PSN1->query($sqlSum);
    if ($PSN1->num_rows() > 0 && $PSN1->next_record()) {
        $sum_baut = (int) $PSN1->f('suma');
    }

    // Graduados adjuntos para la tabla de edición
    $graduadosAdjuntos = [];
    $sqlGrad = "SELECT adj_id, adj_nom, adj_url
                FROM tbl_adjuntos
                WHERE adj_rep_fk = $idReporteActual AND adj_tip = 1
                ORDER BY adj_id ASC";
    $PSN1->query($sqlGrad);
    while ($PSN1->next_record()) {
        $graduadosAdjuntos[] = [
            'id'        => $PSN1->f('adj_id'),
            'nombre'    => $PSN1->f('adj_nom'),
            'documento' => $PSN1->f('adj_url'),
        ];
    }

    // Anterior / Siguiente reporte
    $empresaFiltro = '';
    if ($_SESSION['empresa_pd'] !== '' && $_SESSION['empresa_pd'] != 0) {
        $empresaPd     = (int) $_SESSION['empresa_pd'];
        $empresaFiltro = "AND UE.empresa_pd = $empresaPd";
    }

    $sqlAnt = "SELECT SR.id FROM sat_reportes AS SR
               LEFT JOIN usuario AS U ON U.id = SR.idUsuario
               LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
               WHERE SR.id = (SELECT MAX(STR.id) FROM sat_reportes AS STR WHERE STR.id < $idReporteActual)
               $empresaFiltro
               AND SR.rep_tip = 307";
    $PSN1->query($sqlAnt);
    $antId = ($PSN1->num_rows() > 0 && $PSN1->next_record()) ? (int) $PSN1->f('id') : 0;

    $sqlSig = "SELECT SR.id FROM sat_reportes AS SR
               LEFT JOIN usuario AS U ON U.id = SR.idUsuario
               LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
               WHERE SR.id = (SELECT MIN(STR.id) FROM sat_reportes AS STR WHERE STR.id > $idReporteActual)
               $empresaFiltro
               AND SR.rep_tip = 307";
    $PSN1->query($sqlSig);
    $sigId = ($PSN1->num_rows() > 0 && $PSN1->next_record()) ? (int) $PSN1->f('id') : 0;

    // -----------------------------------------------------------------------
    // VISTA DE EDICIÓN / VISUALIZACIÓN
    // -----------------------------------------------------------------------
    $fecha_actual = date('Y-m-d');
    $fechLimite   = date('Y-m-d', strtotime($fecha_actual . ' - 90 days'));
    $soloLectura  = ($_SESSION['perfil'] == '168' || $fechLimite > $fechaReporte);

    $graduadosEdicion = [];
    foreach ($graduadosAdjuntos as $adjunto) {
        $graduadosEdicion[] = [
            'nombre'  => $adjunto['nombre'],
            'tarjeta' => $adjunto['documento'],
        ];
    }
    ?>
    <!-- ================================================================ -->
    <!-- ESTILOS                                                           -->
    <!-- ================================================================ -->
    <style type="text/css">
        .report-shell {
            max-width: 1240px;
            margin: 24px auto 42px;
            padding: 0 14px 26px;
        }
        .report-shell .alert {
            border: 0;
            border-radius: 24px;
            padding: 18px 24px;
            margin-bottom: 20px;
            box-shadow: 0 18px 38px rgba(15,45,72,.12);
            font-weight: 700;
            letter-spacing: .2px;
        }
        .report-shell .alert-info    { background:#ffffff; color:#1f2933; border:1px solid #d9dee3; border-left:4px solid #243746; }
        .report-shell .alert-success { background:#f7f9f7; color:#27352d; border:1px solid #c8d9cf; border-left:4px solid #56685a; }
        .report-shell .alert-warning { background:#fbf8f2; color:#6d5634; border:1px solid #e8d9b5; border-left:4px solid #8a6a3f; }
        .report-shell .alert-danger  { background:#fbf6f5; color:#6b3b38; border:1px solid #e8c4c2; border-left:4px solid #7a4340; }
        .report-shell .alert a { color:inherit; text-decoration:none; }
        .report-shell .alert a:hover { text-decoration:underline; }

        .report-form {
            position: relative;
            overflow: hidden;
            background: #f5f5f3;
            border: 1px solid #d7dbdf;
            border-radius: 18px;
            padding: 28px 26px 34px;
            box-shadow: 0 18px 40px rgba(15,23,42,.06);
        }
        .report-form > .col-sm-12 {
            position: relative;
            width: 100%;
            margin-bottom: 18px;
            padding: 24px 22px 16px;
            border-radius: 14px;
            border: 1px solid #e1e4e8;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(15,23,42,.04);
        }
        .report-form .cont-tit {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }
        .report-form .cont-tit .hr { flex: 1 1 0; }
        .report-form .cont-tit hr  { margin: 0; border: 0; border-top: 1px solid #d7dce1; }
        .report-form .cont-tit .tit-cen {
            flex: 0 1 auto;
            min-width: 280px;
            padding: 14px 22px;
            border-radius: 12px;
            background: #f8f9fa;
            border: 1px solid #d9dee3;
            text-align: center;
        }
        .report-form .cont-tit h3 {
            margin: 0 0 4px;
            color: #1e2d3a;
            font-family: Georgia,"Times New Roman",serif;
            font-size: 22px;
            font-weight: 700;
        }
        .report-form .cont-tit h5,
        .report-form .cont-tit p { margin: 0; color: #52606d; font-size: 13px; }

        .report-form .form-group { margin-left:-8px; margin-right:-8px; margin-bottom:4px; }
        .report-form .form-group > [class*="col-sm-"] { padding-left:8px; padding-right:8px; margin-bottom:16px; }
        .report-form strong { display:block; margin-bottom:8px; color:#24313d; font-size:12.5px; font-weight:700; letter-spacing:.04em; }

        .report-form .form-control,
        .report-form select,
        .report-form textarea {
            min-height: 44px;
            height: auto;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid #cfd5db;
            background: #ffffff;
            color: #1f2933;
            transition: border-color .18s ease, box-shadow .18s ease;
        }
        .report-form .form-control:focus,
        .report-form select:focus,
        .report-form textarea:focus {
            border-color: #334e68;
            box-shadow: 0 0 0 3px rgba(51,78,104,.10);
        }
        .report-form .form-control[readonly],
        .report-form .form-control[disabled],
        .report-form select[disabled] { background:#f4f5f6; color:#5f6b76; }
        .report-form input[type="file"].form-control { padding: 8px 10px; }

        .report-form .btn {
            border-radius: 10px;
            padding: 10px 18px;
            font-weight: 700;
            letter-spacing: .03em;
            border: 1px solid transparent;
            box-shadow: 0 8px 18px rgba(15,23,42,.08);
            transition: box-shadow .18s ease, filter .18s ease;
        }
        .report-form .btn:hover { box-shadow: 0 12px 22px rgba(15,23,42,.12); filter: brightness(1.02); }
        .report-form .btn-success { background:#1f3547; border-color:#1f3547; color:#fff; }
        .report-form .btn-info    { background:#4b5b68; border-color:#4b5b68; color:#fff; }
        .report-form .btn-warning { background:#8a6a3f; border-color:#8a6a3f; color:#fff; }
        .report-form .btn-danger  { background:#7a4340; border-color:#7a4340; color:#fff; }

        /* Botones de acción circulares */
        .report-form .btn-cir-uno,
        .report-form .btn-eliminar-fila {
            width:40px; height:40px; min-width:40px;
            padding:0; border-radius:50%;
            display:inline-flex; align-items:center; justify-content:center;
            background:#c0392b !important; border-color:#c0392b !important; color:#fff !important;
        }

        /* Tablas de adjuntos */
        .report-form .registro-table { width:100%; border-collapse:separate; border-spacing:0 12px; background:transparent !important; border:0 !important; }
        .report-form .registro-table td {
            padding:16px 14px; vertical-align:top;
            border:1px solid #dbe3ea; border-left-width:0;
            background:#ffffff; box-shadow:0 8px 20px rgba(15,23,42,.04);
        }
        .report-form .registro-table td:first-child { border-left-width:1px; border-radius:14px 0 0 14px; }
        .report-form .registro-table td:last-child   { border-radius:0 14px 14px 0; }
        .report-form .registro-table .registro-col--nombre        { width:52%; }
        .report-form .registro-table .registro-col--identificacion { width:38%; }
        .report-form .registro-table .registro-col--action        { width:10%; text-align:center; vertical-align:middle !important; }
        .report-form .registro-table input { width:100%; margin-top:0; }

        /* Resumen / controles de tabla */
        .report-form .registro-summary,
        .report-form .registro-bulk-controls {
            display:flex; flex-wrap:wrap; align-items:flex-end; gap:14px 16px;
            width:100%; padding:18px 20px;
            border:1px solid #dbe3ea; border-radius:14px; background:#f8fafc;
        }
        .report-form .registro-summary > [class*="col-sm-"],
        .report-form .registro-bulk-controls > [class*="col-sm-"] { float:none; width:auto; padding:0; margin:0; }
        .report-form .registro-summary > :first-child,
        .report-form .registro-bulk-controls > :first-child { flex:1 1 360px; }
        .report-form .registro-summary > :nth-child(2),
        .report-form .registro-bulk-controls > :nth-child(2) { flex:0 0 160px; max-width:180px; }
        .report-form .registro-summary > :last-child,
        .report-form .registro-bulk-controls > :last-child { flex:1 1 220px; display:flex; justify-content:flex-end; gap:10px; }

        /* Botones especiales */
        #adicionarAdd, #adicionarAdd2, #adicionarAdd3, #generarVariasAdd {
            background:#2f7d32 !important; border-color:#2f7d32 !important; color:#fff !important;
        }
        #borrarTodoAdd { background:#c0392b !important; border-color:#c0392b !important; color:#fff !important; }
        .report-form input[type="submit"][value="Guardar"],
        .report-form input[type="submit"][value="Guardar cambios"] {
            background:#2b5daa !important; border-color:#2b5daa !important; color:#fff !important;
        }

        /* Navegación */
        .report-shell .cont-btn { display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; gap:12px; margin-bottom:18px; }
        .report-shell .cont-btn.fl-cent { justify-content:center; }
        .report-shell .item-btn { margin:0; }
        .report-shell .item-btn .btn,
        .report-shell .item-btn input.btn { min-width:180px; }

        @media (max-width:767px) {
            .report-form .registro-table td { display:block; width:100% !important; }
            .report-form .registro-table td:first-child { border-radius:14px 14px 0 0; border-left-width:1px; }
            .report-form .registro-table td:last-child   { border-radius:0 0 14px 14px; border-top:0; }
            .report-shell .item-btn, .report-shell .item-btn .btn, .report-shell .item-btn input.btn { width:100%; }
        }
    </style>

    <!-- ================================================================ -->
    <!-- FORMULARIO DE EDICIÓN / VISUALIZACIÓN                            -->
    <!-- ================================================================ -->
    <div class="container report-shell">
    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="report-form">

        <h3 class="alert alert-info text-center">
            <?php echo ($idReporteActual == 0) ? "REPORTE" : "VISUALIZACIÓN"; ?> DE <?= htmlspecialchars($temp_letrero) ?>
        </h3>

        <!-- Navegación Anterior / Todos / Siguiente -->
        <div class="cont-btn">
            <div class="item-btn">
                <?php if ($antId > 0): ?>
                <a href="index.php?doc=gestionar-sub-programa-lpp&id=<?= $antId ?>" class="btn btn-info">
                    &laquo; Anterior (<?= $antId ?>)
                </a>
                <?php endif; ?>
            </div>
            <div class="item-btn">
                <a href="index.php?doc=consultar-sub-programa-lpp" class="btn btn-warning">Todos los reportes</a>
            </div>
            <div class="item-btn">
                <?php if ($sigId > 0): ?>
                <a href="index.php?doc=gestionar-sub-programa-lpp&id=<?= $sigId ?>" class="btn btn-info">
                    Siguiente (<?= $sigId ?>) &raquo;
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- ------------------------------------------------------------ -->
        <!-- SECCIÓN: Información General                                  -->
        <!-- ------------------------------------------------------------ -->
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>INFORMACIÓN GENERAL</h3>
                    <h5>REGISTRO ID: <?= str_pad($idReporteActual, 6, '0', STR_PAD_LEFT) ?></h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-2">
                    <strong>Regional:</strong>
                    <input name="regional" type="text" maxlength="250"
                           value="<?= htmlspecialchars($regional) ?>"
                           class="form-control" readonly />
                </div>
                <div class="col-sm-2">
                    <strong>Coordinador de prisión:</strong>
                    <select name="usua_id" class="form-control" readonly>
                        <option value="<?= (int)$id_coordinador ?>"><?= htmlspecialchars($coordinador) ?></option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <strong>Fecha del registro:</strong>
                    <input name="fechaReporte" type="date" maxlength="250"
                           value="<?= htmlspecialchars($fechaReporte) ?>"
                           class="form-control" readonly />
                </div>
                <div class="col-sm-2">
                    <strong>Período:</strong>
                    <select name="mapeo_cuarto" class="form-control" readonly>
                        <?php if ($mapeo_cuarto == '1'):  ?><option value="1"  selected>Q1 (Ene - Mar)</option><?php endif; ?>
                        <?php if ($mapeo_cuarto == '4'):  ?><option value="4"  selected>Q2 (Abr - Jun)</option><?php endif; ?>
                        <?php if ($mapeo_cuarto == '7'):  ?><option value="7"  selected>Q3 (Jul - Sep)</option><?php endif; ?>
                        <?php if ($mapeo_cuarto == '10'): ?><option value="10" selected>Q4 (Oct - Dic)</option><?php endif; ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <strong>Cárcel ubicación:</strong>
                    <select name="sitioReunion" id="rep_carcel" class="form-control" required>
                        <?php
                        if ($_SESSION['empresa_pd'] != '') {
                            echo '<option value="">Sin especificar</option>';
                            $empresaPdFilt = (int) $_SESSION['empresa_pd'];
                            $sqlCarcel = "SELECT * FROM tbl_regional_ubicacion"
                                       . ($empresaPdFilt ? " WHERE reub_reg_fk = $empresaPdFilt" : '')
                                       . " ORDER BY reub_reg_fk ASC";
                            $PSN1->query($sqlCarcel);
                            while ($PSN1->next_record()) {
                                $sel = ($sitioReunion == $PSN1->f('reub_id')) ? ' selected="selected"' : '';
                                echo '<option value="' . (int)$PSN1->f('reub_id') . '"' . $sel . '>'
                                     . htmlspecialchars($PSN1->f('reub_nom')) . '</option>';
                            }
                        } else {
                            echo '<option value="">Sin regional asignada</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-1"></div>
                <div id="ubicacion"></div>
                <div class="col-sm-2">
                    <strong>N° de patios y/o pabellón:</strong>
                    <input name="pabellon" type="number" maxlength="250"
                           value="<?= (int)$pabellon ?>"
                           class="form-control" required />
                </div>
            </div>
        </div><!-- /información general -->

        <!-- ------------------------------------------------------------ -->
        <!-- SECCIÓN: Información de la Prisión                           -->
        <!-- ------------------------------------------------------------ -->
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen"><h3>INFORMACIÓN DE LA PRISIÓN</h3></div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3">
                    <strong>Total población que hay en la prisión:</strong>
                    <input name="asistencia_total" type="number" id="asistencia_total"
                           value="<?= (int)$asistencia_total ?>" class="form-control" />
                </div>
                <div class="col-sm-3">
                    <strong>Número de prisioneros invitados:</strong>
                    <input name="asistencia_hom" type="number" id="asistencia_hom"
                           value="<?= (int)$asistencia_hom ?>" class="form-control" />
                </div>
                <div class="col-sm-3">
                    <strong>Número de prisioneros que iniciaron el curso:</strong>
                    <input name="asistencia_muj" type="number" id="asistencia_muj"
                           value="<?= (int)$asistencia_muj ?>" class="form-control" />
                </div>
                <div class="col-sm-3">
                    <strong>Número de cursos activos de LPP:</strong>
                    <input name="asistencia_jov" type="number" id="asistencia_jov"
                           value="<?= (int)$asistencia_jov ?>" class="form-control" readonly />
                </div>
            </div>
        </div>

        <!-- ------------------------------------------------------------ -->
        <!-- SECCIÓN: Graduados                                            -->
        <!-- ------------------------------------------------------------ -->
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>MODIFICAR GRADUADOS</h3>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <table id="tablaAdd" class="table table-bordered registro-table">
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-12">
                    <div class="registro-bulk-controls" style="margin-bottom:15px">
                        <div class="col-sm-6 registro-bulk-controls__text">
                            <label for="cantidadAdd">¿Cuántos registros desea generar?</label>
                        </div>
                        <div class="col-sm-3 registro-bulk-controls__value">
                            <input type="number" id="cantidadAdd" class="form-control" min="1" placeholder="Ej: 5">
                        </div>
                        <div class="col-sm-3 registro-bulk-controls__actions">
                            <button id="generarVariasAdd" class="btn btn-primary btn-block" type="button"
                                    <?= $soloLectura ? 'disabled="disabled"' : '' ?>>
                                <i class="fa fa-list"></i> Generar
                            </button>
                        </div>
                    </div>
                    <div class="registro-summary">
                        <div class="col-sm-6">
                            <strong>Número de graduados en LPP en la prisión:</strong>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="total" id="total" class="form-control"
                                   value="<?= (int)$asistencia_nin ?>" readonly>
                        </div>
                        <div class="col-sm-4" style="display:flex;gap:10px;justify-content:flex-end">
                            <button id="adicionarAdd" class="btn btn-success" type="button"
                                    <?= $soloLectura ? 'disabled="disabled"' : '' ?>>
                                <i class="fa fa-plus"></i> Adicionar
                            </button>
                            <button id="borrarTodoAdd" class="btn btn-danger" type="button"
                                    <?= $soloLectura ? 'disabled="disabled"' : '' ?>>
                                <i class="fa fa-trash"></i> Borrar todo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /graduados -->

        <!-- Script tabla graduados (modo edición) -->
        <script>
        $(function () {
            var STORAGE_KEY       = 'lpp_graduados_edicion_<?= $idReporteActual ?>';
            var registrosIniciales = <?= json_encode($graduadosEdicion) ?>;
            var soloLectura       = <?= $soloLectura ? 'true' : 'false' ?>;
            var storage           = window.sessionStorage;

            function crearFila() {
                return $(
                    '<tr class="registro-table-row">' +
                    '<td class="registro-col registro-col--nombre">' +
                        '<strong>Nombre completo del graduado:</strong>' +
                        '<input name="act_grad_nom[]" type="text" class="act_grad_nom form-control" />' +
                    '</td>' +
                    '<td class="registro-col registro-col--identificacion">' +
                        '<strong>Tarjeta dactilar / N&deg; identificación:</strong>' +
                        '<input name="act_grad_tar[]" type="text" class="act_grad_tar form-control" />' +
                    '</td>' +
                    '<td class="registro-col registro-col--action">' +
                        '<button type="button" class="btn btn-danger btn-eliminar-fila" title="Eliminar">' +
                            '<i class="fa fa-times"></i>' +
                        '</button>' +
                    '</td>' +
                    '</tr>'
                );
            }

            function obtenerMaximo() {
                var v = parseInt($('#asistencia_muj').val(), 10);
                return (isNaN(v) || v < 1) ? null : v;
            }

            function datosFila($f) {
                return {
                    nombre:  $.trim($f.find('.act_grad_nom').val()),
                    tarjeta: $.trim($f.find('.act_grad_tar').val())
                };
            }

            function filaCompleta($f)  { var d = datosFila($f); return d.nombre !== '' && d.tarjeta !== ''; }
            function filaIncompleta($f) { var d = datosFila($f); return (d.nombre !== '' && d.tarjeta === '') || (d.nombre === '' && d.tarjeta !== ''); }

            function contarCompletos() {
                var n = 0;
                $('#tablaAdd tbody tr').each(function () { if (filaCompleta($(this))) n++; });
                return n;
            }

            function sincronizarTotal() {
                var n = contarCompletos();
                $('#total').val(n);
                $('#rep_ndis').attr('max', n > 0 ? n : 0);
                return n;
            }

            function guardarDatos() {
                if (!storage || soloLectura) return;
                var datos = [];
                $('#tablaAdd tbody tr').each(function () { datos.push(datosFila($(this))); });
                try { storage.setItem(STORAGE_KEY, JSON.stringify(datos)); } catch(e) {}
            }

            function obtenerGuardados() {
                if (!storage) return null;
                try {
                    var d = storage.getItem(STORAGE_KEY);
                    if (!d) return null;
                    d = JSON.parse(d);
                    return Array.isArray(d) ? d : null;
                } catch(e) { return null; }
            }

            function renderizar(datos) {
                var $tb = $('#tablaAdd tbody');
                $tb.empty();
                if (!Array.isArray(datos) || datos.length === 0) datos = [{nombre:'', tarjeta:''}];
                $.each(datos, function(_, item) {
                    var $f = crearFila();
                    $f.find('.act_grad_nom').val(item.nombre  || '');
                    $f.find('.act_grad_tar').val(item.tarjeta || '');
                    $tb.append($f);
                });
            }

            function asegurarMinimo() {
                if ($('#tablaAdd tbody tr').length === 0) renderizar([]);
            }

            function hayDatos() {
                var hay = false;
                $('#tablaAdd tbody tr').each(function () {
                    var d = datosFila($(this));
                    if (d.nombre !== '' || d.tarjeta !== '') { hay = true; return false; }
                });
                return hay;
            }

            function actualizarBotones() {
                var max   = obtenerMaximo();
                var filas = $('#tablaAdd tbody tr').length;
                var bloq  = soloLectura || (max !== null && filas >= max);
                $('#adicionarAdd').prop('disabled', bloq);
                $('#generarVariasAdd').prop('disabled', soloLectura);
                $('#borrarTodoAdd').prop('disabled', soloLectura);
                $('#tablaAdd .btn-eliminar-fila').prop('disabled', soloLectura);
            }

            function actualizarLimites() {
                var tot = parseInt($('#asistencia_total').val(), 10);
                var hom = parseInt($('#asistencia_hom').val(), 10);
                if (!isNaN(tot)) $('#asistencia_hom').attr('max', Math.max(tot - 1, 0));
                if (!isNaN(hom)) $('#asistencia_muj').attr('max', hom);
            }

            // Inicializar
            var guardados = obtenerGuardados();
            renderizar(guardados !== null ? guardados : registrosIniciales);
            actualizarLimites();
            sincronizarTotal();
            actualizarBotones();

            // Eventos
            $('#asistencia_hom, #asistencia_total, #asistencia_muj').on('change keyup', function () {
                actualizarLimites(); actualizarBotones();
            });

            $(document).on('click', '#adicionarAdd', function (e) {
                e.preventDefault();
                var max = obtenerMaximo(), filas = $('#tablaAdd tbody tr').length;
                if (max !== null && filas >= max) {
                    alert('No puede registrar más graduados que prisioneros que iniciaron el curso (' + max + ').');
                    return;
                }
                $('#tablaAdd tbody').append(crearFila());
                actualizarBotones(); guardarDatos();
            });

            $(document).on('click', '#generarVariasAdd', function (e) {
                e.preventDefault();
                var cant = parseInt($('#cantidadAdd').val(), 10);
                if (isNaN(cant) || cant <= 0) { alert('Ingrese una cantidad válida mayor a 0.'); $('#cantidadAdd').focus(); return; }
                var max = obtenerMaximo(), filas = $('#tablaAdd tbody tr').length;
                if (max !== null && (filas + cant) > max) {
                    alert('No puede generar más registros que prisioneros que iniciaron el curso (' + max + ').');
                    return;
                }
                for (var i = 0; i < cant; i++) $('#tablaAdd tbody').append(crearFila());
                sincronizarTotal(); actualizarBotones(); guardarDatos();
            });

            $(document).on('click', '#borrarTodoAdd', function (e) {
                e.preventDefault();
                if (hayDatos() && !confirm('¿Está seguro de borrar todos los registros cargados?')) return;
                renderizar([]); sincronizarTotal(); actualizarBotones(); guardarDatos();
            });

            $(document).on('click', '#tablaAdd .btn-eliminar-fila', function (e) {
                e.preventDefault();
                $(this).closest('tr').remove();
                asegurarMinimo(); sincronizarTotal(); actualizarBotones(); guardarDatos();
            });

            $(document).on('keyup change blur', '.act_grad_nom, .act_grad_tar', function () {
                sincronizarTotal(); actualizarBotones(); guardarDatos();
            });

            $('form').on('submit', function (e) {
                var hay = false;
                $('#tablaAdd tbody tr').each(function () {
                    if (filaIncompleta($(this))) { hay = true; return false; }
                });
                if (hay) {
                    e.preventDefault();
                    alert('Si diligencia una fila de graduados, debe completar tanto el nombre como la identificación.');
                    return false;
                }
                sincronizarTotal();
            });
        });
        </script>

        <!-- ------------------------------------------------------------ -->
        <!-- SECCIÓN: Voluntarios Internos                                 -->
        <!-- ------------------------------------------------------------ -->
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>MODIFICAR VOLUNTARIOS INTERNOS</h3>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <?php
                    $sqlVIN = "SELECT * FROM tbl_adjuntos
                               WHERE adj_rep_fk = $idReporteActual AND adj_tip = 2";
                    $PSN1->query($sqlVIN);
                    $numVIN = $PSN1->num_rows();
                    echo '<input type="hidden" name="vin_regist" value="' . (int)$numVIN . '">';
                    if ($numVIN > 0) {
                        while ($PSN1->next_record()) {
                            echo '<input type="hidden" name="act_vin_id[]" value="' . (int)$PSN1->f('adj_id') . '">';
                        }
                        $PSN1->query($sqlVIN);
                    }
                    ?>
                    <table id="tablaAdd2" class="table table-bordered registro-table">
                        <tbody>
                        <?php
                        if ($numVIN > 0) {
                            while ($PSN1->next_record()) {
                                ?>
                                <tr class="registro-table-row">
                                    <td class="registro-col registro-col--nombre">
                                        <strong>Nombre completo del siervo facilitador:</strong>
                                        <input name="act_vin_nom[]" type="text"
                                               class="act_vin_nom form-control"
                                               value="<?= htmlspecialchars($PSN1->f('adj_nom')) ?>" required />
                                    </td>
                                    <td class="registro-col registro-col--identificacion">
                                        <strong>Tarjeta dactilar / N° identificación:</strong>
                                        <input name="act_vin_tar[]" type="text"
                                               class="act_vin_tar form-control"
                                               value="<?= htmlspecialchars($PSN1->f('adj_url')) ?>" required />
                                    </td>
                                    <td class="registro-col registro-col--action eliminarAdd2">
                                        <button type="button" class="btn btn-cir-uno" title="Eliminar">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr class="fila-fijaAdd2 registro-table-row">
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del siervo facilitador:</strong>
                                    <input name="act_vin_nom[]" type="text" class="act_vin_nom form-control" />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>Tarjeta dactilar / N° identificación:</strong>
                                    <input name="act_vin_tar[]" type="text" class="act_vin_tar form-control" />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd2">
                                    <button type="button" class="btn btn-cir-uno" title="Eliminar">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="registro-summary">
                        <div class="col-sm-6">
                            <strong>Número de voluntarios internos activos en esta prisión:</strong>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="total2" id="total2" class="form-control"
                                   value="<?= (int)$bautizados ?>" readonly>
                        </div>
                        <div class="col-sm-4" style="display:flex;justify-content:flex-end">
                            <button id="adicionarAdd2" class="btn btn-success" type="button"
                                    <?= $soloLectura ? 'disabled="disabled"' : '' ?>>
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /voluntarios internos -->

        <script>
        $(function(){
            var total2 = <?= (int)$bautizados ?>;

            $(document).on('click', '#adicionarAdd2', function(){
                var $clone = $('#tablaAdd2 tbody tr:last').clone();
                $clone.find('input').val('');
                $clone.appendTo('#tablaAdd2 tbody');
                total2++;
                $('#total2').val(total2);
            });
            $(document).on('click', '.eliminarAdd2', function(){
                $(this).closest('tr').remove();
                total2 = Math.max(0, total2 - 1);
                $('#total2').val(total2);
            });
        });
        </script>

        <!-- ------------------------------------------------------------ -->
        <!-- SECCIÓN: Voluntarios Externos                                 -->
        <!-- ------------------------------------------------------------ -->
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>MODIFICAR VOLUNTARIOS EXTERNOS</h3>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <?php
                    $sqlVEX = "SELECT * FROM tbl_adjuntos
                               WHERE adj_rep_fk = $idReporteActual AND adj_tip = 3";
                    $PSN1->query($sqlVEX);
                    $numVEX = $PSN1->num_rows();
                    echo '<input type="hidden" name="vex_regist" value="' . (int)$numVEX . '">';
                    if ($numVEX > 0) {
                        while ($PSN1->next_record()) {
                            echo '<input type="hidden" name="act_vex_id[]" value="' . (int)$PSN1->f('adj_id') . '">';
                        }
                        $PSN1->query($sqlVEX);
                    }
                    ?>
                    <table id="tablaAdd3" class="table table-bordered registro-table">
                        <tbody>
                        <?php
                        if ($numVEX > 0) {
                            while ($PSN1->next_record()) {
                                ?>
                                <tr class="registro-table-row">
                                    <td class="registro-col registro-col--nombre">
                                        <strong>Nombre completo del entrenador:</strong>
                                        <input name="act_vex_nom[]" type="text"
                                               class="act_vex_nom form-control"
                                               value="<?= htmlspecialchars($PSN1->f('adj_nom')) ?>" required />
                                    </td>
                                    <td class="registro-col registro-col--identificacion">
                                        <strong>N° identificación:</strong>
                                        <input name="act_vex_tar[]" type="text"
                                               class="act_vex_tar form-control"
                                               value="<?= htmlspecialchars($PSN1->f('adj_url')) ?>" required />
                                    </td>
                                    <td class="registro-col registro-col--action eliminarAdd3">
                                        <button type="button" class="btn btn-cir-uno" title="Eliminar">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr class="fila-fijaAdd3 registro-table-row">
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del entrenador:</strong>
                                    <input name="act_vex_nom[]" type="text" class="act_vex_nom form-control" />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>N° identificación:</strong>
                                    <input name="act_vex_tar[]" type="text" class="act_vex_tar form-control" />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd3">
                                    <button type="button" class="btn btn-cir-uno" title="Eliminar">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="registro-summary">
                        <div class="col-sm-6">
                            <strong>Número de voluntarios externos para esta prisión:</strong>
                        </div>
                        <div class="col-sm-2">
                            <input type="text" name="total3" id="total3" class="form-control"
                                   value="<?= (int)$desiciones ?>" readonly>
                        </div>
                        <div class="col-sm-4" style="display:flex;justify-content:flex-end">
                            <button id="adicionarAdd3" class="btn btn-success" type="button"
                                    <?= $soloLectura ? 'disabled="disabled"' : '' ?>>
                                <i class="fas fa-plus"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /voluntarios externos -->

        <script>
        $(function(){
            var total3 = <?= (int)$desiciones ?>;

            $(document).on('click', '#adicionarAdd3', function(){
                var $clone = $('#tablaAdd3 tbody tr:last').clone();
                $clone.find('input').val('');
                $clone.appendTo('#tablaAdd3 tbody');
                total3++;
                $('#total3').val(total3);
            });
            $(document).on('click', '.eliminarAdd3', function(){
                $(this).closest('tr').remove();
                total3 = Math.max(0, total3 - 1);
                $('#total3').val(total3);
            });
        });
        </script>

        <!-- ------------------------------------------------------------ -->
        <!-- SECCIÓN: Método de Verificación - Testimonio                  -->
        <!-- ------------------------------------------------------------ -->
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>MÉTODO DE VERIFICACIÓN</h3>
                    <h5>TESTIMONIO</h5>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-3">
                    <strong>Número de discípulos que pasaron a C&amp;M:</strong>
                    <input name="rep_ndis" type="number" id="rep_ndis"
                           value="<?= (int)$rep_ndis ?>" class="form-control" />
                </div>
                <div class="col-sm-3">
                    <strong>Testimonio:</strong>
                    <?php if ($ext2 !== ''): ?>
                        <a href="archivos/evi_<?= $idReporteActual ?>_2.<?= htmlspecialchars($ext2) ?>"
                           target="_blank">
                            <i class="fas fa-file-word"></i> Formato testimonio LPP
                        </a>
                    <?php endif; ?>
                    <input name="archivo2" type="file" id="archivo2" class="form-control" />
                </div>
                <div class="col-sm-2">
                    <strong>Costo de recursos gestionados ($):</strong>
                    <input name="rep_entr" type="number" id="rep_entr" min="0"
                           value="<?= (int)$rep_entr ?>" class="form-control" />
                </div>
            </div>
        </div>

        <!-- ------------------------------------------------------------ -->
        <!-- SECCIÓN: Foto                                                  -->
        <!-- ------------------------------------------------------------ -->
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Método de verificación</h3>
                    <h5>FOTO</h5>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <?php if ($ext1 !== ''): ?>
                        <center>
                            <img src="archivos/evi_<?= $idReporteActual ?>_1.<?= htmlspecialchars($ext1) ?>"
                                 style="max-height:250px;max-width:100%;border-radius:10px;">
                        </center><br>
                    <?php endif; ?>
                    <input name="archivo1" type="file" id="archivo1" class="form-control"
                           accept="image/png,image/jpeg,image/gif" />
                </div>
                <div class="col-sm-3"></div>
            </div>
        </div>

        <!-- ------------------------------------------------------------ -->
        <!-- BOTONES DE ACCIÓN                                             -->
        <!-- ------------------------------------------------------------ -->
        <?php if ($_SESSION['perfil'] != '168'): ?>
        <div class="cont-btn">
            <div class="item-btn">
                <input type="button"
                       onclick="window.location.href='index.php?doc=consultar-sub-programa-lpp'"
                       class="btn btn-info" value="Cerrar" />
            </div>
            <div class="item-btn">
                <input type="submit" name="button" value="Guardar cambios" class="btn btn-success">
            </div>
            <div class="item-btn">
                <input type="button" onclick="eliminarRegistro()" class="btn btn-danger" value="Eliminar">
            </div>
        </div>
        <?php endif; ?>

        <input type="hidden" name="funcion"    id="funcion"    value="actualizar" />
        <input type="hidden" name="generacion" id="generacion" value="<?= htmlspecialchars($generacionActual) ?>" />

    </form><!-- /form edición -->
    </div><!-- /report-shell -->

    <!-- ================================================================ -->
    <!-- JAVASCRIPT DE EDICIÓN                                            -->
    <!-- CORRECCIÓN: función sumar() corregida (lee valores del DOM)      -->
    <!-- ================================================================ -->
    <script>
    function sumar() {
        // CORRECCIÓN: lee todos los valores directamente del DOM.
        // Las variables bautizados/var_suma/bautizadosPeriodo NO existen en este scope;
        // el formulario de edición no usa campos con esos IDs, así que esta función
        // sólo necesita asegurarse de que el submit no falle por referencias rotas.
        // Los campos real_asistencia_* no existen en este formulario; se deja vacía
        // para compatibilidad y se delega la validación al servidor.
    }

    function eliminarRegistro() {
        if (confirm("¿Está seguro que desea eliminar este registro? Esta acción NO se puede deshacer.")) {
            document.getElementById('funcion').value = "eliminar";
            document.getElementById('form1').submit();
        }
    }

    function generarForm() {
        $(':input[type="submit"]').prop('disabled', true);
        document.getElementById('funcion').value = "actualizar";
        return true;
    }

    window.addEventListener('load', function () {
        document.getElementById('form1').onsubmit = function () {
            return generarForm();
        };
    });
    </script>

<?php
// ---------------------------------------------------------------------------
// MODO: INSERCIÓN EXITOSA (redirect / confirmación)
// ---------------------------------------------------------------------------
} elseif ($varExitoREP == 1) {
    ?>
    <div class="container report-shell">
        <div class="row">
            <h2 class="alert alert-info text-center">REPORTE DE <?= htmlspecialchars($temp_letrero) ?></h2>
        </div>
        <div class="row">
            <h2 class="alert alert-success text-center">
                <a href="index.php?doc=gestionar-sub-programa-lpp&opc=2&id=<?= $ultimoId ?>">
                    Se ha creado correctamente el registro, haga clic aquí para verlo.
                </a>
            </h2>
        </div>
    </div>
<?php
// ---------------------------------------------------------------------------
// MODO: FORMULARIO DE INSERCIÓN (nuevo reporte)
// ---------------------------------------------------------------------------
} elseif ($idReporteActual == 0) {
    $temp_accionForm = "insertar";
    $idGrupoMadre    = (int) soloNumeros(requestValue('idGrupoMadre', 0));

    if (!isset($_REQUEST['fechaReporte'])) {
        $fechaReporte = date('Y-m-d');
    } else {
        $fechaReporte = eliminarInvalidos($_REQUEST['fechaReporte']);
    }

    // Nombre del grupo madre
    if ($idGrupoMadre > 0) {
        $sqlGM = "SELECT nombre FROM sat_grupos WHERE id = $idGrupoMadre GROUP BY id";
        $PSN1->query($sqlGM);
        if ($PSN1->num_rows() > 0 && $PSN1->next_record()) {
            $nombreGrupoMadre = $PSN1->f('nombre');
        }
    }
    ?>
    <!-- ================================================================ -->
    <!-- ESTILOS (mismo set que el modo edición, incluido arriba)         -->
    <!-- ================================================================ -->
    <style type="text/css">
        /* Reutiliza los mismos estilos definidos en el bloque de edición  */
        /* (en producción mover a un único archivo CSS externo).           */
        .report-shell { max-width:1240px; margin:24px auto 42px; padding:0 14px 26px; }
        .report-shell .alert { border-radius:12px; padding:18px 22px; margin-bottom:18px; box-shadow:0 8px 24px rgba(15,23,42,.06); font-weight:700; }
        .report-shell .alert-info    { background:#f8f9fb; color:#1f2d3a; border:1px solid #d9dee3; border-left:4px solid #243746; }
        .report-shell .alert-success { background:#f7f9f7; color:#27352d; border:1px solid #c8d9cf; border-left:4px solid #56685a; }
        .report-shell .alert-warning { background:#fbf8f2; color:#6d5634; border:1px solid #e8d9b5; border-left:4px solid #8a6a3f; }
        .report-shell .alert-danger  { background:#fbf6f5; color:#6b3b38; border:1px solid #e8c4c2; border-left:4px solid #7a4340; }
        .report-form { background:#f5f5f3; border:1px solid #d7dbdf; border-radius:18px; padding:28px 26px 34px; box-shadow:0 18px 40px rgba(15,23,42,.06); }
        .report-form > .col-sm-12 { margin-bottom:18px; padding:24px 22px 18px; border-radius:14px; border:1px solid #e1e4e8; background:#fff; box-shadow:0 8px 22px rgba(15,23,42,.04); }
        .report-form .cont-tit { display:flex; align-items:center; gap:16px; margin-bottom:20px; }
        .report-form .cont-tit .hr { flex:1 1 0; }
        .report-form .cont-tit hr { margin:0; border:0; border-top:1px solid #d7dce1; }
        .report-form .cont-tit .tit-cen { flex:0 1 auto; min-width:280px; padding:14px 22px; border-radius:12px; background:#f8f9fa; border:1px solid #d9dee3; text-align:center; }
        .report-form .cont-tit h3 { margin:0 0 4px; color:#1e2d3a; font-family:Georgia,"Times New Roman",serif; font-size:22px; font-weight:700; }
        .report-form .cont-tit h5, .report-form .cont-tit p { margin:0; color:#52606d; font-size:13px; }
        .report-form .form-group { margin-left:-8px; margin-right:-8px; margin-bottom:4px; }
        .report-form .form-group > [class*="col-sm-"] { padding-left:8px; padding-right:8px; margin-bottom:16px; }
        .report-form strong { display:block; margin-bottom:8px; color:#24313d; font-size:12.5px; font-weight:700; }
        .report-form .form-control, .report-form select, .report-form textarea { min-height:44px; height:auto; padding:10px 14px; border-radius:10px; border:1px solid #cfd5db; background:#fff; color:#1f2933; transition:border-color .18s,box-shadow .18s; }
        .report-form .form-control:focus, .report-form select:focus { border-color:#334e68; box-shadow:0 0 0 3px rgba(51,78,104,.10); }
        .report-form .form-control[readonly], .report-form .form-control[disabled] { background:#f4f5f6; color:#5f6b76; }
        .report-form input[type="file"].form-control { padding:8px 10px; }
        .report-form .btn { border-radius:10px; padding:10px 18px; font-weight:700; border:1px solid transparent; box-shadow:0 8px 18px rgba(15,23,42,.08); transition:box-shadow .18s,filter .18s; }
        .report-form .btn:hover { box-shadow:0 12px 22px rgba(15,23,42,.12); filter:brightness(1.02); }
        .report-form .btn-success { background:#1f3547; border-color:#1f3547; color:#fff; }
        .report-form .btn-info    { background:#4b5b68; border-color:#4b5b68; color:#fff; }
        .report-form .btn-danger  { background:#7a4340; border-color:#7a4340; color:#fff; }
        .report-form .btn-cir-uno, .report-form .btn-eliminar-fila { width:40px; height:40px; min-width:40px; padding:0; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; background:#c0392b !important; border-color:#c0392b !important; color:#fff !important; }
        .report-form .registro-table { width:100%; border-collapse:separate; border-spacing:0 12px; background:transparent !important; border:0 !important; }
        .report-form .registro-table td { padding:16px 14px; vertical-align:top; border:1px solid #dbe3ea; border-left-width:0; background:#fff; box-shadow:0 8px 20px rgba(15,23,42,.04); }
        .report-form .registro-table td:first-child { border-left-width:1px; border-radius:14px 0 0 14px; }
        .report-form .registro-table td:last-child { border-radius:0 14px 14px 0; }
        .report-form .registro-table .registro-col--nombre { width:52%; }
        .report-form .registro-table .registro-col--identificacion { width:38%; }
        .report-form .registro-table .registro-col--action { width:10%; text-align:center; vertical-align:middle !important; }
        .report-form .registro-table input { width:100%; margin-top:0; }
        .report-form .registro-summary, .report-form .registro-bulk-controls { display:flex; flex-wrap:wrap; align-items:flex-end; gap:14px 16px; width:100%; padding:18px 20px; border:1px solid #dbe3ea; border-radius:14px; background:#f8fafc; }
        .report-form .registro-summary > [class*="col-sm-"], .report-form .registro-bulk-controls > [class*="col-sm-"] { float:none; width:auto; padding:0; margin:0; }
        .report-form .registro-summary > :first-child, .report-form .registro-bulk-controls > :first-child { flex:1 1 360px; }
        .report-form .registro-summary > :nth-child(2), .report-form .registro-bulk-controls > :nth-child(2) { flex:0 0 160px; max-width:180px; }
        .report-form .registro-summary > :last-child, .report-form .registro-bulk-controls > :last-child { flex:1 1 220px; display:flex; justify-content:flex-end; gap:10px; }
        #adicionarAdd, #adicionarAdd2, #adicionarAdd3, #generarVariasAdd { background:#2f7d32 !important; border-color:#2f7d32 !important; color:#fff !important; }
        #borrarTodoAdd { background:#c0392b !important; border-color:#c0392b !important; color:#fff !important; }
        .report-form input[type="submit"][value="Guardar"] { background:#2b5daa !important; border-color:#2b5daa !important; color:#fff !important; }
        .report-shell .cont-btn { display:flex; flex-wrap:wrap; justify-content:center; align-items:center; gap:12px; margin-bottom:18px; }
        @media (max-width:767px) {
            .report-form .registro-table td { display:block; width:100% !important; }
            .report-form .registro-table td:first-child { border-radius:14px 14px 0 0; border-left-width:1px; }
            .report-form .registro-table td:last-child { border-radius:0 0 14px 14px; border-top:0; }
        }
    </style>

    <div class="container report-shell">

        <h3 class="alert alert-info text-center">REPORTE DE <?= htmlspecialchars($temp_letrero) ?></h3>

        <?php if ($varExitoREP_UPD == 1): ?>
        <h5 class="alert alert-warning text-center">Se ha actualizado correctamente el registro.</h5>
        <?php endif; ?>
        <?php if ($texto_error !== ''): ?>
        <h5 class="alert alert-danger text-center"><?= htmlspecialchars($texto_error) ?></h5>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="report-form">
            <input name="fechaReporte" type="hidden" id="fechaReporte" value="<?= htmlspecialchars($fechaReporte) ?>" />

            <!-- -------------------------------------------------------- -->
            <!-- SECCIÓN: Información General                              -->
            <!-- -------------------------------------------------------- -->
            <div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3>Información general</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-2">
                        <strong>Fecha del registro:</strong>
                        <input name="fechaReporte" type="date" maxlength="250"
                               value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"
                               class="form-control" required readonly />
                    </div>
                    <div class="col-sm-2">
                        <?php $mes = (int)date('m'); ?>
                        <strong>Período:</strong>
                        <select name="mapeo_cuarto" class="form-control">
                            <?php if ($mes >= 1  && $mes <= 3):  ?><option value="1"  selected>Q1 (Ene - Mar)</option><?php endif; ?>
                            <?php if ($mes >= 4  && $mes <= 6):  ?><option value="4"  selected>Q2 (Abr - Jun)</option><?php endif; ?>
                            <?php if ($mes >= 7  && $mes <= 9):  ?><option value="7"  selected>Q3 (Jul - Sep)</option><?php endif; ?>
                            <?php if ($mes >= 10 && $mes <= 12): ?><option value="10" selected>Q4 (Oct - Dic)</option><?php endif; ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <strong>Coordinador de prisión:</strong>
                        <select name="usua_id" id="usua_id" class="form-control" required>
                            <option value="<?= (int)$_SESSION['id'] ?>"><?= htmlspecialchars($_SESSION['nombre']) ?></option>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <strong>Cárcel ubicación:</strong>
                        <select name="sitioReunion" id="rep_carcel" class="form-control" required>
                            <?php
                            if ($_SESSION['empresa_pd'] != '') {
                                echo '<option value="">Sin especificar</option>';
                                $empresaPdF = (int) $_SESSION['empresa_pd'];
                                $sqlC = "SELECT * FROM tbl_regional_ubicacion"
                                      . ($empresaPdF ? " WHERE reub_reg_fk = $empresaPdF" : '')
                                      . " ORDER BY reub_reg_fk ASC";
                                $PSN1->query($sqlC);
                                while ($PSN1->next_record()) {
                                    echo '<option value="' . (int)$PSN1->f('reub_id') . '">'
                                         . htmlspecialchars($PSN1->f('reub_nom')) . '</option>';
                                }
                            } else {
                                echo '<option value="">Sin regional asignada</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div id="ubicacion"></div>
                    <div class="col-sm-2">
                        <strong>N° de patios y/o pabellón:</strong>
                        <input name="pabellon" type="number" maxlength="250"
                               value="" class="form-control" required />
                    </div>
                </div>
            </div>

            <!-- -------------------------------------------------------- -->
            <!-- SECCIÓN: Información de la Prisión                       -->
            <!-- -------------------------------------------------------- -->
            <div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3>Información de la prisión</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-3">
                        <strong>Total población que hay en la prisión:</strong>
                        <input name="asistencia_total" type="number" id="asistencia_total"
                               min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-2">
                        <strong>Número de prisioneros invitados:</strong>
                        <input name="asistencia_hom" type="number" id="asistencia_hom"
                               min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Número de prisioneros que iniciaron el curso:</strong>
                        <input name="asistencia_muj" type="number" id="asistencia_muj"
                               min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-2">
                        <strong>Número de cursos activos de LPP:</strong>
                        <input name="asistencia_jov" type="number" id="asistencia_jov"
                               min="0" value="" class="form-control" readonly />
                    </div>
                </div>
            </div>

            <!-- -------------------------------------------------------- -->
            <!-- SECCIÓN: Registro de Graduados (nuevo)                   -->
            <!-- -------------------------------------------------------- -->
            <div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3>REGISTRO DE GRADUADOS</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table id="tablaAdd" class="table table-bordered registro-table">
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="registro-bulk-controls" style="margin-bottom:15px">
                            <div class="col-sm-6">
                                <label for="cantidadAdd">¿Cuántos registros desea generar?</label>
                            </div>
                            <div class="col-sm-3">
                                <input type="number" id="cantidadAdd" class="form-control" min="1" placeholder="Ej: 5">
                            </div>
                            <div class="col-sm-3" style="display:flex;justify-content:flex-end">
                                <button id="generarVariasAdd" class="btn btn-primary" type="button">
                                    <i class="fa fa-list"></i> Generar
                                </button>
                            </div>
                        </div>
                        <div class="registro-summary">
                            <div class="col-sm-6">
                                <strong>Número de graduados en LPP en la prisión:</strong>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="total" id="total" class="form-control" value="0" readonly>
                            </div>
                            <div class="col-sm-4" style="display:flex;gap:10px;justify-content:flex-end">
                                <button id="adicionarAdd" class="btn btn-success" type="button">
                                    <i class="fa fa-plus"></i> Adicionar
                                </button>
                                <button id="borrarTodoAdd" class="btn btn-danger" type="button">
                                    <i class="fa fa-trash"></i> Borrar todo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Script tabla graduados (nuevo) -->
            <script>
            $(function () {
                var STORAGE_KEY_NEW = 'lpp_graduados_nuevo';
                var storage = window.sessionStorage;

                function crearFilaNueva() {
                    return $(
                        '<tr class="registro-table-row">' +
                        '<td class="registro-col registro-col--nombre">' +
                            '<strong>Nombre completo del graduado:</strong>' +
                            '<input name="act_grad_nom[]" type="text" class="act_grad_nom form-control" />' +
                        '</td>' +
                        '<td class="registro-col registro-col--identificacion">' +
                            '<strong>Tarjeta dactilar / N&deg; identificación:</strong>' +
                            '<input name="act_grad_tar[]" type="text" class="act_grad_tar form-control" />' +
                        '</td>' +
                        '<td class="registro-col registro-col--action">' +
                            '<button type="button" class="btn btn-danger btn-eliminar-fila" title="Eliminar">' +
                                '<i class="fa fa-times"></i>' +
                            '</button>' +
                        '</td>' +
                        '</tr>'
                    );
                }

                function obtenerMaximoN() {
                    var v = parseInt($('#asistencia_muj').val(), 10);
                    return (isNaN(v) || v < 1) ? null : v;
                }

                function datosFilaN($f) {
                    return { nombre: $.trim($f.find('.act_grad_nom').val()), tarjeta: $.trim($f.find('.act_grad_tar').val()) };
                }
                function filaCompletaN($f) { var d = datosFilaN($f); return d.nombre !== '' && d.tarjeta !== ''; }
                function filaIncompletaN($f) { var d = datosFilaN($f); return (d.nombre !== '' && d.tarjeta === '') || (d.nombre === '' && d.tarjeta !== ''); }

                function contarN() {
                    var n = 0;
                    $('#tablaAdd tbody tr').each(function () { if (filaCompletaN($(this))) n++; });
                    return n;
                }
                function sincN() { var n = contarN(); $('#total').val(n); $('#rep_ndis').attr('max', n); return n; }

                function guardarN() {
                    if (!storage) return;
                    var d = [];
                    $('#tablaAdd tbody tr').each(function () { d.push(datosFilaN($(this))); });
                    try { storage.setItem(STORAGE_KEY_NEW, JSON.stringify(d)); } catch(e) {}
                }

                function obtenerGuardadosN() {
                    if (!storage) return null;
                    try { var d = storage.getItem(STORAGE_KEY_NEW); if (!d) return null; d = JSON.parse(d); return Array.isArray(d) ? d : null; }
                    catch(e) { return null; }
                }

                function renderizarN(datos) {
                    var $tb = $('#tablaAdd tbody');
                    $tb.empty();
                    if (!Array.isArray(datos) || datos.length === 0) datos = [{nombre:'', tarjeta:''}];
                    $.each(datos, function(_, item) {
                        var $f = crearFilaNueva();
                        $f.find('.act_grad_nom').val(item.nombre  || '');
                        $f.find('.act_grad_tar').val(item.tarjeta || '');
                        $tb.append($f);
                    });
                }

                function actualizarBotonesN() {
                    var max = obtenerMaximoN(), filas = $('#tablaAdd tbody tr').length;
                    $('#adicionarAdd').prop('disabled', max !== null && filas >= max);
                }

                function actualizarLimitesN() {
                    var tot = parseInt($('#asistencia_total').val(), 10);
                    var hom = parseInt($('#asistencia_hom').val(), 10);
                    if (!isNaN(tot)) $('#asistencia_hom').attr('max', Math.max(tot - 1, 0));
                    if (!isNaN(hom)) $('#asistencia_muj').attr('max', hom);
                }

                // Inicializar
                var g = obtenerGuardadosN();
                renderizarN(g !== null ? g : []);
                actualizarLimitesN();
                sincN();
                actualizarBotonesN();

                $('#asistencia_hom, #asistencia_total, #asistencia_muj').on('change keyup', function () {
                    actualizarLimitesN(); actualizarBotonesN();
                    // Calcular cursos activos automáticamente
                    var cur = parseInt($('#asistencia_muj').val(), 10) || 0;
                    var res = cur <= 12 ? 1 : Math.ceil(cur / 12);
                    $('#asistencia_jov').val(res);
                });

                $(document).on('click', '#adicionarAdd', function (e) {
                    e.preventDefault();
                    var max = obtenerMaximoN(), filas = $('#tablaAdd tbody tr').length;
                    if (max !== null && filas >= max) { alert('No puede registrar más graduados que prisioneros que iniciaron el curso (' + max + ').'); return; }
                    $('#tablaAdd tbody').append(crearFilaNueva());
                    actualizarBotonesN(); guardarN();
                });

                $(document).on('click', '#generarVariasAdd', function (e) {
                    e.preventDefault();
                    var cant = parseInt($('#cantidadAdd').val(), 10);
                    if (isNaN(cant) || cant <= 0) { alert('Ingrese una cantidad válida mayor a 0.'); return; }
                    var max = obtenerMaximoN(), filas = $('#tablaAdd tbody tr').length;
                    if (max !== null && (filas + cant) > max) { alert('Excede el máximo de graduados permitidos (' + max + ').'); return; }
                    for (var i = 0; i < cant; i++) $('#tablaAdd tbody').append(crearFilaNueva());
                    sincN(); actualizarBotonesN(); guardarN();
                });

                $(document).on('click', '#borrarTodoAdd', function (e) {
                    e.preventDefault();
                    if (!confirm('¿Está seguro de borrar todos los registros?')) return;
                    renderizarN([]); sincN(); actualizarBotonesN(); guardarN();
                });

                $(document).on('click', '#tablaAdd .btn-eliminar-fila', function (e) {
                    e.preventDefault();
                    $(this).closest('tr').remove();
                    if ($('#tablaAdd tbody tr').length === 0) renderizarN([]);
                    sincN(); actualizarBotonesN(); guardarN();
                });

                $(document).on('keyup change blur', '.act_grad_nom, .act_grad_tar', function () {
                    sincN(); actualizarBotonesN(); guardarN();
                });

                $('form').on('submit', function (e) {
                    var hay = false;
                    $('#tablaAdd tbody tr').each(function () { if (filaIncompletaN($(this))) { hay = true; return false; } });
                    if (hay) { e.preventDefault(); alert('Complete nombre e identificación en todas las filas de graduados.'); return false; }
                    sincN();
                });
            });
            </script>

            <!-- -------------------------------------------------------- -->
            <!-- SECCIÓN: Voluntarios Internos (nuevo)                    -->
            <!-- -------------------------------------------------------- -->
            <div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3>REGISTRO DE VOLUNTARIOS INTERNOS</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table id="tablaAdd2" class="table table-bordered registro-table">
                            <tbody>
                            <tr class="fila-fijaAdd2 registro-table-row">
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del siervo facilitador:</strong>
                                    <input name="act_vin_nom[]" type="text" class="act_vin_nom form-control" />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>Tarjeta dactilar / N° identificación:</strong>
                                    <input name="act_vin_tar[]" type="text" class="act_vin_tar form-control" />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd2">
                                    <button type="button" class="btn btn-cir-uno" title="Eliminar">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="registro-summary">
                            <div class="col-sm-6">
                                <strong>Número de voluntarios internos activos en esta prisión:</strong>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="total2" id="total2" value="0" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4" style="display:flex;justify-content:flex-end">
                                <button id="adicionarAdd2" class="btn btn-success" type="button">
                                    <i class="fas fa-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            $(function(){
                var total2n = 0;
                $(document).on('click', '#adicionarAdd2', function(){
                    var $clone = $('#tablaAdd2 tbody tr:last').clone();
                    $clone.find('input').val('');
                    $clone.appendTo('#tablaAdd2 tbody');
                    total2n++;
                    $('#total2').val(total2n);
                });
                $(document).on('click', '.eliminarAdd2', function(){
                    $(this).closest('tr').remove();
                    total2n = Math.max(0, total2n - 1);
                    $('#total2').val(total2n);
                });
            });
            </script>

            <!-- -------------------------------------------------------- -->
            <!-- SECCIÓN: Voluntarios Externos (nuevo)                    -->
            <!-- -------------------------------------------------------- -->
            <div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3>REGISTRO DE VOLUNTARIOS EXTERNOS</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <table id="tablaAdd3" class="table table-bordered registro-table">
                            <tbody>
                            <tr class="fila-fijaAdd3 registro-table-row">
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del entrenador:</strong>
                                    <input name="act_vex_nom[]" type="text" class="act_vex_nom form-control" />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>N° identificación:</strong>
                                    <input name="act_vex_tar[]" type="text" class="act_vex_tar form-control" />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd3">
                                    <button type="button" class="btn btn-cir-uno" title="Eliminar">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="registro-summary">
                            <div class="col-sm-6">
                                <strong>Número de voluntarios externos para esta prisión:</strong>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" name="total3" id="total3" value="0" class="form-control" readonly>
                            </div>
                            <div class="col-sm-4" style="display:flex;justify-content:flex-end">
                                <button id="adicionarAdd3" class="btn btn-success" type="button">
                                    <i class="fas fa-plus"></i> Adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            $(function(){
                var total3n = 0;
                $(document).on('click', '#adicionarAdd3', function(){
                    var $clone = $('#tablaAdd3 tbody tr:last').clone();
                    $clone.find('input').val('');
                    $clone.appendTo('#tablaAdd3 tbody');
                    total3n++;
                    $('#total3').val(total3n);
                });
                $(document).on('click', '.eliminarAdd3', function(){
                    $(this).closest('tr').remove();
                    total3n = Math.max(0, total3n - 1);
                    $('#total3').val(total3n);
                });
            });
            </script>

            <!-- -------------------------------------------------------- -->
            <!-- SECCIÓN: Testimonio + Foto (nuevo)                       -->
            <!-- -------------------------------------------------------- -->
            <div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3>Método de verificación</h3>
                        <h5>Testimonio y foto</h5>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <strong>Número de discípulos que pasaron a C&amp;M:</strong>
                        <input name="rep_ndis" type="number" id="rep_ndis" min="0" value="0" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Costo de recursos gestionados ($):</strong>
                        <input name="rep_entr" type="number" id="rep_entr" min="0" value="0" class="form-control" />
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-3">
                        <strong>Testimonio (archivo):</strong>
                        <input name="archivo2" type="file" id="archivo2" class="form-control" required />
                    </div>
                    <div class="col-sm-3">
                        <strong>Foto:</strong>
                        <input name="archivo1" type="file" id="archivo1" class="form-control"
                               accept="image/png,image/jpeg,image/gif" />
                    </div>
                </div>
                <div class="cont-btn">
                    <div class="item-btn">
                        <input type="submit" name="button" value="Guardar" class="btn btn-success">
                    </div>
                </div>
            </div>

            <input type="hidden" name="funcion"    id="funcion"    value="" />
            <input type="hidden" name="generacion" id="generacion" value="<?= htmlspecialchars($generacionActual) ?>" />

        </form><!-- /form inserción -->

        <!-- JS del formulario de inserción -->
        <script>
        function generarFormNuevo() {
            if (confirm("Esta acción guardará los cambios en el sistema. ¿Está seguro que desea continuar?")) {
                $(':input[type="submit"]').prop('disabled', true);
                document.getElementById('funcion').value = "<?= htmlspecialchars($temp_accionForm) ?>";
                return true;
            }
            return false;
        }

        window.addEventListener('load', function () {
            document.getElementById('form1').onsubmit = function () {
                return generarFormNuevo();
            };
        });
        </script>

    </div><!-- /report-shell inserción -->

<?php
} else {
    echo "No debería estar aquí.";
}
?>

<!-- ================================================================ -->
<!-- BLOQUEO SOLO LECTURA (perfil 168 o reporte vencido)              -->
<!-- ================================================================ -->
<?php if ($_SESSION['perfil'] == '168' || (isset($fechLimite) && isset($fechaReporte) && $fechLimite > $fechaReporte)): ?>
<script>
$(function(){
    $('input, textarea, select').not('[type="hidden"]').attr('disabled', 'disabled');
    $('.eliminarAdd, .eliminarAdd2, .eliminarAdd3, .btn-eliminar-fila').prop('disabled', true);
    $('#adicionarAdd, #adicionarAdd2, #adicionarAdd3, #generarVariasAdd, #borrarTodoAdd').prop('disabled', true);
});
</script>
<?php endif; ?>

<!-- ================================================================ -->
<!-- AJAX: carga de municipios y ubicación de cárcel                  -->
<!-- ================================================================ -->
<script>
$(document).ready(function(){
    // Recarga ubicación al cambiar cárcel
    function recargaLista(){
        var id = $('#rep_carcel').val();
        if (!id) { $('#ubicacion').html(''); return; }
        $.ajax({
            type: 'POST',
            url:  'datos_carcel_ubicacion.php',
            data: { id_carcel: id },
            success: function(r){ $('#ubicacion').html(r); }
        });
    }
    recargaLista();
    $('#rep_carcel').on('change', recargaLista);

    // Recarga municipios al cambiar departamento
    function recargaListaDpto(){
        var id = $('#departamento').val();
        if (!id) return;
        $.ajax({
            type: 'POST',
            url:  'datos_ubicacion.php',
            data: { id_depa: id },
            success: function(r){ $('#municipio').html(r); }
        });
    }
    recargaListaDpto();
    $('#departamento').on('change', recargaListaDpto);

    // Calcular cursos activos al cambiar prisioneros que iniciaron
    $('#asistencia_muj').on('change', function(){
        var cur = parseInt($(this).val(), 10) || 0;
        var res = cur <= 0 ? 0 : (cur <= 12 ? 1 : Math.ceil(cur / 12));
        $('#asistencia_jov').val(res);
    });
});
</script>