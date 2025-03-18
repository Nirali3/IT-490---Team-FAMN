<?php
//require 'vendor/autoload.php';

//$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html', 'api.env');
//$dotenv->load();

//echo "API Key: " . $_ENV['GOOGLE_API_KEY'];

if (function_exists('mail')) {
    echo "Mail function is enabled.";
} else {
    echo "Mail function is not enabled on this server.";
}
?>

