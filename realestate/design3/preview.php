<?php require_once('Connections/cms.php'); ?>
<?php
$pageID = $_GET['pageID'];
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

mysqli_select_db($database_cms, $cms);
$query_currentPage = "SELECT * FROM cmsPages WHERE pageID = ".$pageID;
$currentPage = mysqli_query($query_currentPage, $cms) or die(mysqli_error());
$row_currentPage = mysqli_fetch_assoc($currentPage);
$totalRows_currentPage = mysqli_num_rows($currentPage);

mysqli_select_db($database_cms, $cms);
$query_websiteInfo = "SELECT * FROM cmsWebsites WHERE websiteID = ".$websiteID;
$websiteInfo = mysqli_query($query_websiteInfo, $cms) or die(mysqli_error());
$row_websiteInfo = mysqli_fetch_assoc($websiteInfo);
$totalRows_websiteInfo = mysqli_num_rows($websiteInfo);
?>
<?php
$pageTitle = $row_currentPage['pageTitle'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo $row_websiteInfo['firstName']; ?> <?php echo $row_websiteInfo['lastName']; ?> | <?php echo $row_currentPage['pageTitle']; ?></title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="styles/Wicked.css"/>
<link rel="stylesheet" type="text/css" href="styles/styles.css"/>
<!-- InstanceBeginEditable name="head" -->
<!-- InstanceEndEditable -->
<script src="scripts/modernizr.custom.js"></script>
</head>

<body>
<div class="container">
  <div class="hero">
    <div class="heroContent">
      <h1><?php echo $pageTitle; ?></h1>
    </div>
  </div>
  <div class="main clearfix">
    <nav id="menu" class="nav">
      <ul>
        <li> <a href="index.php"> <span class="icon"> <i aria-hidden="true" class="icon-home"></i> </span> <span>Home</span> </a> </li>
        <li> <a href="listings.php"> <span class="icon"> <i aria-hidden="true" class="icon-listings"></i> </span> <span>Listings</span> </a> </li>
        <li> <a href="search.php"> <span class="icon"> <i aria-hidden="true" class="icon-search"></i> </span> <span>MLS Search</span> </a> </li>
        <li> <a href="localInfo.php"> <span class="icon"> <i aria-hidden="true" class="icon-local"></i> </span> <span>Local Info</span> </a> </li>
        <li> <a href="about.php"> <span class="icon"> <i aria-hidden="true" class="icon-about"></i> </span> <span>About Me</span> </a> </li>
        <li> <a href="contact.php"> <span class="icon"> <i aria-hidden="true" class="icon-contact"></i> </span> <span>Contact</span> </a> </li>
      </ul>
    </nav>
  </div>
</div>
<!-- /container --> 

<!-- main content -->
<div class="content"> <!-- InstanceBeginEditable name="mainContent" --><?php echo $row_currentPage['pageContent']; ?>

<!-- InstanceEndEditable -->
</div>
<!-- footer -->
<div class="footer">
  <div class="wf_row">
    <div class="wf_column wf_two">
      <h2><a href="index.php">Home</a> | <a href="about.php">About Me</a> | <a href="listings.php">Listings</a> | <a href="search.php">Property Search</a> | <a href="localInfo.php">Local Info</a> | <a href="contact.php">Contact Me</a></h2>
      <p>Copyright &copy; <?php echo $row_websiteInfo['firstName']; ?> <?php echo $row_websiteInfo['lastName']; ?> <?php echo date("Y"); ?>, All Rights Reserved.</p>
      <p>Web Design by <a href="http://www.4siteusa.com">4 Site</a>.</p>
    </div>
    <div class="wf_column wf_two wf_text_right">
      <h2><?php echo $row_websiteInfo['companyName']; ?></h2>
      <p><?php echo $row_websiteInfo['iaddress']; ?></p>
      <?php if ($row_websiteInfo['iaddress2'] <> ''){ ?>
      <p><?php echo $row_websiteInfo['iaddress2']; ?></p>
      <?php } ?>
      <p><?php echo $row_websiteInfo['phoneNumber']; ?></p>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> 
<!-- InstanceBeginEditable name="scripts" -->
<!-- InstanceEndEditable --> 
<script src="scripts/scripts.js"></script>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($currentPage);

mysqli_free_result($websiteInfo);
?>
