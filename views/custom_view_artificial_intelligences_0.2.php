<?php 
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
//echo "<pre>"; 
print_r($_POST); 
//echo "</pre>"; 
$admin_type = $_SESSION['admin_type'];
if($_POST['page_type'] == "HR_ADMIN_DASHBOARD"){
	$url = API."?mode=employeedashboard&emp_id=".$_SESSION['emp_id']."&admin_type=".$admin_type."&user_email=".$_SESSION['user_email']; 
	$responseData = callAPI($url);
	$data = json_decode($responseData,1);
}else{
    $rawJson = $_POST['dashboard_data'] ?? '';
    $dashboardData = json_decode($rawJson, true); 
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "❌ JSON Decode Error: " . json_last_error_msg();
        exit;
    } 
    $fullHtml = $dashboardData['full_html'] ?? '';
    $forms = $dashboardData['forms'] ?? [];
    $tables = $dashboardData['tables'] ?? []; 
    $summaryText = "🔍 Extracted Dashboard Summary\n";
    $summaryText .= "----------------------------------\n";
    $summaryText .= "Page Type: (Dashboard)\n";
    $summaryText .= "Full HTML Length: " . strlen($fullHtml) . " characters\n";
    $summaryText .= "Total Forms Found: " . count($forms) . "\n";
    $summaryText .= "Total Tables Found: " . count($tables) . "\n\n"; 
    if (!empty($forms)) {
        $summaryText .= "📋 Forms Summary:\n";
        foreach ($forms as $form) {
            $summaryText .= "- Form ID: " . ($form['id'] ?? 'N/A') . "\n";
            $summaryText .= "  Inputs: " . count($form['inputs']) . " fields\n";
            if (!empty($form['inputs'])) {
                foreach ($form['inputs'] as $input) {
                    $summaryText .= "   • {$input['name']} ({$input['type']})\n";
                }
            }
        }
        $summaryText .= "\n";
    } 
    if (!empty($tables)) {
        $summaryText .= "📊 Tables Summary:\n";
        foreach ($tables as $tbl) {
            $summaryText .= "- Table #" . $tbl['index'] . "\n";
            $summaryText .= "  Headers: " . implode(', ', $tbl['headers']) . "\n";
            $summaryText .= "  Rows: " . count($tbl['rows']) . "\n";
        }
        $summaryText .= "\n";
    } 

    $responseData = $summaryText;
}

$request = array(
    "model" => "gpt-4o-2024-08-06",
    "store" => true,
    "messages" => array(
        array(
            "role" => "system",
            "content" => "You are an expert data interpreter and business dashboard analyst. 
            Your task is to transform raw structured dashboard data into a well-written, 
            insightful, and easy-to-understand summary for users. 
            Write like a human explaining insights, not just listing facts. 
            Your tone should be professional, analytical, and slightly conversational."
        ),
        array(
            "role" => "user",
            "content" => "Analyze this dashboard data and give me 3–5 short insights with 
            clear action points — keep it concise (less than 10 words per bullet), 
            easy to understand, and avoid long explanations.\n\n" . $responseData
        )
    ),
    "temperature" => 0.2
); 
$send_req = json_encode($request);
// echo "</br></br></br></br>output json</br></br></br></br>";
// echo $send_req;
/**/
$curlurl = "https://api.openai.com/v1/chat/completions";
$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $curlurl,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS =>$send_req,
	CURLOPT_HTTPHEADER => array(
  		'Content-Type: application/json',
  		'Authorization: Bearer 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',
  	),
));
$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$returnresult = json_decode($response,1);
/**/
// print_r($response);
?>
<div class="modal-header  ">
	<h5 class="modal-title">Dashboard Summary</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body w-b-w-s">
<?php 
$content = $returnresult['choices'][0]['message']['content'] ?? ''; 
$lines = preg_split('/[\n\r]+/', trim($content)); 
$i = 1; 
foreach ($lines as $line) { 
    $bullet = trim($line, "-• \t");
    $bullet = str_replace(['**', '*'], '', $bullet); // remove markdown bold/italic
    $bullet = trim($bullet); 
    if ($bullet !== '') {
        echo "<span class='d-block m-2 f-14 f-w-600'>{$i}) {$bullet}</span>";
        $i++;
    }
}
?>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" >Close</button>
</div>