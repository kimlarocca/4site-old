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

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $insertSQL = sprintf("INSERT INTO productCategories (categoryName, categoryDescription, websiteID) VALUES (%s, %s, %s)",
                       GetSQLValueString($_POST['categoryName'], "text"),
                       GetSQLValueString($_POST['categoryDescription'], "text"),
                       GetSQLValueString($_POST['websiteID'], "int"));

  mysqli_select_db($cms, $database_cms);
  $Result1 = mysqli_query($cms, $insertSQL) or die(mysqli_error($cms));

  $insertGoTo = "products-categories.php?action=added";
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
$currentUser = mysqli_query($cms, $query_currentUser) or die(mysqli_error($cms));
$row_currentUser = mysqli_fetch_assoc($currentUser);
$totalRows_currentUser = mysqli_num_rows($currentUser);

mysqli_select_db($cms, $database_cms);
$query_categories = "SELECT * FROM productCategories ORDER BY categoryName ASC";
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
</head>
<body>
<h1>Website Administration</h1>
<div class="nav"><a class="navItem iconLinks" href="home.php"><img src="images/home.png" /></a> <a class="navItem iconLinks tooltip2" title="update your profile" href="settings.php"><img src="images/settings.png" /></a> <a class="navItem iconLinks tooltip2" title="questions? get help" href="help.php"><img src="images/help.png" /></a> <a class="navItem iconLinks tooltip2" title="logout" href="logout.php"><img src="images/logout.png" /></a></div>
<div class="twd_container">
  <div class="twd_row">
    <div class="twd_column twd_two twd_margin20">
      <h2>Product Catalog Module: Categories</h2>
      <p><a href="products.php" class="button">go back &amp; manage products</a></p>
      <h3>add a new product category
	  <?php
//check url parameters
if ($_GET['action'] == 'added') print '<p style="color:red;">Your category has been added!</p>';
if ($_GET['action'] == 'deleted') print '<p style="color:red;">Your category has been deleted!</p>';
?></h3>
      <form action="<?php echo $editFormAction; ?>" method="post" name="form1" id="form1">
        <table border="0" cellpadding="3" cellspacing="0">
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">Category Name:</td>
            <td><input type="text" name="categoryName" value="" size="32" /></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right" valign="top">Description:</td>
            <td><textarea name="categoryDescription" cols="50" rows="5"></textarea></td>
          </tr>
          <tr valign="baseline">
            <td nowrap="nowrap" align="right">&nbsp;</td>
            <td><input type="submit" value="Add Category" /></td>
          </tr>
        </table>
        <input type="hidden" name="websiteID" value="<?php echo $row_currentUser['websiteID']; ?>" />
        <input type="hidden" name="MM_insert" value="form1" />
      </form>
      <h3>manage product categories</h3>
      <table border="0" cellpadding="5" cellspacing="0">
        <tr>
          <td>&nbsp;</td>
    <td><u><strong>name</strong></u></td>
    <td><u><strong>description</strong></u></td>
    <td>&nbsp;</td>
  </tr>
  <?php do { ?>
    <tr>
      <td>
	  <?php
		if ($row_categories['file_name'] == NULL){
		?>
        <img style="height:auto; width:150px" src="uploads/comingsoon.jpg">
        <?php
		} else {
		?>
        <img style="height:auto; width:150px" src="uploads/<?php echo $row_categories['file_name']; ?>">
        <?php
		}
		?>
	 </td>
      <td><?php echo $row_categories['categoryName']; ?></td>
      <td><?php echo $row_categories['categoryDescription']; ?></td>
      <td><a class="tooltip" title="edit this category" href="products-categories-modify.php?productCategoryID=<?php echo $row_categories['productCategoryID']; ?>"><img src="images/edit.png" width="22" height="22" /></a> <a class="tooltip" title="delete this category" href="products-categories-delete.php?productCategoryID=<?php echo $row_categories['productCategoryID']; ?>"><img src="images/delete.png" width="22" height="22" /></a></td>
      </tr>
    <?php } while ($row_categories = mysqli_fetch_assoc($categories)); ?>
</table>
    </div>
  </div>
</div>
</body>
</html>
<?php
mysqli_free_result($currentUser);

mysqli_free_result($categories);
?>
