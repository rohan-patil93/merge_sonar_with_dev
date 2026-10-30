<?php
// Token must be supplied by the caller at runtime — never hard-code secrets in source.
// Accepts the Firebase ID token from:
//   1. POST field       → $_POST['idToken']
//   2. Authorization header → Bearer <token>

$idToken = '';

$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
if (str_starts_with($authHeader, 'Bearer ')) {
    $idToken = trim(substr($authHeader, 7));
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idToken'])) {
    $idToken = trim($_POST['idToken']);
}

if (empty($idToken)) {
    http_response_code(401);
    echo json_encode(['error' => 'No ID token provided.']);
    exit;
}

// Verify the token with Google's tokeninfo endpoint
$url      = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
$response = file_get_contents($url);
$data     = json_decode($response, true);

if (isset($data['email'])) {
    echo 'Verified user: ' . htmlspecialchars($data['email']);
} else {
    http_response_code(401);
    echo 'Token invalid or expired.';
}
?>