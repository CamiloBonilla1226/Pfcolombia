<?php
$PSN1 = new DBbase_Sql;
$PSN = new DBbase_Sql;
$webArchivo = "preoperacional";
$temp_letrero = "CAPACITAR Y MULTIPLICAR (C&M)";


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



/*
*   VERIFICAMOS CON QUE GENERACIÓN NOS ESTAMOS ENFRENTANDO ACTUALMENTE.
*/
$preguntarGeneracion = 0;
if(isset($_REQUEST["generacion"]) && $_REQUEST["generacion"] != ""){
    $generacionActual = eliminarInvalidos($_REQUEST["generacion"]);
}else{
    $preguntarGeneracion = 1;
}
//echo " GENERACIÓN: ".$generacionActua;

/*
*   Comprobamos si viene en modo de actualización o de insersión.
*/
if(isset($_REQUEST["id"]) && $_REQUEST["id"] != ""){
    $idReporteActual = soloNumeros($_REQUEST["id"]);
    if ($_SESSION["perfil"] == 162 || $_SESSION["perfil"] == 163) {
        $sql = "UPDATE  sat_reportes SET 
                    mapeo_fecha = '".date('Y-m-d')."'";
    
        $sql .= "WHERE id = '".$idReporteActual."'";
        $PSN1->query($sql);
    }  
}else{
    $idReporteActual = 0;
}

// Control de permisos para edición de reportes - Solo Manuel y Danny pueden editar
$usuarios_autorizados_edicion = array(1, 231); // Manuel José Obando (ID: 1) y Danny Hernandez (ID: 231)
$puede_editar_reporte = false;
if (isset($_SESSION["id"]) && in_array($_SESSION["id"], $usuarios_autorizados_edicion)) {
    $puede_editar_reporte = true;
}
$readonly_attr = $puede_editar_reporte ? '' : 'readonly';


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
    header ("Expires: Fri, 14 Mar 1980 20:53:00 GMT"); //la pagina expira en fecha pasada 
    header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); //ultima actualizacion ahora cuando la cargamos 
    header ("Cache-Control: no-cache, must-revalidate"); //no guardar en CACHE 
    header ("Pragma: no-cache");
	if($_POST["funcion"] == "insertar"){
        //die("Insertar");
        /*
        *   PESTAÑA GENERAL
        */
        $comentario = eliminarInvalidos($_REQUEST["final_comentarios"]);
        $entrenador = eliminarInvalidos($_REQUEST["entrenador"]);
        $plantador = eliminarInvalidos($_REQUEST["plantador"]);
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
        $fechaInicio = eliminarInvalidos($_REQUEST["fechaInicio"]);        
        if (isset($_REQUEST['sitioReunion'])) {
            $sitioReunion = soloNumeros($_REQUEST["sitioReunion"]);
        }else{
            $sitioReunion = 0;
        }  
        $grupoMadre_txt = eliminarInvalidos($_REQUEST["grupoMadre_txt"]);
        $nombreGrupo_txt = eliminarInvalidos($_REQUEST["nombreGrupo_txt"]);
        
        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);
        $direccion = eliminarInvalidos($_REQUEST["direccion"]);
        if (isset($_REQUEST["municipio"])) {
            $ciudad = soloNumeros($_REQUEST["municipio"]);
        }else{
            $ciudad = 0;
        }
                    
        
        $capacitacion_txt = eliminarInvalidos($_REQUEST["capacitacion_txt"]);        
        $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
        $generacionNumero = soloNumeros($_REQUEST["generacionNumero"]);
        

        $asistencia_hom = soloNumeros($_REQUEST["asistencia_hom"]);
        $asistencia_muj = soloNumeros($_REQUEST["asistencia_muj"]);
        $asistencia_jov = soloNumeros($_REQUEST["asistencia_jov"]);
        $asistencia_nin = soloNumeros($_REQUEST["asistencia_nin"]);

        $bautizados = soloNumeros($_REQUEST["final_bautizados"]);        
        $bautizadosPeriodo = soloNumeros($_REQUEST["final_bautizadosPeriodo"]);
        
        $graduados = soloNumeros($_REQUEST["final_graduados"]);        
        $graduadosPeriodo = soloNumeros($_REQUEST["final_graduadosPeriodo"]);
        
        
        $mapeo_anho = soloNumeros($_REQUEST["mapeo_anho"]);
        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
        
        
		$nombre_archivo = $_FILES['archivo1']['name'];
		$archivo1 = extension_archivo($nombre_archivo);
        
		$nombre_archivo = $_FILES['archivo2']['name'];
		$archivo2 = extension_archivo($nombre_archivo);
		
		// Validar extensiones permitidas para testimonio
		$extensiones_permitidas_testimonio = array('docx', 'odt', 'pdf', 'doc');
		if (!empty($nombre_archivo) && !in_array(strtolower($archivo2), $extensiones_permitidas_testimonio)) {
			$error_datos = 1;
			$error_mensaje = "El testimonio solo acepta archivos: " . implode(', ', array_map('strtoupper', $extensiones_permitidas_testimonio));
		}
        
		$nombre_archivo = $_FILES['archivo3']['name'];
		$archivo3 = extension_archivo($nombre_archivo);

        
        
        $mapeo_fecha = eliminarInvalidos($_REQUEST["mapeo_fecha"]);
        $mapeo_comprometido = soloNumeros($_REQUEST["mapeo_comprometido"]);
        
        $mapeo_oracion = soloNumeros($_REQUEST["mapeo_oracion"]);        
        $mapeo_companerismo = soloNumeros($_REQUEST["mapeo_companerismo"]);        
        $mapeo_adoracion = soloNumeros($_REQUEST["mapeo_adoracion"]);        
        $mapeo_biblia = soloNumeros($_REQUEST["mapeo_biblia"]);        
        $mapeo_evangelizar = soloNumeros($_REQUEST["mapeo_evangelizar"]);        
        $mapeo_cena = soloNumeros($_REQUEST["mapeo_cena"]);        
        $mapeo_dar = soloNumeros($_REQUEST["mapeo_dar"]);        
        $mapeo_bautizar = soloNumeros($_REQUEST["mapeo_bautizar"]);        
        $mapeo_trabajadores = soloNumeros($_REQUEST["mapeo_trabajadores"]);   
        
        
        //Calculados:
        $asistencia_total  = $asistencia_hom+$asistencia_muj+$asistencia_jov+$asistencia_nin;
        $discipulado  = soloNumeros($_REQUEST["final_discipulado"]);
        $desiciones  = soloNumeros($_REQUEST["final_desiciones"]);
        $rep_tip  = 308;
        $rep_ndis  = soloNumeros($_REQUEST["rep_ndis"]);
        $number_person_without_freedom = soloNumeros($_REQUEST["number_person_without_freedom"]);
        $number_person_post_penalties = soloNumeros($_REQUEST["number_person_post_penalties"]);
        
        $preparandose  = soloNumeros($_REQUEST["final_preparandose"]);
        $iglesias_reconocidas = 0;
        //        

        if($error_datos == 0){
            
            /*if($generacionActual == "CERO"){
                $sql = 'INSERT INTO sat_grupos (
                idUsuario,
                fechaInicio,
                nombre,
                descripcion,
                    creacionFecha,
                    creacionUsuario
                )';
            
                $sql .= ' VALUES 
                    (
                    "'.$_SESSION["id"].'", 
                    "'.$fechaInicio.'", 
                    "'.$grupoMadre_txt.'", 
                    "'.$grupoMadre_txt.'", 
                        NOW(), 
                        "'.$_SESSION["id"].'"
                    )';
                //
                //echo "Insertar sat_grupos: ".$sql;
                $ultimoQuery = $PSN1->query($sql);
                $idGrupoMadre =  $PSN1->ultimoId();
            }
            else{
                $fechaInicio = date("Y-m-d");
                $sql = 'SELECT fechaInicio FROM sat_grupos ';
                $sql .= ' WHERE id = "'.$idGrupoMadre.'"';
                $PSN1->query($sql);
                if($PSN1->num_rows() > 0)
                {
                    if($PSN1->next_record())
                    {
                        $fechaInicio = $PSN1->f("fechaInicio");
                    }
                }
            }*/
            
            
            /*
            *	DEBEMOS INSERTAR LA INFORMACION DEL REPORTE SEGUN CORRESPONDA.
            */
            $sql = 'INSERT INTO sat_reportes (
                idUsuario,
                comentario,
                plantador,
                rep_entr,
                fechaReporte,
                fechaInicio,
                sitioReunion,
                grupoMadre_txt,
                nombreGrupo_txt,
                capacitacion_txt,
                idGrupoMadre,
                generacionNumero,
                
                pabellon,
                direccion,
                ciudad,
                
                    asistencia_hom,
                    asistencia_muj,
                    asistencia_jov,
                    asistencia_nin,

                bautizados,
                bautizadosPeriodo,
                
                graduados,
                graduadosPeriodo,
                
                asistencia_total,
                discipulado,
                desiciones,
                rep_ndis,
                number_person_without_freedom,
                number_person_post_penalties,
                preparandose,
                
                creacionFecha,
                creacionUsuario,
                ext1,
                ext2,
                
                    mapeo_fecha,
                    mapeo_comprometido,

                        mapeo_oracion,
                        mapeo_companerismo,
                        mapeo_adoracion,
                        mapeo_biblia,
                        mapeo_evangelizar,
                        mapeo_cena,
                        mapeo_dar,
                        mapeo_bautizar,
                        mapeo_trabajadores,                
                
                mapeo_anho,
                mapeo_cuarto,
                ext3,
                rep_tip
                )';
            
            $sql .= ' VALUES 
                (
                "'.$_SESSION["id"].'",
                "'.$comentario.'", 
                "'.$plantador.'",
                "'.$entrenador.'", 
                "'.$fechaReporte.'", 
                "'.$fechaInicio.'", 
                '.$sitioReunion.', 
                "'.$grupoMadre_txt.'", 
                "'.$nombreGrupo_txt.'",                 
                "'.$capacitacion_txt.'", 
                "'.$idGrupoMadre.'", 
                "'.$generacionNumero.'", 
                
                "'.$pabellon.'", 
                "'.$direccion.'", 
                '.$ciudad.', 
                

                    "'.$asistencia_hom.'", 
                    "'.$asistencia_muj.'", 
                    "'.$asistencia_jov.'", 
                    "'.$asistencia_nin.'", 
                    
                "'.$bautizados.'", 
                "'.$bautizadosPeriodo.'", 
                
                "'.$graduados.'", 
                "'.$graduadosPeriodo.'", 
                
                
                "'.$asistencia_total.'", 
                "'.$discipulado.'", 
                "'.$desiciones.'",
                '.$rep_ndis.',
                "'.$number_person_without_freedom.'", 
                "'.$number_person_post_penalties.'", 
                "'.$preparandose.'",

                NOW(), 
                "'.$_SESSION["id"].'",

                "'.$archivo1.'",
                "'.$archivo2.'",
                
                    "'.$mapeo_fecha.'",
                    "'.$mapeo_comprometido.'",

                    "'.$mapeo_oracion.'",
                    "'.$mapeo_companerismo.'",
                    "'.$mapeo_adoracion.'",
                    "'.$mapeo_biblia.'",
                    "'.$mapeo_evangelizar.'",
                    "'.$mapeo_cena.'",
                    "'.$mapeo_dar.'",
                    "'.$mapeo_bautizar.'",
                    "'.$mapeo_trabajadores.'",
                        
                    "'.$mapeo_anho.'",
                    "'.$mapeo_cuarto.'",                    
                "'.$archivo3.'",
                '.$rep_tip.'

                )';
            
            //
            //
            //echo "Insertar sat_reportes: ".$sql;
            $ultimoQuery = $PSN1->query($sql);
            $ultimoId =  $PSN1->ultimoId();
            //echo $bautizadosPeriodo."_____";
            
            
            if ($bautizadosPeriodo>0) {
                $act_bau_img = $_FILES["act_bau_img"];
                $act_bau_fec = $_REQUEST['act_bau_fec'];
                $act_bau_can = $_REQUEST['act_bau_can'];

                $sql = 'INSERT INTO tbl_adjuntos (
                    adj_nom,
                    adj_url,
                    adj_fec,
                    adj_can, 
                    adj_rep_fk)';
                $sql .= 'VALUES';
                
                for ($i=0; $i < sizeof($act_bau_fec); $i++) { 
                    $tp_arch = extension_archivo($act_bau_img['name'][$i]);
                    $sql .= "('".$act_bau_img['name'][$i]."','archivos/evi_".$ultimoId."_".$i.".".$tp_arch."','".$act_bau_fec[$i]."',".$act_bau_can[$i].",".$ultimoId."),";
                    $extArchivo = $tp_arch;
                    if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                        $rutaOr = $act_bau_img['tmp_name'][$i];
                        $rutaDe = "archivos/evi_".$ultimoId."_".$i.".".$tp_arch;
                        compressImage($rutaOr, $rutaDe, 80);
                    }else{
                        if(move_uploaded_file($act_bau_img['tmp_name'][$i], "archivos/evi_".$i.".".$tp_arch)){
                        }            
                    }
                }
                $sql = substr($sql, 0, -1);
                //echo $sql;
                $ultimoQuery = $PSN1->query($sql);
            }
            
            
            
            
            
            // Aqui se crea carga adjuntos Graduados
            
            
            if ($graduadosPeriodo>0) {
                $act_bau_imgG = $_FILES["act_bau_imgG"];
                $act_bau_fecG = $_REQUEST['act_bau_fecG'];
                $act_bau_canG = $_REQUEST['act_bau_canG'];
                $act_bau_curG = $_REQUEST['act_bau_curG'];
    
                $sql = 'INSERT INTO tbl_adjuntos (
                    adj_nom,
                    adj_url,
                    adj_fec,
                    adj_can,
                    adj_curso,
                    adj_rep_fk)';
                $sql .= 'VALUES';
                
                for ($i=0; $i < sizeof($act_bau_fecG); $i++) { 
                    $tp_arch = extension_archivo($act_bau_imgG['name'][$i]);
                    $sql .= "('".$act_bau_imgG['name'][$i]."','archivos/evi_".$ultimoId."_".$i.".".$tp_arch."','".$act_bau_fecG[$i]."',".$act_bau_canG[$i].",".$act_bau_curG[$i].",".$ultimoId."),";
                    $extArchivo = $tp_arch;
                    if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                        $rutaOr = $act_bau_imgG['tmp_name'][$i];
                        $rutaDe = "archivos/evi_".$ultimoId."_".$i.".".$tp_arch;
                        compressImage($rutaOr, $rutaDe, 80);
                    }else{
                        if(move_uploaded_file($act_bau_imgG['tmp_name'][$i], "archivos/evi_".$i.".".$tp_arch)){
                        }            
                    }
                }
                $sql = substr($sql, 0, -1);
                //echo $sql;
                $ultimoQuery = $PSN1->query($sql);
            }
            
            
            
            
            //      
            //if($generacionNumero > 0){
                // Compress Image
                $extArchivo = $archivo1;
                if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                    $rutaOrigen = $_FILES['000000']['tmp_name'];
                    $rutaDestino = "archivos/evi_".$ultimoId."_1.".$archivo1;
                    compressImage($rutaOrigen, $rutaDestino, 80);
                }
                else{
                    if(move_uploaded_file($_FILES['archivo1']['tmp_name'], "archivos/evi_".$ultimoId."_1.".$archivo1))
                    {
                    }            
                }

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

                $extArchivo = $archivo3;
                if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                    $rutaOrigen = $_FILES['archivo3']['tmp_name'];
                    $rutaDestino = "archivos/evi_".$ultimoId."_3.".$archivo3;
                    compressImage($rutaOrigen, $rutaDestino, 80);
                }
                else{
                    if(move_uploaded_file($_FILES['archivo3']['tmp_name'], "archivos/evi_".$ultimoId."_3.".$archivo3))
                    {
                    }            
                }
            //}
            //            
            $varExitoREP = 1;
        }
	}//Fin del IF de insertar
    else if($_POST["funcion"] == "eliminar"){
        $sql = 'DELETE from sat_reportes WHERE id = "'.$idReporteActual.'"';
        $PSN1->query($sql);
    }else if($_POST["funcion"] == "actualizar"){
       // die("Actualizar");
        //
        /*
        *   PESTAÑA GENERAL
        */
        $entrenador = eliminarInvalidos($_REQUEST["entrenador"]);
        $plantador = eliminarInvalidos($_REQUEST["plantador"]);
        $comentario = eliminarInvalidos($_REQUEST["final_comentarios"]);
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
        $fechaInicio = eliminarInvalidos($_REQUEST["fechaInicio"]);
        if (isset($_REQUEST['sitioReunion'])) {
            $sitioReunion = soloNumeros($_REQUEST["sitioReunion"]);
        }else{
            $sitioReunion = 0;
        }  
        
        $grupoMadre_txt = eliminarInvalidos($_REQUEST["grupoMadre_txt"]);
        $nombreGrupo_txt = eliminarInvalidos($_REQUEST["nombreGrupo_txt"]);
        
        if (!empty($_REQUEST["inactivo"])) {
            $inactivo = soloNumeros($_REQUEST["inactivo"]);
        }else{
            $inactivo = 0;
        }
        
        
        
        $capacitacion_txt = eliminarInvalidos($_REQUEST["capacitacion_txt"]);        
        $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
        $generacionNumero = soloNumeros($_REQUEST["generacionNumero"]);
        
        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);
        $direccion = eliminarInvalidos($_REQUEST["direccion"]);
        if (!empty($_REQUEST["municipio"])) {
            $ciudad = soloNumeros($_REQUEST["municipio"]);
        }else{
            $ciudad = 0;
        }

        $asistencia_hom = soloNumeros($_REQUEST["final_asistencia_hom"]);
        $asistencia_muj = soloNumeros($_REQUEST["final_asistencia_muj"]);
        $asistencia_jov = soloNumeros($_REQUEST["final_asistencia_jov"]);
        $asistencia_nin = soloNumeros($_REQUEST["final_asistencia_nin"]);

        $bautizados = soloNumeros($_REQUEST["final_bautizados"]);        
        $bautizadosPeriodo = soloNumeros($_REQUEST["final_bautizadosPeriodo"]);
        
        $graduados = soloNumeros($_REQUEST["final_graduados"]);        
        $graduadosPeriodo = soloNumeros($_REQUEST["final_graduadosPeriodo"]);

        //Calculados:
        $asistencia_total  = $asistencia_hom+$asistencia_muj+$asistencia_jov+$asistencia_nin;
        $discipulado  = soloNumeros($_REQUEST["final_discipulado"]);
        $desiciones  = soloNumeros($_REQUEST["final_desiciones"]);
        $rep_ndis  = soloNumeros($_REQUEST["rep_ndis"]);
        $number_person_without_freedom = soloNumeros($_REQUEST["number_person_without_freedom"]);
        $number_person_post_penalties = soloNumeros($_REQUEST["number_person_post_penalties"]);
        $preparandose  = soloNumeros($_REQUEST["final_preparandose"]);
        $iglesias_reconocidas = 0;
        
        
        $mapeo_anho = soloNumeros($_REQUEST["mapeo_anho"]);
        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
        
        
		$nombre_archivo = $_FILES['archivo1']['name'];
		$archivo1 = extension_archivo($nombre_archivo);
        
		$nombre_archivo = $_FILES['archivo2']['name'];
		$archivo2 = extension_archivo($nombre_archivo);
		
		// Validar extensiones permitidas para testimonio
		$extensiones_permitidas_testimonio = array('docx', 'odt', 'pdf', 'doc');
		if (!empty($nombre_archivo) && !in_array(strtolower($archivo2), $extensiones_permitidas_testimonio)) {
			$error_datos = 1;
			$error_mensaje = "El testimonio solo acepta archivos: " . implode(', ', array_map('strtoupper', $extensiones_permitidas_testimonio));
		}
        
		$nombre_archivo = $_FILES['archivo3']['name'];
		$archivo3 = extension_archivo($nombre_archivo);

        
        
        $mapeo_fecha = eliminarInvalidos($_REQUEST["mapeo_fecha"]);
        $mapeo_comprometido = soloNumeros($_REQUEST["mapeo_comprometido"]);
        
        $mapeo_oracion = soloNumeros($_REQUEST["mapeo_oracion"]);        
        $mapeo_companerismo = soloNumeros($_REQUEST["mapeo_companerismo"]);        
        $mapeo_adoracion = soloNumeros($_REQUEST["mapeo_adoracion"]);        
        $mapeo_biblia = soloNumeros($_REQUEST["mapeo_biblia"]);        
        $mapeo_evangelizar = soloNumeros($_REQUEST["mapeo_evangelizar"]);        
        $mapeo_cena = soloNumeros($_REQUEST["mapeo_cena"]);        
        $mapeo_dar = soloNumeros($_REQUEST["mapeo_dar"]);        
        $mapeo_bautizar = soloNumeros($_REQUEST["mapeo_bautizar"]);        
        $mapeo_trabajadores = soloNumeros($_REQUEST["mapeo_trabajadores"]);        
        
        //
        $sql = 'UPDATE  sat_reportes SET 
                    inactivo = '.$inactivo.', 
                    comentario = "'.$comentario.'", 
                    rep_entr = "'.$entrenador.'", 
                    plantador = "'.$plantador.'", 
                    fechaInicio = "'.$fechaInicio.'", 
                    sitioReunion = '.$sitioReunion.', 
                    grupoMadre_txt = "'.$grupoMadre_txt.'", 
                    nombreGrupo_txt = "'.$nombreGrupo_txt.'",                     
                    capacitacion_txt = "'.$capacitacion_txt.'", 
                    generacionNumero = "'.$generacionNumero.'", 

                    pabellon = "'.$pabellon.'", 
                    direccion = "'.$direccion.'", 
                    ciudad = '.$ciudad.', 

                        asistencia_hom = "'.$asistencia_hom.'", 
                        asistencia_muj = "'.$asistencia_muj.'", 
                        asistencia_jov = "'.$asistencia_jov.'", 
                        asistencia_nin =  "'.$asistencia_nin.'", 

                    bautizados =  "'.$bautizados.'", 
                    bautizadosPeriodo = "'.$bautizadosPeriodo.'", 
                    
                    bautizados =  "'.$bautizados.'", 
                    bautizadosPeriodo = "'.$bautizadosPeriodo.'", 
                    
                    graduados =  "'.$graduados.'", 
                    graduadosPeriodo = "'.$graduadosPeriodo.'", 

                    asistencia_total = "'.$asistencia_total.'", 
                    discipulado = "'.$discipulado.'", 
                    desiciones =  "'.$desiciones.'",
                    rep_ndis =  "'.$rep_ndis.'",
                    number_person_without_freedom = "'.$number_person_without_freedom.'",
                    number_person_post_penalties = "'.$number_person_post_penalties.'",
                    preparandose = "'.$preparandose.'",


                    mapeo_fecha = "'.$mapeo_fecha.'",
                    mapeo_comprometido = "'.$mapeo_comprometido.'",

                        mapeo_oracion = "'.$mapeo_oracion.'",
                        mapeo_companerismo = "'.$mapeo_companerismo.'",
                        mapeo_adoracion = "'.$mapeo_adoracion.'",
                        mapeo_biblia = "'.$mapeo_biblia.'",
                        mapeo_evangelizar = "'.$mapeo_evangelizar.'",
                        mapeo_cena = "'.$mapeo_cena.'",
                        mapeo_dar = "'.$mapeo_dar.'",
                        mapeo_bautizar = "'.$mapeo_bautizar.'",
                        mapeo_trabajadores = "'.$mapeo_trabajadores.'",

                    mapeo_anho = "'.$mapeo_anho.'",
                    mapeo_cuarto = "'.$mapeo_cuarto.'"';

    
                if($archivo1 != ""){
                    $sql .= ', ext1 = "'.$archivo1.'"';
                }

        
                if($archivo2 != ""){
                    $sql .= ', ext2 = "'.$archivo2.'"';
                }

        
                if($archivo3 != ""){
                    $sql .= ', ext3 = "'.$archivo3.'"';
                }


        $sql .= '   ,modificacionFecha = NOW(),
                    modificacionUsuario = "'.$_SESSION["id"].'"
                WHERE id = "'.$idReporteActual.'"';
        $PSN1->query($sql);
        $numero_baut = 0;
        
        if (isset($_REQUEST['act_bau_id'])) {
            $act_bau_img = $_FILES["act_bau_img"];
            $act_bau_imgAn = $_REQUEST["act_bau_img_an"];
            $act_bau_fec = $_REQUEST['act_bau_fec'];
            $act_bau_can = $_REQUEST['act_bau_can'];
            $act_bau_id = $_REQUEST['act_bau_id'];
            $numero_baut = sizeof($act_bau_id);
            //echo "Si hay antiguos a modificar: ".sizeof($act_bau_id);
            //var_dump($act_bau_id);
            for ($i=0; $i < sizeof($act_bau_id); $i++) {
                
                $sqlA = "UPDATE  tbl_adjuntos SET ";
                if (!empty($act_bau_img['name'][$i])) {
                    $tp_arch = extension_archivo($act_bau_img['name'][$i]);
                    $sqlA .= "adj_nom = '".$act_bau_img['name'][$i]."', adj_url = 'archivos/evi_".$idReporteActual."_".$i.".".$tp_arch."',";
                    if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                        //echo "No elimina";
                        $rutaOr = $act_bau_img['tmp_name'][$i];
                        $rutaDe = "archivos/evi_".$idReporteActual."_".$i.".".$tp_arch;

                        compressImage($rutaOr, $rutaDe, 80);
                    }else{
                        //echo "Si elimina: ".$act_bau_imgAn[$i];
                        unlink("./".$act_bau_imgAn[$i]);
                        if(move_uploaded_file($act_bau_img['tmp_name'][$i], "archivos/evi_".$idReporteActual."_".$i.".".$tp_arch)){
                        }            
                    }
                }
                    $sqlA .= "adj_fec = '".$act_bau_fec[$i]."', 
                    adj_can = ".$act_bau_can[$i].",
                    adj_rep_fk = ".$idReporteActual."
                    WHERE adj_id = ".$act_bau_id[$i]." ";
                //echo $sqlA;
                $PSN1->query($sqlA);
            }
        }
        $act_bau_can = $_REQUEST['act_bau_can'];
        $act_bau_fec = $_REQUEST['act_bau_fec'];
        $totalReg= 0;
        //var_dump($act_bau_can);
        for ($i=0; $i < sizeof($act_bau_can); $i++) { 
            if (!empty($act_bau_can[$i])&& !empty($act_bau_fec[$i])) {
                $totalReg++;
            }
        }
        //echo $totalReg;
        
        
        
        $nuevos = $totalReg-$numero_baut;
        //echo "Total de registros: ".$totalReg." nuevos: ".$nuevos;
        if ($nuevos>0) {
            //echo "Si hay nuevos a crear: ".$nuevos;
            $act_bau_img = $_FILES["act_bau_img"];                
            $sql = 'INSERT INTO tbl_adjuntos (
                adj_nom,
                adj_url,
                adj_fec,
                adj_can, 
                adj_rep_fk)';
            $sql .= 'VALUES';
            for ($i=(sizeof($act_bau_fec)-$nuevos); $i < sizeof($act_bau_fec); $i++) { 
                $tp_arch = extension_archivo($act_bau_img['name'][$i]);
                $sql .= "('".$act_bau_img['name'][$i]."','archivos/evi_".$idReporteActual."_".$i.".".$tp_arch."','".$act_bau_fec[$i]."',".$act_bau_can[$i].",".$idReporteActual."),";
                $extArchivo = $tp_arch;
                if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                    $rutaOr = $act_bau_img['tmp_name'][$i];
                    $rutaDe = "archivos/evi_".$idReporteActual."_".$i.".".$tp_arch;
                    compressImage($rutaOr, $rutaDe, 80);
                }else{
                    if(move_uploaded_file($act_bau_img['tmp_name'][$i], "archivos/evi_".$idReporteActual."_".$i.".".$tp_arch)){
                    }            
                }
            }
            $sql = substr($sql, 0, -1);
            //echo $sql;
            $ultimoQuery = $PSN1->query($sql);
        }
        
        
        
        
        
        
        
        $varExitoREP_UPD = 1;
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


        if($archivo3 != ""){
            $extArchivo = $archivo3;
            if($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif"){
                $rutaOrigen = $_FILES['archivo3']['tmp_name'];
                $rutaDestino = "archivos/evi_".$ultimoId."_3.".$archivo3;
                compressImage($rutaOrigen, $rutaDestino, 80);
            }
            else{
                if(move_uploaded_file($_FILES['archivo3']['tmp_name'], "archivos/evi_".$ultimoId."_3.".$archivo3))
                {
                }            
                else{
                    echo "Error";

                }
            }
        }
	}
}


switch($error_datos){
    case 1:
        $texto_error = isset($error_mensaje) ? $error_mensaje : "Datos requeridos.";
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
    *	TRAEMOS LOS DATOS PRINCIPALES DEL USUARIO
    */
    $sql = "SELECT CA.descripcion AS zona,C.descripcion AS regional, U.nombre AS coordinador, U.id as id_coordinador, sat_reportes.*, sat_grupos.nombre, D.id_departamento,M.id_municipio FROM sat_reportes"; 
    $sql.=" LEFT JOIN sat_grupos ON sat_grupos.id = sat_reportes.idGrupoMadre 
LEFT JOIN dane_municipios AS M ON sat_reportes.ciudad = M.id_municipio 
LEFT JOIN dane_departamentos AS D ON M.departamento_id = D.id_departamento 
LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario
LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
LEFT JOIN categorias AS C ON C.id = UE.empresa_pd
LEFT JOIN categorias AS CA ON CA.id = C.idSec";
    $sql.=" WHERE sat_reportes.id = '".$idReporteActual."'";
    $sql.=" GROUP BY sat_reportes.id";
    $PSN1->query($sql);
    //echo $sql;
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $zona = $PSN1->f("zona");
            $regional = $PSN1->f("regional");
            $inactivo = $PSN1->f("inactivo");
            $comentario = $PSN1->f("comentario");
            $plantador = $PSN1->f("plantador");
            $entrenador = $PSN1->f("rep_entr");
            $coordinador = $PSN1->f("coordinador");
            $id_coordinador = $PSN1->f("id_coordinador");
            $fechaReporte = $PSN1->f("fechaReporte");
            $fechaInicio = $PSN1->f("fechaInicio");        
            $sitioReunion = $PSN1->f("sitioReunion");
            $grupoMadre_txt = $PSN1->f("grupoMadre_txt");
            $nombreGrupo_txt = $PSN1->f("nombreGrupo_txt");
            
            $capacitacion_txt = $PSN1->f("capacitacion_txt");

            $pabellon = $PSN1->f("pabellon");
            $direccion = $PSN1->f("direccion");
            $municipio = $PSN1->f("ciudad");
            $departamento = $PSN1->f("id_departamento");
            $_SESSION['muni'] = $PSN1->f("ciudad");
            
            $ext1 = $PSN1->f("ext1");
            $ext2 = $PSN1->f("ext2");
            $ext3 = $PSN1->f("ext3");
            
            $idGrupoMadre = $PSN1->f("idGrupoMadre");
            $generacionNumero = $PSN1->f("generacionNumero");

            $asistencia_hom = $PSN1->f("asistencia_hom");
            $asistencia_muj = $PSN1->f("asistencia_muj");
            $asistencia_jov = $PSN1->f("asistencia_jov");
            $asistencia_nin = $PSN1->f("asistencia_nin");

            $bautizados = $PSN1->f("bautizados");
            $bautizadosPeriodo = $PSN1->f("bautizadosPeriodo");
            
            $graduados = $PSN1->f("graduados");
            $graduadosPeriodo = $PSN1->f("graduadosPeriodo");
            

            //Calculados:
            $asistencia_total  = $PSN1->f("asistencia_total");
            $discipulado  = $PSN1->f("discipulado");
            $desiciones  = $PSN1->f("desiciones");
            $rep_ndis  = $PSN1->f("rep_ndis");
            $number_person_without_freedom = $PSN1->f("number_person_without_freedom");
            $number_person_post_penalties = $PSN1->f("number_person_post_penalties");
            
            $preparandose  = $PSN1->f("preparandose");
            $iglesias_reconocidas = $PSN1->f("iglesias_reconocidas");  
            
            
            $mapeo_fecha = $PSN1->f("mapeo_fecha");  
            $mapeo_comprometido = $PSN1->f("mapeo_comprometido");  
            
            $mapeo_oracion = $PSN1->f("mapeo_oracion");  
            $mapeo_companerismo = $PSN1->f("mapeo_companerismo");  
            $mapeo_adoracion = $PSN1->f("mapeo_adoracion");  
            $mapeo_biblia = $PSN1->f("mapeo_biblia");  
            $mapeo_evangelizar = $PSN1->f("mapeo_evangelizar");  
            $mapeo_cena = $PSN1->f("mapeo_cena");  
            $mapeo_dar = $PSN1->f("mapeo_dar");  
            $mapeo_bautizar = $PSN1->f("mapeo_bautizar");  
            $mapeo_trabajadores = $PSN1->f("mapeo_trabajadores");  
            
            
            //
        }//chequear el registro
    }else{
        ?><div class="row">
            <h3 class="alert alert-info text-center">Registro eliminado</h3>
        </div>
        <div class="form-group">
            <center><input type="button" onClick="window.location.href='index.php?doc=consultar-sub-programa-ecc'" name="previous" class="previous btn btn-danger" value="Cerrar" /> <br />
        </div>
        <?php
        exit;
    }
    $sql = "SELECT SUM(adj_can) as suma";
    $sql.=" FROM tbl_adjuntos ";
    $sql.=" WHERE adj_rep_fk = '".$idReporteActual."'";
    $PSN1->query($sql);
    if($PSN1->num_rows() > 0){
        if($PSN1->next_record()){
            $sum_baut = $PSN1->f("suma");
        }
    }
    
    ?><div class="container">
    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
        <h3 class="alert alert-info text-center"><?php
            if($idReporteActual == 0){
                echo "REPORTE";
            }else{
                echo "VISUALIZACIÓN";
                $sqlU = "SELECT SR.id FROM sat_reportes AS SR
                LEFT JOIN usuario AS U ON U.id = SR.idUsuario
                LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
                LEFT JOIN categorias AS C ON C.id = UE.empresa_pd
                WHERE SR.id = (SELECT MAX(STR.id)FROM sat_reportes AS STR WHERE STR.id < ".$idReporteActual.") ";
                    if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                        $sqlU .= "AND UE.empresa_pd = ".$_SESSION["empresa_pd"]." ";
                    }
                    $sqlU .= "AND SR.rep_tip = 308";
                $PSN1->query($sqlU);
                //echo $sqlU; 
                if($PSN1->num_rows() > 0){
                    if($PSN1->next_record()){
                    $antId  = $PSN1->f('id');
                    }
                }else{
                   $antId  = 0; 
                }
                $sqlU = "SELECT SR.id FROM sat_reportes AS SR
                LEFT JOIN usuario AS U ON U.id = SR.idUsuario
                LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id
                LEFT JOIN categorias AS C ON C.id = UE.empresa_pd
                WHERE SR.id = (SELECT MIN(STR.id)FROM sat_reportes AS STR WHERE STR.id > ".$idReporteActual.") ";
                    if ($_SESSION["empresa_pd"]!="" && $_SESSION["empresa_pd"]!=0) {
                        $sqlU .= "AND UE.empresa_pd = ".$_SESSION["empresa_pd"]." ";
                    }
                    $sqlU .= "AND SR.rep_tip = 308";
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
            <?php if (!$puede_editar_reporte && $idReporteActual > 0) { ?>
                <div class="alert alert-warning text-center" style="margin: 10px 0;">
                    <strong>⚠️ Solo los usuarios autorizados pueden editar reportes.</strong>
                </div>
            <?php } ?>
            <?php //if ($_SESSION["perfil"] == 162 || $_SESSION["perfil"] == 2){ ?>
            <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <?php
                    if ($antId != 0) {?>
                    <a href="index.php?doc=gestionar-sub-programa-ecc&id=<?=$antId ?>" name="previous" class="previous btn btn-info">Anterior reporte <?=$antId ?></a>
                    <?php } ?>
                </div>
                <div class="item-btn">
                    <a href="index.php?doc=consultar-sub-programa-ecc" name="previous" class="btn btn-warning">Todos los reportes</a>
                </div>
                <div class="item-btn">
                    <?php
                    if ($sigId != 0) {?>
                    <a href="index.php?doc=gestionar-sub-programa-ecc&id=<?=$sigId ?>" name="previous" class="previous btn btn-info">Siguiente reporte <?=$sigId ?></a>
                    <?php } ?>
                </div>
            </div>
    <?php //} ?>
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
                <strong>Zona:</strong>
                <input name="zona" type="text" id="zona" maxlength="250" value="<?=$zona; ?>" class="form-control" readonly required />
            </div>
            <div class="col-sm-2">
                <strong>Regional:</strong>
                <input name="regional" type="text" id="regional" maxlength="250" value="<?=$regional; ?>" class="form-control" readonly required />
            </div>
            <div class="col-sm-3">
                <strong>Miembro de la regional:</strong>
                <select required readonly name="usua_id" id="usua_id" class="form-control">
                    <option value="<?=$id_coordinador; ?>"><?=$coordinador; ?></option>
                </select>
            </div>
            <div class="col-sm-3">
                <strong>Entrenador:</strong>
                <input name="entrenador" type="text" id="entrenador" maxlength="50" value="<?=$entrenador; ?>" class="form-control" required  />
            </div>
            
            <div class="col-sm-1"></div>
        </div>
        <div class="form-group">
            <div class="col-sm-1"></div>
            <div class="col-sm-3">
                <strong>Siervo Facilitador:</strong>
                <input name="plantador" type="text" id="plantador" maxlength="255" value="<?=$plantador; ?>" class="form-control" required  />
            </div>
            <div class="col-sm-2">
                <strong>Fecha del reporte:</strong>
                <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250" value="<?=$fechaReporte; ?>" class="form-control" required readonly  />
            </div>
            <!--<label class="control-label col-sm-2" for="sitioReunion"><strong>Sitio de la reunión:</strong></label>
            <div class="col-sm-4"><input name="sitioReunion" type="text" id="sitioReunion" maxlength="250" value="<?=$sitioReunion; ?>" class="form-control" required  />
            </div>//-->

            <div class="col-sm-3">
                <strong>Fecha de inicio <?php echo($sitioReunion != 0)?"de la confraternidad restaurativa":"de la iglesia extramuros" ?>:</strong>
                <input name="fechaInicio" type="date" id="fechaInicio" maxlength="250" value="<?=$fechaInicio; ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required  />
            </div>
            <?php if($sitioReunion != 0){  ?>
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
            <div class="col-sm-1">
                <strong>Patio/Pabellón:</strong></label>
                <input name="pabellon" type="text" id="pabellon" maxlength="255" value="<?=$pabellon; ?>" class="form-control" required />
            </div> 
        
        <?php }else{ ?>
        </div>
        <div class="form-group">
            <div class="col-sm-1"></div>
            <div class="col-sm-2">
                    <strong>Departamento</strong>
                    <select name="departamento" id="departamento" style="text-transform: capitalize;" class="form-control">
                        <option value="">Sin especificar</option>
                        <?php
                        /*
                        *   TRAEMOS LOS TIPOS DE IDENTIFICACION (2)
                        */
                        $sql = "SELECT id_departamento,lower(departamento) as departamento ";
                        $sql.=" FROM dane_departamentos ";
                        $sql.=" ORDER BY departamento asc";
                        $PSN1->query($sql);
                        $numero=$PSN1->num_rows();
                        if($numero > 0){
                            while($PSN1->next_record()){
                                ?><option style="text-transform: capitalize;" value="<?=$PSN1->f('id_departamento'); ?>" <?php
                                if($departamento == $PSN1->f('id_departamento'))
                                {
                                    ?>selected="selected" <?php
                                }
                                ?> ><?=$PSN1->f('departamento'); ?></option><?php
                            }
                        }
                        ?>
                    </select>
                </div>
                 <div class="col-sm-2">
                    <div id="municipio"></div>
                </div>
                <div class="col-sm-3">
                    <strong>Dirección</strong><input name="direccion" type="text" id="direccion" value="<?=$direccion; ?>" class="form-control" />
                </div>
            <?php } ?>

            <div class="col-sm-<?php echo($sitioReunion != 0)?"2":"3" ?>">
                <strong>Grupo madre:</strong>
                <input readonly name="grupoMadre_txt" type="text" id="grupoMadre_txt" value="Confraternidad carcelaria de Colombia" class="form-control" />
            </div>
            </div>
        <div class="form-group">
            
            <?php
            if($generacionNumero == 0){
                ?><div class="col-sm-4">
                    <strong>Generación:</strong>
                    <input name="temporal_solotxt" type="text" id="temporal_solotxt" value="0" readonly class="form-control"  /><input name="generacionNumero" type="hidden" id="generacionNumero" value="<?=$generacionNumero; ?>" readonly class="form-control" required /></div><?php
            }else if ($generacionNumero == 77){?>
                <div class="col-sm-4">
                    <strong>Generación:</strong>
                    <input name="temporal_solotxt" type="text" id="temporal_solotxt" value="EVANGELISMO" readonly class="form-control"  />
                    <input name="generacionNumero" type="hidden" id="generacionNumero" value="<?=$generacionNumero; ?>" readonly class="form-control" required />
                </div>
            <?php  }else if ($generacionNumero == 8){?>
                <div class="col-sm-4">
                    <strong>Generación:</strong>
                    <input name="temporal_solotxt" type="text" id="temporal_solotxt" value="GRAN CELEBRACIÓN" readonly class="form-control"  />
                    <input name="generacionNumero" type="hidden" id="generacionNumero" value="<?=$generacionNumero; ?>" readonly class="form-control" required />
                </div>
            <?php  } ?>            
        </div>
        <?php  if($generacionNumero != 0 && $generacionNumero != 77 && $generacionNumero != 8){?>            
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">GENERACIÓN</h3>
                    <h5>seleccione una opción</h5>
                </div>
                <div class="hr"><hr></div>
            </div>         
            <div class="form-group">
                <label class="control-label col-sm-1" for="inlineRadio1">1</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="1" <?php if($generacionNumero == 1){ ?>checked<?php } ?> required class="form-control" />
                </div>
            
                <label class="control-label col-sm-1" for="inlineRadio2">2</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="2" <?php if($generacionNumero == 2){ ?>checked<?php } ?> <?php if($sitioReunion != 0){ ?>disabled <?php } ?> class="form-control" />
                </div>

                <label class="control-label col-sm-1" for="inlineRadio3">3</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="3" <?php if($generacionNumero == 3){ ?>checked<?php } ?> <?php if($sitioReunion != 0){ ?>disabled <?php } ?> class="form-control" />
                </div>

                <label class="control-label col-sm-1" for="inlineRadio4">4</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="4" <?php if($generacionNumero == 4){ ?>checked<?php } ?> <?php if($sitioReunion != 0){ ?>disabled <?php } ?> class="form-control" />
                </div>

                <label class="control-label col-sm-1" for="inlineRadio5">5</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="5" <?php if($generacionNumero == 5){ ?>checked<?php } ?> <?php if($sitioReunion != 0){ ?>disabled <?php } ?> class="form-control" />
                </div>
            </div>
        <?php
        }
        ?>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3><?php if($generacionNumero == 77){ echo "ALCANZADOS"; }else{ echo "ASISTENCIA"; } ?></h3>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-3"></div>
            <?php if($generacionNumero == 0 || $generacionNumero == 77){?>
                <div class="col-sm-1">
                    <strong>Hombres:</strong>
                    <input name="final_asistencia_hom" type="number" id="final_asistencia_hom" value="<?=$asistencia_hom; ?>" class="form-control" onChange="sumar()"  />
                </div>
                <div class="col-sm-1">
                    <strong>Mujeres:</strong>
                    <input name="final_asistencia_muj" type="number" id="final_asistencia_muj" value="<?=$asistencia_muj; ?>" class="form-control"  onChange="sumar()" />
                </div>
                <div class="col-sm-1">
                    <strong>Jóvenes:</strong>
                    <input name="final_asistencia_jov" type="number" id="final_asistencia_jov" value="<?=$asistencia_jov; ?>" class="form-control" onChange="sumar()"  />
                </div>
                <div class="col-sm-1">
                    <strong>Niños:</strong>
                    <input name="final_asistencia_nin" type="number" id="final_asistencia_nin" value="<?=$asistencia_nin; ?>" class="form-control" onChange="sumar()"  />
                </div>
            <?php }else{?>
                <div class="col-sm-1">
                    <strong>Hombres:</strong>
                    <input name="final_asistencia_hom" type="number" id="final_asistencia_hom" value="<?=$asistencia_hom; ?>" class="form-control" onChange="sumar()"  />
                </div>
                <div class="col-sm-1">
                    <strong>Mujeres:</strong>
                    <input name="final_asistencia_muj" type="number" id="final_asistencia_muj" value="<?=$asistencia_muj; ?>" class="form-control" onChange="sumar()"  />
                </div>
                <div class="col-sm-1">
                    <strong>Jóvenes:</strong>
                    <input name="final_asistencia_jov" type="number" id="final_asistencia_jov" value="<?=$asistencia_jov; ?>" class="form-control" onChange="sumar()"  />
                </div>
                <div class="col-sm-1">
                    <strong>Niños:</strong>
                    <input name="final_asistencia_nin" type="number" id="final_asistencia_nin" value="<?=$asistencia_nin; ?>" class="form-control" onChange="sumar()"  />
                </div>
            
            <?php } ?>
            <div class="col-sm-2">
                <strong><?php if($generacionNumero == 77 && $generacionNumero == 8){ echo "Alcanzados"; }else{ echo "Asistencia"; } ?> total:</strong>
                <input name="asistencia_total" type="number" id="asistencia_total" value="<?=$asistencia_total; ?>" readonly class="form-control"  />
            </div>
            <div class="col-sm-3"></div>
        </div>
            
        <?php if($generacionNumero == 0 || $generacionNumero == 77 || $generacionNumero == 8){?>
            <input name="final_bautizados" type="hidden" id="final_bautizados"  value="<?=$bautizados; ?>" class="form-control" readonly />
            <input name="final_discipulado" type="hidden" id="final_discipulado" value="<?=$discipulado; ?>" class="form-control" readonly />
            <input name="final_desiciones" type="hidden" id="final_desiciones" value="<?=$desiciones; ?>" class="form-control" readonly />
            <input name="final_preparandose" type="hidden" id="final_preparandose" value="<?=$preparandose; ?>" class="form-control" readonly />
            <input name="final_bautizadosPeriodo" type="hidden" id="final_bautizadosPeriodo" value="<?=$bautizadosPeriodo; ?>" class="form-control" readonly />
            
             <input name="final_graduadosPeriodo" type="hidden" id="final_graduadosPeriodo" value="<?=$graduadosPeriodo; ?>" class="form-control" readonly />

            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método DE VERIFICACIÓN</h3>
                    <h5>Fotográfias</h5>
                </div>
                <div class="hr"><hr></div>
            </div> 
            <div class="cont-flex fl-sard">
                <div class="cont-item col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <strong style="font-size: 18px;display: block;margin-top: 10px;">Foto 1:</strong>
                            <?php
                            if($ext1 == "" || !file_exists("archivos/evi_".$idReporteActual."_1.".$ext1)){
                                echo "<div class='alert alert-danger' >Sin foto cargada</div>";
                            }else{?>
                                <a href="archivos/evi_<?=$idReporteActual; ?>_1.<?=$ext1; ?>" target="_blank"><img src="archivos/evi_<?=$idReporteActual; ?>_1.<?=$ext1; ?>" width="100%" /></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <strong>Cargar foto 1:</strong>
                            <input name="archivo1" type="file" id="archivo1" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="cont-item col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <strong style="font-size: 18px;display: block;margin-top: 10px;">Foto 2:</strong>
                            <?php
                            if($ext2 == "" || !file_exists("archivos/evi_".$idReporteActual."_2.".$ext2)){
                                echo "<div class='alert alert-danger' >Sin foto cargada</div>";
                            }else{?>
                                <a href="archivos/evi_<?=$idReporteActual; ?>_2.<?=$ext2; ?>" target="_blank"><img src="archivos/evi_<?=$idReporteActual; ?>_2.<?=$ext2; ?>" width="100%" /></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <strong>Cargar foto 2:</strong>
                            <input name="archivo2" type="file" id="archivo2" class="form-control" />
                        </div>
                    </div>
                </div>
                <div class="cont-item col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12">
                            <strong style="font-size: 18px;display: block;margin-top: 10px;">Foto 3:</strong>
                            <?php
                            if($ext3 == "" || !file_exists("archivos/evi_".$idReporteActual."_3.".$ext3)){
                                echo "<div class='alert alert-danger' >Sin foto cargada</div>";
                            }else{?>
                                <a href="archivos/evi_<?=$idReporteActual; ?>_3.<?=$ext3; ?>" target="_blank"><img src="archivos/evi_<?=$idReporteActual; ?>_3.<?=$ext3; ?>" width="100%" /></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12">
                            <strong>Cargar foto 3:</strong>
                            <input name="archivo3" type="file" id="archivo3" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
                
        <?php }else{ ?>
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">OTROS DATOS</h3>
                    <h5>DEL PROCESO</h5>
                </div>
                <div class="hr"><hr></div>
            </div> 
            <div class="form-group">
                <div class="col-sm-2">
                    <strong>Miembros Bautizados:</strong>
                    <input name="final_bautizados" type="number" id="final_bautizados" readonly value="<?=$bautizados; ?>" class="form-control"  />
                </div>
                <div class="col-sm-2">
                    <strong>En discipulado:</strong>
                    <input name="final_discipulado" type="number" id="final_discipulado" readonly value="<?=$discipulado; ?>" class="form-control"  />
                </div>
                <div class="col-sm-2">
                    <strong>Decisiones para Cristo:</strong>
                    <input name="final_desiciones" type="number" id="final_desiciones" value="<?=$desiciones; ?>" class="form-control"  />
                </div>
                <div class="col-sm-2">
                    <strong>Numero de discípulos LPP:</strong>
                    <input name="rep_ndis" type="number" id="rep_ndis" value="<?=$rep_ndis; ?>" min="0" class="form-control"  />
                </div>
                <div class="col-sm-2">
                    <strong>Preparándose para bautismo:</strong>
                <input name="final_preparandose" type="number" id="final_preparandose" readonly value="<?=$preparandose; ?>" class="form-control"  /></div>
                <div class="col-sm-2">
                    <strong>Bautizados este período:</strong>
                    <input readonly name="final_bautizadosPeriodo" type="number" id="final_bautizadosPeriodo" value="<?=$bautizadosPeriodo;  ?>" class="form-control"  onChange="sumar()" />
                </div>
                
                <div class="col-sm-2">
                    <strong>Graduados este período:</strong>
                    <input readonly name="final_graduadosPeriodo" type="number" id="final_graduadosPeriodo" value="<?=$graduadosPeriodo;  ?>" class="form-control"  onChange="sumar()" />
                </div>
                <div class="col-sm-1"></div>
            </div>  
             <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-5">
                    <strong>Número de familias de personas privadas de la libertad atendidas</strong>
                    <input min="0" max="9999" value="<?php echo $number_person_without_freedom; ?>" required type="number" name="number_person_without_freedom" id="number_person_without_freedom" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control" />
                </div>
                <div class="col-sm-5">
                    <strong>Número de familias de pospenados atendidas</strong>
                    <input min="0" value="<?php echo $number_person_post_penalties; ?>" required type="number" name="number_person_post_penalties" id="number_person_post_penalties" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control" />
                </div>
            </div>

            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">MAPEO</h3>
                    <h5>De la iglesia</h5>
                </div>
                <div class="hr"><hr></div>
            </div> 

            <div class="form-group">
                <div class="col-sm-4">
                    <strong>Fecha de mapeo:</strong>
                    <input required name="mapeo_fecha" type="date" id="mapeo_fecha" value="<?=$mapeo_fecha; ?>" max='<?=date("Y-m-d"); ?>' class="form-control" readonly />
                </div>
                <div class="col-sm-4">
                    <strong>¿Este grupo está comprometido como iglesia?:</strong>
                    <select required name="mapeo_comprometido" id="mapeo_comprometido" class="form-control">
                        <option value="">Sin seleccionar</option>
                        <option value="3" <?php if($mapeo_comprometido == 3){ ?>selected="selected"<?php } ?>>NO está comprometido como iglesia</option>
                        <option value="4" <?php if($mapeo_comprometido == 4){ ?>selected="selected"<?php } ?>>SI está comprometido como iglesia</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <strong>Nombre grupo/iglesia:</strong>
                    <input required name="nombreGrupo_txt" type="text" id="nombreGrupo_txt" value="<?=$nombreGrupo_txt; ?>" class="form-control" readonly />
                </div>
            </div>
            <div style="display: flex;flex-wrap: wrap; justify-content: space-between;">
            <?php
            $array_campos = array(
                "mapeo_oracion",
                "mapeo_companerismo",
                "mapeo_adoracion",
                "mapeo_biblia",
                "mapeo_evangelizar",
                "mapeo_cena",
                "mapeo_dar",
                "mapeo_bautizar",
                "mapeo_trabajadores"                
            );
            $array_campos_valor = array(
                $mapeo_oracion,
                $mapeo_companerismo,
                $mapeo_adoracion,
                $mapeo_biblia,
                $mapeo_evangelizar,
                $mapeo_cena,
                $mapeo_dar,
                $mapeo_bautizar,
                $mapeo_trabajadores
            );            
            $array_campos_txt = array(
                "Orar",
                "Compañerismo",
                "Adorar",
                "Aplicar la biblia",
                "Evangelizar",
                "Cena del Señor",
                "Dar",
                "Bautizar",
                "Entrenar nuevos lideres"
            );
            $total_campos = count($array_campos);
            for($i=0; $i<$total_campos;$i++){
                $total_valor += $array_campos_valor[$i];
                ?>
                <div class="row col-sm-6">
                    <h4 class="alert alert-warning"><?=$array_campos_txt[$i]; ?></h4>
                    <div class="form-group cont-mapeo">
                        <div class="map-chec"><input required type="radio" name="<?=$array_campos[$i]; ?>"  <?php
                        if($array_campos_valor[$i] == 1){
                            ?>checked="checked"<?php
                        }
                        ?> value="1" class="form-control" /></div>
                        <div class="map-text" style="display: flex;align-items: center;"><img src="mapeo_img/<?=$array_campos[$i]; ?>1.png" class="img-responsive" /> NO REALIZAN LA TAREA</div>
                    </div>
                    <div class="form-group cont-mapeo">
                        <div class="map-chec"><input required type="radio" name="<?=$array_campos[$i]; ?>" <?php
                        if($array_campos_valor[$i] == 2){
                            ?>checked="checked"<?php
                        }
                        ?> value="2" class="form-control" /></div>
                        <div class="map-text"><img width="40" src="mapeo_img/<?=$array_campos[$i]; ?>2.png" class="img-responsive" /> REALIZAN LA TAREA EN COMPAÑIA DEL ENTRENADOR</div>
                    </div>
                    <div class="form-group cont-mapeo">
                        <div class="map-chec"><input type="radio" name="<?=$array_campos[$i]; ?>" <?php
                        if($array_campos_valor[$i] == 3){
                            ?>checked="checked"<?php
                        }
                        ?> value="3" class="form-control"  /></div>
                        <div class="map-text"><img src="mapeo_img/<?=$array_campos[$i]; ?>3.png" class="img-responsive" /> REALIZAN LA TAREA PERO ESTE MES NO LO HIZO</div>
                    </div>
                    <div class="form-group cont-mapeo">
                        <div class="map-chec"><input required type="radio" name="<?=$array_campos[$i]; ?>" <?php
                        if($array_campos_valor[$i] == 4){
                            ?>checked="checked"<?php
                        }
                        ?> value="4" class="form-control"  /></div>
                        <div class="map-text"><img width="40" src="mapeo_img/<?=$array_campos[$i]; ?>4.png" class="img-responsive" /> REALIZAN LA TAREA AUTONOMAMENTE</div>
                    </div>
                </div>
            <?php } ?>
            
                <div class="row col-sm-6">
                    <h5 class="alert alert-info text-center">IMAGEN DEL MAPEO</h5>
                    <div class="form-group">
                        <div class="col-sm-3">&nbsp;</div>
                        <div class="col-sm-6"><img src="mapeo_img.php?id=<?=$idReporteActual; ?>&time=<?=strtotime("now"); ?>" class="img-responsive" /></div>
                        <div class="col-sm-3">&nbsp;</div>
                    </div>
                </div>     
            </div>

            <?php if ($generacionNumero != 0 && $generacionNumero != 77 && $generacionNumero != 8 ) {?>
                <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método DE VERIFICACIÓN</h3>
                    <h5>BAUTIZOS</h5>
                </div>
                <div class="hr"><hr></div>
            </div>
            <?php if ($_POST["funcion"]=="actualizar") {
                        echo "<div class='alert alert-danger text-center' >Si alguna actulización no se ve reflajada al instante, puede ser cache!</div>";
                    } ?>
                    
            <div class="cont-flex fl-sard">
                <script>
                       $(function(){
                        var tot = 0;
                        var tar = $(".act_bau_fec").val();
                        var nom = $(".act_bau_can").val();
                        if (tar == "" || nom == "") {
                            $("#adicionarAdd").prop( "disabled", true );
                        }else{
                            <?php if($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte){ ?>
                                $("#adicionarAdd").prop( "disabled", true );
                            <?php }else{ ?>
                                $("#adicionarAdd").prop( "disabled", false );
                            <?php } ?>
                        }
                        $(".act_bau_fec").change(function(){
                            var tar3 = $(".act_bau_can").val();
                            var nom3 = $(".act_bau_fec").val();
                            if (tar3 != "" && nom3 !="") {
                                if (tot < 1) {
                                    tot = tot + 1;
                                }
                                $("#adicionarAdd").prop( "disabled", false );
                            }else if (tar3 == "" && nom3 =="") {
                                if (tot == 1) {
                                    tot = tot - 1;
                                    $(".act_bau_fec").prop('required',false);
                                    $(".act_bau_can").prop('required',false);
                                }
                            }else{
                                $("#adicionarAdd").prop( "disabled", true );
                            }
                        });
                        $(".act_bau_can").change(function(){
                            var nom2 = $(".act_bau_fec").val();
                            var tar2 = $(".act_bau_can").val();
                            if (nom2 != ""&& tar2 != "") {
                                if (tot < 1) {
                                    tot = tot + 1;
                                }
                                $("#adicionarAdd").prop( "disabled", false );
                            }else if (tar3 == "" && nom3 =="") {
                                if (tot == 1) {
                                    tot = tot - 1;
                                    $(".act_bau_fec").prop('required',false);
                                    $(".act_bau_can").prop('required',false);
                                }
                            }else{
                                $("#adicionarAdd").prop( "disabled", true );
                            }
                        });
                        $("#adicionarAdd").on('click',function(){
                            $("#tablaAdd tbody tr:eq(0)").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");
                            var total = 0;
                            $('.subtotal').each(function(){
                                 total = total + Number($(this).val());
                            });
                            $('#final_bautizadosPeriodo').val(total);
                            $('#final_bautizados').val(total+1);
                            
                        });
                        $(document).on("click",".eliminarAdd",function(){
                            var parent = $(this).parents().get(0);
                            $(parent).remove();
                        });

                        $(document).on("click","#act_bau_can",function(){
                            var total = 0;
                            $('.subtotal').each(function(){
                                 total = total + Number($(this).val());
                            });
                            $('#final_bautizadosPeriodo').val(total);
                            $('#final_bautizados').val(total+1);
                        });
                        $(document).on("click","#guarda_rep",function(){
                            var total = 0;
                            $('.subtotal').each(function(){
                                 total = total + Number($(this).val());
                            });
                            $('#final_bautizadosPeriodo').val(total);
                            $('#final_bautizados').val(total+1);
                        });
                        
                    });
                    </script>
                    
                    
                <?php 
                $sql = "SELECT * ";
                $sql.=" FROM tbl_adjuntos ";
                $sql.=" WHERE adj_rep_fk = '".$idReporteActual."'";
                $PSN1->query($sql);
                $numero=$PSN1->num_rows();
                if($numero > 0){
                    while($PSN1->next_record()){ ?>
                        <div class="cont-item col-sm-3">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="hidden" name="act_bau_id[]" value="<?= $PSN1->f("adj_id");  ?>" placeholder="">
                                    <strong style="font-size: 18px;display: block;margin-top: 10px;">Foto:</strong>
                                    <?php
                                    if(empty($PSN1->f("adj_url"))){
                                        echo "<div class='alert alert-danger' >Sin foto cargada</div>";
                                    }else{?>
                                        <a href="<?=$PSN1->f("adj_url"); ?>" target="_blank"><img src="<?=$PSN1->f("adj_url"); ?>" width="100%" /></a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <strong>Cargar foto:</strong>
                                    <input multiple name="act_bau_img[]" type="file" id="act_bau_img[]" class="form-control" value="<?=$PSN1->f("adj_url"); ?>" />
                                    <input type="hidden" name="act_bau_img_an[]" value="<?=$PSN1->f("adj_url"); ?>" placeholder="">
                                </div>
                                <div class="col-sm-7">
                                    <strong>Fecha:</strong>
                                    <input name="act_bau_fec[]" type="date" id="act_bau_fec" class="act_bau_fec form-control" value="<?=$PSN1->f("adj_fec"); ?>" />
                                </div>
                                <div class="col-sm-5">
                                    <strong>Cantidad:</strong>
                                    <input name="act_bau_can[]" type="number" id="act_bau_can" min="0" class="act_bau_can subtotal form-control" value="<?php echo $PSN1->f("adj_can"); ?>" />
                                </div>
                            </div>
                        </div>
                        
                    <?php }
                }
                
                
                if ($numero < 3) {?>
                    
                    <div class="form-group col-sm-12"><br>
                    <table id="tablaAdd">
                        <tr class="fila-fijaAdd">
                            <td class="col-sm-4">
                                <strong>Foto:</strong>
                                <input multiple name="act_bau_img[]" type="file" id="act_bau_img" class="form-control" />
                            </td>
                            <td class="col-sm-3">
                                <strong>Fecha:</strong>
                                <input name="act_bau_fec[]" type="date" id="act_bau_fec" class="form-control" />
                            </td>
                            <td class="col-sm-2">
                                <strong>Cantidad bautizados:</strong>
                                <input name="act_bau_can[]" type="number" id="act_bau_can" min="0" class="subtotal form-control" />
                            </td>
                            <td class="eliminarAdd"><button type="button" class="btn btn-cir-uno usua-col"><i class="fa fa-times"></i></button></td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-12">
                    <div class="col-sm-5">
                        <input type="hidden" name="total" id="total">
                    </div>
                    <div class="col-sm-2">
                        <center>
                            <button id="adicionarAdd" class="btn btn-success" type="button" class="boton"><i class="fas fa-plus"></i>  Adicionar</button>
                        </center>
                    </div>
                    <div class="col-sm-5"></div>
                </div>
                
                
                
                
                
                
                
             <?php } ?>
            </div>

            <?php } ?>
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método DE VERIFICACIÓN</h3>
                    <h5>FOTO DE LA CONFRATERNIDAD RESTAURATIVA</h5>
                    <p>Valide la información correspondiente</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                    <strong>Foto:</strong>
                    <?php
                    if($ext1 == "" || !file_exists("archivos/evi_".$idReporteActual."_1.".$ext1)){
                        ?><div class='alert alert-danger' style="margin-bottom: 0px !important;">Sin foto cargada</div><?php
                    }else{?>
                        <a href="archivos/evi_<?=$idReporteActual; ?>_1.<?=$ext1; ?>" target="_blank"><img src="archivos/evi_<?=$idReporteActual; ?>_1.<?=$ext1; ?>" width="100%" /></a>
                    <?php }?><br>
                    <strong>Cargar foto:</strong>
                    <input required name="archivo1" type="file" id="archivo1" class="form-control" />
                </div>
                <div class="col-sm-4"></div>
            </div>
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>Testimonio</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <strong>Testimonio:</strong>
                    <a href="archivos/evi_<?=$idReporteActual ?>_2.<?= $ext2?>">Archivo adjunto</a>
                    <input name="archivo2" type="file" id="archivo2" required class="form-control" accept=".docx,.doc,.odt,.pdf" />
                    <div id="archivo2-nombre" class="file-name-display" style="margin-top: 5px; color: #666; font-size: 14px;"></div>
                </div>
                <div class="col-sm-3"></div>
            </div>
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">ESTADO DEL REPORTE</h3>
                    <h5>ACTIVO/INACTIVO</h5>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-4"></div>
                <div class="col-sm-4">
                    <strong>Seleccione si el grupo dejó de reunirse y/o no continua en el proceso:</strong>
                    <label>
                        <input type="checkbox" name="inactivo" id="inactivo" value="1" <?php if($inactivo == 1){ ?>checked="checked"<?php } ?> />
                        <span class="check"></span>
                    </label> 
                </div>
                <div class="col-sm-4"></div>
            </div>
        <?php } ?>
        <?php if ($generacionNumero == 8) {?>
            <div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">OTROS DATOS DEL PROCESO</h3>
                        <h5>COMENTARIOS</h5>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <textarea name="final_comentarios" id="final_comentarios" style="width: 100%;"><?php echo $comentario; ?></textarea>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
            </div>
        <?php }  if ($_SESSION['perfil']!="168" && $puede_editar_reporte) {?>
            <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <input type="button" onClick="window.location.href='index.php?doc=reportar_buscar'" name="previous" class="previous btn btn-info" value="Cerrar" />
                </div>
                <div class="item-btn">
                    <input type="submit" name="button" value="Guardar cambios" class="btn btn-success" id="guarda_rep">
                </div>
                <div class="item-btn">
                    <input type="button" onClick="eliminarRegistro()" name="button" value="Eliminar" class="btn btn-danger">
                </div>
            </div>  
    <?php } ?>        
    <input type="hidden" name="funcion" id="funcion" value="" />
    <input type="hidden" name="generacion" id="generacion" value="<?=$generacionActual; ?>" />
    </form>
        <script language="javascript">
        //
            function sumar(){
                var asistencia_hom = 0;
                var asistencia_muj = 0;
                var asistencia_jov = 0;
                var asistencia_nin = 0;
                
                if(document.getElementById("asistencia_hom").value != ""){
                    var asistencia_hom = document.getElementById("asistencia_hom").value;
                }
                if(document.getElementById("asistencia_muj").value != ""){
                    var asistencia_muj = document.getElementById("asistencia_muj").value;
                }
                if(document.getElementById("asistencia_jov").value != ""){
                    var asistencia_jov = document.getElementById("asistencia_jov").value;
                }
                if(document.getElementById("asistencia_nin").value != ""){
                    var asistencia_nin = document.getElementById("asistencia_nin").value;
                }
                
                <?php
                if($generacionNumero == 0 || $generacionNumero == 77 || $generacionNumero == 8){
                    ?>               
                    var bautizados = 0;
                    var bautizadosPeriodo = 0;
                    var bautizados = parseInt(asistencia_hom) + parseInt(asistencia_muj) + parseInt(asistencia_jov) + parseInt(asistencia_nin) - 1;
                    <?php
                }
                else{
                    ?>
                    var bautizados = 0;
                    var bautizadosPeriodo = 0;
                    //
                    if(document.getElementById("final_bautizadosPeriodo").value != ""){
                        var bautizados = document.getElementById("final_bautizadosPeriodo").value;
                    }
                    if(document.getElementById("final_bautizadosPeriodo").value != ""){
                        var bautizadosPeriodo = document.getElementById("final_bautizadosPeriodo").value;
                    }
                    <?php
                }
                ?>
                var var_suma = parseInt(asistencia_hom) + parseInt(asistencia_muj) + parseInt(asistencia_jov) + parseInt(asistencia_nin);
                document.getElementById("asistencia_total").value = parseInt(var_suma);
                //
                /*document.getElementById("final_asistencia_hom").value = parseInt(asistencia_hom);
                document.getElementById("final_asistencia_muj").value = parseInt(asistencia_muj);
                document.getElementById("final_asistencia_jov").value = parseInt(asistencia_jov);
                document.getElementById("final_asistencia_nin").value = parseInt(asistencia_nin);*/
                
                
                
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
        
    <?php
}else if($preguntarGeneracion == 1){?>
<!-----REGISTRO DE REPORTES------>
<div class="container">
    <div class="row">
        <h3 class="alert alert-info text-center">PROGRAMA DE EVANGELISMO Y DISCIPULADO</h3>
    </div>

    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

    <!--<fieldset>-->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">CAPACITAR Y MULTIPLICAR (C&M)</h3>
                <p>Escoja una de las siguientes opciones</p>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="cont-flex fl-cent">
                <button type="button" onClick="generarForm('INTRA')" name="generacionOtra" class="btn-mar btn btn-danger">INTRAMUROS<br><span class="btn-desc">(Al interior de las prisiones)</span></button>
                <button type="button" onClick="generarForm('EXTRA')" name="generacionEvangelismo" class="btn-mar btn btn-success">EXTRAMUROS<br><span class="btn-desc">(Al exterior de las prisiones)</span></button>
                      
        </div><br><br>
        
        <input type="hidden" name="funcion" id="funcion" value="" />
        <input type="hidden" name="generacion" id="generacion" value="<?=$idVehiculo; ?>" />
    </form>


    <script language="javascript">
    //
        function generarForm(generacion){
            if(generacion == "INTRA"){
                document.getElementById('generacion').value = "INTRA";
            }else if(generacion == "EXTRA"){
                document.getElementById('generacion').value = "EXTRA";
            }
            //Completo el formulario  
          document.getElementById('form1').submit();
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
    <?php
    
}
else if(!isset($_REQUEST["id"])){
    $temp_accionForm = "insertar";
    $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
    //
    if(!isset($_REQUEST["fechaReporte"])){
        $fechaReporte = date("Y-m-d");        
    }else{
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
    }
    //
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
}
else{
    $temp_accionForm = "actualizar";
    //  ID del usuario actual
    $idReporteActual = soloNumeros($_REQUEST["id"]);
    
}


/*
*   SI SE INSERTO REGISTRO SE REDIRIGE
*/
if($idReporteActual > 0){
    //No hacemos nada.
    
}
else if($preguntarGeneracion == 1){
    //No sabemos aún la GENERACIÓN.
}
else if($varExitoREP == 1){
    ?><div class="container">
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
            <h2 class="alert alert-success text-center"><a href="index.php?doc=gestionar-sub-programa-ecc&opc=2&id=<?=$ultimoId; ?>" class="h2">Se ha <?php
            if($idReporteActual == 0){
                echo "creado";
            }
            else{
                echo "actualizado";
            }
            ?> correctamente el registro, para ver el reporte de clic aquí</a>.</h2>
        </div>
    </div>
        
    <script LANGUAGE="JavaScript">
        //alert("Se ha creado correctamente el registro.");
        //window.location.href= "index.php?doc=reportar&opc=2&id=<?=$ultimoId; ?>";
    </script>
    <?php
}
else if($idReporteActual == 0){
    ?><style type="text/css">
          #form1 fieldset:not(:first-of-type) {
            display: none;
          }
      </style>

<div class="container">
    <div class="row">
        <h3 class="alert alert-info text-center"><?php
            if($idReporteActual == 0){
                echo "REPORTE";
            }
            else{
                echo "ACTUALIZACIÓN";
            }
            ?> DE <?=$temp_letrero; ?></h3>
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
    if($errorLogueo == 1)
    {
        ?><div class="row"><h1><font color="red"><u>ATENCION:</u> NO SE CREO EL INFORME<BR /><u>MOTIVO:</u> YA EXISTE UN INFORME CON ESE VEHÍCULO Y FECHA.<br />POR FAVOR VERIFIQUE.</font></h1></div><?php
    }
    //
    //
    if($error_fatal == 1){
        //No hacer nada.
    }
    else{
        ?>
        <!--<div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
        </div>-->
    <div class="container">
    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
    <input name="fechaReporte" type="hidden" id="fechaReporte" value="<?=$fechaReporte; ?>" />
    <!--<fieldset>-->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Información general</h3>
            <h5><?php 
                if($generacionActual == "INTRA"){echo "Intramuros";}
                else if($generacionActual == "EXTRA"){echo "Extramuros";}
                ?></h5>
            <p>A continuación por favor ingrese los datos requeridos</p>
            </div>
            <div class="hr"><hr></div>
        </div> 
        <?php if($generacionActual == "INTRA"){ ?> 
        <div class="form-group">
            <div class="col-sm-1"></div>
            <div class="col-sm-3">
                <strong>Zona:</strong>
                <input name="zona" type="text" id="zona" maxlength="250" value="<?=$_SESSION["usua_zona"]; ?>" class="form-control" readonly required />
            </div>
            <div class="col-sm-3">
                <strong>Regional:</strong>
                <input name="regional" type="text" id="regional" maxlength="250" value="<?=$_SESSION["usua_regional"]; ?>" class="form-control" readonly required />
            </div>
            <div class="col-sm-4">
                <strong>Coordinador de prisión:</strong>
                <input  name="usua_id" id="usua_id" class="form-control" value="<?=$_SESSION["nombre"]; ?>" readonly required />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-1"></div>
            <div class="col-sm-3">
                <strong>Entrenador:</strong>
                <input name="entrenador" type="text" id="entrenador" maxlength="50" value="<?=$entrenador; ?>" class="form-control" required />
            </div>
            <div class="col-sm-3">
                <strong>Siervo Facilitador:</strong>
                <input name="plantador" type="text" id="plantador" maxlength="255" value="<?=$plantador; ?>" class="form-control" required />
            </div>
            <div class="col-sm-4">
                <strong>Cárcel ubicación:</strong>
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
                                if($cliente_servicio1 == $PSN1->f('reub_id'))
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
            <div class="col-sm-3">
                <strong>Patio/Pabellón:</strong></label>
                <input name="pabellon" type="text" id="pabellon" maxlength="255" value="<?=$pabellon; ?>" class="form-control" required />
            </div> 
        </div>

        <?php }else if($generacionActual == "EXTRA"){ ?>
            <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-2">
                    <strong>Zona:</strong>
                    <input name="zona" type="text" id="zona" maxlength="250" value="<?=$_SESSION["usua_zona"]; ?>" class="form-control" readonly required />
                </div>
                <div class="col-sm-3">
                    <strong>Regional:</strong>
                    <input name="regional" type="text" id="regional" maxlength="250" value="<?=$_SESSION["usua_regional"]; ?>" class="form-control" readonly required />
                </div>
                <div class="col-sm-3">
                    <strong>Coordinador de prisión:</strong>
                    <input  name="usua_id" id="usua_id" class="form-control" value="<?=$_SESSION["nombre"]; ?>" readonly required />
                </div>
                <div class="col-sm-2">
                    <strong>Entrenador:</strong>
                    <input name="entrenador" type="text" id="entrenador" maxlength="50" value="<?=$entrenador; ?>" class="form-control" required />
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-3">
                    <strong>Siervo Facilitador:</strong>
                    <input name="plantador" type="text" id="plantador" maxlength="255" value="<?=$plantador; ?>" class="form-control" required />
                </div>
                <div class="col-sm-2">
                    <strong>Departamento</strong>
                    <select name="departamento" id="departamento" style="text-transform: capitalize;" class="form-control">
                        <option value="">Sin especificar</option>
                        <?php
                        /*
                        *   TRAEMOS LOS TIPOS DE IDENTIFICACION (2)
                        */
                        $sql = "SELECT id_departamento,lower(departamento) as departamento ";
                        $sql.=" FROM dane_departamentos ";
                        $sql.=" ORDER BY departamento asc";
                        $PSN1->query($sql);
                        $numero=$PSN1->num_rows();
                        if($numero > 0){
                            while($PSN1->next_record()){
                                ?><option style="text-transform: capitalize;" value="<?=$PSN1->f('id_departamento'); ?>" <?php
                                if($general_departamento == $PSN1->f('id_departamento'))
                                {
                                    ?>selected="selected" <?php
                                }
                                ?> ><?=$PSN1->f('departamento'); ?></option><?php
                            }
                        }
                        ?>
                    </select>
                </div>
                 <div class="col-sm-2">
                    <?php $_SESSION['muni'] = $general_municipio; ?>
                    <div id="municipio"></div>
                </div>
                <div class="col-sm-3">
                    <strong>Dirección</strong><input name="direccion" type="text" id="direccion" value="<?=$direccion; ?>" class="form-control" />
                </div>
            </div>
        <?php } ?>
        
        <div class="form-group">
            <div class="col-sm-1"></div>
            <div class="col-sm-5">
                <strong>Grupo madre:</strong>
                <input readonly="" name="grupoMadre_txt" type="text" id="grupoMadre_txt" value="Confraternidad carcelaria de Colombia" class="form-control" />
            </div>
            <div class="col-sm-4">
                <strong>Fecha de inicio <?php echo($generacionActual == "INTRA")?"de la confraternidad restaurativa":"de la iglesia extramuros" ?>:</strong>
                <input name="fechaInicio" type="date" id="fechaInicio" maxlength="250" value="<?=$fechaInicio; ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required />
            </div> 
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Generación</h3>
                <h5>de la actividad</h5>
            </div>
            <div class="hr"><hr></div>
        </div> 
        <div class="form-group cont-flex fl-sard">
            <div class="cont-flex">
                <label class="control-label col-sm-1" for="inlineRadio1">1</label>
                <input type="radio" name="generacionNumero" id="inlineRadio1" value="1" checked required class="form-control" />
            </div>
            <div class="cont-flex">
                <label  class="control-label col-sm-1" for="inlineRadio2">2</label>
                <input <?php echo($generacionActual == "INTRA")?"disabled":""; ?> type="radio" name="generacionNumero" id="inlineRadio1" value="2" required class="form-control" />
            </div>
            <div class="cont-flex">
                <label class="control-label col-sm-1" for="inlineRadio3">3</label>
                <input <?php echo($generacionActual == "INTRA")?"disabled":""; ?> type="radio" name="generacionNumero" id="inlineRadio1" value="3" required class="form-control" />
            </div>
            <div class="cont-flex">
                <label class="control-label col-sm-1" for="inlineRadio4">4</label>
                <input <?php echo($generacionActual == "INTRA")?"disabled":""; ?> type="radio" name="generacionNumero" id="inlineRadio1" value="4" required class="form-control" />
            </div>
            <div class="cont-flex">
                <label class="control-label col-sm-1" for="inlineRadio5">5</label>
                <input <?php echo($generacionActual == "INTRA")?"disabled":""; ?> type="radio" name="generacionNumero" id="inlineRadio1" value="5" required class="form-control" />
            </div>
        </div>
        <!--<div class="cont-btn cont-flex fl-sbet">
            <div class="item-btn"></div>
            <div class="item-btn">
                <input type="button" name="next" class="next btn btn-success" value="Siguiente" />
            </div>
        </div>	        
    </fieldset>-->
    <?php
    if($generacionActual == "INTRA" || $generacionActual == "EXTRA"){
        
        ?>
        <!--<fieldset>-->
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Mapeo</h3>
                    <h5>de la iglesia</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-2">
                    <strong>Fecha de mapeo:</strong>
                    <input name="mapeo_fecha" type="date" id="mapeo_fecha" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" readonly />
                </div>
                <div class="col-sm-4">
                    <strong>¿Este grupo está comprometido como iglesia?:</strong>
                    <select name="mapeo_comprometido" id="mapeo_comprometido" class="form-control">
                        <option value="">Sin seleccionar</option>
                        <option value="3" <?php if($mapeo_comprometido == 3){ ?>selected="selected"<?php } ?>>NO está comprometido como iglesia</option>
                        <option value="4" <?php if($mapeo_comprometido == 4){ ?>selected="selected"<?php } ?>>SI está comprometido como iglesia</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <strong>Nombre grupo/iglesia:</strong>
                    <input name="nombreGrupo_txt" type="text" id="nombreGrupo_txt" maxlength="250" value="<?php echo($generacionActual == "INTRA")?"Confraternidad Restaurativa":"Iglesia extramuros"; ?>"readonly class="form-control"  />
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
        </fieldset>-->
        <?php
        $array_campos = array(
            "mapeo_oracion",
            "mapeo_companerismo",
            "mapeo_adoracion",
            "mapeo_biblia",
            "mapeo_evangelizar",
            "mapeo_cena",
            "mapeo_dar",
            "mapeo_bautizar",
            "mapeo_trabajadores"
        );
        $array_campos_valor = array(
            $mapeo_oracion,
            $mapeo_companerismo,
            $mapeo_adoracion,
            $mapeo_biblia,
            $mapeo_evangelizar,
            $mapeo_cena,
            $mapeo_dar,
            $mapeo_bautizar,
            $mapeo_trabajadores
        );            
        $array_campos_txt = array(
            "Orar",
            "Compañerismo",
            "Adorar",
            "Aplicar la biblia",
            "Evangelizar",
            "Cena del Señor",
            "Dar",
            "Bautizar",
            "Entrenar nuevos lideres"
        );
        $total_campos = count($array_campos);
        for($i=0; $i<$total_campos;$i=$i+2){
            $total_valor += $array_campos_valor[$i];
            ?>
            <!--<fieldset>-->
                <div class="cont-tit col-sm-12">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Método de verificación</h3>
                        <h5>Mapeo</h5>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="col-sm-6">
                    <h3><?=$array_campos_txt[$i]; ?></h3>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>1.png" class="img-responsive" />
                                <h5>NO REALIZAN LA TAREA</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" checked type="radio" name="<?=$array_campos[$i]; ?>" value="1" <?php
                            if($array_campos_valor[$i] == 1){
                                ?>checked="checked"<?php
                            }
                            ?> class="form-control"  />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>2.png" class="img-responsive" />
                                <h5>REALIZAN LA TAREA EN COMPAÑIA DEL ENTRENADOR</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i]; ?>" <?php
                            if($array_campos_valor[$i] == 2){
                                ?>checked="checked"<?php
                            }
                            ?> value="2" class="form-control"  />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>3.png" class="img-responsive" />
                                <h5>REALIZAN LA TAREA PERO ESTE MES NO LO HIZO</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i]; ?>" <?php
                            if($array_campos_valor[$i] == 3){
                                ?>checked="checked"<?php
                            }
                            ?> value="3" class="form-control"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>4.png" class="img-responsive" /><h5>REALIZAN LA TAREA AUTONOMAMENTE</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i]; ?>" <?php
                            if($array_campos_valor[$i] == 4){
                                ?>checked="checked"<?php
                            }
                            ?> value="4" class="form-control"  />
                        </div>
                    </div> 
                </div> 
                <!----------------COLUMNA DOS DE MAPEO--------------------------->
                <?php if (($i+1)<$total_campos) {?>
                <div class="col-sm-6">
                    <h3><?=$array_campos_txt[$i+1]; ?></h3>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i+1]; ?>1.png" class="img-responsive" />
                                <h5>NO REALIZAN LA TAREA</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" checked type="radio" name="<?=$array_campos[$i+1]; ?>" value="1" <?php
                            if($array_campos_valor[$i+1] == 1){
                                ?>checked="checked"<?php
                            }
                            ?> class="form-control"  />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i+1]; ?>2.png" class="img-responsive" />
                                <h5>REALIZAN LA TAREA EN COMPAÑIA DEL ENTRENADOR</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i+1]; ?>" <?php
                            if($array_campos_valor[$i+1] == 2){
                                ?>checked="checked"<?php
                            }
                            ?> value="2" class="form-control"  />
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i+1]; ?>3.png" class="img-responsive" />
                                <h5>REALIZAN LA TAREA PERO ESTE MES NO LO HIZO</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i+1]; ?>" <?php
                            if($array_campos_valor[$i+1] == 3){
                                ?>checked="checked"<?php
                            }
                            ?> value="3" class="form-control"  />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i+1]; ?>4.png" class="img-responsive" /><h5>REALIZAN LA TAREA AUTONOMAMENTE</h5>
                            </div>
                            <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i+1]; ?>" <?php
                            if($array_campos_valor[$i+1] == 4){
                                ?>checked="checked"<?php
                            }
                            ?> value="4" class="form-control"  />
                        </div>
                    </div> 
                </div> 
            <?php } ?>
                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </div>
                </div>         
            </fieldset>-->            
        <?php } ?>
        
        
        <!--<fieldset>-->
            <div class="cont-tit col-sm-12">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>Bautizos</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <script>
                        $(function(){
                            var tot = 0;
                            var tar = $(".act_bau_fec").val();
                            var nom = $(".act_bau_can").val();
                            if (tar == "" || nom == "") {
                                $("#adicionarAdd").prop( "disabled", true );
                            }else{
                                $("#adicionarAdd").prop( "disabled", false );
                            }
                            $(".act_bau_fec").change(function(){
                                var tar3 = $(".act_bau_can").val();
                                var nom3 = $(".act_bau_fec").val();
                                if (tar3 != "" && nom3 !="") {
                                    if (tot < 1) {
                                        tot = tot + 1;
                                    }
                                    $("#adicionarAdd").prop( "disabled", false );
                                }else if (tar3 == "" && nom3 =="") {
                                    if (tot == 1) {
                                        tot = tot - 1;
                                        $(".act_bau_fec").prop('required',false);
                                        $(".act_bau_can").prop('required',false);
                                    }
                                }else{
                                    $("#adicionarAdd").prop( "disabled", true );
                                }
                            });
                            $(".act_bau_can").change(function(){
                                var nom2 = $(".act_bau_fec").val();
                                var tar2 = $(".act_bau_can").val();
                                if (nom2 != ""&& tar2 != "") {
                                    if (tot < 1) {
                                        tot = tot + 1;
                                    }
                                    $("#adicionarAdd").prop( "disabled", false );
                                }else if (tar2 == "" && nom2 =="") {
                                    if (tot == 1) {
                                        tot = tot - 1;
                                        $(".act_bau_fec").prop('required',false);
                                        $(".act_bau_can").prop('required',false);
                                    }
                                }else{
                                    $("#adicionarAdd").prop( "disabled", true );
                                }
                                var total = 0;
                                $('.subtotal').each(function(){
                                     total = total + Number($(this).val());
                                });
                                $('#total').val(total);
                                sumar();
                            });
                            $("#adicionarAdd").on('click',function(){
                                $("#tablaAdd tbody tr:eq(0)").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");
                                var tar2 = $(".act_bau_fec").val();
                                var nom2 = $(".act_bau_can").val();
                                if (tar2!="" && nom2!="") {
                                    tot = tot + 1;
                                }
                                $(".act_bau_fec").prop('required',true);
                                $(".act_bau_can").prop('required',true);
                                var total = 0;
                                $('.subtotal').each(function(){
                                     total = total + Number($(this).val());
                                });
                                $('#total').val(total);
                            });
                            $(document).on("click",".eliminarAdd",function(){
                                var parent = $(this).parents().get(0);
                                tot = tot - 1;
                                $(parent).remove();
                            });

                            $(document).on("click","#archivo1_sig",function(){
                                var total = 0;
                                $('.subtotal').each(function(){
                                     total = total + Number($(this).val());
                                });

                                $('#total').val(total);
                            });
                            
                        });
                        
                    </script>
                    
                    
                    <table id="tablaAdd">
                        <tr class="fila-fijaAdd">
                            <td class="col-sm-6">
                                <strong>Foto:</strong>
                                <input multiple name="act_bau_img[]" type="file" id="act_bau_img" class="form-control" />
                            </td>
                            <td class="col-sm-3">
                                <strong>Fecha:</strong>
                                <input name="act_bau_fec[]" type="date" id="act_bau_fec" class="act_bau_fec form-control" />
                            </td>
                            <td class="col-sm-3">
                                <strong>Cantidad bautizados:</strong>
                                <input name="act_bau_can[]" type="number" id="act_bau_can" min="0" class="act_bau_can subtotal form-control" />
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
                    <div class="col-sm-5">
                        <input type="hidden" name="total" id="total">
                    </div>
                    <div class="col-sm-2">
                        <center>
                            <button id="adicionarAdd" class="btn btn-success" type="button" class="boton"><i class="fas fa-plus"></i>  Adicionar</button>
                        </center>
                    </div>
                    <div class="col-sm-5"></div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            
            
            
            <!--  ----------  NUEVO FORM POR JK -->
            
            
             <div class="cont-tit col-sm-12">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>Graduados</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    
                    <script>
                     $(function(){
                            var tot = 0;
                            var tar = $(".act_bau_fecG").val();
                            var nom = $(".act_bau_canG").val();
                            if (tar == "" || nom == "") {
                                $("#adicionarAddG").prop( "disabled", true );
                            }else{
                                $("#adicionarAddG").prop( "disabled", false );
                            }
                            $(".act_bau_fecG").change(function(){
                                var tar3 = $(".act_bau_canG").val();
                                var nom3 = $(".act_bau_fecG").val();
                                if (tar3 != "" && nom3 !="") {
                                    if (tot < 1) {
                                        tot = tot + 1;
                                    }
                                    $("#adicionarAddG").prop( "disabled", false );
                                }else if (tar3 == "" && nom3 =="") {
                                    if (tot == 1) {
                                        tot = tot - 1;
                                        $(".act_bau_fecG").prop('required',false);
                                        $(".act_bau_canG").prop('required',false);
                                    }
                                }else{
                                    $("#adicionarAddG").prop( "disabled", true );
                                }
                            });
                            $(".act_bau_canG").change(function(){
                                var nom2 = $(".act_bau_fecG").val();
                                var tar2 = $(".act_bau_canG").val();
                                if (nom2 != ""&& tar2 != "") {
                                    if (tot < 1) {
                                        tot = tot + 1;
                                    }
                                    $("#adicionarAddG").prop( "disabled", false );
                                }else if (tar2 == "" && nom2 =="") {
                                    if (tot == 1) {
                                        tot = tot - 1;
                                        $(".act_bau_fecG").prop('required',false);
                                        $(".act_bau_canG").prop('required',false);
                                    }
                                }else{
                                    $("#adicionarAddG").prop( "disabled", true );
                                }
                                var total = 0;
                                $('.subtotalG').each(function(){
                                     total = total + Number($(this).val());
                                });
                                $('#totalG').val(total);
                                sumar();
                            });
                            $("#adicionarAddG").on('click',function(){
                                $("#tablaAddG tbody tr:eq(0)").clone().removeClass('fila-fijaAddG').appendTo("#tablaAddG");
                                var tar2 = $(".act_bau_fecG").val();
                                var nom2 = $(".act_bau_canG").val();
                                if (tar2!="" && nom2!="") {
                                    tot = tot + 1;
                                }
                                $(".act_bau_fecG").prop('required',true);
                                $(".act_bau_canG").prop('required',true);
                                var total = 0;
                                $('.subtotalG').each(function(){
                                     total = total + Number($(this).val());
                                });
                                $('#totalG').val(total);
                            });
                            $(document).on("click",".eliminarAddG",function(){
                                var parent = $(this).parents().get(0);
                                tot = tot - 1;
                                $(parent).remove();
                            });

                            $(document).on("click","#archivo1_sigG",function(){
                                var total = 0;
                                $('.subtotalG').each(function(){
                                     total = total + Number($(this).val());
                                });

                                $('#totalG').val(total);
                            });
                            
                        });
                    </script>
            
            <table id="tablaAddG">
                        <tr class="fila-fijaAddG">
                            <td class="col-sm-6">
                                <strong>Foto:</strong>
                                <input multiple name="act_bau_imgG[]" type="file" id="act_bau_imgG" class="form-control" />
                            </td>
                            <td class="col-sm-3">
                                <strong>Fecha graduados:</strong>
                                <input name="act_bau_fecG[]" type="date" id="act_bau_fecG" class="act_bau_fecG form-control" />
                            </td>
                            <td class="col-sm-3">
                                <strong style="white-space: nowrap;">Cantidad graduados:</strong>
                                <input name="act_bau_canG[]" type="number" id="act_bau_canG" min="0" class="act_bau_canG subtotalG form-control" />
                            </td>
                            <td class="col-sm-4">
                                <strong style="white-space: nowrap;">Curso de graduación:</strong>
                                <select name="act_bau_curG[]" id="act_bau_curG" class="act_bau_curG form-control">
                                    <?php
                                        $sql = "SELECT * ";
                                        $sql.=" FROM categorias AS C";
                                        $sql.=" WHERE C.idSec = 88 ";
                                        $sql.=" ORDER BY C.descripcion ASC";
                                        $PSN1->query($sql);
                                        $numero_cat=$PSN1->num_rows();
                                        if($numero_cat > 0){
                                            while($PSN1->next_record()){
                                                ?><option value="<?=$PSN1->f('id'); ?>">
                                                    <?=$PSN1->f('descripcion'); ?></option>
                                                <?php 
                                            }
                                        }
                                        ?>
                                </select>
                            </td>
                            <td class="eliminarAddG"><br><button type="button" class="btn btn-cir-uno usua-col"><i class="fa fa-times"></i></button></td>
                        </tr>
                    </table>
                </div>
                <div class="col-sm-2"></div>
            </div>
            <div class="form-group">
                <div class="col-sm-2"></div>
                <div class="col-sm-8">
                    <div class="col-sm-5">
                        <input type="hidden" name="totalG" id="totalG">
                    </div>
                    <div class="col-sm-2">
                        <center>
                            <button id="adicionarAddG" class="btn btn-success" type="button" class="boton"><i class="fas fa-plus"></i>  Adicionar</button>
                        </center>
                    </div>
                    <div class="col-sm-5"></div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            
            <!--  FIN NUEVO FORM POR JK -->
         
            
            
            <!--<div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                </div>
                <div class="item-btn">
                    <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                </div>
            </div>
        </fieldset>
        <fieldset>-->
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>FOTO DE LA CONFRATERNIDAD RESTAURATIVA</h5>
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
            <!--<div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                </div>
                <div class="item-btn">
                    <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                </div>
            </div>
        </fieldset>
            <fieldset>-->
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>Testimonio</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <strong>Testimonio:</strong>
                    <input name="archivo2" type="file" id="archivo2" required class="form-control" accept=".docx,.doc,.odt,.pdf" />
                    <div id="archivo2-nombre" class="file-name-display" style="margin-top: 5px; color: #666; font-size: 14px;"></div>
                </div>
                <div class="col-sm-3"></div>
            </div>
            <!--<div class="form-group">
                <div class="col-sm-3">
                    <center>
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </center>
                    
                </div>
                <div class="col-sm-6"></div>
                <div class="col-sm-3">
                    <center>
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </center>
                </div>
            </div>
        </fieldset>-->
    <?php }else{
        ?><!--<fieldset>-->
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>Fotográfias</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-4">
                    <strong>Foto 1:</strong>
                    <input name="archivo1" type="file" id="archivo1" class="form-control" />
                </div>
                <div class="col-sm-4">
                    <strong>Foto 2:</strong>
                    <input name="archivo2" type="file" id="archivo2" class="form-control" />
                </div>
                <div class="col-sm-4">
                    <strong>Foto 3:</strong>
                    <input name="archivo3" type="file" id="archivo3" class="form-control" />
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
        </fieldset>--><?php
    }
        
        
        
$campos = array();

if($generacionActual == "CERO"){
    $campos[] = array("ASISTENCIA: HOMBRES", "asistencia_hom");
    $campos[] = array("ASISTENCIA: MUJERES", "asistencia_muj");
    $campos[] = array("ASISTENCIA: JÓVENES", "asistencia_jov");
    $campos[] = array("ASISTENCIA: NIÑOS", "asistencia_nin");
}else if($generacionActual == "EVAN"){
    $campos[] = array("ALCANZADOS: HOMBRES", "asistencia_hom");
    $campos[] = array("ALCANZADOS: MUJERES", "asistencia_muj");
    $campos[] = array("ALCANZADOS: JÓVENES", "asistencia_jov");
    $campos[] = array("ALCANZADOS: NIÑOS", "asistencia_nin");
}else if($generacionActual == "GCEL"){
    $campos[] = array("ASISTENCIA: HOMBRES", "asistencia_hom");
    $campos[] = array("ASISTENCIA: MUJERES", "asistencia_muj");
    $campos[] = array("ASISTENCIA: JÓVENES", "asistencia_jov");
    $campos[] = array("ASISTENCIA: NIÑOS", "asistencia_nin");
}else{
    $campos[] = array("ASISTENCIA: HOMBRES", "asistencia_hom");
    $campos[] = array("ASISTENCIA: MUJERES", "asistencia_muj");
    $campos[] = array("ASISTENCIA: JÓVENES", "asistencia_jov");
    $campos[] = array("ASISTENCIA: NIÑOS", "asistencia_nin");
    
    $campos[] = array("DECISIONES PARA CRISTO", "desiciones");
    //$campos[] = array("BAUTIZADOS ESTE PERIODO", "bautizadosPeriodo");   
}
?>
    <!--<fieldset>-->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Método de verificación</h3>
                <h5>Asistencia</h5>
                <p>A continuación por favor ingrese los datos requeridos</p>
            </div>
            <div class="hr"><hr></div>
        </div>       
        <div class="form-group">
            <div class="col-sm-1"></div>
            <div class="col-sm-2">
                <strong>Hombres:</strong>
                <input name="asistencia_hom" type="number" id="asistencia_hom" min="0" max="9999" value="<?=$_REQUEST['asistencia_hom']; ?>" class="form-control" onChange="sumar()" />
            </div>
            <div class="col-sm-2">
                <strong>Mujeres:</strong>
                <input name="asistencia_muj" type="number" id="asistencia_muj" min="0" max="9999" value="<?=$_REQUEST['asistencia_muj']; ?>" class="form-control" onChange="sumar()" />
            </div>
            <div class="col-sm-2">
                <strong>Jóvenes:</strong>
                <input name="asistencia_jov" type="number" id="asistencia_jov" min="0" max="9999" value="<?=$_REQUEST['asistencia_jov']; ?>" class="form-control" onChange="sumar()" />
            </div>
            <div class="col-sm-2">
                <strong>Niños:</strong>
                <input name="asistencia_nin" type="number" id="asistencia_nin" min="0" max="9999" value="<?=$_REQUEST['asistencia_nin']; ?>" class="form-control" onChange="sumar()" />
            </div>
            <div class="col-sm-2">
                <strong><?php if($generacionNumero == 77 && $generacionNumero == 8){ echo "Alcanzados"; }else{ echo "Asistencia"; } ?> total:</strong>
                <input name="asistencia_total" type="number" id="asistencia_total" value="<?=$asistencia_total; ?>" readonly class="form-control"  />
            </div>
            <div class="col-sm-1"></div>
        </div>
        <!--<div class="cont-btn cont-flex fl-sbet">
            <div class="item-btn">
                <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
            </div>
            <div class="item-btn">
                <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
            </div>
        </div>
    </fieldset>
    <fieldset>-->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Método de verificación</h3>
                <h5>datos adicionales</h5>
                <p>A continuación por favor ingrese los datos requeridos</p>
            </div>
            <div class="hr"><hr></div>
        </div>       
        <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-4">
                <strong>Numero de discípulos LPP:</strong>
                <input name="rep_ndis" type="number" id="rep_ndis"  maxlength="255" onChange="sumar()" min="0" class="form-control" />
            </div>
            <div class="col-sm-4">
                <strong>Decisiones para cristo:</strong>
                <input name="desiciones" type="number" id="desiciones" min="0" maxlength="255" onChange="sumar()"  value="<?=$_REQUEST['desiciones']; ?>" class="form-control" />
            </div>
            <div class="col-sm-2"></div>
        </div>

        <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-5">
                    <strong>Número de familias de personas privadas de la libertad atendidas</strong>
                    <input min="0" max="9999" value="<?php echo $number_person_without_freedom; ?>" required type="number" name="number_person_without_freedom" id="number_person_without_freedom" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control" />
                </div>
                <div class="col-sm-5">
                    <strong>Número de familias de pospenados atendidas</strong>
                    <input min="0" value="<?php echo $number_person_post_penalties; ?>" required type="number" name="number_person_post_penalties" id="number_person_post_penalties" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control" />
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
    </fieldset>-->
<?php 
if ($generacionActual == "GCEL") {?>
    <!--<fieldset>-->
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Otros datos del proceso</h3>
                <h5>Comentarios</h5>
                <p>Ingrese comentarios sobre la actividad realizada</p>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <textarea name="comentario" id="comentario" style="width: 100%;"></textarea>
            </div>
            <div class="col-sm-3"></div>
        </div>
        <!--<div class="cont-btn cont-flex fl-sbet">
            <div class="item-btn">
                <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
            </div>
            <div class="item-btn">
                <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
            </div>
        </div>
    </fieldset>-->
<?php }?>

    <!--<fieldset>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center"><?php if($generacionActual == "EVAN"){ echo "ALCANZADOS"; }else{ ?>ASISTENCIA<?php } ?></h3>
                <h5><?php 
                    if($generacionActual == "EVAN"){echo "evangelismo";}
                    else if($generacionActual == "CERO"){echo "generación 0";}
                    else if($generacionActual == "OTRA"){echo "generación 1 al 5";}
                    else if($generacionActual == "GCEL"){echo "gran celebración";}
                    ?></h5>
                <p>A continuación se muestra un resumen del evento</p>
            </div>
            <div class="hr"><hr></div>
        </div> 
        <div class="form-group">
            <div class="col-sm-4"></div>
            <label class="control-label col-sm-1" for="final_asistencia_hom">
                <strong>Hombres:</strong>
            </label>
            <div class="col-sm-1">
                <input name="final_asistencia_hom" type="number" id="final_asistencia_hom" value="0" class="form-control" readonly />
            </div>
            
            <label class="control-label col-sm-1" for="final_asistencia_muj">
                <strong>Mujeres:</strong>
            </label>
            <div class="col-sm-1">
                <input name="final_asistencia_muj" type="number" id="final_asistencia_muj" value="0" class="form-control" readonly />
            </div>
            <div class="col-sm-4"></div>
        </div>
        <div class="form-group">
            <div class="col-sm-4"></div>
            <label class="control-label col-sm-1" for="final_asistencia_jov">
                <strong>Jóvenes:</strong>
            </label>
            <div class="col-sm-1">
                <input name="final_asistencia_jov" type="number" id="final_asistencia_jov" value="0" class="form-control" readonly />
            </div>

            <label class="control-label col-sm-1" for="final_asistencia_nin">
                <strong>Niños:</strong>
            </label>
            <div class="col-sm-1">
                <input name="final_asistencia_nin" type="number" id="final_asistencia_nin" value="0" class="form-control" readonly />
            </div>
            <div class="col-sm-4"></div>
        </div>
        <div class="form-group">
            <div class="col-sm-4"></div>
            <label class="control-label col-sm-2" for="final_asistencia_total"><strong><?php if($generacionActual == "EVAN"){ echo "Alcanzados"; }else{ ?>Asistencia<?php } ?> total:</strong></label>
            <div class="col-sm-1"><input name="final_asistencia_total" type="number" id="final_asistencia_total" value="0" class="form-control" readonly /></div>
            <div class="col-sm-5"></div>
        </div>-->
        <?php
        if($generacionActual == "CERO" || $generacionActual == "EVAN"){
            ?>
                <input name="final_bautizados" type="hidden" id="final_bautizados" value="0" class="form-control" readonly />
                 <input name="final_graduados" type="hidden" id="final_graduados" value="0" class="form-control" readonly />
                 
                <input name="final_discipulado" type="hidden" id="final_discipulado" value="0" class="form-control" readonly />
                <input name="final_desiciones" type="hidden" id="final_desiciones" value="0" class="form-control" readonly />
                <input name="final_preparandose" type="hidden" id="final_preparandose" value="0" class="form-control" readonly />
                <input name="final_bautizadosPeriodo" type="hidden" id="final_bautizadosPeriodo" value="0" class="form-control" readonly />
                <input name="final_graduadosPeriodo" type="hidden" id="final_graduadosPeriodo" value="0" class="form-control" readonly />
            <?php
        }
        else{
            ?>
            <div class="cont-tit col-sm-12" >
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">OTROS DATOS DEL PROCESO</h3>
                    <h5>COMENTARIOS</h5>
                    <p>A continuación se muestra otros datos</p>
                </div>
                <div class="hr"><hr></div>
            </div>  
            <?php if ($generacionActual == "GCEL") { ?>
                <div class="form-group">
                    <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <textarea style="width: 100%;" name="final_comentarios" id="final_comentarios" readonly></textarea>
                        </div>
                    <div class="col-sm-3"></div>
                </div>
            <?php 
            }else{ ?>
            
            <!-- Campos ReadOnly abajo  -->
            
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>Miembros Bautizados:</strong>
                        <input name="final_bautizados" type="number" id="final_bautizados" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-2">
                        <strong>En discipulado:</strong>
                        <input name="final_discipulado" type="number" id="final_discipulado" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-2">
                        <strong>Decisiones para Cristo:</strong>
                        <input name="final_desiciones" type="number" id="final_desiciones" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-2">
                        <strong>Preparándose para bautismo:</strong>
                        <input name="final_preparandose" type="number" id="final_preparandose" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-2">
                        <strong>Bautizados este período:</strong>
                        <input name="final_bautizadosPeriodo" type="number" id="final_bautizadosPeriodo" value="0" class="form-control" readonly />
                    </div>
                    
                    <div class="col-sm-2">
                        <strong>Graduados este período:</strong>
                        <input name="final_graduadosPeriodo" type="number" id="final_graduadosPeriodo" value="0" class="form-control" readonly />
                    </div>
                    
                    <div class="col-sm-2">
                        <strong>Numero de discípulos LPP:</strong>
                        <input name="final_rep_ndis" type="number" id="final_rep_ndis" value="0" min="0" class="form-control" readonly/>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                <?php
            }
        }
        ?>
            <div class="cont-btn cont-flex fl-cent">
                <!--<div class="item-btn">
                    <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                </div>-->
                <div class="item-btn">
                    <input type="submit" name="button" value="Guardar" class="btn btn-success">
                </div>
            </div>
    <!--</fieldset>-->
        
        
    <input type="submit" name="button-hidden" id="button-hidden" style="display:none">
    <input type="hidden" name="funcion" id="funcion" value="" />
    <input type="hidden" name="generacion" id="generacion" value="<?=$generacionActual; ?>" />
</form>


    <script language="javascript">
        
        var current = 1,current_step,next_step,steps;
        //
        function generarForm(){
            sumar();
            //Completo el formulario  
            if(true){
                <?php
                
                if($generacionActual == "INTRA" || $generacionActual == "EXTRA"){
                    ?>
                    var checks_total_seleccionados;
                    checks_total_seleccionados = 0;
                    <?php
                    for($i=0; $i<$total_campos;$i++){
                        ?>
                        //
                        var radios<?=$i; ?> = document.getElementsByName("<?=$array_campos[$i]; ?>");
                        for(var i = 0, len = radios<?=$i; ?>.length; i < len; i++) {
                              if (radios<?=$i; ?>[i].checked) {
                                  checks_total_seleccionados++;
                              }
                         }
                         <?php
                    }
                    ?>

                    if(parseInt(checks_total_seleccionados) < 8){
                        alert("Debe llenar todos el diagnostico del mapeo por cada uno de los 9 items, oración, dar, etc.");
                        return false;
                    }
                    
                    if(parseInt(document.getElementById("asistencia_total").value) < 3){
                        alert("La asistencia total no puede ser menor a 3 personas");
                        return false;
                    }
                    
                    var e = document.getElementById("mapeo_comprometido");
                    var value = e.options[e.selectedIndex].value;
                    

                    if(document.getElementById("mapeo_fecha").value != "" && value != "" && document.getElementById("nombreGrupo_txt").value != ""){
                        if(confirm("Esta accion guardara los cambios en el sistema, ¿esta seguro que desea continuar?"))
                        {
                            $(':input[type="submit"]').prop('disabled', true);
                            document.getElementById('funcion').value = "<?=$temp_accionForm; ?>";
                        }else{
                            return false;
                        }
                        return true;
                    }
                    else{
                        alert("Por favor verifique la información, debe llenar todo el mapeo.");
                        return false;
                    }
                    <?php
                }
                else{
                    ?>
                    if(confirm("Esta accion guardara los cambios en el sistema, ¿esta seguro que desea continuar?"))
                    {
                        $(':input[type="submit"]').prop('disabled', true);
                        document.getElementById('funcion').value = "<?=$temp_accionForm; ?>";
                    }else{
                        return false;
                    }
                    return true;
                    <?php
                }
                ?>
            }else{
                return false;
            }
        }
        
        //
        function sumar(){
            //alert("ESta sumando");
            var asistencia_hom = 0;
            var asistencia_muj = 0;
            var asistencia_jov = 0;
            var asistencia_nin = 0;
            var desiciones = 0;
            var rep_ndis = 0;
            //
            if(document.getElementById("asistencia_hom").value != ""){
                var asistencia_hom = document.getElementById("asistencia_hom").value;
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
            if(document.getElementById("rep_ndis").value != ""){
                var rep_ndis = document.getElementById("rep_ndis").value;
            }
            
            var var_suma = parseInt(asistencia_hom) + parseInt(asistencia_muj) + parseInt(asistencia_jov) + parseInt(asistencia_nin);
            //total = var_suma;
            //alert(var_suma);
            $("#rep_ndis").attr('max', (total-1));
            $("#desiciones").attr('max', var_suma);

            document.getElementById("asistencia_total").value = parseInt(var_suma);
            
            <?php
            if($generacionActual == "CERO" || $generacionActual == "GCEL"){
                ?>               
                alert(<?= $generacionActual; ?>   );
                var bautizados = 0;
                var bautizadosPeriodo = 0;
                var desiciones = 0;

                var bautizados = parseInt(asistencia_hom) + parseInt(asistencia_muj) + parseInt(asistencia_jov) + parseInt(asistencia_nin) - 1;
                
                <?php
                if ($generacionActual == "GCEL") {?>
                    document.getElementById("final_comentarios").value = document.getElementById("comentario").value;
                <?php
                }
            }else{
                    ?>
                if(document.getElementById("desiciones").value != ""){
                    var desiciones = document.getElementById("desiciones").value;
                }

                var bautizados = 0;
                var bautizadosPeriodo = 0;
                if(document.getElementById("total").value != ""){
                    var bautizados = document.getElementById("total").value;
                    var bautizadosPeriodo = document.getElementById("total").value;
                }
                
                
                var graduados = 0;
                var graduadosPeriodo = 0;
                if(document.getElementById("totalG").value != ""){
                    var graduados = document.getElementById("totalG").value;
                    var graduadosPeriodo = document.getElementById("totalG").value;
                }
                <?php
            }
            ?>
            //alert(bautizados);
            document.getElementById("final_rep_ndis").value = parseInt(rep_ndis);
            
            
            document.getElementById("final_bautizados").value = parseInt(bautizados) + 1;
            document.getElementById("final_discipulado").value = parseInt(var_suma) - 1;
            //
            document.getElementById("final_bautizadosPeriodo").value = parseInt(bautizadosPeriodo);
            
            document.getElementById("final_graduadosPeriodo").value = parseInt(graduadosPeriodo);
            
            document.getElementById("final_desiciones").value = parseInt(var_suma) - 1; //Antigua logica
            document.getElementById("final_desiciones").value = parseInt(desiciones);
            document.getElementById("final_preparandose").value = parseInt(var_suma) - 1 - parseInt(bautizadosPeriodo);               
        }
            
        function init(){
            var total = 0;
            document.getElementById('form1').onsubmit = function(){
                    return generarForm();
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
        $("eliminarAdd").prop( "disabled", true );
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
<script type="text/javascript">
    // Mostrar nombre del archivo seleccionado para testimonios
    document.addEventListener('DOMContentLoaded', function() {
        // Para todos los inputs de archivo2 (testimonio)
        const fileInputs = document.querySelectorAll('input[name="archivo2"]');
        
        fileInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                const displayDiv = document.getElementById('archivo2-nombre');
                if (displayDiv) {
                    if (this.files && this.files.length > 0) {
                        const fileName = this.files[0].name;
                        const fileSize = (this.files[0].size / 1024).toFixed(1) + ' KB';
                        displayDiv.innerHTML = '<strong>Archivo seleccionado:</strong> ' + fileName + ' (' + fileSize + ')';
                        displayDiv.style.color = '#28a745';
                    } else {
                        displayDiv.innerHTML = '';
                    }
                }
            });
        });
    });
</script>