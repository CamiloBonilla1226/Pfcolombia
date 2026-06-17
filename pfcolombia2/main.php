<?php
function obtenerPorcentaje($cantidad, $total)
{
  $porcentaje = ((float)$cantidad * 100) / $total; // Regla de tres
  $porcentaje = round($porcentaje, 2);  // Quitar los decimales
  return $porcentaje;
}
$sqlUser = "";
if ($_SESSION["perfil"] == 163) {
  $buscar_idUsuario = soloNumeros($_SESSION["id"]);
  $sqlUser .= "sat_reportes.idUsuario = '" . $buscar_idUsuario . "' AND ";
}
if ($_SESSION["perfil"] == 167) {
  if ($_SESSION["id_zona"] != "" && $_SESSION["id_zona"] != 0) {
    $sqlFiltro .= " AND C.idSec = '" . $_SESSION["id_zona"] . "'";
    $_REQUEST["empresa_sitio_cor"] = $_SESSION["id_zona"];
    $buscar_zona = $_SESSION["id_zona"];
  }
}
if ($_SESSION["perfil"] == 162) {
  if ($_SESSION["empresa_pd"] != "" && $_SESSION["empresa_pd"] != 0) {
    $sqlFiltro .= " AND UE.empresa_pd = '" . $_SESSION["empresa_pd"] . "'";
    $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
  }
}
$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN4 = new DBbase_Sql;
if ($_SESSION["id"] == "") {
  $_SESSION["id"] = 0;
}
$nombreGrafica = "CONSOLIDADOS CONFRATERNIDAD CARCELARIA DE COLOMBIA";
?>
<style>
  /* Evita que números grandes se recorten en las tarjetas de resumen (resu-item) */
  .resu-item .item-con {
    overflow: visible;
    min-width: 0;
  }
  .resu-item .item-num,
  .resu-item .item-num span {
    overflow: visible !important;
    text-overflow: unset !important;
    white-space: nowrap !important;
    width: auto !important;
    max-width: none !important;
  }
</style>
<div class="container-fluid" style="display: flex; flex-wrap: wrap;">
  <div class="jumbotron">
    <div class="container-fluid cont-info ">
      <?php if ($_SESSION["youtube"] != "") { ?>
        <div class="col-sm-3 item-grf">
          <iframe width="100%" height="200" src="https://www.youtube.com/embed/<?= $_SESSION["youtube"]; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
      <?php
        $ancho = 9;
      } else {
        $ancho = 8; ?>
        <div class="col-sm-4 item-grf">
          <img src="images/titulo.png" class="img-responsive" />
        </div>
      <?php } ?>
      <div class="col-sm-<?php echo $ancho; ?> item-tex">
        <strong style="font-size: 24px;">Bienvenid@ <?= $_SESSION["nombre"]; ?> al</strong>
        <h2 style="margin-top: -5px;font-size: 28px; "><?= $gloPrograma; ?> de la</h2>
        <h1 style="margin-top: -14px;font-size: 32px;"><?= $_SESSION["empresa_socio"]; ?>.</h1>
        <p style="margin: 0px">Desde aquí usted podrá contar con toda la información a tan solo un clic de distancia.</p>
        <ul class="social">
          <li class="social-item"><a href="https://www.pfcolombia.org/" target="_blank"><i class="fas fa-globe-americas"></i></a></li>
          <li class="social-item"><a href="https://www.facebook.com/pfcolombia.org/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
          <li class="social-item"><a href="https://www.instagram.com/accounts/login/?next=/pfcolombia/" target="_blank"><i class="fab fa-instagram"></i></a></li>
          <li class="social-item"><a href="https://www.youtube.com/channel/UCXBvL-G9chy6qJ9hcxBgv0Q/featured" target="_blank"><i class="fab fa-youtube"></i></a></li>
          <li class="social-item"><a href="https://twitter.com/pfcolombia" target="_blank"><i class="fab fa-twitter"></i></a></li>
        </ul>
      </div>
    </div>
  </div>
  <div style="width: 100%;">
    <br>
    <div class="t-container">
      <ul class="t-tabs">
        <li class="t-tab">Evangelistas</li>
        <li class="t-tab">LPP</li>
        <li class="t-tab">C&M</li>
        <li class="t-tab">Proyecto Felipe</li>
        <li class="t-tab">Instituto Biblico</li>
        <li class="t-tab">Resumen Anual</li>
      </ul>
      <ul class="t-contents">
        <li class="t-content" id="tab_eva">
          <div class="cont-tit">
            <div class="hr">
              <hr>
            </div>
            <div class="tit-cen">
              <h3>GRÁFICA DE CONSOLIDADO</h3>
              <h5>EVANGELISTAS</h5>
            </div>
            <div class="hr">
              <hr>
            </div>
          </div>
          <?php
          $rep_prom = "SUM";
          $sql = "SELECT     
                        " . $rep_prom . "(asistencia_total) as prisiones_atendidas,
                        " . $rep_prom . "(asistencia_hom) as grupos_intramuros,
                        " . $rep_prom . "(asistencia_muj) as grupos_extramuros,
                        " . $rep_prom . "(asistencia_jov) as total_creyente,
                        " . $rep_prom . "(asistencia_nin) as total_discipulos,
                        " . $rep_prom . "(bautizados) as bautizados,
                        " . $rep_prom . "(discipulado) as discipulado,
                        " . $rep_prom . "(desiciones) as decisiones,
                        " . $rep_prom . "(preparandose) as preparandose,
                        COUNT(sat_reportes.id) AS tot_registros,
                        SUM(mapeo_oracion) AS act_oracion,
                        SUM(mapeo_companerismo) AS act_companerismo,
                        SUM(mapeo_adoracion) AS act_adoracion,
                        SUM(mapeo_biblia) AS act_biblia,
                        SUM(mapeo_evangelizar) AS act_evangelizar,
                        SUM(mapeo_cena) AS act_cena,
                        SUM(mapeo_dar) AS act_dar,
                        SUM(mapeo_bautizar) AS act_bautizar,
                        SUM(mapeo_trabajadores) AS act_trabajadores
                        ";
          $sql .= " FROM sat_reportes ";
          $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario  LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id LEFT JOIN categorias AS C ON C.id = UE.empresa_pd LEFT JOIN categorias AS CA ON CA.id = C.idSec";
          $sql .= " WHERE " . $sqlUser . " 1 " . $sqlFiltro . " AND sat_reportes.rep_tip = 318 ";
          //echo $sql;;
          $PSN->query($sql);
          $num = $PSN->num_rows();
          if ($num > 0) {
            while ($PSN->next_record()) {
              $prisiones_atendidas = round($PSN->f('prisiones_atendidas'));
              $grupos_intramuros = round($PSN->f('grupos_intramuros'));
              $grupos_extramuros = round($PSN->f('grupos_extramuros'));
              $total_creyente = round($PSN->f('total_creyente'));
              $total_discipulos = round($PSN->f('total_discipulos'));
              $bautizados = round($PSN->f('bautizados'));
              $discipulado = round($PSN->f('discipulado'));
              $decisiones = round($PSN->f('decisiones'));
              $preparandose = round($PSN->f('preparandose'));
              $tot_registros = round($PSN->f('tot_registros'));
              $act_oracion = round($PSN->f('act_oracion'));
              $act_companerismo = round($PSN->f('act_companerismo'));
              $act_adoracion = round($PSN->f('act_adoracion'));
              $act_biblia = round($PSN->f('act_biblia'));
              $act_evangelizar = round($PSN->f('act_evangelizar'));
              $act_cena = round($PSN->f('act_cena'));
              $act_dar = round($PSN->f('act_dar'));
              $act_bautizar = round($PSN->f('act_bautizar'));
              $act_trabajadores = round($PSN->f('act_trabajadores'));
            }
          } else {
            $varError = 1;
          } ?>
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
                ["Número de cárceles atendidas", <?= $prisiones_atendidas; ?>, "#2E86C1"],
                ["Número de personas Intramuros atendidas", <?= $grupos_intramuros ?>, "#239B56"],
                ["Número de personas extramuros atendidas", <?= $grupos_extramuros; ?>, "#F39C12"],
                ["Creyentes Asistentes", <?= $total_creyente; ?>, "#E74C3C"],
                ["Total de discípulos (LPP) que pasan a C&M", <?= $total_discipulos; ?>, "#8E44AD"],
                ["Número de bautizados", <?= $bautizados; ?>, "#F1C40F"],
                ["Número de voluntarios internos", <?= $discipulado; ?>, "#C0392B"],
                ["Número de voluntarios externos", <?= $desiciones; ?>, "#C02B97"],
                ["Número de pospenados que está acompañando", <?= $preparandose; ?>, "#6244AD"]
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
              var chart = new google.visualization.BarChart(document.getElementById("evangelistas_grafica"));
              chart.draw(view, options);
            }
          </script>
          <div class="contenedor-flex content-grafic cont-just-sbet" id="eva">
            <div class="cont-resu">
              <div class="resu-item bck-col-1">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Número</h3>
                    <p>de cárceles atendidas</p>
                  </div>
                  <div class="item-num">
                    <span><?= $prisiones_atendidas; ?></span>
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
                    <p>de personas intramuros atendidas</p>
                  </div>
                  <div class="item-num">
                    <span><?= $grupos_intramuros; ?></span>
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
                    <p>de personas extramuros atendidas</p>
                  </div>
                  <div class="item-num">
                    <span><?= $grupos_extramuros; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-4">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de creyentes que asistieron</p>
                  </div>
                  <div class="item-num">
                    <span><?= $total_creyente; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-5">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de discípulos (LPP) que pasan a C&M</p>
                  </div>
                  <div class="item-num">
                    <span><?= $total_discipulos; ?></span>
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
                    <p>de bautizados</p>
                  </div>
                  <div class="item-num">
                    <span><?= $bautizados; ?></span>
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
                    <p>de voluntarios internos</p>
                  </div>
                  <div class="item-num">
                    <span><?= $discipulado; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-8">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Número</h3>
                    <p>de voluntarios externos</p>
                  </div>
                  <div class="item-num">
                    <span><?= $decisiones; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-9">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Número</h3>
                    <p>de pospenados que está acompañando</p>
                  </div>
                  <div class="item-num">
                    <span><?= $preparandose; ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="evangelistas_grafica" style="width: 100%; height: 500px;"></div>
          <div class="cont-resu">
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_oracion2.png" alt=""></div>
              <div class="act-tex">
                <h4>Orar:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_oracion . "</b> Sí - <b>" . ($tot_registros - $act_oracion) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_companerismo2.png" alt=""></div>
              <div class="act-tex">
                <h4>Compañerismo:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_companerismo . "</b> Sí - <b>" . ($tot_registros - $act_companerismo) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_adoracion2.png" alt=""></div>
              <div class="act-tex">
                <h4>Adoración:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_adoracion . "</b> Sí - <b>" . ($tot_registros - $act_adoracion) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_biblia2.png" alt=""></div>
              <div class="act-tex">
                <h4> Aplicar la biblia:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_biblia . "</b> Sí - <b>" . ($tot_registros - $act_biblia) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_evangelizar2.png" alt=""></div>
              <div class="act-tex">
                <h4>Evangelizar:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_evangelizar . "</b> Sí - <b>" . ($tot_registros - $act_evangelizar) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_cena2.png" alt=""></div>
              <div class="act-tex">
                <h4>Cena del Señor:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_cena . "</b> Sí - <b>" . ($tot_registros - $act_cena) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_dar2.png" alt=""></div>
              <div class="act-tex">
                <h4>Dar:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_dar . "</b> Sí - <b>" . ($tot_registros - $act_dar) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_bautizar2.png" alt=""></div>
              <div class="act-tex">
                <h4>Bautizar:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_bautizar . "</b> Sí - <b>" . ($tot_registros - $act_bautizar) . "</b> No la realizan"; ?>
              </div>
            </div>
            <div class="act-item">
              <div class="act-img"><img src="mapeo_img/mapeo_trabajadores2.png" alt=""></div>
              <div class="act-tex">
                <h4>Entrenar nuevos lideres:</h4>
                <?php echo $tot_registros . " Registros: <b>" . $act_trabajadores . "</b> Sí - <b>" . ($tot_registros - $act_trabajadores) . "</b> No la realizan"; ?>
              </div>
            </div>
          </div>
        </li>
        <li class="t-content" id="tab_lpp">
          <div class="cont-tit">
            <div class="hr">
              <hr>
            </div>
            <div class="tit-cen">
              <h3>GRÁFICA DE CONSOLIDADO</h3>
              <h5>LA PEREGRINACIÓN DEL PRISIONERO (LPP)</h5>
            </div>
            <div class="hr">
              <hr>
            </div>
          </div>
          <?php
          $datos = array();
          //
          $sqlUserLPP = "";
          if ($_SESSION["perfil"] == 163) {
            $sqlUserLPP = "RL.usuario_id = '" . $buscar_idUsuario . "' AND ";
          }
          $sql = "SELECT SUM(RL.poblacion_total) AS total_poblacion, SUM(RL.prisioneros_invitados) AS prns_invitados, SUM(RL.prisioneros_iniciaron) AS prns_iniciaron, SUM(RL.cursos_activos) AS cursos_act, SUM(RL.total_graduados) AS prns_graduados, SUM(RL.total_voluntarios_internos) AS internos, SUM(RL.total_voluntarios_externos) AS externos, SUM(RL.total_voluntarios_internos + RL.total_voluntarios_externos) AS voluntarios, SUM(RL.discipulos_pasaron_cm) AS discipulos FROM reporte_lpp AS RL";
          $sql .= " LEFT JOIN usuario AS U ON U.id = RL.usuario_id
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = RL.carcel_id
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = RL.usuario_id
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
          $sql .= " WHERE " . $sqlUserLPP . " 1=1 " . $sqlFiltro . "";
          //
          $datosArr[] = '["Tipo", "Cantidad"]';
          $datosArr2[] = '["Tipo", "Cantidad"]';
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
          $sql = "SELECT count(RL.id_lpp) AS total_prisiones FROM reporte_lpp AS RL";
          $sql .= " LEFT JOIN usuario AS U ON U.id = RL.usuario_id
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = RL.carcel_id
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = RL.usuario_id
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
          $sql .= " WHERE " . $sqlUserLPP . " 1=1 " . $sqlFiltro . " GROUP BY RL.carcel_id";

         //

          $PSN->query($sql);
          $total_prisiones = $PSN->num_rows();

          $sql = "SELECT RL.poblacion_total AS total_poblacion
                            FROM reporte_lpp AS RL ";
          $sql .= "LEFT JOIN usuario AS U ON U.id = RL.usuario_id LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = RL.carcel_id LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = RL.usuario_id LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
          $sql .= " WHERE 1=1 " . $sqlFiltro . " GROUP BY RU.reub_id ORDER BY RL.fecha_reporte";
          $result = $PSN4->query($sql);

          //if ($result && $result->num_rows > 0) {
          //  while ($row = $result->fetch_assoc()) {
          //    $total_poblacion += intval($row['total_poblacion']);
          //  }
          //} else {
          //  echo "No se encontraron resultados.";
          //}

          $result = $PSN4->query($sql);
          //echo $sql;
          $num = $PSN4->num_rows();
          //$total_poblacion = 0;
          if ($num > 0) {
            while ($PSN4->next_record()) {
              $total_poblacion += intval($PSN4->f('total_poblacion'));
            }
          }

          if ($varError != 1) { ?>
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
                var chart = new google.visualization.BarChart(document.getElementById("lpp_grafica"));
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
              </div>
            </div>
            <div id="lpp_grafica" style="width: 100%; height: 500px;"></div>

          <?php } ?>
        </li>
        <li class="t-content" id="tab_ecc">
          <div class="cont-tit">
            <div class="hr">
              <hr>
            </div>
            <div class="tit-cen">
              <h3>GRÁFICA DE CONSOLIDADO</h3>
              <h5>CAPACITAR Y MULTIPLICAR (C&M)</h5>
            </div>
            <div class="hr">
              <hr>
            </div>
          </div>
          <?php
          $sqlUserCM = "";
          if ($_SESSION["perfil"] == 163) {
            $sqlUserCM = "RC.usuario_id = '" . $buscar_idUsuario . "' AND ";
          }
          $sql = "SELECT
                        SUM(RC.asistencia_total) as asistencia_total,
                        SUM(RC.en_discipulado) as discipulado,
                        SUM(RC.decisiones_cristo) as decisiones,
                        SUM(RC.bautizados_periodo) as bautizos,
                        SUM(RC.graduados_periodo) as graduados,
                        COUNT(RC.id_cm) as total_grupos";
          $sql .= " FROM reporte_cm AS RC ";
          $sql .= " LEFT JOIN usuario AS U ON U.id = RC.usuario_id
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = RC.carcel_id
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = RC.usuario_id
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
          $sql .= " WHERE " . $sqlUserCM . " 1=1 " . $sqlFiltro . "";
          $PSN->query($sql);
          $num = $PSN->num_rows();
          if ($num > 0) {
            while ($PSN->next_record()) {
              $satura_asistencia_total = $PSN->f('asistencia_total');
              $satura_discipulado = $PSN->f('discipulado');
              $satura_decisiones = $PSN->f('decisiones');
              $satura_bautizos = $PSN->f('bautizos');
              $satura_graduados = $PSN->f('graduados');
              $satura_total_grupos = $PSN->f('total_grupos');
            }
          }
          ?>
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
                ["Total de asistencia en personas", <?= $satura_asistencia_total; ?>, "#2E86C1"],
                ["Total de discipulados", <?= $satura_discipulado ?>, "#239B56"],
                ["Total de decisiones", <?= $satura_decisiones; ?>, "#F39C12"],
                ["Total de bautizados", <?= $satura_bautizos; ?>, "#E74C3C"],
                ["Total de graduados", <?= $satura_graduados; ?>, "#E74C3C"],
                ["Total de personas", <?= $satura_total_grupos; ?>, "#8E44AD"]
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
              var chart = new google.visualization.BarChart(document.getElementById("grafica_ecc"));
              chart.draw(view, options);
            }
          </script>
          <div class="contenedor-flex content-grafic cont-just-sbet">
            <div class="cont-resu">
              <div class="resu-item bck-col-1">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de asistencia en personas</p>
                  </div>
                  <div class="item-num">
                    <span><?= $satura_asistencia_total; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-2">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de discipulados</p>
                  </div>
                  <div class="item-num">
                    <span><?= $satura_discipulado; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-3">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de Decisiones</p>
                  </div>
                  <div class="item-num">
                    <span><?= $satura_decisiones; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-4">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de bautizados</p>
                  </div>
                  <div class="item-num">
                    <span><?= $satura_bautizos; ?></span>
                  </div>
                </div>
              </div>


              <div class="resu-item bck-col-4">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de graduados</p>
                  </div>
                  <div class="item-num">
                    <span><?= $satura_graduados; ?></span>
                  </div>
                </div>
              </div>


              <div class="resu-item bck-col-5">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de personas</p>
                  </div>
                  <div class="item-num">
                    <span><?= $satura_total_grupos; ?></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div id="grafica_ecc" style="width: 100%; height: 500px;"></div>
        </li>
        <!-- Grafica Proyecto Felipe -->
        <li class="t-content" id="tab_prf">
          <div class="cont-tit">
            <div class="hr">
              <hr>
            </div>
            <div class="tit-cen">
              <h3>GRÁFICA DE CONSOLIDADO</h3>
              <h5>PROYECTO FELIPE</h5>
            </div>
            <div class="hr">
              <hr>
            </div>
          </div>
          <?php
          $sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes";
          $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
          $sql .= " WHERE 1 AND sat_reportes.rep_tip = 319 " . $sqlFiltro . "";
          //echo $sql;
          $PSN1->query($sql);
          $num = $PSN1->num_rows();
          if ($num > 0) {
            while ($PSN1->next_record()) {
              $total_poblacion = intval($PSN1->f('total_poblacion'));
              $prns_invitados = intval($PSN1->f('prns_invitados'));
              $prns_iniciaron = intval($PSN1->f('prns_iniciaron'));
              $cursos_act = intval($PSN1->f('cursos_act'));
              $prns_graduados = intval($PSN1->f('prns_graduados'));
              $invt_internos = intval($PSN1->f('internos'));
              $invt_externos = intval($PSN1->f('externos'));
              $voluntarios = intval($PSN1->f('voluntarios'));
              $discipulos = intval($PSN1->f('discipulos'));
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
          $sql .= " WHERE 1 AND sat_reportes.rep_tip = 319 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
          //

          $PSN1->query($sql);
          //echo $sql;

          $total_prisiones = $PSN1->num_rows();


          $total_nivel = array();
          for ($i = 320; $i < 323; $i++) {
            $sql = "SELECT COUNT(AD.adj_can) nivel FROM tbl_adjuntos AS AD 
                        LEFT JOIN sat_reportes ON AD.adj_rep_fk = sat_reportes.id
                        LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec";
            $sql .= " WHERE AD.adj_can = '" . $i . "' AND sat_reportes.rep_tip = 319 " . $sqlFiltro . " ORDER BY sat_reportes.fechaReporte";
            $PSN4->query($sql);
            $num = $PSN4->num_rows();
            if ($num > 0) {
              while ($PSN4->next_record()) {
                $total_nivel[$i] = $PSN4->f('nivel');
              }
            }
          }
          ?>
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
                ["Número de prisioneros invitados al curso:", <?= $total_poblacion; ?>, "#239B56"],
                ["Número de prisioneros inscritos en el curso:", <?= $prns_invitados; ?>, "#F39C12"],
                ["Número de prisioneros que iniciaron el curso:", <?= $prns_iniciaron; ?>, "#F1C40F"],
                ["Número total de graduados:", <?= $prns_graduados; ?>, "#C0392B"],
                ["Total de cursos completados:", <?= $cursos_act; ?>, "#E74C3C"],
                ["Total de voluntarios internos:", <?= $invt_internos; ?>, "#8E44AD"],
                ["Total de voluntarios externos:", <?= $invt_externos; ?>, "#6244AD"]
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
            <div class="cont-resu fl-sard" id="PF">
              <div class="resu-item bck-col-1">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Número</h3>
                    <p>de prisiones</p>
                  </div>
                  <div class="item-num">
                    <span><?= $total_prisiones; ?></span>
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
                    <p>de prisioneros invitados al curso</p>
                  </div>
                  <div class="item-num">
                    <span><?= $total_poblacion; ?></span>
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
                    <p>de prisioneros inscritos en el curso</p>
                  </div>
                  <div class="item-num">
                    <span><?= $prns_invitados; ?></span>
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
                    <p>de prisioneros que iniciaron el curso</p>
                  </div>
                  <div class="item-num">
                    <span><?= $prns_iniciaron; ?></span>
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
                    <p>total de graduados</p>
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
                    <h3>Total</h3>
                    <p>de cursos completados de proyecto Felipe</p>
                  </div>
                  <div class="item-num">
                    <span><?= $cursos_act; ?></span>
                  </div>
                </div>
              </div>
              <div class="resu-item bck-col-5">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Total</h3>
                    <p>de voluntarios internos</p>
                  </div>
                  <div class="item-num">
                    <span><?= $invt_internos; ?></span>
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
                    <p>de voluntarios externos</p>
                  </div>
                  <div class="item-num">
                    <span><?= $invt_externos; ?></span>
                  </div>
                </div>
              </div>
            </div>

          </div>
          <div id="barchart_values" style="width: 100%; height: 500px;"></div>
          <div class="cont-resu fl-sard" id="lpp">
            <div class="resu-item bck-col-bronce">
              <div class="item-ico">
                <i class="fas fa-trophy"></i>
              </div>
              <div class="item-con">
                <div class="item-text">
                  <h3>Bronce</h3>
                  <p>Número de prisioneros graduados</p>
                </div>
                <div class="item-num">
                  <span><?= $total_nivel[322]; ?></span>
                </div>
              </div>
            </div>
            <div class="resu-item bck-col-plata">
              <div class="item-ico">
                <i class="fas fa-trophy"></i>
              </div>
              <div class="item-con">
                <div class="item-text">
                  <h3>Plata</h3>
                  <p>Número de prisioneros graduados</p>
                </div>
                <div class="item-num">
                  <span><?= $total_nivel[321]; ?></span>
                </div>
              </div>
            </div>
            <div class="resu-item bck-col-oro">
              <div class="item-ico">
                <i class="fas fa-trophy"></i>
              </div>
              <div class="item-con">
                <div class="item-text">
                  <h3>Oro</h3>
                  <p>Número de prisioneros graduados</p>
                </div>
                <div class="item-num">
                  <span><?= $total_nivel[320]; ?></span>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="t-content" id="tab_ib">
          <div class="cont-tit">
            <div class="hr">
              <hr>
            </div>
            <div class="tit-cen">
              <h3>GRÁFICA DE CONSOLIDADO</h3>
              <h5>INSTITUTO BIBLICO</h5>
            </div>
            <div class="hr">
              <hr>
            </div>
          </div>
          <?php
          $sql = "SELECT SUM(sat_reportes.asistencia_total) AS total_poblacion,SUM(sat_reportes.asistencia_hom) AS prns_invitados, SUM(sat_reportes.asistencia_muj) AS prns_iniciaron, SUM(sat_reportes.asistencia_jov) AS cursos_act, SUM(sat_reportes.asistencia_nin) AS prns_graduados, SUM(sat_reportes.bautizados) AS internos, SUM(sat_reportes.desiciones) AS externos, SUM(sat_reportes.bautizados + sat_reportes.desiciones) AS voluntarios, SUM(sat_reportes.rep_ndis) AS discipulos FROM sat_reportes";
          $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
                        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
                        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
                        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
          $sql .= " WHERE 1 AND sat_reportes.rep_tip = 317 " . $sqlFiltro . "";
          //

          $PSN->query($sql);
          //echo $sql;
          $num = $PSN->num_rows();
          if ($num > 0) {
            while ($PSN->next_record()) {
              $total_poblacion = intval($PSN->f('total_poblacion'));
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
          $sql .= " WHERE 1 AND sat_reportes.rep_tip = 317 " . $sqlFiltro . " GROUP BY sat_reportes.sitioReunion";
          $PSN1->query($sql);
          //echo $sql;

          $total_prisiones = $PSN1->num_rows();
          ?>
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
                /*["Total de la población de la prisión", <?= $total_prisiones; ?>, "#2E86C1"],*/
                ["Número de prisioneros invitados al diplomado", <?= $total_poblacion; ?>, "#239B56"],
                ["Número de prisioneros inscritos en el diplomado", <?= $prns_invitados; ?>, "#F39C12"],
                ["Número de prisioneros que iniciaron el diplomado", <?= $prns_iniciaron; ?>, "#F1C40F"],
                ["Número de prisioneros graduados", <?= $prns_graduados; ?>, "#C0392B"]
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
              var chart = new google.visualization.BarChart(document.getElementById("barchart_values2"));
              chart.draw(view, options);
            }
          </script>
          <div class="contenedor-flex content-grafic fl-sard">
            <div class="cont-resu fl-sard" id="PF">
              <div class="resu-item bck-col-1">
                <div class="item-ico">
                  <i class="fas fa-users"></i>
                </div>
                <div class="item-con">
                  <div class="item-text">
                    <h3>Número</h3>
                    <p>de prisiones</p>
                  </div>
                  <div class="item-num">
                    <span><?= $total_prisiones; ?></span>
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
                    <p>de prisioneros invitados al diplomado</p>
                  </div>
                  <div class="item-num">
                    <span><?= $total_poblacion; ?></span>
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
                    <p>de prisioneros inscritos en el diplomado</p>
                  </div>
                  <div class="item-num">
                    <span><?= $prns_invitados; ?></span>
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
                    <p>de prisioneros que iniciaron el diplomado</p>
                  </div>
                  <div class="item-num">
                    <span><?= $prns_iniciaron; ?></span>
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
                    <p>de prisioneros graduados</p>
                  </div>
                  <div class="item-num">
                    <span><?= $prns_graduados; ?></span>
                  </div>
                </div>
              </div>

            </div>
          </div>
          <div id="barchart_values2" style="width: 100%; height: 500px;"></div>
        </li>
        <li class="t-content" id="tab_ib">
          <div class="cont-tit">
            <div class="hr">
              <hr>
            </div>
            <div class="tit-cen">
              <h3>Resumen Anual</h3>
              <h5>Suma de proyectos</h5>
            </div>
            <div class="hr">
              <hr>
            </div>
          </div>
          <div class="form-container">
            <form id="filtroReporte" action="#">
              <div class="form-group">
                <label for="selectorProyecto">Seleccionar Proyecto</label>
                <select id="selectorProyecto" name="proyecto">
                  <!-- <option value="">Proyecto</option> -->
                  <option value="evangelistas">Evangelistas</option>
                  <option value="lpp">LPP</option>
                  <option value="cm">C&M</option>
                  <option value="proyecto-felipe">Proyecto Felipe</option>
                  <option value="instituto-biblico">Instituto Bíblico</option>
                </select>
              </div>
              <div class="form-group">
                <label for="anio">Seleccionar Año</label>
                <select id="anio" name="anio">
                  <!-- <option value="">Año</option> -->
                  <?php
                  for ($i = 2020; $i <= date("Y"); $i++) {
                    echo '<option value="' . $i . '">' . $i . '</option>';
                  }
                  ?>
                </select>
              </div>
              <button type="submit">Generar Reporte</button>
            </form>
          </div>
          <style>
            /* Estilos generales para el formulario */
            .form-container {
              max-width: 1000px;
              margin: 0 auto;
              padding: 20px;
              background-color: #f9f9f9;
              border-radius: 8px;
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            /* Estilos para los grupos de formulario */
            .form-group {
              margin-bottom: 15px;
            }

            /* Estilos para las etiquetas */
            .form-group label {
              display: block;
              margin-bottom: 5px;
              font-weight: bold;
              color: #333;
            }

            /* Estilos para los selectores */
            .form-group select {
              width: 100%;
              padding: 10px;
              border: 1px solid #ccc;
              border-radius: 4px;
              background-color: #fff;
              font-size: 16px;
              color: #333;
              appearance: none;
              /* Elimina la flecha por defecto en algunos navegadores */
              background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="%23333" class="bi bi-chevron-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>');
              background-repeat: no-repeat;
              background-position: right 10px center;
              background-size: 16px 16px;
            }

            /* Estilos para el botón */
            button[type="submit"] {
              width: 100%;
              padding: 10px;
              background-color: #007bff;
              border: none;
              border-radius: 4px;
              color: #fff;
              font-size: 16px;
              cursor: pointer;
              transition: background-color 0.3s ease;
            }

            button[type="submit"]:hover {
              background-color: #0056b3;
            }

            /* Media Queries para disposición horizontal en pantallas grandes */
            @media (min-width: 768px) {
              form {
                display: flex;
                align-items: center;
                justify-content: space-between;
              }

              .form-group {
                flex: 1;
                margin-right: 20px;
              }

              .form-group:last-child {
                margin-right: 0;
              }

              button[type="submit"] {
                width: auto;
                padding: 12px 20px;
                margin-top: 0;
              }
            }
          </style>
          <div id="GraficaReporte" style="width: 100%; height: 500px;"></div>
          <script src="scripts/reports.js"></script>
        </li>
      </ul>
    </div>
    <script type="text/javascript">
      $(document).ready(function() {
        $('ul.t-tabs li.t-tab:first').addClass('selected');
        $('ul.t-contents li.t-content:first').addClass('selected');
        $('ul.t-contents li.t-content:first').show();

      })
    </script>
    <script>
      function easyTabs() {
        var groups = document.querySelectorAll('.t-container');
        if (groups.length > 0) {
          for (i = 0; i < groups.length; i++) {
            var tabs = groups[i].querySelectorAll('.t-tab');
            for (t = 0; t < tabs.length; t++) {
              tabs[t].setAttribute("index", t + 1);
              if (t == 0) tabs[t].className = "t-tab";
            }
            var contents = groups[i].querySelectorAll('.t-content');
            for (c = 0; c < contents.length; c++) {
              contents[c].setAttribute("index", c + 1);
              if (c == 0) contents[c].className = "t-content";
            }
          }
          var clicks = document.querySelectorAll('.t-tab');
          for (i = 0; i < clicks.length; i++) {
            clicks[i].onclick = function() {
              var tSiblings = this.parentElement.children;
              for (i = 0; i < tSiblings.length; i++) {
                tSiblings[i].className = "t-tab";
              }
              this.className = "t-tab selected";
              var idx = this.getAttribute("index");
              var cSiblings = this.parentElement.parentElement.querySelectorAll('.t-content');
              for (i = 0; i < cSiblings.length; i++) {
                cSiblings[i].className = "t-content";
                if (cSiblings[i].getAttribute("index") == idx) {
                  cSiblings[i].className = "t-content selected";
                }
              }
            };
          }
        }
      }
    </script>
    <script type="text/javascript">
      (function() {
        easyTabs();
      })();
    </script>
  </div>
</div>
