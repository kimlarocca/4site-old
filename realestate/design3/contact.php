<?php require_once('Connections/cms.php'); ?>
<?php
$pageID = $contactPage;
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysqli_real_escape_string") ? mysqli_real_escape_string($theValue) : mysqli_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_Recordset1 = "-1";
if (isset($_GET['listingID'])) {
  $colname_Recordset1 = $_GET['listingID'];
}
mysqli_select_db($database_cms, $cms);
$query_Recordset1 = sprintf("SELECT * FROM listings WHERE listingID = %s", GetSQLValueString($colname_Recordset1, "int"));
$Recordset1 = mysqli_query($query_Recordset1, $cms) or die(mysqli_error());
$row_Recordset1 = mysqli_fetch_assoc($Recordset1);
$totalRows_Recordset1 = mysqli_num_rows($Recordset1);

mysqli_select_db($database_cms, $cms);
$query_currentPage = "SELECT * FROM cmsPages WHERE pageID = ".$pageID;
$currentPage = mysqli_query($query_currentPage, $cms) or die(mysqli_error());
$row_currentPage = mysqli_fetch_assoc($currentPage);
$totalRows_currentPage = mysqli_num_rows($currentPage);

mysqli_select_db($database_cms, $cms);
$query_websiteInfo = "SELECT * FROM cmsWebsites WHERE websiteID = ".$websiteID;
$websiteInfo = mysqli_query($query_websiteInfo, $cms) or die(mysqli_error());
$row_websiteInfo = mysqli_fetch_assoc($websiteInfo);
$totalRows_websiteInfo = mysqli_num_rows($websiteInfo);
?>
<?php
$emailSent = false;
$subject = '[Contact from '.$row_websiteInfo['url'].']';
$emailTo = $row_websiteInfo['emailAddress'];
$emailTo = 'kim@4siteusa.com';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = stripslashes(trim($_POST['name']));
    $email    = stripslashes(trim($_POST['email']));
    $phone    = stripslashes(trim($_POST['phone']));
    $message  = stripslashes(trim($_POST['message']));
    $pattern  = '/[\r\n]|Content-Type:|Bcc:|Cc:/i';
    if (preg_match($pattern, $name) || preg_match($pattern, $email) || preg_match($pattern, $subject)) {
        die("Header injection detected");
    }
    $emailIsValid = preg_match('/^[^0-9][A-z0-9._%+-]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/', $email);
    if($name && $email && $emailIsValid && $subject && $message){
        $body = "Nome: $name <br /><br /> Email: $email <br /><br /> phone: $phone";
		if ($totalRows_Recordset1 > 0) $body = $body."<br/><br/>Listing Inquiry For: ".$row_Recordset1['propertyLocation'];
		$body = $body." <br /><br /> message: $message";
        $headers  = 'MIME-Version: 1.1' . PHP_EOL;
        $headers .= 'Content-type: text/html; charset=utf-8' . PHP_EOL;
        $headers .= "From: $name <$email>" . PHP_EOL;
        $headers .= "Return-Path: $emailTo" . PHP_EOL;
        $headers .= "Reply-To: $email" . PHP_EOL;
        $headers .= "X-Mailer: PHP/". phpversion() . PHP_EOL;
        mail($emailTo, $subject, $body, $headers);
        $emailSent = true;
    } else {
        $hasError = true;
    }
}
?>
<?php
$pageTitle = $row_currentPage['pageTitle'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><!-- InstanceBegin template="/Templates/template.dwt.php" codeOutsideHTMLIsLocked="false" -->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php echo $row_websiteInfo['firstName']; ?> <?php echo $row_websiteInfo['lastName']; ?> | <?php echo $row_currentPage['pageTitle']; ?></title>
<!-- InstanceEndEditable -->
<link rel="stylesheet" type="text/css" href="styles/Wicked.css"/>
<link rel="stylesheet" type="text/css" href="styles/styles.css"/>
<!-- InstanceBeginEditable name="head" -->
<script type="text/javascript">
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.name; if ((val=val.value)!="") {
        if (test.indexOf('isEmail')!=-1) { p=val.indexOf('@');
          if (p<1 || p==(val.length-1)) errors+='- '+nm+' must contain an e-mail address.\n';
        } else if (test!='R') { num = parseFloat(val);
          if (isNaN(val)) errors+='- '+nm+' must contain a number.\n';
          if (test.indexOf('inRange') != -1) { p=test.indexOf(':');
            min=test.substring(8,p); max=test.substring(p+1);
            if (num<min || max<num) errors+='- '+nm+' must contain a number between '+min+' and '+max+'.\n';
      } } } else if (test.charAt(0) == 'R') errors += '- '+nm+' is required.\n'; }
    } if (errors) alert('The following error(s) occurred:\n'+errors);
    document.MM_returnValue = (errors == '');
} }
</script>
<!-- InstanceEndEditable -->
<script src="scripts/modernizr.custom.js"></script>
</head>

<body>
<div class="container">
  <div class="hero">
    <div class="heroContent">
      <h1><?php echo $pageTitle; ?></h1>
    </div>
  </div>
  <div class="main clearfix">
    <nav id="menu" class="nav">
      <ul>
        <li> <a href="index.php"> <span class="icon"> <i aria-hidden="true" class="icon-home"></i> </span> <span>Home</span> </a> </li>
        <li> <a href="listings.php"> <span class="icon"> <i aria-hidden="true" class="icon-listings"></i> </span> <span>Listings</span> </a> </li>
        <li> <a href="search.php"> <span class="icon"> <i aria-hidden="true" class="icon-search"></i> </span> <span>MLS Search</span> </a> </li>
        <li> <a href="localInfo.php"> <span class="icon"> <i aria-hidden="true" class="icon-local"></i> </span> <span>Local Info</span> </a> </li>
        <li> <a href="about.php"> <span class="icon"> <i aria-hidden="true" class="icon-about"></i> </span> <span>About Me</span> </a> </li>
        <li> <a href="contact.php"> <span class="icon"> <i aria-hidden="true" class="icon-contact"></i> </span> <span>Contact</span> </a> </li>
      </ul>
    </nav>
  </div>
</div>
<!-- /container --> 

<!-- main content -->
<div class="content"> <!-- InstanceBeginEditable name="mainContent" -->

<?php if ($_GET['action'] == 'sent') { echo '<br><br><p class="wf_centered">Thank you for your submission! Someone will get back to you shortly.<br><br /></p>'; } else { ?>
<?php echo $row_currentPage['pageContent']; ?>
<hr />
<form class="wickedForm" action="http://www.4siteusa.com/process-form.php" method="post" id="contact-form" onsubmit="MM_validateForm('name','','R','email','','RisEmail','message','','R');return document.MM_returnValue">
<input type="text" id="name" name="name" placeholder=" name" required><br />
<input type="text" id="email" name="email" placeholder=" email address" required><br />
<input type="text" id="phone" name="phone" type="tel" placeholder=" phone number"><br />
<textarea rows="5" id="message" name="message" placeholder=" please enter your message here!" required></textarea><br />
<input name="submit" value="submit" type="submit" class="button" style="margin:0; margin-bottom:20px" />
<input type="hidden" value="<?php echo $row_websiteInfo['emailAddress']; ?>" name="toEmail" id="toEmail" />
<input type="hidden" value="http://<?php echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]; ?>" name="redirect" id="redirect" />
</form>
<?php } ?>
<!-- InstanceEndEditable -->
</div>
<!-- footer -->
<div class="footer">
  <div class="wf_row">
    <div class="wf_column wf_two">
      <h2><a href="index.php">Home</a> | <a href="about.php">About Me</a> | <a href="listings.php">Listings</a> | <a href="search.php">Property Search</a> | <a href="localInfo.php">Local Info</a> | <a href="contact.php">Contact Me</a></h2>
      <p>Copyright &copy; <?php echo $row_websiteInfo['firstName']; ?> <?php echo $row_websiteInfo['lastName']; ?> <?php echo date("Y"); ?>, All Rights Reserved.</p>
      <p>Web Design by <a href="http://www.4siteusa.com">4 Site</a>.</p>
    </div>
    <div class="wf_column wf_two wf_text_right">
      <h2><?php echo $row_websiteInfo['companyName']; ?></h2>
      <p><?php echo $row_websiteInfo['iaddress']; ?></p>
      <?php if ($row_websiteInfo['iaddress2'] <> ''){ ?>
      <p><?php echo $row_websiteInfo['iaddress2']; ?></p>
      <?php } ?>
      <p><?php echo $row_websiteInfo['phoneNumber']; ?></p>
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> 
<!-- InstanceBeginEditable name="scripts" -->
<!-- InstanceEndEditable --> 
<script src="scripts/scripts.js"></script>
</body>
<!-- InstanceEnd --></html>
<?php
mysqli_free_result($Recordset1);

mysqli_free_result($currentPage);

mysqli_free_result($websiteInfo);
?>
