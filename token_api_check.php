<?php
require __DIR__.'/vendor/autoload.php';

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
//echo  __DIR__; exit;
// Initialize Firebase

$serviceAccountPath = __DIR__ . '/infistaq-firebase-adminsdk-s10we-0ea8b831cd.json';
$factory = (new Factory)->withServiceAccount($serviceAccountPath);
$auth = $factory->createAuth();

// Load credentials from .env file (never hard-code secrets in source)
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        putenv(trim($key) . '=' . trim($value));
    }
}

$email    = getenv('FIREBASE_EMAIL')    ?: null;
$password = getenv('FIREBASE_PASSWORD') ?: null;

if (empty($email) || empty($password)) {
    http_response_code(500);
    echo '❌ Firebase credentials are not configured. Set FIREBASE_EMAIL and FIREBASE_PASSWORD environment variables.';
    exit;
}

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