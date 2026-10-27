<?php
$_SESSION['apply_js']['Switch'] = '1';
if(isset($_POST['id']) && $_POST['id'] !=""){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  $fr_id = $_POST['fr_id'];
  $_POST['footer_total'] = ($_POST['footer_total']=='0')?"0":"1";
  $vurl = API."?mode=getfinanacereportgroups&Rid=".$_POST['id']."&fr_id=".$fr_id;
  $resultapi = callAPI($vurl);
  $result = json_decode($resultapi,1);
  $chart_of_accounts =$result['chart_of_accounts'];
  $finance_report_groups =$result['finance_report_groups'];
  $chartofaccounts = array();
  foreach($chart_of_accounts as $chartofacc){
      $chartofaccounts[$chartofacc['id']] = $chartofacc;
  }
  foreach($finance_report_groups as $finanacerepogrp){
      $financereportgroups[$finanacerepogrp['id']] = $finanacerepogrp;
  }
  $frgres =$result['finance_report_groups_edit'];
  $reportgroupid = ($_POST['pargrp'] !="0" && $_POST['pargrp'] !="")?$_POST['pargrp']:$frgres['parent_group_id'];
  $cudata = $result['selectedaccountlist'];
  $selectedode = json_decode($frgres['chart_of_account_codes'],1);
}
?>
<form id="financereportgrpfrom" style="position: sticky;top: 0px; z-index:9">
  <div class="card">
    <div class="card-body row p-3">
      <div class="form-group col-lg-12">
        <span class="form-label">Group Name<span class="text-danger">*</span></span>
        <input aria-label="Group Name" type="text"  class="form-control text-capitalize"  name="group_name" value="<?php echo $frgres['group_name']; ?>" />
      </div>
      <div class="form-group col-lg-6">
        <span class="form-label">Short Code<span class="text-danger">*</span></span>
        <input aria-label="Short Code" type="text"  class="form-control text-capitalize"  name="short_code" value="<?php echo $frgres['short_code']; ?>" />
      </div>
      <div class="form-group col-lg-6">
        <span class="form-label">Display Priority<span class="text-danger">*</span></span>
        <input aria-label="Display Priority" type="number"  class="form-control"  name="display_priority" value="<?php echo $frgres['display_priority']; ?>" />
      </div>

      <div class="form-group">
        <span class="form-label">Parent Group</span>

        <select aria-label="Parent Group" class="form-control js-example-basic-single" name="parent_group_id" style="-webkit-appearance:auto;" >
          <option  value="0">Select Any One</option>
          <?php
          foreach($financereportgroups as $finrepgrpkey=>$financereportgroup){ ?>

           <option <?php if($finrepgrpkey== $reportgroupid){ echo "selected";
} ?> value="<?php echo $finrepgrpkey; ?>"><?php echo $financereportgroup['group_name']; ?></option>
         <?php } ?>
       </select>
     </div>
     <div class="form-group row" id="custom-popup">
      <span class="form-label">Chart Of Account</span>
      <div class=" col-lg-11">
      <select aria-label="Chart Of Account Codes[]" class="form-control js-example-basic-multiple" name="chart_of_account_codes[]" id="chart_of_account_codes" onchange="selectChartOfAccont()" style="-webkit-appearance:auto;" multiple="">
        <?php

        foreach($chartofaccounts as $coakey=>$chartofaccount){ ?>

         <option <?php if(in_array($chartofaccount['account_code'],(array)$selectedode)){ echo "selected";
} ?> value="<?php echo $chartofaccount['account_code']; ?>"><?php echo $chartofaccount['account_name']; ?></option>
       <?php } ?>
     </select>
   </div>
      <div class="col-lg-1 p-0"><button type="button" class="btn bg-primary p-2" onclick="showChartAccount()"><i class="ti-briefcase text-white m-l-5"></i></button></div>
      <div class="custom-popup-open custom-popup-slide ">
      <div class="card custom-scroll" style="width: 130%; height: 350px;">
          <div class="card-header">
            <h5>Chart Of Account</h5>
            <div class="card-header-right">
              <i class="ti-close" onclick="closedPopup('checklistuser-<?php echo $compliancelist['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
            </div>
          </div>
          <div class="card-body">
            <div class="row" id="chartofaccountmsg">
             <?php
              if(count((array)$cudata['chart_of_accounts_parent']) !=0){
                $chilaccount = $cudata['chart_of_accounts_child'];
                $alreadyused = $cudata['chart_of_accounts_already_used'];
                $groupaccountlist = $cudata['finance_report_chart_of_account'];
                $list = "";
                $checkall = "";
                foreach($cudata['chart_of_accounts_parent'] as $cudatas){
                    $alreadwork = "";
                    if(isset($alreadyused[$cudatas['account_code']])){
                      $alreadwork = '<i class="ti-alert float-end  text-danger"></i>';
                    }
                    $singlecheck = (isset($groupaccountlist[$cudatas['account_code']]))?"checked":"";
                    $list .='<div class="col-xl-12 mt-2"><h4 class="sub-title"><div class="checkbox-fade fade-in-primary"><label class="form-label" style="pointer-events: none;"><input aria-label="Chart Of Account Code[]" type="checkbox" name="chart_of_account_code[]" value="'.$cudatas['account_code'].'" '.$checkall.' '.$singlecheck.' readonly><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>'.$cudatas['account_name'].'</label></div>'.$alreadwork.'</h4><ul>';
                    foreach($chilaccount[$cudatas['account_code']] as $chilaccounts){
                      $alreadwork = "";
                      if(isset($alreadyused[$chilaccounts['account_code']])){
                          $alreadwork = '<i class="ti-alert float-end  text-danger"></i>';
                      }
                      $singlecheck = (isset($groupaccountlist[$chilaccounts['account_code']]))?"checked":"";
                      $list .='<li><div class="checkbox-fade fade-in-primary m-l-20"><label class="form-label"><input aria-label="Chart Of Account Code[]" type="checkbox" value="'.$chilaccounts['account_code'].'" name="chart_of_account_code[]" '.$checkall.' '.$singlecheck.'><span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>'.$chilaccounts['account_name'].'</label></div>'.$alreadwork.'</li>';
                    }
                    $list .= '</ul></div>';
                }  echo $list;
}else{ ?>
                <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Please Select Chart Of Account </p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 150px!important;">
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
          </div>
      </div>
   </div>

   <div class="form-group ">
    <span class="form-label">Calculation Formula</span>
    <textarea aria-label="Calculation Formula" class="form-control" name="calculation_formula"><?php echo $frgres['calculation_formula']; ?></textarea>
  </div>

   <div class="form-group col-lg-6">
    <span class="form-label d-block">Show Group Total</span>
    <input aria-label="Footer Total" type="checkbox" class="js-switch" name="footer_total" value='0' <?php echo $frgres['footer_total']=="0"?"checked":""; ?>>
  </div>
  <div class="col-lg-6 form-group">
    <input type="hidden" name="Rid" id="Rid" value="<?php echo $frgres['id']; ?>">
    <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mt-2" onclick="addFinaanceAccGrp()" >
   <?php if($frgres['id'] !="" && $frgres['id'] !="0"){ echo "Update Group";
}else{ echo "<i class='fa fa-plus'></i> Add Group";
} ?>

  </button></div>
  </div>
</div>
</form>
