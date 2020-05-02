<?php require_once('Connections/cms.php'); ?>
<?php
//update record
    $updaterecord = "UPDATE cmsPages SET pageActive=1 WHERE pageID=".$_GET['pageID'];
    mysqli_select_db($cms, $database_cms);
    mysqli_query($cms, $updaterecord) or die(mysqli_error($cms));
    //print '<p style="color:red;">Your page has been published!</p>';
	header("Location: home.php?action=published");
?>
