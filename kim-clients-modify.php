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

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE cmsWebsites SET url=%s, firstName=%s, lastName=%s, emailAddress=%s, iaddress=%s, icity=%s, istate=%s, izip=%s, phoneNumber=%s, companyName=%s, iaddress2=%s, cellNumber=%s, faxNumber=%s, facebook=%s, linkedin=%s, twitter=%s, youtube=%s, pinterest=%s, vimeo=%s, adminNotes=%s WHERE websiteID=%s",
                       GetSQLValueString($_POST['url'], "text"),
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['emailAddress'], "text"),
                       GetSQLValueString($_POST['iaddress'], "text"),
                       GetSQLValueString($_POST['icity'], "text"),
                       GetSQLValueString($_POST['istate'], "text"),
                       GetSQLValueString($_POST['izip'], "text"),
                       GetSQLValueString($_POST['phoneNumber'], "text"),
                       GetSQLValueString($_POST['companyName'], "text"),
                       GetSQLValueString($_POST['iaddress2'], "text"),
                       GetSQLValueString($_POST['cellNumber'], "text"),
                       GetSQLValueString($_POST['faxNumber'], "text"),
                       GetSQLValueString($_POST['facebook'], "text"),
                       GetSQLValueString($_POST['linkedin'], "text"),
                       GetSQLValueString($_POST['twitter'], "text"),
                       GetSQLValueString($_POST['youtube'], "text"),
                       GetSQLValueString($_POST['pinterest'], "text"),
                       GetSQLValueString($_POST['vimeo'], "text"),
                       GetSQLValueString($_POST['adminNotes'], "text"),
                       GetSQLValueString($_POST['websiteID'], "int"));

  mysql_select_db($database_cms, $cms);
  $Result1 = mysql_query($updateSQL, $cms) or die(mysql_error());

  $updateGoTo = "kim-clients-modify.php?action=saved";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
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

$colname_Recordset1 = "-1";
if (isset($_GET['websiteID'])) {
  $colname_Recordset1 = $_GET['websiteID'];
}
mysql_select_db($database_cms, $cms);
$query_Recordset1 = sprintf("SELECT * FROM cmsWebsites WHERE websiteID = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysql_query($query_Recordset1, $cms) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
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
    <div class="twd_column twd_one twd_margin20">
      <h2><?php echo $row_currentUser['firstName']; ?>'s Admin Page</h2>
      <?php
if ($row_currentUser['username'] == 'kim.henry') echo '<p><a class="button" href="kim.php">go to the admin page</a></p>';
?>
      <h3>update website information</h3>
      <p><?php
//check url parameters
if ($_GET['action'] == 'saved') print '<p style="color:red;">Your changes have been saved!</p>';
?>&nbsp;</p>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table border="0" cellpadding="3" cellspacing="0" id="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Url:</td>
            <td><input type="text" name="url" value="<?php echo htmlentities($row_Recordset1['url'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">FirstName:</td>
            <td><input type="text" name="firstName" value="<?php echo htmlentities($row_Recordset1['firstName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">LastName:</td>
            <td><input type="text" name="lastName" value="<?php echo htmlentities($row_Recordset1['lastName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">EmailAddress:</td>
            <td><input type="text" name="emailAddress" value="<?php echo htmlentities($row_Recordset1['emailAddress'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Iaddress:</td>
            <td><input type="text" name="iaddress" value="<?php echo htmlentities($row_Recordset1['iaddress'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Icity:</td>
            <td><input type="text" name="icity" value="<?php echo htmlentities($row_Recordset1['icity'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Istate:</td>
            <td><input type="text" name="istate" value="<?php echo htmlentities($row_Recordset1['istate'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Izip:</td>
            <td><input type="text" name="izip" value="<?php echo htmlentities($row_Recordset1['izip'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">PhoneNumber:</td>
            <td><input type="text" name="phoneNumber" value="<?php echo htmlentities($row_Recordset1['phoneNumber'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">CompanyName:</td>
            <td><input type="text" name="companyName" value="<?php echo htmlentities($row_Recordset1['companyName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Iaddress2:</td>
            <td><input type="text" name="iaddress2" value="<?php echo htmlentities($row_Recordset1['iaddress2'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">CellNumber:</td>
            <td><input type="text" name="cellNumber" value="<?php echo htmlentities($row_Recordset1['cellNumber'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">FaxNumber:</td>
            <td><input type="text" name="faxNumber" value="<?php echo htmlentities($row_Recordset1['faxNumber'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Facebook:</td>
            <td><input type="text" name="facebook" value="<?php echo htmlentities($row_Recordset1['facebook'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Linkedin:</td>
            <td><input type="text" name="linkedin" value="<?php echo htmlentities($row_Recordset1['linkedin'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Twitter:</td>
            <td><input type="text" name="twitter" value="<?php echo htmlentities($row_Recordset1['twitter'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Youtube:</td>
            <td><input type="text" name="youtube" value="<?php echo htmlentities($row_Recordset1['youtube'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Pinterest:</td>
            <td><input type="text" name="pinterest" value="<?php echo htmlentities($row_Recordset1['pinterest'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Vimeo:</td>
            <td><input type="text" name="vimeo" value="<?php echo htmlentities($row_Recordset1['vimeo'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">AdminNotes:</td>
            <td><input type="text" name="adminNotes" value="<?php echo htmlentities($row_Recordset1['adminNotes'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="save changes" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_update" value="form1" />
        <input type="hidden" name="websiteID" value="<?php echo $row_Recordset1['websiteID']; ?>" />
      </form>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysql_free_result($currentUser);

mysql_free_result($Recordset1);
?>