<?php
/*******************************************
En un rango de fechas:
Cuantos prospecto hay desde el día 1 y cuantos el día último.
La diferencia es = nuevos prospectos, 
con TODOS los COMERCIALES.

ID MENU = 1
*******************************************/

$mesesNom = array("No", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

$PSN = new DBbase_Sql;
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;

$sql= "SELECT idMenu ";
$sql.=" FROM usuarios_menu ";
$sql.=" WHERE idMenu = 76 
AND idUsuario = '".$_SESSION["id"]."'";
$PSN->query($sql);
$m_usua = $PSN->num_rows();

$sql= "SELECT idMenu ";
$sql.=" FROM usuarios_menu_graphs ";
$sql.=" WHERE idMenu = 4 
AND idUsuario = '".$_SESSION["id"]."'";
$PSN1->query($sql);
$m_graf = $PSN1->num_rows();
if($m_usua == 0 && $m_graf == 0){
    die("<h5 class='alert alert-danger text-center'>NO esta autorizado a ver esta grafica</h5>");
}

/*
*   FILTROS DE FECHAS.
*/
//$busquedaFechaIni = date("Y-m-d", strtotime("-3 months"));
$busquedaFechaIni = '2000-01-01';
if(isset($_REQUEST["fechaInicial"]) && soloNumeros($_REQUEST["fechaInicial"]) != ""){
	$busquedaFechaIni = eliminarInvalidos($_REQUEST["fechaInicial"]);
}
else{
    $_REQUEST["fechaInicial"] = $busquedaFechaIni;
}
//
$siguiente_anho = date("Y", strtotime("+1 year"));
//$busquedaFechaFin = $siguiente_anho."-01-31";
$busquedaFechaFin = date("Y-m-d");
if(isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != ""){
	$busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);
}else{
    $_REQUEST["fechaFinal"] = $busquedaFechaFin;
}



/*****************************************************************************
//  EN ESTA GRAFICA NO CUENTA PERO PODRIA SERVIR PARA OTRAS
//Si es cliente o autorizado
*****************************************************************************/
if($_SESSION["perfil"] == 163){
    //
    $_REQUEST["idUsuario"] = $_SESSION["id"];
    //  
}

$sqlFiltros = "";


if(!isset($_REQUEST["fechaInicial"]) || eliminarInvalidos($_REQUEST["fechaInicial"]) == ""){
    $_REQUEST["fechaInicial"] = date("Y-m-01", strtotime("-2 months"));
}
if(!isset($_REQUEST["fechaFinal"]) || eliminarInvalidos($_REQUEST["fechaFinal"]) == ""){
    $_REQUEST["fechaFinal"] = date("Y-m-d");
}


if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) > 0){
    $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
    $sqlFiltro .= " AND RC.usuario_id = '".$buscar_idUsuario."'";
}

if(isset($_REQUEST["empresa_pd"]) && soloNumeros($_REQUEST["empresa_pd"]) != ""){
    $buscar_regional = soloNumeros($_REQUEST["empresa_pd"]);
}else if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
    $buscar_regional = soloNumeros($_SESSION["empresa_pd"]);
}

if(isset($_REQUEST["sitioReunion"]) && soloNumeros($_REQUEST["sitioReunion"]) > 0){
    $buscar_prision = soloNumeros($_REQUEST["sitioReunion"]);
    $sqlFiltro .= " AND RC.carcel_id = ".$buscar_prision."";
}

//
if(isset($_REQUEST["rep_inex"]) && eliminarInvalidos($_REQUEST["rep_inex"]) != ""){
    $tipo = eliminarInvalidos($_REQUEST["rep_inex"]);
    if ($tipo == 2) {
        $sqlFiltro .= " AND RC.tipo = 'EXTRA' ";
    }else{
        $sqlFiltro .= " AND RC.tipo = 'INTRA' ";
    }
}else{
    $_REQUEST["rep_inex"] = "";
}
//
if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
    $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
    $sqlFiltro .= " AND RC.fecha_reporte >= '".$fechaInicial."'";
}
//
if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
    $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
    $sqlFiltro .= " AND RC.fecha_reporte <= '".$fechaFinal."'";
}
//
if(isset($_REQUEST["empresa_paisid"]) && soloNumeros($_REQUEST["empresa_paisid"]) != ""){
    $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
    $sqlFiltro .= " AND UE.empresa_paisid = '".$empresa_paisid."'";
}


$sqlFiltro .= " AND RC.generacion != 0";

/*
*	PIE - Grafica de PIE mostrando cantidad de nuevos prospectos X comercial
*/
$nombreGrafica ="DATOS DEL PROCESO";
$datos = array();
//
//
$sql = "SELECT
            COUNT(RC.usuario_id) as conteoUsuarios,

            SUM(CASE WHEN RC.generacion > 0 THEN 1 ELSE 0 END) AS gruposConteo,

            SUM(RC.asistencia_total) as asistencia_total,
            SUM(RC.asistencia_hombres) as asistencia_hom,
            SUM(RC.asistencia_mujeres) as asistencia_muj,
            SUM(RC.asistencia_jovenes) as asistencia_jov,
            SUM(RC.asistencia_ninos) as asistencia_nin,

            SUM(RC.miembros_bautizados) as bautizados,
            SUM(RC.bautizados_periodo) as bautizadosPeriodo,
            SUM(RC.familias_ppl) as familias_privadas,
            SUM(RC.familias_pospenados) as familias_pospenados,
            SUM(RC.en_discipulado) as discipulado,
            SUM(RC.decisiones_cristo) as desiciones,
            SUM(RC.preparandose_bautismo) as preparandose
            ";
$sql.=" FROM reporte_cm AS RC ";
$sql .= " LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = RC.usuario_id
LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = RC.carcel_id
LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
LEFT JOIN categorias AS CA ON CA.id = C.idSec";
//
$sql.=" WHERE 1 ".$sqlFiltro."";
//echo $sql;
//
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
		//datos[] = "['MIEMBROS BAUTIZADOS', ".$PSN->f('bautizados').", 'blue']";
		$datos[] = "['BAUTIZADOS PERIODO', 0".$PSN->f('bautizadosPeriodo').", '#2E86C1']";
		$datos[] = "['EN DISCIPULADO', 0".$PSN->f('discipulado').", '#239B56']";
		$datos[] = "['DECISIONES', 0".$PSN->f('desiciones').", '#F39C12']";
		$datos[] = "['PREPA RANDOSE', 0".$PSN->f('preparandose').", '#F1C40F']";
		$datos[] = "['Familias de personas privadas de la libertad atendidas', 0".$PSN->f('familias_privadas').", '#8E44AD']";
		$datos[] = "['Familias de pospenados atendidas', 0".$PSN->f('familias_pospenados').", '#E74C3C']";
		$totalProspectos += $PSN->f('asistencia_total');
        //
	}
}else{
    $varError = 1;
}

//
//
$sqlFiltro = "";


if(isset($_REQUEST["idUsuario"]) && soloNumeros($_REQUEST["idUsuario"]) > 0){
    $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
    $sqlFiltro .= " AND RC.usuario_id = '".$buscar_idUsuario."'";
}

//
if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
    $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
    $sqlFiltro .= " AND RC.fecha_reporte >= '".$fechaInicial."'";
}
//
if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
    $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
    $sqlFiltro .= " AND RC.fecha_reporte <= '".$fechaFinal."'";
}


/*
$sql = "SELECT
            COUNT(RC.id_cm) as conteo,
            SUM(RC.asistencia_total) as asistencia_total
            ";
$sql.=" FROM reporte_cm AS RC ";
$sql.=" WHERE RC.generacion = 0 ".$sqlFiltro."";
//
$PSN->query($sql);
$num=$PSN->num_rows();
if($num > 0)
{
	while($PSN->next_record())
	{
        //
		$datos[] = "['LIDERES CAPACITADOS', ".($PSN->f('asistencia_total')-$PSN->f('conteo')).", 'grey']";
		$totalProspectos += $PSN->f('asistencia_total');
        //
	}
}
*/

if($varError != 1){
    ?><script type="text/javascript">
      google.charts.load("current", {packages:["corechart", "treemap"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
            //GRAFICA DE PIE DE NUEVOS PROSPECTOS- //['Clase', 'Cantidad', { role: 'style' }], //
              var data = google.visualization.arrayToDataTable([
                ['Clase', 'Cantidad', { role: 'style' }], 
                <?=implode(",", $datos); ?>
            ]);
          
            var view = new google.visualization.DataView(data);
                  view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);
          

            var options = {
                chartArea: {
                  // leave room for y-axis labels
                  width: '80%'
                },
                bar: {groupWidth: "95%"},
                legend: { position: 'none' },
                width: '100%'
                //colors: ['crimson', 'limegreen']
            };
            //
            var chart = new google.visualization.BarChart(document.getElementById('donutchart'));
            chart.draw(view, options);	//PIE
      }
    </script><?php
}
//
?>
<div class="container">
    <form action="index.php" method="get" name="form1" class="form-horizontal">
        <input type="hidden" name="doc" value="grafica-datos-del-proceso-ecc" />
        <div class="row">
            <h3 class="alert alert-info text-center">GRÁFICA DE DATOS DEL PROCESO C&M</h3>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3>FILTRO DE BUSQUEDA</h3>
                <h5>de reportes</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
                <strong>Miembro de la regional:</strong><?php
            ?><select name="idUsuario" onchange="this.form.submit()" class="form-control">
            <?php
            if($_SESSION["perfil"] != 163){
                ?><option value="">Ver todos</option><?php
            }

            /*
            *   TRAEMOS LOS USUARIOS
            */
            $sql = "SELECT * ";
            $sql.=" FROM usuario AS U
            LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id";
            $sql.=" WHERE U.tipo IN (162, 163, 167) ";
            if($_SESSION["perfil"] == 163){
                $sql.=" AND U.id = '".$_SESSION["id"]."'";
            }
            if (!empty($buscar_regional)) {
                $sql.=" AND UE.empresa_pd = ".$buscar_regional." ";
            }
            $sql.=" ORDER BY U.nombre asc";

            $PSN2->query($sql);
            $numero_coo=$PSN2->num_rows();
            if($numero_coo > 0)
            {
                while($PSN2->next_record())
                {
                    ?><option value="<?=$PSN2->f('id'); ?>" <?php
                    if($buscar_idUsuario == $PSN2->f('id'))
                    {
                        ?>selected="selected"<?php
                    }
                    ?>><?=$PSN2->f('nombre'); ?></option><?php
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
                        $sql.=" FROM tbl_regional_ubicacion ";
                        if(!empty($buscar_regional)){
                            $sql.=" WHERE reub_reg_fk = ".$buscar_regional;
                        }
                        $sql.=" ORDER BY reub_reg_fk asc";

                        $PSN2->query($sql);
                        $numero_pri=$PSN2->num_rows();
                        if($numero_pri > 0){
                            while($PSN2->next_record()){
                                ?><option value="<?=$PSN2->f('reub_id'); ?>" <?php
                                if($buscar_prision == $PSN2->f('reub_id'))
                                {
                                    ?>selected="selected"<?php
                                }
                                ?>><?=$PSN2->f('reub_nom'); ?></option><?php
                            }
                        }
                    }else{
                        echo '<option value="">Sin regional asignada</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Tipo:</strong>
                <select name="rep_inex" class="form-control" onchange="this.form.submit()">
                    <option value="">Intramuros / Extramuros</option>
                    <option value="1" <?php echo($_REQUEST["rep_inex"] == 1)?'selected="selected"':""; ?>>Intramuros</option>
                    <option value="2" <?php echo($_REQUEST["rep_inex"] == 2)?'selected="selected"':""; ?>>Extramuros</option>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Fecha Inicial:</strong>
                <input type="date" name="fechaInicial" id="fechaInicial" value="<?=$fechaInicial; ?>" class="form-control" />
            </div>
            <div class="col-sm-2">
                <strong>Fecha Final:</strong>
                <input type="date" name="fechaFinal" id="fechaFinal" value="<?=$fechaFinal; ?>" class="form-control" />
            </div>
            <div class="col-sm-2"><br>
                <input type="submit" value="Buscar" class="btn btn-success" style="float:center" />
            </div>
        </div>
    </form>

<?php
/*
*    
*/
if($varError == 1){
  ?><div class="container">
        <div class="row">
            <h5 class="alert alert-warning text-center">No se ha encontrado ningun registro para el rango de fechas seleccionado.</h5>
        </div>
    </div><?php  
}
if($varError != 1){?>
    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3 class="text-center">RESULTADOS DE BUSQUEDA</h3>
            <h5><?=$totalProspectos; ?> Registros encontrados</h5>
        </div>
        <div class="hr"><hr></div>
    </div>
    <div class="col-md-12 text-center">
        <div id="donutchart" style="width: 100%; height: 500px;"></div>
    </div>
<?php } ?>
</div>