<?php
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable('/var/www/html', 'api.env');
$dotenv->load();

echo "API Key: " . $_ENV['GOOGLE_API_KEY'];
