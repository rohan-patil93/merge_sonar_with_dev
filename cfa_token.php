<?php
require 'vendor/autoload.php';

//echo "Test Work 1"; exit;

use Google\Auth\Credentials\ServiceAccountCredentials;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

//echo "Test Work 2"; exit;
// Path to your service account JSON file
$serviceAccountPath = __DIR__ . '/infistaq-firebase-adminsdk-s10we-0ea8b831cd.json';
 
//echo "Test Work 3".$serviceAccountPath; exit;

// Define the required scopes
$scopes = [
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/firebase.messaging',
    'https://www.googleapis.com/auth/firebase.database',
];
 
//echo "Test Work 4"; exit;

try {
    // Authenticate using the service account credentials
    //echo "Test Work 5"; exit;
    $credentials = new ServiceAccountCredentials($scopes, $serviceAccountPath);
    //echo "Test Work 6"; exit;
    $accessToken = $credentials->fetchAuthToken()['access_token'];
    //echo "Access Token: " . $accessToken . PHP_EOL;  exit;  
    echo $accessToken; exit;
    /*// Initialize the Firebase Admin SDK
    $factory = (new Factory)
        ->withServiceAccount($serviceAccountPath);
 
    $messaging = $factory->createMessaging();
 
    // The FCM token should be obtained from the client application
    $fcmToken = 'your-client-fcm-token';
 
    // Example: Send a test notification
    $message = CloudMessage::withTarget('token', $fcmToken)
        ->withNotification([
            'title' => 'Test Notification',
            'body' => 'This is a test notification from PHP',
        ]);
 
    $messaging->send($message);
    echo "Message sent successfully." . PHP_EOL;*/
 
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}

?>