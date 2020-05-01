<?php require_once('simpleimage.php'); ?>
<?php
ini_set('memory_limit', '1024M');
ini_set('upload-max-filesize', '10M');
ini_set('post_max_size', '10M');
//generate timestamp
$date = date_create();
$now = "ck-".date_timestamp_get($date)."-";
	

 move_uploaded_file($_FILES["upload"]["tmp_name"],
 "uploads/".$now.$_FILES["upload"]["name"]);
 $targetFile = "uploads/".$now.$_FILES["upload"]["name"];
 //resize image
	list($width, $height) = getimagesize($targetFile);
	if($width >= $height && $width > 1200){ //landscape
	  $image = new SimpleImage($targetFile);
	  $image->resizeToWidth(1200);
	  $image->save($targetFile);
	}
	if($width < $height && $height > 500){ //portrait
	  $image3 = new SimpleImage($targetFile);
	  $image3->resizeToHeight(500);
	  $image3->save($targetFile);
	}
 
 echo "Stored in: " . $targetFile;
 
 // Required: anonymous function reference number as explained above.
$funcNum = $_GET['CKEditorFuncNum'] ;
// Optional: instance name (might be used to load a specific configuration file or anything else).
$CKEditor = $_GET['CKEditor'] ;
// Optional: might be used to provide localized messages.
$langCode = $_GET['langCode'] ;
 
// Check the $_FILES array and save the file. Assign the correct path to a variable ($url).
$url = 'http://4siteusa.com/'.$targetFile;
// Usually you will only assign something here if the file could not be uploaded.
$message = '';
 
echo "<script type='text/javascript'> window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
?>