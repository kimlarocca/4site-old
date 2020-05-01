<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_cms = "localhost";
$database_cms = "kim_4site";
$username_cms = "kim_larocca";
$password_cms = "Lotus18641864!";
$cms = mysql_pconnect($hostname_cms, $username_cms, $password_cms) or trigger_error(mysql_error(),E_USER_ERROR); 
?>