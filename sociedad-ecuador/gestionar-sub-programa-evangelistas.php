<?php
$PSN1 = new DBbase_Sql;
$PSN2 = new DBbase_Sql;
$PSN = new DBbase_Sql;
$webArchivo = "preoperacional";
$temp_letrero = "EVANGELISTAS";
$usua_id = isset($_SESSION["id"]) ? soloNumeros($_SESSION["id"]) : 0;
$nombre_evangelista = isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : "";
$tipos_usuario_evangelista = array(162, 163);


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

function obtenerTiposUsuarioEvangelistaSql($tiposUsuarioEvangelista) {
    if(!is_array($tiposUsuarioEvangelista)){
        $tiposUsuarioEvangelista = array($tiposUsuarioEvangelista);
    }

    $tiposLimpios = array();
    foreach($tiposUsuarioEvangelista as $tipoUsuarioEvangelista){
        $tipoUsuarioEvangelista = intval($tipoUsuarioEvangelista);
        if($tipoUsuarioEvangelista > 0){
            $tiposLimpios[] = $tipoUsuarioEvangelista;
        }
    }

    return implode(",", $tiposLimpios);
}

function obtenerOpcionesEvangelista($idSeleccionado, $tiposUsuarioEvangelista) {
    $PSNUsuarios = new DBbase_Sql;
    $idSeleccionado = intval($idSeleccionado);
    $tiposUsuarioEvangelistaSql = obtenerTiposUsuarioEvangelistaSql($tiposUsuarioEvangelista);
    if($tiposUsuarioEvangelistaSql == ""){
        return "";
    }

    $sql = "SELECT DISTINCT U.id, U.nombre FROM usuario AS U ";
    $sql .= "WHERE U.id != 2 ";
    $sql .= "AND U.tipo IN (".$tiposUsuarioEvangelistaSql.") ";
    $sql .= "ORDER BY U.nombre ASC";

    $PSNUsuarios->query($sql);

    $opciones = "";
    if($PSNUsuarios->num_rows() > 0){
        while($PSNUsuarios->next_record()){
            $idUsuarioLista = $PSNUsuarios->f("id");
            $nombreUsuarioLista = htmlspecialchars($PSNUsuarios->f("nombre"), ENT_QUOTES, "UTF-8");
            $selected = ((string)$idSeleccionado === (string)$idUsuarioLista) ? ' selected="selected"' : '';
            $opciones .= '<option value="'.$idUsuarioLista.'"'.$selected.'>'.$nombreUsuarioLista.'</option>';
        }
    }

    return $opciones;
}

$tipos_usuario_evangelista_sql = obtenerTiposUsuarioEvangelistaSql($tipos_usuario_evangelista);



/*
*   VERIFICAMOS CON QUE GENERACIÓN NOS ESTAMOS ENFRENTANDO ACTUALMENTE.
*/
$preguntarGeneracion = 0;
if(isset($_REQUEST["generacion"]) && $_REQUEST["generacion"] != ""){
    $generacionActual = eliminarInvalidos($_REQUEST["generacion"]);
}else{
    $generacionActual = "EVA";
}


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
        $usua_id = soloNumeros($_REQUEST["usua_id"]);
        if($usua_id == ""){
            $usua_id = soloNumeros($_SESSION["id"]);
        }
        $sqlUsuario = "SELECT id FROM usuario WHERE id = '".$usua_id."' AND tipo IN (".$tipos_usuario_evangelista_sql.") LIMIT 1";
        $PSN2->query($sqlUsuario);
        if($PSN2->num_rows() == 0){
            $error_datos = 4;
        }

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

        $asistencia_total = soloNumeros($_REQUEST["total"]);
        $bautizados  = soloNumeros($_REQUEST["bautizados"]);
        $discipulado  = soloNumeros($_REQUEST["discipulado"]);
        $desiciones  = soloNumeros($_REQUEST["desiciones"]);
        $preparandose  = soloNumeros($_REQUEST["preparandose"]);

        $nombre_archivo = $_FILES['archivo1']['name'];
        $archivo1 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo2']['name'];
        $archivo2 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo3']['name'];
        $archivo3 = extension_archivo($nombre_archivo);

        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
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
        
        $rep_text1 = eliminarInvalidos($_REQUEST["rep_text1"]);
        $rep_text2 = eliminarInvalidos($_REQUEST["rep_text2"]);
        $rep_text3 = eliminarInvalidos($_REQUEST["rep_text3"]);
        $rep_text4 = eliminarInvalidos($_REQUEST["rep_text4"]);
        $number_person_without_freedom = soloNumeros($_REQUEST["number_person_without_freedom"]);
        $number_person_post_penalties = soloNumeros($_REQUEST["number_person_post_penalties"]);
        //Calculados:
        //$asistencia_total  = soloNumeros($_REQUEST["asistencia_total"]);
        
        
        $rep_tip  = 318;
        if ($_REQUEST["rep_ndis"]!= 0 && $_REQUEST["rep_ndis"]!= null) {
            $rep_ndis  = soloNumeros($_REQUEST["rep_ndis"]);
        }else{
            $rep_ndis  = 0;
        }
        
        
        $iglesias_reconocidas = 0;
        //        

        if($error_datos == 0){
            
            /*
            *   DEBEMOS INSERTAR LA INFORMACION DEL REPORTE SEGUN CORRESPONDA.
            */
            $sql = 'INSERT INTO sat_reportes (
                idUsuario,
                comentario,
                rep_text2,
                rep_text3,
                rep_text4,
                number_person_without_freedom,
                number_person_post_penalties,
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

                asistencia_total,
                discipulado,
                desiciones,
                rep_ndis,
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
                "'.$usua_id.'",
                "'.$rep_text1.'",
                "'.$rep_text2.'", 
                "'.$rep_text3.'", 
                "'.$rep_text4.'",
                "'.$number_person_without_freedom.'", 
                "'.$number_person_post_penalties.'",
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
                
                
                "'.$asistencia_total.'", 
                "'.$discipulado.'", 
                "'.$desiciones.'",
                '.$rep_ndis.', 
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

            if (isset($_REQUEST["car_id"]) && is_array($_REQUEST["car_id"])) {
                $car_id = $_REQUEST["car_id"];

                // Asegurarse de que hay algo que insertar
                if (!empty($car_id)) {
                    $sql = 'INSERT INTO tbl_adjuntos (
                        adj_nom,
                        adj_url,
                        adj_fec,
                        adj_tip, 
                        adj_rep_fk)';
                    $sql .= ' VALUES ';
                    
                    $values = [];
                    foreach ($car_id as $id_carcel) {
                        $id_carcel_limpio = intval($id_carcel);
                        if ($id_carcel_limpio > 0) {
                            $values[] = "('".$id_carcel_limpio."','".$id_carcel_limpio."','".date('Y-m-d')."',4,".$ultimoId.")";
                        }
                    }

                    if (!empty($values)) {
                        $sql .= implode(",", $values);
                        $PSN1->query($sql);
                    }
                }
            } 
            
            $varExitoREP = 1;
        }
    }else if($_POST["funcion"] == "eliminar"){
        $sql = 'DELETE from sat_reportes WHERE id = "'.$idReporteActual.'"';
        $PSN1->query($sql);
    }
    else if($_POST["funcion"] == "actualizar"){
       // die("Actualizar");
        //
        /*
        *   PESTAÑA GENERAL
        */
        $usua_id = soloNumeros($_REQUEST["usua_id"]);
        if($usua_id == ""){
            $usua_id = soloNumeros($_SESSION["id"]);
        }
        $sqlUsuario = "SELECT id FROM usuario WHERE id = '".$usua_id."' AND tipo IN (".$tipos_usuario_evangelista_sql.") LIMIT 1";
        $PSN2->query($sqlUsuario);
        if($PSN2->num_rows() == 0){
            $error_datos = 4;
        }

        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
        $fechaInicio = eliminarInvalidos($_REQUEST["fechaInicio"]);        
        if (isset($_REQUEST['sitioReunion'])) {
            $sitioReunion = soloNumeros($_REQUEST["sitioReunion"]);
        }else{
            $sitioReunion = 0;
        }  
        if (!empty($_REQUEST["inactivo"])) {
            $inactivo = soloNumeros($_REQUEST["inactivo"]);
        }else{
            $inactivo = 0;
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

        $asistencia_total = soloNumeros($_REQUEST["total"]);
        $bautizados  = soloNumeros($_REQUEST["bautizados"]);
        $discipulado  = soloNumeros($_REQUEST["discipulado"]);
        $desiciones  = soloNumeros($_REQUEST["desiciones"]);
        $preparandose  = soloNumeros($_REQUEST["preparandose"]);

        $nombre_archivo = $_FILES['archivo1']['name'];
        $archivo1 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo2']['name'];
        $archivo2 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo3']['name'];
        $archivo3 = extension_archivo($nombre_archivo);

        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
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
        
        $rep_text1 = eliminarInvalidos($_REQUEST["rep_text1"]);
        $rep_text2 = eliminarInvalidos($_REQUEST["rep_text2"]);
        $rep_text3 = eliminarInvalidos($_REQUEST["rep_text3"]);
        $rep_text4 = eliminarInvalidos($_REQUEST["rep_text4"]);
        $number_person_without_freedom = soloNumeros($_REQUEST["number_person_without_freedom"]);
        $number_person_post_penalties = soloNumeros($_REQUEST["number_person_post_penalties"]);

        $nombre_archivo = $_FILES['archivo1']['name'];
        $archivo1 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo2']['name'];
        $archivo2 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo3']['name'];
        $archivo3 = extension_archivo($nombre_archivo);
      
        
        if($error_datos == 0){
        //
        $sql = 'UPDATE  sat_reportes SET 
                    idUsuario = "'.$usua_id.'",
                    inactivo = '.$inactivo.', 
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

                    asistencia_total = "'.$asistencia_total.'", 
                    discipulado = "'.$discipulado.'", 
                    desiciones =  "'.$desiciones.'",
                    rep_ndis =  "'.$rep_ndis.'", 
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
                    mapeo_cuarto = "'.$mapeo_cuarto.'",
                    comentario = "'.$rep_text1.'",
                    rep_text2 = "'.$rep_text2.'",
                    rep_text3 = "'.$rep_text3.'",
                    rep_text4 = "'.$rep_text4.'",
                    number_person_without_freedom = "'.$number_person_without_freedom.'",
                    number_person_post_penalties = "'.$number_person_post_penalties.'"
                    ';
                    

    
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
                //echo $sql;
        $PSN1->query($sql);
        $car_id = $_REQUEST['car_id'];
        $sqlDel = "DELETE FROM tbl_adjuntos WHERE adj_rep_fk = ".$idReporteActual." AND adj_tip = 4 ";
        $PSN1->query($sqlDel);
        //echo "Si hay antiguos a modificar: ".sizeof($act_bau_id);
        //var_dump($act_bau_id);
        for ($i=0; $i < sizeof($car_id); $i++) {
            
            $sqlA = "REPLACE INTO tbl_adjuntos (adj_id,adj_nom,adj_url,adj_fec,adj_can, adj_tip,adj_rep_fk)";
                $sqlA .= "VALUES (0".$car_id[$i].",'".$car_id[$i]."','','".date('Y-m-d')."',NULL,4,".$idReporteActual."); ";
            //echo $sqlA;
            $PSN1->query($sqlA);
        }
        
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
                //
            //}        
        }
        
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
    case 4:
        $texto_error = "El evangelista seleccionado no esta registrado en el sistema.";
        break;
    default:
        break;
}

if($idReporteActual > 0){
    /*
    *   TRAEMOS LOS DATOS PRINCIPALES DEL USUARIO
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

            $plantador = $PSN1->f("plantador");
            $entrenador = $PSN1->f("rep_entr");
            $coordinador = $PSN1->f("coordinador");
            $id_coordinador = $PSN1->f("id_coordinador");
            $usua_id = $id_coordinador;
            $nombre_evangelista = $coordinador;
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
            

            //Calculados:
            $asistencia_total  = $PSN1->f("asistencia_total");
            $discipulado  = $PSN1->f("discipulado");
            $desiciones  = $PSN1->f("desiciones");
            $rep_ndis  = $PSN1->f("rep_ndis");
            
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
            
            $rep_text1 = $PSN1->f("comentario");
            $rep_text2 = $PSN1->f("rep_text2");
            $rep_text3 = $PSN1->f("rep_text3");
            $rep_text4 = $PSN1->f("rep_text4");
            $number_person_without_freedom = $PSN->f("number_person_without_freedom");
            $number_person_post_penalties = $PSN->f("number_person_post_penalties");
            
            //
        }//chequear el registro
    }else{
        ?><div class="row">
            <h3 class="alert alert-info text-center">Registro eliminado</h3>
        </div>
        <div class="form-group">
            <center><input type="button" onClick="window.location.href='index.php?doc=consultar-sub-programa-evangelistas'" name="previous" class="previous btn btn-danger" value="Cerrar" /> <br />
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
                    $sqlU .= "AND SR.rep_tip = 318";
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
                    $sqlU .= "AND SR.rep_tip = 318";
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
                    <a href="index.php?doc=gestionar-sub-programa-evangelistas&id=<?=$antId ?>" name="previous" class="previous btn btn-info">Anterior reporte <?=$antId ?></a>
                    <?php } ?>
                </div>
                <div class="item-btn">
                    <a href="index.php?doc=consultar-sub-programa-evangelistas" name="previous" class="btn btn-warning">Todos los reportes</a>
                </div>
                <div class="item-btn">
                    <?php
                    if ($sigId != 0) {?>
                    <a href="index.php?doc=gestionar-sub-programa-evangelistas&id=<?=$sigId ?>" name="previous" class="previous btn btn-info">Siguiente reporte <?=$sigId ?></a>
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
                <strong>Nombre del evangelista:</strong>
                <input type="text" name="usua_nombre" id="usua_nombre" class="form-control" value="<?=htmlspecialchars($nombre_evangelista, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" required />
                <input type="hidden" name="usua_id" id="usua_id" value="<?=$usua_id; ?>" />
                <select id="usua_selector" class="form-control" size="6" style="margin-top: 8px; display: none;">
                    <?=obtenerOpcionesEvangelista($usua_id, $tipos_usuario_evangelista); ?>
                </select>
                <small>Escriba para filtrar y seleccione un usuario.</small>
            </div>
            <div class="col-sm-2">
                <strong>Fecha del reporte:</strong>
                <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250" value="<?=$fechaReporte; ?>" class="form-control" required readonly  />
            </div>            
            <div class="col-sm-1"></div>
        </div>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">MODIFICAR CÁRCELES </h3>
                <p>QUE SON ATENDIDAS POR USTED EN LA ACTUALIDAD</p>               
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <strong>Cárceles Atendidas en este Reporte:</strong>
                <?php
                // Consulta para obtener SOLAMENTE los nombres de las ubicaciones guardadas en el reporte
                $sql_carceles_guardadas = "SELECT RU.reub_nom 
                                         FROM tbl_adjuntos AS TA
                                         JOIN tbl_regional_ubicacion AS RU ON TA.adj_nom = RU.reub_id
                                         WHERE TA.adj_rep_fk = '".intval($idReporteActual)."' AND TA.adj_tip = 4";
                
                $PSN_carceles = new DBbase_Sql;
                $PSN_carceles->query($sql_carceles_guardadas);

                if ($PSN_carceles->num_rows() > 0) {
                    echo '<ul class="list-group" style="margin-top: 10px;">';
                    while ($PSN_carceles->next_record()) {
                        echo '<li class="list-group-item">' . htmlspecialchars($PSN_carceles->f('reub_nom')) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo '<p class="form-control-static">No se especificaron cárceles en este reporte.</p>';
                }
                ?>
            </div>
            <div class="col-sm-2"></div>
        </div>
       
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3>MODIFICAR INFORMACIÓN DE LA PRISIÓN</h3>
                <h5>A continuación por favor ingrese los datos requeridos</h5>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="form-group">
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <strong>Número de grupos intramuros atendidos:</strong>
                <input name="asistencia_hom" required type="number" id="asistencia_hom" value="<?=$asistencia_hom; ?>" class="form-control" onChange="sumar()"  />
            </div>
            <div class="col-sm-3">
                <strong>Número de grupos extramuros atendidos:</strong>
                <input name="asistencia_muj" type="number" id="asistencia_muj" value="<?=$asistencia_muj; ?>" required class="form-control"  onChange="sumar()" />
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="form-group">
            <div class="col-sm-2"></div>           
            <div class="col-sm-4">
                <strong>Total de creyentes que asistieron a los grupos en el mes:</strong>
                <input name="asistencia_jov" type="number" id="asistencia_jov" value="<?=$asistencia_jov; ?>" required class="form-control" onChange="sumar()"  />
            </div>
            <div class="col-sm-4">
                <strong>Total de discípulos (LPP) que pasan a C&M en el mes:</strong>
                <input name="asistencia_nin" type="number" id="asistencia_nin" value="<?=$asistencia_nin; ?>" required class="form-control" onChange="sumar()"  />
            </div> 
            <div class="col-sm-2"></div>
        </div> 
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">OTROS DATOS</h3>
                    <h5>DEL PROCESO</h5>
                </div>
                <div class="hr"><hr></div>
            </div> 
            <div class="form-group">
                
                <div class="col-sm-3">
                    <strong>Número de bautizos en el mes:</strong>
                    <input name="bautizados" type="number" id="bautizados" value="<?=$bautizados; ?>" required class="form-control"  />
                </div>
                <div class="col-sm-3">
                    <strong>Número de voluntarios internos activos:</strong>
                    <input name="discipulado" type="number" id="discipulado" value="<?=$discipulado; ?>" required class="form-control"  />
                </div>
                <div class="col-sm-3">
                    <strong>Número de voluntarios externos activos:</strong>
                    <input name="desiciones" type="number" id="desiciones" value="<?=$desiciones; ?>" required class="form-control"  />
                </div>
                <div class="col-sm-3">
                    <strong>Número de pospenados que está acompañando:</strong>
                <input name="preparandose" type="number" id="preparandose" value="<?=$preparandose; ?>" required class="form-control"  /></div>
            </div>        

            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">ACTIVIDADES QUE ESTÁN REALIZANDO</h3>
                    <h5>LOS GRUPOS INTRAMUROS Y EXTRAMUROS EN EL MES</h5>
                </div>
                <div class="hr"><hr></div>
            </div> 
            
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
            );?>
            <div class="col-sm-1"></div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[0]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[0]; ?></h5>
                            </div>
                            <label>
                                <input  type="checkbox" name="<?=$array_campos[0]; ?>" value="1" <?php if($array_campos_valor[0] == 1){ ?>checked="checked"<?php } ?> />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[1]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[1]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[1]; ?>" <?php if($array_campos_valor[1] == 1){?> checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[2]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[2]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[2]; ?>" <?php if($array_campos_valor[2] == 1){ ?> checked="checked" <?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                </div> 
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[3]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[3]; ?></h5>
                            </div>
                            <label>
                                <input  type="checkbox" name="<?=$array_campos[3]; ?>" value="1" <?php if($array_campos_valor[3] == 1){ ?>checked="checked"<?php } ?> />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[4]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[4]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?= $array_campos[4]; ?>" <?php if( $array_campos_valor[4] == 1){ ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[5]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[5]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[5]; ?>" <?php if($array_campos_valor[5] == 1){ ?> checked="checked" <?php }?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                </div> 
                <!----------------COLUMNA DOS DE MAPEO--------------------------->
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[6]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[6]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[6]; ?>" value="1" <?php if($array_campos_valor[6] == 1){ ?>checked="checked"<?php } ?> />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[7]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[7]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[7]; ?>" <?php if($array_campos_valor[7] == 1){ ?>checked="checked"<?php } ?> value="1" class="form-control"  />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[8]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[8]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[8]; ?>" <?php if($array_campos_valor[8] == 1){ ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                </div>    
        <div class="col-sm-12">
           <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>testimonio</h5>
                    <!--<p>A continuación por favor ingrese los datos requeridos</p>-->
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-5">
                    <strong>Descripción de un testimonio de Impacto positivo en la vida de un PPL como resultado de su proceso de Evangelismo y Discipulado:</strong>
                    <textarea required minlength="300" name="rep_text1" id="rep_text1" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"><?php echo $rep_text1; ?></textarea>
                </div>
                <div class="col-sm-5">
                    <strong>Describir una experiencia de superación personal de un participante del Programa Pospenado en su Regional:</strong>
                    <textarea name="rep_text2" id="rep_text2" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"><?php echo $rep_text2; ?></textarea>
                </div>
                <div class="col-sm-1"></div>
            </div>
            <div class="form-group">
                <div class="col-sm-1"></div>
                <div class="col-sm-5">
                    <strong>Describir un testimonio de la autoridad Carcelaria acerca del impacto positivo que ha generado la implementación de los programas de la CCC en la vida de los internos:</strong>
                    <textarea name="rep_text3" id="rep_text3" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"><?php echo $rep_text3; ?></textarea>
                </div>
                <div class="col-sm-5">
                    <strong>Describir las observaciones o comentarios sobre los obstáculos y dificultades durante este período en el desarrollo de las actividades:</strong>
                    <textarea name="rep_text4" id="rep_text4" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"><?php echo $rep_text4; ?></textarea>
                </div>
                <div class="col-sm-1"></div>
            </div>

        </div>
        <div class="col-sm-12">
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método DE VERIFICACIÓN</h3>
                    <h5>FOTOGRAFIA</h5>
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
                    <input name="archivo1" type="file" id="archivo1" class="form-control" />
                </div>
                <div class="col-sm-4"></div>
            </div>
            <?php if ($_SESSION['perfil']!="168") {?>
                
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
            </div><br><br>  
            <?php } if ($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte) {?>
            <script type="text/javascript">
                $("input").attr('disabled','disabled');
                $("textarea").attr('disabled','disabled');
                $("select").attr('disabled','disabled');
                $("button").attr('disabled','disabled');
                $(".eliminarAdd").prop('disabled', true);
                $("#btn-check").prop('disabled', false);
            </script>
        <?php } ?>           
            <input type="hidden" name="funcion" id="funcion" value="actualizar" />
            <input type="hidden" name="generacion" id="generacion" value="<?=$generacionActual; ?>" />
        </div>
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
        generarForm('EVA');
        function generarForm(generacion){
            if(generacion == "EVA"){
                document.getElementById('generacion').value = "EVA";
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
if($idReporteActual > 0){
    //No hacemos nada.
}else if($varExitoREP == 1){?>
    <?php
    $urlNuevoFormulario = "index.php?doc=gestionar-sub-programa-evangelistas";
    if($generacionActual != ""){
        $urlNuevoFormulario .= "&generacion=".$generacionActual;
    }
    if($idGrupoMadre != ""){
        $urlNuevoFormulario .= "&idGrupoMadre=".$idGrupoMadre;
    }
    ?>
    <div class="container">
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
            <h2 class="alert alert-success text-center"><a href="index.php?doc=gestionar-sub-programa-evangelistas&opc=2&id=<?=$ultimoId; ?>" class="h2">Se ha <?php
            if($idReporteActual == 0){
                echo "creado";
            }
            else{
                echo "actualizado";
            }
            ?> correctamente el registro, para ver el reporte de clic aquí</a>.</h2>
        </div>
    </div>   
    <?php if(isset($_POST["guardar_modo"]) && $_POST["guardar_modo"] == "nuevo"){ ?>
    <script type="text/javascript">
        window.location.href = "<?=$urlNuevoFormulario; ?>";
    </script>
    <?php } ?>
<?php }else if($idReporteActual == 0){?>
    <style type="text/css">
        #form1 fieldset:not(:first-of-type){
            display: none;
        }
    </style>
<div class="container">
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
        <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
            
            <!-- La fecha de creación se maneja automáticamente en el servidor -->
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Información general</h3>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div> 
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-2">
                        <strong>Fecha del informe:</strong>
                        <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required />
                    </div>
<?php /*                   <div class="col-sm-2">
                        <strong>Zona:</strong>
                        <input name="zona" type="text" id="zona" maxlength="250" value="<?=$_SESSION["usua_zona"]; ?>" class="form-control" readonly required />
                    </div>
                    <div class="col-sm-3">
                        <strong>Regional:</strong>
                        <input name="regional" type="text" id="regional" maxlength="250" value="<?=$_SESSION["usua_regional"]; ?>" class="form-control" readonly required />
                    </div>  */ ?>
                    <div class="col-sm-3">
                        <strong>Nombre del evangelista:</strong>
                        <input type="text" name="usua_nombre" id="usua_nombre" class="form-control" value="<?=htmlspecialchars($nombre_evangelista, ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" required />
                        <input type="hidden" name="usua_id" id="usua_id" value="<?=$usua_id; ?>" />
                        <select id="usua_selector" class="form-control" size="6" style="margin-top: 8px; display: none;">
                            <?=obtenerOpcionesEvangelista($usua_id, $tipos_usuario_evangelista); ?>
                        </select>
                        <small>Escriba para filtrar y seleccione un usuario tipo 162 o 163.</small>
                    </div>
                    
                </div>

                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn"></div>
                    <div class="item-btn">
                        <input type="button" name="next" class="next btn btn-success" id="secc-1" value="Siguiente" />
                    </div>
                </div> -->        
            <!--</fieldset>--></div>
        <!--INFORMACIÓN DE LA PRISION
            <fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Información de la prisión</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-3">
                        <strong>Total población que hay en la prisión:</strong>
                        <input name="asistencia_total" required  type="number" id="asistencia_total" min="0" onchange="sumar()" value="" class="form-control" />
                    </div> 
                    <div class="col-sm-2">
                        <strong>Número de prisioneros invitados:</strong>
                        <input name="asistencia_hom" required type="number" id="asistencia_hom" min="0" onchange="sumar()" value="" class="form-control"  />
                    </div>
                    <div class="col-sm-3">
                        <strong>Número de prisioneros que iniciaron el curso:</strong>
                        <input name="asistencia_muj" required type="number" id="asistencia_muj" min="0" onchange="sumar()" value="" class="form-control" />
                    </div> 
<!--                    <div class="col-sm-2">
                        <strong>Numero de cursos activos de LPP: </strong>
                        <input name="asistencia_jov" type="number" id="asistencia_jov" min="0" onChange="sumar()" value="" readonly class="form-control"  />
                    </div>   -->
                </div>
                <div class="cont-btn cont-flex fl-sbet">
                    <!--<div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig2" class="next btn btn-success" value="Siguiente" />
                    </div>-->
                </div>
            <!--</fieldset>--></div>
        <!---REGISTRO DE GRADUADOS-->
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">REGISTRO DE CARCELES</h3>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8">
                        <script>
                            $(function(){
                                var total = 1;
                                var car = $(".act_carc_id").val();

                                //$("#asistencia_total").prop('required',true);

                                /*if (tar == "" || nom == "") {
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
                                });*/

                                $("#adicionarAdd").on('click',function(){
                                    $("#tablaAdd tbody tr:eq(0)").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");
                                    var vtotal = $("#asistencia_muj").val();
                                    total = total + 1;
                                    /*var tar2 = $(".act_grad_tar").val();
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
                                    $(".act_grad_tar").prop('required',true);*/
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
                                    /*$("#rep_ndis").attr('max', (totalG));
                                    if (total >= vtotal) {
                                        $("#adicionarAdd").prop( "disabled", true );
                                    }else{
                                        $("#adicionarAdd").prop( "disabled", false );
                                    }*/
                                });
                                
                            });
                        </script>
                        <table id="tablaAdd">
                            <tr class="fila-fijaAdd">
                                <td class="col-sm-12">
                                    <strong>Cárcel ubicación:</strong>
                                    <select name="car_id[]" id="rep_carcel" class="form-control">           
                                        <?php
                                        /*
                                        *   TRAEMOS LOS TIPOS DE SERVICIOS QUE PRESTA (25)
                                        */
//                                        if ($_SESSION['empresa_pd'] != "") {
                                            echo '<option value="">Sin especificar</option>';
                                            $sql = "SELECT * ";
                                            $sql.=" FROM tbl_regional_ubicacion";
/*                                            if($_SESSION['empresa_sitio_cor'] != null){
                                                $sql.="LEFT JOIN categorias AS CA ON CA.id = reub_reg_fk
                                            LEFT JOIN categorias AS C ON C.id = CA.idSec 
                                            WHERE CA.idSec = ".$_SESSION['empresa_sitio_cor'];
                                            }else{
                                                if($_SESSION['empresa_pd'] != 0){
                                                    $sql.=" WHERE reub_reg_fk = ".$_SESSION['empresa_pd'];
                                                }
                                            } */
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
//                                        }else{
//                                            echo '<option value="">Sin regional asignada</option>';
//                                        }
                                        ?>
                                    </select>
                                    <?php //echo $sql; ?>
                                    <!--<div id="ubicacion" class="form-group"></div>-->
                                </td>
                                <!--<td class="col-sm-4">
                                    <strong>Tarjeta dactilar / N° identificación:</strong>
                                    <input name="act_grad_tar[]" type="text" id="act_grad_tar" min="0" class="act_grad_tar form-control" />
                                </td>-->
                                <td class="eliminarAdd"><br><button type="button" class="btn btn-cir-uno usua-col"><i class="fa fa-times"></i></button></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2"></div>
                    <!--
                    <div class="col-sm-8">
                        <div class="col-sm-4"><strong>¿Cuántas cárceles son atendidas por usted en la actualidad?:</strong> </div>
                        <div class="col-sm-2">
                            <input type="text" name="total" id="total" class="form-control" value="1" readonly>
                        </div>
                        <div class="col-sm-4"></div>
                        <div class="col-sm-2">
                            <center>
                                <button id="adicionarAdd" class="btn btn-success" type="button" class="boton"><i class="fas fa-plus"></i>  Adicionar</button>
                            </center>
                        </div>
                    </div>
                                        -->
                <!--<div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </div>
                </div>
            </fieldset>--></div>
        <!--TESTIMONIO--->
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
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5">
                        <strong>Número de grupos intramuros atendidos:</strong>
                        <input name="asistencia_hom" type="number" id="asistencia_hom" min="0" value="" class="form-control"  />
                    </div>
                    <div class="col-sm-5">
                        <strong>Número de grupos extramuros atendidos:</strong>
                        <input name="asistencia_muj" type="number" id="asistencia_muj" min="0" value="" class="form-control" />
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
            <!--ACTIVIDADES QUE REALIZAN-->
            
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
        //$total_campos = count($array_campos);
        //for($i=0; $i<$total_campos;$i=$i+2){
            //total_valor += $array_campos_valor[$i];?>
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">ACTIVIDADES QUE ESTÁN REALIZANDO</h3>
                        <h5>LOS GRUPOS INTRAMUROS Y EXTRAMUROS EN EL MES</h5>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="col-sm-1"></div>
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[0]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[0]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[0]; ?>" value="1" <?php if($array_campos_valor[0] == 1){ ?>checked="checked"<?php } ?> />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[1]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[1]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[1]; ?>" <?php if($array_campos_valor[1] == 1){ ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[2]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[2]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[2]; ?>" <?php if($array_campos_valor[2] == 1){ ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                </div> 
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[3]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[3]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[3]; ?>" value="1" <?php if($array_campos_valor[3] == 1){ ?>checked="checked"<?php } ?> />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[4]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[4]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[4]; ?>" <?php if($array_campos_valor[4] == 1){
                                ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[5]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[5]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[5]; ?>" <?php if($array_campos_valor[5] == 1){
                                ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                </div> 
                <!----------------COLUMNA DOS DE MAPEO--------------------------->
                <div class="col-sm-3">
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[6]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[6]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[6]; ?>" value="1" <?php if($array_campos_valor[6] == 1){
                                ?>checked="checked"<?php } ?> />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[7]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[7]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[7]; ?>" <?php if($array_campos_valor[7] == 1){
                                ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-12 cont-flex-2 vl-cent fl-sbet">
                            <div class="cont-flex-2 vl-cent">
                                <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[8]; ?>2.png" class="img-responsive" />
                                <h5><?=$array_campos_txt[8]; ?></h5>
                            </div>
                            <label>
                                <input type="checkbox" name="<?=$array_campos[8]; ?>" <?php if($array_campos_valor[8] == 1){
                                ?>checked="checked"<?php } ?> value="1" />
                                <span class="check"></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="cont-btn cont-flex fl-sbet">
                    <!--<div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </div>-->
                </div>         
            <!--</fieldset>--></div>
            
            <!-- ASISTENCIA -->
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Método de verificación</h3>
                        <h5>asistencia</h5>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-4">
                        <strong>Total de creyentes que asistieron a los grupos en el mes: </strong>
                        <input name="asistencia_jov" type="number" id="asistencia_jov" min="0" value="" class="form-control"  />
                    </div>   
<!--                    <div class="col-sm-3">
                        <strong>Total de discípulos (LPP) que pasan a C&M en el mes: </strong>
                        <input name="asistencia_nin" type="number" id="asistencia_nin" min="0" value="" class="form-control"  />
                    </div> -->
                    <div class="col-sm-3">
                        <strong>Número de bautizos en el mes:</strong>
                        <input name="bautizados"  type="number" id="bautizados" min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-3"> 
                        <strong>Número de voluntarios internos activos:</strong>
                        <input name="discipulado"  type="number" id="discipulado" min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Número de voluntarios externos activos:</strong>
                        <input name="desiciones"  type="number" id="desiciones" min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-3">
                        <strong>Número de pospenados que está acompañando:</strong>
                        <input name="preparandose"  type="number" id="preparandose" min="0" value="" class="form-control" />
                    </div>
                    <div class="col-sm-1"></div>
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
            <!-- TESTIMONIO-->
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Método de verificación</h3>
                        <h5>testimonio</h5>
                        <!--<p>A continuación por favor ingrese los datos requeridos</p>-->
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5">
                        <strong>Descripción de un testimonio de Impacto positivo en la vida de un PPL como resultado de su proceso de Evangelismo y Discipulado:</strong>
                        <textarea name="rep_text1" id="rep_text1" required style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"></textarea>
                    </div>
                    <div class="col-sm-5">
                        <strong>Describir una experiencia de superación personal de un participante del Programa Pospenado en su Regional:</strong>
                        <textarea name="rep_text2" id="rep_text2" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"></textarea>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5">
                        <strong>Describir un testimonio de la autoridad Carcelaria acerca del impacto positivo que ha generado la implementación de los programas de la CCC en la vida de los internos:</strong>
                        <textarea name="rep_text3" id="rep_text3" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"></textarea>
                    </div>
                    <div class="col-sm-5">
                        <strong>Describir las observaciones o comentarios sobre los obstáculos y dificultades durante este período en el desarrollo de las actividades:</strong>
                        <textarea name="rep_text4" id="rep_text4" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control"></textarea>
                    </div>
                    <div class="col-sm-1"></div>
                </div>
<!--                <div class="form-group">
                    <div class="col-sm-1"></div>
                    <div class="col-sm-5">
                    <strong>Número de familias de personas privadas de la libertad atendidas</strong>
                    <input min="0" max="9999" value="<?php echo $number_person_without_freedom; ?>" required type="number" name="number_person_without_freedom" id="number_person_without_freedom" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control" />
                </div>
                <div class="col-sm-5">
                    <strong>Número de familias de pospenados atendidas</strong>
                    <input min="0" max="4" value="<?php echo $number_person_post_penalties; ?>" required type="number" name="number_person_post_penalties" id="number_person_post_penalties" style="width: 100%; max-width: 100%; min-width: 100%;" class="form-control" />
                </div> -->
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
            <!-- FOTOGRAFIA-->
            <!--<fieldset>--><div class="col-sm-12">
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Método de verificación</h3>
                        <h5>fotografía</h5>
                        <!--<p>A continuación por favor ingrese los datos requeridos</p>-->
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <input name="archivo1" type="file" required id="archivo1" class="form-control" />
                    </div>
                    <div class="col-sm-3"></div>
                </div>
                <?php 
/* Retirar la restricción de que no se pueden hacer mas registros a 1 mensual
                    $sql = "SELECT COUNT(id) AS registro FROM sat_reportes ";
                    $sql .= "WHERE rep_tip = 318 AND idUsuario = ".$_SESSION['id']." AND YEAR(fechaReporte) = '".date('Y')."' AND MONTH(fechaReporte) = '".date('m')."';";
                    $resul = 0;
                    $PSN1->query($sql);
                    if($PSN1->next_record()){
                        $resul = $PSN1->f('registro');  
                    }
                ?>
                <?php echo($resul>0)?"<div class='alert alert-danger text-center'><b>Ya existe un reporte de Evangelistas para el mes de ".$array_meses[date('n')]."</b></div>":""; ?> */
?>                
                <div class="cont-btn cont-flex fl-cent">
                    <!--<div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>-->
                    <div class="item-btn">
                        <input type="submit" name="button" id="btn_guardar" <?php echo($resul!=0)?'disabled="disabled"':""; ?>  value="Guardar" class="btn btn-success">
                    </div>
                    <div class="item-btn">
                        <input type="button" name="button_guardar_nuevo" id="btn_guardar_nuevo" <?php echo($resul!=0)?'disabled="disabled"':""; ?> value="Guardar y nuevo" class="btn btn-primary">
                    </div>
                </div>
            <!--</fieldset>--></div>
            <input type="submit" name="button-hidden" id="button-hidden" style="display:none">
            <input type="hidden" name="funcion" id="funcion" value="insertar" />
            <input type="hidden" name="generacion" id="generacion" value="<?=$generacionActual; ?>" />
            <input type="hidden" name="idGrupoMadre" id="idGrupoMadre" value="<?=$idGrupoMadre; ?>" />
            <input type="hidden" name="guardar_modo" id="guardar_modo" value="guardar" />
        </form>
    <script language="javascript">
        var current = 1,current_step,next_step,steps;
        function guardarYNuevo(){
            document.getElementById('guardar_modo').value = "nuevo";
            if(document.getElementById('form1').requestSubmit){
                document.getElementById('form1').requestSubmit();
            }else{
                $('#form1').trigger('submit');
            }
        }
        //
        function generarForm(){
            //Completo el formulario  
            if(true){

                <?php
                
                if($generacionActual == "EVA" ){
                    ?>
                    
                    
                    if(parseInt(document.getElementById("final_asistencia_total").value) < 3){
                        alert("La asistencia total no puede ser menor a 3 personas");
                        return false;
                    }
                    

                    if(parseInt(inputs_null) ==0){
                        if(confirm("Esta accion guardara los cambios en el sistema, ¿esta seguro que desea continuar?"))
                        {
                            $(':input[type="submit"]').prop('disabled', true);
                            document.getElementById('funcion').value = "<?=$temp_accionForm; ?>";
                        }else{
                            document.getElementById('guardar_modo').value = "guardar";
                            return false;
                        }
                        return true;
                    }                    else{
                        alert("Por favor verifique la información, debe llenar todo el mapeo.");
                        return false;
                    }
                    <?php
                }else{
                    ?>
                    var inputs_null;
                    inputs_null = 0;
                    var inp_1 = document.getElementById("asistencia_total").value;
                    if (inp_1=="") {
                        inputs_null++;
                    }
                    var inp_2 = document.getElementById("asistencia_muj").value;
                    if (inp_2=="") {
                        inputs_null++;
                    }
                    var inp_3 = document.getElementById("asistencia_hom").value;
                    if (inp_3=="") {
                        inputs_null++;
                    }
                    var inp_4 = document.getElementById("rep_ndis").value;
                    if (inp_4=="") {
                        inputs_null++;
                    }
                    var inp_5 = document.getElementById("act_grad_nom").value;
                    if (inp_5=="") {
                        inputs_null++;
                    }
                    var inp_6 = document.getElementById("act_grad_tar").value;
                    if (inp_6=="") {
                        inputs_null++;
                    }
                    var inp_7 = document.getElementById("act_vin_nom").value;
                    if (inp_7=="") {
                        inputs_null++;
                    }
                    var inp_8 = document.getElementById("act_vex_tar").value;
                    if (inp_8=="") {
                        inputs_null++;
                    }
                    var inp_9 = document.getElementById("act_vex_nom").value;
                    if (inp_9=="") {
                        inputs_null++;
                    }
                    var inp_10 = document.getElementById("act_vex_tar").value;
                    if (inp_10=="") {
                        inputs_null++;
                    }
                    
                    
                    if(parseInt(inputs_null) > 0){
                        alert("Debe llenar todos los datos requeridos.");
                        return false;
                    }else{
                        if(confirm("Esta accion guardara los cambios en el sistema, ¿esta seguro que desea continuar?")){
                        $(':input[type="submit"]').prop('disabled', true);
                        document.getElementById('funcion').value = "<?=$temp_accionForm; ?>";
                        }else{
                            document.getElementById('guardar_modo').value = "guardar";
                            return false;
                        }
                        return true;
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
            if(document.getElementById('btn_guardar')){
                document.getElementById('btn_guardar').onclick = function(){
                    document.getElementById('guardar_modo').value = "guardar";
                }
            }
            if(document.getElementById('btn_guardar_nuevo')){
                document.getElementById('btn_guardar_nuevo').onclick = function(){
                    guardarYNuevo();
                }
            }
            
            <?php
            if($varExitoREP == 1)
            {
                ?>alert("Se ha colocado correctamente el ACCESO, espere mientras es dirigido.");
                window.location.href = "index.php?doc=admin_usu4&id=<?=$ultimoId;?>";<?php
            }
            ?>
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
<script type="text/javascript">
    function inicializarBuscadorEvangelista(){
        var input = document.getElementById('usua_nombre');
        var hidden = document.getElementById('usua_id');
        var selector = document.getElementById('usua_selector');
        var form = document.getElementById('form1');

        if(!input || !hidden || !selector){
            return;
        }

        var opcionesOriginales = [];
        for(var i = 0; i < selector.options.length; i++){
            opcionesOriginales.push({
                value: selector.options[i].value,
                text: selector.options[i].text
            });
        }

        function normalizarTexto(texto){
            return (texto || '').toLowerCase().trim();
        }

        function mostrarSelector(){
            selector.style.display = 'block';
        }

        function ocultarSelector(){
            selector.style.display = 'none';
        }

        function renderizarOpciones(filtro){
            var filtroNormalizado = normalizarTexto(filtro);
            selector.innerHTML = '';

            var coincidencias = [];
            for(var j = 0; j < opcionesOriginales.length; j++){
                if(filtroNormalizado === '' || normalizarTexto(opcionesOriginales[j].text).indexOf(filtroNormalizado) !== -1){
                    coincidencias.push(opcionesOriginales[j]);
                }
            }

            if(coincidencias.length === 0){
                var opcionVacia = document.createElement('option');
                opcionVacia.value = '';
                opcionVacia.text = 'Sin coincidencias';
                opcionVacia.disabled = true;
                selector.appendChild(opcionVacia);
                mostrarSelector();
                return;
            }

            for(var k = 0; k < coincidencias.length; k++){
                var option = document.createElement('option');
                option.value = coincidencias[k].value;
                option.text = coincidencias[k].text;
                if(coincidencias[k].value === hidden.value){
                    option.selected = true;
                }
                selector.appendChild(option);
            }

            mostrarSelector();
        }

        function resolverCoincidenciaExacta(texto){
            var textoNormalizado = normalizarTexto(texto);
            for(var j = 0; j < opcionesOriginales.length; j++){
                if(normalizarTexto(opcionesOriginales[j].text) === textoNormalizado){
                    hidden.value = opcionesOriginales[j].value;
                    input.value = opcionesOriginales[j].text;
                    return true;
                }
            }

            hidden.value = '';
            return false;
        }

        input.addEventListener('input', function(){
            hidden.value = '';
            renderizarOpciones(this.value);
        });

        input.addEventListener('focus', function(){
            renderizarOpciones(this.value);
        });

        input.addEventListener('click', function(){
            renderizarOpciones(this.value);
        });

        input.addEventListener('blur', function(){
            window.setTimeout(function(){
                resolverCoincidenciaExacta(input.value);
            }, 150);
        });

        selector.addEventListener('change', function(){
            if(this.value === ''){
                hidden.value = '';
                return;
            }

            hidden.value = this.value;
            input.value = this.options[this.selectedIndex].text;
            renderizarOpciones(input.value);
            ocultarSelector();
        });

        selector.addEventListener('click', function(){
            if(this.value === ''){
                return;
            }

            hidden.value = this.value;
            input.value = this.options[this.selectedIndex].text;
            ocultarSelector();
        });

        document.addEventListener('click', function(e){
            if(e.target !== input && e.target !== selector){
                ocultarSelector();
            }
        });

        if(form){
            form.addEventListener('submit', function(e){
                if(hidden.value !== ''){
                    return;
                }

                if(!resolverCoincidenciaExacta(input.value)){
                    e.preventDefault();
                    alert('Debe seleccionar un evangelista registrado de la lista.');
                    input.focus();
                }
            });
        }

        ocultarSelector();
    }

    $(document).ready(function(){
        inicializarBuscadorEvangelista();
        recargaLista();
        $('#rep_carcel').change(function(){
            recargaLista();
        });
        recargaListaDpto();
        $('#departamento').change(function(){
            recargaListaDpto();
        }); 
    })
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
</script>
<?php if ($_SESSION['perfil']=="168" || $fechLimite > $fechaReporte) {?>
    <script type="text/javascript">
        $("input").attr('disabled','disabled');
        $("textarea").attr('disabled','disabled');
        $("select").attr('disabled','disabled');
        $("button").attr('disabled','disabled');
        $("#btn-check").prop('disabled', false);                    
    </script>
<?php } ?>
