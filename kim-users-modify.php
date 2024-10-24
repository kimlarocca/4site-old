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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE cmsUsers SET username=%s, password=%s, firstName=%s, lastName=%s, securityLevelID=%s, websiteID=%s WHERE userID=%s",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['securityLevelID'], "int"),
                       GetSQLValueString($_POST['websiteID'], "int"),
                       GetSQLValueString($_POST['userID'], "int"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($cms, $updateSQL) or die(mysqli_error($cms));

  $updateGoTo = "kim-users-modify.php?action=saved";
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
mysqli_select_db($cms, $database_cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($cms, $query_currentUser) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

$colname_user = "-1";
if (isset($_GET['userID'])) {
  $colname_user = $_GET['userID'];
}
mysqli_select_db($cms, $database_cms);
$query_user = sprintf("SELECT * FROM cmsUsers WHERE userID = %s", GetSQLValueString($colname_user, "int"));
$user = mysqli_query($query_user, $cms) or die(mysqli_error($cms));
$row_user = mysqli_fetch_assoc($user);
$totalRows_user = mysqli_num_rows($user);

mysqli_select_db($cms, $database_cms);
$query_securityLevels = "SELECT * FROM cmsSecurityLevels ORDER BY securityLevel ASC";
$securityLevels = mysqli_query($query_securityLevels, $cms) or die(mysqli_error($cms));
$row_securityLevels = mysqli_fetch_assoc($securityLevels);
$totalRows_securityLevels = mysqli_num_rows($securityLevels);
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
      <h2><?php echo $row_currentUser['firstName']; ?>'s Admin Page</h2>
<?php
if ($row_currentUser['username'] == 'kim.henry') echo '<p><a class="button" href="kim.php">go to the admin page</a></p>';
?>
<?php
//check url parameters
if ($_GET['action'] == 'saved') print '<p style="color:red;">Your changes have been saved!</p>';
?>
      <h3>update user information      </h3>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table border="0" cellpadding="3" cellspacing="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Username:</td>
            <td><input type="text" name="username" value="<?php echo htmlentities($row_user['username']); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Password:</td>
            <td><input type="text" name="password" value="<?php echo htmlentities($row_user['password']); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">FirstName:</td>
            <td><input type="text" name="firstName" value="<?php echo htmlentities($row_user['firstName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">LastName:</td>
            <td><input type="text" name="lastName" value="<?php echo htmlentities($row_user['lastName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">SecurityLevelID:</td>
            <td><select name="securityLevelID">
              <?php
do {
?>
              <option value="<?php echo $row_securityLevels['securityLevelID']?>" <?php if (!(strcmp($row_securityLevels['securityLevelID'], htmlentities($row_user['securityLevelID'])))) {echo "SELECTED";} ?>><?php echo $row_securityLevels['securityLevel']?></option>
              <?php
} while ($row_securityLevels = mysqli_fetch_assoc($securityLevels));
?>
            </select></td>
          </tr>
          <tr> </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">WebsiteID:</td>
            <td><input type="text" name="websiteID" value="<?php echo htmlentities($row_user['websiteID'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="save changes" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_update" value="form1" />
        <input type="hidden" name="userID" value="<?php echo $row_user['userID']; ?>" />
      </form>
      <p>&nbsp;</p>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($user);

mysqli_free_result($securityLevels);
?>
