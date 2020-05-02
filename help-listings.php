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

$colname_currentUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_currentUser = $_SESSION['MM_Username'];
}
mysqli_select_db($cms, $database_cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);
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
      <h2>Listings Module Help</h2>
  <h3>manage your Listing module</h3>
      <p>From the website administration home page you can manage your website modules - all available modules that are enabled in your account will be listed here. Click on the &quot;listings&quot; button to manage the listing module.</p>
      <p><img src="images/help-modules.jpg" width="800" height="598" class="helpImage" /></p>
      <h3>&nbsp;</h3>
      <h3>How to add photos to a listing</h3>
      <p>In your listings module, you will see all your listings displayed. Click on the edit icon to update any of your listings:</p>
      <p><img src="images/help-listingsEdit.jpg" width="800" height="482" class="helpImage" /></p>
      <p>Scroll to the bottom. You will see a drop down menu for the listing photo album. Select the album and hit &quot;save changes&quot; to link your listing to one of your photo albums! If you have not yet created the photo album, go to your photos module and do that first. You can click on the &quot;Photo Album&quot; link to get there.</p>
      <p><img src="images/help-addListingPhotos.jpg" width="546" height="222" class="helpImage" /></p>
      <p>Click SAVE CHANGES when you are done to save your changes!</p>
      <h3>&nbsp;</h3>
      <h3>how to feature a listing on your home page      </h3>
      <p>In your listings module, you will see all your listings displayed. Click on the edit icon to update any of your listings:</p>
      <p><img src="images/help-listingsEdit.jpg" width="800" height="482" class="helpImage" /></p>
      <p>You can feature as many listings as you like on your home page. To feature a listing, check the “feature this listing?” check box towards the bottom of the page when you add or edit a listing:</p>
      <p><img src="images/help-listingsFeature.jpg" class="helpImage" alt=""/></p>
      <p>Click SAVE CHANGES when you are done to save your changes!</p>
      <p>&nbsp;</p>
      <h3>how to delete a listing</h3>
      <p>In your listings module, you will see all your listings displayed. Click on the delete icon to permanently delete a listing from your website:</p>
      <p><img src="images/help-deleteListing.jpg" width="883" height="397" class="helpImage" /></p>
      <p>&nbsp;</p>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);
?>
