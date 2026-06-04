<style type="text/css">

a.reportes {
    background-color: #87BBF5;
    color:#fff;
}

.nav-pills > li.active > a.btnEntrada {
    color: #fff;
    background-color: #449d44;
    border-color: #398439;
}
.nav-pills > li.active > a.btnSalida {
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}
.nav-pills > li.active > a.btnEvangelismo {
    color: #fff;
    background-color: #FFCD00;
    border-color: #d43f3a;
}
.nav-pills > li.active > a.btnPrimeraEntrega {
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
}
.nav-pills > li.active > a.btnOtrasEntregas {
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}
</style>

<?php
$PSN2 = new DBbase_Sql;
$usua_id = isset($_SESSION["id"]) ? soloNumeros($_SESSION["id"]) : 0;
$nombre_evangelista = isset($_SESSION["nombre"]) ? $_SESSION["nombre"] : "";
$tipo_usuario_ecc = 163;

function obtenerOpcionesUsuarioGestionEcc($idSeleccionado, $tipoUsuarioEcc) {
    $PSNUsuarios = new DBbase_Sql;
    $idSeleccionado = intval($idSeleccionado);

    $sql = "SELECT DISTINCT U.id, U.nombre FROM usuario AS U ";
    $sql .= "WHERE U.id != 2 ";
    $sql .= "AND U.tipo = '".$tipoUsuarioEcc."' ";
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

if(isset($_POST["SendOpcion"])){
    $PSN_Save = new DBbase_Sql;
    $PSN_Save01 = new DBbase_Sql;
    $DataSave = 0;

    if($_POST["SendOpcion"]=="1a"){
        if (isset($_POST["EntChkPD"])){
            $Donante1 = 1;        
        }else{
            $Donante1 = 0; 
        }
        if (isset($_POST["EntChkSP"])){
            $Donante2 = 1;        
        }else{
            $Donante2 = 0; 
        }
        if (isset($_POST["EntradaMix"]) && !empty($_POST["EntradaMix"])){
            $EntradaMix = $_POST["EntradaMix"];        
        }else{
            $EntradaMix = 0; 
        }
        if (isset($_POST["EntradaNab"]) && !empty($_POST["EntradaNab"]) ){
            $EntradaNab = $_POST["EntradaNab"];        
        }else{
            $EntradaNab = 0; 
        }
        $sql = ' insert into inventario (Tipo,Donante1,Donante2,TipoSopa1,TipoSopa2,Fecha,Responsable,Facilitador,Pais,Departamento,Municipio, Barrio, TotalBeneficiados, Beneficiario) values(';
        $sql .= "1".",".$Donante1.",".$Donante2.",".$EntradaMix.",".$EntradaNab.",'".$_POST['fechaEntrada']."','".$_POST["ResponsableEnt"]."','',".$_POST['pais'].",".$_POST['departamento'].",".$_POST['ciudad'].",'',"."0,0".")";
    }
    else if($_POST["SendOpcion"]=="2a"){
        if (isset($_POST["salDon1"])){
            $Donante1 = 1;        
        }else{
            $Donante1 = 0; 
        }
        if (isset($_POST["salDon2"])){
            $Donante2 = 1;        
        }else{
            $Donante2 = 0; 
        }
        if (isset($_POST["SalidaMix"]) && !empty($_POST["SalidaMix"])){
            $SalidaMix = $_POST["SalidaMix"];        
        }else{
            $SalidaMix = 0; 
        }
        if (isset($_POST["SalidaNab"]) && !empty($_POST["SalidaNab"]) ){
            $SalidaNab = $_POST["SalidaNab"];        
        }else{
            $SalidaNab = 0; 
        }
        $sql = ' insert into inventario (Tipo,Donante1,Donante2,TipoSopa1,TipoSopa2,Fecha,Responsable,Facilitador,Pais,Departamento,Municipio, Barrio, TotalBeneficiados,Beneficiario) values(';
        $sql .= "2".",".$Donante1.",".$Donante2.",".$SalidaMix.",".$SalidaNab.",'".$_POST['fechaSalida']."','".$_POST["ResSalida"]."','".$_POST["SalFacilitador"]."',".$_POST['salpais'].",".$_POST['saldepartamento'].",".$_POST['salciudad'].",'',"."0,0".")";
    } 
    else if($_POST["SendOpcion"]=="1aa"){
        if (isset($_POST["EvaChkPD"])){
            $Donante1 = 1;        
        }else{
            $Donante1 = 0; 
        }
        if (isset($_POST["EvaChkSP"])){
            $Donante2 = 1;        
        }else{
            $Donante2 = 0; 
        }
        if (isset($_POST["EvaMix"]) && !empty($_POST["EvaMix"])){
            $EvaMix = $_POST["EvaMix"];        
        }else{
            $EvaMix = 0; 
        }
        if (isset($_POST["EvaNab"]) && !empty($_POST["EvaNab"]) ){
            $EvaNab = $_POST["EvaNab"];        
        }else{
            $EvaNab = 0; 
        }
        $sql = ' insert into inventario (Tipo,Donante1,Donante2,TipoSopa1,TipoSopa2,Fecha,Responsable,Facilitador,Pais,Departamento,Municipio, Barrio, TotalBeneficiados, Beneficiario ) values(';
        $sql .= "3".",".$Donante1.",".$Donante2.",".$EvaMix.",".$EvaNab.",'".$_POST['fechaEva']."','".$_POST["ResEva"]."','',".$_POST['evapais'].",".$_POST['evadepartamento'].",".$_POST['evaciudad'].",'".$_POST['barrioEv']."',".$_POST['PerBeneficiadas'].",0)";
    } 
    else if($_POST["SendOpcion"]=="2aa"){
        if (isset($_POST["PEChkPD"])){
            $Donante1 = 1;        
        }else{
            $Donante1 = 0; 
        }
        if (isset($_POST["PEChkSP"])){
            $Donante2 = 1;        
        }else{
            $Donante2 = 0; 
        }
        if (isset($_POST["PEMix"]) && !empty($_POST["PEMix"])){
            $PEMix = $_POST["PEMix"];        
        }else{
            $PEMix = 0; 
        }
        if (isset($_POST["PENab"]) && !empty($_POST["PENab"]) ){
            $PENab = $_POST["PENab"];        
        }else{
            $PENab = 0; 
        }
        $Discapacidades="";
        if (isset($_POST["ChkMovilidad"]) && !empty($_POST["ChkMovilidad"]) ){
            $Discapacidades .= "Movilidad".",";        
        }
        if (isset($_POST["ChkMental"]) && !empty($_POST["ChkMental"]) ){
            $Discapacidades .= "Mental".",";        
        }
        if (isset($_POST["ChkAuditiva"]) && !empty($_POST["ChkAuditiva"]) ){
            $Discapacidades .= "Auditiva".",";        
        }
        if (isset($_POST["ChkVisual"]) && !empty($_POST["ChkVisual"]) ){
            $Discapacidades .= "Visual".",";        
        }
        if (isset($_POST["ChkOtras"]) && !empty($_POST["ChkOtras"]) ){
            $Discapacidades .= "Otras".",";        
        }
        if (isset($_POST["ChkNingunaDis"]) && !empty($_POST["ChkNingunaDis"]) ){
            $Discapacidades .= "Ninguna".",";        
        }
        $Ingresos = 0;
        if ($_POST["OptIngresos"] == "Opt01"){
            $Ingresos= 1;
        }else if ($_POST["OptIngresos"] == "Opt02"){
            $Ingresos= 2;
        }
        else if ($_POST["OptIngresos"] == "Opt03"){
            $Ingresos= 3;
        }
        else if ($_POST["OptIngresos"] == "Opt04"){
            $Ingresos= 4;
        }
        else if ($_POST["OptIngresos"] == "Opt05"){
            $Ingresos= 5;
        }
        $ComidaNoConsumida = "";
        if (isset($_POST["ChkDesayuno"]) && !empty($_POST["ChkDesayuno"]) ){
            $ComidaNoConsumida.= "Desayuno".",";        
        }
        if (isset($_POST["ChkAlmuerzo"]) && !empty($_POST["ChkAlmuerzo"]) ){
            $ComidaNoConsumida .= "Almuerzo".",";        
        }
        if (isset($_POST["ChkCena"]) && !empty($_POST["ChkCena"]) ){
            $ComidaNoConsumida .= "Cena".",";        
        }
        if (isset($_POST["ChkTodas"]) && !empty($_POST["ChkTodas"]) ){
            $ComidaNoConsumida .= "Todas".",";        
        }

        $Situaciones="";
        if (isset($_POST["ChkSuicidio"]) && !empty($_POST["ChkSuicidio"]) ){
            $Situaciones .= "Suicidio".",";        
        }
        if (isset($_POST["ChkViolencia"]) && !empty($_POST["ChkViolencia"]) ){
            $Situaciones .= "Violencia".",";        
        }
        if (isset($_POST["ChkAbuso"]) && !empty($_POST["ChkAbuso"]) ){
            $Situaciones .= "Abuso".",";        
        }
        if (isset($_POST["ChkDesaparicion"]) && !empty($_POST["ChkDesaparicion"]) ){
            $Situaciones .= "Desaparicion".",";        
        }
        if (isset($_POST["ChkDesplazamiento"]) && !empty($_POST["ChkDesplazamiento"]) ){
            $Situaciones .= "Desplazamiento".",";        
        }
        if (isset($_POST["ChkTrafico"]) && !empty($_POST["ChkTrafico"]) ){
            $Situaciones .= "Trafico".",";        
        }
        if (isset($_POST["ChkNingunaSit"]) && !empty($_POST["ChkNingunaSit"]) ){
            $Situaciones .= "Ninguna".",";        
        }
        $Discapacidades = trim($Discapacidades, ',');
        $ComidaNoConsumida  = trim($ComidaNoConsumida , ',');
        $Situaciones = trim($Situaciones, ',');

        $sql01 = 'insert into beneficiarios ( Nombre,telefono,TotalPersonas,TotalNinos,TotalNinosBeneficiados,TotalAdolescentes,TotalAdultos,TotalDiscapacitados,Discapacidad,Ingresos,ComidaNoConsumida, Situaciones) values(';
        $sql01 .="'".$_POST['nombre']."','".$_POST['telefono']."',".$_POST['PersonasCasa'].",".$_POST['NinosCasa'].",".$_POST['SoySatura'].",".$_POST['AdoCasa'].",".$_POST['Adultos'].",".$_POST['Discapacidad'].",'".$Discapacidades."',".$Ingresos.",'".$ComidaNoConsumida."','".$Situaciones."')";
        $PSN_Save01->query($sql01);
        $SetBen = $PSN_Save01->ultimoId();
        
        $sql = ' insert into inventario (Tipo,Donante1,Donante2,TipoSopa1,TipoSopa2,Fecha,Responsable,Facilitador,Pais,Departamento,Municipio, Barrio, TotalBeneficiados, Beneficiario ) values(';
        $sql .= "4".",".$Donante1.",".$Donante2.",".$PEMix.",".$PENab.",'".$_POST['fechaPE']."','".$_POST["ResPE"]."','',".$_POST['pepais'].",".$_POST['pedepartamento'].",".$_POST['peciudad'].",'".$_POST['barrioPE']."',0,".$SetBen.")";
    } 
    else if($_POST["SendOpcion"]=="3aa"){
        if (isset($_POST["OEChkPD"])){
            $Donante1 = 1;        
        }else{
            $Donante1 = 0; 
        }
        if (isset($_POST["OEChkSP"])){
            $Donante2 = 1;        
        }else{
            $Donante2 = 0; 
        }
        if (isset($_POST["OEMix"]) && !empty($_POST["OEMix"])){
            $OEMix = $_POST["OEMix"];        
        }else{
            $OEMix = 0; 
        }
        if (isset($_POST["OENab"]) && !empty($_POST["OENab"]) ){
            $OENab = $_POST["OENab"];        
        }else{
            $OENab = 0; 
        }
        if (isset($_POST["IPGAsiste"]) && !empty($_POST["IPGAsiste"]) ){
            $IPG = $_POST["IPGAsiste"];        
        }else{
            $IPG = ""; 
        }
        if (!is_null($IPG) || strlen($IPG) > 0){
            $sql01  = "update beneficiarios set IPG = '".$IPG."'";
            $sql01 .=" Where IdBeneficiado = ".$_POST['SelBeneficiario'];
           $PSN_Save01->query($sql01); 
        }
        $sql = ' insert into inventario (Tipo,Donante1,Donante2,TipoSopa1,TipoSopa2,Fecha,Responsable,Facilitador,Pais,Departamento,Municipio, Barrio, TotalBeneficiados, Beneficiario ) values(';
        $sql .= "5".",".$Donante1.",".$Donante2.",".$OEMix.",".$OENab.",'".$_POST['fechaOE']."','".$_POST["ResOE"]."','',0,0,0,0,0,".$_POST['SelBeneficiario'].")";
   
    }
    $DataSave = $PSN_Save->query($sql); 
    if ($DataSave>0){
    ?>
    <hr>
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <h3 class="alert alert-success text-center">Datos almacenados con exito.</h3>
            </div>
            <div class="row" >
                <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
                <div class="cont-btn cont-flex fl-sbet"> <div class="item-btn">
                        <input type="submit" value="Volver" class="btn btn-success">
                    </div></div>
                </form>
            </div>
            <br><br>   <hr>
        </div>
    <?php
    }else{
        ?>
    <hr>
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <h3 class="alert alert-danger text-center">Imposible almacenar los datos, por favor intente de nuevo.</h3>
            </div>
            <div class="row" >
                <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
                <div class="cont-btn cont-flex fl-sbet"> <div class="item-btn">
                        <input type="submit" value="Volver" class="btn btn-success">
                    </div></div>
                </form>
            </div>
            <br><br>   <hr>
        </div>
    <?php
    }
}

$PSN1 = new DBbase_Sql;
$PSN = new DBbase_Sql;
$webArchivo = "preoperacional";
$temp_letrero = "REPORTE MENSUAL";

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
        $sqlUsuario = "SELECT id FROM usuario WHERE id = '".$usua_id."' AND tipo = '".$tipo_usuario_ecc."' LIMIT 1";
        $PSN2->query($sqlUsuario);
        if($PSN2->num_rows() == 0){
            $error_datos = 4;
        }

        $comentario = eliminarInvalidos($_REQUEST["final_comentarios"]);
        $plantador = eliminarInvalidos($_REQUEST["plantador"]);
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
        $fechaInicio = eliminarInvalidos($_REQUEST["fechaInicio"]);        
        $sitioReunion = eliminarInvalidos($_REQUEST["sitioReunion"]);
        $grupoMadre_txt = eliminarInvalidos($_REQUEST["grupoMadre_txt"]);
        $nombreGrupo_txt = eliminarInvalidos($_REQUEST["nombreGrupo_txt"]);
        
        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);
        $direccion = eliminarInvalidos($_REQUEST["direccion"]);
        $ciudad = eliminarInvalidos($_REQUEST["ciudad"]);            
        
        $capacitacion_txt = eliminarInvalidos($_REQUEST["capacitacion_txt"]);        
        $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
        $generacionNumero = soloNumeros($_REQUEST["generacionNumero"]);
        

        $asistencia_hom = soloNumeros($_REQUEST["asistencia_hom"]);
        $asistencia_muj = soloNumeros($_REQUEST["asistencia_muj"]);
        $asistencia_jov = soloNumeros($_REQUEST["asistencia_jov"]);
        $asistencia_nin = soloNumeros($_REQUEST["asistencia_nin"]);

        $bautizados = soloNumeros($_REQUEST["final_bautizados"]);        
        $bautizadosPeriodo = soloNumeros($_REQUEST["final_bautizadosPeriodo"]);
        
        $mapeo_anho = soloNumeros($_REQUEST["mapeo_anho"]);
        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
        
        
        $nombre_archivo = $_FILES['archivo1']['name'];
        $archivo1 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo2']['name'];
        $archivo2 = extension_archivo($nombre_archivo);
        
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
        $preparandose  = soloNumeros($_REQUEST["final_preparandose"]);
        $iglesias_reconocidas = 0;
        //        
		
		// "CERO";
        // "EVAN";
        // "GCEL";                
        // "SOPA";   
		// "OTRA";                
		if($generacionActual == "EVAN"){
			$rep_tip = 317;
		}else if($generacionActual == "GCEL"){
			$rep_tip = 327;
		}

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
            *   DEBEMOS INSERTAR LA INFORMACION DEL REPORTE SEGUN CORRESPONDA.
            */
            $sql = 'INSERT INTO sat_reportes (
                idUsuario,
                comentario,
                plantador,
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
                "'.$comentario.'", 
                "'.$plantador.'", 
                "'.$fechaReporte.'", 
                "'.$fechaInicio.'", 
                "'.$sitioReunion.'", 
                "'.$grupoMadre_txt.'", 
                "'.$nombreGrupo_txt.'",                 
                "'.$capacitacion_txt.'", 
                "'.$idGrupoMadre.'", 
                "'.$generacionNumero.'", 
                
                "'.$pabellon.'", 
                "'.$direccion.'", 
                "'.$ciudad.'", 
                

                    "'.$asistencia_hom.'", 
                    "'.$asistencia_muj.'", 
                    "'.$asistencia_jov.'", 
                    "'.$asistencia_nin.'", 
                    
                "'.$bautizados.'", 
                "'.$bautizadosPeriodo.'", 
                
                
                "'.$asistencia_total.'", 
                "'.$discipulado.'", 
                "'.$desiciones.'", 
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
                "'.$rep_tip.'"
                )';
            
            //
            //
            //echo "Insertar sat_reportes: ".$sql;
            $ultimoQuery = $PSN1->query($sql);
            $ultimoId =  $PSN1->ultimoId();
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
            //      
            //if($generacionNumero > 0){
                // Compress Image
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
    }
    else if($_POST["funcion"] == "actualizar"){
       // die("Actualizar");
        //
        $usua_id = soloNumeros($_REQUEST["usua_id"]);
        if($usua_id == ""){
            $usua_id = soloNumeros($_SESSION["id"]);
        }
        $sqlUsuario = "SELECT id FROM usuario WHERE id = '".$usua_id."' AND tipo = '".$tipo_usuario_ecc."' LIMIT 1";
        $PSN2->query($sqlUsuario);
        if($PSN2->num_rows() == 0){
            $error_datos = 4;
        }

        /*
        *   PESTAÑA GENERAL
        */
        $plantador = eliminarInvalidos($_REQUEST["plantador"]);
        $comentario = eliminarInvalidos($_REQUEST["final_comentarios"]);
        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);
        $fechaInicio = eliminarInvalidos($_REQUEST["fechaInicio"]);        
        $sitioReunion = eliminarInvalidos($_REQUEST["sitioReunion"]);
        $grupoMadre_txt = eliminarInvalidos($_REQUEST["grupoMadre_txt"]);
        $nombreGrupo_txt = eliminarInvalidos($_REQUEST["nombreGrupo_txt"]);
        
        
        $inactivo = soloNumeros($_REQUEST["inactivo"]);
        
        
        $capacitacion_txt = eliminarInvalidos($_REQUEST["capacitacion_txt"]);        
        $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);
        $generacionNumero = soloNumeros($_REQUEST["generacionNumero"]);
        
        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);
        $direccion = eliminarInvalidos($_REQUEST["direccion"]);
        $ciudad = eliminarInvalidos($_REQUEST["ciudad"]);

        $asistencia_hom = soloNumeros($_REQUEST["final_asistencia_hom"]);
        $asistencia_muj = soloNumeros($_REQUEST["final_asistencia_muj"]);
        $asistencia_jov = soloNumeros($_REQUEST["final_asistencia_jov"]);
        $asistencia_nin = soloNumeros($_REQUEST["final_asistencia_nin"]);

        $bautizados = soloNumeros($_REQUEST["final_bautizados"]);        
        $bautizadosPeriodo = soloNumeros($_REQUEST["final_bautizadosPeriodo"]);
        

        //Calculados:
        $asistencia_total  = $asistencia_hom+$asistencia_muj+$asistencia_jov+$asistencia_nin;
        $discipulado  = soloNumeros($_REQUEST["final_discipulado"]);
        $desiciones  = soloNumeros($_REQUEST["final_desiciones"]);
        $preparandose  = soloNumeros($_REQUEST["final_preparandose"]);
        $iglesias_reconocidas = 0;
        
        
        $mapeo_anho = soloNumeros($_REQUEST["mapeo_anho"]);
        $mapeo_cuarto = soloNumeros($_REQUEST["mapeo_cuarto"]);
        
        
        $nombre_archivo = $_FILES['archivo1']['name'];
        $archivo1 = extension_archivo($nombre_archivo);
        
        $nombre_archivo = $_FILES['archivo2']['name'];
        $archivo2 = extension_archivo($nombre_archivo);
        
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
        if($error_datos == 0){
        $sql = 'UPDATE  sat_reportes SET 
                    idUsuario = "'.$usua_id.'", 
                    inactivo = "'.$inactivo.'", 
                    comentario = "'.$comentario.'", 
                    plantador = "'.$plantador.'", 
                    fechaInicio = "'.$fechaInicio.'", 
                    sitioReunion = "'.$sitioReunion.'", 
                    grupoMadre_txt = "'.$grupoMadre_txt.'", 
                    nombreGrupo_txt = "'.$nombreGrupo_txt.'",                     
                    capacitacion_txt = "'.$capacitacion_txt.'", 
                    generacionNumero = "'.$generacionNumero.'", 

                    pabellon = "'.$pabellon.'", 
                    direccion = "'.$direccion.'", 
                    ciudad = "'.$ciudad.'", 

                        asistencia_hom = "'.$asistencia_hom.'", 
                        asistencia_muj = "'.$asistencia_muj.'", 
                        asistencia_jov = "'.$asistencia_jov.'", 
                        asistencia_nin =  "'.$asistencia_nin.'", 

                    bautizados =  "'.$bautizados.'", 
                    bautizadosPeriodo = "'.$bautizadosPeriodo.'", 

                    asistencia_total = "'.$asistencia_total.'", 
                    discipulado = "'.$discipulado.'", 
                    desiciones =  "'.$desiciones.'", 
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
        if (isset($_REQUEST['act_bau_id'])) {
            $act_bau_img = $_FILES["act_bau_img"];
            $act_bau_imgAn = $_REQUEST["act_bau_img_an"];
            $act_bau_fec = $_REQUEST['act_bau_fec'];
            $act_bau_can = $_REQUEST['act_bau_can'];
            $act_bau_id = $_REQUEST['act_bau_id'];
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
        $nuevos = $totalReg-sizeof($act_bau_id );
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
        $texto_error = "El usuario seleccionado no esta registrado en el sistema o no corresponde al tipo permitido.";
        break;
    default:
        break;
}

if($idReporteActual > 0){
    /*
    *   TRAEMOS LOS DATOS PRINCIPALES DEL USUARIO
    */
    $sql = "SELECT sat_reportes.*, sat_grupos.nombre, U.id as id_usuario_reporte, U.nombre as nombre_usuario_reporte ";
    $sql.=" FROM sat_reportes LEFT JOIN sat_grupos ON sat_grupos.id = sat_reportes.idGrupoMadre ";
    $sql.=" LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario ";
    $sql.=" WHERE sat_reportes.id = '".$idReporteActual."'";
    $sql.=" GROUP BY sat_reportes.id";
    $PSN1->query($sql);
    if($PSN1->num_rows() > 0)
    {
        if($PSN1->next_record())
        {
            $inactivo = $PSN1->f("inactivo");
            $comentario = $PSN1->f("comentario");
            $plantador = $PSN1->f("plantador");
            $usua_id = $PSN1->f("id_usuario_reporte");
            $nombre_evangelista = $PSN1->f("nombre_usuario_reporte");
            if($usua_id == ""){
                $usua_id = soloNumeros($_SESSION["id"]);
            }
            if($nombre_evangelista == ""){
                $nombre_evangelista = $_SESSION["nombre"];
            }
            $fechaReporte = $PSN1->f("fechaReporte");
            $fechaInicio = $PSN1->f("fechaInicio");        
            $sitioReunion = $PSN1->f("sitioReunion");
            $grupoMadre_txt = $PSN1->f("grupoMadre_txt");
            $nombreGrupo_txt = $PSN1->f("nombreGrupo_txt");
            
            $capacitacion_txt = $PSN1->f("capacitacion_txt");

            $pabellon = $PSN1->f("pabellon");
            $direccion = $PSN1->f("direccion");
            $ciudad = $PSN1->f("ciudad");
            
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
            <center><input type="button" onClick="window.location.href='index.php?doc=reportar_buscar'" name="previous" class="previous btn btn-danger" value="Cerrar" /> <br />
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
        <h2 class="alert alert-info text-center"><?php
            if($idReporteActual == 0){
                echo "CREACIÓN";
            }else{
                echo "VISUALIZACIÓN";
                $sqlU = "SELECT id FROM sat_reportes WHERE id = (SELECT MAX(id)FROM sat_reportes        WHERE id < ".$idReporteActual.");";
                $PSN1->query($sqlU); 
                if($PSN1->num_rows() > 0){
                    if($PSN1->next_record()){
                    $antId  = $PSN1->f('id');
                    }
                }else{
                   $antId  = 0; 
                }
                $sqlU = "SELECT id FROM sat_reportes WHERE id = (SELECT MIN(id)FROM sat_reportes        WHERE id > ".$idReporteActual.");";
                $PSN1->query($sqlU); 
                if($PSN1->num_rows() > 0){
                    if($PSN1->next_record()){
                    $sigId  = $PSN1->f('id');
                    }
                }else{
                   $sigId  = 0; 
                }              
            }
            
            ?> DE <?=$temp_letrero; ?></h2>
            <?php if ($_SESSION["perfil"] == 162 || $_SESSION["perfil"] == 2){ ?>
            <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <?php
                    if ($antId != 0) {?>
                    <a href="index.php?doc=reportar&id=<?=$antId ?>" name="previous" class="previous btn btn-info">Anterior reporte <?=$antId ?></a>
                    <?php } ?>
                </div>
                <div class="item-btn">
                    <a href="index.php?doc=reportar_buscar" name="previous" class="btn btn-warning">Todos los reportes</a>
                </div>
                <div class="item-btn">
                    <?php
                    if ($sigId != 0) {?>
                    <a href="index.php?doc=reportar&id=<?=$sigId ?>" name="previous" class="previous btn btn-info">Siguiente reporte <?=$sigId ?></a>
                    <?php } ?>
                </div>
            </div>
    <?php } ?>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">INFORMACIÓN GENERAL</h3>
                <h5>REGISTRO ID: <?=str_pad($idReporteActual, 6, "0", STR_PAD_LEFT); ?></h5>
            </div>
            <div class="hr"><hr></div>
        </div> 
        
        <?php
        if($generacionNumero == 0){
            /*?><div class="form-group">
                <label class="control-label col-sm-2" for="capacitacion_txt"><strong>¿Qué capacitación?:</strong></label>
                <div class="col-sm-10"><input name="capacitacion_txt" type="text" id="capacitacion_txt" maxlength="250" value="<?=$capacitacion_txt; ?>" class="form-control" required />
                </div>
            </div><?*/
        }
        ?>        
        <div class="form-group">
            <div class="col-sm-6">
                <strong>Pastor/Plantador/Entrenador:</strong>
                <div id="usua_tags_container" style="display:flex;flex-wrap:wrap;gap:5px;min-height:36px;border:1px solid #ccc;border-radius:4px;padding:6px 8px;background:#fff;cursor:text;" onclick="document.getElementById('usua_nombre').focus()">
                    <?php
                    $usua_ids_arr = array_filter(array_map('trim', explode(',', $usua_id)));
                    $usua_nombres_arr = array_filter(array_map('trim', explode(',', $nombre_evangelista)));
                    for($ti=0; $ti<count($usua_ids_arr); $ti++){
                        $tid = trim($usua_ids_arr[$ti]);
                        $tnombre = isset($usua_nombres_arr[$ti]) ? htmlspecialchars(trim($usua_nombres_arr[$ti]), ENT_QUOTES, 'UTF-8') : '';
                        if($tid != ''){
                            echo '<span class="usua-tag" data-id="'.$tid.'" style="display:inline-flex;align-items:center;background:#337ab7;color:#fff;border-radius:3px;padding:2px 8px;font-size:13px;">'.$tnombre.' <button type="button" onclick="removeUsuaTag(this)" style="background:none;border:none;color:#fff;margin-left:5px;cursor:pointer;font-size:14px;line-height:1;">&times;</button></span>';
                        }
                    }
                    ?>
                    <input type="text" id="usua_nombre" autocomplete="off" style="border:none;outline:none;flex:1;min-width:150px;font-size:14px;" placeholder="Escriba para buscar..." />
                </div>
                <input type="hidden" name="usua_id" id="usua_id" value="<?=htmlspecialchars($usua_id, ENT_QUOTES, 'UTF-8'); ?>" />
                <select id="usua_selector" class="form-control" size="6" style="margin-top: 4px; display: none;">
                    <?=obtenerOpcionesUsuarioGestionEcc(0, $tipo_usuario_ecc); ?>
                </select>
                <small>Escriba para filtrar y seleccione un usuario.</small>
            </div>
            <div class="col-sm-6"></div>
        </div>
        <div class="form-group">
            <?php if($generacionNumero != 77 && $generacionNumero != 8){ ?>
            <div class="col-sm-3">
                <strong>Plantador/Pastor/Lider:</strong>
                <input name="plantador" type="text" id="plantador" maxlength="250" value="<?=$plantador; ?>" class="form-control" required  />
            </div>
            <?php } else { ?>
            <input type="hidden" name="plantador" id="plantador" value="" />
            <?php } ?>
            <div class="col-sm-3" style="<?php if($generacionNumero == 77 || $generacionNumero == 8){ echo 'margin-left:0'; } ?>">
            </div>
            <div class="col-sm-2">
                <strong>Fecha del reporte:</strong>
                <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250" value="<?=$fechaReporte; ?>" class="form-control" required readonly  />
            </div>
            <!--<label class="control-label col-sm-2" for="sitioReunion"><strong>Sitio de la reunión:</strong></label>
            <div class="col-sm-4"><input name="sitioReunion" type="text" id="sitioReunion" maxlength="250" value="<?=$sitioReunion; ?>" class="form-control" required  />
            </div>//-->

            <div class="col-sm-2">
                <strong>Fecha de inicio:</strong>
                <input name="fechaInicio" type="date" id="fechaInicio" maxlength="250" value="<?=$fechaInicio; ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required  />
            </div>
            <div class="col-sm-2">
                <strong>Barrio (Evento):</strong>
                <input name="pabellon" type="text" id="pabellon" maxlength="250" value="<?=$pabellon; ?>" class="form-control" required  placeholder = "Barrio" />
            </div>
            <div class="col-sm-3">
                <strong><?php if($generacionNumero == 77){ echo "Método de evangelismo"; }else { ?>Dirección (Evento)<?php } ?>:</strong>
                <input name="direccion" type="text" id="direccion" maxlength="250" value="<?=$direccion; ?>" class="form-control" required />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2">
                <strong>Ciudad (Evento):</strong>
                <input name="ciudad" type="text" id="ciudad" maxlength="250" value="<?=$ciudad; ?>" class="form-control" required />
            </div>        
            <div class="col-sm-5">
                <strong>Grupo madre / Denominación / Organización / Red de Iglesias de Pequeño Grupo:</strong>
                <input name="grupoMadre_txt" type="text" id="grupoMadre_txt" value="<?=$grupoMadre_txt; ?>" class="form-control" />
            </div>
            
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
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="2" <?php if($generacionNumero == 2){ ?>checked<?php } ?> required class="form-control" />
                </div>

                <label class="control-label col-sm-1" for="inlineRadio3">3</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="3" <?php if($generacionNumero == 3){ ?>checked<?php } ?> required class="form-control" />
                </div>

                <label class="control-label col-sm-1" for="inlineRadio4">4</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="4" <?php if($generacionNumero == 4){ ?>checked<?php } ?> required class="form-control" />
                </div>

                <label class="control-label col-sm-1" for="inlineRadio5">5</label>
                <div class="col-sm-1"><input type="radio" name="generacionNumero" id="inlineRadio1" value="5" <?php if($generacionNumero == 5){ ?>checked<?php } ?> required class="form-control" />
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
                <input name="final_asistencia_total" type="number" id="final_asistencia_total" value="<?=$asistencia_total; ?>" readonly class="form-control"  />
            </div>
            <div class="col-sm-3"></div>
        </div>
            
        <?php if($generacionNumero == 0 || $generacionNumero == 77 || $generacionNumero == 8){?>
            <input name="final_bautizados" type="hidden" id="final_bautizados"  value="<?=$bautizados; ?>" class="form-control" readonly />
            <input name="final_discipulado" type="hidden" id="final_discipulado" value="<?=$discipulado; ?>" class="form-control" readonly />
            <input name="final_desiciones" type="hidden" id="final_desiciones" value="<?=$desiciones; ?>" class="form-control" readonly />
            <input name="final_preparandose" type="hidden" id="final_preparandose" value="<?=$preparandose; ?>" class="form-control" readonly />
            <input name="final_bautizadosPeriodo" type="hidden" id="final_bautizadosPeriodo" value="<?=$bautizadosPeriodo; ?>" class="form-control" readonly />

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
                <div class="col-sm-1"></div>
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
                    <strong>Preparándose para bautismo:</strong>
                <input name="final_preparandose" type="number" id="final_preparandose" readonly value="<?=$preparandose; ?>" class="form-control"  /></div>
                <div class="col-sm-2">
                    <strong>Bautizados este periodo:</strong>
                    <input readonly name="final_bautizadosPeriodo" type="number" id="final_bautizadosPeriodo" value="<?=$bautizadosPeriodo;  ?>" class="form-control"  onChange="sumar()" />
                </div>
                <div class="col-sm-1"></div>
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
                    <input required name="mapeo_fecha" type="date" id="mapeo_fecha" value="<?=$mapeo_fecha; ?>" max='<?=date("Y-m-d"); ?>' class="form-control" />
                </div>
                <div class="col-sm-4">
                    <strong>¿Este grupo está comprometido como iglesia?:</strong>
                    <select required name="mapeo_comprometido" id="mapeo_comprometido" class="form-control">
                        <option value="">Sin seleccionar</option>
                        <option value="3" <?php if($mapeo_comprometido == 3){ ?>selected="selected"<?php } ?>>NO comprometido</option>
                        <option value="4" <?php if($mapeo_comprometido == 4){ ?>selected="selected"<?php } ?>>SI comprometido como iglesia</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <strong>Nombre grupo/iglesia:</strong>
                    <input required name="nombreGrupo_txt" type="text" id="nombreGrupo_txt" value="<?=$nombreGrupo_txt; ?>" class="form-control" />
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
                        <div class="map-text" style="display: flex;align-items: center;"><img src="mapeo_img/<?=$array_campos[$i]; ?>1.png" class="img-responsive" /> NO REALIZA LA TAREA</div>
                    </div>
                    <div class="form-group cont-mapeo">
                        <div class="map-chec"><input required type="radio" name="<?=$array_campos[$i]; ?>" <?php
                        if($array_campos_valor[$i] == 2){
                            ?>checked="checked"<?php
                        }
                        ?> value="2" class="form-control" /></div>
                        <div class="map-text"><img width="40" src="mapeo_img/<?=$array_campos[$i]; ?>2.png" class="img-responsive" /> REALIZA LA TAREA EN COMPAÑIA DEL FACILITADOR</div>
                    </div>
                    <div class="form-group cont-mapeo">
                        <div class="map-chec"><input type="radio" name="<?=$array_campos[$i]; ?>" <?php
                        if($array_campos_valor[$i] == 3){
                            ?>checked="checked"<?php
                        }
                        ?> value="3" class="form-control"  /></div>
                        <div class="map-text"><img src="mapeo_img/<?=$array_campos[$i]; ?>3.png" class="img-responsive" /> REALIZA LA TAREA PERO ESTE MES NO LO HIZO</div>
                    </div>
                    <div class="form-group cont-mapeo">
                        <div class="map-chec"><input required type="radio" name="<?=$array_campos[$i]; ?>" <?php
                        if($array_campos_valor[$i] == 4){
                            ?>checked="checked"<?php
                        }
                        ?> value="4" class="form-control"  /></div>
                        <div class="map-text"><img width="40" src="mapeo_img/<?=$array_campos[$i]; ?>4.png" class="img-responsive" /> REALIZA LA TAREA AUTONOMAMENTE</div>
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
                if($PSN1->num_rows() > 0){
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
                                    <input name="act_bau_fec[]" type="date" id="act_bau_fec" class="form-control" value="<?=$PSN1->f("adj_fec"); ?>" />
                                </div>
                                <div class="col-sm-5">
                                    <strong>Cantidad:</strong>
                                    <input name="act_bau_can[]" type="number" id="act_bau_can" min="0" class="subtotal form-control" value="<?php echo $PSN1->f("adj_can"); ?>" />
                                </div>
                            </div>
                        </div>
                        
                    <?php }
                }
                if ($PSN1->num_rows()<3) {?>
                    

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
                    <h5>Foto del grupo</h5>
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
                    <input name="archivo1" type="file" id="archivo1" class="form-control" />
                </div>
                <div class="col-sm-4"></div>
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
                    <input type="checkbox" name="inactivo" id="inactivo" value="1" <?php if($inactivo == 1){ ?>checked="checked"<?php } ?> class="form-control" />
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
        <?php } ?>
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
                document.getElementById("final_asistencia_total").value = parseInt(var_suma);
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
        
    <?php
}
else if($preguntarGeneracion == 1 && !isset($_POST["SendOpcion"]) ){
    ?><div class="container">
    <div class="row">
        <h3 class="alert alert-info text-center">CREACIÓN DE REPORTE</h3>
    </div>

    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

    <fieldset>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">REPORTES</h3>
                <p>Escoja una de las siguientes opciones</p>
            </div>
            <div class="hr"><hr></div>
        </div>
        <div class="cont-flex fl-cent">
                <a href="index.php?doc=subcategoria-ecc" class="btn-mar btn btn-primary">REPORTE<br><span class="btn-desc">Cada comunidad para Cristo</span></a>
                <button type="button" onClick="generarForm('evan')" name="generacionEvangelismo" class="btn-mar btn btn-success">ACTIVIDADES<br><span class="btn-desc">DE CAPACITADORES</span></button>
                <button type="button" onClick="generarForm('gcel')" name="granCelebracion" class="btn-mar btn btn-warning">GRAN CELEBRACIÓN<br><span class="btn-desc">(REPORTE)</span></button>  
        </div><br><br>       
        <input type="hidden" name="funcion" id="funcion" value="" />
        <input type="hidden" name="generacion" id="generacion" value="<?=$idVehiculo; ?>" />
    </form>


    <script language="javascript">
        function generarForm(generacion){
            if(generacion == "cero"){
                document.getElementById('generacion').value = "CERO";
            }else if(generacion == "evan"){
                document.getElementById('generacion').value = "EVAN";
            }else if(generacion == "gcel"){
                document.getElementById('generacion').value = "GCEL";                
            }else if(generacion == "sopa"){
                document.getElementById('generacion').value = "SOPA";   
            }else{
                document.getElementById('generacion').value = "OTRA";                
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
    $urlNuevoReporte = "index.php?doc=gestionar-sub-programa-ecc&idGrupoMadre=".$idGrupoMadre."&generacion=".$generacionActual;
    ?><div class="container">
        <div class="row">
            <h2 class="alert alert-info text-center"><?php
            if($idReporteActual == 0){
                echo "CREACIÓN";
            }
            else{
                echo "ACTUALIZACIÓN";
            }
            ?> DE <?=$temp_letrero; ?></h2>
        </div>

        <div class="row">
            <h2 class="alert alert-success text-center"><a href="index.php?doc=reportar&opc=2&id=<?=$ultimoId; ?>" class="h2">Se ha <?php
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
        
        setTimeout(function(){
            window.location.href = "<?=$urlNuevoReporte; ?>";
        }, 600);
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
                echo "CREACIÓN";
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
        ?><div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    
    <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">
    <input name="fechaReporte" type="hidden" id="fechaReporte" value="<?=$fechaReporte; ?>" />
    <fieldset>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Información general</h3>
            <h5><?php 
                if($generacionActual == "EVAN"){echo "evangelismo";}
                else if($generacionActual == "CERO"){echo "generación 0";}
                else if($generacionActual == "OTRA"){echo "generación 1 a la 5";}
                else if($generacionActual == "GCEL"){echo "gran celebración";}
                else if($generacionActual == "SOPA"){echo "Deshidratados";}
                ?></h5>
            <p>A continuación por favor ingrese los datos requeridos</p>
            </div>
            <div class="hr"><hr></div>
        </div>  
        <?php        
        if($generacionActual != "SOPA"){
            ?>
        <div class="form-group">
            <div class="col-sm-6">
                <strong>Pastor/Plantador/Entrenador:</strong>
                <div id="usua_tags_container" style="display:flex;flex-wrap:wrap;gap:5px;min-height:36px;border:1px solid #ccc;border-radius:4px;padding:6px 8px;background:#fff;cursor:text;" onclick="document.getElementById('usua_nombre').focus()">
                    <input type="text" id="usua_nombre" autocomplete="off" style="border:none;outline:none;flex:1;min-width:150px;font-size:14px;" placeholder="Escriba para buscar..." />
                </div>
                <input type="hidden" name="usua_id" id="usua_id" value="" />
                <select id="usua_selector" class="form-control" size="6" style="margin-top: 4px; display: none;">
                    <?=obtenerOpcionesUsuarioGestionEcc(0, $tipo_usuario_ecc); ?>
                </select>
                <small>Escriba para filtrar y seleccione un usuario.</small>
            </div>
            <div class="col-sm-6"></div>
        </div>
        <div class="form-group">
            <?php if($generacionActual != "EVAN" && $generacionActual != "GCEL"){ ?>
            <div class="col-sm-3">
                <strong>Plantador/Pastor/Lider:</strong>
                <input name="plantador" type="text" id="plantador" maxlength="250" value="<?=$plantador; ?>" class="form-control" required />
            </div>
            <?php } else { ?>
            <input type="hidden" name="plantador" id="plantador" value="" />
            <?php } ?>
            <div class="col-sm-3">
                <strong>Barrio (Evento):</strong></label>
            <input name="pabellon" type="text" id="pabellon" maxlength="250" value="<?=$pabellon; ?>" class="form-control" required placeholder = "Barrio" />
            </div>            
            <div class="col-sm-3">
                <strong><?php if($generacionActual == "EVAN"){ echo "Método de evangelismo"; }else{ ?>Dirección (Evento)<?php } ?>:</strong>
                <input name="direccion" type="text" id="direccion" maxlength="250" value="<?=$direccion; ?>" class="form-control" required />
            </div>
                        <div class="col-sm-2">

                    <strong>Provincia</strong>

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

<!--            <div class="col-sm-3">
                <strong>Ciudad (Evento):</strong></label>
                <input name="ciudad" type="text" id="ciudad" maxlength="250" value="<?=$ciudad; ?>" class="form-control" required />
            </div> -->
        </div>
        <?php            
        }else{
                ?>
               

<div class="col-md-10 col-md-offset-1">
    <ul class="nav nav-pills nav-justified " role="tablist" >
        <li role="presentation" class="active"><a class="reportes" id="LiInv" href="#Inventario" aria-controls="Inventario" role="tab" data-toggle="tab">Entrada y Salida (Inventario)</a></li>
        <li role="presentation"><a class="reportes" id="LiES" href="#EntregaSopas" aria-controls="EntregaSopas" role="tab" data-toggle="tab">Entrega Deshidratados</a></li>
    </ul>            
    <div class="tab-content" id="tabSopasContent">
        <div role="tabpanel" class="tab-pane fade in active" id="Inventario">
            <br/>
            <ul class="nav nav-pills nav-justified">
                <li class="active"><a id="Ent" class="btnEntrada reportes"  href="#1a" data-toggle="tab">Entrada</a></li>
                <li><a id="Sal" class="btnSalida reportes" href="#2a" data-toggle="tab">Salida</a></li>               
            </ul>
            <div class="tab-content clearfix">
                <hr/>
                <div class="tab-pane fade in active btnEntrada" id="1a">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Seleccionar Donante:</strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="EntChkPD" name="EntChkPD">
                                    <label class="form-check-label" for="EntChkPD">Pueblo de Dios</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="EntChkSP"  name="EntChkSP">
                                    <label class="form-check-label" for="EntChkSP">Samaritan Purse Canadá</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Tipo de Deshidratados y cantidad:</strong>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" aria-label=".." id="ChkEntradaMix" name="ChkEntradaMix">
                                        <label>Mix de vegetales</label>
                                    </span>
                                    <input type="text" class="form-control" id="EntradaMix" name="EntradaMix" maxlength="6" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"> 
                                        <input type="checkbox" aria-label=".." id="ChkEntradaNab" name="ChkEntradaNab">
                                        <label>Nabo Suizo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    </span> 
                                    <input type="text" class="form-control" id="EntradaNab" name="EntradaNab" maxlength="6" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Fecha de Entrada:</strong>
                                <input name="fechaEntrada" type="date" id="fechaEntrada" maxlength="250" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong> Responsable de la Entrada:</strong>
                                <input name="ResponsableEnt" type="text" id="ResponsableEnt" readonly maxlength="250" value="<?=$_SESSION['nombre'];  ; ?>" class="form-control" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Lugar de Entrada:</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select name="pais" id="pais" class="form-control">
                                    <option value="0">Seleccione el Pais</option>
                                    <option value="1">Colombia</option>
                                    <option value="2">Venezuela</option>
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="departamento" id="departamento" class="form-control">                                
                                    <option value="0">...</option>                                   
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="ciudad" id="ciudad" class="form-control">                            
                                    <option value="0">...</option> 
                                </select>
                            </div>
                       </div>
                    </div>
                    <div class="row">
                        <div class="cont-btn cont-flex fl-sbet">
                            <div class="item-btn"></div>
                            <div class="item-btn">
                                <input type="button" name="Btn1a" onclick="SendData('1a')" class="next btn btn-success" value="Guardar" />
                            </div>
                        </div> 
                    </div>    
                </div>
                <div class="tab-pane fade btnSalida" id="2a">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Seleccionar Donante:</strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="salDon1" name="salDon1" >
                                    <label class="form-check-label" for="salDon1">Pueblo de Dios</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="salDon2" name="salDon2" >
                                    <label class="form-check-label" for="salDon2">Samaritan Purse Canadá</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Tipo de Deshidratados y Cantidad:</strong>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" aria-label=".." id="ChkSalidaMix" name="ChkSalidaMix">
                                        <label>Mix de Vegetales</label>
                                    </span>
                                    <input type="text" class="form-control" id="SalidaMix" name="SalidaMix" maxlength="6" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"> 
                                        <input type="checkbox" aria-label=".." id="ChkSalidaNab" name="ChkSalidaNab">
                                        <label>Nabo Suizo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    </span> 
                                    <input type="text" class="form-control" id="SalidaNab" name="SalidaNab" maxlength="6" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Fecha de Salida:</strong>
                                <input name="fechaSalida" type="date" id="fechaSalida" maxlength="250" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong> Responsable de la Entrega:</strong>
                                <input name="ResSalida" type="text" id="ResSalida" readonly maxlength="250" value="<?=$_SESSION['nombre'];  ; ?>" class="form-control" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group" >
                            <strong>  Facilitador que Recibe:</strong>
                                <select name="SalFacilitador" id="SalFacilitador" class="form-control">
                                <?php
                                    $PSN_FAC = new DBbase_Sql;
                                    $sql = "SELECT id, nombre FROM satukhvt_smsapp.usuario where acceso > 0";//tipo = 163 and
                                    $PSN_FAC->query($sql);
                                    $PSN->query($sql);
                                    if($PSN_FAC->num_rows() > 0){
                                        while($PSN_FAC->next_record()){
                                            ?>
                                            <option value="<?=$PSN_FAC->f("id");?>"><?=$PSN_FAC->f("nombre");?></option>  
                                            <?php
                                        }
                                    }
                                ?>                                                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Lugar de Llegada:</strong>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select name="salpais" id="salpais" class="form-control">
                                    <option value="0">Seleccione el Pais</option>
                                    <option value="1">Colombia</option>
                                    <option value="2">Venezuela</option>
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="saldepartamento" id="saldepartamento" class="form-control">                                
                                    <option value="1">...</option>                                   
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="salciudad" id="salciudad" class="form-control">                            
                                    <option value="1">...</option> 
                                </select>
                            </div>
                       </div>
                    </div>
                    <div class="row">
                        <div class="cont-btn cont-flex fl-sbet">
                            <div class="item-btn"></div>
                            <div class="item-btn">
                                <input type="button" name="Btn2a" onclick="SendData('2a')" class="next btn btn-success" value="Guardar" />
                            </div>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane fade" id="EntregaSopas">
            <br/>
            <ul class="nav nav-pills nav-justified">
                <li class="active"><a id="LiEv" class="btnEvangelismo reportes" href="#1aa" data-toggle="tab">Evangelismo</a></li>
                <li><a id="LiPE"  class="btnPrimeraEntrega reportes" href="#2aa" data-toggle="tab">Primera Entrega</a></li>  
                <li><a id="LiOE"  class="btnOtrasEntregas reportes" href="#3aa" data-toggle="tab">Otras entregas</a></li>               
            </ul>
            <div class="tab-content clearfix">
                <hr/>
                <div class="tab-pane fade in active btnEvangelismo" id="1aa">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Seleccionar Donante:</strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="EvaChkPD" name="EvaChkPD" >
                                    <label class="form-check-label" for="EvaChkPD">Pueblo de Dios</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="EvaChkSP" name="EvaChkSP" >
                                    <label class="form-check-label" for="EvaChkSP">Samaritan Purse Canadá</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Tipo de Deshidratados y cantidad de Bolsas Usadas:</strong>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" aria-label=".." id="ChkEvaMix" name="ChkEvaMix">
                                        <label>Mix de vegetales</label>
                                    </span>
                                    <input type="text" class="form-control" id="EvaMix" name="EvaMix" maxlength="6" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"> 
                                        <input type="checkbox" aria-label=".." id="ChkEvaNab" name="ChkEvaNab">
                                        <label>Nabo Suizo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    </span> 
                                    <input type="text" class="form-control" id="EvaNab" name="EvaNab" maxlength="6" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Fecha de la Actividad:</strong>
                                <input name="fechaEva" type="date" id="fechaEva" maxlength="250" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong> Responsable de la Actividad</strong>
                                <input name="ResEva" type="text" id="ResEva" readonly maxlength="250" value="<?=$_SESSION['nombre'];  ; ?>" class="form-control" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Lugar de la Actividad:</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="evapais" id="evapais" class="form-control">
                                    <option value="0">Seleccione el Pais</option>
                                    <option value="1">Colombia</option>
                                    <option value="2">Venezuela</option>
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="evadepartamento" id="evadepartamento" class="form-control">                                
                                    <option value="1">...</option>                                   
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="evaciudad" id="evaciudad" class="form-control">                            
                                    <option value="1">...</option> 
                                </select>
                            </div>
                       </div>
                       <div class="col-md-2">
                            <div class="form-group">
                            <input name="barrioEv" type="text" id="barrioEv" maxlength="250" value=""  class="form-control"  placeholder = "Barrio"  />
                            </div>
                       </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Total Personas Beneficiadas:</strong>
                                <input name="PerBeneficiadas" type="text" id="PerBeneficiadas" maxlength="4" value=""  class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                        </div>
                    </div>
                    <div class="row">
                        <div class="cont-btn cont-flex fl-sbet">
                            <div class="item-btn"></div>
                            <div class="item-btn">
                                <input type="button" name="Btn1aa" onclick="SendData('1aa')" class="next btn btn-success" value="Guardar" />
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="tab-pane fade btnPrimeraEntrega" id="2aa">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Seleccionar Donante:</strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="PEChkPD" name="PEChkPD" >
                                    <label class="form-check-label" for="PEChkPD">Pueblo de Dios</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="PEChkSP" name="PEChkSP" >
                                    <label class="form-check-label" for="PEChkSP">Samaritan Purse Canadá</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Tipo de Deshidratados y cantidad de Bolsas Usadas:</strong>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" aria-label=".." id="ChkPEMix" name="ChkPEMix">
                                        <label>Mix de vegetales</label>
                                    </span>
                                    <input type="text" class="form-control" id="PEMix" name="PEMix" maxlength="6" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"> 
                                        <input type="checkbox" aria-label=".." id="ChkPENab" name="ChkPENab">
                                        <label>Nabo Suizo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    </span> 
                                    <input type="text" class="form-control" id="PENab" name="PENab" maxlength="6" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Fecha de la Entrega:</strong>
                                <input name="fechaPE" type="date" id="fechaPE" maxlength="250" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong> Responsable de la Entrega</strong>
                                <input name="ResPE" type="text" id="ResPE" maxlength="250" value="<?=$_SESSION['nombre'];  ; ?>" class="form-control"readonly />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Nombre y Apellido de quien recibe:</strong>
                                <input name="nombre" type="text" id="nombre" maxlength="250" value=""  class="form-control"/>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Teléfono de Contacto:</strong>
                                <input name="telefono" type="text" id="telefono" maxlength="12" value="" class="form-control"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Ubicacion Beneficiario:</strong>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <select name="pepais" id="pepais" class="form-control">
                                    <option value="0">Seleccione el Pais</option>
                                    <option value="1">Colombia</option>
                                    <option value="2">Venezuela</option>
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="pedepartamento" id="pedepartamento" class="form-control">                                
                                    <option value="1">...</option>                                   
                                </select>
                            </div>
                       </div>
                       <div class="col-md-4">
                            <div class="form-group">
                                <select name="peciudad" id="peciudad" class="form-control">                            
                                    <option value="1">...</option> 
                                </select>
                            </div>
                       </div>
                       <div class="col-md-2">
                            <div class="form-group">
                            <input name="barrioPE" type="text" id="barrioPE" maxlength="250" value=""  class="form-control"  placeholder = "Barrio"  />
                            </div>
                       </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>¿Cuántas personas viven en la casa?:</strong>
                                <input name="PersonasCasa" type="text" id="PersonasCasa" maxlength="3" value=""  class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>¿Cuántos niños hay? (de 0 a 10 años): </strong>
                                <input name="NinosCasa" type="text" id="NinosCasa"  maxlength="3" value="" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>¿Cuántos niños son beneficiarios de Soy Satura?:</strong>
                                <input name="SoySatura" type="text" id="SoySatura" maxlength="3" value=""  class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>¿Cuántos adolescentes hay? (de 11 a 18 años):  </strong>
                                <input name="AdoCasa" type="text" id="AdoCasa" maxlength="3" value="" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>¿Cuántos adultos mayores hay? (de 60 años en adelante): </strong>
                                <input name="Adultos" type="text" id="Adultos" maxlength="3" value=""  class="form-control"  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>¿Cuántas personas en condición de discapacidad hay?: </strong>
                                <input name="Discapacidad" type="text" id="Discapacidad" maxlength="3" value="" class="form-control"/>
                            </div>
                        </div>
                    </div>                
                    <div class="row">
                        <div class="col-md-5">
                           
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>¿Qué tipo de discapacidad?: </strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkMovilidad" name="ChkMovilidad">
                                    <label class="form-check-label" for="ChkMovilidad">Movilidad</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkMental" Name="ChkMental">
                                    <label class="form-check-label" for="ChkMental">Mental</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkAuditiva" name="ChkAuditiva">
                                    <label class="form-check-label" for="ChkAuditiva">Auditiva</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkVisual" name="ChkVisual" >
                                    <label class="form-check-label" for="ChkVisual">Visual</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkOtras" name="ChkOtras">
                                    <label class="form-check-label" for="ChkOtras">Otras</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkNingunaDis" name="ChkNingunaDis">
                                    <label class="form-check-label" for="ChkNingunaDis">Ninguna</label>
                                </div>
                            </div>   
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>¿Cuánto suman los ingresos mensuales de su grupo familiar?: </strong>
                                <div class="radio">
                                    <label class="radio" >
                                        <input type="radio" name="OptIngresos" id="Opt1" value="Opt01">Actualmente no recibe ningún ingreso
                                    </label>
                                    <label class="radio">
                                        <input type="radio" name="OptIngresos" id="Opt2" value="Opt02">Menos de un salario mínimo
                                    </label>   
                                    <label class="radio">
                                        <input type="radio" name="OptIngresos" id="Opt3" value="Opt03">Un salario mínimo
                                    </label>   
                                    <label class="radio">
                                        <input type="radio" name="OptIngresos" id="Opt4" value="Opt04">Más de un salario mínimo
                                    </label>   
                                    <label class="radio">
                                        <input type="radio" name="OptIngresos" id="Opt5" value="Opt05">No sabe
                                    </label>                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Usted como cabeza del grupo familiar regularmente: ¿Cuál de las siguientes comidas No consume?: </strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkDesayuno" name="ChkDesayuno">
                                    <label class="form-check-label" for="ChkDesayuno">Desayuno</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkAlmuerzo" name="ChkAlmuerzo">
                                    <label class="form-check-label" for="ChkAlmuerzo">Almuerzo</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkCena" name="ChkCena">
                                    <label class="form-check-label" for="ChkCena">Cena</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkTodas" name="ChkTodas">
                                    <label class="form-check-label" for="ChkTodas">Todas</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>¿Dentro de su grupo familiar se ha presentado alguna de las siguientes situaciones?: </strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkSuicidio" name="ChkSuicidio">
                                    <label class="form-check-label" for="ChkSuicidio">Suicidio</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkViolencia" name="ChkViolencia">
                                    <label class="form-check-label" for="ChkViolencia">Violencia Intrafamiliar</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkAbuso" name="ChkAbuso">
                                    <label class="form-check-label" for="ChkAbuso">Abuso sexual a menores</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkDesaparicion" name="ChkDesaparicion">
                                    <label class="form-check-label" for="ChkDesaparicion">Desaparición forzosa</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkDesplazamiento" name="ChkDesplazamiento">
                                    <label class="form-check-label" for="ChkDesplazamiento">Desplazamiento</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkTrafico" name="ChkTrafico">
                                    <label class="form-check-label" for="ChkTrafico">Tráfico de personas</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="ChkNingunaSit" name="ChkNingunaSit">
                                    <label class="form-check-label" for="ChkNingunaSit">Ninguna</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cont-btn cont-flex fl-sbet">
                            <div class="item-btn"></div>
                            <div class="item-btn">
                                <input type="button" name="Btn2aa" onclick="SendData('2aa')" class="next btn btn-success" value="Guardar" />
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="tab-pane fade btnOtrasEntregas" id="3aa">
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Seleccionar Beneficiario:</strong>
                                <select name="SelBeneficiario" id="SelBeneficiario" class="form-control">
                                    <?php
                                        $PSN_BEN = new DBbase_Sql;
                                        $sql_Ben = "select IdBeneficiado, Nombre from beneficiarios order by 1";
                                        $PSN_BEN->query($sql_Ben);
                                        if($PSN_BEN->num_rows() > 0){
                                            while($PSN_BEN->next_record()){
                                                ?>
                                                <option value="<?=$PSN_BEN->f("IdBeneficiado");?>"><?=$PSN_BEN->f("Nombre");?></option>  
                                                <?php
                                            }
                                        }
                                    ?>            
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Seleccionar Donante:</strong>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="OEChkPD" name="OEChkPD" >
                                    <label class="form-check-label" for="OEChkPD">Pueblo de Dios</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="OEChkSP" name="OEChkSP" >
                                    <label class="form-check-label" for="OEChkSP">Samaritan Purse Canadá</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong>Tipo de Deshidratados y cantidad de Bolsas Entregadas:</strong>
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <input type="checkbox" aria-label=".." id="ChkOEMix" name="ChkOEMix">
                                        <label>Mix de vegetales</label>
                                    </span>
                                    <input type="text" class="form-control" id="OEMix" name="OEMix" maxlength="6" readonly>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"> 
                                        <input type="checkbox" aria-label=".." id="ChkOENab" name="ChkOENab">
                                        <label>Nabo Suizo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                                    </span> 
                                    <input type="text" class="form-control" id="OENab" name="OENab" maxlength="6" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>Fecha de la Entrega:</strong>
                                <input name="fechaOE" type="date" id="fechaOE" maxlength="250" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required  />
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <strong> Responsable de la Entrega</strong>
                                <input name="ResOE" type="text" id="ResOE" maxlength="250" value="<?=$_SESSION['nombre'];  ; ?>" class="form-control"  readonly/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-5">   
                            <div class="form-group">
                                <strong>¿Asiste a una IPG?</strong>
                                <div class="btn-group">
                                    <label class=" " >
                                        <input type="radio" name="options" id="optOESi" autocomplete="off" onclick="ChangeOption(event);"> Si
                                    </label>
                                    <label class="">
                                        <input type="radio" name="options" id="optOENo" autocomplete="off" checked onclick="ChangeOption(event);">No
                                    </label>                                   
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-5">
                            <div class="form-group" id="IPG">
                                <strong> ¿A Cual IPG Asiste?</strong>
                                <input name="IPGAsiste" type="text" id="IPGAsiste" maxlength="250" value="" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cont-btn cont-flex fl-sbet">
                            <div class="item-btn"></div>
                            <div class="item-btn">
                                <input type="button" name="Btn3aa" onclick="SendData('3aa')" class="next btn btn-success" value="Guardar" />
                            </div>
                        </div> 
                    </div>
                </div>
            </div>      
        </div>
        <input type="hidden" name="MensajeError" id="MensajeError" value="" />
        <input type="hidden" name="SendOpcion" id="SendOpcion" value="" />
    </div>
</div>
<?php  
    }
            ?>


        <?php   
        if($generacionActual != "SOPA"){    
        if($generacionActual == "CERO" || $generacionActual == "EVAN" || $generacionActual == "GCEL"){
            ?>
            <div class="form-group">
                <div class="col-sm-5">
                    <strong>Grupo madre / Denominación / Organización / Red de Iglesias de Pequeño Grupo:</strong>
                    <input name="grupoMadre_txt" type="text" id="grupoMadre_txt" maxlength="250" value="<?=$grupoMadre_txt; ?>" class="form-control" required /><input name="generacionNumero" type="hidden" id="generacionNumero" value="<?php if($generacionActual == "EVAN"){ echo "77"; }else if($generacionActual == "GCEL"){echo "8";}else{ echo "0"; } ?>" class="form-control" required />
                </div>
                <div class="col-sm-3">
                    <strong>Fecha de inicio:</strong>
                    <input name="fechaInicio" type="date" id="fechaInicio" maxlength="250" value="<?=$fechaInicio; ?>" max='<?=date("Y-m-d"); ?>' class="form-control" required />
                </div>
                <div class="col-sm-4">
                    <strong>Nombre grupo/iglesia:</strong>
                    <input name="nombreGrupo_txt" type="text" id="nombreGrupo_txt" maxlength="250" value="<?=$nombreGrupo_txt; ?>" class="form-control"  />
                </div>
            </div>
            <?php            
        }else if($generacionActual == "OTRA"){
            ?>
            <div class="form-group">
                <div class="col-sm-5">
                    <strong>Grupo madre / Denominación / Organización / Red de Iglesias de Pequeño Grupo:</strong>
                    <input name="grupoMadre_txt" type="text" id="grupoMadre_txt" value="<?=$grupoMadre_txt; ?>" class="form-control" />
                </div>
                <div class="col-sm-4">
                    <strong>Fecha de inicio:</strong>
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
                    <label class="control-label col-sm-1" for="inlineRadio2">2</label>
                    <input type="radio" name="generacionNumero" id="inlineRadio1" value="2" required class="form-control" />
                </div>
                <div class="cont-flex">
                    <label class="control-label col-sm-1" for="inlineRadio3">3</label>
                    <input type="radio" name="generacionNumero" id="inlineRadio1" value="3" required class="form-control" />
                </div>
                <div class="cont-flex">
                    <label class="control-label col-sm-1" for="inlineRadio4">4</label>
                    <input type="radio" name="generacionNumero" id="inlineRadio1" value="4" required class="form-control" />
                </div>
                <div class="cont-flex">
                    <label class="control-label col-sm-1" for="inlineRadio5">5</label>
                    <input type="radio" name="generacionNumero" id="inlineRadio1" value="5" required class="form-control" />
                </div>
            </div>
        
            <?php                        
        }
        ?>
        <div class="cont-btn cont-flex fl-sbet">
            <div class="item-btn"></div>
            <div class="item-btn">
                <input type="button" name="next" class="next btn btn-success" value="Siguiente" />
            </div>
        </div> 
        <?php                        
        }    
        ?>   
    </fieldset>
    <?php
    if($generacionActual != "SOPA"){ 
    if($generacionActual == "OTRA"){
        
        ?>
        <fieldset>
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
                <div class="col-sm-4">
                    <strong>Fecha de mapeo:</strong>
                    <input name="mapeo_fecha" type="date" id="mapeo_fecha" value="<?=date("Y-m-d"); ?>" max='<?=date("Y-m-d"); ?>' class="form-control" />
                </div>
                <div class="col-sm-4">
                    <strong>¿Este grupo está comprometido como iglesia?:</strong>
                    <select name="mapeo_comprometido" id="mapeo_comprometido" class="form-control">
                        <option value="">Sin seleccionar</option>
                        <option value="3" <?php if($mapeo_comprometido == 3){ ?>selected="selected"<?php } ?>>NO comprometido</option>
                        <option value="4" <?php if($mapeo_comprometido == 4){ ?>selected="selected"<?php } ?>>SI comprometido como iglesia</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <strong>Nombre grupo/iglesia:</strong>
                    <input name="nombreGrupo_txt" type="text" id="nombreGrupo_txt" maxlength="250" value="<?=$nombreGrupo_txt; ?>" class="form-control" />
                </div>
            </div>
            <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                </div>
                <div class="item-btn">
                    <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                </div>
            </div>
        </fieldset>
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
            <fieldset>
                <div class="cont-tit">
                    <div class="hr"><hr></div>
                    <div class="tit-cen">
                        <h3 class="text-center">Método de verificación</h3>
                        <h5><?=$array_campos_txt[$i]; ?></h5>
                        <p>A continuación por favor ingrese los datos requeridos</p>
                    </div>
                    <div class="hr"><hr></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-5 cont-flex-2 vl-cent fl-sbet">
                        <div class="cont-flex-2 vl-cent">
                            <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>1.png" class="img-responsive" />
                            <h5>NO REALIZA LA TAREA</h5>
                        </div>
                        <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i]; ?>" value="1" <?php
                    if($array_campos_valor[$i] == 1){
                        ?>checked="checked"<?php
                    }
                    ?> class="form-control"  /></div>
                    <div class="col-sm-3"></div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-5 cont-flex-2 vl-cent fl-sbet">
                        <div class="cont-flex-2 vl-cent">
                            <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>2.png" class="img-responsive" />
                            <h5>REALIZA LA TAREA EN COMPAÑIA DEL FACILITADOR</h5>
                        </div>
                        <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i]; ?>" <?php
                    if($array_campos_valor[$i] == 2){
                        ?>checked="checked"<?php
                    }
                    ?> value="2" class="form-control"  /></div>
                    <div class="col-sm-3"></div>
                </div>

                <div class="form-group">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-5 cont-flex-2 vl-cent fl-sbet">
                        <div class="cont-flex-2 vl-cent">
                            <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>3.png" class="img-responsive" />
                            <h5>REALIZA LA TAREA PERO ESTE MES NO LO HIZO</h5>
                        </div>
                        <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i]; ?>" <?php
                    if($array_campos_valor[$i] == 3){
                        ?>checked="checked"<?php
                    }
                    ?> value="3" class="form-control"  /></div>
                    <div class="col-sm-3"></div>
                </div>
                <div class="form-group">
                    <div class="col-sm-4"></div>
                    <div class="col-sm-5 cont-flex-2 vl-cent fl-sbet">
                        <div class="cont-flex-2 vl-cent">
                            <img style="margin-right: 15px" width="35px" src="mapeo_img/<?=$array_campos[$i]; ?>4.png" class="img-responsive" /><h5>REALIZA LA TAREA AUTONOMAMENTE</h5>
                        </div>
                        <input style="height: 30px; width: 30px; box-shadow: none;" type="radio" name="<?=$array_campos[$i]; ?>" <?php
                    if($array_campos_valor[$i] == 4){
                        ?>checked="checked"<?php
                    }
                    ?> value="4" class="form-control"  /></div>
                    <div class="col-sm-3"></div>
                </div>  
                <div class="cont-btn cont-flex fl-sbet">
                    <div class="item-btn">
                        <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                    </div>
                    <div class="item-btn">
                        <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                    </div>
                </div>         
            </fieldset>            
        <?php } ?>
        <fieldset>
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>Bautizos</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group col-sm-12">
                <script>
                    $(function(){
                        $("#adicionarAdd").on('click',function(){
                            $("#tablaAdd tbody tr:eq(0)").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");
                            var total = 0;
                            $('.subtotal').each(function(){
                                 total = total + Number($(this).val());
                            });
                            $('#total').val(total);
                        });
                        $(document).on("click",".eliminarAdd",function(){
                            var parent = $(this).parents().get(0);
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
            <div class="form-group col-sm-12">
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
            <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                </div>
                <div class="item-btn">
                    <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                </div>
            </div>
        </fieldset>
        <fieldset>
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación</h3>
                    <h5>Foto del grupo</h5>
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
                    <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                </div>
            </div>
        </fieldset>
        <!--<fieldset>
            <div class="cont-tit">
                <div class="hr"><hr></div>
                <div class="tit-cen">
                    <h3 class="text-center">Método de verificación 4</h3>
                    <h5>Testimonio</h5>
                    <p>A continuación por favor ingrese los datos requeridos</p>
                </div>
                <div class="hr"><hr></div>
            </div>
            <div class="form-group">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <strong>Testimonio:</strong>
                    <input name="archivo2" type="file" id="archivo2" class="form-control" />
                </div>
                <div class="col-sm-3"></div>
            </div>
            <div class="form-group">
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
        </fieldset>
        -->
    <?php }else{
        ?><fieldset>
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

            <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                </div>
                <div class="item-btn">
                    <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
                </div>
            </div>
        </fieldset><?php
    }
}    
        
        
$campos = array();
if($generacionActual != "SOPA"){ 
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

foreach($campos as $campo_actual){?>
    <fieldset>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center">Método de verificación</h3>
                <h5><?=$campo_actual[0]; ?></h5>
                <p>A continuación por favor ingrese los datos requeridos</p>
            </div>
            <div class="hr"><hr></div>
        </div>       
        <div class="form-group">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <strong>Cantidad:</strong>
                <input name="<?=$campo_actual[1]; ?>" type="number" id="<?=$campo_actual[1]; ?>" maxlength="255" value="<?=$_REQUEST[$campo_actual[1]]; ?>" class="form-control" />
            </div>
            <div class="col-sm-3"></div>
        </div>
        <div class="cont-btn cont-flex fl-sbet">
            <div class="item-btn">
                <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
            </div>
            <div class="item-btn">
                <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
            </div>
        </div>
    </fieldset>
<?php } 
if ($generacionActual == "GCEL") {?>
    <fieldset>
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
        <div class="cont-btn cont-flex fl-sbet">
            <div class="item-btn">
                <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
            </div>
            <div class="item-btn">
                <input type="button" name="next" id="archivo1_sig" class="next btn btn-success" value="Siguiente" />
            </div>
        </div>
    </fieldset>
<?php }?>

    <fieldset>
        <div class="cont-tit">
            <div class="hr"><hr></div>
            <div class="tit-cen">
                <h3 class="text-center"><?php if($generacionActual == "EVAN"){ echo "ALCANZADOS"; }else{ ?>ASISTENCIA<?php } ?></h3>
                <h5><?php 
                    if($generacionActual == "EVAN"){echo "evangelismo";}
                    else if($generacionActual == "CERO"){echo "generación 0";}
                    else if($generacionActual == "OTRA"){echo "generación 1 al 5";}
                    else if($generacionActual == "GCEL"){echo "gran celebración";}
                    else if($generacionActual == "SOPA"){echo "Entrada y Salida (Inventario) y Entrega de Deshidratados";}
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
        </div>
        <?php
        if($generacionActual == "CERO" || $generacionActual == "EVAN"){
            ?>
                <input name="final_bautizados" type="hidden" id="final_bautizados" value="0" class="form-control" readonly />
                <input name="final_discipulado" type="hidden" id="final_discipulado" value="0" class="form-control" readonly />
                <input name="final_desiciones" type="hidden" id="final_desiciones" value="0" class="form-control" readonly />
                <input name="final_preparandose" type="hidden" id="final_preparandose" value="0" class="form-control" readonly />
                <input name="final_bautizadosPeriodo" type="hidden" id="final_bautizadosPeriodo" value="0" class="form-control" readonly />
            <?php
        }
        else{
            ?>
            <div class="cont-tit">
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
                <div class="form-group">
                    <div class="col-sm-2">
                        <strong>Miembros Bautizados:</strong>
                        <input name="final_bautizados" type="number" id="final_bautizados" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-2">
                        <strong>En discipulado:</strong>
                        <input name="final_discipulado" type="number" id="final_discipulado" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-3">
                        <strong>Decisiones para Cristo:</strong>
                        <input name="final_desiciones" type="number" id="final_desiciones" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-3">
                        <strong>Preparándose para bautismo:</strong>
                        <input name="final_preparandose" type="number" id="final_preparandose" value="0" class="form-control" readonly />
                    </div>
                    <div class="col-sm-2">
                        <strong>Bautizados este periodo:</strong>
                        <input name="final_bautizadosPeriodo" type="number" id="final_bautizadosPeriodo" value="0" class="form-control" readonly />
                    </div>
                </div>
                <?php
            }
        }
        ?>
        <div class="cont-btn cont-flex fl-sbet">
                <div class="item-btn">
                    <input type="button" name="previous" class="previous btn btn-info" value="Anterior" />
                </div>
                <div class="item-btn">
                    <input type="submit" name="button" value="Guardar" class="btn btn-success">
                </div>
            </div>
    </fieldset>

        
    <input type="submit" name="button-hidden" id="button-hidden" style="display:none">
    <input type="hidden" name="funcion" id="funcion" value="<?=$MyFuncion; ?>" />
    <input type="hidden" name="generacion" id="generacion" value="<?=$generacionActual; ?>" />
    <?php

}
?>
    </form>
   

    <script language="javascript">
        jQuery(document).ready(function(){
			jQuery("#EntradaMix").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#EntradaNab").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#SalidaMix").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#SalidaNab").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#EvaMix").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#EvaNab").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#OEMix").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#OENab").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#PEMix").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#PENab").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#PerBeneficiadas").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#telefono").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#PersonasCasa").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#NinosCasa").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#SoySatura").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#AdoCasa").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#Adultos").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});
            jQuery("#Discapacidad").on('input', function (evt) {
				jQuery(this).val(jQuery(this).val().replace(/[^0-9]/g, ''));
			});

            $("#pais").on('change', function () {
                $("#pais option:selected").each(function () {
                    pais=$(this).val();
                    $.post("paises.php", { pais: pais }, function(data){
                        $("#departamento").html(data);
                        $("#ciudad").html("\'<option value='0'>...</option>\'");
                    });			
                });
            });
            $("#departamento").on('change', function () {
                $("#departamento option:selected").each(function () {
                    depto=$(this).val();
                    $.post("paises.php", { depto: depto }, function(data){
                        $("#ciudad").html(data);
                    });			
                });
            });

            $("#salpais").on('change', function () {
                $("#salpais option:selected").each(function () {
                    pais=$(this).val();
                    $.post("paises.php", { pais: pais }, function(data){
                        $("#saldepartamento").html(data);
                        $("#salciudad").html("\'<option value='0'>...</option>\'");
                    });			
                });
            });
            $("#saldepartamento").on('change', function () {
                $("#saldepartamento option:selected").each(function () {
                    depto=$(this).val();
                    $.post("paises.php", { depto: depto }, function(data){
                        $("#salciudad").html(data);
                    });			
                });
            });
            $("#evapais").on('change', function () {
                $("#evapais option:selected").each(function () {
                    pais=$(this).val();
                    $.post("paises.php", { pais: pais }, function(data){
                        $("#evadepartamento").html(data);
                        $("#evaciudad").html("\'<option value='0'>...</option>\'");
                    });			
                });
            });
            $("#evadepartamento").on('change', function () {
                $("#evadepartamento option:selected").each(function () {
                    depto=$(this).val();
                    $.post("paises.php", { depto: depto }, function(data){
                        $("#evaciudad").html(data);
                    });			
                });
            });

            $("#pepais").on('change', function () {
                $("#pepais option:selected").each(function () {
                    pais=$(this).val();
                    $.post("paises.php", { pais: pais }, function(data){
                        $("#pedepartamento").html(data);
                        $("#peciudad").html("\'<option value='0'>...</option>\'");
                    });			
                });
            });
            $("#pedepartamento").on('change', function () {
                $("#pedepartamento option:selected").each(function () {
                    depto=$(this).val();
                    $.post("paises.php", { depto: depto }, function(data){
                        $("#peciudad").html(data);
                    });			
                });
            });

            
		});
        $("#ChkEntradaMix").change(function(){
            if( $('#ChkEntradaMix').prop('checked') ) {
                $("#EntradaMix").removeAttr("readonly");
                $("#EntradaMix").focus();
            }else{
                $('#EntradaMix').val('');
                $("#EntradaMix").attr("readonly","readonly");
            }
        });
        $("#ChkEntradaNab").change(function(){
            if( $('#ChkEntradaNab').prop('checked') ) {
                $("#EntradaNab").removeAttr("readonly");
                $("#EntradaNab").focus();
            }else{
                $('#EntradaNab').val('');
                $("#EntradaNab").attr("readonly","readonly");
            }
        });

        $("#ChkSalidaMix").change(function(){
            if( $('#ChkSalidaMix').prop('checked') ) {
                $("#SalidaMix").removeAttr("readonly");
                $("#SalidaMix").focus();
            }else{
                $('#SalidaMix').val('');
                $("#SalidaMix").attr("readonly","readonly");
            }
        });
        $("#ChkSalidaNab").change(function(){
            if( $('#ChkSalidaNab').prop('checked') ) {
                $("#SalidaNab").removeAttr("readonly");
                $("#SalidaNab").focus();
            }else{
                $('#SalidaNab').val('');
                $("#SalidaNab").attr("readonly","readonly");
            }
        });
        $("#ChkEvaMix").change(function(){
            if( $('#ChkEvaMix').prop('checked') ) {
                $("#EvaMix").removeAttr("readonly");
                $("#EvaMix").focus();
            }else{
                $('#EvaMix').val('');
                $("#EvaMix").attr("readonly","readonly");
            }
        });
        $("#ChkEvaNab").change(function(){
            if( $('#ChkEvaNab').prop('checked') ) {
                $("#EvaNab").removeAttr("readonly");
                $("#EvaNab").focus();
            }else{
                $('#EvaNab').val('');
                $("#EvaNab").attr("readonly","readonly");
            }
        });
        $("#ChkPEMix").change(function(){
            if( $('#ChkPEMix').prop('checked') ) {
                $("#PEMix").removeAttr("readonly");
                $("#PEMix").focus();
            }else{
                $('#PEMix').val('');
                $("#PEMix").attr("readonly","readonly");
            }
        });
        $("#ChkPENab").change(function(){
            if( $('#ChkPENab').prop('checked') ) {
                $("#PENab").removeAttr("readonly");
                $("#PENab").focus();
            }else{
                $('#PENab').val('');
                $("#PENab").attr("readonly","readonly");
            }
        });
        $("#ChkOEMix").change(function(){
            if( $('#ChkOEMix').prop('checked') ) {
                $("#OEMix").removeAttr("readonly");
                $("#OEMix").focus();
            }else{
                $('#OEMix').val('');
                $("#OEMix").attr("readonly","readonly");
            }
        });
        $("#ChkOENab").change(function(){
            if( $('#ChkOENab').prop('checked') ) {
                $("#OENab").removeAttr("readonly");
                $("#OENab").focus();
            }else{
                $('#OENab').val('');
                $("#OENab").attr("readonly","readonly");
            }
        });      


        
        var current = 1,current_step,next_step,steps;
        $('#IPG').attr('hidden', true );
        function SendData(Opc){
            var Mensaje = "";
            if (Opc == "1a"){
                if(!$('#EntChkPD').is(':checked') && !$('#EntChkSP').is(':checked') ){
                    Mensaje += "Debe seleccionar un donante\n"
                }
                if($('#EntradaMix').val().length < 1  && $('#EntradaNab').val().length < 1){
                    Mensaje += "Debe ingresar cantidad y tipo de Deshidratados\n"
                }
                if($('#EntradaMix').val().length >= 1 && ($('#EntradaMix').val() == 0 )){
                    Mensaje += "Debe ingresar cantidad mayor a 0 para Mix de Vegetales\n"
                }
                if($('#EntradaNab').val().length >= 1 && ($('#EntradaNab').val() == 0 )){
                    Mensaje += "Debe ingresar cantidad mayor a 0 para Nabo Suizo\n"
                }
                if($('#pais').val() < 1 ){
                    Mensaje += "Debe seleccionar un pais\n"
                }
                if($('#departamento').val() < 1){
                    Mensaje += "Debe seleccionar un departamento\n"
                }
            }
            if (Opc == "2a"){
                if(!$('#salDon1').is(':checked') && !$('#salDon2').is(':checked') ){
                    Mensaje += "Debe seleccionar un donante\n"
                }
                if($('#SalidaMix').val().length < 1  && $('#SalidaNab').val().length < 1){
                    Mensaje += "Debe ingresar cantidad y tipo de Deshidratados\n"
                }
                if($('#SalidaMix').val().length >= 1 && ($('#SalidaMix').val() == 0 )){
                    Mensaje += "Debe ingresar cantidad mayor a 0 para Mix de Vegetales\n"
                }
                if($('#SalidaNab').val().length >= 1 && ($('#SalidaNab').val() == 0 )){
                    Mensaje += "Debe ingresar cantidad mayor a 0 para Nabo Suizo\n"
                }
                if($('#salpais').val() < 1 ){
                    Mensaje += "Debe seleccionar un pais\n"
                }
                if($('#saldepartamento').val() < 1){
                    Mensaje += "Debe seleccionar un departamento\n"
                }
            }
            if (Mensaje.length >0){
alert(Mensaje);
            }
            $('#MensajeError').val(Mensaje);
            $('#SendOpcion').val(Opc);
            $('#form1').submit();
        }

        function ChangeOption(event){
            if ($('#optOESi').is(":checked")){             
                $('#IPG').attr('hidden', false);              
            }else if ($('#optOENo').is(":checked")){             
                $('#IPG').attr('hidden', true );
            }


            if ($('#optEva').is(":checked")){             
                $('#Eva').attr('hidden', false);
                $('#Ing').attr('hidden', true);
                $('#Seg').attr('hidden', true);
            }else if ($('#optIng').is(":checked")){             
                $('#Eva').attr('hidden', true );
                $('#Ing').attr('hidden', false);
                $('#Seg').attr('hidden', true);
            }else if ($('#optSeg').is(":checked")){             
                $('#Eva').attr('hidden', true );
                $('#Ing').attr('hidden', true);
                $('#Seg').attr('hidden', false);
            }
        }
       
        //
        function generarForm(){
            //Completo el formulario  
            if(current == steps){
                <?php
                 if($generacionActual != "SOPA"){
                if($generacionActual != "CERO" && $generacionActual != "EVAN" && $generacionActual != "GCEL"){
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
                    
                    if(parseInt(document.getElementById("final_asistencia_total").value) < 3){
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

            steps = $("fieldset").length;
            $(".next").click(function(){
                //current_step = $(this).parent();
                //$(this).closest(“fieldset”)
                //next_step = $(this).parent().next();
                if (!$("#form1")[0].checkValidity()) { 
                    document.getElementById('button-hidden').click();
                }else{
                    current_step = $(this).closest("fieldset");      //
                    next_step = $(this).closest("fieldset").next();
                    next_step.show();
                    current_step.hide();
                    setProgressBar(++current);
                }
            });

            $(".previous").click(function(){
                //current_step = $(this).parent();
                //next_step = $(this).parent().prev();
                current_step = $(this).closest("fieldset");      //
                next_step = $(this).closest("fieldset").prev();
                next_step.show();
                current_step.hide();
                setProgressBar(--current);
            });

            setProgressBar(current);
            // Change progress bar action
            function setProgressBar(curStep){
                var percent = parseFloat(100 / steps) * curStep;
                percent = percent.toFixed();
                $(".progress-bar")
                .css("width",percent+"%")
                .html(percent+"%"); 
                
                sumar();
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
                //
                if(document.getElementById("asistencia_jov").value != ""){
                    var asistencia_jov = document.getElementById("asistencia_jov").value;
                }
                if(document.getElementById("asistencia_nin").value != ""){
                    var asistencia_nin = document.getElementById("asistencia_nin").value;
                }
                
                
                <?php
                if($generacionActual == "CERO" || $generacionActual == "GCEL"){
                    ?>               
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
                    if($generacionActual != "EVAN"){
                        ?>
                        if(document.getElementById("desiciones").value != ""){
                            var desiciones = document.getElementById("desiciones").value;
                        }


                        var bautizados = 0;
                        var bautizadosPeriodo = 0;
                        if(document.getElementById("total").value != ""){
                            var bautizados = document.getElementById("total").value;
                        }
                        if(document.getElementById("total").value != ""){
                            var bautizadosPeriodo = document.getElementById("total").value;
                        }
                        <?php
                    }
                    
                }
                ?>
                var var_suma = parseInt(asistencia_hom) + parseInt(asistencia_muj) + parseInt(asistencia_jov) + parseInt(asistencia_nin);
                document.getElementById("final_asistencia_total").value = parseInt(var_suma);
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
        
        <script type="text/javascript">
            // ---- Buscador multi-selección con tags para Pastor/Plantador/Entrenador ----
            function removeUsuaTag(btn){
                var tag = btn.parentNode;
                var removedId = tag.getAttribute('data-id');
                tag.parentNode.removeChild(tag);
                actualizarHiddenUsua();
            }

            function actualizarHiddenUsua(){
                var hidden = document.getElementById('usua_id');
                var tags = document.querySelectorAll('#usua_tags_container .usua-tag');
                var ids = [];
                for(var i = 0; i < tags.length; i++){
                    ids.push(tags[i].getAttribute('data-id'));
                }
                hidden.value = ids.join(',');
            }

            function agregarUsuaTag(id, nombre){
                var container = document.getElementById('usua_tags_container');
                var input = document.getElementById('usua_nombre');

                // Eliminar cualquier tag existente (solo se permite uno)
                var tagsExistentes = container.querySelectorAll('.usua-tag');
                for(var i = 0; i < tagsExistentes.length; i++){
                    tagsExistentes[i].parentNode.removeChild(tagsExistentes[i]);
                }

                var tag = document.createElement('span');
                tag.className = 'usua-tag';
                tag.setAttribute('data-id', id);
                tag.style.cssText = 'display:inline-flex;align-items:center;background:#337ab7;color:#fff;border-radius:3px;padding:2px 8px;font-size:13px;';
                tag.innerHTML = nombre + ' <button type="button" onclick="removeUsuaTag(this)" style="background:none;border:none;color:#fff;margin-left:5px;cursor:pointer;font-size:14px;line-height:1;">&times;</button>';
                container.insertBefore(tag, input);
                input.value = '';
                actualizarHiddenUsua();
            }

            function inicializarBuscadorUsuarioGestionEcc(){
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
                    return (texto || '').toLowerCase().trim()
                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                }

                function mostrarSelector(){
                    selector.style.display = 'block';
                }

                function ocultarSelector(){
                    selector.style.display = 'none';
                    input.value = '';
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
                        selector.appendChild(option);
                    }

                    mostrarSelector();
                }

                input.addEventListener('input', function(){
                    if(this.value.length > 0){
                        renderizarOpciones(this.value);
                    } else {
                        ocultarSelector();
                    }
                });

                input.addEventListener('focus', function(){
                    if(this.value.length > 0){
                        renderizarOpciones(this.value);
                    }
                });

                input.addEventListener('click', function(){
                    if(this.value.length > 0){
                        renderizarOpciones(this.value);
                    }
                });

                selector.addEventListener('change', function(){
                    if(this.value === ''){ return; }
                    var nombre = this.options[this.selectedIndex].text;
                    agregarUsuaTag(this.value, nombre);
                    ocultarSelector();
                });

                selector.addEventListener('click', function(){
                    if(this.value === ''){ return; }
                    var nombre = this.options[this.selectedIndex].text;
                    agregarUsuaTag(this.value, nombre);
                    ocultarSelector();
                });

                document.addEventListener('click', function(e){
                    var container = document.getElementById('usua_tags_container');
                    if(e.target !== input && e.target !== selector && !container.contains(e.target)){
                        ocultarSelector();
                    }
                });

                if(form){
                    form.addEventListener('submit', function(e){
                        var tags = document.querySelectorAll('#usua_tags_container .usua-tag');
                        if(tags.length === 0){
                            e.preventDefault();
                            alert('Debe seleccionar al menos un Pastor/Plantador/Entrenador de la lista.');
                            input.focus();
                        }
                    });
                }

                ocultarSelector();
            }

            $(document).ready(function(){
                inicializarBuscadorUsuarioGestionEcc();

                /*recargaLista();

                $('#rep_carcel').change(function(){

                    recargaLista();

                });*/

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

        </script> <?php
    }
}   //FIN DEL IF DE REDIRIGIR SI YA INSERTO EL REGISTRO
else{
    echo "No deberia estar aquí.";
}
?>