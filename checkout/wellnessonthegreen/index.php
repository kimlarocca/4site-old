<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>Wellness On The Green | Checkout</title>
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400" rel="stylesheet" type="text/css">
<style>
body {
	font-family: 'Source Sans Pro', Arial, sans-serif;
	margin: 0;
	color: #333;
	font-size: 16px;
}
a, a:link, a:visited {
	color: #522c91; text-decoration:none;
}
p { margin:0; }
form {
	width: 300px; margin:auto;
}
input[type=submit], .button {
	border: 2px #999 solid;
	color: #333;
	text-transform: uppercase;
	cursor: pointer;
	display: inline-block;
	padding: 10px;
	font-family: 'Source Sans Pro', Arial, sans-serif;
	font-size: 16px;
	letter-spacing: 0.08em;
	background-color: transparent;
	width: 250px;
}
input[type=submit]:hover, .button:hover {
	opacity: .7;
}
input[type="text"], select {
	background-color: #f0f0f0;
	display: block;
	width: 250px; height:40px;
	font-family: 'Source Sans Pro', Arial, sans-serif;
	font-size: 16px;
	appearance: none;
	box-shadow: none;
	border-radius: none;
	padding: 10px;
	margin: 5px 0 5px 0;
	border: none;
	border-bottom: solid 3px #666;
	transition: border 0.3s;
}
input[type="text"]:focus {
	outline: none;
	border-bottom: solid 3px #999;
}
strong {
	text-transform:uppercase; font-size:1.2em;
}
</style>
<script type="text/javascript">
function MM_validateForm() { //v4.0
  if (document.getElementById){
    var i,p,q,nm,test,num,min,max,errors='',args=MM_validateForm.arguments;
    for (i=0; i<(args.length-2); i+=3) { test=args[i+2]; val=document.getElementById(args[i]);
      if (val) { nm=val.alt; if ((val=val.value)!="") {
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
</head>

<body>
<form action="buy.php" method="post" onsubmit="MM_validateForm('notes','','R','firstName','','R','lastName','','R','emailAddress','','RisEmail');return document.MM_returnValue">
 
Choose One:<br />
  <select name="amount" style="margin-bottom:20px">
      <option value="20">$20: Drop In</option>
      <option value="15" <?php if ($_GET["class"]=="bellydance") echo selected ?>>$15: Drop In (Bellydance Classes Only)</option>
      <option value="75">$75: 4 Class Package</option>
      <option value="120">$120: 8 Class Package</option>
      <option value="135">$135: Membership (1 Month)</option>
    </select>
Class You Want To Take:<br />
(please enter the class name, date &amp; time)<br />
  <input type="text" id="notes" alt="Class Name" name="notes" />
    <strong><br />
    Student Information</strong><br /><br />
First Name:<br />
  <input type="text" id="firstName" alt="First Name" name="firstName" />
  <br />
  Last Name:<br />
  <input type="text" id="lastName" alt="Last Name" name="lastName" />
  <br />
  Email Address:<br />
  <input name="emailAddress" type="text" id="emailAddress" onblur="MM_validateForm('notes','','R','firstName','','R','lastName','','R','emailAddress','','RisEmail');return document.MM_returnValue" alt="Email Address" />
  <br />
  <input type="submit" value="CONTINUE &amp; PAY" />
  <input type="hidden" name="studioID" value="1" />
  </p>
</form>
</body>
</html>
