<?php require_once('simpleimage.php'); ?>
<?php require_once('Connections/cms.php'); ?>
<?php
ini_set('memory_limit', '1024M');
if(!empty($_FILES)){
		
	$websiteID = $_POST['websiteID'];
	$albumID = $_POST['albumID'];
    $targetDir = "uploads/";
    $fileName = $_FILES['file']['name'];
	$date = date_create();
	
	//generate timestamp
	$now = date_timestamp_get($date);
	
    //connect with the database
    $conn = new mysqli($hostname_cms, $username_cms, $password_cms, $database_cms);
    if($mysqli->connect_errno){
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    }
	
	//rename file
	//rename($targetDir.$fileName, $targetDir.$albumID."-".$now."-".$fileName);
	//$fileName = $albumID."-".$now."-".$fileName;
	$goodFileName = str_replace(array( '(', ')' , ' ' ), '', $fileName);
	$goodFileName = htmlspecialchars($goodFileName);
	rename($targetDir.$fileName, $targetDir.$albumID."-".$now."-".$goodFileName);
	$fileName = $albumID."-".$now."-".$goodFileName;
	
    $targetFile = $targetDir.$fileName;
    
    if(move_uploaded_file($_FILES['file']['tmp_name'],$targetFile)){
        //insert file information into db table
        $conn->query("INSERT INTO photos (file_name, uploaded, websiteID, albumID) VALUES('".$fileName."','".date("Y-m-d H:i:s")."', ".$websiteID.", ".$albumID.")");
    }
	
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
	//generate 300px wide thumbnail
	$image2 = new SimpleImage($targetFile);
	$image2->resizeToWidth(300);
	$image2->save($targetDir."thumb-".$fileName);
}
?>