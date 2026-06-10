<?php
/*
 * crear-reporte-lpp.php
 * Formulario de creación de reporte LPP
 */

$PSN = new DBbase_Sql;

$webArchivo   = 'lpp';
$temp_letrero = 'LA PEREGRINACIÓN DEL PRISIONERO (LPP)';

$empresa_pd = isset($_SESSION['empresa_pd']) ? $_SESSION['empresa_pd'] : '';

/* ============================================================
   AJAX — info de la cárcel se maneja via datos_carcel_ubicacion.php
   (archivo ya existente en el sistema)
   ============================================================ */

/* ============================================================
   PROCESAMIENTO
   ============================================================ */

/* Comprime imágenes al moverlas al servidor */
function compressImage($source, $destination, $quality) {
    $info = getimagesize($source);
    if ($info['mime'] === 'image/jpeg') {
        $image = imagecreatefromjpeg($source);
    } elseif ($info['mime'] === 'image/gif') {
        $image = imagecreatefromgif($source);
    } elseif ($info['mime'] === 'image/png') {
        $image = imagecreatefrompng($source);
    } else {
        return false;
    }
    return imagejpeg($image, $destination, $quality);
}

$varExito    = 0;
$error_datos = 0;
$texto_error = '';

if (isset($_POST['funcion']) && $_POST['funcion'] === 'insertar') {

    $fecha_reporte         = eliminarInvalidos($_POST['fecha_reporte']);
    $periodo_trimestre     = soloNumeros($_POST['periodo_trimestre']);
    $carcel_id             = soloNumeros($_POST['carcel_id']);
    $pabellon              = soloNumeros($_POST['pabellon']);
    $poblacion_total       = soloNumeros($_POST['poblacion_total']);
    $prisioneros_invitados = soloNumeros($_POST['prisioneros_invitados']);
    $prisioneros_iniciaron = soloNumeros($_POST['prisioneros_iniciaron']);
    $cursos_activos        = soloNumeros($_POST['cursos_activos']);
    $total_graduados            = soloNumeros($_POST['total_graduados']);
    $total_voluntarios_internos = soloNumeros($_POST['total_voluntarios_internos']);
    $total_voluntarios_externos = soloNumeros($_POST['total_voluntarios_externos']);
    $discipulos_pasaron_cm      = soloNumeros($_POST['discipulos_pasaron_cm']);
    $costo_recursos             = soloNumeros($_POST['costo_recursos']);

    /* Archivos */
    $ext_foto       = extension_archivo($_FILES['archivo_foto']['name']       ?? '');
    $ext_testimonio = extension_archivo($_FILES['archivo_testimonio']['name'] ?? '');

    /* Validaciones de respaldo (por si se evita el JS) */
    if ($fecha_reporte === '' || $carcel_id == 0) {
        $error_datos = 1;
        $texto_error = 'La fecha y la cárcel son requeridas.';
    }

    /* Minimo 1 voluntario interno con nombre e identificacion */
    if ($error_datos == 0) {
        $internos_validos = 0;
        if (!empty($_POST['interno_nombre'])) {
            foreach ($_POST['interno_nombre'] as $idx => $nom_int) {
                $n = eliminarInvalidos($nom_int);
                $d = eliminarInvalidos($_POST['interno_identificacion'][$idx] ?? '');
                if ($n !== '' && $d !== '') $internos_validos++;
            }
        }
        if ($internos_validos === 0) {
            $error_datos = 1;
            $texto_error = 'Debes registrar al menos un voluntario interno con nombre e identificación.';
        }
    }

    /* Minimo 1 voluntario externo con nombre e identificacion */
    if ($error_datos == 0) {
        $externos_validos = 0;
        if (!empty($_POST['externo_nombre'])) {
            foreach ($_POST['externo_nombre'] as $idx => $nom_ext) {
                $n = eliminarInvalidos($nom_ext);
                $d = eliminarInvalidos($_POST['externo_identificacion'][$idx] ?? '');
                if ($n !== '' && $d !== '') $externos_validos++;
            }
        }
        if ($externos_validos === 0) {
            $error_datos = 1;
            $texto_error = 'Debes registrar al menos un voluntario externo con nombre e identificación.';
        }
    }

    if ($error_datos == 0) {
        $sql = "INSERT INTO reporte_lpp (
                    usuario_id, carcel_id, programa_id, fecha_reporte,
                    periodo_trimestre, pabellon, poblacion_total,
                    prisioneros_invitados, prisioneros_iniciaron, cursos_activos,
                    total_graduados, total_voluntarios_internos, total_voluntarios_externos,
                    discipulos_pasaron_cm, costo_recursos,
                    archivo_foto, archivo_testimonio
                ) VALUES (
                    ".(int)$_SESSION['id'].",
                    ".(int)$carcel_id.",
                    307,
                    '".$fecha_reporte."',
                    ".(int)$periodo_trimestre.",
                    ".(int)$pabellon.",
                    ".(int)$poblacion_total.",
                    ".(int)$prisioneros_invitados.",
                    ".(int)$prisioneros_iniciaron.",
                    ".(int)$cursos_activos.",
                    ".(int)$total_graduados.",
                    ".(int)$total_voluntarios_internos.",
                    ".(int)$total_voluntarios_externos.",
                    ".(int)$discipulos_pasaron_cm.",
                    ".(int)$costo_recursos.",
                    '".$ext_foto."',
                    '".$ext_testimonio."'
                )";
        $PSN->query($sql);
        $ultimoId = $PSN->ultimoId();

        /* ---- Guardar graduados ---- */
        if ($ultimoId > 0 && !empty($_POST['grad_nombre'])) {
            $nombres         = $_POST['grad_nombre'];
            $identificaciones = $_POST['grad_identificacion'];
            $fecha_hoy       = date('Y-m-d');

            foreach ($nombres as $i => $nombre) {
                $nom  = eliminarInvalidos($nombre);
                $iden = eliminarInvalidos($identificaciones[$i] ?? '');
                if ($nom === '') continue;

                $PSN->query("INSERT INTO reporte_graduado_lpp
                                (id_reporte_lpp, nombre, identificacion, fecha_registro)
                             VALUES
                                (" . (int)$ultimoId . ",
                                 '" . $nom  . "',
                                 '" . $iden . "',
                                 '" . $fecha_hoy . "')");
            }
        }

        /* ---- Guardar voluntarios internos ---- */
        if ($ultimoId > 0 && !empty($_POST['interno_nombre'])) {
            $nombres_int = $_POST['interno_nombre'];
            $iden_int    = $_POST['interno_identificacion'];
            $fecha_hoy   = date('Y-m-d');
            foreach ($nombres_int as $i => $nombre) {
                $nom  = eliminarInvalidos($nombre);
                $iden = eliminarInvalidos($iden_int[$i] ?? '');
                if ($nom === '') continue;
                $PSN->query("INSERT INTO reporte_interno_lpp
                                (id_reporte_lpp, nombre, identificacion, fecha_registro)
                             VALUES
                                (" . (int)$ultimoId . ",
                                 '" . $nom  . "',
                                 '" . $iden . "',
                                 '" . $fecha_hoy . "')");
            }
        }

        /* ---- Guardar voluntarios externos ---- */
        if ($ultimoId > 0 && !empty($_POST['externo_nombre'])) {
            $nombres_ext = $_POST['externo_nombre'];
            $iden_ext    = $_POST['externo_identificacion'];
            $fecha_hoy   = date('Y-m-d');
            foreach ($nombres_ext as $i => $nombre) {
                $nom  = eliminarInvalidos($nombre);
                $iden = eliminarInvalidos($iden_ext[$i] ?? '');
                if ($nom === '') continue;
                $PSN->query("INSERT INTO reporte_externo_lpp
                                (id_reporte_lpp, nombre, identificacion, fecha_registro)
                             VALUES
                                (" . (int)$ultimoId . ",
                                 '" . $nom  . "',
                                 '" . $iden . "',
                                 '" . $fecha_hoy . "')");
            }
        }

        /* ---- Mover archivos al servidor ---- */
        if ($ultimoId > 0) {

            /* Foto de grupo (imagen) */
            if ($ext_foto !== '') {
                $rutaOrigen  = $_FILES['archivo_foto']['tmp_name'];
                $rutaDestino = 'archivos/evi_' . $ultimoId . '_foto.' . $ext_foto;
                $imgExts = ['png', 'jpg', 'jpeg', 'gif'];
                if (in_array($ext_foto, $imgExts)) {
                    compressImage($rutaOrigen, $rutaDestino, 80);
                } else {
                    move_uploaded_file($rutaOrigen, $rutaDestino);
                }
            }

            /* Testimonio (word / pdf / doc) */
            if ($ext_testimonio !== '') {
                $rutaOrigen  = $_FILES['archivo_testimonio']['tmp_name'];
                $rutaDestino = 'archivos/evi_' . $ultimoId . '_testimonio.' . $ext_testimonio;
                move_uploaded_file($rutaOrigen, $rutaDestino);
            }
        }

        $varExito = 1;
    }
}
?>
<style>
    .report-shell {
        max-width: 1260px;
        margin: 28px auto 40px;
        padding: 0 18px 24px;
    }
    .report-shell .alert {
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 18px;
        font-weight: 700;
        border-left-width: 4px;
        border-left-style: solid;
    }
    .report-shell .alert-info    { border-left-color: #243746; background:#f0f4f8; color:#1e2d3a; }
    .report-shell .alert-success { border-left-color: #56685a; background:#f0f5f1; color:#1e2d3a; }
    .report-shell .alert-danger  { border-left-color: #7a4340; background:#fdf3f3; color:#5a1f1e; }

    .report-form {
        background: #f5f5f3;
        border: 1px solid #d7dbdf;
        border-radius: 18px;
        padding: 28px 26px 34px;
        box-shadow: 0 18px 40px rgba(15,23,42,.06);
    }
    .report-form > .seccion {
        background: #ffffff;
        border: 1px solid #e1e4e8;
        border-radius: 14px;
        padding: 24px 22px 20px;
        margin-bottom: 18px;
        box-shadow: 0 8px 22px rgba(15,23,42,.04);
    }

    /* Título de sección */
    .cont-tit {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 22px;
    }
    .cont-tit .hr { flex: 1; }
    .cont-tit hr  { margin: 0; border: 0; border-top: 1px solid #d7dce1; }
    .cont-tit .tit-cen {
        padding: 12px 22px 10px;
        border-radius: 12px;
        background: #f8f9fa;
        border: 1px solid #d9dee3;
        text-align: center;
        white-space: nowrap;
    }
    .cont-tit h3 {
        margin: 0;
        color: #1e2d3a;
        font-family: Georgia, serif;
        font-size: 20px;
        font-weight: 700;
    }
    .cont-tit h5 {
        margin: 4px 0 0;
        color: #52606d;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    /* Labels y controles */
    .report-form strong {
        display: block;
        margin-bottom: 6px;
        color: #24313d;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        line-height: 1.4;
    }
    .report-form .form-control,
    .report-form select.form-control {
        padding: 10px 13px;
        border-radius: 9px;
        border: 1px solid #cfd5db;
        background: #ffffff;
        color: #1f2933;
        height: 44px;
        width: 100%;
        box-shadow: none;
        transition: border-color .16s, box-shadow .16s;
        font-size: 13px;
    }
    .report-form .form-control:focus,
    .report-form select.form-control:focus {
        border-color: #334e68;
        box-shadow: 0 0 0 3px rgba(51,78,104,.10);
        outline: none;
    }
    .report-form .form-control[readonly] {
        background: #f0f4f8;
        color: #4a5568;
        cursor: default;
        border-color: #d0dbe5;
    }

    /* Bloque ubicación inyectado por datos_carcel_ubicacion.php */
    #ubicacion {
        margin-top: 12px;
    }
    #ubicacion table {
        width: 100%;
        border-collapse: collapse;
    }
    #ubicacion td,
    #ubicacion th {
        padding: 8px 12px;
        font-size: 13px;
        color: #2d3f50;
        border-bottom: 1px solid #e8ecf0;
    }
    #ubicacion th {
        font-weight: 700;
        color: #1e2d3a;
        background: #f0f4f8;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }
    #ubicacion tr:last-child td { border-bottom: none; }

    /* Separador entre filas dentro de una sección */
    .report-form .fila-form {
        margin-bottom: 16px;
    }
    .report-form .fila-form:last-child {
        margin-bottom: 0;
    }

    /* Botones */
    .cont-btn {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-top: 10px;
    }
    .report-form .btn {
        border-radius: 10px;
        padding: 11px 26px;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: .03em;
        border: 1px solid transparent;
        box-shadow: 0 6px 16px rgba(15,23,42,.10);
        cursor: pointer;
        transition: box-shadow .16s, filter .16s;
    }
    .report-form .btn:hover { box-shadow: 0 10px 22px rgba(15,23,42,.14); filter: brightness(1.04); }
    .report-form .btn-success { background: #1f3547; color: #fff; }
    .report-form .btn-info    { background: #4b5b68; color: #fff; }

    /* ── Tabla de registros (graduados / internos / externos) ── */
    .table-graduados {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        font-size: 13px;
    }
    .table-graduados thead th {
        background: #f0f4f8;
        color: #1e2d3a;
        font-weight: 700;
        font-size: 12px;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 11px 14px;
        border-bottom: 2px solid #d0dbe5;
        text-align: left;
    }
    .table-graduados thead th:first-child { width: 44px; text-align: center; }
    .table-graduados thead th:last-child  { width: 44px; }

    .table-graduados tbody tr {
        border-bottom: 1px solid #e8ecf0;
        transition: background .12s;
    }
    .table-graduados tbody tr:hover { background: #f8fafc; }
    .table-graduados tbody td {
        padding: 10px 12px;
        vertical-align: middle;
    }
    .table-graduados tbody td:first-child {
        text-align: center;
        color: #7a8a99;
        font-weight: 700;
        font-size: 12px;
        width: 44px;
    }
    .table-graduados tbody td:last-child { width: 44px; text-align: center; }

    .table-graduados .inp-tabla {
        width: 100%;
        padding: 8px 11px;
        border-radius: 8px;
        border: 1px solid #cfd5db;
        background: #fff;
        font-size: 13px;
        color: #1f2933;
        height: 38px;
        transition: border-color .15s, box-shadow .15s;
    }
    .table-graduados .inp-tabla:focus {
        border-color: #334e68;
        box-shadow: 0 0 0 3px rgba(51,78,104,.10);
        outline: none;
    }

    .btn-eliminar-fila {
        background: none;
        border: none;
        color: #c0392b;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background .12s;
        line-height: 1;
    }
    .btn-eliminar-fila:hover { background: #fdf3f3; }

    .sin-registros td {
        text-align: center;
        color: #8a9bac;
        font-style: italic;
        padding: 20px;
    }

    /* ── Barra de controles ── */
    .barra-controles {
        margin-top: 14px;
        display: flex;
        flex-direction: column;
        gap: 0;
    }
    .barra-fila {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .barra-fila + .barra-fila {
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px dashed #dde4ec;
    }
    .btn-agregar {
        background: #f0f4f8;
        border: 1px dashed #9ab0c4;
        color: #2d4a62;
        border-radius: 9px;
        padding: 9px 20px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background .14s, border-color .14s;
        white-space: nowrap;
    }
    .btn-agregar:hover { background: #e2eaf2; border-color: #6b8fa8; }
    .sep-agregar {
        color: #c0ccd8;
        font-size: 18px;
        line-height: 1;
    }
    .grupo-generar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f7f9fb;
        border: 1px solid #dde4ec;
        border-radius: 9px;
        padding: 5px 12px 5px 10px;
    }
    .lbl-generar {
        font-size: 13px;
        font-weight: 700;
        color: #3a5068;
        white-space: nowrap;
    }
    .inp-generar {
        width: 62px;
        padding: 6px 8px;
        border-radius: 8px;
        border: 1px solid #cfd5db;
        font-size: 13px;
        color: #1f2933;
        height: 34px;
        text-align: center;
    }
    .inp-generar:focus {
        border-color: #334e68;
        box-shadow: 0 0 0 3px rgba(51,78,104,.10);
        outline: none;
    }
    .btn-generar-inline {
        background: #f0f4f8;
        border: 1px dashed #9ab0c4;
        color: #2d4a62;
        border-radius: 8px;
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        height: 34px;
        transition: background .14s, border-color .14s;
        white-space: nowrap;
    }
    .btn-generar-inline:hover { background: #e2eaf2; border-color: #6b8fa8; }
    .btn-eliminar-todo {
        background: #fdf3f3;
        border: 1px dashed #c0392b;
        color: #c0392b;
        border-radius: 9px;
        padding: 9px 16px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: background .14s;
        white-space: nowrap;
        margin-left: auto;
    }
    .btn-eliminar-todo:hover { background: #fae0de; }

    /* ── Tarjeta de total ── */
    .card-total {
        display: inline-flex;
        align-items: stretch;
        margin-top: 16px;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid #d0dbe5;
        box-shadow: 0 4px 10px rgba(15,23,42,.06);
    }
    .card-total-label {
        padding: 11px 18px;
        background: #f0f4f8;
        border-right: 1px solid #d0dbe5;
        font-size: 12.5px;
        font-weight: 700;
        color: #1e2d3a;
        white-space: nowrap;
        display: flex;
        align-items: center;
    }
    .card-total-badge {
        padding: 8px 20px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 60px;
    }
    .card-total-badge input {
        background: transparent;
        border: none;
        color: #1f3547;
        font-size: 20px;
        font-weight: 700;
        text-align: center;
        width: 48px;
        cursor: default;
        outline: none;
        padding: 0;
        line-height: 1;
        -moz-appearance: textfield;
    }
    .card-total-badge input::-webkit-outer-spin-button,
    .card-total-badge input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    @media (max-width: 767px) {
        .cont-tit { flex-direction: column; gap: 8px; }
        .cont-tit .tit-cen { width: 100%; white-space: normal; }
        .report-form .btn { width: 100%; }
        .barra-fila { flex-direction: column; align-items: flex-start; }
        .btn-eliminar-todo { margin-left: 0; width: 100%; justify-content: center; }
        .grupo-generar { width: 100%; }
        .card-total { width: 100%; }
        .card-total-badge { flex: 1; justify-content: center; }
    }

    /* ── Sección Método de verificación ── */
    .verif-indicadores {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .verif-card {
        flex: 1;
        min-width: 220px;
        display: flex;
        align-items: center;
        gap: 16px;
        background: #f7f9fb;
        border: 1px solid #d8e2eb;
        border-radius: 12px;
        padding: 16px 20px;
    }
   
    .verif-card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .verif-label {
        font-size: 12px;
        font-weight: 700;
        color: #24313d;
        letter-spacing: .02em;
        line-height: 1.4;
    }
    .verif-input {
        width: 100%;
        padding: 9px 13px;
        border-radius: 9px;
        border: 1px solid #cfd5db;
        background: #ffffff;
        color: #1f2933;
        font-size: 18px;
        font-weight: 700;
        height: 46px;
        text-align: center;
        transition: border-color .15s, box-shadow .15s;
        -moz-appearance: textfield;
    }
    .verif-input::-webkit-outer-spin-button,
    .verif-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .verif-input:focus {
        border-color: #334e68;
        box-shadow: 0 0 0 3px rgba(51,78,104,.10);
        outline: none;
    }

    .verif-adjuntos {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }
    .verif-upload-card {
        flex: 1;
        min-width: 240px;
        border: 1px solid #d8e2eb;
        border-radius: 12px;
        overflow: hidden;
    }
    .verif-upload-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 16px;
        background: #f0f4f8;
        border-bottom: 1px solid #d8e2eb;
    }

    .verif-upload-title {
        font-size: 13px;
        font-weight: 700;
        color: #1e2d3a;
    }
    .verif-upload-body {
        padding: 14px 16px 12px;
        background: #ffffff;
    }
    .verif-file-input {
        display: block;
        width: 100%;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px dashed #9ab0c4;
        background: #f7f9fb;
        font-size: 13px;
        color: #3a5068;
        cursor: pointer;
        transition: border-color .14s, background .14s;
    }
    .verif-file-input:hover { border-color: #6b8fa8; background: #eef4f9; }
    .verif-upload-hint {
        margin: 7px 0 0;
        font-size: 11px;
        color: #7a8e9c;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    @media (max-width: 767px) {
        .verif-indicadores,
        .verif-adjuntos { flex-direction: column; }
        .verif-card,
        .verif-upload-card { min-width: 0; width: 100%; }
    }

    /* -- Estilos de validacion visual -- */
    .field-error {
        border-color: #c0392b !important;
        box-shadow: 0 0 0 3px rgba(192,57,43,.15) !important;
        background: #fff8f8 !important;
    }
    .inp-tabla.field-error {
        border-color: #c0392b !important;
        box-shadow: 0 0 0 3px rgba(192,57,43,.15) !important;
        background: #fff8f8 !important;
    }
    .verif-file-input.field-error,
    .verif-upload-card.field-error {
        border-color: #c0392b !important;
        background: #fff8f8 !important;
    }
    .field-error-msg {
        display: block;
        margin-top: 5px;
        font-size: 11.5px;
        font-weight: 700;
        color: #c0392b;
        letter-spacing: .02em;
    }
    #panel-errores {
        display: none;
        background: #fdf3f3;
        border: 1px solid #e8b4b0;
        border-left: 4px solid #c0392b;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
    }
    #panel-errores .panel-titulo {
        font-size: 13px;
        font-weight: 700;
        color: #7a1f1e;
        margin-bottom: 8px;
    }
    #panel-errores ul { margin: 0; padding-left: 18px; }
    #panel-errores ul li {
        font-size: 12.5px;
        color: #7a1f1e;
        margin-bottom: 4px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline dotted;
    }
    #panel-errores ul li:last-child { margin-bottom: 0; }
    #panel-errores ul li:hover { color: #c0392b; }

    /* -- Modal de confirmacion -- */
    #modal-confirmar {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9000;
        background: rgba(15,23,42,.45);
        align-items: center;
        justify-content: center;
    }
    #modal-confirmar.activo { display: flex; }
    .modal-caja {
        background: #fff;
        border-radius: 16px;
        padding: 32px 28px 26px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 24px 60px rgba(15,23,42,.22);
        text-align: center;
    }
    .modal-caja p {
        font-size: 15px;
        font-weight: 600;
        color: #1e2d3a;
        margin: 0 0 22px;
        line-height: 1.5;
    }
    .modal-botones {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    .modal-botones button {
        border-radius: 10px;
        padding: 10px 28px;
        font-weight: 700;
        font-size: 13px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: filter .14s;
    }
    .modal-botones button:hover { filter: brightness(1.06); }
    #modal-btn-si  { background: #1f3547; color: #fff; }
    #modal-btn-no  { background: #f0f4f8; color: #1e2d3a; border-color: #cfd5db; }

    /* -- Overlay de carga -- */
    #overlay-carga {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9100;
        background: rgba(15,23,42,.55);
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 18px;
    }
    #overlay-carga.activo { display: flex; }
    .carga-spinner {
        width: 52px;
        height: 52px;
        border: 5px solid rgba(255,255,255,.25);
        border-top-color: #fff;
        border-radius: 50%;
        animation: girar .7s linear infinite;
    }
    @keyframes girar { to { transform: rotate(360deg); } }
    .carga-texto {
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: .04em;
    }
</style>

<!-- Modal de confirmacion -->
<div id="modal-confirmar">
    <div class="modal-caja">
        <p>¿Está seguro de que desea guardar este reporte?</p>
        <div class="modal-botones">
            <button id="modal-btn-si">Sí, guardar</button>
            <button id="modal-btn-no">Cancelar</button>
        </div>
    </div>
</div>

<!-- Overlay de carga -->
<div id="overlay-carga">
    <div class="carga-spinner"></div>
    <span class="carga-texto">Guardando reporte…</span>
</div>

<div class="container report-shell">

    <h3 class="alert alert-info text-center">REPORTE DE <?= $temp_letrero ?></h3>

    <?php if ($texto_error !== ''): ?>
        <div class="alert alert-danger text-center"><?= $texto_error ?></div>
    <?php endif; ?>

    <?php if ($varExito === 1): ?>
        <div class="alert alert-success text-center">
            <a href="index.php?doc=gestionar-reporte-lpp&id=<?= $ultimoId ?>">
                Reporte creado correctamente. Haz clic aquí para verlo.
            </a>
        </div>
    <?php else: ?>

    <div id="panel-errores"><div class="panel-titulo">&#9888; Por favor corrige los siguientes errores antes de continuar:</div><ul id="lista-errores"></ul></div>

    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="report-form">

        <!-- ================================================
             SECCIÓN 1 — INFORMACIÓN GENERAL
             ================================================ -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Información general</h3>
                    <h5>Datos del coordinador y la cárcel</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <!-- Fila 1: Fecha · Período · Coordinador · Patios -->
            <div class="form-group row fila-form">

                <div class="col-sm-2">
                    <strong>Fecha del registro:</strong>
                    <input type="date" name="fecha_reporte" id="fecha_reporte"
                           class="form-control"
                           value="<?= date('Y-m-d') ?>"
                           max="<?= date('Y-m-d') ?>"
                           readonly required />
                </div>

                <div class="col-sm-2">
                    <?php $mes = (int)date('m'); ?>
                    <strong>Período:</strong>
                    <select name="periodo_trimestre" id="periodo_trimestre" class="form-control" required>
                        <option value="1"  <?= ($mes >= 1  && $mes <= 3)  ? 'selected' : '' ?>>Q1 (Ene – Mar)</option>
                        <option value="4"  <?= ($mes >= 4  && $mes <= 6)  ? 'selected' : '' ?>>Q2 (Abr – Jun)</option>
                        <option value="7"  <?= ($mes >= 7  && $mes <= 9)  ? 'selected' : '' ?>>Q3 (Jul – Sep)</option>
                        <option value="10" <?= ($mes >= 10 && $mes <= 12) ? 'selected' : '' ?>>Q4 (Oct – Dic)</option>
                    </select>
                </div>

                <div class="col-sm-5">
                    <strong>Coordinador de prisión:</strong>
                    <select name="usuario_id" id="usuario_id" class="form-control" required>
                        <option value="<?= (int)$_SESSION['id'] ?>"><?= htmlspecialchars($_SESSION['nombre']) ?></option>
                    </select>
                </div>

                <div class="col-sm-3">
                    <strong>N° de patios y/o pabellón:</strong>
                    <input type="number" name="pabellon" id="pabellon"
                           class="form-control" min="1" value="" required />
                </div>

            </div><!-- /fila 1 -->

            <!-- Fila 2: Cárcel (select + bloque de ubicación debajo) -->
            <div class="form-group row fila-form">

                <div class="col-sm-12">
                    <strong>Cárcel ubicación:</strong>
                    <select name="carcel_id" id="carcel_id" class="form-control" required>
                        <option value="">Sin especificar</option>
                        <?php
                        if ($empresa_pd != '') {
                            $sql_c = "SELECT * FROM tbl_regional_ubicacion";
                            if ($empresa_pd != 0) {
                                $sql_c .= " WHERE reub_reg_fk = " . (int)$empresa_pd;
                            }
                            $sql_c .= " ORDER BY reub_nom ASC";
                            $PSN->query($sql_c);
                            if ($PSN->num_rows() > 0) {
                                while ($PSN->next_record()) {
                                    echo '<option value="' . $PSN->f('reub_id') . '">'
                                       . $PSN->f('reub_nom') . '</option>';
                                }
                            }
                        } else {
                            echo '<option value="">Sin regional asignada</option>';
                        }
                        ?>
                    </select>

                    <!-- Se despliega al seleccionar una cárcel —
                         datos_carcel_ubicacion.php inyecta aquí
                         departamento, municipio y dirección -->
                    <div id="ubicacion"></div>
                </div>

            </div><!-- /fila 2 -->

        </div><!-- /seccion 1 -->

        <!-- ================================================
             SECCIÓN 2 — INFORMACIÓN DE LA PRISIÓN
             ================================================ -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Información de la prisión</h3>
                    <h5>Asistencia y actividad del programa</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="form-group row fila-form">

                <div class="col-sm-3">
                    <strong>Total población en la prisión:</strong>
                    <input type="number" name="poblacion_total" id="poblacion_total"
                           class="form-control" min="1" value="" required />
                </div>

                <div class="col-sm-3">
                    <strong>Prisioneros invitados:</strong>
                    <input type="number" name="prisioneros_invitados" id="prisioneros_invitados"
                           class="form-control" min="1" value="" required />
                </div>

                <div class="col-sm-3">
                    <strong>Prisioneros que iniciaron el curso:</strong>
                    <input type="number" name="prisioneros_iniciaron" id="prisioneros_iniciaron"
                           class="form-control" min="1" value="" required />
                </div>

                <div class="col-sm-3">
                    <strong>Cursos activos de LPP:</strong>
                    <input type="number" name="cursos_activos" id="cursos_activos"
                           class="form-control" readonly />
                </div>

            </div>

        </div><!-- /seccion 2 -->

        <!-- ================================================
             SECCIÓN 3 — REGISTRO DE GRADUADOS
             ================================================ -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Registro de graduados</h3>
                    <h5>Listado de prisioneros graduados del programa</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="table-responsive">
                <table class="table-graduados" id="tabla-graduados">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre completo del graduado</th>
                            <th>Tarjeta dactilar / N° identificación</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="body-graduados"></tbody>
                </table>
            </div>

            <div class="barra-controles">
                <div class="barra-fila">
                    <button type="button" class="btn-agregar" id="btn-agregar-graduado">
                        &#43; Agregar graduado
                    </button>
                    <span class="sep-agregar">|</span>
                    <div class="grupo-generar">
                        <span class="lbl-generar">Generar registros:</span>
                        <input type="number" id="cant-generar" class="inp-generar" min="1" max="100" placeholder="N°" />
                        <button type="button" class="btn-generar-inline" id="btn-generar">Generar</button>
                    </div>
                    <button type="button" class="btn-eliminar-todo" id="btn-eliminar-todo">
                        &#128465; Eliminar todo
                    </button>
                </div>
            </div>

            <div class="card-total">
                <span class="card-total-label">Graduados registrados</span>
                <div class="card-total-badge">
                    <input type="number" id="total_graduados_vis" readonly value="0" />
                </div>
            </div>
            <input type="hidden" name="total_graduados" id="total_graduados" value="0" />

        </div><!-- /seccion 3 -->

        <!-- ================================================
             SECCIÓN 4 — REGISTRO DE VOLUNTARIOS INTERNOS
             ================================================ -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Registro de voluntarios internos</h3>
                    <h5>Siervos facilitadores del programa</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="table-responsive">
                <table class="table-graduados" id="tabla-internos">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre completo del voluntario interno</th>
                            <th>Tarjeta dactilar / N° identificación</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="body-internos"></tbody>
                </table>
            </div>

            <div class="barra-controles">
                <div class="barra-fila">
                    <button type="button" class="btn-agregar" id="btn-agregar-interno">
                        &#43; Agregar voluntario interno
                    </button>
                    <span class="sep-agregar">|</span>
                    <div class="grupo-generar">
                        <span class="lbl-generar">Generar registros:</span>
                        <input type="number" id="cant-generar-interno" class="inp-generar" min="1" max="100" placeholder="N°" />
                        <button type="button" class="btn-generar-inline" id="btn-generar-interno">Generar</button>
                    </div>
                    <button type="button" class="btn-eliminar-todo" id="btn-eliminar-todo-interno">
                        &#128465; Eliminar todo
                    </button>
                </div>
            </div>

            <div class="card-total">
                <span class="card-total-label">Voluntarios internos registrados</span>
                <div class="card-total-badge">
                    <input type="number" id="total_internos_vis" readonly value="0" />
                </div>
            </div>
            <input type="hidden" name="total_voluntarios_internos" id="total_voluntarios_internos" value="0" />

        </div><!-- /seccion 4 -->

        <!-- ================================================
             SECCIÓN 5 — REGISTRO DE VOLUNTARIOS EXTERNOS
             ================================================ -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Registro de voluntarios externos</h3>
                    <h5>Voluntarios externos que apoyan el programa</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="table-responsive">
                <table class="table-graduados" id="tabla-externos">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre completo del voluntario externo</th>
                            <th>Tarjeta dactilar / N° identificación</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="body-externos"></tbody>
                </table>
            </div>

            <div class="barra-controles">
                <div class="barra-fila">
                    <button type="button" class="btn-agregar" id="btn-agregar-externo">
                        &#43; Agregar voluntario externo
                    </button>
                    <span class="sep-agregar">|</span>
                    <div class="grupo-generar">
                        <span class="lbl-generar">Generar registros:</span>
                        <input type="number" id="cant-generar-externo" class="inp-generar" min="1" max="100" placeholder="N°" />
                        <button type="button" class="btn-generar-inline" id="btn-generar-externo">Generar</button>
                    </div>
                    <button type="button" class="btn-eliminar-todo" id="btn-eliminar-todo-externo">
                        &#128465; Eliminar todo
                    </button>
                </div>
            </div>

            <div class="card-total">
                <span class="card-total-label">Voluntarios externos registrados</span>
                <div class="card-total-badge">
                    <input type="number" id="total_externos_vis" readonly value="0" />
                </div>
            </div>
            <input type="hidden" name="total_voluntarios_externos" id="total_voluntarios_externos" value="0" />

        </div><!-- /seccion 5 -->

        <!-- ================================================
             SECCIÓN 6 — MÉTODO DE VERIFICACIÓN
             ================================================ -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Método de verificación</h3>
                    <h5>Indicadores de seguimiento del programa</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <!-- Indicadores numéricos -->
            <div class="verif-indicadores">

                <div class="verif-card">
                    
                    <div class="verif-card-body">
                        <label class="verif-label" for="discipulos_pasaron_cm">
                            Discípulos que pasaron a C&amp;M
                        </label>
                        <input type="number" name="discipulos_pasaron_cm" id="discipulos_pasaron_cm"
                               class="verif-input" min="0" value="0" required />
                    </div>
                </div>

                <div class="verif-card">
                  
                    <div class="verif-card-body">
                        <label class="verif-label" for="costo_recursos">
                            Costo de recursos gestionados ($)
                        </label>
                        <input type="number" name="costo_recursos" id="costo_recursos"
                               class="verif-input" min="0" value="0" required />
                    </div>
                </div>

            </div>

            <!-- Archivos adjuntos -->
            <div class="verif-adjuntos">

                <div class="verif-upload-card">
                    <div class="verif-upload-header">

                        <span class="verif-upload-title">Foto de grupo <span style="color:#c0392b">*</span></span>
                    </div>
                    <div class="verif-upload-body">
                        <input type="file" name="archivo_foto" id="archivo_foto"
                               class="verif-file-input" accept=".jpg,.jpeg,.png,.gif" />
                        <p class="verif-upload-hint">JPG, JPEG, PNG, GIF &mdash; <span style="color:#c0392b;font-style:normal">Obligatorio</span></p>
                    </div>
                </div>

                <div class="verif-upload-card">
                    <div class="verif-upload-header">
                        <span class="verif-upload-title">Testimonio <span style="color:#c0392b">*</span></span>
                    </div>
                    <div class="verif-upload-body">
                        <input type="file" name="archivo_testimonio" id="archivo_testimonio"
                               class="verif-file-input" accept=".pdf,.doc,.docx" />
                        <p class="verif-upload-hint">PDF, DOC, DOCX &mdash; <span style="color:#c0392b;font-style:normal">Obligatorio</span></p>
                    </div>
                </div>

            </div>

        </div><!-- /seccion 6 -->

        <!-- Botones -->
        <div class="cont-btn">
            <input type="button"
                   onclick="window.location.href='index.php?doc=consultar-reporte-lpp'"
                   class="btn btn-info" value="Cancelar" />
            <input type="submit" name="button" value="Continuar"
                   class="btn btn-success" id="btn-guardar" />
        </div>

        <input type="hidden" name="funcion" value="insertar" />

    </form>

    <?php endif; ?>
</div>

<script type="text/javascript">
$(document).ready(function () {

    /* ----------------------------------------------------------
       1. Cárcel → inyecta departamento, municipio y dirección
          usando datos_carcel_ubicacion.php igual que el sistema original
    ---------------------------------------------------------- */
    function recargaLista() {
        $.ajax({
            type   : 'POST',
            url    : 'datos_carcel_ubicacion.php',
            data   : 'id_carcel=' + $('#carcel_id').val(),
            success: function (r) {
                $('#ubicacion').html(r);
            }
        });
    }

    /* Disparar al cargar y al cambiar */
    recargaLista();
    $('#carcel_id').on('change', function () {
        recargaLista();
    });

    /* ----------------------------------------------------------
       2. Tabla de graduados — agregar / eliminar filas
    ---------------------------------------------------------- */
    var contadorGraduados = 0;

    function actualizarNumeracion() {
        $('#body-graduados tr').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }

    function crearFila() {
        contadorGraduados++;
        return '<tr>' +
            '<td>' + contadorGraduados + '</td>' +
            '<td><input type="text" class="inp-tabla grad-nombre" name="grad_nombre[]" placeholder="Nombre completo" /></td>' +
            '<td><input type="text" class="inp-tabla grad-identificacion" name="grad_identificacion[]" placeholder="N° identificación" /></td>' +
            '<td>' +
                '<button type="button" class="btn-eliminar-fila" title="Eliminar">&#10005;</button>' +
            '</td>' +
        '</tr>';
    }

    function agregarFila(enfocar) {
        $('#body-graduados').append(crearFila());
        actualizarNumeracion();
        if (enfocar !== false) {
            $('#body-graduados tr:last .grad-nombre').focus();
        }
    }

    function puedeEliminar() {
        /* Solo se puede eliminar si hay más de 1 fila */
        return $('#body-graduados tr').length > 1;
    }

    function actualizarBotonesEliminar() {
        if (puedeEliminar()) {
            $('.btn-eliminar-fila').prop('disabled', false).css('opacity', '1');
        } else {
            $('.btn-eliminar-fila').prop('disabled', true).css('opacity', '0.3');
        }
    }

    /* Inicializar con 1 fila obligatoria */
    agregarFila(false);
    actualizarBotonesEliminar();

    /* Botón agregar una fila */
    $('#btn-agregar-graduado').on('click', function () {
        agregarFila(true);
        actualizarBotonesEliminar();
        actualizarTotalGraduados();
    });

    /* Generador masivo */
    $('#btn-generar').on('click', function () {
        var cant = parseInt($('#cant-generar').val(), 10);
        if (isNaN(cant) || cant < 1) {
            alert('Ingresa un número válido mayor a 0.');
            $('#cant-generar').focus();
            return;
        }
        for (var i = 0; i < cant; i++) {
            agregarFila(false);
        }
        actualizarBotonesEliminar();
        actualizarTotalGraduados();
        /* Enfocar el primer campo vacío generado */
        $('#body-graduados .grad-nombre').filter(function () {
            return $(this).val() === '';
        }).first().focus();
        $('#cant-generar').val('');
    });

    /* Eliminar fila (delegado) — mínimo 1 fila siempre */
    $('#body-graduados').on('click', '.btn-eliminar-fila', function () {
        if (!puedeEliminar()) return;
        $(this).closest('tr').remove();
        actualizarNumeracion();
        actualizarBotonesEliminar();
        actualizarTotalGraduados();
    });

    /* Eliminar todo */
    $('#btn-eliminar-todo').on('click', function () {
        var total = $('#body-graduados tr').length;
        if (!confirm('¿Estás seguro de que deseas eliminar todos los ' + total + ' registros de graduados? Esta acción no se puede deshacer.')) return;
        $('#body-graduados').empty();
        contadorGraduados = 0;
        agregarFila(false);       // siempre queda 1 fila vacía
        actualizarBotonesEliminar();
        actualizarTotalGraduados();
    });

    /* Recalcular total de graduados completos */
    function actualizarTotalGraduados() {
        var completos = 0;
        $('#body-graduados tr').each(function () {
            var nom  = $(this).find('.grad-nombre').val().trim();
            var iden = $(this).find('.grad-identificacion').val().trim();
            if (nom !== '' && iden !== '') completos++;
        });
        $('#total_graduados_vis').val(completos);
        $('#total_graduados').val(completos);

        /* Restriccion 2: graduados <= poblacion_total */
        var poblacion = parseInt($('#poblacion_total').val(), 10);
        if (!isNaN(poblacion) && completos > poblacion) {
            alert('La cantidad de graduados registrados (' + completos + ') no puede superar la poblacion total de la prision (' + poblacion + ').');
        }

        /* Restriccion 3: discipulos_pasaron_cm <= graduados */
        $('#discipulos_pasaron_cm').attr('max', completos);
        var discipulos = parseInt($('#discipulos_pasaron_cm').val(), 10);
        if (!isNaN(discipulos) && discipulos > completos) {
            $('#discipulos_pasaron_cm').val(completos);
        }
    }

    /* Recalcular al escribir en cualquier input de la tabla */
    $('#body-graduados').on('input', '.grad-nombre, .grad-identificacion', function () {
        actualizarTotalGraduados();
    });

    /* Inicializar total */
    actualizarTotalGraduados();

    /* ----------------------------------------------------------
       3. Tabla de voluntarios internos — misma lógica que graduados
    ---------------------------------------------------------- */
    var contadorInternos = 0;

    function actualizarNumeracionInternos() {
        $('#body-internos tr').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }

    function crearFilaInterno() {
        contadorInternos++;
        return '<tr>' +
            '<td>' + contadorInternos + '</td>' +
            '<td><input type="text" class="inp-tabla int-nombre" name="interno_nombre[]" placeholder="Nombre completo" /></td>' +
            '<td><input type="text" class="inp-tabla int-identificacion" name="interno_identificacion[]" placeholder="N° identificación" /></td>' +
            '<td><button type="button" class="btn-eliminar-fila btn-elim-interno" title="Eliminar">&#10005;</button></td>' +
        '</tr>';
    }

    function agregarFilaInterno(enfocar) {
        $('#body-internos').append(crearFilaInterno());
        actualizarNumeracionInternos();
        if (enfocar !== false) $('#body-internos tr:last .int-nombre').focus();
    }

    function puedeEliminarInterno() {
        return $('#body-internos tr').length > 1;
    }

    function actualizarBotonesEliminarInterno() {
        if (puedeEliminarInterno()) {
            $('.btn-elim-interno').prop('disabled', false).css('opacity', '1');
        } else {
            $('.btn-elim-interno').prop('disabled', true).css('opacity', '0.3');
        }
    }

    function actualizarTotalInternos() {
        var completos = 0;
        $('#body-internos tr').each(function () {
            var nom  = $(this).find('.int-nombre').val().trim();
            var iden = $(this).find('.int-identificacion').val().trim();
            if (nom !== '' && iden !== '') completos++;
        });
        $('#total_internos_vis').val(completos);
        $('#total_voluntarios_internos').val(completos);
    }

    /* Inicializar con 1 fila */
    agregarFilaInterno(false);
    actualizarBotonesEliminarInterno();
    actualizarTotalInternos();

    $('#btn-agregar-interno').on('click', function () {
        agregarFilaInterno(true);
        actualizarBotonesEliminarInterno();
        actualizarTotalInternos();
    });

    $('#btn-generar-interno').on('click', function () {
        var cant = parseInt($('#cant-generar-interno').val(), 10);
        if (isNaN(cant) || cant < 1) {
            alert('Ingresa un número válido mayor a 0.');
            $('#cant-generar-interno').focus();
            return;
        }
        for (var i = 0; i < cant; i++) agregarFilaInterno(false);
        actualizarBotonesEliminarInterno();
        actualizarTotalInternos();
        $('#body-internos .int-nombre').filter(function () { return $(this).val() === ''; }).first().focus();
        $('#cant-generar-interno').val('');
    });

    $('#btn-eliminar-todo-interno').on('click', function () {
        var total = $('#body-internos tr').length;
        if (!confirm('¿Estás seguro de que deseas eliminar todos los ' + total + ' registros de voluntarios internos? Esta acción no se puede deshacer.')) return;
        $('#body-internos').empty();
        contadorInternos = 0;
        agregarFilaInterno(false);
        actualizarBotonesEliminarInterno();
        actualizarTotalInternos();
    });

    $('#body-internos').on('click', '.btn-elim-interno', function () {
        if (!puedeEliminarInterno()) return;
        $(this).closest('tr').remove();
        actualizarNumeracionInternos();
        actualizarBotonesEliminarInterno();
        actualizarTotalInternos();
    });

    $('#body-internos').on('input', '.int-nombre, .int-identificacion', function () {
        actualizarTotalInternos();
    });

    /* ----------------------------------------------------------
       5. Tabla de voluntarios externos — misma lógica que internos
    ---------------------------------------------------------- */
    var contadorExternos = 0;

    function actualizarNumeracionExternos() {
        $('#body-externos tr').each(function (i) {
            $(this).find('td:first').text(i + 1);
        });
    }

    function crearFilaExterno() {
        contadorExternos++;
        return '<tr>' +
            '<td>' + contadorExternos + '</td>' +
            '<td><input type="text" class="inp-tabla ext-nombre" name="externo_nombre[]" placeholder="Nombre completo" /></td>' +
            '<td><input type="text" class="inp-tabla ext-identificacion" name="externo_identificacion[]" placeholder="N° identificación" /></td>' +
            '<td><button type="button" class="btn-eliminar-fila btn-elim-externo" title="Eliminar">&#10005;</button></td>' +
        '</tr>';
    }

    function agregarFilaExterno(enfocar) {
        $('#body-externos').append(crearFilaExterno());
        actualizarNumeracionExternos();
        if (enfocar !== false) $('#body-externos tr:last .ext-nombre').focus();
    }

    function puedeEliminarExterno() {
        return $('#body-externos tr').length > 1;
    }

    function actualizarBotonesEliminarExterno() {
        if (puedeEliminarExterno()) {
            $('.btn-elim-externo').prop('disabled', false).css('opacity', '1');
        } else {
            $('.btn-elim-externo').prop('disabled', true).css('opacity', '0.3');
        }
    }

    function actualizarTotalExternos() {
        var completos = 0;
        $('#body-externos tr').each(function () {
            var nom  = $(this).find('.ext-nombre').val().trim();
            var iden = $(this).find('.ext-identificacion').val().trim();
            if (nom !== '' && iden !== '') completos++;
        });
        $('#total_externos_vis').val(completos);
        $('#total_voluntarios_externos').val(completos);
    }

    /* Inicializar con 1 fila */
    agregarFilaExterno(false);
    actualizarBotonesEliminarExterno();
    actualizarTotalExternos();

    $('#btn-agregar-externo').on('click', function () {
        agregarFilaExterno(true);
        actualizarBotonesEliminarExterno();
        actualizarTotalExternos();
    });

    $('#btn-generar-externo').on('click', function () {
        var cant = parseInt($('#cant-generar-externo').val(), 10);
        if (isNaN(cant) || cant < 1) {
            alert('Ingresa un número válido mayor a 0.');
            $('#cant-generar-externo').focus();
            return;
        }
        for (var i = 0; i < cant; i++) agregarFilaExterno(false);
        actualizarBotonesEliminarExterno();
        actualizarTotalExternos();
        $('#body-externos .ext-nombre').filter(function () { return $(this).val() === ''; }).first().focus();
        $('#cant-generar-externo').val('');
    });

    $('#btn-eliminar-todo-externo').on('click', function () {
        var total = $('#body-externos tr').length;
        if (!confirm('¿Estás seguro de que deseas eliminar todos los ' + total + ' registros de voluntarios externos? Esta acción no se puede deshacer.')) return;
        $('#body-externos').empty();
        contadorExternos = 0;
        agregarFilaExterno(false);
        actualizarBotonesEliminarExterno();
        actualizarTotalExternos();
    });

    $('#body-externos').on('click', '.btn-elim-externo', function () {
        if (!puedeEliminarExterno()) return;
        $(this).closest('tr').remove();
        actualizarNumeracionExternos();
        actualizarBotonesEliminarExterno();
        actualizarTotalExternos();
    });

    $('#body-externos').on('input', '.ext-nombre, .ext-identificacion', function () {
        actualizarTotalExternos();
    });

    /* ----------------------------------------------------------
       4. Restricciones numéricas en cascada
          · invitados  ≤ población_total
          · iniciaron  ≤ invitados
    ---------------------------------------------------------- */
    $('#poblacion_total').on('input change', function () {
        var poblacion = parseInt($(this).val(), 10);
        if (!isNaN(poblacion) && poblacion >= 1) {
            $('#prisioneros_invitados').attr('max', poblacion);
            /* Si el valor actual ya supera el nuevo límite, corregirlo */
            var invitados = parseInt($('#prisioneros_invitados').val(), 10);
            if (!isNaN(invitados) && invitados > poblacion) {
                $('#prisioneros_invitados').val(poblacion);
                $('#prisioneros_invitados').trigger('change');
            }
        }
    });

    $('#prisioneros_invitados').on('input change', function () {
        var invitados  = parseInt($(this).val(), 10);
        var poblacion  = parseInt($('#poblacion_total').val(), 10);

        /* Corregir si supera la población */
        if (!isNaN(invitados) && !isNaN(poblacion) && invitados > poblacion) {
            $(this).val(poblacion);
            invitados = poblacion;
        }

        if (!isNaN(invitados) && invitados >= 1) {
            $('#prisioneros_iniciaron').attr('max', invitados);
            /* Si el valor actual ya supera el nuevo límite, corregirlo */
            var iniciaron = parseInt($('#prisioneros_iniciaron').val(), 10);
            if (!isNaN(iniciaron) && iniciaron > invitados) {
                $('#prisioneros_iniciaron').val(invitados);
                $('#prisioneros_iniciaron').trigger('change');
            }
        }
    });

    /* ----------------------------------------------------------
       5. Cursos activos — ceil(iniciaron / 12), mínimo 1
    ---------------------------------------------------------- */
    $('#prisioneros_iniciaron').on('input change', function () {
        var invitados = parseInt($('#prisioneros_invitados').val(), 10);
        var iniciaron = parseInt($(this).val(), 10);

        /* Corregir si supera los invitados */
        if (!isNaN(iniciaron) && !isNaN(invitados) && iniciaron > invitados) {
            $(this).val(invitados);
            iniciaron = invitados;
        }

        var cursos = '';
        if (!isNaN(iniciaron) && iniciaron > 0) {
            cursos = iniciaron <= 12 ? 1 : Math.trunc(iniciaron / 12) + (iniciaron % 12 !== 0 ? 1 : 0);
        }
        $('#cursos_activos').val(cursos);
    });

    /* ----------------------------------------------------------
       Helpers de validacion visual
    ---------------------------------------------------------- */
    function limpiarErrores() {
        $('.field-error').removeClass('field-error');
        $('.field-error-msg').remove();
        $('#lista-errores').empty();
        $('#panel-errores').hide();
    }

    function marcarError($el, mensaje) {
        $el.addClass('field-error');
        if ($el.next('.field-error-msg').length === 0) {
            $el.after('<span class="field-error-msg">' + mensaje + '</span>');
        }
    }

    function agregarAlPanel(mensaje, $elFoco) {
        var $li = $('<li></li>').text(mensaje);
        if ($elFoco) {
            $li.on('click', function () {
                $('html, body').animate({ scrollTop: $elFoco.offset().top - 120 }, 300);
                setTimeout(function () { $elFoco.focus(); }, 320);
            });
        }
        $('#lista-errores').append($li);
    }

    function mostrarPanel() {
        $('#panel-errores').show();
        $('html, body').animate({ scrollTop: $('#panel-errores').offset().top - 80 }, 400);
    }

    /* Limpiar error individual al corregir el campo */
    $(document).on('input change', '.field-error', function () {
        $(this).removeClass('field-error');
        $(this).next('.field-error-msg').remove();
    });

    /* ----------------------------------------------------------
       3. Validacion antes de enviar
    ---------------------------------------------------------- */
    $('#form1').on('submit', function (e) {

        limpiarErrores();
        var hayError = false;

        /* ---- Carcel ---- */
        if (!$('#carcel_id').val()) {
            marcarError($('#carcel_id'), 'Seleccione una cárcel.');
            agregarAlPanel('Seleccione una cárcel.', $('#carcel_id'));
            hayError = true;
        }

        /* ---- Campos numericos obligatorios > 0 ---- */
        var numericos = [
            { id: 'pabellon',              label: 'N° de patios y/o pabbellón' },
            { id: 'poblacion_total',       label: 'Total población' },
            { id: 'prisioneros_invitados', label: 'Prisioneros invitados' },
            { id: 'prisioneros_iniciaron', label: 'Prisioneros que iniciaron el curso' }
        ];
        for (var i = 0; i < numericos.length; i++) {
            var $f  = $('#' + numericos[i].id);
            var val = parseInt($f.val(), 10);
            if (isNaN(val) || val < 1) {
                var msg = 'El campo "' + numericos[i].label + '" debe ser un número mayor a 0.';
                marcarError($f, msg);
                agregarAlPanel(msg, $f);
                hayError = true;
            }
        }

        /* ---- Restriccion 1: invitados <= poblacion ---- */
        var poblacionVal = parseInt($('#poblacion_total').val(), 10);
        var invitadosVal = parseInt($('#prisioneros_invitados').val(), 10);
        var iniciaronVal = parseInt($('#prisioneros_iniciaron').val(), 10);
        if (!isNaN(invitadosVal) && !isNaN(poblacionVal) && invitadosVal > poblacionVal) {
            var msg1 = '"Prisioneros invitados" (' + invitadosVal + ') no puede superar "Total población" (' + poblacionVal + ').';
            marcarError($('#prisioneros_invitados'), msg1);
            agregarAlPanel(msg1, $('#prisioneros_invitados'));
            hayError = true;
        }

        /* ---- Restriccion 1: iniciaron <= invitados ---- */
        if (!isNaN(iniciaronVal) && !isNaN(invitadosVal) && iniciaronVal > invitadosVal) {
            var msg2 = '"Prisioneros que iniciaron" (' + iniciaronVal + ') no puede superar "Prisioneros invitados" (' + invitadosVal + ').';
            marcarError($('#prisioneros_iniciaron'), msg2);
            agregarAlPanel(msg2, $('#prisioneros_iniciaron'));
            hayError = true;
        }

        /* ---- Campos numericos >= 0 (verificacion) ---- */
        var verificacion = [
            { id: 'discipulos_pasaron_cm', label: 'Número de discípulos que pasaron a C&M' },
            { id: 'costo_recursos',        label: 'Costo de recursos gestionados' }
        ];
        for (var j = 0; j < verificacion.length; j++) {
            var $fv  = $('#' + verificacion[j].id);
            var vval = parseInt($fv.val(), 10);
            if (isNaN(vval) || vval < 0) {
                var msgv = 'El campo "' + verificacion[j].label + '" debe ser un número mayor o igual a 0.';
                marcarError($fv, msgv);
                agregarAlPanel(msgv, $fv);
                hayError = true;
            }
        }

        /* ---- Restriccion 2: graduados <= poblacion ---- */
        var totalGradVal = parseInt($('#total_graduados').val(), 10) || 0;
        if (!isNaN(poblacionVal) && totalGradVal > poblacionVal) {
            var msg3 = 'Los graduados registrados (' + totalGradVal + ') no pueden superar la población total (' + poblacionVal + ').';
            marcarError($('#total_graduados_vis'), msg3);
            agregarAlPanel(msg3, $('#body-graduados .grad-nombre').first());
            hayError = true;
        }

        /* ---- Restriccion 3: discipulos <= graduados ---- */
        var discipulosVal = parseInt($('#discipulos_pasaron_cm').val(), 10) || 0;
        if (discipulosVal > totalGradVal) {
            var msg4 = '"Discípulos que pasaron a C&M" (' + discipulosVal + ') no puede superar los graduados registrados (' + totalGradVal + ').';
            marcarError($('#discipulos_pasaron_cm'), msg4);
            agregarAlPanel(msg4, $('#discipulos_pasaron_cm'));
            hayError = true;
        }

        /* ---- Voluntarios internos: minimo 1 completo ---- */
        var intCompletos = 0;
        $('#body-internos tr').each(function () {
            var nom  = $(this).find('.int-nombre').val().trim();
            var iden = $(this).find('.int-identificacion').val().trim();
            if (nom !== '' && iden !== '') {
                intCompletos++;
            } else if (nom !== '' || iden !== '') {
                /* Fila incompleta: marcar el campo vacio */
                if (nom === '')  { marcarError($(this).find('.int-nombre'),          'Nombre requerido.'); }
                if (iden === '') { marcarError($(this).find('.int-identificacion'),  'Identificación requerida.'); }
            }
        });
        if (intCompletos === 0) {
            var msg5 = 'Debes registrar al menos un voluntario interno con nombre e identificación completos.';
            marcarError($('#body-internos .int-nombre').first(), msg5);
            agregarAlPanel(msg5, $('#body-internos .int-nombre').first());
            hayError = true;
        } else {
            /* Si hay incompletos ademas del minimo requerido */
            var intIncomp = $('#body-internos tr').filter(function () {
                var n = $(this).find('.int-nombre').val().trim();
                var d = $(this).find('.int-identificacion').val().trim();
                return (n !== '' || d !== '') && (n === '' || d === '');
            }).length;
            if (intIncomp > 0) {
                var msg5b = 'Hay ' + intIncomp + ' fila(s) de voluntarios internos con datos incompletos.';
                agregarAlPanel(msg5b, $('#body-internos .int-nombre').first());
                hayError = true;
            }
        }

        /* ---- Graduados: minimo 1 completo ---- */
        var gradCompletos = 0;
        $('#body-graduados tr').each(function () {
            var nom  = $(this).find('.grad-nombre').val().trim();
            var iden = $(this).find('.grad-identificacion').val().trim();
            if (nom !== '' && iden !== '') {
                gradCompletos++;
            } else if (nom !== '' || iden !== '') {
                if (nom === '')  { marcarError($(this).find('.grad-nombre'),          'Nombre requerido.'); }
                if (iden === '') { marcarError($(this).find('.grad-identificacion'),  'Identificación requerida.'); }
            }
        });
        if (gradCompletos === 0) {
            var msg6 = 'Debes registrar al menos un graduado con nombre e identificación completos.';
            marcarError($('#body-graduados .grad-nombre').first(), msg6);
            agregarAlPanel(msg6, $('#body-graduados .grad-nombre').first());
            hayError = true;
        } else {
            var gradIncomp = $('#body-graduados tr').filter(function () {
                var n = $(this).find('.grad-nombre').val().trim();
                var d = $(this).find('.grad-identificacion').val().trim();
                return (n !== '' || d !== '') && (n === '' || d === '');
            }).length;
            if (gradIncomp > 0) {
                var msg6b = 'Hay ' + gradIncomp + ' fila(s) de graduados con datos incompletos.';
                agregarAlPanel(msg6b, $('#body-graduados .grad-nombre').first());
                hayError = true;
            }
        }

        /* ---- Externos: minimo 1 completo (igual que internos) ---- */
        var extCompletos = 0;
        $('#body-externos tr').each(function () {
            var nom  = $(this).find('.ext-nombre').val().trim();
            var iden = $(this).find('.ext-identificacion').val().trim();
            if (nom !== '' && iden !== '') {
                extCompletos++;
            } else if (nom !== '' || iden !== '') {
                if (nom === '')  { marcarError($(this).find('.ext-nombre'),          'Nombre requerido.'); }
                if (iden === '') { marcarError($(this).find('.ext-identificacion'),  'Identificación requerida.'); }
            }
        });
        if (extCompletos === 0) {
            var msg7 = 'Debes registrar al menos un voluntario externo con nombre e identificación completos.';
            marcarError($('#body-externos .ext-nombre').first(), msg7);
            agregarAlPanel(msg7, $('#body-externos .ext-nombre').first());
            hayError = true;
        } else {
            var extIncomp = $('#body-externos tr').filter(function () {
                var n = $(this).find('.ext-nombre').val().trim();
                var d = $(this).find('.ext-identificacion').val().trim();
                return (n !== '' || d !== '') && (n === '' || d === '');
            }).length;
            if (extIncomp > 0) {
                var msg7b = 'Hay ' + extIncomp + ' fila(s) de voluntarios externos con datos incompletos.';
                agregarAlPanel(msg7b, $('#body-externos .ext-nombre').first());
                hayError = true;
            }
        }

        /* ---- Restriccion 4a: foto obligatoria ---- */
        var fotoInput = $('#archivo_foto')[0];
        if (!fotoInput || fotoInput.files.length === 0) {
            var msg8 = 'La Foto de grupo es obligatoria.';
            marcarError($('#archivo_foto'), msg8);
            $('#archivo_foto').closest('.verif-upload-card').addClass('field-error');
            agregarAlPanel(msg8, $('#archivo_foto'));
            hayError = true;
        } else {
            var fotoExt = fotoInput.files[0].name.split('.').pop().toLowerCase();
            if (['jpg','jpeg','png','gif'].indexOf(fotoExt) === -1) {
                var msg8b = 'La Foto de grupo debe ser una imagen (JPG, JPEG, PNG o GIF).';
                marcarError($('#archivo_foto'), msg8b);
                agregarAlPanel(msg8b, $('#archivo_foto'));
                hayError = true;
            }
        }

        /* ---- Restriccion 4b: testimonio obligatorio ---- */
        var testInput = $('#archivo_testimonio')[0];
        if (!testInput || testInput.files.length === 0) {
            var msg9 = 'El archivo de Testimonio es obligatorio.';
            marcarError($('#archivo_testimonio'), msg9);
            $('#archivo_testimonio').closest('.verif-upload-card').addClass('field-error');
            agregarAlPanel(msg9, $('#archivo_testimonio'));
            hayError = true;
        } else {
            var testExt = testInput.files[0].name.split('.').pop().toLowerCase();
            if (['pdf','doc','docx'].indexOf(testExt) === -1) {
                var msg9b = 'El archivo de Testimonio debe ser PDF, DOC o DOCX.';
                marcarError($('#archivo_testimonio'), msg9b);
                agregarAlPanel(msg9b, $('#archivo_testimonio'));
                hayError = true;
            }
        }

        /* ---- Resultado final ---- */
        if (hayError) {
            e.preventDefault();
            mostrarPanel();
            return false;
        }

        /* Sin errores: mostrar modal de confirmacion (no bloquea el hilo) */
        e.preventDefault();
        $('#modal-confirmar').addClass('activo');
        $('#modal-btn-si').off('click').on('click', function () {
            $('#modal-confirmar').removeClass('activo');
            /* Mostrar overlay de carga de inmediato */
            $('#overlay-carga').addClass('activo');
            $('#btn-guardar').prop('disabled', true);
            /* Enviar el formulario en el proximo tick para que el overlay se pinte antes */
            setTimeout(function () {
                document.getElementById('form1').submit();
            }, 30);
        });
        $('#modal-btn-no').off('click').on('click', function () {
            $('#modal-confirmar').removeClass('activo');
            $('#btn-guardar').prop('disabled', false);
        });
        return false;
    });

});
</script>