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
        $hostname_cms = "localhost";
        $database_cms = "kim_4site";
        $username_cms = "kim_larocca";
        $password_cms = "Lotus18641864!";
        $cms = mysqli_connect($hostname_cms, $username_cms, $password_cms, $database_cms) or trigger_error(mysqli_error($cms),E_USER_ERROR);

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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO cmsWebsites (url, firstName, lastName, emailAddress, iaddress, icity, istate, izip, phoneNumber, companyName, iaddress2, cellNumber, faxNumber, facebook, linkedin, twitter, youtube, pinterest, vimeo) VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['url'], "text"),
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['emailAddress'], "text"),
                       GetSQLValueString($_POST['iaddress'], "text"),
                       GetSQLValueString($_POST['icity'], "text"),
                       GetSQLValueString($_POST['istate'], "text"),
                       GetSQLValueString($_POST['izip'], "text"),
                       GetSQLValueString($_POST['phoneNumber'], "text"),
                       GetSQLValueString($_POST['companyName'], "text"),
                       GetSQLValueString($_POST['iaddress2'], "text"),
                       GetSQLValueString($_POST['cellNumber'], "text"),
                       GetSQLValueString($_POST['faxNumber'], "text"),
                       GetSQLValueString($_POST['facebook'], "text"),
                       GetSQLValueString($_POST['linkedin'], "text"),
                       GetSQLValueString($_POST['twitter'], "text"),
                       GetSQLValueString($_POST['youtube'], "text"),
                       GetSQLValueString($_POST['pinterest'], "text"),
                       GetSQLValueString($_POST['vimeo'], "text"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($cms, $insertSQL) or die(mysqli_error());

  $insertGoTo = "kim.php?action=added";
  if (isset($_SERVER['QUERY_STRING'])) {
    $insertGoTo .= (strpos($insertGoTo, '?')) ? "&" : "?";
    $insertGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $insertGoTo));
}

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form2")) {
  $insertSQL = sprintf("INSERT INTO cmsUsers (username, password, firstName, lastName, securityLevelID, websiteID) VALUES (%s, %s, %s, %s, %s, %s)",
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['firstName'], "text"),
                       GetSQLValueString($_POST['lastName'], "text"),
                       GetSQLValueString($_POST['securityLevelID'], "int"),
                       GetSQLValueString($_POST['websiteID'], "int"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($cms, $insertSQL) or die(mysqli_error());

  $insertGoTo = "kim.php?action=added";
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
$currentUser = mysqli_query($cms, $query_currentUser) or die(mysqli_error());
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

$query_securityLevels = "SELECT * FROM cmsSecurityLevels ORDER BY securityLevel ASC";
$securityLevels = mysqli_query($cms, $query_securityLevels) or die(mysqli_error());
$row_securityLevels = mysqli_fetch_assoc($securityLevels);
$totalRows_securityLevels = mysqli_num_rows($securityLevels);

$query_websites = "SELECT * FROM cmsWebsites ORDER BY url";
$websites = mysqli_query($cms, $query_websites) or die(mysqli_error());
$row_websites = mysqli_fetch_assoc($websites);
$totalRows_websites = mysqli_num_rows($websites);

$query_users = "SELECT * FROM cmsUsers,cmsSecurityLevels WHERE cmsUsers.securityLevelID=cmsSecurityLevels.securityLevelID ORDER BY cmsUsers.username ASC";
$users = mysqli_query($cms, $query_users) or die(mysqli_error());
$row_users = mysqli_fetch_assoc($users);
$totalRows_users = mysqli_num_rows($users);
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
      <h2><?php echo $row_currentUser['firstName']; ?>'s Admin Page</h2>
      <?php
//check url parameters
if ($_GET['action'] == 'userDeleted') print '<p style="color:red;">CMS user has been deleted!</p>';
?>
  <div class="twd_row">
    <div class="twd_column twd_two twd_margin20">
      <p><a href="javascript:void();" class="button" id="addClient">ADD CLIENT</a> <a href="javascript:void();" class="button" id="addUser">ADD CMS USER</a> <a href="modules.php" class="button">MANAGE MODULES</a> <a href="pages.php" class="button">MANAGE PAGES</a></p>
      <h3>CMS Websites</h3>
      <div id="clients" style="display:none">
      <p>Add a new client:</p>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table border="0" cellpadding="0" cellspacing="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Url:</td>
            <td><input type="text" name="url" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">FirstName:</td>
            <td><input type="text" name="firstName" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">LastName:</td>
            <td><input type="text" name="lastName" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">EmailAddress:</td>
            <td><input type="text" name="emailAddress" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Iaddress:</td>
            <td><input type="text" name="iaddress" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Icity:</td>
            <td><input type="text" name="icity" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Istate:</td>
            <td><input type="text" name="istate" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Izip:</td>
            <td><input type="text" name="izip" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">PhoneNumber:</td>
            <td><input type="text" name="phoneNumber" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">CompanyName:</td>
            <td><input type="text" name="companyName" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Iaddress2:</td>
            <td><input type="text" name="iaddress2" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">CellNumber:</td>
            <td><input type="text" name="cellNumber" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">FaxNumber:</td>
            <td><input type="text" name="faxNumber" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Facebook:</td>
            <td><input type="text" name="facebook" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Linkedin:</td>
            <td><input type="text" name="linkedin" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Twitter:</td>
            <td><input type="text" name="twitter" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Youtube:</td>
            <td><input type="text" name="youtube" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Pinterest:</td>
            <td><input type="text" name="pinterest" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Vimeo:</td>
            <td><input type="text" name="vimeo" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="Insert record" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_insert" value="form1" />
      </form>
      <hr style="margin:20px 0 20px 0" />
      </div>
      <table border="0" cellpadding="3" cellspacing="0">
        <tr>
          <td><strong>ID</strong></td>
          <td><strong>URL</strong></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <?php do { ?>
          <tr>
            <td><?php echo $row_websites['websiteID']; ?></td>
            <td><a href="<?php echo $row_websites['url']; ?></a>"><?php echo $row_websites['url']; ?></a></td>
            <td><a href="kim-clients-modify.php?websiteID=<?php echo $row_websites['websiteID']; ?>"><img src="images/edit.png" width="22" height="22" /></a></td>
            <td><a href="kim-clients-delete.php?websiteID=<?php echo $row_websites['websiteID']; ?>"><img src="images/delete.png" width="22" height="22" /></a></td>
          </tr>
          <?php } while ($row_websites = mysqli_fetch_assoc($websites)); ?>
      </table>
    </div>
    <div class="twd_column twd_two twd_margin20">
      <h3>CMS USERS</h3>
      <div id="users" style="display:none">
      <p>Add a new cms user:</p>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form2" id="form2">
        <table border="0" cellpadding="0" cellspacing="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Username:</td>
            <td><input type="text" name="username" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Password:</td>
            <td><input type="text" name="password" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">FirstName:</td>
            <td><input type="text" name="firstName" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">LastName:</td>
            <td><input type="text" name="lastName" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">SecurityLevelID:</td>
            <td><select name="securityLevelID">
              <?php
do {
?>
              <option value="<?php echo $row_securityLevels['securityLevelID']?>"><?php echo $row_securityLevels['securityLevel']?></option>
              <?php
} while ($row_securityLevels = mysqli_fetch_assoc($securityLevels));
  $rows = mysqli_num_rows($securityLevels);
  if($rows > 0) {
      mysqli_data_seek($securityLevels, 0);
	  $row_securityLevels = mysqli_fetch_assoc($securityLevels);
  }
?>
            </select>
            </td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">WebsiteID:</td>
            <td><input type="text" name="websiteID" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="Insert record" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_insert" value="form2" />
      </form>
      <hr style="margin:20px 0 20px 0" />
      </div>
      <table border="0" cellpadding="3" cellspacing="0">
        <tr>
          <td><strong>Username</strong></td>
          <td><strong>Password</strong></td>
          <td><strong>Name</strong></td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <?php do { ?>
          <tr>
            <td><?php echo $row_users['username']; ?></td>
            <td><?php echo $row_users['password']; ?></td>
            <td><?php echo $row_users['lastName']; ?>, <?php echo $row_users['firstName']; ?></td>
            <td><a href="kim-users-modify.php?userID=<?php echo $row_users['userID']; ?>"><img src="images/edit.png" width="22" height="22" /></a></td>
            <td><a href="kim-users-delete.php?userID=<?php echo $row_users['userID']; ?>"><img src="images/delete.png" width="22" height="22" /></a></td>
          </tr>
          <?php } while ($row_users = mysqli_fetch_assoc($users)); ?>
      </table>
    </div>
  </div>
</div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script type="text/javascript">
$("#addUser").click(function() {
	$("#users").slideToggle('slow');
});
$("#addClient").click(function() {
	$("#clients").slideToggle('slow');
});
</script>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($securityLevels);

mysqli_free_result($websites);

mysqli_free_result($users);
?>
