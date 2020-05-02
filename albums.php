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
  $insertSQL = sprintf("INSERT INTO photoAlbums (websiteID, albumName) VALUES (%s, %s)",
                       GetSQLValueString($_POST['websiteID'], "int"),
                       GetSQLValueString($_POST['albumName'], "text"));

  mysqli_select_db($database_cms, $cms);
  $Result1 = mysqli_query($insertSQL, $cms) or die(mysqli_error());

  $insertGoTo = "albums.php";
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
mysqli_select_db($database_cms, $cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error());
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

mysqli_select_db($database_cms, $cms);
$query_albums = "SELECT * FROM photoAlbums LEFT JOIN (SELECT photos.id,photos.file_name FROM photos) AS a ON photoAlbums.coverPhotoID=a.id WHERE photoAlbums.websiteID = ".$row_currentUser['websiteID']." ORDER BY albumName";
$albums = mysqli_query($query_albums, $cms) or die(mysqli_error());
$row_albums = mysqli_fetch_assoc($albums);
$totalRows_albums = mysqli_num_rows($albums);
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
	  <?php
if ($_GET['action'] == 'deleted') print '<p style="color:red;">Album has been deleted.</p>';
?>
      <h3>create a new photo album      </h3>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table border="0" cellpadding="5" cellspacing="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Album Name:</td>
            <td><input type="text" name="albumName" value="" size="32" /></td>
            <td><input type="submit" value="Add" /></td>
          </tr>
        </table>
        <input type="hidden" name="websiteID" value="<?php echo $row_currentUser['websiteID']; ?>" />
        <input type="hidden" name="MM_insert" value="form1" />
      </form>
      <h3>manage photo albums</h3>
        <?php 
		if ($totalRows_albums < 1) echo '<p>You do not have any photo albums.</p>';
		else {
			?>
			<table>
			<?php
            do { 
			$albumID = $row_albums['albumID'];
			?><tr>
            <td style="width:200px;">
            <?php  
		if ($row_albums['coverPhotoID'] == NULL){ 
		?>
        <a href="albums-photos.php?albumID=<?php echo $albumID ?>"><img src="uploads/comingsoon.jpg"></a>
        <?php
		} else {			
		?>
        <a href="albums-photos.php?albumID=<?php echo $albumID; ?>"><img src="uploads/<?php echo $row_albums['file_name']; ?>"></a>
        <?php
		}
		?>
            </td>
            <td><a href="albums-photos.php?albumID=<?php echo $albumID; ?>"><?php echo $row_albums['albumName']; ?></a></td>
            <td><a class="tooltip" title="manage this album" href="albums-photos.php?albumID=<?php echo $albumID; ?>"><img src="images/edit.png" width="22" height="22" /></a> <a class="tooltip" title="delete this album" href="albums-delete.php?albumID=<?php echo $albumID; ?>"><img src="images/delete.png" width="22" height="22" /></a></td></tr>
          <?php } while ($row_albums = mysqli_fetch_assoc($albums)); } ?>
          </table>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($albums);
?>
