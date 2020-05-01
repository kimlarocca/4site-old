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

$church = false;
if ($_GET['church'] == 'yes') $church = true;

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO listings (displayOnSite, mlsNumber, propertyPrice, propertyStatus, shortDescription, longDescription, propertyType, propertyStyle, beds, fullBaths, halfBaths, propertyLocation, interiorFeatures, exteriorFeatures, virtualTourLink, featureListing, websiteID, albumID, agentName, customField) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString(isset($_POST['displayOnSite']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['mlsNumber'], "text"),
                       GetSQLValueString($_POST['propertyPrice'], "int"),
                       GetSQLValueString($_POST['propertyStatus'], "text"),
                       GetSQLValueString($_POST['shortDescription'], "text"),
                       GetSQLValueString($_POST['longDescription'], "text"),
                       GetSQLValueString($_POST['propertyType'], "text"),
                       GetSQLValueString($_POST['propertyStyle'], "text"),
                       GetSQLValueString($_POST['beds'], "text"),
                       GetSQLValueString($_POST['fullBaths'], "text"),
                       GetSQLValueString($_POST['halfBaths'], "text"),
                       GetSQLValueString($_POST['propertyLocation'], "text"),
                       GetSQLValueString($_POST['interiorFeatures'], "text"),
                       GetSQLValueString($_POST['exteriorFeatures'], "text"),
                       GetSQLValueString($_POST['virtualTourLink'], "text"),
                       GetSQLValueString(isset($_POST['featureListing']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['websiteID'], "int"),
                       GetSQLValueString($_POST['albumID'], "int"),
                       GetSQLValueString($_POST['agentName'], "text"),
                       GetSQLValueString($_POST['customField'], "text"));

  mysql_select_db($database_cms, $cms);
  $Result1 = mysql_query($insertSQL, $cms) or die(mysql_error());

  $insertGoTo = "listings-photos.php?albumName=".$_POST['albumName'];
  if($church) $insertGoTo = "church-rentals.php?action=added";
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
mysql_select_db($database_cms, $cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysql_query($query_currentUser, $cms) or die(mysql_error());
$row_currentUser = mysql_fetch_assoc($currentUser);
$totalRows_currentUser = mysql_num_rows($currentUser);

mysql_select_db($database_cms, $cms);
$query_albums = "SELECT * FROM photoAlbums WHERE websiteID = ".$row_currentUser['websiteID']." ORDER BY albumName ASC";
$albums = mysql_query($query_albums, $cms) or die(mysql_error());
$row_albums = mysql_fetch_assoc($albums);
$totalRows_albums = mysql_num_rows($albums);
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
    <?php if ($church) { ?>
        <h2>Rentals Module</h2>
        <h3>add a new rental</h3>
        <a href="church-rentals.php" class="button">back to the rentals page</a>
    <?php } else { ?>
        <h2>Listings Module</h2>
        <h3>add a new listing</h3>
        <a href="listings.php" class="button">back to the listings page</a>
    <?php } ?>
    <br><br>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table border="0" cellpadding="5" cellspacing="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Display On Site?</td>
            <td><input type="checkbox" name="displayOnSite" value="" checked="checked" /></td>
          </tr>
<?php if (!$church) { ?>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">MLS Number:</td>
            <td><input type="text" name="mlsNumber" value="" size="32" /></td>
          </tr>
<?php } ?>
            <tr valign="baseline">
                <td nowrap="nowrap" align="right">Property Location:</td>
                <td><input type="text" name="propertyLocation" value="" size="32" /></td>
            </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Property Price:</td>
            <td><input type="text" name="propertyPrice" value="" size="32" /></td>
          </tr>
<?php if (!$church) { ?>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Property Status:</td>
            <td><input type="text" name="propertyStatus" value="" size="32" /></td>
          </tr>
<?php } ?>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Short Description:</td>
            <td><input type="text" name="shortDescription" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Long Description:</td>
            <td><textarea name="longDescription" cols="50" rows="5"></textarea></td>
          </tr>
<?php if (!$church) { ?>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Property Type:</td>
            <td><input type="text" name="propertyType" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Property Style:</td>
            <td><input type="text" name="propertyStyle" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Beds:</td>
            <td><input type="text" name="beds" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Full Baths:</td>
            <td><input type="text" name="fullBaths" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Half Baths:</td>
            <td><input type="text" name="halfBaths" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right" valign="top">Interior Features:</td>
            <td><textarea name="interiorFeatures" cols="50" rows="5"></textarea></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right" valign="top">Exterior Features:</td>
            <td><textarea name="exteriorFeatures" cols="50" rows="5"></textarea></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Virtual Tour Link:</td>
            <td><input type="text" name="virtualTourLink" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">  Feature This Listing?</td>
            <td><input type="checkbox" name="featureListing" value="" /></td>
          </tr>
<?php } ?>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right"><a href="albums.php">Choose Existing Photo Album</a>:</td>
            <td><label for="albumID"></label>
              <select name="albumID" id="albumID">
              <option value="">None</option>
                <?php
do {
?>
                <option value="<?php echo $row_albums['albumID']?>"<?php if (!(strcmp($row_albums['albumID'], $row_listing['albumID']))) {echo "selected=\"selected\"";} ?>><?php echo $row_albums['albumName']?></option>
                <?php
} while ($row_albums = mysql_fetch_assoc($albums));
  $rows = mysql_num_rows($albums);
  if($rows > 0) {
      mysql_data_seek($albums, 0);
	  $row_albums = mysql_fetch_assoc($albums);
  }
?>
              </select></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">New Photo Album Name:</td>
            <td><input name="albumName" type="text" id="albumName" value="" size="48" placeholder="leave blank if this listing does not have photos"/></td>
          </tr>
            <?php if (!$church) { ?>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Agent Name:</td>
            <td><input name="agentName" type="text" id="agentName" value="" size="48" placeholder="for office websites only"/></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Custom Field:</td>
            <td><input name="customField" type="text" id="customField" value="" size="48" placeholder="leave blank unless otherwise instructed"/></td>
          </tr>
            <?php } ?>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="Save & Add" /></td>
          </tr>
        </table>
        <input type="hidden" name="websiteID" value="<?php echo $row_currentUser['websiteID']; ?>" />
        <input type="hidden" name="MM_insert" value="form1" />
  </form>
</div>
</body>
</html>
<?php
mysql_free_result($currentUser);

mysql_free_result($albums);
?>
