<?php
//
/*
smsapp_smsapp

smsapp_smsappus
OU;G{xj.Gu+}
*/
error_reporting(E_ERROR | E_PARSE);
//
date_default_timezone_set('America/Bogota');
//Variables Globales
$gloCliente = "Confraternidad Carcelaria";
$gloPrograma = "Sistema de gestión integral";
$gloEmpresa = "Confraternidad Carcelaria";

class DBbase_Sql
{
  var $Host     = "localhost";

  var $Database = "pfcoiied_db";
  var $User     = "pfcoiied_root";
  var $Password = "Pfcol2023*";

  var $Link_ID  = 0;
  var $Query_ID = 0;
  var $Record   = array();
  var $Row;

  var $Errno    = 0;
  var $Error    = "";

  var $Auto_free   = 1;   ## Set this to 1 for automatic mysql_free_result()
  var $Auto_commit = 0;   ## set this to 1 to automatically commit results

  function connect()
  {
    global $FC_Link_ID;
    if (!empty($FC_Link_ID)) {
      $this->Link_ID = $FC_Link_ID;
    }
    if (true) {
      //$this->Link_ID = mysql_pconnect($this->Host, $this->User, $this->Password);

      $this->Link_ID = mysqli_connect($this->Host, $this->User, $this->Password, $this->Database);
      if (!$this->Link_ID) {
        $this->halt("Failed to connect to MySQL: (" . mysqli_error());
      }
      //
      mysqli_set_charset($this->Link_ID, "utf8");
    }
    //mysql_select_db($this->Database,$this->Link_ID);
  }

  function query($Query_String)
  {
    $this->connect();

    #   printf("Debug: query = %s<br>n", $Query_String);

    $this->Query_ID = mysqli_query($this->Link_ID, $Query_String);
    $this->Row   = 0;
    $this->Errno = mysqli_errno($this->Link_ID);
    $this->Error = mysqli_error($this->Link_ID);
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: " . $Query_String);
    }
    return $this->Query_ID;
  }

  function ultimoId()
  {
    return mysqli_insert_id($this->Link_ID);
  }

  function next_record()
  {
    $this->Record = mysqli_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno = mysqli_errno($this->Link_ID);
    $this->Error = mysqli_error($this->Link_ID);

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_free) {
      mysqli_free_result($this->Query_ID);
      $this->Query_ID = 0;
    }
    return $stat;
  }

  function seek($pos)
  {
    $status = mysqli_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    return;
  }


  function metadata($table)
  {
    $count = 0;
    $id    = 0;
    $res   = array();

    $this->connect();
    $id = @mysql_list_fields($this->Database, $table);
    if ($id < 0) {
      $this->Errno = mysqli_errno();
      $this->Error = mysqli_error();
      $this->halt("Metadata query failed.");
    }

    $count = mysqli_num_fields($id);

    for ($i = 0; $i < $count; $i++) {
      $res[$i]["table"] = mysql_field_table($id, $i);
      $res[$i]["name"]  = mysql_field_name($id, $i);
      $res[$i]["type"]  = mysql_field_type($id, $i);
      $res[$i]["len"]   = mysql_field_len($id, $i);
      $res[$i]["flags"] = mysql_field_flags($id, $i);
      $res["meta"][$res[$i]["name"]] = $i;
      $res["num_fields"] = $count;
    }

    mysql_free_result($id);
    return $res;
  }

  function affected_rows()
  {
    return mysqli_affected_rows($this->Link_ID);
  }

  function num_rows()
  {
    return mysqli_num_rows($this->Query_ID);
  }

  function num_fields()
  {
    return mysqli_num_fields($this->Query_ID);
  }

  function nf()
  {
    return $this->num_rows();
  }

  function np()
  {
    print $this->num_rows();
  }

  function f($Name)
  {
    return $this->Record[$Name];
  }

  function p($Name)
  {
    print $this->Record[$Name];
  }

  function halt($msg)
  {
    /*printf("</td></tr></table><b>Database error:</b> %s<br>n", $msg);
    	printf("<b>MySQL Error</b>: %s (%s)<br>n",
      	$this->Errno,
      	$this->Error);*/
?><style type="text/css">
      #error-page {
        margin-top: 50px
      }

      #error-page p {
        font-size: 14px;
        line-height: 1.5;
        margin: 25px 0 20px
      }

      #error-page code {
        font-family: Consolas, Monaco, monospace
      }

      a {
        color: #21759B;
        text-decoration: none
      }

      a:hover {
        color: #D54E21
      }
    </style>
    </head>
    <div id="error-page">
      <h1>Parece que tenemos un inconveniente!</h1>
      <p>Por el momento parece que tenemos inconvenientes, la base de datos reporta un error. Pedimos disculpas por los inconvenientes ocasionados.</p>
    </div>
  <?php
    echo $msg;
    //enviarEmail("jlmflash@gmail.com", "En http://sms.videoexpress.org".$_SERVER["REQUEST_URI"]." Ha surgido el siguiente error: <br /><br />".$msg."<br /><br /><strong>Usuario Logueado:</strong> ".$_SESSION["id"]." - ".$_SESSION["nombre"], "SMS ".$gloCliente.": Error Base de Datos");
    die();
  }
}

class FC_SQL extends DBbase_Sql
{
  var $Host     = "DATABASEHOST";
  var $Database = "DATABASENAME";
  var $User     = "USERID";
  var $Password = "USERPW";

  function free_result()
  {
    return @mysql_free_result($this->Query_ID);
  }

  function rollback()
  {
    return 1;
  }

  function commit()
  {
    return 1;
  }

  function autocommit($onezero)
  {
    return 1;
  }

  function insert_id($col = "", $tbl = "", $qual = "")
  {
    return mysql_insert_id($this->Link_ID);
  }
}

//=======================
//=======================
// INVERSION:
//=======================
//=======================

class DBbase_SqlB
{
  var $Host     = "localhost";

  var $Database = "jocadima_gilberto_inversion";
  var $User     = "jocadima_transfo";
  var $Password = "123456";

  var $Link_ID  = 0;
  var $Query_ID = 0;
  var $Record   = array();
  var $Row;

  var $Errno    = 0;
  var $Error    = "";

  var $Auto_free   = 1;   ## Set this to 1 for automatic mysql_free_result()
  var $Auto_commit = 0;   ## set this to 1 to automatically commit results

  function connect()
  {
    global $FC_Link_ID;
    if (!empty($FC_Link_ID)) {
      $this->Link_ID = $FC_Link_ID;
    }
    if (0 == $this->Link_ID) {
      $this->Link_ID = mysql_pconnect($this->Host, $this->User, $this->Password);
      if (!$this->Link_ID) {
        $this->halt("Link-ID == false, pconnect failed");
      }
      if (!mysql_select_db($this->Database, $this->Link_ID)) {
        $this->halt("cannot use database " . $this->Database);
      }
    }
    mysql_select_db($this->Database, $this->Link_ID);
  }

  function query($Query_String)
  {
    $this->connect();

    #   printf("Debug: query = %s<br>n", $Query_String);

    $this->Query_ID = mysql_query($Query_String, $this->Link_ID);
    $this->Row   = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: " . $Query_String);
    }

    return $this->Query_ID;
  }

  function next_record()
  {
    $this->Record = mysql_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_free) {
      mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
    }
    return $stat;
  }

  function seek($pos)
  {
    $status = mysql_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    return;
  }

  function metadata($table)
  {
    $count = 0;
    $id    = 0;
    $res   = array();

    $this->connect();
    $id = @mysql_list_fields($this->Database, $table);
    if ($id < 0) {
      $this->Errno = mysql_errno();
      $this->Error = mysql_error();
      $this->halt("Metadata query failed.");
    }
    $count = mysql_num_fields($id);

    for ($i = 0; $i < $count; $i++) {
      $res[$i]["table"] = mysql_field_table($id, $i);
      $res[$i]["name"]  = mysql_field_name($id, $i);
      $res[$i]["type"]  = mysql_field_type($id, $i);
      $res[$i]["len"]   = mysql_field_len($id, $i);
      $res[$i]["flags"] = mysql_field_flags($id, $i);
      $res["meta"][$res[$i]["name"]] = $i;
      $res["num_fields"] = $count;
    }

    mysql_free_result($id);
    return $res;
  }

  function affected_rows()
  {
    return mysql_affected_rows($this->Link_ID);
  }

  function num_rows()
  {
    return mysql_num_rows($this->Query_ID);
  }

  function num_fields()
  {
    return mysql_num_fields($this->Query_ID);
  }

  function nf()
  {
    return $this->num_rows();
  }

  function np()
  {
    print $this->num_rows();
  }

  function f($Name)
  {
    return $this->Record[$Name];
  }

  function p($Name)
  {
    print $this->Record[$Name];
  }

  function halt($msg)
  {
    printf("</td></tr></table><b>Database error:</b> %s<br>n", $msg);
    printf(
      "<b>MySQL Error</b>: %s (%s)<br>n",
      $this->Errno,
      $this->Error
    );
    die("Session halted.");
  }
}

class FC_SQLB extends DBbase_SqlB
{
  var $Host     = "DATABASEHOST";
  var $Database = "DATABASENAME";
  var $User     = "USERID";
  var $Password = "USERPW";

  function free_result()
  {
    return @mysql_free_result($this->Query_ID);
  }

  function rollback()
  {
    return 1;
  }

  function commit()
  {
    return 1;
  }

  function autocommit($onezero)
  {
    return 1;
  }

  function insert_id($col = "", $tbl = "", $qual = "")
  {
    return mysql_insert_id($this->Link_ID);
  }
}


//=======================
//=======================
// EDUCATIVO
//=======================
//=======================

class DBbase_SqlC
{
  var $Host     = "localhost";

  var $Database = "jocadima_gilberto";
  var $User     = "jocadima_transfo";
  var $Password = "123456";

  var $Link_ID  = 0;
  var $Query_ID = 0;
  var $Record   = array();
  var $Row;

  var $Errno    = 0;
  var $Error    = "";

  var $Auto_free   = 1;   ## Set this to 1 for automatic mysql_free_result()
  var $Auto_commit = 0;   ## set this to 1 to automatically commit results

  function connect()
  {
    global $FC_Link_ID;
    if (!empty($FC_Link_ID)) {
      $this->Link_ID = $FC_Link_ID;
    }
    if (0 == $this->Link_ID) {
      $this->Link_ID = mysql_pconnect($this->Host, $this->User, $this->Password);
      if (!$this->Link_ID) {
        $this->halt("Link-ID == false, pconnect failed");
      }
      if (!mysql_select_db($this->Database, $this->Link_ID)) {
        $this->halt("cannot use database " . $this->Database);
      }
    }
    mysql_select_db($this->Database, $this->Link_ID);
  }

  function query($Query_String)
  {
    $this->connect();

    #   printf("Debug: query = %s<br>n", $Query_String);

    $this->Query_ID = mysql_query($Query_String, $this->Link_ID);
    $this->Row   = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: " . $Query_String);
    }

    return $this->Query_ID;
  }

  function next_record()
  {
    $this->Record = mysql_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_free) {
      mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
    }
    return $stat;
  }

  function seek($pos)
  {
    $status = mysql_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    return;
  }

  function metadata($table)
  {
    $count = 0;
    $id    = 0;
    $res   = array();

    $this->connect();
    $id = @mysql_list_fields($this->Database, $table);
    if ($id < 0) {
      $this->Errno = mysql_errno();
      $this->Error = mysql_error();
      $this->halt("Metadata query failed.");
    }
    $count = mysql_num_fields($id);

    for ($i = 0; $i < $count; $i++) {
      $res[$i]["table"] = mysql_field_table($id, $i);
      $res[$i]["name"]  = mysql_field_name($id, $i);
      $res[$i]["type"]  = mysql_field_type($id, $i);
      $res[$i]["len"]   = mysql_field_len($id, $i);
      $res[$i]["flags"] = mysql_field_flags($id, $i);
      $res["meta"][$res[$i]["name"]] = $i;
      $res["num_fields"] = $count;
    }

    mysql_free_result($id);
    return $res;
  }

  function affected_rows()
  {
    return mysql_affected_rows($this->Link_ID);
  }

  function num_rows()
  {
    return mysql_num_rows($this->Query_ID);
  }

  function num_fields()
  {
    return mysql_num_fields($this->Query_ID);
  }

  function nf()
  {
    return $this->num_rows();
  }

  function np()
  {
    print $this->num_rows();
  }

  function f($Name)
  {
    return $this->Record[$Name];
  }

  function p($Name)
  {
    print $this->Record[$Name];
  }

  function halt($msg)
  {
    printf("</td></tr></table><b>Database error:</b> %s<br>n", $msg);
    printf(
      "<b>MySQL Error</b>: %s (%s)<br>n",
      $this->Errno,
      $this->Error
    );
    die("Session halted.");
  }
}

class FC_SQLC extends DBbase_SqlC
{
  var $Host     = "DATABASEHOST";
  var $Database = "DATABASENAME";
  var $User     = "USERID";
  var $Password = "USERPW";

  function free_result()
  {
    return @mysql_free_result($this->Query_ID);
  }

  function rollback()
  {
    return 1;
  }

  function commit()
  {
    return 1;
  }

  function autocommit($onezero)
  {
    return 1;
  }

  function insert_id($col = "", $tbl = "", $qual = "")
  {
    return mysql_insert_id($this->Link_ID);
  }
}

//=======================
//=======================
// OTRO_COBRANZA
//=======================
//=======================

class DBbase_SqlD
{
  var $Host     = "localhost";

  var $Database = "jocadima_gilberto_otros";
  var $User     = "jocadima_transfo";
  var $Password = "123456";

  var $Link_ID  = 0;
  var $Query_ID = 0;
  var $Record   = array();
  var $Row;

  var $Errno    = 0;
  var $Error    = "";

  var $Auto_free   = 1;   ## Set this to 1 for automatic mysql_free_result()
  var $Auto_commit = 0;   ## set this to 1 to automatically commit results

  function connect()
  {
    global $FC_Link_ID;
    if (!empty($FC_Link_ID)) {
      $this->Link_ID = $FC_Link_ID;
    }
    if (0 == $this->Link_ID) {
      $this->Link_ID = mysql_pconnect($this->Host, $this->User, $this->Password);
      if (!$this->Link_ID) {
        $this->halt("Link-ID == false, pconnect failed");
      }
      if (!mysql_select_db($this->Database, $this->Link_ID)) {
        $this->halt("cannot use database " . $this->Database);
      }
    }
    mysql_select_db($this->Database, $this->Link_ID);
  }

  function query($Query_String)
  {
    $this->connect();

    #   printf("Debug: query = %s<br>n", $Query_String);

    $this->Query_ID = mysql_query($Query_String, $this->Link_ID);
    $this->Row   = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: " . $Query_String);
    }

    return $this->Query_ID;
  }

  function next_record()
  {
    $this->Record = mysql_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_free) {
      mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
    }
    return $stat;
  }

  function seek($pos)
  {
    $status = mysql_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    return;
  }

  function metadata($table)
  {
    $count = 0;
    $id    = 0;
    $res   = array();

    $this->connect();
    $id = @mysql_list_fields($this->Database, $table);
    if ($id < 0) {
      $this->Errno = mysql_errno();
      $this->Error = mysql_error();
      $this->halt("Metadata query failed.");
    }
    $count = mysql_num_fields($id);

    for ($i = 0; $i < $count; $i++) {
      $res[$i]["table"] = mysql_field_table($id, $i);
      $res[$i]["name"]  = mysql_field_name($id, $i);
      $res[$i]["type"]  = mysql_field_type($id, $i);
      $res[$i]["len"]   = mysql_field_len($id, $i);
      $res[$i]["flags"] = mysql_field_flags($id, $i);
      $res["meta"][$res[$i]["name"]] = $i;
      $res["num_fields"] = $count;
    }

    mysql_free_result($id);
    return $res;
  }

  function affected_rows()
  {
    return mysql_affected_rows($this->Link_ID);
  }

  function num_rows()
  {
    return mysql_num_rows($this->Query_ID);
  }

  function num_fields()
  {
    return mysql_num_fields($this->Query_ID);
  }

  function nf()
  {
    return $this->num_rows();
  }

  function np()
  {
    print $this->num_rows();
  }

  function f($Name)
  {
    return $this->Record[$Name];
  }

  function p($Name)
  {
    print $this->Record[$Name];
  }

  function halt($msg)
  {
    printf("</td></tr></table><b>Database error:</b> %s<br>n", $msg);
    printf(
      "<b>MySQL Error</b>: %s (%s)<br>n",
      $this->Errno,
      $this->Error
    );
    die("Session halted.");
  }
}

class FC_SQLD extends DBbase_SqlD
{
  var $Host     = "DATABASEHOST";
  var $Database = "DATABASENAME";
  var $User     = "USERID";
  var $Password = "USERPW";

  function free_result()
  {
    return @mysql_free_result($this->Query_ID);
  }

  function rollback()
  {
    return 1;
  }

  function commit()
  {
    return 1;
  }

  function autocommit($onezero)
  {
    return 1;
  }

  function insert_id($col = "", $tbl = "", $qual = "")
  {
    return mysql_insert_id($this->Link_ID);
  }
}


/* 
funciones generales que podrian servir
*/
function redirect($url, $message = "", $delay = 0)
{
  /* redirects to a new URL using meta tags */
  echo "<meta http-equiv='Refresh' content='$delay; url=$url'>";
  if (!empty($message)) echo "<div style='font-family: Arial, Sans-serif; font-size: 12pt;' align=center>$message</div>";
  die;
}

function is_logged_in()
{
  global $_SESSION;
  if (empty($_SESSION["id"])) {
    return 0;
  } else {
    return 1;
  }
}

function is_admin()
{
  global $_SESSION;
  if (empty($_SESSION["administrador"])) {
    return 0;
  } else {
    return 1;
  }
}

function require_login()
{
  /* this function checks to see if the user is logged in.  if not, it will show
 * the login screen before allowing the user to continue */
  global $_SESSION;
  if (! is_logged_in()) {
    redirect("main.php?doc=login&mn=none");
  }
}

function require_admin()
{
  /* this function checks to see if the user is logged in.  if not, it will show
 * the login screen before allowing the user to continue */
  global $_SESSION;
  if (! is_admin()) {
    redirect("main.php?doc=login&mn=none");
  }
}

function getRealIP()
{

  if ($_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
    $client_ip =
      (!empty($_SERVER['REMOTE_ADDR'])) ?
      $_SERVER['REMOTE_ADDR']
      : ((!empty($_ENV['REMOTE_ADDR'])) ?
        $_ENV['REMOTE_ADDR']
        :
        "unknown");

    // los proxys van a�adiendo al final de esta cabecera
    // las direcciones ip que van "ocultando". Para localizar la ip real
    // del usuario se comienza a mirar por el principio hasta encontrar
    // una direcci�n ip que no sea del rango privado. En caso de no
    // encontrarse ninguna se toma como valor el REMOTE_ADDR

    $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);

    reset($entries);
    while (list(, $entry) = each($entries)) {
      $entry = trim($entry);
      if (preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list)) {
        // http://www.faqs.org/rfcs/rfc1918.html
        $private_ip = array(
          '/^0\./',
          '/^127\.0\.0\.1/',
          '/^192\.168\..*/',
          '/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
          '/^10\..*/'
        );

        $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);

        if ($client_ip != $found_ip) {
          $client_ip = $found_ip;
          break;
        }
      }
    }
  } else {
    $client_ip =
      (!empty($_SERVER['REMOTE_ADDR'])) ?
      $_SERVER['REMOTE_ADDR']
      : ((!empty($_ENV['REMOTE_ADDR'])) ?
        $_ENV['REMOTE_ADDR']
        :
        "unknown");
  }

  return $client_ip;
}

function birthday($birthday)
{
  list($year, $month, $day) = explode("-", $birthday);
  $year_diff  = date("Y") - $year;
  $month_diff = date("m") - $month;
  $day_diff   = date("d") - $day;
  if ($day_diff < 0 || $month_diff < 0)
    $year_diff--;
  return $year_diff;
}

function daysDifference($endDate, $beginDate)
{

  //explode the date by "-" and storing to array
  $date_parts1 = explode("-", $beginDate);
  $date_parts2 = explode("-", $endDate);
  //gregoriantojd() Converts a Gregorian date to Julian Day Count
  $start_date = gregoriantojd($date_parts1[1], $date_parts1[2], $date_parts1[0]);
  $end_date = gregoriantojd($date_parts2[1], $date_parts2[2], $date_parts2[0]);
  return $end_date - $start_date;
}

function enviarEmail($mailito, $mensaje, $asunto)
{
  $VarEmail = htmlspecialchars($mailito);
  $email_from = "soporte@jocadimama.com";

  //La lista de precio son los productos que en su campo listaPrecio estan marcados con un 1.
  //$fileatt_type = "application/octet-stream";

  $VarMes = array("Nada", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

  $VarFechaHoy .= date("d");
  $VarFechaHoy .= $VarMes[date("n")];
  $VarFechaHoy .= date("y");

  //$fileatt_name = $VarFactura.'.pdf';
  //$fileatt_name2 = 'ARCHIVO.pdf';

  $email_subject = $asunto;


  $VarFechaHoy = date("d");
  $VarFechaHoy .= " de ";
  $VarFechaHoy .= $VarMes[date("n")];
  $VarFechaHoy .= " a las ";
  $VarFechaHoy .= (date("h", strtotime("+1 hours")));
  $VarFechaHoy .= ":";
  $VarFechaHoy .= date("i");
  $VarFechaHoy .= " " . date("a");

  $email_to = $VarEmail;
  $headers = "From: " . $email_from;

  //$ch = curl_init();
  //$timeout = 0;

  //ESTE SI ES *******************************************************
  //ESTE SI ES *******************************************************
  //ESTE SI ES *******************************************************
  //curl_setopt ($ch, CURLOPT_URL, 'http://www.dynamicconsultants.net/');
  //curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  //$data = curl_exec($ch);
  //curl_close($ch);
  $semi_rand = md5(time());
  $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";


  $headers .= "\nMIME-Version: 1.0\n" .
    "Content-Type: multipart/mixed;\n" .
    " boundary=\"{$mime_boundary}\"";

  $email_message .= "This is a multi-part message in MIME format.\n\n" .
    "--{$mime_boundary}\n" .
    "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n";
  $email_message .= "\n\n";
  $email_message .= '<style type="text/css">
			<!--
			.Estilo2 {color: #FF0000}
			-->
			</style>
		

			<body style="font-size:11px">
			<table width="600" border="0">
			  <tr>
				<td colspan="4"><img src="http://www.goafinaval.com/gilberto/images/email.jpg" width="600" height="73" /></td>
			  </tr>

			  <tr>

				<td colspan="4"><p>' . $mensaje . '</p>

				<p>&nbsp;</p></td>

			  </tr>

			  <tr>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

			  </tr>

			<tr>
				<td colspan="4">&nbsp;</td>

			  </tr>

			  <tr>

				<td colspan="4"><hr><div align="center" style="font-size:9px"><a href="http://www.goafinaval.com/gilberto/">FINAVAL, S.A.S.</a><br />
				Todos los derechos Reservados 2010-2011</div></td>

			  </tr>

			</table>

			</body>

			</html>';

  $email_message .= "\n\n";

  /*$data = chunk_split(base64_encode($data)); 
		$email_message .= "--{$mime_boundary}\n" . 
		"Content-Type: {$fileatt_type};\n" . 

		" name=\"{$fileatt_name}\"\n" . 

		"Content-Transfer-Encoding: base64\n\n" . 

		$data . "\n\n" . 

		"--{$mime_boundary}\n"; 



		if($anexo == 1)
		{
			$data2 = chunk_split(base64_encode($data2)); 
	
			$email_message .= "Content-Type: {$fileatt_type};\n" . 
	
			" name=\"{$fileatt_name2}\"\n" . 
	
			"Content-Transfer-Encoding: base64\n\n" . 
	
			$data2 . "\n\n" . 
	
			"--{$mime_boundary}--\n"; 
		}*/



  $ok = @mail($email_to, $email_subject, $email_message, $headers);



  if ($ok) {
    return 1;
  } else {
    return 0;
  }
}


function enviarEmailINV($mailito, $mensaje, $asunto, $invitacion)
{
  $VarEmail = htmlspecialchars($mailito);
  $email_from = "operaciones@goafinaval.com";

  //La lista de precio son los productos que en su campo listaPrecio estan marcados con un 1.
  //$fileatt_type = "application/octet-stream";

  $VarMes = array("Nada", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

  $VarFechaHoy .= date("d");
  $VarFechaHoy .= $VarMes[date("n")];
  $VarFechaHoy .= date("y");

  //$fileatt_name = $VarFactura.'.pdf';
  //$fileatt_name2 = 'ARCHIVO.pdf';

  $email_subject = $asunto;


  $VarFechaHoy = date("d");
  $VarFechaHoy .= " de ";
  $VarFechaHoy .= $VarMes[date("n")];
  $VarFechaHoy .= " a las ";
  $VarFechaHoy .= (date("h", strtotime("+1 hours")));
  $VarFechaHoy .= ":";
  $VarFechaHoy .= date("i");
  $VarFechaHoy .= " " . date("a");

  $email_to = $VarEmail;
  $headers = "From: " . $email_from;

  //$ch = curl_init();
  //$timeout = 0;

  //ESTE SI ES *******************************************************
  //ESTE SI ES *******************************************************
  //ESTE SI ES *******************************************************
  //curl_setopt ($ch, CURLOPT_URL, 'http://www.dynamicconsultants.net/');
  //curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
  //curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
  //$data = curl_exec($ch);
  //curl_close($ch);
  $semi_rand = md5(time());
  $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";


  $headers .= "\nMIME-Version: 1.0\n" .
    "Content-Type: multipart/mixed;\n" .
    " boundary=\"{$mime_boundary}\"";

  $email_message .= "This is a multi-part message in MIME format.\n\n" .
    "--{$mime_boundary}\n" .
    "Content-Type:text/html; charset=\"iso-8859-1\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n";
  $email_message .= "\n\n";
  $email_message .= '<style type="text/css">
			<!--
			.Estilo2 {color: #FF0000}
			-->
			</style>
		

			<body style="font-size:11px">
			<table width="600" border="0">
			  <tr>
				<td colspan="4"><img src="http://www.goafinaval.com/gilberto/images/email.jpg" width="600" height="73" /></td>
			  </tr>

			  <tr>

				<td colspan="4"><p>' . $mensaje . '</p>

				<p>&nbsp;</p></td>

			  </tr>

			  <tr>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

				<td>&nbsp;</td>

			  </tr>

			<tr>
				<td colspan="4">&nbsp;</td>

			  </tr>

			  <tr>

				<td colspan="4"><hr><div align="center" style="font-size:9px"><a href="http://www.goafinaval.com/gilberto/">FINAVAL, S.A.S.</a><br />
				Todos los derechos Reservados 2010-2011</div></td>

			  </tr>

			</table>

			</body>

			</html>';

  $email_message .= "\n";
  $email_message .= "--{$mime_boundary}\n" . "Content-Type: text/calendar; method=REQUEST;\n";
  $email_message .= 'charset="UTF-8"';
  $email_message .= "\n";
  $email_message .= "Content-Transfer-Encoding: 7bit\n";
  $email_mensaje .= $invitacion;
  $email_message .= "\n\n";

  /*$data = chunk_split(base64_encode($data)); 
		$email_message .= "--{$mime_boundary}\n" . 
		"Content-Type: {$fileatt_type};\n" . 

		" name=\"{$fileatt_name}\"\n" . 

		"Content-Transfer-Encoding: base64\n\n" . 

		$data . "\n\n" . 

		"--{$mime_boundary}\n"; 



		if($anexo == 1)
		{
			$data2 = chunk_split(base64_encode($data2)); 
	
			$email_message .= "Content-Type: {$fileatt_type};\n" . 
	
			" name=\"{$fileatt_name2}\"\n" . 
	
			"Content-Transfer-Encoding: base64\n\n" . 
	
			$data2 . "\n\n" . 
	
			"--{$mime_boundary}--\n"; 
		}*/

  $ok = @mail($email_to, $email_subject, $email_message, $headers);



  if ($ok) {
    return 1;
  } else {
    return 0;
  }
}

//
// $tipo 1 = CEDULA 2 = nombres
function encontrarCedula($dato, $tipo, $idactual)
{
  $PSN_AVAL = new DBbase_Sql;
  $PSN_INVERSION = new DBbase_SqlB;
  $PSN_EDUCATIVO = new DBbase_SqlC;
  $PSN_COBRANZA = new DBbase_SqlD;
  //
  $retorno["encontro"] = 0;
  //
  //
  $sql = "SELECT id, identificacion_deudor, nombre_deudor ";
  $sql .= " FROM cuporotativo ";
  if ($tipo == 1) {
    $sql .= " WHERE trim(identificacion_deudor) = '" . trim($dato) . "'";
  } else {
    $sql .= " WHERE trim(nombre_deudor) = '" . trim($dato) . "'";
  }
  //
  //
  //*********************************************************
  // AVAL - CUPOROTATIVO
  //*********************************************************
  $PSN_AVAL->query($sql);
  $numero = $PSN_AVAL->num_rows();
  if ($numero > 0) {
    while ($PSN_AVAL->next_record()) {
      $retorno["cuporotativo"]["id"][] = $PSN_AVAL->f('id');
      $retorno["cuporotativo"]["nombre_deudor"][] = $PSN_AVAL->f('nombre_deudor');
      $retorno["cuporotativo"]["identificacion_deudor"][] = $PSN_AVAL->f('identificacion_deudor');
      $retorno["encontro"]++;
    }
  }
  //
  //
  $sql = "SELECT id, identificacion_deudor, nombre_deudor ";
  $sql .= " FROM estudiante ";
  if ($tipo == 1) {
    $sql .= " WHERE trim(identificacion_deudor) = '" . trim($dato) . "'";
  } else {
    $sql .= " WHERE trim(nombre_deudor) = '" . trim($dato) . "'";
  }
  //
  //
  //*********************************************************
  // AVAL
  //*********************************************************
  //
  //$PSN_AVAL->query($sql." AND id != '".$idactual."'");
  $PSN_AVAL->query($sql);
  $numero = $PSN_AVAL->num_rows();
  if ($numero > 0) {
    while ($PSN_AVAL->next_record()) {
      $retorno["aval"]["id"][] = $PSN_AVAL->f('id');
      $retorno["aval"]["nombre_deudor"][] = $PSN_AVAL->f('nombre_deudor');
      $retorno["aval"]["identificacion_deudor"][] = $PSN_AVAL->f('identificacion_deudor');
      $retorno["encontro"]++;
    }
  }
  //
  //
  //*********************************************************
  // INVERSION
  //*********************************************************
  $PSN_INVERSION->query($sql);
  $numero = $PSN_INVERSION->num_rows();
  if ($numero > 0) {
    while ($PSN_INVERSION->next_record()) {
      $retorno["inversion"]["id"][] = $PSN_INVERSION->f('id');
      $retorno["inversion"]["nombre_deudor"][] = $PSN_INVERSION->f('nombre_deudor');
      $retorno["inversion"]["identificacion_deudor"][] = $PSN_INVERSION->f('identificacion_deudor');
      $retorno["encontro"]++;
    }
  }
  //
  //
  $sql = "SELECT id, identificacion, nombre ";
  $sql .= " FROM estudiante ";
  if ($tipo == 1) {
    $sql .= " WHERE trim(identificacion) = '" . trim($dato) . "'";
  } else {
    $sql .= " WHERE trim(nombre) = '" . trim($dato) . "'";
  }
  //*********************************************************
  // EDUCATIVO
  //*********************************************************
  $PSN_EDUCATIVO->query($sql);
  $numero = $PSN_EDUCATIVO->num_rows();
  if ($numero > 0) {
    while ($PSN_EDUCATIVO->next_record()) {
      $retorno["educativo"]["id"][] = $PSN_EDUCATIVO->f('id');
      $retorno["educativo"]["nombre_deudor"][] = $PSN_EDUCATIVO->f('nombre');
      $retorno["educativo"]["identificacion_deudor"][] = $PSN_EDUCATIVO->f('identificacion');
      $retorno["encontro"]++;
    }
  }
  //*********************************************************
  // COBRANZA
  //*********************************************************
  $PSN_COBRANZA->query($sql);
  $numero = $PSN_COBRANZA->num_rows();
  if ($numero > 0) {
    while ($PSN_COBRANZA->next_record()) {
      $retorno["cobranza"]["id"][] = $PSN_COBRANZA->f('id');
      $retorno["cobranza"]["nombre_deudor"][] = $PSN_COBRANZA->f('nombre');
      $retorno["cobranza"]["identificacion_deudor"][] = $PSN_COBRANZA->f('identificacion');
      $retorno["encontro"]++;
    }
  }
  //
  //
  //
  return $retorno;
}

//======================================================
//IMPRIMIR CEDULA
//======================================================
function imprimirEncontrarCedula($dato, $tipo, $idactual)
{
  $retorno = encontrarCedula($dato, $tipo, $idactual);
  if ($retorno["encontro"] > 0) {
  ?><br /><br />
    <table width="100%" border="1" bgcolor="#FDFDFD">
      <tr><?php
          if ($retorno["encontro"] > 1) {
          ?><th colspan="4" height="40px" valign="middle" style="vertical-align:middle;background-color:#990000;color:#FFFFFF">
            <h2>OJO: LA IDENTIFICACI&Oacute;N SE ENCUENTRA EN OTROS SISTEMAS</h2>
          </th><?php
              } else {
                ?><th colspan="4" height="40px" valign="middle" style="vertical-align:middle;">
            <h2>LA IDENTIFICACI&Oacute;N NO SE ENCUENTRA EN NINGUN OTRO LUGAR:</h2>
          </th><?php
              }
                ?>
      </tr>
      <tr>
        <th>Sistema</th>
        <th>ID</th>
        <th>Nombre</th>
        <th>Identificaci&oacute;n</th>
      </tr>
      <?php
      //
      //
      //
      //********************************************************
      //	PARA PLATAFORMA CUPO ROTATIVO
      //********************************************************
      $i = 0;
      while ($retorno["cuporotativo"]["id"][$i] != "") {
      ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=cup_mainl&id=<?= $retorno["cuporotativo"]["id"][$i]; ?>" target="_blank">CUPO ROTATIVO</a></td>
          <td><?= $retorno["cuporotativo"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=cup_mainl&id=<?= $retorno["cuporotativo"]["id"][$i]; ?>" target="_blank"><?= $retorno["cuporotativo"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["cuporotativo"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
              $i++;
            }
            //
            //
            //********************************************************
            //	PARA PLATAFORMA AVAL
            //********************************************************
            $i = 0;
            while ($retorno["aval"]["id"][$i] != "") {
              ?><tr <?php
                    if ($idactual == $retorno["aval"]["id"][$i]) {
                    ?>bgcolor="#00D800" <?php
                              }
                                ?>>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=os_mainl&id=<?= $retorno["aval"]["id"][$i]; ?>" target="_blank">AVAL</a></td>
          <td><?= $retorno["aval"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=os_mainl&id=<?= $retorno["aval"]["id"][$i]; ?>" target="_blank"><?= $retorno["aval"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["aval"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
              $i++;
            }
            //
            //
            //********************************************************
            //	PARA PLATAFORMA INVERSION
            //********************************************************
            $i = 0;
            while ($retorno["inversion"]["id"][$i] != "") {
              ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto_inversion/index.php?doc=os_mainl&id=<?= $retorno["inversion"]["id"][$i]; ?>" target="_blank">INVERSION</a></td>
          <td><?= $retorno["inversion"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_inversion/index.php?doc=os_mainl&id=<?= $retorno["inversion"]["id"][$i]; ?>" target="_blank"><?= $retorno["inversion"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["inversion"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
              $i++;
            }
            //
            //
            //********************************************************
            //	PARA PLATAFORMA EDUCATIVO
            //********************************************************
            $i = 0;
            while ($retorno["educativo"]["id"][$i] != "") {
              ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto/index.php?doc=cup_mainl&id=<?= $retorno["educativo"]["id"][$i]; ?>" target="_blank">EDUCATIVO</a></td>
          <td><?= $retorno["educativo"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto/index.php?doc=cup_mainl&id=<?= $retorno["educativo"]["id"][$i]; ?>" target="_blank"><?= $retorno["educativo"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["educativo"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
              $i++;
            }
            //
            //
            //********************************************************
            //	PARA PLATAFORMA COBRANZA
            //********************************************************
            $i = 0;
            while ($retorno["cobranza"]["id"][$i] != "") {
              ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto_otro/index.php?doc=os_mainl&id=<?= $retorno["cobranza"]["id"][$i]; ?>" target="_blank">SECTOR REAL</a></td>
          <td><?= $retorno["cobranza"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_otro/index.php?doc=os_mainl&id=<?= $retorno["cobranza"]["id"][$i]; ?>" target="_blank"><?= $retorno["cobranza"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["cobranza"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
              $i++;
            }
              ?>
    </table><br /><br /><?php
                      }
                    }

                    //======================================================
                    //IMPRIMIR CEDULA CUPO ROTATIVO
                    //======================================================
                    function imprimirEncontrarCedulaCup($dato, $tipo, $idactual)
                    {
                      $retorno = encontrarCedula($dato, $tipo, $idactual);
                      if ($retorno["encontro"] > 0) {
                        ?><br /><br />
    <table width="100%" border="1" bgcolor="#FDFDFD">
      <tr><?php
                        if ($retorno["encontro"] > 1) {
          ?><th colspan="4" height="40px" valign="middle" style="vertical-align:middle;background-color:#990000;color:#FFFFFF">
            <h2>OJO: LA IDENTIFICACI&Oacute;N SE ENCUENTRA EN OTROS SISTEMAS</h2>
          </th><?php
                        } else {
                ?><th colspan="4" height="40px" valign="middle" style="vertical-align:middle;">
            <h2>LA IDENTIFICACI&Oacute;N NO SE ENCUENTRA EN NINGUN OTRO LUGAR:</h2>
          </th><?php
                        }
                ?>
      </tr>
      <tr>
        <th>Sistema</th>
        <th>ID</th>
        <th>Nombre</th>
        <th>Identificaci&oacute;n</th>
      </tr>
      <?php
                        //
                        //
                        //
                        //********************************************************
                        //	PARA PLATAFORMA CUPO ROTATIVO
                        //********************************************************
                        $i = 0;
                        while ($retorno["cuporotativo"]["id"][$i] != "") {
      ?><tr <?php
                          if ($idactual == $retorno["cuporotativo"]["id"][$i]) {
            ?>bgcolor="#00D800" <?php
                              }
                                ?>>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=cup_mainl&id=<?= $retorno["cuporotativo"]["id"][$i]; ?>" target="_blank">CUPO ROTATIVO</a></td>
          <td><?= $retorno["cuporotativo"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=cup_mainl&id=<?= $retorno["cuporotativo"]["id"][$i]; ?>" target="_blank"><?= $retorno["cuporotativo"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["cuporotativo"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
                          $i++;
                        }
                        //
                        //
                        //********************************************************
                        //	PARA PLATAFORMA AVAL
                        //********************************************************
                        $i = 0;
                        while ($retorno["aval"]["id"][$i] != "") {
              ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=os_mainl&id=<?= $retorno["aval"]["id"][$i]; ?>" target="_blank">AVAL</a></td>
          <td><?= $retorno["aval"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_aval/index.php?doc=os_mainl&id=<?= $retorno["aval"]["id"][$i]; ?>" target="_blank"><?= $retorno["aval"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["aval"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
                          $i++;
                        }
                        //
                        //
                        //********************************************************
                        //	PARA PLATAFORMA INVERSION
                        //********************************************************
                        $i = 0;
                        while ($retorno["inversion"]["id"][$i] != "") {
              ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto_inversion/index.php?doc=os_mainl&id=<?= $retorno["inversion"]["id"][$i]; ?>" target="_blank">INVERSION</a></td>
          <td><?= $retorno["inversion"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_inversion/index.php?doc=os_mainl&id=<?= $retorno["inversion"]["id"][$i]; ?>" target="_blank"><?= $retorno["inversion"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["inversion"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
                          $i++;
                        }
                        //
                        //
                        //********************************************************
                        //	PARA PLATAFORMA EDUCATIVO
                        //********************************************************
                        $i = 0;
                        while ($retorno["educativo"]["id"][$i] != "") {
              ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto/index.php?doc=cup_mainl&id=<?= $retorno["educativo"]["id"][$i]; ?>" target="_blank">EDUCATIVO</a></td>
          <td><?= $retorno["educativo"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto/index.php?doc=cup_mainl&id=<?= $retorno["educativo"]["id"][$i]; ?>" target="_blank"><?= $retorno["educativo"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["educativo"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
                          $i++;
                        }
                        //
                        //
                        //********************************************************
                        //	PARA PLATAFORMA COBRANZA
                        //********************************************************
                        $i = 0;
                        while ($retorno["cobranza"]["id"][$i] != "") {
              ?><tr>
          <td><a href="http://www.goafinaval.com/gilberto_otro/index.php?doc=os_mainl&id=<?= $retorno["cobranza"]["id"][$i]; ?>" target="_blank">SECTOR REAL</a></td>
          <td><?= $retorno["cobranza"]["id"][$i]; ?></td>
          <td><a href="http://www.goafinaval.com/gilberto_otro/index.php?doc=os_mainl&id=<?= $retorno["cobranza"]["id"][$i]; ?>" target="_blank"><?= $retorno["cobranza"]["nombre_deudor"][$i]; ?></a></td>
          <td><?= $retorno["cobranza"]["identificacion_deudor"][$i]; ?></td>
        </tr><?php
                          $i++;
                        }
              ?>
    </table><br /><br /><?php
                      }
                    }



                    //*********************************************************
                    //Jorge Luis Martinez Mazuera - 23 de Julio de 2008
                    //DESCRIPCION: Funcion para eliminar caracteres no validos.
                    //*********************************************************
                    function eliminarInvalidos($texto)
                    {
                      strip_tags($texto);
                      addslashes($texto);
                      trim($texto);
                      return $texto;
                      //static $acentos = "áéíóúÁÉÍÓÚàèìòùÀÈÌÒÙâêîôûÂÊÎÔÛäëïöüÄËÏÖÜ";
                      //static $validos = "aeiouAEIOUaeiouAEIOUaeiouAEIOUaeiouAEIOU";
                      //return strtr($texto, $acentos, $validos);
                    }

                    function extension_archivo($ruta)
                    {
                      $res = explode(".", $ruta);
                      $extension = $res[count($res) - 1];
                      return $extension;
                    } // fin extension_archivo


                    function sendIcalEvent($from_name, $from_address, $to_name, $to_address, $startTime, $endTime, $subject, $description, $location)
                    {
                      $domain = 'exchangecore.com';
                      //Create Email Headers
                      $mime_boundary = "----Meeting Booking----" . MD5(TIME());
                      $headers = "From: " . $from_name . " <" . $from_address . ">\n";
                      $headers .= "Reply-To: " . $from_name . " <" . $from_address . ">\n";
                      $headers .= "MIME-Version: 1.0\n";
                      $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
                      $headers .= "Content-class: urn:content-classes:calendarmessage\n";
                      //Create Email Body (HTML)
                      $message = "--$mime_boundary\r\n";
                      $message .= "Content-Type: text/html; charset=UTF-8\n";
                      $message .= "Content-Transfer-Encoding: 8bit\n\n";
                      $message .= "<html>\n";
                      $message .= "<body>\n";
                      $message .= '<p>Dear ' . $to_name . ',</p>';
                      $message .= '<p>' . $description . '</p>';
                      $message .= "</body>\n";
                      $message .= "</html>\n";
                      $message .= "--$mime_boundary\r\n";

                      //status / url / recurid /

                      $ical = 'BEGIN:VCALENDAR' . "\r\n" .
                        'PRODID:-//Microsoft Corporation//Outlook 10.0 MIMEDIR//EN' . "\r\n" .
                        'VERSION:2.0' . "\r\n" .
                        'METHOD:REQUEST' . "\r\n" .
                        'BEGIN:VTIMEZONE' . "\r\n" .
                        'TZID:Eastern Time' . "\r\n" .
                        'BEGIN:STANDARD' . "\r\n" .
                        'DTSTART:20091101T020000' . "\r\n" .
                        'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=1SU;BYMONTH=11' . "\r\n" .
                        'TZOFFSETFROM:-0400' . "\r\n" .
                        'TZOFFSETTO:-0500' . "\r\n" .
                        'TZNAME:EST' . "\r\n" .
                        'END:STANDARD' . "\r\n" .
                        'BEGIN:DAYLIGHT' . "\r\n" .
                        'DTSTART:20090301T020000' . "\r\n" .
                        'RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=2SU;BYMONTH=3' . "\r\n" .
                        'TZOFFSETFROM:-0500' . "\r\n" .
                        'TZOFFSETTO:-0400' . "\r\n" .
                        'TZNAME:EDST' . "\r\n" .
                        'END:DAYLIGHT' . "\r\n" .
                        'END:VTIMEZONE' . "\r\n" .
                        'BEGIN:VEVENT' . "\r\n" .
                        'ORGANIZER;CN="' . $from_name . '":MAILTO:' . $from_address . "\r\n" .
                        'ATTENDEE;CN="' . $to_name . '";ROLE=REQ-PARTICIPANT;RSVP=TRUE:MAILTO:' . $to_address . "\r\n" .
                        'LAST-MODIFIED:' . date("Ymd\TGis") . "\r\n" .
                        'UID:' . date("Ymd\TGis", strtotime($startTime)) . rand() . "@" . $domain . "\r\n" .
                        'DTSTAMP:' . date("Ymd\TGis") . "\r\n" .
                        'DTSTART;TZID="Eastern Time":' . date("Ymd\THis", strtotime($startTime)) . "\r\n" .
                        'DTEND;TZID="Eastern Time":' . date("Ymd\THis", strtotime($endTime)) . "\r\n" .
                        'TRANSP:OPAQUE' . "\r\n" .
                        'SEQUENCE:1' . "\r\n" .
                        'SUMMARY:' . $subject . "\r\n" .
                        'LOCATION:' . $location . "\r\n" .
                        'CLASS:PUBLIC' . "\r\n" .
                        'PRIORITY:5' . "\r\n" .
                        'BEGIN:VALARM' . "\r\n" .
                        'TRIGGER:-PT15M' . "\r\n" .
                        'ACTION:DISPLAY' . "\r\n" .
                        'DESCRIPTION:Reminder' . "\r\n" .
                        'END:VALARM' . "\r\n" .
                        'END:VEVENT' . "\r\n" .
                        'END:VCALENDAR' . "\r\n";
                      $message .= 'Content-Type: text/calendar;name="cobranza.ics";method=REQUEST\n';
                      $message .= "Content-Transfer-Encoding: 8bit\n\n";
                      $message .= $ical;

                      $mailsent = mail($to_address, $subject, $message, $headers);

                      if ($mailsent) {
                        return true;
                      } else {
                        return false;
                      }
                    }


                    function soloNumeros($string)
                    {
                      $result = preg_replace("/[^0-9]/", "", $string);
                      return $result === "" ? "0" : $result;
                    }

                    function alertaDiasFaltantes()
                    {
                      $diasParaSiguientePeriodo = 0;
                      $siguiente_anho = date("Y", strtotime("+1 year"));
                      $siguienteFecha = $siguiente_anho . "-01-31";

                      if (date("n") == 1) {
                        $siguienteFecha = date("Y") . "-01-31";
                      }

                      $diasFaltantes = daysDifference($siguienteFecha, date("Y-m-d"));
                        ?><div class="noti-all">
    <div class="text-center">
      <b>Atención:</b> Quedan <?= $diasFaltantes; ?> días para cumplir nuestras metas.
    </div>
  </div><?php
                    } ?>
