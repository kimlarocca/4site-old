<?php require_once('Connections/cms.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($theValue) : mysqli_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php
ini_set('session.save_path',getcwd(). '/../tmp/');
?>
<?php
// *** Validate request to login to this site.
if (!isset($_SESSION)) {
  session_start();
}

$loginFormAction = $_SERVER['PHP_SELF'];
if (isset($_GET['accesscheck'])) {
  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
}

if (isset($_POST['username'])) {
  $loginUsername=$_POST['username'];
  $password=$_POST['password'];
  $MM_fldUserAuthorization = "";
  $MM_redirectLoginSuccess = "home.php";
  $MM_redirectLoginFailed = "index.php?action=failed&1";
  $MM_redirecttoReferrer = false;
  mysqli_select_db($cms, $database_cms);

  $LoginRS__query="SELECT username, password FROM cmsUsers WHERE username='".$loginUsername."' AND password='".$password."'";

  $LoginRS = mysqli_query($cms, $LoginRS__query) or die(mysqli_error($cms));
  $loginFoundUser = mysqli_num_rows($LoginRS);
  if ($loginFoundUser) {
      $MM_redirectLoginFailed = "index.php?action=failed&2";
     $loginStrGroup = "";

	if (PHP_VERSION >= 5.1) {session_regenerate_id(true);} else {session_regenerate_id();}
    //declare two session variables and assign them
    $_SESSION['MM_Username'] = $loginUsername;
    $_SESSION['MM_UserGroup'] = $loginStrGroup;

    if (isset($_SESSION['PrevUrl']) && false) {
      $MM_redirectLoginSuccess = $_SESSION['PrevUrl'];
    }
    header("Location: " . $MM_redirectLoginSuccess );
  }
  else {
    header("Location: ". $MM_redirectLoginFailed );
  }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles.css"/>
<title>Website Administration Login</title>
</head>

<body>
<div class="logo"><img src="images/logo-new.png" height="300"/></div>
<h1 class="twd_centered">Client Login</h1>
<div class="twd_container twd_centered" style="padding-top:20px;">
<?php
//check if login failed
if ($_GET['action'] == 'failed') print '<p style="color:#ff0000;">LOGIN FAILED! Please try again:</p>';
?>
<form id="form1" name="form1" method="POST" action="<?php echo $loginFormAction; ?>" style="text-align:center">
Username:<br />
<input type="text" name="username" id="username"  style="margin:auto"/><br />
Password:
<br />
  <input type="password" name="password" id="password"  style="margin:auto"/><br />
  <input type="submit" name="button" id="button" value="Login" />
</p>
</form>
    </div>
</body>
</html>
