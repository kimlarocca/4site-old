<?php
ini_set('session.save_path', getcwd() . '/../tmp/');
session_start();
?>
<?php require_once('Connections/cms.php'); ?>
<?php
mysqli_select_db($cms, $database_cms);
//$result = mysqli_query($cms, "SELECT * FROM photos WHERE albumID = " . $_GET['albumID'] . " AND id >= " . $_GET['lastID'] . " ORDER BY id");
$result = mysqli_query($cms, "SELECT * FROM photos WHERE albumID = " . $_GET['albumID'] . " ORDER BY id");
$data = array();
console_log($result);
while ($row = mysqli_fetch_row($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>
