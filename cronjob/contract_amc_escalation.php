<?php
/**
 * Hourly AMC visit escalations — contract_amc_details.escalation_config
 * Dev server cron: php cron_job/contract_amc_escalation.php [company_id] [database_name]
 * Example: php cron_job/contract_amc_escalation.php 1 infistaqin_infisuite_2
 */
$apiBase = 'http://dev.infistaq.in/api.php';

$companyId = (int) ($argv[1] ?? 1);
$dbName = $argv[2] ?? 'infistaqin_infisuite_2';

$url = $apiBase
    . '?mode=runcontractamcescalationcron'
    . '&method=get'
    . '&company_id=' . $companyId
    . '&data_name=' . urlencode($dbName)
    . '&user_id=1';

$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
));
$response = curl_exec($curl);
if ($response === false) {
    echo 'Curl Error: ' . curl_error($curl) . PHP_EOL;
} else {
    echo $response . PHP_EOL;
}
