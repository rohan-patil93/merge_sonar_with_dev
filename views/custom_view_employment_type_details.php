<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'emp_id'=>$_GET['emp_id'],'is_active'=>'0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=emp_employment_type_histories&data=".$vdata;
    $ttdat = callAPI($vurl);
    $ttdata = json_decode($ttdat,1);
    $vdata = urlencode(json_encode($vdata1));

    $vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'genc_code'=>'EMPLOYEETYPES','is_active'=>'0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=general_code_details&data=".$vdata;
    $gendat = callAPI($vurl);
    $gendata = json_decode($gendat,1);
    foreach($gendata as $genda){
        $gencode[$genda['genc_detail_code']] = $genda['genc_detail_name'];
    }
    $vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=customers&data=".$vdata;
    $customers_dat = callAPI($vurl);
    $customers_response = json_decode($customers_dat,1);
    $customers_array = array();
    foreach ($customers_response as $row) {
        $customers_array[$row['id']] = $row['first_name'].$row['last_name'];
    }
?>

<div class="second-modal" id="secondModal" style="border: 1px #cccc solid; border-radius: 20px;" >
    <div class="modal-header">
        <h5 class="modal-title">
            Employment Type History
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body" >
        <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Employment Type</th>
                    <th scope="col">Effective From Date</th>
                    <th scope="col">Effective To Date</th>
                    <th scope="col">Customer</th>
                    <th scope="col">Billing Amount</th>
                    <th scope="col">Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($ttdata)){ $sl=1;
                    foreach ($ttdata as  $value) { ?>
                <tr>
                    <td><?php echo $sl; ?></td>
                    <td><?php echo $gencode[$value['employment_type']]; ?></td>
                    <td><?php echo fromDate($value['effective_from_date']); ?></td>
                    <td><?php echo fromDate($value['effective_to_date']); ?></td>
                    <td><?php echo $customers_array[$value['customer_id']]; ?></td>
                    <td><?php echo customeummarmonyformate($value['billing_amount']); ?></td>
                    <td><?php echo $value['remarks']; ?></td>
                </tr>
                <?php $sl++;
}
                    }else{ ?>
                        <tr>
                            <td colspan="7">
                                <div class="card-px text-center py-20 mt-2">
                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Data </p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                </div>
                            </td>
                        </tr>
                <?php }
                ?>
            </tbody>
        </table>
        </div>
    </div>

</div>
