<?php

//echo  __DIR__; exit;
require_once __DIR__.'/vendor/autoload.php';
//echo  __DIR__; exit;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

$serviceAccountPath = __DIR__ . '/infistaq-firebase-adminsdk-s10we-0ea8b831cd.json';

$factory = (new Factory)->withServiceAccount($serviceAccountPath);
$auth = $factory->createAuth();

// Token must be supplied at runtime by the caller — never hard-code secrets in source.
// Accept the Firebase ID token from the Authorization header (Bearer <token>)
// or fall back to a POST field named 'id_token'.
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (str_starts_with($authHeader, 'Bearer ')) {
    $idTokenString = trim(substr($authHeader, 7));
} else {
    $idTokenString = $_POST['id_token'] ?? '';
}

if (empty($idTokenString)) {
    http_response_code(401);
    echo json_encode(['error' => 'No ID token provided.']);
    exit;
}

try {
    $verifiedIdToken = $auth->verifyIdToken($idTokenString);
    $uid = $verifiedIdToken->claims()->get('sub');

    echo "User ID: " . $uid;
} catch (\Kreait\Firebase\Exception\Auth\FailedToVerifyToken $e) {
    echo "Invalid token: " . $e->getMessage();
}
