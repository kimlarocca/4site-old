<?php
ini_set('session.save_path', getcwd() . '/../tmp/');
session_start();
?>
<?php require_once('Connections/cms.php'); ?>
<?php
$host = $hostname_cms;
$user = $username_cms;
$pass = $password_cms;
$databaseName = $database_cms;
$con = mysqli_connect($host, $user, $pass);
$dbs = mysqli_select_db($con, $databaseName);
$result = mysqli_query($cms, "SELECT * FROM photos WHERE albumID = " . $_GET['albumID'] . " AND id >= " . $_GET['lastID'] . " ORDER BY id");
$data = array();
while ($row = mysqli_fetch_row($result)) {
    $data[] = $row;
}
echo json_encode($data);
?>
