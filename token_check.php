<?php
echo phpinfo(); exit;
//if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idToken'])) {
    $idToken = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjcxMTE1MjM1YTZjNjE0NTRlZmRlZGM0NWE3N2U0MzUxMzY3ZWViZTAiLCJ0eXAiOiJKV1QifQ.eyJuYW1lIjoiaW5maXN0YXEgZmlyZWJhc2UiLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSVhOUldFRm5OQ1VJT2JXYjhqZF9IaFA4ME0xcmNqX0pEa0tpTHBfUzZTT0hvU2lnPXM5Ni1jIiwiaXNzIjoiaHR0cHM6Ly9zZWN1cmV0b2tlbi5nb29nbGUuY29tL2luZmlzdGFxIiwiYXVkIjoiaW5maXN0YXEiLCJhdXRoX3RpbWUiOjE3NDM5OTY1NDQsInVzZXJfaWQiOiJUSVBXTVh0UndzZFhJdVNMS3U1dFFhYnZJSVAyIiwic3ViIjoiVElQV01YdFJ3c2RYSXVTTEt1NXRRYWJ2SUlQMiIsImlhdCI6MTc0Mzk5NjU0NCwiZXhwIjoxNzQ0MDAwMTQ0LCJlbWFpbCI6ImluZmlzdGFxZmlyZWJhc2VAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImZpcmViYXNlIjp7ImlkZW50aXRpZXMiOnsiZ29vZ2xlLmNvbSI6WyIxMDYxODI1MTg5NDYzMjQwODYxMTIiXSwiZW1haWwiOlsiaW5maXN0YXFmaXJlYmFzZUBnbWFpbC5jb20iXX0sInNpZ25faW5fcHJvdmlkZXIiOiJnb29nbGUuY29tIn19.gnpbQpdbumW8zBprefPZ_OiKVVpN7Umslv3aSaC3KTrRjT_AKLJyZYoVeYHCXRd3wVvPxs4xi9GL7ppuJytUoPlQKAcvGQzv3cDLI6x4WbIlRfbWCNZb_OBuhpmRGjFGIzLelmeuYva_PDNLWi0TDvB4lq04nVk7AHoj-uui2Ix0ps2lCOy738f3p9qqPWOVlC7vp0Z_5xUOi0b5h3KlCvVF4b8JZHQJt1bUqXJ10uHgxwNIlWlZmy7W03NYKRKHnDGOxZ4ogKEzQXo0KVzCdJVR3YHYHqsi76uNqvPZK8f61Yv5hBYsfCCbz7oFfafSUYk2P3UWzpKUqlUDSpP_6g";

    // Verify with Google
    echo $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($idToken);
    $response = file_get_contents($url);
    $data = json_decode($response, true);
    echo "Check : "; print_r($data);
    if (isset($data['email'])) {
        echo "Verified user: " . htmlspecialchars($data['email']);
    } else {
        echo "Token invalid or expired.";
    }
/*} else {
    echo "No token received.";
}*/
?>