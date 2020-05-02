<?php
ini_set('session.save_path', getcwd() . '/../tmp/');
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
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup)
{
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
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("", $MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {
    $MM_qsChar = "?";
    $MM_referrer = $_SERVER['PHP_SELF'];
    if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
    if (isset($_SERVER['QUERY_STRING']) && strlen($_SERVER['QUERY_STRING']) > 0)
        $MM_referrer .= "?" . $_SERVER['QUERY_STRING'];
    $MM_restrictGoTo = $MM_restrictGoTo . $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
    header("Location: " . $MM_restrictGoTo);
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
    $updateSQL = sprintf("UPDATE listings SET displayOnSite=%s, mlsNumber=%s, propertyPrice=%s, propertyStatus=%s, shortDescription=%s, longDescription=%s, propertyType=%s, propertyStyle=%s, beds=%s, fullBaths=%s, halfBaths=%s, propertyLocation=%s, interiorFeatures=%s, exteriorFeatures=%s, virtualTourLink=%s, featureListing=%s, albumID=%s, agentName=%s, customField=%s  WHERE listingID=%s",
        GetSQLValueString(isset($_POST['displayOnSite']) ? "true" : "", "defined", "1", "0"),
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
        GetSQLValueString(isset($_POST['featureListing']) ? "true" : "", "defined", "1", "0"),
        GetSQLValueString($_POST['albumID'], "int"),
        GetSQLValueString($_POST['agentName'], "text"),
        GetSQLValueString($_POST['customField'], "text"),
        GetSQLValueString($_POST['listingID'], "int"));

    mysqli_select_db($database_cms, $cms);
    $Result1 = mysqli_query($updateSQL, $cms) or die(mysqli_error());

    $updateGoTo = "listings-modify.php?action=saved";
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
mysqli_select_db($database_cms, $cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers,cmsWebsites WHERE cmsUsers.websiteID=cmsWebsites.websiteID AND cmsUsers.username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error());
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

$colname_listing = "-1";
if (isset($_GET['listingID'])) {
    $colname_listing = $_GET['listingID'];
}
mysqli_select_db($database_cms, $cms);
$query_listing = sprintf("SELECT * FROM listings WHERE listingID = %s", GetSQLValueString($colname_listing, "int"));
$listing = mysqli_query($query_listing, $cms) or die(mysqli_error());
$row_listing = mysqli_fetch_assoc($listing);
$totalRows_listing = mysqli_num_rows($listing);

mysqli_select_db($database_cms, $cms);
$query_albums = "SELECT * FROM photoAlbums WHERE websiteID = " . $row_currentUser['websiteID'] . " ORDER BY albumName ASC";
$albums = mysqli_query($query_albums, $cms) or die(mysqli_error());
$row_albums = mysqli_fetch_assoc($albums);
$totalRows_albums = mysqli_num_rows($albums);

$church = false;
if ($_GET['church'] == 'yes') $church = true;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css"/>
    <title>Website Administration</title>
</head>
<body>
<h1>Website Administration</h1>
<div class="nav"><a class="navItem iconLinks" href="home.php"><img src="images/home.png"/></a> <a
            class="navItem iconLinks tooltip2" title="update your profile" href="settings.php"><img
                src="images/settings.png"/></a> <a class="navItem iconLinks tooltip2" title="questions? get help"
                                                   href="help.php"><img src="images/help.png"/></a> <a
            class="navItem iconLinks tooltip2" title="logout" href="logout.php"><img src="images/logout.png"/></a></div>
<div class="twd_container">
    <div class="twd_row">
        <div class="twd_column twd_two twd_margin20">
            <?php if ($church) { ?>
                <h2>Rentals Module</h2>
                <h3>update this rental</h3>
                <a href="church-rentals.php" class="button">back to the rentals page</a>
            <?php } else { ?>
                <h2>Listings Module</h2>
                <h3>update this listing</h3>
                <a href="<?php echo $row_currentUser['url']; ?>/listing-details.php?listingID=<?php echo $row_listing['listingID']; ?>"
                   target="_blank" class="button">preview this listing</a>
                <a href="listings.php" class="button">back to the listings page</a>
            <?php } ?>
            <br/><br/>
            <?php
            if ($_GET['action'] == 'saved') {
                echo '<p><span style="color:red;">Your changes have been saved!</span></p>';
            }
            ?>
            <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
                <table border="0" cellpadding="5" cellspacing="0">
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Display On Site?</td>
                        <td><input type="checkbox" name="displayOnSite"
                                   value="" <?php if (!(strcmp(htmlentities($row_listing['displayOnSite'], ENT_COMPAT, 'UTF-8'), 1))) {
                                echo "checked=\"checked\"";
                            } ?> /></td>
                    </tr>
                    <?php if (!$church) { ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">MLS Number:</td>
                        <td><input type="text" name="mlsNumber"
                                   value="<?php echo htmlentities($row_listing['mlsNumber'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <?php } ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Property Location:</td>
                        <td><input type="text" name="propertyLocation"
                                   value="<?php echo htmlentities($row_listing['propertyLocation'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Property Price:</td>
                        <td><input type="text" name="propertyPrice"
                                   value="<?php echo htmlentities($row_listing['propertyPrice'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <?php if (!$church) { ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Property Status:</td>
                        <td><input type="text" name="propertyStatus"
                                   value="<?php echo htmlentities($row_listing['propertyStatus'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <?php } ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Short Description:</td>
                        <td><input type="text" name="shortDescription"
                                   value="<?php echo htmlentities($row_listing['shortDescription'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Long Description:</td>
                        <td><textarea name="longDescription" cols="50"
                                      rows="5"><?php echo htmlentities($row_listing['longDescription'], ENT_COMPAT, 'UTF-8'); ?></textarea>
                        </td>
                    </tr>
                    <?php if (!$church) { ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Property Type:</td>
                        <td><input type="text" name="propertyType"
                                   value="<?php echo htmlentities($row_listing['propertyType'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Property Style:</td>
                        <td><input type="text" name="propertyStyle"
                                   value="<?php echo htmlentities($row_listing['propertyStyle'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Beds:</td>
                        <td><input type="text" name="beds"
                                   value="<?php echo htmlentities($row_listing['beds'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Full Baths:</td>
                        <td><input type="text" name="fullBaths"
                                   value="<?php echo htmlentities($row_listing['fullBaths'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Half Baths:</td>
                        <td><input type="text" name="halfBaths"
                                   value="<?php echo htmlentities($row_listing['halfBaths'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right" valign="top">Interior Features:</td>
                        <td><textarea name="interiorFeatures" cols="50"
                                      rows="5"><?php echo htmlentities($row_listing['interiorFeatures'], ENT_COMPAT, 'UTF-8'); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right" valign="top">Exterior Features:</td>
                        <td><textarea name="exteriorFeatures" cols="50"
                                      rows="5"><?php echo htmlentities($row_listing['exteriorFeatures'], ENT_COMPAT, 'UTF-8'); ?></textarea>
                        </td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Virtual Tour Link:</td>
                        <td><input type="text" name="virtualTourLink"
                                   value="<?php echo htmlentities($row_listing['virtualTourLink'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap"> Feature This Listing?</td>
                        <td><input type="checkbox" name="featureListing"
                                   value="" <?php if (!(strcmp(htmlentities($row_listing['featureListing'], ENT_COMPAT, 'UTF-8'), 1))) {
                                echo "checked=\"checked\"";
                            } ?> /></td>
                    </tr>
                    <?php } ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap"><a href="albums.php">Photo Album</a>:</td>
                        <td><label for="albumID"></label>
                            <select name="albumID" id="albumID">
                                <option value="">None</option>
                                <?php
                                do {
                                    ?>
                                    <option value="<?php echo $row_albums['albumID'] ?>"<?php if (!(strcmp($row_albums['albumID'], $row_listing['albumID']))) {
                                        echo "selected=\"selected\"";
                                    } ?>><?php echo $row_albums['albumName'] ?></option>
                                    <?php
                                } while ($row_albums = mysqli_fetch_assoc($albums));
                                $rows = mysqli_num_rows($albums);
                                if ($rows > 0) {
                                    mysqli_data_seek($albums, 0);
                                    $row_albums = mysqli_fetch_assoc($albums);
                                }
                                ?>
                            </select></td>
                    </tr>
                    <?php if (!$church) { ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Agent Name:<br/>
                            (for office websites only)
                        </td>
                        <td><input type="text" name="agentName"
                                   value="<?php echo htmlentities($row_listing['agentName'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">Custom Field:</td>
                        <td><input type="text" name="customField"
                                   value="<?php echo htmlentities($row_listing['customField'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                    </tr>
                    <?php } ?>
                    <tr valign="baseline">
                        <td align="right" nowrap="nowrap">&nbsp;</td>
                        <td><input type="submit" value="Save Changes"/></td>
                    </tr>
                </table>
                <input type="hidden" name="MM_update" value="form1"/>
                <input type="hidden" name="listingID" value="<?php echo $row_listing['listingID']; ?>"/>
            </form>
        </div>
    </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($listing);

mysqli_free_result($albums);
?>
