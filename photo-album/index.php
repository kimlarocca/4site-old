<?php require_once('Connections/cms.php'); ?>
<?php
$albumID = 119;
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

mysql_select_db($database_cms, $cms);
$query_Recordset1 = "SELECT * FROM photos WHERE albumID = ".$albumID." ORDER BY photoSequence ASC";
$Recordset1 = mysql_query($query_Recordset1, $cms) or die(mysql_error());
$row_Recordset1 = mysql_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysql_num_rows($Recordset1);
?>
<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>Bellydancer Yuliya in NYC New York &amp; New Jersey</title>
<link rel="stylesheet" type="text/css" href="styles/masonry.css"/>
<link rel="stylesheet" type="text/css" href="styles/lightbox.css"/>
</head>

<body>
<h1>Sample Photo Album</h1>
<!-- grid -->
<div class="masonry">
  <?php do { ?>
    <a class="image-link" href="http://4siteusa.com/uploads/<?php echo $row_Recordset1['file_name']; ?>"><div class="item">
      <div class="overlay-item">
        <div class="item-image"><img src="http://4siteusa.com/uploads/thumb-<?php echo $row_Recordset1['file_name']; ?>"></div>
        <?php if ($row_Recordset1['photoTitle'] != ''){ ?>
        <div class="item-title">
          <h2><?php echo $row_Recordset1['photoTitle']; ?></h2>
        </div>
        <?php 
		} 
		if ($row_Recordset1['photoDescription'] != ''){
		?>
        <p><?php echo $row_Recordset1['photoDescription']; ?></p>
        <?php 
		} 
		?>
      </div>
    </div>
    </a>
    <?php } while ($row_Recordset1 = mysql_fetch_assoc($Recordset1)); ?>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script> 
<script type="text/javascript" src="scripts/lightbox.js"></script> 
<script type="text/javascript" src="scripts/masonry.pkgd.min.js"></script> 
<script type="text/javascript" src="scripts/imagesloaded.pkgd.min.js"></script> 
<script>
$(document).ready(function() {
  //lightbox
  $('.image-link').magnificPopup({type:'image'});
			  
  //masonry
  var $grid = $('.masonry').imagesLoaded( function() {
	// init Masonry after all images have loaded
	$grid.masonry({
	columnWidth: 320,
	itemSelector: '.item',
	isFitWidth: true
	});
  });
});
</script>
</body>
</html>