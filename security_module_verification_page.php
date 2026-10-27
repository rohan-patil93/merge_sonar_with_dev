<?php 
include('dbconfig.php');
//ini_set("display_errors",1);
$param= $_GET['param'];
$allparam = json_decode(base64_decode($param),1);
$urlhit = SITEURL.'/api.php?mode=approveVisitor&rid='.$allparam['rid'].'&user_id=1&data_name='.$allparam['dbname'].'&company_id='.$allparam['company_id'].'&status='.$allparam['status'];
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $urlhit,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));
$response = curl_exec($curl);
curl_close($curl);
$datas = json_decode($response,1);
header("Content-Type: text/html");
if ($datas['status'] === 'sucess') {
  $emoji = ($allparam['status'] === 'approve') ? '✅' : '❌';
  $title = $allparam['status'] === 'approve' ? 'Entry Approved' : 'Entry Rejected';
  $msg = $allparam['status'] === 'approve' ? 'You have authorized the visitor\'s entry. The guard has been notified.' : 'You have rejected the visitor\'s entry request.';
  $class = $allparam['status'] === 'approve' ? 'success' : 'error';
} else {
  $emoji = '⚠️';
  $title = 'Error';
  $msg   = $datas['message'] ?? 'An error occurred.';
  $class = 'error';
}
echo '<!DOCTYPE html><html><head><title>Visitor Authorization</title>
<style>body{font-family:Arial,sans-serif;background:#f4f5f7;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
.card{background:#fff;padding:40px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,.1);text-align:center;max-width:400px;width:90%}
.icon{font-size:64px;margin-bottom:20px}.success{color:#10b981}.error{color:#ef4444}
h2{margin:0 0 10px;color:#1e293b}p{color:#64748b;font-size:14px;margin:0;line-height:1.5}</style></head>
<body><div class="card"><div class="icon ' . $class . '">' . $emoji . '</div>
<h2>' . $title . '</h2><p>' . $msg . '</p></div></body></html>';
?>