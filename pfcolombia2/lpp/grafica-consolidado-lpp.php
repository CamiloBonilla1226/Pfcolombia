<?php

/*******************************************
GRAFICA DE CAPACITACION
 *******************************************/

$mesesNom = array("No", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;
$PSN4 = new DBbase_Sql;
$PSN5 = new DBbase_Sql;

$sql = "SELECT idMenu ";
$sql .= " FROM usuarios_menu ";
$sql .= " WHERE idMenu = 69 
AND idUsuario = '" . $_SESSION["id"] . "'";
$PSN->query($sql);
$m_usua = $PSN->num_rows();

$sql = "SELECT idMenu ";
$sql .= " FROM usuarios_menu_graphs ";
$sql .= " WHERE idMenu = 11 
AND idUsuario = '" . $_SESSION["id"] . "'";
$PSN1->query($sql);
$m_graf = $PSN1->num_rows();
if ($m_usua == 0 && $m_graf == 0) {
  die("<h5 class='alert alert-danger text-center'>NO esta autorizado a ver esta grafica</h5>");
}

/*
*   FILTROS DE FECHAS.
*/
//$busquedaFechaIni = date("Y-m-d", strtotime("-3 months"));
$busquedaFechaIni = '2000-01-01';
if (isset($_REQUEST["fechaInicial"]) && soloNumeros($_REQUEST["fechaInicial"]) != "") {
  $busquedaFechaIni = eliminarInvalidos($_REQUEST["fechaInicial"]);
} else {
  $_REQUEST["fechaInicial"] = $busquedaFechaIni;
}
//
//
//$busquedaFechaFin = $siguiente_anho."-01-31";
$busquedaFechaFin = date("Y-m-d");
if (isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != "") {
  $busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);
} else {
  $_REQUEST["fechaFinal"] = $busquedaFechaFin;
}



/*****************************************************************************
//  EN ESTA GRAFICA NO CUENTA PERO PODRIA SERVIR PARA OTRAS
//Si es cliente o autorizado
 *****************************************************************************/
// Comentado: Filtro automático por perfil puede limitar resultados innecesariamente
// if ($_SESSION["perfil"] == 163) {
//   $_REQUEST["idUsuario"] = $_SESSION["id"];
// }




$sqlFiltros = "";
/*
*   FILTROS DE FECHAS.
*/
if (isset($_REQUEST["fechaInicial"]) && soloNumeros($_REQUEST["fechaInicial"]) != "") {
  $busquedaFechaIni = eliminarInvalidos($_REQUEST["fechaInicial"]);
}
//
if (isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != "") {
  $busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);
}

if (isset($_REQUEST["idUsuario"]) && trim($_REQUEST["idUsuario"]) != "" && soloNumeros($_REQUEST["idUsuario"]) != "") {
  $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
  $sqlFiltro .= " AND sat_reportes.idUsuario = '" . $buscar_idUsuario . "'";
}
if ($_SESSION["id_zona"] != "" && $_SESSION["id_zona"] != 0) {
  $sqlFiltro .= " AND C.idSec = '" . $_SESSION["id_zona"] . "'";
  $_REQUEST["empresa_sitio_cor"] = $_SESSION["id_zona"];
  $buscar_zona = $_SESSION["id_zona"];
}

if (isset($_REQUEST["empresa_pd"]) && trim($_REQUEST["empresa_pd"]) != "" && soloNumeros($_REQUEST["empresa_pd"]) != "") {
  $buscar_regional = soloNumeros($_REQUEST["empresa_pd"]);
  $sqlFiltro .= " AND RU.reub_reg_fk = '" . $buscar_regional . "'";
} else  if ($_SESSION["empresa_pd"] != "" && $_SESSION["empresa_pd"] != 0) {
  $buscar_regional = soloNumeros($_SESSION["empresa_pd"]);
  $sqlFiltro .= " AND RU.reub_reg_fk = '" . $_SESSION["empresa_pd"] . "'";
  $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
}

if (isset($_REQUEST["sitioReunion"]) && trim($_REQUEST["sitioReunion"]) != "" && soloNumeros($_REQUEST["sitioReunion"]) != "") {
  $buscar_prision = soloNumeros($_REQUEST["sitioReunion"]);
  $sqlFiltro .= " AND sat_reportes.sitioReunion = " . $buscar_prision . "";
}
if (isset($_REQUEST["empresa_sitio_cor"]) && trim($_REQUEST["empresa_sitio_cor"]) != "" && soloNumeros($_REQUEST["empresa_sitio_cor"]) != "") {
  $buscar_zona = soloNumeros($_REQUEST["empresa_sitio_cor"]);
  $sqlFiltro .= " AND C.idSec = '" . $buscar_zona . "'";
}
if (isset($_REQUEST["rep_qua"]) && trim($_REQUEST["rep_qua"]) != "" && soloNumeros($_REQUEST["rep_qua"]) != "") {
  $buscar_periodo = soloNumeros($_REQUEST["rep_qua"]);
  $sqlFiltro .= " AND sat_reportes.mapeo_cuarto = '" . $buscar_periodo . "'";
}

//
if (isset($_REQUEST["rep_inex"]) && eliminarInvalidos($_REQUEST["rep_inex"]) != "") {
  $tipo = eliminarInvalidos($_REQUEST["rep_inex"]);
  if ($tipo == 2) {
    $sqlFiltro .= " AND sat_reportes.sitioReunion = 0 ";
  } else {
    $sqlFiltro .= " AND sat_reportes.sitioReunion <> 0 ";
  }
} else {
  $_REQUEST["rep_inex"] = "";
}
//
if (isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != "") {
  $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
  $sqlFiltro .= " AND sat_reportes.fechaReporte >= '" . $fechaInicial . "'";
}
//
if (isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != "") {
  $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
  $sqlFiltro .= " AND sat_reportes.fechaReporte <= '" . $fechaFinal . "'";
}

if (isset($_REQUEST["empresa_paisid"]) && trim($_REQUEST["empresa_paisid"]) != "" && soloNumeros($_REQUEST["empresa_paisid"]) != "") {
  $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
  $sqlFiltro .= " AND usuario_empresa.empresa_paisid = '" . $empresa_paisid . "'";
}


/*
*	PIE - Grafica de PIE mostrando cantidad de nuevos prospectos X comercial
*/
$nombreGrafica = "CONSOLIDADO";
$datos = array();
//
$sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes ";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . "";
//

$PSN->query($sql);
//echo $sql;
$num = $PSN->num_rows();
if ($num > 0) {
  while ($PSN->next_record()) {
    //$total_poblacion = intval($PSN->f('total_poblacion'));
    $prns_invitados = intval($PSN->f('prns_invitados'));
    $prns_iniciaron = intval($PSN->f('prns_iniciaron'));
    $cursos_act = intval($PSN->f('cursos_act'));
    $prns_graduados = intval($PSN->f('prns_graduados'));
    $invt_internos = intval($PSN->f('internos'));
    $invt_externos = intval($PSN->f('externos'));
    $voluntarios = intval($PSN->f('voluntarios'));
    $discipulos = intval($PSN->f('discipulos'));
  }
} else {
  $varError = 1;
}

$sql = "SELECT count(sat_reportes.id) AS total_prisiones FROM sat_reportes";
$sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
//
// echo $sql; // Debug comentado
$PSN1->query($sql);
//echo $sql;
$total_prisiones = $PSN1->num_rows();

$sql = "SELECT sat_reportes.asistencia_total AS total_poblacion, sat_reportes.rep_entr
FROM sat_reportes ";
$sql .= "LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . " GROUP BY RU.reub_id ORDER BY sat_reportes.fechaReporte";
$PSN4->query($sql);
//echo $sql;

$num = $PSN4->num_rows();
if ($num > 0) {
  while ($PSN4->next_record()) {
    $total_poblacion += intval($PSN4->f('total_poblacion'));
  }
}

$total_recursos = 0;
$sql = "SELECT sat_reportes.rep_entr
FROM sat_reportes ";
$sql .= "LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
$sql .= " WHERE 1 AND sat_reportes.rep_tip = 307 " . $sqlFiltro . " ORDER BY sat_reportes.fechaReporte";
$PSN5->query($sql);
//echo $sql;
$num = $PSN5->num_rows();
if ($num > 0) {
  while ($PSN5->next_record()) {
    $total_recursos += intval($PSN5->f('rep_entr'));
  }
}

?>
<div class="container">
  <form action="index.php" method="get" name="form1" class="form-horizontal">
    <input type="hidden" name="doc" value="grafica-consolidado-lpp" />
    <div class="row">
      <h3 class="alert alert-info text-center">GRÁFICA DE CONSOLIDADO LPP</h3>
    </div>
    <div class="cont-tit">
      <div class="hr">
        <hr>
      </div>
      <div class="tit-cen">
        <h3>FILTRO DE BUSQUEDA</h3>
        <h5>de la gráfica</h5>
      </div>
      <div class="hr">
        <hr>
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-2">
        <strong>Zona:</strong>
        <select name="empresa_sitio_cor" id="zona" class="form-control" onchange="this.form.submit()">
          <option value="" <?php if ($empresa_pd == "") { ?> selected="selected" <?php
                                                                            } ?>>Todas la zonas</option>
          <?php
          /*
                    *   TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
                    */
          $sql = "SELECT * ";
          $sql .= " FROM categorias ";
          $sql .= " WHERE idSec = 85 ";
          if ($_SESSION["id_zona"] != "" && $_SESSION["id_zona"] != 0) {
            $sql .= " AND id = " . $_SESSION["id_zona"];
          }
          $sql .= " ORDER BY descripcion ASC ";

          $PSN3->query($sql);
          $numero = $PSN3->num_rows();
          if ($numero > 0) {
            while ($PSN3->next_record()) {
          ?><option value="<?= $PSN3->f('id'); ?>" <?php
                                                                    if ($buscar_zona == $PSN3->f('id')) { ?> selected="selected" <?php
                                                                                    }
                                                                                      ?>><?= $PSN3->f('descripcion'); ?></option><?php
                                                                    }
                                                                  } ?>
        </select>
      </div>
      <div class="col-sm-2">
        <strong>Regional:</strong>
        <select name="empresa_pd" id="regional" class="form-control" onchange="this.form.submit()">
          <?php echo ($zona == "" && $_SESSION["perfil"] != 163 && $_SESSION["perfil"] != 162) ? '<option value="" selected >Todas la regionales</option>' : "";
          $sql = "SELECT C.id, C.descripcion AS regional, CA.descripcion AS zona FROM categorias AS C";
          $sql .= " LEFT JOIN categorias AS CA ON CA.id = C.idSec
                    WHERE CA.idSec = 85 ";
          if (!empty($buscar_zona)) {
            $sql .= " AND CA.id = " . $buscar_zona;
          }
          if ($_SESSION["perfil"] != 167) {
            if ($_SESSION["empresa_pd"] != "" && $_SESSION["empresa_pd"] != 0) {
              $sql .= " AND C.id = " . $_SESSION["empresa_pd"];
            }
          }

          $PSN2->query($sql);
          // echo $sql; // Debug comentado
          $numero = $PSN2->num_rows();
          if ($numero > 0) {
            while ($PSN2->next_record()) { ?>
              <option value="<?= $PSN2->f('id'); ?>" <?php
                                                    if ($buscar_regional == $PSN2->f('id')) {
                                                    ?>selected="selected" <?php
                                                    }
                                                      ?>><?= $PSN2->f('regional'); ?></option><?php
                                                                  }
                                                                }
                                                                    ?>
        </select>
      </div>
      <div class="col-sm-2">
        <strong>Miembro de la regional:</strong><?php
                                                ?><select name="idUsuario" onchange="this.form.submit()" class="form-control">
          <?php
          if ($_SESSION["perfil"] != 163) {
          ?><option value="">Ver todos</option><?php
                                                    }

                                                    /*
            *   TRAEMOS LOS USUARIOS
            */
                                                    $sql = "SELECT * ";
                                                    $sql .= " FROM usuario AS U
            LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id";
                                                    $sql .= " WHERE U.tipo IN (162, 163, 167) ";
                                                    if ($_SESSION["perfil"] == 163) {
                                                      $sql .= " AND U.id = '" . $_SESSION["id"] . "'";
                                                    }
                                                    if (!empty($buscar_regional)) {
                                                      $sql .= " AND UE.empresa_pd = " . $buscar_regional . " ";
                                                    }
                                                    $sql .= " ORDER BY U.nombre asc";

                                                    $PSN2->query($sql);
                                                    $numero_coo = $PSN2->num_rows();
                                                    if ($numero_coo > 0) {
                                                      while ($PSN2->next_record()) {
                                                      ?><option value="<?= $PSN2->f('id'); ?>" <?php
                                                            if ($buscar_idUsuario == $PSN2->f('id')) {
                                                            ?>selected="selected" <?php
                                                            }
                                              ?>><?= $PSN2->f('nombre'); ?></option><?php
                                                        }
                                                      }
                                                          ?></select>
      </div>
      <div class="col-sm-3">
        <strong>Prisión:</strong>
        <select name="sitioReunion" id="rep_carcel" class="form-control" onchange="this.form.submit()">
          <?php
          /*
                    *   TRAEMOS LOS TIPOS DE SERVICIOS QUE PRESTA (25)
                    */
          if ($_SESSION['empresa_pd'] != "") {
            echo '<option value="">Sin especificar</option>';
            $sql = "SELECT * ";
            $sql .= " FROM tbl_regional_ubicacion ";
            if (!empty($buscar_regional)) {
              $sql .= " WHERE reub_reg_fk = " . $buscar_regional;
            }
            $sql .= " ORDER BY reub_reg_fk asc";

            $PSN2->query($sql);
            $numero_pri = $PSN2->num_rows();
            if ($numero_pri > 0) {
              while ($PSN2->next_record()) {
          ?><option value="<?= $PSN2->f('reub_id'); ?>" <?php
                                                                              if ($buscar_prision == $PSN2->f('reub_id')) {
                                                                              ?>selected="selected" <?php
                                                                              }
                                                          ?>><?= $PSN2->f('reub_nom'); ?></option><?php
                                                                      }
                                                                    }
                                                                  } else {
                                                                    echo '<option value="">Sin regional asignada</option>';
                                                                  }
                                                                        ?>
        </select>
      </div>
      <div class="col-sm-2">
        <strong>Período:</strong>
        <select name="rep_qua" onchange="this.form.submit()" class="form-control">
          <option value="">Sin especificar</option>
          <option value="1" <?php echo ($_REQUEST['rep_qua'] == 1) ? 'selected' : ''; ?>>Q1 (Ene - Mar)</option>
          <option value="4" <?php echo ($_REQUEST['rep_qua'] == 4) ? 'selected' : ''; ?>>Q2 (Abr - Jun)</option>
          <option value="7" <?php echo ($_REQUEST['rep_qua'] == 7) ? 'selected' : ''; ?>>Q3 (Jul - Sep)</option>
          <option value="10" <?php echo ($_REQUEST['rep_qua'] == 10) ? 'selected' : ''; ?>>Q4 (Oct - Dic)</option>
        </select>
      </div>
      <div class="col-sm-2">
        <strong>Fecha Inicial:</strong>
        <input type="date" name="fechaInicial" id="fechaInicial" value="<?= $fechaInicial; ?>" class="form-control" />
      </div>
      <div class="col-sm-2">
        <strong>Fecha Final:</strong>
        <input type="date" name="fechaFinal" id="fechaFinal" value="<?= $fechaFinal; ?>" class="form-control" />
      </div>
      <div class="col-sm-2"><br>
        <input type="submit" value="Buscar" class="btn btn-success" style="float:center" />
      </div>
    </div>
  </form>
  <div class="cont-tit">
    <div class="hr">
      <hr>
    </div>
    <div class="tit-cen">
      <h3 class="text-center">RESULTADOS DEL FILTRO</h3>
      <h5>Gráficas de consolidado</h5>
    </div>
    <div class="hr">
      <hr>
    </div>
  </div>
  <?php
  if ($varError == 1) {
  ?><div class="container">
      <div class="row">
        <h5 class="alert alert-warning text-center">No se ha encontrado ningun registro para el rango de fechas seleccionado.</h5>
      </div>
    </div><?php
        }

        if ($varError != 1) { ?>
    <div class="row">
      <script type="text/javascript">
        google.charts.load("current", {
          packages: ["corechart"]
        });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
          var data = google.visualization.arrayToDataTable([
            ["Element", "Density", {
              role: "style"
            }],
            ["Total de la población de la prisión", <?= $total_poblacion; ?>, "#2E86C1"],
            ["N° Prisioneros invitados", <?= $prns_invitados; ?>, "#239B56"],
            ["N° Prisioneros que iniciaron el curso", <?= $prns_iniciaron; ?>, "#F39C12"],
            ["N° de cursos", <?= $cursos_act; ?>, "#F1C40F"],
            ["N° de graduados", <?= $prns_graduados; ?>, "#C0392B"],
            ["N° de voluntarios que atendieron el curso", <?= $voluntarios; ?>, "#E74C3C"],
            ["N° de discípulos que pasan a C&M", <?= $discipulos; ?>, "#8E44AD"]
          ]);

          var view = new google.visualization.DataView(data);
          view.setColumns([0, 1,
            {
              calc: "stringify",
              sourceColumn: 1,
              type: "string",
              role: "annotation"
            },
            2
          ]);

          var options = {

            bar: {
              groupWidth: "95%"
            },
            legend: {
              position: "none"
            },
          };
          var chart = new google.visualization.BarChart(document.getElementById("barchart_values"));
          chart.draw(view, options);
        }
      </script>
      <div class="contenedor-flex content-grafic fl-sard">
        <div class="cont-resu fl-sard" id="lpp">
          <div class="resu-item bck-col-1">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Total</h3>
                <p>de la población de la prisión</p>
              </div>
              <div class="item-num">
                <span><?= $total_poblacion; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item bck-col-2">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Número</h3>
                <p>de prisioneros invitados</p>
              </div>
              <div class="item-num">
                <span><?= $prns_invitados; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item bck-col-3">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Número</h3>
                <p>de prisioneros que iniciaron el curso</p>
              </div>
              <div class="item-num">
                <span><?= $prns_iniciaron; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item bck-col-6">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Número</h3>
                <p>de cursos</p>
              </div>
              <div class="item-num">
                <span><?= $cursos_act; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item bck-col-7">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Número</h3>
                <p>de graduados</p>
              </div>
              <div class="item-num">
                <span><?= $prns_graduados; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item bck-col-4">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Número</h3>
                <p>de voluntarios que atendieron el curso</p>
              </div>
              <div class="item-num">
                <span><?= $voluntarios; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item bck-col-5">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Número</h3>
                <p>de discípulos que pasan a C&M</p>
              </div>
              <div class="item-num">
                <span><?= $discipulos; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item bck-col-8">
            <div class="item-ico">
              <i class="fas fa-users"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Total</h3>
                <p>de prisiones alcanzadas</p>
              </div>
              <div class="item-num">
                <span><?= $total_prisiones; ?></span>
              </div>
            </div>
          </div>
          <div class="resu-item tot-recu">
            <div class="item-ico">
              <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="item-con">
              <div class="item-text">
                <h3>Costos</h3>
                <p>de recursos</p>
              </div>
              <div class="item-num">
                <span>$ <?php echo number_format($total_recursos); ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="barchart_values" style="width: 100%; height: 500px;"></div>
      <center><a href="generaExcel-graduados.php?rep_tip=307&idUsuario=<?= $_REQUEST["idUsuario"]; ?>&empresa_paisid=<?= $_REQUEST["empresa_paisid"]; ?>&sitioReunion=<?= $_REQUEST["sitioReunion"]; ?>&empresa_pd=<?= $_REQUEST["empresa_pd"]; ?>&rep_qua=<?= $_REQUEST['rep_qua']; ?>&rep_ani=<?= $_REQUEST['rep_ani']  ?>&fechaInicial=<?= $_REQUEST['fechaInicial']  ?>&fechaFinal=<?= $_REQUEST['fechaFinal']  ?>&rep_inex=<?= $_REQUEST['rep_inex']  ?>" target="_blank" class="btn btn-info"><span class="glyphicon glyphicon-cloud-download"></span> DESCARGAR EXCEL</a></center>
    </div>
  <?php } ?>
</div>
