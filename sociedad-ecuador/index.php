<?php 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
//
error_reporting(E_ERROR | E_PARSE);
header('Content-Type: text/html; charset=utf-8');  



//
if($_GET["doc"] == "mail_envio"){
    // Ignore user aborts and allow the script
    // to run forever
    ignore_user_abort(true);
    set_time_limit(0);
    //
    include_once('phpmailer/src/PHPMailer.php');
    include_once('phpmailer/src/SMTP.php');
    include_once('phpmailer/src/Exception.php');

    //$mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail = new PHPMailer();
    //$Exception = new PHPMailer\PHPMailer\Exception;
}

//
session_set_cookie_params(60*60*3);
session_start();
//session_register("SESSION");
include_once('funciones.php');

if(!isset($_GET["salir"]) && $_SESSION["id"] != "" && $_SESSION["id"] != 0 && $_SESSION["sistema"] != "videx")
{
	header("Location: index.php?salir=");
	exit;
}


$array_meses = array(
"Enero",
"Enero",
"Febrero",
"Marzo",
"Abril",
"Mayo",
"Junio",
"Julio",
"Agosto",
"Septiembre",
"Octubre",
"Noviembre",
"Diciembre"
);

$array_semana = array(
"Mon" => "Lunes",
"Tue" => "Martes", 
"Wed" => "Miercoles", 
"Thu" => "Jueves", 
"Fri" => "Viernes", 
"Sat" => "S&aacute;bado", 
"Sun" => "Domingo",
); 

$mesactual = intval(date("m"));
$anhoactual = intval(date("Y"));
$diaactual = intval(date("d"));
$semanaactual = date("D"); 

if(isset($_GET["salir"]))
{
	session_unset();
	redirect('index.php');
}

if(isset($_POST["logueo"]) && trim($_POST["logueo"]) != "")
{
	$PSN = new DBbase_Sql;
	$logueo = eliminarInvalidos($_POST["logueo"]);
	$pass = eliminarInvalidos($_POST["passwordlogueo"]);
	$error = 0;
	
	$sql= "SELECT CA.id AS id_zona, CA.descripcion AS zona,C.descripcion AS regional, usuario.*, UE.empresa_socio, UE.empresa_pd, UE.empresa_sitio_cor
FROM usuario";
	   $sql.=" LEFT JOIN usuario_empresa AS UE ON UE.idUsuario = usuario.id
LEFT JOIN categorias AS C ON C.id = UE.empresa_pd
LEFT JOIN categorias AS CA ON CA.id = C.idSec";
	$sql.=" WHERE acceso = 1 AND identificacion='".$logueo."'";

	$PSN->query($sql);
	//echo $sql;
	if($PSN->next_record())
	{
		if(md5($pass) == $PSN->f('password'))
		{
			$_SESSION["empresa_socio"] = $PSN->f('empresa_socio');
            $_SESSION["empresa_pd"] = $PSN->f('empresa_pd');
            $_SESSION["empresa_sitio_cor"] = $PSN->f('empresa_sitio_cor');
            
            $_SESSION["id_zona"] = $PSN->f('id_zona');
            //            
			$_SESSION["administrador"] = "admin";
			$_SESSION["sistema"] = "videx";
			$_SESSION["nombre"] = $PSN->f('nombre');
			$_SESSION["identificacion"] = $PSN->f('identificacion');
			$_SESSION["direccion"] = $PSN->f('direccion');
			$_SESSION["telefono1"] = $PSN->f('telefono1');
			$_SESSION["telefono2"] = $PSN->f('telefono2');
			$_SESSION["celular"] = $PSN->f('celular');
			$_SESSION["email"] = $PSN->f('email');
			$_SESSION["youtube"] = $PSN->f('url');
			$_SESSION["drive"] = $PSN->f('url2');
			$_SESSION["id"] = $PSN->f('id');
			$_SESSION["superusuario"] = $PSN->f('superusuario');
            $_SESSION["menu_graphs"] = $PSN->f('acceso_graphs');
            $_SESSION["usua_regional"] = $PSN->f('regional');
            $_SESSION["usua_zona"] = $PSN->f('zona');
			//
            //                
			$_SESSION['KCFINDER'] = array(
    			'disabled' => false
			);
            
            /*
            *
            */			
			$_SESSION["perfil"] = $PSN->f('tipo');
            //
            if($_SESSION["perfil"] == 160){
                //
                $_SESSION["tipo_user_cli"] = $PSN->f('tipo_user_cli');
                //                
                $sql= "SELECT usuario_relacion.idUsuario2 ";
                $sql.=" FROM usuario_relacion, usuario ";
                $sql.=" WHERE idUsuario1 = '".$_SESSION["id"]."' AND usuario.id = usuario_relacion.idUsuario2 AND usuario.tipo = 3";
                $PSN->query($sql);
                if($PSN->next_record())
                {
                    $_SESSION["micliente"] = $PSN->f('idUsuario2');
                    //$_SESSION["micliente"] = $PSN->f('idUsuario2');
                    
                    //die("Encontrado ".$_SESSION["micliente"]);
                }else{
                    //die("NO Encontrado ".$sql);
                }
                
            }
            else if($_SESSION["perfil"] == 3){
                $_SESSION["micliente"] = $PSN->f('id');
            }
			//
			//
			if($_POST["redireccion"] != "")
			{
				redirect(eliminarInvalidos($_POST["redireccion"]));
			}
			else
			{
				redirect('index.php?doc=main');
			}
		}
		else
		{
			$error = 2;
		}
	}
	else
	{
		$error = 1;
	}
}

if(trim($_SESSION["imagen"]) == "")
{
//	$_SESSION["imagen"] = "titulo.png";				
	$_SESSION["imagen"] = "https://app.sociedadmisionera.org/images/titulo.png";				
}


if(isset($_GET["excelX"]))
{
	header('Content-type: application/vnd.ms-excel; charset=utf-8');
	header("Content-Disposition: attachment; filename=archivo".date("Ymd_His").".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
	$docu = eliminarInvalidos($_GET["doc"]);
	include_once($docu.".php");
	exit;
}
else if(isset($_GET["excelXML"])){
	header('Content-type: application/vnd.ms-excel; charset=iso-8859-1');
	header("Content-Disposition: attachment; filename=archivo".date("Ymd_His").".xls");
	header("Pragma: no-cache");
	header("Expires: 0");
    ?><?php echo "<?"; ?>xml version="1.0" encoding="UTF-8" <?php echo "?>"; ?>

    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">

    <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Bottom"/>
            <Borders/>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
            <Interior/>
            <Protection/>
        </Style>
        <Style ss:ID="s66">
           <NumberFormat ss:Format="dd\-mm\-yyyy"/>
          </Style>
          <Style ss:ID="s68">
           <NumberFormat ss:Format="Short Date"/>
          </Style>
      <Style ss:ID="amarillo">
       <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
      </Style>
         
      <Style ss:ID="verdoso">
       <Interior ss:Color="#C8FFFF" ss:Pattern="Solid"/>
      </Style>
         
      <Style ss:ID="verdosoBold">
       <Interior ss:Color="#C8FFFF" ss:Pattern="Solid"/>
       <Font ss:Bold="1"/>
      </Style>
         
     </Styles><?php
	$docu = eliminarInvalidos($_GET["doc"]);
	include_once($docu.".php");
    echo "</Workbook>";
	exit;
}

?>

<!DOCTYPE html>
<html>
<head><meta charset="UTF-8">

	<title><?=$gloPrograma; ?> - <?=$gloEmpresa	; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="./images/favico.png" sizes="32x32">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="stylesheet" type="text/css" href="estilos_chart.css" />
    <!--<?php
	if($_GET["doc"] == "vehiculo_graph_cli1" || $_SESSION["menu_graphs"] == 1){
            $mostrar_dashboard = 1;
            ?>
            <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
            <link rel="stylesheet" type="text/css" href="estilos_chart.css" />
            <?php
	}
    ?>-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css">
    <link rel="stylesheet" type="text/css" href="estilos_chart.css" />
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <style>
    .navbar-default {
        background-color: #000;
        border-color: #000;
        border-radius: 0;
        box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.3);
        border-radius: 0px 25px 25px 0px;
        margin-top: 5px;
    }

    .navbar-default .navbar-brand,
    .navbar-default .navbar-brand:hover,
    .navbar-default .navbar-brand:focus {
        background-color: transparent;
        color: #FFF;
    }

    .navbar-default .navbar-nav > li > a {
        color: #FFF;
    }

    .navbar-default .navbar-nav > li > a:hover,
    .navbar-default .navbar-nav > li > a:focus {
        opacity: .8;
        color: #ddd;
    }

    .navbar-default .navbar-nav > .active > a,
    .navbar-default .navbar-nav > .active > a:hover,
    .navbar-default .navbar-nav > .active > a:focus {
        color: #FFF;
    }

    .navbar-default .navbar-text {
        color: #FFF;
    }

    .navbar-default .navbar-toggle {
        border-color: #385FBE;
        color: #FFF;
    }

    .navbar-default .navbar-toggle:hover,
    .navbar-default .navbar-toggle:focus {
        color: #FFF;
    }

    .navbar-default .navbar-toggle .icon-bar {
        background-color: #FFF;
    }   
    </style>
    <?php

    if($_GET["doc"] == "graphs_007"){
        ?><style>
            .funnel_outer{width:100%;float: left;position: relative;padding:0 10%;}
            .funnel_outer *{box-sizing:border-box}
                .funnel_outer ul{margin:0;padding:0;}
                .funnel_outer ul li{float: left;position: relative;margin:2px 0;height: 150px;clear: both;text-align: center;vertical-align: middle;width:100%;list-style:none}
                .funnel_outer li span{ border-top-width: 150px;border-top-style:  solid; border-left: 25px solid transparent; border-right:25px solid transparent; height: 0;display: inline-block;vertical-align: middle; } 
                .funnel_step_1 span{width:100%;border-top-color: #8080b6;}
                .funnel_step_2 span{width:calc(100% - 50px);border-top-color: #8E44AD}
                .funnel_step_3 span{width:calc(100% - 100px);border-top-color: #2C3E50}
                .funnel_step_4 span{width:calc(100% - 150px);border-top-color: #2ECC71}
                .funnel_step_5 span{width:calc(100% - 200px);border-top-color: #8E44AD}
                .funnel_step_6 span{width:calc(100% - 250px);border-top-color: #2C3E50}
                .funnel_step_7 span{width:calc(100% - 300px);border-top-color: #3498DB;}
            .funnel_outer ul li:last-child span{border-left: 0;border-right: 0;border-top-width: 40px;}
            .funnel_outer ul li.not_last span{border-left: 5px  solid transparent;border-right:5px  solid transparent;border-top-width:150px;}
              .funnel_outer ul li span p{margin-top: -30px;color:#fff;font-weight: bold;text-align: center;}
        </style><?php
    }
    
    
    if($_GET["doc"] != "sms_envio")
    {
        /*?>
        <!-- Script -->
        <!-- <script src="scripts/jquery-1.12.4.js"></script> //-->
        <!-- jQuery UI -->
        <link rel="stylesheet" href="scripts/jquery-ui.css">
        <script src="scripts/jquery-ui.js"></script><?*/
    }	
?>
	
<style>
* {
   font-size: 12px;
   line-height: 1.428;
}	
    
@media (max-width: 767px) {
  .navbar-default .navbar-nav .open .dropdown-menu>li>a,
  .navbar.navbar-nav li.dropdown .dropdown-menu > li > a {
    color: #fff; !important
  }
  /***Dropdown-menu Color Hover and Focus State***/
  .navbar-default .navbar-nav .open .dropdown-menu>li>a:hover,
  .navbar-default .navbar-nav .open .dropdown-menu>li>a:focus,
  .navbar.navbar-nav li.dropdown .dropdown-menu > li > a:hover,
  .navbar.navbar-nav li.dropdown .dropdown-menu > li > a:focus {
    color: #fff; !important
  }
}    
    
</style>
	<script>
    document.addEventListener("DOMContentLoaded", function(){
        // Invocamos cada 5 segundos ;)
        const milisegundos = 300 *1000;
        setInterval(function(){
            // No esperamos la respuesta de la petición porque no nos importa
            fetch("./refrescar.php");
        },milisegundos);
        const inactividad = 1200 *1000;
        setInterval(function(){
            // No esperamos la respuesta de la petición porque no nos importa
            alert("Tiene 20 de minutos de inactividad en el sistema");
        },inactividad);
    });
</script>
	
<script type="text/javascript">
function getfocus(id){
	if(document.getElementById(id))
    {
		document.getElementById(id).focus()
	}
}
function salirSistema()
{
	if(confirm("Desea salir del sistema?"))
	{
		window.location.href = "index.php?salir=";
	}
}
function cambiar(url)
{
		window.location.href = url.value;
}
</script>
</head>
<body <?php if($mostrar_dashboard == 1){ ?>onresize="drawChart()"<?php } ?>>

<?php
/*
*	AQUI VA EL CONTENIDO.
*/

if(!isset($_GET["doc"]) || $_GET["doc"] == ""){
	$_GET["doc"] = "main";
}
	
if(isset($_GET["doc"]) && !empty( $_GET["doc"]) && is_logged_in())
{?>
<div class="container">
    <div class="cont-menu cont-flex-2 fl-cent post-rela">
		<div class="navbar-header" style="width: 230px;height: 52px; box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.3); margin-top: 5px; border: 1px solid #000; border-radius: 25px 0px 0px 25px;">
			<a href="index.php?doc=main"><img src="images/logo.png" width="200px" /></a>
		</div>
        <div class="navbar-header">
            <input type="checkbox" name="btn-check" id="btn-check" value="1" class="btn-check">
            <label for="btn-check">
                <i class="abr fas fa-bars"></i>
                <i class="cer fas fa-times"></i>
            </label>
        
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
    		<?php
            if($_SESSION["perfil"] == 4){
                ?>
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fas fa-user"></i> Mi cuenta<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                <?php      
                
            }
            else{
                $PSNMenu = new DBbase_Sql;
                $sqlMenu = "SELECT menu.*";
                $sqlMenu.=" FROM menu, usuarios_menu WHERE menu.id =  usuarios_menu.idMenu AND  usuarios_menu.idUsuario = ".$_SESSION["id"];
                $sqlMenu.=" AND menu.estado = 1 ORDER BY principal, orden asc";
                $PSNMenu->query($sqlMenu);
                if($PSNMenu->num_rows() > 0)
                {
                    $principal_old = 0;
                    while($PSNMenu->next_record())
                    { 
                        if($principal_old != $PSNMenu->f("principal"))
                        {
                            if($principal_old != 0){
                                ?></ul>
                                </li><?php
                            }
                            $principal_old = $PSNMenu->f("principal");
                            
               
                            //
                            ?><li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><?php
                                switch($PSNMenu->f("principal")){
                                    case 1:
                                        echo "<i class='fas fa-sliders-h'></i> Configuración";
                                        break;
                                    case 2:
                                        echo "SMS + Emailing";
                                        break;
                                    case 3:
                                        echo "Cotizaciones";
                                        break;
                                    case 4:
                                        echo "<i class='fas fa-file-invoice'></i> Reportes";
                                        break;
                                    case 5:
                                        echo "Facilitadores";
                                        break;
                                    case 6:
                                        echo "Programa 2";
                                        break;
                                    case 7:
                                        echo "ECC-Capacitadores";
                                        break;
                                    case 8:
                                        echo "Proyecto Felipe";
                                        break;
                                    case 9:
                                        echo "Instituto Biblico";
                                        break;
                                    case 99:
                                        echo "<i class='fas fa-user'></i> Mi cuenta";
                                        break;
                                    default:
                                        echo "Otras opciones";
                                        break;
                                }
                                ?><span class="caret"></span></a>
                            <ul class="dropdown-menu"><?php					
                        }

                        
                        
                        ?><li><?php			
                        ?><a href="<?php
                             if($PSNMenu->f("directo") == 1){
                                echo $PSNMenu->f("php");
                             }
                             else{
                                ?>index.php?doc=<?=$PSNMenu->f("php"); ?><?php
                            }    
                             if($PSNMenu->f("opc") > 0){
                                 ?>&opc=<?php
                                 echo $PSNMenu->f("opc");
                             }
                             //  Extra
                             if($PSNMenu->f("extra") != ""){
                                 ?>&<?php
                                 echo $PSNMenu->f("extra");
                             }
                             ?>" title="<?=$PSNMenu->f("nombre"); ?>"<?php
                             if($PSNMenu->f("directo") == 1){
                                 ?> target="_blank"<?php
                             }else{
                                 ?> target="_self"<?php
                             }
                             ?>><?=$PSNMenu->f("imagen"); ?> <?=$PSNMenu->f("nombre"); ?></a><?php

                        ?></li><?php
                    }
                    
                    if($principal_old != 0){
                        ?></ul>
                        </li><?php
                    }
                }
                //
                /*
                *   MENU GRAPHS
                */
                //echo "Menu: ".$temp_menu_graphs;
                //
                if($_SESSION["menu_graphs"] == 1){
                    $PSNMenu2 = new DBbase_Sql;
                    $sqlMenu = "SELECT menu_graphs.*";
                    $sqlMenu.=" FROM menu_graphs, usuarios_menu_graphs WHERE menu_graphs.estado = 1 AND menu_graphs.id =  usuarios_menu_graphs.idMenu AND  usuarios_menu_graphs.idUsuario = ".$_SESSION["id"];
                    $sqlMenu.=" ORDER BY principal, orden asc";
                    $PSNMenu2->query($sqlMenu);
                    if($PSNMenu2->num_rows() > 0)
                    {
                        ?><li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fas fa-chart-pie"></i> Gráficas<span class="caret"></span></a>
                        <ul class="dropdown-menu"><?php
                        while($PSNMenu2->next_record())
                        { 
                            ?><li><?php			
                            ?><a href="index.php?doc=<?=$PSNMenu2->f("php"); ?><?php
                                 if($PSNMenu2->f("opc") > 0){
                                     ?>&opc=<?php
                                     echo $PSNMenu2->f("opc");
                                 }
                                 //  Extra
                                 if($PSNMenu2->f("extra") != ""){
                                     ?>&<?php
                                     echo $PSNMenu2->f("extra");
                                 }
                                 ?>" target="_self" title="<?=$PSNMenu2->f("nombre"); ?>"><?=$PSNMenu2->f("imagen"); ?> <?=$PSNMenu2->f("nombre"); ?></a><?php

                            ?></li><?php
                        }
                        ?></ul>
                        </li><?php
                    }
                }   //FIN MENU GRAPHS            
                
            }
        
            /*if($_SESSION["drive"] != ""){?>  
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fab fa-google-drive"></i> Materiales CCC<span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?=$_SESSION["drive"]; ?>" target="_blank" title="Lista de estudios pastorales y descargas"><img  src="images/png/download-from-cloud.png" border="0" height="10px" align="left" /> Lista de estudios pastorales y descargas</a></li>
                    </ul>
                </li>
            <?php
        }*/
    
        /*if($_SESSION["perfil"] == 2 || $_SESSION["perfil"] == 161){
            ?><li><a href="index.php?doc=videos" target="_self" title="Graficas"><img  src="images/png/sms-message.png" border="0" height="10px" align="left" /> Video tutorial</a></li><?
        }*/
        ?>
        <li><a href="index.php?doc=mis_documentos" target="_self" title="Ver mis documentos"><i class="fas fa-cloud-download-alt"></i> Tutoriales / Videos</a></li>
            
        <li><a href="javascript:salirSistema();void(0);" target="_self" title="Salir del Sistema"><i class="fas fa-power-off"></i></a></li>

        <!--<?php
        if($_SESSION["superusuario"] == 1){
            ?><li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">Superusuario<span class="caret"></span></a>
                <ul class="dropdown-menu">
                    <li><a href="index.php?doc=sms_saldo" target="_self" title="Agregar Saldo"><img  src="images/png/sms-message.png" border="0" height="10px" align="left" /> Agregar Saldo SMS</a></li>
                </ul>
            </li><?php
        }
        ?>-->
                    </ul>
                </div>
    	   </nav>
        </div>   
    </div>
	<br>
    
    <?php
    /*
	* FIN DEL MENU - MENU - MENU
	*/
	$docu = eliminarInvalidos($_GET["doc"]);
	if(trim($docu) == ""){
		$docu = "main";
	}
	include_once($docu.".php");
	
	?></center>
    <div id="footer">
        <center>
            <hr color="#0000FF">
        </center>
        <div class="cont-flex" style="width: 100%;">
            <div style="width: 50%;">
                Bienvenido <?=$_SESSION["nombre"]; ?>.<br />
                Hoy es <?=$array_semana[$semanaactual]; ?> <?=$diaactual; ?> de <?=$array_meses[$mesactual]; ?> del <?=$anhoactual; ?><br><br>
            </div>
            <div style="width: 50%; text-align: right;">
<?/*                Calle 74 #70- 125 Sector Pilarica. Medellín- Antioquia - Colombia.
                <br />
                Copyright 2022 - <?=date("Y"); ?> <a href="http://Videx.online/">Videx.online</a> desarrollado para <a href="https://www.pfcolombia.org/"><?=$gloEmpresa; ?></a><br><br>  */?>
            </div>
        </div>
	</div>
	<?php
}else{
	//Sin loguear
	?>
	<div class="cont-menu cont-flex-2 fl-sbet post-rela">
        <div class="navbar-header" style="width: 230px;height: 52px; box-shadow: 0px 3px 3px rgba(0, 0, 0, 0.3); margin-top: 5px; border: 1px solid #000; border-radius: 25px 0px 0px 25px;">	
			<a href="https://app.sociedadmisionera.org/"><img src="images/logo.png" height="50px" /></a>
		</div>

        <div class="navbar-header">
            <nav class="navbar navbar-default">
                <ul class="nav navbar-nav navbar-center">
                    <li><a href="index.php?doc=main"><strong><?=$gloPrograma; ?></strong></a></li>
                </ul>
            </nav>
            
        </div>          
	</div>	
	<div class="cont-flex fl-cent" style="min-height: 590px;align-items: center;">
        <div class="col-sm-1"></div>
        <div class="col-sm-10">
            <div class="col-sm-8">
                <br><br>
                <strong style="font-size: 24px;">Bienvenido al</strong>
    			<h2 style="margin-top: -5px;font-size: 28px; ">Sistema de Gestión Integral</h2>
<?/*    			<h2 style="margin-top: -5px;font-size: 28px; "><?=$gloPrograma; ?> de la</h2>
                <h1 style="margin-top: -10px;font-size: 38px;">Confraternidad Carcelaria de Colombia.</h1> */ ?>
                <p style="font-size: 16px;">Una herramienta diseñada para acompañar los procesos de los diferentes programas que hacen parte de nuestra organización. Podrás acceder a nuestra plataforma desde cualquier computador, Tablet y celular.</p>
                <ul class="social">
<?/*                    <li class="social-item"><a href="https://www.pfcolombia.org/" target="_blank"><i class="fas fa-globe-americas"></i></a></li>
                    <li class="social-item"><a href="https://www.facebook.com/pfcolombia.org/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                    <li class="social-item"><a href="https://www.instagram.com/accounts/login/?next=/pfcolombia/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                    <li class="social-item"><a href="https://www.youtube.com/channel/UCXBvL-G9chy6qJ9hcxBgv0Q/featured" target="_blank"><i class="fab fa-youtube"></i></a></li>
                    <li class="social-item"><a href="https://twitter.com/pfcolombia" target="_blank"><i class="fab fa-twitter"></i></a></li> */ ?>
                    <li class="social-item"><a href="https://www.bible.com/reading-plans/39520" target="_blank"><i class="fas fa-globe-americas"></i></a></li>
                    <li class="social-item"><a href="https://www.facebook.com/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                    <li class="social-item"><a href="https://www.instagram.com/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                    <li class="social-item"><a href="https://www.youtube.com/" target="_blank"><i class="fab fa-youtube"></i></a></li>
                    <li class="social-item"><a href="https://twitter.com/" target="_blank"><i class="fab fa-twitter"></i></a></li> 
                </ul>
                <h3>Patrocinadores</h3>
                <div class="cont-flex">
<?/*                    <img src="./images/publicidad/patrocinador-01.jpg" height="38px" alt=""/>
                    <img src="./images/publicidad/patrocinador-02.jpg" height="38px" style="margin: 0 5px;" alt=""/>
                    <img src="./images/publicidad/patrocinador-03.jpg" height="58px" style="margin: 0 5px;" alt=""/> */?>
                    <img src="./images/publicidad/patrocinador_07_7.jpg" height="58px" alt=""/>  
                    <img src="./images/publicidad/patrocinador_03_2.png" height="58px" style="margin: 0 5px;" alt=""/>
                    <img src="./images/publicidad/Prision_Fellowship_International.jpg" height="58px" style="margin: 0 5px;" alt=""/>
                    <img src="./images/publicidad/patrocinador_05_2.png" height="58px" style="margin: 0 5px;" alt=""/>
                    <img src="./images/publicidad/patrocinador_06_2.png" height="58px" alt=""/>  
                </div>
            </div>
    		<div class="col-sm-4">
                <br><br>
                <img src="images/titulo.png" width="100%" alt=""><br><br>
    			<form name="form1" method="post" action="" class="form-horizontal">
        			<?php if(isset($_GET["doc"])){?>
        				<input type="hidden" name="redireccion" value="<?=$_SERVER['REQUEST_URI']; ?>" />
        			<?php } 
        			if ($error == 1){?>
                        <div class="form-group">
        					<div class="col-sm-12">
                                <div class="alert alert-danger">
                                    <strong>ERROR:</strong> Usuario incorrecto.
        						</div>
                            </div>
                        </div>
                    <?php }else if ($error == 2){ ?>
                        <div class="form-group">
        					<div class="col-sm-12">
                                <div class="alert alert-danger">
                                    <strong>ERROR:</strong> Constraseña incorrecta.
        						</div>
                            </div>
        				</div>
                    <?php } ?>				
        			<div class="form-group">
                        <div class="col-sm-12">
        				    <strong>Usuario:</strong>
                        </div>
        				<div class="col-sm-12">
                            <input name="logueo" type="text" id="logueo"  value="<?=eliminarInvalidos($_POST["logueo"]); ?>" class="form-control" placeholder="Ingrese su usuario" required autofocus />
                        </div>
        			</div>	
        			<div class="form-group">
        				<div class="col-sm-12">
                            <strong>Contraseña:</strong>
                        </div>
        				<div class="col-sm-12">
                            <input name="passwordlogueo" type="password" id="passwordlogueo"  class="form-control" placeholder="Ingrese su contraseña"  required>
                        </div>
        			</div>
        			<div class="col-sm-12 text-right">
        				<input type="submit" value="Ingresar" class="btn btn-success" />
        			</div>				
    			</form>
    		</div>
        </div>
        <div class="col-sm-1"></div>
        <div class="col-sm-12">
            <hr color="#0000FF">
            <span id="footer">
                <div class="col-sm-6" style="text-align: left;">
<? /*            Calle 74 #70- 125 Sector Pilarica. Medellín- Antioquia - Colombia. */ ?>
            </div>
            <div class="col-sm-6" style="text-align: right;">
            Copyright 2019 - <?=date("Y"); ?> <a href="http://Videx.online/">Videx.online</a> desarrollado para <a href="http://www.sociedadmisionera.org/">Sociedad Misionera</a></span>
        </div>
		</div>	
	</div><?php } ?>
</body>
</html>