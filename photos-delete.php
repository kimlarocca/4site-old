<?php
ini_set('session.save_path',getcwd(). '/../tmp/');
session_start();
?>
<?php require_once('Connections/cms.php'); ?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "index.php?action=failed";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0) 
  $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

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

$colname_currentUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_currentUser = $_SESSION['MM_Username'];
}
mysql_select_db($database_cms, $cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysql_query($query_currentUser, $cms) or die(mysql_error());
$row_currentUser = mysql_fetch_assoc($currentUser);
$totalRows_currentUser = mysql_num_rows($currentUser);

$colname_photo = "-1";
if (isset($_GET['photoID'])) {
  $colname_photo = $_GET['photoID'];
}
mysql_select_db($database_cms, $cms);
$query_photo = sprintf("SELECT * FROM photos WHERE id = %s", GetSQLValueString($colname_photo, "int"));
$photo = mysql_query($query_photo, $cms) or die(mysql_error());
$row_photo = mysql_fetch_assoc($photo);
$totalRows_photo = mysql_num_rows($photo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles.css"/>
<title>Website Administration</title>
</head>
<body>
<h1>Website Administration</h1>
<div class="nav"><a class="navItem iconLinks" href="home.php"><img src="images/home.png" /></a> <a class="navItem iconLinks tooltip2" title="update your profile" href="settings.php"><img src="images/settings.png" /></a> <a class="navItem iconLinks tooltip2" title="questions? get help" href="help.php"><img src="images/help.png" /></a> <a class="navItem iconLinks tooltip2" title="logout" href="logout.php"><img src="images/logout.png" /></a></div>
<div class="twd_container">
  <div class="twd_row">
    <div class="twd_column twd_two twd_margin20">
      <h2>Photo Albums</h2>
      <h3>delete this photo?</h3>
      <p><img src="uploads/<?php echo $row_photo['file_name']; ?>" /></p>
      <p>Are you sure you want to delete this photo? </p>
      <p class="red">Caution: this action cannot be undone!!</p>
      <form id="form1" name="form1" method="get" action="photos-delete2.php">
        <input type="submit" name="button" id="button" value="Delete Photo" />
        <input name="photoID" type="hidden" id="photoID" value="<?php echo $row_photo['id']; ?>" />
        <input name="albumID" type="hidden" id="albumID" value="<?php echo $row_photo['albumID']; ?>" />
      </form>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysql_free_result($currentUser);

mysql_free_result($photo);
?>