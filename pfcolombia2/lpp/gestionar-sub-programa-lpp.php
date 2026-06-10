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

    if ($fecha_reporte === '' || $carcel_id == 0) {
        $error_datos = 1;
        $texto_error = 'La fecha y la cárcel son requeridas.';
    }

    if ($error_datos == 0) {
        $sql = "INSERT INTO reporte_lpp (
                    usuario_id, carcel_id, programa_id, fecha_reporte,
                    periodo_trimestre, pabellon, poblacion_total,
                    prisioneros_invitados, prisioneros_iniciaron, cursos_activos,
                    total_graduados, total_voluntarios_internos, total_voluntarios_externos
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
                    ".(int)$total_voluntarios_externos."
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

    /* Tabla de graduados */
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
        padding: 8px 10px;
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

    .cont-agregar {
        margin-top: 10px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
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
    .lbl-generar {
        font-size: 13px;
        font-weight: 700;
        color: #3a5068;
        white-space: nowrap;
    }
    .inp-generar {
        width: 70px;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #cfd5db;
        font-size: 13px;
        color: #1f2933;
        height: 38px;
        text-align: center;
    }
    .inp-generar:focus {
        border-color: #334e68;
        box-shadow: 0 0 0 3px rgba(51,78,104,.10);
        outline: none;
    }
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
    }
    .btn-eliminar-todo:hover { background: #fae0de; }

    .cont-total-graduados {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 12px;
        padding: 10px 14px;
        background: #f0f4f8;
        border: 1px solid #d0dbe5;
        border-radius: 10px;
        width: fit-content;
    }
    .lbl-total-grad {
        font-size: 13px;
        font-weight: 700;
        color: #1e2d3a;
        white-space: nowrap;
    }
    .inp-total-grad {
        width: 70px;
        padding: 7px 10px;
        border-radius: 8px;
        border: 1px solid #b0c4d4;
        background: #ffffff;
        font-size: 15px;
        font-weight: 700;
        color: #1f3547;
        text-align: center;
        height: 38px;
        cursor: default;
    }

    @media (max-width: 767px) {
        .cont-tit { flex-direction: column; gap: 8px; }
        .cont-tit .tit-cen { width: 100%; white-space: normal; }
        .report-form .btn { width: 100%; }
    }
</style>

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
                    <tbody id="body-graduados">
                        <!-- La primera fila se genera automáticamente -->
                    </tbody>
                </table>
            </div>

            <div class="cont-agregar">
                <button type="button" class="btn btn-agregar" id="btn-agregar-graduado">
                    + Agregar graduado
                </button>
                <span class="sep-agregar">|</span>
                <span class="lbl-generar">¿Cuántos registros deseas generar?</span>
                <input type="number" id="cant-generar" class="inp-generar" min="1" max="100" placeholder="N°" />
                <button type="button" class="btn btn-agregar" id="btn-generar">Generar</button>
                <span class="sep-agregar">|</span>
                <button type="button" class="btn btn-eliminar-todo" id="btn-eliminar-todo">
                    &#128465; Eliminar todo
                </button>
            </div>

            <div class="cont-total-graduados">
                <span class="lbl-total-grad">Total de graduados completos:</span>
                <input type="number" id="total_graduados_vis" class="inp-total-grad" readonly value="0" />
                <input type="hidden" name="total_graduados" id="total_graduados" value="0" />
            </div>

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

            <div class="cont-agregar">
                <button type="button" class="btn btn-agregar" id="btn-agregar-interno">
                    + Agregar voluntario interno
                </button>
                <span class="sep-agregar">|</span>
                <span class="lbl-generar">¿Cuántos registros deseas generar?</span>
                <input type="number" id="cant-generar-interno" class="inp-generar" min="1" max="100" placeholder="N°" />
                <button type="button" class="btn btn-agregar" id="btn-generar-interno">Generar</button>
                <span class="sep-agregar">|</span>
                <button type="button" class="btn btn-eliminar-todo" id="btn-eliminar-todo-interno">
                    &#128465; Eliminar todo
                </button>
            </div>

            <div class="cont-total-graduados">
                <span class="lbl-total-grad">Total de voluntarios internos completos:</span>
                <input type="number" id="total_internos_vis" class="inp-total-grad" readonly value="0" />
                <input type="hidden" name="total_voluntarios_internos" id="total_voluntarios_internos" value="0" />
            </div>

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

            <div class="cont-agregar">
                <button type="button" class="btn btn-agregar" id="btn-agregar-externo">
                    + Agregar voluntario externo
                </button>
                <span class="sep-agregar">|</span>
                <span class="lbl-generar">¿Cuántos registros deseas generar?</span>
                <input type="number" id="cant-generar-externo" class="inp-generar" min="1" max="100" placeholder="N°" />
                <button type="button" class="btn btn-agregar" id="btn-generar-externo">Generar</button>
                <span class="sep-agregar">|</span>
                <button type="button" class="btn btn-eliminar-todo" id="btn-eliminar-todo-externo">
                    &#128465; Eliminar todo
                </button>
            </div>

            <div class="cont-total-graduados">
                <span class="lbl-total-grad">Total de voluntarios externos completos:</span>
                <input type="number" id="total_externos_vis" class="inp-total-grad" readonly value="0" />
                <input type="hidden" name="total_voluntarios_externos" id="total_voluntarios_externos" value="0" />
            </div>

        </div><!-- /seccion 5 -->

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
       4. Cursos activos — ceil(iniciaron / 12), mínimo 1
    ---------------------------------------------------------- */
    $('#prisioneros_iniciaron').on('input change', function () {
        var iniciaron = parseInt($(this).val(), 10);
        var cursos    = '';
        if (!isNaN(iniciaron) && iniciaron > 0) {
            cursos = iniciaron <= 12 ? 1 : Math.trunc(iniciaron / 12) + (iniciaron % 12 !== 0 ? 1 : 0);
        }
        $('#cursos_activos').val(cursos);
    });

    /* ----------------------------------------------------------
       3. Validación antes de enviar
    ---------------------------------------------------------- */
    $('#form1').on('submit', function (e) {

        if (!$('#carcel_id').val()) {
            e.preventDefault();
            alert('Por favor seleccione una cárcel.');
            $('#carcel_id').focus();
            return false;
        }

        var numericos = [
            { id: 'pabellon',              label: 'N° de patios y/o pabellón' },
            { id: 'poblacion_total',       label: 'Total población' },
            { id: 'prisioneros_invitados', label: 'Prisioneros invitados' },
            { id: 'prisioneros_iniciaron', label: 'Prisioneros que iniciaron el curso' }
        ];

        for (var i = 0; i < numericos.length; i++) {
            var val = parseInt($('#' + numericos[i].id).val(), 10);
            if (isNaN(val) || val < 1) {
                e.preventDefault();
                alert('El campo "' + numericos[i].label + '" debe ser un número mayor a 0.');
                $('#' + numericos[i].id).focus();
                return false;
            }
        }

        /* Validar internos: mínimo 1 completo */
        var intCompletos = 0;
        var intIncompletos = 0;
        $('#body-internos tr').each(function () {
            var nom  = $(this).find('.int-nombre').val().trim();
            var iden = $(this).find('.int-identificacion').val().trim();
            if (nom !== '' && iden !== '') { intCompletos++; }
            else if (nom !== '' || iden !== '') { intIncompletos++; }
        });
        if (intCompletos === 0) {
            e.preventDefault();
            alert('Debes registrar al menos un voluntario interno con nombre e identificación completos.');
            $('#body-internos .int-nombre').first().focus();
            return false;
        }
        if (intIncompletos > 0) {
            e.preventDefault();
            alert('Hay ' + intIncompletos + ' fila(s) de voluntarios internos con datos incompletos. Completa ambos campos o elimínalas.');
            $('#body-internos tr').each(function () {
                var nom  = $(this).find('.int-nombre').val().trim();
                var iden = $(this).find('.int-identificacion').val().trim();
                if (nom === '' && iden !== '') { $(this).find('.int-nombre').focus(); return false; }
                if (iden === '' && nom !== '') { $(this).find('.int-identificacion').focus(); return false; }
            });
            return false;
        }

        /* Validar graduados: mínimo 1 completo (nombre + identificación) */
        var gradCompletos = 0;
        var gradIncompletos = 0;
        $('#body-graduados tr').each(function () {
            var nom  = $(this).find('.grad-nombre').val().trim();
            var iden = $(this).find('.grad-identificacion').val().trim();
            if (nom !== '' && iden !== '') {
                gradCompletos++;
            } else if (nom !== '' || iden !== '') {
                gradIncompletos++;
            }
        });

        if (gradCompletos === 0) {
            e.preventDefault();
            alert('Debes registrar al menos un graduado con nombre e identificación completos.');
            $('#body-graduados .grad-nombre').first().focus();
            return false;
        }

        if (gradIncompletos > 0) {
            e.preventDefault();
            alert('Hay ' + gradIncompletos + ' fila(s) de graduados con datos incompletos. Completa ambos campos o elimínalas.');
            /* Enfocar la primera incompleta */
            $('#body-graduados tr').each(function () {
                var nom  = $(this).find('.grad-nombre').val().trim();
                var iden = $(this).find('.grad-identificacion').val().trim();
                if (nom === '' && iden !== '') {
                    $(this).find('.grad-nombre').focus();
                    return false;
                }
                if (iden === '' && nom !== '') {
                    $(this).find('.grad-identificacion').focus();
                    return false;
                }
            });
            return false;
        }

        /* Validar externos: filas incompletas (mínimo 0, pero si hay datos deben estar completos) */
        var extCompletos = 0;
        var extIncompletos = 0;
        $('#body-externos tr').each(function () {
            var nom  = $(this).find('.ext-nombre').val().trim();
            var iden = $(this).find('.ext-identificacion').val().trim();
            if (nom !== '' && iden !== '') { extCompletos++; }
            else if (nom !== '' || iden !== '') { extIncompletos++; }
        });
        if (extIncompletos > 0) {
            e.preventDefault();
            alert('Hay ' + extIncompletos + ' fila(s) de voluntarios externos con datos incompletos. Completa ambos campos o elimínalas.');
            $('#body-externos tr').each(function () {
                var nom  = $(this).find('.ext-nombre').val().trim();
                var iden = $(this).find('.ext-identificacion').val().trim();
                if (nom === '' && iden !== '') { $(this).find('.ext-nombre').focus(); return false; }
                if (iden === '' && nom !== '') { $(this).find('.ext-identificacion').focus(); return false; }
            });
            return false;
        }

        if (confirm('¿Está seguro de que desea guardar este reporte?')) {
            $('#btn-guardar').prop('disabled', true);
            return true;
        }

        e.preventDefault();
        return false;
    });

});
</script>