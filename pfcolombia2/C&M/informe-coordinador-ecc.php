<?php

/*
*	CONSOLIDADO CON SUMATORIA DE CAMPOS
*/
// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;


if(!isset($_REQUEST["fechaInicial"]) || eliminarInvalidos($_REQUEST["fechaInicial"]) == ""){
    //$three_months_ago = date("Y-m-d", strtotime("-3 months"));
        $three_months_ago = '2000-01-01';
    $_REQUEST["fechaInicial"] = $three_months_ago;
}
if(!isset($_REQUEST["fechaFinal"]) || eliminarInvalidos($_REQUEST["fechaFinal"]) == ""){
    $siguiente_anho = date("Y", strtotime("+1 year"));
    //$_REQUEST["fechaFinal"] = $siguiente_anho."-01-31";
    $_REQUEST["fechaFinal"] = date("Y-m-d");
}

if (!empty($_REQUEST['rep_ani'])) {
    $anio = $_REQUEST['rep_ani'];
}else{
    $anio = date('Y');
}
if (!empty($_REQUEST['rep_qua'])) {
    $q = $_REQUEST['rep_qua'];
    $iniQ = $anio.'-'.$q.'-01';
    $iniQ = date("Y-m-d", strtotime($iniQ));
    if ($_REQUEST['rep_qua']==1) {
        $finQ = $anio.'-'.($q+2).'-31';
    }else if ($_REQUEST['rep_qua']==10) {
        $finQ = $anio.'-'.($q+2).'-31';
    }else{
        $finQ = $anio.'-'.($q+2).'-30';
    }
    $finQ = date("Y-m-d", strtotime($finQ));
}else{
   $iniQ = $_REQUEST["fechaInicial"];
   $finQ = $_REQUEST["fechaFinal"];
}
//echo $iniQ.' - '.$finQ;
/*
*   GENERAR EXCEL
*/
if(isset($_REQUEST["excelXML"])){

    //  YA GENERACION 0 NO CUENTA
    //$sqlFiltro .= " AND sat_reportes.generacionNumero != 0";
    //$sqlFiltro .= " AND sat_reportes.generacionNumero != 77";
    //$sqlFiltro .= " AND sat_reportes.generacionNumero != 8";

    // Comentado: Filtro automático por perfil puede limitar resultados innecesariamente
    // if($_SESSION["perfil"] == 163){
    //     $_REQUEST["idUsuario"] = $_SESSION["id"];
    // } 
    $empresa_paisid_txt = "Confraternidad carcelaria";
    if(isset($_REQUEST["empresa_paisid"]) && soloNumeros($_REQUEST["empresa_paisid"]) != ""){
        $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
        $sqlFiltro .= " AND usuario_empresa.empresa_paisid = '".$empresa_paisid."'";
        
        /*
        *	TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
        */
        $sql = "SELECT * ";
        $sql.=" FROM categorias ";
        $sql.=" WHERE idSec = 37 ORDER BY descripcion asc";
        $PSN2->query($sql);
        $numero=$PSN2->num_rows();
        if($numero > 0)
        {
            while($PSN2->next_record())
            {
                $empresa_paisid_txt = "Satura ".$PSN2->f('descripcion');
            }
        }        
        
    }
    
    
    if(isset($_REQUEST["idUsuario"]) && trim($_REQUEST["idUsuario"]) != "" && soloNumeros($_REQUEST["idUsuario"]) != ""){
        $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
    }
    //
    if(isset($_REQUEST["idGrupoMadre"]) && soloNumeros($_REQUEST["idGrupoMadre"]) != ""){
        $buscar_idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
        $sqlFiltro .= " AND sat_reportes.idGrupoMadre = '".$buscar_idGrupoMadre."'";
    }
    
    //
    if(isset($_REQUEST["nombre"]) && eliminarInvalidos($_REQUEST["nombre"]) != ""){
        $buscar_nombre = eliminarInvalidos($_REQUEST["nombre"]);
        $sqlFiltro .= " AND sat_reportes.plantador LIKE '%".$buscar_nombre."%'";
    }
    
    //
    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
        $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte >= '".$fechaInicial."'";
    }
    
    //
    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
        $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte <= '".$fechaFinal."'";
    }
                    
    $sql = "SELECT
                sat_reportes.*,
                usuario.nombre as nombreUsuario,
                usuario.direccion as direccionUsuario,
                usuario.identificacion as identificacionUsuario,
                usuario_empresa.empresa_sitio,
                usuario_empresa.empresa_socio,
                usuario_empresa.empresa_rm,
                usuario_empresa.empresa_proceso,
                usuario_empresa.empresa_paisid 
                ";
    $sql.=" FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
    LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 
    LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk 
    LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario 
    LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    //
    $sql.=" WHERE 1 ".$sqlFiltro." ORDER BY usuario_empresa.empresa_paisid ASC, usuario.nombre ASC";
    $PSN1->query($sql);
    $numero=$PSN1->num_rows();
    //
    //
    $sql = "SELECT usuario.*, usuario_empresa.* FROM usuario LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = usuario.id WHERE usuario.id = '".$_SESSION["id"]."'";
    $PSN2->query($sql);
    if($PSN2->num_rows() > 0)
    {
        if($PSN2->next_record())
        {
            $empresa_pais = $PSN2->f('empresa_pais');
            $empresa_sitio_cor = $PSN2->f('empresa_sitio_cor');
            $empresa_socio = $PSN2->f('empresa_socio');   
            $empresa_rm = $PSN2->f('empresa_rm');
        }
    }

   
}
else{
    //
    $registros = 50;
    $pagina = soloNumeros($_GET["pagina"]);

    if (!$pagina) { 
        $inicio = 0; 
        $pagina = 1; 
    } 
    else
    { 
        $inicio = ($pagina - 1) * $registros; 
    }


    //  YA GENERACION 0 NO CUENTA
    $sqlFiltro .= " AND sat_reportes.generacionNumero != 0";
    $sqlFiltro .= " AND sat_reportes.generacionNumero != 77";

    //
    // Comentado: Filtro automático por perfil puede limitar resultados innecesariamente
    // if($_SESSION["perfil"] == 163){
    //     $_REQUEST["idUsuario"] = $_SESSION["id"];
    // }
    

    if(isset($_REQUEST["fechaInicial"]) && soloNumeros($_REQUEST["fechaInicial"]) != ""){
        $busquedaFechaIni = eliminarInvalidos($_REQUEST["fechaInicial"]);
    }
    //
    if(isset($_REQUEST["fechaFinal"]) && soloNumeros($_REQUEST["fechaFinal"]) != ""){
        $busquedaFechaFin = eliminarInvalidos($_REQUEST["fechaFinal"]);
    }

    if(isset($_REQUEST["idUsuario"]) && trim($_REQUEST["idUsuario"]) != "" && soloNumeros($_REQUEST["idUsuario"]) != ""){
        $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
    }
    if ($_SESSION["id_zona"]!="" && $_SESSION["id_zona"]!=0) {
        $sqlFiltro .= " AND C.idSec = '".$_SESSION["id_zona"]."'";
        $_REQUEST["empresa_sitio_cor"] = $_SESSION["id_zona"];
        $buscar_zona = $_SESSION["id_zona"];
    }

    if(isset($_REQUEST["empresa_pd"]) && trim($_REQUEST["empresa_pd"]) != "" && soloNumeros($_REQUEST["empresa_pd"]) != ""){
        $buscar_regional = soloNumeros($_REQUEST["empresa_pd"]);
        $sqlFiltro .= " AND RU.reub_reg_fk = '".$buscar_regional."'";
    }else  if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
        $buscar_regional = soloNumeros($_SESSION["empresa_pd"]);
        $sqlFiltro .= " AND RU.reub_reg_fk = '".$_SESSION["empresa_pd"]."'";
        $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
    }

    if(isset($_REQUEST["sitioReunion"]) && trim($_REQUEST["sitioReunion"]) != "" && soloNumeros($_REQUEST["sitioReunion"]) != ""){
        $buscar_prision = soloNumeros($_REQUEST["sitioReunion"]);
        $sqlFiltro .= " AND sat_reportes.sitioReunion = ".$buscar_prision."";
    }
    if(isset($_REQUEST["empresa_sitio_cor"]) && trim($_REQUEST["empresa_sitio_cor"]) != "" && soloNumeros($_REQUEST["empresa_sitio_cor"]) != ""){
        $buscar_zona = soloNumeros($_REQUEST["empresa_sitio_cor"]);
        $sqlFiltro .= " AND C.idSec = '".$buscar_zona."'";
    }
    if(isset($_REQUEST["rep_qua"]) && trim($_REQUEST["rep_qua"]) != "" && soloNumeros($_REQUEST["rep_qua"]) != ""){
        $buscar_periodo = soloNumeros($_REQUEST["rep_qua"]);
        $sqlFiltro .= " AND sat_reportes.mapeo_cuarto = '".$buscar_periodo."'";
    }

    //
    if(isset($_REQUEST["rep_inex"]) && eliminarInvalidos($_REQUEST["rep_inex"]) != ""){
        $tipo = eliminarInvalidos($_REQUEST["rep_inex"]);
        if ($tipo == 2) {
            $sqlFiltro .= " AND sat_reportes.sitioReunion = 0 ";
        }else{
            $sqlFiltro .= " AND sat_reportes.sitioReunion <> 0 ";
        }    
    }else{
        $_REQUEST["rep_inex"] = "";
    }
    //
    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
        $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte >= '".$fechaInicial."'";
    }
    //
    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
        $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte <= '".$fechaFinal."'";
    }

    if(isset($_REQUEST["empresa_paisid"]) && soloNumeros($_REQUEST["empresa_paisid"]) != ""){
        $empresa_paisid = soloNumeros($_REQUEST["empresa_paisid"]);
        $sqlFiltro .= " AND usuario_empresa.empresa_paisid = '".$empresa_paisid."'";
    }

    /*
    *	TRAEMOS EL CONTEO DE LOS REGISTROS POR USUARIO QUE ES EL AGRUPADOR.
    */
    $sql = "SELECT count(DISTINCT sat_reportes.idUsuario) as conteo ";
    $sql .= " FROM sat_reportes ";
    $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario
    LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion
    LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
    LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario
    LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE 1 AND sat_reportes.rep_tip = 308";
    //
    $sql .= $sqlFiltro." ORDER BY sat_reportes.id DESC";
    //

    $PSN1->query($sql);
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $total_registros = $PSN1->f('conteo');
        }
    }
    $total_paginas = ceil($total_registros / $registros); 

    // Paso 1: Obtener solo los IDs de usuarios que cumplen con los filtros (ULTRA OPTIMIZADO)

    // Para casos sin filtros o solo filtros básicos, usar consulta súper simple
    $only_basic_filters = (
        strpos($sqlFiltro, 'RU.reub_reg_fk') === false &&
        strpos($sqlFiltro, 'C.idSec') === false &&
        strpos($sqlFiltro, 'usuario_empresa.empresa_paisid') === false
    );

    if ($only_basic_filters) {
        // Consulta súper rápida sin JOINs para casos básicos
        $sql_user_ids = "SELECT DISTINCT sat_reportes.idUsuario FROM sat_reportes
        WHERE 1 ".$sqlFiltro." AND sat_reportes.rep_tip = 308
        ORDER BY sat_reportes.idUsuario LIMIT ".$inicio.", ".$registros;
    } else {
        // Consulta con JOINs solo cuando sea necesario
        $sql_user_ids = "SELECT DISTINCT sat_reportes.idUsuario FROM sat_reportes";

        $needs_regional_join = (strpos($sqlFiltro, 'RU.reub_reg_fk') !== false);
        $needs_categoria_join = (strpos($sqlFiltro, 'C.idSec') !== false);
        $needs_empresa_join = (strpos($sqlFiltro, 'usuario_empresa.empresa_paisid') !== false);

        if ($needs_regional_join || $needs_categoria_join) {
            $sql_user_ids .= " LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion";
        }
        if ($needs_categoria_join) {
            $sql_user_ids .= " LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk";
        }
        if ($needs_empresa_join) {
            $sql_user_ids .= " LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario";
        }

        $sql_user_ids .= " WHERE 1 ".$sqlFiltro." AND sat_reportes.rep_tip = 308
        ORDER BY sat_reportes.idUsuario LIMIT ".$inicio.", ".$registros;
    }

    $PSN_user_ids = new DBbase_Sql;
    $PSN_user_ids->query($sql_user_ids);
    $user_ids = [];
    while($PSN_user_ids->next_record()){
        $user_ids[] = $PSN_user_ids->f('idUsuario');
    }

    // Paso 2: Solo si hay IDs de usuarios, obtener los datos completos agrupados (RÁPIDO)
    if (count($user_ids) > 0) {
        $sql = "SELECT
                    sat_reportes.idUsuario,
                    COUNT(sat_reportes.generacionNumero) AS gruposConteo,

                    SUM(sat_reportes.asistencia_total) as asistencia_total,
                    SUM(sat_reportes.asistencia_hom) as asistencia_hom,
                    SUM(sat_reportes.asistencia_muj) as asistencia_muj,
                    SUM(sat_reportes.asistencia_jov) as asistencia_jov,
                    SUM(sat_reportes.asistencia_nin) as asistencia_nin,

                    SUM(sat_reportes.bautizados) as bautizados,
                    SUM(sat_reportes.bautizadosPeriodo) as bautizadosPeriodo,
                    SUM(sat_reportes.discipulado) as discipulado,
                    SUM(sat_reportes.desiciones) as desiciones,
                    SUM(sat_reportes.preparandose) as preparandose,
                    SUM(sat_reportes.iglesias_reconocidas) as iglesias_reconocidas,
                    SUM(sat_reportes.graduados) as graduados,
                    SUM(sat_reportes.graduadosPeriodo) as graduadosPeriodo,
                    U.nombre as nombreUsuario,
                    UE.empresa_sitio,
                    UE.empresa_rm,
                    UE.empresa_proceso ";
        $sql.=" FROM sat_reportes ";
        $sql .= " LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario
        LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion
        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = sat_reportes.idUsuario
        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
        //
        $sql.=" WHERE sat_reportes.idUsuario IN (" . implode(',', $user_ids) . ") AND sat_reportes.rep_tip = 308 ".$sqlFiltro." GROUP BY sat_reportes.idUsuario ORDER BY U.nombre ASC";
        //
        $PSN1->query($sql);
    }
    $numero=$PSN1->num_rows();
?>
<div class="container">
    <form name="form" id="form" method="get" class="form-horizontal">
        <input type="hidden" name="doc" value="informe-coordinador-ecc" />
        <div>
            <h3 class="alert alert-info text-center">CAPACITAR  Y MULTIPLICAR (C&M)</h3>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3>FILTRO DE BUSQUEDA</h3>
                <h5>de REPORTES</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
                <strong>Zona:</strong>
                <select name="empresa_sitio_cor" id="zona" class="form-control" onchange="this.form.submit()">
                    <option value="" <?php if($empresa_pd == ""){?>
                                selected="selected" <?php
                            } ?>>Todas la zonas</option>
                    <?php
                    /*
                    *   TRAEMOS LOS TIPOS DE CLIENTE/EMPRESA (15)
                    */
                    $sql = "SELECT * ";
                    $sql.=" FROM categorias ";
                    $sql.=" WHERE idSec = 85 ";
                    if ($_SESSION["id_zona"]!="" && $_SESSION["id_zona"]!=0) {
                        $sql.=" AND id = ".$_SESSION["id_zona"];
                    }
                    $sql .= " ORDER BY descripcion ASC ";

                    $PSN3->query($sql);
                    $numero=$PSN3->num_rows();
                    if($numero > 0){
                        while($PSN3->next_record()){
                            ?><option value="<?=$PSN3->f('id'); ?>" <?php
                            if($buscar_zona == $PSN3->f('id')){?>
                                selected="selected" <?php
                            }
                            ?>><?=$PSN3->f('descripcion'); ?></option><?php
                        }
                    }?>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Regional:</strong>
                <select  name="empresa_pd" id="regional" class="form-control" onchange="this.form.submit()">
                    <?php echo($zona == ""  && $_SESSION["perfil"]!=163 && $_SESSION["perfil"]!=162 )?'<option value="" selected >Todas la regionales</option>':"";
                    $sql = "SELECT C.id, C.descripcion AS regional, CA.descripcion AS zona FROM categorias AS C";
                    $sql.=" LEFT JOIN categorias AS CA ON CA.id = C.idSec
                    WHERE CA.idSec = 85 ";
                    if (!empty($buscar_zona)) {
                        $sql.=" AND CA.id = ".$buscar_zona;
                    }
                    if ($_SESSION["perfil"]!=167) {
                        if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                            $sql.=" AND C.id = ".$_SESSION["empresa_pd"];
                        }
                    }
                    
                    $PSN2->query($sql); 
                    echo $sql;
                    $numero=$PSN2->num_rows();
                    if($numero > 0){
                        while($PSN2->next_record()){?>
                            <option value="<?=$PSN2->f('id'); ?>" <?php
                            if($buscar_regional == $PSN2->f('id')){
                                ?>selected="selected"<?php
                            }
                            ?> ><?=$PSN2->f('regional'); ?></option><?php
                        }
                    }
                    ?>
                </select>                    
            </div>
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
                $sql.=" WHERE U.tipo IN (162, 163) ";
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
            <div class="col-sm-2">
                <strong>Período:</strong>
                <select name="rep_qua" onchange="this.form.submit()" class="form-control">
                    <option value="">Sin especificar</option>
                    <option value="1" <?php echo($_REQUEST['rep_qua']==1)?'selected':''; ?>>Q1 (Ene - Mar)</option>
                    <option value="4" <?php echo($_REQUEST['rep_qua']==4)?'selected':''; ?>>Q2 (Abr - Jun)</option>
                    <option value="7" <?php echo($_REQUEST['rep_qua']==7)?'selected':''; ?>>Q3 (Jul - Sep)</option>
                    <option value="10" <?php echo($_REQUEST['rep_qua']==10)?'selected':''; ?>>Q4 (Oct - Dic)</option>
                </select>
            </div>
            <!--
            <div class="col-sm-1">
                <strong>Año:</strong>
                <select name="rep_ani" onchange="this.form.submit()" class="form-control">
                    <option value="">Sin especificar</option>
                    <?php for ($i=2021; $i <= date('Y'); $i++) { 
                        echo '<option value="'.$i.'"';
                        echo ($i == $_REQUEST['rep_ani'])?'selected':'';
                        echo ' >'.$i.'</option>';
                    } ?>
                </select>
            </div>-->
            <div class="col-sm-1">
                <br>
                <input type="submit" value="Buscar" class="btn btn-success" />
            </div>
        </div>
    </form>
</div>

    <style>
    .table tbody tr:hover td, .table tbody tr:hover th {
        background-color: #E0EEEE;
        cursor:pointer;
        color:#000;
    }

    .table thead tr{
        background-color: #C7C7C7;
    }

    .table thead th{
        vertical-align: middle;text-align: center;
    }

    .table a{
        color:#000;
    }

    </style>

    <div class="container">
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3>RESULTADOS DE BUSQUEDA</h3>
                <h5><?php echo $total_registros; ?> Registros encontrados</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
    <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-bordered" style="font-size:12px">
        <thead>
            <tr> 
                <th>Id</th>
                <th>Facilitador</th>
                    <th>RM</th>
                    <th>Proceso</th>
                    <th>Sitio</th>
                <th>Grupos</th>
                <th>Grupos Nuevos</th>
                <th>Bautizados</th>
                    <th>Asistencia</th>
                <th>Decisiones</th>
                <th>Preparandose</th>
                    <th>Bautizados este período</th>
                <th>En Discipulado</th>
                    <th>Lideres capacitandose</th>
                <th>Graduados</th>
                <th>Graduados período</th>
                <th>Cursos de graduación</th>
                <th>Iglesias Reconocidas</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($total_registros > 0)
            {
                $contador = 1;
                while($PSN1->next_record())
                {
                    //Solo si no se ha modificado ya el formulario.
                    $idUsuario = $PSN1->f('idUsuario');
                    $plantador = $PSN1->f("plantador");
                    $fechaReporte = $PSN1->f("fechaReporte");
                    $fechaInicio = $PSN1->f("fechaInicio");        
                    $sitioReunion = $PSN1->f("sitioReunion");
                    $grupoMadre_txt = $PSN1->f("grupoMadre_txt");
                        
                    $idGrupoMadre = $PSN1->f("idGrupoMadre");
                    $generacionNumero = intval($PSN1->f("generacionNumero"));

                    $gruposConteo = $PSN1->f("gruposConteo");
                    
                    $sql = "SELECT COUNT(DISTINCT sat_reportes.idGrupoMadre) as conteo ";
                    $sql.=" FROM sat_reportes ";
                    //
                    //
                    $sqlFiltro = "";
                    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
                        $sqlFiltro .= " AND sat_reportes.fechaInicio >= '".$fechaInicial."'";
                    }
                    //
                    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
                        $sqlFiltro .= " AND sat_reportes.fechaInicio <= '".$fechaFinal."'";
                    }

                    $sql.=" WHERE idUsuario = '".$idUsuario."' ".$sqlFiltro;
                    $PSN2->query($sql);
                    if($PSN2->num_rows() > 0)
                    {
                        if($PSN2->next_record())
                        {
                            $gruposNuevos = $PSN2->f('conteo');
                        }
                    }
                    
                    
                    
                    $nombreUsuario = $PSN1->f("nombreUsuario");
                        $empresa_sitio = $PSN1->f("empresa_sitio");
                        $empresa_rm = $PSN1->f("empresa_rm");
                        $empresa_proceso = $PSN1->f("empresa_proceso");

                    $asistencia_hom = $PSN1->f("asistencia_hom");
                    $asistencia_muj = $PSN1->f("asistencia_muj");
                    $asistencia_jov = $PSN1->f("asistencia_jov");
                    $asistencia_nin = $PSN1->f("asistencia_nin");

                    $bautizados = $PSN1->f("bautizados");
                    $bautizadosPeriodo = $PSN1->f("bautizadosPeriodo");

                    //Calculados:
                    $asistencia_total  = $PSN1->f("asistencia_total");
                    $discipulado  = $PSN1->f("discipulado");
                    $desiciones  = $PSN1->f("desiciones");
                    $preparandose  = $PSN1->f("preparandose");
                    $graduados  = $PSN1->f("graduados");
                    $graduadosPeriodo  = $PSN1->f("graduadosPeriodo");
                    $iglesias_reconocidas = $PSN1->f("iglesias_reconocidas");
                    
                    // Obtener cursos de graduación para este facilitador
                    $cursosGraduacion = "";
                    $sql_cursos = "SELECT DISTINCT C.descripcion 
                                  FROM tbl_adjuntos AS A 
                                  LEFT JOIN categorias AS C ON C.id = A.adj_curso 
                                  LEFT JOIN sat_reportes AS S ON S.id = A.adj_rep_fk 
                                  WHERE S.idUsuario = '".$idUsuario."'";
                    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
                        $sql_cursos .= " AND A.adj_fec >= '".$fechaInicial."'";
                    }
                    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
                        $sql_cursos .= " AND A.adj_fec <= '".$fechaFinal."'";
                    }
                    $sql_cursos .= " ORDER BY C.descripcion ASC";
                    
                    $PSN3->query($sql_cursos);
                    $cursos_array = array();
                    if($PSN3->num_rows() > 0){
                        while($PSN3->next_record()){
                            $curso_desc = $PSN3->f('descripcion');
                            if($curso_desc != "" && $curso_desc != null){
                                $cursos_array[] = $curso_desc;
                            } else {
                                $cursos_array[] = "No especificado";
                            }
                        }
                    }
                    if(empty($cursos_array) && ($graduados > 0 || $graduadosPeriodo > 0)){
                        $cursosGraduacion = "No especificado";
                    } else if(!empty($cursos_array)) {
                        $cursosGraduacion = implode(", ", array_unique($cursos_array));
                    } else {
                        $cursosGraduacion = "-";
                    }  
                    
                    $lideresCapacitandose = 0;                        
                    
                    $sql = "SELECT SUM(asistencia_total) as total, COUNT(sat_reportes.id) as conteo ";
                    $sql.=" FROM sat_reportes ";
                    $sql .= " LEFT JOIN usuario ON usuario.id = sat_reportes.idUsuario";
                    $sql .= " LEFT JOIN usuario_empresa ON usuario_empresa.idUsuario = sat_reportes.idUsuario";
                    //
                    $sql.=" WHERE 1 ".$sqlFiltro." AND  sat_reportes.idUsuario = '".$idUsuario."' AND sat_reportes.generacionNumero = 0 GROUP BY sat_reportes.idUsuario";
                    $PSN2->query($sql);
                    if($PSN2->num_rows() > 0)
                    {
                        if($PSN2->next_record())
                        {
                            $lideresCapacitandose = ($PSN2->f('total')-$PSN2->f('conteo'));
                        }
                    }
                    //
                    ?><tr>
                        <td><?=$contador; ?></td>
                        <td><?=$nombreUsuario; ?></td>
                            <td><?=$empresa_rm; ?></td>
                            <td><?=$empresa_proceso; ?></td>
                            <td><?=$empresa_sitio; ?></td>
                        <td align="center"><?=$gruposConteo; ?></td>
                        <td align="center"><?=$gruposNuevos; ?></td>
                        <td align="center"><?=$bautizados; ?></td>
                            <td align="center"><?=$asistencia_total; ?></td>
                        <td align="center"><?=$desiciones; ?></td>
                        <td align="center"><?=$preparandose; ?></td>
                            <td align="center"><?=$bautizadosPeriodo; ?></td>
                        <td align="center"><?=$discipulado; ?></td>
                            <td align="center"><?=$lideresCapacitandose; ?></td>
                        <td align="center"><?=$graduados; ?></td>
                        <td align="center"><?=$graduadosPeriodo; ?></td>
                        <td><?=$cursosGraduacion; ?></td>
                        <td align="center"><?=$iglesias_reconocidas; ?></td>
                    </tr>
                    <?php
                    $contador++;
                }
            }
            ?>
        </tbody>
        </table>
    </div>


    <center>
    <div class="container">
        <ul class="pagination">
            <?php
            //
            $paginaActualTxT = "&pagina=".$pagina;
            $_SERVER['REQUEST_URI'] = str_replace($paginaActualTxT,"", $_SERVER['REQUEST_URI']);
            //
            if(($pagina - 1) > 0)
            {
                echo "<li><a href='".$_SERVER['REQUEST_URI']."&pagina=".($pagina-1)."'>&laquo;</a></li>"; 
            }

            for ($i=1; $i<=$total_paginas; $i++)
            { 
                if ($pagina == $i)
                {
                    echo "<li class='active'><a href='".$_SERVER['REQUEST_URI']."&pagina=$i'>$i</a>"; 
                }
                else 
                { 
                    echo "<li><a href='".$_SERVER['REQUEST_URI']."&pagina=$i'>$i</a></li>";
                } 
            }

            if(($pagina + 1)<=$total_paginas)
            { 
                echo "<li><a href='".$_SERVER['REQUEST_URI']."&pagina=".($pagina+1)."'>&raquo;</a></li>"; 
            }
            ?>
        </ul>
    </div>
    </center>

    <br />
    <center>
    <a href="generaExcelECC.php?<?php echo http_build_query($_REQUEST); ?>" target="_blank" class="btn btn-info"><span class="glyphicon glyphicon-cloud-download"></span> DESCARGAR PARA EXCEL</a></center>


    <script language="javascript">
    function init(){
    }
    window.onload = function(){
        init();
    }
    </script>


    <script>
    jQuery(document).ready(function($) {
        $(".clickable-row").click(function() {
            window.location = $(this).data("href");
        });
    });
    </script><?php
}
?>