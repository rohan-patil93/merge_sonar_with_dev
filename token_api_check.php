<?php
require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
//echo  __DIR__; exit;
// Initialize Firebase

$serviceAccountPath = __DIR__ . '/infistaq-firebase-adminsdk-s10we-0ea8b831cd.json';
$factory = (new Factory)->withServiceAccount($serviceAccountPath);
$auth = $factory->createAuth();

$email = 'infistaqfirebase@gmail.com';
$password = 'Infisuite@123';
//echo  __DIR__; exit;
/*
try {
    echo "Valid credentials q";
    $signInResult = $auth->signInWithEmailAndPassword($email, $password);
    echo "ID Token: ";
    $idToken = $signInResult->idToken();
    print_r($idToken);

   // echo "ID Token: " . $idToken->toString();
} catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
    echo "Invalid credentials";
}*/
try {
    // Sign in with email and password
    $signInResult = $auth->signInWithEmailAndPassword($email, $password);
    $idToken = $signInResult->idToken();

    echo "✅ Sign-in successful!<br>ID Token: " . $idToken->toString();
} catch (\Kreait\Firebase\Exception\Auth\InvalidPassword $e) {
    echo "❌ Invalid password: " . $e->getMessage();
} catch (\Kreait\Firebase\Exception\Auth\UserNotFound $e) {
    echo "❌ No user found with this email.";
} catch (\Throwable $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>