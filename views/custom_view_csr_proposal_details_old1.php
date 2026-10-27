<?php 
if(!isset($_GET['Rid'])){
    include('../includes/session.php');
    include('../dbconfig.php');
    include('../comm.php');
}

if(isset($_POST['lastId'])) {

    $last_rec_id = $_POST['lastId'];
    $table_name = "temp_csr_proposal_data";
    $url = API."?mode=singlerowtabledata&method=get&rid=".$last_rec_id."&table=".$table_name;
    $che = callAPI($url);
    $return = json_decode($che, 1);
    $old_id = $_POST['oldId'];
}else {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name = 'csr_proposal_data';
    $url = API . "?mode=singlerowtabledata&method=get&rid=" . $rid . "&table=" . $table_name;
    $che = callAPI($url);
    $return = json_decode($che, 1);
}
    // print_r($return);
?>
<script type="text/javascript">

    function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
        jQuery.ajax({
            url: "views/custom_insert_dynamic_status_popup.php",
            data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }

    function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Please note the status of the document will change to "+new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function(){
            console.log("dataa aa");
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    console.log(response);
                    var res = JSON.parse(response);
                    //console.log(res);
                    window.location.href = window.location.href;
                    if(res.transaction_update.status=='sucess'){
                       window.location.href = window.location.href;
                       swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                        type: "success",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function () {
                        window.location.href = window.location.href;
                    });
                   }else{
                    swal({
                        title: "Oops...",
                        text: res.transaction_update.message,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                }
            },
            error:function (){}
        });
        }); 
    }
    function PageChange() {

        $('#kt_modal_dynamic_popupxxl').modal('hide');
    };
</script>
<style type="text/css">
    .text_align_right {
        text-align: right;
    }
</style>
<div class="card card-body">
    <div class="p-3">
        <div>
            <?php if(empty($_POST['lastId']) || $_POST['lastId'] == '' ): ?>
                <?php $workflowsteps = getWorkflowStatus($table_name,$return['id'],$return['current_status']); ?>
                <div class="text-left"><?php echo $workflowsteps['breadcrumb']; ?></div>
                <div class="text-right m-r-50">
                    <?php
               // print_r($workflowsteps); 
                    echo $workflowsteps['workflow_btn'];

                    ?>
                </div>
                <?php echo $workflowsteps['current_status_ribbon']; ?>
            <?php endif; ?>
        </div>
        <div class="table-responsive" id="printableArea">
           <table  style="width:100%;border-collapse:collapse;">

               <tr>
                   <td colspan="2" style="font-size:16px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">GENERAL INSTRUCTIONS:</td>
               </tr>
               <?php 
               $url = API . "?mode=getcsrsetting&method=get";
               $response = callAPI($url);
               $response = json_decode($response, 1);
               $description = $response['description'];
               $list_items = preg_split('/\d+\)/', $description, -1, PREG_SPLIT_NO_EMPTY);
               foreach ($list_items as $item) {
                ?>
                <tr>
                   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">- <?php  echo trim($item); ?></td>
               </tr>
           <?php }?>

           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <tr>
               <td width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Date:</td>
               <td width="90%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;"><?php echo fromDate($return['date']) ?? ''; ?></td>
           </tr>
           <tr>
               <td width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">To:</td>
               <td width="90%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;"><?php echo htmlspecialchars($return['institue_name']) ?? ''; ?></td>
           </tr>
            <tr>
               <td width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Start Date:</td>
               <td width="90%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;"><?php echo fromDate($return['project_start_date']) ?? ''; ?></td>
           </tr>
            <tr>
               <td width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">End Date:</td>
               <td width="90%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;"><?php echo fromDate($return['project_end_date']) ?? ''; ?></td>
           </tr>
           <tr>
               <td width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Subject:</td>
               <td width="90%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;"><?php echo htmlspecialchars($return['subject']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">1.0 About the Organisation</td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['about_organisation']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">2.0 Background</td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['background']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">2.1 Thematic Area</td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['thematic_area']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <tr>
               <th style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Thrust Area</th>
               <th style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Schedule VII item</th>
           </tr>

           <tr>
               <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Education</td>
               <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;"> <?php                                                                                                 // Check the value of $return['education']
               echo ($return['education'] ?? null) == 1 ? '✔' : '✘';
           ?>(ii) promotion of education including special education</td>
       </tr>
       <tr>
           <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Environment Sustainability</td>
           <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">
               <?php
                    // Check the value of $return['education']
               echo ($return['env_sustian'] ?? null) == 1 ? '✔' : '✘';
           ?>(iv) Ensuring environmental sustainability</td>
       </tr>
       <tr>
           <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Health/Hygiene</td>
           <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">
               <?php
                    // Check the value of $return['education']
               echo ($return['health'] ?? null) == 1 ? '✔' : '✘';
           ?>(i) promoting healthcare including preventive healthcare and sanitation
       </td>
   </tr>
   <tr>
       <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Skill Development</td>
       <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">
           <?php
                    // Check the value of $return['education']
           echo ($return['skill_development'] ?? null) == 1 ? '✔' : '✘';
       ?>(ii) employment enhancing vocational skills and livelihood enhancement projects
   </td>
</tr>
<tr>
   <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Community Development</td>
   <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">
       <?php
                    // Check the value of $return['education']
       echo ($return['comu_development'] ?? null) == 1 ? '✔' : '✘';
   ?>(x) Rural development projects/ Multiple categories
</td>
</tr>
<tr>
   <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;">Others or More than one of
   the above (Please Specify)</td>
   <td style="font-size:13px;font-family:'Tahoma';text-transform:capitalize;text-align:left;border:1px solid #858585;"><?php echo htmlspecialchars($return['other_thrust_area']) ?? ''; ?>

</td>
</tr>
<tr>
   <th colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">Please specify the SDG/s in addition to the above.</th>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">3.0 Proposal</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['SDGproposal']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">3.1 Methodology</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['methodology']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">3.2 Target Group</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['target_group']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">3.3 Team for the project</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['team_project']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">3.4 Monitoring Mechanism</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['monitoring_mechanism']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">4.1 Outcomes/Result expected</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['outcomes_expected']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">4.2 Execution Plan</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['execution_plan']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">4.3 Impact Assessment</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['impact_assessment']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
</tr>
<tr>
   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">5.1 Financial Implications</td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['financial_implications']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><span style="margin-left:20px;">- Breakup for each line item should be given. Separate annexure to be provided if needed</span></td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><span style="margin-left:20px;">- Any procurement items has to be supported with documents proving competitive rate in the market</span></td>
</tr>
<tr>
   <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><span style="margin-left:20px;">- List down items covered under Administrative expenses.</span></td>
</tr>
<tr>
   <td colspan="2">
       <table style="width:100%;border-collapse:collapse;">
           <tr bgcolor="#eeeeee">
               <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sr. No</th>
               <th width="15%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Details</th>
               <th width="15%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Per unit cost (A)</th>
               <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Total Number of Units (B)</th>
               <th width="15%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Total Budget for 1 month/year (C=AXB)</th>
               <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">No. of months/years</th>
               <th width="15%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Budget for Months/Years (D=CX No. Of Months/Years)</th>
               <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Remarks (Justification of cost element)</th>
           </tr>
           <?php
           if (!empty($return['groups_data'])) {
                            $groupsData = json_decode($return['groups_data'], true); // Decode the JSON data into an array

                            if ($groupsData && is_array($groupsData)) {
                                foreach ($groupsData as $groupId => $groupDetails) {
                                    $details = $groupDetails['details'] ?? [];
                                    $unitCost = $groupDetails['unit_cost'] ?? [];
                                    $numUnits = $groupDetails['num_units'] ?? [];
                                    $budget = $groupDetails['budget'] ?? [];
                                    $num_months = $groupDetails['num_months'] ?? [];
                                    $totalBudget = $groupDetails['total_budget'] ?? [];
                                    $remarks = $groupDetails['remarks'] ?? [];
                                    $subtotal = $groupDetails['subtotal'] ?? [];
                                    $total_subtotal = $groupDetails['total_subtotal'] ?? [];

                                    // Loop through the inner details for each group
                                    foreach ($details as $index => $detail) {
                                        ?>
                                        <tr>
                                           <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($index) ?? '-'; ?>
                                           </th>
                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($detail) ?? '-'; ?>
                                           </td>
                                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($unitCost[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($numUnits[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($budget[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($num_months[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($totalBudget[$index] ?? '-'); ?>
                                           </td>
                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($remarks[$index] ?? '-'); ?>
                                           </td>
                                       </tr>


                                       <?php
                                   }
                                   ?>
                                   <tr bgcolor="#eeeeee">
                                       <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;"></th>
                                       <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Sub Total <?php echo $groupId ?? '-'; ?></th>
                                       <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                                       <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                                       <th class="text_align_right"  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;"><?php echo !empty($subtotal[$groupId])?  customeummarmonyformate($subtotal[$groupId] ) : '0'; ?></th>
                                       <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                                       <th class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;"><?php echo !empty($total_subtotal[$groupId]) ?customeummarmonyformate($total_subtotal[$groupId]) : '0'; ?></th>
                                       <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                                   </tr>
                                   <?php

                               }
                           }
                       }
                       ?>

                       <tr>
                           <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">-</th>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                       </tr>

                       <tr bgcolor="#eeeeee">
                           <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;"></th>
                           <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">GRAND TOTAL</th>
                           <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                           <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                           <th class="text_align_right"  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;"><?php echo customeummarmonyformate($return['budget_grand_total']) ?? ''; ?></th>
                           <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                           <th class="text_align_right"  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;"><?php echo customeummarmonyformate($return['total_budget_grand_total']) ?? ''; ?></th>
                           <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;"></th>
                       </tr>


                   </table>
               </td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">5.2 Tentative payment schedule</td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['payment_schedule']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2">
                   <table style="width:100%;border-collapse:collapse;">
                       <tr bgcolor="#eeeeee">
                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sr. No</th>
                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Description</th>
                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">% of Total Project Budget</th>
                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Amount in Rs</th>
                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Deliverables to be achieved</th>
                       </tr>
                       <?php
                       if (!empty($return['tentative_payment_schedule'])) {
                        $paymentScheduleData = json_decode($return['tentative_payment_schedule'], true);

                            // Extract the individual arrays from $paymentScheduleData
                        $description = $paymentScheduleData['payment'] ?? $paymentScheduleData['description'] ?? [];
                        $percent = $paymentScheduleData['percent'] ?? [];
                        $amount = $paymentScheduleData['amount'] ?? [];
                        $deliverables = $paymentScheduleData['deliverables'] ?? [];
                            // Loop through the array and create table rows
                        foreach ($percent as $payId => $percentValue) {
                            echo '<tr>';
                            echo '<th  style="font-size:13px;font-family:\'Tahoma\';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">' . htmlspecialchars($payId) ?? '-' . '</th>';
                            echo '<td style="font-size:13px;font-family:\'Tahoma\';font-weight:500;text-transform:capitalize;border:1px solid #858585;">' . htmlspecialchars($description[$payId] ?? '-') . '</td>';
                            echo '<td class="text_align_right" style="font-size:13px;font-family:\'Tahoma\';font-weight:500;text-transform:capitalize;border:1px solid #858585;">' . htmlspecialchars($percentValue) ?? '-' . '</td>';
                            echo '<td class="text_align_right" style="font-size:13px;font-family:\'Tahoma\';font-weight:500;text-transform:capitalize;border:1px solid #858585;">' . (!empty($amount[$payId]) ? customeummarmonyformate($amount[$payId]) : '0') . '</td>';
                            echo '<td style="font-size:13px;font-family:\'Tahoma\';font-weight:500;text-transform:capitalize;border:1px solid #858585;">' . htmlspecialchars($deliverables[$payId] ?? '-') . '</td>';
                            echo '</tr>';
                        }
                    }

                    ?>

                    <tr>
                       <th  style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;"></th>
                       <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;text-align:left;">Grand Total</th>
                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">-</td>
                       <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo customeummarmonyformate($return['payment_grand_total']) ?? ''; ?></td>
                       <th class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;text-align:left;"><?php echo htmlspecialchars($return['payment_grand_total_in_words']) ?? ''; ?></th>
                   </tr>
               </table>
           </td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">6.0 Sustainability Plan</td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['sustainability_plan']) ?? ''; ?></td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">7.0 Conclusion</td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;"><?php echo htmlspecialchars($return['conclusion']) ?? ''; ?></td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">8.0 Enclosures</td>
       </tr>
       <tr>
           <td colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
               <p style="margin:5px 0 0 20px;">1. Form A:- CHECKLIST FOR SUBMISSION OF DOCUMENTS
                   <?php echo ($return['form_a'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                   <?php if (!empty($return['form_a_file'])): ?>
                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_a_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                <?php endif; ?>
                </p>
                <p style="margin:5px 0 0 20px;">2. Form B :- CHECKLIST FOR INFRASTRUCTURE PROJECTS
                   <?php echo ($return['form_b'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                   <?php if (!empty($return['form_b_file'])): ?>
                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_b_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                <?php endif; ?>
                </p>
                <p style="margin:5px 0 0 20px;">3. Form C :- CHECKLIST FOR SUBMISSION OF PROPOSAL
                   <?php echo ($return['form_c'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                   <?php if (!empty($return['form_c_file'])): ?>
                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_c_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                <?php endif; ?>
                </p>
                <p style="margin:5px 0 0 20px;">4. Form D :- SUMMARY SHEET FOR THE PROPOSAL
                    <?php echo ($return['form_d'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                    <?php if (!empty($return['form_d_file'])): ?>
                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_d_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                    </p>
                <p style="margin:5px 0 0 20px;">5. Form E :- TRACK RECORD OF PROJECTS
                    <?php echo ($return['form_e'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                    <?php if (!empty($return['form_e_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form_e_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                </p>
                   <!--  -->
               </td>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <tr>
               <th colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">Kindly ensure the Organisation Seal/Stamp & Sign on each page</th>
           </tr>
           <tr>
               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
           </tr>
           <?php if (isset($return['form_a']) && $return['form_a'] == 1) { ?>
               <tr>
                   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Form A</td>
               </tr>
               <tr>
                   <td colspan="2">
                       <table style="width:100%;border-collapse:collapse;">
                           <tr>
                               <td colspan="4" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">CHECKLIST FOR SUBMISSION OF DOCUMENTS</td>
                           </tr>
                           <tr bgcolor="#eeeeee">
                               <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sr. No</th>
                               <th width="30%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Description of documents</th>
                               <th width="30%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Attached (Yes / No)</th>
                               <th width="30%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Remark</th>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">1*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Trust deed</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['trust_deed'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['trust_deed_file'])){ ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['trust_deed_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php } ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['trust_deed_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">2*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Certificate of incorporation/Registration certificate</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['incorporation'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['incorporation_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['incorporation_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['incorporation_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">3*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">CSR 1 Registration certificate</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['csr1'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['csr1_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['csr1_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['csr1_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">4*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Form 12 A</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['form12a'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['form12a_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form12a_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['form12a_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">5*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Form 80 G</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['form80g'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['form80g_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form80g_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['form80g_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">6*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">35 AC registration if any</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['form35ac'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['form35ac_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form35ac_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['form35ac_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">7*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">PAN card</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['pan'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['pan_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['pan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['pan_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">8*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Last 3 financial years audited statements & ITR proof</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['financial_statements'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['financial_statements_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['financial_statements_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['financial_statements_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">9*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Detailed proposal as per format given</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['proposal'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['proposal_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['proposal_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['proposal_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">10*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">FCRA approval</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['fcra'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['fcra_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['fcra_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['fcra_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">11*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">TDS exemption certificate if applicable</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['tds_exemption'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['tds_exemption_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['tds_exemption_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['tds_exemption_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">12*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">GST Registration certificate if applicable</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['gst'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['gst_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['gst_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['gst_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">13*</th>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">2 Reference letters of past or present supporters for CSR project</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                                   <?php echo ($return['reference'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['reference_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['reference_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                               </td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['reference_remark']) ?? ''; ?></td>
                           </tr>
                       </table>
                   </td>
               </tr>
               <tr>
                   <th colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">*Sr. Nos. are mandatory documents</th>
               </tr>
               <tr>
                   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
               </tr>
               <tr>
                   <th colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;">Additional Information in respect of Audited Accounts as mentioned in Serial No. 7 of Form-A of the Proposal Format for submission of CSR Proposals.</th>
               </tr>
               <tr>
                   <th colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">1. Please give the sources of income for each of the last 3 Financial Years for which audited accounts have been submitted with the application.</th>
               </tr>
               <tr>
                   <th colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;">Rs.</th>
               </tr>
               <tr>
                   <?php
                    $currentYear = date("Y"); // Get the current year
                    $years = [$currentYear - 1, $currentYear - 2, $currentYear - 3]; // Calculate past 3 years
                    ?>
                    <td colspan="2">
                       <table style="width:100%;border-collapse:collapse;">
                           <tr bgcolor="#eeeeee">
                               <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sr. No</th>
                               <th width="30%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sources of Funds</th>

                               <?php foreach ($years as $year): ?>
                                   <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">FY (<?= $year ?> in Rs.)</th>
                               <?php endforeach; ?>
                           </tr>
                           <?php

                            // Example data structure (replace this with $return['source_income_data'])
                           if (!empty($return['source_income_data'])) {
                            $source_income_data = json_decode($return['source_income_data'], true);

                            foreach ($source_income_data as $id => $data) {
                                    // Loop through each item's source and other fields
                                foreach ($data['source'] as $index => $source) {
                                    $fy1 = isset($data['fy1'][$index]) ? $data['fy1'][$index] : '-';
                                    $fy2 = isset($data['fy2'][$index]) ? $data['fy2'][$index] : '-';
                                    $fy3 = isset($data['fy3'][$index]) ? $data['fy3'][$index] : '-';

                                        // Append each row to the tableRows variable
                                    echo  "
                                    <tr>
                                    <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;\">$id</th>
                                    <td style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;\">$source</td>
                                    <td class=\"text_align_right\" style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;\">" . (!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</td>
                                    <td class=\"text_align_right\" style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;\">" . (!empty($fy2) ? customeummarmonyformate($fy2) : '0') . "</td>
                                    <td class=\"text_align_right\" style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;\">" . (!empty($fy3) ? customeummarmonyformate($fy3) : '0') . "</td>
                                    </tr>";
                                }
                            }
                        }
                        ?>

                        <tr>
                           <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;"></th>
                           <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;text-align:right;">Total</th>
                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo (!empty($return['total_fy1']) ? customeummarmonyformate($return['total_fy1']) : '0') ; ?></td>
                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo (!empty($return['total_fy2']) ? customeummarmonyformate($return['total_fy2']) : '0') ; ?></td>
                           <td class="text_align_right" style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo  (!empty($return['total_fy3']) ? customeummarmonyformate($return['total_fy3']) : '0') ; ?></td>
                       </tr>
                   </table>
               </td>
                                       </tr>
                                       <tr>
                                           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                                       </tr>
                                       <tr>
                                           <th colspan="2" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">2. Provide details of income, expenditure, assets and liabilities during the last 3 Financial Years for which audited accounts are being submitted along with the application.</th>
                                       </tr>
                                       <tr>
                                           <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">Total Income:</th>
                                           <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;">Rs.</th>
                                       </tr>
                                       <tr>
                                           <td colspan="2">
                                               <table style="width:100%;border-collapse:collapse;">
                                                   <tr bgcolor="#eeeeee">
                                                       <?php foreach ($years as $year): ?>
                                                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">FY (<?= $year ?> in Rs.)</th>
                                                       <?php endforeach; ?>
                                                       <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                                       <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                                   </tr>
                                                   <?php

                                                   if ($return['income_data']) {
                                                    $income_data = json_decode($return['income_data'], true);

                                                            // Initialize table rows
                                                    $tableRows = '';

                                                    foreach ($income_data as $id => $income) {
                                                                // Values for each column
                                                        $fy1 = isset($income['fy1']) ? $income['fy1'] : '-';
                                                        $fy2 = isset($income['fy2']) ? $income['fy2'] : '-';
                                                        $fy3 = isset($income['fy3']) ? $income['fy3'] : '-';
                                                        $var1 = isset($income['var1']) ? $income['var1'] : '-';
                                                        $var2 = isset($income['var2']) ? $income['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                                        echo  "
                                                        <tr>
                                                        <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" . (!empty($fy1) ? customeummarmonyformate($fy1) : '0')  . "</th>
                                                        <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" . (!empty($fy2) ? customeummarmonyformate($fy2) : '0')  . "</th>
                                                        <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" . (!empty($fy3) ? customeummarmonyformate($fy3) : '0')  . "</th>
                                                        <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var1</th>
                                                        <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var2</th>
                                                        </tr>";
                                                    }
                                                }
                                                ?>

                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                                   </tr>
                                   <tr>
                                       <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">Total Expenditure:</th>
                                       <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;">Rs.</th>
                                   </tr>
                                   <tr>
                                       <td colspan="2">
                                           <table style="width:100%;border-collapse:collapse;">
                                               <tr bgcolor="#eeeeee">
                                                   <?php foreach ($years as $year): ?>
                                                       <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">FY (<?= $year ?> in Rs.)</th>
                                                   <?php endforeach; ?>
                                                   <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                                   <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                               </tr>
                                               <?php
                                               if ($return['expenditure_data']) {
                                                $expenditure_data = json_decode($return['expenditure_data'], true);

                                                            // Initialize table rows
                                                $tableRows = '';

                                                foreach ($expenditure_data as $id => $expenditure) {
                                                                // Values for each column
                                                    $fy1 = isset($expenditure['fy1']) ? $expenditure['fy1'] : '-';
                                                    $fy2 = isset($expenditure['fy2']) ? $expenditure['fy2'] : '-';
                                                    $fy3 = isset($expenditure['fy3']) ? $expenditure['fy3'] : '-';
                                                    $var1 = isset($expenditure['var1']) ? $expenditure['var1'] : '-';
                                                    $var2 = isset($expenditure['var2']) ? $expenditure['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                                    echo  "
                                                    <tr>
                                                    <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</th>
                                                    <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy2) ? customeummarmonyformate($fy2) : '0') . "</th>
                                                    <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy3) ? customeummarmonyformate($fy3) : '0') . "</th>
                                                    <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var1</th>
                                                    <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var2</th>
                                                    </tr>";
                                                }
                                            }
                                            ?>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                               </tr>
                               <tr>
                                   <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">Total Assets:</th>
                                   <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;">Rs.</th>
                               </tr>
                               <tr>
                                   <td colspan="2">
                                       <table style="width:100%;border-collapse:collapse;">
                                           <tr bgcolor="#eeeeee">
                                               <?php foreach ($years as $year): ?>
                                                   <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">FY (<?= $year ?> in Rs.)</th>
                                               <?php endforeach; ?>
                                               <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                               <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                           </tr>
                                           <?php
                                                        // Decode JSON data into a PHP associative array
                                           if ($return['assets_data']) {
                                            $assets_data = json_decode($return['assets_data'], true);

                                                            // Initialize table rows
                                            $tableRows = '';

                                            foreach ($assets_data as $id => $assets) {
                                                                // Values for each column
                                                $fy1 = isset($assets['fy1']) ? $assets['fy1'] : '-';
                                                $fy2 = isset($assets['fy2']) ? $assets['fy2'] : '-';
                                                $fy3 = isset($assets['fy3']) ? $assets['fy3'] : '-';
                                                $var1 = isset($assets['var1']) ? $assets['var1'] : '-';
                                                $var2 = isset($assets['var2']) ? $assets['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                                echo  "
                                                <tr>
                                                <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</th>
                                                <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy2) ? customeummarmonyformate($fy2) : '0') . "</th>
                                                <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy3) ? customeummarmonyformate($fy3) : '0') . "</th>
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var1</th>
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var2</th>
                                                </tr>";
                                            }
                                        }
                                        ?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                            </tr>
                            <tr>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;">Total Liabilities:</th>
                               <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;">Rs.</th>
                            </tr>
                            <tr>
                               <td colspan="2">
                                   <table style="width:100%;border-collapse:collapse;">
                                       <tr bgcolor="#eeeeee">
                                           <?php foreach ($years as $year): ?>
                                               <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">FY (<?= $year ?> in Rs.)</th>
                                           <?php endforeach; ?>
                                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                           <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Major Reasons for variation</th>
                                       </tr>
                                       <?php
                                                        // Decode JSON data into a PHP associative array
                                       if ($return['liabilities_data']) {
                                        $liabilities_data = json_decode($return['liabilities_data'], true);

                                                            // Initialize table rows
                                        $tableRows = '';

                                        foreach ($liabilities_data as $id => $liabilities) {
                                                                // Values for each column
                                            $fy1 = isset($liabilities['fy1']) ? $liabilities['fy1'] : '-';
                                            $fy2 = isset($liabilities['fy2']) ? $liabilities['fy2'] : '-';
                                            $fy3 = isset($liabilities['fy3']) ? $liabilities['fy3'] : '-';
                                            $var1 = isset($liabilities['var1']) ? $liabilities['var1'] : '-';
                                            $var2 = isset($liabilities['var2']) ? $liabilities['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                            echo  "
                                            <tr>
                                            <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</th>
                                            <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy2) ? customeummarmonyformate($fy2) : '0')  . "</th>
                                            <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" .(!empty($fy3) ? customeummarmonyformate($fy3) : '0')  . "</th>
                                            <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var1</th>
                                            <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$var2</th>
                                            </tr>";
                                        }
                                    }
                                    ?>
                                </table>
                            </td>
                            </tr>

                            <tr>
                               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                            </tr>
    <?php } ?>
<?php if (isset($return['form_b']) && $return['form_b'] == 1) { ?>
   <tr>
       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Form B</td>
   </tr>
   <tr>
       <td colspan="2">
           <table style="width:100%;border-collapse:collapse;">
               <tr>
                   <td colspan="4" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">CHECKLIST FOR INFRASTRUCTURE PROJECTS</td>
               </tr>
               <tr bgcolor="#eeeeee">
                   <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sr. No</th>
                   <th width="30%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Items</th>
                   <th width="30%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Attached (Yes / No)</th>
                   <th width="30%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Remark</th>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">A</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;">LAND RELATED</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"></td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">1</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Land Ownership document</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <?php echo ($return['land_ownership'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['land_ownership_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['land_ownership_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['land_ownership_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">2</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Property Card (Commercial/ Vastu/Wetland)</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <?php echo ($return['property_card'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['property_card_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['property_card_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['property_card_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">B</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;border:1px solid #858585;">ESTIMATES & APROVALS</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"></td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">1</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <p style="margin:0;">Drawings- Site Plan, Building Plan, Elevation, foundation / structural details. All drawings needs to be signed & stamped by Licensed Engineer/ Architect & Client.</p>
                       <p style="margin:5px 0 0 0;">License no. / Registration no. of Architect/Engineer needs to be mentioned.</p>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <?php echo ($return['drawings'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['drawings_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['drawings_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">2</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Drawings sanctioned by Govt. Authorities</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <?php echo ($return['govt_drawings'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['govt_drawings_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['govt_drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['govt_drawings_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">3</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <p style="margin:0;">Basis of Estimate -</p>
                       <p style="margin:5px 0 0 0;">For schedule items - Preferably to be made from latest CPWD/ DSR /State Govt published rate along with GST if any.</p>
                       <p style="margin:5px 0 0 0;">Brought out items- Preferably to be made from Quotations of supplier / rate charts of Manufacturers. Estimated needs to be vetted by Govt. Engineer/ Retd. CPWD/ Retd State Govt. Engineer</p>
                       <p style="margin:5px 0 0 0;">Estimate also to be signed & stamped by licensed Engineer & Client</p>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <?php echo ($return['basis_estimate'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['basis_estimate_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['basis_estimate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['basis_estimate_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">4</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Stability certificate needs to be attached issued by licensed engineer in case of expansion of existing building.</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <?php echo ($return['stability_certificate'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['stability_certificate_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['stability_certificate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['stability_certificate_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">C</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">EXECUTION WORKS</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"></td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">1</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Execution Plan (Mention in briefly, separate annexure can be attached)</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <?php echo ($return['execution_works_plan'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['execution_works_plan_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['execution_works_plan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['execution_works_plan_remark']) ?? ''; ?></td>
               </tr>
           </table>
       </td>
   </tr>
   <tr>
       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
   </tr>
<?php } ?>
<?php if (isset($return['form_c']) && $return['form_c'] == 1) { ?>
   <tr>
       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Form C</td>
   </tr>
   <tr>
       <td colspan="2">
           <table style="width:100%;border-collapse:collapse;">
               <tr>
                   <td colspan="4" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">CHECKLIST FOR SUBMISSION OF PROPOSAL</td>
               </tr>
               <tr bgcolor="#eeeeee">
                   <th width="10%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">S.N</th>
                   <th width="45%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Points</th>
                   <th width="45%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Details</th>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">1</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Address of registered office of the Organisation (in India)</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['address_registered_office']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">2</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Any branches of the Organisation? Kindly mention</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['branches_organisation']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">3</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">Registered as a trust/society/ section 8 company or Other</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['registered_as']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">4</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">How long has the Organisation been in existence in years?</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['years_of_existence']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">5</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Which is the Organisation’s geographical area of work?
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['geographical_area_of_work']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">6</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       <p style="margin:0;">What is/are your Organisation's expertise area/s?</p>
                       <p style="margin:5px 0 0 0;">a. Skill Development</p>
                       <p style="margin:5px 0 0 0;">b. Health & Hygiene</p>
                       <p style="margin:5px 0 0 0;">c. Education</p>
                       <p style="margin:5px 0 0 0;">d. Community Development</p>
                       <p style="margin:5px 0 0 0;">e. Environment Sustainability</p>
                       <p style="margin:5px 0 0 0;">f. Other/Multiple (Kindly mention)</p>
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['expertise_areas']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">7</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Names of Board Members/Trustees and CEO.
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['board_members']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">8</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Are any of the trustees/board members affiliated to any political / religious Organization?
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['affiliated_political_religious']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">9</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       What are the sources of funding of the Organisation?
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['sources_of_funding']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">10</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Has the Organisation received any awards/recognition for its work? Kindly mention
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['awards_recognition']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">11</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Does it receive funds / implement programs for the government? If yes-details
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['government_funds_programs']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">12</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Has the Organisation been successful in handing over projects/making projects sustainable? Give example
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['projects_sustainability']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">13</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Has the Organisation been blacklisted by government/PSU or any corporate? If yes, reasons for it
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['blacklisted_details']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">14</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Is the Organisation empanelled by any reputed institute or agency? If yes, give details
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['empanelled_details']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">15</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Is the Organisation associated with any other corporate for implementing CSR projects? Mention names
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['associated_csr_projects']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">16</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Total turnover in the last 3 financial years
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['total_turnover']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">17</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Strength of staff (number of full time employees)
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['staff_strength']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">18</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Any past projects with Infisuite/other PSU’s If yes then details thereof
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['past_projects_bpcl']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">19</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Qualification/experience of staff/project team
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['staff_qualification_experience']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">20</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Please provide two references of past funders of your projects (Email id/ Contact Person with Designation/ Contact Number)
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['references_past_funders']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">21</th>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;">
                       Have you filled all the information in the proposal?
                   </td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;border:1px solid #858585;"><?php echo htmlspecialchars($return['filled_all_information']) ?? ''; ?> </td>
               </tr>
           </table>
       </td>
   </tr>
   <tr>
       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
   </tr>
<?php } ?>
<?php if (isset($return['form_d']) && $return['form_d'] == 1) { ?>
   <tr>
       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Form D</td>
   </tr>
   <tr>
       <td colspan="2">
           <table style="width:100%;border-collapse:collapse;">
               <tr>
                   <td colspan="4" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Summary Sheet for the Proposal</td>
               </tr>
               <tr bgcolor="#eeeeee">
                   <th width="20%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Title of the project</th>
                   <th width="80%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;"><?php echo htmlspecialchars($return['title_of_project']) ?? ''; ?></th>
               </tr>
               <tr bgcolor="#eeeeee">
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">Project Description</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;"><?php echo htmlspecialchars($return['formd_project_description']) ?? ''; ?></td>
               </tr>
               <tr bgcolor="#eeeeee">
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                   Name of Executing Organisation</td>
                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                       <?php echo htmlspecialchars($return['executing_organisation']) ?? ''; ?></td>
                   </tr>
                   <tr bgcolor="#eeeeee">
                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                       Address of Organisation (Registered address & address of branch from which proposal is given)</td>
                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                           <?php echo htmlspecialchars($return['organisation_address']) ?? ''; ?></td>
                       </tr>
                       <tr bgcolor="#eeeeee">
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                           Contact Person</td>
                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                               <?php echo htmlspecialchars($return['contact_person']) ?? ''; ?></td>
                           </tr>
                           <tr bgcolor="#eeeeee">
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                               Contact Number & email address</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                   <?php echo htmlspecialchars($return['contact_details']) ?? ''; ?></td>
                               </tr>
                               <tr bgcolor="#eeeeee">
                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                   Experience of the Organisation</td>
                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;"><?php echo htmlspecialchars($return['organisation_experience']) ?? ''; ?>
                               </td>
                           </tr>
                           <tr bgcolor="#eeeeee">
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                               Place of execution of project</td>
                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                   <?php echo htmlspecialchars($return['execution_place']) ?? ''; ?></td>
                               </tr>
                               <tr bgcolor="#eeeeee">
                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                   Reason for choice of place</td>
                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                       <?php echo htmlspecialchars($return['place_reason']) ?? ''; ?></td>
                                   </tr>
                                   <tr bgcolor="#eeeeee">
                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                       Tenure of the project</td>
                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                           <?php echo htmlspecialchars($return['project_tenure']) ?? ''; ?></td>
                                       </tr>
                                       <tr bgcolor="#eeeeee">
                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                           Cost of the project (enclose a detailed budget)</td>
                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                               <?php echo htmlspecialchars($return['project_cost']) ?? ''; ?></td>
                                           </tr>
                                           <tr bgcolor="#eeeeee">
                                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                               Funds requested from Infisuite</td>
                                               <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                   <?php echo htmlspecialchars($return['funds_infisuite']) ?? ''; ?></td>
                                               </tr>
                                               <tr bgcolor="#eeeeee">
                                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                   Funds requested from any other sources if any</td>
                                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                       <?php echo htmlspecialchars($return['funds_other_sources']) ?? ''; ?></td>
                                                   </tr>
                                                   <tr bgcolor="#eeeeee">
                                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                       Objective of the project</td>
                                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                           <?php echo htmlspecialchars($return['project_objective']) ?? ''; ?></td>
                                                       </tr>
                                                       <tr bgcolor="#eeeeee">
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                           Reason of Organisation preferring Infisuite to be part of this project</td>
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;"><?php echo htmlspecialchars($return['reason_infisuite']) ?? ''; ?>
                                                       </td>
                                                   </tr>
                                                   <tr bgcolor="#eeeeee">
                                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                       Benefits/Outcomes expected from the project</td>
                                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                           <?php echo htmlspecialchars($return['project_benefits']) ?? ''; ?></td>
                                                       </tr>
                                                       <tr bgcolor="#eeeeee">
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                           Quantitative (Mention as per parameter suitable, but make sure to mention number of beneficiaries)</td>
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                               <p style="margin:0;"><?php echo htmlspecialchars($return['quantitative_1']) ?? ''; ?></p>
                                                               <p style="margin:5px 0 0 0;"><?php echo htmlspecialchars($return['quantitative_2']) ?? ''; ?></p>
                                                               <p style="margin:5px 0 0 0;"><?php echo htmlspecialchars($return['quantitative_3']) ?? ''; ?></p>
                                                               <p style="margin:5px 0 0 0;"><?php echo htmlspecialchars($return['quantitative_4']) ?? ''; ?></p>
                                                               <p style="margin:5px 0 0 0;"><?php echo htmlspecialchars($return['quantitative_5']) ?? ''; ?></p>
                                                           </td>
                                                       </tr>
                                                       <tr bgcolor="#eeeeee">
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                           Qualitative (parameters which are difficult to capture in numbers)</td>
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                               <p style="margin:0;"><?php echo htmlspecialchars($return['qualitative_1']) ?? ''; ?></p>
                                                               <p style="margin:5px 0 0 0;"><?php echo htmlspecialchars($return['qualitative_2']) ?? ''; ?></p>
                                                               <p style="margin:5px 0 0 0;"><?php echo htmlspecialchars($return['qualitative_3']) ?? ''; ?></p>
                                                           </td>
                                                       </tr>
                                                       <tr bgcolor="#eeeeee">
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                           Requirement of any approval from local bodies/government/municipality or any statutory body</td>
                                                           <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;"><?php echo htmlspecialchars($return['approval_requirement']) ?? ''; ?>
                                                       </td>
                                                   </tr>
                                                   <tr bgcolor="#eeeeee">
                                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                       Whether similar projects executed earlier?</td>
                                                       <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;"><?php echo htmlspecialchars($return['similar_projects']) ?? ''; ?>
                                                   </td>
                                               </tr>
                                               <tr bgcolor="#eeeeee">
                                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                   In case the proposal is approved, the organisation is okay to open separate bank account for BPCL? (Yes/No)</td>
                                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                       <?php echo htmlspecialchars($return['bank_account_infisuite']) ?? ''; ?>
                                                   </td>
                                               </tr>
                                               <tr bgcolor="#eeeeee">
                                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                   Information given above is true and we can provide the documentary proof of it in case of enquiry</td>
                                                   <td style="font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:left;border:1px solid #858585;">
                                                   <?php echo ($return['information_true'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                                                    <?php if (!empty($return['information_true_file'])): ?>
                                                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['information_true_file']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                                                    <?php endif; ?>
                                               </td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr>
                                   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                               </tr>
                           <?php } ?>
                           <?php if (isset($return['form_e']) && $return['form_e'] == 1) { ?>
                               <tr>
                                   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;">Form E</td>
                               </tr>
                               <tr>
                                   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;text-decoration:underline;">Track Record of Projects</td>
                               </tr>
                               <tr>
                                   <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;">(Kindly mention projects undertaken in the last 3 years. If an organisation is existing for 3 years, track record from inception till date is required)</td>
                               </tr>
                               <tr>
                                   <td colspan="2">
                                       <table style="width:100%;border-collapse:collapse;">
                                           <tr bgcolor="#eeeeee">
                                               <th width="9%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sr.No.</th>
                                               <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Project Description</th>
                                               <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Focus Area</th>
                                               <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Location</th>
                                               <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Period (To - From)</th>
                                               <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Value in INR Lakhs</th>
                                               <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Number of Beneficiaries</th>
                                               <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Partner Name (Funding Agency)</th>
                                           </tr>
                                           <?php

                                           if ($return['project_data']) {
                                // Decode JSON data into a PHP associative array
                                            $project_data = json_decode($return['project_data'], true);

                                            foreach ($project_data as $id => $project) {
                                    // Values for each column
                                                $project_description = isset($project['project_description']) ? $project['project_description'] : '-';
                                                $focus_area = isset($project['focus_area']) ? $project['focus_area'] : '-';
                                                $location = isset($project['location']) ? $project['location'] : '-';
                                                $period = isset($project['period']) ? $project['period'] : '-';
                                                $value = isset($project['value']) ? $project['value'] : '-';
                                                $beneficiaries = isset($project['beneficiaries']) ? $project['beneficiaries'] : '-';
                                                $partner_name = isset($project['partner_name']) ? $project['partner_name'] : '-';

                                    // Append each row to the tableRows variable
                                                echo "
                                                <tr bgcolor=\"#eeeeee\">
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;\">$id</th>
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$project_description</th>
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$focus_area</th>
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$location</th>
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$period</th>
                                                <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" . (!empty($value) ? customeummarmonyformate($value) : '0') . "</th>
                                                <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" . $beneficiaries . "</th>
                                                <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$partner_name</th>
                                                </tr>";
                                            }
                                        }
                                        ?>


                                    </table>
                                </td>
                            </tr>
                            <tr>
                               <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                           </tr>
                       <?php } ?>

                       <tr>
                           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                       </tr>
                       <tr>
                           <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;text-decoration:underline;">Project Tracking</td>
                       </tr>
                       <tr>
                           <td colspan="2">
                               <table style="width:100%;border-collapse:collapse;">
                                   <tr bgcolor="#eeeeee">
                                       <th width="9%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;">Sr.No.</th>
                                       <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Tracking Name</th>
                                       <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Description</th>
                                       <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Start Date</th>
                                       <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">End Date</th>
                                       <th width="13%" style="font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;border:1px solid #858585;">Budget</th>

                                   </tr>
                                   <?php

                                   if ($return['tracking_data']) {
                                // Decode JSON data into a PHP associative array
                                    $tracking_data = json_decode($return['tracking_data'], true);

                                    foreach ($tracking_data as $id => $project) {
                                    // Values for each column
                                        $tracking_name = isset($project['tracking_name']) ? $project['tracking_name'] : '-';
                                        $description = isset($project['description']) ? $project['description'] : '-';
                                        $start_date = isset($project['start_date']) ? $project['start_date'] : '-';
                                        $end_date = isset($project['end_date']) ? $project['end_date'] : '-';
                                        $tracking_budget = isset($project['tracking_budget']) ? $project['tracking_budget'] : '-';

                                    // Append each row to the tableRows variable
                                        echo "
                                        <tr bgcolor=\"#eeeeee\">
                                        <th style=\"font-size:13px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:left;border:1px solid #858585;\">$id</th>
                                        <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$tracking_name</th>
                                        <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">$description</th>
                                        <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">" . fromDate($start_date) . "</th>
                                        <th style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:center;border:1px solid #858585;\">" . fromDate($end_date) . "</th>
                                        <th  style=\"font-size:13px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;text-align:right;border:1px solid #858585;\">" . (!empty($tracking_budget) ? customeummarmonyformate($tracking_budget) : '0') . "</th>

                                        </tr>";
                                    }
                                }
                                ?>


                            </table>
                        </td>
                    </tr>
                    <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                   </tr>

                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;"></td>
                   </tr>
                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:600;text-transform:capitalize;text-align:center;">Declaration</td>
                   </tr>

                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                           Signature: <?php echo htmlspecialchars($return['signature']) ?? ''; ?>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                           Name: <?php echo htmlspecialchars($return['name']) ?? ''; ?>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                           Designation : <?php echo htmlspecialchars($return['designation']) ?? ''; ?>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                           Seal of the Organisation : 
                           <?php if (!empty($return['organisation_seal'])): ?>
                        <a  target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['organisation_seal']); ?>"  class="mb-2" download><i class="ti-cloud-down" style="font-size: 1.5rem; font-weight: bold;"></i></a>
                    <?php endif; ?>
                       </td>
                   </tr>

               </table>
           </div>
           <?php if (!$rid) { ?>
               <table>
                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                           <input type="checkbox" id="declaration-checkbox" onclick="toggleSubmitButton()">
                           I / We hereby declare and certify that all the information given in the application form and in the documents submitted are true and correct to the best of my knowledge.
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                           <input type="checkbox" id="misrepresentation-checkbox" onclick="toggleSubmitButton()">
                           I / We acknowledge that in case of misrepresentation of information or furnishing of false information, my application is liable to be rejected and /or approved project is liable to be terminated by BPCL at any stage.
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" style="font-size:14px;font-family:'Tahoma';font-weight:500;text-transform:capitalize;">
                           <input type="checkbox" id="blacklist-checkbox" onclick="toggleSubmitButton()">
                           I / We further declare and certify that our organisation / company / group of companies has / have not been blacklisted or been put on the holiday list by any of the Public Sector Undertakings in India or Statutory authority or Government.
                       </td>
                   </tr>
               </table>
               <div class="row">
               
               <p style="color: red; font-size: 14px; font-family: 'Tahoma'; font-weight: 500; margin-top: 10px;">
               Note: Please ensure all the above declarations are checked before submitting the application.
                </p>
               </div>
               <div class="row">
                   <div class="col-lg-6 mt-5">
                       <button type="button" class="btn btn-primary waves-effect waves-light m-r-30 mt-3" onclick="PageChange()">Edit</button>
                   </div>
                   <div class="col-lg-6 mt-5">
                       <button id="submit-button" class="btn btn-primary mt-3 float-end" type="button" onclick="submitFormData(<?php echo $last_rec_id ?>)" disabled>Submit</button>
                   </div>
               </div>
           <?php } ?>
       </div></div>
       <script>
           function toggleSubmitButton() {
         // Get all checkboxes
               const checkbox1 = document.getElementById('declaration-checkbox');
               const checkbox2 = document.getElementById('misrepresentation-checkbox');
               const checkbox3 = document.getElementById('blacklist-checkbox');

         // Get the submit button
               const submitButton = document.getElementById('submit-button');

         // Enable the button if all checkboxes are checked
               if (checkbox1.checked && checkbox2.checked && checkbox3.checked) {
                   submitButton.disabled = false;
               } else {
                   submitButton.disabled = true;
               }
           }
       </script>
       <script type="text/javascript">
           function submitFormData(lastRecId) {
               console.log('lastRecId: ', lastRecId)
               console.log('lastRecId: ', <?php echo $old_id ?? '';  ?>)
               $.ajax({
                   url: "ajax.php",
                   data: {
                       page: 'insertData',
                       rid: lastRecId,
                       old_id: <?php echo isset($old_id) ? json_encode($old_id) : 'null'; ?>
                   },
                   type: "POST",
                   success: function(response) {
                       console.log(response);
                       const data = JSON.parse(JSON.parse(response));
                       if (data['status'] === 'sucess') {
                     /*Swal.fire({
                         title: "Data Saved",
                         text: "Do you want to print a PDF?",
                         icon: "success",
                         showCancelButton: true,
                         confirmButtonText: "Print PDF",
                     }).then((result) => {
                        if (result.isConfirmed) {
                            printPDF('CSRViewContainer');

                        }
                        window.location.reload();
                     });*/
                           window.location.href = 'index.php';
                       }
                   },
                   error: function(error) {
                       console.error('Error fetching UOM:', error);
                   }
               });
           }

           function printPDF(divName) {
               var printContents = document.getElementById(divName).innerHTML;
               console.log(printContents)
               var originalContents = document.body.innerHTML;
               document.body.innerHTML = printContents;

               window.print();

               document.body.innerHTML = originalContents;
               window.location.reload();
           }
       </script>