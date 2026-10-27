<?php
if($_POST['ajaxpagecallll'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getequipmentallocations&pagetype=".$_POST['pagetype']."&equipment_category_id=".$_POST['equipment_category_id']."&equipment_type_id=".$_POST['equipment_type_id']."&equipment_id=".$_POST['equipment_id']."&building_id=".$_POST['building_id']."&building_floor_id=".$_POST['building_floor_id']."&emp_id=".$_POST['emp_id']."&seating_equipment=".$_POST['seating_equipment']."&equipment_allocation_id=".$_POST['equipment_allocation_id']."&building_floor_space_id=".$_POST['building_floor_space_id']."&admin_type=".$_SESSION['admin_type']."&employee_id=".$_SESSION['emp_id']."&location_id=".$_SESSION['user']['location_id']."&space_type_id=".$_POST['space_type_id']."&seating_pool_id=".$_POST['seating_pool_id'];
    $response = callAPI($url);
    $result = json_decode($response,1);
}
$buildings_report = $result['buildings_report'];
$building_floors_report = $result['building_floors_report'];
$space_types = $result['space_types'];
$building_with_floors = array();
foreach($building_floors_report as $bfr){
    $building_with_floors[$bfr['building_id']][] = $bfr;
}
$building_floor_spaces_report = $result['building_floor_spaces_report'];
$floors_with_spaces = array();
foreach($building_floor_spaces_report as $bfsr){
    $floors_with_spaces[$bfsr['building_floor_id']][] = $bfsr;
}
$employees = $result['employees'];
$employees_list = array();
foreach($employees as $empd){
    $employees_list[$empd['id']] = $empd;
}
$printfinalaray = array();
$seating_pools = $result['seating_pools'];
$space_cnt = $allocemp = $reserve_poolcnt = $freespacecnt = 0;
foreach($buildings_report as $buldd){
    $buildingwithfloors = $building_with_floors[$buldd['id']];
    foreach($buildingwithfloors as $floors){
        foreach($floors_with_spaces[$floors['id']] as $fspaceres){
            $status="Vacant";
            $emp_code = $employee_name = $emp_email_id = $manager_name = $status = $reserve_pool = "";
            if($fspaceres['emp_id'] !=""){
                $emp_code = $employees_list[$fspaceres['emp_id']]['emp_code'];
                $employee_name = $employees_list[$fspaceres['emp_id']]['display_name'];
                $emp_email_id = $employees_list[$fspaceres['emp_id']]['email_id'];
                $manager_email_id = $employees_list[$employees_list[$fspaceres['emp_id']]['reporting_manager_id']]['email_id'];
                $manager_name = $employees_list[$employees_list[$fspaceres['emp_id']]['reporting_manager_id']]['display_name'];
                $status="Occupied";
                $allocemp++;
            }elseif($fspaceres['reserve_pool_id'] !=""){
                $reserve_poolcnt++;
                $reserve_pool = $seating_pools[$fspaceres['reserve_pool_id']]['pool_name'];
                $status="Occupied";
            }else{
                $freespacecnt++;
            }
            $space_cnt++;
            $printfinalaray[] = array("floor_name"=>$floors['floor_name'],"reserve_pool"=>$reserve_pool,"emp_code"=>$emp_code!=""?$emp_code:"-","employee_name"=>$employee_name!=""?$employee_name:"-","emp_email_id"=>$emp_email_id!=""?$emp_email_id:"-","cubical_cabin"=>$fspaceres['cabin_name'],"type"=>$space_types[$fspaceres['space_type_id']],"status"=>$status,"building_name"=>$buldd['building_name'],"manager_email_id"=>$manager_email_id!=""?$manager_email_id:"-","manager_name"=>$manager_name!=""?$manager_name:"-");

        }
    }
}

if(count((array)$printfinalaray) !="0"){ ?>

        <table class="table table-hover m-b-0" style="font-size:13px;">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Floor Name</th>
                    <th scope="col">Reserve Pool</th>
                    <th scope="col">Employee  Code</th>
                    <th scope="col">Employee Name</th>
                    <th scope="col">Employee  Email Id</th>
                    <th scope="col">Cubical Name</th>
                    <th scope="col">Type</th>
                    <th scope="col">Status</th>
                    <th scope="col">Building Name</th>
                    <th scope="col">Manager Email Id</th>
                    <th scope="col">Manager Name</th>
                </tr>
            </thead>
            <tbody>
                <?php $sq = 1; $sesssionprintfinalaray= array(); foreach($printfinalaray as $rcs){
                    $sesssionprintfinalaray[] = array("#"=>$sq,"Floor Name"=>$rcs['floor_name'],"Reserve pool"=>$rcs['reserve_pool'],"Employee  Code"=>$rcs['emp_code'],"Employee Name"=>$rcs['employee_name'],"Employee  Email Id"=>$rcs['emp_email_id'],"Cubical Name"=>$rcs['cubical_cabin'],"Type"=>$rcs['type'],"Status"=>$rcs['status'],"Building Name"=> $rcs['building_name'],"Manager Email Id"=>$rcs['manager_email_id'],"Manager Name"=>$rcs['manager_name']);

                    ?>
                <tr>
                    <td class="p-2"><?php echo $sq; ?></td>
                    <td class="p-2"><?php echo $rcs['floor_name']; ?></td>
                    <td class="p-2"><?php echo $rcs['reserve_pool']; ?></td>
                    <td class="p-2"><?php echo $rcs['emp_code']; ?></td>
                    <td class="p-2"><?php echo $rcs['employee_name']; ?></td>
                    <td class="p-2"><?php echo $rcs['emp_email_id']; ?></td>
                    <td class="p-2"><?php echo $rcs['cubical_cabin']; ?></td>
                    <td class="p-2"><?php echo $rcs['type']; ?></td>
                    <td class="p-2"><?php echo $rcs['status']; ?></td>
                    <td class="p-2"><?php echo $rcs['building_name']; ?></td>
                    <td class="p-2"><?php echo $rcs['manager_email_id']; ?></td>
                    <td class="p-2"><?php echo $rcs['manager_name']; ?></td>
                </tr>
                <?php $sq++;
} ?>
            </tbody>
        </table>

    <?php
    $_SESSION['download_data'] = $sesssionprintfinalaray;
}else{ ?>
        <div class="card-px text-center py-20 m-t-30">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Spaces</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
            </div>
        </div>
    <?php } ?>

