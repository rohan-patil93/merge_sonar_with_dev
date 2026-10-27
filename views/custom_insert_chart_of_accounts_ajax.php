<?php 
if(isset($_POST['id']) && $_POST['id'] !=""){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  $vurl = API."?mode=getchartofaccounts&Rid=".$_POST['id'];
$resultapi = callAPI($vurl);
$result = json_decode($resultapi,1);

$chart_of_accounts =$result['chart_of_accounts'];
$account_type =$result['account_type'];

$chartofaccountssingle =$result['chart_of_accounts_single'];
$taxes =$result['taxes'];
$journals =$result['journals'];
$currencies =$result['currencies'];
$locations =$result['locations'];
$departments =$result['departments'];
$cost_centers =$result['cost_centers'];
//print_r($chart_of_accounts_single);
$chartofaccounts = array();
foreach($chart_of_accounts as $finanacerepogrp){
    $financereportgroupsreport[$finanacerepogrp['parent_account_code']][$finanacerepogrp['account_code']] = $finanacerepogrp;
    $financereportgroups[$finanacerepogrp['account_code']] = $finanacerepogrp;
}
 $reportgroupid = ($_POST['pargrp'] !="0" && $_POST['pargrp'] !="")?$_POST['pargrp']:$chartofaccountssingle['parent_account_code'];
 

  // $vurl = API."?mode=getfinanacereportgroups&Rid=".$_POST['id'];
  // $resultapi = callAPI($vurl);
  // $result = json_decode($resultapi,1);

  // $chart_of_accounts =$result['chart_of_accounts'];
  // $finance_report_groups =$result['finance_report_groups'];
  // $chartofaccounts = array();
  // $chartofaccounts = array();
  // foreach($chart_of_accounts as $chartofacc){
  //     $chartofaccounts[$chartofacc['id']] = $chartofacc;
  // }
  // foreach($finance_report_groups as $finanacerepogrp){
  //     $financereportgroups[$finanacerepogrp['id']] = $finanacerepogrp;
  // }
  // $frgres =$result['finance_report_groups_edit'];
  // $reportgroupid = ($_POST['pargrp'] !="0" && $_POST['pargrp'] !="")?$_POST['pargrp']:$frgres['parent_group_id'];
  // $cudata = $result['selectedaccountlist'];  
  // $selectedode = json_decode($frgres['chart_of_account_codes'],1);
}
$account_groups = $result['account_groups'];
 //echo "<pre>"; print_r($selectedode);  echo "</pre>";
//echo "-----------".$reportgroupid;
?>
<?php if($chartofaccountssingle['id']!=""){ ?>
<div >
  <?php 
    if($chartofaccountssingle['lock_and_unlock_status'] =="" || $chartofaccountssingle['lock_and_unlock_status'] =="N"){  ?>
      <span class="pcoded-badge label label-danger  cursor-pointer" onclick="clickChartOfAccountLockAndUnlock('Y','<?php echo $chartofaccountssingle['id']; ?>')"><i class="ti-lock"></i> Click To lock</span>
  <?php }else{  $evendisable = 'event_disabled';
        if($chartofaccountssingle['lock_and_unlock_user_id'] == $_SESSION['userid']){   
   ?>
      <span class="pcoded-badge label label-primary  cursor-pointer" onclick="clickChartOfAccountLockAndUnlock('N','<?php echo $chartofaccountssingle['id']; ?>')"><i class="ti-unlock"></i> Click To Unlock</span>
  <?php }else{ echo "<span class='text-danger'>Chart Of Account is locked. ".$_SESSION['error_message']['9002']."</span>"; } } ?>
</div>
<?php } ?>
<form id="financereportgrpfrom" class="<?php echo $evendisable; ?>"> <!-- style="position: sticky;top: 10px;" -->
  <div class="card p-2">
    <div class="row">
      <div class="form-group col-lg-6">
        <label class="form-label">Account Name <span class="text-danger">*</span></label>
         <span class="d-block error-message text-danger"></span>
        <input type="text"  class="form-control requried_field"  name="account_name" value="<?php echo $chartofaccountssingle['account_name']; ?>" />
      </div>
      <div class="form-group col-lg-6">
        <label class="form-label">Short Name <span class="text-danger">*</span></label>
         <span class="d-block error-message text-danger"></span>
        <input type="text"  class="form-control requried_field"  name="account_short_name" value="<?php echo $chartofaccountssingle['account_short_name']; ?>" />
      </div>
      
      <div class="form-group col-lg-6">
        <label class="form-label">Account Code</label>
        <input type="text"  class="form-control"  name="account_code" value="<?php echo $chartofaccountssingle['account_code']; ?>" readonly>
      </div>
      <div class="form-group col-lg-6">
        <label class="form-label">Display Priority </label>
        <input type="number"  class="form-control "  name="display_priority" value="<?php echo $chartofaccountssingle['display_priority']; ?>" />
      </div>
      <div class="form-group col-lg-6">
        <label class="form-label">Parent Account Code</label>
 
        <select class="form-control border js-example-basic-single" name="parent_account_code" style="-webkit-appearance:auto;" >
            <option  value="0">Parent Account</option>
          <?php 
          foreach($chart_of_accounts as $chartofaccount){ ?>
           <option <?php if($chartofaccount['account_code']== $reportgroupid){ echo "selected"; } ?> value="<?php echo $chartofaccount['account_code']; ?>"><?php echo $chartofaccount['account_name'].'-'.$chartofaccount['account_code']; ?></option>
         <?php } ?>
       </select>
     </div>
      <div class="form-group col-lg-6">
        <label class="form-label">Account Currency</label>
        <select class="form-control border js-example-basic-single" name="currency_id" style="-webkit-appearance:auto;" >
          <option  value="">Select Any One</option>
          <?php 
          foreach($currencies as $currencie){ ?>

           <option <?php if($currencie['id']== $chartofaccountssingle['currency_id']){ echo "selected"; } ?> value="<?php echo $currencie['id']; ?>"><?php echo $currencie['currency_name']." - ".$currencie['currency_symbol']; ?></option>
         <?php } ?>
       </select>
     </div>
     <div class="form-group col-lg-6 <?php echo ($reportgroupid !="" AND $reportgroupid !="0")?"event_disabled":""; ?>">
        <label class="form-label">Account Groups <span class="text-danger">*</span></label>
        <span class="d-block error-message text-danger"></span>
        <select class="form-control border js-example-basic-single requried_field" name="account_groups"
          style="-webkit-appearance:auto;">
          <option value="">Select Any One</option>
          <?php
          foreach ($account_groups as $accountgroups) { ?>

            <option <?php if ($accountgroups['id'] == $chartofaccountssingle['accgro_group_id']) {
              echo "selected";
            } ?>
              value="<?php echo $accountgroups['id']; ?>"><?php echo $accountgroups['group_name']; ?></option>
          <?php } ?>
        </select>
      </div>
     <div class="form-group col-lg-6 <?php echo ($reportgroupid !="" AND $reportgroupid !="0")?"event_disabled":""; ?>">
        <label class="form-label">Account Type <span class="text-danger">*</span></label>
        <span class="d-block error-message text-danger"></span>
        <select class="form-control border js-example-basic-single requried_field" name="account_type" style="-webkit-appearance:auto;" >
          <option  value="">Select Any One</option>
          <?php 
          foreach($account_type as $accounttype){ ?>

           <option <?php if($accounttype['id']==$chartofaccountssingle['account_type']){ echo "selected"; } ?> value="<?php echo $accounttype['id']; ?>"><?php echo $accounttype['account_type_name']; ?></option>
         <?php } ?>
       </select>
     </div>
     <?php /** ?>
     <div class="form-group col-lg-6">
        <label class="form-label">Default Tax</label>
        <select class="form-control border js-example-basic-single" name="tax_id" style="-webkit-appearance:auto;" >
          <option  value="">Select Any One</option>
          <?php 
          foreach($taxes as $tax){ ?>

           <option <?php if($tax['id']== $chartofaccountssingle['tax_id']){ echo "selected"; } ?> value="<?php echo $tax['id']; ?>"><?php echo $tax['tax_name']; ?></option>
         <?php } ?>
       </select>
     </div>
     <?php **/ ?>
     
     <?php /** ?>
     <div class="form-group col-lg-6">
        <label class="form-label">Allowed Journals</label>
        <select class="form-control border js-example-basic-single" name="journal_id" style="-webkit-appearance:auto;" >
          <option  value="">Select Any One</option>
          <?php 
          foreach($journals as $journal){ ?>

           <option <?php if($journal['id']== $chartofaccountssingle['journal_id']){ echo "selected"; } ?> value="<?php echo $journal['id']; ?>"><?php echo $journal['journal_name']; ?></option>
         <?php } ?>
       </select>
     </div>
     <?php **/ ?>
     <div class="form-group col-lg-6">
        <label class="form-label d-block">Allow Reconciliation</label>
        <input class="js-primary-sm" type="checkbox" name="reconciliation" <?php echo ($chartofaccountssingle['reconciliation']=='0')?"checked":"";?> value="0" >
     </div>

     <div class="form-group col-lg-6">
        <label class="form-label d-block">Allow Posting</label>
        <input class="js-primary-sm" type="checkbox" name="allow_posting" <?php echo ($chartofaccountssingle['allow_posting']=='0')?"checked":"";?> value="0" >
     </div>
     <div class="form-group col-lg-6">
        <label class="form-label d-block">Allow Manual Entries</label>
        <input class="js-primary-sm" type="checkbox" name="allow_manual_entries" <?php echo ($chartofaccountssingle['allow_manual_entries']=='0')?"checked":"";?> value="0" >
     </div>
     <?php if($chartofaccountssingle['id']==""){ ?>
     <div class="form-group col-lg-6">
       <!--  <label class="form-label">Opening Balance <span class="text-danger">*</span></label> -->
         <span class="d-block error-message text-danger"></span>
        <input type="number"  class="form-control text-right requried_field"  id="opening_balance" value="0" readonly="" />
      </div>
      <div class="form-group col-lg-6" id="custom-popup">
        <label class="form-label">&nbsp;</label>
        <button type="button" class="btn bg-primary p-2" onclick="openAccountBalances()"> Add Balances</button>
        <div class="custom-popup-open account_balances_module text-start" style="display: none;left: 0px; width :650px">
            <div class="card">
              <div class="card-header">
                <h5>Balances</h5>
                <div class="card-header-right">
                  <i class="ti-close" onclick="closeAccountBalances()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed"></i>
                </div>
              </div>
              <div class="card-body"> 
                <dic class="row">
                <div class="form-group col-lg-3">
                  <label class="form-label">Location</label>
                  <select class="form-select js-example-basic-single" id="location_filter" onchange="getfilterSearch()">
                    <option value="0">Select Any One</option>
                    <?php foreach((array)$locations as $applicationquery){ ?>
                      <option   value="<?php echo $applicationquery['id']; ?>"><?php echo $applicationquery['location_name'];  ?></option>
                    <?php } ?>
                  </select>
                </div> 


                <div class="form-group  col-lg-3">
                  <label class="form-label">Department</label>
                  <select class="form-select js-example-basic-single" id="department_filter" onchange="getfilterSearch()">
                    <option value="0">Select Any One</option>
                    <?php foreach((array)$departments as $applicationquery){ ?>
                      <option   value="<?php echo $applicationquery['id']; ?>"><?php echo $applicationquery['department_name'];  ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="form-group  col-lg-3">
                  <label class="form-label">Cost Centers</label>
                  <select class="form-select js-example-basic-single" id="cost_center_filter" onchange="getfilterSearch()">
                    <option value="0">Select Any One</option>
                    <?php foreach((array)$cost_centers as $applicationquery){ ?>
                      <option   value="<?php echo $applicationquery['id']; ?>"><?php echo $applicationquery['cost_center_name'];  ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div class="col-lg-3 text-center">  
                  <label class="form-label d-block">Balance</label>
                  <label class="form-label d-block" id="balance_cnt_label">Total : 0</label>
                </div>
              </dic>
             <div class="custom-scroll" style="max-height: 400px;">
              <?php
                $balanceaccountarray = array();
                foreach($locations as $loc){
                    foreach($departments as $dep){
                        foreach($cost_centers as $cc){
                          ?>
                           <div class="row hideallbalancematch matchingid_loc_<?php echo $loc['id']; ?> matchingid_dep_<?php echo $dep['id']; ?> matchingid_cost_<?php echo $cc['id']; ?>" id="">
                              <div class="form-group  col-lg-3">
                                <label class="form-label"><?php echo $loc['location_name']; ?></label>
                              </div>
                              <div class="form-group  col-lg-3">
                                <label class="form-label"><?php echo $dep['department_name']; ?></label>
                              </div>
                              <div class="form-group  col-lg-3">
                                <label class="form-label"><?php echo $cc['cost_center_name']; ?></label>
                              </div>
                              <div class="form-group  col-lg-3">
                                 <input type="number"  class="form-control balance_cnt_text"  name="opening_balance[<?php echo $loc['id']; ?>][<?php echo $dep['id']; ?>][<?php echo $cc['id']; ?>]" value="0" onblur="updatebalanceamount()" />
                              </div>
                            </div>
                            <?php
                        }
                    }
                }
                ?>
              </div>
              </div>
              <div class="card-footer pt-0">
                
              </div>
            </div>
        </div>

      </div>


    <?php } ?>

  <div class="col-lg-12 form-group">
    <input type="hidden" name="Rid" id="Rid" value="<?php echo $chartofaccountssingle['id']; ?>">
    <button type="button" class="btn btn-primary mt-2" onclick="addFinaanceAccGrp()" >  <?php if($chartofaccountssingle['id'] !="" AND $chartofaccountssingle['id'] !="0"){ echo "Update Chart Of Accounts"; }else{ echo "<i class='fa fa-plus'></i> Chart Of Accounts"; } ?></button></div>
  </div>
</div> 
</form>