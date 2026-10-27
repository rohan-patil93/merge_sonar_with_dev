<?php
if($_POST['ajaxpagecall'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=bugetvendorrareport&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&fromdate=".$_POST['fromdate']."&emp_id=".$_SESSION['emp_id']."&check=1";
    $data_details = callAPI($url);
    $report_data = json_decode($data_details,1);
    $from_cur_year = date('Y', strtotime($_POST['fromdate']));
    $from_cur_month = date('m', strtotime($_POST['fromdate']));

}else{
    $report_data = $emp_count_dashboard['report_data'];
    $from_cur_year = date('Y');
    $from_cur_month = date('m');
}
foreach((array)$report_data as $role){
    $bblist['vendors'][$role['vendor_id']] = $role['vendor_name'];
    $disdatav['vendors'][$role['vendor_id']]['resource'][$role['employee_id']] = $role['employee_id'];
    $disdatav['vendors'][$role['vendor_id']]['budgeted_amount'] = $disdatav['vendors'][$role['vendor_id']]['budgeted_amount']+$role['budgeted_amount'];

    $bblist['location'][$role['location_id']] =$role['location_name'];
    $disdatav['location'][$role['location_id']]['resource'][$role['employee_id']] = $role['employee_id'];
    $disdatav['location'][$role['location_id']]['budgeted_amount'] = $disdatav['location'][$role['location_id']]['budgeted_amount']+$role['budgeted_amount'];
    $role['manager_id'] = $role['reports_to'];
    $bblist['manager'][$role['manager_id']] = $_SESSION['users'][$role['manager_id']];
    $disdatav['manager'][$role['manager_id']]['resource'][$role['employee_id']] = $role['employee_id'];
    $disdatav['manager'][$role['manager_id']]['budgeted_amount'] = $disdatav['manager'][$role['manager_id']]['budgeted_amount']+$role['budgeted_amount'];

}

?>
<script type="text/javascript">
    function selectanyonedate(val){
        console.log("weeeeee cheeeeee okkkkkk");
        $('#replaceiddata').html('<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner2.gif" style="width: 300px;" alt="Preview"></div>');
        jQuery.ajax({
            url: "views/vendorstaq_admin_dashboarad_steps.php",
            data:'ajaxpagecall=Y&fromdate='+val,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#replaceiddata').html(data);

            },
            error:function (){}
        });
    }
</script>
<div class="card table-card">
        <div class="row p-3">
            <div class="col-lg-6"></div>
            <div class="col-lg-6">
                <input aria-label="From Date" class="form-control" type="month" name="from_date" onchange="selectanyonedate(this.value)" value="<?php echo $from_cur_year."-".$from_cur_month?>"  id="att_month_year"  class="" >
            </div>
        </div>
        <div class="card-header">
            <h5>Billing</h5>
            <div class="card-header-right">
                <ul class="nav md-tabs infisuite_tab float-end">
                    <li class="nav-item">
                        <a class="nav-link f-12 f-w-600 text-uppercase active" data-bs-toggle="tab" href="#vendor" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Vendor</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-12 f-w-600 text-uppercase " data-bs-toggle="tab" href="#location" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Location</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link f-12 f-w-600 text-uppercase " data-bs-toggle="tab" href="#manager" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Manager</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content tabs">
            <div class="tab-pane m-r-10 m-l-10 active" id="vendor" role="tabpanel">
                <div class="card-block pb-0">
                    <div class="table-responsive custom-scroll mb-4" style="height: 410px;">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="f-12">Vendor</th>
                                    <th scope="col" class="f-12">Resources#</th>
                                    <th scope="col" class="f-12">Avg Billing</th>
                                    <th scope="col" class="f-12">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count((array)$bblist['vendors'])>0){
                                 foreach($disdatav['vendors'] as $ddkey=>$viewdds){
                                     if($bblist['vendors'][$ddkey]!=""){
                                     $cc = count((array)$viewdds['resource']);
                                     $aveg = $viewdds['budgeted_amount']/$cc;
                                 ?>

                                <tr>
                                    <td class="p-3">
                                        <div class="d-inline-block align-middle">
                                            <h6 class="f-12"><?php echo $bblist['vendors'][$ddkey]; ?></h6>
                                        </div>
                                    </td>
                                    <td class="p-3 text-right f-12"><?php echo $cc; ?></td>
                                    <td class="p-3 text-right f-12"><?php echo customeummarmonyformate($aveg); ?></td>
                                    <td class="p-3 text-c-blue text-right f-12">
                                        <?php echo customeummarmonyformate($viewdds['budgeted_amount']); ?>

                                    </td>
                                </tr>
                                <?php } ?>
                                <?php }
                                }else{ ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center mt-3">
                                                   <h6 class="text-center mt-3">
                                                        <?php if($_POST['ajaxpagecall'] == "Y"){
                                                        echo  "No Data";
                                                    }else{
                                                        echo  "Please Select the Year and Month";
                                                    } ?>
                                                   </h6>
                                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane m-r-10 m-l-10" id="location" role="tabpanel">
                <div class="card-block pb-0">
                    <div class="table-responsive custom-scroll mb-4" style="height: 410px;">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="f-12">Location</th>
                                    <th scope="col" class="text-right f-12">Resources#</th>
                                    <th scope="col" class="text-right f-12">Avg Billing</th>
                                    <th scope="col" class="text-right f-12">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count((array)$bblist['location'])>0){
                                 foreach($disdatav['location'] as $ddkey=>$viewdds){
                                     if($bblist['location'][$ddkey]!=""){
                                     $cc = count((array)$viewdds['resource']);
                                     $aveg = $viewdds['budgeted_amount']/$cc;
                                 ?>

                                <tr>
                                    <td class="p-3">
                                        <div class="d-inline-block align-middle">
                                            <h6 class="f-12"><?php echo $bblist['location'][$ddkey]; ?></h6>
                                        </div>
                                    </td>
                                    <td class="p-3 text-right f-12"><?php echo $cc; ?></td>
                                    <td class="p-3 text-right f-12"><?php echo customeummarmonyformate($aveg); ?></td>
                                    <td class="p-3 text-c-blue text-right f-12">
                                        <?php echo customeummarmonyformate($viewdds['budgeted_amount']); ?>

                                    </td>
                                </tr>
                                <?php } ?>
                                <?php }
                                }else{ ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center mt-3">
                                                   <h6 class="text-center mt-3">
                                                    <?php if($_POST['ajaxpagecall'] == "Y"){
                                                        echo  "No Data";
                                                    }else{
                                                        echo  "Please Select the Year and Month";
                                                    } ?>

                                                   </h6>
                                                   <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="tab-pane m-r-10 m-l-10" id="manager" role="tabpanel">
                <div class="card-block pb-0">
                    <div class="table-responsive custom-scroll mb-4" style="height: 410px;">
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="f-12">Manager</th>
                                    <th scope="col" class="text-right f-12">Resources#</th>
                                    <th scope="col" class="text-right f-12">Avg Billing</th>
                                    <th scope="col" class="text-right f-12">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count((array)$bblist['manager'])>0){
                                 foreach($disdatav['manager'] as $ddkey=>$viewdds){
                                     if($bblist['manager'][$ddkey]!=""){
                                     $cc = count((array)$viewdds['resource']);
                                     $aveg = $viewdds['budgeted_amount']/$cc;
                                 ?>

                                <tr>
                                    <td class="p-3">
                                        <div class="d-inline-block align-middle">
                                            <h6 class="f-12"><?php echo $bblist['manager'][$ddkey]; ?></h6>
                                        </div>
                                    </td>
                                    <td class="p-3 text-right f-12"><?php echo $cc; ?></td>
                                    <td class="p-3 text-right f-12"><?php echo customeummarmonyformate($aveg); ?></td>
                                    <td class="p-3 text-c-blue text-right f-12">
                                        <?php echo customeummarmonyformate($viewdds['budgeted_amount']); ?>

                                    </td>
                                </tr>
                                <?php } ?>
                                <?php }
                                }else{ ?>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center mt-3">
                                                   <h6 class="text-center mt-3">  <?php if($_POST['ajaxpagecall'] == "Y"){
                                                        echo  "No Data";
                                                    }else{
                                                        echo  "Please Select the Year and Month";
                                                    } ?></h6>
                                                   <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

