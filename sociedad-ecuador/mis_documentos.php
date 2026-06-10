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
if($idUsuarioActual == 1 && isset($_POST["del_sistema"]) && soloNumeros($_POST["del_sistema"]) != ""){
    $idDel = soloNumeros($_POST["del_sistema"]);
    $PSN->query("SELECT archivo FROM sistema_documentos WHERE id = '".$idDel."'");
    if($PSN->num_rows() > 0 && $PSN->next_record()){
        $archivoEliminar = $PSN->f("archivo");
        if($archivoEliminar != ""){
            if(file_exists("archivos/sistema/".$archivoEliminar)){
                unlink("archivos/sistema/".$archivoEliminar);
            } else if(file_exists("archivos/usuarios/".$archivoEliminar)){
                unlink("archivos/usuarios/".$archivoEliminar);
            }
        }
    }
    $PSN->query("DELETE FROM sistema_documentos WHERE id = '".$idDel."'");
    $msg_sistema = "ok_delete";
}

// -- SUBIR documento del sistema (endpoint AJAX) --
if($idUsuarioActual == 1 && isset($_POST["subir_sistema_ajax"])){
    @ini_set('upload_max_filesize', '500M');
    @ini_set('post_max_size',       '500M');
    @ini_set('max_execution_time',  '600');
    @ini_set('max_input_time',      '600');

    // Limpiar cualquier output que el layout padre haya acumulado
    while(ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');
    $respuesta = array("ok" => false, "msg" => "");

    $descripcionDoc = htmlspecialchars(trim($_POST["descripcion_sistema"]));
    if($descripcionDoc == ""){
        $respuesta["msg"] = "err_desc";
    } else if(!isset($_FILES["archivo_sistema"]) || $_FILES["archivo_sistema"]["error"] != 0){
        $respuesta["msg"] = "err_file";
        $respuesta["php_error"] = $_FILES["archivo_sistema"]["error"] ?? -1;
    } else {
        $extPermitidas  = array("pdf","doc","docx","xls","xlsx","mp4","avi","mov","webm");
        $nombreOriginal = $_FILES["archivo_sistema"]["name"];
        $extension      = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if(!in_array($extension, $extPermitidas)){
            $respuesta["msg"] = "err_ext";
        } else {
            if(!is_dir("archivos/sistema/")) mkdir("archivos/sistema/", 0755, true);
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
                $respuesta["ok"]  = true;
                $respuesta["msg"] = "ok_upload";
            } else {
                $respuesta["msg"] = "err_move";
            }
        }
    }
    echo json_encode($respuesta);
    exit;
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
                    <form id="form-subir-sistema" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Descripción <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion_sistema" id="descripcion_sistema"
                                   class="form-control" placeholder="Ej: Manual de capacitadores 2024"
                                   maxlength="200" required>
                        </div>
                        <div class="form-group">
                            <label>Archivo <span class="text-danger">*</span></label>
                            <input type="file" name="archivo_sistema" id="archivo_sistema"
                                   class="form-control"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.mp4,.avi,.mov,.webm" required>
                            <small class="text-muted">
                                Formatos permitidos: PDF &nbsp;|&nbsp; Word (.doc, .docx) &nbsp;|&nbsp;
                                Excel (.xls, .xlsx) &nbsp;|&nbsp; Video (.mp4, .avi, .mov, .webm)
                            </small>
                        </div>

                        <!-- Barra de progreso (oculta hasta que empieza la subida) -->
                        <div id="bloque-progreso" style="display:none; margin-bottom:12px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                <small id="progreso-label" class="text-muted">Subiendo archivo...</small>
                                <small id="progreso-pct" class="text-muted">0%</small>
                            </div>
                            <div class="progress" style="margin-bottom:4px;">
                                <div id="barra-progreso" class="progress-bar progress-bar-striped active"
                                     role="progressbar" style="width:0%; min-width:18px; transition:width 0.2s;">
                                    &nbsp;
                                </div>
                            </div>
                            <small id="progreso-detalle" class="text-muted"></small>
                        </div>

                        <!-- Mensajes de resultado inline -->
                        <div id="msg-subida" style="margin-bottom:10px;"></div>

                        <button type="submit" id="btn-subir" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Subir documento
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-2"></div>
    </div>

    <script>
    (function(){
        var form     = document.getElementById('form-subir-sistema');
        var btnSubir = document.getElementById('btn-subir');
        var bloqueP  = document.getElementById('bloque-progreso');
        var barra    = document.getElementById('barra-progreso');
        var pct      = document.getElementById('progreso-pct');
        var label    = document.getElementById('progreso-label');
        var detalle  = document.getElementById('progreso-detalle');
        var msgDiv   = document.getElementById('msg-subida');

        function formatBytes(bytes){
            if(bytes < 1024*1024) return (bytes/1024).toFixed(1)+' KB';
            return (bytes/(1024*1024)).toFixed(1)+' MB';
        }

        function mostrarMsg(tipo, texto){
            var icono = tipo === 'success'
                ? '<i class="fas fa-check-circle"></i>'
                : '<i class="fas fa-exclamation-circle"></i>';
            msgDiv.innerHTML = '<div class="alert alert-'+tipo+'">'+icono+' '+texto+'</div>';
        }

        form.addEventListener('submit', function(e){
            e.preventDefault();

            var desc    = document.getElementById('descripcion_sistema').value.trim();
            var fileInp = document.getElementById('archivo_sistema');

            if(desc === ''){
                mostrarMsg('danger', 'Debe ingresar una descripción para el documento.');
                return;
            }
            if(!fileInp.files || fileInp.files.length === 0){
                mostrarMsg('danger', 'Debe seleccionar un archivo.');
                return;
            }

            var fd = new FormData();
            fd.append('subir_sistema_ajax', '1');
            fd.append('descripcion_sistema', desc);
            fd.append('archivo_sistema', fileInp.files[0]);

            var xhr = new XMLHttpRequest();
            var tamanio = fileInp.files[0].size;

            // -- Mostrar barra y deshabilitar botón --
            bloqueP.style.display = 'block';
            msgDiv.innerHTML      = '';
            btnSubir.disabled     = true;
            btnSubir.innerHTML    = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
            barra.style.width     = '0%';
            pct.textContent       = '0%';
            label.textContent     = 'Subiendo archivo...';
            detalle.textContent   = '';

            xhr.upload.addEventListener('progress', function(ev){
                if(ev.lengthComputable){
                    var porc = Math.round((ev.loaded / ev.total) * 100);
                    barra.style.width = porc + '%';
                    pct.textContent   = porc + '%';
                    detalle.textContent = formatBytes(ev.loaded) + ' de ' + formatBytes(ev.total);
                    if(porc === 100){
                        label.textContent     = 'Procesando en el servidor...';
                        barra.classList.add('progress-bar-striped', 'active');
                    }
                }
            });

            xhr.addEventListener('load', function(){
                btnSubir.disabled  = false;
                btnSubir.innerHTML = '<i class="fas fa-upload"></i> Subir documento';
                bloqueP.style.display = 'none';

                var mensajes = {
                    ok_upload : ['success', 'Documento subido correctamente.'],
                    err_desc  : ['danger',  'Debe ingresar una descripción para el documento.'],
                    err_file  : ['danger',  'Debe seleccionar un archivo válido.'],
                    err_ext   : ['danger',  'Tipo de archivo no permitido. Use PDF, Word, Excel o video (mp4, avi, mov, webm).'],
                    err_move  : ['danger',  'Error al guardar el archivo en el servidor. Verifique los permisos de la carpeta.']
                };

                try {
                    // Extraer solo el JSON aunque el servidor devuelva HTML extra alrededor
                    var raw  = xhr.responseText;
                    var ini  = raw.indexOf('{');
                    var fin  = raw.lastIndexOf('}');
                    var json = (ini !== -1 && fin !== -1) ? raw.substring(ini, fin + 1) : raw;
                    var resp = JSON.parse(json);
                    var info = mensajes[resp.msg] || ['danger', 'Respuesta inesperada del servidor.'];
                    mostrarMsg(info[0], info[1]);
                    if(resp.ok){
                        form.reset();
                        // Recargar la tabla de documentos tras 1.5s
                        setTimeout(function(){ location.reload(); }, 1500);
                    }
                } catch(err){
                    mostrarMsg('danger', 'Error al procesar la respuesta del servidor.');
                }
            });

            xhr.addEventListener('error', function(){
                btnSubir.disabled  = false;
                btnSubir.innerHTML = '<i class="fas fa-upload"></i> Subir documento';
                bloqueP.style.display = 'none';
                mostrarMsg('danger', 'Error de red al subir el archivo. Intente nuevamente.');
            });

            xhr.open('POST', '', true);
            xhr.send(fd);
        });
    })();
    </script>
    <?php } ?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <table class="table table-striped table-hover">
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
                                <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este documento del sistema?');">
                                    <input type="hidden" name="del_sistema" value="<?=$sdId; ?>">
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
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


<?php } ?>
</div>




