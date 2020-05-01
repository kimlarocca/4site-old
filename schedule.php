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
//$query_products = "SELECT * FROM products WHERE websiteID = ".$row_currentUser['websiteID']." ORDER BY productCategory, productName ASC";
$query_products = "SELECT * FROM events WHERE websiteID = " . $row_currentUser['websiteID'] . " ORDER BY dayOfWeek, title ASC";
$products = mysql_query($query_products, $cms) or die(mysql_error());
$row_products = mysql_fetch_assoc($products);
$totalRows_products = mysql_num_rows($products);
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
            <h2>Schedule</h2>
            <p><a href="schedule-add.php" class="button">add a new scheduled event</a></p>
            <br><br>
            <h3>manage your scheduled events</h3>
            <?php
            //check url parameters
            if ($_GET['action'] == 'deleted') print '<p style="color:red;">Your event has been deleted.</p>';
            if ($_GET['action'] == 'added') print '<p style="color:red;">Your event has been added.</p>';
            ?>
            <table border="0" cellpadding="5" cellspacing="0">
                <tr>
                    <td><u><strong>day</strong></u></td>
                    <td><u><strong>title</strong></u></td>
                    <td><u><strong>duration</strong></u></td>
                    <td><u><strong>location</strong></u></td>
                    <td></td>
                </tr>
                <?php do { ?>
                    <tr>
                        <td><?php echo $row_products['dayOfWeek']; ?></td>
                        <td><?php echo $row_products['title']; ?></td>
                        <td><?php echo $row_products['duration']; ?></td>
                        <td><?php echo $row_products['location']; ?></td>
                        <td>
                            <a class="tooltip" title="edit this event"
                               href="schedule-modify.php?eventID=<?php echo $row_products['eventID']; ?>"><img
                                        src="images/edit.png" width="22" height="22"/></a> <a class="tooltip"
                                                                                              title="delete this event"
                                                                                              href="schedule-delete.php?eventID=<?php echo $row_products['eventID']; ?>"><img
                                        src="images/delete.png" width="22" height="22"/></a>
                        </td>
                    </tr>
                <?php } while ($row_products = mysql_fetch_assoc($products)); ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>
