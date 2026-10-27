<?php
if($_POST['ajaxpagecall'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    echo $url = API."?mode=getequipmentallocations&pagetype=".$_POST['pagetype']."&equipment_category_id=".$_POST['equipment_category_id']."&equipment_type_id=".$_POST['equipment_type_id']."&equipment_id=".$_POST['equipment_id']."&building_id=".$_POST['building_id']."&building_floor_id=".$_POST['building_floor_id']."&emp_id=".$_POST['emp_id']."&seating_equipment=".$_POST['seating_equipment']."&equipment_allocation_id=".$_POST['equipment_allocation_id']."&building_floor_space_id=".$_POST['building_floor_space_id']."&admin_type=".$_SESSION['admin_type']."&employee_id=".$_SESSION['emp_id']."&location_id=".$_SESSION['user']['location_id']."&space_type_id=".$_POST['space_type_id']."&seating_pool_id=".$_POST['seating_pool_id'];
    $response = callAPI($url);
    $result = json_decode($response,1);
    $employees = array();
    foreach($result['employees'] as $emp){
        $employees[$emp['id']] = $emp;
    }
    echo "<pre>"; print_r($result['buildingsql']);  echo "</pre>";


}
if($_POST['pagetype'] =="IEQ"){
    $equipment_categories = $result['equipment_categories'];
    $equipment_types = $result['equipment_types'];
    $equipments = $result['equipments'];
    $equipment_allocat_with_equipment_id = array();
    foreach($result['equipment_allocations'] as $equiall){
        $equipment_allocat_with_equipment_id[$equiall['equipment_id']] = $equiall;
    }
    $equipments_list = $result['equipments_list'];
    $allocated_equipments = $unallocated_equipments = $unlocated_equipments = array();
    foreach($equipments_list as $equip){
        $equdetailary = $equipment_allocat_with_equipment_id[$equip['id']];
        if($equdetailary['emp_id'] !="" && $equdetailary['equipment_id'] !=""){
            $allocated_equipments[] = $equip;
        }elseif($equdetailary['emp_id'] =="" && $equdetailary['equipment_id'] !=""){
            $unallocated_equipments[] = $equip;
        }else{
            $unlocated_equipments[] = $equip;
        }
    }
    ?>
    <div class="card">
        <form id="form_IEQ">
            <div class="card-header table-card-header">
                <h5>Equipments</h5>
                <div class="card-header-right">
                    <button type="button" class="btn btn-success mb-2 desktop-view"  onclick="insertEquipemnt()"><i class="icofont icofont-plus icon-white "></i> Add Equipment</button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-2"></div>
                    <div class="col-lg-2 form-group  w-full">
                        <span class="form-label">Category</span>
                         <select aria-label="Equipment Category" class="js-example-basic-single" name="equipment_category_id">
                             <option value="">All Categories</option>
                            <?php foreach($equipment_categories as $stytpe){ ?>
                            <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['equipment_category_id']?"selected":""; ?>><?php echo $stytpe['category_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-2 form-group  w-full">
                        <span class="form-label">Type</span>
                          <select aria-label="Equipment Type" class="js-example-basic-single" name="equipment_type_id">
                              <option value="">All Types</option>
                            <?php foreach($equipment_types as $stytpe){ ?>
                            <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['equipment_type_id']?"selected":""; ?>><?php echo $stytpe['equipment_types_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-2 form-group  w-full">
                        <span class="form-label">Equipment</span>
                         <select aria-label="Equipment" class="js-example-basic-single" name="equipment_id">
                             <option value="">All Equipments</option>
                            <?php foreach($equipments as $stytpe){ ?>
                            <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['equipment_id']?"selected":""; ?>><?php echo $stytpe['equipment_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-2 form-group  w-full">
                        <span class="form-label">Seating Equipment</span>
                         <select aria-label="Seating Equipment" class="js-example-basic-single" name="seating_equipment">
                             <option value="">All Equipments</option>
                            <option value="Y" <?php echo $_POST['seating_equipment']=="Y"?"selected":""; ?>>Yes</option>
                            <option value="N" <?php echo $_POST['seating_equipment']=="N"?"selected":""; ?>>No</option>
                        </select>
                    </div>
                    <div class="col-lg-2 text-right">
                        <button type="button" class="btn btn-primary mb-2 desktop-view mt-4" onclick="getBuildingDynamicPage('IEQ')"><i class="ti-search"></i> Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Allocated Equipments</h5>
                    <div class="card-header-right">
                        <span class="form-label label label-md label-success"><?php echo count((array)$allocated_equipments); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)$allocated_equipments) !=0){ ?>
                    <div class="row">
                    <?php
                        $sq = 1;
                        foreach($allocated_equipments as $equip){
                            $equipment_allocat_detail = $equipment_allocat_with_equipment_id[$equip['id']];
                            $empary = $employees[$equipment_allocat_detail['emp_id']];
                             ?>
                        <div class="card col-lg-12">
                            <div class="card-body">
                                <div class="row  border-bottom">
                                    <div class="col-lg-7">
                                        <div class="align-middle">
                                            <?php if($equip['equipment_image'] !="") { ?>
                                                <img src="uploads/equipments/<?php echo $equip['equipment_image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($equip['equipment_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <h6 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $equip['equipment_name']; ?></h6>
                                                <span class="m-b-0 f-14 custom-limit-text" style="--linehide: 1;"><?php echo $equip['category_name']; ?></span>
                                                   <span class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $equip['equipment_types_name']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="align-middle float-left ml-2">
                                              <?php if($empary['image']!=""){?>
                                                <img src="uploads/all/<?php echo $empary['image']; ?>" class="img-radius img-30 align-top m-r-10" alt="Attachment Preview">
                                            <?php }else{ ?>
                                                <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($empary['display_name']); ?></span>
                                            <?php } ?>
                                              <div class="d-inline-block">
                                                <p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $empary['display_name']; ?></p>
                                                <p class="f-10 custom-limit-text" style="--linehide: 1;"><?php echo $empary['emp_code']; ?></p>
                                              </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center pt-2">
                                    <i class="ti-pencil-alt cursor-pointer m-r-30" onclick="insertEquipemnt('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Details" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-location-pin m-0 cursor-pointer" onclick="insertEquipmentAllocation('<?php echo $equipment_allocat_detail['building_floor_id']; ?>','<?php echo $equipment_allocat_detail['building_floor_space_id']; ?>','<?php echo $equipment_allocat_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Allocation" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-eye cursor-pointer m-l-30 m-r-30" onclick="ShowBuildingFloors('<?php echo $equipment_allocat_detail['building_floor_id']; ?>','<?php echo $equipment_allocat_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Location" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <a href="index.php?option=COM_INSERT_ONBOARDING_EMPLOYEES&Rid=<?php echo deocdeId('id:'.$empary['id']); ?>" target="_blank"><i class="ti-user m-0 cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Employee Details" ></i></a>

                                    <i class="ti-time cursor-pointer m-l-30" onclick="showEquipmentLogs('<?php echo $equipment_allocat_detail['equipment_id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Logs" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    <?php if($equip['seating_equipment']=='Y'){ ?>
                                        <span class="float-end text-info">Seating</span>
                                    <?php }else{ ?>
                                        <span class="float-end text-warning">Non-Seating</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Allocated Equipment</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Un-Allocated Equipments </h5>
                    <div class="card-header-right">
                        <span class="form-label label label-md label-warning"><?php echo count((array)$unallocated_equipments); ?></span>
                    </div>
                </div>
                 <div class="card-body">
                     <?php if(count((array)$unallocated_equipments) !=0){ ?>
                    <div class="row">
                    <?php
                        $sq = 1;
                        foreach($unallocated_equipments as $equip){
                            $equipment_allocat_detail = $equipment_allocat_with_equipment_id[$equip['id']];
                    ?>
                        <div class="card col-lg-12">
                            <div class="card-body">
                                <div class="row border-bottom">
                                    <div class="align-middle">
                                        <?php if($equip['equipment_image'] !="") { ?>
                                            <img src="uploads/equipments/<?php echo $equip['equipment_image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($equip['equipment_name']); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <h6 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $equip['equipment_name']; ?></h6>
                                            <span class="m-b-0 f-14 custom-limit-text" style="--linehide: 1;"><?php echo $equip['category_name']; ?></span>
                                               <span class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $equip['equipment_types_name']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center pt-2">
                                    <i class="ti-pencil-alt cursor-pointer m-r-30" onclick="insertEquipemnt('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Details" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-location-pin m-0 cursor-pointer" onclick="insertEquipmentAllocation('<?php echo $equipment_allocat_detail['building_floor_id']; ?>','<?php echo $equipment_allocat_detail['building_floor_space_id']; ?>','<?php echo $equipment_allocat_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Allocation" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-eye cursor-pointer m-l-30" onclick="ShowBuildingFloors('<?php echo $equipment_allocat_detail['building_floor_id']; ?>','<?php echo $equipment_allocat_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Location" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-time cursor-pointer m-l-30" onclick="showEquipmentLogs('<?php echo $equipment_allocat_detail['equipment_id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Logs" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    <?php if($equip['seating_equipment']=='Y'){ ?>
                                        <span class="float-end text-info">Seating</span>
                                    <?php }else{ ?>
                                        <span class="float-end text-warning">Non-Seating</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Un-Allocated Equipments</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>Equipments For Which Locations To Be Set </h5>
                    <div class="card-header-right">
                        <span class="form-label label label-md label-danger"><?php echo count((array)$unlocated_equipments); ?></span>
                    </div>
                </div>
                 <div class="card-body">
                     <?php if(count((array)$unlocated_equipments) !=0){ ?>
                    <div class="row">
                    <?php
                        $sq = 1;
                        foreach($unlocated_equipments as $equip){
                        ?>
                        <div class="card col-lg-12">
                            <div class="card-body">
                                <div class="row border-bottom">
                                    <div class="align-middle">
                                        <?php if($equip['equipment_image'] !="") { ?>
                                            <img src="uploads/equipments/<?php echo $equip['equipment_image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($equip['equipment_name']); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <h6 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $equip['equipment_name']; ?></h6>
                                            <span class="m-b-0 f-14 custom-limit-text" style="--linehide: 1;"><?php echo $equip['category_name']; ?></span>
                                               <span class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $equip['equipment_types_name']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center pt-2">
                                    <i class="ti-pencil-alt cursor-pointer m-r-30" onclick="insertEquipemnt('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Details" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-location-pin m-0 cursor-pointer" onclick="setLocationEquipments('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Set Location" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-time cursor-pointer m-l-30" onclick="showEquipmentLogs('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Logs" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <?php if($equip['seating_equipment']=='Y'){ ?>
                                        <span class="float-end text-info">Seating</span>
                                    <?php }else{ ?>
                                        <span class="float-end text-warning">Non-Seating</span>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Equipments For Which Locatons To Be Set</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php }elseif($_POST['pagetype'] =="AR"){
    $buildings = $result['buildings'];
 ?>

                <div class="owl-carousel carousel-box owl-theme">
                <?php foreach($buildings as $buld){ ?>
                <div  >
                    <div class="card cursor-pointer emptycss_building_selectio" id="building_selection_<?php echo $buld['id']; ?>" >
                        <div class="card-header pb-2">
                            <h5 class="custom-limit-text" style="--linehide: 1;"><?php echo $buld['building_name']; ?></h5>
                            <p class="f-w-600 text-muted f-12 custom-limit-text" style="--linehide: 1;"><?php echo $buld['building_address']; ?></p>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex align-items-center mb-4">
                                <div class="w-80px flex-shrink-0 me-2">
                                    <img src="uploads/all/<?php echo $buld['building_image']; ?>" class="img-radius align-top m-r-10" alt="Attachment Preview">
                                </div>
                                <div class="m-0">
                                    <h6 class="fw-bold mb-3 ">Building Details</h6>
                                    <div class="d-flex d-grid gap-3">
                                        <div class="d-flex flex-column flex-shrink-0 me-1">
                                            <span class="d-flex align-items-center fs-7 fw-bold text-muted mb-2">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['floor_cnt']; ?> Floors</span>
                                            </span>
                                            <span class="d-flex align-items-center text-muted fw-bold fs-7">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['building_floor_space_cnt']; ?> Spaces</span>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-shrink-0">
                                            <span class="d-flex align-items-center fs-7 fw-bold text-muted mb-2">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['equipments_cnt']; ?> Seats</span>
                                            </span>
                                            <span class="d-flex align-items-center text-muted fw-bold fs-7">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['free_equipments']; ?> Free</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-0">
                                <button type="button" class="btn btn-success btn-light me-2 mb-2 f-14" onclick="getEquipemntsWithSeat('AR_SEAT','<?php echo $buld['id']; ?>')">Seat Allocation </button>
                                <button type="button" class="btn btn-primary mb-2 f-14" onclick="getBuildingDetails('<?php echo $buld['id']; ?>')">Edit Details</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

            <div class="booking-seat"></div>

<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(function(){
        $('.carousel-box').owlCarousel({
            items:4,
            loop:false,
            autoplay:false,
            margin: 20,
            nav:true
        });
        },350);
    });
</script>
<script type="text/javascript" src="assets/bower_components/owl.carousel/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/js/owl-custom.js"></script>

<?php }elseif($_POST['pagetype'] =="AR_SEAT"){
    $building_floors = $result['building_floors'];
    $equipment_allocations = $result['equipment_allocations'];
    $total_allocated_eq = $total_unallocated_eq =  0;
    foreach($building_floors as $bfloor){
        foreach($equipment_allocations[$bfloor['id']] as $equal){
            if($equal['employee_seating'] =="Y"){
                if($equal['emp_id'] !=""){
                    $total_allocated_eq++;
                   }else{
                    $total_unallocated_eq++;
                   }
               }
        }
    }
    ?>

                <div class="card">
                    <div class="card-body ">
                        <div class="seat-wrapper-sec">
                            <div class="card-body ">
                                <div class="row align-items-center" id="ar_seat_id">
                                    <div class="col-lg-5">
                                        <span class="form-label">Floors</span>
                                    </div>
                                    <div class="col-lg-7 text-right">
                                        <div class="d-inline-block m-l-10">
                                            <button type="button" class="btn waves-effect waves-light btn-primary p-0 img-30 m-r-10"><?php echo $total_allocated_eq; ?></button>
                                            <span>Allocated Seat</span>
                                        </div>
                                        <div class="d-inline-block m-l-10 m-l-20">
                                            <button type="button" class="btn waves-effect waves-light p-0 img-30 m-r-10" style="border: 1px solid;"><?php echo $total_unallocated_eq; ?></button>
                                            <span>Free Seat</span>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <?php if(count((array)$building_floors)){
                                    foreach($building_floors as $bfloor){
                                    $total_seating_eq = $total_unseating_eq = $total_allocated_eq = $total_unallocated_eq =  0;
                                    foreach($equipment_allocations[$bfloor['id']] as $equal){
                                        if($equal['employee_seating'] =="Y"){
                                            $total_seating_eq++;
                                            if($equal['emp_id'] !=""){
                                                $total_allocated_eq++;
                                            }else{
                                                $total_unallocated_eq++;
                                            }
                                        }else{
                                            $total_unseating_eq++;
                                        }
                                    }
                             ?>
                                <div class="card-body ">
                                    <div class="row align-items-center" >
                                        <div class="col-lg-2">
                                             <h6 class="text-muted mb-0"> <?php echo $bfloor['floor_name']; ?></h6>
                                        </div>
                                        <div class="col-lg-10">
                                        <div class="seat-left-sec">
                                            <?php if(count((array)$equipment_allocations[$bfloor['id']]) != 0){ ?>
                                            <ul>
                                            <?php
                                                $eacnt = 1;
                                                foreach($equipment_allocations[$bfloor['id']] as $equal){
                                                    if($equal['employee_seating'] =="Y"){
                                                        $allocated = $tooltip =$addtooltip =  "";
                                                        if($equal['emp_id'] !=""){
                                                            $allocated = "allocated";
                                                            $tooltip = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$employees[$equal['emp_id']]['display_name'].'"';
                                                        }else{
                                                            $tooltip = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate seat"';
                                                        }
                                                        ?>
                                                        <li class="<?php echo $allocated; ?> custom-popup" >
                                                            <span class="cursor-pointer" onclick="toAllocateEmployee('<?php echo $equal['id']; ?>')" <?php echo $tooltip; ?> tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"> <?php echo $eacnt; ?></span>

                                                            <div class="custom-popup-open to_allocate_employee_<?php echo $equal['id']; ?> text-start" style="right: 34px; top: -80px; width: 280px;">

                                                            </div>
                                                        </li>
                                                    <?php }else{ ?>
                                                         <li class="bg-success">
                                                              <span class="cursor-pointer" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open Menu"><?php echo $eacnt; ?></span>
                                                        <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                            <button type="button" class="dropdown-item waves-light waves-effect cursor-pointer"  onclick="insertEquipemnt('<?php echo $equal['equipment_id']; ?>')">Edit Equipment Details</button>

                                                            <button type="button" class="dropdown-item waves-light waves-effect cursor-pointer"  onclick="insertEquipmentAllocation('<?php echo $equal['building_floor_id']; ?>','<?php echo $equal['building_floor_space_id']; ?>','<?php echo $equal['id']; ?>')" >Edit Allocation</button>

                                                            <button type="button" class="dropdown-item waves-light waves-effect cursor-pointer"  onclick="ShowBuildingFloors('<?php echo $equal['building_floor_id']; ?>','<?php echo $equal['id']; ?>')" >View Location</button>
                                                            <button type="button" class="dropdown-item waves-light waves-effect cursor-pointer"  onclick="showEquipmentLogs('<?php echo $equal['equipment_id']; ?>')" >View Logs</button>
                                                        </div>
                                                    </li>
                                                    <?php } ?>
                                                   <?php $eacnt++;
} ?>
                                            </ul>
                                        <?php }else{ ?>
                                            <h5 class="text-center"><i class="ti-alert"></i> No Equipments</h5>
                                        <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                </div>

                            <?php } ?>
                            <?php }else{ ?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Floors</p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

<?php }elseif($_POST['pagetype'] =="ALLOCATEEMP"){  ?>
    $selected_employees = $result['selected_employees'];
    $equipment_allocations = $result['equipment_allocations'];
    $postemp = $result['emp_id'];
    $eq_id = $equipment_allocations['id'];
    $disabled = "";
    if($postemp !=""){
        $disabled = 'event_disabled';
    }
 ?>
<div class="card">
    <div class="card-header">
        <h5>Employees</h5>
        <div class="card-header-right">
            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closeToAllocateEmployee()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-12">
                <div class="input-group input-group-button mb-0" >
                    <input aria-label="Search" type="text" id="filter1111" data-id='categoryfilter' data-type="div" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search">
                    <div class="input-group-text addon-style">
                        <span class=""><i class="ti-search text-white mt-2"></i></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="custom-scroll" style="max-height: 200px;" >
            <?php foreach($selected_employees as $emplist){ ?>
                <div id="results">
                <div class="row mt-2" >
                    <div class="col-lg-10">
                        <div class="align-middle  ">
                            <?php if($emplist['image']!=""){?>
                                <img src="uploads/all/<?php echo $emplist['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                            <?php }else{ ?>
                                <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($emplist['display_name']); ?></span>
                            <?php } ?>
                            <div class="d-inline-block">
                                <p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $emplist['display_name']; ?>
                                    <?php if($postemp== $emplist['id']){ ?>
                                        <i class="ti-check text-success f-14 f-w-700"></i>
                                    <?php } ?>
                                </p>
                                <p class="f-10 mb-0"><?php echo $emplist['emp_code']; ?> </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 d-inline-block">
                        <?php if($postemp == $emplist['id']){ ?>
                            <i class="ti-close text-danger f-w-700 " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Un-Allocate Seat" onclick="allocateEmployeeSeat('<?php echo $eq_id; ?>','')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                        <?php }else{  ?>
                            <i class="ti-check text-success f-w-700 <?php echo $disabled; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate Seat" onclick="allocateEmployeeSeat('<?php echo $eq_id; ?>','<?php echo $emplist['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php }elseif($_POST['pagetype'] =="CSA"){
    $buildings = $result['buildings'];

    ?>

                <div class="owl-carousel carousel-box owl-theme">
                <?php foreach($buildings as $buld){  ?>
                <div  >
                    <div class="card cursor-pointer emptycss_building_selectio" id="building_selection_<?php echo $buld['id']; ?>" >
                        <div class="card-header pb-2">
                            <h5 class="custom-limit-text" style="--linehide: 1;"><?php echo $buld['building_name']; ?></h5>
                            <p class="f-w-600 text-muted f-12 custom-limit-text" style="--linehide: 1;"><?php echo $buld['building_address']; ?></p>
                        </div>
                        <div class="card-body pt-0">
                            <div class="d-flex align-items-center mb-4">
                                <div class="w-80px flex-shrink-0 me-2">
                                    <img src="uploads/all/<?php echo $buld['building_image']; ?>" class="img-radius align-top m-r-10" alt="Attachment Preview">
                                </div>
                                <div class="m-0">
                                    <h6 class="fw-bold mb-3 ">Building Details</h6>
                                    <div class="d-flex d-grid gap-3">
                                        <div class="d-flex flex-column flex-shrink-0 me-1">
                                            <span class="d-flex align-items-center fs-7 fw-bold text-muted mb-2">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['floor_cnt']; ?> Floors</span>
                                            </span>
                                            <span class="d-flex align-items-center text-muted fw-bold fs-7">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['building_floor_space_cnt']; ?> Spaces</span>
                                            </span>
                                        </div>
                                        <div class="d-flex flex-column flex-shrink-0">
                                            <span class="d-flex align-items-center fs-7 fw-bold text-muted mb-2">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['usedspace_cnt']; ?> Allocated</span>
                                            </span>
                                            <span class="d-flex align-items-center text-muted fw-bold fs-7">
                                                <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['building_floor_space_cnt']-$buld['usedspace_cnt']; ?> Free</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="m-0 text-center">
                                <button type="button" class="btn btn-success btn-light me-2 mb-2 f-14" onclick="getEquipemntsWithSeat('CSA_ALLOCATION','<?php echo $buld['id']; ?>')">Cubicle Allocation </button>

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

            <div class="booking-seat"></div>

<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(function(){
        $('.carousel-box').owlCarousel({
            items:4,
            loop:false,
            autoplay:false,
            margin: 20,
            nav:true
        });
        },350);
    });
</script>
<script type="text/javascript" src="assets/bower_components/owl.carousel/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/js/owl-custom.js"></script>

<?php }elseif($_POST['pagetype'] =="CSA_ALLOCATION"){
    $buildings = $result['buildings'];
    $building_floors = $result['building_floors'];
    $building_floor_spaces = $result['building_floor_spaces'];
    $building_floor_space_allocations = $result['building_floor_space_allocations'];
    $reservepool = $result['reservepool'];
    $empallocatedcnt = $reservecnt = $notallocatedcnt = $inprogresscnt =  0;
    foreach($building_floors as $bfloor){
        if(count((array)$building_floor_spaces[$bfloor['id']]) != 0){
            foreach($building_floor_spaces[$bfloor['id']] as $bfspace){
                $bfsa_details = $building_floor_space_allocations[$bfspace['id']];
                if($bfsa_details['current_status'] ==ST_INPROGRESS){
                    $inprogresscnt++;
                }elseif($bfsa_details['emp_id'] !=""){
                    $empallocatedcnt++;
                }elseif($bfsa_details['reserve_pool_id'] !=""){
                    $reservecnt++;
                }else{
                     $notallocatedcnt++;
                }
            }
        }
    }

     ?>
<div class="card">
    <div class="card-body ">

        <div class="seat-wrapper-sec">
            <div class="card-body ">
                <div class="row align-items-center" id="ar_seat_id_2">
                    <div class="col-lg-4">
                        <span class="form-label mb-0"><?php echo $buildings['building_name']; ?></span>
                        <span class="f-12 d-block"><?php echo $buildings['building_address']; ?></span>
                    </div>
                    <div class="col-lg-8 text-right">
                        <div class="d-inline-block m-l-10">
                            <span class="form-label label label-lg label-danger"><?php echo $inprogresscnt; ?></span>

                            <span class="f-12">In-Progress</span>
                        </div>
                        <div class="d-inline-block m-l-10">
                            <span class="form-label label label-lg label-warning"><?php echo $reservecnt; ?></span>

                            <span class="f-12">Reserved</span>
                        </div>
                        <div class="d-inline-block m-l-10">
                            <span class="form-label label label-lg label-primary" style="background-color: #e4e4e4"><?php echo $empallocatedcnt; ?></span>

                            <span class="f-12" >Allocated</span>
                        </div>
                        <div class="d-inline-block m-l-10 m-l-20">
                            <span class="form-label label label-lg text-dark" style="border: 1px black solid;"><?php echo $notallocatedcnt; ?></span>

                            <span class="f-12">Free</span>
                        </div>
                        <div class="d-inline-block m-l-10 m-l-20">
                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close text-danger cursor-pointer" onclick="closeBookingSeat()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Close" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(count((array)$building_floors)){
                $k=1;    foreach($building_floors as $bfloor){
             ?>
                <div class="card-body ">
                    <div class="row align-items-center">
                        <div class="col-lg-2">
                             <h6 class="text-muted mb-0"> <?php echo $bfloor['floor_name']; ?></h6>
                              <button type="button" class="nav-link  p-0 mt-2" data-bs-toggle="tab" week" role="tab" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $bfloor['id']; ?>')"><i class="ti-eye"></i> Floor Plan</button>
                        </div>
                        <div class="col-lg-10">
                        <div class="seat-left-sec">
                            <?php if(count((array)$building_floor_spaces[$bfloor['id']]) != 0){ ?>
                            <ul id="tophieghtcheck_<?php echo $k; ?>">
                            <?php
                                $eacnt = 1;
                                foreach($building_floor_spaces[$bfloor['id']] as $bfspace){
                                    $bfsa_details = $building_floor_space_allocations[$bfspace['id']];
                                    $allocated = $tooltip = "";
                                    if($bfsa_details['current_status'] ==ST_INPROGRESS){
                                        $allocated = "bg-danger";
                                        $tooltip = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$bfspace['cabin_name'].' ('.$employees[$bfsa_details['emp_id']]['display_name'].') - Approval Pending"';
                                    }elseif($bfsa_details['emp_id'] !=""){
                                        $allocated = "allocated";
                                        $tooltip = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$bfspace['cabin_name'].' ('.$employees[$bfsa_details['emp_id']]['display_name'].')"';
                                    }elseif($bfsa_details['reserve_pool_id'] !=""){
                                        $allocated = "reserve_allocated";
                                        $tooltip = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$bfspace['cabin_name'].' ('.$reservepool[$bfsa_details['reserve_pool_id']]['pool_name'].')"';
                                    }else{
                                        $allocated = "free_allocation";
                                        $tooltip = 'data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'.$bfspace['cabin_name'].' : Click To Allocate Cubicle Space"';
                                    }
                                    ?>
                                    <li class="custom-popup" >
                                        <span class="cursor-pointer comman_allocation <?php echo $allocated; ?>" id="custom_popup_btn_position_<?php echo $bfspace['id']; ?>" onclick="toCubicleSpaceAllocateEmployee('<?php echo $bfspace['id']; ?>',event)" <?php echo $tooltip; ?> style="overflow: hidden;display:-webkit-box;-webkit-line-clamp:1;line-clamp:1;-webkit-box-orient: vertical;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <?php echo $bfspace['cabin_name']; ?></span>


                                        <div class="empty_popup_template custom-popup-open to_allocate_employee_<?php echo $bfspace['id']; ?> text-start" style="top:auto; left:auto; width: 350px; position: absolute; ">

                                        </div>
                                    </li>
                                 <?php $eacnt++;
} ?>
                            </ul>
                        <?php }else{ ?>
                            <h5 class="text-center"><i class="ti-alert"></i> No Cubicle Space</h5>
                        <?php } ?>
                        </div>
                    </div>
                </div>
             </div>
            <?php $k++;
} ?>
            <?php }else{ ?>
                <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Floors</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php }elseif($_POST['pagetype'] =="CSA_ALLOCATION_EMP"){  ?>
<?php
    $selected_employees = $result['selected_employees'];
    $building_floor_spaces = $result['building_floor_spaces'];
    $building_floor_space_allocations = $result['building_floor_space_allocations'];
    $bfsa_id = $building_floor_space_allocations['id'];
    $postemp = $result['emp_id'];
    $reserve_pool_id =$result['reserve_pool_id'];
    $bfs_id = $building_floor_spaces['id'];
    $disabled = $empactive = $reserveactive = "";
    if($postemp !=""){
        $disabled = 'event_disabled';
        $empactive = "active show";
    }elseif($reserve_pool_id !=""){
        $disabled = 'event_disabled';
        $reserveactive = "active show";
    }else{
        $empactive = "active show";
    }
    $reservepool = $result['reservepool'];
    $unallocated_building_floor_spaces = $result['unallocated_building_floor_spaces'];
    $building_floor_id = $building_floor_spaces['building_floor_id'];
    $assets = $result['assets'];
    $asset_allocations = $result['asset_allocations'];
 ?>
<div class="card">
    <div class="card-body pb-0">
        <div class="row">
            <div class="col-lg-10">
                <ul class="nav md-tabs infisuite_tab">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $empactive; ?>" data-bs-toggle="tab" href="#employee_space_allocation" role="tab">
                            <p class="nav-text mb-0 f-10">Emp</p>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a class="nav-link <?php echo $reserveactive; ?>" id="home-tab-6" data-bs-toggle="tab" href="#reserve_space_allocation" role="tab">
                        <p class="nav-text mb-0 f-10">Reserve</p>
                        </a>
                    </li>
                    <?php if($postemp !="" && $building_floor_space_allocations['current_status'] !="3"){ ?>
                    <li class="nav-item ">
                        <a class="nav-link" id="home-tab-6_2" data-bs-toggle="tab" href="#switch_space_allocation" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <p class="nav-text mb-0 f-10">Switch</p>
                        </a>
                    </li>
                <?php } ?>
                <li class="nav-item ">
                        <a class="nav-link " id="home-tab-6_3" data-bs-toggle="tab" href="#assets_list_select" role="tab">
                        <p class="nav-text mb-0 f-10">Assets</p>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-lg-2">
                <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closeToAllocateEmployee()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
             <div class="tab-pane fade <?php echo $empactive; ?>" id="employee_space_allocation" role="tabpanel">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="input-group input-group-button mb-0" >
                            <input aria-label="Search" type="text" id="filter1111_2" data-id='categoryfilter_2' data-type="div" class="form-control form-control-sm width1"  placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="Search">
                            <div class="input-group-text addon-style">
                                <span class=""><i class="ti-search text-white mt-2"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="custom-scroll" style="max-height: 250px; min-height: 250px;" >
                    <?php
                    if(count((array)$selected_employees) != 0){
                        foreach($selected_employees as $emplist){ ?>
                            <div id="results_2">
                                <div class="row mt-2" >
                                    <div class="col-lg-10">
                                        <div class="align-middle  ">
                                            <?php if($emplist['image']!=""){?>
                                                <img src="uploads/all/<?php echo $emplist['image']; ?>"   class="img-30 align-middle m-r-10 seating_emp_image" alt="Attachment Preview">
                                            <?php }else{ ?>
                                                <span  alt="user" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($emplist['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block align-middle">
                                                <p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $emplist['display_name']; ?>
                                                    <?php if($postemp== $emplist['id']){ ?>
                                                        <i class="ti-check text-success f-14 f-w-700"></i>
                                                    <?php } ?>
                                                </p>
                                                <p class="f-10 mb-0"><?php echo $emplist['emp_code']; ?> </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-inline-block align-middle">
                                        <?php if($postemp == $emplist['id']){ ?>
                                            <i class="ti-close text-danger f-w-700 " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Un-Allocate Cubicle Space" onclick="allocateEmployeecubicleSpace('<?php echo $bfs_id; ?>','<?php echo $bfsa_id; ?>','','')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                        <?php }else{  ?>
                                            <i class="ti-check text-success f-w-700 <?php echo $disabled; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate Cubicle Space" onclick="allocateEmployeecubicleSpace('<?php echo $bfs_id; ?>','<?php echo $bfsa_id; ?>','<?php echo $emplist['id']; ?>','')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Employees</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane fade <?php echo $reserveactive; ?>" id="reserve_space_allocation" role="tabpanel">
                <div class="custom-scroll" style="max-height: 250px; min-height: 250px;" >
                    <?php
                    if(count((array)$reservepool) != 0){
                    foreach($reservepool as $rpoolkey => $rpoolval){ ?>
                        <div class="row mt-2" >
                            <div class="col-lg-10">
                                <div class="align-middle">
                                    <span  alt="user" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($rpoolval['pool_name']); ?></span>

                                    <div class="d-inline-block align-middle">
                                        <p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $rpoolval['pool_name']; ?>
                                            <?php if($reserve_pool_id== $rpoolkey){ ?>
                                                <i class="ti-check text-success f-14 f-w-700"></i>
                                            <?php } ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 d-inline-block align-middle">
                                <?php if($reserve_pool_id == $rpoolkey){ ?>
                                    <i class="ti-close text-danger f-w-700 " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Un-Allocate Cubicle Space" onclick="allocateEmployeecubicleSpace('<?php echo $bfs_id; ?>','<?php echo $bfsa_id; ?>','','')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                <?php }else{ ?>
                                    <i class="ti-check text-success f-w-700 <?php echo $disabled; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate Cubicle Space" onclick="allocateEmployeecubicleSpace('<?php echo $bfs_id; ?>','<?php echo $bfsa_id; ?>','','<?php echo $rpoolkey; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Pools</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane fade" id="switch_space_allocation" role="tabpanel">
                <div class="custom-scroll" style="max-height: 250px; min-height: 250px;" >
                    <?php
                    if(count((array)$unallocated_building_floor_spaces) != 0){
                        foreach($unallocated_building_floor_spaces as $bfsdata){ ?>
                            <div id="results555">
                                <div class="row mt-2" >
                                    <div class="col-lg-10">
                                        <div class="align-middle  ">
                                               <span  alt="user" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($bfsdata['cabin_name']); ?></span>
                                            <div class="d-inline-block align-middle">
                                                <p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $bfsdata['cabin_name']; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-inline-block align-middle">
                                        <i class="ti-check text-success f-w-700" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate Cubicle Space" onclick="allocateEmployeecubicleSpace('<?php echo $bfsdata['id']; ?>','<?php echo $bfsa_id; ?>','','',0)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Spaces</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane fade" id="assets_list_select" role="tabpanel">
                <div class="custom-scroll" style="max-height: 250px; min-height: 250px;" >
                    <?php
                    if(count((array)$assets) != 0){
                        foreach($assets as $bfsdata){ ?>
                            <div id="results555_2">
                                <div class="row mt-2" >
                                    <div class="col-lg-10">
                                        <div class="align-middle  ">
                                               <span  alt="user" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($bfsdata['asset_description']); ?></span>
                                            <div class="d-inline-block align-middle">
                                                <p class="f-10 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $bfsdata['asset_description']; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 d-inline-block align-middle assetallocationwithbtnchnage_<?php echo $bfsdata['id']; ?>" >
                                    <?php if(isset($asset_allocations[$bfsdata['id']])){ ?>
                                        <i class="ti-trash text-danger f-w-700" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Un-Allocate Assets"  onclick="insertAssetallocationlist('<?php echo $building_floor_id; ?>','<?php echo $bfs_id; ?>','<?php echo $bfsdata['id']; ?>','<?php echo $asset_allocations[$bfsdata['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    <?php }else{ ?>
                                        <i class="ti-check text-success f-w-700" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate Assets"  onclick="insertAssetallocationlist('<?php echo $building_floor_id; ?>','<?php echo $bfs_id; ?>','<?php echo $bfsdata['id']; ?>','')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                    <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Assets</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php }elseif($_POST['pagetype'] =="BSEM"){
    $buildings = $result['buildings'];
    $building_floors = $result['building_floors'];
    $building_floor_space_allocations = $result['building_floor_space_allocations'];
    $select_locations = $result['select_locations'];
    $allocated_emp =array();
    foreach($building_floor_space_allocations as $bfsaemp){
        $allocated_emp[$bfsaemp['emp_id']] = $bfsaemp['emp_id'];
        $allocated_emp_spaces[$bfsaemp['emp_id']] = $bfsaemp;
    }
    $allocated_employees = $unallocated_employees = array();
    foreach($result['select_employees'] as $emp){
        if($allocated_emp[$emp['id']] !=""){
            $allocated_employees[] = $emp;
        }else{
            $unallocated_employees[] = $emp;
        }
    }
    ?>
    <div class="card">
        <div class="table-card-header">
            <div class="card-header">
                <h5>Employees <?php echo $select_locations['location_name'] !=""?"(".ucwords($select_locations['location_name'])." Only)":""; ?></h5>
                <div class="card-header-right">
                    <button type="button" onclick="insertEmployeePopUp()" class="btn btn-success mb-2 desktop-view" ><i class="icofont icofont-plus icon-white "></i> Add Employee</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row">


                <div class="col-lg-3 form-group  w-full">
                    <span class="form-label">Employee</span>
                     <select aria-label="Emp" class="js-example-basic-single" name="emp_id" onchange="selectSpaceallocatedemployee(this.value)">
                         <option value="">All Employees</option>
                        <?php foreach($result['select_employees'] as $emp){ ?>
                        <option value="<?php echo $emp['id']; ?>" <?php echo $emp['id']==$_POST['emp_id']?"selected":""; ?>><?php echo $emp['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-10"></div>

            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header table-card-header ">
                    <h5>Allocated Employees </h5>
                    <div class="card-header-right">
                        <span class="form-label label label-md label-primary"><?php echo count((array)$allocated_employees); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)$allocated_employees) !=0){ ?>
                    <div class="row custom-scroll" style="max-height: 600px; min-height: 600px;">
                    <?php $sq = 1;
                        foreach($allocated_employees as $eqa_detail){
                            ?>
                        <div class="col-lg-6 spaceallocated_allemp" id="spaceallocated_emp<?php echo $eqa_detail['id']; ?>">
                            <div class="card ">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="align-middle border-bottom">
                                            <?php if($eqa_detail['image'] !="") { ?>
                                                <img src="uploads/all/<?php echo $eqa_detail['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($eqa_detail['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <h6 class="mb-0 custom-limit-text text-primary cursor-pointer" style="--linehide: 1;" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Employee Details" onclick="insertEmployeePopUp('<?php echo $eqa_detail['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo $eqa_detail['display_name']; ?> <i class="ti-pencil f-12"></i></h6>
                                                <p class="m-b-0 custom-limit-text" style="--linehide: 1;"><?php echo $eqa_detail['emp_code']; ?></p>
                                                <p class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $eqa_detail['email_id']; ?></p>
                                            </div>
                                        </div>
                                        <p class="m-b-0 f-w-600 custom-limit-text mt-2" style="--linehide: 1;"><i class="ti-location-pin"></i>  <?php echo $allocated_emp_spaces[$eqa_detail['id']]['building_name']; ?></p>
                                        <div class="col-lg-6 text-left">
                                            <span class="f-w-600 d-block">Floor</span>
                                            <?php echo $allocated_emp_spaces[$eqa_detail['id']]['floor_name']; ?></div>
                                        <div class="col-lg-6 text-left">
                                            <span class="f-w-600 d-block">Cubicle Space</span>
                                            <?php echo $allocated_emp_spaces[$eqa_detail['id']]['cabin_name']; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Allocated Employees</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header table-card-header ">
                    <h5>Un-Allocated Employees</h5>
                    <div class="card-header-right">
                         <span class="form-label label label-md label-primary"><?php echo count((array)$unallocated_employees); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)$unallocated_employees) !=0){ ?>
                    <div class="row custom-scroll" style="max-height: 600px; min-height: 600px;">
                    <?php $sq = 1;
                        foreach($unallocated_employees as $empdetail){
                            ?>
                        <div class="col-lg-6 spaceallocated_allemp" id="spaceallocated_emp<?php echo $empdetail['id']; ?>">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="align-middle">
                                            <?php if($empdetail['image'] !="") { ?>
                                                <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($empdetail['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <h6 class="mb-0 custom-limit-text text-primary cursor-pointer" style="--linehide: 1;" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Employee Details" onclick="insertEmployeePopUp('<?php echo $empdetail['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><?php echo $empdetail['display_name']; ?> <i class="ti-pencil f-12"></i></h6>
                                                <p class="m-b-0 custom-limit-text" style="--linehide: 1;"><?php echo $empdetail['emp_code']; ?></p>
                                                <p class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $empdetail['email_id']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Un-Allocated Employees</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

<?php }elseif($_POST['pagetype'] =="IEM"){
    $buildings = $result['buildings'];
    $building_floors = $result['building_floors'];
    $equipments = $result['equipments'];
    $equipment_allocations = $result['equipment_allocations'];
    $allocated_employees = $unallocated_employees = array();

    foreach($result['employees_list'] as $emp){
        if($equipment_allocat_with_employee_id[$emp['id']] =="Y"){
            $allocated_employees[] = $emp;
        }else{
            $unallocated_employees[] = $emp;
        }
    }
    ?>
    <div class="card">
        <div class="table-card-header">
            <div class="card-header">
                <h5>Employees</h5>
                <div class="card-header-right">
                    <button type="button" onclick="insertEmployeePopUp()" class="btn btn-success mb-2 desktop-view" ><i class="icofont icofont-plus icon-white "></i> Add Employee</button>
                </div>
            </div>
        </div>
        <form id="form_IEM">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-2"></div>
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label">Building</span>
                     <select aria-label="Building" class="js-example-basic-single" name="building_id">
                         <option value="">All Buildings</option>
                        <?php foreach($buildings as $stytpe){ ?>
                        <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['building_id']?"selected":""; ?>><?php echo $stytpe['building_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label">Building Floor</span>
                      <select aria-label="Building Floor" class="js-example-basic-single" name="building_floor_id">
                          <option value="">All Building Floor</option>
                        <?php foreach($building_floors as $stytpe){ ?>
                        <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['building_floor_id']?"selected":""; ?>><?php echo $stytpe['floor_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label">Equipment</span>
                     <select aria-label="Equipment" class="js-example-basic-single" name="equipment_id">
                         <option value="">All Equipments</option>
                        <?php foreach($equipments as $stytpe){ ?>
                        <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['equipment_id']?"selected":""; ?>><?php echo $stytpe['equipment_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label">Employee</span>
                     <select aria-label="Emp" class="js-example-basic-single" name="emp_id">
                         <option value="">All Employees</option>
                        <?php foreach($employees as $emp){ ?>
                        <option value="<?php echo $emp['id']; ?>" <?php echo $emp['id']==$_POST['emp_id']?"selected":""; ?>><?php echo $emp['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 text-right">
                    <button type="button" class="btn btn-primary mb-2 desktop-view mt-4" onclick="getBuildingDynamicPage('IEM')"><i class="ti-search"></i> Search</button>
                </div>
            </div>
        </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header table-card-header ">
                    <h5>Seat Allocated Employees </h5>
                    <div class="card-header-right">
                        <span class="form-label label label-md label-primary"><?php echo count((array)$equipment_allocations); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)$equipment_allocations) !=0){ ?>
                    <div class="row custom-scroll" style="max-height: 500px;">
                    <?php $sq = 1;
                        foreach($equipment_allocations as $eqa_detail){
                            ?>
                            <div class="card col-lg-6">
                                <div class="card-body">
                                    <div class="row border-bottom">
                                        <div class="align-middle">
                                            <?php if($eqa_detail['image'] !="") { ?>
                                                <img src="uploads/all/<?php echo $eqa_detail['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($eqa_detail['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <h6 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $eqa_detail['display_name']; ?></h6>
                                                <p class="m-b-0 custom-limit-text" style="--linehide: 1;"><?php echo $eqa_detail['emp_code']; ?></p>
                                                <p class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $eqa_detail['email_id']; ?></p>
                                                <p class="m-b-0 f-12 f-w-600 custom-limit-text" style="--linehide: 1;"><?php echo $eqa_detail['cabin_name']; ?></p>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-center">


                                        <i class="ti-user cursor-pointer " data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Employee Details" onclick="insertEmployeePopUp('<?php echo $eqa_detail['empp_id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="ti-location-pin cursor-pointer m-l-30" onclick="insertEquipmentAllocation('<?php echo $eqa_detail['building_floor_id']; ?>','<?php echo $eqa_detail['building_floor_space_id']; ?>','<?php echo $eqa_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Allocation" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="ti-eye cursor-pointer m-l-30" onclick="ShowBuildingFloors('<?php echo $eqa_detail['building_floor_id']; ?>','<?php echo $eqa_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Employee Location" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    </div>
                                </div>
                            </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Allocated Employees</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header table-card-header ">
                    <h5>Seat Un-Allocated Employees</h5>
                    <div class="card-header-right">
                         <span class="form-label label label-md label-primary"><?php echo count((array)$unallocated_employees); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)$unallocated_employees) !=0){ ?>
                    <div class="row custom-scroll" style="max-height: 500px;">
                    <?php $sq = 1;
                        foreach($unallocated_employees as $empdetail){
                            ?>
                            <div class="card col-lg-6">
                                <div class="card-body">
                                    <div class="row border-bottom">
                                        <div class="align-middle">
                                            <?php if($empdetail['image'] !="") { ?>
                                                <img src="uploads/all/<?php echo $empdetail['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($empdetail['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <h6 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $empdetail['display_name']; ?></h6>
                                                <p class="m-b-0 custom-limit-text" style="--linehide: 1;"><?php echo $empdetail['emp_code']; ?></p>
                                                <p class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $empdetail['email_id']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-center">


                                        <i class="ti-user cursor-pointer " data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Employee Details" onclick="insertEmployeePopUp('<?php echo $empdetail['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="ti-location-pin cursor-pointer m-l-30" onclick="setLocationEmployee('<?php echo $empdetail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Set Employee Location" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    </div>
                                </div>
                            </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Un-Allocated Employees</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

<?PHP  ?>


<?php }elseif($_POST['pagetype'] =="B"){  ?>
<?php
    $buildings = $result['buildings'];
    $building_floors = $result['building_floors'];
    $building_floor_spaces_cnt = $result['building_floor_spaces_cnt'];


    ?>
<div class="card">
    <div class="card-header mb-2">
        <h5>Buildings</h5>
        <div class="card-header-right">
            <button type="button" class="btn btn-success mb-2 desktop-view"  onclick="getBuildingDetails()"><i class="icofont icofont-plus icon-white "></i> Add Building</button>
        </div>
    </div>
</div>
<?php if(count((array)$buildings) !="0"){ ?>
<div class="owl-carousel carousel-box owl-theme">
    <?php foreach($buildings as $buld){  ?>
    <div id="custom-popup" >
        <div class="card cursor-pointer emptycss_building_selectio" id="building_selection_<?php echo $buld['id']; ?>" >
            <div class="card-header pb-2">
                <h5 class="custom-limit-text" style="--linehide: 1;"><?php echo $buld['building_name']; ?></h5>
                <p class="f-w-600 text-muted f-12 custom-limit-text" style="--linehide: 1;"><?php echo $buld['building_address']; ?></p>

                <div class="card-header-right">
                    <div class="dropdown-primary dropdown open">
                        <span class="" id="dropdown-2_2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="ti-more-alt"></i></span>
                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                            <button type="button" class="dropdown-item waves-light waves-effect"  onclick="getBuildingDetails('<?php echo $buld['id']; ?>')">Manage Building & Floors</button>

                            <div class="dropdown-divider"></div>


                             <button type="button" class="dropdown-item waves-light waves-effect"  onclick="showPopup('bulding_floors_<?php echo $buld['id']; ?>','MFSNEW')">Manage Cubicle Space</button>

                            <button type="button" class="dropdown-item waves-light waves-effect"  onclick="getEquipemntsWithSeat('CSA_ALLOCATION','<?php echo $buld['id']; ?>')">Allocate Cubicle Space</button>

                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="showPopup('bulding_floors_<?php echo $buld['id']; ?>','VCSPACE')">View Cubicle Spaces</button>
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="showPopup('bulding_floors_<?php echo $buld['id']; ?>','ASSETALLOCATION')">Asset Allocation</button>

                            <button type="button" class="dropdown-item waves-light waves-effect" onclick="showPopup('bulding_floors_<?php echo $buld['id']; ?>','VIEWASSETALLOCATION')">View Assets</button>


                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="d-flex align-items-center mb-4">
                    <div class="w-80px flex-shrink-0 me-2">
                        <img src="uploads/all/<?php echo $buld['building_image']; ?>" class="img-radius align-top m-r-10" alt="Attachment Preview">
                    </div>
                    <div class="m-0">
                        <h6 class="fw-bold mb-3 ">Building Details</h6>
                        <div class="d-flex d-grid gap-3">
                            <div class="d-flex flex-column flex-shrink-0 me-1">
                                <span class="d-flex align-items-center fs-7 fw-bold text-muted mb-2">
                                    <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['floor_cnt']; ?> Floors</span>
                                </span>
                                <span class="d-flex align-items-center text-muted fw-bold fs-7">
                                    <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['building_floor_space_cnt']; ?> Spaces</span>
                                </span>
                            </div>
                            <div class="d-flex flex-column flex-shrink-0">
                                <span class="d-flex align-items-center fs-7 fw-bold text-muted mb-2">
                                    <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['usedspace_cnt']; ?> Allocated</span>
                                </span>
                                <span class="d-flex align-items-center text-muted fw-bold fs-7">
                                    <span class="form-label badge bg-gray p-1 mb-0 m-r-5"><i class="ti-angle-right text-muted"></i></span> <span class="text-muted f-12"><?php echo $buld['building_floor_space_cnt']-$buld['usedspace_cnt']; ?> Free</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="m-0 text-center">
                    <button type="button" class="btn btn-success btn-light me-2 mb-2 f-14" onclick="getEquipemntsWithSeat('CSA_ALLOCATION','<?php echo $buld['id']; ?>')">Cubicle Allocation </button>

                </div>
            </div>
        </div>
        <div class="custom-popup-open bulding_floors_<?php echo $buld['id']; ?> text-start" style="top: -20px; display: none;z-index: 999;">
              <div class="card">
                  <div class="card-header">
                      <h5>Floors <span class="form-label label label-md label-primary"><?php echo count((array)$building_floors[$buld['id']]); ?></span></h5>
                      <div class="card-header-right">
                        <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closedPopup()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                      </div>
                </div>
                <div class="card-body custom-scroll" style="max-height: 130px;">
                    <?php
                    if(count((array)$building_floors[$buld['id']]) !="0"){
                         foreach($building_floors[$buld['id']] as $bfloor){ ?>
                        <div class="card bg-white" >
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <img src="uploads/building_floors/<?php echo $bfloor['floor_image']; ?>" class="img-100" alt="Attachment Preview">
                                    </div>
                                    <div class="col-lg-5">
                                        <h5 class="custom-limit-text" style="--linehide: 1;"><?php echo $bfloor['floor_name']; ?></h5>
                                        <p class="custom-limit-text" style="--linehide: 1;"><span>Spaces : </span><?php echo customNanAndEmptyValidation($building_floor_spaces_cnt[$bfloor['id']]); ?> </p>
                                    </div>

                                    <div class="col-lg-2">
                                        <i class="fa fa-edit text-primary mb-2 hidebuildingmenu buildingmenu_MFS" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Manage Cubicle Space"  onclick="getBuildingFoolSpaceDetails('<?php echo $bfloor['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="fa fa-edit text-primary mb-2 hidebuildingmenu buildingmenu_MFSNEW" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Manage Cubicle Space"  onclick="getBuildingFoolSpaceDetailsNew('<?php echo $bfloor['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="ti-widgetized text-primary hidebuildingmenu buildingmenu_AE" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Allocate Equipments" onclick="insertEquipmentAllocation('<?php echo $bfloor['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="ti-user text-primary hidebuildingmenu buildingmenu_AS" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Allocate Seats" onclick="insertEquipmentAllocation('<?php echo $bfloor['id']; ?>','','','','','','','AS')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="fa fa-eye mt-2 text-primary hidebuildingmenu  buildingmenu_VFD" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Floor Details" onclick="ShowBuildingFloors('<?php echo $bfloor['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="fa fa-eye mt-2 text-primary hidebuildingmenu  buildingmenu_VCSPACE" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Cubicle Spaces" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $bfloor['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="fa fa-eye mt-2 text-primary hidebuildingmenu  buildingmenu_ASSETALLOCATION" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Insert Asset Allocations" onclick="insertAssetsAllocation('','<?php echo $bfloor['id']; ?>','')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <i class="fa fa-eye mt-2 text-primary hidebuildingmenu  buildingmenu_VIEWASSETALLOCATION" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="View Asset Allocations" onclick="ShowAssetsLocations('<?php echo $bfloor['id']; ?>','')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Floors</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                        </div>
                    </div>
                <?php } ?>
                 </div>
             </div>
        </div>
    </div>
<?php } ?>
</div>

<div class="booking-seat"></div>
 <?php }else{  ?>
     <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Buildings</p>
        <div class="text-center px-4">
            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
        </div>
    </div>
 <?php } ?>
<script type="text/javascript">
    $(document).ready(function() {
        setTimeout(function(){
        $('.carousel-box').owlCarousel({
            items:4,
            loop:false,
            autoplay:false,
            margin: 20,
            nav:true
        });
        },350);
    });
</script>
<script type="text/javascript" src="assets/bower_components/owl.carousel/js/owl.carousel.min.js"></script>
<script type="text/javascript" src="assets/js/owl-custom.js"></script>

<?php
}elseif($_POST['pagetype'] =="ASSETS"){
    $asset_categories = $result['asset_categories'];
    $equipment_types = $result['equipment_types'];
    $assets = $result['assets'];

    $equipment_allocat_with_equipment_id = array();
    foreach($result['asset_allocations'] as $equiall){
        $equipment_allocat_with_equipment_id[$equiall['asset_id']] = $equiall;
    }
    $allocated_equipments = $unallocated_equipments = $unlocated_equipments = array();
    foreach($assets as $equip){
        $equdetailary = $equipment_allocat_with_equipment_id[$equip['id']];
        if($equdetailary['id'] !=""){
            $allocated_equipments[] = $equip;
        }else{
            $unlocated_equipments[] = $equip;
        }
    }
    ?>
    <div class="card">
        <form id="form_ASSETS">
            <div class="card-header table-card-header">
                <h5>Assets</h5>
                <div class="card-header-right">
                    <button type="button" class="btn btn-success mb-2 desktop-view"  onclick="AddAssets()"><i class="icofont icofont-plus icon-white "></i> Add Assets</button>

                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6"></div>
                    <div class="col-lg-2 form-group  w-full">
                        <span class="form-label">Category</span>
                         <select aria-label="Equipment Category" class="js-example-basic-single" name="equipment_category_id">
                             <option value="">All Categories</option>
                            <?php foreach($asset_categories as $stytpe){ ?>
                            <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['equipment_category_id']?"selected":""; ?>><?php echo $stytpe['category_description']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-2 form-group  w-full">
                        <span class="form-label">Assets</span>
                          <select aria-label="Equipment Type" class="js-example-basic-single" name="equipment_type_id">
                              <option value="">All Assets</option>
                            <?php foreach($assets as $stytpe){ ?>
                            <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['equipment_type_id']?"selected":""; ?>><?php echo $stytpe['asset_description']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-lg-2 text-right">
                        <button type="button" class="btn btn-primary mb-2 desktop-view mt-4" onclick="getBuildingDynamicPage('ASSETS')"><i class="ti-search"></i> Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Allocated Assets</h5>
                    <div class="card-header-right">
                        <span class="form-label label label-md label-success"><?php echo count((array)$allocated_equipments); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(count((array)$allocated_equipments) !=0){ ?>
                    <div class="row">
                    <?php
                        $sq = 1;
                        foreach($allocated_equipments as $equip){
                            $equipment_allocat_detail = $equipment_allocat_with_equipment_id[$equip['id']];
                             ?>
                        <div class="card col-lg-12">
                            <div class="card-body">
                                <div class="row border-bottom">
                                    <div class="align-middle">
                                        <?php if($equip['asset_img'] !="") { ?>
                                            <img src="uploads/equipments/<?php echo $equip['asset_img']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($equip['asset_description']); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <h6 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $equip['asset_description']; ?></h6>
                                            <span class="m-b-0 f-14 custom-limit-text" style="--linehide: 1;"><?php echo $equip['asset_code']; ?></span>
                                            <span class="m-b-0 f-14 custom-limit-text" style="--linehide: 1;"><?php echo $asset_categories[$equip['category_id']]['category_description']; ?></span>

                                        </div>
                                    </div>
                                </div>
                                <div class="text-center pt-2">
                                    <i class="ti-pencil-alt cursor-pointer m-r-30" onclick="AddAssets('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Details" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                     <i class="ti-location-pin m-0 m-r-30 cursor-pointer" onclick="insertAssetsAllocation('','<?php echo $equipment_allocat_detail['building_floor_id']; ?>','<?php echo $equipment_allocat_detail['asset_id']; ?>','<?php echo $equipment_allocat_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Allocation" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-eye cursor-pointer m-l-30" onclick="ShowAssetsLocations('<?php echo $equipment_allocat_detail['building_floor_id']; ?>','<?php echo $equipment_allocat_detail['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Location" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>


                                    <i class="ti-time cursor-pointer m-l-30" onclick="showEquipmentLogs('<?php echo $equipment_allocat_detail['asset_id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Logs" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Allocated Assets</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5>Un-Allocated Assets</h5>
                    <div class="card-header-right">
                        <span class="form-label label label-md label-danger"><?php echo count((array)$unlocated_equipments); ?></span>
                    </div>
                </div>
                 <div class="card-body">
                     <?php if(count((array)$unlocated_equipments) !=0){ ?>
                    <div class="row">
                    <?php
                        $sq = 1;
                        foreach($unlocated_equipments as $equip){
                        ?>
                        <div class="card col-lg-12">
                            <div class="card-body">
                                <div class="row border-bottom">
                                    <div class="align-middle">
                                        <?php if($equip['asset_img'] !="") { ?>
                                            <img src="uploads/equipments/<?php echo $equip['asset_img']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                        <?php } else { ?>
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($equip['asset_description']); ?></span>
                                        <?php } ?>
                                        <div class="d-inline-block width73">
                                            <h6 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $equip['asset_description']; ?></h6>
                                            <span class="m-b-0 f-14 custom-limit-text" style="--linehide: 1;"><?php echo $equip['asset_code']; ?></span>
                                               <span class="m-b-0 f-14 custom-limit-text" style="--linehide: 1;"><?php echo $asset_categories[$equip['category_id']]['category_description']; ?></span>

                                        </div>
                                    </div>
                                </div>
                                <div class="text-center pt-2">
                                    <i class="ti-pencil-alt cursor-pointer m-r-30" onclick="AddAssets('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Edit Details" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-location-pin m-0 cursor-pointer" onclick="setLocationAssets('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Set Location" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                    <i class="ti-time cursor-pointer m-l-30" onclick="showEquipmentLogs('<?php echo $equip['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="View Logs" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Assets</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

<?php }elseif($_POST['pagetype'] =="DETAILREPORT"){

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
                $allocemp++;
            }elseif($fspaceres['reserve_pool_id'] !=""){
                $reserve_poolcnt++;
            }else{
                $freespacecnt++;
            }
            $space_cnt++;
        }
    }
}
?>
<script type="text/javascript">
    function getDetailReportsAjax(){
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#detailreportid').html(htmloader);
        formdata = $("#form_detailreport").serialize();
        jQuery.ajax({
            url: "views/custom_view_equipment_allocation_detail_reports.php",
            data: formdata+"&ajaxpagecallll=Y&pagetype=<?php echo $_POST['pagetype']; ?>",
            type: "POST",
            success:function(data){
                $('#detailreportid').html(data);
                $('.tooltip').remove();
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    }
    function getfloorslist(building_id){
        console.log(building_id);
        jQuery.ajax({
            url: "ajax.php",
            data: "&building_id="+building_id+"&page=getbuildingoptionlist",
            type: "POST",
            success:function(data){
                $('#detailreportfoolr').html(data);            }
        });
    }
</script>
<div class="card">
    <div class="card-header">
        <h5>Detail Reports</h5>
        <div class="card-header-right">
            <div class="d-inline-block m-l-10">
                  <span class="form-label label label-lg label-warning"><?php echo $space_cnt; ?></span>
                  <span class="f-12">Total Spaces</span>
            </div>
            <div class="d-inline-block m-l-10">
                  <span class="form-label label label-lg label-success"><?php echo $reserve_poolcnt; ?></span>
                  <span class="f-12">Allocated Pool</span>
            </div>
            <div class="d-inline-block m-l-10">
                  <span class="form-label label label-lg label-primary text-dark" style="background-color: #e4e4e4"><?php echo $allocemp; ?></span>
                  <span class="f-12 ">Allocated Emp</span>
            </div>
            <div class="d-inline-block m-l-10 m-l-20">
                <span class="form-label label label-lg text-dark" style="border: 1px black solid;"><?php echo $freespacecnt; ?></span>
                  <span class="f-12">Free</span>
            </div>
            <div class="d-inline-block m-l-10 m-l-20">
                <a href="custom_view_download.php?page=Space Management Report" class="btn btn-inverse mb-2 desktop-view">
                <i class="ti-export icon-white"></i> Export </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form id="form_detailreport">
            <div class="row">
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label d-block">Building</span>
                     <select aria-label="Building" class="js-example-basic-single" name="building_id" onchange="getfloorslist(this.value)">
                         <option value="">All Buildings</option>
                        <?php foreach($buildings_report as $stytpe){ ?>
                        <option value="<?php echo $stytpe['id']; ?>"><?php echo $stytpe['building_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label d-block">Building Floor</span>
                      <select aria-label="Building Floor" class="js-example-basic-single" name="building_floor_id" id="detailreportfoolr">
                          <option value="">All Building Floor</option>
                        <?php foreach($buildings_report as $buldd){ ?>
                            <optgroup label="<?php echo $buldd['building_name']; ?>" >
                            <?php    $buildingwithfloors = $building_with_floors[$buldd['id']];
                                foreach($buildingwithfloors as $floors){
                         ?>
                        <option value="<?php echo $floors['id']; ?>" ><?php echo $floors['floor_name']; ?></option>
                        <?php } ?>
                        </optgroup>
                    <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label d-block">Space Types</span>
                     <select aria-label="Space Type" class="js-example-basic-single" name="space_type_id">
                         <option value="">All Space Types</option>
                        <?php foreach($space_types as $keyid=>$sptype){ ?>
                        <option value="<?php echo $keyid; ?>"><?php echo $sptype; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 form-group  w-full">
                    <span class="form-label d-block">Reserve Pool</span>
                     <select aria-label="Seating Pool" class="js-example-basic-single" name="seating_pool_id">
                         <option value="">All Reserve Pool</option>
                        <?php foreach($seating_pools as $poll){ ?>
                        <option value="<?php echo $poll['id']; ?>"><?php echo $poll['pool_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-2 text-right">
                    <button type="button" class="btn btn-primary mb-2 desktop-view mt-4" onclick="getDetailReportsAjax()"><i class="ti-search"></i> Search</button>
                </div>
            </div>
        </form>
        <div class="table-responsive mt-3" id="detailreportid">
            <?php include_once 'custom_view_equipment_allocation_detail_reports.php'; ?>
        </div>
    </div>
</div>

<?php }elseif($_POST['pagetype'] =="REPORT"){
$buildings_report = $result['buildings_report'];
$building_floors_report = $result['building_floors_report'];

$building_floors_report = $result['building_floors_report'];
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

$seating_pools = $result['seating_pools'];
foreach($buildings_report as $buldd){
$buildingwithfloors = $building_with_floors[$buldd['id']];
    foreach($buildingwithfloors as $floors){
        $space_cnt = $allocemp = $allocares = 0;
        foreach($floors_with_spaces[$floors['id']] as $fspaceres){
            if($fspaceres['emp_id'] !=""){
                $allocemp++;
            }elseif($fspaceres['reserve_pool_id'] !=""){
                $allocares++;
            }
            $space_cnt++;
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h5>Building Reports</h5>
        <div class="card-header-right">
        </div>
    </div>
    <div class="card-body">
        <?php if(count((array)$buildings_report) !="0"){ ?>
        <div class="card-block accordion-block color-accordion-block itemcategory_sec">
            <div class="color-accordion">
                <?php foreach($buildings_report as $buldd){
                        $total_cubicle_space_list = $total_emp_cubicle_space = $total_reserve_cubicle_space = array();
                        $floorsdemo = $building_with_floors[$buldd['id']];
                        foreach($floorsdemo as $floordeatils){
                            $cubicle_spaces1 = $floors_with_spaces[$floordeatils['id']];
                            foreach($cubicle_spaces1 as $cspace){
                                if($cspace['emp_id'] !=""){
                                    $total_emp_cubicle_space[] = $cspace;
                                }elseif($cspace['reserve_pool_id'] !=""){
                                    $total_reserve_cubicle_space[] = $cspace;
                                }else{
                                    $total_cubicle_space_list[] = $cspace;
                                }
                            }
                        }
                 ?>
                <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray pb-3">
                    <div class="row">
                        <div class="col-lg-6">
                            <?php echo $buldd['building_name']; ?> <span class="f-10 d-block"><i class="ti-location-pin"></i> <?php echo $buldd['building_address']; ?></span>
                        </div>
                        <div class="col-lg-6">
                            <div class="text-right">
                                  <div class="d-inline-block m-l-10">
                                  <span class="form-label label label-lg label-warning"><?php echo count((array)$total_reserve_cubicle_space); ?></span>
                                  <span class="f-12">Reserved</span>
                                </div>
                                <div class="d-inline-block m-l-10">
                                  <span class="form-label label label-lg label-primary text-dark" style="background-color: #e4e4e4"><?php echo count((array)$total_emp_cubicle_space); ?></span>
                                  <span class="f-12 ">Allocated</span>
                                </div>
                                <div class="d-inline-block m-l-10 m-l-20">
                                    <span class="form-label label label-lg text-dark" style="border: 1px black solid;"><?php echo count((array)$total_cubicle_space_list); ?></span>
                                  <span class="f-12">Free</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
                <div class="accordion-desc p-0">
                    <?php
                        $floors = $building_with_floors[$buldd['id']];
                        foreach($floors as $floordeatils){
                            $cubicle_spaces = $floors_with_spaces[$floordeatils['id']];
                            $cubicle_space_list = $emp_cubicle_space = $reserve_cubicle_space = array();
                            foreach($cubicle_spaces as $cspace){
                                if($cspace['emp_id'] !=""){
                                    $emp_cubicle_space[] = $cspace;
                                }elseif($cspace['reserve_pool_id'] !=""){
                                    $reserve_cubicle_space[] = $cspace;
                                }else{
                                    $cubicle_space_list[] = $cspace;
                                }
                            }
                    ?>
                    <div class="card-block accordion-block color-accordion-block">
                        <div class="color-accordion" >
                            <a class="accordion-msg b-none waves-effect waves-light f-w-600 bg-gray pb-3"><?php echo $floordeatils['floor_name']; ?>
                            <div class="float-end">
                              <div class="d-inline-block m-l-10">
                                  <span class="form-label label label-lg label-warning"><?php echo count((array)$reserve_cubicle_space); ?></span>
                                  <span class="f-12">Reserved</span>
                                </div>
                                <div class="d-inline-block m-l-10">
                                  <span class="form-label label label-lg label-primary text-dark" style="background-color: #e4e4e4"><?php echo count((array)$emp_cubicle_space); ?></span>
                                  <span class="f-12 ">Allocated</span>
                                </div>
                                <div class="d-inline-block m-l-10 m-l-20">
                                    <span class="form-label label label-lg text-dark" style="border: 1px black solid;"><?php echo count((array)$cubicle_space_list); ?></span>
                                  <span class="f-12">Free</span>
                                </div>
                            </div>

                            </a>
                            <div class="accordion-desc p-0">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="card new-cust-card emptycss_building_selectio" id="building_selection_3">
                                            <div class="card-header">
                                                <h5>Reserved Spaces</h5>
                                                <div class="card-header-right">
                                                    <span class="form-label label label-lg label-warning"><?php echo count((array)$reserve_cubicle_space); ?></span>

                                                </div>
                                            </div>
                                            <div class="card-body pt-0 custom-scroll" style="max-height: 400px; min-height: 400px;">
                                                <?php if(count((array)$reserve_cubicle_space) != 0){ ?>
                                                <table class="table table-hover m-b-0" style="font-size:13px;">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Cubicle</th>
                                                            <th scope="col">Reserved</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($reserve_cubicle_space as $rcs){ ?>
                                                        <tr>
                                                            <td class="p-2"><?php echo $rcs['cabin_name']; ?></td>
                                                            <td class="p-2"><?php echo $seating_pools[$rcs['reserve_pool_id']]['pool_name']; ?></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <?php }else{ ?>
                                                    <div class="card-px text-center py-20 m-t-30">
                                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Reserved Cubicle Spaces</p>
                                                        <div class="text-center px-4">
                                                            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="card new-cust-card emptycss_building_selectio" id="building_selection_3_2">
                                            <div class="card-header">
                                                <h5>Allocated Spaces</h5>
                                                <div class="card-header-right">
                                                    <span class="form-label label label-lg label-primary text-dark" style="background-color: #e4e4e4"><?php echo count((array)$emp_cubicle_space); ?></span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 custom-scroll" style="max-height: 400px; min-height: 400px;">
                                                <?php if(count((array)$emp_cubicle_space) != 0){ ?>
                                                <table class="table table-hover m-b-0" style="font-size:13px;">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Cubicle</th>
                                                            <th scope="col">Employee Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($emp_cubicle_space as $cs){ ?>
                                                        <tr>
                                                            <td class="p-2"><?php echo $cs['cabin_name']; ?></td>
                                                            <td class="p-2"><?php echo $employees_list[$cs['emp_id']]['display_name']; ?></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <?php }else{ ?>
                                                    <div class="card-px text-center py-20 m-t-30">
                                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Employee Cubicle Spaces</p>
                                                        <div class="text-center px-4">
                                                            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="card new-cust-card emptycss_building_selectio" id="building_selection_3_3">
                                            <div class="card-header">
                                                <h5>Un-Allocated Spaces</h5>
                                                <div class="card-header-right">
                                                    <span class="form-label label label-lg text-dark" style="border: 1px black solid;"><?php echo count((array)$cubicle_space_list); ?></span>
                                                </div>
                                            </div>
                                            <div class="card-body pt-0 custom-scroll" style="max-height: 400px; min-height: 400px;">
                                                <?php if(count((array)$cubicle_space_list) != 0){ ?>
                                                <table class="table table-hover m-b-0" style="font-size:13px;">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Cubicle</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach($cubicle_space_list as $ccl){ ?>
                                                        <tr>
                                                            <td class="p-2"><?php echo $ccl['cabin_name']; ?></td>
                                                        </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                                <?php }else{ ?>
                                                    <div class="card-px text-center py-20 m-t-30">
                                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Cubicle Spaces</p>
                                                        <div class="text-center px-4">
                                                            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
                <?php } ?>

            </div>
        </div>
    <?php }else{ ?>
        <div class="card-px text-center py-20 m-t-30">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Buildings</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
            </div>
        </div>
    <?php } ?>
    </div>
</div>

<script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>

<?php }else{

    $buildings = $result['buildings'];
    $dash_cnt = $result['dash_cnt'];
    $seating_pools = $result['seating_pools'];
    $equipments_list =$result['equipments_list'];
    $employees_list =$result['employees_list'];
    $equipment_types = $result['equipment_types'];
    foreach($equipment_types as $et){
        $equipment_type[$et['id']] = $et;
    }

    $buildings_report = $result['buildings_report'];
    $building_floors_report = $result['building_floors_report'];
    $building_with_floors = array();
    foreach($building_floors_report as $bfr){
        $building_with_floors[$bfr['building_id']][] = $bfr;
    }
    $building_floor_spaces_report = $result['building_floor_spaces_report'];
    $floors_with_spaces = array();
    foreach($building_floor_spaces_report as $bfsr){
        $floors_with_spaces[$bfsr['building_floor_id']][] = $bfsr;
    }
    $building_floor_space_allocation = $result['building_floor_space_allocations'];
    $building_floor_space_allocations = array();
    foreach($building_floor_space_allocation as $bfsr){
        if($bfsr['current_status'] == "3"){
            $building_floor_space_allocations['PENDING'][] = $bfsr;
        }else{
            $building_floor_space_allocations['APPROVED'][] = $bfsr;
        }
    }

    $employees = $result['reporting_employee'];
    $employees_name_list = $empparentwithchild = array();
    foreach($employees as $empd){
        $employees_name_list[$empd['id']] = $empd;
        if($empd['reporting_manager_id'] =="" || $empd['reporting_manager_id'] =="0"){
            $empparentwithchild[0][] = $empd;
        }else{
            $empparentwithchild[$empd['reporting_manager_id']][] = $empd;
        }
    }
    $locationbdydashboard = $result['locationbdydashboard'];

    $empwithallocationposition = $result['empwithallocationposition'];

?>

    <link rel="stylesheet" type="text/css" href="assets/bower_components/jstree/css/style.min.css">
    <link rel="stylesheet" type="text/css" href="assets/pages/treeview/treeview.css">

<script type="text/javascript" src="assets/bower_components/jstree/js/jstree.min.js"></script>
<script type="text/javascript" src="assets/pages/treeview/jquery.tree.js"></script>
<?php

    if($_SESSION['working_role_short_code'] == SPACE_LOCATION_ADMIN_ROLE){ ?>

        <div class="row">
            <div class="col-xl-3">
                <div class="card ticket-card">
                    <div class="card-body">
                        <p class="m-b-30 bg-c-blue  lbl-card"><i class="fas fa-folder-open"></i> All Employees</p>
                        <div class="text-center">
                            <h2 class="m-b-0 d-inline-block text-c-blue "><?php echo $locationbdydashboard['all_emp_cnt']!=""?$locationbdydashboard['all_emp_cnt']:"0"; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card ticket-card">
                    <div class="card-body">
                        <p class="m-b-30 bg-c-green  lbl-card"><i class="fas fa-folder-open"></i> Allocated Employee</p>
                        <div class="text-center">
                            <h2 class="m-b-0 d-inline-block text-c-green "><?php echo $locationbdydashboard['alocated_emp_cnt'] !=""?$locationbdydashboard['alocated_emp_cnt']:"0"; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card ticket-card">
                    <div class="card-body">
                        <p class="m-b-30 bg-c-yellow  lbl-card"><i class="fas fa-folder-open"></i> Un-Allocated Employee</p>
                        <div class="text-center">
                            <h2 class="m-b-0 d-inline-block text-c-yellow "><?php echo $locationbdydashboard['all_emp_cnt']-$locationbdydashboard['alocated_emp_cnt']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card" >
                    <div class="card-header table-card-header ">
                        <h5>Search Employee Locations</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body" >
                        <div class="col-lg-12">
                            <div class="input-group input-group-button mb-0">
                                <input type="text" class="form-control form-control-sm width1" placeholder="Enter Minimum 3 Characters" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search" onkeyup="getSearchEmployeeLocations(this.value)">
                                <div class="input-group-text addon-style">
                                    <span class=""><i class="ti-search text-white mt-2"></i></span>
                                </div>
                            </div>
                        </div>
                        <div id="search_result_employee" class="mt-2 custom-scroll" style=" min-height: 160px; max-height: 160px; display: none">

                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card" style=" min-height: 500px; max-height: 500px;">
                    <div class="card-header table-card-header ">
                        <h5>Approval Pending</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <?php if(count((array)$building_floor_space_allocations['PENDING']) !=0){ ?>
                        <div class="row">
                        <?php $sq = 1;
                            foreach($building_floor_space_allocations['PENDING'] as $empd){
                                ?>
                            <div class="align-middle mt-3">
                                <?php if($employees_name_list[$empd['emp_id']]['image'] !="") { ?>
                                    <img src="uploads/all/<?php echo $empd['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees_name_list[$empd['emp_id']]['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <h7 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['display_name']; ?></h7>
                                   <p class="m-b-0 f-12  custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['email_id']; ?></p>
                                   <p class="m-b-0 f-12  custom-limit-text" style="--linehide: 1;"><?php echo $empd['emp_allocation_datetime']; ?></p>
                                   <button type="button" class="btn btn-primary btn-mini waves-effect waves-light" onclick="approvedSpaceAllocations('<?php echo $empd['id']; ?>')">Approve</button>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <?php }else{ ?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Approval Pending</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card" style=" min-height: 500px; max-height: 500px;">
                    <div class="card-header table-card-header ">
                        <h5>Approved</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <?php if(count((array)$building_floor_space_allocations['APPROVED']) !=0){ ?>
                        <div class="row">
                        <?php $sq = 1;
                            foreach($building_floor_space_allocations['APPROVED'] as $empd){
                                ?>
                            <div class="align-middle mt-3">
                                <?php if($employees_name_list[$empd['emp_id']]['image'] !="") { ?>
                                    <img src="uploads/all/<?php echo $empd['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees_name_list[$empd['emp_id']]['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <h7 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['display_name']; ?></h7>
                                   <p class="m-b-0 f-12  custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['email_id']; ?></p>

                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <?php }else{ ?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Approved</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Reporting Manager Tree</h5>
                    </div>
                    <div class="card-block custom-scroll" style=" min-height: 500px; max-height: 500px;">
                            <?php
if($_SESSION['admin_type']=='1'){
    $selecteduser = 0;
}else{
    $selecteduser = $_SESSION['userid'];
}
if(count((array)$empparentwithchild[$selecteduser]) != '0'){ ?>
                        <div class="card-block tree-view">
                            <div id="basicTree">
                                <ul>
                                    <?php
foreach($empparentwithchild[$selecteduser] as $empd){ ?>

<li data-jstree='{"opened":true}' class="<?php echo count((array)$empparentwithchild[$empd['id']]) !=0?"text-primary":""; ?>"><?php echo $empd['display_name']." - ".count((array)$empparentwithchild[$empd['id']]); ?>
    <ul>
     <?php foreach($empparentwithchild[$empd['id']] as $empd1){ ?>
         <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd1['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd1['id']]['display_name']." - ".count((array)$empparentwithchild[$empd1['id']]); ?>
             <ul>
             <?php foreach($empparentwithchild[$empd1['id']] as $empd2){ ?>
                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd2['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd2['id']]['display_name']." - ".count((array)$empparentwithchild[$empd2['id']]); ?>
                     <ul>
                     <?php foreach($empparentwithchild[$empd2['id']] as $empd3){ ?>
                         <li data-jstree='{"opened":false}'  class="<?php echo count((array)$empparentwithchild[$empd3['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd3['id']]['display_name']." - ".count((array)$empparentwithchild[$empd3['id']]); ?>
                             <ul>
                             <?php foreach($empparentwithchild[$empd3['id']] as $empd4){ ?>
                                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd4['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd4['id']]['display_name']." - ".count((array)$empparentwithchild[$empd4['id']]); ?>
                                     <ul>
                                     <?php foreach($empparentwithchild[$empd4['id']] as $empd5){ ?>
                                         <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd5['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd5['id']]['display_name']." - ".count((array)$empparentwithchild[$empd5['id']]); ?>
                                             <ul>
                                             <?php foreach($empparentwithchild[$empd5['id']] as $empd6){ ?>
                                                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd6['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd6['id']]['display_name']." - ".count((array)$empparentwithchild[$empd6['id']]); ?>
                                                     <ul>
                                                     <?php foreach($empparentwithchild[$empd6['id']] as $empd7){ ?>
                                                         <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd7['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd7['id']]['display_name']." - ".count((array)$empparentwithchild[$empd7['id']]); ?>
                                                             <ul>
                                                             <?php foreach($empparentwithchild[$empd7['id']] as $empd8){ ?>
                                                                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd8['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd8['id']]['display_name']." - ".count((array)$empparentwithchild[$empd8['id']]); ?>
                                                                 <ul>
                                                                 <?php foreach($empparentwithchild[$empd8['id']] as $empd9){ ?>
                                                                     <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd9['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd9['id']]['display_name']." - ".count((array)$empparentwithchild[$empd9['id']]); ?>
                                                                         <ul>
                                                                         <?php foreach($empparentwithchild[$empd9['id']] as $empd10){ ?>
                                                                             <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd10['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd10['id']]['display_name']." - ".count((array)$empparentwithchild[$empd10['id']]); ?>
                                                                             </li>
                                                                        <?php } ?>
                                                                        </ul>
                                                                     </li>
                                                                <?php } ?>
                                                                </ul>
                                                                 </li>
                                                            <?php } ?>
                                                            </ul>
                                                         </li>
                                                    <?php } ?>
                                                    </ul>
                                                 </li>
                                            <?php } ?>
                                            </ul>
                                         </li>
                                    <?php } ?>
                                    </ul>
                                 </li>
                            <?php } ?>
                            </ul>
                         </li>
                    <?php } ?>
                    </ul>
                 </li>
            <?php } ?>
            </ul>
         </li>
    <?php } ?>
    </ul>
</li>
<?php } ?>
                            </ul>
                        </div>
                    </div>
                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Reporting Manager</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>

    <?php }else{ ?>

<div class="row">
    <div class="col-lg-7">


        <?php foreach($buildings_report as $buldd){ ?>
            <?php
            $buildingwithfloors = $building_with_floors[$buldd['id']];
            $totalfllors = $totalspaces= $space_cnt = $allocemp = $allocares =$mngspace = $mngspaceallocation = $mngreservedspace = $inprogresscnt =  0;
            foreach($buildingwithfloors as $floors){
                foreach($floors_with_spaces[$floors['id']] as $fspaceres){
                    $totalspaces++;
                    if($fspaceres['space_type_id'] =="7"){
                        $mngspace++;
                        if($fspaceres['current_status'] ==ST_INPROGRESS){
                            $inprogresscnt++;
                        }elseif($fspaceres['emp_id'] !=""){
                            $mngspaceallocation++;
                        }elseif($fspaceres['reserve_pool_id'] !=""){
                            $mngreservedspace++;
                        }
                    }elseif($fspaceres['emp_id'] !=""){
                        $allocemp++;
                        $space_cnt++;
                    }elseif($fspaceres['reserve_pool_id'] !=""){
                        $allocares++;
                        $space_cnt++;
                    }else{
                        $space_cnt++;
                    }
                }
                $totalfllors++;
            }
             ?>
        <div class="card">
            <div class="row mt-4">
                <div class="col-2 text-center">
                    <span class="f-w-700 f-14"><?php echo $buldd['building_name']; ?></span>
                </div>
                <div class="col-2 text-center">
                    <div class="s-caption  text-primary"><i class="fa fa-bars f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo $totalfllors; ?></h5>
                        <p class="m-b-0 f-12">Floors</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-caption  text-danger"><i class="fa fa-cubes f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo $totalspaces; ?></h5>
                        <p class="m-b-0 f-12">Cubicle    </p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-caption  text-info"><i class="fa fa-cubes f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo $allocemp; ?></h5>
                        <p class="m-b-0  f-12">Allocation</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-caption  text-info"><i class="fa fa-cubes  f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo $allocares; ?></h5>
                        <p class="m-b-0 f-12">Resolve</p>
                    </div>
                </div>
                <div class="col-2 b-l-default text-center">
                    <div class="s-caption  text-success"><i class="fa fa-cubes f-20"></i></div>
                    <div class="s-cont d-inline-block">
                        <h5 class="f-w-700 m-b-0"><?php echo $space_cnt - ($allocemp+$allocares); ?></h5>
                        <p class="m-b-0 f-12">Available</p>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="card new-cust-card" style=" min-height: 500px; max-height: 500px;">
                    <div class="card-header">
                        <h5>Seating Pools</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body" >
                        <?php foreach($seating_pools as $sp){ ?>
                        <div class="card prod-p-card <?php echo $sp['bg_color']!=""?"":"card-blue"; ?>  " style="background-color: <?php echo $sp['bg_color']; ?>">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h6 class="m-b-5 text-white"><?php echo $sp['pool_name']; ?></h6>
                                        <h3 class="m-b-0 f-w-700 text-white f-18"><?php echo $sp['building_floor_space_allocation_cnt']; ?></h3>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fa fa-universal-access text-c-blue f-18"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card new-cust-card" style=" min-height: 500px; max-height: 500px;">
                    <div class="card-header">
                        <h5>Assets</h5>
                        <div class="card-header-right">
                            <button type="button" onclick="getBuildingDynamicPage('ASSETS')" class=" b-b-primary text-primary float-right" data-bs-toggle="tab" costcenter" role="tab" aria-selected="true">View all</button>

                        </div>
                    </div>
                    <div class="card-body" style="height: 300px;">
                    <?php foreach($equipments_list as $equp){ ?>
                        <div class="align-middle mt-3">
                            <span alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($equp['asset_description']); ?></span>
                            <div class="d-inline-block width73">
                                <h7 class="mb-0 custom-limit-text text-primary cursor-pointer" style="--linehide: 1;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button" onclick="AddAssets('<?php echo $equp['id']; ?>')"><?php echo $equp['asset_description']; ?> </h7>
                                <span class="m-b-0 f-12 custom-limit-text" style="--linehide: 1;"><?php echo $equp['asset_code'];  ?></span>
                               </div>
                        </div>
                    <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card" style=" min-height: 500px; max-height: 500px;">
                    <div class="card-header table-card-header ">
                        <h5>Employees </h5>
                        <div class="card-header-right">
                            <button type="button" onclick="getBuildingDynamicPage('BSEM')" class=" b-b-primary text-primary float-right" data-bs-toggle="tab" costcenter" role="tab" aria-selected="true">View all</button>
                        </div>
                    </div>
                    <div class="card-body" style="height: 300px;">
                        <?php if(count((array)$employees_list) !=0){ ?>
                        <div class="row">
                        <?php $sq = 1;
                            foreach($employees_list as $empd){
                                ?>
                            <div class="align-middle mt-3">
                                <?php if($empd['image'] !="") { ?>
                                    <img src="uploads/all/<?php echo $empd['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($empd['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <h7 class="mb-0 custom-limit-text " style="--linehide: 1;"><?php echo $empd['display_name']; ?></h7>
                                   <p class="m-b-0 f-12  custom-limit-text" style="--linehide: 1;"><?php echo $empd['email_id']; ?></p>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <?php }else{ ?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Allocated Employees</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card" style=" min-height: 500px; max-height: 500px;">
                    <div class="card-header table-card-header ">
                        <h5>Approval Pending</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body custom-scroll" style=" min-height: 437px; max-height: 437px;">
                        <?php if(count((array)$building_floor_space_allocations['PENDING']) !=0){ ?>
                        <div class="row">
                        <?php $sq = 1;
                            foreach($building_floor_space_allocations['PENDING'] as $empd){
                                ?>
                            <div class="align-middle mt-3">
                                <?php if($employees_name_list[$empd['emp_id']]['image'] !="") { ?>
                                    <img src="uploads/all/<?php echo $empd['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees_name_list[$empd['emp_id']]['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <h7 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['display_name']; ?></h7>
                                   <p class="m-b-0 f-12  custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['email_id']; ?></p>
                                   <p class="m-b-0 f-12  custom-limit-text" style="--linehide: 1;"><?php echo $empd['emp_allocation_datetime']; ?></p>
                                   <button type="button" class="btn btn-primary btn-mini waves-effect waves-light" onclick="approvedSpaceAllocations('<?php echo $empd['id']; ?>')">Approve</button>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <?php }else{ ?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Approval Pending</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card" style=" min-height: 500px; max-height: 500px;">
                    <div class="card-header table-card-header ">
                        <h5>Approved</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-body custom-scroll" style=" min-height: 437px; max-height: 437px;">
                        <?php if(count((array)$building_floor_space_allocations['APPROVED']) !=0){ ?>
                        <div class="row">
                        <?php $sq = 1;
                            foreach($building_floor_space_allocations['APPROVED'] as $empd){
                                ?>
                            <div class="align-middle mt-3">
                                <?php if($employees_name_list[$empd['emp_id']]['image'] !="") { ?>
                                    <img src="uploads/all/<?php echo $empd['image']; ?>"  class="img-radius img-50 align-top m-r-10" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees_name_list[$empd['emp_id']]['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <h7 class="mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['display_name']; ?></h7>
                                   <p class="m-b-0 f-12  custom-limit-text" style="--linehide: 1;"><?php echo $employees_name_list[$empd['emp_id']]['email_id']; ?></p>

                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <?php }else{ ?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Approved</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card" >
            <div class="card-header table-card-header ">
                <h5>Search Employee Locations</h5>
                <div class="card-header-right"></div>
            </div>
            <div class="card-body" >
                <div class="col-lg-12">
                    <div class="input-group input-group-button mb-0">
                        <input type="text" class="form-control form-control-sm width1" placeholder="Enter Minimum 3 Characters" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search" onkeyup="getSearchEmployeeLocations(this.value)">
                        <div class="input-group-text addon-style">
                            <span class=""><i class="ti-search text-white mt-2"></i></span>
                        </div>
                    </div>
                </div>
                <div id="search_result_employee_2" class="mt-2 custom-scroll" style=" min-height: 160px; max-height: 160px; display: none">

                </div>
            </div>
        </div>
        <div class="custom-scroll" style=" min-height: 670px; max-height: 670px;">
            <?php foreach($buildings_report as $buldd){ ?>
            <div class="card new-cust-card emptycss_building_selectio" id="building_selection_<?php echo $buldd['id']; ?>">
                <div class="card-header">
                    <h5><?php echo $buldd['building_name']; ?></h5>
                    <div class="card-header-right">
                        <i class="ti-eye allshowbuldicon" onclick="getEquipemntsWithSeat('CSA_ALLOCATION','<?php echo $buldd['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Click To Open" id="waiticon_<?php echo $buldd['id']; ?>" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                        <span class="text-danger allshowbuldwaitmsg" id="waitmsg_<?php echo $buldd['id']; ?>" style="display: none">Please Wait.....</span>
                    </div>
                </div>
                <div class="card-body pt-0">
                    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_3">
                        <thead>
                            <th scope="col">Floor</th>
                            <th scope="col" class="text-right">Cubicle</th>
                            <th scope="col" class="text-right">Aloc</th>
                            <th scope="col" class="text-right">Res</th>
                            <th scope="col" class="text-right">Avl</th>
                            <th scope="col" class="text-right">Cabin</th>
                            <th scope="col" class="text-right">Inpr</th>
                            <th scope="col" class="text-right">Aloc</th>
                            <th scope="col" class="text-right">Res</th>
                            <th scope="col" class="text-right">Avl</th>
                        </thead>
                        <tbody>
                        <?php
                        $buildingwithfloors = $building_with_floors[$buldd['id']];
                        foreach($buildingwithfloors as $floors){
                            $space_cnt = $allocemp = $allocares =$mngspace = $mngspaceallocation = $mngreservedspace = $inprogresscnt =  0;
                            foreach($floors_with_spaces[$floors['id']] as $fspaceres){
                                if($fspaceres['space_type_id'] =="7"){
                                    $mngspace++;
                                    if($fspaceres['current_status'] ==ST_INPROGRESS){
                                        $inprogresscnt++;
                                    }elseif($fspaceres['emp_id'] !=""){
                                        $mngspaceallocation++;
                                    }elseif($fspaceres['reserve_pool_id'] !=""){
                                        $mngreservedspace++;
                                    }
                                }elseif($fspaceres['emp_id'] !=""){
                                    $allocemp++;
                                    $space_cnt++;
                                }elseif($fspaceres['reserve_pool_id'] !=""){
                                    $allocares++;
                                    $space_cnt++;
                                }else{
                                    $space_cnt++;
                                }

                            }
                         ?>
                            <tr>
                                <td class="p-2"><?php echo $floors['floor_name']; ?></td>
                                <td class="p-2 text-right text-warning"><?php echo $space_cnt; ?></td>
                                <td class="p-2 text-right text-warning"><?php echo $allocemp; ?></td>
                                <td class="p-2 text-right"><?php echo $allocares; ?></td>
                                <td class="p-2 text-right text-danger"><?php echo $space_cnt - ($allocemp+$allocares); ?></td>
                                <td class="p-2 text-right text-warning"><?php echo $mngspace;?></td>
                                <td class="p-2 text-right text-danger"><?php echo $inprogresscnt;?></td>
                                <td class="p-2 text-right text-warning"><?php echo $mngspaceallocation;?></td>
                                <td class="p-2 text-right text-warning"><?php echo $mngreservedspace;?></td>
                                <td class="p-2 text-right text-warning"><?php echo $mngspace - $mngspaceallocation; ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        <?php } ?>
        </div>

<!-- { -->
            <div class="card mt-4" >
                <div class="card-header">
                    <h5>Reporting Manager Tree</h5>
                </div>
                <div class="card-block custom-scroll " style=" min-height: 440px; max-height: 440px;">
                    <?php
                    if($_SESSION['admin_type']=='1'){
                    $selecteduser = 0;
                    }else{
                    $selecteduser = $_SESSION['userid'];
                    }
if(count((array)$empparentwithchild[$selecteduser]) != '0'){ ?>
                    <div class="card-block tree-view">
                        <div id="basicTree_2">
                            <ul>
                                <?php
foreach($empparentwithchild[$selecteduser] as $empd){ ?>

<li data-jstree='{"opened":true}' class="<?php echo count((array)$empparentwithchild[$empd['id']]) !=0?"text-primary":""; ?>"><?php echo $empd['display_name']." - ".count((array)$empparentwithchild[$empd['id']]); ?>
    <?php if($empwithallocationposition[$empd['id']]['id'] !=""){ ?>
    <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
    <?php } ?>
    <ul>
     <?php foreach($empparentwithchild[$empd['id']] as $empd1){ ?>
         <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd1['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd1['id']]['display_name']." - ".count((array)$empparentwithchild[$empd1['id']]); ?>
         <?php if($empwithallocationposition[$empd1['id']]['id'] !=""){ ?>
    <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd1['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd1['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
    <?php } ?>
             <ul>
             <?php foreach($empparentwithchild[$empd1['id']] as $empd2){ ?>
                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd2['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd2['id']]['display_name']." - ".count((array)$empparentwithchild[$empd2['id']]); ?>
                 <?php if($empwithallocationposition[$empd2['id']]['id'] !=""){ ?>
                <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd2['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd2['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                <?php } ?>
                     <ul>
                     <?php foreach($empparentwithchild[$empd2['id']] as $empd3){ ?>
                         <li data-jstree='{"opened":false}'  class="<?php echo count((array)$empparentwithchild[$empd3['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd3['id']]['display_name']." - ".count((array)$empparentwithchild[$empd3['id']]); ?>
                         <?php if($empwithallocationposition[$empd3['id']]['id'] !=""){ ?>
                        <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd3['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd3['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                        <?php } ?>
                             <ul>
                             <?php foreach($empparentwithchild[$empd3['id']] as $empd4){ ?>
                                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd4['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd4['id']]['display_name']." - ".count((array)$empparentwithchild[$empd4['id']]); ?>
                                 <?php if($empwithallocationposition[$empd4['id']]['id'] !=""){ ?>
                                <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd4['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd4['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                                <?php } ?>
                                     <ul>
                                     <?php foreach($empparentwithchild[$empd4['id']] as $empd5){ ?>
                                         <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd5['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd5['id']]['display_name']." - ".count((array)$empparentwithchild[$empd5['id']]); ?>
                                         <?php if($empwithallocationposition[$empd5['id']]['id'] !=""){ ?>
                                        <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd5['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd5['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                                        <?php } ?>
                                             <ul>
                                             <?php foreach($empparentwithchild[$empd5['id']] as $empd6){ ?>
                                                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd6['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd6['id']]['display_name']." - ".count((array)$empparentwithchild[$empd6['id']]); ?>
                                                 <?php if($empwithallocationposition[$empd6['id']]['id'] !=""){ ?>
                                                <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd6['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd6['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                                                <?php } ?>
                                                     <ul>
                                                     <?php foreach($empparentwithchild[$empd6['id']] as $empd7){ ?>
                                                         <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd7['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd7['id']]['display_name']." - ".count((array)$empparentwithchild[$empd7['id']]); ?>
                                                         <?php if($empwithallocationposition[$empd7['id']]['id'] !=""){ ?>
                                                        <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd7['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd7['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                                                        <?php } ?>

                                                             <ul>
                                                             <?php foreach($empparentwithchild[$empd7['id']] as $empd8){ ?>
                                                                 <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd8['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd8['id']]['display_name']." - ".count((array)$empparentwithchild[$empd8['id']]); ?>
                                                                 <?php if($empwithallocationposition[$empd8['id']]['id'] !=""){ ?>
                                                                <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd8['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd8['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                                                                <?php } ?>
                                                                 <ul>
                                                                 <?php foreach($empparentwithchild[$empd8['id']] as $empd9){ ?>
                                                                     <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd9['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd9['id']]['display_name']." - ".count((array)$empparentwithchild[$empd9['id']]); ?>
                                                                     <?php if($empwithallocationposition[$empd9['id']]['id'] !=""){ ?>
                                                                    <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd9['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd9['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                                                                    <?php } ?>
                                                                         <ul>
                                                                         <?php foreach($empparentwithchild[$empd9['id']] as $empd10){ ?>
                                                                             <li data-jstree='{"opened":false}' class="<?php echo count((array)$empparentwithchild[$empd10['id']]) !=0?"text-primary":""; ?>"><?php echo $employees_name_list[$empd10['id']]['display_name']." - ".count((array)$empparentwithchild[$empd10['id']]); ?>
                                                                             <?php if($empwithallocationposition[$empd10['id']]['id'] !=""){ ?>
                                                                                <span  class="ti-location-pin text-primary" onclick="ShowBuildingFloorSpaceAllocations('<?php echo $empwithallocationposition[$empd10['id']]['building_floor_id']; ?>','<?php echo $empwithallocationposition[$empd10['id']]['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></span>
                                                                                <?php } ?>
                                                                             </li>
                                                                        <?php } ?>
                                                                        </ul>
                                                                     </li>
                                                                <?php } ?>
                                                                </ul>
                                                                 </li>
                                                            <?php } ?>
                                                            </ul>
                                                         </li>
                                                    <?php } ?>
                                                    </ul>
                                                 </li>
                                            <?php } ?>
                                            </ul>
                                         </li>
                                    <?php } ?>
                                    </ul>
                                 </li>
                            <?php } ?>
                            </ul>
                         </li>
                    <?php } ?>
                    </ul>
                 </li>
            <?php } ?>
            </ul>
         </li>
    <?php } ?>
    </ul>
</li>
<?php } ?>
                        </ul>
                    </div>
                </div>
                <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Reporting Manager</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>

    </div>

<div class="booking-seat"></div>
<?php } ?>

<?php } ?>

<?php if($_POST['ajaxpagecall']=="Y"){  ?>
    <!--------------  data table   --------------->
    <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
    <script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
    <script src="assets/pages/data-table/js/jszip.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/extensions/responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/extension-btns-custom.js"></script>
    <?php } ?>
