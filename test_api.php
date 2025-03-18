<?php
//require 'vendor/autoload.php';

//$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html', 'api.env');
//$dotenv->load();

//echo "API Key: " . $_ENV['GOOGLE_API_KEY'];

//if (function_exists('mail')) {
//    echo "Mail function is enabled.";
//} else {
  //  echo "Mail function is not enabled on this server.";
//}
$to = "jj@gmail.com"; // Replace with your email
$subject = "Test Email from PHP Mail()";
$message = "This is a test email sent from the PHP mail() function.";
$headers = "From: noreply@yourwebsite.com\r\n";
$headers .= "Reply-To: noreply@yourwebsite.com\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

if (mail($to, $subject, $message, $headers)) {
    echo "Test email sent successfully!";
} else {
    echo "Failed to send test email.";
}
?>

