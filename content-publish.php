<?php require_once('Connections/cms.php'); ?>
<?php
//update record
    $updaterecord = "UPDATE cmsPages SET pageActive=1 WHERE pageID=".$_GET['pageID'];
    mysql_select_db($database_cms, $cms);
    mysql_query($updaterecord, $cms) or die(mysql_error());
    //print '<p style="color:red;">Your page has been published!</p>';
	header("Location: home.php?action=published"); 
?>