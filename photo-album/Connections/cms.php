<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_cms = "localhost";
$database_cms = "kim_4site";
$username_cms = "kim_larocca";
$password_cms = "Lotus18641864!";
$cms = mysqli_pconnect($hostname_cms, $username_cms, $password_cms) or trigger_error(mysqli_error($cms),E_USER_ERROR);
$websiteID = 9;
?>
