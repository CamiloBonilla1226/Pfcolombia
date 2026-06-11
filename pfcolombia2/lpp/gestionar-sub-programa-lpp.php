<?php
$PSN1 = new DBbase_Sql;
$PSN = new DBbase_Sql;
$webArchivo = "preoperacional";
$temp_letrero = "LA PEREGRINACIÓN DEL PRISIONERO (LPP)";


// Compress image
function compressImage($source, $destination, $quality) {
  $info = getimagesize($source);
  if($info['mime'] == 'image/jpeg'){
        $image = imagecreatefromjpeg($source);
  }
  elseif ($info['mime'] == 'image/gif'){
        $image = imagecreatefromgif($source);
  }
  elseif ($info['mime'] == 'image/png') {
        $image = imagecreatefrompng($source);
  }
  imagejpeg($image, $destination, $quality);
}

function requestValue($key, $default = '') {
    return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
}

function normalizarAdjuntos($nombres, $documentos) {
    $adjuntos = array();

    if (!is_array($nombres) || !is_array($documentos)) {
        return $adjuntos;
    }

    $total = min(sizeof($nombres), sizeof($documentos));

    for ($i = 0; $i < $total; $i++) {
        $nombre = addslashes(eliminarInvalidos(trim((string) $nombres[$i])));
        $documento = addslashes(eliminarInvalidos(trim((string) $documentos[$i])));

        if ($nombre === '' || $documento === '') {
            continue;
        }

        $adjuntos[] = array(
            'nombre' => $nombre,
            'documento' => $documento,
        );
    }

    return $adjuntos;
}

function guardarAdjuntos($db, $reporteId, $tipo, $adjuntos, $reemplazar = false) {
    // Determinar tabla y columna PK según tipo
    $tablaMap = array(
        1 => array('tabla' => 'reporte_graduado_lpp',  'fk' => 'id_reporte_lpp'),
        2 => array('tabla' => 'reporte_interno_lpp',   'fk' => 'id_reporte_lpp'),
        3 => array('tabla' => 'reporte_externo_lpp',   'fk' => 'id_reporte_lpp'),
    );

    if (!isset($tablaMap[$tipo])) return false;
    $tabla = $tablaMap[$tipo]['tabla'];
    $fk    = $tablaMap[$tipo]['fk'];

    if ($reemplazar) {
        $db->query("DELETE FROM ".$tabla." WHERE ".$fk." = ".$reporteId);
    }

    if (!is_array($adjuntos) || sizeof($adjuntos) === 0) {
        return true;
    }

    $valores = array();
    foreach ($adjuntos as $adjunto) {
        $valores[] = "(".$reporteId.",'".$adjunto['nombre']."','".$adjunto['documento']."','".date('Y-m-d')."')";
    }

    $sql = "INSERT INTO ".$tabla." (".$fk.", nombre, identificacion, fecha_registro) VALUES ".implode(',', $valores);
    return $db->query($sql);
}



/*
*   VERIFICAMOS CON QUE GENERACIÓN NOS ESTAMOS ENFRENTANDO ACTUALMENTE.
*/
$preguntarGeneracion = 0;
if(isset($_REQUEST["generacion"]) && $_REQUEST["generacion"] != ""){
    $generacionActual = eliminarInvalidos($_REQUEST["generacion"]);
}else{
    $generacionActual = "LPP";
}


/*
*   Comprobamos si viene en modo de actualización o de insersión.
*/
if(isset($_REQUEST["id"]) && $_REQUEST["id"] != ""){
    $idReporteActual = soloNumeros($_REQUEST["id"]);
    if ($_SESSION["perfil"] == 162 || $_SESSION["perfil"] == 163) {
        $sql = "UPDATE reporte_lpp SET 
                    fecha_reporte = '".date('Y-m-d')."'";
    
        $sql .= "WHERE id_lpp = '".$idReporteActual."'";
        $PSN1->query($sql);
    }  
}else{
    $idReporteActual = 0;
}


// Array que nos servira para ir llevando cuenta de los requerimientos.
$arrayRequerimientos = array();
if(isset($_POST["funcion"])){
    /*
    *   Para verificar errores a futuro.
        1   Campos requeridos en BLANCO (Nombre, identificacion, password)
        2   Password no coincide
        3   Identificacion YA existente
    */
    $error_datos = 0;
    //
    if($_POST["funcion"] == "insertar"){
        //die("Insertar");
        /*
        *   PESTAÑA GENERAL
        */
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
        if (isset($_REQUEST['sitioReunion'])) {
            $sitioReunion = soloNumeros($_REQUEST["sitioReunion"]);
        }else{
            $sitioReunion = 0;
        }  
        
        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);
        
        $asistencia_hom = soloNumeros($_REQUEST["asistencia_hom"]);
        $asistencia_muj = soloNumeros($_REQUEST["asistencia_muj"]);
        $asistencia_jov = soloNumeros($_REQUEST["asistencia_jov"]);

        $asistencia_nin = soloNumeros($_REQUEST["total"]);
        $bautizados = soloNumeros($_REQUEST["total2"]);
        $desiciones  = soloNumeros($_REQUEST["total3"]);

        $asistencia_total  = soloNumeros($_REQUEST["asistencia_total"]);

        if ($_REQUEST["rep_ndis"]!= 0 && $_REQUEST["rep_ndis"]!= null) {
            $rep_ndis  = soloNumeros($_REQUEST["rep_ndis"]);
        }else{
            $rep_ndis  = 0;
        }
        $rep_entr = eliminarInvalidos($_REQUEST["rep_entr"]);

        $nombre_archivo = $_FILES['archivo1']['name'];
        $archivo1 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo2']['name'];
        $archivo2 = extension_archivo($nombre_archivo);        

        if($error_datos == 0){
            
            /*
            *   DEBEMOS INSERTAR LA INFORMACION DEL REPORTE SEGUN CORRESPONDA.
            */
            $sql = 'INSERT INTO reporte_lpp (
                usuario_id,
                carcel_id,
                programa_id,
                fecha_reporte,
                periodo_trimestre,
                pabellon,
                poblacion_total,
                prisioneros_invitados,
                prisioneros_iniciaron,
                cursos_activos,
                total_graduados,
                total_voluntarios_internos,
                total_voluntarios_externos,
                discipulos_pasaron_cm,
                costo_recursos,
                archivo_foto,
                archivo_testimonio
                )';
            
            $sql .= ' VALUES 
                (
                "'.$_SESSION["id"].'",
                '.$sitioReunion.', 
                307,
                "'.$fechaReporte.'", 
                '.$mapeo_cuarto.', 
                "'.$pabellon.'", 
                "'.$asistencia_total.'", 
                "'.$asistencia_hom.'", 
                "'.$asistencia_muj.'", 
                "'.$asistencia_jov.'", 
                "'.$asistencia_nin.'", 
                "'.$bautizados.'", 
                "'.$desiciones.'",
                '.$rep_ndis.', 
                "'.$rep_entr.'",
                "'.$archivo1.'",
                "'.$archivo2.'"
            )';
            
            $ultimoQuery = $PSN1->query($sql);
            $ultimoId =  $PSN1->ultimoId();
                //
                if($archivo1 != ""){
                    $extArchivo = $archivo1;
                    if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                        $rutaOrigen = $_FILES['archivo1']['tmp_name'];
                        $rutaDestino = "archivos/evi_".$ultimoId."_1.".$archivo1;
                        compressImage($rutaOrigen, $rutaDestino, 80);
                    }
                    else{
                        if(move_uploaded_file($_FILES['archivo1']['tmp_name'], "archivos/evi_".$ultimoId."_1.".$archivo1))
                        {
                        }            
                    }
                }
            

                if($archivo2 != ""){
                    $extArchivo = $archivo2;
                    if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                        $rutaOrigen = $_FILES['archivo2']['tmp_name'];
                        $rutaDestino = "archivos/evi_".$ultimoId."_2.".$archivo2;
                        compressImage($rutaOrigen, $rutaDestino, 80);
                    }
                    else{
                        if(move_uploaded_file($_FILES['archivo2']['tmp_name'], "archivos/evi_".$ultimoId."_2.".$archivo2))
                        {
                        }            
                    }
                }

            if ($asistencia_nin > 0) {
                $adjuntosGraduados = normalizarAdjuntos(requestValue("act_grad_nom", array()), requestValue('act_grad_tar', array()));
                guardarAdjuntos($PSN1, $ultimoId, 1, $adjuntosGraduados);
            } 
            if ($bautizados > 0) {
                $adjuntosVoluntariosInternos = normalizarAdjuntos(requestValue("act_vin_nom", array()), requestValue('act_vin_tar', array()));
                guardarAdjuntos($PSN1, $ultimoId, 2, $adjuntosVoluntariosInternos);
            } 
            if ($desiciones > 0) {
                $adjuntosVoluntariosExternos = normalizarAdjuntos(requestValue("act_vex_nom", array()), requestValue('act_vex_tar', array()));
                guardarAdjuntos($PSN1, $ultimoId, 3, $adjuntosVoluntariosExternos);
            }         
            $varExitoREP = 1;
        }
    }else if($_POST["funcion"] == "eliminar"){
        $sql = 'DELETE FROM reporte_lpp WHERE id_lpp = "'.$idReporteActual.'"';
        $PSN1->query($sql);
    }
    else if($_POST["funcion"] == "actualizar"){
       // die("Actualizar");
        //
        /*
        *   PESTAÑA GENERAL
        */
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
        if (isset($_REQUEST['sitioReunion'])) {
            $sitioReunion = soloNumeros($_REQUEST["sitioReunion"]);
        }else{
            $sitioReunion = 0;
        }  
        
        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);

        $asistencia_hom = soloNumeros($_REQUEST["asistencia_hom"]);
        $asistencia_muj = soloNumeros($_REQUEST["asistencia_muj"]);
        $asistencia_jov = soloNumeros($_REQUEST["asistencia_jov"]);
        $asistencia_nin = soloNumeros($_REQUEST["total"]);

        $bautizados = soloNumeros($_REQUEST["total2"]);        

        //Calculados:
        $asistencia_total  = soloNumeros($_REQUEST["asistencia_total"]);
        $desiciones  = soloNumeros($_REQUEST["total3"]);
        if ($_REQUEST["rep_ndis"]!= 0 && $_REQUEST["rep_ndis"]!= null) {
            $rep_ndis  = soloNumeros($_REQUEST["rep_ndis"]);
        }else{
            $rep_ndis  = 0;
        }
        $rep_entr = eliminarInvalidos($_REQUEST["rep_entr"]);

        $nombre_archivo = $_FILES['archivo1']['name'];
        $archivo1 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo2']['name'];
        $archivo2 = extension_archivo($nombre_archivo);
        
        
        //
        $sql = 'UPDATE reporte_lpp SET 
                    carcel_id = '.$sitioReunion.', 
                    fecha_reporte = "'.$fechaReporte.'",
                    periodo_trimestre = '.$mapeo_cuarto.',
                    pabellon = "'.$pabellon.'", 
                    poblacion_total = "'.$asistencia_total.'", 
                    prisioneros_invitados = "'.$asistencia_hom.'", 
                    prisioneros_iniciaron = "'.$asistencia_muj.'", 
                    cursos_activos = "'.$asistencia_jov.'", 
                    total_graduados = "'.$asistencia_nin.'", 
                    total_voluntarios_internos = "'.$bautizados.'", 
                    total_voluntarios_externos = "'.$desiciones.'",
                    discipulos_pasaron_cm = "'.$rep_ndis.'", 
                    costo_recursos = "'.$rep_entr.'"';

    
                if($archivo1 != ""){
                    $sql .= ', archivo_foto = "'.$archivo1.'"';
                }

        
                if($archivo2 != ""){
                    $sql .= ', archivo_testimonio = "'.$archivo2.'"';
                }


        $sql .= ' WHERE id_lpp = "'.$idReporteActual.'"';
                //echo $sql;
        $PSN1->query($sql);
        $adjuntosGraduados = normalizarAdjuntos(requestValue('act_grad_nom', array()), requestValue('act_grad_tar', array()));
        guardarAdjuntos($PSN1, $idReporteActual, 1, $adjuntosGraduados, true);
        $num_vin_ant = 0;
        $act_vin_id = $_REQUEST['act_vin_id'];
        $act_vin_nom = $_REQUEST['act_vin_nom'];
        $act_vin_tar = $_REQUEST['act_vin_tar'];
        $num_vin_ant = $_REQUEST['vin_regist'];
        $num_vin_nue = $_REQUEST['total2'];
        $adjuntosVoluntariosInternos = normalizarAdjuntos($act_vin_nom, $act_vin_tar);
        guardarAdjuntos($PSN1, $idReporteActual, 2, $adjuntosVoluntariosInternos, true);

        $num_vex_ant = 0;
        $act_vex_id = $_REQUEST['act_vex_id'];
        $act_vex_nom = $_REQUEST['act_vex_nom'];
        $act_vex_tar = $_REQUEST['act_vex_tar'];
        $num_vex_ant = $_REQUEST['vex_regist'];
        $num_vex_nue = $_REQUEST['total3'];
        $adjuntosVoluntariosExternos = normalizarAdjuntos($act_vex_nom, $act_vex_tar);
        guardarAdjuntos($PSN1, $idReporteActual, 3, $adjuntosVoluntariosExternos, true);
        $varExitoREP_UPD = 1;
        //
        //
        //if($generacionNumero > 0){
                // Compress Image
                $ultimoId = $idReporteActual;
                //
                if($archivo1 != ""){
                    $extArchivo = $archivo1;
                    if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                        $rutaOrigen = $_FILES['archivo1']['tmp_name'];
                        $rutaDestino = "archivos/evi_".$ultimoId."_1.".$archivo1;
                        compressImage($rutaOrigen, $rutaDestino, 80);
                    }
                    else{
                        if(move_uploaded_file($_FILES['archivo1']['tmp_name'], "archivos/evi_".$ultimoId."_1.".$archivo1))
                        {
                        }            
                    }
                }
            

                if($archivo2 != ""){
                    $extArchivo = $archivo2;
                    if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                        $rutaOrigen = $_FILES['archivo2']['tmp_name'];
                        $rutaDestino = "archivos/evi_".$ultimoId."_2.".$archivo2;
                        compressImage($rutaOrigen, $rutaDestino, 80);
                    }
                    else{
                        if(move_uploaded_file($_FILES['archivo2']['tmp_name'], "archivos/evi_".$ultimoId."_2.".$archivo2))
                        {
                        }            
                    }
                }
                //
            //}        
        
        //
    }
}


switch($error_datos){
    case 1:
        $texto_error = "Datos requeridos.";
        break;
    case 2:
        $texto_error = "Error no especificado.";
        break;
    case 3:
        $texto_error = "Ese REPORTE ya existe en el sistema para el grupo y lugar seleccionado.";
        break;
    default:
        break;
}

if($idReporteActual > 0){
    /*
    *   TRAEMOS LOS DATOS PRINCIPALES DEL USUARIO
    */
    $sql = "SELECT RL.*, U.nombre AS coordinador, U.id AS id_coordinador, TRU.reub_nom AS carcel_nombre
            FROM reporte_lpp AS RL
            LEFT JOIN usuario AS U ON U.id = RL.usuario_id
            LEFT JOIN tbl_regional_ubicacion AS TRU ON TRU.reub_id = RL.carcel_id";
    $sql .= " WHERE RL.id_lpp = '".$idReporteActual."'";
    $sql .= " GROUP BY RL.id_lpp";
    $PSN1->query($sql);
    //echo $sql;
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $coordinador = $PSN1->f("coordinador");
            $id_coordinador = $PSN1->f("id_coordinador");
            $fechaReporte = $PSN1->f("fecha_reporte");
            $mapeo_cuarto = $PSN1->f("periodo_trimestre");
            $sitioReunion = $PSN1->f("carcel_id");
            
            $pabellon = $PSN1->f("pabellon");
            
            $ext1 = $PSN1->f("archivo_foto");
            $ext2 = $PSN1->f("archivo_testimonio");

            $asistencia_hom = $PSN1->f("prisioneros_invitados");
            $asistencia_muj = $PSN1->f("prisioneros_iniciaron");
            $asistencia_jov = $PSN1->f("cursos_activos");
            $asistencia_nin = $PSN1->f("total_graduados");

            $bautizados = $PSN1->f("total_voluntarios_internos");
            $desiciones  = $PSN1->f("total_voluntarios_externos");

            $asistencia_total  = $PSN1->f("poblacion_total");
            $rep_ndis  = $PSN1->f("discipulos_pasaron_cm");
            $rep_entr  = $PSN1->f("costo_recursos");
            
            //
        }//chequear el registro
    }else{
        ?><div class="row">
            <h3 class="alert alert-info text-center">Registro eliminado</h3>
        </div>
        <div class="form-group">
            <center><input type="button" onClick="window.location.href='index.php?doc=consultar-sub-programa-lpp'" name="previous" class="previous btn btn-danger" value="Cerrar" /> <br />
        </div>
        <?php
        exit;
    }
    $sum_baut = 0;
    $graduadosAdjuntos = array();
    if ((int) $idReporteActual > 0) {
        $sql = "SELECT id_graduado_lpp, nombre, identificacion FROM reporte_graduado_lpp WHERE id_reporte_lpp = '".$idReporteActual."' ORDER BY id_graduado_lpp ASC";
        $PSN1->query($sql);
        while ($PSN1->next_record()) {
            $graduadosAdjuntos[] = array(
                'id' => $PSN1->f("id_graduado_lpp"),
                'nombre' => $PSN1->f("nombre"),
                'documento' => $PSN1->f("identificacion"),
            );
        }
    }
    ?>
    <style type="text/css">
        .report-shell{
            max-width: 1240px;
            margin: 24px auto 42px;
            padding: 0 14px 26px;
        }

        .report-shell .alert{
            border: 0;
            border-radius: 24px;
            padding: 18px 24px;
            margin-bottom: 20px;
            box-shadow: 0 18px 38px rgba(15, 45, 72, 0.12);
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .report-shell .alert-info{
            background: linear-gradient(135deg, #123e59 0%, #296d92 60%, #8ec6df 100%);
            color: #ffffff;
        }

        .report-shell .alert-success{
            background: linear-gradient(135deg, #28724f 0%, #39a46e 100%);
            color: #ffffff;
        }

        .report-shell .alert-warning{
            background: #fff5d7;
            color: #7a5a08;
            border: 1px solid #f2d78d;
        }

        .report-shell .alert-danger{
            background: #fff1f0;
            color: #8f2b24;
            border: 1px solid #f1b7b3;
        }

        .report-shell .alert a{
            color: inherit;
            text-decoration: none;
        }

        .report-shell .alert a:hover{
            text-decoration: underline;
        }

        .report-form{
            position: relative;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            border: 1px solid #dbe7f1;
            border-radius: 30px;
            padding: 24px 24px 34px;
            box-shadow: 0 24px 60px rgba(16, 46, 72, 0.12);
        }

        .report-form:before{
            content: "";
            position: absolute;
            top: -140px;
            right: -70px;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(85, 154, 196, 0.18) 0%, rgba(85, 154, 196, 0) 70%);
            pointer-events: none;
        }

        .report-form > .col-sm-12{
            position: relative;
            width: 100%;
            margin-bottom: 22px;
            padding: 24px 22px 16px;
            border-radius: 24px;
            border: 1px solid #e4edf4;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 14px 34px rgba(22, 53, 80, 0.08);
        }

        .report-form > .col-sm-12:last-of-type{
            margin-bottom: 0;
        }

        .report-form .cont-tit{
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
        }

        .report-form .cont-tit .hr{
            flex: 1 1 0;
        }

        .report-form .cont-tit hr{
            margin: 10px 0;
            border-top: 1px solid #cad8e4;
        }

        .report-form .cont-tit .tit-cen{
            flex: 0 1 auto;
            min-width: 280px;
            padding: 12px 20px;
            border-radius: 18px;
            background: linear-gradient(135deg, #f1f7fb 0%, #ffffff 100%);
            border: 1px solid #d9e6f0;
            text-align: center;
        }

        .report-form .cont-tit h3{
            margin: 0 0 4px;
            color: #19344c;
            font-family: Georgia, "Times New Roman", serif;
            font-size: 24px;
            font-weight: 700;
        }

        .report-form .cont-tit h5,
        .report-form .cont-tit p{
            margin: 0;
            color: #577086;
            font-size: 14px;
            line-height: 1.5;
        }

        .report-form .form-group{
            margin-left: -8px;
            margin-right: -8px;
            margin-bottom: 4px;
        }

        .report-form .form-group > [class*="col-sm-"]{
            padding-left: 8px;
            padding-right: 8px;
            margin-bottom: 16px;
        }

        .report-form strong{
            display: block;
            margin-bottom: 8px;
            color: #243c55;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        .report-form .form-control,
        .report-form select,
        .report-form textarea{
            min-height: 46px;
            height: auto;
            border-radius: 14px;
            border: 1px solid #c7d6e2;
            background: #f8fbfd;
            box-shadow: none;
            color: #1f3348;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
        }

        .report-form textarea.form-control{
            min-height: 120px;
        }

        .report-form .form-control:focus,
        .report-form select:focus,
        .report-form textarea:focus{
            border-color: #4f8db6;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(79, 141, 182, 0.14);
        }

        .report-form .form-control[readonly],
        .report-form .form-control[disabled],
        .report-form select[disabled],
        .report-form textarea[disabled]{
            background: #eef4f8;
            color: #53687b;
        }

        .report-form input[type="file"].form-control{
            padding: 10px 12px;
        }

        .report-form .btn{
            border-radius: 14px;
            padding: 11px 18px;
            font-weight: 700;
            letter-spacing: 0.2px;
            border: 0;
            box-shadow: 0 12px 24px rgba(20, 56, 86, 0.14);
            transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
        }

        .report-form .btn:hover,
        .report-form .btn:focus{
            transform: translateY(-1px);
            box-shadow: 0 16px 26px rgba(20, 56, 86, 0.18);
            filter: brightness(1.02);
        }

        .report-form .btn-success{
            background: linear-gradient(135deg, #2d8658 0%, #39a977 100%);
            color: #ffffff;
        }

        .report-form .btn-info{
            background: linear-gradient(135deg, #2c6d8f 0%, #3d90bb 100%);
            color: #ffffff;
        }

        .report-form .btn-warning{
            background: linear-gradient(135deg, #d89d2f 0%, #efb64c 100%);
            color: #ffffff;
        }

        .report-form .btn-danger{
            background: linear-gradient(135deg, #c54c43 0%, #df6a61 100%);
            color: #ffffff;
        }

        .report-form .btn-cir-uno,
        .report-form .btn-eliminar-fila{
            width: 42px;
            height: 42px;
            min-width: 42px;
            padding: 0;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .report-form #tablaAdd,
        .report-form #tablaAdd2,
        .report-form #tablaAdd3{
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #dce7ef;
            background: #f9fbfd;
        }

        .report-form #tablaAdd td,
        .report-form #tablaAdd2 td,
        .report-form #tablaAdd3 td{
            padding: 16px 14px;
            vertical-align: top;
            border-top: 1px solid #e2eaf1;
        }

        .report-form #tablaAdd tr:first-child td,
        .report-form #tablaAdd2 tr:first-child td,
        .report-form #tablaAdd3 tr:first-child td{
            border-top: 0;
        }

        .report-shell .cont-btn{
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .report-shell .cont-btn.fl-cent{
            justify-content: center;
        }

        .report-shell .item-btn{
            margin: 0;
        }

        .report-shell .item-btn .btn,
        .report-shell .item-btn input.btn{
            min-width: 180px;
        }

        @media (max-width: 991px){
            .report-shell{
                padding: 0 12px 22px;
            }

            .report-form{
                padding: 18px 16px 26px;
                border-radius: 24px;
            }

            .report-form > .col-sm-12{
                padding: 20px 16px 12px;
                border-radius: 20px;
            }

            .report-form .cont-tit{
                flex-direction: column;
                gap: 10px;
            }

            .report-form .cont-tit .hr{
                width: 100%;
            }

            .report-form .cont-tit .tit-cen{
                width: 100%;
                min-width: 0;
            }
        }

        @media (max-width: 767px){
            .report-shell{
                margin-top: 14px;
                padding: 0 10px 18px;
            }

            .report-shell .alert{
                border-radius: 18px;
                padding: 16px 18px;
                font-size: 18px;
            }

            .report-form .form-group{
                margin-left: 0;
                margin-right: 0;
            }

            .report-form .form-group > [class*="col-sm-"]{
                padding-left: 0;
                padding-right: 0;
                margin-bottom: 14px;
            }

            .report-form [class*="col-sm-"]:empty{
                display: none;
            }

            .report-form #tablaAdd td,
            .report-form #tablaAdd2 td,
            .report-form #tablaAdd3 td{
                display: block;
                width: 100% !important;
            }

            .report-shell .cont-btn{
                justify-content: center;
            }

            .report-shell .item-btn,
            .report-shell .item-btn .btn,
            .report-shell .item-btn input.btn{
                width: 100%;
            }

            .report-form center .btn{
                width: 100%;
                max-width: none;
            }
        }
    </style>
    <style type="text/css">
        .report-shell .alert,
        .report-shell .alert-info,
        .report-shell .alert-success,
        .report-shell .alert-warning,
        .report-shell .alert-danger{
            background: #ffffff;
            color: #20262d;
            border: 1px solid #d7dce1;
            border-left: 4px solid #2d3338;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        }

        .report-form{
            background: #f7f7f7;
            border: 1px solid #d8d8d8;
            box-shadow: 0 14px 32px rgba(0, 0, 0, 0.05);
        }

        .report-form:before{
            display: none !important;
        }

        .report-form > .col-sm-12{
            background: #ffffff;
            border: 1px solid #e1e1e1;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        }

        .report-form .cont-tit .tit-cen{
            background: #fbfbfb;
            border: 1px solid #d9d9d9;
            box-shadow: none;
        }

        .report-form .cont-tit hr{
            border-top-color: #d0d0d0;
        }

        .report-form .cont-tit h3{
            color: #111111;
        }

        .report-form .cont-tit h5,
        .report-form .cont-tit p{
            color: #555555;
        }

        .report-form .form-group > [class*="col-sm-"] > strong:first-child{
            min-height: 3.7em;
        }

        .report-form .form-control,
        .report-form select,
        .report-form textarea{
            background: #ffffff;
            border: 1px solid #cfcfcf;
            color: #1f1f1f;
        }

        .report-form .form-control:focus,
        .report-form select:focus,
        .report-form textarea:focus{
            border-color: #6f7780;
            box-shadow: 0 0 0 3px rgba(70, 70, 70, 0.08);
        }

        .report-form .btn,
        .report-form input.btn{
            background: #f3f3f3;
            border-color: #cccccc;
            color: #222222;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .report-form .btn-success,
        .report-form .btn-info,
        .report-form .btn-warning{
            background: #f3f3f3;
            border-color: #cccccc;
            color: #222222;
        }

        #adicionarAdd,
        #adicionarAdd2,
        #adicionarAdd3,
        #generarVariasAdd{
            background: #2f7d32 !important;
            border-color: #2f7d32 !important;
            color: #ffffff !important;
        }

        #borrarTodoAdd,
        .report-form .btn-danger,
        .report-form .btn-cir-uno,
        .report-form .btn-eliminar-fila{
            background: #c0392b !important;
            border-color: #c0392b !important;
            color: #ffffff !important;
        }

        .report-form input[type="submit"][name="button"],
        .report-form input[type="submit"][value="Guardar"]{
            background: #2b5daa !important;
            border-color: #2b5daa !important;
            color: #ffffff !important;
        }

        @media (max-width: 767px){
            .report-form .form-group > [class*="col-sm-"] > strong:first-child{
                min-height: 0;
            }
        }
    </style>
    <?php
    ?><div class="container report-shell">
    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal report-form">
        <h3 class="alert alert-info text-center"><?php
            if($idReporteActual == 0){
                echo "REPORTE";
            }else{
                echo "VISUALIZACIÓN";
                $sqlU = "SELECT RL.id_lpp AS id FROM reporte_lpp AS RL
                LEFT JOIN usuario AS U ON U.id = RL.usuario_id
                LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
                WHERE RL.id_lpp = (SELECT MAX(STR.id_lpp) FROM reporte_lpp AS STR WHERE STR.id_lpp < ".$idReporteActual.") ";
                    if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                        $sqlU .= "AND UE.empresa_pd = ".$_SESSION["empresa_pd"]." ";
                    }
                $PSN1->query($sqlU); 
                if($PSN1->num_rows() > 0){
                    if($PSN1->next_record()){
                    $antId  = $PSN1->f('id');
                    }
                }else{
                   $antId  = 0; 
                }
                $sqlU = "SELECT RL.id_lpp AS id FROM reporte_lpp AS RL
                LEFT JOIN usuario AS U ON U.id = RL.usuario_id
                LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
                WHERE RL.id_lpp = (SELECT MIN(STR.id_lpp) FROM reporte_lpp AS STR WHERE STR.id_lpp > ".$idReporteActual.") ";
                    if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                        $sqlU .= "AND UE.empresa_pd = ".$_SESSION["empresa_pd"]." ";
                    }
                $PSN1->query($sqlU);
                //echo  $sqlU;
                if($PSN1->num_rows() > 0){
                    if($PSN1->next_record()){
                    $sigId  = $PSN1->f('id');
                    }
                }else{
                   $sigId  = 0; 
                }              
            }
            
            ?> DE <?=$temp_letrero; ?></h3>
            <?php //if ($_SESSION["perfil"] == 162 || $_SESSION["perfil"] == 2){ ?>
            <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <?php
                    if ($antId != 0) {?>
                    <a href="index.php?doc=gestionar-sub-programa-lpp&id=<?=$antId ?>" name="previous" class="previous btn btn-info">Anterior reporte <?=$antId ?></a>
                    <?php } ?>
                </div>
                <div class="item-btn">
                    <a href="index.php?doc=consultar-sub-programa-lpp" name="previous" class="btn btn-warning">Todos los reportes</a>
                </div>
                <div class="item-btn">
                    <?php
                    if ($sigId != 0) {?>
                    <a href="index.php?doc=gestionar-sub-programa-lpp&id=<?=$sigId ?>" name="previous" class="previous btn btn-info">Siguiente reporte <?=$sigId ?></a>
                    <?php } ?>
                </div>
            </div>
        <?php 
            $fecha_actual = date("Y-m-d");
            $fechLimite = date("Y-m-d",strtotime($fecha_actual."- 90 days"));
            //echo $fechLimite ." - ". $fechaReporte;
        ?>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">INFORMACIÓN GENERAL</h3>
                <h5>REGISTRO ID: <?=str_pad($idReporteActual, 6, "0", STR_PAD_LEFT); ?></h5>
            </div>
            <div class="hr"><hr></div>
        </div> 
              
        <div class="form-group">
            <div class="col-sm-1"></div>
            <div class="col-sm-2">
                <strong>Regional:</strong>
                <input name="regional" type="text" id="regional" maxlength="250" value="<?=$regional; ?>" class="form-control" required readonly />
            </div>
            <div class="col-sm-2">
                <strong>Coordinador de prisión:</strong>
                <select required readonly name="usua_id" id="usua_id" class="form-control">
                    <option value="<?=$id_coordinador; ?>"><?=$coordinador; ?></option>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Fecha del registro:</strong>
                <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250" value="<?=$fechaReporte; ?>" class="form-control" required readonly  />
            </div>
            <div class="col-sm-2">
                <strong>Período:</strong>
                <select name="mapeo_cuarto" readonly class="form-control">
                    <?php echo($mapeo_cuarto== "1")?'<option value="1" selected >Q1 (Ene - Mar)':''; ?>
                    <?php echo($mapeo_cuarto== "4")?'<option value="4" selected >Q2 (Abr - Jun)':''; ?>
                    <?php echo($mapeo_cuarto== "7")?'<option value="7" selected >Q3 (Jul - Sep)':''; ?>
                    <?php echo($mapeo_cuarto== "10")?'<option value="10" selected >Q4 (Oct - Dic)':''; ?></option>
                </select>
            </div>
            <div class="col-sm-2">
                <strong>Cárcel ubicación: </strong>
                <select required name="sitioReunion" id="rep_carcel" class="form-control">        
                    <?php
                    /*
                    *   TRAEMOS LOS TIPOS DE SERVICIOS QUE PRESTA (25)
                    */
                    if ($_SESSION['empresa_pd'] != "") {
                        echo '<option value="">Sin especificar</option>';
                        $sql = "SELECT * ";
                       $sql.=" FROM tbl_regional_ubicacion ";
                        if($_SESSION['empresa_pd'] != 0){
                            $sql.=" WHERE reub_reg_fk = ".$_SESSION['empresa_pd'];
                        }
                        $sql.=" ORDER BY reub_reg_fk asc";

                        $PSN1->query($sql);
                        $numero=$PSN1->num_rows();
                        if($numero > 0){
                            while($PSN1->next_record()){
                                ?><option value="<?=$PSN1->f('reub_id'); ?>" <?php
                                if($sitioReunion == $PSN1->f('reub_id'))
                                {
                                    ?>selected="selected"<?php
                                }
                                ?>><?=$PSN1->f('reub_nom'); ?></option><?php
                            }
                        }
                    }else{
                        echo '<option value="">Sin regional asignada</option>';
                    }
                    ?>
                </select>
            </div>     
        </div>
        <div class="form-group">
            <div class="col-sm-1"></div> 
              
            <div id="ubicacion"></div>
            <div class="col-sm-2">
                <strong>N° de patios y/o pabellón:</strong>
                <input name="pabellon" type="number" id="pabellon" maxlength="250" value="<?=$pabellon; ?>" class="form-control" required />
            </div> 
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3>INFORMACIÓN DE LA PRISIÓN</h3>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-3">
                <strong>Total población que hay en la prisión:</strong>
                <input name="asistencia_total" type="number" id="asistencia_total" value="<?=$asistencia_total; ?>" class="form-control" required />
            </div>
            <div class="col-sm-3">
                <strong>Número de prisioneros invitados:</strong>
                <input name="asistencia_hom" type="number" id="asistencia_hom" value="<?=$asistencia_hom; ?>" class="form-control" required  />
            </div>
            <div class="col-sm-3">
                <strong>Número de prisioneros que iniciaron el curso:</strong>
                <input name="asistencia_muj" type="number" id="asistencia_muj" value="<?=$asistencia_muj; ?>" class="form-control" required />
            </div>
            <div class="col-sm-3">
                <strong>Numero de cursos activos de LPP:</strong>
                <input name="asistencia_jov" type="number" id="asistencia_jov" value="<?=$asistencia_jov; ?>" class="form-control" readonly />
            </div>
        </div>
        <?php if (false) { ?>
        <!--MODIFICAR REGISTRO DE GRADUADOS--->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">MODIFICAR DE GRADUADOS</h3>
                <p>A continuación por favor ingrese los datos requeridos</p>               
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 registro-table-wrap">
                <script>
                        $(function(){
                            var total = <?= $asistencia_nin; ?>;
                            var tar = $(".act_grad_tar").val();
                            var nom = $(".act_grad_nom").val();

                            //$("#asistencia_total").prop('required',true);

                            if (tar == "" || nom == "") {
                                $("#adicionarAdd").prop( "disabled", true );
                            }else{
                                <?php if($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte){ ?>
                                $("#adicionarAdd").prop( "disabled", true );
                            <?php }else{ ?>
                                $("#adicionarAdd").prop( "disabled", false );
                            <?php } ?>
                            }
                            var vtotal = $("#asistencia_total").val();
                            $("#asistencia_hom").attr('max', (vtotal-1));

                            var vtotal = $("#asistencia_hom").val();
                            $("#asistencia_muj").attr('max', vtotal);

                            $("#asistencia_hom").change(function(){
                                var vtotal = $("#asistencia_hom").val();
                                $("#asistencia_muj").attr('max', vtotal);
                            });
                            $("#asistencia_total").change(function(){
                                var vtotal = $("#asistencia_total").val();
                                $("#asistencia_hom").attr('max', (vtotal-1));
                            });
                            var totalG = $("#total").val();
                                if (totalG<=0) {
                                   totalG = 0; 
                                }
                                $("#rep_ndis").attr('max', totalG);
                            $("#total").change(function(){
                                var totalG = $("#total").val();
                                if (totalG<=0) {
                                   totalG = 0; 
                                }
                                $("#rep_ndis").attr('max', totalG);
                            });
                            $("#asistencia_muj").change(function(){
                                var vtotal = $("#asistencia_muj").val();
                                if (total >= vtotal) {
                                    $("#adicionarAdd").prop( "disabled", true );
                                }else{
                                    $("#adicionarAdd").prop( "disabled", false );
                                }
                            });
                            
                            $(".act_grad_nom").change(function(){
                                var vtotal = $("#asistencia_muj").val();
                                var tar3 = $(".act_grad_tar").val();
                                var nom3 = $(".act_grad_nom").val();
                                if (tar3 != "" && nom3 !="") {
                                    if (total < 1) {
                                        total = total + 1;
                                    }
                                    $("#adicionarAdd").prop( "disabled", false );
                                }else if (tar3 == "" && nom3 =="") {
                                    if (total == 1) {
                                        total = total - 1;
                                        $(".act_grad_nom").prop('required',false);
                                        $(".act_grad_tar").prop('required',false);
                                    }
                                }else{
                                    $("#adicionarAdd").prop( "disabled", true );
                                }
                                $('#total').val(total);
                            });
                            $(".act_grad_tar").change(function(){
                                var vtotal = $("#asistencia_muj").val();
                                var nom2 = $(".act_grad_nom").val();
                                var tar2 = $(".act_grad_tar").val();
                                if (nom2 != ""&& tar2 != "") {
                                    if (total < 1) {
                                        total = total + 1;
                                    }
                                    $("#adicionarAdd").prop( "disabled", false );
                                }else if (tar3 == "" && nom3 =="") {
                                    if (total == 1) {
                                        total = total - 1;
                                        $(".act_grad_nom").prop('required',false);
                                        $(".act_grad_tar").prop('required',false);
                                    }
                                }else{
                                    $("#adicionarAdd").prop( "disabled", true );
                                }
                                $('#total').val(total);
                            });

                            $("#adicionarAdd").on('click',function(){
                                $("#tablaAdd tr:last").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");
                                $("#tablaAdd tr input.act_grad_nom:last").val('');
                                $("#tablaAdd tr input.act_grad_tar:last").val('');
                                var vtotal = $("#asistencia_muj").val();
                                var tar2 = $(".act_grad_tar").val();
                                var nom2 = $(".act_grad_nom").val();
                                if (tar2!="" && nom2!="") {
                                    total = total + 1;
                                }
                                if (total >= vtotal) {
                                    $("#adicionarAdd").prop( "disabled", true );
                                }else{
                                    $("#adicionarAdd").prop( "disabled", false );
                                }
                                $(".act_grad_nom").prop('required',true);
                                $(".act_grad_tar").prop('required',true);
                                $('#total').val(total);
                                var totalG = $("#total").val();
                                $("#rep_ndis").attr('max', totalG);
                            });
                            $(document).on("click",".eliminarAdd",function(){
                                var vtotal = $("#asistencia_muj").val();
                                var parent = $(this).parents().get(0);
                                $(parent).remove();
                                total = total - 1;
                                $('#total').val(total);
                                var totalG = $("#total").val();
                                $("#rep_ndis").attr('max', (totalG));
                                if (total >= vtotal) {
                                    $("#adicionarAdd").prop( "disabled", true );
                                }else{
                                    $("#adicionarAdd").prop( "disabled", false );
                                }
                            });
                            
                        });
                    </script>
                <table id="tablaAdd">
                    <?php 
                    $sql = "SELECT id_graduado_lpp AS adj_id, nombre AS adj_nom, identificacion AS adj_url FROM reporte_graduado_lpp ";
                    $sql.=" WHERE id_reporte_lpp = '".$idReporteActual."' ";
                    $PSN1->query($sql);
                    $numero=$PSN1->num_rows();
                    $cont = 0;
                    echo '<input type="hidden" name="grad_regist" value="'.$numero.'" placeholder="">';
                    if($numero > 0){
                        while($PSN1->next_record()){ ?>
                            <input type="hidden" name="act_grad_id[]" value="<?= $PSN1->f("adj_id");  ?>">
                            <tr <?php echo($cont==0)?'class="fila-fijaAdd"':''; ?>>
                                <td class="col-sm-7">
                                   
                                    <strong>Nombre completo del graduado:</strong>
                                    <input name="act_grad_nom[]" type="text" id="act_grad_nom" class="act_grad_nom form-control" value="<?=$PSN1->f("adj_nom"); ?>" required />
                                </td>
                                <td class="col-sm-4">
                                    <strong>Tarjeta dactilar / N° identificación:</strong>
                                    <input name="act_grad_tar[]" type="text" id="act_grad_tar" min="0" class="act_grad_tar form-control" value="<?=$PSN1->f("adj_url"); ?>" required />
                                </td>
                                <td class="eliminarAdd"><br><button type="button" class="btn btn-cir-uno usua-col"><i class="fa fa-times"></i></button></td>
                            </tr>
                        <?php $cont++;
                        }
                    }else{ ?>
                        
                        <tr class="fila-fijaAdd">
                            <td class="col-sm-7">
                                <strong>Nombre completo del graduado:</strong>
                                <input name="act_grad_nom[]" type="text" id="act_grad_nom" class="act_grad_nom form-control"  />
                            </td>
                            <td class="col-sm-4">
                                <strong>Tarjeta dactilar / N° identificación:</strong>
                                <input name="act_grad_tar[]" type="text" id="act_grad_tar" min="0" class="act_grad_tar form-control" />
                            </td>
                            <td class="eliminarAdd"><br><button type="button" class="btn btn-cir-uno usua-col"><i class="fa fa-times"></i></button></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
            <div class="col-sm-2"></div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 registro-table-wrap">
                <div class="col-sm-4"><strong>Número de graduados en LPP en la prisión:</strong> </div>
                <div class="col-sm-2">
                    <input type="text" name="total" id="total" class="form-control" value="<?=$asistencia_nin; ?>" readonly>
                </div>
                <div class="col-sm-4"></div>
                <div class="col-sm-2">
                    <center>
                        <button id="adicionarAdd" class="btn btn-success" type="button" class="boton" <?php echo($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte)?'disabled="disabled"':'';?>><i class="fas fa-plus"></i>  Adicionar</button>
                    </center>
                </div>
            </div>
        </div>
        <?php } ?>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">MODIFICAR DE GRADUADOS</h3>
                <p>A continuacion por favor ingrese los datos requeridos</p>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 registro-table-wrap">
                <?php
                $graduadosEdicion = array();
                foreach ($graduadosAdjuntos as $adjunto) {
                    $graduadosEdicion[] = array(
                        'nombre' => $adjunto['nombre'],
                        'tarjeta' => $adjunto['documento'],
                    );
                }
                ?>
                <table id="tablaAdd" class="table table-bordered registro-table">
                    <tbody></tbody>
                </table>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="registro-bulk-controls" style="margin-bottom: 15px;">
                    <div class="col-sm-6 registro-bulk-controls__text">
                        <label for="cantidadAdd">Cuantos registros desea realizar?</label>
                    </div>
                    <div class="col-sm-3 registro-bulk-controls__value">
                        <input type="number" id="cantidadAdd" class="form-control" min="1" placeholder="Ej: 5">
                    </div>
                    <div class="col-sm-3 registro-bulk-controls__actions">
                        <button id="generarVariasAdd" class="btn btn-primary btn-block" type="button" <?php echo($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte)?'disabled="disabled"':'';?>>
                            <i class="fa fa-list"></i> Generar
                        </button>
                    </div>
                </div>
                <div class="registro-summary">
                    <div class="col-sm-6 registro-summary__text">
                        <strong>Numero de graduados en LPP en la prision:</strong>
                    </div>
                    <div class="col-sm-2 registro-summary__value">
                        <input type="text" name="total" id="total" class="form-control" value="<?=$asistencia_nin; ?>" readonly>
                    </div>
                    <div class="col-sm-4 registro-summary__actions">
                        <div class="col-sm-6">
                            <button id="adicionarAdd" class="btn btn-success btn-block" type="button" <?php echo($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte)?'disabled="disabled"':'';?>>
                                <i class="fa fa-plus"></i> Adicionar
                            </button>
                        </div>
                        <div class="col-sm-6">
                            <button id="borrarTodoAdd" class="btn btn-danger btn-block" type="button" <?php echo($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte)?'disabled="disabled"':'';?>>
                                <i class="fa fa-trash"></i> Borrar todo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <style>
            #tablaAdd {
                width: 100%;
                max-width: 100%;
            }

            #tablaAdd td {
                vertical-align: top;
                padding: 14px;
            }

            #tablaAdd input {
                margin-top: 8px;
            }

            #tablaAdd .campo-identificacion-graduado {
                display: flex !important;
                align-items: center;
                gap: 12px;
                margin-top: 8px;
            }

            #tablaAdd .campo-identificacion-graduado .contenedor-input {
                flex: 1 1 auto;
                min-width: 0;
            }

            #tablaAdd .btn-eliminar-fila {
                width: 36px;
                height: 34px;
                min-width: 36px;
                padding: 0;
                border-radius: 4px;
                display: flex !important;
                align-items: center;
                justify-content: center;
                background-color: #d9534f !important;
                border: 1px solid #d43f3a;
                color: #fff !important;
                font-size: 22px;
                font-weight: bold;
                line-height: 1;
                text-align: center;
                text-decoration: none !important;
                cursor: pointer;
                flex: 0 0 36px;
                opacity: 1 !important;
                visibility: visible !important;
            }

            #cantidadAdd {
                text-align: center;
                font-size: 16px;
                font-weight: bold;
            }

            #generarVariasAdd,
            #adicionarAdd,
            #borrarTodoAdd {
                white-space: nowrap;
            }

            @media (min-width: 992px) {
                #tablaAdd td:first-child {
                    width: 58% !important;
                }

                #tablaAdd td:nth-child(2) {
                    width: 42% !important;
                }
            }
        </style>
        <script>
            $(function () {
                var STORAGE_KEY = 'lpp_graduados_edicion_<?= $idReporteActual; ?>';
                var registrosIniciales = <?= json_encode($graduadosEdicion); ?>;
                var soloLectura = <?= ($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte) ? 'true' : 'false'; ?>;
                var storage = window.sessionStorage;

                function crearFila() {
                    return $(
                        '<tr class="fila-fijaAdd registro-table-row">' +
                            '<td class="registro-col registro-col--nombre">' +
                                '<strong>Nombre completo del graduado:</strong>' +
                                '<input name="act_grad_nom[]" type="text" class="act_grad_nom form-control" />' +
                            '</td>' +
                            '<td class="registro-col registro-col--identificacion">' +
                                '<strong>Tarjeta dactilar / N&deg; identificacion:</strong>' +
                                '<input name="act_grad_tar[]" type="text" class="act_grad_tar form-control" />' +
                            '</td>' +
                            '<td class="registro-col registro-col--action">' +
                                '<button type="button" class="btn btn-danger btn-eliminar-fila" title="Eliminar" aria-label="Eliminar registro">' +
                                    '<i class="fa fa-times"></i>' +
                                '</button>' +
                            '</td>' +
                        '</tr>'
                    );
                }

                function obtenerMaximoGraduados() {
                    var maximo = parseInt($('#asistencia_muj').val(), 10);
                    return isNaN(maximo) || maximo < 1 ? null : maximo;
                }

                function actualizarLimitesAsistencia() {
                    var asistenciaTotal = parseInt($('#asistencia_total').val(), 10);
                    var asistenciaHombres = parseInt($('#asistencia_hom').val(), 10);

                    if (!isNaN(asistenciaTotal) && asistenciaTotal >= 0) {
                        $('#asistencia_hom').attr('max', Math.max(asistenciaTotal - 1, 0));
                    }

                    if (!isNaN(asistenciaHombres) && asistenciaHombres >= 0) {
                        $('#asistencia_muj').attr('max', asistenciaHombres);
                    }
                }

                function obtenerDatosFila($fila) {
                    return {
                        nombre: $.trim($fila.find('.act_grad_nom').val()),
                        tarjeta: $.trim($fila.find('.act_grad_tar').val())
                    };
                }

                function filaCompleta($fila) {
                    var datos = obtenerDatosFila($fila);
                    return datos.nombre !== '' && datos.tarjeta !== '';
                }

                function filaIncompleta($fila) {
                    var datos = obtenerDatosFila($fila);
                    return (datos.nombre !== '' && datos.tarjeta === '') || (datos.nombre === '' && datos.tarjeta !== '');
                }

                function contarCompletos() {
                    var total = 0;
                    $('#tablaAdd tbody tr').each(function () {
                        if (filaCompleta($(this))) {
                            total++;
                        }
                    });
                    return total;
                }

                function sincronizarTotal() {
                    var total = contarCompletos();
                    $('#total').val(total);
                    $('#rep_ndis').attr('max', total > 0 ? total : 0);
                    return total;
                }

                function guardarDatos() {
                    if (!storage || soloLectura) {
                        return;
                    }

                    var datos = [];
                    $('#tablaAdd tbody tr').each(function () {
                        datos.push(obtenerDatosFila($(this)));
                    });

                    try {
                        storage.setItem(STORAGE_KEY, JSON.stringify(datos));
                    } catch (error) {
                    }
                }

                function obtenerDatosGuardados() {
                    if (!storage) {
                        return null;
                    }

                    try {
                        var datos = storage.getItem(STORAGE_KEY);
                        if (!datos) {
                            return null;
                        }

                        datos = JSON.parse(datos);
                        return Array.isArray(datos) ? datos : null;
                    } catch (error) {
                        return null;
                    }
                }

                function renderizarFilas(datos) {
                    var $tbody = $('#tablaAdd tbody');
                    $tbody.empty();

                    if (!Array.isArray(datos) || datos.length === 0) {
                        datos = [{ nombre: '', tarjeta: '' }];
                    }

                    $.each(datos, function (_, item) {
                        var $fila = crearFila();
                        $fila.find('.act_grad_nom').val(item.nombre || '');
                        $fila.find('.act_grad_tar').val(item.tarjeta || '');
                        $tbody.append($fila);
                    });
                }

                function asegurarMinimoUnaFila() {
                    if ($('#tablaAdd tbody tr').length === 0) {
                        renderizarFilas([]);
                    }
                }

                function hayDatosCapturados() {
                    var hayDatos = false;
                    $('#tablaAdd tbody tr').each(function () {
                        var datos = obtenerDatosFila($(this));
                        if (datos.nombre !== '' || datos.tarjeta !== '') {
                            hayDatos = true;
                            return false;
                        }
                    });
                    return hayDatos;
                }

                function actualizarBotones() {
                    var maximo = obtenerMaximoGraduados();
                    var filas = $('#tablaAdd tbody tr').length;
                    var deshabilitarAgregar = soloLectura || (maximo !== null && filas >= maximo);

                    $('#adicionarAdd').prop('disabled', deshabilitarAgregar);
                    $('#generarVariasAdd').prop('disabled', soloLectura);
                    $('#borrarTodoAdd').prop('disabled', soloLectura);
                    $('#tablaAdd .btn-eliminar-fila').prop('disabled', soloLectura);
                }

                function restaurarDatos() {
                    var datosGuardados = obtenerDatosGuardados();
                    renderizarFilas(datosGuardados !== null ? datosGuardados : registrosIniciales);
                    actualizarLimitesAsistencia();
                    sincronizarTotal();
                    actualizarBotones();
                }

                function agregarFila() {
                    var maximo = obtenerMaximoGraduados();
                    var filas = $('#tablaAdd tbody tr').length;

                    if (maximo !== null && filas >= maximo) {
                        alert('No puede registrar mas graduados que prisioneros que iniciaron el curso (' + maximo + ').');
                        return;
                    }

                    $('#tablaAdd tbody').append(crearFila());
                    actualizarBotones();
                    guardarDatos();
                }

                function generarFilas(cantidad) {
                    var maximo = obtenerMaximoGraduados();
                    var filasActuales = $('#tablaAdd tbody tr').length;

                    if (maximo !== null && (filasActuales + cantidad) > maximo) {
                        alert('No puede generar mas registros que prisioneros que iniciaron el curso (' + maximo + ').');
                        return;
                    }

                    for (var i = 0; i < cantidad; i++) {
                        $('#tablaAdd tbody').append(crearFila());
                    }

                    sincronizarTotal();
                    actualizarBotones();
                    guardarDatos();
                }

                function borrarTodo() {
                    renderizarFilas([]);
                    sincronizarTotal();
                    actualizarBotones();
                    guardarDatos();
                }

                restaurarDatos();

                $('#asistencia_hom, #asistencia_total, #asistencia_muj').on('change keyup', function () {
                    actualizarLimitesAsistencia();
                    actualizarBotones();
                });

                $(document).on('click', '#adicionarAdd', function (e) {
                    e.preventDefault();
                    agregarFila();
                });

                $(document).on('click', '#generarVariasAdd', function (e) {
                    e.preventDefault();

                    var cantidad = parseInt($('#cantidadAdd').val(), 10);
                    if (isNaN(cantidad) || cantidad <= 0) {
                        alert('Ingrese una cantidad valida mayor a 0.');
                        $('#cantidadAdd').focus();
                        return;
                    }

                    generarFilas(cantidad);
                });

                $(document).on('click', '#borrarTodoAdd', function (e) {
                    e.preventDefault();

                    if (hayDatosCapturados() && !confirm('Esta seguro de borrar todos los registros cargados?')) {
                        return;
                    }

                    borrarTodo();
                });

                $(document).on('click', '#tablaAdd .btn-eliminar-fila', function (e) {
                    e.preventDefault();

                    $(this).closest('tr').remove();
                    asegurarMinimoUnaFila();
                    sincronizarTotal();
                    actualizarBotones();
                    guardarDatos();
                });

                $(document).on('keyup change blur', '.act_grad_nom, .act_grad_tar', function () {
                    sincronizarTotal();
                    actualizarBotones();
                    guardarDatos();
                });

                $('form').on('submit', function (e) {
                    var hayIncompletas = false;

                    $('#tablaAdd tbody tr').each(function () {
                        if (filaIncompleta($(this))) {
                            hayIncompletas = true;
                            return false;
                        }
                    });

                    if (hayIncompletas) {
                        e.preventDefault();
                        alert('Si diligencia una fila de graduados, debe completar tanto el nombre como la identificacion.');
                        return false;
                    }

                    sincronizarTotal();
                });
            });
        </script>
        <!--MODIFICAR REGISTRO DE VOLUNTARIOS INTERNOS-->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">MODIFICAR DE VOLUNTARIOS INTERNOS</h3>
                <p>A continuación por favor ingrese los datos requeridos</p>               
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 registro-table-wrap">
                <script>
                    $(function(){
                        var total = <?=$bautizados; ?>;
                        var tar = $(".act_vin_tar").val();
                        var nom = $(".act_vin_nom").val();
                        if (tar == "" || nom == "") {
                            $("#adicionarAdd2").prop( "disabled", true );
                        }else{
                            <?php if($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte){ ?>
                                $("#adicionarAdd2").prop( "disabled", true );
                            <?php }else{ ?>
                                $("#adicionarAdd2").prop( "disabled", false );
                            <?php } ?>
                        }
                        $(".act_vin_nom").change(function(){
                            var tar3 = $(".act_vin_tar").val();
                            var nom3 = $(".act_vin_nom").val();
                            if (tar3 != "" && nom3 !="") {
                                if (total < 1) {
                                    total = total + 1;
                                }
                                $("#adicionarAdd2").prop( "disabled", false );
                            }else if (tar3 == "" && nom3 =="") {
                                if (total == 1) {
                                    total = total - 1;
                                    $(".act_vin_nom").prop('required',false);
                                    $(".act_vin_tar").prop('required',false);
                                }
                            }else{
                                $("#adicionarAdd2").prop( "disabled", true );
                            }
                            $('#total2').val(total);
                        });
                        $(".act_vin_tar").change(function(){
                            var nom2 = $(".act_vin_nom").val();
                            var tar2 = $(".act_vin_tar").val();
                            if (nom2 != ""&& tar2 != "") {
                                if (total < 1) {
                                    total = total + 1;
                                }
                                $("#adicionarAdd2").prop( "disabled", false );
                            }else if (tar2 == "" && nom2 =="") {
                                if (total == 1) {
                                    total = total - 1;
                                    $(".act_vin_nom").prop('required',false);
                                    $(".act_vin_tar").prop('required',false);
                                }
                            }else{
                                $("#adicionarAdd2").prop( "disabled", true );
                            }
                            $('#total2').val(total);
                        });

                        $("#adicionarAdd2").on('click',function(){
                            $("#tablaAdd2 tbody tr:last").clone().removeClass('fila-fijaAdd2').appendTo("#tablaAdd2 tbody");
                            $("#tablaAdd2 tbody tr input.act_vin_nom:last").val('');
                            $("#tablaAdd2 tbody tr input.act_vin_tar:last").val('');
                            var tar2 = $(".act_vin_tar").val();
                            var nom2 = $(".act_vin_nom").val();
                            if (tar2!="" && nom2!="") {
                                total = total + 1;
                            }
                            $(".act_vin_nom").prop('required',true);
                            $(".act_vin_tar").prop('required',true);
                            $('#total2').val(total);
                        });
                        $(document).on("click",".eliminarAdd2",function(){
                            var parent = $(this).parents().get(0);
                            $(parent).remove();
                            total = total - 1;
                            $('#total2').val(total);
                        });
                        
                    });
                </script>
                <?php 
                $sql = "SELECT id_interno_lpp AS adj_id, nombre AS adj_nom, identificacion AS adj_url FROM reporte_interno_lpp ";
                $sql.=" WHERE id_reporte_lpp = '".$idReporteActual."' ";
                $PSN1->query($sql);
                $numero=$PSN1->num_rows();
                $cont = 0;
                echo '<input type="hidden" name="vin_regist" value="'.$numero.'" placeholder="">';
                if($numero > 0){
                    while($PSN1->next_record()){ ?>
                        <input type="hidden" name="act_vin_id[]" value="<?= $PSN1->f("adj_id");  ?>">
                    <?php }
                    $PSN1->query($sql);
                }
                ?>
                <table id="tablaAdd2" class="table table-bordered registro-table">
                    <tbody>
                    <?php
                    if($numero > 0){
                        while($PSN1->next_record()){ ?>
                            <tr <?php echo($cont==0)?'class="fila-fijaAdd2 registro-table-row"':'class="registro-table-row"'; ?>>
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del siervo facilitador:</strong>
                                    <input name="act_vin_nom[]" type="text" id="act_vin_nom" class="act_vin_nom form-control" value="<?=$PSN1->f("adj_nom"); ?>" required />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>Tarjeta dactilar / N° identificación:</strong>
                                    <input name="act_vin_tar[]" type="text" id="act_vin_tar" min="0" class="act_vin_tar form-control" value="<?=$PSN1->f("adj_url"); ?>" required />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd2"><button type="button" class="btn btn-cir-uno usua-col" title="Eliminar"><i class="fa fa-times"></i></button></td>
                            </tr>
                        <?php $cont++;
                        }
                    }else{ ?>
                        <tr class="fila-fijaAdd2 registro-table-row">
                            <td class="registro-col registro-col--nombre">
                                <strong>Nombre completo del siervo facilitador:</strong>
                                <input name="act_vin_nom[]" type="text" id="act_vin_nom" class="act_vin_nom form-control"  />
                            </td>
                            <td class="registro-col registro-col--identificacion">
                                <strong>Tarjeta dactilar / N° identificación:</strong>
                                <input name="act_vin_tar[]" type="text" id="act_vin_tar" min="0" class="act_vin_tar form-control"  />
                            </td>
                            <td class="registro-col registro-col--action eliminarAdd2"><button type="button" class="btn btn-cir-uno usua-col" title="Eliminar"><i class="fa fa-times"></i></button></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="registro-summary">
                    <div class="col-sm-6 registro-summary__text"><strong>Número de voluntarios internos activos en esta prisión:</strong></div>
                    <div class="col-sm-2 registro-summary__value">
                        <input type="text" name="total2" id="total2" class="form-control" value="<?=$bautizados; ?>" readonly>
                    </div>
                    <div class="col-sm-4 registro-summary__actions">
                        <center>
                            <button id="adicionarAdd2" class="btn btn-success" type="button" class="boton" <?= ($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte)?'disabled="disabled"':'';?>><i class="fas fa-plus"></i>  Adicionar</button>
                        </center>
                    </div>
                </div>
            </div>
        </div>
        <!--MODIFICAR REGISTRO DE VOLUNTARIOS EXTERNOS-->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">MODIFICAR DE VOLUNTARIOS EXTERNOS</h3>
                <p>A continuación por favor ingrese los datos requeridos</p>               
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 registro-table-wrap">
                <script>
                    $(function(){
                        var total = <?=$desiciones; ?>;
                        var tar = $(".act_vex_tar").val();
                        var nom = $(".act_vex_nom").val();
                        if (tar == "" || nom == "") {
                            $("#adicionarAdd3").prop( "disabled", true );
                        }else{
                            <?php if($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte){ ?>
                                $("#adicionarAdd3").prop( "disabled", true );
                            <?php }else{ ?>
                                $("#adicionarAdd3").prop( "disabled", false );
                            <?php } ?>
                        }
                        $(".act_vex_nom").change(function(){
                            var tar3 = $(".act_vex_tar").val();
                            var nom3 = $(".act_vex_nom").val();
                            if (tar3 != "" && nom3 !="") {
                                if (total < 1) {
                                    total = total + 1;
                                }
                                $("#adicionarAdd3").prop( "disabled", false );
                            }else if (tar3 == "" && nom3 =="") {
                                if (total == 1) {
                                    total = total - 1;
                                    $(".act_vex_nom").prop('required',false);
                                    $(".act_vex_tar").prop('required',false);
                                }
                            }else{
                                $("#adicionarAdd3").prop( "disabled", true );
                            }
                            $('#total3').val(total);
                        });
                        $(".act_vex_tar").change(function(){
                            var nom2 = $(".act_vex_nom").val();
                            var tar2 = $(".act_vex_tar").val();
                            if (nom2 != ""&& tar2 != "") {
                                if (total < 1) {
                                    total = total + 1;
                                }
                                $("#adicionarAdd3").prop( "disabled", false );
                            }else if (tar2 == "" && nom2 =="") {
                                if (total == 1) {
                                    total = total - 1;
                                    $(".act_vex_nom").prop('required',false);
                                    $(".act_vex_tar").prop('required',false);
                                }
                            }else{
                                $("#adicionarAdd3").prop( "disabled", true );
                            }
                            $('#total3').val(total);
                        });

                        $("#adicionarAdd3").on('click',function(){
                            $("#tablaAdd3 tbody tr:last").clone().removeClass('fila-fijaAdd3').appendTo("#tablaAdd3 tbody");
                            $("#tablaAdd3 tbody tr input.act_vex_nom:last").val('');
                            $("#tablaAdd3 tbody tr input.act_vex_tar:last").val('');
                            var tar2 = $(".act_vex_tar").val();
                            var nom2 = $(".act_vex_nom").val();
                            if (tar2!="" && nom2!="") {
                                total = total + 1;
                            }
                            $(".act_vex_nom").prop('required',true);
                            $(".act_vex_tar").prop('required',true);
                            $('#total3').val(total);
                        });
                        $(document).on("click",".eliminarAdd3",function(){
                            var parent = $(this).parents().get(0);
                            $(parent).remove();
                            total = total - 1;
                            $('#total3').val(total);
                        });
                        
                    });
                </script>
                <?php 
                $sql = "SELECT id_externo_lpp AS adj_id, nombre AS adj_nom, identificacion AS adj_url FROM reporte_externo_lpp ";
                $sql.=" WHERE id_reporte_lpp = '".$idReporteActual."' ";
                $PSN1->query($sql);
                $numero=$PSN1->num_rows();
                $cont = 0;
                echo '<input type="hidden" name="vex_regist" value="'.$numero.'" placeholder="">';
                if($numero > 0){
                    while($PSN1->next_record()){ ?>
                        <input type="hidden" name="act_vex_id[]" value="<?= $PSN1->f("adj_id");  ?>">
                    <?php }
                    $PSN1->query($sql);
                }
                ?>
                <table id="tablaAdd3" class="table table-bordered registro-table">
                    <tbody>
                    <?php
                    if($numero > 0){
                        while($PSN1->next_record()){ ?>
                            <tr <?php echo($cont==0)?'class="fila-fijaAdd3 registro-table-row"':'class="registro-table-row"'; ?>>
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del entrenador:</strong>
                                    <input name="act_vex_nom[]" type="text" id="act_vex_nom" class="act_vex_nom form-control" value="<?=$PSN1->f("adj_nom"); ?>" required />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>N° identificación:</strong>
                                    <input name="act_vex_tar[]" type="text" id="act_vex_tar" min="0" class="act_vex_tar form-control" value="<?=$PSN1->f("adj_url"); ?>" required />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd3"><button type="button" class="btn btn-cir-uno usua-col" title="Eliminar"><i class="fa fa-times"></i></button></td>
                            </tr>
                        <?php $cont++;
                        }
                    }else{ ?>
                        <tr class="fila-fijaAdd3 registro-table-row">
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del entrenador:</strong>
                                    <input name="act_vex_nom[]" type="text" id="act_vex_nom" class="act_vex_nom form-control"  />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>N° identificación:</strong>
                                    <input name="act_vex_tar[]" type="text" id="act_vex_tar" min="0" class="act_vex_tar form-control"  />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd3"><button type="button" class="btn btn-cir-uno usua-col" title="Eliminar"><i class="fa fa-times"></i></button></td>
                            </tr>
                    <?php } ?>
                    </tbody>
                </table>
        </div>
        <div class="form-group">
            <div class="col-sm-12">
                <div class="registro-summary">
                <div class="col-sm-6 registro-summary__text"><strong>Número de voluntarios externos para esta prisión:</strong> </div>
                <div class="col-sm-2 registro-summary__value">
                    <input type="text" name="total3" id="total3" class="form-control" value="<?=$desiciones; ?>" readonly>
                </div>
                <div class="col-sm-4 registro-summary__actions">
                    <center>
                        <button id="adicionarAdd3" class="btn btn-success" type="button" class="boton" ><i class="fas fa-plus"></i>  Adicionar</button>
                    </center>
                </div>
                </div>
            </div>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">MÉTODO DE VERIFICACIÓN</h3>
                <h5>TESTIMONIO</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
        <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-3">
            <strong>Número de discípulos que pasaron a C&M:</strong>
                <input name="rep_ndis" type="number" id="rep_ndis"  maxlength="255" value="<?=$rep_ndis; ?>" class="form-control" required />
            </div>
            <div class="col-sm-3">
                <strong>Testimonio:</strong>
                <?php if ($ext2!=""){?>
                    <a href='archivos/evi_<?=$idReporteActual; ?>_2.<?=$ext2; ?>' target="_blank"><i class="fas fa-file-word"></i> Formato testimonio LPP</a>
                <?php } ?>  
                <input name="archivo2" type="file" id="archivo2" class="form-control" />
            </div>
            <div class="col-sm-2">
                <strong>Costo de recursos gestionados($):</strong>
                <input name="rep_entr" type="number" id="rep_entr" min="0" value="<?= $rep_entr; ?>" class="form-control" />
            </div>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Método de verificación</h3>
                <h5>FOTO</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <?php if ($ext1!=""){?>
                    <center><img src="archivos/evi_<?=$idReporteActual; ?>_1.<?=$ext1; ?>" style="max-height:250px; max-width: 100%; "></center><br>  
                <?php } ?>                
                <input name="archivo1" type="file" id="archivo1" class="form-control" />
            </div>
            <div class="col-sm-3"></div>
        </div>
        <?php if ($_SESSION['perfil']!="168") {?>
        <div class="cont-btn cont-flex fl-sbet">
            <div class="item-btn">
                <input type="button" onClick="window.location.href='index.php?doc=consultar-sub-programa-lpp'" name="previous" class="previous btn btn-info" value="Cerrar" />
            </div>
            <div class="item-btn">
                <input type="submit" name="button" value="Guardar cambios" class="btn btn-success" id="guarda_rep">
            </div>
            <div class="item-btn">
                <input type="button" onClick="eliminarRegistro()" name="button" value="Eliminar" class="btn btn-danger">
            </div>
        </div>
        <?php } ?>         
        <input type="hidden" name="funcion" id="funcion" value="actualizar" />
        <input type="hidden" name="generacion" id="generacion" value="<?=$generacionActual; ?>" />
    </form>
    <script language="javascript">
        function sumar(){
            var asistencia_hom = 0;
            var asistencia_muj = 0;
            var asistencia_jov = 0;
            var asistencia_nin = 0;
            
            if(document.getElementById("final_asistencia_hom").value != ""){
                var asistencia_hom = document.getElementById("final_asistencia_hom").value;
            }
            if(document.getElementById("final_asistencia_muj").value != ""){
                var asistencia_muj = document.getElementById("final_asistencia_muj").value;
            }
            if(document.getElementById("final_asistencia_jov").value != ""){
                var asistencia_jov = document.getElementById("final_asistencia_jov").value;
            }
            if(document.getElementById("final_asistencia_nin").value != ""){
                var asistencia_nin = document.getElementById("final_asistencia_nin").value;
            }
            
            var var_suma = parseInt(asistencia_hom) + parseInt(asistencia_muj) + parseInt(asistencia_jov) + parseInt(asistencia_nin);
            //
            document.getElementById("final_asistencia_hom").value = parseInt(asistencia_hom);
            document.getElementById("final_asistencia_muj").value = parseInt(asistencia_muj);
            document.getElementById("final_asistencia_jov").value = parseInt(asistencia_jov);
            document.getElementById("final_asistencia_nin").value = parseInt(asistencia_nin);
            
            
            
            document.getElementById("final_bautizados").value = parseInt(bautizados) + 1;
            document.getElementById("final_discipulado").value = parseInt(var_suma) - 1;
            //
            document.getElementById("final_bautizadosPeriodo").value = parseInt(bautizadosPeriodo);
            
            //document.getElementById("final_desiciones").value = parseInt(var_suma) - 1;
            document.getElementById("final_preparandose").value = parseInt(var_suma) - 1 - parseInt(bautizadosPeriodo);                
        }            
        
        function eliminarRegistro(){
            if(confirm("Esta seguro que desea eliminar este registro, esta acción NO se puede deshacer.")){
                document.getElementById('funcion').value = "eliminar";
                document.getElementById('form1').submit();
            }                
        }
        
        function generarForm(generacion){
            sumar();
            <?php
            //if($_SESSION["perfil"] == 163){
                ?>
                $(':input[type="submit"]').prop('disabled', true);
                document.getElementById('funcion').value = "actualizar";
                //Completo el formulario  
                //document.getElementById('form1').submit();
                return true;
            <?php
            //}
            //else{
            //    /* //return false; */
            //}
            ?>
        }            

        function init(){
            document.getElementById('form1').onsubmit = function(){
                    return generarForm();
            }

        }        
        //
        window.onload = function(){
            init();
        }
    </script>
<?php }else if($preguntarGeneracion == 1){?>
    <script language="javascript">
        generarForm('LPP');
        function generarForm(generacion){
            if(generacion == "LPP"){
                document.getElementById('generacion').value = "LPP";
            }
            //Completo el formulario  
          document.getElementById('form1').submit();
        }            
        
        function init(){
            document.getElementById('form1').onsubmit = function(){
                    return generarForm();
            }
        }        
        window.onload = function(){
            init();
        }
    </script>        
<?php }else if(!isset($_REQUEST["id"])){
    $temp_accionForm = "insertar";
    $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
    //
    if(!isset($_REQUEST["fechaReporte"])){
        $fechaReporte = date("Y-m-d");        
    }else{
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
    }
    //
    $sql = "SELECT sat_grupos.nombre ";
    $sql.=" FROM sat_grupos ";
    $sql.=" WHERE sat_grupos.id = '".$idGrupoMadre."'";
    $sql.=" GROUP BY sat_grupos.id";
    $PSN1->query($sql);
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $nombreGrupoMadre =  $PSN1->f("nombre");
        }//chequear el registro
    }//chequear el numero
}else{
    $temp_accionForm = "actualizar";
    //  ID del usuario actual
    $idReporteActual = soloNumeros($_REQUEST["id"]);   
}
/*
*   SI SE INSERTO REGISTRO SE REDIRIGE
*/
?>
<style type="text/css">
    .report-shell{
        max-width: 1240px;
        margin: 24px auto 42px;
        padding: 0 14px 26px;
    }

    .report-shell .alert{
        border: 0;
        border-radius: 24px;
        padding: 18px 24px;
        margin-bottom: 20px;
        box-shadow: 0 18px 38px rgba(15, 45, 72, 0.12);
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .report-shell .alert-info{
        background: linear-gradient(135deg, #123e59 0%, #296d92 60%, #8ec6df 100%);
        color: #ffffff;
    }

    .report-shell .alert-success{
        background: linear-gradient(135deg, #28724f 0%, #39a46e 100%);
        color: #ffffff;
    }

    .report-shell .alert-warning{
        background: #fff5d7;
        color: #7a5a08;
        border: 1px solid #f2d78d;
    }

    .report-shell .alert-danger{
        background: #fff1f0;
        color: #8f2b24;
        border: 1px solid #f1b7b3;
    }

    .report-shell .alert a{
        color: inherit;
        text-decoration: none;
    }

    .report-shell .alert a:hover{
        text-decoration: underline;
    }

    .report-form{
        position: relative;
        overflow: hidden;
        background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
        border: 1px solid #dbe7f1;
        border-radius: 30px;
        padding: 24px 24px 34px;
        box-shadow: 0 24px 60px rgba(16, 46, 72, 0.12);
    }

    .report-form:before{
        content: "";
        position: absolute;
        top: -140px;
        right: -70px;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(85, 154, 196, 0.18) 0%, rgba(85, 154, 196, 0) 70%);
        pointer-events: none;
    }

    .report-form > .col-sm-12{
        position: relative;
        width: 100%;
        margin-bottom: 22px;
        padding: 24px 22px 16px;
        border-radius: 24px;
        border: 1px solid #e4edf4;
        background: rgba(255, 255, 255, 0.94);
        box-shadow: 0 14px 34px rgba(22, 53, 80, 0.08);
    }

    .report-form > .col-sm-12:last-of-type{
        margin-bottom: 0;
    }

    .report-form .cont-tit{
        display: flex;
        align-items: center;
        gap: 18px;
        margin-bottom: 20px;
    }

    .report-form .cont-tit .hr{
        flex: 1 1 0;
    }

    .report-form .cont-tit hr{
        margin: 10px 0;
        border-top: 1px solid #cad8e4;
    }

    .report-form .cont-tit .tit-cen{
        flex: 0 1 auto;
        min-width: 280px;
        padding: 12px 20px;
        border-radius: 18px;
        background: linear-gradient(135deg, #f1f7fb 0%, #ffffff 100%);
        border: 1px solid #d9e6f0;
        text-align: center;
    }

    .report-form .cont-tit h3{
        margin: 0 0 4px;
        color: #19344c;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 24px;
        font-weight: 700;
    }

    .report-form .cont-tit h5,
    .report-form .cont-tit p{
        margin: 0;
        color: #577086;
        font-size: 14px;
        line-height: 1.5;
    }

    .report-form .form-group{
        margin-left: -8px;
        margin-right: -8px;
        margin-bottom: 4px;
    }

    .report-form .form-group > [class*="col-sm-"]{
        padding-left: 8px;
        padding-right: 8px;
        margin-bottom: 16px;
    }

    .report-form strong{
        display: block;
        margin-bottom: 8px;
        color: #243c55;
        font-size: 13px;
        letter-spacing: 0.2px;
    }

    .report-form .form-control,
    .report-form select,
    .report-form textarea{
        min-height: 46px;
        height: auto;
        border-radius: 14px;
        border: 1px solid #c7d6e2;
        background: #f8fbfd;
        box-shadow: none;
        color: #1f3348;
        transition: border-color 0.18s ease, box-shadow 0.18s ease, background-color 0.18s ease;
    }

    .report-form textarea.form-control{
        min-height: 120px;
    }

    .report-form .form-control:focus,
    .report-form select:focus,
    .report-form textarea:focus{
        border-color: #4f8db6;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(79, 141, 182, 0.14);
    }

    .report-form .form-control[readonly],
    .report-form .form-control[disabled],
    .report-form select[disabled],
    .report-form textarea[disabled]{
        background: #eef4f8;
        color: #53687b;
    }

    .report-form input[type="file"].form-control{
        padding: 10px 12px;
    }

    .report-form .btn{
        border-radius: 14px;
        padding: 11px 18px;
        font-weight: 700;
        letter-spacing: 0.2px;
        border: 0;
        box-shadow: 0 12px 24px rgba(20, 56, 86, 0.14);
        transition: transform 0.18s ease, box-shadow 0.18s ease, filter 0.18s ease;
    }

    .report-form .btn:hover,
    .report-form .btn:focus{
        transform: translateY(-1px);
        box-shadow: 0 16px 26px rgba(20, 56, 86, 0.18);
        filter: brightness(1.02);
    }

    .report-form .btn-success{
        background: linear-gradient(135deg, #2d8658 0%, #39a977 100%);
        color: #ffffff;
    }

    .report-form .btn-info{
        background: linear-gradient(135deg, #2c6d8f 0%, #3d90bb 100%);
        color: #ffffff;
    }

    .report-form .btn-warning{
        background: linear-gradient(135deg, #d89d2f 0%, #efb64c 100%);
        color: #ffffff;
    }

    .report-form .btn-danger{
        background: linear-gradient(135deg, #c54c43 0%, #df6a61 100%);
        color: #ffffff;
    }

    .report-form .btn-cir-uno,
    .report-form .btn-eliminar-fila{
        width: 42px;
        height: 42px;
        min-width: 42px;
        padding: 0;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .report-form #tablaAdd,
    .report-form #tablaAdd2,
    .report-form #tablaAdd3{
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        border-radius: 18px;
        border: 1px solid #dce7ef;
        background: #f9fbfd;
    }

    .report-form #tablaAdd td,
    .report-form #tablaAdd2 td,
    .report-form #tablaAdd3 td{
        padding: 16px 14px;
        vertical-align: top;
        border-top: 1px solid #e2eaf1;
    }

    .report-form #tablaAdd tr:first-child td,
    .report-form #tablaAdd2 tr:first-child td,
    .report-form #tablaAdd3 tr:first-child td{
        border-top: 0;
    }

    .report-shell .cont-btn{
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .report-shell .cont-btn.fl-cent{
        justify-content: center;
    }

    .report-shell .item-btn{
        margin: 0;
    }

    .report-shell .item-btn .btn,
    .report-shell .item-btn input.btn{
        min-width: 180px;
    }

    @media (max-width: 991px){
        .report-shell{
            padding: 0 12px 22px;
        }

        .report-form{
            padding: 18px 16px 26px;
            border-radius: 24px;
        }

        .report-form > .col-sm-12{
            padding: 20px 16px 12px;
            border-radius: 20px;
        }

        .report-form .cont-tit{
            flex-direction: column;
            gap: 10px;
        }

        .report-form .cont-tit .hr{
            width: 100%;
        }

        .report-form .cont-tit .tit-cen{
            width: 100%;
            min-width: 0;
        }
    }

    @media (max-width: 767px){
        .report-shell{
            margin-top: 14px;
            padding: 0 10px 18px;
        }

        .report-shell .alert{
            border-radius: 18px;
            padding: 16px 18px;
            font-size: 18px;
        }

        .report-form .form-group{
            margin-left: 0;
            margin-right: 0;
        }

        .report-form .form-group > [class*="col-sm-"]{
            padding-left: 0;
            padding-right: 0;
            margin-bottom: 14px;
        }

        .report-form [class*="col-sm-"]:empty{
            display: none;
        }

        .report-form #tablaAdd td,
        .report-form #tablaAdd2 td,
        .report-form #tablaAdd3 td{
            display: block;
            width: 100% !important;
        }

        .report-shell .cont-btn{
            justify-content: center;
        }

        .report-shell .item-btn,
        .report-shell .item-btn .btn,
        .report-shell .item-btn input.btn{
            width: 100%;
        }

        .report-form center .btn{
            width: 100%;
            max-width: none;
        }
    }
</style>
<style type="text/css">
    .report-shell{
        max-width: 1260px;
        margin: 28px auto 40px;
        padding: 0 18px 24px;
    }

    .report-shell .alert{
        background: #ffffff;
        color: #1f2933;
        border: 1px solid #d9dee3;
        border-left-width: 4px;
        border-radius: 12px;
        padding: 18px 22px;
        margin-bottom: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .report-shell .alert-info{
        background: #f8f9fb;
        color: #1f2d3a;
        border-left-color: #243746;
    }

    .report-shell .alert-success{
        background: #f7f9f7;
        color: #27352d;
        border-left-color: #56685a;
    }

    .report-shell .alert-warning{
        background: #fbf8f2;
        color: #6d5634;
        border-left-color: #8a6a3f;
    }

    .report-shell .alert-danger{
        background: #fbf6f5;
        color: #6b3b38;
        border-left-color: #7a4340;
    }

    .report-form{
        background: #f5f5f3;
        border: 1px solid #d7dbdf;
        border-radius: 18px;
        padding: 28px 26px 34px;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
    }

    .report-form:before{
        display: none;
    }

    .report-form > .col-sm-12{
        margin-bottom: 18px;
        padding: 24px 22px 18px;
        border-radius: 14px;
        border: 1px solid #e1e4e8;
        background: #ffffff;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.04);
    }

    .report-form .cont-tit{
        gap: 16px;
        margin: 0 0 24px;
    }

    .report-form .cont-tit hr{
        margin: 0;
        border: 0;
        border-top: 1px solid #d7dce1;
    }

    .report-form .cont-tit .tit-cen{
        width: auto;
        max-width: 560px;
        min-width: 320px;
        padding: 14px 22px 12px;
        border-radius: 12px;
        background: #f8f9fa;
        border: 1px solid #d9dee3;
        text-align: center;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.8);
    }

    .report-form .cont-tit h3{
        margin: 0;
        color: #1e2d3a;
        font-family: Georgia, "Times New Roman", serif;
        font-size: 23px;
        font-weight: 700;
        letter-spacing: 0.02em;
    }

    .report-form .cont-tit h5{
        margin: 5px 0 0;
        color: #52606d;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .report-form .cont-tit p{
        margin: 6px 0 0;
        color: #5b6875;
        font-size: 13px;
        line-height: 1.5;
    }

    .report-form .form-group{
        margin-left: -10px;
        margin-right: -10px;
        margin-bottom: 2px;
    }

    .report-form .form-group > [class*="col-sm-"]{
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 18px;
    }

    .report-form strong{
        margin-bottom: 7px;
        color: #24313d;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: 0.04em;
        line-height: 1.45;
    }

    .report-form .form-control,
    .report-form select,
    .report-form textarea{
        padding: 11px 14px;
        border-radius: 10px;
        border: 1px solid #cfd5db;
        background: #ffffff;
        color: #1f2933;
    }

    .report-form .form-control:focus,
    .report-form select:focus,
    .report-form textarea:focus{
        border-color: #334e68;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(51, 78, 104, 0.10);
    }

    .report-form .form-control[readonly],
    .report-form .form-control[disabled],
    .report-form select[disabled],
    .report-form textarea[disabled]{
        background: #f4f5f6;
        color: #5f6b76;
    }

    .report-form input[type="file"].form-control{
        padding: 8px 10px;
    }

    .report-form .btn{
        border-radius: 10px;
        padding: 11px 18px;
        letter-spacing: 0.03em;
        border: 1px solid transparent;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
    }

    .report-form .btn:hover,
    .report-form .btn:focus{
        box-shadow: 0 12px 22px rgba(15, 23, 42, 0.10);
        filter: brightness(1.01);
    }

    .report-form .btn-success{
        background: #1f3547;
        border-color: #1f3547;
        color: #ffffff;
    }

    .report-form .btn-info{
        background: #4b5b68;
        border-color: #4b5b68;
        color: #ffffff;
    }

    .report-form .btn-warning{
        background: #8a6a3f;
        border-color: #8a6a3f;
        color: #ffffff;
    }

    .report-form .btn-danger{
        background: #7a4340;
        border-color: #7a4340;
        color: #ffffff;
    }

    .report-form .btn-cir-uno,
    .report-form .btn-eliminar-fila{
        width: 40px;
        height: 40px;
        min-width: 40px;
    }

    .report-form #tablaAdd,
    .report-form #tablaAdd2,
    .report-form #tablaAdd3{
        border-radius: 14px;
        border: 1px solid #e0e4e7;
        background: #ffffff;
    }

    .report-form #tablaAdd td,
    .report-form #tablaAdd2 td,
    .report-form #tablaAdd3 td{
        border-top: 1px solid #eceff2;
    }

    .report-shell .cont-btn{
        margin-bottom: 20px;
    }

    @media (max-width: 991px){
        .report-shell{
            padding: 0 14px 22px;
        }

        .report-form{
            padding: 22px 18px 28px;
            border-radius: 16px;
        }

        .report-form > .col-sm-12{
            padding: 20px 16px 14px;
            border-radius: 12px;
        }

        .report-form .cont-tit{
            gap: 12px;
        }

        .report-form .cont-tit .tit-cen{
            width: 100%;
            min-width: 0;
            max-width: none;
        }
    }

    @media (max-width: 767px){
        .report-shell{
            margin-top: 16px;
            padding: 0 10px 16px;
        }

        .report-shell .alert{
            padding: 16px 18px;
            border-radius: 10px;
            font-size: 16px;
        }
    }
</style>
<style type="text/css">
    .report-shell .alert,
    .report-shell .alert-info,
    .report-shell .alert-success,
    .report-shell .alert-warning,
    .report-shell .alert-danger{
        background: #ffffff;
        color: #20262d;
        border: 1px solid #d7dce1;
        border-left: 4px solid #2d3338;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
    }

    .report-form{
        background: #f7f7f7;
        border: 1px solid #d8d8d8;
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.05);
    }

    .report-form:before{
        display: none !important;
    }

    .report-form > .col-sm-12{
        background: #ffffff;
        border: 1px solid #e1e1e1;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    }

    .report-form .cont-tit .tit-cen{
        background: #fbfbfb;
        border: 1px solid #d9d9d9;
        box-shadow: none;
    }

    .report-form .cont-tit hr{
        border-top-color: #d0d0d0;
    }

    .report-form .cont-tit h3{
        color: #111111;
    }

    .report-form .cont-tit h5,
    .report-form .cont-tit p{
        color: #555555;
    }

    /* Alineación de etiquetas — misma altura mínima para que los campos queden al mismo nivel */
    .report-form .form-group > [class*="col-sm-"] > strong:first-child{
        min-height: 2.6em;
        display: flex;
        align-items: flex-end;
        padding-bottom: 6px;
    }

    /* Separación entre secciones del formulario */
    .report-form > .col-sm-12 + .col-sm-12{
        margin-top: 6px;
    }

    /* Botón guardar más prominente */
    .report-form .cont-btn.fl-cent .btn-success{
        min-width: 220px;
        padding: 13px 28px;
        font-size: 15px;
    }

    .report-form .form-control,
    .report-form select,
    .report-form textarea{
        background: #ffffff;
        border: 1px solid #cfcfcf;
        color: #1f1f1f;
    }

    .report-form .form-control:focus,
    .report-form select:focus,
    .report-form textarea:focus{
        border-color: #6f7780;
        box-shadow: 0 0 0 3px rgba(70, 70, 70, 0.08);
    }

    .report-form .btn,
    .report-form input.btn{
        background: #f3f3f3;
        border-color: #cccccc;
        color: #222222;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .report-form .btn-success,
    .report-form .btn-info,
    .report-form .btn-warning{
        background: #f3f3f3;
        border-color: #cccccc;
        color: #222222;
    }

    #adicionarAdd,
    #adicionarAdd2,
    #adicionarAdd3,
    #generarVariasAdd{
        background: #2f7d32 !important;
        border-color: #2f7d32 !important;
        color: #ffffff !important;
    }

    #borrarTodoAdd,
    .report-form .btn-danger,
    .report-form .btn-cir-uno,
    .report-form .btn-eliminar-fila{
        background: #c0392b !important;
        border-color: #c0392b !important;
        color: #ffffff !important;
    }

    .report-form input[type="submit"][name="button"],
    .report-form input[type="submit"][value="Guardar"]{
        background: #2b5daa !important;
        border-color: #2b5daa !important;
        color: #ffffff !important;
    }

    /* ---- Fila 2: cárcel + campos AJAX de ubicación ---- */
    .report-form .lpp-fila-ubicacion{
        align-items: flex-start;
        margin-top: -4px;
    }

    .report-form .lpp-ubicacion-ajax{
        display: flex;
        flex-wrap: wrap;
        flex: 1 1 0;
        gap: 0;
    }

    .report-form .lpp-ubicacion-ajax > [class*="col-sm-"],
    .report-form .lpp-ubicacion-ajax [class*="col-sm-"]{
        float: none;
        flex: 1 1 160px;
        padding-left: 10px;
        padding-right: 10px;
        margin-bottom: 18px;
    }

    /* ---- Fila de información general: centrada horizontalmente ---- */
    .report-form .lpp-info-general-row{
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: flex-start;
        margin-left: -10px;
        margin-right: -10px;
        gap: 0;
    }

    .report-form .lpp-info-general-row > [class*="col-sm-"]{
        float: none;
    }

    /* ---- Tablas de registro: diseño fino tipo card por fila ---- */
    .report-form .registro-table-wrap{
        float: none;
        width: 100% !important;
        max-width: none !important;
        margin: 0;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .report-form .registro-table{
        width: 100% !important;
        max-width: none !important;
        margin: 0;
        table-layout: fixed;
        border: 0 !important;
        border-collapse: separate !important;
        border-spacing: 0 10px !important;
        background: transparent !important;
    }

    /* Cada fila como una tarjeta con borde izquierdo de acento */
    .report-form .registro-table > tbody > tr{
        transition: box-shadow 0.15s ease;
    }

    .report-form .registro-table > tbody > tr:hover > td{
        background: #f8f9fa !important;
    }

    .report-form .registro-table > tbody > tr > td,
    .report-form .registro-table > tr > td{
        padding: 16px 18px !important;
        vertical-align: middle !important;
        border: 1px solid #e4e8ec !important;
        border-left-width: 0 !important;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        transition: background 0.15s ease;
    }

    .report-form .registro-table > tbody > tr > td:first-child,
    .report-form .registro-table > tr > td:first-child{
        border-left: 3px solid #c0c0c0 !important;
        border-radius: 8px 0 0 8px;
    }

    .report-form .registro-section.registro-graduados .registro-table > tbody > tr > td:first-child{
        border-left-color: #3a6ea5 !important;
    }

    .report-form .registro-section.registro-internos .registro-table > tbody > tr > td:first-child{
        border-left-color: #2e7d52 !important;
    }

    .report-form .registro-section.registro-externos .registro-table > tbody > tr > td:first-child{
        border-left-color: #7a4ca0 !important;
    }

    .report-form .registro-table > tbody > tr > td:last-child,
    .report-form .registro-table > tr > td:last-child{
        border-radius: 0 8px 8px 0;
    }

    /* Número de fila — indicador visual sutil */
    .report-form .registro-table > tbody > tr{
        counter-increment: fila-registro;
    }

    .report-form .registro-table .registro-col--nombre{
        width: 50%;
    }

    .report-form .registro-table .registro-col--identificacion{
        width: 37%;
    }

    .report-form .registro-table .registro-col--action{
        width: 13%;
        text-align: center;
        vertical-align: middle !important;
        padding: 16px 10px !important;
    }

    .report-form .registro-table strong{
        display: block;
        width: 100%;
        min-height: 0 !important;
        margin-bottom: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #6b7a8d;
    }

    .report-form .registro-table input.form-control{
        width: 100% !important;
        min-height: 40px !important;
        margin-top: 0;
        border-radius: 6px !important;
        font-size: 14px;
    }

    .report-form .registro-table .btn-eliminar-fila,
    .report-form .registro-table .btn-cir-uno{
        margin: 0 auto;
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 36px !important;
        height: 36px !important;
        min-width: 36px !important;
        border-radius: 8px !important;
        font-size: 14px;
        opacity: 0.75;
        transition: opacity 0.15s ease, transform 0.15s ease;
    }

    .report-form .registro-table .btn-eliminar-fila:hover,
    .report-form .registro-table .btn-cir-uno:hover{
        opacity: 1;
        transform: scale(1.08);
    }

    /* Panel de controles (resumen + botones) */
    .report-form .registro-summary,
    .report-form .registro-bulk-controls{
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px 16px;
        width: 100%;
        padding: 14px 18px;
        border: 1px solid #e4e8ec;
        border-radius: 10px;
        background: #f9fafb;
        margin-top: 4px;
    }

    .report-form .registro-summary > [class*="col-sm-"],
    .report-form .registro-bulk-controls > [class*="col-sm-"]{
        float: none;
        width: auto;
        padding: 0;
        margin: 0;
    }

    .report-form .registro-summary > :first-child,
    .report-form .registro-bulk-controls > :first-child{
        flex: 1 1 300px;
        min-width: 0;
    }

    .report-form .registro-summary > :nth-child(2),
    .report-form .registro-bulk-controls > :nth-child(2){
        flex: 0 0 140px;
        max-width: 160px;
        min-width: 110px;
    }

    .report-form .registro-summary > :nth-child(3){
        display: none;
    }

    .report-form .registro-summary > :last-child,
    .report-form .registro-bulk-controls > :last-child{
        flex: 1 1 200px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .report-form .registro-summary > :last-child > center,
    .report-form .registro-bulk-controls > :last-child > center{
        width: 100%;
    }

    .report-form .registro-summary__text,
    .report-form .registro-bulk-controls__text{
        flex: 1 1 300px;
        min-width: 0;
    }

    .report-form .registro-summary__text strong,
    .report-form .registro-bulk-controls__text label{
        display: block;
        width: 100%;
        margin: 0;
        min-height: 0 !important;
        font-size: 13px;
        text-transform: none;
        letter-spacing: 0;
        color: #374151;
    }

    .report-form .registro-summary__value,
    .report-form .registro-bulk-controls__value{
        flex: 0 0 130px;
        max-width: 150px;
        min-width: 100px;
    }

    .report-form .registro-summary__value .form-control,
    .report-form .registro-bulk-controls__value .form-control{
        width: 100%;
        text-align: center;
        font-weight: 700;
        font-size: 15px;
        border-radius: 6px !important;
        background: #ffffff;
    }

    .report-form .registro-summary__actions,
    .report-form .registro-bulk-controls__actions{
        flex: 1 1 200px;
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 10px;
    }

    .report-form .registro-summary__actions > [class*="col-sm-"],
    .report-form .registro-bulk-controls__actions > [class*="col-sm-"]{
        float: none;
        width: auto;
        padding: 0;
        margin: 0;
    }

    .report-form .registro-summary__actions .btn,
    .report-form .registro-bulk-controls__actions .btn,
    .report-form .registro-summary > :last-child .btn,
    .report-form .registro-bulk-controls > :last-child .btn{
        min-width: 140px;
        white-space: nowrap;
        border-radius: 8px !important;
        font-size: 13px;
        padding: 9px 16px;
    }

    /* Separador visual entre secciones de registro */
    .report-form .registro-section{
        border-top: 2px solid #e8e8e8 !important;
        background: #ffffff !important;
    }

    .report-form .registro-section.registro-graduados{
        border-top-color: #3a6ea5 !important;
    }

    .report-form .registro-section.registro-internos{
        border-top-color: #2e7d52 !important;
    }

    .report-form .registro-section.registro-externos{
        border-top-color: #7a4ca0 !important;
    }

    @media (max-width: 767px){
        .report-form .lpp-info-general-row > [class*="col-sm-"]{
            width: 100%;
        }

        .report-form .form-group > [class*="col-sm-"] > strong:first-child{
            min-height: 0;
        }

        .report-form .registro-table{
            border-spacing: 0 10px !important;
        }

        .report-form .registro-table > tbody > tr,
        .report-form .registro-table > tr{
            display: block;
        }

        .report-form .registro-table > tbody > tr > td,
        .report-form .registro-table > tr > td{
            display: block;
            width: 100% !important;
            border-left-width: 1px !important;
            border-radius: 0 !important;
        }

        .report-form .registro-table > tbody > tr > td:first-child,
        .report-form .registro-table > tr > td:first-child{
            border-left-width: 3px !important;
            border-radius: 8px 8px 0 0 !important;
        }

        .report-form .registro-table > tbody > tr > td:last-child,
        .report-form .registro-table > tr > td:last-child{
            border-top: 0 !important;
            border-radius: 0 0 8px 8px !important;
        }

        .report-form .registro-table .registro-col--action{
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .report-form .registro-summary,
        .report-form .registro-bulk-controls{
            padding: 14px;
        }

        .report-form .registro-summary__text,
        .report-form .registro-summary__value,
        .report-form .registro-summary__actions,
        .report-form .registro-bulk-controls__text,
        .report-form .registro-bulk-controls__value,
        .report-form .registro-bulk-controls__actions{
            flex: 1 1 100%;
            max-width: none;
        }

        .report-form .registro-summary__actions,
        .report-form .registro-bulk-controls__actions{
            justify-content: stretch;
        }

        .report-form .registro-summary__actions .btn,
        .report-form .registro-bulk-controls__actions .btn,
        .report-form .registro-summary > :last-child .btn,
        .report-form .registro-bulk-controls > :last-child .btn{
            width: 100%;
            min-width: 0;
        }

        .report-form .registro-summary > :last-child,
        .report-form .registro-bulk-controls > :last-child{
            width: 100%;
            justify-content: stretch;
        }

        .report-form .registro-summary__actions > [class*="col-sm-"],
        .report-form .registro-bulk-controls__actions > [class*="col-sm-"]{
            width: 100%;
        }
    }
</style>
<?php
if($idReporteActual > 0){
    //No hacemos nada.
}else if($varExitoREP == 1){?>
    <div class="container report-shell">
        <div class="row">
            <h2 class="alert alert-info text-center"><?php
            if($idReporteActual == 0){
                echo "REPORTE";
            }
            else{
                echo "ACTUALIZACIÓN";
            }
            ?> DE <?=$temp_letrero; ?></h2>
        </div>

        <div class="row">
            <h2 class="alert alert-success text-center"><a href="index.php?doc=gestionar-sub-programa-lpp&opc=2&id=<?=$ultimoId; ?>" class="h2">Se ha <?php
            if($idReporteActual == 0){
                echo "creado";
            }
            else{
                echo "actualizado";
            }
            ?> correctamente el registro, para ver el reporte de clic aquí</a>.</h2>
        </div>
    </div>
    <script type="text/javascript">
    /* El reporte fue guardado exitosamente — limpiar el borrador local */
    (function(){
        try {
            var STORAGE_KEY = 'lpp_autosave_' + (window.location.search || 'nuevo');
            localStorage.removeItem(STORAGE_KEY);
        } catch(e) {}
    })();
    </script>
<?php }else if($idReporteActual == 0){?>
    <style type="text/css">
        #form1 fieldset:not(:first-of-type){
            display: none;
        }
    </style>
<div class="container report-shell">
    <div class="row">
        <h3 class="alert alert-info text-center"><?php
            if($idReporteActual == 0){
                echo "REPORTE";
            }else{
                echo "ACTUALIZACIÓN";
            }?> DE <?=$temp_letrero; ?></h3>
    </div>

    <?php
    //
    if($varExitoREP_UPD == 1){
        ?><div class="row">
            <h5 class="alert alert-warning text-center">Se ha actualizado correctamente el registro.</h5>
        </div><?php
    }
    //
    if($texto_error != ""){
        ?><div class="row">
            <h5 class="alert alert-danger text-center"><?=$texto_error; ?></h5>
        </div><?php
    }

    //
    if($errorLogueo == 1){
        ?><div class="row"><h1><font color="red"><u>ATENCION:</u> NO SE CREO EL INFORME<BR /><u>MOTIVO:</u> YA EXISTE UN INFORME CON ESE VEHÍCULO Y FECHA.<br />POR FAVOR VERIFIQUE.</font></h1></div><?php
    }
    //
    if($error_fatal == 1){
        //No hacer nada.
    }else{?>
    <!-----FORMULARIO DE REGISTO LPP---->
        <!--<div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
        </div>-->
        <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal report-form">
            <input name="fechaReporte" type="hidden" id="fechaReporte" value="<?=$fechaReporte; ?>" />
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Información general</h3>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div> 
                <!-- Fila 1: Coordinador | Fecha | Período | Pabellón -->
                <div class="form-group lpp-info-general-row">
                    <div class="col-sm-3">
                        <strong>Coordinador de prisión:</strong>
                        <select required name="usua_id" id="usua_id" class="form-control">
                            <option value="<?=$_SESSION["id"]; ?>"><?=$_SESSION["nombre"]; ?></option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <strong>Fecha del registro:</strong>
                        <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required readonly />
                    </div>
                    <div class="col-sm-3">
                        <?php $mes = date("m"); ?>
                        <strong>Período:</strong>
                        <select name="mapeo_cuarto" class="form-control">
                            <?php echo($mes>=1 && $mes<=3)?'<option value="1" selected>Q1 (Ene - Mar)</option>':''; ?>
                            <?php echo($mes>=4 && $mes<=6)?'<option value="4" selected>Q2 (Abr - Jun)</option>':''; ?>
                            <?php echo($mes>=7 && $mes<=9)?'<option value="7" selected>Q3 (Jul - Sep)</option>':''; ?>
                            <?php echo($mes>=10 && $mes<=12)?'<option value="10" selected>Q4 (Oct - Dic)</option>':''; ?>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <strong>N° de patios / pabellón:</strong>
                        <input name="pabellon" type="number" id="pabellon" maxlength="250" value="<?=$pabellon; ?>" min="1" class="form-control" required />
                    </div>
                </div>
                <!-- Fila 2: Cárcel/Ubicación | Departamento | Municipio | Dirección (AJAX) -->
                <div class="form-group lpp-info-general-row lpp-fila-ubicacion">
                    <div class="col-sm-3 lpp-carcel-col">
                        <strong>Cárcel / Ubicación:</strong>
                        <select required name="sitioReunion" id="rep_carcel" class="form-control">
                            <?php
                            if ($_SESSION['empresa_pd'] != "") {
                                echo '<option value="">Sin especificar</option>';
                                $sql = "SELECT * FROM tbl_regional_ubicacion";
                                if($_SESSION['empresa_pd'] != 0){
                                    $sql.=" WHERE reub_reg_fk = ".$_SESSION['empresa_pd'];
                                }
                                $sql.=" ORDER BY reub_reg_fk asc";
                                $PSN1->query($sql);
                                if($PSN1->num_rows() > 0){
                                    while($PSN1->next_record()){
                                        ?><option value="<?=$PSN1->f('reub_id'); ?>" <?php
                                        if($cliente_servicio1 == $PSN1->f('reub_id')) echo 'selected="selected"';
                                        ?>><?=$PSN1->f('reub_nom'); ?></option><?php
                                    }
                                }
                            }else{
                                echo '<option value="">Sin regional asignada</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div id="ubicacion" class="lpp-ubicacion-ajax"></div>
                </div>
                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn"></div>
                    <div class="item-btn">
                        <input type="button" name="next" class="next btn btn-success" id="secc-1" value="Siguiente" />
                    </div>
                </div>          
            </fieldset>--></div>
        <!--INFORMACIÓN DE LA PRISION-->
            <!--<fieldset>--><div class="col-sm-12">
                
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Información de la prisión</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3">
                        <strong>Total población en la prisión:</strong>
                        <input name="asistencia_total" type="number" id="asistencia_total" min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Prisioneros invitados:</strong>
                        <input name="asistencia_hom" type="number" id="asistencia_hom" min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Prisioneros que iniciaron el curso:</strong>
                        <input name="asistencia_muj" type="number" id="asistencia_muj" min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Cursos activos de LPP:</strong>
                        <input name="asistencia_jov" type="number" id="asistencia_jov" min="0" value="" readonly class="form-control" />
                    </div>
                </div>
                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig2" class="next btn btn-success" value="Siguiente" />
                    </div>
                </div>
            </fieldset>--></div>
        <?php if (false) { ?>
        <!--REGISTRO DE GRADUADOS--->
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">REGISTRO DE GRADUADOS</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <script>
                            $(function(){
                                var total = 0;
                                var tar = $(".act_grad_tar").val();
                                var nom = $(".act_grad_nom").val();
                                $("#adicionarAdd").prop( "disabled", true );
                                //$("#asistencia_total").prop('required',true);

                                if (tar == "" || nom == "") {
                                    $("#adicionarAdd").prop( "disabled", true );
                                }else{
                                    $("#adicionarAdd").prop( "disabled", false );
                                }
                                
                                $("#asistencia_hom").change(function(){
                                    var vtotal = $("#asistencia_hom").val();
                                    $("#asistencia_muj").attr('max', vtotal);
                                });
                                $("#asistencia_total").change(function(){
                                    var vtotal = $("#asistencia_total").val();
                                    $("#asistencia_hom").attr('max', (vtotal-1));
                                });
                                $("#total").change(function(){
                                    var totalG = $("#total").val();
                                    $("#rep_ndis").attr('max', totalG);
                                });
                                $("#rep_ndis").change(function(){
                                    var totalG = $("#total").val();
                                    $("#rep_ndis").attr('max', totalG);
                                });
                                $("#asistencia_muj").change(function(){
                                    var vtotal = $("#asistencia_muj").val();
                                    if (total >= vtotal) {
                                        $("#adicionarAdd").prop( "disabled", true );
                                    }else{
                                        $("#adicionarAdd").prop( "disabled", false );
                                    }
                                });
                                
                                $(".act_grad_nom").change(function(){
                                    var vtotal = $("#asistencia_muj").val();
                                    var tar3 = $(".act_grad_tar").val();
                                    var nom3 = $(".act_grad_nom").val();
                                    if (tar3 != "" && nom3 !="") {
                                        if (total < 1) {
                                            total = total + 1;
                                        }
                                        $("#adicionarAdd").prop( "disabled", false );
                                    }else if (tar3 == "" && nom3 =="") {
                                        if (total == 1) {
                                            total = total - 1;
                                            $(".act_grad_nom").prop('required',false);
                                            $(".act_grad_tar").prop('required',false);
                                        }
                                    }else{
                                        $("#adicionarAdd").prop( "disabled", true );
                                    }
                                    $('#total').val(total);
                                });
                                $(".act_grad_tar").change(function(){
                                    var vtotal = $("#asistencia_muj").val();
                                    var nom2 = $(".act_grad_nom").val();
                                    var tar2 = $(".act_grad_tar").val();
                                    if (nom2 != ""&& tar2 != "") {
                                        if (total < 1) {
                                            total = total + 1;
                                        }
                                        $("#adicionarAdd").prop( "disabled", false );
                                    }else if (tar3 == "" && nom3 =="") {
                                        if (total == 1) {
                                            total = total - 1;
                                            $(".act_grad_nom").prop('required',false);
                                            $(".act_grad_tar").prop('required',false);
                                        }
                                    }else{
                                        $("#adicionarAdd").prop( "disabled", true );
                                    }
                                    $('#total').val(total);
                                });

                                $("#adicionarAdd").on('click',function(){
                                    var vtotal = $("#asistencia_muj").val();
                                    var tar2 = $(".act_grad_tar").val();
                                    var nom2 = $(".act_grad_nom").val();
                                    if (tar2!="" && nom2!="") {
                                        $("#tablaAdd tr:last").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");
                                        $("#tablaAdd tr input.act_grad_nom:last").val('');
                                        $("#tablaAdd tr input.act_grad_tar:last").val('');
                                        total = total + 1;
                                    }else{
                                        alert("Ingrese toda la información antes de agregar otro campo");
                                    }
                                    
                                    if (total >= vtotal) {
                                        $("#adicionarAdd").prop( "disabled", true );
                                    }else{
                                        $("#adicionarAdd").prop( "disabled", false );
                                    }
                                    $(".act_grad_nom").prop('required',true);
                                    $(".act_grad_tar").prop('required',true);
                                    $('#total').val(total);
                                    var totalG = $("#total").val();
                                    $("#rep_ndis").attr('max', totalG);
                                });
                                $(document).on("click",".eliminarAdd",function(){
                                    var vtotal = $("#asistencia_muj").val();
                                    var parent = $(this).parents().get(0);
                                    $(parent).remove();
                                    total = total - 1;
                                    $('#total').val(total);
                                    var totalG = $("#total").val();
                                    $("#rep_ndis").attr('max', (totalG));
                                    if (total >= vtotal) {
                                        $("#adicionarAdd").prop( "disabled", true );
                                    }else{
                                        $("#adicionarAdd").prop( "disabled", false );
                                    }
                                });
                                
                            });
                        </script>
                        <table id="tablaAdd">
                            <tr class="fila-fijaAdd">
                                <td class="col-sm-7">
                                    <strong>Nombre completo del graduado:</strong>
                                    <input name="act_grad_nom[]" type="text" id="act_grad_nom" class="act_grad_nom form-control" />
                                </td>
                                <td class="col-sm-4">
                                    <strong>Tarjeta dactilar / N° identificación:</strong>
                                    <input name="act_grad_tar[]" type="text" id="act_grad_tar" min="0" class="act_grad_tar form-control" />
                                </td>
                                <td class="eliminarAdd"><br><button type="button" class="btn btn-cir-uno usua-col"><i class="fa fa-times"></i></button></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <div class="col-sm-4"><strong>Número de graduados en LPP en la prisión:</strong> </div>
                        <div class="col-sm-2">
                            <input type="text" name="total" id="total" class="form-control" value="" readonly>
                        </div>
                        <div class="col-sm-4"></div>
                        <div class="col-sm-2">
                            <center>
                                <button id="adicionarAdd" class="btn btn-success" type="button" class="boton" ><i class="fas fa-plus"></i>  Adicionar</button>
                            </center>
                        </div>
                    </div>
                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </div>
                </div>
            </fieldset>--></div>
        <?php } ?>
        <!--<fieldset>--><div class="col-sm-12 registro-section registro-graduados">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">REGISTRO DE GRADUADOS</h3>
                    <p>A continuacion por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-12 registro-table-wrap">
                    <table id="tablaAdd" class="table table-bordered registro-table">
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <div class="registro-bulk-controls" style="margin-bottom: 15px;">
                        <div class="col-sm-6 registro-bulk-controls__text">
                            <label for="cantidadAdd">
                                Cuantos registros desea realizar?
                            </label>
                        </div>
                        <div class="col-sm-3 registro-bulk-controls__value">
                            <input type="number" id="cantidadAdd" class="form-control" min="1" placeholder="Ej: 5">
                        </div>
                        <div class="col-sm-3 registro-bulk-controls__actions">
                            <button id="generarVariasAdd" class="btn btn-primary btn-block" type="button">
                                <i class="fa fa-list"></i> Generar
                            </button>
                        </div>
                    </div>
                    <div class="registro-summary">
                        <div class="col-sm-6 registro-summary__text">
                            <strong>Numero de graduados en LPP en la prision:</strong>
                        </div>
                        <div class="col-sm-2 registro-summary__value">
                            <input type="text" name="total" id="total" class="form-control" value="0" readonly>
                        </div>
                        <div class="col-sm-4 registro-summary__actions">
                            <div class="col-sm-6">
                                <button id="adicionarAdd" class="btn btn-success btn-block" type="button">
                                    <i class="fa fa-plus"></i> Adicionar
                                </button>
                            </div>
                            <div class="col-sm-6">
                                <button id="borrarTodoAdd" class="btn btn-danger btn-block" type="button">
                                    <i class="fa fa-trash"></i> Borrar todo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                #tablaAdd {
                    width: 100%;
                    max-width: 100%;
                }

                #tablaAdd td {
                    vertical-align: top;
                    padding: 14px;
                }

                #tablaAdd input {
                    margin-top: 8px;
                }

                #tablaAdd .btn-eliminar-fila {
                    margin-top: 18px;
                    padding: 6px 10px;
                    border-radius: 4px;
                }

                #cantidadAdd {
                    text-align: center;
                    font-size: 16px;
                    font-weight: bold;
                }

                #generarVariasAdd,
                #adicionarAdd,
                #borrarTodoAdd {
                    white-space: nowrap;
                }

                @media (min-width: 992px) {
                    #tablaAdd td:first-child {
                        width: 58% !important;
                    }

                    #tablaAdd td:nth-child(2) {
                        width: 32% !important;
                    }

                    #tablaAdd td:nth-child(3) {
                        width: 10% !important;
                    }
                }
            </style>
            <script>
                $(function () {
                    var STORAGE_KEY = 'lpp_graduados_nuevo';
                    var registrosIniciales = [];
                    var storage = window.sessionStorage;

                    function crearFila() {
                        return $(
                            '<tr class="fila-fijaAdd registro-table-row">' +
                                '<td class="registro-col registro-col--nombre">' +
                                    '<strong>Nombre completo del graduado:</strong>' +
                                    '<input name="act_grad_nom[]" type="text" class="act_grad_nom form-control" />' +
                                '</td>' +
                                '<td class="registro-col registro-col--identificacion">' +
                                    '<strong>Tarjeta dactilar / N&deg; identificacion:</strong>' +
                                    '<input name="act_grad_tar[]" type="text" class="act_grad_tar form-control" />' +
                                '</td>' +
                                '<td class="registro-col registro-col--action">' +
                                    '<button type="button" class="btn btn-danger btn-eliminar-fila" title="Eliminar">' +
                                        '<i class="fa fa-times"></i>' +
                                    '</button>' +
                                '</td>' +
                            '</tr>'
                        );
                    }

                    function obtenerMaximoGraduados() {
                        var maximo = parseInt($('#asistencia_muj').val(), 10);
                        return isNaN(maximo) || maximo < 1 ? null : maximo;
                    }

                    function actualizarLimitesAsistencia() {
                        var asistenciaTotal = parseInt($('#asistencia_total').val(), 10);
                        var asistenciaHombres = parseInt($('#asistencia_hom').val(), 10);

                        if (!isNaN(asistenciaTotal) && asistenciaTotal >= 0) {
                            $('#asistencia_hom').attr('max', asistenciaTotal);
                        }

                        if (!isNaN(asistenciaHombres) && asistenciaHombres >= 0) {
                            $('#asistencia_muj').attr('max', asistenciaHombres);
                        }
                    }

                    function actualizarLimiteDiscipulos() {
                        var totalGraduados = parseInt($('#total').val(), 10);
                        var maximo = (!isNaN(totalGraduados) && totalGraduados > 0) ? totalGraduados : 0;
                        $('#rep_ndis').attr('max', maximo);
                        var ndis = parseInt($('#rep_ndis').val(), 10);
                        if (!isNaN(ndis) && ndis > maximo) {
                            $('#rep_ndis').val(maximo);
                        }
                    }

                    function obtenerDatosFila($fila) {
                        return {
                            nombre: $.trim($fila.find('.act_grad_nom').val()),
                            tarjeta: $.trim($fila.find('.act_grad_tar').val())
                        };
                    }

                    function filaCompleta($fila) {
                        var datos = obtenerDatosFila($fila);
                        return datos.nombre !== '' && datos.tarjeta !== '';
                    }

                    function filaIncompleta($fila) {
                        var datos = obtenerDatosFila($fila);
                        return (datos.nombre !== '' && datos.tarjeta === '') || (datos.nombre === '' && datos.tarjeta !== '');
                    }

                    function contarCompletos() {
                        var total = 0;
                        $('#tablaAdd tbody tr').each(function () {
                            if (filaCompleta($(this))) {
                                total++;
                            }
                        });
                        return total;
                    }

                    function sincronizarTotal() {
                        var total = contarCompletos();
                        $('#total').val(total);
                        $('#rep_ndis').attr('max', total > 0 ? total : 0);
                        return total;
                    }

                    function guardarDatos() {
                        if (!storage) {
                            return;
                        }

                        var datos = [];
                        $('#tablaAdd tbody tr').each(function () {
                            datos.push(obtenerDatosFila($(this)));
                        });

                        try {
                            storage.setItem(STORAGE_KEY, JSON.stringify(datos));
                        } catch (error) {
                        }
                    }

                    function obtenerDatosGuardados() {
                        if (!storage) {
                            return null;
                        }

                        try {
                            var datos = storage.getItem(STORAGE_KEY);
                            if (!datos) {
                                return null;
                            }

                            datos = JSON.parse(datos);
                            return Array.isArray(datos) ? datos : null;
                        } catch (error) {
                            return null;
                        }
                    }

                    function renderizarFilas(datos) {
                        var $tbody = $('#tablaAdd tbody');
                        $tbody.empty();

                        if (!Array.isArray(datos) || datos.length === 0) {
                            datos = [{ nombre: '', tarjeta: '' }];
                        }

                        $.each(datos, function (_, item) {
                            var $fila = crearFila();
                            $fila.find('.act_grad_nom').val(item.nombre || '');
                            $fila.find('.act_grad_tar').val(item.tarjeta || '');
                            $tbody.append($fila);
                        });
                    }

                    function asegurarMinimoUnaFila() {
                        if ($('#tablaAdd tbody tr').length === 0) {
                            renderizarFilas([]);
                        }
                    }

                    function hayDatosCapturados() {
                        var hayDatos = false;
                        $('#tablaAdd tbody tr').each(function () {
                            var datos = obtenerDatosFila($(this));
                            if (datos.nombre !== '' || datos.tarjeta !== '') {
                                hayDatos = true;
                                return false;
                            }
                        });
                        return hayDatos;
                    }

                    function actualizarBotones() {
                        var maximo = obtenerMaximoGraduados();
                        var filas = $('#tablaAdd tbody tr').length;
                        $('#adicionarAdd').prop('disabled', maximo !== null && filas >= maximo);
                        $('#tablaAdd .btn-eliminar-fila').prop('disabled', false);
                    }

                    function restaurarDatos() {
                        var datosGuardados = obtenerDatosGuardados();
                        renderizarFilas(datosGuardados !== null ? datosGuardados : registrosIniciales);
                        actualizarLimitesAsistencia();
                        sincronizarTotal();
                        actualizarBotones();
                    }

                    function agregarFila() {
                        var maximo = obtenerMaximoGraduados();
                        var filas = $('#tablaAdd tbody tr').length;

                        if (maximo !== null && filas >= maximo) {
                            alert('No puede registrar mas graduados que prisioneros que iniciaron el curso (' + maximo + ').');
                            return;
                        }

                        $('#tablaAdd tbody').append(crearFila());
                        actualizarBotones();
                        guardarDatos();
                    }

                    function generarFilas(cantidad) {
                        var maximo = obtenerMaximoGraduados();
                        var filasActuales = $('#tablaAdd tbody tr').length;

                        if (maximo !== null && (filasActuales + cantidad) > maximo) {
                            alert('No puede generar mas registros que prisioneros que iniciaron el curso (' + maximo + ').');
                            return;
                        }

                        for (var i = 0; i < cantidad; i++) {
                            $('#tablaAdd tbody').append(crearFila());
                        }

                        sincronizarTotal();
                        actualizarBotones();
                        guardarDatos();
                    }

                    function borrarTodo() {
                        renderizarFilas([]);
                        sincronizarTotal();
                        actualizarBotones();
                        guardarDatos();
                    }

                    restaurarDatos();

                    $('#asistencia_hom, #asistencia_total, #asistencia_muj').on('change keyup', function () {
                        actualizarLimitesAsistencia();
                        actualizarBotones();
                    });

                    $(document).on('click', '#adicionarAdd', function (e) {
                        e.preventDefault();
                        agregarFila();
                    });

                    $(document).on('click', '#generarVariasAdd', function (e) {
                        e.preventDefault();

                        var cantidad = parseInt($('#cantidadAdd').val(), 10);
                        if (isNaN(cantidad) || cantidad <= 0) {
                            alert('Ingrese una cantidad valida mayor a 0.');
                            $('#cantidadAdd').focus();
                            return;
                        }

                        generarFilas(cantidad);
                    });

                    $(document).on('click', '#borrarTodoAdd', function (e) {
                        e.preventDefault();

                        if (hayDatosCapturados() && !confirm('Esta seguro de borrar todos los registros cargados?')) {
                            return;
                        }

                        borrarTodo();
                    });

                    $(document).on('click', '#tablaAdd .btn-eliminar-fila', function (e) {
                        e.preventDefault();

                        $(this).closest('tr').remove();
                        asegurarMinimoUnaFila();
                        sincronizarTotal();
                        actualizarBotones();
                        guardarDatos();
                    });

                    $(document).on('keyup change blur', '.act_grad_nom, .act_grad_tar', function () {
                        sincronizarTotal();
                        actualizarBotones();
                        guardarDatos();
                    });

                    $('form').on('submit', function (e) {
                        var hayIncompletas = false;

                        $('#tablaAdd tbody tr').each(function () {
                            if (filaIncompleta($(this))) {
                                hayIncompletas = true;
                                return false;
                            }
                        });

                        if (hayIncompletas) {
                            e.preventDefault();
                            alert('Si diligencia una fila de graduados, debe completar tanto el nombre como la identificacion.');
                            return false;
                        }

                        sincronizarTotal();
                    });
                });
            </script>
        </div>
        <!--REGISTRO DE VOLUNTARIOS INTERNOS-->
            <!--<fieldset>--><div class="col-sm-12 registro-section registro-internos">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">REGISTRO DE VOLUNTARIOS INTERNOS</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 registro-table-wrap">
                        <script>
                            $(function(){
                                var total = 0;
                                var tar = $(".act_vin_tar").val();
                                var nom = $(".act_vin_nom").val();
                                if (tar == "" || nom == "") {
                                    $("#adicionarAdd2").prop( "disabled", true );
                                }else{
                                    $("#adicionarAdd2").prop( "disabled", false );
                                }
                                $(".act_vin_nom").change(function(){
                                    var tar3 = $(".act_vin_tar").val();
                                    var nom3 = $(".act_vin_nom").val();
                                    if (tar3 != "" && nom3 !="") {
                                        if (total < 1) {
                                            total = total + 1;
                                        }
                                        $("#adicionarAdd2").prop( "disabled", false );
                                    }else if (tar3 == "" && nom3 =="") {
                                        if (total == 1) {
                                            total = total - 1;
                                            $(".act_vin_nom").prop('required',false);
                                            $(".act_vin_tar").prop('required',false);
                                        }
                                    }else{
                                        $("#adicionarAdd2").prop( "disabled", true );
                                    }
                                    $('#total2').val(total);
                                });
                                $(".act_vin_tar").change(function(){
                                    var nom2 = $(".act_vin_nom").val();
                                    var tar2 = $(".act_vin_tar").val();
                                    if (nom2 != ""&& tar2 != "") {
                                        if (total < 1) {
                                            total = total + 1;
                                        }
                                        $("#adicionarAdd2").prop( "disabled", false );
                                    }else if (tar3 == "" && nom3 =="") {
                                        if (total == 1) {
                                            total = total - 1;
                                            $(".act_vin_nom").prop('required',false);
                                            $(".act_vin_tar").prop('required',false);
                                        }
                                    }else{
                                        $("#adicionarAdd2").prop( "disabled", true );
                                    }
                                    $('#total2').val(total);
                                });

                                $("#adicionarAdd2").on('click',function(){
                                    var tar2 = $(".act_vin_tar").val();
                                    var nom2 = $(".act_vin_nom").val();
                                    if (tar2!="" && nom2!="") {
                                        $("#tablaAdd2 tbody tr:last").clone().removeClass('fila-fijaAdd2').appendTo("#tablaAdd2 tbody");
                                        $("#tablaAdd2 tbody tr input.act_vin_nom:last").val('');
                                        $("#tablaAdd2 tbody tr input.act_vin_tar:last").val('');
                                        total = total + 1;
                                    }else{
                                        alert("Ingrese toda la información antes de agregar otro campo");
                                    }
                                    $(".act_vin_nom").prop('required',true);
                                    $(".act_vin_tar").prop('required',true);
                                    $('#total2').val(total);
                                });
                                $(document).on("click",".eliminarAdd2",function(){
                                    var parent = $(this).parents().get(0);
                                    $(parent).remove();
                                    total = total - 1;
                                    $('#total2').val(total);
                                });
                                
                            });
                        </script>
                        <table id="tablaAdd2" class="table table-bordered registro-table">
                            <tbody>
                            <tr class="fila-fijaAdd2 registro-table-row">
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del siervo Facilitador:</strong>
                                    <input name="act_vin_nom[]" type="text" id="act_vin_nom" class="act_vin_nom form-control" />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>Tarjeta dactilar / N° identificación:</strong>
                                    <input name="act_vin_tar[]" type="text" id="act_vin_tar" min="0" class="act_vin_tar form-control" />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd2"><button type="button" class="btn btn-cir-uno usua-col" title="Eliminar"><i class="fa fa-times"></i></button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="registro-summary">
                        <div class="col-sm-6 registro-summary__text"><strong>Número de voluntarios internos activos en esta prisión:</strong> </div>
                        <div class="col-sm-2 registro-summary__value">
                            <input type="text" name="total2" id="total2" value="" class="form-control" readonly>
                        </div>
                        <div class="col-sm-4 registro-summary__actions">
                            <center>
                                <button id="adicionarAdd2" class="btn btn-success" type="button" class="boton"><i class="fas fa-plus"></i>  Adicionar</button>
                            </center>
                        </div>
                        </div>
                    </div>
                </div>
                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </div>
                </div>
            </fieldset>--></div>
        <!--REGISTRO DE VOLUNTARIOS EXTERNOS--->
            <!--<fieldset>--><div class="col-sm-12 registro-section registro-externos">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">REGISTRO DE VOLUNTARIOS EXTERNOS</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12 registro-table-wrap">
                        <script>
                            $(function(){
                                var total = 0;
                                var tar = $(".act_vex_tar").val();
                                var nom = $(".act_vex_nom").val();
                                if (tar == "" || nom == "") {
                                    $("#adicionarAdd3").prop( "disabled", true );
                                }else{
                                    $("#adicionarAdd3").prop( "disabled", false );
                                }
                                $(".act_vex_nom").change(function(){
                                    var tar3 = $(".act_vex_tar").val();
                                    var nom3 = $(".act_vex_nom").val();
                                    if (tar3 != "" && nom3 !="") {
                                        if (total < 1) {
                                            total = total + 1;
                                        }
                                        $("#adicionarAdd3").prop( "disabled", false );
                                    }else if (tar3 == "" && nom3 =="") {
                                        if (total == 1) {
                                            total = total - 1;
                                            $(".act_vex_nom").prop('required',false);
                                            $(".act_vex_tar").prop('required',false);
                                        }
                                    }else{
                                        $("#adicionarAdd3").prop( "disabled", true );
                                    }
                                    $('#total3').val(total);
                                });
                                $(".act_vex_tar").change(function(){
                                    var nom2 = $(".act_vex_nom").val();
                                    var tar2 = $(".act_vex_tar").val();
                                    if (nom2 != ""&& tar2 != "") {
                                        if (total < 1) {
                                            total = total + 1;
                                        }
                                        $("#adicionarAdd3").prop( "disabled", false );
                                    }else if (tar3 == "" && nom3 =="") {
                                        if (total == 1) {
                                            total = total - 1;
                                            $(".act_vex_nom").prop('required',false);
                                            $(".act_vex_tar").prop('required',false);
                                        }
                                    }else{
                                        $("#adicionarAdd3").prop( "disabled", true );
                                    }
                                    $('#total3').val(total);
                                });

                                $("#adicionarAdd3").on('click',function(){
                                    var tar2 = $(".act_vex_tar").val();
                                    var nom2 = $(".act_vex_nom").val();
                                    if (tar2!="" && nom2!="") {
                                        $("#tablaAdd3 tbody tr:last").clone().removeClass('fila-fijaAdd3').appendTo("#tablaAdd3 tbody");
                                        $("#tablaAdd3 tbody tr input.act_vex_nom:last").val('');
                                        $("#tablaAdd3 tbody tr input.act_vex_tar:last").val('');
                                        total = total + 1;
                                    }else{
                                        alert("Ingrese toda la información antes de agregar otro campo");
                                    }
                                    $(".act_vex_nom").prop('required',true);
                                    $(".act_vex_tar").prop('required',true);
                                    $('#total3').val(total);
                                });
                                $(document).on("click",".eliminarAdd3",function(){
                                    var parent = $(this).parents().get(0);
                                    $(parent).remove();
                                    total = total - 1;
                                    $('#total3').val(total);
                                });
                                
                            });
                        </script>
                        <table id="tablaAdd3" class="table table-bordered registro-table">
                            <tbody>
                            <tr class="fila-fijaAdd3 registro-table-row">
                                <td class="registro-col registro-col--nombre">
                                    <strong>Nombre completo del entrenador:</strong>
                                    <input name="act_vex_nom[]" type="text" id="act_vex_nom" class="act_vex_nom form-control"  />
                                </td>
                                <td class="registro-col registro-col--identificacion">
                                    <strong>N° identificación:</strong>
                                    <input name="act_vex_tar[]" type="text" id="act_vex_tar" min="0" class="act_vex_tar form-control" />
                                </td>
                                <td class="registro-col registro-col--action eliminarAdd3"><button type="button" class="btn btn-cir-uno usua-col" title="Eliminar"><i class="fa fa-times"></i></button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-12">
                        <div class="registro-summary">
                        <div class="col-sm-6 registro-summary__text"><strong>Número de voluntarios externos para esta prisión:</strong> </div>
                        <div class="col-sm-2 registro-summary__value">
                            <input type="text" name="total3" id="total3" value="" class="form-control" readonly>
                        </div>
                        <div class="col-sm-4 registro-summary__actions">
                            <center>
                                <button id="adicionarAdd3" class="btn btn-success" type="button" class="boton" ><i class="fas fa-plus"></i>  Adicionar</button>
                            </center>
                        </div>
                        </div>
                    </div>
                </div>
                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </div>
                </div>
            </fieldset>--></div>
        <!---TESTIMONIO--->
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Método de verificación</h3>
                        <h5>testimonio</h5>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3">
                        <strong>Discípulos que pasaron a C&M:</strong>
                        <input name="rep_ndis" type="number" id="rep_ndis" min="0" value="<?=$rep_ndis; ?>" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Costo de recursos gestionados ($):</strong>
                        <input name="rep_entr" type="number" id="rep_entr" min="0" value="<?= $rep_entr; ?>" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Testimonio:</strong>
                        <input name="archivo2" type="file" id="archivo2" class="form-control" required />
                    </div>
                    <div class="col-sm-3">
                        <strong>Foto:</strong>
                        <input name="archivo1" type="file" id="archivo1" class="form-control" accept="image/png, image/jpeg, image/jpg, image/gif" />
                    </div>
                </div>
                <div class="cont-btn cont-flex fl-cent">
                    <!--<div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>-->
                    <div class="item-btn">
                        <input type="submit" name="button" value="Guardar" class="btn btn-success">
                    </div>
                </div>
            <!--</fieldset>--></div> 
            <!-- FOTOGRAFIA
            <fieldset>
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                        <div class="tit-cen">
                            <h3 class="text-center">Método de verificación</h3>
                            <h5>FOTO</h5>
                            <p>A continuación por favor ingrese los datos requeridos</p>
                        </div>
                        <div class="hr"><hr></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <input name="archivo1" type="file" id="archivo1" class="form-control" />
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                    <div class="cont-btn cont-flex fl-sbet">
                        <div class="item-btn">
                            <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                        </div>
                        <div class="item-btn">
                            <input type="submit" name="button" value="Guardar" class="btn btn-success">
                        </div>
                    </div>
                </div>
            </fieldset>-->
            <input type="submit" name="button-hidden" id="button-hidden" style="display:none">
            <input type="hidden" name="funcion" id="funcion" value="" />
            <input type="hidden" name="generacion" id="generacion" value="<?=$generacionActual; ?>" />
        </form>
    <script language="javascript">
        var current = 1,current_step,next_step,steps;
        //
        function generarForm(){
            //Completo el formulario  
            if(true){

                <?php
                
                if($generacionActual == "INTRA" ){
                    ?>
                    
                    
                    if(parseInt(document.getElementById("final_asistencia_total").value) < 3){
                        alert("La asistencia total no puede ser menor a 3 personas");
                        return false;
                    }else{
                        return true;
                    }
                    
                    <?php
                }else{
                    ?>
                    
                    // Regla 1: pabellón positivo
                    var pabellon = parseInt(document.getElementById('pabellon') ? document.getElementById('pabellon').value : 1, 10);
                    if (isNaN(pabellon) || pabellon < 1) {
                        alert('El N° de patios / pabellón debe ser un número positivo (mayor o igual a 1).');
                        if (document.getElementById('pabellon')) document.getElementById('pabellon').focus();
                        return false;
                    }

                    // Regla 2: invitados <= total población; iniciaron <= invitados
                    var elTotal = document.getElementById('asistencia_total');
                    var elHom   = document.getElementById('asistencia_hom');
                    var elMuj   = document.getElementById('asistencia_muj');
                    if (elTotal && elHom && elMuj) {
                        var vTotal = parseInt(elTotal.value, 10);
                        var vHom   = parseInt(elHom.value, 10);
                        var vMuj   = parseInt(elMuj.value, 10);
                        if (!isNaN(vTotal) && !isNaN(vHom) && vHom > vTotal) {
                            alert('El número de prisioneros invitados (' + vHom + ') no puede superar la población total (' + vTotal + ').');
                            elHom.focus();
                            return false;
                        }
                        if (!isNaN(vHom) && !isNaN(vMuj) && vMuj > vHom) {
                            alert('El número de prisioneros que iniciaron el curso (' + vMuj + ') no puede superar los invitados (' + vHom + ').');
                            elMuj.focus();
                            return false;
                        }
                    }

                    // Regla 3: discípulos C&M <= total graduados
                    var elNdis = document.getElementById('rep_ndis');
                    var elTotalGrad = document.getElementById('total');
                    if (elNdis && elTotalGrad) {
                        var vNdis     = parseInt(elNdis.value, 10);
                        var vTotalGrad = parseInt(elTotalGrad.value, 10);
                        if (!isNaN(vNdis) && !isNaN(vTotalGrad) && vNdis > vTotalGrad) {
                            alert('Los discípulos que pasaron a C&M (' + vNdis + ') no pueden superar el total de graduados (' + vTotalGrad + ').');
                            elNdis.focus();
                            return false;
                        }
                    }

                    // Regla 4: costo >= 0
                    var elEntr = document.getElementById('rep_entr');
                    if (elEntr) {
                        var vEntr = parseFloat(elEntr.value);
                        if (!isNaN(vEntr) && vEntr < 0) {
                            alert('El costo de recursos gestionados no puede ser negativo.');
                            elEntr.focus();
                            return false;
                        }
                    }

                    if(confirm("Esta accion guardara los cambios en el sistema, ¿esta seguro que desea continuar?")){
                    $(':input[type="submit"]').prop('disabled', true);
                    document.getElementById('funcion').value = "<?=$temp_accionForm; ?>";
                    }else{
                        return false;
                    }
                    
                    <?php
                }
                ?>
            }else{
                return false;
            }
        }
        
        //
        function init(){
            document.getElementById('form1').onsubmit = function(){
                    return generarForm();
            }

            
            function sumar(){
                var asistencia_hom = 0;
                var asistencia_muj = 0;
                var asistencia_jov = 0;
                var asistencia_nin = 0;
                var desiciones = 0;
                //
                if(document.getElementById("asistencia_hom").value != ""){
                    var asistencia_hom = document.getElementById("asistencia_hom").value;
                }
                if(document.getElementById("asistencia_muj").value != ""){
                    var asistencia_muj = document.getElementById("asistencia_muj").value;
                }
                if(document.getElementById("asistencia_muj").value != ""){
                    var asistencia_muj = document.getElementById("asistencia_muj").value;
                }
                //
                if(document.getElementById("asistencia_jov").value != ""){
                    var asistencia_jov = document.getElementById("asistencia_jov").value;
                }
                if(document.getElementById("asistencia_nin").value != ""){
                    var asistencia_nin = document.getElementById("asistencia_nin").value;
                }
                if(document.getElementById("asistencia_total").value != ""){
                    var asistencia_total = document.getElementById("asistencia_total").value;
                }
                
                document.getElementById("final_asistencia_total").value = parseInt(asistencia_total);
                //
                

                document.getElementById("final_asistencia_hom").value = parseInt(asistencia_hom);
                document.getElementById("final_asistencia_muj").value = parseInt(asistencia_muj);
                document.getElementById("final_asistencia_jov").value = parseInt(asistencia_jov);
                document.getElementById("final_asistencia_nin").value = parseInt(asistencia_nin);
                
                
                
                document.getElementById("final_bautizados").value = parseInt(bautizados) + 1;
                document.getElementById("final_discipulado").value = parseInt(var_suma) - 1;
                //
                document.getElementById("final_bautizadosPeriodo").value = parseInt(bautizadosPeriodo);
                
                //document.getElementById("final_desiciones").value = parseInt(var_suma) - 1; //Antigua logica
                document.getElementById("final_desiciones").value = parseInt(desiciones);
                document.getElementById("final_preparandose").value = parseInt(var_suma) - 1 - parseInt(bautizadosPeriodo);                
            }
            
            <?php
            if($varExitoREP == 1)
            {
                ?>alert("Se ha colocado correctamente el ACCESO, espere mientras es dirigido.");
                window.location.href = "index.php?doc=admin_usu4&id=<?=$ultimoId;?>";<?php
            }
            ?>
        }
        

        window.onload = function(){
            init();
        }
    </script>

        
        <?php
    }
}   //FIN DEL IF DE REDIRIGIR SI YA INSERTO EL REGISTRO
else{
    echo "No deberia estar aquí.";
}
?>
<?php if ($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte) {?>
    <script type="text/javascript">
        $("input").attr('disabled','disabled');
        $("textarea").attr('disabled','disabled');
        $("select").attr('disabled','disabled');
        $(".eliminarAdd").prop( "disabled", true );
        $(".eliminarAdd2").prop( "disabled", true );
        $(".eliminarAdd3").prop( "disabled", true );
        $(".btn-eliminar-fila").prop("disabled", true);
        $("#adicionarAdd").prop('disabled', true);
        $("#generarVariasAdd").prop('disabled', true);
        $("#borrarTodoAdd").prop('disabled', true);
        $("#btn-check").prop('disabled', false);
    </script>
<?php } ?> 
<script type="text/javascript">
    $(document).ready(function(){
        recargaLista();
        $('#rep_carcel').change(function(){
            recargaLista();
        });
        recargaListaDpto();
        $('#departamento').change(function(){
            recargaListaDpto();
        });
        $('#asistencia_muj').change(function(){

            var cursos = $('#asistencia_muj').val();
            var resul = cursos/12;
            var mod = resul%2;
            if (mod != 0) {
                resul = Math.trunc(resul)+1;
            }
            if (cursos<=12) {
                resul = 1;
            }
            $('#asistencia_jov').val(resul);
        });

        /* ---- Límites dinámicos del formulario de inserción ---- */
        function lppMostrarError(campo, msg) {
            var $campo = $(campo);
            var $err = $campo.next('.lpp-field-error');
            if ($err.length === 0) {
                $err = $('<span class="lpp-field-error" style="display:block;color:#c0392b;font-size:12px;margin-top:4px;font-weight:600;"></span>');
                $campo.after($err);
            }
            $err.text(msg);
            $campo.css('border-color', '#c0392b');
        }
        function lppLimpiarError(campo) {
            var $campo = $(campo);
            $campo.next('.lpp-field-error').remove();
            $campo.css('border-color', '');
        }

        // asistencia_hom <= asistencia_total
        $('#asistencia_total, #asistencia_hom').on('input change', function(){
            var total = parseInt($('#asistencia_total').val(), 10);
            var hom   = parseInt($('#asistencia_hom').val(), 10);
            if (!isNaN(total) && !isNaN(hom) && hom > total) {
                lppMostrarError('#asistencia_hom', 'No puede superar la población total (' + total + ').');
            } else {
                lppLimpiarError('#asistencia_hom');
            }
            // Cascada: revisar también muj vs hom
            var muj = parseInt($('#asistencia_muj').val(), 10);
            var homActual = isNaN(hom) ? 0 : hom;
            if (!isNaN(muj) && muj > homActual) {
                lppMostrarError('#asistencia_muj', 'No puede superar los prisioneros invitados (' + homActual + ').');
            } else {
                lppLimpiarError('#asistencia_muj');
            }
        });

        // asistencia_muj <= asistencia_hom
        $('#asistencia_muj').on('input change', function(){
            var hom = parseInt($('#asistencia_hom').val(), 10);
            var muj = parseInt($(this).val(), 10);
            if (!isNaN(hom) && !isNaN(muj) && muj > hom) {
                lppMostrarError('#asistencia_muj', 'No puede superar los prisioneros invitados (' + hom + ').');
            } else {
                lppLimpiarError('#asistencia_muj');
            }
        });

        // rep_ndis <= #total (graduados)
        $('#rep_ndis').on('input change', function(){
            var maxGrad = parseInt($('#total').val(), 10) || 0;
            var ndis = parseInt($(this).val(), 10);
            if (!isNaN(ndis) && ndis > maxGrad) {
                lppMostrarError('#rep_ndis', 'No puede superar el total de graduados (' + maxGrad + ').');
            } else {
                lppLimpiarError('#rep_ndis');
            }
            if (!isNaN(ndis) && ndis < 0) {
                lppMostrarError('#rep_ndis', 'El valor no puede ser negativo.');
            }
        });

        // rep_entr >= 0
        $('#rep_entr').on('input change', function(){
            var v = parseFloat($(this).val());
            if (!isNaN(v) && v < 0) {
                lppMostrarError('#rep_entr', 'El costo no puede ser negativo.');
            } else {
                lppLimpiarError('#rep_entr');
            }
        });
    })
</script>
<script type="text/javascript">
    function recargaListaDpto(){
        $.ajax({
            type: "POST",
            url: "datos_ubicacion.php",
            data: "id_depa=" + $('#departamento').val(),
            success: function(r){
                $('#municipio').html(r);
            }
        })
    }
</script>
<script type="text/javascript">
    function recargaLista(){
        $.ajax({
            type: "POST",
            url: "datos_carcel_ubicacion.php",
            data: "id_carcel=" + $('#rep_carcel').val(),
            success: function(r){
                $('#ubicacion').html(r);
            }
        })
    }
</script>

<!-- ============================================================
     MEJORAS DE RESILIENCIA DEL FORMULARIO LPP
     1. Autoguardado en localStorage (cada 30 seg y en cada cambio)
     2. Keepalive de sesión (ping cada 10 min para evitar timeout)
     3. Detección de pérdida de conexión con aviso visual
     ============================================================ -->
<style>
    #lpp-status-bar {
        position: fixed;
        bottom: 0; left: 0; right: 0;
        z-index: 9999;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: bold;
        text-align: center;
        display: none;
        transition: background 0.3s;
    }
    #lpp-status-bar.guardado   { background:#28a745; color:#fff; display:block; }
    #lpp-status-bar.offline    { background:#dc3545; color:#fff; display:block; }
    #lpp-status-bar.restaurado { background:#17a2b8; color:#fff; display:block; }
    #lpp-status-bar.guardando  { background:#ffc107; color:#333; display:block; }
    #lpp-recuperar-banner {
        display:none;
    }
</style>

<div id="lpp-status-bar"></div>
<div id="lpp-recuperar-banner" style="display:none;"></div>

<script type="text/javascript">
(function(){
    /* ---- Configuración ---- */
    var FORM_ID             = 'form1';
    var STORAGE_KEY         = 'lpp_autosave_' + (window.location.search || 'nuevo');
    var SAVE_INTERVAL       = 30000;    // 30 segundos
    var KEEPALIVE_INTERVAL  = 600000;   // 10 minutos
    var statusBar           = document.getElementById('lpp-status-bar');
    var recuperarBanner     = document.getElementById('lpp-recuperar-banner');
    var saveTimer, hideTimer;
    var formularioEnviado   = false;

    /* ============================================================
       TABLAS DINÁMICAS
       tablaAdd  → graduados      (act_grad_nom / act_grad_tar)
       tablaAdd2 → bautizados     (act_vin_nom  / act_vin_tar)
       tablaAdd3 → decisiones     (act_vex_nom  / act_vex_tar)
    ============================================================ */
    var TABLAS = [
        { tablaId: 'tablaAdd',  claseNom: 'act_grad_nom', claseTar: 'act_grad_tar' },
        { tablaId: 'tablaAdd2', claseNom: 'act_vin_nom',  claseTar: 'act_vin_tar'  },
        { tablaId: 'tablaAdd3', claseNom: 'act_vex_nom',  claseTar: 'act_vex_tar'  }
    ];

    /* ---- Utilidades de UI ---- */
    function mostrarEstado(msg, clase, duracion) {
        clearTimeout(hideTimer);
        statusBar.textContent = msg;
        statusBar.className   = clase;
        if (duracion) {
            hideTimer = setTimeout(function(){ statusBar.style.display = 'none'; }, duracion);
        }
    }

    /* ---- Serializar campos normales del formulario ---- */
    function obtenerDatosFormulario() {
        var form = document.getElementById(FORM_ID);
        if (!form) return null;
        var datos = {};
        var elementos = form.querySelectorAll('input, select, textarea');
        for (var i = 0; i < elementos.length; i++) {
            var el = elementos[i];
            /* Saltar archivos, botones y campos de tablas dinámicas (se guardan aparte) */
            if (!el.name || el.type === 'file' || el.type === 'submit' || el.type === 'button') continue;
            if (el.classList.contains('act_grad_nom') || el.classList.contains('act_grad_tar') ||
                el.classList.contains('act_vin_nom')  || el.classList.contains('act_vin_tar')  ||
                el.classList.contains('act_vex_nom')  || el.classList.contains('act_vex_tar')) continue;
            if (el.type === 'checkbox' || el.type === 'radio') {
                if (el.checked) datos[el.name] = el.value;
            } else {
                datos[el.name] = el.value;
            }
        }
        return datos;
    }

    /* ---- Serializar tablas dinámicas ---- */
    function obtenerDatosTablas() {
        var resultado = {};
        for (var t = 0; t < TABLAS.length; t++) {
            var cfg   = TABLAS[t];
            var tabla = document.getElementById(cfg.tablaId);
            if (!tabla) continue;
            var filas = [];
            var inputsNom = tabla.querySelectorAll('input.' + cfg.claseNom);
            var inputsTar = tabla.querySelectorAll('input.' + cfg.claseTar);
            for (var i = 0; i < inputsNom.length; i++) {
                filas.push({
                    nom: inputsNom[i] ? inputsNom[i].value : '',
                    tar: inputsTar[i] ? inputsTar[i].value : ''
                });
            }
            resultado[cfg.tablaId] = filas;
        }
        return resultado;
    }

    /* ---- Restaurar campos normales ---- */
    function restaurarCamposNormales(datos) {
        var form = document.getElementById(FORM_ID);
        if (!form) return;
        var elementos = form.querySelectorAll('input, select, textarea');
        for (var i = 0; i < elementos.length; i++) {
            var el = elementos[i];
            if (!el.name || el.type === 'file' || el.type === 'submit' || el.type === 'button') continue;
            if (datos.hasOwnProperty(el.name)) {
                if (el.type === 'checkbox' || el.type === 'radio') {
                    el.checked = (el.value === datos[el.name]);
                } else {
                    el.value = datos[el.name];
                }
            }
        }
        /* Recalcular totales y selectores dependientes */
        $('#asistencia_muj').trigger('change');
        $('#rep_carcel').trigger('change');
        $('#departamento').trigger('change');
    }

    /* ---- Restaurar una tabla dinámica ---- */
    function restaurarTabla(cfg, filas) {
        var tabla = document.getElementById(cfg.tablaId);
        if (!tabla || !filas || filas.length === 0) return;

        /* Obtener la fila plantilla (primera fila) */
        var filaPlantilla = tabla.querySelector('tr');
        if (!filaPlantilla) return;

        /* Eliminar todas las filas excepto la primera */
        var todasFilas = tabla.querySelectorAll('tr');
        for (var i = todasFilas.length - 1; i >= 1; i--) {
            todasFilas[i].parentNode.removeChild(todasFilas[i]);
        }

        /* Rellenar la primera fila con el primer registro */
        var primerNom = tabla.querySelector('input.' + cfg.claseNom);
        var primerTar = tabla.querySelector('input.' + cfg.claseTar);
        if (primerNom) primerNom.value = filas[0].nom || '';
        if (primerTar) primerTar.value = filas[0].tar || '';

        /* Clonar filas para el resto de registros */
        for (var j = 1; j < filas.length; j++) {
            var nuevaFila = filaPlantilla.cloneNode(true);
            nuevaFila.classList.remove('fila-fijaAdd', 'fila-fijaAdd2', 'fila-fijaAdd3');
            var inputNom = nuevaFila.querySelector('input.' + cfg.claseNom);
            var inputTar = nuevaFila.querySelector('input.' + cfg.claseTar);
            if (inputNom) inputNom.value = filas[j].nom || '';
            if (inputTar) inputTar.value = filas[j].tar || '';

            /* Agregar la fila al tbody si existe, o directamente a la tabla */
            var tbody = tabla.querySelector('tbody');
            if (tbody) tbody.appendChild(nuevaFila);
            else tabla.appendChild(nuevaFila);
        }
    }

    /* ---- Autoguardado completo ---- */
    function lppGuardar() {
        try {
            var datos  = obtenerDatosFormulario();
            var tablas = obtenerDatosTablas();
            if (!datos) return;
            mostrarEstado('💾 Guardando borrador...', 'guardando');
            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                ts: Date.now(),
                datos: datos,
                tablas: tablas
            }));
            mostrarEstado('✔ Borrador guardado automáticamente', 'guardado', 3000);
        } catch(e) { /* localStorage puede no estar disponible */ }
    }

    /* ---- Restaurar todo ---- */
    window.lppRestaurarDatos = function() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return;
            var obj = JSON.parse(raw);

            /* 1. Restaurar campos normales */
            if (obj.datos) restaurarCamposNormales(obj.datos);

            /* 2. Restaurar tablas dinámicas */
            if (obj.tablas) {
                for (var t = 0; t < TABLAS.length; t++) {
                    var cfg = TABLAS[t];
                    if (obj.tablas[cfg.tablaId]) {
                        restaurarTabla(cfg, obj.tablas[cfg.tablaId]);
                    }
                }
            }

            recuperarBanner.style.display = 'none';
            mostrarEstado('✔ Datos restaurados correctamente (incluye graduados, bautizados y decisiones)', 'restaurado', 5000);
        } catch(e) {
            mostrarEstado('⚠ Error al restaurar los datos', 'offline', 4000);
        }
    };

    window.lppDescartarDatos = function() {
        try { localStorage.removeItem(STORAGE_KEY); } catch(e) {}
        recuperarBanner.style.display = 'none';
    };

    /* ---- Keepalive de sesión ---- */
    function keepAlive() {
        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: { funcion: 'keepalive_ping' },
            error: function() { /* silencioso */ }
        });
    }

    /* ---- Detección online / offline ---- */
    function onOffline() {
        mostrarEstado('⚠ Sin conexión — los datos están guardados localmente y no se perderán', 'offline');
        lppGuardar();
    }
    function onOnline() {
        mostrarEstado('✔ Conexión restaurada', 'restaurado', 4000);
    }

    /* ---- Inicialización ---- */
    $(document).ready(function(){

        /* Restaurar datos automáticamente al cargar si existen */
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (raw) {
                window.lppRestaurarDatos();
            }
        } catch(e) {}

        /* Escuchar cambios en el formulario y en las tablas dinámicas */
        $('#' + FORM_ID).on('change input', function(){
            clearTimeout(saveTimer);
            saveTimer = setTimeout(lppGuardar, 2000);
        });
        /* Delegación de eventos para inputs dentro de las tablas dinámicas */
        $(document).on('input change', '#tablaAdd input, #tablaAdd2 input, #tablaAdd3 input', function(){
            clearTimeout(saveTimer);
            saveTimer = setTimeout(lppGuardar, 2000);
        });

        /* Autoguardado periódico */
        setInterval(lppGuardar, SAVE_INTERVAL);

        /* Keepalive de sesión */
        setInterval(keepAlive, KEEPALIVE_INTERVAL);

        /* Eventos de red */
        window.addEventListener('offline', onOffline);
        window.addEventListener('online',  onOnline);

        /* Al enviar, marcar como enviado para evitar guardado de emergencia */
        $('#' + FORM_ID).on('submit', function(){
            formularioEnviado = true;
        });

        /* Último guardado de emergencia al cerrar/recargar */
        window.addEventListener('beforeunload', function(){
            if (!formularioEnviado) lppGuardar();
        });
    });

})();
</script>