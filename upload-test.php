<?php require_once('simpleimage.php'); ?>
<?php	
	
//resize image
$image = new SimpleImage('uploads/1-1435844927IMG_1180.JPG');
$image->resizeToWidth(1000);
$image->save('uploads/resized2-1-1435844927IMG_1180.JPG');
	
//generate thumbnail
$image2 = new SimpleImage('uploads/1-1435844927IMG_1180.JPG');
$image2->resizeToWidth(300);
$image2->save('uploads/newthumb2-1-1435844927IMG_1180.JPG');

	//resize file
	/*
	$image = new SimpleImage();
	$image->load($targetFile);
	$image->resizeToWidth(1000);
	$image->save($targetFile);
	
	//generate thumbnail
	
	$image2 = new SimpleImage('uploads/1-1435782893DanAndKim.jpg');
	$image2->load($targetFile);
	$image2->resizeToWidth(300);
	$image2->save($targetDir.'thumb-'.$fileName);
	*/
	

?>
<img src="uploads/resized2-1-1435844927IMG_1180.JPG" /><br /><br /><img src="uploads/newthumb2-1-1435844927IMG_1180.JPG" />