<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname_cms = "localhost";
$database_cms = "kim_4site";
$username_cms = "kim_larocca";
$password_cms = "Lotus18641864!";
$cms = mysqli_pconnect($hostname_cms, $username_cms, $password_cms) or trigger_error(mysqli_error($cms),E_USER_ERROR); 
$websiteID = 10;
$idxLink = 'http://fl.living.net/idxrealtor/1197631';
$homePage = 54;
$aboutmePage = 55;
$listingsPage = 56;
$contactPage = 58;
$localinfoPage = 100;
$searchPage = 57;
$resourcesPage = 139;
?>
