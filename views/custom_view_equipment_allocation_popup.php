<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getequipmentallocations&building_floor_id=".$_POST['building_floor_id']."&pagetype=building_floors";
$response = callAPI($url);
$result = json_decode($response,1);
$building_floors = $result['building_floors'];
$building_floor_spaces = $result['building_floor_spaces'];
$equipment_allocations = $result['equipment_allocations'];
$buildings = $result['buildings'];
$equipment_res = $result['equipments'];
$equipments =$employees = array();
foreach($equipment_res as $equi){
    $equipments[$equi['id']] = $equi['equipment_name'];
}
$employee_res = $result['employees'];
foreach($employee_res as $empr){
    $employees[$empr['id']] = $empr;
}
$unallocate_equipmentemp_qty = $equipmentemp_qty = $equipment_chear_qty = 0;
foreach($equipment_allocations as $equipres){
    if($equipres['equipment_type_id'] == '1'){
        $equipment_chear_qty++;
        if($equipres['emp_id'] !=""){
            $equipmentemp_qty++;
        }else{
            $unallocate_equipmentemp_qty++;
        }
    }
}
?>
<script type="text/javascript">
    <?php if($_POST['eaid'] !=""){ ?>
        showPopup('equipment_alloc_id_<?php echo $_POST['eaid']; ?>');
    <?php } ?>
</script>
<div class="modal-header  ">
    <h5 class="modal-title"> <?= __('Building Floor'); ?></h5>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <div class="row">
        <div class="col-xl-6 col-md-6 text-center">
            <h5><?= __('Building Name'); ?></h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $buildings['building_name']; ?></h6>
            <p><i class="ti-location-pin"></i> <?php echo $buildings['building_address']; ?></p>
        </div>
        <div class="col-xl-6 col-md-6 text-center">
            <h5><?= __('Floor Name'); ?></h5>
            <h6 class="m-b-30 f-w-700 mt-2"><?php echo $building_floors['floor_name']; ?></h6>
        </div>

    </div>

        <div class="row mt-3">
            <div class="col-1 text-center"></div>
            <div class="col-2 text-center">
                <div class="s-caption  text-primary"><i class="ti-list f-20"></i></div>
                <div class="s-cont d-inline-block">
                    <h5 class="f-w-700 m-b-0"><?php echo count((array)$equipment_allocations); ?></h5>
                    <p class="m-b-0 f-12"><?= __('Total Equipments'); ?></p>
                </div>
            </div>
            <div class="col-2 b-l-default text-center">
                <div class="s-caption  text-info"><i class="ti-write f-20"></i></div>
                <div class="s-cont d-inline-block">
                    <h5 class="f-w-700 m-b-0"><?php echo count((array)$building_floor_spaces);?></h5>
                    <p class="m-b-0 f-12"><?= __('Total Spaces'); ?></p>
                </div>
            </div>
            <div class="col-2 b-l-default text-center">
                <div class="s-caption  text-success"><i class="ti-check-box f-20"></i></div>
                <div class="s-cont d-inline-block">
                    <h5 class="f-w-700 m-b-0"><?php echo $equipment_chear_qty; ?></h5>
                    <p class="m-b-0 f-12"><?= __('Total Chairs'); ?></p>
                </div>
            </div>
            <div class="col-2 b-l-default text-center">
                <div class="s-caption  text-danger"><i class="ti-time f-20"></i></div>
                <div class="s-cont d-inline-block">
                    <h5 class="f-w-700 m-b-0"><?php echo $equipmentemp_qty; ?></h5>
                    <p class="m-b-0 f-12"><?= __('Allocated Chairs'); ?></p>
                </div>
            </div>
            <div class="col-2 b-l-default text-center">
                <div class="s-caption  text-info"><i class="ti-timer f-20"></i></div>
                <div class="s-cont d-inline-block">
                    <h5 class="f-w-700 m-b-0"><?php echo $unallocate_equipmentemp_qty; ?></h5>
                    <p class="m-b-0 f-12"><?= __('Un-Allocated Chairs'); ?></p>
                </div>
            </div>
            <div class="col-1 text-center"></div>
        </div>
    <div class="building_floor_size p-0" id="seat_info">
        <img src="uploads/building_floors/<?php echo $building_floors['floor_image'];  ?>" class="img" alt="<?= htmlspecialchars(__('Attachment Preview'), ENT_QUOTES, 'UTF-8'); ?>">
        <?php
            $cabincnt = 0;
            foreach($equipment_allocations as $ealloc){  $cabincnt++;
                ?>
                    <div id="position_<?php echo $cabincnt; ?>" class="equipment-allocation-icon cursor-pointer" tabindex="0" onkeydown="if(event.target===this&&(event.key==='Enter'||event.key===' ')){event.preventDefault();this.click();}" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $cabincnt; ?> <?= htmlspecialchars(__('Seat'), ENT_QUOTES, 'UTF-8'); ?>" style="top: <?php echo $ealloc['top_size']; ?>%; left: <?php echo $ealloc['left_size']; ?>%;" onclick="showPopup('equipment_alloc_id_<?php echo $ealloc['id']; ?>')">
                        <?php if($employees[$ealloc['emp_id']]['display_name'] !=""){ ?>
                        <?php if($employees[$ealloc['emp_id']]['image'] !="") {?>
                            <img src="uploads/all/<?php echo $employees[$ealloc['emp_id']]['image']; ?>" class="img-radius img-30 align-top" alt="<?= htmlspecialchars(__('Attachment Preview'), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php } else { ?>
                            <span  alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius align-top custom_txt_workentry f-12 text-center" ><?php echo initials($employees[$ealloc['emp_id']]['display_name']); ?></span>
                        <?php } ?>
                        <?php } else { ?>
                            <i class="ti-location-pin"></i>
                        <?php } ?>

                        <div class="custom-popup" id="custom-popup_<?php echo $cabincnt; ?>">

                            <div class="custom-popup-open equipment_alloc_id_<?php echo $ealloc['id']; ?> text-start" style="top: 0px; display: none; width: 300px;">
                                <div class="card">
                                    <div class="card-body">
                                        <?php if($employees[$ealloc['emp_id']]['display_name'] !=""){ ?>
                                            <div class="align-middle">
                                                <?php if($employees[$ealloc['emp_id']]['image'] !="") {?>
                                                    <img src="uploads/all/<?php echo $employees[$ealloc['emp_id']]['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="<?= htmlspecialchars(__('Attachment Preview'), ENT_QUOTES, 'UTF-8'); ?>">
                                                <?php } else { ?>
                                                    <span  alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees[$ealloc['emp_id']]['display_name']); ?></span>
                                                <?php } ?>
                                                <div class="d-inline-block width73 text-dark">
                                                    <h6 class="mb-0"><?php echo $employees[$ealloc['emp_id']]['display_name']; ?>
                                                    </h6>
                                                    <p class="m-b-0"><?php echo $employees[$ealloc['emp_id']]['emp_code']; ?></p>
                                                </div>
                                            </div>
                                            <strong class="d-block mt-2 text-dark"><i class="ti-email"></i> &nbsp <?php echo $employees[$ealloc['emp_id']]['email_id']; ?></strong>
                                        <?php } ?>
                                        <strong class="d-block text-dark"><i class="ti-layout"></i>&nbsp  <?php echo $building_floor_spaces[$ealloc['building_floor_space_id']]['cabin_name']; ?>
                                        <button type="button" class="btn btn-link p-0 border-0 m-0 float-end text-reset" data-bs-toggle="tooltip" data-placement="bottom" title="<?= htmlspecialchars(__('Edit Equipment Allocation'), ENT_QUOTES, 'UTF-8'); ?>" data-bs-original-title="<?= htmlspecialchars(__('Edit Equipment Allocation'), ENT_QUOTES, 'UTF-8'); ?>" onclick="insertEquipmentAllocation('<?php echo $building_floors['id']; ?>','<?php echo $ealloc['building_floor_space_id']; ?>','<?php echo $ealloc['id']; ?>')" aria-label="<?= htmlspecialchars(__('Edit Equipment Allocation'), ENT_QUOTES, 'UTF-8'); ?>"><i class="ti-pencil-alt"></i></button>
                                        </strong>
                                        <strong class="d-block text-dark mt-3"><i class="ti-layout-list-large-image"></i> &nbsp <?php echo $equipments[$ealloc['equipment_id']]; ?></strong>
                                     </div>
                                 </div>
                            </div>

                        </div>
                    </div>
            <?php } ?>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
</div>
