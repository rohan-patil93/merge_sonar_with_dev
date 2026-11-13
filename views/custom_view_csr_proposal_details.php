<?php
if(!isset($_GET['Rid'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}
$return = array();
$csr_type_of_works = array();
$csr_areas = array();
$csr_type_of_projects = array();
if(isset($_POST['lastId'])) {

    $last_rec_id = $_POST['lastId'];
    $table_name = "temp_csr_proposal_data";
    $url = API . "?mode=getCsrViewRelatedData&method=get&rid=" . $last_rec_id . "&table=" . $table_name;
    $che = callAPI($url);
    $data = json_decode($che, 1);
    $return = $data['csr_data'] ?? array();
    $csr_type_of_works = $data['csr_type_of_works'] ?? array();
    $csr_areas = $data['csr_areas'] ?? array();
    $csr_type_of_projects = $data['csr_type_of_projects'] ?? array();
    $locations = $data['locations'] ?? array();
    $old_id = $_POST['oldId'];
}else {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
    $table_name = 'csr_proposal_data';
    $url = API . "?mode=getCsrViewRelatedData&method=get&rid=" . $rid . "&table=" . $table_name;

    $che = callAPI($url);
    $data = json_decode($che, 1);
    $return = $data['csr_data'] ?? array();
    $csr_type_of_works = $data['csr_type_of_works'] ?? array();
    $csr_areas = $data['csr_areas'] ?? array();
    $csr_type_of_projects = $data['csr_type_of_projects'] ?? array();
    $locations = $data['locations'] ?? array();

}
$approvedStatus = defined('ST_APPROVED') ? ST_APPROVED : 199;
$proposalRidParam = !empty($return['id']) ? deocdeId('id:' . $return['id']) : '';
?>

<div class="card card-body">
    <div class="p-3">
        <div>
            <?php if(empty($_POST['lastId']) || $_POST['lastId'] == '' ): ?>
                <?php $workflowsteps = getWorkflowStatus($table_name,$return['id'],$return['current_status']); ?>
                <div class="text-left"><?php echo $workflowsteps['breadcrumb']; ?></div>
                <div class="text-right m-r-50">
                    <?php
                    echo $workflowsteps['workflow_btn'];

                    ?>
                    <?php if (!empty($proposalRidParam) && isset($return['current_status']) && (int)$return['current_status'] == (int)$approvedStatus) { ?>
                        <a class="btn btn-primary waves-effect waves-light" href="index.php?option=COM_VIEW_CSR_TRACKING&Rid=<?php echo $proposalRidParam; ?>">
                            <i class="fa fa-tasks m-r-5"></i> <?= __('Tracking'); ?>
                        </a>
                    <?php } ?>
                </div>
                <?php echo $workflowsteps['current_status_ribbon']; ?>
            <?php endif; ?>
        </div>
        <div class="table-responsive" id="printableArea">

            <link rel="stylesheet" type="text/css" href="assets/css/custom_view_csr_proposal_details.css">

           <table role="presentation" class="w-100 border-collapse">

               <tr>
                   <td colspan="2" class="f-16 f-w-500 text-capitalize"><?= __('GENERAL INSTRUCTIONS'); ?>:</td>
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
                   <td colspan="2" class="f-13 f-w-500 text-capitalize">- <?php  echo trim($item); ?></td>
               </tr>
           <?php }?>

           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Date'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo fromDate($return['date']) ?? ''; ?></td>
           </tr>
           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Financial Year'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo htmlspecialchars($return['financial_year']) ?? ''; ?></td>
           </tr>
           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Entity Name'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo htmlspecialchars($return['institue_name']) ?? ''; ?></td>
           </tr>
            <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Start Date'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo fromDate($return['project_start_date']) ?? ''; ?></td>
           </tr>
            <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('End Date'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo fromDate($return['project_end_date']) ?? ''; ?></td>
           </tr>
           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Type Of Work'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo $csr_type_of_works[$return['type_of_work']]['type_of_work'] ?? ''; ?></td>
           </tr>
           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Type Of Project'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo $csr_type_of_projects[$return['type_of_project']]['genc_detail_name'] ?? ''; ?></td>
           </tr>
           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('CSR Working Area'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo $csr_areas[$return['working_area']]['csr_area'] ?? ''; ?></td>
           </tr>
           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Location'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo $locations[$return['csr_location']]['location_name'] ?? ''; ?></td>
           </tr>

           <tr>
               <td class="f-13 f-w-600 text-capitalize" style="width: 10%;"><?= __('Project Name'); ?>:</td>
               <td class="f-13 f-w-600 text-capitalize text-start" style="width: 90%;"><?php echo htmlspecialchars($return['subject']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('1.0 About the Organisation'); ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['about_organisation']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('2.0 Background'); ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['background']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('2.1 Thematic Area'); ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['thematic_area']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <tr>
               <td class="f-13 text-capitalize text-start border"><?= __('Thrust Area'); ?></td>
               <td class="f-13 text-capitalize text-start border"><?= __('Schedule VII item'); ?></td>
           </tr>

           <tr>
               <td class="f-13 text-capitalize text-start border"><?= __('Education'); ?></td>
               <td class="f-13 text-capitalize text-start border">
               echo ($return['education'] ?? null) == 1 ? '✔' : '✘';
            ?>(ii) promotion of education including special education</td>
            </tr>
       <tr>
           <td class="f-13 text-capitalize text-start border"><?= __('Environment Sustainability'); ?></td>
           <td class="f-13 text-capitalize text-start border">
               <?php
               echo ($return['env_sustian'] ?? null) == 1 ? '✔' : '✘';
           ?><?= __('(iv) Ensuring environmental sustainability'); ?></td>
       </tr>
       <tr>
           <td class="f-13 text-capitalize text-start border"><?= __('Health/Hygiene'); ?></td>
           <td class="f-13 text-capitalize text-start border">
               <?php
               echo ($return['health'] ?? null) == 1 ? '✔' : '✘';
           ?><?= __('(i) promoting healthcare including preventive healthcare and sanitation'); ?>
       </td>
   </tr>
   <tr>
       <td class="f-13 text-capitalize text-start border"><?= __('Skill Development'); ?></td>
       <td class="f-13 text-capitalize text-start border">
           <?php
           echo ($return['skill_development'] ?? null) == 1 ? '✔' : '✘';
       ?><?= __('(ii) employment enhancing vocational skills and livelihood enhancement projects'); ?>
   </td>
</tr>
<tr>
   <td class="f-13 text-capitalize text-start border"><?= __('Community Development'); ?></td>
   <td class="f-13 text-capitalize text-start border">
       <?php
       echo ($return['comu_development'] ?? null) == 1 ? '✔' : '✘';
   ?><?= __('(x) Rural development projects/ Multiple categories'); ?>
</td>
</tr>
<tr>
   <td class="f-13 text-capitalize text-start border"><?= __('Others or More than one of the above (Please Specify)'); ?></td>
   <td class="f-13 text-capitalize text-start border"><?php echo htmlspecialchars($return['other_thrust_area']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-600 text-capitalize text-start"><?= __('Please specify the SDG/s in addition to the above.'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('3.0 Proposal'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['SDGproposal']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('3.1 Methodology'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['methodology']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('3.2 Target Group'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['target_group']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('3.3 Team for the project'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['team_project']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('3.4 Monitoring Mechanism'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['monitoring_mechanism']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('4.1 Outcomes/Result expected'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['outcomes_expected']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('4.2 Execution Plan'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['execution_plan']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('4.3 Impact Assessment'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['impact_assessment']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
</tr>
<tr>
   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('5.1 Financial Implications'); ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['financial_implications']) ?? ''; ?></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><span class="m-l-20">- <?= __('Breakup for each line item should be given. Separate annexure to be provided if needed'); ?></span></td>
</tr>
<tr>
   <td colspan="2" class="f-13 f-w-500 text-capitalize"><span class="m-l-20">- <?= __('Any procurement items has to be supported with documents proving competitive rate in the market'); ?></span></td>
</tr>
<tr>
    <td colspan="2" class="f-13 f-w-500 text-capitalize">
        <span class="m-l-20">- <?= __('List down items covered under Administrative expenses.'); ?></span>
    </td>
</tr>
<tr>
   <td colspan="2">
       <table class="w-100 border-collapse">
           <tr style="background-color: #eeeeee;">
               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 10%;"><?= __('Sr. No'); ?></th>
               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 15%;"><?= __('Details'); ?></th>
               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 15%;"><?= __('Per unit cost (A)'); ?></th>
               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 10%;"><?= __('Total Number of Units (B)'); ?></th>
               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 15%;"><?= __('Total Budget for 1 month/year (C=AXB)'); ?></th>
               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 10%;"><?= __('No. of months/years'); ?></th>
               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 15%;"><?= __('Budget for Months/Years (D=CX No. Of Months/Years)'); ?></th>
               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 10%;"><?= __('Remarks (Justification of cost element)'); ?></th>
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

                                    foreach ($details as $index => $detail) {
                                        ?>
                                        <tr>
                                           <td class="f-13 f-w-600 text-capitalize text-start border">
                                               <?php echo htmlspecialchars($index) ?? '-'; ?>
                                           </td>
                                           <td class="f-13 f-w-500 text-capitalize border">
                                               <?php echo htmlspecialchars($detail) ?? '-'; ?>
                                           </td>
                                           <td class="text_align_right f-13 f-w-500 text-capitalize border">
                                               <?php echo htmlspecialchars($unitCost[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right f-13 f-w-500 text-capitalize border">
                                               <?php echo htmlspecialchars($numUnits[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right f-13 f-w-500 text-capitalize border">
                                               <?php echo htmlspecialchars($budget[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right f-13 f-w-500 text-capitalize border">
                                               <?php echo htmlspecialchars($num_months[$index] ?? '-'); ?>
                                           </td>
                                           <td class="text_align_right f-13 f-w-500 text-capitalize border">
                                               <?php echo htmlspecialchars($totalBudget[$index] ?? '-'); ?>
                                           </td>
                                           <td class="f-13 f-w-500 text-capitalize border">
                                               <?php echo htmlspecialchars($remarks[$index] ?? '-'); ?>
                                           </td>
                                       </tr>

                                       <?php
                                   }
                                   ?>
                                   <tr style="background-color: #eeeeee;">
                                       <td class="f-13 f-w-600 text-capitalize text-start border"></td>
                                       <td class="f-13 f-w-600 text-capitalize text-center border"><?= __('Sub Total'); ?> <?php echo $groupId ?? '-'; ?></td>
                                       <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                                       <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                                       <td class="text_align_right f-13 f-w-600 text-capitalize border"><?php echo !empty($subtotal[$groupId])?  customeummarmonyformate($subtotal[$groupId] ) : '0'; ?></td>
                                       <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                                       <td class="text_align_right f-13 f-w-600 text-capitalize border"><?php echo !empty($total_subtotal[$groupId]) ?customeummarmonyformate($total_subtotal[$groupId]) : '0'; ?></td>
                                       <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                                   </tr>
                                   <?php

                               }
                           }
                       }
                       ?>

                       <tr>
                           <td class="f-13 f-w-600 text-capitalize text-start border">-</td>
                           <td class="f-13 f-w-500 text-capitalize border">-</td>
                           <td class="f-13 f-w-500 text-capitalize border">-</td>
                           <td class="f-13 f-w-500 text-capitalize border">-</td>
                           <td class="f-13 f-w-500 text-capitalize border">-</td>
                           <td class="f-13 f-w-500 text-capitalize border">-</td>
                           <td class="f-13 f-w-500 text-capitalize border">-</td>
                           <td class="f-13 f-w-500 text-capitalize border">-</td>
                       </tr>

                       <tr style="background-color: #eeeeee;">
                           <td class="f-13 f-w-600 text-capitalize text-start border"></td>
                           <td class="f-13 f-w-600 text-capitalize text-center border"><?= __('GRAND TOTAL'); ?></td>
                           <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                           <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                           <td class="text_align_right f-13 f-w-600 text-capitalize border"><?php echo customeummarmonyformate($return['budget_grand_total']) ?? ''; ?></td>
                           <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                           <td class="text_align_right f-13 f-w-600 text-capitalize border"><?php echo customeummarmonyformate($return['total_budget_grand_total']) ?? ''; ?></td>
                           <td class="f-13 f-w-600 text-capitalize text-center border"></td>
                       </tr>

                   </table>
               </td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('5.2 Tentative payment schedule'); ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['payment_schedule']) ?? ''; ?></td>
           </tr>
           <tr>
               <td colspan="2">
                   <table class="w-100 border-collapse">
                       <tr style="background-color: #eeeeee;">
                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Sr. No'); ?></th>
                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Description'); ?></th>
                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('% of Total Project Budget'); ?></th>
                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Amount in Rs'); ?></th>
                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Deliverables to be achieved'); ?></th>
                       </tr>
                       <?php
                       if (!empty($return['tentative_payment_schedule'])) {
                        $paymentScheduleData = json_decode($return['tentative_payment_schedule'], true);

                        $description = $paymentScheduleData['payment'] ?? $paymentScheduleData['description'] ?? [];
                        $percent = $paymentScheduleData['percent'] ?? [];
                        $amount = $paymentScheduleData['amount'] ?? [];
                        $deliverables = $paymentScheduleData['deliverables'] ?? [];
                        foreach ($percent as $payId => $percentValue) {
                            echo '<tr>';
                            echo '<td class="tbl-font tbl-th" >' . htmlspecialchars($payId) ?? '-' . '</td>';
                            echo '<td class="tbl-font tbl-td" >' . htmlspecialchars($description[$payId] ?? '-') . '</td>';
                            echo '<td class="text_align_right tbl-font tbl-td" >' . htmlspecialchars($percentValue) ?? '-' . '</td>';
                            echo '<td class="text_align_right tbl-font tbl-td" >' . (!empty($amount[$payId]) ? customeummarmonyformate($amount[$payId]) : '0') . '</td>';
                            echo '<td class=" tbl-font tbl-td">' . htmlspecialchars($deliverables[$payId] ?? '-') . '</td>';
                            echo '</tr>';
                        }
                    }

                    ?>

                    <tr>
                       <td class="f-13 f-w-600 text-capitalize text-start border"></td>
                       <td class="f-13 f-w-600 text-capitalize border text-start"><?= __('Grand Total'); ?></td>
                       <td class="f-13 f-w-500 text-capitalize border">-</td>
                       <td class="text_align_right f-13 f-w-500 text-capitalize border"><?php echo customeummarmonyformate($return['payment_grand_total']) ?? ''; ?></td>
                       <td class="text_align_right f-13 f-w-600 text-capitalize border text-start"><?php echo htmlspecialchars($return['payment_grand_total_in_words']) ?? ''; ?></td>
                   </tr>
               </table>
           </td>
       </tr>
       <tr>
           <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
       </tr>
       <tr>
           <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('6.0 Sustainability Plan'); ?></td>
       </tr>
       <tr>
           <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['sustainability_plan']) ?? ''; ?></td>
       </tr>
       <tr>
           <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
       </tr>
       <tr>
           <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('7.0 Conclusion'); ?></td>
       </tr>
       <tr>
           <td colspan="2" class="f-13 f-w-500 text-capitalize"><?php echo htmlspecialchars($return['conclusion']) ?? ''; ?></td>
       </tr>
       <tr>
           <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
       </tr>
       <tr>
           <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('8.0 Enclosures'); ?></td>
       </tr>
       <tr>
           <td colspan="2" class="f-13 f-w-500 text-capitalize">
               <p class="m-t-5 m-l-20"><?= __('1. Form A:- CHECKLIST FOR SUBMISSION OF DOCUMENTS'); ?>
                   <?php echo ($return['form_a'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                   <?php if (!empty($return['form_a_file'])): ?>
                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_a_file']); ?>"  class="mb-2" download>
                        <i class="ti-cloud-down f-15rem f-w-600"></i></a>
                <?php endif; ?>
                </p>
                <p class="m-t-5 m-l-20"><?= __('2. Form B :- CHECKLIST FOR INFRASTRUCTURE PROJECTS'); ?>
                   <?php echo ($return['form_b'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                   <?php if (!empty($return['form_b_file'])): ?>
                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_b_file']); ?>"  class="mb-2" download>
                        <i class="ti-cloud-down f-15rem f-w-600"></i></a>
                <?php endif; ?>
                </p>
                <p class="m-t-5 m-l-20"><?= __('3. Form C :- CHECKLIST FOR SUBMISSION OF PROPOSAL'); ?>
                   <?php echo ($return['form_c'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                   <?php if (!empty($return['form_c_file'])): ?>
                    <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_c_file']); ?>"  class="mb-2" download>
                        <i class="ti-cloud-down f-15rem f-w-600"></i></a>
                <?php endif; ?>
                </p>
                <p class="m-t-5 m-l-20"><?= __('4. Form D :- SUMMARY SHEET FOR THE PROPOSAL'); ?>
                    <?php echo ($return['form_d'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                    <?php if (!empty($return['form_d_file'])): ?>
                        <a target="_blank" href="uploads/csr/<?php echo htmlspecialchars($return['form_d_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-600"></i></a>
                    <?php endif; ?>
                    </p>
                <p class="m-t-5 m-l-20"><?= __('5. Form E :- TRACK RECORD OF PROJECTS'); ?>
                    <?php echo ($return['form_e'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                    <?php if (!empty($return['form_e_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form_e_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                </p>

               </td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <tr>
               <td colspan="2" class="f-13 f-w-600 text-capitalize text-center"><?= htmlspecialchars(__('Kindly ensure the Organisation Seal/Stamp & Sign on each page'), ENT_QUOTES, 'UTF-8'); ?></td>
           </tr>
           <tr>
               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
           </tr>
           <?php   if (isset($return['form_a']) && $return['form_a'] == 1) { ?>
               <tr>
                   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('Form A'); ?></td>
               </tr>
               <tr>
                   <td colspan="2">
                       <table class="w-100 border-collapse">
                           <tr>
                               <td colspan="4" class="f-13 f-w-600 text-capitalize text-center border"><?= __('CHECKLIST FOR SUBMISSION OF DOCUMENTS'); ?></td>
                           </tr>
                           <tr style="background-color: #eeeeee;">
                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 10%;"><?= __('Sr. No'); ?></th>
                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 30%;"><?= __('Description of documents'); ?></th>
                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 30%;"><?= __('Attached (Yes / No)'); ?></th>
                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 30%;"><?= __('Remark'); ?></th>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">1*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('Trust deed'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['trust_deed'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['trust_deed_file'])){ ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['trust_deed_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php } ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['trust_deed_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">2*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('Certificate of incorporation/Registration certificate'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['incorporation'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['incorporation_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['incorporation_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['incorporation_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">3*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('CSR 1 Registration certificate'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['csr1'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['csr1_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['csr1_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['csr1_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">4*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('Form 12 A'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['form12a'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['form12a_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form12a_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['form12a_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">5*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('Form 80 G'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['form80g'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['form80g_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form80g_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['form80g_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">6*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('35 AC registration if any'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['form35ac'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['form35ac_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['form35ac_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['form35ac_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">7*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('PAN card'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['pan'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['pan_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['pan_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['pan_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">8*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= htmlspecialchars(__('Last 3 financial years audited statements & ITR proof'), ENT_QUOTES, 'UTF-8'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['financial_statements'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['financial_statements_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['financial_statements_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['financial_statements_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">9*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('Detailed proposal as per format given'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['proposal'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['proposal_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['proposal_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['proposal_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">10</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('FCRA approval'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['fcra'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['fcra_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['fcra_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['fcra_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">11</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('TDS exemption certificate if applicable'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['tds_exemption'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['tds_exemption_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['tds_exemption_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['tds_exemption_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">12</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('GST Registration certificate if applicable'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['gst'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['gst_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['gst_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['gst_remark']) ?? ''; ?></td>
                           </tr>
                           <tr>
                               <td class="f-13 f-w-600 text-capitalize text-start border">13*</td>
                               <td class="f-13 f-w-500 text-capitalize border"><?= __('2 Reference letters of past or present supporters for CSR project'); ?></td>
                               <td class="f-13 f-w-500 text-capitalize border">
                                   <?php echo ($return['reference'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                                   <?php if (!empty($return['reference_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['reference_file']); ?>"  class="mb-2" download>
                            <i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                               </td>
                               <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['reference_remark']) ?? ''; ?></td>
                           </tr>
                       </table>
                   </td>
               </tr>
               <tr>
                   <td colspan="2" class="f-13 f-w-600 text-capitalize text-center">*<?= __('Sr. Nos. are mandatory documents'); ?></td>
               </tr>
               <tr>
                   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
               </tr>
               <tr>
                   <td colspan="2" class="f-13 f-w-600 text-capitalize text-center"><?= __('Additional Information in respect of Audited Accounts as mentioned in Serial No. 8 of Form-A of the Proposal Format for submission of CSR Proposals.'); ?></td>
               </tr>
               <tr>
                   <td colspan="2" class="f-13 f-w-600 text-capitalize text-center"><?= __('1. Please give the sources of income for each of the last 3 Financial Years for which audited accounts have been submitted with the application.'); ?></td>
               </tr>
               <tr>
                   <td colspan="2" class="f-13 f-w-600 text-capitalize text-end"><?= __('Rs.'); ?></td>
               </tr>
               <tr>
                   <?php
                    $currentYear = date("Y"); // Get the current year
                    $years = [$currentYear - 1, $currentYear - 2, $currentYear - 3]; // Calculate past 3 years
                    ?>
                    <td colspan="2">
                       <table class="w-100 border-collapse">
                           <tr style="background-color: #eeeeee;">
                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 10%;"><?= __('Sr. No'); ?></th>
                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 30%;"><?= __('Sources of Funds'); ?></th>

                               <?php foreach ($years as $year): ?>
                                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                               <?php endforeach; ?>
                           </tr>
                           <?php

                           if (!empty($return['source_income_data'])) {
                            $source_income_data = json_decode($return['source_income_data'], true);

                            foreach ($source_income_data as $id => $data) {
                                foreach ($data['source'] as $index => $source) {
                                    $fy1 = isset($data['fy1'][$index]) ? $data['fy1'][$index] : '-';
                                    $fy2 = isset($data['fy2'][$index]) ? $data['fy2'][$index] : '-';
                                    $fy3 = isset($data['fy3'][$index]) ? $data['fy3'][$index] : '-';

                                        // Append each row to the tableRows variable
                                    echo  "
                                    <tr>
                                    <td class='tbl-font tbl-th'>$id</td>
                                    <td class='tbl-font tbl-td'>$source</td>
                                    <td class='text_align_right tbl-font tbl-td' >" . (!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</td>
                                    <td class=\"text_align_right tbl-font tbl-td\" >" . (!empty($fy2) ? customeummarmonyformate($fy2) : '0') . "</td>
                                    <td class=\"text_align_right tbl-font tbl-td\" >" . (!empty($fy3) ? customeummarmonyformate($fy3) : '0') . "</td>
                                    </tr>";
                                }
                            }
                        }
                        ?>

                        <tr>
                           <td class="f-13 f-w-600 text-capitalize text-start border"></td>
                           <td class="f-13 f-w-600 text-capitalize border text-end"><?= __('Total'); ?></td>
                           <td class="text_align_right f-13 f-w-500 text-capitalize border"><?php echo !empty($return['total_fy1']) ? customeummarmonyformate($return['total_fy1']) : '0'; ?></td>
                           <td class="text_align_right f-13 f-w-500 text-capitalize border"><?php echo !empty($return['total_fy2']) ? customeummarmonyformate($return['total_fy2']) : '0'; ?></td>
                           <td class="text_align_right f-13 f-w-500 text-capitalize border"><?php echo !empty($return['total_fy3']) ? customeummarmonyformate($return['total_fy3']) : '0'; ?></td>
                       </tr>
                   </table>
               </td>
                                       </tr>
                                       <tr>
                                           <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                                       </tr>
                                       <tr>
                                           <td colspan="2" class="f-13 f-w-600 text-capitalize text-center"><?= __('2. Provide details of income, expenditure, assets and liabilities during the last 3 Financial Years for which audited accounts are being submitted along with the application.'); ?></td>
                                       </tr>
                                       <tr>
                                           <td class="f-13 f-w-600 text-capitalize text-center"><?= __('Total Income'); ?>:</td>
                                           <td class="f-13 f-w-600 text-capitalize text-end"><?= __('Rs.'); ?></td>
                                       </tr>
                                       <tr>
                                           <td colspan="2">
                                               <table class="w-100 border-collapse">
                                                   <tr style="background-color: #eeeeee;">
                                                       <?php foreach ($years as $year): ?>
                                                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                                       <?php endforeach; ?>
                                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                                   </tr>
                                                   <?php

                                                   if ($return['income_data']) {
                                                    $income_data = json_decode($return['income_data'], true);

                                                            // Initialize table rows
                                                    $tableRows = '';

                                                    foreach ($income_data as $id => $income) {
                                                        $fy1 = isset($income['fy1']) ? $income['fy1'] : '-';
                                                        $fy2 = isset($income['fy2']) ? $income['fy2'] : '-';
                                                        $fy3 = isset($income['fy3']) ? $income['fy3'] : '-';
                                                        $var1 = isset($income['var1']) ? $income['var1'] : '-';
                                                        $var2 = isset($income['var2']) ? $income['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                                        echo  "
                                                        <tr>
                                                        <td class='tbl-font tbl-th'>" . (!empty($fy1) ? customeummarmonyformate($fy1) : '0')  . "</td>
                                                        <td class='tbl-font tbl-th tbl-right'>" . (!empty($fy2) ? customeummarmonyformate($fy2) : '0')  . "</td>
                                                        <td class='tbl-font tbl-th tbl-right'>" . (!empty($fy3) ? customeummarmonyformate($fy3) : '0')  . "</td>
                                                        <td class='tbl-font tbl-th text-center'>$var1</td>
                                                        <td class='tbl-font tbl-th text-center'>$var2</td>
                                                        </tr>";
                                                    }
                                                }
                                                ?>

                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                       <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                                   </tr>
                                   <tr>
                                       <td class="f-13 f-w-600 text-capitalize text-center"><?= __('Total Expenditure'); ?>:</td>
                                       <td class="f-13 f-w-600 text-capitalize text-end"><?= __('Rs.'); ?></td>
                                   </tr>
                                   <tr>
                                       <td colspan="2">
                                           <table class="w-100 border-collapse">
                                               <tr style="background-color: #eeeeee;">
                                                   <?php foreach ($years as $year): ?>
                                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                                   <?php endforeach; ?>
                                                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                               </tr>
                                               <?php
                                               if ($return['expenditure_data']) {
                                                $expenditure_data = json_decode($return['expenditure_data'], true);

                                                            // Initialize table rows
                                                $tableRows = '';

                                                foreach ($expenditure_data as $id => $expenditure) {
                                                    $fy1 = isset($expenditure['fy1']) ? $expenditure['fy1'] : '-';
                                                    $fy2 = isset($expenditure['fy2']) ? $expenditure['fy2'] : '-';
                                                    $fy3 = isset($expenditure['fy3']) ? $expenditure['fy3'] : '-';
                                                    $var1 = isset($expenditure['var1']) ? $expenditure['var1'] : '-';
                                                    $var2 = isset($expenditure['var2']) ? $expenditure['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                                    echo  "
                                                    <tr>
                                                    <td class='tbl-font tbl-th tbl-right'>" .(!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</td>
                                                    <td class='tbl-font tbl-th tbl-right'>" .(!empty($fy2) ? customeummarmonyformate($fy2) : '0') . "</td>
                                                    <td class='tbl-font tbl-th tbl-right'>" .(!empty($fy3) ? customeummarmonyformate($fy3) : '0') . "</td>
                                                    <td class='tbl-font tbl-th text-center'>$var1</td>
                                                    <td class='tbl-font tbl-th text-center'>$var2</td>
                                                    </tr>";
                                                }
                                            }
                                            ?>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                               </tr>
                               <tr>
                                   <td class="f-13 f-w-600 text-capitalize text-center"><?= __('Total Assets'); ?>:</td>
                                   <td class="f-13 f-w-600 text-capitalize text-end"><?= __('Rs.'); ?></td>
                               </tr>
                               <tr>
                                   <td colspan="2">
                                       <table class="w-100 border-collapse">
                                           <tr style="background-color: #eeeeee;">
                                               <?php foreach ($years as $year): ?>
                                                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                               <?php endforeach; ?>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                           </tr>
                                           <?php
                                                        // Decode JSON data into a PHP associative array
                                           if ($return['assets_data']) {
                                            $assets_data = json_decode($return['assets_data'], true);

                                                            // Initialize table rows
                                            $tableRows = '';

                                            foreach ($assets_data as $id => $assets) {
                                                $fy1 = isset($assets['fy1']) ? $assets['fy1'] : '-';
                                                $fy2 = isset($assets['fy2']) ? $assets['fy2'] : '-';
                                                $fy3 = isset($assets['fy3']) ? $assets['fy3'] : '-';
                                                $var1 = isset($assets['var1']) ? $assets['var1'] : '-';
                                                $var2 = isset($assets['var2']) ? $assets['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                                echo  "
                                                <tr>
                                                <td class='tbl-font tbl-th tbl-right'>" .(!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</td>
                                                <td class='tbl-font tbl-th tbl-right'>" .(!empty($fy2) ? customeummarmonyformate($fy2) : '0') . "</td>
                                                <td class='tbl-font tbl-th tbl-right'>" .(!empty($fy3) ? customeummarmonyformate($fy3) : '0') . "</td>
                                                <td class='tbl-font tbl-th text-center'>$var1</td>
                                                <td class='tbl-font tbl-th text-center'>$var2</td>
                                                </tr>";
                                            }
                                        }
                                        ?>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                            </tr>
                            <tr>
                               <td class="f-13 f-w-600 text-capitalize text-center"><?= __('Total Liabilities'); ?>:</td>
                               <td class="f-13 f-w-600 text-capitalize text-end"><?= __('Rs.'); ?></td>
                            </tr>
                            <tr>
                               <td colspan="2">
                                   <table class="w-100 border-collapse">
                                       <tr style="background-color: #eeeeee;">
                                           <?php foreach ($years as $year): ?>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('FY ('); ?><?= $year ?> <?= __('in Rs.)'); ?></th>
                                           <?php endforeach; ?>
                                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                           <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Major Reasons for variation'); ?></th>
                                       </tr>
                                       <?php
                                                        // Decode JSON data into a PHP associative array
                                       if ($return['liabilities_data']) {
                                        $liabilities_data = json_decode($return['liabilities_data'], true);

                                                            // Initialize table rows
                                        $tableRows = '';

                                        foreach ($liabilities_data as $id => $liabilities) {
                                            $fy1 = isset($liabilities['fy1']) ? $liabilities['fy1'] : '-';
                                            $fy2 = isset($liabilities['fy2']) ? $liabilities['fy2'] : '-';
                                            $fy3 = isset($liabilities['fy3']) ? $liabilities['fy3'] : '-';
                                            $var1 = isset($liabilities['var1']) ? $liabilities['var1'] : '-';
                                            $var2 = isset($liabilities['var2']) ? $liabilities['var2'] : '-';

                                                                // Append each row to the tableRows variable
                                            echo  "
                                            <tr>
                                            <th scope='col'  class='tbl-font tbl-th tbl-right'>" .(!empty($fy1) ? customeummarmonyformate($fy1) : '0') . "</th>
                                            <th scope='col'  class='tbl-font tbl-th tbl-right'>" .(!empty($fy2) ? customeummarmonyformate($fy2) : '0')  . "</th>
                                            <th scope='col'  class='tbl-font tbl-th tbl-right'>" .(!empty($fy3) ? customeummarmonyformate($fy3) : '0')  . "</th>
                                            <th scope='col' class='tbl-font tbl-th text-center'>$var1</th>
                                            <th scope='col' class='tbl-font tbl-th text-center'>$var2</th>
                                            </tr>";
                                        }
                                    }
                                    ?>
                                </table>
                            </td>
                            </tr>

                            <tr>
                               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                            </tr>
    <?php } ?>
<?php if (isset($return['form_b']) && $return['form_b'] == 1) { ?>
   <tr>
       <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('Form B'); ?></td>
   </tr>
   <tr>
       <td colspan="2">
           <table class="w-100 border-collapse">
               <tr>
                   <td colspan="4" class="f-13 f-w-600 text-capitalize text-center border"><?= __('CHECKLIST FOR INFRASTRUCTURE PROJECTS'); ?></td>
               </tr>
               <tr style="background-color: #eeeeee;">
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 10%;"><?= __('Sr. No'); ?></th>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 30%;"><?= __('Items'); ?></th>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 30%;"><?= __('Attached (Yes / No)'); ?></th>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 30%;"><?= __('Remark'); ?></th>
               </tr>
               <tr>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border">A</th>
                   <td class="f-13 f-w-600 text-capitalize border"><?= __('LAND RELATED'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border"></td>
                   <td class="f-13 f-w-500 text-capitalize border"></td>
               </tr>
               <tr>
                   <th scope="col" class="f-13 f-w-500 text-capitalize text-start border">1</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Land Ownership document'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?php echo ($return['land_ownership'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['land_ownership_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['land_ownership_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['land_ownership_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-13 f-w-500 text-capitalize text-start border">2</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Property Card (Commercial/ Vastu/Wetland)'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?php echo ($return['property_card'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['property_card_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['property_card_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['property_card_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border">B</th>
                   <td class="f-13 f-w-600 text-capitalize border"><?= htmlspecialchars(__('ESTIMATES & APROVALS'), ENT_QUOTES, 'UTF-8'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border"></td>
                   <td class="f-13 f-w-500 text-capitalize border"></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">1</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <p class="m-0"><?= htmlspecialchars(__('Drawings- Site Plan, Building Plan, Elevation, foundation / structural details. All drawings needs to be signed & stamped by Licensed Engineer/ Architect & Client.'), ENT_QUOTES, 'UTF-8'); ?></p>
                       <p class="m-t-0"><?= __('License no. / Registration no. of Architect/Engineer needs to be mentioned.'); ?></p>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?php echo ($return['drawings'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['drawings_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['drawings_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">2</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Drawings sanctioned by Govt. Authorities'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?php echo ($return['govt_drawings'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['govt_drawings_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['govt_drawings_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['govt_drawings_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">3</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <p class="m-0"><?= __('Basis of Estimate'); ?> -</p>
                       <p class="m-t-0"><?= __('For schedule items - Preferably to be made from latest CPWD/ DSR /State Govt published rate along with GST if any.'); ?></p>
                       <p class="m-t-0"><?= __('Brought out items- Preferably to be made from Quotations of supplier / rate charts of Manufacturers. Estimated needs to be vetted by Govt. Engineer/ Retd. CPWD/ Retd State Govt. Engineer'); ?></p>
                       <p class="m-t-0"><?= htmlspecialchars(__('Estimate also to be signed & stamped by licensed Engineer & Client'), ENT_QUOTES, 'UTF-8'); ?></p>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?php echo ($return['basis_estimate'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['basis_estimate_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['basis_estimate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['basis_estimate_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">4</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Stability certificate needs to be attached issued by licensed engineer in case of expansion of existing building.'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?php echo ($return['stability_certificate'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['stability_certificate_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['stability_certificate_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['stability_certificate_remark']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border">C</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('EXECUTION WORKS'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border"></td>
                   <td class="f-13 f-w-500 text-capitalize border"></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">1</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Execution Plan (Mention in briefly, separate annexure can be attached)'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?php echo ($return['execution_works_plan'] ?? null) == 1 ? 'Yes' : 'No'; ?>
                       <?php if (!empty($return['execution_works_plan_file'])): ?>
                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['execution_works_plan_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['execution_works_plan_remark']) ?? ''; ?></td>
               </tr>
           </table>
       </td>
   </tr>
   <tr>
       <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
   </tr>
<?php } ?>
<?php if (isset($return['form_c']) && $return['form_c'] == 1) { ?>
   <tr>
       <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('Form C'); ?></td>
   </tr>
   <tr>
       <td colspan="2">
           <table class="w-100 border-collapse">
               <tr>
                   <td colspan="4" class="f-13 f-w-600 text-capitalize text-center border"><?= __('CHECKLIST FOR SUBMISSION OF PROPOSAL'); ?></td>
               </tr>
               <tr style="background-color: #eeeeee;">
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 10%;"><?= __('Sr. No'); ?></th>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 45%;"><?= __('Points'); ?></th>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 45%;"><?= __('Details'); ?></th>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">1</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Address of registered office of the Organisation (in India)'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['address_registered_office']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">2</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Any branches of the Organisation? Kindly mention'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['branches_organisation']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">3</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('Registered as a trust/society/ section 8 company or Other'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['registered_as']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">4</th>
                   <td class="f-13 f-w-500 text-capitalize border"><?= __('How long has the Organisation been in existence in years?'); ?></td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['years_of_existence']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">5</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Which is the Organisation’s geographical area of work?'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['geographical_area_of_work']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">6</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <p class="m-0"><?= __('What is/are your Organisation\'s expertise area/s?'); ?></p>
                       <p class="m-t-0"><?= __('a. Skill Development'); ?></p>
                       <p class="m-t-0"><?= htmlspecialchars(__('b. Health & Hygiene'), ENT_QUOTES, 'UTF-8'); ?></p>
                       <p class="m-t-0"><?= __('c. Education'); ?></p>
                       <p class="m-t-0"><?= __('d. Community Development'); ?></p>
                       <p class="m-t-0"><?= __('e. Environment Sustainability'); ?></p>
                       <p class="m-t-0"><?= __('f. Other/Multiple (Kindly mention)'); ?></p>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['expertise_areas']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">7</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Names of Board Members/Trustees and CEO.'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['board_members']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">8</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Are any of the trustees/board members affiliated to any political / religious Organization?'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['affiliated_political_religious']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">9</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('What are the sources of funding of the Organisation?'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['sources_of_funding']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">10</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Has the Organisation received any awards/recognition for its work? Kindly mention'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['awards_recognition']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">11</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Does it receive funds / implement programs for the government? If yes-details'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['government_funds_programs']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">12</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Has the Organisation been successful in handing over projects/making projects sustainable? Give example'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['projects_sustainability']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">13</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Has the Organisation been blacklisted by government/PSU or any corporate? If yes, reasons for it'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['blacklisted_details']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">14</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Is the Organisation empanelled by any reputed institute or agency? If yes, give details'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['empanelled_details']) ?? ''; ?></td>
               </tr>
               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">15</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Is the Organisation associated with any other corporate for implementing CSR projects? Mention names'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['associated_csr_projects']) ?? ''; ?></td>
               </tr>

               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">16</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Strength of staff (number of full time employees)'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['staff_strength']) ?? ''; ?></td>
               </tr>

               <tr>
                   <th scope="col" class="f-3 f-w-500 text-capitalize text-start border">17</th>
                   <td class="f-13 f-w-500 text-capitalize border">
                       <?= __('Please provide two references of past funders of your projects (Email id/ Contact Person with Designation/ Contact Number)'); ?>
                   </td>
                   <td class="f-13 f-w-500 text-capitalize border"><?php echo htmlspecialchars($return['references_past_funders']) ?? ''; ?></td>
               </tr>

           </table>
       </td>
   </tr>
   <tr>
       <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
   </tr>
<?php  }  ?>
<?php if (isset($return['form_d']) && $return['form_d'] == 1) { ?>
   <tr>
       <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('Form D'); ?></td>
   </tr>
   <tr>
       <td colspan="2">
           <table class="w-100 border-collapse">
               <tr>

                   <td colspan="4" class="f-13 f-w-600 text-capitalize text-center border" style="width: 100%;"><?= __('Summary Sheet for the Proposal'); ?></td>
               </tr>
               <tr style="background-color: #eeeeee;">
                <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 10%;"><?= __('Sr. No'); ?></th>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 20%;"><?= __('Title of the project'); ?></th>
                   <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 70%;"><?php echo htmlspecialchars($return['title_of_project']) ?? ''; ?></th>
               </tr>
               <tr style="background-color: #eeeeee;">
                <td class="f-3 f-w-500 text-capitalize text-start border">1</td>
                   <td class="f-3 f-w-500 text-capitalize text-start border"><?= __('Project Description'); ?></td>
                   <td class="f-3 f-w-500 text-capitalize text-start border"><?php echo htmlspecialchars($return['formd_project_description']) ?? ''; ?></td>
               </tr>
               <tr style="background-color: #eeeeee;">
                <td class="f-3 f-w-500 text-capitalize text-start border">2</td>
                   <td class="f-3 f-w-500 text-capitalize text-start border">
                   <?= __('Name of Executing Organisation'); ?></td>
                   <td class="f-3 f-w-500 text-capitalize text-start border">
                       <?php echo htmlspecialchars($return['executing_organisation']) ?? ''; ?></td>
                   </tr>
                   <tr style="background-color: #eeeeee;">
                    <td class="f-3 f-w-500 text-capitalize text-start border">3</td>
                       <td class="f-3 f-w-500 text-capitalize text-start border">
                       <?= htmlspecialchars(__('Address of Organisation (Registered address & address of branch from which proposal is given)'), ENT_QUOTES, 'UTF-8'); ?></td>
                       <td class="f-3 f-w-500 text-capitalize text-start border">
                           <?php echo htmlspecialchars($return['organisation_address']) ?? ''; ?></td>
                       </tr>
                       <tr style="background-color: #eeeeee;">
                        <td class="f-3 f-w-500 text-capitalize text-start border">4</td>
                           <td class="f-3 f-w-500 text-capitalize text-start border">
                           <?= __('Contact Person'); ?></td>
                           <td class="f-3 f-w-500 text-capitalize text-start border">
                               <?php echo htmlspecialchars($return['contact_person']) ?? ''; ?></td>
                           </tr>
                           <tr style="background-color: #eeeeee;">
                            <td class="f-3 f-w-500 text-capitalize text-start border">5</td>
                               <td class="f-3 f-w-500 text-capitalize text-start border">
                               <?= htmlspecialchars(__('Contact Number & email address'), ENT_QUOTES, 'UTF-8'); ?></td>
                               <td class="f-3 f-w-500 text-capitalize text-start border">
                                   <?php echo htmlspecialchars($return['contact_details']) ?? ''; ?></td>
                               </tr>
                               <tr style="background-color: #eeeeee;">
                                <td class="f-3 f-w-500 text-capitalize text-start border">6</td>
                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                   <?= __('Experience of the Organisation'); ?></td>
                                   <td class="f-3 f-w-500 text-capitalize text-start border"><?php echo htmlspecialchars($return['organisation_experience']) ?? ''; ?>
                               </td>
                           </tr>
                           <tr style="background-color: #eeeeee;">
                            <td class="f-3 f-w-500 text-capitalize text-start border">7</td>
                               <td class="f-3 f-w-500 text-capitalize text-start border">
                               <?= __('Place of execution of project'); ?></td>
                               <td class="f-3 f-w-500 text-capitalize text-start border">
                                   <?php echo htmlspecialchars($return['execution_place']) ?? ''; ?></td>
                               </tr>
                               <tr style="background-color: #eeeeee;">
                                <td class="f-3 f-w-500 text-capitalize text-start border">8</td>
                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                   <?= __('Reason for choice of place'); ?></td>
                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                       <?php echo htmlspecialchars($return['place_reason']) ?? ''; ?></td>
                                   </tr>
                                   <tr style="background-color: #eeeeee;">
                                    <td class="f-3 f-w-500 text-capitalize text-start border">9</td>
                                       <td class="f-3 f-w-500 text-capitalize text-start border">
                                       <?= __('Tenure of the project'); ?></td>
                                       <td class="f-3 f-w-500 text-capitalize text-start border">
                                           <?php echo htmlspecialchars($return['project_tenure']) ?? ''; ?></td>
                                       </tr>
                                       <tr style="background-color: #eeeeee;">
                                        <td class="f-3 f-w-500 text-capitalize text-start border">10</td>
                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                           <?= __('Cost of the project (enclose a detailed budget)'); ?></td>
                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                               <?php echo htmlspecialchars($return['project_cost']) ?? ''; ?></td>
                                           </tr>
                                           <tr style="background-color: #eeeeee;">
                                            <td class="f-3 f-w-500 text-capitalize text-start border">11</td>
                                               <td class="f-3 f-w-500 text-capitalize text-start border">
                                               <?= __('Funds requested from Infisuite'); ?></td>
                                               <td class="f-3 f-w-500 text-capitalize text-start border">
                                                   <?php echo htmlspecialchars($return['funds_infisuite']) ?? ''; ?></td>
                                               </tr>
                                               <tr style="background-color: #eeeeee;">
                                                <td class="f-3 f-w-500 text-capitalize text-start border">12</td>
                                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                                   <?= __('Funds requested from any other sources if any'); ?></td>
                                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                                       <?php echo htmlspecialchars($return['funds_other_sources']) ?? ''; ?></td>
                                                   </tr>
                                                   <tr style="background-color: #eeeeee;">
                                                    <td class="f-3 f-w-500 text-capitalize text-start border">13</td>
                                                       <td class="f-3 f-w-500 text-capitalize text-start border">
                                                       <?= __('Objective of the project'); ?></td>
                                                       <td class="f-3 f-w-500 text-capitalize text-start border">
                                                           <?php echo htmlspecialchars($return['project_objective']) ?? ''; ?></td>
                                                       </tr>
                                                       <tr style="background-color: #eeeeee;">
                                                        <td class="f-3 f-w-500 text-capitalize text-start border">14</td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                                           <?= __('Reason of Organisation preferring Infisuite to be part of this project'); ?></td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border"><?php echo htmlspecialchars($return['reason_infisuite']) ?? ''; ?>
                                                       </td>
                                                   </tr>
                                                   <tr style="background-color: #eeeeee;">
                                                    <td class="f-3 f-w-500 text-capitalize text-start border">15</td>
                                                       <td class="f-3 f-w-500 text-capitalize text-start border">
                                                       <?= __('Benefits/Outcomes expected from the project'); ?></td>
                                                       <td class="f-3 f-w-500 text-capitalize text-start border">
                                                           <?php echo htmlspecialchars($return['project_benefits']) ?? ''; ?></td>
                                                       </tr>
                                                       <tr style="background-color: #eeeeee;">
                                                        <td class="f-3 f-w-500 text-capitalize text-start border">16</td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                                           <?= __('Quantitative (Mention as per parameter suitable, but make sure to mention number of beneficiaries)'); ?></td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                                               <p class="m-0"><?php echo htmlspecialchars($return['quantitative_1']) ?? ''; ?></p>
                                                               <p class="m-t-0"><?php echo htmlspecialchars($return['quantitative_2']) ?? ''; ?></p>
                                                               <p class="m-t-0"><?php echo htmlspecialchars($return['quantitative_3']) ?? ''; ?></p>
                                                               <p class="m-t-0"><?php echo htmlspecialchars($return['quantitative_4']) ?? ''; ?></p>
                                                               <p class="m-t-0"><?php echo htmlspecialchars($return['quantitative_5']) ?? ''; ?></p>
                                                           </td>
                                                       </tr>
                                                       <tr style="background-color: #eeeeee;">
                                                        <td class="f-3 f-w-500 text-capitalize text-start border">17</td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                                           <?= __('Qualitative (parameters which are difficult to capture in numbers)'); ?></td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                                               <p class="m-0"><?php echo htmlspecialchars($return['qualitative_1']) ?? ''; ?></p>
                                                               <p class="m-t-0"><?php echo htmlspecialchars($return['qualitative_2']) ?? ''; ?></p>
                                                               <p class="m-t-0"><?php echo htmlspecialchars($return['qualitative_3']) ?? ''; ?></p>
                                                           </td>
                                                       </tr>
                                                       <tr style="background-color: #eeeeee;">
                                                        <td class="f-3 f-w-500 text-capitalize text-start border">18</td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border">
                                                           <?= __('Requirement of any approval from local bodies/government/municipality or any statutory body'); ?></td>
                                                           <td class="f-3 f-w-500 text-capitalize text-start border"><?php echo htmlspecialchars($return['approval_requirement']) ?? ''; ?>
                                                       </td>
                                                   </tr>
                                                   <tr style="background-color: #eeeeee;">
                                                    <td class="f-3 f-w-500 text-capitalize text-start border">19</td>
                                                       <td class="f-3 f-w-500 text-capitalize text-start border">
                                                       <?= __('Whether similar projects executed earlier?'); ?></td>
                                                       <td class="f-3 f-w-500 text-capitalize text-start border"><?php echo htmlspecialchars($return['similar_projects']) ?? ''; ?>
                                                   </td>
                                               </tr>
                                               <tr style="background-color: #eeeeee;">
                                                <td class="f-3 f-w-500 text-capitalize text-start border">20</td>
                                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                                   <?= __('In case the proposal is approved, the organisation is okay to open separate bank account for BPCL? (Yes/No)'); ?></td>
                                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                                       <?php echo htmlspecialchars($return['bank_account_infisuite']) ?? ''; ?>
                                                   </td>
                                               </tr>
                                               <tr style="background-color: #eeeeee;">
                                                <td class="f-3 f-w-500 text-capitalize text-start border">21</td>
                                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                                   <?= __('Information given above is true and we can provide the documentary proof of it in case of enquiry'); ?></td>
                                                   <td class="f-3 f-w-500 text-capitalize text-start border">
                                                   <?php echo ($return['information_true'] ?? null) == 1 ? ' Yes' : ' No'; ?>
                                                    <?php if (!empty($return['information_true_file'])): ?>
                                                        <a target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['information_true_file']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                                                    <?php endif; ?>
                                               </td>
                                           </tr>
                                       </table>
                                   </td>
                               </tr>
                               <tr>
                                   <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                               </tr>
                           <?php } ?>
                           <?php if (isset($return['form_e']) && $return['form_e'] == 1) { ?>
                               <tr>
                                   <td colspan="2" class="f-14 f-w-600 text-capitalize"><?= __('Form E'); ?></td>
                               </tr>
                               <tr>
                                   <td colspan="2" class="f-14 f-w-600 text-capitalize text-center text-underline"><?= __('Track Record of Projects'); ?></td>
                               </tr>
                               <tr>
                                   <td colspan="2" class="f-14 f-w-500 text-capitalize text-center"><?= __('(Kindly mention projects undertaken in the last 3 years. If an organisation is existing for 3 years, track record from inception till date is required)'); ?></td>
                               </tr>
                               <tr>
                                   <td colspan="2">
                                       <table class="w-100 border-collapse">
                                           <tr style="background-color: #eeeeee;">
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 9%;"><?= __('Sr.No.'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Project Description'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Focus Area'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Location'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Period (To - From)'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Value in INR Lakhs'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Number of Beneficiaries'); ?></th>
                                               <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Partner Name (Funding Agency)'); ?></th>
                                           </tr>
                                           <?php

                                           if ($return['project_data']) {
                                // Decode JSON data into a PHP associative array
                                            $project_data = json_decode($return['project_data'], true);

                                            foreach ($project_data as $id => $project) {
                                                $project_description = isset($project['project_description']) ? $project['project_description'] : '-';
                                                $focus_area = isset($project['focus_area']) ? $project['focus_area'] : '-';
                                                $location = isset($project['location']) ? $project['location'] : '-';
                                                $period = isset($project['period']) ? $project['period'] : '-';
                                                $value = isset($project['value']) ? $project['value'] : '-';
                                                $beneficiaries = isset($project['beneficiaries']) ? $project['beneficiaries'] : '-';
                                                $partner_name = isset($project['partner_name']) ? $project['partner_name'] : '-';

                                    // Append each row to the tableRows variable
                                                echo "
                                                <tr style=\"background-color: #eeeeee;\">
                                                <th scope='col' class='tbl-font tbl-th'>$id</th>
                                                <th scope='col' class='tbl-font tbl-td text-center'>$project_description</th>
                                                <th scope='col' class='tbl-font tbl-td text-center'>$focus_area</th>
                                                <th scope='col' class='tbl-font tbl-td text-center'>$location</th>
                                                <th scope='col' class='tbl-font tbl-td text-center'>$period</th>
                                                <th scope='col'  class='tbl-font tbl-td tbl-right'>" . (!empty($value) ? customeummarmonyformate($value) : '0') . "</th>
                                                <th scope='col'  class='tbl-font tbl-td tbl-center'>" . $beneficiaries . "</th>
                                                <th scope='col' class='tbl-font tbl-td tbl-center'>$partner_name</th>
                                                </tr>";
                                            }
                                        }
                                        ?>

                                    </table>
                                </td>
                            </tr>
                            <tr>
                               <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                           </tr>
                       <?php } ?>

                       <tr>
                           <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                       </tr>
                       <tr>
                           <td colspan="2" class="f-14 f-w-600 text-capitalize text-center text-underline"><?= __('Project Tracking'); ?></td>
                       </tr>
                       <tr>
                           <td colspan="2">
                               <table class="w-100 border-collapse">
                                   <tr style="background-color: #eeeeee;">
                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-start border" style="width: 9%;"><?= __('Sr.No.'); ?></th>
                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Tracking Name'); ?></th>
                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Description'); ?></th>
                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Start Date'); ?></th>
                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('End Date'); ?></th>
                                       <th scope="col" class="f-13 f-w-600 text-capitalize text-center border" style="width: 13%;"><?= __('Budget'); ?></th>

                                   </tr>
                                   <?php

                                   if ($return['tracking_data']) {
                                // Decode JSON data into a PHP associative array
                                    $tracking_data = json_decode($return['tracking_data'], true);

                                    foreach ($tracking_data as $id => $project) {
                                        $tracking_name = isset($project['tracking_name']) ? $project['tracking_name'] : '-';
                                        $description = isset($project['description']) ? $project['description'] : '-';
                                        $start_date = isset($project['start_date']) ? $project['start_date'] : '-';
                                        $end_date = isset($project['end_date']) ? $project['end_date'] : '-';
                                        $tracking_budget = isset($project['tracking_budget']) ? $project['tracking_budget'] : '-';

                                    // Append each row to the tableRows variable
                                        echo "
                                        <tr style=\"background-color: #eeeeee;\">
                                        <th scope='col' class='tbl-font tbl-th'>$id</th>
                                        <th scope='col' class='tbl-font tbl-td text-center'>$tracking_name</th>
                                        <th scope='col' class='tbl-font tbl-td text-center'>$description</th>
                                        <th scope='col' class='tbl-font tbl-td text-center'>" . fromDate($start_date) . "</th>
                                        <th scope='col' class='tbl-font tbl-td text-center'>" . fromDate($end_date) . "</th>
                                        <th scope='col'  class='tbl-font tbl-td tbl-right'>" . (!empty($tracking_budget) ? customeummarmonyformate($tracking_budget) : '0') . "</th>

                                        </tr>";
                                    }
                                }
                                ?>

                            </table>
                        </td>
                    </tr>
                    <tr>
                       <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                   </tr>

                   <tr>
                       <td colspan="2" class="f-14 f-w-600 text-capitalize"></td>
                   </tr>
                   <tr>
                       <td colspan="2" class="f-14 f-w-600 text-capitalize text-center"><?= __('Declaration'); ?></td>
                   </tr>

                   <tr>
                       <td colspan="2" class="f-14 f-w-500 text-capitalize">
                           <?= __('Signature'); ?>: <?php echo htmlspecialchars($return['signature']) ?? ''; ?>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" class="f-14 f-w-500 text-capitalize">
                           <?= __('Name'); ?>: <?php echo htmlspecialchars($return['name']) ?? ''; ?>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" class="f-14 f-w-500 text-capitalize">
                           <?= __('Designation'); ?> : <?php echo htmlspecialchars($return['designation']) ?? ''; ?>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" class="f-14 f-w-500 text-capitalize">
                           <?= __('Seal of the Organisation'); ?> :
                           <?php if (!empty($return['organisation_seal'])): ?>
                        <a  target="_blank"  href="uploads/csr/<?php echo htmlspecialchars($return['organisation_seal']); ?>"  class="mb-2" download><i class="ti-cloud-down f-15rem f-w-60"></i></a>
                    <?php endif; ?>
                       </td>
                   </tr>

               </table>
           </div>
           <?php if (!$rid) { ?>
               <table>
                   <tr>
                       <th scope="col" colspan="2" class="f-14 f-w-500 text-capitalize">
                           <input aria-label="<?= htmlspecialchars(__('Declaration Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" id="declaration-checkbox" >
                           <?= __('I / We hereby declare and certify that all the information given in the application form and in the documents submitted are true and correct to the best of my knowledge.'); ?>
                       </th>
                   </tr>
                   <tr>
                       <td colspan="2" class="f-14 f-w-500 text-capitalize">
                           <input aria-label="<?= htmlspecialchars(__('Misrepresentation Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" id="misrepresentation-checkbox" >
                           <?= __('I / We acknowledge that in case of misrepresentation of information or furnishing of false information, my application is liable to be rejected and /or approved project is liable to be terminated by BPCL at any stage.'); ?>
                       </td>
                   </tr>
                   <tr>
                       <td colspan="2" class="f-14 f-w-500 text-capitalize">
                           <input aria-label="<?= htmlspecialchars(__('Blacklist Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" type="checkbox" id="blacklist-checkbox" >
                           <?= __('I / We further declare and certify that our organisation / company / group of companies has / have not been blacklisted or been put on the holiday list by any of the Public Sector Undertakings in India or Statutory authority or Government.'); ?>
                       </td>
                   </tr>
               </table>
               <div class="row">

               <p class="text-danger f-14 f-w-500 m-t-10">
               <?= __('Note: Please ensure all the above declarations are checked before submitting the application.'); ?>
                </p>
               </div>
               <div class="row">
                   <div class="col-lg-6 mt-5">
                       <button type="button" class="btn btn-primary waves-effect waves-light m-r-30 mt-3" id="editBtn"><?= __('Edit'); ?></button>
                   </div>
                   <div class="col-lg-6 mt-5">
                       <button id="submit-button" class="btn btn-primary mt-3 float-end" type="button" data-record-id="<?php echo $last_rec_id ?>" disabled><?= __('Submit'); ?></button>
                   </div>
               </div>
           <?php } ?>
       </div></div>
<div
    id="csr_config3"
    data-old-id="<?= isset($old_id)
        ? htmlspecialchars($old_id, ENT_QUOTES, 'UTF-8')
        : '' ?>"
    hidden>
</div>

<script src="assets/js/custom_view_csr_proposal_details.js"></script>
