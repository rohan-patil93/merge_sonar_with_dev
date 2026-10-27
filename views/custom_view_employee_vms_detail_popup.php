<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$crurl = API."?mode=togetempvmsdetails&empid=".$_POST['emp_id'];
$cr_data = callAPI($crurl);
$listdata = json_decode($cr_data,1);
$main = array('emp_code'=>'Employee Code','employee_name'=>'Name','email_id'=>'Email', 'vendor_name'=>'Vendor Name',  'job_title'=>'Job Title', 'department_name'=>'Department', 'location_name'=>'Location', 'vertical_name'=>'Vertical', 'approval_manager'=>'Approval Manager',  'reporting_manager'=>'Reporting Manager', 'hr_buddy'=>'HR Buddy', 'finance_buddy'=>'Finance Buddy', 'admin_buddy'=>'Admin Buddy');
$data = $listdata['emp_detail'];
?>
<div class="modal-header card-header">

    <h5 class="text-white">Employee Details</h5>

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
    <div class="card mt-4">
      <div class="card-header">
        <h5 class="f-30">Work Order Details</h5>
       </div>
        <table class=" table table-bordered">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Vendor Rate Card </br> Transaction No</th>
                <th scope="col"> Purchase Order</br> Transaction No</th>
                <th scope="col">Effective </br> From</th>
                <th scope="col">Effective </br> To</th>
                <th scope="col" >Vendor Rate Card  Amount</th>
                <th scope="col" > Purchase Order Amount</th>
                <th scope="col"></th>
            </tr>
            <?php $k=1;
             if (count((array)$listdata['work_order_detail'])>0) {

             foreach($listdata['work_order_detail'] as $ewo){ ?>
            <tr>
                <td> <?php echo $k; ?></td>
                <td> <?php echo $ewo['vrc_transaction_number']; ?></td>
                <td> <?php echo $ewo['mpc_transaction_number']; ?></td>
                <td> <?php echo fromDate($ewo['effective_from_date']); ?></td>
                <td> <?php echo fromDate($ewo['effective_to_date']); ?></td>
                <td class="text-right"> <?php echo customeummarmonyformateround($ewo['vrc_amount']); ?></td>
                <td class="text-right"> <?php echo customeummarmonyformateround($ewo['mpc_amount']); ?></td>
                <td>
                    <?php if(strtotime($ewo['effective_to_date']) < strtotime(date('Y-m-d'))  ){?>
                        <span class="text-danger f-w-600"> Expired </span>
                    <?php }else{ ?>
                        <span class="text-success f-w-600"> Processing </span>
                     <?php }  ?>

                </td>

            </tr>
            <?php $k++;
}
}else{?>
                <tr>
                    <td colspan="8" class="text-center text-danger f-18 f-w-600"> No Records Found </td>
                </tr>
            <?php } ?>
        </table>

    </div>
    <div class="card mt-2">
      <div class="card-header">
        <h5 class="f-30">LN Details</h5>
       </div>
        <table class=" table table-bordered">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Transaction No</th>
                <th scope="col">Effective From</th>
                <th scope="col">Effective  To</th>
                <th scope="col">Amount</th>
                <th scope="col"></th>
            </tr>
            <?php $k=1;
             if (count((array)$listdata['ln_detail'])>0) {

             foreach($listdata['ln_detail'] as $ln){ ?>
            <tr>
                <td> <?php echo $k; ?></td>
                <td> <?php echo $ewo['transaction_number']; ?></td>

                <td> <?php echo fromDate($ewo['effective_from_date']); ?></td>
                <td> <?php echo fromDate($ewo['effective_to_date']); ?></td>
                <td class="text-right"> <?php echo customeummarmonyformateround($ewo['amount']); ?></td>
                <td>
                    <?php if(strtotime($ewo['effective_to_date']) < strtotime(date('Y-m-d'))  ){?>
                        <span class="text-danger f-w-600"> Expired </span>
                    <?php }else{ ?>
                        <span class="text-success f-w-600"> Processing </span>
                     <?php }  ?>
                </td>
            </tr>
            <?php $k++;
}
}else{?>
                <tr>
                    <td colspan="8" class="text-center text-danger f-18 f-w-600"> No Records Found </td>
                </tr>
            <?php } ?>
        </table>

    </div>
</div>
