<?php
function generateLicenseKey($userLimit, $expiryDate, $secretKey) {
    $data = "$userLimit|$expiryDate";
    $hash = hash_hmac('sha256', $data, $secretKey);
    return base64_encode("$data|$hash");
}

$secretKey = "z@Y4!dQ2#vG8^pL6&Xf9*KcM1$wT7~B0"; // Keep this private
$userLimit = 20;
$expiryDate = "2026-06-30";

$licenseKey = generateLicenseKey($userLimit, $expiryDate, $secretKey);
echo "License Key: $licenseKey</br>";


$result = validateLicenseKey($licenseKey, $secretKey);
function validateLicenseKey($licenseKey, $secretKey) {
    $decoded = base64_decode($licenseKey);
    if (!$decoded) {
        return false;
    }

    list($userLimit, $expiryDate, $hash) = explode('|', $decoded);

    // Recalculate the hash
    $validHash = hash_hmac('sha256', "$userLimit|$expiryDate", $secretKey);

    if ($hash !== $validHash) {
        return false; // Invalid key
    }

    if (strtotime($expiryDate) < time()) {
        return false; // Expired key
    }

    return [
        "userLimit" => (int)$userLimit,
        "expiryDate" => $expiryDate
    ];
}



if ($result) {
    echo "License is valid.\n";
    echo "User Limit: " . $result['userLimit'] . "\n";
    echo "Expires on: " . $result['expiryDate'] . "\n";
} else {
    echo "Invalid or expired license key.\n";
}


