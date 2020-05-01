<?php require_once('simpleimage.php'); ?>
<?php
ini_set('memory_limit', '1024M');
if(!empty($_FILES)){
		
	$targetDir = "uploads/";
    $fileName = $_FILES['file']['name'];
	$date = date_create();
	
	//generate timestamp
	$now = date_timestamp_get($date);
	
	//rename file
	rename($targetDir.$fileName, $targetDir."-".$now."-".$fileName);
	$fileName = $now."-".$fileName;
	
    $targetFile = $targetDir.$fileName;
	
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
	echo "Saved in: " . "uploads/" . $fileName;
}
?>