<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_POST['company_id'] = $_SESSION['company_id'];
$_POST['user_id'] = $_SESSION['userid'];
//value
//transaction_table
//filter_field
$resultid = $_POST['resultid'];
$url = API . "?method=post&mode=gettexttodyanmicsearching";
$response = file_post_contents($url, $_POST, 'update');
$resultres = json_decode($response, 1);
$result = $resultres['result'];

<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_POST['company_id'] = $_SESSION['company_id'];
$_POST['user_id'] = $_SESSION['userid'];
//value
//transaction_table
//filter_field
$resultid = $_POST['resultid'];
$url = API . "?method=post&mode=gettexttodyanmicsearching";
$response = file_post_contents($url, $_POST, 'update');
$resultres = json_decode($response, 1);
$result = $resultres['result'];

$no_image = "uploads/all/no-image.jpg";
if(count((array)$result) != 0){
    foreach($result as $vencust){
        $str = '';
            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
        </div>
    </div>';
}
echo $str;
?>
