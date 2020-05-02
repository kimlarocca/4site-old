<?php require_once('Connections/cms.php'); ?>
<?php
$pageID = $homePage;
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

mysqli_select_db($cms, $database_cms);
$query_Recordset1 = "SELECT * FROM listings  LEFT JOIN (SELECT photoAlbums.albumID,photoAlbums.coverPhotoID,photoAlbums.albumName,photos.id,photos.file_name FROM photoAlbums,photos WHERE photoAlbums.coverPhotoID=photos.id)  AS a ON listings.albumID=a.albumID  WHERE featureListing = 1 AND listings.websiteID = ".$websiteID;
$Recordset1 = mysqli_query($query_Recordset1, $cms) or die(mysqli_error($cms));
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

mysqli_select_db($cms, $database_cms);
$query_currentPage = "SELECT * FROM cmsPages WHERE pageID = ".$pageID;
$currentPage = mysqli_query($query_currentPage, $cms) or die(mysqli_error($cms));
$row_currentPage = mysqli_fetch_assoc($currentPage);
$totalRows_currentPage = mysqli_num_rows($currentPage);

mysqli_select_db($cms, $database_cms);
$query_websiteInfo = "SELECT * FROM cmsWebsites WHERE websiteID = ".$websiteID;
$websiteInfo = mysqli_query($query_websiteInfo, $cms) or die(mysqli_error($cms));
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
<link rel="stylesheet" type="text/css" href="styles/masonry.css"/>
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
<div class="content"> <!-- InstanceBeginEditable name="mainContent" -->
 
  <div class="main"> 
  <!-- grid -->
  <?php
  if ($totalRows_Recordset1 > 0) { 
  ?>
  <h2 class="wf_centered" style="padding-top:15px;">Featured Listings</h2>
  <div class="masonry js-masonry"  data-masonry-options='{ "isFitWidth": true }'>
    <?php do { ?>
      <div class="item"><a href="listing-details.php?listingID=<?php echo $row_Recordset1['listingID']; ?>">
        <div class="overlay-item">
          <div class="item-image">
            <div class="crop-height">
              <?php  
		if ($row_Recordset1['coverPhotoID'] == NULL){ 
		?>
              <img class="scale" src="http://4siteusa.com/uploads/comingsoon.jpg">
              <?php
		} else {			
		?>
              <img class="scale" src="http://4siteusa.com/uploads/thumb-<?php echo $row_Recordset1['file_name']; ?>">
              <?php
		}
		?>
            </div>
          </div>
          <div class="item-title">
            <h2><?php echo $row_Recordset1['shortDescription']; ?></h2>
          </div>
          <div class="wf_centered"><a class="button" href="listing-details.php?listingID=<?php echo $row_Recordset1['listingID']; ?>">view details</a></div>
        </div>
        </a> </div>
      <?php } while ($row_Recordset1 = mysqli_fetch_assoc($Recordset1)); ?>
  </div>
  <?php } ?>
  <div style="padding-top:10px"><?php echo $row_currentPage['pageContent']; ?></div>
  </div>
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
<script type="text/javascript" src="scripts/masonry.pkgd.min.js"></script> 
<script type="text/javascript" src="scripts/imagesloaded.pkgd.min.js"></script> 
<script>
$(document).ready(function() {
  // initiallize masonry
  var $container = $('.masonry').masonry();
  $container.imagesLoaded( function() {
    $container.masonry();
  $container.masonry('bindResize');
  });
  
  $container.masonry('bindResize');
});
</script> 
<!-- InstanceEndEditable --> 
<script src="scripts/scripts.js"></script>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($Recordset1);

mysqli_free_result($currentPage);

mysqli_free_result($websiteInfo);
?>
