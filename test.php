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


/*

$method = $_GET['method'];

if($_GET['method']=='todisplayemployee'){
	if($_GET['token']=='xcqwerty1456315'){	
		$array[] = array('id'=>1, 'name'=>'Ramana Kumar', 'department'=> 'Sale', 'designation'=>'Sale Manager', 'join_date'=> '2024-03-12', 'phone'=>'89765432'); 
		$array[] = array('id'=>2, 'name'=>'Kiran Kumar', 'department'=> 'Sale', 'designation'=>'Sales Person', 'join_date'=> '2024-02-22', 'phone'=>'79765432'); 
		$array[] = array('id'=>3, 'name'=>'Usha Rani', 'department'=> 'Sale', 'designation'=>'CEO', 'join_date'=> '2024-01-18', 'phone'=>'69765432'); 
		$array[] = array('id'=>4, 'name'=>'Sadya', 'department'=> 'Admin', 'designation'=>'Admin', 'join_date'=> '2024-04-02', 'phone'=>'65565432'); 
		$array[] = array('id'=>5, 'name'=>'Pavitra', 'department'=> 'Admin', 'designation'=>'Offer', 'join_date'=> '2024-01-11', 'phone'=>'8435432'); 
	}
	echo json_encode($array);exit;
}
if($_GET['method']=='tocheckpost'){
	if($_GET['token']=='xcqwerty1456315'){	
		if($_GET['approve_id']==1){
			$array = array('status'=>'success', 'Message'=>'Aproved the data'); 
		}
	}
	echo json_encode($array);exit;
}*/
?>
