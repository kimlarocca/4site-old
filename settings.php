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

$colname_currentUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_currentUser = $_SESSION['MM_Username'];
}
mysqli_select_db($cms, $database_cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

$colname_cmsUser = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_cmsUser = $_SESSION['MM_Username'];
}
mysqli_select_db($cms, $database_cms);
$query_cmsUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_cmsUser, "text"));
$cmsUser = mysqli_query($query_cmsUser, $cms) or die(mysqli_error($cms));
$row_cmsUser = mysqli_fetch_assoc($cmsUser);
$totalRows_cmsUser = mysqli_num_rows($cmsUser);

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form2")) {
	if($_POST['Password']==$_POST['Password2']){
  $updateSQL = sprintf("UPDATE cmsUsers SET password=%s WHERE userID=%s",
                       GetSQLValueString($_POST['Password'], "text"),
                       GetSQLValueString($_POST['userID'], "int"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($updateSQL, $cms) or die(mysqli_error($cms));

  $updateGoTo = "settings.php?action=password";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header("Location: settings.php?action=password");
	}
	else {
		
  header("Location: settings.php?action=failed");
	}
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE cmsWebsites SET firstName=%s, lastName=%s, emailAddress=%s, iaddress=%s, icity=%s, istate=%s, izip=%s, phoneNumber=%s, companyName=%s, iaddress2=%s, cellNumber=%s, faxNumber=%s, facebook=%s, linkedin=%s, twitter=%s, youtube=%s, pinterest=%s, vimeo=%s WHERE websiteID=".$row_currentUser['websiteID'],
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
  $Result1 = mysqli_query($updateSQL, $cms) or die(mysqli_error($cms));

  $updateGoTo = "settings.php?action=saved";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}

mysqli_select_db($cms, $database_cms);
$query_userSettings = "SELECT * FROM cmsWebsites WHERE websiteID = ".$row_currentUser['websiteID'];
$userSettings = mysqli_query($query_userSettings, $cms) or die(mysqli_error($cms));
$row_userSettings = mysqli_fetch_assoc($userSettings);
$totalRows_userSettings = mysqli_num_rows($userSettings);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="styles.css"/>
<title>Website Administration</title>
<script type="text/javascript">
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
    } if (errors) alert('The following error(s) occurred:\n'+errors);
    document.MM_returnValue = (errors == '');
} }
</script>
</head>
<body>
<h1>Website Administration</h1>
<div class="nav"><a class="navItem iconLinks" href="home.php"><img src="images/home.png" /></a> <a class="navItem iconLinks tooltip2" title="update your profile" href="settings.php"><img src="images/settings.png" /></a> <a class="navItem iconLinks tooltip2" title="questions? get help" href="help.php"><img src="images/help.png" /></a> <a class="navItem iconLinks tooltip2" title="logout" href="logout.php"><img src="images/logout.png" /></a></div>
<div class="twd_container">
  <div class="twd_row">
    <div class="twd_column twd_two twd_margin20">
      <h2>Update Your Personal Settings</h2>
      <p>Primary Website Address (URL): <?php echo $row_userSettings['url']; ?></p>
      
<?php
if ($_GET['action'] == 'saved') echo '<p style="color:red">Your changes have been saved!</p>';
?>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" onsubmit="MM_validateForm('firstName','','R','lastName','','R','emailAddress','','RisEmail');return document.MM_returnValue">
        <table border="0" cellpadding="0" cellspacing="0">
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">First Name: *</td>
            <td><input name="firstName" type="text" id="firstName" value="<?php echo htmlentities($row_userSettings['firstName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Last Name: *</td>
            <td><input name="lastName" type="text" id="lastName" value="<?php echo htmlentities($row_userSettings['lastName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Email Address: *</td>
            <td><input name="emailAddress" type="text" id="emailAddress" value="<?php echo htmlentities($row_userSettings['emailAddress'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Company Name:</td>
            <td><input type="text" name="companyName" value="<?php echo htmlentities($row_userSettings['companyName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Address Line 1:</td>
            <td><input type="text" name="iaddress" value="<?php echo htmlentities($row_userSettings['iaddress'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Address Line 2:</td>
            <td><input type="text" name="iaddress2" value="<?php echo htmlentities($row_userSettings['iaddress2'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">City:</td>
            <td><input type="text" name="icity" value="<?php echo htmlentities($row_userSettings['icity'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">State:</td>
            <td>
            <select name="istate">
              <option value="AL" <?php if (!(strcmp("AL", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Alabama</option>
              <option value="AK" <?php if (!(strcmp("AK", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Alaska</option>
              <option value="AZ" <?php if (!(strcmp("AZ", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Arizona</option>
              <option value="AR" <?php if (!(strcmp("AR", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Arkansas</option>
              <option value="CA" <?php if (!(strcmp("CA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>California</option>
              <option value="CO" <?php if (!(strcmp("CO", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Colorado</option>
              <option value="CT" <?php if (!(strcmp("CT", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Connecticut</option>
              <option value="DE" <?php if (!(strcmp("DE", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Delaware</option>
              <option value="DC" <?php if (!(strcmp("DC", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>District Of Columbia</option>
              <option value="FL" <?php if (!(strcmp("FL", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Florida</option>
              <option value="GA" <?php if (!(strcmp("GA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Georgia</option>
              <option value="HI" <?php if (!(strcmp("HI", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Hawaii</option>
              <option value="ID" <?php if (!(strcmp("ID", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Idaho</option>
              <option value="IL" <?php if (!(strcmp("IL", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Illinois</option>
              <option value="IN" <?php if (!(strcmp("IN", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Indiana</option>
              <option value="IA" <?php if (!(strcmp("IA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Iowa</option>
              <option value="KS" <?php if (!(strcmp("KS", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Kansas</option>
              <option value="KY" <?php if (!(strcmp("KY", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Kentucky</option>
              <option value="LA" <?php if (!(strcmp("LA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Louisiana</option>
              <option value="ME" <?php if (!(strcmp("ME", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Maine</option>
              <option value="MD" <?php if (!(strcmp("MD", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Maryland</option>
              <option value="MA" <?php if (!(strcmp("MA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Massachusetts</option>
              <option value="MI" <?php if (!(strcmp("MI", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Michigan</option>
              <option value="MN" <?php if (!(strcmp("MN", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Minnesota</option>
              <option value="MS" <?php if (!(strcmp("MS", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Mississippi</option>
              <option value="MO" <?php if (!(strcmp("MO", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Missouri</option>
              <option value="MT" <?php if (!(strcmp("MT", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Montana</option>
              <option value="NE" <?php if (!(strcmp("NE", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Nebraska</option>
              <option value="NV" <?php if (!(strcmp("NV", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Nevada</option>
              <option value="NH" <?php if (!(strcmp("NH", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>New Hampshire</option>
              <option value="NJ" <?php if (!(strcmp("NJ", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>New Jersey</option>
              <option value="NM" <?php if (!(strcmp("NM", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>New Mexico</option>
              <option value="NY" <?php if (!(strcmp("NY", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>New York</option>
              <option value="NC" <?php if (!(strcmp("NC", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>North Carolina</option>
              <option value="ND" <?php if (!(strcmp("ND", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>North Dakota</option>
              <option value="OH" <?php if (!(strcmp("OH", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Ohio</option>
              <option value="OK" <?php if (!(strcmp("OK", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Oklahoma</option>
              <option value="OR" <?php if (!(strcmp("OR", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Oregon</option>
              <option value="PA" <?php if (!(strcmp("PA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Pennsylvania</option>
              <option value="RI" <?php if (!(strcmp("RI", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Rhode Island</option>
              <option value="SC" <?php if (!(strcmp("SC", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>South Carolina</option>
              <option value="SD" <?php if (!(strcmp("SD", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>South Dakota</option>
              <option value="TN" <?php if (!(strcmp("TN", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Tennessee</option>
              <option value="TX" <?php if (!(strcmp("TX", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Texas</option>
              <option value="UT" <?php if (!(strcmp("UT", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Utah</option>
              <option value="VT" <?php if (!(strcmp("VT", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Vermont</option>
              <option value="VA" <?php if (!(strcmp("VA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Virginia</option>
              <option value="WA" <?php if (!(strcmp("WA", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Washington</option>
              <option value="WV" <?php if (!(strcmp("WV", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>West Virginia</option>
              <option value="WI" <?php if (!(strcmp("WI", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Wisconsin</option>
              <option value="WY" <?php if (!(strcmp("WY", $row_userSettings['istate']))) {echo "selected=\"selected\"";} ?>>Wyoming</option>
</select>	
            </td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Zip:</td>
            <td><input type="text" name="izip" value="<?php echo htmlentities($row_userSettings['izip'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Phone Number:</td>
            <td><input type="text" name="phoneNumber" value="<?php echo htmlentities($row_userSettings['phoneNumber'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Cell Number:</td>
            <td><input type="text" name="cellNumber" value="<?php echo htmlentities($row_userSettings['cellNumber'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Fax Number:</td>
            <td><input type="text" name="faxNumber" value="<?php echo htmlentities($row_userSettings['faxNumber'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Facebook:</td>
            <td><input type="text" name="facebook" value="<?php echo htmlentities($row_userSettings['facebook'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Linkedin:</td>
            <td><input type="text" name="linkedin" value="<?php echo htmlentities($row_userSettings['linkedin'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Twitter:</td>
            <td><input type="text" name="twitter" value="<?php echo htmlentities($row_userSettings['twitter'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Youtube:</td>
            <td><input type="text" name="youtube" value="<?php echo htmlentities($row_userSettings['youtube'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Pinterest:</td>
            <td><input type="text" name="pinterest" value="<?php echo htmlentities($row_userSettings['pinterest'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td align="right" nowrap="nowrap">Vimeo:</td>
            <td><input type="text" name="vimeo" value="<?php echo htmlentities($row_userSettings['vimeo'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="Save Changes" /></td>
          </tr>
        </table>
        <input type="hidden" name="MM_update" value="form1" />
      </form>
      <p>&nbsp;</p>
    </div>
    
    <div class="twd_column twd_two twd_margin20">
    <h2>Change Your Password</h2>
    <?php
//check url parameters
if ($_GET['action'] == 'password') print '<p style="color:red;">Your password has been updated!</p>';
if ($_GET['action'] == 'failed') print '<p style="color:red;">Passwords do not match!</p>';
?>
    <form id="form2" name="form2" method="POST" action="<?php echo $editFormAction; ?>">
      <p>
        <label for="Password"></label>
        New Password:        </p>
      <p>
  <input type="password" name="Password" id="Password" />
      </p>
      <p onmouseover="MM_validateForm('Password','','R','Password2','','R');return document.MM_returnValue">Re-type Password:</p>
      <p>
        <input type="password" name="Password2" id="Password2" />
      </p>
      <p>
        <input type="submit" value="Change Password" />
        <input name="userID" type="hidden" id="userID" value="<?php echo $row_cmsUser['userID']; ?>" />
      </p>
      <input type="hidden" name="MM_update" value="form2" />
    </form>
    <p>&nbsp;</p></div>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($cmsUser);

mysqli_free_result($userSettings);
?>
