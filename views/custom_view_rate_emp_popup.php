<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$crurl = API."?mode=togetcarddetailsemp&trn=".$_POST['trn']."&job_id=".$_POST['job_id']."&min=".$_POST['min']."&max=".$_POST['max']."&vendor_id=".$_POST['vendor_id']."&price=".$_POST['price'];
$cr_data = callAPI($crurl);
$listdata = json_decode($cr_data,1);
$main = array('transaction_number'=>'Vendor Rate Card No','vendor_name'=>'Vendor','effective_from_date'=>'Effective From', 'effective_to_date'=>'Effective To',  'job_title'=>'Job Title', 'min_exp'=>'Minimum  Experience', 'item_price'=>'Amount', 'max_exp'=>'Maximum  Experience');
$data = $listdata['fulldata']['0'];
if(count((array)$data)==0){
$data = $listdata['subdata'];
}
?>
<div class="modal-header card-header">

    <h5 class="text-white">Vendor rate card Employees</h5>

    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

</div>

<div class="modal-body p-r-50 p-l-50">
    <div class="row col-lg-12">
        <?php foreach($main as $key=>$dd){ ?>
            <div class="col-lg-3 mt-2 f-w-600">
                <?php echo $dd; ?>
            </div>
            <div class="col-lg-3 mt-2  "> :&nbsp;
                <?php
                    if(in_array($key,array('effective_to_date','effective_from_date'))){
                        echo fromDate($data[$key]);
                    }elseif($key=='item_price'){
                        echo customeummarmonyformate($data[$key]);
                    }else{
                        echo $data[$key];
                    }
                  ?>
            </div>
        <?php } ?>
    </div>
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
                <th scope="col">Employee Code</th>
                <th scope="col">Employee Name</th>
                <th scope="col">Effective From</th>
                <th scope="col">Effective To</th>
                <th scope="col">Total Experience</th>
                <th scope="col">Similar Vendor/ Rate</th>
            </tr>
            <?php $k=1; if(count((array)$data)>0){ foreach($listdata['fulldata'] as $emp){ ?>
            <tr>
                <td> <?php echo $k; ?></td>
                <td> <?php echo $emp['emp_code']; ?></td>
                <td> <?php echo $emp['display_name']; ?></td>
                <td> <?php echo fromDate($emp['ewo_effective_from_date']); ?></td>
                <td> <?php echo fromDate($emp['ewo_effective_to_date']); ?></td>
                <td> <?php if($emp['career_start_date']!=""){
                    echo dateyearcalucates($emp['career_start_date']);
                    }else{ echo "-";
}  ?></td>
                <td> <?php echo $listdata['rela_data']['0']['vendor_name']." / ".customeummarmonyformate($listdata['rela_data']['0']['item_price']); ?></td>
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
