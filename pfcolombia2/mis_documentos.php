<?php
//Si es un usuario externo o cliente o proveedor NO mostrar.
if($_SESSION["perfil"] == 3 || $_SESSION["perfil"] == 4 || $_SESSION["perfil"] == 160)
{
    die("<h1>No esta autorizado para ver esta informacion</h1>");
}

$PSN1 = new DBbase_Sql;
$PSN  = new DBbase_Sql;
$webArchivo = "usuario";

if(!isset($_REQUEST["ctrl"]) || soloNumeros($_REQUEST["ctrl"]) == "" || soloNumeros($_REQUEST["ctrl"]) == "0"){
    $ctrl = 1;
} else {
    $ctrl = soloNumeros($_REQUEST["ctrl"]);
}

$arrayRequerimientos = array();
$idUsuarioActual = soloNumeros($_SESSION["id"]);

// ================================================================
//  GESTION DE DOCUMENTOS DEL SISTEMA (solo usuario id = 1)
// ================================================================
$msg_sistema = "";

if($idUsuarioActual == 1 && isset($_POST["del_sistema"]) && soloNumeros($_POST["del_sistema"]) != ""){
    $idDel = soloNumeros($_POST["del_sistema"]);
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

if($idUsuarioActual == 1 && isset($_POST["subir_sistema_ajax"])){
    @ini_set('upload_max_filesize', '500M');
    @ini_set('post_max_size',       '500M');
    @ini_set('max_execution_time',  '600');
    @ini_set('max_input_time',      '600');
    while(ob_get_level()) ob_end_clean();
    header('Content-Type: application/json');
    $respuesta = array("ok" => false, "msg" => "");
    $descripcionDoc = htmlspecialchars(trim($_POST["descripcion_sistema"]));
    if($descripcionDoc == ""){
        $respuesta["msg"] = "err_desc";
    } else if(!isset($_FILES["archivo_sistema"]) || $_FILES["archivo_sistema"]["error"] != 0){
        $respuesta["msg"] = "err_file";
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

// Eliminar documentos del usuario
if(isset($_REQUEST["deldoc"]) && $_REQUEST["deldoc_name"] != ""){
    unlink("archivos/usuarios/".$_REQUEST["deldoc_name"]);
    if($_REQUEST["deldoc"] == "contrato"){
        $PSN1->query("UPDATE usuario_documentos SET documento_contrato = '' WHERE idUsuario = '".$idUsuarioActual."'");
    } else if($_REQUEST["deldoc"] == "constitucion"){
        $PSN1->query("UPDATE usuario_documentos SET documento_constitucion = '' WHERE idUsuario = '".$idUsuarioActual."'");
    } else if($_REQUEST["deldoc"] == "rut"){
        $PSN1->query("UPDATE usuario_documentos SET documento_rut = '' WHERE idUsuario = '".$idUsuarioActual."'");
    } else if($_REQUEST["deldoc"] == "identificacion"){
        $PSN1->query("UPDATE usuario_documentos SET documento_identificacion = '' WHERE idUsuario = '".$idUsuarioActual."'");
    } else if(soloNumeros($_REQUEST["deldoc"]) != "" && soloNumeros($_REQUEST["deldoc"]) != "0"){
        $PSN1->query("DELETE FROM usuario_documentos_add WHERE id = '".soloNumeros($_REQUEST["deldoc"])."' AND idUsuario = '".$idUsuarioActual."'");
    }
}

// Datos principales del usuario
$sql = "SELECT usuario.*, cliente.id as idCliente, cliente.nombre as nomcliente ";
$sql.=" FROM usuario ";
$sql.=" LEFT JOIN usuario_relacion ON usuario_relacion.idUsuario1 = usuario.id ";
$sql.=" LEFT JOIN usuario as cliente ON cliente.id = usuario_relacion.idUsuario2 AND cliente.tipo = 3";
$sql.=" WHERE usuario.id = '".$idUsuarioActual."' GROUP BY usuario.id";
$PSN1->query($sql);
if($PSN1->num_rows() > 0 && $PSN1->next_record()){
    $general_nombre = $PSN1->f("nombre");
    $general_tipo   = $PSN1->f("tipo");
    if($general_tipo == 3)        { $ctrl = 2; }
    else if($general_tipo == 4)   { $ctrl = 3; }
    else if($general_tipo == 160) { $ctrl = 4; $idCliente = $PSN1->f("idCliente"); }

    $general_tipo_user_cli      = $PSN1->f("tipo_user_cli");
    $general_identificacion     = $PSN1->f("identificacion");
    $general_tipoIdentificacion = $PSN1->f("tipoIdentificacion");
    $general_direccion          = $PSN1->f("direccion");
    $general_telefono1          = $PSN1->f("telefono1");
    $general_telefono2          = $PSN1->f("telefono2");
    $general_celular            = $PSN1->f("celular");
    $general_celular2           = $PSN1->f("celular2");
    $general_email              = $PSN1->f("email");
    $general_url                = $PSN1->f("url");
    $general_observaciones      = $PSN1->f("observaciones");
    $general_password           = $PSN1->f("password");
    $general_acceso             = $PSN1->f("acceso");
    $general_acceso_graphs      = $PSN1->f("acceso_graphs");

    $PSN1->query("SELECT * FROM usuario_empresa WHERE idUsuario = '".$idUsuarioActual."'");
    if($PSN1->num_rows() > 0 && $PSN1->next_record()){
        $empresa_tipo=$PSN1->f("empresa_tipo"); $empresa_nombre=$PSN1->f("empresa_nombre");
        $empresa_nit=$PSN1->f("empresa_nit"); $empresa_representante=$PSN1->f("empresa_representante");
        $empresa_contacto=$PSN1->f("empresa_contacto"); $empresa_direccion=$PSN1->f("empresa_direccion");
        $empresa_url=$PSN1->f("empresa_url"); $empresa_telefono1=$PSN1->f("empresa_telefono1");
        $empresa_telefono2=$PSN1->f("empresa_telefono2"); $empresa_celular1=$PSN1->f("empresa_celular1");
        $empresa_celular2=$PSN1->f("empresa_celular2"); $empresa_email1=$PSN1->f("empresa_email1");
        $empresa_email2=$PSN1->f("empresa_email2"); $empresa_cargo=$PSN1->f("empresa_cargo");
        $empresa_aprobacion=$PSN1->f("empresa_aprobacion"); $empresa_pais=$PSN1->f("empresa_pais");
        $empresa_socio=$PSN1->f("empresa_socio"); $empresa_proceso=$PSN1->f("empresa_proceso");
        $empresa_pd=$PSN1->f("empresa_pd"); $empresa_sitio_cor=$PSN1->f("empresa_sitio_cor");
        $empresa_sitio=$PSN1->f("empresa_sitio"); $empresa_rm=$PSN1->f("empresa_rm");
        $empresa_circuito=$PSN1->f("empresa_circuito");
    }

    $PSN1->query("SELECT * FROM usuario_servicios WHERE idUsuario = '".$idUsuarioActual."'");
    if($PSN1->num_rows() > 0 && $PSN1->next_record()){
        $servicios_tipo1=$PSN1->f("servicios_tipo1"); $servicios_tipo2=$PSN1->f("servicios_tipo2");
        $servicios_contrato1=$PSN1->f("servicios_contrato1"); $servicios_contrato2=$PSN1->f("servicios_contrato2");
        $servicios_observaciones=$PSN1->f("servicios_observaciones"); $servicios_fechaInicio=$PSN1->f("servicios_fechaInicio");
        $servicios_fechaFin=$PSN1->f("servicios_fechaFin"); $servicios_tipoPersona=$PSN1->f("servicios_tipoPersona");
        $servicios_porcentaje=$PSN1->f("servicios_porcentaje");
    }

    $PSN1->query("SELECT * FROM usuario_cliente WHERE idUsuario = '".$idUsuarioActual."'");
    if($PSN1->num_rows() > 0 && $PSN1->next_record()){
        $cliente_tipo1=$PSN1->f("cliente_tipo1"); $cliente_servicio1=$PSN1->f("cliente_servicio1");
        $cliente_observaciones=$PSN1->f("cliente_observaciones"); $cliente_valor1=$PSN1->f("cliente_valor1");
        $cliente_diaPago=$PSN1->f("cliente_diaPago"); $cliente_fechaAprob=$PSN1->f("cliente_fechaAprob");
        $cliente_fechaAprobCont=$PSN1->f("cliente_fechaAprobCont"); $cliente_fechaInicial=$PSN1->f("cliente_fechaInicial");
        $cliente_fechaFinal=$PSN1->f("cliente_fechaFinal"); $cliente_tipoPersona=$PSN1->f("cliente_tipoPersona");
    }

    $PSN1->query("SELECT * FROM usuario_documentos WHERE idUsuario = '".$idUsuarioActual."'");
    if($PSN1->num_rows() > 0 && $PSN1->next_record()){
        $documento_identificacion=$PSN1->f("documento_identificacion");
        $documento_rut=$PSN1->f("documento_rut");
        $documento_constitucion=$PSN1->f("documento_constitucion");
        $documento_contrato=$PSN1->f("documento_contrato");
    }
}

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
        <h3 class="alert alert-info text-center">TUTORIALES / VIDEOS</h3>
    </div>

    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen"><h3 class="text-center">DOCUMENTOS</h3><h5>DEL SISTEMA</h5></div>
        <div class="hr"><hr></div>
    </div>

    <?php
    if($msg_sistema == "ok_upload") echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Documento subido correctamente.</div>';
    if($msg_sistema == "ok_delete") echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Documento eliminado correctamente.</div>';
    if($msg_sistema == "err_desc")  echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Debe ingresar una descripcion para el documento.</div>';
    if($msg_sistema == "err_file")  echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Debe seleccionar un archivo valido.</div>';
    if($msg_sistema == "err_ext")   echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Tipo de archivo no permitido. Use PDF, Word, Excel o video (mp4, avi, mov, webm).</div>';
    if($msg_sistema == "err_move")  echo '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Error al guardar el archivo. Verifique los permisos de la carpeta archivos/sistema/.</div>';
    ?>

    <?php if($idUsuarioActual == 1){ ?>
    <div class="row" style="margin-bottom:20px;">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <strong><i class="fas fa-upload"></i> Subir nuevo documento / tutorial / video</strong>
                </div>
                <div class="panel-body">
                    <form id="form-subir-sistema" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Descripcion <span class="text-danger">*</span></label>
                            <input type="text" name="descripcion_sistema" id="descripcion_sistema"
                                   class="form-control" placeholder="Ej: Tutorial de ingreso al sistema"
                                   maxlength="200" required>
                        </div>
                        <div class="form-group">
                            <label>Archivo <span class="text-danger">*</span></label>
                            <input type="file" name="archivo_sistema" id="archivo_sistema"
                                   class="form-control"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.mp4,.avi,.mov,.webm" required>
                            <small class="text-muted">
                                Formatos: PDF | Word (.doc, .docx) | Excel (.xls, .xlsx) | Video (.mp4, .avi, .mov, .webm)
                            </small>
                        </div>
                        <div id="bloque-progreso" style="display:none; margin-bottom:12px;">
                            <div style="display:flex; justify-content:space-between; margin-bottom:4px;">
                                <small id="progreso-label" class="text-muted">Subiendo archivo...</small>
                                <small id="progreso-pct" class="text-muted">0%</small>
                            </div>
                            <div class="progress" style="margin-bottom:4px;">
                                <div id="barra-progreso" class="progress-bar progress-bar-striped active"
                                     role="progressbar" style="width:0%; min-width:18px; transition:width 0.2s;">&nbsp;</div>
                            </div>
                            <small id="progreso-detalle" class="text-muted"></small>
                        </div>
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
        var form=document.getElementById('form-subir-sistema'),btnSubir=document.getElementById('btn-subir'),
            bloqueP=document.getElementById('bloque-progreso'),barra=document.getElementById('barra-progreso'),
            pct=document.getElementById('progreso-pct'),label=document.getElementById('progreso-label'),
            detalle=document.getElementById('progreso-detalle'),msgDiv=document.getElementById('msg-subida');
        function formatBytes(b){ return b<1048576?(b/1024).toFixed(1)+' KB':(b/1048576).toFixed(1)+' MB'; }
        function mostrarMsg(t,x){
            var i=t==='success'?'<i class="fas fa-check-circle"></i>':'<i class="fas fa-exclamation-circle"></i>';
            msgDiv.innerHTML='<div class="alert alert-'+t+'">'+i+' '+x+'</div>';
        }
        form.addEventListener('submit',function(e){
            e.preventDefault();
            var desc=document.getElementById('descripcion_sistema').value.trim();
            var fi=document.getElementById('archivo_sistema');
            if(!desc){ mostrarMsg('danger','Debe ingresar una descripcion.'); return; }
            if(!fi.files||fi.files.length===0){ mostrarMsg('danger','Debe seleccionar un archivo.'); return; }
            var fd=new FormData();
            fd.append('subir_sistema_ajax','1');
            fd.append('descripcion_sistema',desc);
            fd.append('archivo_sistema',fi.files[0]);
            var xhr=new XMLHttpRequest();
            bloqueP.style.display='block'; msgDiv.innerHTML='';
            btnSubir.disabled=true; btnSubir.innerHTML='<i class="fas fa-spinner fa-spin"></i> Subiendo...';
            barra.style.width='0%'; pct.textContent='0%'; label.textContent='Subiendo archivo...'; detalle.textContent='';
            xhr.upload.addEventListener('progress',function(ev){
                if(ev.lengthComputable){
                    var p=Math.round(ev.loaded/ev.total*100);
                    barra.style.width=p+'%'; pct.textContent=p+'%';
                    detalle.textContent=formatBytes(ev.loaded)+' de '+formatBytes(ev.total);
                    if(p===100) label.textContent='Procesando en el servidor...';
                }
            });
            xhr.addEventListener('load',function(){
                btnSubir.disabled=false; btnSubir.innerHTML='<i class="fas fa-upload"></i> Subir documento';
                bloqueP.style.display='none';
                var msgs={ok_upload:['success','Documento subido correctamente.'],
                    err_desc:['danger','Debe ingresar una descripcion.'],
                    err_file:['danger','Debe seleccionar un archivo valido.'],
                    err_ext:['danger','Tipo de archivo no permitido.'],
                    err_move:['danger','Error al guardar el archivo en el servidor.']};
                try{
                    var raw=xhr.responseText,ini=raw.indexOf('{'),fin=raw.lastIndexOf('}');
                    var resp=JSON.parse(ini!==-1&&fin!==-1?raw.substring(ini,fin+1):raw);
                    var info=msgs[resp.msg]||['danger','Respuesta inesperada del servidor.'];
                    mostrarMsg(info[0],info[1]);
                    if(resp.ok){ form.reset(); setTimeout(function(){ location.reload(); },1500); }
                }catch(err){ mostrarMsg('danger','Error al procesar la respuesta del servidor.'); }
            });
            xhr.addEventListener('error',function(){
                btnSubir.disabled=false; btnSubir.innerHTML='<i class="fas fa-upload"></i> Subir documento';
                bloqueP.style.display='none'; mostrarMsg('danger','Error de red. Intente nuevamente.');
            });
            xhr.open('POST','',true); xhr.send(fd);
        });
    })();
    </script>
    <?php } ?>

    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <table class="table table-striped table-hover">
                <?php
                $PSN->query("SELECT * FROM sistema_documentos ORDER BY fecha DESC");
                if($PSN->num_rows() > 0){
                    while($PSN->next_record()){
                        $sdId=$PSN->f("id"); $sdDesc=htmlspecialchars($PSN->f("descripcion"));
                        $sdArch=$PSN->f("archivo"); $sdExt=$PSN->f("extension");
                        ?>
                        <tr>
                            <td>
                                <a href='descarga_sistema.php?archivo=<?=urlencode($sdArch);?>' target="_blank">
                                    <?=iconoPorExtension($sdExt);?> <?=$sdDesc;?>
                                </a>
                            </td>
                            <?php if($idUsuarioActual == 1){ ?>
                            <td style="width:50px;text-align:center;">
                                <form method="POST" style="display:inline;"
                                      onsubmit="return confirm('Eliminar este documento?');">
                                    <input type="hidden" name="del_sistema" value="<?=$sdId;?>">
                                    <button type="submit" class="btn btn-danger btn-xs" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <?php } ?>
                        </tr>
                        <?php
                    }
                } else { ?>
                    <tr><td><i class="far fa-file-alt"></i> No se han cargado documentos aun.</td></tr>
                <?php } ?>
            </table>
        </div>
        <div class="col-sm-2"></div>
    </div><br>

    <div class="cont-tit">
        <div class="hr"><hr></div>
        <div class="tit-cen"><h3 class="text-center">DOCUMENTOS</h3><h5>DEL USUARIO</h5></div>
        <div class="hr"><hr></div>
    </div>
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-8">
            <table class="table table-striped table-hover">
                <?php
                if($documento_identificacion != ""){?>
                    <tr><td><a href='descarga_usuario.php?archivo=<?=$documento_identificacion;?>' target="_blank"><i class="fas fa-id-card"></i> Documento de Identificacion</a></td></tr>
                <?php }
                if($documento_rut != ""){?>
                    <tr><td><a href='descarga_usuario.php?archivo=<?=$documento_rut;?>' target="_blank"><i class="fas fa-file-alt"></i> RUT</a></td></tr>
                <?php }
                if($documento_constitucion != ""){?>
                    <tr><td><a href='descarga_usuario.php?archivo=<?=$documento_constitucion;?>' target="_blank"><i class="fas fa-file-alt"></i> Constitucion</a></td></tr>
                <?php }
                if($documento_contrato != ""){?>
                    <tr><td><a href='descarga_usuario.php?archivo=<?=$documento_contrato;?>' target="_blank"><i class="fas fa-file-contract"></i> Contrato</a></td></tr>
                <?php }
                $PSN1->query("SELECT * FROM usuario_documentos_add WHERE idUsuario = '".$idUsuarioActual."' ORDER BY descripcion asc");
                if($PSN1->num_rows() > 0){
                    while($PSN1->next_record()){?>
                        <tr><td><a href='descarga_usuario.php?archivo=<?=$PSN1->f('archivo');?>' target="_blank"><i class="fas fa-file-pdf"></i> <?=$PSN1->f('descripcion');?></a></td></tr>
                    <?php }
                } else {?>
                    <tr><td><i class="far fa-file-alt"></i> No se encontraron archivos cargados en el sistema</td></tr>
                <?php }?>
            </table>
        </div>
        <div class="col-sm-2"></div>
    </div>

<?php } ?>
</div>