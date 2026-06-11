<?php
//Si es un usuario externo o cliente o proveedor NO mostrar.
if($_SESSION["perfil"] == 3 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 160)
{
	die("<h1>No esta autorizado para ver esta información</h1>");
}

// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN = new DBbase_Sql;
$webArchivo = "usuario";
    
/*
*   AFECTA FORMULARIO Y ACTUAR DE LA PÁGINA
    1   USUARIO INTERNO
    2   CLIENTE
    3   PROVEEDOR
    4   USUARIO CLIENTE
*/
if(!isset($_REQUEST["ctrl"]) || soloNumeros($_REQUEST["ctrl"]) == "" || soloNumeros($_REQUEST["ctrl"]) == "0"){
    $ctrl = 1;
}
else{
    $ctrl = soloNumeros($_REQUEST["ctrl"]);
}

// Array que nos servira para ir llevando cuenta de los requerimientos.
$arrayRequerimientos = array();

//  ID del usuario actual
$idUsuarioActual = soloNumeros($_SESSION["id"]);
    
if(isset($_REQUEST["deldoc"]) && $_REQUEST["deldoc_name"] != ""){
    unlink("archivos/usuarios/".$_REQUEST["deldoc_name"]);
    //
    if($_REQUEST["deldoc"] == "contrato"){
        $sql ="UPDATE usuario_documentos SET 
            documento_contrato = ''
            WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
    }
    else if($_REQUEST["deldoc"] == "constitucion"){
        $sql ="UPDATE usuario_documentos SET 
            documento_constitucion = ''
            WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
    }
    else if($_REQUEST["deldoc"] == "rut"){
        $sql ="UPDATE usuario_documentos SET 
            documento_rut = ''
            WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
    }
    else if($_REQUEST["deldoc"] == "identificacion"){
        $sql ="UPDATE usuario_documentos SET 
            documento_identificacion = ''
            WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
    }
    else if(soloNumeros($_REQUEST["deldoc"]) != "" && soloNumeros($_REQUEST["deldoc"]) != "0"){
        $sql ="DELETE FROM usuario_documentos_add 
                WHERE id = '".soloNumeros($_REQUEST["deldoc"])."' 
                AND idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
    }
}

/*
*	TRAEMOS LOS DATOS PRINCIPALES DEL USUARIO
*/
$sql = "SELECT usuario.*, cliente.id as idCliente, cliente.nombre as nomcliente ";
$sql.=" FROM usuario ";
$sql.=" LEFT JOIN usuario_relacion ON usuario_relacion.idUsuario1 = usuario.id ";
$sql.=" LEFT JOIN usuario as cliente ON cliente.id = usuario_relacion.idUsuario2 AND cliente.tipo = 3";
$sql.=" WHERE usuario.id = '".$idUsuarioActual."'";
$sql.=" GROUP BY usuario.id";
$PSN1->query($sql);
if($PSN1->num_rows() > 0)
{
    if($PSN1->next_record())
    {
        $general_nombre = $PSN1->f("nombre");
        $general_tipo = $PSN1->f("tipo");
        if($general_tipo == 3){
            $ctrl = 2;
        }
        else if($general_tipo == 4){
            $ctrl = 3;
        }
        else if($general_tipo == 160){
            $ctrl = 4;
            $idCliente = $PSN1->f("idCliente");
        }

        $general_tipo_user_cli = $PSN1->f("tipo_user_cli");
        //
        $general_identificacion = $PSN1->f("identificacion");
        $general_tipoIdentificacion = $PSN1->f("tipoIdentificacion");
        $general_direccion = $PSN1->f("direccion"); 
        $general_telefono1 = $PSN1->f("telefono1");
        $general_telefono2 = $PSN1->f("telefono2");
        $general_celular = $PSN1->f("celular");
        $general_celular2 = $PSN1->f("celular2");
        $general_email = $PSN1->f("email");
        $general_url = $PSN1->f("url");
        $general_observaciones = $PSN1->f("observaciones");
        $general_password = $PSN1->f("password");
        $general_acceso = $PSN1->f("acceso");
        $general_acceso_graphs = $PSN1->f("acceso_graphs");

        /*
        *	TRAEMOS LOS DATOS EMPRESARIALES
        */
        $sql = "SELECT * ";
        $sql.=" FROM usuario_empresa ";
        $sql.=" WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
        if($PSN1->num_rows() > 0)
        {
            if($PSN1->next_record())
            {
                $empresa_tipo = $PSN1->f("empresa_tipo");
                $empresa_nombre = $PSN1->f("empresa_nombre");
                $empresa_nit = $PSN1->f("empresa_nit");
                $empresa_representante = $PSN1->f("empresa_representante");
                $empresa_contacto = $PSN1->f("empresa_contacto");
                $empresa_direccion = $PSN1->f("empresa_direccion");
                $empresa_url = $PSN1->f("empresa_url");
                $empresa_telefono1 = $PSN1->f("empresa_telefono1");
                $empresa_telefono2 = $PSN1->f("empresa_telefono2");
                $empresa_celular1 = $PSN1->f("empresa_celular1");
                $empresa_celular2 = $PSN1->f("empresa_celular2");
                $empresa_email1 = $PSN1->f("empresa_email1");
                $empresa_email2 = $PSN1->f("empresa_email2");
                $empresa_cargo = $PSN1->f("empresa_cargo");
                $empresa_aprobacion = $PSN1->f("empresa_aprobacion");

                    $empresa_pais = $PSN1->f("empresa_pais");
                    $empresa_socio = $PSN1->f("empresa_socio");
                    $empresa_proceso = $PSN1->f("empresa_proceso");
                    $empresa_pd = $PSN1->f("empresa_pd");
                    $empresa_sitio_cor = $PSN1->f("empresa_sitio_cor");
                    $empresa_sitio = $PSN1->f("empresa_sitio");
                    $empresa_rm = $PSN1->f("empresa_rm");
                    $empresa_circuito = $PSN1->f("empresa_circuito");


            }
        }

        /*
        *	TRAEMOS LOS DATOS DE PROVEEDOR
        */
        $sql = "SELECT * ";
        $sql.=" FROM usuario_servicios ";
        $sql.=" WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
        if($PSN1->num_rows() > 0)
        {
            if($PSN1->next_record())
            {
                $servicios_tipo1 = $PSN1->f("servicios_tipo1");
                $servicios_tipo2 = $PSN1->f("servicios_tipo2");
                $servicios_contrato1 = $PSN1->f("servicios_contrato1");
                $servicios_contrato2 = $PSN1->f("servicios_contrato2");
                $servicios_observaciones = $PSN1->f("servicios_observaciones");
                $servicios_fechaInicio = $PSN1->f("servicios_fechaInicio");
                $servicios_fechaFin = $PSN1->f("servicios_fechaFin");
                $servicios_tipoPersona = $PSN1->f("servicios_tipoPersona");
                $servicios_porcentaje = $PSN1->f("servicios_porcentaje");

            }
        }

        /*
        *	TRAEMOS LOS DATOS DE CLIENTE
        */
        $sql = "SELECT * ";
        $sql.=" FROM usuario_cliente ";
        $sql.=" WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
        if($PSN1->num_rows() > 0)
        {
            if($PSN1->next_record())
            {
                $cliente_tipo1 = $PSN1->f("cliente_tipo1");
                $cliente_servicio1 = $PSN1->f("cliente_servicio1");
                $cliente_observaciones = $PSN1->f("cliente_observaciones");
                $cliente_valor1 = $PSN1->f("cliente_valor1");
                $cliente_diaPago = $PSN1->f("cliente_diaPago");
                $cliente_fechaAprob = $PSN1->f("cliente_fechaAprob");
                $cliente_fechaAprobCont = $PSN1->f("cliente_fechaAprobCont");
                $cliente_fechaInicial = $PSN1->f("cliente_fechaInicial");
                $cliente_fechaFinal = $PSN1->f("cliente_fechaFinal");
                $cliente_tipoPersona = $PSN1->f("cliente_tipoPersona");
            }
        }

        /*
        *	TRAEMOS LOS DATOS DE DOCUMENTOS PRINCIPALES
        */
        $sql = "SELECT * ";
        $sql.=" FROM usuario_documentos ";
        $sql.=" WHERE idUsuario = '".$idUsuarioActual."'";
        $PSN1->query($sql);
        if($PSN1->num_rows() > 0)
        {
            if($PSN1->next_record())
            {
                $documento_identificacion = $PSN1->f("documento_identificacion");
                $documento_rut = $PSN1->f("documento_rut");
                $documento_constitucion = $PSN1->f("documento_constitucion");
                $documento_contrato = $PSN1->f("documento_contrato");
            }
        }


    }//chequear el registro
}//chequear el numero
?>
<div class="container">
    <?php
    if($idUsuarioActual  > 0){?>
    
        <div class="row">
            <h3 class="alert alert-info text-center">DOCUMENTOS CARGADOS</h3>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">DOCUMENTOS</h3>
                <h5>DEL SISTEMA</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <table class="table table-striped table-hover">
                    <tr>
                        <td>
                            <a href='archivos/usuarios/Manual_de_Uso-Sistema-de-gestion-integral-CCC.pdf' target="_blank"><i class="fas fa-file-pdf"></i> Manual de usuario - Sistema de gestion integral - CCC</a>
                        </td>
                    <tr>
                    </tr>
                        <td>
                            <a href='archivos/usuarios/Formato_Testimonio_LPP.docx' target="_blank"><i class="fas fa-file-word"></i> Formato testimonio LPP - Sistema de gestion integral - CCC</a>
                        </td>
                    </tr>
                    </tr>
                        <td>
                            <a href='archivos/usuarios/Formato_Narrativa_Proyecto_Felipe.docx' target="_blank"><i class="fas fa-file-word"></i> Formato narrativo Proyecto Felipe - Sistema de gestion integral - CCC</a>
                        </td>
                    </tr>
                    </tr>
                        <td>
                            <a href='archivos/usuarios/Formato_Hoja_de_Vida_Estudiantes.pdf' target="_blank"><i class="fas fa-file-pdf"></i> Formato hoja de vida Instituto Biblico - Sistema de gestion integral - CCC</a>
                        </td>
                    </tr>
                    </tr>
                        <td>
                            <a href='archivos/usuarios/FORMATO-TESTIMONIO-CAPACITAR-Y-MULTIPLICAR.pdf' target="_blank"><i class="fas fa-file-pdf"></i> Formato testimonio capacitar y multiplicar - CCC</a>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-2"></div>
        </div><br>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">DOCUMENTOS</h3>
                <h5>DEL USUARIO</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="row">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <table class="table table-striped table-hover">
                    <?php
                    if($documento_identificacion != ""){?>
                        <tr>
                            <td>
                                <a href='descarga_usuario.php?&archivo=<?=$documento_identificacion; ?>' target="_blank">Documento de Identificación</a>
                            </td>              
                        </tr><?php
                    }
                    if($documento_rut != ""){?>
                        <tr>
                            <td>
                                <a href='descarga_usuario.php?&archivo=<?=$documento_rut; ?>' target="_blank">RUT</a>
                            </td>
                        </tr><?php
                    }

                    if($documento_constitucion != ""){?>
                        <tr>
                            <td>
                                <a href='descarga_usuario.php?&archivo=<?=$documento_constitucion; ?>' target="_blank">Constitución</a>
                            </td>
                        </tr>
                    <?php }

                    if($documento_contrato != ""){?>
                        <tr>
                            <td>
                                <a href='descarga_usuario.php?&archivo=<?=$documento_contrato; ?>' target="_blank">Contrato</a>
                            </td>
                        </tr>
                    <?php }

                    /*
                    *	TRAEMOS LOS DOCUMENTOS ADICIONALES
                    */
                    $sql = "SELECT * ";
                    $sql.=" FROM usuario_documentos_add	 ";
                    $sql.=" WHERE idUsuario = '".$idUsuarioActual."' ORDER BY descripcion asc";
                    //
                    $PSN1->query($sql);
                    $numero=$PSN1->num_rows();
                    if($numero > 0){
                        while($PSN1->next_record()){?>
                            <tr>
                                <td>
                                    <a href='descarga_usuario.php?&archivo=<?=$PSN1->f('archivo'); ?>' target="_blank"><i class="fas fa-file-pdf"></i> <?=$PSN1->f('descripcion'); ?></a>
                                </td>
                            </tr>
                        <?php }
                    }else{?> 
                        <tr>
                            <td>
                                <div>
                                  <i class="far fa-file-alt"></i> No se encontraron archivos cargados en el sistema  
                                </div>
                            </td>
                        </tr>
                    <?php }
                    ?>            
                </table>
            </div>
            <div class="col-sm-2"></div>
        </div>
    <?php }
   ?>
</div>