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
  $updateSQL = sprintf("UPDATE photos SET photoDescription=%s, photoSequence=%s, photoTitle=%s, photoLink=%s WHERE id=%s",
                       GetSQLValueString($_POST['photoDescription'], "text"),
                       GetSQLValueString($_POST['photoSequence'], "int"),
                       GetSQLValueString($_POST['photoTitle'], "text"),
                       GetSQLValueString($_POST['photoLink'], "text"),
                       GetSQLValueString($_POST['id'], "int"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($updateSQL, $cms) or die(mysqli_error($cms));

  $updateGoTo = "photos-modify.php?action=saved";
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
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

$colname_photo = "-1";
if (isset($_GET['photoID'])) {
  $colname_photo = $_GET['photoID'];
}
mysqli_select_db($cms, $database_cms);
$query_photo = sprintf("SELECT * FROM photos WHERE id = %s", GetSQLValueString($colname_photo, "int"));
$photo = mysqli_query($query_photo, $cms) or die(mysqli_error($cms));
$row_photo = mysqli_fetch_assoc($photo);
$totalRows_photo = mysqli_num_rows($photo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles.css"/>
<title>Website Administration</title>
<link rel="stylesheet" type="text/css" href="dropzone.css"/>
<script src="dropzone.js"></script>
</head>
<body>
<h1>Website Administration</h1>
<div class="nav"><a class="navItem iconLinks" href="home.php"><img src="images/home.png" /></a> <a class="navItem iconLinks tooltip2" title="update your profile" href="settings.php"><img src="images/settings.png" /></a> <a class="navItem iconLinks tooltip2" title="questions? get help" href="help.php"><img src="images/help.png" /></a> <a class="navItem iconLinks tooltip2" title="logout" href="logout.php"><img src="images/logout.png" /></a></div>
<div class="twd_container">
  <div class="twd_row">
    <div class="twd_column twd_two twd_margin20">
      <h2>Photo Albums</h2>
      <p><a class="button" href="photos-delete.php?photoID=<?php echo $row_photo['id']; ?>">delete this photo</a> <a class="button" href="albums-photos.php?albumID=<?php echo $_GET['albumID']; ?>">go back to album</a></p>
      <h3>Update this photo</h3>

      <div style="width:420px;">
<div style="width:200px;  margin:auto; padding:10px 20px 0 0; float:left" id="logo">
 <img src="uploads/<?php echo $row_photo['file_name']; ?>" />
    </div>
 <div class="image_upload_div" style="width:200px; float:left">
        <form action="upload-replace.php" class="dropzone" id="myAwesomeForm">
          <input name="websiteID" type="hidden" value="<?php echo $row_currentUser['websiteID']; ?>" />
          <input name="photoID" type="hidden" value="<?php echo $_GET['photoID']; ?>" />
          <input name="albumID" type="hidden" value="<?php echo $_GET['albumID']; ?>" />
        </form>
      </div>
  </div>
  <div class="twd_clearfloat" style="padding-top:20px"></div>

<?php
if ($_GET['action'] == 'saved') print '<p style="color:red;">Your photo information has been updated.</p>';
?><br />
<br />
<form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
  <table border="0" cellpadding="3" cellspacing="0">
    <tr valign="baseline">
      <td nowrap="nowrap" align="right" valign="top">Photo Title:</td>
      <td><input contenteditable="true" type="text" name="photoTitle" value="<?php echo htmlentities($row_photo['photoTitle'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right" valign="top">Photo Description:</td>
      <td><textarea contenteditable="true" name="photoDescription" cols="50" rows="5"><?php echo htmlentities($row_photo['photoDescription'], ENT_COMPAT, 'UTF-8'); ?></textarea></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Photo Link:</td>
      <td><input type="text" name="photoLink" value="<?php echo htmlentities($row_photo['photoLink'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">Photo Sequence:</td>
      <td><input type="text" name="photoSequence" value="<?php echo htmlentities($row_photo['photoSequence'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
    </tr>
    <tr valign="baseline">
      <td nowrap="nowrap" align="right">&nbsp;</td>
      <td><input type="submit" value="Save Changes" /></td>
    </tr>
  </table>
  <input type="hidden" name="MM_update" value="form1" />
  <input type="hidden" name="id" value="<?php echo $row_photo['id']; ?>" />
</form>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
Dropzone.autoDiscover = false;
$(function() {
  var myDropzone = new Dropzone("#myAwesomeForm");
  myDropzone.on("queuecomplete", function(file) {
		//location.reload();
		$('#logo').load(document.URL +  ' #logo');
  });
})
</script>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($photo);
?>
