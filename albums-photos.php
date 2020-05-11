<?php
ini_set('session.save_path', getcwd() . '/../tmp/');
session_start();
?>
<?php require_once('Connections/cms.php'); ?>
<?php
if ($_GET['albumID'] == '') header("Location: albums-notFound.php?listingID=" . $_GET['listingID'] . "&productID=" . $_GET['productID']);
?>
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
$coverPhotoID = -1;
if ($_GET['action'] == 'cover') {
    //add the new album
    $db = new mysqli($hostname_cms, $username_cms, $password_cms, $database_cms);
    $db->query("UPDATE photoAlbums SET coverPhotoID=" . $_GET['photoID'] . " WHERE albumID=" . $_GET['albumID']);
    $coverPhotoID = $_GET['photoID'];
}
?>
<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }
        $hostname_cms = "localhost";
        $database_cms = "kim_4site";
        $username_cms = "kim_larocca";
        $password_cms = "Lotus18641864!";
        $cms = mysqli_connect($hostname_cms, $username_cms, $password_cms, $database_cms) or trigger_error(mysqli_error($cms), E_USER_ERROR);

        $theValue = mysqli_real_escape_string($cms, $theValue);

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
    $updateSQL = sprintf("UPDATE photoAlbums SET albumName=%s WHERE albumID=%s",
        GetSQLValueString($_POST['albumName'], "text"),
        GetSQLValueString($_POST['albumID'], "int"));

    mysqli_select_db($cms, $database_cms);
    $Result1 = mysqli_query($cms, $updateSQL) or die(mysqli_error($cms));

    $updateGoTo = "?action=saved&albumID=" . $_POST['albumID'];
    //if (isset($_SERVER['QUERY_STRING'])) {
    //  $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    //  $updateGoTo .= $_SERVER['QUERY_STRING'];
    //}
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

$colname_album = "-1";
if (isset($_GET['albumID'])) {
    $colname_album = $_GET['albumID'];
}
mysqli_select_db($cms, $database_cms);
$query_album = sprintf("SELECT * FROM photoAlbums WHERE albumID = %s", GetSQLValueString($colname_album, "int"));
$album = mysqli_query($cms, $query_album) or die(mysqli_error($cms));
$row_album = mysqli_fetch_assoc($album);
$totalRows_album = mysqli_num_rows($album);
if ($row_album['coverPhotoID'] != NULL) $coverPhotoID = $row_album['coverPhotoID'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css"/>
    <link rel="stylesheet" type="text/css" href="dropzone.css"/>
    <script src="dropzone.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
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
            <h2><a href="albums.php">Photo Albums</a> &gt;&gt; <?php echo $row_album['albumName']; ?></h2>
            <?php
            //check if login failed
            if ($_GET['action'] == 'deleted') print '<p style="color:red;">Photo has been deleted.</p>';
            if ($_GET['action'] == 'saved') print '<p style="color:red;">Album name has been updated.</p>';
            if ($_GET['action'] == 'cover') print '<p style="color:red;">Album cover photo been updated.</p>';
            ?>
            <br/>
            <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
                <table border="0" cellpadding="3" cellspacing="0">
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right">Rename this Album:</td>
                        <td><input type="text" name="albumName"
                                   value="<?php echo htmlentities($row_album['albumName'], ENT_COMPAT, 'UTF-8'); ?>"
                                   size="32"/></td>
                        <td><input type="submit" value="Save Changes"/><input name="albumID" type="hidden"
                                                                              value="<?php echo $_GET['albumID']; ?>"/>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="MM_update" value="form1"/>
                <input type="hidden" name="albumID" value="<?php echo $row_album['albumID']; ?>"/>
            </form>
            <h3>Add Photos to this album</h3>
            <div class="image_upload_div" style="width:90%;">
                <form action="upload.php" class="dropzone" id="myAwesomeForm">
                    <input name="websiteID" type="hidden" value="<?php echo $row_currentUser['websiteID']; ?>"/>
                    <input name="albumID" type="hidden" value="<?php echo $_GET['albumID']; ?>"/>
                </form>
            </div>
            <h3>Photos in this album</h3>
            <p>Click on any photo to update or delete.</p>
            <div class="pinGridWrapper">
                <div id="photos" class="pinGrid">

                    <?php
                    mysqli_select_db($cms, $database_cms);
                    $query_photos = "SELECT * FROM photos WHERE albumID = " . $_GET['albumID'] . " ORDER BY photoSequence ASC";
                    $photos = mysqli_query($cms, $query_photos) or die(mysqli_error($cms));
                    $row_photos = mysqli_fetch_assoc($photos);
                    $totalRows_photos = mysqli_num_rows($photos);
                    ?>
                    <?php do { ?>

                        <div class="pin"><a
                                    href="photos-modify.php?albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"><img
                                        src="uploads/thumb-<?php echo $row_photos['file_name']; ?>"/></a>
                            <h2><?php echo $row_photos['photoTitle']; ?></h2>
                            <p><?php echo $row_photos['photoDescription']; ?></p>
                            <div style="margin:auto; width:100px; padding:10px 0 10px 0"><a
                                        href="albums-photos.php?action=cover&albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"
                                        class="tooltip" title="set album cover">
                                    <?php if ($row_photos['id'] === $coverPhotoID) { ?>
                                        <img style="width:22px" src="images/heart-pink.png" width="32" height="32"/>
                                    <?php } else { ?>
                                        <img style="width:22px" src="images/heart.png" width="32" height="32"/>
                                    <?php } ?>
                                </a>
                                <a href="photos-modify.php?albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"
                                   class="tooltip" title="update photo"><img style="width:22px" src="images/edit.png"
                                                                             width="32" height="32"/></a> <a
                                        href="photos-delete.php?albumID=<?php echo $row_album['albumID']; ?>&photoID=<?php echo $row_photos['id']; ?>"
                                        class="tooltip" title="delete photo"><img style="width:22px"
                                                                                  src="images/delete.png" width="32"
                                                                                  height="32"/></a></div>
                        </div>

                    <?php } while ($row_photos = mysqli_fetch_assoc($photos)); ?>

                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    setInterval("my_function();", 2000);

    function my_function () {
        $('#photos').reload();
        console.log('kim')
    }
</script>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($album);

mysqli_free_result($photos);
?>
