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
   AJAX — info de la cárcel (departamento, municipio, dirección)
   ============================================================ */
if (isset($_POST['accion']) && $_POST['accion'] === 'info_carcel') {
    $id_carcel = (int)$_POST['id_carcel'];
    $sql = "SELECT
                ru.reub_dir        AS direccion,
                m.municipio        AS municipio,
                d.departamento     AS departamento
            FROM tbl_regional_ubicacion AS ru
            LEFT JOIN dane_municipios    AS m ON m.id_municipio    = ru.reub_mun_fk
            LEFT JOIN dane_departamentos AS d ON d.id_departamento = m.departamento_id
            WHERE ru.reub_id = " . $id_carcel;
    $PSN->query($sql);
    if ($PSN->num_rows() > 0 && $PSN->next_record()) {
        echo json_encode([
            'ok'           => true,
            'departamento' => $PSN->f('departamento'),
            'municipio'    => $PSN->f('municipio'),
            'direccion'    => $PSN->f('direccion'),
        ]);
    } else {
        echo json_encode(['ok' => false]);
    }
    exit;
}

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

    if ($fecha_reporte === '' || $carcel_id == 0) {
        $error_datos = 1;
        $texto_error = 'La fecha y la cárcel son requeridas.';
    }

    if ($error_datos == 0) {
        $sql = "INSERT INTO reporte_lpp (
                    usuario_id, carcel_id, programa_id, fecha_reporte,
                    periodo_trimestre, pabellon, poblacion_total,
                    prisioneros_invitados, prisioneros_iniciaron, cursos_activos
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
                    ".(int)$cursos_activos."
                )";
        $PSN->query($sql);
        $ultimoId = $PSN->ultimoId();
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

    /* Bloque ubicación (se muestra al elegir cárcel) */
    .bloque-ubicacion {
        display: none;
        margin-top: 14px;
        padding: 14px 16px;
        background: #f8fafc;
        border: 1px solid #d0dbe5;
        border-radius: 10px;
    }
    .bloque-ubicacion .fila-ubi {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .bloque-ubicacion .campo-ubi {
        flex: 1;
        min-width: 140px;
    }
    .bloque-ubicacion strong {
        font-size: 11px;
        letter-spacing: .05em;
        color: #5a6a78;
        margin-bottom: 4px;
    }
    .bloque-ubicacion .form-control {
        height: 38px;
        font-size: 13px;
        background: #ffffff;
    }

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

    @media (max-width: 767px) {
        .cont-tit { flex-direction: column; gap: 8px; }
        .cont-tit .tit-cen { width: 100%; white-space: normal; }
        .report-form .btn { width: 100%; }
        .bloque-ubicacion .campo-ubi { min-width: 100%; }
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

                    <!-- Se despliega al seleccionar una cárcel -->
                    <div class="bloque-ubicacion" id="bloque-ubicacion">
                        <div class="fila-ubi">
                            <div class="campo-ubi">
                                <strong>Departamento</strong>
                                <input type="text" id="txt-departamento" class="form-control" readonly />
                            </div>
                            <div class="campo-ubi">
                                <strong>Municipio</strong>
                                <input type="text" id="txt-municipio" class="form-control" readonly />
                            </div>
                            <div class="campo-ubi">
                                <strong>Dirección</strong>
                                <input type="text" id="txt-direccion" class="form-control" readonly />
                            </div>
                        </div>
                    </div>
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
       1. Cárcel → departamento, municipio, dirección
    ---------------------------------------------------------- */
    $('#carcel_id').on('change', function () {
        var id = $(this).val();

        if (!id) {
            $('#bloque-ubicacion').hide();
            $('#txt-departamento, #txt-municipio, #txt-direccion').val('');
            return;
        }

        $.ajax({
            type    : 'POST',
            url     : window.location.href,
            data    : { accion: 'info_carcel', id_carcel: id },
            dataType: 'json',
            success : function (data) {
                if (data.ok) {
                    $('#txt-departamento').val(data.departamento || '');
                    $('#txt-municipio').val(data.municipio      || '');
                    $('#txt-direccion').val(data.direccion      || '');
                    $('#bloque-ubicacion').show();
                } else {
                    $('#bloque-ubicacion').hide();
                    $('#txt-departamento, #txt-municipio, #txt-direccion').val('');
                }
            }
        });
    });

    /* ----------------------------------------------------------
       2. Cursos activos — ceil(iniciaron / 12), mínimo 1
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

        if (confirm('¿Está seguro de que desea guardar este reporte?')) {
            $('#btn-guardar').prop('disabled', true);
            return true;
        }

        e.preventDefault();
        return false;
    });

});
</script>