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
$query_userSettings = "SELECT * FROM cmsWebsites WHERE websiteID = " . $row_currentUser['websiteID'];
$userSettings = mysql_query($query_userSettings, $cms) or die(mysql_error());
$row_userSettings = mysql_fetch_assoc($userSettings);
$totalRows_userSettings = mysql_num_rows($userSettings);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css"/>
    <title>Website Administration</title>
    <link rel="stylesheet" type="text/css" href="dropzone.css"/>
    <script src="dropzone.js"></script>
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
            <h2>Hero Image</h2>
            <h3>Update this photo</h3>

            <div style="width:100%;">
                <div style="width:70%;  margin:auto; padding:10px 20px 0 0; float:left" id="logo">
                    <img style="width: 100%; height: auto;" src="<?php echo $row_userSettings['heroImage']; ?>"/>
                </div>
                <div class="image_upload_div" style="width:25%; float:left">
                    <form action="upload-replace-hero.php" class="dropzone" id="myAwesomeForm">
                        <input name="websiteID" type="hidden" value="<?php echo $row_currentUser['websiteID']; ?>"/>
                    </form>
                </div>
            </div>
            <div class="twd_clearfloat" style="padding-top:20px"></div>

        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script>
    Dropzone.autoDiscover = false;
    $(function () {
        var myDropzone = new Dropzone("#myAwesomeForm");
        myDropzone.on("queuecomplete", function (file) {
            //location.reload();
            $('#logo').load(document.URL + ' #logo');
        });
    })
</script>
</body>
</html>
