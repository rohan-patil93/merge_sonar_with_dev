<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$data = array();
$data = json_decode(urldecode($_POST['data']),1);

$wh1["where"] = array('company_id'=>$_SESSION['company_id'], 'genc_code'=>'JOBUOM');
$wh1["field"] = "id,genc_code, genc_detail_code, genc_detail_name";
$data1 = urlencode(json_encode($wh1));
$errorurl = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$data1;
$urlcode = callAPI($errorurl);
$gen_codes = json_decode($urlcode,1);
foreach($gen_codes as $gen_code){
    $code[$gen_code['genc_detail_code']] = $gen_code['genc_detail_name'];
}
?>
<div class="modal-header card-header">

    <h5 class="text-white">Average Experience Rates Detailes</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

</div>

<div class="modal-body p-r-50 p-l-50">
    <div class="card mt-2">
      <div class="card-header">
        <h5 class="f-30">Employees</h5>
        <div class="card-header-right">

        </div>
      </div>
    <div class="table-responsive mt-3">
        <table class=" table table-bordered">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Vendor</th>
                <th scope="col">Job Tittle</th>
                <th scope="col"> UOM </th>

                <th scope="col" class="text-right">Mini Exp</th>
                <th scope="col" class="text-right">Max Exp</th>
                <th scope="col" class="text-right">Unit Price</th>
                <th scope="col" class="text-right">Price</th>
            </tr>
            <?php $k=1; if(count((array)$data)>0){ foreach($data as $emp){ ?>
            <tr>
                <td> <?php echo $k; ?></td>
                <td> <?php echo $emp['vendor_name']; ?></td>
                <td> <?php echo $emp['job_title']; ?></td>
                <td> <?php echo $code[$emp['purchase_uom']]; ?> </td>
                <td class="text-right"> <?php echo $emp['min_exp']; ?></td>
                <td class="text-right"> <?php echo $emp['max_exp']; ?></td>
                <td class="text-right"> <?php echo customeummarmonyformate($emp['item_wise_price']); ?> </td>
                <td class="text-right"> <?php echo customeummarmonyformate($emp['item_price']); ?> </td>
            </tr>
            <?php $k++;
}
}else{?>
                <tr>
                    <td colspan="7"> No data Found</td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
