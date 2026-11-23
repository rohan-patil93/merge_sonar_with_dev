<?php
if($_POST['ajaxsub'] =='Y'){
    $rid=$_POST['subscription_group_id'];
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getsubscriptionusers&rid=".$rid;
    $subscripusers = callAPI($url);
    $subscriptusers = json_decode($subscripusers,1);
    $applicationuser = $subscriptusers['application_user'];
    $company_subscription_plans = $subscriptusers['company_subscription_plans'];
    $subscription_plans = $subscriptusers['subscription_plans'];
    $subscriptiontypeunit = $subscriptusers['subscriptiontypeunit'];
    $module_setup = $subscriptusers['module_setup'];
}
?>
<?php if($tabletype == "client"){ ?>

<table class="table table-hover m-b-0">
    <thead>
        <tr>
            <th scope="col"><?php echo __('Product'); ?></th>
            <th scope="col"><span class="d-block"><?php echo __('Product Code'); ?></span></th>
            <th scope="col"><span class="d-block"><?php echo __('Used/Unused'); ?></span></th>
             <th scope="col"><span class="d-block"><?php echo __('Price'); ?></span></th>
            <th scope="col"><span class="d-block"><?php echo __('Start Date'); ?></span></th>
             <th scope="col"><span class="d-block"><?php echo __('End Date'); ?></span></th>
            <th scope="col"><?php echo __('Action'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $totalstandarduser =$totalactualuser=$totalstandardprice = $totalactualprice= 0;
        foreach($company_subscription_plans as $companysubscriptionplans){
          $subscription_plansdetails =  $subscription_plans[$companysubscriptionplans['subscription_plan_id']];

          $agreed_user_limit = $companysubscriptionplans['agreed_user_limit'] != "0"?$companysubscriptionplans['agreed_user_limit']:$companysubscriptionplans['standard_user_limit'];

          $agreed_price = $companysubscriptionplans['agreed_price'] != "0"?$companysubscriptionplans['agreed_price']:$companysubscriptionplans['standard_price'];
          $totalstandarduser +=$companysubscriptionplans['standard_user_limit'];
          $totalactualuser +=$agreed_user_limit;
          $totalstandardprice +=$companysubscriptionplans['standard_price'];
          $totalactualprice +=$agreed_price;
          ?>
          <tr id="softdeleteid<?php echo $companysubscriptionplans['id']; ?>">
            <td>
                <input type="hidden" name="rowid[]" value="<?php echo $companysubscriptionplans['id']; ?>">
                <input type="hidden" name="planunit[]" value="<?php echo $subscription_plansdetails['renew_unit']; ?>">
                <?php echo "<span class='f-w-700 d-block'>".$module_setup[$companysubscriptionplans['module_id']]."</span>".$subscription_plansdetails['plan_name']; ?>
        </td>
        <td> <div class="badge label-danger ms-2"><?php echo $companysubscriptionplans['license_code']; ?> </div></td>
            <td><b><?php echo $agreed_user_limit; ?></b></td>

            <td><b><?php echo $agreed_price; ?> / <?php echo $subscriptiontypeunit[$companysubscriptionplans['lot_type']];?></b></td>
               <td><b><?php echo $companysubscriptionplans['start_date']; ?></b></td>
                  <td><b><?php echo $companysubscriptionplans['end_date']; ?></b></td>
            <td>
               <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="viewSubscriptonHistory('<?php echo $companysubscriptionplans['id']; ?>')" aria-label="<?php echo __('History'); ?>"><i class="ti-notepad" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo __('History'); ?>"></i></button> &nbsp;&nbsp;
                <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="AddUserPlans('<?php echo $companysubscriptionplans['id']; ?>')" aria-label="<?php echo __('Assign Users Policy'); ?>"><i class="ti-user" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo __('Assign Users Policy'); ?>"></i></button>

            </td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr>
            <td class="f-w-700" colspan="2"><?php echo __('Total'); ?></td>
            <td id="totalactualuser" class="f-w-700"><?php echo $totalactualuser; ?></td>
            <td  class="f-w-700" id="totalactualprice"><?php echo "Rs.".$totalactualprice; ?></td>
        </tr>
    </tfoot>
</tbody>
</table>

<?php }else{ ?>
    <?php if(count((array)$company_subscription_plans) !='0'){ ?>
    <table class="table table-hover m-b-0">
    <thead>
        <tr>
            <th scope="col"><?php echo __('Product'); ?></th>
            <th scope="col"><span class="d-block"><?php echo __('Standard'); ?></span><small><?php echo __('(User Limit)'); ?></small></th>

            <th scope="col"><span class="d-block"><?php echo __('Standard'); ?></span><small><?php echo __('(Amount)'); ?></small></th>
            <th scope="col"><span class="d-block"><?php echo __('Discount'); ?></span></th>
            <th scope="col"><span class="d-block"><?php echo __('Actual'); ?></span><small><?php echo __('(Amount)'); ?></small></th>
            <th scope="col"><?php echo __('Action'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php $totalstandarduser =$totalactualuser=$totalstandardprice = $totalactualprice=$totaldiscountprice= 0;
        foreach($company_subscription_plans as $companysubscriptionplans){
          $subscription_plansdetails =  $subscription_plans[$companysubscriptionplans['subscription_plan_id']];


          $agreed_user_limit = $companysubscriptionplans['agreed_user_limit'] != "0"?$companysubscriptionplans['agreed_user_limit']:$companysubscriptionplans['standard_user_limit'];

          $agreed_price = $companysubscriptionplans['agreed_price'] != "0"?$companysubscriptionplans['agreed_price']:$companysubscriptionplans['standard_price'];
          $totalstandarduser +=$companysubscriptionplans['standard_user_limit'];
          $totalactualuser +=$agreed_user_limit;
          $totalstandardprice +=$companysubscriptionplans['standard_price'];
          $totalactualprice +=$agreed_price;
          $totaldiscountprice += $companysubscriptionplans['discounted_price'];

          ?>
          <tr id="softdeleteid<?php echo $companysubscriptionplans['id']; ?>">
            <td>
                <input type="hidden" name="subscription_type[]"  class="form-control" value="<?php echo $companysubscriptionplans['subscription_type']; ?>" >
                <input type="hidden" name="agreed_users[]"  class="form-control" value="<?php echo $agreed_user_limit; ?>" >
                <input type="hidden" name="rowid[]" value="<?php echo $companysubscriptionplans['id']; ?>">
                <input type="hidden" name="planunit[]" value="<?php echo $companysubscriptionplans['lot_type']; ?>">
                <?php echo "<span class='f-w-700 d-block'>".$module_setup[$companysubscriptionplans['module_id']]."</span>".$subscription_plansdetails['plan_name']; ?>
            </td>
            <td><b><?php echo $companysubscriptionplans['standard_user_limit']; ?></b></td>


            <td><b><?php echo $companysubscriptionplans['standard_price']; ?> / <?php echo $subscriptiontypeunit[$companysubscriptionplans['lot_type']];?></b></td>
            <td>
              <input type="hidden" id="standard_price_<?php echo $companysubscriptionplans['id']; ?>" value="<?php echo $companysubscriptionplans['standard_price']; ?>">
                <input aria-label="<?php echo __('Discount Price[]'); ?>" type="number" name="discount_price[]"  id="discount_price_<?php echo $companysubscriptionplans['id']; ?>" class="form-control discount_prices text-right" value="<?php echo $companysubscriptionplans['discounted_price']; ?>" style="width: 90px;" onblur="chekTotalAmount('<?php echo $companysubscriptionplans['id']; ?>')" <?php echo $companysubscriptionplans['subscription_type']=="N"?"":"readonly"; ?>>
            </td>
            <td>
                <input aria-label="<?php echo __('Agreed Price[]'); ?>" type="number" name="agreed_price[]" id="agreed_price_<?php echo $companysubscriptionplans['id']; ?>" class="form-control agreedprice text-right" value="<?php echo $agreed_price; ?>" style="width: 130px;"  readonly>
            </td>
            <td>
                <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="doftDeleteRow('<?php echo $companysubscriptionplans['id']; ?>')" aria-label="Action"><i class="ti-trash"></i></button>&nbsp;
              <?php if($subscription_plansdetails['additional_license_lot'] !='0' && $subscription_plansdetails['additional_license_lot'] !=""){ ?>
                <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="AdditionalSubscription('<?php echo $companysubscriptionplans['id']; ?>')" aria-label="<?php echo __('Add Additional Subscription'); ?>"><i class="ti-plus" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo __('Add Additional Subscription'); ?>"></i></button>&nbsp;
              <?php } ?>

                <button type="button" class="btn btn-link p-0 border-0 text-reset" onclick="viewSubscriptonHistory('<?php echo $companysubscriptionplans['id']; ?>')" aria-label="<?php echo __('History'); ?>"><i class="ti-notepad" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo __('History'); ?>"></i></button>
            </td>
        </tr>
    <?php } ?>
    <tfoot>
        <tr>
            <td class="f-w-700"><?php echo __('Total'); ?></td>
            <td class="f-w-700"><?php echo $totalstandarduser; ?></td>


            <td class="f-w-700"><?php echo __('Rs.'); ?><?php echo $totalstandardprice; ?></td>

            <td class="f-w-700" id="totaldiscount_prices"><?php echo "Rs.".$totaldiscountprice; ?></td>
            <td  class="f-w-700" id="totalactualprice text-right"><?php echo "Rs.".$totalactualprice; ?></td>
        </tr>
    </tfoot>
</tbody>
</table>
<?php }else{ ?>

<div class="card-px text-center py-20 m-t-30">
    <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> <?php echo __('No Subscription'); ?> </p>
    <div class="text-center px-4">
      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
  </div>

<?php } ?>
<?php } ?>
