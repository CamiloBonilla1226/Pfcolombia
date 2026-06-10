<?php
/*
 * crear-reporte-lpp.php
 * Formulario de creación de reporte LPP
 */

$PSN = new DBbase_Sql;

$webArchivo   = 'lpp';
$temp_letrero = 'LA PEREGRINACIÓN DEL PRISIONERO (LPP)';

/* ============================================================
   HELPERS — se declaran aquí por si no existen en el sistema base
   ============================================================ */
if (!function_exists('requestValue')) {
    function requestValue($key, $default = '') {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }
}

/* ============================================================
   PROCESAMIENTO DEL FORMULARIO
   ============================================================ */
$varExito    = 0;
$error_datos = 0;
$texto_error = '';

if (isset($_POST['funcion']) && $_POST['funcion'] === 'insertar') {

    /* ---- SECCIÓN: INFORMACIÓN GENERAL ---- */
    $fecha_reporte     = eliminarInvalidos($_POST['fecha_reporte']);
    $periodo_trimestre = soloNumeros($_POST['periodo_trimestre']);
    $usuario_id        = soloNumeros($_POST['usuario_id']);
    $carcel_id         = soloNumeros($_POST['carcel_id']);
    $pabellon          = soloNumeros($_POST['pabellon']);

    /* ---- SECCIÓN: INFORMACIÓN DE LA PRISIÓN ---- */
    $poblacion_total       = soloNumeros($_POST['poblacion_total']);
    $prisioneros_invitados = soloNumeros($_POST['prisioneros_invitados']);
    $prisioneros_iniciaron = soloNumeros($_POST['prisioneros_iniciaron']);
    $cursos_activos        = soloNumeros($_POST['cursos_activos']);

    /* ---- Validaciones básicas ---- */
    if ($fecha_reporte === '' || $carcel_id == 0) {
        $error_datos = 1;
        $texto_error = 'La fecha y la cárcel son requeridas.';
    }

    if ($error_datos == 0) {
        $sql = "INSERT INTO reporte_lpp (
                    usuario_id,
                    carcel_id,
                    programa_id,
                    fecha_reporte,
                    periodo_trimestre,
                    pabellon,
                    poblacion_total,
                    prisioneros_invitados,
                    prisioneros_iniciaron,
                    cursos_activos
                ) VALUES (
                    " . (int)$_SESSION['id'] . ",
                    " . (int)$carcel_id . ",
                    307,
                    '" . $fecha_reporte . "',
                    " . (int)$periodo_trimestre . ",
                    " . (int)$pabellon . ",
                    " . (int)$poblacion_total . ",
                    " . (int)$prisioneros_invitados . ",
                    " . (int)$prisioneros_iniciaron . ",
                    " . (int)$cursos_activos . "
                )";

        $PSN->query($sql);
        $ultimoId = $PSN->ultimoId();
        $varExito = 1;
    }
}
?>
<!-- ============================================================
     ESTILOS
     ============================================================ -->
<style>
    .report-shell {
        max-width: 1260px;
        margin: 28px auto 40px;
        padding: 0 18px 24px;
    }

    .report-shell .alert {
        background: #ffffff;
        color: #1f2933;
        border: 1px solid #d9dee3;
        border-left: 4px solid #2d3338;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 18px;
        box-shadow: 0 8px 24px rgba(15,23,42,.06);
        font-weight: 700;
        letter-spacing: .01em;
    }
    .report-shell .alert-info    { border-left-color: #243746; }
    .report-shell .alert-success { border-left-color: #56685a; }
    .report-shell .alert-danger  { border-left-color: #7a4340; }

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
        padding: 24px 22px 18px;
        margin-bottom: 18px;
        box-shadow: 0 8px 22px rgba(15,23,42,.04);
    }

    .cont-tit {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }
    .cont-tit .hr { flex: 1; }
    .cont-tit hr  { margin: 0; border: 0; border-top: 1px solid #d7dce1; }
    .cont-tit .tit-cen {
        padding: 14px 22px 12px;
        border-radius: 12px;
        background: #f8f9fa;
        border: 1px solid #d9dee3;
        text-align: center;
    }
    .cont-tit h3 {
        margin: 0;
        color: #1e2d3a;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 22px;
        font-weight: 700;
    }
    .cont-tit h5 {
        margin: 5px 0 0;
        color: #52606d;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .report-form strong {
        display: block;
        margin-bottom: 7px;
        color: #24313d;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .04em;
        line-height: 1.45;
    }
    .report-form .form-control,
    .report-form select {
        padding: 11px 14px;
        border-radius: 10px;
        border: 1px solid #cfd5db;
        background: #ffffff;
        color: #1f2933;
        min-height: 46px;
        height: auto;
        width: 100%;
        box-shadow: none;
        transition: border-color .18s, box-shadow .18s;
    }
    .report-form .form-control:focus,
    .report-form select:focus {
        border-color: #334e68;
        box-shadow: 0 0 0 3px rgba(51,78,104,.10);
        outline: none;
    }
    .report-form .form-control[readonly] {
        background: #f4f5f6;
        color: #5f6b76;
        cursor: not-allowed;
    }

    /* Bloque info cárcel — igual que el original: div#ubicacion debajo del select */
    #ubicacion {
        margin-top: 10px;
    }

    .report-form .btn {
        border-radius: 10px;
        padding: 11px 22px;
        font-weight: 700;
        letter-spacing: .03em;
        border: 1px solid transparent;
        box-shadow: 0 8px 18px rgba(15,23,42,.08);
        cursor: pointer;
        transition: box-shadow .18s, filter .18s;
    }
    .report-form .btn:hover { box-shadow: 0 12px 22px rgba(15,23,42,.12); filter: brightness(1.02); }
    .report-form .btn-success { background: #1f3547; color: #fff; }
    .report-form .btn-info    { background: #4b5b68; color: #fff; }

    .cont-btn {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
        margin-top: 10px;
    }

    @media (max-width: 767px) {
        .cont-tit { flex-direction: column; gap: 10px; }
        .cont-tit .tit-cen { width: 100%; }
        .report-form .btn { width: 100%; }
    }
</style>

<!-- ============================================================
     HTML
     ============================================================ -->
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

        <!-- ==================================================
             SECCIÓN 1 — INFORMACIÓN GENERAL
             ================================================== -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Información general</h3>
                    <h5>Datos del coordinador y la cárcel</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="form-group row">

                <!-- Fecha del registro -->
                <div class="col-sm-2">
                    <strong>Fecha del registro:</strong>
                    <input type="date"
                           name="fecha_reporte"
                           id="fecha_reporte"
                           class="form-control"
                           value="<?= date('Y-m-d') ?>"
                           max="<?= date('Y-m-d') ?>"
                           readonly
                           required />
                </div>

                <!-- Período -->
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

                <!-- Coordinador de prisión -->
                <div class="col-sm-3">
                    <strong>Coordinador de prisión:</strong>
                    <select name="usuario_id" id="usuario_id" class="form-control" required>
                        <option value="<?= (int)$_SESSION['id'] ?>"><?= htmlspecialchars($_SESSION['nombre']) ?></option>
                    </select>
                </div>

                <!-- Cárcel ubicación -->
                <div class="col-sm-3">
                    <strong>Cárcel ubicación:</strong>
                    <select name="carcel_id" id="rep_carcel" class="form-control" required>
                        <option value="">Seleccione una cárcel...</option>
                        <?php
                        if (!empty($_SESSION['empresa_pd'])) {
                            $sql_carceles = "SELECT reub_id, reub_nom
                                             FROM tbl_regional_ubicacion
                                             WHERE reub_reg_fk = " . (int)$_SESSION['empresa_pd'] . "
                                             ORDER BY reub_nom ASC";
                            $PSN->query($sql_carceles);
                            while ($PSN->next_record()) {
                                echo '<option value="' . $PSN->f('reub_id') . '">'
                                   . htmlspecialchars($PSN->f('reub_nom'))
                                   . '</option>';
                            }
                        } else {
                            echo '<option value="">Sin regional asignada</option>';
                        }
                        ?>
                    </select>
                    <!-- El endpoint datos_carcel_ubicacion.php inyecta aquí
                         el HTML con departamento, municipio y dirección -->
                    <div id="ubicacion"></div>
                </div>

                <!-- Número de patios -->
                <div class="col-sm-2">
                    <strong>N° de patios y/o pabellón:</strong>
                    <input type="number"
                           name="pabellon"
                           id="pabellon"
                           class="form-control"
                           min="1"
                           value=""
                           required />
                </div>

            </div><!-- /.form-group -->
        </div><!-- /.seccion -->

        <!-- ==================================================
             SECCIÓN 2 — INFORMACIÓN DE LA PRISIÓN
             ================================================== -->
        <div class="seccion">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3>Información de la prisión</h3>
                    <h5>Asistencia y actividad del programa</h5>
                </div>
                <div class="hr"><hr></div>
            </div>

            <div class="form-group row">

                <!-- Población total -->
                <div class="col-sm-3">
                    <strong>Total población que hay en la prisión:</strong>
                    <input type="number"
                           name="poblacion_total"
                           id="poblacion_total"
                           class="form-control"
                           min="1"
                           value=""
                           required />
                </div>

                <!-- Prisioneros invitados -->
                <div class="col-sm-3">
                    <strong>Número de prisioneros invitados:</strong>
                    <input type="number"
                           name="prisioneros_invitados"
                           id="prisioneros_invitados"
                           class="form-control"
                           min="1"
                           value=""
                           required />
                </div>

                <!-- Prisioneros que iniciaron el curso -->
                <div class="col-sm-3">
                    <strong>Número de prisioneros que iniciaron el curso:</strong>
                    <input type="number"
                           name="prisioneros_iniciaron"
                           id="prisioneros_iniciaron"
                           class="form-control"
                           min="1"
                           value=""
                           required />
                </div>

                <!-- Cursos activos — calculado automáticamente -->
                <div class="col-sm-3">
                    <strong>Número de cursos activos de LPP:</strong>
                    <input type="number"
                           name="cursos_activos"
                           id="cursos_activos"
                           class="form-control"
                           min="1"
                           value=""
                           readonly />
                </div>

            </div><!-- /.form-group -->
        </div><!-- /.seccion -->

        <!-- Botones -->
        <div class="cont-btn">
            <input type="button"
                   onclick="window.location.href='index.php?doc=consultar-reporte-lpp'"
                   class="btn btn-info"
                   value="Cancelar" />
            <input type="submit"
                   name="button"
                   value="Continuar"
                   class="btn btn-success"
                   id="btn-guardar" />
        </div>

        <input type="hidden" name="funcion" value="insertar" />

    </form>

    <?php endif; ?>
</div><!-- /.report-shell -->

<!-- ============================================================
     JAVASCRIPT
     ============================================================ -->
<script type="text/javascript">
$(document).ready(function () {

    /* ----------------------------------------------------------
       1. Cárcel — igual que el original:
          - Al cargar la página se llama recargaLista() de entrada
          - Al cambiar el select se vuelve a llamar
          - El resultado HTML se inyecta en div#ubicacion
    ---------------------------------------------------------- */
    function recargaLista() {
        $.ajax({
            type   : 'POST',
            url    : 'datos_carcel_ubicacion.php',
            data   : 'id_carcel=' + $('#rep_carcel').val(),
            success: function (r) {
                $('#ubicacion').html(r);
            }
        });
    }

    recargaLista();

    $('#rep_carcel').change(function () {
        recargaLista();
    });

    /* ----------------------------------------------------------
       2. Cursos activos — misma lógica del original:
          ceil(iniciaron / 12), mínimo 1 si iniciaron > 0
    ---------------------------------------------------------- */
    $('#prisioneros_iniciaron').on('input change', function () {
        var iniciaron = parseInt($(this).val(), 10);
        var cursos    = 0;

        if (!isNaN(iniciaron) && iniciaron > 0) {
            var resul = iniciaron / 12;
            var mod   = resul % 2;
            if (mod !== 0) {
                resul = Math.trunc(resul) + 1;
            }
            if (iniciaron <= 12) {
                resul = 1;
            }
            cursos = resul;
        }

        $('#cursos_activos').val(cursos > 0 ? cursos : '');
    });

    /* ----------------------------------------------------------
       3. Validación numérica — todos los campos número deben
          ser enteros positivos > 0 antes de enviar
    ---------------------------------------------------------- */
    $('#form1').on('submit', function (e) {

        var camposNumericos = [
            { id: 'pabellon',              label: 'N° de patios y/o pabellón' },
            { id: 'poblacion_total',       label: 'Total población' },
            { id: 'prisioneros_invitados', label: 'Prisioneros invitados' },
            { id: 'prisioneros_iniciaron', label: 'Prisioneros que iniciaron el curso' }
        ];

        for (var i = 0; i < camposNumericos.length; i++) {
            var campo = camposNumericos[i];
            var val   = parseInt($('#' + campo.id).val(), 10);
            if (isNaN(val) || val < 1) {
                e.preventDefault();
                alert('El campo "' + campo.label + '" debe ser un número mayor a 0.');
                $('#' + campo.id).focus();
                return false;
            }
        }

        var carcel = $('#rep_carcel').val();
        if (!carcel || carcel === '') {
            e.preventDefault();
            alert('Por favor seleccione una cárcel.');
            $('#rep_carcel').focus();
            return false;
        }

        if (confirm('¿Está seguro de que desea guardar este reporte?')) {
            $('#btn-guardar').prop('disabled', true);
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });

});
</script>