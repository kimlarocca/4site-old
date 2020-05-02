<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

//get credit card type
function getCreditCardType($str, $format = 'string')
    {
        if (empty($str)) {
            return false;
        }

        $matchingPatterns = [
            'visa' => '/^4[0-9]{12}(?:[0-9]{3})?$/',
            'mastercard' => '/^5[1-5][0-9]{14}$/',
            'amex' => '/^3[47][0-9]{13}$/',
            'diners' => '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
            'discover' => '/^6(?:011|5[0-9]{2})[0-9]{12}$/',
            'jcb' => '/^(?:2131|1800|35\d{3})\d{11}$/',
            'any' => '/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/'
        ];

        $ctr = 1;
        foreach ($matchingPatterns as $key=>$pattern) {
            if (preg_match($pattern, $str)) {
                return $format == 'string' ? $key : $ctr;
            }
            $ctr++;
        }
    }

//get payment info from paydunk
$expiration_date = $_POST["expiration_date"]; 
$expiration_dates = explode('/', $expiration_date);
$month = $expiration_dates[0];
$year = "20".$expiration_dates[1];
$date = date($year."-".$month);
$today = date("Y-m");
$card_number = $_POST["card_number"];
$card_type = getCreditCardType($card_number);
$cvv = $_POST["cvv"];
$transaction_uuid = $_POST["transaction_uuid"];
$order_number = $_POST["order_number"];
$firstName = $_POST["billing_first_name"];
$lastName = $_POST["billing_last_name"];

//get order info from database
$hostname_wotg = "localhost";
$database_wotg = "studiocm_cms";
$username_wotg = "studiocm_kim";
$password_wotg = "Lotus18641864!";
$wotg = mysqli_pconnect($hostname_wotg, $username_wotg, $password_wotg) or trigger_error(mysqli_error(),E_USER_ERROR); 
mysqli_select_db($database_wotg, $wotg);
$query_order = "SELECT * FROM orders WHERE orderID='".$order_number."'";
$order = mysqli_query($query_order, $wotg) or die(mysqli_error());
$row_orderInfo = mysqli_fetch_assoc($order);

//update the following lines from your order database	
$amount = $row_orderInfo['amount']-5;
$paymentID = $row_orderInfo['paymentID'];
$orderInfo = $row_orderInfo['orderInfo'];
$studentID = $row_orderInfo['studentID'];
$tax = 0;
$shipping = 0;

// Setup some more variables
$status = 'error';
$date = date($year."-".$month);
$today = date("Y-m");
$card_type = getCreditCardType($card_number);
$total = $amount;
	
	//get student info
	$query_student = "SELECT * FROM students WHERE studentID=".$studentID;
	$student = mysqli_query($query_student, $wotg) or die(mysqli_error());
	$row_student = mysqli_fetch_assoc($student);


error_log("date: ".$date." | today: ".$today." | month: ".$month." | year: ".$year." | #: ".$card_number." | type: ".$card_type." | ccv: ".$cvv);
error_log("orderID: ".$order_number);
error_log("orderInfo: ".$orderInfo);
error_log("paymentID: ".$paymentID);
error_log("studentID: ".$studentID);
error_log("amount: ".$amount);


// PayPal REST API
// # CreatePaymentSample
//
// This sample code demonstrate how you can process
// a direct credit card payment. Please note that direct 
// credit card payment and related features using the 
// REST API is restricted in some countries.
// API used: /v1/payments/payment

require __DIR__ . '/PayPalSDK/paypal/rest-api-sdk-php/sample/bootstrap.php';
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\CreditCard;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;

// ### CreditCard
// A resource representing a credit card that can be
// used to fund a payment.

$card = new CreditCard();
$card->setType($card_type)
    ->setNumber($card_number)
    ->setExpireMonth($month)
    ->setExpireYear($year)
    ->setCvv2($cvv)
    ->setFirstName($firstName)
    ->setLastName($lastName);

// ### FundingInstrument
// A resource representing a Payer's funding instrument.
// For direct credit card payments, set the CreditCard
// field on this object.
$fi = new FundingInstrument();
$fi->setCreditCard($card);

// ### Payer
// A resource representing a Payer that funds a payment
// For direct credit card payments, set payment method
// to 'credit_card' and add an array of funding instruments.
$payer = new Payer();
$payer->setPaymentMethod("credit_card")
    ->setFundingInstruments(array($fi));

// ### Additional payment details
// Use this optional field to set additional
// payment information such as tax, shipping
// charges etc.
$details = new Details();
$details->setShipping($shipping)
    ->setTax($tax)
    ->setSubtotal($amount);

// ### Amount
// Lets you specify a payment amount.
// You can also specify additional details
// such as shipping, tax.
$amount = new Amount();
$amount->setCurrency("USD")
    ->setTotal($total)
    ->setDetails($details);

// ### Transaction
// A transaction defines the contract of a
// payment - what is the payment for and who
// is fulfilling it. 
$transaction = new Transaction();
$transaction->setAmount($amount)
    ->setInvoiceNumber(uniqid());

// ### Payment
// A Payment Resource; create one using
// the above types and intent set to sale 'sale'
$payment = new Payment();
$payment->setIntent("sale")
    ->setPayer($payer)
    ->setTransactions(array($transaction));

// For Sample Purposes Only.
$request = clone $payment;

// ### Create Payment
// Create a payment by calling the payment->create() method
// with a valid ApiContext (See bootstrap.php for more on `ApiContext`)
// The return object contains the state.
try {
    $payment->create($apiContext);
} catch (Exception $ex) {
	$status = 'error';
	if ($date < $today) $status = 'declined';
	error_log($ex);
}

/*
possible PayPal payment states: created, approved, failed, cancelled, expired
paydunk status - must be "success", "cancelled", or "error" depending on the outcome of your transaction.
*/
 
if ($payment->getState()=='approved') {
	$status = 'success';
	//the payment was approved!
	//update order database
	$wotg = mysqli_pconnect($hostname_wotg, $username_wotg, $password_wotg) or trigger_error(mysqli_error(),E_USER_ERROR); 
	mysqli_select_db($database_wotg, $wotg);
	$query_order = "UPDATE orders SET orderStatus = 'complete',paymentType = 'Paydunk' WHERE paymentID='".$paymentID."'";
	$order = mysqli_query($query_order, $wotg) or die(mysqli_error());
	error_log("order table updated");
	
	if ($orderInfo=='Drop In'){
	
	  //get instructor ID
	  $query_classInfo = "SELECT * FROM classes,instructors WHERE classes.instructorID=instructors.instructorID AND classes.classID=".$row_orderInfo['classID'];
	  $classInfo = mysqli_query($query_classInfo, $wotg) or die(mysqli_error());
	  $row_classInfo = mysqli_fetch_assoc($classInfo);
	  $instructorID = $row_classInfo['instructorID'];
	  error_log("drop in info updated");
  
	  //update attendance records
	  $addrecords = "INSERT INTO attendance(studentID, classID, instructorID, dateAdded, attendanceType, studioID, paymentType) VALUES (".$row_orderInfo['studentID'].",".$row_orderInfo['classID'].",".$instructorID.",'".$row_orderInfo['classDate']."','Drop In',".$row_orderInfo['studioID'].",'Paydunk')";
	  mysqli_select_db($database_wotg, $wotg);
	  mysqli_query($addrecords, $wotg) or die(mysqli_error());	
	}
	if ($orderInfo=='Pre Paid Package'){
		//update student table
		$classesLeft = (int)$row_student['classesLeft']+$row_orderInfo['classesAdded'];
		$query_prepaid = "UPDATE students SET classesLeft = ".$classesLeft." WHERE studentID=".$studentID;
		$prepaid = mysqli_query($query_prepaid, $wotg) or die(mysqli_error());
		error_log("prepaid info updated");
	}
	if ($orderInfo=='Membership'){
		//$startDate = date("Y-m-d", strtotime("now"));
		$startDate = $row_orderInfo['membershipStartDate'];
		$startDate = date ( 'Y-m-d' , $startDate );
		$endDate = strtotime ( '+1 month' , strtotime ( $startDate ) ) ;
		$endDate = date ( 'Y-m-d' , $endDate );
		
		error_log("startDate: ".$startDate);
		error_log("endDate: ".$endDate);
	
		//update members table
		$query_prepaid = "INSERT INTO members (studentID, studioID, membershipFee, startDate, endDate) VALUES (".$row_orderInfo['studentID'].",".$row_orderInfo['studioID'].",".(int)$row_orderInfo['amount'].",'".$startDate."','".$endDate."')";
		$prepaid = mysqli_query($query_prepaid, $wotg) or die(mysqli_error());
		error_log("member info updated");
	}
	
	
}
if ($payment->getState()=='failed' || $payment->getState()=='expired') {
	$status = 'error';
		error_log("status = error!");
	//the payment failed to process
	//do some stuff here - update your order database, etc.
}

//set data for PUT request
$bodyparams = array(
			"client_id" => "KXQl371hm0g9tWCTu48RIYpfys3UObvCnl6j9QeN", // your APP ID goes here!!!
			"client_secret" => "2th83nuwPyQjoaFP5botvl2jfr6r3hM0wMK0Mm6w", // your APP SECRET goes here!!!
			"status" => $status);
//sends the PUT request to the Paydunk API
function CallAPI($method, $url, $data = false){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_PUT, 1);		
		$update_json = json_encode($data);	
		curl_setopt($curl, CURLOPT_URL, $url . "?" . http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSLVERSION, 4);
		$result = curl_exec($curl);  
		$api_response_info = curl_getinfo($curl);
		curl_close($curl);
		return $result;
}
//get the transaction_uuid from Paydunk & call the the Paydunk API
$transaction_uuid = $_POST['transaction_uuid'];
if (isset($transaction_uuid)) {
	error_log("calling paydunk api");
	$url = "https://api.paydunk.com/api/v1/transactions/".$transaction_uuid;
	CallAPI("PUT", $url, $bodyparams);	
}
if ($status=='error' OR $status=='declined') header("Location: http://studio.4siteusa.com/student-home.php?action=error");
header("Location: http://studio.4siteusa.com/student-home.php?action=reserved");
	error_log("you shouldn't see this right now");
?>
