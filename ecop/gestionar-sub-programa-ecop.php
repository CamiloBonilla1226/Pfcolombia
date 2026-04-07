<?php

$PSN1 = new DBbase_Sql;

$PSN = new DBbase_Sql;

$webArchivo = "preoperacional";

$temp_letrero = "ECOP";



function compressImage($source, $destination, $quality)
{

    $info = getimagesize($source);

    if ($info['mime'] == 'image/jpeg') {

        $image = imagecreatefromjpeg($source);

    } elseif ($info['mime'] == 'image/gif') {

        $image = imagecreatefromgif($source);

    } elseif ($info['mime'] == 'image/png') {

        $image = imagecreatefrompng($source);

    }

    imagejpeg($image, $destination, $quality);

}

$preguntarGeneracion = 0;

if (isset($_REQUEST["generacion"]) && $_REQUEST["generacion"] != "") {

    $generacionActual = eliminarInvalidos($_REQUEST["generacion"]);

} else {

    $generacionActual = "ECOP";

}

if (isset($_REQUEST["id"]) && $_REQUEST["id"] != "") {

    $idReporteActual = soloNumeros($_REQUEST["id"]);

    if ($_SESSION["perfil"] == 162 || $_SESSION["perfil"] == 163) {

        $sql = "UPDATE  sat_reportes SET 

                    mapeo_fecha = '" . date('Y-m-d') . "'";



        $sql .= "WHERE id = '" . $idReporteActual . "'";

        $PSN1->query($sql);

    }

} else {

    $idReporteActual = 0;

}

$arrayRequerimientos = array();

if (isset($_POST["funcion"])) {

    $error_datos = 0;

    if ($_POST["funcion"] == "insertar") {

        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);

        $fechaInicio = eliminarInvalidos($_REQUEST["fechaInicio"]);

        if (isset($_REQUEST['sitioReunion'])) {

            $sitioReunion = soloNumeros($_REQUEST["sitioReunion"]);

        } else {

            $sitioReunion = 0;

        }

        $grupoMadre_txt = eliminarInvalidos($_REQUEST["grupoMadre_txt"]);

        $nombreGrupo_txt = eliminarInvalidos($_REQUEST["nombreGrupo_txt"]);
        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);

        $direccion = eliminarInvalidos($_REQUEST["direccion"]);

        if (isset($_REQUEST["municipio"])) {

            $ciudad = soloNumeros($_REQUEST["municipio"]);

        } else {

            $ciudad = 0;

        }

        $capacitacion_txt = eliminarInvalidos($_REQUEST["capacitacion_txt"]);

        $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);

        $generacionNumero = soloNumeros($_REQUEST["generacionNumero"]);

        $asistencia_hom = soloNumeros($_REQUEST["asistencia_hom"]);

        $asistencia_muj = soloNumeros($_REQUEST["asistencia_muj"]);

        $asistencia_jov = soloNumeros($_REQUEST["asistencia_jov"]);

        $asistencia_nin = soloNumeros($_REQUEST["total"]);

        $bautizados = soloNumeros($_REQUEST["total2"]);

        $desiciones = soloNumeros($_REQUEST["total3"]);

        $discipulado = soloNumeros($_REQUEST["discipulado"]);

        $rep_entr = soloNumeros($_REQUEST["rep_entr"]);
        $rep_nuevo = soloNumeros($_REQUEST["rep_nuevo"]);

        $unidad_2 = soloNumeros($_REQUEST["unidad_2"]);
        $unidad_3 = soloNumeros($_REQUEST["unidad_3"]);
        $unidad_4 = soloNumeros($_REQUEST["unidad_4"]);
        $unidad_5 = soloNumeros($_REQUEST["unidad_5"]);
        $unidad_6 = soloNumeros($_REQUEST["unidad_6"]);
        $unidad_total = soloNumeros($_REQUEST["unidad_total"]);

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

        $asistencia_total = soloNumeros($_REQUEST["asistencia_total"]);

        $bautizadosPeriodo = isset($_REQUEST["bautizadosPeriodo"]) ? soloNumeros($_REQUEST["bautizadosPeriodo"]) : 0;
        $preparandose = isset($_REQUEST["preparandose"]) ? soloNumeros($_REQUEST["preparandose"]) : 0;

        $rep_tip = 347;

        if ($_REQUEST["rep_ndis"] != 0 && $_REQUEST["rep_ndis"] != null) {

            $rep_ndis = soloNumeros($_REQUEST["rep_ndis"]);

        } else {

            $rep_ndis = 0;

        }
        if ($_REQUEST["rep_entr"] != 0 && $_REQUEST["rep_entr"] != null) {

            $rep_entr = soloNumeros($_REQUEST["rep_entr"]);

        } else {

            $rep_entr = 0;

        }

        if ($_REQUEST["rep_nuevo"] != 0 && $_REQUEST["rep_nuevo"] != null) {

            $rep_nuevo = soloNumeros($_REQUEST["rep_nuevo"]);

        } else {

            $rep_nuevo = 0;

        }

        if ($_REQUEST["unidad_2"] != 0 && $_REQUEST["unidad_2"] != null) {

            $unidad_2 = soloNumeros($_REQUEST["unidad_2"]);

        } else {

            $unidad_2 = 0;

        }

        if ($_REQUEST["unidad_3"] != 0 && $_REQUEST["unidad_3"] != null) {

            $unidad_3 = soloNumeros($_REQUEST["unidad_3"]);

        } else {

            $unidad_3 = 0;

        }
        if ($_REQUEST["unidad_4"] != 0 && $_REQUEST["unidad_4"] != null) {

            $unidad_4 = soloNumeros($_REQUEST["unidad_4"]);

        } else {

            $unidad_4 = 0;

        }
        if ($_REQUEST["unidad_5"] != 0 && $_REQUEST["unidad_5"] != null) {

            $unidad_5 = soloNumeros($_REQUEST["unidad_5"]);

        } else {

            $unidad_5 = 0;

        }
        if ($_REQUEST["unidad_6"] != 0 && $_REQUEST["unidad_6"] != null) {

            $unidad_6 = soloNumeros($_REQUEST["unidad_6"]);

        } else {

            $unidad_6 = 0;

        }
        if ($_REQUEST["unidad_total"] != 0 && $_REQUEST["unidad_total"] != null) {

            $unidad_total = soloNumeros($_REQUEST["unidad_total"]);

        } else {

            $unidad_total = 0;

        }







        $iglesias_reconocidas = 0;

        if ($error_datos == 0) {

            $sql = 'INSERT INTO sat_reportes (

                idUsuario,

                            plantador,

                rep_entr,
                rep_nuevo,
                unidad_2 ,
unidad_3 ,
unidad_4 ,
unidad_5 ,
unidad_6 ,
unidad_total ,

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

                "' . $_SESSION["id"] . '",

                "' . $plantador . '",

                "' . $rep_entr . '", 
                "' . $rep_nuevo . '", 
                "' . $unidad_2 . '",
                "' . $unidad_3 . '",
                "' . $unidad_4 . '",
                "' . $unidad_5 . '",
                "' . $unidad_6 . '",
                "' . $unidad_total . '",

                "' . $fechaReporte . '", 

                "' . $fechaInicio . '", 

                ' . $sitioReunion . ', 

                "' . $grupoMadre_txt . '", 

                "' . $nombreGrupo_txt . '",                 

                "' . $capacitacion_txt . '", 

                "' . $idGrupoMadre . '", 

                "' . $generacionNumero . '", 

                

                "' . $pabellon . '", 

                "' . $direccion . '", 

                ' . $ciudad . ', 

                



                    "' . $asistencia_hom . '", 

                    "' . $asistencia_muj . '", 

                    "' . $asistencia_jov . '", 

                    "' . $asistencia_nin . '", 

                    

                "' . $bautizados . '", 

                "' . $bautizadosPeriodo . '", 

                

                

                "' . $asistencia_total . '", 

                "' . $discipulado . '", 

                "' . $desiciones . '",

                ' . $rep_ndis . ', 

                "' . $preparandose . '",



                NOW(), 

                "' . $_SESSION["id"] . '",



                "' . $archivo1 . '",

                "' . $archivo2 . '",

                

                    "' . $mapeo_fecha . '",

                    "' . $mapeo_comprometido . '",



                    "' . $mapeo_oracion . '",

                    "' . $mapeo_companerismo . '",

                    "' . $mapeo_adoracion . '",

                    "' . $mapeo_biblia . '",

                    "' . $mapeo_evangelizar . '",

                    "' . $mapeo_cena . '",

                    "' . $mapeo_dar . '",

                    "' . $mapeo_bautizar . '",

                    "' . $mapeo_trabajadores . '",

                        

                    "' . $mapeo_anho . '",

                    "' . $mapeo_cuarto . '",                    

                "' . $archivo3 . '",

                ' . $rep_tip . '

            )';



            //

            //

            $ultimoQuery = $PSN1->query($sql);

            $ultimoId = $PSN1->ultimoId();

            //

            if ($archivo1 != "") {

                $extArchivo = $archivo1;

                if ($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif") {

                    $rutaOrigen = $_FILES['archivo1']['tmp_name'];

                    $rutaDestino = "archivos/evi_" . $ultimoId . "_1." . $archivo1;

                    compressImage($rutaOrigen, $rutaDestino, 80);

                } else {

                    if (move_uploaded_file($_FILES['archivo1']['tmp_name'], "archivos/evi_" . $ultimoId . "_1." . $archivo1)) {

                    }

                }

            }





            if ($archivo2 != "") {

                $extArchivo = $archivo2;

                if ($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif") {

                    $rutaOrigen = $_FILES['archivo2']['tmp_name'];

                    $rutaDestino = "archivos/evi_" . $ultimoId . "_2." . $archivo2;

                    compressImage($rutaOrigen, $rutaDestino, 80);

                } else {

                    if (move_uploaded_file($_FILES['archivo2']['tmp_name'], "archivos/evi_" . $ultimoId . "_2." . $archivo2)) {

                    }

                }

            }



            if ($asistencia_nin > 0) {

                $act_grad_nom = $_REQUEST["act_grad_nom"];

                $act_grad_tar = $_REQUEST['act_grad_tar'];



                $sql = 'INSERT INTO tbl_adjuntos (

                    adj_nom,

                    adj_url,

                    adj_fec,

                    adj_tip, 

                    adj_rep_fk)';

                $sql .= 'VALUES';

                for ($i = 0; $i < sizeof($act_grad_tar); $i++) {

                    $sql .= "('" . $act_grad_nom[$i] . "','" . $act_grad_tar[$i] . "','" . date('Y-m-d') . "',1," . $ultimoId . "),";

                }

                $sql = substr($sql, 0, -1);

                //echo $sql;

                $ultimoQuery = $PSN1->query($sql);

            }

            if ($bautizados > 0) {

                $act_vin_nom = $_REQUEST["act_vin_nom"];

                $act_vin_tar = $_REQUEST['act_vin_tar'];



                $sql = 'INSERT INTO tbl_adjuntos (

                    adj_nom,

                    adj_url,

                    adj_fec,

                    adj_tip, 

                    adj_rep_fk)';

                $sql .= 'VALUES';

                for ($i = 0; $i < sizeof($act_vin_tar); $i++) {

                    $sql .= "('" . $act_vin_nom[$i] . "','" . $act_vin_tar[$i] . "','" . date('Y-m-d') . "',2," . $ultimoId . "),";

                }

                $sql = substr($sql, 0, -1);

                $ultimoQuery = $PSN1->query($sql);

            }

            if ($desiciones > 0) {

                $act_vex_nom = $_REQUEST["act_vex_nom"];

                $act_vex_tar = $_REQUEST['act_vex_tar'];



                $sql = 'INSERT INTO tbl_adjuntos (

                    adj_nom,

                    adj_url,

                    adj_fec,

                    adj_tip, 

                    adj_rep_fk)';

                $sql .= 'VALUES';

                for ($i = 0; $i < sizeof($act_vex_tar); $i++) {

                    $sql .= "('" . $act_vex_nom[$i] . "','" . $act_vex_tar[$i] . "','" . date('Y-m-d') . "',3," . $ultimoId . "),";

                }

                $sql = substr($sql, 0, -1);

                $ultimoQuery = $PSN1->query($sql);

            }

            $varExitoREP = 1;

        }

    } else if ($_POST["funcion"] == "eliminar") {

        $sql = 'DELETE from sat_reportes WHERE id = "' . $idReporteActual . '"';

        $PSN1->query($sql);

    } else if ($_POST["funcion"] == "actualizar") {

        // die("Actualizar");

        //

        /*

        *   PESTAÑA GENERAL

        */

        $plantador = eliminarInvalidos($_REQUEST["plantador"]);

        $rep_entr = eliminarInvalidos($_REQUEST["rep_entr"]);
        $rep_nuevo = eliminarInvalidos($_REQUEST["rep_nuevo"]);
        $unidad_2 = eliminarInvalidos($_REQUEST["unidad_2"]);
        $unidad_3 = eliminarInvalidos($_REQUEST["unidad_3"]);
        $unidad_4 = eliminarInvalidos($_REQUEST["unidad_4"]);
        $unidad_5 = eliminarInvalidos($_REQUEST["unidad_5"]);
        $unidad_6 = eliminarInvalidos($_REQUEST["unidad_6"]);
        $unidad_total = eliminarInvalidos($_REQUEST["unidad_total"]);

        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);

        $fechaInicio = eliminarInvalidos($_REQUEST["fechaInicio"]);

        if (isset($_REQUEST['sitioReunion'])) {

            $sitioReunion = soloNumeros($_REQUEST["sitioReunion"]);

        } else {

            $sitioReunion = 0;

        }



        $grupoMadre_txt = eliminarInvalidos($_REQUEST["grupoMadre_txt"]);

        $nombreGrupo_txt = eliminarInvalidos($_REQUEST["nombreGrupo_txt"]);



        if (!empty($_REQUEST["inactivo"])) {

            $inactivo = soloNumeros($_REQUEST["inactivo"]);

        } else {

            $inactivo = 0;

        }







        $capacitacion_txt = eliminarInvalidos($_REQUEST["capacitacion_txt"]);

        $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);

        $generacionNumero = soloNumeros($_REQUEST["generacionNumero"]);



        $pabellon = eliminarInvalidos($_REQUEST["pabellon"]);

        $direccion = eliminarInvalidos($_REQUEST["direccion"]);

        if (!empty($_REQUEST["municipio"])) {

            $ciudad = soloNumeros($_REQUEST["municipio"]);

        } else {

            $ciudad = 0;

        }



        $asistencia_hom = soloNumeros($_REQUEST["asistencia_hom"]);

        $asistencia_muj = soloNumeros($_REQUEST["asistencia_muj"]);

        $asistencia_jov = soloNumeros($_REQUEST["asistencia_jov"]);

        $asistencia_nin = soloNumeros($_REQUEST["total"]);



        $bautizados = soloNumeros($_REQUEST["total2"]);

        $bautizadosPeriodo = soloNumeros($_REQUEST["bautizadosPeriodo"]);





        //Calculados:

        $asistencia_total = soloNumeros($_REQUEST["asistencia_total"]);

        $discipulado = soloNumeros($_REQUEST["discipulado"]);

        $desiciones = soloNumeros($_REQUEST["total3"]);

        if ($_REQUEST["rep_ndis"] != 0 && $_REQUEST["rep_ndis"] != null) {

            $rep_ndis = soloNumeros($_REQUEST["rep_ndis"]);

        } else {

            $rep_ndis = 0;

        }
        if ($_REQUEST["rep_entr"] != 0 && $_REQUEST["rep_entr"] != null) {

            $rep_entr = soloNumeros($_REQUEST["rep_entr"]);

        } else {

            $rep_entr = 0;

        }
        if ($_REQUEST["rep_nuevo"] != 0 && $_REQUEST["rep_nuevo"] != null) {

            $rep_nuevo = soloNumeros($_REQUEST["rep_nuevo"]);

        } else {

            $rep_nuevo = 0;

        }
        if ($_REQUEST["unidad_2"] != 0 && $_REQUEST["unidad_2"] != null) {

            $unidad_2 = soloNumeros($_REQUEST["unidad_2"]);

        } else {

            $unidad_2 = 0;

        }
        if ($_REQUEST["unidad_3"] != 0 && $_REQUEST["unidad_3"] != null) {

            $unidad_3 = soloNumeros($_REQUEST["unidad_3"]);

        } else {

            $unidad_3 = 0;

        }
        if ($_REQUEST["unidad_4"] != 0 && $_REQUEST["unidad_4"] != null) {

            $unidad_4 = soloNumeros($_REQUEST["unidad_4"]);

        } else {

            $unidad_4 = 0;

        }
        if ($_REQUEST["unidad_5"] != 0 && $_REQUEST["unidad_5"] != null) {

            $unidad_5 = soloNumeros($_REQUEST["unidad_5"]);

        } else {

            $unidad_5 = 0;

        }
        if ($_REQUEST["unidad_6"] != 0 && $_REQUEST["unidad_6"] != null) {

            $unidad_6 = soloNumeros($_REQUEST["unidad_6"]);

        } else {

            $unidad_6 = 0;

        }
        if ($_REQUEST["unidad_total"] != 0 && $_REQUEST["unidad_total"] != null) {

            $unidad_total = soloNumeros($_REQUEST["unidad_total"]);

        } else {

            $unidad_total = 0;

        }


        $preparandose = soloNumeros($_REQUEST["preparandose"]);

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

        $sql = 'UPDATE  sat_reportes SET 

                    inactivo = ' . $inactivo . ', 

                    rep_entr = "' . $rep_entr . '", 
                    rep_nuevo = "' . $rep_nuevo . '", 
                    unidad_2 = "' . $unidad_2 . '", 
                    unidad_3 = "' . $unidad_3 . '", 
                    unidad_4 = "' . $unidad_4 . '", 
                    unidad_5 = "' . $unidad_5 . '", 
                    unidad_6 = "' . $unidad_6 . '", 
                    unidad_total = "' . $unidad_total . '", 

                    plantador = "' . $plantador . '", 

                    fechaInicio = "' . $fechaInicio . '", 

                    sitioReunion = ' . $sitioReunion . ', 

                    grupoMadre_txt = "' . $grupoMadre_txt . '", 

                    nombreGrupo_txt = "' . $nombreGrupo_txt . '",                     

                    capacitacion_txt = "' . $capacitacion_txt . '", 

                    generacionNumero = "' . $generacionNumero . '", 



                    pabellon = "' . $pabellon . '", 

                    direccion = "' . $direccion . '", 

                    ciudad = ' . $ciudad . ', 



                        asistencia_hom = "' . $asistencia_hom . '", 

                        asistencia_muj = "' . $asistencia_muj . '", 

                        asistencia_jov = "' . $asistencia_jov . '", 

                        asistencia_nin =  "' . $asistencia_nin . '", 



                    bautizados =  "' . $bautizados . '", 

                    bautizadosPeriodo = "' . $bautizadosPeriodo . '", 



                    asistencia_total = "' . $asistencia_total . '", 

                    discipulado = "' . $discipulado . '", 

                    desiciones =  "' . $desiciones . '",

                    rep_ndis =  "' . $rep_ndis . '", 

                    preparandose = "' . $preparandose . '",





                    mapeo_fecha = "' . $mapeo_fecha . '",

                    mapeo_comprometido = "' . $mapeo_comprometido . '",



                        mapeo_oracion = "' . $mapeo_oracion . '",

                        mapeo_companerismo = "' . $mapeo_companerismo . '",

                        mapeo_adoracion = "' . $mapeo_adoracion . '",

                        mapeo_biblia = "' . $mapeo_biblia . '",

                        mapeo_evangelizar = "' . $mapeo_evangelizar . '",

                        mapeo_cena = "' . $mapeo_cena . '",

                        mapeo_dar = "' . $mapeo_dar . '",

                        mapeo_bautizar = "' . $mapeo_bautizar . '",

                        mapeo_trabajadores = "' . $mapeo_trabajadores . '",



                    mapeo_anho = "' . $mapeo_anho . '",

                    mapeo_cuarto = "' . $mapeo_cuarto . '"';





        if ($archivo1 != "") {

            $sql .= ', ext1 = "' . $archivo1 . '"';

        }





        if ($archivo2 != "") {

            $sql .= ', ext2 = "' . $archivo2 . '"';

        }





        if ($archivo3 != "") {

            $sql .= ', ext3 = "' . $archivo3 . '"';

        }





        $sql .= '   ,modificacionFecha = NOW(),

                    modificacionUsuario = "' . $_SESSION["id"] . '"

                WHERE id = "' . $idReporteActual . '"';

        //echo $sql;

        $PSN1->query($sql);

        $num_grad_ant = 0;

        $act_grad_id = $_REQUEST['act_grad_id'];

        $act_grad_nom = $_REQUEST['act_grad_nom'];

        $act_grad_tar = $_REQUEST['act_grad_tar'];

        $num_grad_ant = $_REQUEST['grad_regist'];

        $num_grad_nue = $_REQUEST['total'];

        $sqlDel = "DELETE FROM tbl_adjuntos WHERE adj_rep_fk = " . $idReporteActual . " AND adj_tip = 1 ";

        $PSN1->query($sqlDel);

        //echo "Si hay antiguos a modificar: ".sizeof($act_bau_id);

        //var_dump($act_bau_id);

        for ($i = 0; $i < $num_grad_nue; $i++) {



            $sqlA = "REPLACE INTO tbl_adjuntos (adj_id,adj_nom,adj_url,adj_fec,adj_can, adj_tip,adj_rep_fk)";

            $sqlA .= "VALUES (0" . $act_grad_id[$i] . ",'" . $act_grad_nom[$i] . "','" . $act_grad_tar[$i] . "','" . date('Y-m-d') . "',NULL,1," . $idReporteActual . "); ";

            //echo $sqlA;

            $PSN1->query($sqlA);

        }

        $num_vin_ant = 0;

        $act_vin_id = $_REQUEST['act_vin_id'];

        $act_vin_nom = $_REQUEST['act_vin_nom'];

        $act_vin_tar = $_REQUEST['act_vin_tar'];

        $num_vin_ant = $_REQUEST['vin_regist'];

        $num_vin_nue = $_REQUEST['total2'];

        $sqlDel2 = "DELETE FROM tbl_adjuntos WHERE adj_rep_fk = " . $idReporteActual . " AND adj_tip = 2 ";

        $PSN1->query($sqlDel2);

        //echo "Si hay antiguos a modificar: ".sizeof($act_bau_id);

        //var_dump($act_bau_id);

        for ($i = 0; $i < $num_vin_nue; $i++) {



            $sqlA2 = "REPLACE INTO tbl_adjuntos (adj_id,adj_nom,adj_url,adj_fec,adj_can, adj_tip,adj_rep_fk)";

            $sqlA2 .= "VALUES (0" . $act_vin_id[$i] . ",'" . $act_vin_nom[$i] . "','" . $act_vin_tar[$i] . "','" . date('Y-m-d') . "',NULL,2," . $idReporteActual . "); ";

            //echo $sqlA2;

            $PSN1->query($sqlA2);

        }



        $num_vex_ant = 0;

        $act_vex_id = $_REQUEST['act_vex_id'];

        $act_vex_nom = $_REQUEST['act_vex_nom'];

        $act_vex_tar = $_REQUEST['act_vex_tar'];

        $num_vex_ant = $_REQUEST['vex_regist'];

        $num_vex_nue = $_REQUEST['total3'];

        $sqlDel3 = "DELETE FROM tbl_adjuntos WHERE adj_rep_fk = " . $idReporteActual . " AND adj_tip = 3 ";

        $PSN1->query($sqlDel3);

        //echo "Si hay antiguos a modificar: ".sizeof($act_bau_id);

        //var_dump($act_bau_id);

        for ($i = 0; $i < $num_vex_nue; $i++) {



            $sqlA3 = "REPLACE INTO tbl_adjuntos (adj_id,adj_nom,adj_url,adj_fec,adj_can, adj_tip,adj_rep_fk)";

            $sqlA3 .= "VALUES (0" . $act_vex_id[$i] . ",'" . $act_vex_nom[$i] . "','" . $act_vex_tar[$i] . "','" . date('Y-m-d') . "',NULL,3," . $idReporteActual . "); ";

            //echo $sqlA3;

            $PSN1->query($sqlA3);

        }

        $varExitoREP_UPD = 1;

        //

        //

        //if($generacionNumero > 0){

        // Compress Image

        $ultimoId = $idReporteActual;

        //

        if ($archivo1 != "") {

            $extArchivo = $archivo1;

            if ($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif") {

                $rutaOrigen = $_FILES['archivo1']['tmp_name'];

                $rutaDestino = "archivos/evi_" . $ultimoId . "_1." . $archivo1;

                compressImage($rutaOrigen, $rutaDestino, 80);

            } else {

                if (move_uploaded_file($_FILES['archivo1']['tmp_name'], "archivos/evi_" . $ultimoId . "_1." . $archivo1)) {

                }

            }

        }





        if ($archivo2 != "") {

            $extArchivo = $archivo2;

            if ($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif") {

                $rutaOrigen = $_FILES['archivo2']['tmp_name'];

                $rutaDestino = "archivos/evi_" . $ultimoId . "_2." . $archivo2;

                compressImage($rutaOrigen, $rutaDestino, 80);

            } else {

                if (move_uploaded_file($_FILES['archivo2']['tmp_name'], "archivos/evi_" . $ultimoId . "_2." . $archivo2)) {

                }

            }

        }





        if ($archivo3 != "") {

            $extArchivo = $archivo3;

            if ($extArchivo == "png" || $extArchivo == "jpg" || $extArchivo == "jpeg" || $extArchivo == "gif") {

                $rutaOrigen = $_FILES['archivo3']['tmp_name'];

                $rutaDestino = "archivos/evi_" . $ultimoId . "_3." . $archivo3;

                compressImage($rutaOrigen, $rutaDestino, 80);

            } else {

                if (move_uploaded_file($_FILES['archivo3']['tmp_name'], "archivos/evi_" . $ultimoId . "_3." . $archivo3)) {

                } else {

                    echo "Error";



                }

            }

        }

        //

        //}        



        //

    }

}





switch ($error_datos) {

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



if ($idReporteActual > 0) {

    /*

    *   TRAEMOS LOS DATOS PRINCIPALES DEL USUARIO

    */

    $sql = "SELECT C.descripcion AS regional, U.nombre AS coordinador, U.id as id_coordinador, sat_reportes.*, sat_grupos.nombre, D.id_departamento,M.id_municipio FROM sat_reportes";

    $sql .= " LEFT JOIN sat_grupos ON sat_grupos.id = sat_reportes.idGrupoMadre 

LEFT JOIN dane_municipios AS M ON sat_reportes.ciudad = M.id_municipio 

LEFT JOIN dane_departamentos AS D ON M.departamento_id = D.id_departamento 

LEFT JOIN usuario AS U ON U.id = sat_reportes.idUsuario

LEFT JOIN tbl_regional_ubicacion AS RU ON RU.reub_id = sat_reportes.sitioReunion 

LEFT JOIN categorias AS C ON C.id = RU.reub_reg_fk";

    $sql .= " WHERE sat_reportes.id = '" . $idReporteActual . "'";

    $sql .= " GROUP BY sat_reportes.id";

    $PSN1->query($sql);

    //echo $sql;

    if ($PSN1->num_rows() > 0) {

        if ($PSN1->next_record()) {

            $inactivo = $PSN1->f("inactivo");

            $regional = $PSN1->f("regional");

            $plantador = $PSN1->f("plantador");

            $rep_entr = $PSN1->f("rep_entr");
            $rep_nuevo = $PSN1->f("rep_nuevo");
            $unidad_2 = $PSN1->f("unidad_2");
            $unidad_3 = $PSN1->f("unidad_3");
            $unidad_4 = $PSN1->f("unidad_4");
            $unidad_5 = $PSN1->f("unidad_5");
            $unidad_6 = $PSN1->f("unidad_6");
            $unidad_total = $PSN1->f("unidad_total");

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





            //Calculados:

            $asistencia_total = $PSN1->f("asistencia_total");

            $discipulado = $PSN1->f("discipulado");

            $desiciones = $PSN1->f("desiciones");

            $rep_ndis = $PSN1->f("rep_ndis");
            $rep_entr = $PSN1->f("rep_entr");
            $rep_nuevo = $PSN1->f("rep_nuevo");
            $unidad_2 = $PSN1->f("unidad_2");
            $unidad_3 = $PSN1->f("unidad_3");
            $unidad_4 = $PSN1->f("unidad_4");
            $unidad_5 = $PSN1->f("unidad_5");
            $unidad_6 = $PSN1->f("unidad_6");
            $unidad_total = $PSN1->f("unidad_total");

            $preparandose = $PSN1->f("preparandose");

            $iglesias_reconocidas = $PSN1->f("iglesias_reconocidas");





            $mapeo_fecha = $PSN1->f("mapeo_fecha");

            $mapeo_cuarto = $PSN1->f("mapeo_cuarto");

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

    } else {

        ?>
        <div class="row">

            <h3 class="alert alert-info text-center">Registro eliminado</h3>

        </div>

        <div class="form-group">

            <center><input type="button" onClick="window.location.href='index.php?doc=consultar-sub-programa-ecop'"
                    name="previous" class="previous btn btn-danger" value="Cerrar" /> <br />

        </div>

        <?php

        exit;

    }

    $sql = "SELECT SUM(adj_can) as suma";

    $sql .= " FROM tbl_adjuntos ";

    $sql .= " WHERE adj_rep_fk = '" . $idReporteActual . "'";

    $PSN1->query($sql);

    if ($PSN1->num_rows() > 0) {

        if ($PSN1->next_record()) {

            $sum_baut = $PSN1->f("suma");

        }

    }

    ?>
    <div class="container">

        <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

            <h3 class="alert alert-info text-center"><?php

            if ($idReporteActual == 0) {

                echo "REPORTE";

            } else {

                echo "VISUALIZACIÓN";

                $sqlU = "SELECT SR.id FROM sat_reportes AS SR

                LEFT JOIN usuario AS U ON U.id = SR.idUsuario

                LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id

                LEFT JOIN categorias AS C ON C.id = UE.empresa_pd

                WHERE SR.id = (SELECT MAX(STR.id)FROM sat_reportes AS STR WHERE STR.id < " . $idReporteActual . ") ";

                if ($_SESSION["empresa_pd"] != "" && $_SESSION["empresa_pd"] != 0) {

                    $sqlU .= "AND UE.empresa_pd = " . $_SESSION["empresa_pd"] . " ";

                }

                $sqlU .= "AND SR.rep_tip = 347";

                $PSN1->query($sqlU);

                if ($PSN1->num_rows() > 0) {

                    if ($PSN1->next_record()) {

                        $antId = $PSN1->f('id');

                    }

                } else {

                    $antId = 0;

                }

                $sqlU = "SELECT SR.id FROM sat_reportes AS SR

                LEFT JOIN usuario AS U ON U.id = SR.idUsuario

                LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = U.id

                LEFT JOIN categorias AS C ON C.id = UE.empresa_pd

                WHERE SR.id = (SELECT MIN(STR.id)FROM sat_reportes AS STR WHERE STR.id > " . $idReporteActual . ") ";

                if ($_SESSION["empresa_pd"] != "" && $_SESSION["empresa_pd"] != 0) {

                    $sqlU .= "AND UE.empresa_pd = " . $_SESSION["empresa_pd"] . " ";

                }

                $sqlU .= "AND SR.rep_tip = 347";

                $PSN1->query($sqlU);

                //echo  $sqlU;
        
                if ($PSN1->num_rows() > 0) {

                    if ($PSN1->next_record()) {

                        $sigId = $PSN1->f('id');

                    }

                } else {

                    $sigId = 0;

                }

            }



            ?> DE <?= $temp_letrero; ?></h3>

            <?php //if ($_SESSION["perfil"] == 162 || $_SESSION["perfil"] == 2){ ?>

            <div class="cont-btn cont-flex fl-sbet">

                <div class="item-btn">

                    <?php

                    if ($antId != 0) { ?>

                        <a href="index.php?doc=gestionar-sub-programa-ecop&id=<?= $antId ?>" name="previous"
                            class="previous btn btn-info">Anterior reporte <?= $antId ?></a>

                    <?php } ?>

                </div>

                <div class="item-btn">

                    <a href="index.php?doc=consultar-sub-programa-ecop" name="previous" class="btn btn-warning">Todos los
                        reportes</a>

                </div>

                <div class="item-btn">

                    <?php

                    if ($sigId != 0) { ?>

                        <a href="index.php?doc=gestionar-sub-programa-ecop&id=<?= $sigId ?>" name="previous"
                            class="previous btn btn-info">Siguiente reporte <?= $sigId ?></a>

                    <?php } ?>

                </div>

            </div>

            <?php

            $fecha_actual = date("Y-m-d");

            $fechLimite = date("Y-m-d", strtotime($fecha_actual . "- 90 days"));

            //echo $fechLimite ." - ". $fechaReporte;
        
            ?>

            <div class="cont-tit">

                <div class="hr">
                    <hr>
                </div>

                <div class="tit-cen">

                    <h3 class="text-center">INFORMACIÓN GENERAL</h3>

                    <h5>REGISTRO ID: <?= str_pad($idReporteActual, 6, "0", STR_PAD_LEFT); ?></h5>

                </div>

                <div class="hr">
                    <hr>
                </div>

            </div>



            <div class="form-group">

                <div class="col-sm-1"></div>

                <div class="col-sm-2">

                    <strong>Regional:</strong>

                    <input name="regional" type="text" id="regional" maxlength="250" value="<?= $regional; ?>"
                        class="form-control" required readonly />

                </div>

                <div class="col-sm-2">

                    <strong>Coordinador de prisión:</strong>

                    <select required readonly name="usua_id" id="usua_id" class="form-control">

                        <option value="<?= $id_coordinador; ?>"><?= $coordinador; ?></option>

                    </select>

                </div>

                <div class="col-sm-2">

                    <strong>Fecha del registro:</strong>

                    <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250" value="<?= $fechaReporte; ?>"
                        class="form-control" required readonly />

                </div>

                <div class="col-sm-2">

                    <strong>Período:</strong>

                    <select name="mapeo_cuarto" readonly class="form-control">

                        <?php echo ($mapeo_cuarto == "1") ? '<option value="1" selected >Q1 (Ene - Mar)' : ''; ?>

                        <?php echo ($mapeo_cuarto == "4") ? '<option value="4" selected >Q2 (Abr - Jun)' : ''; ?>

                        <?php echo ($mapeo_cuarto == "7") ? '<option value="7" selected >Q3 (Jul - Sep)' : ''; ?>

                        <?php echo ($mapeo_cuarto == "10") ? '<option value="10" selected >Q4 (Oct - Dic)' : ''; ?></option>

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

                            $sql .= " FROM tbl_regional_ubicacion ";

                            if ($_SESSION['empresa_pd'] != 0) {

                                $sql .= " WHERE reub_reg_fk = " . $_SESSION['empresa_pd'];

                            }

                            $sql .= " ORDER BY reub_reg_fk asc";



                            $PSN1->query($sql);

                            $numero = $PSN1->num_rows();

                            if ($numero > 0) {

                                while ($PSN1->next_record()) {

                                    ?>
                                    <option value="<?= $PSN1->f('reub_id'); ?>" <?php

                                      if ($sitioReunion == $PSN1->f('reub_id')) {

                                          ?>selected="selected" <?php

                                      }

                                      ?>><?= $PSN1->f('reub_nom'); ?></option><?php

                                }

                            }

                        } else {

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

                    <input name="pabellon" type="number" id="pabellon" maxlength="250" value="<?= $pabellon; ?>"
                        class="form-control" required />

                </div>

            </div>

            <div class="cont-tit">

                <div class="hr">
                    <hr>
                </div>

                <div class="tit-cen">

                    <h3>INFORMACIÓN DE LA PRISIÓN</h3>

                </div>

                <div class="hr">
                    <hr>
                </div>

            </div>

            <div class="form-group">

                <div class="col-sm-3">

                    <strong>Total población que hay en la prisión:</strong>

                    <input name="asistencia_total" type="number" id="asistencia_total" value="<?= $asistencia_total; ?>"
                        class="form-control" required />

                </div>

                <div class="col-sm-3">

                    <strong>Número de prisioneros invitados:</strong>

                    <input name="asistencia_hom" type="number" id="asistencia_hom" value="<?= $asistencia_hom; ?>"
                        class="form-control" required />

                </div>

                <div class="col-sm-3">

                    <strong>Número Graduados:</strong>

                    <input name="asistencia_muj" type="number" id="asistencia_muj" value="<?= $asistencia_muj; ?>"
                        class="form-control" required />

                </div>

                <div class="col-sm-3">

                    <strong>Numero de cursos activos de ECOP:</strong>

                    <input name="asistencia_jov" type="number" id="asistencia_jov" value="<?= $asistencia_jov; ?>"
                        class="form-control" readonly />

                </div>

            </div>

            <!--MODIFICAR REGISTRO DE GRADUADOS--->

        <div class="cont-tit">

            <div class="hr">
                <hr>
            </div>

            <div class="tit-cen">

                <h3 class="text-center">MODIFICAR DE GRADUADOS</h3>

                <p>A continuación por favor ingrese los datos requeridos</p>

            </div>

            <div class="hr">
                <hr>
            </div>

        </div>

        <div class="form-group">

            <div class="col-sm-2"></div>

            <div class="col-sm-8">

                <script>

                    $(function () {

                        var total = <?= $asistencia_nin; ?>;

                        var tar = $(".act_grad_tar").val();

                        var nom = $(".act_grad_nom").val();



                        //$("#asistencia_total").prop('required',true);



                        if (tar == "" || nom == "") {

                            $("#adicionarAdd").prop("disabled", true);

                        } else {

                                <?php if ($_SESSION['perfil'] == "168" || $fechLimite > $fechaReporte) { ?>

                                $("#adicionarAdd").prop("disabled", true);

                            <?php } else { ?>

                                $("#adicionarAdd").prop("disabled", false);

                            <?php } ?>

                            }

                        var vtotal = $("#asistencia_total").val();

                        $("#asistencia_hom").attr('max', (vtotal - 1));



                        var vtotal = $("#asistencia_hom").val();

                        $("#asistencia_muj").attr('max', vtotal);



                        $("#asistencia_hom").change(function () {

                            var vtotal = $("#asistencia_hom").val();

                            $("#asistencia_muj").attr('max', vtotal);

                        });

                        $("#asistencia_total").change(function () {

                            var vtotal = $("#asistencia_total").val();

                            $("#asistencia_hom").attr('max', (vtotal - 1));

                        });

                        var totalG = $("#total").val();

                        if (totalG <= 0) {

                            totalG = 0;

                        }

                        $("#rep_ndis").attr('max', 1000);
                        $("#rep_nuevo").attr('max', 64);
                        $("#rep_entr").attr('max', 1000);
                        $("#unidad_2").attr('max', 10000);
                        $("#unidad_3").attr('max', 10000);
                        $("#unidad_4").attr('max', 10000);
                        $("#unidad_5").attr('max', 10000);
                        $("#unidad_6").attr('max', 10000);
                        $("#unidad_total").attr('max', 100000);



                        $("#asistencia_muj").change(function () {

                            var vtotal = $("#asistencia_muj").val();

                            if (total >= vtotal) {

                                $("#adicionarAdd").prop("disabled", true);

                            } else {

                                $("#adicionarAdd").prop("disabled", false);

                            }

                        });



                        $(".act_grad_nom").change(function () {

                            var vtotal = $("#asistencia_muj").val();

                            var tar3 = $(".act_grad_tar").val();

                            var nom3 = $(".act_grad_nom").val();

                            if (tar3 != "" && nom3 != "") {

                                if (total < 1) {

                                    total = total + 1;

                                }

                                $("#adicionarAdd").prop("disabled", false);

                            } else if (tar3 == "" && nom3 == "") {

                                if (total == 1) {

                                    total = total - 1;

                                    $(".act_grad_nom").prop('required', false);

                                    $(".act_grad_tar").prop('required', false);

                                }

                            } else {

                                $("#adicionarAdd").prop("disabled", true);

                            }

                            $('#total').val(total);

                        });

                        $(".act_grad_tar").change(function () {

                            var vtotal = $("#asistencia_muj").val();

                            var nom2 = $(".act_grad_nom").val();

                            var tar2 = $(".act_grad_tar").val();

                            if (nom2 != "" && tar2 != "") {

                                if (total < 1) {

                                    total = total + 1;

                                }

                                $("#adicionarAdd").prop("disabled", false);

                            } else if (tar3 == "" && nom3 == "") {

                                if (total == 1) {

                                    total = total - 1;

                                    $(".act_grad_nom").prop('required', false);

                                    $(".act_grad_tar").prop('required', false);

                                }

                            } else {

                                $("#adicionarAdd").prop("disabled", true);

                            }

                            $('#total').val(total);

                        });



                        $("#adicionarAdd").on('click', function () {

                            $("#tablaAdd tr:last").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");

                            $("#tablaAdd tr input.act_grad_nom:last").val('');

                            $("#tablaAdd tr input.act_grad_tar:last").val('');

                            var vtotal = $("#asistencia_muj").val();

                            var tar2 = $(".act_grad_tar").val();

                            var nom2 = $(".act_grad_nom").val();

                            if (tar2 != "" && nom2 != "") {

                                total = total + 1;

                            }

                            if (total >= vtotal) {

                                $("#adicionarAdd").prop("disabled", true);

                            } else {

                                $("#adicionarAdd").prop("disabled", false);

                            }

                            $(".act_grad_nom").prop('required', true);

                            $(".act_grad_tar").prop('required', true);

                            $('#total').val(total);

                            var totalG = $("#total").val();

                            $("#rep_ndis").attr('max', 1000);
                            $("#rep_nuevo").attr('max', 64);
                            $("#rep_entr").attr('max', 1000);
                            $("#unidad_2").attr('max', 100000);
                            $("#unidad_3").attr('max', 100000);
                            $("#unidad_4").attr('max', 100000);
                            $("#unidad_5").attr('max', 100000);
                            $("#unidad_6").attr('max', 100000);
                            $("#unidad_total").attr('max', 10000000);

                        });

                        $(document).on("click", ".eliminarAdd", function () {

                            var vtotal = $("#asistencia_muj").val();

                            var parent = $(this).parents().get(0);

                            $(parent).remove();

                            total = total - 1;

                            $('#total').val(total);

                            var totalG = $("#total").val();

                            $("#rep_nuevo").attr('max', (64));
                            $("#rep_entr").attr('max', (1000));
                            $("#rep_ndis").attr('max', (1000));

                            $("#unidad_2").attr('max', 100000);
                            $("#unidad_3").attr('max', 100000);
                            $("#unidad_4").attr('max', 100000);
                            $("#unidad_5").attr('max', 100000);
                            $("#unidad_6").attr('max', 100000);
                            $("#unidad_total").attr('max', 10000000);

                            if (total >= vtotal) {

                                $("#adicionarAdd").prop("disabled", true);

                            } else {

                                $("#adicionarAdd").prop("disabled", false);

                            }

                        });



                    });

                </script>

                <table id="tablaAdd">

                    <?php

                    $sql = "SELECT * ";

                    $sql .= " FROM tbl_adjuntos ";

                    $sql .= " WHERE adj_rep_fk = '" . $idReporteActual . "' AND adj_tip = 1 ";

                    $PSN1->query($sql);

                    $numero = $PSN1->num_rows();

                    $cont = 0;

                    echo '<input type="hidden" name="grad_regist" value="' . $numero . '" placeholder="">';

                    if ($numero > 0) {

                        while ($PSN1->next_record()) { ?>

                    <input type="hidden" name="act_grad_id[]" value="<?= $PSN1->f("adj_id"); ?>">

                    <tr <?php echo ($cont == 0) ? 'class="fila-fijaAdd"' : ''; ?>>

                        <td class="col-sm-7">



                            <strong>Nombre completo del graduado linea dos mil:</strong>

                            <input name="act_grad_nom[]" type="text" id="act_grad_nom" class="act_grad_nom form-control"
                                value="<?= $PSN1->f("adj_nom"); ?>" required />

                        </td>

                        <td class="col-sm-4">

                            <strong>Tarjeta dactilar / N° identificación: linea dos mil</strong>

                            <input name="act_grad_tar[]" type="text" id="act_grad_tar" min="0"
                                class="act_grad_tar form-control" value="<?= $PSN1->f("adj_url"); ?>" required />

                        </td>

                        <td class="eliminarAdd"><br><button type="button" class="btn btn-cir-uno usua-col"><i
                                    class="fa fa-times"></i></button></td>

                    </tr>

                    <?php $cont++;

                        }

                    } else { ?>



                    <tr class="fila-fijaAdd">

                        <td class="col-sm-7">

                            <strong>Nombre completo del graduado linea dos mil74:</strong>

                            <input name="act_grad_nom[]" type="text" id="act_grad_nom"
                                class="act_grad_nom form-control" />

                        </td>

                        <td class="col-sm-4">

                            <strong>Tarjeta dactilar / N° identificación: linea dos mil74</strong>

                            <input name="act_grad_tar[]" type="text" id="act_grad_tar" min="0"
                                class="act_grad_tar form-control" />

                        </td>

                        <td class="eliminarAdd"><br><button type="button" class="btn btn-cir-uno usua-col"><i
                                    class="fa fa-times"></i></button></td>

                    </tr>

                    <?php } ?>

                </table>

            </div>

            <div class="col-sm-2"></div>

        </div>

        <div class="form-group">

            <div class="col-sm-2"></div>

            <div class="col-sm-8">

                <div class="col-sm-4"><strong>Número de graduados en ECOP en la prisión:</strong> </div>

                <div class="col-sm-2">

                    <input type="text" name="total" id="total" class="form-control" value="<?= $asistencia_nin; ?>"
                        readonly>

                </div>

                <div class="col-sm-4"></div>

                <div class="col-sm-2">

                    <center>

                        <button id="adicionarAdd" class="btn btn-success" type="button" class="boton" <?php echo ($_SESSION['perfil'] == "168" || $fechLimite > $fechaReporte) ? 'disabled="disabled"' : ''; ?>><i class="fas fa-plus"></i> Adicionar</button>

                    </center>

                </div>

            </div>

        </div>

        <!--MODIFICAR REGISTRO DE VOLUNTARIOS INTERNOS-->

            <div class="cont-tit">

                <div class="hr">
                    <hr>
                </div>

                <div class="tit-cen">

                    <h3 class="text-center">MODIFICAR DE VOLUNTARIOS INTERNOS</h3>

                    <p>A continuación por favor ingrese los datos requeridos</p>

                </div>

                <div class="hr">
                    <hr>
                </div>

            </div>

            <div class="form-group">

                <div class="col-sm-2"></div>

                <div class="col-sm-8">

                    <script>

                        $(function () {

                            var total = <?= $bautizados; ?>;

                            var tar = $(".act_vin_tar").val();

                            var nom = $(".act_vin_nom").val();

                            if (tar == "" || nom == "") {

                                $("#adicionarAdd2").prop("disabled", true);

                            } else {

                                <?php if ($_SESSION['perfil'] == "168" || $fechLimite > $fechaReporte) { ?>

                                    $("#adicionarAdd2").prop("disabled", true);

                                <?php } else { ?>

                                    $("#adicionarAdd2").prop("disabled", false);

                                <?php } ?>

                            }

                            $(".act_vin_nom").change(function () {

                                var tar3 = $(".act_vin_tar").val();

                                var nom3 = $(".act_vin_nom").val();

                                if (tar3 != "" && nom3 != "") {

                                    if (total < 1) {

                                        total = total + 1;

                                    }

                                    $("#adicionarAdd2").prop("disabled", false);

                                } else if (tar3 == "" && nom3 == "") {

                                    if (total == 1) {

                                        total = total - 1;

                                        $(".act_vin_nom").prop('required', false);

                                        $(".act_vin_tar").prop('required', false);

                                    }

                                } else {

                                    $("#adicionarAdd2").prop("disabled", true);

                                }

                                $('#total2').val(total);

                            });

                            $(".act_vin_tar").change(function () {

                                var nom2 = $(".act_vin_nom").val();

                                var tar2 = $(".act_vin_tar").val();

                                if (nom2 != "" && tar2 != "") {

                                    if (total < 1) {

                                        total = total + 1;

                                    }

                                    $("#adicionarAdd2").prop("disabled", false);

                                } else if (tar2 == "" && nom2 == "") {

                                    if (total == 1) {

                                        total = total - 1;

                                        $(".act_vin_nom").prop('required', false);

                                        $(".act_vin_tar").prop('required', false);

                                    }

                                } else {

                                    $("#adicionarAdd2").prop("disabled", true);

                                }

                                $('#total2').val(total);

                            });



                            $("#adicionarAdd2").on('click', function () {

                                $("#tablaAdd2 tr:last").clone().removeClass('fila-fijaAdd2').appendTo("#tablaAdd2");

                                $("#tablaAdd2 tr input.act_vin_nom:last").val('');

                                $("#tablaAdd2 tr input.act_vin_tar:last").val('');

                                var tar2 = $(".act_vin_tar").val();

                                var nom2 = $(".act_vin_nom").val();

                                if (tar2 != "" && nom2 != "") {

                                    total = total + 1;

                                }

                                $(".act_vin_nom").prop('required', true);

                                $(".act_vin_tar").prop('required', true);

                                $('#total2').val(total);

                            });

                            $(document).on("click", ".eliminarAdd2", function () {

                                var parent = $(this).parents().get(0);

                                $(parent).remove();

                                total = total - 1;

                                $('#total2').val(total);

                            });



                        });

                    </script>

                    <table id="tablaAdd2">

                        <?php

                        $sql = "SELECT * ";

                        $sql .= " FROM tbl_adjuntos ";

                        $sql .= " WHERE adj_rep_fk = '" . $idReporteActual . "' AND adj_tip = 2 ";

                        $PSN1->query($sql);

                        $numero = $PSN1->num_rows();

                        $cont = 0;

                        echo '<input type="hidden" name="vin_regist" value="' . $numero . '" placeholder="">';

                        if ($numero > 0) {

                            while ($PSN1->next_record()) { ?>

                                <input type="hidden" name="act_vin_id[]" value="<?= $PSN1->f("adj_id"); ?>">

                                <tr <?php echo ($cont == 0) ? 'class="fila-fijaAdd2"' : ''; ?>>

                                    <td class="col-sm-7">

                                        <strong>Nombre completo del siervo facilitador:</strong>

                                        <input name="act_vin_nom[]" type="text" id="act_vin_nom" class="act_vin_nom form-control"
                                            value="<?= $PSN1->f("adj_nom"); ?>" required />

                                    </td>

                                    <td class="col-sm-4">

                                        <strong>Tarjeta dactilar / N° identificación:</strong>

                                        <input name="act_vin_tar[]" type="text" id="act_vin_tar" min="0"
                                            class="act_vin_tar form-control" value="<?= $PSN1->f("adj_url"); ?>" required />

                                    </td>

                                    <td class="eliminarAdd2"><br><button type="button" class="btn btn-cir-uno usua-col"><i
                                                class="fa fa-times"></i></button></td>

                                </tr>

                                <?php $cont++;

                            }

                        } else { ?>

                            <tr class="fila-fijaAdd2">

                                <td class="col-sm-7">

                                    <strong>Nombre completo del siervo facilitador:</strong>

                                    <input name="act_vin_nom[]" type="text" id="act_vin_nom" class="act_vin_nom form-control" />

                                </td>

                                <td class="col-sm-4">

                                    <strong>Tarjeta dactilar / N° identificación:</strong>

                                    <input name="act_vin_tar[]" type="text" id="act_vin_tar" min="0"
                                        class="act_vin_tar form-control" />

                                </td>

                                <td class="eliminarAdd2"><br><button type="button" class="btn btn-cir-uno usua-col"><i
                                            class="fa fa-times"></i></button></td>

                            </tr>

                        <?php } ?>

                    </table>

                </div>

                <div class="col-sm-2"></div>

            </div>

            <div class="form-group">

                <div class="col-sm-2"></div>

                <div class="col-sm-8">

                    <div class="col-sm-4"><strong>Número de voluntarios internos activos en esta prisión:</strong> </div>

                    <div class="col-sm-2">

                        <input type="text" name="total2" id="total2" class="form-control" value="<?= $bautizados; ?>"
                            readonly>

                    </div>

                    <div class="col-sm-4"></div>

                    <div class="col-sm-2">

                        <center>

                            <button id="adicionarAdd2" class="btn btn-success" type="button" class="boton"
                                <?= ($_SESSION['perfil'] == "168" || $fechLimite > $fechaReporte) ? 'disabled="disabled"' : ''; ?>><i class="fas fa-plus"></i> Adicionar</button>

                        </center>

                    </div>

                </div>

            </div>

            <!--MODIFICAR REGISTRO DE VOLUNTARIOS EXTERNOS-->

            <div class="cont-tit">

                <div class="hr">
                    <hr>
                </div>

                <div class="tit-cen">

                    <h3 class="text-center">MODIFICAR DE VOLUNTARIOS EXTERNOS</h3>

                    <p>A continuación por favor ingrese los datos requeridos</p>

                </div>

                <div class="hr">
                    <hr>
                </div>

            </div>

            <div class="form-group">

                <div class="col-sm-2"></div>

                <div class="col-sm-8">

                    <script>

                        $(function () {

                            var total = <?= $desiciones; ?>;

                            var tar = $(".act_vex_tar").val();

                            var nom = $(".act_vex_nom").val();

                            if (tar == "" || nom == "") {

                                $("#adicionarAdd3").prop("disabled", true);

                            } else {

                                <?php if ($_SESSION['perfil'] == "168" || $fechLimite > $fechaReporte) { ?>

                                    $("#adicionarAdd3").prop("disabled", true);

                                <?php } else { ?>

                                    $("#adicionarAdd3").prop("disabled", false);

                                <?php } ?>

                            }

                            $(".act_vex_nom").change(function () {

                                var tar3 = $(".act_vex_tar").val();

                                var nom3 = $(".act_vex_nom").val();

                                if (tar3 != "" && nom3 != "") {

                                    if (total < 1) {

                                        total = total + 1;

                                    }

                                    $("#adicionarAdd3").prop("disabled", false);

                                } else if (tar3 == "" && nom3 == "") {

                                    if (total == 1) {

                                        total = total - 1;

                                        $(".act_vex_nom").prop('required', false);

                                        $(".act_vex_tar").prop('required', false);

                                    }

                                } else {

                                    $("#adicionarAdd3").prop("disabled", true);

                                }

                                $('#total3').val(total);

                            });

                            $(".act_vex_tar").change(function () {

                                var nom2 = $(".act_vex_nom").val();

                                var tar2 = $(".act_vex_tar").val();

                                if (nom2 != "" && tar2 != "") {

                                    if (total < 1) {

                                        total = total + 1;

                                    }

                                    $("#adicionarAdd3").prop("disabled", false);

                                } else if (tar2 == "" && nom2 == "") {

                                    if (total == 1) {

                                        total = total - 1;

                                        $(".act_vex_nom").prop('required', false);

                                        $(".act_vex_tar").prop('required', false);

                                    }

                                } else {

                                    $("#adicionarAdd3").prop("disabled", true);

                                }

                                $('#total3').val(total);

                            });



                            $("#adicionarAdd3").on('click', function () {

                                $("#tablaAdd3 tr:last").clone().removeClass('fila-fijaAdd3').appendTo("#tablaAdd3");

                                $("#tablaAdd3 tr input.act_vex_nom:last").val('');

                                $("#tablaAdd3 tr input.act_vex_tar:last").val('');

                                var tar2 = $(".act_vex_tar").val();

                                var nom2 = $(".act_vex_nom").val();

                                if (tar2 != "" && nom2 != "") {

                                    total = total + 1;

                                }

                                $(".act_vex_nom").prop('required', true);

                                $(".act_vex_tar").prop('required', true);

                                $('#total3').val(total);

                            });

                            $(document).on("click", ".eliminarAdd3", function () {

                                var parent = $(this).parents().get(0);

                                $(parent).remove();

                                total = total - 1;

                                $('#total3').val(total);

                            });



                        });

                    </script>

                    <table id="tablaAdd3">

                        <?php

                        $sql = "SELECT * ";

                        $sql .= " FROM tbl_adjuntos ";

                        $sql .= " WHERE adj_rep_fk = '" . $idReporteActual . "' AND adj_tip = 3 ";

                        $PSN1->query($sql);

                        $numero = $PSN1->num_rows();

                        $cont = 0;

                        echo '<input type="hidden" name="vex_regist" value="' . $numero . '" placeholder="">';

                        if ($numero > 0) {

                            while ($PSN1->next_record()) { ?>

                                <input type="hidden" name="act_vex_id[]" value="<?= $PSN1->f("adj_id"); ?>">

                                <tr <?php echo ($cont == 0) ? 'class="fila-fijaAdd3"' : ''; ?>>

                                    <td class="col-sm-7">

                                        <strong>Nombre completo del entrenador:</strong>

                                        <input name="act_vex_nom[]" type="text" id="act_vex_nom" class="act_vex_nom form-control"
                                            value="<?= $PSN1->f("adj_nom"); ?>" required />

                                    </td>

                                    <td class="col-sm-4">

                                        <strong>N° identificación:</strong>

                                        <input name="act_vex_tar[]" type="text" id="act_vex_tar" min="0"
                                            class="act_vex_tar form-control" value="<?= $PSN1->f("adj_url"); ?>" required />

                                    </td>

                                    <td class="eliminarAdd3"><br><button type="button" class="btn btn-cir-uno usua-col"><i
                                                class="fa fa-times"></i></button></td>

                                </tr>

                                <?php $cont++;

                            }

                        } else { ?>

                            <tr class="fila-fijaAdd3">

                                <td class="col-sm-7">

                                    <strong>Nombre completo del entrenador:</strong>

                                    <input name="act_vex_nom[]" type="text" id="act_vex_nom" class="act_vex_nom form-control" />

                                </td>

                                <td class="col-sm-4">

                                    <strong>N° identificación:</strong>

                                    <input name="act_vex_tar[]" type="text" id="act_vex_tar" min="0"
                                        class="act_vex_tar form-control" />

                                </td>

                                <td class="eliminarAdd3"><br><button type="button" class="btn btn-cir-uno usua-col"><i
                                            class="fa fa-times"></i></button></td>

                            </tr>

                        <?php } ?>

                    </table>

                </div>

                <div class="col-sm-2"></div>

            </div>

            <div class="form-group">

                <div class="col-sm-2"></div>

                <div class="col-sm-8">

                    <div class="col-sm-4"><strong>Número de voluntarios externos para esta prisión:</strong> </div>

                    <div class="col-sm-2">

                        <input type="text" name="total3" id="total3" class="form-control" value="<?= $desiciones; ?>"
                            readonly>

                    </div>

                    <div class="col-sm-4"></div>

                    <div class="col-sm-2">

                        <center>

                            <button id="adicionarAdd3" class="btn btn-success" type="button" class="boton"><i
                                    class="fas fa-plus"></i> Adicionar</button>

                        </center>

                    </div>

                </div>

            </div>

            <div class="cont-tit">

                <div class="hr">
                    <hr>
                </div>

                <div class="tit-cen">

                    <h3 class="text-center">CONTINUIDAD DE FORMACIÓN</h3>

                </div>

                <div class="hr">
                    <hr>
                </div>

            </div>

            <div class="form-group">

                <div class="form-group">

                    <div class="col-sm-2"></div>

                    <div class="col-sm-3">

                        <strong>Número de discípulos que pasaron a C&M:</strong>

                        <input name="rep_ndis" type="number" id="rep_ndis" maxlength="255" value="<?= $rep_ndis; ?>"
                            class="form-control" required />

                    </div>

                    <div class="col-sm-3">

                        <strong>Testimonio:</strong>

                        <?php if ($ext2 != "") { ?>

                            <a href='archivos/evi_<?= $idReporteActual; ?>_2.<?= $ext2; ?>' target="_blank"><i
                                    class="fas fa-file-word"></i> Formato testimonio ECOP</a>

                        <?php } ?>

                        <input name="archivo2" type="file" id="archivo2" class="form-control" />

                    </div>

                    <div class="col-sm-2">

                        <strong>Costo de recursos gestionados($):</strong>

                        <input name="rep_entr" type="number" id="rep_entr" min="0" value="<?= $rep_entr; ?>"
                            class="form-control" />

                    </div>

                </div>

                <div class="cont-tit">

                    <div class="hr">
                        <hr>
                    </div>

                    <div class="tit-cen">

                        <h3 class="text-center">Método de verificación</h3>

                        <h5>FOTO</h5>

                    </div>

                    <div class="hr">
                        <hr>
                    </div>

                </div>

                <div class="form-group">

                    <div class="col-sm-3"></div>

                    <div class="col-sm-6">

                        <?php if ($ext1 != "") { ?>

                            <center><img src="archivos/evi_<?= $idReporteActual; ?>_1.<?= $ext1; ?>"
                                    style="max-height:250px; max-width: 100%; "></center><br>

                        <?php } ?>

                        <input name="archivo1" type="file" id="archivo1" class="form-control" />

                    </div>

                    <div class="col-sm-3"></div>

                </div>

                <?php if ($_SESSION['perfil'] != "168") { ?>

                    <div class="cont-btn cont-flex fl-sbet">

                        <div class="item-btn">

                            <input type="button" onClick="window.location.href='index.php?doc=consultar-sub-programa-ecop'"
                                name="previous" class="previous btn btn-info" value="Cerrar" />

                        </div>

                        <div class="item-btn">

                            <input type="submit" name="button" value="Guardar cambios" class="btn btn-success" id="guarda_rep">

                        </div>

                        <div class="item-btn">

                            <input type="button" onClick="eliminarRegistro()" name="button" value="Eliminar"
                                class="btn btn-danger">

                        </div>

                    </div>

                <?php } ?>

                <input type="hidden" name="funcion" id="funcion" value="actualizar" />

                <input type="hidden" name="generacion" id="generacion" value="<?= $generacionActual; ?>" />

        </form>

        <script language="javascript">

            function sumar() {

                var asistencia_hom = 0;

                var asistencia_muj = 0;

                var asistencia_jov = 0;

                var asistencia_nin = 0;



                if (document.getElementById("final_asistencia_hom").value != "") {

                    var asistencia_hom = document.getElementById("final_asistencia_hom").value;

                }

                if (document.getElementById("final_asistencia_muj").value != "") {

                    var asistencia_muj = document.getElementById("final_asistencia_muj").value;

                }

                if (document.getElementById("final_asistencia_jov").value != "") {

                    var asistencia_jov = document.getElementById("final_asistencia_jov").value;

                }

                if (document.getElementById("final_asistencia_nin").value != "") {

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



            function eliminarRegistro() {

                if (confirm("Esta seguro que desea eliminar este registro, esta acción NO se puede deshacer.")) {

                    document.getElementById('funcion').value = "eliminar";

                    document.getElementById('form1').submit();

                }

            }



            function generarForm(generacion) {

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



            function init() {

                document.getElementById('form1').onsubmit = function () {

                    return generarForm();

                }



            }

            //

            window.onload = function () {

                init();

            }

        </script>

    <?php } else if ($preguntarGeneracion == 1) { ?>

            <script language="javascript">

                generarForm('ECOP');

                function generarForm(generacion) {

                    if (generacion == "ECOP") {

                        document.getElementById('generacion').value = "ECOP";

                    }

                    //Completo el formulario  

                    document.getElementById('form1').submit();

                }



                function init() {

                    document.getElementById('form1').onsubmit = function () {

                        return generarForm();

                    }

                }

                window.onload = function () {

                    init();

                }

            </script>

    <?php } else if (!isset($_REQUEST["id"])) {

    $temp_accionForm = "insertar";

    $idGrupoMadre = soloNumeros($_REQUEST["idGrupoMadre"]);

    //

    if (!isset($_REQUEST["fechaReporte"])) {

        $fechaReporte = date("Y-m-d");

    } else {

        $fechaReporte = eliminarInvalidos($_REQUEST["fechaReporte"]);

    }

    //

    $sql = "SELECT sat_grupos.nombre ";

    $sql .= " FROM sat_grupos ";

    $sql .= " WHERE sat_grupos.id = '" . $idGrupoMadre . "'";

    $sql .= " GROUP BY sat_grupos.id";

    $PSN1->query($sql);

    if ($PSN1->num_rows() > 0) {

        if ($PSN1->next_record()) {

            $nombreGrupoMadre = $PSN1->f("nombre");

        }//chequear el registro

    }//chequear el numero

} else {

    $temp_accionForm = "actualizar";

    //  ID del usuario actual

    $idReporteActual = soloNumeros($_REQUEST["id"]);

}



if ($idReporteActual > 0) {

    //No hacemos nada.

} else if ($varExitoREP == 1) { ?>

            <div class="container">

                <div class="row">

                    <h2 class="alert alert-info text-center"><?php

                    if ($idReporteActual == 0) {

                        echo "REPORTE";

                    } else {

                        echo "ACTUALIZACIÓN";

                    }

                    ?> DE <?= $temp_letrero; ?></h2>

                </div>



                <div class="row">

                    <h2 class="alert alert-success text-center"><a
                            href="index.php?doc=gestionar-sub-programa-ecop&opc=2&id=<?= $ultimoId; ?>" class="h2">Se ha <?php

                              if ($idReporteActual == 0) {

                                  echo "creado";

                              } else {

                                  echo "actualizado";

                              }

                              ?> correctamente el registro, para ver el reporte de clic aquí</a>.</h2>

                </div>

            </div>

    <?php } else if ($idReporteActual == 0) { ?>

                <style type="text/css">
                    #form1 fieldset:not(:first-of-type) {

                        display: none;

                    }
                </style>

                <div class="container">

                    <div class="row">

                        <h3 class="alert alert-info text-center"><?php

                        if ($idReporteActual == 0) {

                            echo "REPORTE";

                        } else {

                            echo "ACTUALIZACIÓN";

                        } ?> DE <?= $temp_letrero; ?></h3>

                    </div>



                <?php


                if ($varExitoREP_UPD == 1) {

                    ?>
                        <div class="row">

                            <h5 class="alert alert-warning text-center">Se ha actualizado correctamente el registro.</h5>

                        </div><?php

                }


                if ($texto_error != "") {

                    ?>
                        <div class="row">

                            <h5 class="alert alert-danger text-center"><?= $texto_error; ?></h5>

                        </div><?php

                }




                if ($errorLogueo == 1) {

                    ?>
                        <div class="row">
                            <h1>
                                <font color="red"><u>ATENCION:</u> NO SE CREO EL INFORME<BR /><u>MOTIVO:</u> YA EXISTE UN INFORME CON
                                    ESE VEHÍCULO Y FECHA.<br />POR FAVOR VERIFIQUE.</font>
                            </h1>
                        </div><?php

                }



                if ($error_fatal == 1) {

                } else { ?>



                        <form method="post" enctype="multipart/form-data" name="form1" id="form1" class="form-horizontal">

                            <input name="fechaReporte" type="hidden" id="fechaReporte" value="<?= $fechaReporte; ?>" />

                            <!--<fieldset>-->
                            <div class="col-sm-12">

                                <div class="cont-tit">

                                    <div class="hr">
                                        <hr>
                                    </div>

                                    <div class="tit-cen">

                                        <h3 class="text-center">Información general</h3>

                                        <p>A continuación por favor ingrese los datos requeridos</p>

                                    </div>

                                    <div class="hr">
                                        <hr>
                                    </div>

                                </div>

                                <div class="form-group">

                                    <div class="col-sm-1"></div>

                                    <div class="col-sm-2">

                                        <strong>Fecha del registro:</strong>

                                        <input name="fechaReporte" type="date" id="fechaReporte" maxlength="250"
                                            value="<?= date("Y-m-d"); ?>" max='<?= date("Y-m-d"); ?>' class="form-control" required
                                            readonly />

                                    </div>

                                    <div class="col-sm-2">

                                <?php $mes = date("m"); ?>

                                        <strong>Período:</strong>

                                        <select name="mapeo_cuarto" class="form-control">

                                    <?php echo ($mes >= 1 && $mes <= 3) ? '<option value="1" selected >Q1 (Ene - Mar)</option>' : ''; ?>

                                    <?php echo ($mes >= 4 && $mes <= 6) ? '<option value="4" selected >Q2 (Abr - Jun)</option>' : ''; ?>

                                    <?php echo ($mes >= 7 && $mes <= 9) ? '<option value="7" selected >Q3 (Jul - Sep)</option>' : ''; ?>

                                    <?php echo ($mes >= 10 && $mes <= 12) ? '<option value="10" selected >Q4 (Oct - Dic)</option>' : ''; ?>

                                        </select>

                                    </div>

                                    <div class="col-sm-3">

                                        <strong>Coordinador de prisión:</strong>

                                        <select required name="usua_id" id="usua_id" class="form-control">

                                            <option value="<?= $_SESSION["id"]; ?>"><?= $_SESSION["nombre"]; ?></option>

                                        </select>

                                    </div>

                                    <div class="col-sm-2">

                                        <strong>Cárcel ubicación:</strong>

                                        <select required name="sitioReunion" id="rep_carcel" class="form-control">



                                        <?php

                                        /*

                                        *   TRAEMOS LOS TIPOS DE SERVICIOS QUE PRESTA (25)

                                        */

                                        if ($_SESSION['empresa_pd'] != "") {

                                            echo '<option value="">Sin especificar</option>';

                                            $sql = "SELECT * ";

                                            $sql .= " FROM tbl_regional_ubicacion ";

                                            if ($_SESSION['empresa_pd'] != 0) {

                                                $sql .= " WHERE reub_reg_fk = " . $_SESSION['empresa_pd'];

                                            }

                                            $sql .= " ORDER BY reub_reg_fk asc";



                                            $PSN1->query($sql);

                                            $numero = $PSN1->num_rows();

                                            if ($numero > 0) {

                                                while ($PSN1->next_record()) {

                                                    ?>
                                                        <option value="<?= $PSN1->f('reub_id'); ?>" <?php

                                                          if ($cliente_servicio1 == $PSN1->f('reub_id')) {

                                                              ?>selected="selected" <?php

                                                          }

                                                          ?>>
                                                    <?= $PSN1->f('reub_nom'); ?>
                                                        </option><?php

                                                }

                                            }

                                        } else {

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

                                        <input name="pabellon" type="number" id="pabellon" maxlength="250" value="<?= $pabellon; ?>"
                                            class="form-control" required />

                                    </div>

                                </div>

                                <!--<div class="cont-btn cont-flex fl-sbet">

                    <div class="item-btn"></div>

                    <div class="item-btn">

                        <input type="button" name="next" class="next btn btn-success" id="secc-1" value="Siguiente" />

                    </div>

                </div>          

            </fieldset>-->
                            </div>

                            <!--INFORMACIÓN DE LA PRISION-->

                            <!--<fieldset>-->
                            <div class="col-sm-12">



                                <div class="cont-tit">

                                    <div class="hr">
                                        <hr>
                                    </div>

                                    <div class="tit-cen">

                                        <h3 class="text-center">Información de la prisión</h3>

                                        <p>A continuación por favor ingrese los datos requeridos</p>

                                    </div>

                                    <div class="hr">
                                        <hr>
                                    </div>

                                </div>

                                <div class="form-group">

                                    <div class="col-sm-1"></div>

                                    <div class="col-sm-3">

                                        <strong>Total población que hay en la prisión:</strong>

                                        <input name="asistencia_total" type="number" id="asistencia_total" min="0" value=""
                                            class="form-control" />

                                    </div>

                                    <div class="col-sm-2">

                                        <strong>Número de prisioneros invitados:</strong>

                                        <input name="asistencia_hom" type="number" id="asistencia_hom" min="0" value=""
                                            class="form-control" />

                                    </div>

                                    <div class="col-sm-3">

                                        <strong>Número de personas formadas:</strong>

                                        <input name="asistencia_muj" type="number" id="asistencia_muj" min="0" value=""
                                            class="form-control" />

                                    </div>

                                    <div class="col-sm-2">

                                        <strong>Numero de cursos activos de ECOP: </strong>

                                        <input name="asistencia_jov" type="number" id="asistencia_jov" min="0" value="" readonly
                                            class="form-control" />

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

            </fieldset>-->
                            </div>

                            <!--REGISTRO DE GRADUADOS--->

            <!--<fieldset>-->
                            <div class="col-sm-12">

                                <div class="cont-tit">

                                    <div class="hr">
                                        <hr>
                                    </div>

                                    <div class="tit-cen">

                                        <h3 class="text-center">REGISTRO DE GRADUADOS</h3>

                                        <p>A continuación por favor ingrese los datos requeridos</p>

                                    </div>

                                    <div class="hr">
                                        <hr>
                                    </div>

                                </div>

                                <div class="form-group">

                                    <div class="col-sm-2"></div>

                                    <div class="col-sm-8">

                                        <script>

                                            $(function () {

                                                var total = 0;

                                                var tar = $(".act_grad_tar").val();

                                                var nom = $(".act_grad_nom").val();

                                                $("#adicionarAdd").prop("disabled", true);

                                                //$("#asistencia_total").prop('required',true);



                                                if (tar == "" || nom == "") {

                                                    $("#adicionarAdd").prop("disabled", true);

                                                } else {

                                                    $("#adicionarAdd").prop("disabled", false);

                                                }



                                                $("#asistencia_hom").change(function () {

                                                    var vtotal = $("#asistencia_hom").val();

                                                    $("#asistencia_muj").attr('max', vtotal);

                                                });

                                                $("#asistencia_total").change(function () {

                                                    var vtotal = $("#asistencia_total").val();

                                                    $("#asistencia_hom").attr('max', (vtotal - 1));

                                                });



                                                $("#asistencia_muj").change(function () {

                                                    var vtotal = $("#asistencia_muj").val();

                                                    if (total >= vtotal) {

                                                        $("#adicionarAdd").prop("disabled", true);

                                                    } else {

                                                        $("#adicionarAdd").prop("disabled", false);

                                                    }

                                                });



                                                $(".act_grad_nom").change(function () {

                                                    var vtotal = $("#asistencia_muj").val();

                                                    var tar3 = $(".act_grad_tar").val();

                                                    var nom3 = $(".act_grad_nom").val();

                                                    if (tar3 != "" && nom3 != "") {

                                                        if (total < 1) {

                                                            total = total + 1;

                                                        }

                                                        $("#adicionarAdd").prop("disabled", false);

                                                    } else if (tar3 == "" && nom3 == "") {

                                                        if (total == 1) {

                                                            total = total - 1;

                                                            $(".act_grad_nom").prop('required', false);

                                                            $(".act_grad_tar").prop('required', false);

                                                        }

                                                    } else {

                                                        $("#adicionarAdd").prop("disabled", true);

                                                    }

                                                    $('#total').val(total);

                                                });

                                                $(".act_grad_tar").change(function () {

                                                    var vtotal = $("#asistencia_muj").val();

                                                    var nom2 = $(".act_grad_nom").val();

                                                    var tar2 = $(".act_grad_tar").val();

                                                    if (nom2 != "" && tar2 != "") {

                                                        if (total < 1) {

                                                            total = total + 1;

                                                        }

                                                        $("#adicionarAdd").prop("disabled", false);

                                                    } else if (tar3 == "" && nom3 == "") {

                                                        if (total == 1) {

                                                            total = total - 1;

                                                            $(".act_grad_nom").prop('required', false);

                                                            $(".act_grad_tar").prop('required', false);

                                                        }

                                                    } else {

                                                        $("#adicionarAdd").prop("disabled", true);

                                                    }

                                                    $('#total').val(total);

                                                });



                                                $("#adicionarAdd").on('click', function () {

                                                    var vtotal = $("#asistencia_muj").val();

                                                    var tar2 = $(".act_grad_tar").val();

                                                    var nom2 = $(".act_grad_nom").val();

                                                    if (tar2 != "" && nom2 != "") {

                                                        $("#tablaAdd tr:last").clone().removeClass('fila-fijaAdd').appendTo("#tablaAdd");

                                                        $("#tablaAdd tr input.act_grad_nom:last").val('');

                                                        $("#tablaAdd tr input.act_grad_tar:last").val('');

                                                        total = total + 1;

                                                    } else {

                                                        alert("Ingrese toda la información antes de agregar otro campo");

                                                    }



                                                    if (total >= vtotal) {

                                                        $("#adicionarAdd").prop("disabled", true);

                                                    } else {

                                                        $("#adicionarAdd").prop("disabled", false);

                                                    }

                                                    $(".act_grad_nom").prop('required', true);

                                                    $(".act_grad_tar").prop('required', true);

                                                    $('#total').val(total);

                                                    var totalG = $("#total").val();

                                                    $("#rep_nuevo").attr('max', 64);
                                                    $("#rep_entr").attr('max', 1000);
                                                    $("#rep_ndis").attr('max', 1000);
                                                    $("#unidad_2").attr('max', 100000);
                                                    $("#unidad_3").attr('max', 100000);
                                                    $("#unidad_4").attr('max', 100000);
                                                    $("#unidad_5").attr('max', 100000);
                                                    $("#unidad_6").attr('max', 100000);
                                                    $("#unidad_total").attr('max', 10000000);

                                                });

                                                $(document).on("click", ".eliminarAdd", function () {

                                                    var vtotal = $("#asistencia_muj").val();

                                                    var parent = $(this).parents().get(0);

                                                    $(parent).remove();

                                                    total = total - 1;

                                                    $('#total').val(total);

                                                    var totalG = $("#total").val();

                                                    $("#rep_nuevo").attr('max', (64));
                                                    $("#rep_entr").attr('max', (1000));
                                                    $("#rep_ndis").attr('max', (1000));
                                                    $("#unidad_2").attr('max', 100000);
                                                    $("#unidad_3").attr('max', 100000);
                                                    $("#unidad_4").attr('max', 100000);
                                                    $("#unidad_5").attr('max', 100000);
                                                    $("#unidad_6").attr('max', 100000);
                                                    $("#unidad_total").attr('max', 10000000);

                                                    if (total >= vtotal) {

                                                        $("#adicionarAdd").prop("disabled", true);

                                                    } else {

                                                        $("#adicionarAdd").prop("disabled", false);

                                                    }

                                                });



                                            });

                                        </script>
                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <table id="tablaAdd" class="table table-bordered">
                                                    <tbody>
                                                        <tr class="fila-fijaAdd">
                                                            <td style="width: 58%;">
                                                                <strong>Nombre completo del graduado:</strong>
                                                                <input name="act_grad_nom[]" type="text"
                                                                    class="act_grad_nom form-control" />
                                                            </td>

                                                            <td style="width: 32%;">
                                                                <strong>Tarjeta dactilar / N° identificación:</strong>
                                                                <input name="act_grad_tar[]" type="text"
                                                                    class="act_grad_tar form-control" />
                                                            </td>

                                                            <td style="width: 10%; text-align: center; vertical-align: middle;">
                                                                <button type="button" class="btn btn-danger btn-eliminar-fila"
                                                                    title="Eliminar">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-12">
                                                <div class="row" style="margin-bottom: 15px;">
                                                    <div class="col-sm-6">
                                                        <label for="cantidadAdd" style="font-weight: bold; margin-top: 8px;">
                                                            ¿Cuántos registros desea realizar?
                                                        </label>
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <input type="number" id="cantidadAdd" class="form-control" min="1"
                                                            placeholder="Ej: 5">
                                                    </div>

                                                    <div class="col-sm-3">
                                                        <button id="generarVariasAdd" class="btn btn-primary btn-block" type="button">
                                                            <i class="fa fa-list"></i> Generar
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="row" style="margin-bottom: 15px;">
                                                    <div class="col-sm-6">
                                                        <strong>Número de graduados en ECOP en la prisión:</strong>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <input type="text" name="total" id="total" class="form-control" value="0"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-sm-3" style="margin-bottom: 10px;">
                                                        <button id="adicionarAdd" class="btn btn-success btn-block" type="button">
                                                            <i class="fa fa-plus"></i> Adicionar
                                                        </button>
                                                    </div>

                                                    <div class="col-sm-3" style="margin-bottom: 10px;">
                                                        <button id="borrarTodoAdd" class="btn btn-danger btn-block" type="button">
                                                            <i class="fa fa-trash"></i> Borrar todo
                                                        </button>
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

                                            .form-group .col-sm-12 {
                                                width: 100%;
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
                                            $(document).ready(function () {

                                                var STORAGE_KEY = 'graduados_temp';

                                                function crearFila() {
                                                    return `
            <tr class="fila-fijaAdd">
                <td style="width: 58%;">
                    <strong>Nombre completo del graduado:</strong>
                    <input name="act_grad_nom[]" type="text" class="act_grad_nom form-control" />
                </td>

                <td style="width: 32%;">
                    <strong>Tarjeta dactilar / N° identificación:</strong>
                    <input name="act_grad_tar[]" type="text" class="act_grad_tar form-control" />
                </td>

                <td style="width: 10%; text-align: center; vertical-align: middle;">
                    <button type="button" class="btn btn-danger btn-eliminar-fila" title="Eliminar">
                        <i class="fa fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
                                                }

                                                function obtenerValoresFila($fila) {
                                                    var nombre = $.trim($fila.find('.act_grad_nom').val());
                                                    var tarjeta = $.trim($fila.find('.act_grad_tar').val());

                                                    return {
                                                        nombre: nombre,
                                                        tarjeta: tarjeta
                                                    };
                                                }

                                                function filaCompleta($fila) {
                                                    var datos = obtenerValoresFila($fila);
                                                    return datos.nombre !== '' && datos.tarjeta !== '';
                                                }

                                                function filaIncompleta($fila) {
                                                    var datos = obtenerValoresFila($fila);
                                                    return (datos.nombre !== '' && datos.tarjeta === '') ||
                                                        (datos.nombre === '' && datos.tarjeta !== '');
                                                }

                                                function actualizarTotal() {
                                                    var totalCompletos = 0;

                                                    $('#tablaAdd tbody tr').each(function () {
                                                        if (filaCompleta($(this))) {
                                                            totalCompletos++;
                                                        }
                                                    });

                                                    $('#total').val(totalCompletos);
                                                }

                                                function asegurarMinimoUnaFila() {
                                                    if ($('#tablaAdd tbody tr').length === 0) {
                                                        $('#tablaAdd tbody').append(crearFila());
                                                    }
                                                }

                                                function guardarDatos() {
                                                    var datos = [];

                                                    $('#tablaAdd tbody tr').each(function () {
                                                        datos.push({
                                                            nombre: $(this).find('.act_grad_nom').val() || '',
                                                            tarjeta: $(this).find('.act_grad_tar').val() || ''
                                                        });
                                                    });

                                                    localStorage.setItem(STORAGE_KEY, JSON.stringify(datos));
                                                }

                                                function restaurarDatos() {
                                                    var datosGuardados = localStorage.getItem(STORAGE_KEY);

                                                    $('#tablaAdd tbody').html('');

                                                    if (!datosGuardados) {
                                                        asegurarMinimoUnaFila();
                                                        actualizarTotal();
                                                        return;
                                                    }

                                                    try {
                                                        var datos = JSON.parse(datosGuardados);

                                                        if (!Array.isArray(datos) || datos.length === 0) {
                                                            asegurarMinimoUnaFila();
                                                        } else {
                                                            for (var i = 0; i < datos.length; i++) {
                                                                $('#tablaAdd tbody').append(crearFila());

                                                                var $ultimaFila = $('#tablaAdd tbody tr:last');
                                                                $ultimaFila.find('.act_grad_nom').val(datos[i].nombre || '');
                                                                $ultimaFila.find('.act_grad_tar').val(datos[i].tarjeta || '');
                                                            }
                                                        }

                                                        asegurarMinimoUnaFila();
                                                        actualizarTotal();
                                                    } catch (error) {
                                                        console.error('Error al restaurar datos del localStorage:', error);
                                                        asegurarMinimoUnaFila();
                                                        actualizarTotal();
                                                    }
                                                }

                                                function agregarFila() {
                                                    $('#tablaAdd tbody').append(crearFila());
                                                    actualizarTotal();
                                                    guardarDatos();
                                                }

                                                function borrarTodo() {
                                                    $('#tablaAdd tbody').html('');
                                                    asegurarMinimoUnaFila();
                                                    actualizarTotal();
                                                    guardarDatos();
                                                }

                                                restaurarDatos();

                                                $(document).on('click', '#adicionarAdd', function (e) {
                                                    e.preventDefault();
                                                    agregarFila();
                                                });

                                                $(document).on('click', '#generarVariasAdd', function (e) {
                                                    e.preventDefault();

                                                    var cantidad = parseInt($('#cantidadAdd').val(), 10);

                                                    if (isNaN(cantidad) || cantidad <= 0) {
                                                        alert('Ingrese una cantidad válida mayor a 0.');
                                                        $('#cantidadAdd').focus();
                                                        return;
                                                    }

                                                    for (var i = 0; i < cantidad; i++) {
                                                        $('#tablaAdd tbody').append(crearFila());
                                                    }

                                                    actualizarTotal();
                                                    guardarDatos();
                                                });

                                                $(document).on('click', '#borrarTodoAdd', function (e) {
                                                    e.preventDefault();

                                                    var confirmar = confirm('¿Está seguro de que desea borrar todos los registros?');

                                                    if (confirmar) {
                                                        borrarTodo();
                                                    }
                                                });

                                                $(document).on('click', '.btn-eliminar-fila', function (e) {
                                                    e.preventDefault();
                                                    $(this).closest('tr').remove();
                                                    asegurarMinimoUnaFila();
                                                    actualizarTotal();
                                                    guardarDatos();
                                                });

                                                $(document).on('keyup change blur', '.act_grad_nom, .act_grad_tar', function () {
                                                    actualizarTotal();
                                                    guardarDatos();
                                                });

                                                $('form').on('submit', function (e) {
                                                    actualizarTotal();

                                                    var totalCompletos = parseInt($('#total').val(), 10) || 0;
                                                    var hayIncompletas = false;

                                                    $('#tablaAdd tbody tr').each(function () {
                                                        if (filaIncompleta($(this))) {
                                                            hayIncompletas = true;
                                                            return false;
                                                        }
                                                    });

                                                    if (hayIncompletas) {
                                                        e.preventDefault();
                                                        alert('Si diligencia un campo en una fila, debe completar también el nombre y el número de identificación.');
                                                        return false;
                                                    }

                                                    if (totalCompletos < 1) {
                                                        e.preventDefault();
                                                        alert('Debe diligenciar mínimo un registro completo de graduado.');
                                                        return false;
                                                    }

                                                    localStorage.removeItem(STORAGE_KEY);
                                                });

                                            });
                                        </script>

                                        <div class="col-sm-12">

                                            <div class="cont-tit">

                                                <div class="hr">
                                                    <hr>
                                                </div>

                                                <div class="tit-cen">

                                                    <h3 class="text-center">REGISTRO DE VOLUNTARIOS INTERNOS</h3>

                                                    <p>A continuación por favor ingrese los datos requeridos</p>

                                                </div>

                                                <div class="hr">
                                                    <hr>
                                                </div>

                                            </div>

                                            <div class="form-group">

                                                <div class="col-sm-1"></div>

                                                <div class="col-sm-10">

                                                    <script>
                                                        $(function () {

                                                            var total = 0;

                                                            var tar = $(".act_vin_tar").val();
                                                            var nom = $(".act_vin_nom").val();

                                                            if (tar == "" || nom == "") {
                                                                $("#adicionarAdd2").prop("disabled", true);
                                                            } else {
                                                                $("#adicionarAdd2").prop("disabled", false);
                                                            }

                                                            $(".act_vin_nom").change(function () {
                                                                var tar3 = $(".act_vin_tar").val();
                                                                var nom3 = $(".act_vin_nom").val();

                                                                if (tar3 != "" && nom3 != "") {
                                                                    if (total < 1) {
                                                                        total = total + 1;
                                                                    }
                                                                    $("#adicionarAdd2").prop("disabled", false);
                                                                } else if (tar3 == "" && nom3 == "") {
                                                                    if (total == 1) {
                                                                        total = total - 1;
                                                                        $(".act_vin_nom").prop('required', false);
                                                                        $(".act_vin_tar").prop('required', false);
                                                                    }
                                                                } else {
                                                                    $("#adicionarAdd2").prop("disabled", true);
                                                                }

                                                                $('#total2').val(total);
                                                            });

                                                            $(".act_vin_tar").change(function () {
                                                                var nom2 = $(".act_vin_nom").val();
                                                                var tar2 = $(".act_vin_tar").val();

                                                                if (nom2 != "" && tar2 != "") {
                                                                    if (total < 1) {
                                                                        total = total + 1;
                                                                    }
                                                                    $("#adicionarAdd2").prop("disabled", false);
                                                                } else if (tar2 == "" && nom2 == "") {
                                                                    if (total == 1) {
                                                                        total = total - 1;
                                                                        $(".act_vin_nom").prop('required', false);
                                                                        $(".act_vin_tar").prop('required', false);
                                                                    }
                                                                } else {
                                                                    $("#adicionarAdd2").prop("disabled", true);
                                                                }

                                                                $('#total2').val(total);
                                                            });

                                                            $("#adicionarAdd2").on('click', function () {
                                                                var tar2 = $(".act_vin_tar").val();
                                                                var nom2 = $(".act_vin_nom").val();

                                                                if (tar2 != "" && nom2 != "") {
                                                                    $("#tablaAdd2 tr:last").clone().removeClass('fila-fijaAdd2').appendTo("#tablaAdd2");
                                                                    $("#tablaAdd2 tr input.act_vin_nom:last").val('');
                                                                    $("#tablaAdd2 tr input.act_vin_tar:last").val('');
                                                                    total = total + 1;
                                                                } else {
                                                                    alert("Ingrese toda la información antes de agregar otro campo");
                                                                }

                                                                $(".act_vin_nom").prop('required', true);
                                                                $(".act_vin_tar").prop('required', true);
                                                                $('#total2').val(total);
                                                            });

                                                            $(document).on("click", ".eliminarAdd2", function () {
                                                                var parent = $(this).parents().get(0);
                                                                $(parent).remove();
                                                                total = total - 1;
                                                                $('#total2').val(total);
                                                            });

                                                        });
                                                    </script>

                                                    <table id="tablaAdd2" class="table table-bordered"
                                                        style="width:100%; table-layout:fixed;">

                                                        <tr class="fila-fijaAdd2">

                                                            <td style="width:58%;">

                                                                <strong>Nombre completo del siervo Facilitador:</strong>

                                                                <input name="act_vin_nom[]" type="text" class="act_vin_nom form-control"
                                                                    required style="width:100%; height:40px;" />

                                                            </td>

                                                            <td style="width:32%;">

                                                                <strong>Tarjeta dactilar / N° identificación:</strong>

                                                                <input name="act_vin_tar[]" type="text" class="act_vin_tar form-control"
                                                                    required style="width:100%; height:40px;" />

                                                            </td>

                                                            <td class="eliminarAdd2"
                                                                style="width:10%; text-align:center; vertical-align:middle;">

                                                                <button type="button" class="btn btn-cir-uno usua-col">
                                                                    <i class="fa fa-times"></i>
                                                                </button>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </div>

                                                <div class="col-sm-1"></div>

                                            </div>

                                            <div class="col-sm-2"></div>

                                        </div>

                                        <div class="form-group">

                                            <div class="col-sm-2"></div>

                                            <div class="col-sm-8">

                                                <div class="col-sm-4"><strong>Número de voluntarios internos activos en esta
                                                        prisión:</strong> </div>

                                                <div class="col-sm-2">

                                                    <input type="text" name="total2" id="total2" value="" class="form-control" readonly>

                                                </div>

                                                <div class="col-sm-4"></div>

                                                <div class="col-sm-2">

                                                    <center>

                                                        <button id="adicionarAdd2" class="btn btn-success" type="button"
                                                            class="boton"><i class="fas fa-plus"></i> Adicionar</button>

                                                    </center>

                                                </div>

                                            </div>


                                        </div>

                                        <div class="col-sm-12">

                                            <div class="cont-tit">

                                                <div class="hr">
                                                    <hr>
                                                </div>

                                                <div class="tit-cen">

                                                    <h3 class="text-center">REGISTRO DE VOLUNTARIOS EXTERNOS</h3>

                                                    <p>A continuación por favor ingrese los datos requeridos</p>

                                                </div>

                                                <div class="hr">
                                                    <hr>
                                                </div>

                                            </div>

                                            <div class="form-group">

                                                <div class="col-sm-1"></div>

                                                <div class="col-sm-10">

                                                    <script>
                                                        $(function () {

                                                            var total = 0;

                                                            var tar = $(".act_vex_tar").val();
                                                            var nom = $(".act_vex_nom").val();

                                                            if (tar == "" || nom == "") {
                                                                $("#adicionarAdd3").prop("disabled", true);
                                                            } else {
                                                                $("#adicionarAdd3").prop("disabled", false);
                                                            }

                                                            $(".act_vex_nom").change(function () {
                                                                var tar3 = $(".act_vex_tar").val();
                                                                var nom3 = $(".act_vex_nom").val();

                                                                if (tar3 != "" && nom3 != "") {
                                                                    if (total < 1) {
                                                                        total = total + 1;
                                                                    }

                                                                    $("#adicionarAdd3").prop("disabled", false);

                                                                } else if (tar3 == "" && nom3 == "") {
                                                                    if (total == 1) {
                                                                        total = total - 1;
                                                                        $(".act_vex_nom").prop('required', false);
                                                                        $(".act_vex_tar").prop('required', false);
                                                                    }

                                                                } else {
                                                                    $("#adicionarAdd3").prop("disabled", true);
                                                                }

                                                                $('#total3').val(total);
                                                            });

                                                            $(".act_vex_tar").change(function () {

                                                                var nom2 = $(".act_vex_nom").val();
                                                                var tar2 = $(".act_vex_tar").val();

                                                                if (nom2 != "" && tar2 != "") {
                                                                    if (total < 1) {
                                                                        total = total + 1;
                                                                    }

                                                                    $("#adicionarAdd3").prop("disabled", false);

                                                                } else if (tar2 == "" && nom2 == "") {
                                                                    if (total == 1) {
                                                                        total = total - 1;
                                                                        $(".act_vex_nom").prop('required', false);
                                                                        $(".act_vex_tar").prop('required', false);
                                                                    }

                                                                } else {
                                                                    $("#adicionarAdd3").prop("disabled", true);
                                                                }

                                                                $('#total3').val(total);
                                                            });

                                                            $("#adicionarAdd3").on('click', function () {

                                                                var tar2 = $(".act_vex_tar").val();
                                                                var nom2 = $(".act_vex_nom").val();

                                                                if (tar2 != "" && nom2 != "") {
                                                                    $("#tablaAdd3 tr:last").clone().removeClass('fila-fijaAdd3').appendTo("#tablaAdd3");
                                                                    $("#tablaAdd3 tr input.act_vex_nom:last").val('');
                                                                    $("#tablaAdd3 tr input.act_vex_tar:last").val('');
                                                                    total = total + 1;
                                                                } else {
                                                                    alert("Ingrese toda la información antes de agregar otro campo");
                                                                }

                                                                $(".act_vex_nom").prop('required', true);
                                                                $(".act_vex_tar").prop('required', true);
                                                                $('#total3').val(total);
                                                            });

                                                            $(document).on("click", ".eliminarAdd3", function () {
                                                                var parent = $(this).parents().get(0);
                                                                $(parent).remove();
                                                                total = total - 1;
                                                                $('#total3').val(total);
                                                            });

                                                        });
                                                    </script>

                                                    <table id="tablaAdd3" class="table table-bordered"
                                                        style="width:100%; table-layout:fixed;">

                                                        <tr class="fila-fijaAdd3">

                                                            <td style="width:58%;">

                                                                <strong>Nombre completo del entrenador:</strong>

                                                                <input name="act_vex_nom[]" type="text" class="act_vex_nom form-control"
                                                                    required style="width:100%; height:40px;" />

                                                            </td>

                                                            <td style="width:32%;">

                                                                <strong>N° identificación:</strong>

                                                                <input name="act_vex_tar[]" type="text" class="act_vex_tar form-control"
                                                                    required style="width:100%; height:40px;" />

                                                            </td>

                                                            <td class="eliminarAdd3"
                                                                style="width:10%; text-align:center; vertical-align:middle;">

                                                                <button type="button" class="btn btn-cir-uno usua-col">
                                                                    <i class="fa fa-times"></i>
                                                                </button>

                                                            </td>

                                                        </tr>

                                                    </table>

                                                </div>

                                                <div class="col-sm-1"></div>

                                            </div>

                                            <div class="form-group">

                                                <div class="col-sm-1"></div>

                                                <div class="col-sm-10">

                                                    <div class="col-sm-4">
                                                        <strong>Número de voluntarios externos para esta prisión:</strong>
                                                    </div>

                                                    <div class="col-sm-2">
                                                        <input type="text" name="total3" id="total3" value="" class="form-control"
                                                            readonly>
                                                    </div>

                                                    <div class="col-sm-4"></div>

                                                    <div class="col-sm-2">
                                                        <center>
                                                            <button id="adicionarAdd3" class="btn btn-success" type="button">
                                                                <i class="fas fa-plus"></i> Adicionar
                                                            </button>
                                                        </center>
                                                    </div>

                                                </div>

                                                <div class="col-sm-1"></div>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="col-sm-12">

                                        <div class="cont-tit">

                                            <div class="hr">
                                                <hr>
                                            </div>

                                            <div class="tit-cen">
                                                <h3 class="text-center">CONTINUIDAD DE FORMACIÓN</h3>
                                            </div>

                                            <div class="hr">
                                                <hr>
                                            </div>

                                        </div>

                                        <div class="form-group">

                                            <div class="col-sm-1"></div>

                                            <div class="col-sm-5">
                                                <strong>Personas formadas:</strong>
                                                <input name="rep_ndis" type="number" id="rep_ndis" min="0" value="<?= $rep_ndis; ?>"
                                                    class="form-control" style="width:100%;" />
                                            </div>

                                            <div class="col-sm-5">
                                                <strong>Mini proclamadores distribuidos:</strong>
                                                <input name="rep_entr" type="number" id="rep_entr" min="0" max="64"
                                                    value="<?= $rep_entr; ?>" class="form-control" style="width:100%;"
                                                    oninvalid="this.setCustomValidity('Debe ingresar un número entre 0 y 64')"
                                                    oninput="this.setCustomValidity('')" />
                                            </div>

                                            <div class="col-sm-1"></div>

                                        </div>

                                        <div class="form-group" style="margin-top:25px; clear:both;">

                                            <div class="col-sm-1"></div>

                                            <div class="col-sm-2">
                                                <strong>Unidad 2:</strong>
                                                <input name="unidad_2" type="number" id="unidad_2" min="0" value="<?= $unidad_2; ?>"
                                                    class="form-control unidad-input" style="width:100%;" />
                                            </div>

                                            <div class="col-sm-2">
                                                <strong>Unidad 3:</strong>
                                                <input name="unidad_3" type="number" id="unidad_3" min="0" value="<?= $unidad_3; ?>"
                                                    class="form-control unidad-input" style="width:100%;" />
                                            </div>

                                            <div class="col-sm-2">
                                                <strong>Unidad 4:</strong>
                                                <input name="unidad_4" type="number" id="unidad_4" min="0" value="<?= $unidad_4; ?>"
                                                    class="form-control unidad-input" style="width:100%;" />
                                            </div>

                                            <div class="col-sm-2">
                                                <strong>Unidad 5:</strong>
                                                <input name="unidad_5" type="number" id="unidad_5" min="0" value="<?= $unidad_5; ?>"
                                                    class="form-control unidad-input" style="width:100%;" />
                                            </div>

                                            <div class="col-sm-2">
                                                <strong>Unidad 6:</strong>
                                                <input name="unidad_6" type="number" id="unidad_6" min="0" value="<?= $unidad_6; ?>"
                                                    class="form-control unidad-input" style="width:100%;" />
                                            </div>

                                            <div class="col-sm-1"></div>

                                        </div>

                                        <div class="form-group" style="margin-top:15px; clear:both;">

                                            <div class="col-sm-4"></div>

                                            <div class="col-sm-4">
                                                <strong>Total:</strong>
                                                <input name="unidad_total" type="number" id="unidad_total" min="0"
                                                    value="<?= $unidad_total; ?>" class="form-control" readonly style="width:100%;" />
                                            </div>

                                            <div class="col-sm-4"></div>

                                        </div>

                                        <script>
                                            function calcularTotalUnidades() {
                                                var unidad2 = parseInt(document.getElementById('unidad_2').value) || 0;
                                                var unidad3 = parseInt(document.getElementById('unidad_3').value) || 0;
                                                var unidad4 = parseInt(document.getElementById('unidad_4').value) || 0;
                                                var unidad5 = parseInt(document.getElementById('unidad_5').value) || 0;
                                                var unidad6 = parseInt(document.getElementById('unidad_6').value) || 0;

                                                var total = unidad2 + unidad3 + unidad4 + unidad5 + unidad6;
                                                document.getElementById('unidad_total').value = total;
                                            }

                                            document.getElementById('unidad_2').addEventListener('input', calcularTotalUnidades);
                                            document.getElementById('unidad_3').addEventListener('input', calcularTotalUnidades);
                                            document.getElementById('unidad_4').addEventListener('input', calcularTotalUnidades);
                                            document.getElementById('unidad_5').addEventListener('input', calcularTotalUnidades);
                                            document.getElementById('unidad_6').addEventListener('input', calcularTotalUnidades);

                                            calcularTotalUnidades();
                                        </script>

                                        <div class="form-group" style="margin-top:25px; clear:both;">

                                            <div class="col-sm-2"></div>

                                            <div class="col-sm-4">
                                                <strong>Testimonio:</strong>
                                                <input name="archivo2" type="file" id="archivo2" class="form-control"
                                                    accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                                    required style="width:100%;">
                                            </div>

                                            <div class="col-sm-4">
                                                <strong>Foto:</strong>
                                                <input name="archivo1" type="file" id="archivo1" class="form-control"
                                                    accept="image/png, image/jpeg, image/jpg, image/gif" required style="width:100%;">
                                            </div>

                                            <div class="col-sm-2"></div>

                                        </div>

                                        <div class="cont-btn cont-flex fl-cent" style="margin-top:25px;">
                                            <div class="item-btn">
                                                <input type="submit" name="button" value="Guardar" class="btn btn-success">
                                            </div>
                                        </div>

                                        <input type="submit" name="button-hidden" id="button-hidden" style="display:none">
                                        <input type="hidden" name="funcion" id="funcion" value="" />
                                        <input type="hidden" name="generacion" id="generacion" value="<?= $generacionActual; ?>" />

                                    </div>

                        </form>

                        <script language="javascript">

                            var current = 1, current_step, next_step, steps;

                            //

                            function generarForm() {

                                //Completo el formulario  

                                if (true) {



                                <?php



                                if ($generacionActual == "INTRA") {

                                    ?>





                                        if (parseInt(document.getElementById("final_asistencia_total").value) < 3) {

                                            alert("La asistencia total no puede ser menor a 3 personas");

                                            return false;

                                        } else {

                                            return true;

                                        }



                                <?php

                                } else {

                                    ?>



                                        if (confirm("Esta accion guardara los cambios en el sistema, ¿esta seguro que desea continuar?")) {

                                            $(':input[type="submit"]').prop('disabled', true);

                                            document.getElementById('funcion').value = "<?= $temp_accionForm; ?>";

                                        } else {

                                            return false;

                                        }



                                <?php

                                }

                                ?>

                                } else {

                                    return false;

                                }

                            }



                            //

                            function init() {

                                document.getElementById('form1').onsubmit = function () {

                                    return generarForm();

                                }





                                function sumar() {

                                    var asistencia_hom = 0;

                                    var asistencia_muj = 0;

                                    var asistencia_jov = 0;

                                    var asistencia_nin = 0;

                                    var desiciones = 0;

                                    //

                                    if (document.getElementById("asistencia_hom").value != "") {

                                        var asistencia_hom = document.getElementById("asistencia_hom").value;

                                    }

                                    if (document.getElementById("asistencia_muj").value != "") {

                                        var asistencia_muj = document.getElementById("asistencia_muj").value;

                                    }

                                    if (document.getElementById("asistencia_muj").value != "") {

                                        var asistencia_muj = document.getElementById("asistencia_muj").value;

                                    }

                                    //

                                    if (document.getElementById("asistencia_jov").value != "") {

                                        var asistencia_jov = document.getElementById("asistencia_jov").value;

                                    }

                                    if (document.getElementById("asistencia_nin").value != "") {

                                        var asistencia_nin = document.getElementById("asistencia_nin").value;

                                    }

                                    if (document.getElementById("asistencia_total").value != "") {

                                        var asistencia_total = document.getElementById("asistencia_total").value;

                                    }



                                    document.getElementById("final_asistencia_total").value = parseInt(asistencia_total);

                                    document.getElementById("final_asistencia_hom").value = parseInt(asistencia_hom);

                                    document.getElementById("final_asistencia_muj").value = parseInt(asistencia_muj);

                                    document.getElementById("final_asistencia_jov").value = parseInt(asistencia_jov);

                                    document.getElementById("final_asistencia_nin").value = parseInt(asistencia_nin);
                                    document.getElementById("final_bautizados").value = parseInt(bautizados) + 1;

                                    document.getElementById("final_discipulado").value = parseInt(var_suma) - 1;
                                    document.getElementById("final_bautizadosPeriodo").value = parseInt(bautizadosPeriodo);

                                    document.getElementById("final_desiciones").value = parseInt(desiciones);

                                    document.getElementById("final_preparandose").value = parseInt(var_suma) - 1 - parseInt(bautizadosPeriodo);

                                }



                            <?php

                            if ($varExitoREP == 1) {

                                ?>alert("Se ha colocado correctamente el ACCESO, espere mientras es dirigido.");

                                    window.location.href = "index.php?doc=admin_usu4&id=<?= $ultimoId; ?>"; <?php

                            }

                            ?>

                            }





                            window.onload = function () {

                                init();

                            }

                        </script>





                <?php

                }

}   //FIN DEL IF DE REDIRIGIR SI YA INSERTO EL REGISTRO
else {

    echo "No deberia estar aquí.";

}

?>

        <?php if ($_SESSION['perfil'] == "168" || $fechLimite > $fechaReporte) { ?>

            <script type="text/javascript">

                $("input").attr('disabled', 'disabled');

                $("textarea").attr('disabled', 'disabled');

                $("select").attr('disabled', 'disabled');

                $(".eliminarAdd").prop("disabled", true);

                $(".eliminarAdd2").prop("disabled", true);

                $(".eliminarAdd3").prop("disabled", true);

                $("#btn-check").prop('disabled', false);

            </script>

        <?php } ?>

        <script type="text/javascript">

            $(document).ready(function () {

                recargaLista();

                $('#rep_carcel').change(function () {

                    recargaLista();

                });

                recargaListaDpto();

                $('#departamento').change(function () {

                    recargaListaDpto();

                });

                $('#asistencia_muj').change(function () {



                    var cursos = $('#asistencia_muj').val();

                    var resul = cursos / 12;

                    var mod = resul % 2;

                    if (mod != 0) {

                        resul = Math.trunc(resul) + 1;

                    }

                    if (cursos <= 12) {

                        resul = 1;

                    }

                    $('#asistencia_jov').val(resul);

                });

            })

        </script>

        <script type="text/javascript">

            function recargaListaDpto() {

                $.ajax({

                    type: "POST",

                    url: "datos_ubicacion.php",

                    data: "id_depa=" + $('#departamento').val(),

                    success: function (r) {

                        $('#municipio').html(r);

                    }

                })

            }

        </script>

        <script type="text/javascript">

            function recargaLista() {

                $.ajax({

                    type: "POST",

                    url: "datos_carcel_ubicacion.php",

                    data: "id_carcel=" + $('#rep_carcel').val(),

                    success: function (r) {

                        $('#ubicacion').html(r);

                    }

                })

            }

        </script>