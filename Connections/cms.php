<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_cms = "localhost";
$database_cms = "kim_4site";
$username_cms = "kim_larocca";
$password_cms = "Lotus18641864!";
$cms = mysqli_connect($hostname_cms, $username_cms, $password_cms, $database_cms) or trigger_error(mysqli_error(),E_USER_ERROR);
?>
