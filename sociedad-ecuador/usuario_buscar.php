<?php

if($_SESSION["perfil"] == 3 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 160)
{
	die("<h1>No esta autorizado para ver esta información</h1>");
}
/*
*	$PSN = new DBbase_Sql;
*/
// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN3 = new DBbase_Sql;
// Array que nos servira para ir llevando cuenta de los requerimientos.
if(isset($_GET["del"]))
{
	//$sql = 'delete from usuario where id = "'.$_GET["del"].'" and tipo != 4 and tipo != 5 and tipo != 1';
	//$ultimoQuery = $PSN1->query($sql);
	//$varExitoDEL = 1;
}
if(isset($_GET["del"])){
        $sql = 'DELETE FROM usuario WHERE id = "'.$_GET["del"].'" and tipo != 4 and tipo != 5 and tipo != 1';
        $ultimoQuery = $PSN1->query($sql);
        $varExitoDEL = 1;
    }  

/*
*   AFECTA FORMULARIO Y ACTUAR DE LA PÁGINA
    1   USUARIO INTERNO
    2   CLIENTE
    3   PROVEEDOR
    4   USUARIO CLIENTE
*/
$ctrl = "";
if(!isset($_REQUEST["ctrl"]) || soloNumeros($_REQUEST["ctrl"]) == "" || soloNumeros($_REQUEST["ctrl"]) == "0"){
    $ctrl = 1;
}else{
    $ctrl = soloNumeros($_REQUEST["ctrl"]);
}

/*
*   DETECTAMOS EL TIPO DE FORMULARIO QUE VAMOS A MOSTRAR.
*/
switch($ctrl){
    case 1:
        $temp_tiposUsuario = "2, 162, 163, 164,165,166,167,168";
        $temp_letrero = "USUARIO INTERNO";
        break;
    case 2:
        $temp_tiposUsuario = "3";
        $temp_letrero = "CLIENTE";
        break;
    case 3:
        $temp_tiposUsuario = "4";
        $temp_letrero = "PROVEEDOR";
        break;
    case 4:
        $temp_tiposUsuario = "160";
        $temp_letrero = "AUTORIZADO DEL CLIENTE:";
        break;
    default:
        $temp_tiposUsuario = "2, 3, 4, 160, 161, 162, 163, 164";
        $temp_letrero = "TODOS LOS TIPOS";
        break;
}

/*
*   GENERAR EXCEL
*/
if(isset($_REQUEST["excelX"])){
    
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
    <table class="table table-striped">
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
            <th>Empresa - P&aacute;gina Web</th><?php
        }
    

        if($ctrl == "" || $ctrl == 1 || $ctrl == 4){
            ?><th>Empresa - Cargo</th>
            <th>Empresa - Tel&eacute;fono 1</th>
            <th>Empresa - Tel&eacute;fono 2</th>
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
            <th>Cliente - D&iacute;a de pago</th>
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

                ?><tr <?php if($temp_acceso == 0){
                        echo "class='danger' ";
                    } ?> <?php if($contador%2==0){ ?>bgcolor="#EEEEEE"<?php } ?>>
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
    *	TRAEMOS LOS colegioS.
    */
    $sql = "SELECT count(DISTINCT usuario.id) as conteo ";
    $sql .= " FROM usuario ";
    $sql .= " LEFT JOIN usuario_relacion ON usuario_relacion.idUsuario1 = usuario.id ";
    $sql .= " LEFT JOIN usuario as cliente ON cliente.id = usuario_relacion.idUsuario2 AND cliente.tipo = 3 
    LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = usuario.id
LEFT JOIN categorias AS C ON C.id = UE.empresa_pd 
LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
    $sql .= " WHERE usuario.id != 2 ";
    //
    $sqlFiltro .= " AND usuario.tipo IN (".$temp_tiposUsuario.")";

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
    if(isset($_REQUEST["empresa_sitio_cor"]) && soloNumeros($_REQUEST["empresa_sitio_cor"]) != ""){
        $buscar_zona = soloNumeros($_REQUEST["empresa_sitio_cor"]);
        $sqlFiltro .= " AND C.idSec = '".$buscar_zona."'";
    }

    if(isset($_REQUEST["cliente"]) && eliminarInvalidos($_REQUEST["cliente"]) != ""){
        $buscar_cliente = eliminarInvalidos($_REQUEST["cliente"]);
        $sqlFiltro .= " AND cliente.id = '".$buscar_cliente."'";
    }
    if(isset($_REQUEST["empresa_pd"]) && soloNumeros($_REQUEST["empresa_pd"]) != ""){
        $buscar_regional = soloNumeros($_REQUEST["empresa_pd"]);
        $sqlFiltro .= " AND UE.empresa_pd = '".$buscar_regional."'";
    }
    //    
    $sql .= $sqlFiltro." ORDER BY usuario.tipo ASC, usuario.nombre ASC";


    $PSN1->query($sql);
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $total_registros = $PSN1->f('conteo');
        }
    }
    $total_paginas = ceil($total_registros / $registros); 

    if (($_SESSION["superusuario"] == 1) OR ($_SESSION["perfil"] == 164)) {
        $sql = "SELECT C.descripcion AS regional, usuario.*, categorias.descripcion, cliente.nombre as nomcliente FROM usuario";
        $sql .= " LEFT JOIN categorias ON categorias.id = usuario.tipo 
        LEFT JOIN usuario_relacion ON usuario_relacion.idUsuario1 = usuario.id 
        LEFT JOIN usuario as cliente ON cliente.id = usuario_relacion.idUsuario2 AND cliente.tipo = 3 
        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = usuario.id
        LEFT JOIN categorias AS C ON C.id = UE.empresa_pd 
        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
            //
            $sql.=" WHERE usuario.id != 2 ".$sqlFiltro." GROUP BY usuario.id ORDER BY usuario.tipo ASC, usuario.nombre ASC";
            $sql.= " LIMIT ".$inicio.", ".$registros;
            //
    } else {
        $sql = "SELECT C.descripcion AS regional, usuario.*, categorias.descripcion, cliente.nombre as nomcliente FROM usuario";
        $sql .= " LEFT JOIN categorias ON categorias.id = usuario.tipo 
        LEFT JOIN usuario_relacion ON usuario_relacion.idUsuario1 = usuario.id 
        LEFT JOIN usuario as cliente ON cliente.id = usuario_relacion.idUsuario2 AND cliente.tipo = 3 
        LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = usuario.id
        LEFT JOIN categorias AS C ON C.id = UE.empresa_pd 
        LEFT JOIN categorias AS CA ON CA.id = C.idSec ";
            //
            $sql.=" WHERE usuario.id = ".$_SESSION["id"]." ".$sqlFiltro." GROUP BY usuario.id ORDER BY usuario.tipo ASC, usuario.nombre ASC";
            $sql.= " LIMIT ".$inicio.", ".$registros;
            //
    }
    $PSN1->query($sql);
    //echo $sql;
    $numero=$PSN1->num_rows();

    ?><div class="container">

    <form name="form" id="form" method="get" class="form-horizontal">
    <input type="hidden" name="doc" value="usuario_buscar" />
    <input type="hidden" name="ctrl" value="<?=$ctrl; ?>" />

        <div class="row">
            <h3 class="alert alert-info text-center">CONSULTA DE USUARIOS</h3>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">FILTRO DE BUSQUEDA</h3>
                <h5>de usuarios</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-1"></div>
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
                    $sql.=" WHERE idSec = 85 ORDER BY descripcion asc";


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
                    <?php echo($zona == "")?'<option value="" selected >Todas la regionales</option>':"";
                    $sql = "SELECT C.id, C.descripcion AS regional, CA.descripcion AS zona FROM categorias AS C";
                    $sql.=" LEFT JOIN categorias AS CA ON CA.id = C.idSec
                    WHERE CA.idSec = 85 ";
                    if (!empty($buscar_zona)) {
                        $sql.=" AND CA.id = ".$buscar_zona;
                    }
                    if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                        $sql.=" AND CA.id = ".$_SESSION["empresa_pd"];
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
                <strong>Tipo de usuario:</strong>
            <?php
            ?><select name="tipo" onchange="this.form.submit()" class="form-control">
            <?php
            if($ctrl != 3 && $ctrl != 4 && $ctrl != 2){
                ?><option value="">Ver todos</option><?php
            }
    
            /*
            *	TRAEMOS LOS TIPOS DE USUARIO
            */
            $sql = "SELECT * ";
            $sql.=" FROM categorias ";
            $sql.=" WHERE idSec = 1 AND id IN (".$temp_tiposUsuario.") ORDER BY descripcion asc";

            $PSN2->query($sql);
            $numero=$PSN2->num_rows();
            if($numero > 0)
            {
                while($PSN2->next_record())
                {
                    ?><option value="<?=$PSN2->f('id'); ?>" <?php
                    if($buscar_tipo == $PSN2->f('id'))
                    {
                        ?>selected="selected"<?php
                    }
                    ?>><?=$PSN2->f('descripcion'); ?></option><?php
                }
            }
            ?></select>
            </div>
        <div class="col-sm-3">
            <strong>Nombre:</strong></label>
            <input type="text" name="nombre" id="nombre" value="<?=$buscar_nombre; ?>" class="form-control" />
        </div>
        <div class="col-sm-1">
            <br>
            <input type="submit" value="Buscar" class="btn btn-success" />
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
    .table tr td{
        vertical-align: middle !important;
    }
    .table .btn{
        color: #fff;
    }

    </style>


    <div class="container">
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">RESULTADOS DE BUSQUEDA</h3>
                <h5><?php echo $total_registros; ?> Registros encontrados</h5>
                <!--<p>Tipo de usuario: <?=$temp_letrero; ?></p>-->
            </div>
            <div class="hr"><hr></div>
        </div>
    <table border="0" cellspacing="0" cellpadding="2"  align="center" class="table table-striped">
        <thead>
            <tr> 
                <th>Foto</th>
                <th>Id</th>
                <th>Tipo de usuario</th>
                <th>Regional</th>
                <th>Activo</th>
                <?php
                if($ctrl == "" || $ctrl == 4){
                    ?><th>Autorizado del cliente:</th><?php
                }
                ?>
                <th>Nombre</th>
                <th>Identificación</th>
                <th>Celular</th>
                <th>URL</th>
                <th>E-Mail</th>
                <th>Opciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($total_registros > 0)
            {
                $contador = 0;
                while($PSN1->next_record())
                {
                    //Solo si no se ha modificado ya el formulario.
                    $id = $PSN1->f('id');
                    $nomcliente = $PSN1->f('nomcliente');
                    $tipodesc = $PSN1->f('descripcion');
                    $regional = $PSN1->f('regional');
                    $nombre = $PSN1->f('nombre');
                    $identificacion = $PSN1->f('identificacion');
                    $telefono1 = $PSN1->f('telefono1');
                    $celular = $PSN1->f('celular');
                    $email = $PSN1->f('email');
                    $url = $PSN1->f('url');
                    $temp_acceso = $PSN1->f('acceso');

                    ?><tr <?php if($temp_acceso == 0){
                        echo " class='danger' ";
                    } ?> class='clickable-row' data-href='index.php?doc=usuario&id=<?=$id; ?>'>
                       
                        <td><?php
                        if(file_exists("images/usuarios/thumbs/".$id.".jpg"))
                        {
                            ?><div class="cont-img"><img src="images/usuarios/thumbs/<?=$id;?>.jpg" align="middle"></div><?php
                        }
                        else
                        {
                            ?><div class="cont-img"><img src="images/consultores/desconocido.jpg" align="middle"></div><?php
                        }
                        ?></td>
                         <td><a href="index.php?doc=usuario&id=<?=$id; ?>"><?=str_pad($id, 6, "0", STR_PAD_LEFT); ?></a></td>
                         <td><?=$regional; ?></td>
                        <td><a href="index.php?doc=usuario&id=<?=$id; ?>"><?=$tipodesc; ?></td>
                        <td><a href="index.php?doc=usuario&id=<?=$id; ?>"><?php if($temp_acceso == 1){
                            echo "Si";
                        }else{
                            echo "No";
                        } ?></a></td>
                        <?php
                        if($ctrl == "" || $ctrl == 4){
                            ?><th><a href="index.php?doc=usuario&id=<?=$id; ?>"><?=$nomcliente; ?></a></th><?php
                        }
                        ?>
                        <td><a href="index.php?doc=usuario&id=<?=$id; ?>"><?=$nombre; ?></a></td>
                        <td><a href="index.php?doc=usuario&id=<?=$id; ?>"><?=$identificacion; ?></a></td>
                        <td><a href="tel:<?=$celular; ?>"><?=$celular; ?></a></td>
<? /*                        <td><a href="tel:031<?=$telefono1; ?>"><?=$telefono1; ?></a></td> */ ?>
                        <td><a href="index.php?doc=usuario&id=<?=$id; ?>"><?=$url; ?></a></td>
                        <td><?=$email; ?></td>  
                        <td>
                            <a class="btn btn-danger" href="javascript:borrarRegistro('<?=$id; ?>');void(0);"><i class="far fa-trash-alt"></i> Eliminar</a>
                        </td>
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
    <a href="index.php?excelX=&doc=usuario_buscar&nombre=<?=$buscar_nombre; ?>&identificacion=<?=$buscar_identificacion; ?>&tipo=<?=$buscar_tipo; ?>&ctrl=<?=$ctrl; ?>" class="btn btn-info"><span class="glyphicon glyphicon-cloud-download"></span> DESCARGAR PARA EXCEL</a></center>

<?php /* $sql = "SELECT id ";
    $sql.=" FROM usuario ";


    $PSN2->query($sql);
    $numero=$PSN2->num_rows();
    if($numero > 0)
    {
        echo "INSERT INTO usuarios_menu_graphs (idUsuario,idMenu) VALUES ";
        while($PSN2->next_record())
        { 
            echo "(".$PSN2->f('id').", 1),";
            echo "(".$PSN2->f('id').", 4),";
            echo "(".$PSN2->f('id').", 9),";
            echo "(".$PSN2->f('id').", 11),";
            echo "(".$PSN2->f('id').", 13),";
        }
    }*/?>
    <script language="javascript">
    function borrarRegistro(registro){
            if(confirm("Esta accion BORRARA el USUARIO de el sistema, ¿esta seguro que desea continuar?"))
            {
                if(confirm("Recuerde que si el USUARIO tiene Ordenes de Servicio activas esto puede causar perdida de integridad en los datos, esta seguro que desea eliminar este USUARIO?"))
                {
                    window.location.href = "index.php?doc=usuario_buscar&ctrl="+<?=$ctrl; ?>+"&del="+registro;
                }
            }
    }
    function init(){
        <?php
        if($varExitoDEL == 1){?>
            alert("Usuario eliminado correctamente");
            window.location.href = "index.php?doc=usuario_buscar&ctrl="+<?=$ctrl; ?>;
        <?php }?>
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