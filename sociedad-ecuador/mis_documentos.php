<?php
//Si es un usuario externo o cliente o proveedor NO mostrar.
if($_SESSION["perfil"] == 3 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 160)
{
	die("<h1>No esta autorizado para ver esta información</h1>");
}

// Objeto de Base de Datos
$PSN1 = new DBbase_Sql;
$PSN  = new DBbase_Sql;
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

// ================================================================
//  GESTIÓN DE DOCUMENTOS DEL SISTEMA (solo usuario id = 1)
// ================================================================
$msg_sistema = "";

// -- ELIMINAR documento del sistema --
if($idUsuarioActual == 1 && isset($_GET["del_sistema"]) && soloNumeros($_GET["del_sistema"]) != ""){
    $idDel = soloNumeros($_GET["del_sistema"]);
    $PSN->query("SELECT archivo FROM sistema_documentos WHERE id = '".$idDel."'");
    if($PSN->num_rows() > 0 && $PSN->next_record()){
        $archivoEliminar = $PSN->f("archivo");
        if($archivoEliminar != "" && file_exists("archivos/sistema/".$archivoEliminar)){
            unlink("archivos/sistema/".$archivoEliminar);
        }
    }
    $PSN->query("DELETE FROM sistema_documentos WHERE id = '".$idDel."'");
    $msg_sistema = "ok_delete";
}

// -- SUBIR documento del sistema --
if($idUsuarioActual == 1 && isset($_POST["subir_sistema"])){
    $descripcionDoc = htmlspecialchars(trim($_POST["descripcion_sistema"]));

    if($descripcionDoc == ""){
        $msg_sistema = "err_desc";
    }
    else if(!isset($_FILES["archivo_sistema"]) || $_FILES["archivo_sistema"]["error"] != 0){
        $msg_sistema = "err_file";
    }
    else{
        $extPermitidas = array("pdf","doc","docx","xls","xlsx","mp4","avi","mov","webm");
        $nombreOriginal = $_FILES["archivo_sistema"]["name"];
        $extension      = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

        if(!in_array($extension, $extPermitidas)){
            $msg_sistema = "err_ext";
        }
        else{
            if(!is_dir("archivos/sistema/")){
                mkdir("archivos/sistema/", 0755, true);
            }
            $nombreGuardar = time()."_".preg_replace('/[^a-zA-Z0-9._\-]/', '_', $nombreOriginal);
            $rutaDest      = "archivos/sistema/".$nombreGuardar;

            if(move_uploaded_file($_FILES["archivo_sistema"]["tmp_name"], $rutaDest)){
                $PSN->query("INSERT INTO sistema_documentos (descripcion, archivo, extension, idUsuarioSubio, fecha)
                             VALUES (
                                 '".addslashes($descripcionDoc)."',
                                 '".addslashes($nombreGuardar)."',
                                 '".addslashes($extension)."',
                                 '".$idUsuarioActual."',
                                 NOW()
                             )");
                $msg_sistema = "ok_upload";
            }
            else{
                $msg_sistema = "err_move";
            }
        }
    }
}
// ================================================================
//  FIN GESTIÓN DOCUMENTOS DEL SISTEMA
// ================================================================

if(isset($_REQUEST["deldoc"]) && $_REQUEST["deldoc_name"] != ""){
    unlink("archivos/usuarios/".$_REQUEST["deldoc_name"]);
    if($_REQUEST["deldoc"] == "contrato"){
        $PSN1->query("UPDATE usuario_documentos SET documento_contrato = '' WHERE idUsuario = '".$idUsuarioActual."'");
    }
    else if($_REQUEST["deldoc"] == "constitucion"){
        $PSN1->query("UPDATE usuario_documentos SET documento_constitucion = '' WHERE idUsuario = '".$idUsuarioActual."'");
    }
    else if($_REQUEST["deldoc"] == "rut"){
        $PSN1->query("UPDATE usuario_documentos SET documento_rut = '' WHERE idUsuario = '".$idUsuarioActual."'");
    }
    else if($_REQUEST["deldoc"] == "identificacion"){
        $PSN1->query("UPDATE usuario_documentos SET documento_identificacion = '' WHERE idUsuario = '".$idUsuarioActual."'");
    }
    else if(soloNumeros($_REQUEST["deldoc"]) != "" && soloNumeros($_REQUEST["deldoc"]) != "0"){
        $PSN1->query("DELETE FROM usuario_documentos_add WHERE id = '".soloNumeros($_REQUEST["deldoc"])."' AND idUsuario = '".$idUsuarioActual."'");
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
        $general_tipo   = $PSN1->f("tipo");
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

        $general_tipo_user_cli    = $PSN1->f("tipo_user_cli");
        $general_identificacion   = $PSN1->f("identificacion");
        $general_tipoIdentificacion = $PSN1->f("tipoIdentificacion");
        $general_direccion        = $PSN1->f("direccion"); 
        $general_telefono1        = $PSN1->f("telefono1");
        $general_telefono2        = $PSN1->f("telefono2");
        $general_celular          = $PSN1->f("celular");
        $general_celular2         = $PSN1->f("celular2");
        $general_email            = $PSN1->f("email");
        $general_url              = $PSN1->f("url");
        $general_observaciones    = $PSN1->f("observaciones");
        $general_password         = $PSN1->f("password");
        $general_acceso           = $PSN1->f("acceso");
        $general_acceso_graphs    = $PSN1->f("acceso_graphs");

        /*  DATOS EMPRESARIALES  */
        $PSN1->query("SELECT * FROM usuario_empresa WHERE idUsuario = '".$idUsuarioActual."'");
        if($PSN1->num_rows() > 0 && $PSN1->next_record()){
            $empresa_tipo          = $PSN1->f("empresa_tipo");
            $empresa_nombre        = $PSN1->f("empresa_nombre");
            $empresa_nit           = $PSN1->f("empresa_nit");
            $empresa_representante = $PSN1->f("empresa_representante");
            $empresa_contacto      = $PSN1->f("empresa_contacto");
            $empresa_direccion     = $PSN1->f("empresa_direccion");
            $empresa_url           = $PSN1->f("empresa_url");
            $empresa_telefono1     = $PSN1->f("empresa_telefono1");
            $empresa_telefono2     = $PSN1->f("empresa_telefono2");
            $empresa_celular1      = $PSN1->f("empresa_celular1");
            $empresa_celular2      = $PSN1->f("empresa_celular2");
            $empresa_email1        = $PSN1->f("empresa_email1");
            $empresa_email2        = $PSN1->f("empresa_email2");
            $empresa_cargo         = $PSN1->f("empresa_cargo");
            $empresa_aprobacion    = $PSN1->f("empresa_aprobacion");
            $empresa_pais          = $PSN1->f("empresa_pais");
            $empresa_socio         = $PSN1->f("empresa_socio");
            $empresa_proceso       = $PSN1->f("empresa_proceso");
            $empresa_pd            = $PSN1->f("empresa_pd");
            $empresa_sitio_cor     = $PSN1->f("empresa_sitio_cor");
            $empresa_sitio         = $PSN1->f("empresa_sitio");
            $empresa_rm            = $PSN1->f("empresa_rm");
            $empresa_circuito      = $PSN1->f("empresa_circuito");
        }

        /*  DATOS DE PROVEEDOR  */
        $PSN1->query("SELECT * FROM usuario_servicios WHERE idUsuario = '".$idUsuarioActual."'");
        if($PSN1->num_rows() > 0 && $PSN1->next_record()){
            $servicios_tipo1          = $PSN1->f("servicios_tipo1");
            $servicios_tipo2          = $PSN1->f("servicios_tipo2");
            $servicios_contrato1      = $PSN1->f("servicios_contrato1");
            $servicios_contrato2      = $PSN1->f("servicios_contrato2");
            $servicios_observaciones  = $PSN1->f("servicios_observaciones");
            $servicios_fechaInicio    = $PSN1->f("servicios_fechaInicio");
            $servicios_fechaFin       = $PSN1->f("servicios_fechaFin");
            $servicios_tipoPersona    = $PSN1->f("servicios_tipoPersona");
            $servicios_porcentaje     = $PSN1->f("servicios_porcentaje");
        }

        /*  DATOS DE CLIENTE  */
        $PSN1->query("SELECT * FROM usuario_cliente WHERE idUsuario = '".$idUsuarioActual."'");
        if($PSN1->num_rows() > 0 && $PSN1->next_record()){
            $cliente_tipo1          = $PSN1->f("cliente_tipo1");
            $cliente_servicio1      = $PSN1->f("cliente_servicio1");
            $cliente_observaciones  = $PSN1->f("cliente_observaciones");
            $cliente_valor1         = $PSN1->f("cliente_valor1");
            $cliente_diaPago        = $PSN1->f("cliente_diaPago");
            $cliente_fechaAprob     = $PSN1->f("cliente_fechaAprob");
            $cliente_fechaAprobCont = $PSN1->f("cliente_fechaAprobCont");
            $cliente_fechaInicial   = $PSN1->f("cliente_fechaInicial");
            $cliente_fechaFinal     = $PSN1->f("cliente_fechaFinal");
            $cliente_tipoPersona    = $PSN1->f("cliente_tipoPersona");
        }

        /*  DOCUMENTOS PRINCIPALES DEL USUARIO  */
        $PSN1->query("SELECT * FROM usuario_documentos WHERE idUsuario = '".$idUsuarioActual."'");
        if($PSN1->num_rows() > 0 && $PSN1->next_record()){
            $documento_identificacion = $PSN1->f("documento_identificacion");
            $documento_rut            = $PSN1->f("documento_rut");
            $documento_constitucion   = $PSN1->f("documento_constitucion");
            $documento_contrato       = $PSN1->f("documento_contrato");
        }

    }//chequear el registro
}//chequear el numero

// ================================================================
//  Helper: ícono según extensión
// ================================================================
function iconoPorExtension($ext){
    $ext = strtolower($ext);
    if($ext == "pdf")                                    return '<i class="fas fa-file-pdf"   style="color:#c0392b;"></i>';
    if(in_array($ext, array("doc","docx")))              return '<i class="fas fa-file-word"  style="color:#2980b9;"></i>';
    if(in_array($ext, array("xls","xlsx")))              return '<i class="fas fa-file-excel" style="color:#27ae60;"></i>';
    if(in_array($ext, array("mp4","avi","mov","webm")))  return '<i class="fas fa-file-video" style="color:#8e44ad;"></i>';
    return '<i class="fas fa-file-alt"></i>';
}
?>

<div class="container">
<?php if($idUsuarioActual > 0){ ?>

    <div class="row">
        <h3 class="alert alert-info text-center">DOCUMENTOS CARGADOS</h3>
    </div>

    <!-- ======================================================== -->
    <!--  DOCUMENTOS DEL SISTEMA                                   -->
    <!-- ======================================================== -->
    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen">
            <h3 class="text-center">DOCUMENTOS</h3>
            <h5>DEL SISTEMA</h5>
        </div>
        <div class="hr"><hr></div>
    </div>

    <?php
    // Mensajes de estado
    if($msg_sistema == "ok_upload")  echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Documento subido correctamente.</div>';
    if($msg_sistema == "ok_delete")  echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Documento eliminado correctamente.</div>';
    if($msg_sistema == "err_desc")   echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Debe ingresar una descripción para el documento.</div>';
    if($msg_sistema == "err_file")   echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Debe seleccionar un archivo válido.</div>';
    if($msg_sistema == "err_ext")    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Tipo de archivo no permitido. Use PDF, Word, Excel o video (mp4, avi, mov, webm).</div>';
    if($msg_sistema == "err_move")   echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error al guardar el archivo en el servidor. Verifique los permisos de la carpeta.</div>';
    ?>

    <?php if($idUsuarioActual == 1){ ?>
    <!-- Formulario de subida: SOLO visible para usuario id = 1 -->
    <div class="row" style="margin-bottom:20px;">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <strong><i class="fas fa-upload"></i> Subir nuevo documento del sistema</strong>
                </div>
                <div class="panel-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion_sistema" class="form-control"
                                   placeholder="Ej: Manual de capacitadores 2024" maxlength="200" required>
                        </div>
                        <div class="form-group">
                            <label>Archivo <span class="text-danger">*</span></label>
                            <input type="file" name="archivo_sistema" class="form-control"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.mp4,.avi,.mov,.webm" required>
                            <small class="text-muted">
                                Formatos permitidos: PDF &nbsp;|&nbsp; Word (.doc, .docx) &nbsp;|&nbsp; Excel (.xls, .xlsx) &nbsp;|&nbsp; Video (.mp4, .avi, .mov, .webm)
                            </small>
                        </div>
                        <button type="submit" name="subir_sistema" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Subir documento
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <table class="table table-striped table-hover">
                <!-- Documentos fijos hardcodeados (se mantienen igual) -->
                <tr>
                    <td>
                        <a href='archivos/usuarios/Manual_de_Uso-Sistema-de-gestion-integral-CCC.pdf' target="_blank">
                            <i class="fas fa-file-pdf" style="color:#c0392b;"></i>
                            Manual de usuario - Sistema de gestion integral - CCC
                        </a>
                    </td>
                    <?php if($idUsuarioActual == 1){ ?><td style="width:50px;"></td><?php } ?>
                </tr>
                <tr>
                    <td>
                        <a href='archivos/usuarios/Video No 2 Reportes de Facilitadores.mp4' target="_blank">
                            <i class="fas fa-file-video" style="color:#8e44ad;"></i>
                            Video reporte facilitadores
                        </a>
                    </td>
                    <?php if($idUsuarioActual == 1){ ?><td style="width:50px;"></td><?php } ?>
                </tr>
                <tr>
                    <td>
                        <a href='archivos/usuarios/Video No 1 Reporte Cada Comunidad Capacitadores.mp4' target="_blank">
                            <i class="fas fa-file-video" style="color:#8e44ad;"></i>
                            Video reporte capacitadores
                        </a>
                    </td>
                    <?php if($idUsuarioActual == 1){ ?><td style="width:50px;"></td><?php } ?>
                </tr>

                <?php
                // Documentos subidos dinámicamente desde la plataforma
                $PSN->query("SELECT * FROM sistema_documentos ORDER BY fecha DESC");
                if($PSN->num_rows() > 0){
                    while($PSN->next_record()){
                        $sdId   = $PSN->f("id");
                        $sdDesc = htmlspecialchars($PSN->f("descripcion"));
                        $sdArch = $PSN->f("archivo");
                        $sdExt  = $PSN->f("extension");
                        ?>
                        <tr>
                            <td>
                                <a href='descarga_sistema.php?archivo=<?=urlencode($sdArch); ?>' target="_blank">
                                    <?=iconoPorExtension($sdExt); ?> <?=$sdDesc; ?>
                                </a>
                            </td>
                            <?php if($idUsuarioActual == 1){ ?>
                            <td style="width:50px; text-align:center;">
                                <a href='?del_sistema=<?=$sdId; ?>'
                                   onclick="return confirm('¿Eliminar este documento del sistema?');"
                                   class="btn btn-danger btn-xs" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
        </div>
        <div class="col-sm-2"></div>
    </div><br>

    <!-- ======================================================== -->
    <!--  DOCUMENTOS DEL USUARIO                                   -->
    <!-- ======================================================== -->
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

                /*  DOCUMENTOS ADICIONALES  */
                $PSN1->query("SELECT * FROM usuario_documentos_add WHERE idUsuario = '".$idUsuarioActual."' ORDER BY descripcion asc");
                $numero = $PSN1->num_rows();
                if($numero > 0){
                    while($PSN1->next_record()){?>
                        <tr>
                            <td>
                                <a href='descarga_usuario.php?&archivo=<?=$PSN1->f('archivo'); ?>' target="_blank">
                                    <i class="fas fa-file-pdf"></i> <?=$PSN1->f('descripcion'); ?>
                                </a>
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

<?php } ?>
</div>