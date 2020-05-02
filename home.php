<?php
if (!isset($_SESSION)) {
  ini_set('session.save_path',getcwd(). '/../tmp/');
  session_start();
}
?>
<?php require_once('Connections/cms.php'); ?>
<?php
?>
<?php
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

$MM_restrictGoTo = "index.php?action=failed&session=notset";
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
  $insertSQL = sprintf("INSERT INTO cmsPages (websiteID, username, pageTitle, pageActive) VALUES (%s, %s, %s, %s)",
                       GetSQLValueString($_POST['websiteID'], "int"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['pageTitle'], "text"),
                       GetSQLValueString($_POST['pageActive'], "int"));

  mysqli_select_db($database_cms, $cms);
  $Result1 = mysqli_query($insertSQL, $cms) or die(mysqli_error());

$lastID = mysqli_insert_id($cms);

  $insertGoTo = "content-add.php";
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
$query_currentUser = sprintf("SELECT * FROM cmsUsers,cmsWebsites WHERE cmsUsers.websiteID=cmsWebsites.websiteID AND cmsUsers.username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error());
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

mysqli_select_db($cms, $database_cms);
$query_cmsModules = "SELECT * FROM cmsUserModules,cmsModules WHERE cmsUserModules.moduleID=cmsModules.moduleID AND cmsUserModules.websiteID = ".$row_currentUser['websiteID'].' ORDER BY cmsModules.moduleName';
$cmsModules = mysqli_query($query_cmsModules, $cms) or die(mysqli_error());
$row_cmsModules = mysqli_fetch_assoc($cmsModules);
$totalRows_cmsModules = mysqli_num_rows($cmsModules);

mysqli_select_db($cms, $database_cms);
$query_content = sprintf("SELECT * FROM cmsPages WHERE websiteID = ".$row_currentUser['websiteID']." ORDER BY pageTitle ASC");
$content = mysqli_query($query_content, $cms) or die(mysqli_error());
$row_content = mysqli_fetch_assoc($content);
$totalRows_content = mysqli_num_rows($content);

$realtor = false;
$church = false;
if ($row_currentUser['church'] == 0) $realtor = true;
if ($row_currentUser['church'] == 1) $church = true;
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
<h1>Website Administration<?php if ($church) echo ': Big Little Adventures'; ?></h1>
<div class="nav"><a class="navItem iconLinks" href="home.php"><img src="images/home.png" /></a> <a class="navItem iconLinks tooltip2" title="update your profile" href="settings.php"><img src="images/settings.png" /></a> <a class="navItem iconLinks tooltip2" title="questions? get help" href="help.php"><img src="images/help.png" /></a> <a class="navItem iconLinks tooltip2" title="logout" href="logout.php"><img src="images/logout.png" /></a></div>
<div class="twd_container">
  <div class="twd_row">
    <div class="twd_column twd_two twd_margin20">
      <h2><?php echo $row_currentUser['firstName']; ?>'s Home Page</h2>
<?php
if ($_GET['action'] == 'deleted') print '<p style="color:red;">Page has been deleted.</p>';
if ($_GET['action'] == 'published') print '<p style="color:red;">Your page has been published!</p>';
if ($row_currentUser['username'] == 'kim.henry') echo '<p><a class="button" href="kim.php">go to the admin page</a></p>';
if ($row_currentUser['username'] == 'flightriskaerials') echo '<p><a class="button" href="https://4siteusa.com/content-modify.php?pageID=18">edit studio cancellation notice</a></p>';
if($church == false) {
?>
      <h3>add new web pages</h3>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table border="0" cellpadding="0" cellspacing="0">
          <tr valign="baseline">
            <td>Page Title:
              <input type="hidden" name="websiteID" value="<?php echo $row_currentUser['websiteID']; ?>" />
              <input type="hidden" name="username" value="<?php echo $row_currentUser['username']; ?>" />
              <input type="hidden" name="pageActive" value="0" />
              <input type="hidden" name="MM_insert" value="form1" /></td>
            <td><input type="text" name="pageTitle" value="" size="32" /></td>
            <td><input type="submit" value="Continue" /></td>
          </tr>
        </table>
      </form>
    <?php } ?>
      <h3>manage your web pages</h3>
        <?php
		if ($totalRows_content < 1) echo '<p>You do not have any content pages.</p>';
		else {
			?>
      <table border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td align="center">&nbsp;</td>
          <td><strong><u>id</u></strong></td>
          <td><u><strong>page title</strong></u></td>
          <td width="125">&nbsp;</td>
        </tr>
        <?php
		do { ?>
          <tr>
            <td align="center" style="width:30px">
			<?php
			if ($row_content['pageActive'] == 0) echo '<img src="images/cross.png" width="22" height="22" />';
			else {
				echo '<img src="images/checkmark.png" width="22" height="22" />';
			}
			?></td>
            <td><?php echo $row_content['pageID']; ?></td>
            <td><a href="content-modify.php?pageID=<?php echo $row_content['pageID']; ?>"><?php echo $row_content['pageTitle']; ?></a></td>
            <td width="125"><a class="tooltip" title="publish this page" href="content-publish.php?pageID=<?php echo $row_content['pageID']; ?>"><img src="images/upload.png" width="22" height="22" /></a> <a class="tooltip" target="_blank" title="preview this page" href="<?php echo $row_currentUser['url']; ?>/preview.php?pageID=<?php echo $row_content['pageID']; ?>"><img src="images/view.png" width="22" height="22" /></a> <a class="tooltip" title="edit this page" href="content-modify.php?pageID=<?php echo $row_content['pageID']; ?>"><img src="images/edit.png" width="22" height="22" /></a> <a class="tooltip" title="delete this page" href="content-delete.php?pageID=<?php echo $row_content['pageID']; ?>"><img src="images/delete.png" width="22" height="22" /></a></td>
          </tr>
          <?php } while ($row_content = mysqli_fetch_assoc($content)); } ?>
      </table>
    </div>

    <div class="twd_column twd_two twd_margin20">
      <?php
      if($church == true) {
          ?>
          <h2>Website Look &amp; Feel</h2>
          <p><a class="button" href="hero-image.php">Update Hero Image</a></p>
          <br><br>
      <?php } ?>
      <h2>Website Modules</h2>
      <?php
      if($church == false) {
		if ($totalRows_cmsModules < 1) echo '<p>You do not have any website modules.</p>';
		else {
			?>
        <?php
		do { ?>
          <p><a class="button" href="modules-manage.php?moduleID=<?php echo $row_cmsModules['moduleID']; ?>"><?php echo $row_cmsModules['moduleName']; ?></a></p>
          <?php } while ($row_cmsModules = mysqli_fetch_assoc($cmsModules)); } ?>
        <?php } else { ?>
          <p><a class="button" href="church-rentals.php">Manage Rentals</a></p>
          <p><a class="button" href="modules-manage.php?moduleID=2">Manage Photo Albums</a></p>
          <p><a class="button" href="schedule.php">Manage Schedule</a></p>
      <?php } ?>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($cmsModules);

mysqli_free_result($content);
?>
