<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getequipmentallocations&pagetype=getequipmentlogs&eq_id=".$_POST['eq_id'];
$response = callAPI($url);
$result = json_decode($response,1);
$equipment_logs = $result['equipment_logs'];
$employees =$building_floor_spaces =  array();
foreach($result['employees'] as $emp){
    $employees[$emp['id']] = $emp;
}

foreach($result['building_floor_spaces'] as $bfs){
   $building_floor_spaces[$bfs['id']] = $bfs;
}

$log_type = array("IN"=>"Insert Equipemnt","UP"=>"Update Equipemnt","EA"=>"Equipement Allocated","EAI"=>"Employee Allocated","EAU"=>"Employee OR Space Allocated" ,"EU"=>"Employee Un-Allocated");
?>
<div class="modal-header">
    <h4 class="modal-title">Equipment Logs</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <?php if(count((array)$equipment_logs) != '0'){ ?>
    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
        <thead>
            <tr><th scope="col">User</th>
                <th scope="col">Log Type</th>
                <th scope="col">Log Date Time</th>
                <th scope="col">Employee</th>
                <th scope="col">Space Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($equipment_logs as $el){  ?>
            <tr>
                <td class="p-2"><?php echo $_SESSION['users'][$el['user_id']]; ?></td>
                <td class="p-2"><?php echo $log_type[$el['log_type']]; ?></td>
                <td class="p-2"><?php echo $el['log_datetime']; ?></td>
                <td class="p-2"><?php echo $el['emp_id']!=""?$employees[$el['emp_id']]['display_name']:"-"; ?></td>
                <td class="p-2"><?php echo $el['building_floor_space_id'] !=""?$building_floor_spaces[$el['building_floor_space_id']]['cabin_name']:"-"; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php }else{ ?>
        <div class="card-px text-center py-20 m-t-30">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Equipment logs</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
            </div>
        </div>
    <?php } ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>

</div>
