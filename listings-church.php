<?php
ini_set('session.save_path',getcwd(). '/../tmp/');
session_start();
?>
<?php
$sortBy = 'mlsNumber';
if ($_GET['sortBy'] != '') $sortBy = $_GET['sortBy'];
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
$query_currentUser = sprintf("SELECT * FROM cmsUsers, cmsWebsites WHERE cmsUsers.websiteID=cmsWebsites.websiteID AND cmsUsers.username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

mysqli_select_db($cms, $database_cms);
$query_listings = "SELECT * FROM listings  LEFT JOIN (SELECT photoAlbums.albumID,photoAlbums.coverPhotoID,photoAlbums.albumName,photos.id,photos.file_name FROM photoAlbums,photos WHERE photoAlbums.coverPhotoID=photos.id)  AS a ON listings.albumID=a.albumID  WHERE listings.websiteID = ".$row_currentUser['websiteID']." ORDER BY ".$sortBy;
$listings = mysqli_query($query_listings, $cms) or die(mysqli_error($cms));
$row_listings = mysqli_fetch_assoc($listings);
$totalRows_listings = mysqli_num_rows($listings);
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
      <h2>Rentals Module</h2>
      <p><a href="listings-add.php" class="button">add a new rental</a></p>
<h3>manage your listings</h3>
<?php
//check url parameters
if ($_GET['action'] == 'deleted') print '<p style="color:red;">Your rental has been deleted.</p>';
?>
<table border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td><u><strong>Title</strong></u></td>
          <td>&nbsp;</td>
        </tr>
        <?php do { ?>
          <tr>
            <td>
			<?php  
		if ($row_listings['coverPhotoID'] == NULL){ 
		?>
        <img style="height:auto; width:150px" src="uploads/comingsoon.jpg">
        <?php
		} else {			
		?>
        <img style="height:auto; width:150px" src="uploads/<?php echo $row_listings['file_name']; ?>">
        <?php
		}
		?>
            </td>
            <td><?php 
			if ($row_listings['displayOnSite'] == 0) echo '<img src="images/cross.png" alt="not displayed on site" width="16" height="16" />'; 
			else {
				echo '<img src="images/checkmark.png" alt="displayed on site" width="16" height="16" />';
			}
			?></td>
            <td><?php 
			if ($row_listings['featureListing'] == 1) echo '<img alt="featured" src="images/star.png" width="16" height="16" />'; 
			?></td>

            <td><?php echo $row_listings['shortDescription']; ?></td>
            <td>
            <a class="tooltip" target="_blank" title="preview this listing" href="<?php echo $row_currentUser['url']; ?>/listing-details.php?listingID=<?php echo $row_listings['listingID']; ?>"><img src="images/view.png" width="22" height="22" /></a> <a class="tooltip" title="edit this listing" href="listings-modify.php?listingID=<?php echo $row_listings['listingID']; ?>"><img src="images/edit.png" width="22" height="22" /></a> <a class="tooltip" title="listing photos" href="albums-photos.php?listingID=<?php echo $row_listings['listingID']; ?>&albumID=<?php echo $row_listings['albumID']; ?>"><img src="images/image.png" width="22" height="22" /></a> <a class="tooltip" title="delete this listing" href="listings-delete.php?listingID=<?php echo $row_listings['listingID']; ?>"><img src="images/delete.png" width="22" height="22" /></a>
            </td>
          </tr>
          <?php } while ($row_listings = mysqli_fetch_assoc($listings)); ?>
      </table>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($listings);
?>
