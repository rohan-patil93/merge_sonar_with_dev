<?php
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $rid=$_POST['company_subscr_id'];
    $url = API."?mode=getsubscriptionusers&transactionhistory=Y&company_subscr_id=".$rid;
    $subscripusers = callAPI($url);
    $subscriptusers = json_decode($subscripusers,1);
     $company_subscription_plan_licenses = $subscriptusers['company_subscription_plan_licenses'];
?>
<div class="modal-header">
    <h5 class="modal-title">Subscription History</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <?php if(count((array)$company_subscription_plan_licenses) !='0'){ ?>
        <table class="table table-hover m-b-0">
    <thead>
        <tr>
            <th scope="col"><span class="d-block">#</span></th>
            <th scope="col"><span class="d-block">Request Date</span></th>
            <th scope="col"><span class="d-block">User</span></th>
            <th scope="col"><span class="d-block">Amount</span></th>
            <th scope="col"><span class="d-block">Payment</span></th>
        </tr>
    </thead>
    <tbody>
        <?php $i=1; foreach($company_subscription_plan_licenses as $subscriptuser){?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo $subscriptuser['request_date']; ?></td>
                <td><?php echo $subscriptuser['added_license_users']!='0'?'<span class="form-label label label-md label-success">'.$subscriptuser['added_license_users'].'</span>':'<span class="form-label label label-md label-danger">'.$subscriptuser['deletd_license_users'].'</span>'; ?></td>
                <td><?php echo $subscriptuser['price']; ?></td>
                <td><?php echo $subscriptuser['payment_type']=="N"?"Pendding":"Done"; ?></td>
            </tr>
        <?php $i++;
} ?>
    </tbody>
</table>
<?php }else{ ?>

<div class="card-px text-center py-20 m-t-30">
    <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No Subscription </p>
    <div class="text-center px-4">
      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
  </div>
<?php } ?>
</div>

