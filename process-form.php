<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once 'swift/swift_required.php';

$email = 'noreply@4siteusa.com';
$fromEmail = $_POST['email'];
$toEmail = $_POST['toEmail'];
$message =  'Name: '.$_POST['name'].'<br>Phone: '.$_POST['phone'].'<br>Email: '.$_POST['email'].'<br><br>Message:<br><br>'.$_POST['message'];
$subject = 'Contact from ['.$_POST['redirect'].']';
$redirect = $_POST['redirect'];

$username = "711ee8490b48e5120b50dd66ff9f0ac9";
$password = "40cb03aee60ee4a06f34fb62800c04af";
 
//godaddy $transport = new \Swift_SmtpTransport("smtpout.secureserver.net", 25);
$transport = new \Swift_SmtpTransport("in-v3.mailjet.com", 25);
$transport->setUsername($username);
$transport->setPassword($password);
 
$mailer = new \Swift_Mailer($transport);
 
// Create the message
$message = Swift_Message::newInstance()

  // Give the message a subject
  ->setSubject($subject)

  // Set the From address with an associative array
  ->setFrom(array($email))

  // Set the To addresses with an associative array
  ->setTo(array($toEmail))
  
  ->setReplyTo($fromEmail)

  // Give it a body
  ->setBody($message, 'text/html')
  ;
  
$message->setFrom($email);
 
$mailer->send($message);

flush();
header('Location: '.$redirect.'?action=sent');
die('message sent');
?>