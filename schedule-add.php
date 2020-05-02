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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
    $insertSQL = sprintf("INSERT INTO events (websiteID, weeklyEvent, title, duration, location, details, dayOfWeek) VALUES (%s, %s, %s, %s, %s, %s, %s)",
        GetSQLValueString($_POST['websiteID'], "int"),
        GetSQLValueString($_POST['weeklyEvent'], "int"),
        GetSQLValueString($_POST['title'], "text"),
        GetSQLValueString($_POST['duration'], "text"),
        GetSQLValueString($_POST['location'], "text"),
        GetSQLValueString($_POST['details'], "text"),
        GetSQLValueString($_POST['dayOfWeek'], "text"));

    mysqli_select_db($cms, $database_cms);
    $Result1 = mysqli_query($cms, $insertSQL) or die(mysqli_error($cms));

    $insertGoTo = "schedule.php?action=added";
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
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="styles.css"/>
    <title>Website Administration</title>
    <script type="text/javascript">
        function MM_validateForm () { //v4.0
            if (document.getElementById) {
                var i, p, q, nm, test, num, min, max, errors = '', args = MM_validateForm.arguments;
                for (i = 0; i < (args.length - 2); i += 3) {
                    test = args[i + 2];
                    val = document.getElementById(args[i]);
                    if (val) {
                        nm = val.name;
                        if ((val = val.value) != "") {
                            if (test.indexOf('isEmail') != -1) {
                                p = val.indexOf('@');
                                if (p < 1 || p == (val.length - 1)) errors += '- ' + nm + ' must contain an e-mail address.\n';
                            } else if (test != 'R') {
                                num = parseFloat(val);
                                if (isNaN(val)) errors += '- ' + nm + ' must contain a number.\n';
                                if (test.indexOf('inRange') != -1) {
                                    p = test.indexOf(':');
                                    min = test.substring(8, p);
                                    max = test.substring(p + 1);
                                    if (num < min || max < num) errors += '- ' + nm + ' must contain a number between ' + min + ' and ' + max + '.\n';
                                }
                            }
                        } else if (test.charAt(0) == 'R') errors += '- ' + nm + ' is required.\n';
                    }
                }
                if (errors) alert('The following error(s) occurred:\n' + errors);
                document.MM_returnValue = (errors == '');
            }
        }
    </script>
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
            <h3>add a new scheduled event</h3>
            <p>&nbsp;</p>
            <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
                <table border="0" cellpadding="3" cellspacing="0">
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right">Day of Week</td>
                        <td><select name="dayOfWeek" id="dayOfWeek">
                                <option value="Sunday" selected>Sunday</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                            </select></td>
                    </tr>
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right">Event Title: *</td>
                        <td><input name="title" type="text" id="title" value="" size="32" required/></td>
                    </tr>
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right">Start Time or Duration: *</td>
                        <td><input required name="duration" placeholer="for example, 8 to 9am" type="text" id="duration" value="" size="32"/></td>
                    </tr>
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right">Location: *</td>
                        <td><input name="location" type="text" id="location" value="" size="32" required/></td>
                    </tr>
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right" valign="top">Event Details:</td>
                        <td><textarea name="details" cols="50" rows="5"></textarea></td>
                    </tr>
                    <tr valign="baseline">
                        <td nowrap="nowrap" align="right">&nbsp;</td>
                        <td><input type="submit" value="Add Event"/></td>
                    </tr>
                </table>
                <input type="hidden" name="websiteID" value="<?php echo $row_currentUser['websiteID']; ?>"/>
                <input type="hidden" name="weeklyEvent" value="1"/>
                <input type="hidden" name="MM_insert" value="form1"/>
            </form>
            <p>&nbsp;</p>
        </div>
    </div>
</div>
</body>
</html>
