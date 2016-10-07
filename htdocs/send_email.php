<?php
/**
 * This example shows settings to use when sending via Google's Gmail servers.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Europe/London');

require_once 'phpmailer/PHPMailerAutoload.php';
require_once 'db_functions.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;

//Tell PHPMailer to use SMTP
$mail->isSMTP();

//Enable SMTP debugging
// 0 = off (for production use)
// 1 = client messages
// 2 = client and server messages
$mail->SMTPDebug = 0;

//Ask for HTML-friendly debug output
$mail->Debugoutput = 'html';

//Set the hostname of the mail server
$mail->Host = 'smtp.gmail.com';
// use
// $mail->Host = gethostbyname('smtp.gmail.com');
// if your network does not support SMTP over IPv6

//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->Port = 587;

//Set the encryption system to use - ssl (deprecated) or tls
$mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$mail->SMTPAuth = true;

$config = parse_ini_file('../_myconfig/email_config.ini'); 
$my_username = $config['username'];
$my_pass = $config['pass'];

//Username to use for SMTP authentication - use full email address for gmail
$mail->Username = $my_username;

//Password to use for SMTP authentication
$mail->Password = $my_pass;

//Set who the message is to be sent from
$mail->setFrom('donotreply@orthonics.zapto.org', 'Orthonics Auto-Report');

//Set an alternative reply-to address
$mail->addReplyTo('replyto@example.com', 'First Last');

//Set who the message is to be sent to
$mail->addAddress('jeremych+orthonics@outlook.com', 'Jeremy Chan');
$mail->addAddress('av2013@ic.ac.uk','Alessandro Versini');

$date = date('Y-m-d H:i:s ');
//Set the subject line
$mail->Subject = 'Orthonics Auto-Email: '.$date;

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body


$f_id = @$_GET['flight_id'];
$i_id = @$_GET['image_id'];

if (!isset($f_id) OR !isset($i_id)) {die('Provide an ID please.');}

$email_ver = '2';
$url_contents = url_get_contents('http://orthonics.zapto.org/send_email_contents'.$email_ver.'.php'.'?flight_id='.$f_id.'&image_id='.$i_id);
//echo $url_contents;
//var_dump($url_contents);
$mail->msgHTML($url_contents, dirname(__FILE__));


//Replace the plain text body with one created manually
$mail->AltBody = 'This email has been generated automatically, please do not reply.';

//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
if (!$mail->send()) {
    echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}