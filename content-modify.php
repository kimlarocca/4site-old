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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form")) {
  $updateSQL = sprintf("UPDATE cmsPages SET username=%s, pageTitle=%s, pageContent=%s, pageModified=%s WHERE pageID=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['pageTitle'], "text"),
                       GetSQLValueString($_POST['pageContent'], "text"),
                       GetSQLValueString($_POST['pageModified'], "date"),
                       GetSQLValueString($_POST['pageID'], "int"));

  mysqli_select_db($database_cms, $cms);
  $Result1 = mysqli_query($updateSQL, $cms) or die(mysqli_error());

  $updateGoTo = "content-modify.php?action=saved&pageID=".$row_content['pageID'];
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

//revert to draft
if ($_POST['action'] == 'revert') {
    $updaterecord = "UPDATE cmsPages SET pageActive=0 WHERE pageID=".$_GET['pageID'];
    mysqli_select_db($database_cms, $cms);
    mysqli_query($updaterecord, $cms) or die(mysqli_error());
}
//publish
if ($_POST['action'] == 'publish') {
    $updaterecord = "UPDATE cmsPages SET pageActive=1 WHERE pageID=".$_GET['pageID'];
    mysqli_select_db($database_cms, $cms);
    mysqli_query($updaterecord, $cms) or die(mysqli_error());
}

$colname_currentUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_currentUser = $_SESSION['MM_Username'];
}
mysqli_select_db($database_cms, $cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error());
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

$colname_content = "-1";
if (isset($_GET['pageID'])) {
  $colname_content = $_GET['pageID'];
}
mysqli_select_db($database_cms, $cms);
$query_content = sprintf("SELECT * FROM cmsPages WHERE pageID = %s", GetSQLValueString($colname_content, "int"));
$content = mysqli_query($query_content, $cms) or die(mysqli_error());
$row_content = mysqli_fetch_assoc($content);
$totalRows_content = mysqli_num_rows($content);
?>
<script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="ckeditor/ckeditor.js"></script>
<script src="ckeditor/adapters/jquery.js"></script>
<script>
$( document ).ready( function() {
	//initialize ckeditor
	//$( 'textarea#pageContent' ).ckeditor();
	CKEDITOR.replace('pageContent', {
		"filebrowserImageUploadUrl": "upload-ck.php"
	});
	//publish and save
	$("#publish").click(function() {
	  $("#action").val("publish");
	  $( "#form" ).submit();
	});
	//revert and save
	$("#revert").click(function() {
	  $("#action").val("revert");
	  $( "#form" ).submit();
	});
} );
</script>
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
<h2>Content Editor</h2>
<?php
if ($row_content['pageActive'] == 0) echo '<p><em>This page is currently saved as a draft and is not live on your website.</em></p><a id="publish" class="button">publish this page</a>&nbsp;&nbsp;<a href="home.php" class="button">add a new page</a><br /><br />';
else {
	echo '<p><em>This page is currently live on your website.</em></p><a id="revert" class="button">revert to draft</a>&nbsp;&nbsp;<a href="home.php" class="button">add a new page</a>';	
}
?>
</div>
<hr class="twd_margin20" />
<div class="twd_container">
<?php
if ($_GET['action'] == 'saved') {
	echo '<p><span style="color:red;">Your changes have been saved!</span> (<em>last saved on ';
	echo date('M j Y g:i A', strtotime($row_content['pageModified'])).' by '.$row_content['username'].'</em>)</p>';
}
?>
<form method="POST" action="<?php echo $editFormAction; ?>" name="form" id="form">
  <input name="username" type="hidden" value="<?php echo $row_currentUser['username']; ?>" /><input name="pageModified" type="hidden" value="<?php echo date("Y-m-d H:i:s"); ?>" />
     <input name="pageID" type="hidden" value="<?php echo $row_content['pageID']; ?>" /> <table border="0" cellspacing="0" cellpadding="0">
       
        <tr>
          <td>Page Title:</td>
          <td><input type="text" value="<?php echo $row_content['pageTitle']; ?>" name="pageTitle" /></td>
        </tr>
      </table>
  <br />
      <textarea name="pageContent" id="pageContent" style="width:1000px; margin:auto; height:750px"><?php echo $row_content['pageContent']; ?></textarea>
        <br />
<input type="submit" value="save changes" /><input name="action" type="hidden" id="action" value="kim" />
<input type="hidden" name="MM_update" value="form" />
</form>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($content);
?>
