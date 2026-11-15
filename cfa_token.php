<?php
require_once 'vendor/autoload.php';


use Google\Auth\Credentials\ServiceAccountCredentials;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

//echo "Test Work 2"; exit;
// Path to your service account JSON file
$serviceAccountPath = __DIR__ . '/infistaq-firebase-adminsdk-s10we-0ea8b831cd.json';


// Define the required scopes
$scopes = [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/firebase.messaging',
    'https://www.googleapis.com/auth/firebase.database',
];


try {
    // Authenticate using the service account credentials
    //echo "Test Work 5"; exit;
    $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);
    $accessToken = $credentials->fetchAuthToken()['access_token'];
    echo $accessToken; exit;

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}

