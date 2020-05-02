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

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
  $updateSQL = sprintf("UPDATE products SET displayOnSite=%s, lastUpdate=%s, productCategory=%s, productName=%s, productPDF=%s, albumID=%s, productDescription=%s WHERE productID=%s",
                       GetSQLValueString(isset($_POST['displayOnSite']) ? "true" : "", "defined","1","0"),
                       GetSQLValueString($_POST['lastUpdate'], "text"),
                       GetSQLValueString($_POST['productCategory'], "text"),
                       GetSQLValueString($_POST['productName'], "text"),
                       GetSQLValueString($_POST['productPDF'], "text"),
                       GetSQLValueString($_POST['albumID'], "text"),
                       GetSQLValueString($_POST['productDescription'], "text"),
                       GetSQLValueString($_POST['productID'], "int"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($updateSQL, $cms) or die(mysqli_error($cms));

  $updateGoTo = "products-modify.php?action=saved";
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
mysqli_select_db($cms, $database_cms);
$query_currentUser = sprintf("SELECT * FROM cmsUsers WHERE username = %s", GetSQLValueString($colname_currentUser, "text"));
$currentUser = mysqli_query($query_currentUser, $cms) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

$colname_product = "-1";
if (isset($_GET['productID'])) {
  $colname_product = $_GET['productID'];
}
mysqli_select_db($cms, $database_cms);
$query_product = sprintf("SELECT * FROM products WHERE productID = %s", GetSQLValueString($colname_product, "int"));
$product = mysqli_query($query_product, $cms) or die(mysqli_error($cms));
$row_product = mysqli_fetch_assoc($product);
$totalRows_product = mysqli_num_rows($product);

mysqli_select_db($cms, $database_cms);
$query_albums = "SELECT * FROM photoAlbums WHERE websiteID = ".$row_currentUser['websiteID']." ORDER BY albumName ASC";
$albums = mysqli_query($query_albums, $cms) or die(mysqli_error($cms));
$row_albums = mysqli_fetch_assoc($albums);
$totalRows_albums = mysqli_num_rows($albums);

mysqli_select_db($cms, $database_cms);
$query_categories = "SELECT productCategoryID, categoryName FROM productCategories ORDER BY categoryName ASC";
$categories = mysqli_query($query_categories, $cms) or die(mysqli_error($cms));
$row_categories = mysqli_fetch_assoc($categories);
$totalRows_categories = mysqli_num_rows($categories);
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
      <h2>Product Catalog Module</h2>
      <h3>update this product</h3>
      <a href="<?php echo $row_currentUser['url']; ?>/product-details.php?productID=<?php echo $row_product['productID']; ?>" target="_blank" class="button">preview this product</a>
      <a href="products.php" class="button">back to the products page</a><br /><br />
      <?php
//check url parameters
if ($_GET['action'] == 'saved') print '<p style="color:red;">Your changes have been saved!</p>';
?>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1" onsubmit="MM_validateForm('productName','','R');return document.MM_returnValue">
        <table border="0" cellpadding="3" cellspacing="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Display On Site:</td>
            <td><input <?php if (!(strcmp($row_product['displayOnSite'],1))) {echo "checked=\"checked\"";} ?> type="checkbox" name="displayOnSite" value="<?php if ($row_product['displayOnSite']==1) {echo "1";} ?>" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right"><a href="products-categories.php">Category</a>:</td>
            <td>
              <select name="productCategory" id="productCategory">
                <?php
do {  
?>
                <option value="<?php echo $row_categories['productCategoryID']?>"<?php if (!(strcmp($row_categories['productCategoryID'], $row_product['productCategory']))) {echo "selected=\"selected\"";} ?>><?php echo $row_categories['categoryName']?></option>
                <?php
} while ($row_categories = mysqli_fetch_assoc($categories));
  $rows = mysqli_num_rows($categories);
  if($rows > 0) {
      mysqli_data_seek($categories, 0);
	  $row_categories = mysqli_fetch_assoc($categories);
  }
?>
            </select></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Product Name: *</td>
            <td><input name="productName" type="text" id="productName" value="<?php echo htmlentities($row_product['productName'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Product PDF Link:</td>
            <td><input type="text" name="productPDF" value="<?php echo htmlentities($row_product['productPDF'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right"><a href="albums.php">Photo Album</a>:</td>
            <td><select name="albumID">
              <?php
do {  
?>
              <option value="<?php echo $row_albums['albumID']?>"<?php if (!(strcmp($row_albums['albumID'], $row_product['albumID']))) {echo "selected=\"selected\"";} ?>><?php echo $row_albums['albumName']?></option>
              <?php
} while ($row_albums = mysqli_fetch_assoc($albums));
  $rows = mysqli_num_rows($albums);
  if($rows > 0) {
      mysqli_data_seek($albums, 0);
	  $row_albums = mysqli_fetch_assoc($albums);
  }
?>
            </select></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right" valign="top">Product Description:</td>
            <td><textarea name="productDescription" cols="50" rows="5"><?php echo htmlentities($row_product['productDescription'], ENT_COMPAT, 'UTF-8'); ?></textarea></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="Save Changes" /></td>
          </tr>
        </table>
        <input type="hidden" name="lastUpdate" value="<?php echo htmlentities($row_currentUser['firstName']); ?> <?php echo htmlentities($row_currentUser['lastName']); ?> on <?php echo date("F j, Y, g:i a"); ?>" />
        <input type="hidden" name="MM_update" value="form1" />
        <input type="hidden" name="productID" value="<?php echo $row_product['productID']; ?>" />
      </form>
<p>Last Updated by <em><?php echo $row_product['lastUpdate']; ?></em></p>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($product);

mysqli_free_result($categories);
?>
