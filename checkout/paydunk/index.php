<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

$amount = $_GET["amount"];
$studioID = $_GET["studioID"];
$orderInfo = "Drop In";
$orderStatus = "incomplete";
$paypalButtonID = "HBGLVKVHDH9UW"; //$20

if ($amount==15) {
	$paypalButtonID = "LKYBWV9D4EXSA";
}
if ($amount==10) {
	$paypalButtonID = "E35RGF5X45KG4";
}
if ($amount==75) {
	$paypalButtonID = "YA2E9KZ3VACHQ";
	$orderInfo = "4 Class Card";
}
if ($amount==120) {
	$paypalButtonID = "9SLFAZ7UV8SNY"; 
	$orderInfo = "8 Class Card";
}
if ($amount==135) {
	$paypalButtonID = "B3TA75T56VQYN"; 
	$orderInfo = "Membership";
}
if ($amount==150) {
	$paypalButtonID = "B3TA75T56VQYN";
	$orderInfo = "Membership"; 
}

$hostname_wotg = "localhost";
$database_wotg = "studiocm_cms";
$username_wotg = "studiocm_kim";
$password_wotg = "Lotus18641864!";
$wotg = mysqli_pconnect($hostname_wotg, $username_wotg, $password_wotg) or trigger_error(mysqli_error(),E_USER_ERROR); 
mysqli_select_db($database_wotg, $wotg);
$query_order = "INSERT INTO orders (amount, studioID, orderInfo, orderStatus, notes) VALUES (".$amount.",".$studioID.",'".$orderInfo."','".$orderStatus."','no specific class specified')";
$order = mysqli_query($query_order, $wotg) or die(mysqli_error());
$orderID = mysqli_insert_id();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Wellness On The Green | Checkout</title>
<style>
body { font-family:Arial, Helvetica, sans-serif; }
</style>
</head>

<body>

<p>Amount: $<?php echo $amount; ?></p>

<div id="paydunkButton"></div>

<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_parent">
  <input type="hidden" name="cmd" value="_s-xclick">
  <input type="hidden" name="hosted_button_id" value="<?php echo $paypalButtonID ?>">
  <strong>
  <input style="padding:20px 0 0 0" type="image" src="paypal.png" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
  <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"> &nbsp;&nbsp;
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> 
<script type="text/javascript" src="jquery.paydunk.js"></script> 
<script>
$('#paydunkButton').paydunk({
    appID        : 'huwH5eadRegZF1eE1eOwmruLws2OKmnNGwmNsFaA', //your App ID goes here - required!!
    price        : <?php echo $amount; ?>, //required!!
    order_number : <?php echo $orderID; ?>,
    tax          : 0,
    shipping     : 0
});
</script>
</body>
</html>
