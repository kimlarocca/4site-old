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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO cmsModules (moduleName, moduleDescription) VALUES (%s, %s)",
                       GetSQLValueString($_POST['moduleName'], "text"),
                       GetSQLValueString($_POST['moduleDescription'], "text"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($cms, $insertSQL) or die(mysqli_error($cms));

  $insertGoTo = "modules.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO cmsUserModules (websiteID, moduleID) VALUES (%s, %s)",
                       GetSQLValueString($_POST['websiteID'], "int"),
                       GetSQLValueString($_POST['moduleID'], "int"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($cms, $insertSQL) or die(mysqli_error($cms));

  $insertGoTo = "modules.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
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

mysqli_select_db($cms, $database_cms);
$query_modules = "SELECT * FROM cmsModules ORDER BY moduleName ASC";
$modules = mysqli_query($query_modules, $cms) or die(mysqli_error($cms));
$row_modules = mysqli_fetch_assoc($modules);
$totalRows_modules = mysqli_num_rows($modules);

mysqli_select_db($cms, $database_cms);
$query_userModules = "SELECT * FROM cmsUserModules,cmsWebsites,cmsModules WHERE cmsUserModules.websiteID=cmsWebsites.websiteID AND cmsUserModules.moduleID=cmsModules.moduleID";
$userModules = mysqli_query($query_userModules, $cms) or die(mysqli_error($cms));
$row_userModules = mysqli_fetch_assoc($userModules);
$totalRows_userModules = mysqli_num_rows($userModules);

mysqli_select_db($cms, $database_cms);
$query_websites = "SELECT websiteID, url FROM cmsWebsites ORDER BY url ASC";
$websites = mysqli_query($query_websites, $cms) or die(mysqli_error($cms));
$row_websites = mysqli_fetch_assoc($websites);
$totalRows_websites = mysqli_num_rows($websites);

mysqli_select_db($cms, $database_cms);
$query_modules2 = "SELECT * FROM cmsModules ORDER BY moduleName ASC";
$modules2 = mysqli_query($query_modules2, $cms) or die(mysqli_error($cms));
$row_modules2 = mysqli_fetch_assoc($modules2);
$totalRows_modules2 = mysqli_num_rows($modules2);
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
      <h2>CMS Modules</h2>
      <h3>add a new module </h3>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Module Name:</td>
            <td><input type="text" name="moduleName" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Description:</td>
            <td><input type="text" name="moduleDescription" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="add" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_insert" value="form1" />
      </form>
      <h3>manage modules </h3>
      <table border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td>id</td>
          <td>name</td>
          <td>description</td>
        </tr>
        <?php do { ?>
          <tr>
            <td><?php echo $row_modules['moduleID']; ?></td>
            <td><?php echo $row_modules['moduleName']; ?></td>
            <td><?php echo $row_modules['moduleDescription']; ?></td>
          </tr>
          <?php } while ($row_modules = mysqli_fetch_assoc($modules)); ?>
      </table>
    </div>
    <div class="twd_column twd_two twd_margin20">
      <h2>User Modules</h2>
      <h3>add a new user module</h3>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
        <table>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">WebsiteID:</td>
            <td><select name="websiteID">
              <?php
do {
?>
              <option value="<?php echo $row_websites['websiteID']?>" ><?php echo $row_websites['url']?></option>
              <?php
} while ($row_websites = mysqli_fetch_assoc($websites));
?>
            </select></td>
          </tr>
          <tr> </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">ModuleID:</td>
            <td><select name="moduleID">
              <?php
do {
?>
              <option value="<?php echo $row_modules2['moduleID']?>"><?php echo $row_modules2['moduleName']?></option>
              <?php
} while ($row_modules2 = mysqli_fetch_assoc($modules2));
  $rows = mysqli_num_rows($modules2);
  if($rows > 0) {
      mysqli_data_seek($modules2, 0);
	  $row_modules2 = mysqli_fetch_assoc($modules2);
  }
?>
            </select></td>
          </tr>
          <tr> </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="add" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_insert" value="form2" />
      </form>
      <h3>manage user modules      </h3>
      <table border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td>url</td>
          <td>module</td>
        </tr>
        <?php do { ?>
          <tr>
            <td><?php echo $row_userModules['url']; ?></td>
            <td><?php echo $row_userModules['moduleName']; ?></td>
          </tr>
          <?php } while ($row_userModules = mysqli_fetch_assoc($userModules)); ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($modules);

mysqli_free_result($userModules);

mysqli_free_result($websites);

mysqli_free_result($modules2);
?>
