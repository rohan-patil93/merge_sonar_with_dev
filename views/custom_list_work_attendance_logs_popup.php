<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$datakey["where"] = array('company_id'=> $_SESSION['company_id'],'month_year'=>$_POST['yearmonth'], 'is_active'=>'0');
$dkey = urlencode(json_encode($datakey));
$url = API."?mode=gettabledetailswithconditions&table=work_attendance_logs&data=".$dkey;
$cudata = callAPI($url);
$result = json_decode($cudata,1);
$typeary = array("D"=>"Download","I"=>"Import","R"=>"review","S"=>"Submit");
?>
<div class="modal-header">
    <h4 class="modal-title"><?= __('Logs'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>

<div class="modal-body">
    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
        <thead>
            <tr><th scope="col"><?= __('User'); ?></th>
                <th scope="col"><?= __('Transaction'); ?></th>
                <th scope="col"><?= __('Date Time'); ?></th>

            </tr>
        </thead>
        <tbody>
            <?php foreach($result as $res){ ?>
            <tr>
                <td class="p-2"><?php echo $_SESSION['users'][$res['user_id']]; ?></td>
                <td class="p-2"><?php echo ucwords($typeary[$res['type']]); ?></td>
                <td class="p-2"><?php echo $res['created_date']; ?></td>

            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()"><?= __('Submit'); ?></button>
</div>
