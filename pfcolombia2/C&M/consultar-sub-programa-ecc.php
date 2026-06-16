<?php
/*
*	$PSN = new DBbase_Sql;
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

/*
*   GENERAR EXCEL
*/
if(isset($_REQUEST["excelX"])){
    die("En proceso");
    
    if(isset($_REQUEST["tipo"]) && soloNumeros($_REQUEST["tipo"]) != ""){
        $buscar_tipo = soloNumeros($_REQUEST["tipo"]);
        $sqlFiltro .= " AND usuario.tipo = '".$buscar_tipo."'";
    }

    if(isset($_REQUEST["nombre"]) && eliminarInvalidos($_REQUEST["nombre"]) != ""){
        $buscar_nombre = eliminarInvalidos($_REQUEST["nombre"]);
        $sqlFiltro .= " AND usuario.nombre LIKE '%".$buscar_nombre."%'";
    }

    if(isset($_REQUEST["identificacion"]) && eliminarInvalidos($_REQUEST["identificacion"]) != ""){
        $buscar_identificacion = eliminarInvalidos($_REQUEST["identificacion"]);
        $sqlFiltro .= " AND usuario.identificacion LIKE '%".$buscar_identificacion."%'";
    } 

    if(isset($_REQUEST["cliente"]) && eliminarInvalidos($_REQUEST["cliente"]) != ""){
        $buscar_cliente = eliminarInvalidos($_REQUEST["cliente"]);
        $sqlFiltro .= " AND cliente.id = '".$buscar_cliente."'";
    } 
    
    
    
    
    //
    $sqlFiltro .= " AND usuario.tipo IN (".$temp_tiposUsuario.")";
    //
    $sql = "SELECT usuario.*, categorias.descripcion,  categorias.descripcion as nomTipoID, cliente.nombre as nomcliente ";
    $sql.=" FROM usuario ";
    $sql.=" LEFT JOIN categorias ";
    $sql.=" ON categorias.id = usuario.tipo";
    $sql.=" LEFT JOIN categorias AS tipoID";
    $sql.=" ON tipoID.id = usuario.tipoIdentificacion";
    $sql.=" LEFT JOIN usuario_relacion ON usuario_relacion.idUsuario1 = usuario.id ";
    $sql.=" LEFT JOIN usuario as cliente ON cliente.id = usuario_relacion.idUsuario2 AND cliente.tipo = 3";
    //    
    $sql.=" WHERE usuario.id != 2 ".$sqlFiltro." GROUP BY usuario.id ORDER BY usuario.tipo ASC, usuario.nombre ASC";
    //
    $PSN1->query($sql);
    $numero=$PSN1->num_rows();
    ?><strong><?php echo $numero; ?> REGISTROS DE USUARIOS DEL TIPO: <?=$temp_letrero; ?>.</strong>
    <table border="1">
    <thead>
        <tr> 
        <th>Id</th>
        <th>Tipo de usuario</th>
        <th>Acceso al sistema</th>
        <?php
        if($ctrl == "" || $ctrl == 4){
            ?><th>Autorizado del cliente:</th><?php
        }
        ?>
        <th>Nombre</th>
        <th>Identificación</th>
        <th>Tipo de identificación</th>
        <th>Direccion</th>
        <th>Teléfono 1</th>
        <th>Teléfono 2</th>
        <th>celular</th>
        <th>Celular 2</th>
        <th>Email</th>
        <th>Pagina Web</th>
        <th>Observaciones</th>
        <?php
        if($ctrl == "" || $ctrl == 2 || $ctrl == 3){
            ?><th>Empresa - Tipo de empresa</th>
            <th>Empresa - Representante legal</th>
            <th>Empresa - Nombre contacto</th>
            <th>Empresa - Dirección</th>
            <th>Empresa - Página Web</th><?php
        }
    

        if($ctrl == "" || $ctrl == 1 || $ctrl == 4){
            ?><th>Empresa - Cargo</th>
            <th>Empresa - Teléfono 1</th>
            <th>Empresa - Teléfono 2</th>
            <th>Empresa - Celular 1</th>
            <th>Empresa - Celular 2</th>
            <th>Empresa - Email 1</th>
            <th>Empresa - Email 2</th>
            <?php
        }
    
        if($ctrl == "" || $ctrl == 3){
             ?><th>Proveedor - Tipo de persona</th>
            <th>Proveedor - Tipo de servicio</th>
            <th>Proveedor - Tipo de servicio 2</th>
            <th>Proveedor - Tipo de contrato</th>
            <th>Proveedor - Tipo de contrato 2</th>
            <th>Proveedor - Ampliación de los servicios prestados</th>
            <th>Proveedor - Fecha de inicio VIGENCIA</th>
            <th>Proveedor - Fecha final VIGENCIA</th>
            <th>Proveedor - Porcentaje de descuento</th>
            
            <?php
        }
    
        //Pestaña cliente
        if($ctrl == "" || $ctrl == 2){
            ?><th>Cliente - Tipo de persona</th>
            <th>Cliente - Tipo de servicio</th>
            <th>Cliente - Tipo de contrato:</th>
            <th>Cliente - Ampliación de los servicios ofrecidos</th>
            <th>Cliente - Valor del contrato</th>
            <th>Cliente - Día de pago</th>
            <th>Cliente - Fecha de aprobación cliente:</th>
            <th>Cliente - Fecha aprobación contrato</th>
            <th>Cliente - Fecha de inicio contrato</th>
            <th>Cliente - Fecha final contrato</th><?php
        }
    
        ?>
        </tr>
    </thead>
    <tbody>
        <?php
        if($numero > 0)
        {
            $contador = 0;
            while($PSN1->next_record())
            {
                //Solo si no se ha modificado ya el formulario.
                $id = $PSN1->f('id');
                $idUsuarioActual = $id;
                $nomcliente = $PSN1->f('nomcliente');
                $tipodesc = $PSN1->f('descripcion');
                $nombre = $PSN1->f('nombre');
                $telefono1 = $PSN1->f('telefono1');
                $celular = $PSN1->f('celular');
                $email = $PSN1->f('email');
                $temp_acceso = $PSN1->f('acceso');
                
                /*
                *	TRAEMOS LOS DATOS EMPRESARIALES
                */
                $sql = "SELECT usuario_empresa.*, categorias.descripcion ";
                $sql.=" FROM usuario_empresa LEFT JOIN categorias ON categorias.id = usuario_empresa.empresa_tipo ";
                $sql.=" WHERE idUsuario = '".$idUsuarioActual."'";
                $PSN2->query($sql);
                if($PSN2->num_rows() > 0)
                {
                    if($PSN2->next_record())
                    {
                        $empresa_tipo = $PSN2->f("descripcion");
                        $empresa_nombre = $PSN2->f("empresa_nombre");
                        $empresa_nit = $PSN2->f("empresa_nit");
                        $empresa_representante = $PSN2->f("empresa_representante");
                        $empresa_contacto = $PSN2->f("empresa_contacto");
                        $empresa_direccion = $PSN2->f("empresa_direccion");
                        $empresa_url = $PSN2->f("empresa_url");
                        $empresa_telefono1 = $PSN2->f("empresa_telefono1");
                        $empresa_telefono2 = $PSN2->f("empresa_telefono2");
                        $empresa_celular1 = $PSN2->f("empresa_celular1");
                        $empresa_celular2 = $PSN2->f("empresa_celular2");
                        $empresa_email1 = $PSN2->f("empresa_email1");
                        $empresa_email2 = $PSN2->f("empresa_email2");
                        $empresa_cargo = $PSN2->f("empresa_cargo");
                    }
                }

                /*
                *	TRAEMOS LOS DATOS DE PROVEEDOR
                */
                $sql = "SELECT usuario_servicios.*, categorias.descripcion, cat_contrato1.descripcion as nomcontrato1, cat_contrato2.descripcion as nomcontrato2, cat_servicios1.descripcion as nomservicios1, cat_servicios2.descripcion as nomservicios2 ";
                $sql.=" FROM usuario_servicios 
                        LEFT JOIN categorias ON categorias.id = usuario_servicios.servicios_tipoPersona 
                        LEFT JOIN categorias as cat_contrato1 ON cat_contrato1.id = usuario_servicios.servicios_contrato1 
                        LEFT JOIN categorias as cat_contrato2 ON cat_contrato2.id = usuario_servicios.servicios_contrato2 
                        LEFT JOIN categorias as cat_servicios1 ON cat_servicios1.id = usuario_servicios.servicios_tipo1 
                        LEFT JOIN categorias as cat_servicios2 ON cat_servicios2.id = usuario_servicios.servicios_tipo2
                ";
                $sql.=" WHERE idUsuario = '".$idUsuarioActual."'";
                $PSN2->query($sql);
                if($PSN2->num_rows() > 0)
                {
                    if($PSN2->next_record())
                    {
                        $servicios_tipo1 = $PSN2->f("nomservicios1");
                        $servicios_tipo2 = $PSN2->f("nomservicios2");
                        $servicios_contrato1 = $PSN2->f("nomcontrato1");
                        $servicios_contrato2 = $PSN2->f("nomcontrato2");
                        $servicios_observaciones = $PSN2->f("servicios_observaciones");
                        $servicios_fechaInicio = $PSN2->f("servicios_fechaInicio");
                        $servicios_fechaFin = $PSN2->f("servicios_fechaFin");
                        $servicios_tipoPersona = $PSN2->f("descripcion");
                        $servicios_porcentaje = $PSN2->f("servicios_porcentaje");
                        
                    }
                }

                /*
                *	TRAEMOS LOS DATOS DE CLIENTE
                */
                $sql = "SELECT usuario_cliente.*, categorias.descripcion, cat_tipo1.descripcion as nomtipo1, cat_servicio1.descripcion as nomcontrato1 ";
                $sql.=" FROM usuario_cliente 
                        LEFT JOIN categorias ON categorias.id = usuario_cliente.cliente_tipoPersona 
                        LEFT JOIN categorias as cat_tipo1 ON cat_tipo1.id = usuario_cliente.cliente_tipo1 
                        LEFT JOIN categorias as cat_servicio1 ON cat_servicio1.id = usuario_cliente.cliente_servicio1 
                ";
                $sql.=" WHERE idUsuario = '".$idUsuarioActual."'";
                $PSN2->query($sql);
                if($PSN2->num_rows() > 0)
                {
                    if($PSN2->next_record())
                    {
                        $cliente_tipoPersona = $PSN2->f("descripcion");
                        $cliente_tipo1 = $PSN2->f("nomtipo1");
                        $cliente_servicio1 = $PSN2->f("nomcontrato1");
                        $cliente_observaciones = $PSN2->f("cliente_observaciones");
                        $cliente_valor1 = $PSN2->f("cliente_valor1");
                        $cliente_diaPago = $PSN2->f("cliente_diaPago");
                        $cliente_fechaAprob = $PSN2->f("cliente_fechaAprob");
                        $cliente_fechaAprobCont = $PSN2->f("cliente_fechaAprobCont");
                        $cliente_fechaInicial = $PSN2->f("cliente_fechaInicial");
                        $cliente_fechaFinal = $PSN2->f("cliente_fechaFinal");
                    }
                }     

                ?><tr <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
                    <td><?=str_pad($id, 6, "0", STR_PAD_LEFT); ?></td>
                    <td><?=$PSN1->f("tipo"); ?></td>
                    <td><?php if($temp_acceso == 1){
                        echo "Si";
                    }else{
                        echo "No";
                    } ?></td>
                    <?php
                    if($ctrl == "" || $ctrl == 4){
                        ?><td><?=utf8_decode($PSN1->f("nomcliente")); ?></td><?php
                    }
                    ?>
                    <td><?=utf8_decode($PSN1->f("nombre")); ?></td>
                    <td><?=$PSN1->f("identificacion"); ?></td>
                    <td><?=$PSN1->f("nomTipoID"); ?></td>
                    <td><?=utf8_decode($PSN1->f("direccion")); ?></td>
                    <td><?=$PSN1->f("telefono1"); ?></td>
                    <td><?=$PSN1->f("telefono2"); ?></td>
                    <td><?=$PSN1->f("celular"); ?></td>
                    <td><?=$PSN1->f("celular2"); ?></td>
                    <td><?=$PSN1->f("email"); ?></td>
                    <td><?=$PSN1->f("url"); ?></td>
                    <td><?=utf8_decode($PSN1->f("observaciones")); ?></td>
        
                    <?php
                    //Pestaña empresarial
                    if($ctrl == "" || $ctrl == 2 || $ctrl == 3){
                        ?><td><?=utf8_decode($empresa_tipo); ?></td>
                        <td><?=utf8_decode($empresa_representante); ?></td>
                        <td><?=utf8_decode($empresa_contacto); ?></td>
                        <td><?=utf8_decode($empresa_direccion); ?></td>
                        <td><?=$empresa_url; ?></td><?php
                    }
                    
                    //Pestaña empresarial
                    if($ctrl == "" || $ctrl == 1 || $ctrl == 4){
                        ?><td><?=$empresa_cargo; ?></td>
                        <td><?=$empresa_telefono1; ?></td>
                        <td><?=$empresa_telefono2; ?></td>
                        <td><?=$empresa_celular1; ?></td>
                        <td><?=$empresa_celular2; ?></td>
                        <td><?=$empresa_email1; ?></td>
                        <td><?=$empresa_email2; ?></td><?php
                    }
                
                    //Pestaña proveedor
                    if($ctrl == "" || $ctrl == 3){
                         ?><td><?=utf8_decode($servicios_tipoPersona); ?></td>
                        <td><?=utf8_decode($servicios_tipo1); ?></td>
                        <td><?=utf8_decode($servicios_tipo2); ?></td>
                        <td><?=utf8_decode($servicios_contrato1); ?></td>
                        <td><?=utf8_decode($servicios_contrato2); ?></td>
                        <td><?=utf8_decode($servicios_observaciones); ?></td>
                        <td><?=$servicios_fechaInicio; ?></td>
                        <td><?=$servicios_fechaFin; ?></td>
                        <td><?=$servicios_porcentaje; ?></td><?php
                        
                    }       
                
                    //Pestaña cliente
                    if($ctrl == "" || $ctrl == 2){
                        ?><td><?=utf8_decode($cliente_tipoPersona); ?></td>
                        <td><?=utf8_decode($cliente_tipo1); ?></td>
                        <td><?=utf8_decode($cliente_servicio1); ?></td>
                        <td><?=utf8_decode($cliente_observaciones); ?></td>
                        <td><?=$cliente_valor1; ?></td>
                        <td><?=$cliente_diaPago; ?></td>
                        <td><?=$cliente_fechaAprob; ?></td>
                        <td><?=$cliente_fechaAprobCont; ?></td>
                        <td><?=$cliente_fechaInicial; ?></td>
                        <td><?=$cliente_fechaFinal; ?></td><?php
                    }
                    ?>       
                </tr>
                <?php
                $contador++;
            }
        }
        else{
            echo "Sin registros";
        }
        ?>
    </tbody>
    </table><?php
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


    /*
    *	TRAEMOS LOS registros - PATRÓN OPTIMIZADO
    */
    // Construir filtros una vez
    $sqlFiltro = "";
    
    // Inicializar variables para evitar errores undefined
    $buscar_idUsuario = "";
    $buscar_zona = "";
    $buscar_regional = "";
    $buscar_prision = "";
    $fechaInicial = "";
    $fechaFinal = "";
    
    if($_SESSION["perfil"] == 163){
        $_REQUEST["idUsuario"] = $_SESSION["id"];
    }
    
    if(isset($_REQUEST["idUsuario"]) && $_REQUEST["idUsuario"] != "" && soloNumeros($_REQUEST["idUsuario"]) != ""){
        $buscar_idUsuario = soloNumeros($_REQUEST["idUsuario"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario = '".$buscar_idUsuario."'";
    }
    // Optimizar filtros de zona/regional con subconsultas
    // Priorizar parámetros de REQUEST sobre sesión, pero solo si no están vacíos
    if(isset($_REQUEST["empresa_sitio_cor"]) && $_REQUEST["empresa_sitio_cor"] != "") {
        $buscar_zona = soloNumeros($_REQUEST["empresa_sitio_cor"]);
        if($buscar_zona != "") {
            $sqlFiltro .= " AND sat_reportes.idUsuario IN (SELECT UE.idUsuario FROM usuario_empresa UE LEFT JOIN categorias C ON C.id = UE.empresa_pd WHERE C.idSec = '".$buscar_zona."')";
        }
    } else if (!isset($_REQUEST["empresa_sitio_cor"]) && $_SESSION["id_zona"]!="" && $_SESSION["id_zona"]!=0) {
        $sqlFiltro .= " AND sat_reportes.idUsuario IN (SELECT UE.idUsuario FROM usuario_empresa UE LEFT JOIN categorias C ON C.id = UE.empresa_pd WHERE C.idSec = '".$_SESSION["id_zona"]."')";
        $_REQUEST["empresa_sitio_cor"] = $_SESSION["id_zona"];
        $buscar_zona = $_SESSION["id_zona"];
    }
    
    // Priorizar parámetros de REQUEST sobre sesión para empresa_pd, pero solo si no están vacíos
    if(isset($_REQUEST["empresa_pd"]) && $_REQUEST["empresa_pd"] != "") {
        $buscar_regional = soloNumeros($_REQUEST["empresa_pd"]);
        if($buscar_regional != "") {
            $sqlFiltro .= " AND sat_reportes.idUsuario IN (SELECT idUsuario FROM usuario_empresa WHERE empresa_pd = '".$buscar_regional."')";
        }
    } else if (!isset($_REQUEST["empresa_pd"]) && $_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
        $buscar_regional = soloNumeros($_SESSION["empresa_pd"]);
        $sqlFiltro .= " AND sat_reportes.idUsuario IN (SELECT idUsuario FROM usuario_empresa WHERE empresa_pd = '".$_SESSION["empresa_pd"]."')";
        $_REQUEST["empresa_pd"] = $_SESSION["empresa_pd"];
    }
    if(isset($_REQUEST["sitioReunion"]) && soloNumeros($_REQUEST["sitioReunion"]) != ""){
        $buscar_prision = soloNumeros($_REQUEST["sitioReunion"]);
        $sqlFiltro .= " AND sat_reportes.sitioReunion = ".$buscar_prision."";
    } else {
        $buscar_prision = isset($_REQUEST["sitioReunion"]) ? $_REQUEST["sitioReunion"] : "";
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
    if(isset($_REQUEST["fechaInicial"]) && eliminarInvalidos($_REQUEST["fechaInicial"]) != ""){
        $fechaInicial = eliminarInvalidos($_REQUEST["fechaInicial"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte >= '".$fechaInicial."'";
    } else {
        $fechaInicial = $_REQUEST["fechaInicial"];
    }
    
    if(isset($_REQUEST["fechaFinal"]) && eliminarInvalidos($_REQUEST["fechaFinal"]) != ""){
        $fechaFinal = eliminarInvalidos($_REQUEST["fechaFinal"]);
        $sqlFiltro .= " AND sat_reportes.fechaReporte <= '".$fechaFinal."'";
    } else {
        $fechaFinal = $_REQUEST["fechaFinal"];
    }    
    
    // Conteo optimizado - consulta simple
    $sql = "SELECT count(DISTINCT sat_reportes.id) as conteo FROM sat_reportes WHERE sat_reportes.rep_tip = 308 ".$sqlFiltro;
    $PSN1->query($sql);
    $total_registros = 0;
    if($PSN1->num_rows() > 0){
        if($PSN1->next_record()){
            $total_registros = $PSN1->f('conteo');
        }
    }
    $total_paginas = ceil($total_registros / $registros);
    
    // Paso 1: Obtener solo los IDs necesarios para la página (RÁPIDO)
    $sql_ids = "SELECT sat_reportes.id FROM sat_reportes WHERE sat_reportes.rep_tip = 308 ".$sqlFiltro." ORDER BY sat_reportes.id DESC LIMIT ".$inicio.", ".$registros;
    $PSN_ids = new DBbase_Sql;
    $PSN_ids->query($sql_ids);
    $report_ids = [];
    while($PSN_ids->next_record()){
        $report_ids[] = $PSN_ids->f('id');
    } 

    // Paso 2: Solo si hay IDs, obtener los datos completos (RÁPIDO)
    if (count($report_ids) > 0) {
        $sql = "SELECT C.descripcion AS regional, sat_reportes.*, U.nombre as nombreUsuario, sat_grupos.nombre as nombreGrupo, tbl_adjuntos.adj_url 
        FROM sat_reportes 
        LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario 
        LEFT JOIN sat_grupos ON sat_grupos.id = sat_reportes.idGrupoMadre
        LEFT JOIN tbl_adjuntos ON sat_reportes.id = tbl_adjuntos.adj_rep_fk 
        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
        LEFT JOIN categorias AS C ON C.id = UE.empresa_pd
        LEFT JOIN categorias AS CA ON CA.id = C.idSec 
        WHERE sat_reportes.id IN (" . implode(',', $report_ids) . ") 
        GROUP BY sat_reportes.id
        ORDER BY sat_reportes.fechaReporte DESC";
        
        $PSN1->query($sql);
    } else {
        // No hay registros para mostrar
        $total_registros = 0;
    }

    ?><div class="container">

    <form name="form" id="form" method="get" class="form-horizontal">
        <input type="hidden" name="doc" value="consultar-sub-programa-ecc" />
        <div>
            <h3 class="alert alert-info text-center">CONSULTAR REPORTES - CAPACITAR Y MULTIPLICAR (C&M)</h3>
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
                    <?php echo($zona == "" && $_SESSION["perfil"]!=162)?'<option value="" selected >Todas la regionales</option>':"";
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
                <strong>Miembro de la regional:</strong>
                <select name="idUsuario" onchange="this.form.submit()" class="form-control">
                <?php
                if($_SESSION["perfil"] != 163){
                    ?><option value="">Ver todos</option><?php
                }
        
                /*
                *	TRAEMOS LOS USUARIOS
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
                <div class="col-sm-1" >
                    <br>
                    <input type="submit" value="Filtrar" class="btn btn-success" />
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
                <h3 class="text-center">RESULTADOS DE BUSQUEDA</h3>
                <h5><?php echo $total_registros; ?> Registros encontrados</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div style="overflow-x: auto;">
            <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-striped" style="font-size:12px">
                <thead>
                    <tr> 
                        <th width="120px">Regional</th>
                        <th>Prisión / Ubicación</th>
                        <th>Coordinador de prisión </th>
                        <th>Entrenador</th>
                        <th>Siervo facilitador</th>
                        <th width="80px">Fecha de reporte</th>
                        <th width="80px" title="Fecha de Inicio Confraternidad Restaurativa">Fecha de inicio</th>
                        <th width="30px" title="Asistencia total">Asistencia</th>
                        <th width="30px"  title="Decisiones para cristo">Decisiones</th>
                        <th>Bautizos</th>
                        <th width="80px">Último ingreso</th>
                        <!--<th>Foto/Bautizo</th>
                        <th>Foto/Grupo</th>-->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if($total_registros > 0){
                        $contador = 0;
                        while($PSN1->next_record()){
                            //Solo si no se ha modificado ya el formulario.
                            $id = $PSN1->f('id');
                            $plantador = $PSN1->f("plantador");
                            $rep_entr = $PSN1->f("rep_entr");
                            $fechaReporte = $PSN1->f("fechaReporte");
                            $fechaInicio = $PSN1->f("fechaInicio");        
                            $sitioReunion = $PSN1->f("sitioReunion");
                            $grupoMadre_txt = $PSN1->f("grupoMadre_txt");
                            $idGrupoMadre = $PSN1->f("idGrupoMadre");
                            $pabellon = $PSN1->f("pabellon");
                            $ciudad = $PSN1->f("ciudad");
                            $direccion = $PSN1->f("direccion");
                            $generacionNumero = intval($PSN1->f("generacionNumero"));
                            
                            $nombreUsuario = $PSN1->f("nombreUsuario");
                            $nombreGrupo = $PSN1->f("nombreGrupo");

                            $mapeo_comprometido = $PSN1->f("mapeo_comprometido");
                            $nombreGrupo_txt = $PSN1->f("nombreGrupo_txt");
                            $mapeo_fecha = $PSN1->f("mapeo_fecha");  

                            $mapeo_oracion = $PSN1->f("mapeo_oracion");  
                            $mapeo_companerismo = $PSN1->f("mapeo_companerismo");  
                            $mapeo_adoracion = $PSN1->f("mapeo_adoracion");  
                            $mapeo_biblia = $PSN1->f("mapeo_biblia");  
                            $mapeo_evangelizar = $PSN1->f("mapeo_evangelizar");  
                            $mapeo_cena = $PSN1->f("mapeo_cena");  
                            $mapeo_dar = $PSN1->f("mapeo_dar");  
                            $mapeo_bautizar = $PSN1->f("mapeo_bautizar");  
                            $mapeo_trabajadores = $PSN1->f("mapeo_trabajadores");  
                            

                            $ext1 = $PSN1->f("ext1");
                            $ext2 = $PSN1->f("ext2");
                            $ext3 = $PSN1->f("ext3");
                            

                            $asistencia_hom = $PSN1->f("asistencia_hom");
                            $asistencia_muj = $PSN1->f("asistencia_muj");
                            $asistencia_jov = $PSN1->f("asistencia_jov");
                            $asistencia_nin = $PSN1->f("asistencia_nin");

                            $bautizados = $PSN1->f("bautizados");

                            //Calculados:
                            $asistencia_total  = $PSN1->f("asistencia_total");
                            $discipulado  = $PSN1->f("discipulado");
                            $desiciones  = $PSN1->f("desiciones");
                            $preparandose  = $PSN1->f("preparandose");
                            $url_baut  = $PSN1->f("adj_nom");
                            $iglesias_reconocidas = $PSN1->f("iglesias_reconocidas");  
                            
                            ?><tr class='clickable-row' data-href='index.php?doc=gestionar-sub-programa-ecc&id=<?=$id; ?>' >
                                <!--<td><a href="index.php?doc=gestionar-sub-programa-ecc&id=<?=$id; ?>"><?=str_pad($id, 6, "0", STR_PAD_LEFT); ?></a></td>//-->
                                <?php if($sitioReunion != 0){
                                        $sql = "SELECT LOWER(D.departamento) AS departamento, M.municipio, C.descripcion AS regional, RU.reub_nom,RU.reub_dir ";
                                        $sql.=" FROM tbl_regional_ubicacion AS RU
                                        LEFT JOIN dane_municipios AS M ON M.id_municipio = RU.reub_mun_fk
                                        LEFT JOIN dane_departamentos AS D ON D.id_departamento = M.departamento_id
                                        LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk
                                         WHERE RU.reub_id = ".$sitioReunion;

                                        $PSN2->query($sql);
                                        $numero=$PSN2->num_rows();
                                        if($numero > 0){
                                            while($PSN2->next_record()){
                                                $departamento = $PSN2->f("departamento");
                                                $municipio = $PSN2->f("municipio");
                                                $regional = $PSN2->f("regional");
                                                $prision = $PSN2->f("reub_nom");
                                            }
                                        }
                                        
                                        
                                    }else{
                                        $sql = "SELECT M.municipio,LOWER(D.departamento) AS departamento ";
                                        $sql.=" FROM dane_municipios AS M
                                        LEFT JOIN dane_departamentos AS D ON D.id_departamento = M.departamento_id
                                         WHERE M.id_municipio = ".$ciudad;

                                        $PSN2->query($sql);
                                        $numero=$PSN2->num_rows();
                                        if($numero > 0){
                                            while($PSN2->next_record()){
                                                $regional = $PSN2->f("departamento");
                                                $prision = $PSN2->f("municipio");
                                            }
                                        }
                                        
                                    }?>
                                <td><?=$PSN1->f("regional"); ?></td>
                                <td><?=$prision." - ".$PSN1->f("direccion");?></td>
                                <td><a href="index.php?doc=gestionar-sub-programa-ecc&id=<?=$id; ?>"><?=$nombreUsuario; ?></a></td>
                                <td><?=$rep_entr; ?></td>
                                <td><?=$plantador; ?></td>
                                <td><?= date("d-m-Y", strtotime($fechaReporte)); ?></td>
                                    <td><?= date("d-m-Y", strtotime($fechaInicio)); ?></td>
                                <td><?=$asistencia_total; ?></td>
                                <td><?=$desiciones; ?></td>
                                <td><?php echo($bautizados>0)?($bautizados-1):$bautizados; ?></td>
                                <td><?= date("d-m-Y", strtotime($mapeo_fecha)); ?></td>
                                <!--<td><?=$iglesias_reconocidas; ?></td>//--->
                                
                                <!--<td align="center"><?php
                                if($ext2 != ""){
                                    ?><img src="images/png/thumb-up.png" width="20px" />
                                    <i class="fas fa-thumbs-up ico-lik"></i><?php
                                }else{
                                    ?><img src="images/png/thumb-down.png" width="20px" />
                                    <i class="fas fa-thumbs-down ico-dli"></i><?php                            
                                }
                                ?></td>-->
                                <!--<td align="center"><?php
                                if($url_baut != "" || $url_baut != null){
                                    ?><i class="fas fa-thumbs-up ico-lik"></i>
                                    <!--<img src="images/png/thumb-up.png" width="20px" />--><?php
                                }else{
                                    ?>
                                    <i class="fas fa-thumbs-down ico-dli"></i>
                                    <!--<img src="images/png/thumb-down.png" width="20px" />--><?php                            
                                }
                                ?></td>
                                <!--
                                <td align="center"><?php
                                if($generacionNumero == 0 || $generacionNumero == 77 || $generacionNumero == 8){
                                    if($ext3 != ""){
                                        ?><!--<img src="images/png/thumb-up.png" width="20px" />
                                        <i class="fas fa-thumbs-up ico-lik"></i><?php
                                    }else{
                                        ?><!--<img src="images/png/thumb-down.png" width="20px" />
                                        <i class="fas fa-thumbs-down ico-dli"></i><?php                            
                                    }
                                }
                                else{
                                    /*$total = $mapeo_oracion
                                            + $mapeo_companerismo
                                            + $mapeo_adoracion
                                            + $mapeo_biblia
                                            + $mapeo_evangelizar
                                            + $mapeo_cena
                                            + $mapeo_dar
                                            + $mapeo_bautizar
                                            + $mapeo_trabajadores;*/
                                    if($ext1 != "" || $ext2 != ""){
                                        ?><!--<img src="images/png/thumb-up.png" width="20px" />
                                    <i class="fas fa-thumbs-up ico-lik"></i><?php
                                    }else{
                                        ?><!--<img src="images/png/thumb-down.png" width="20px" />
                                        <i class="fas fa-thumbs-down ico-dli"></i>
                                    <?php                       
                                    }
                                }
                                ?></td>
                                -->
                                
                            </tr>
                            <?php
                            $contador++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>       
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

    <!--<br />
    <center>
    <a href="index.php?excelX=&doc=usuario_buscar&nombre=<?=$buscar_nombre; ?>&identificacion=<?=$buscar_identificacion; ?>&tipo=<?=$buscar_tipo; ?>&ctrl=<?=$ctrl; ?>" class="btn btn-info"><span class="glyphicon glyphicon-cloud-download"></span> DESCARGAR PARA EXCEL</a></center>//-->


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
