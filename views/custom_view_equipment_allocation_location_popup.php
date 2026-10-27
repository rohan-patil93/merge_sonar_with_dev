<?php
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $_SESSION['apply_js']['multiselest'] = "1";
?>
<script type="text/javascript">
    function setLocatonBuilding(building_id){
        jQuery.ajax({
            url: "ajax.php",
            data: "page=dynmictabledata&fid="+building_id+'&rid=building_id&table=building_floors&field=id&value=floor_name',
            type: "POST",
            success:function(data){
                $('#building_floor_id_setlocation_popup').html(data);
            }
        });
    }
    function setLocatonBuildingFloors(building_floor_id){
        jQuery.ajax({
            url: "ajax.php",
            data: "page=dynmictabledata&fid="+building_floor_id+'&rid=building_floor_id&table=building_floor_spaces&field=id&value=cabin_name',
            type: "POST",
            success:function(data){
                $('#building_floor_space_id_setlocation_popup').html(data);
            }
        });
    }
    function setInsertEquipmentAllocation(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            var building_id_setlocation_popup = $("#building_id_setlocation_popup").val();
            var building_floor_id = $("#building_floor_id_setlocation_popup").val();
            var building_floor_space_id = $("#building_floor_space_id_setlocation_popup").val();
            var eq_id = $("#equipment_id_setlocation_popup").val();
            var equipment_category_id = $("#equipment_category_id_setlocation_popup").val();
            var equipment_type_id = $("#equipment_type_id_setlocation_popup").val();
            console.log(building_floor_id+" - "+building_floor_space_id);
            insertEquipmentAllocation(building_floor_id,building_floor_space_id,"",eq_id,equipment_category_id,equipment_type_id);
        }
    }
    function setInsertEmployeeAllocation(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            var building_floor_id = $("#building_floor_id_setlocation_popup").val();
            var building_floor_space_id = $("#building_floor_space_id_setlocation_popup").val();
            var emp_id = $("#employee_id_setlocation_popup").val();
            insertEquipmentAllocation(building_floor_id,building_floor_space_id,"","","","",emp_id);
        }
    }

</script>
<?php
if($_POST['eq_id'] !=""){
    $eq_id = $_POST['eq_id'];
    $url = API."?mode=getequipmentallocations&pagetype=setlocationequipmentspopup&eq_id=".$eq_id;
    $response = callAPI($url);
    $result = json_decode($response,1);
    $equipments_list = $result['equipments_list'];
    $buildings = $result['buildings'];

?>
<div class="modal-header  ">
    <h5 class="modal-title"> Equipments For Which Locations To Be Set</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="getBuildingDynamicPage('IEQ')"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <div class="row">
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Equipment Name</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $equipments_list['equipment_name']; ?></h6>
            <input type="hidden" id="equipment_id_setlocation_popup" value="<?php echo $equipments_list['id']; ?>">
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Equipment Category</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $equipments_list['category_name']; ?></h6>
            <input type="hidden" id="equipment_category_id_setlocation_popup" value="<?php echo $equipments_list['equipment_category_id']; ?>">
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Equipment Type</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $equipments_list['equipment_types_name']; ?></h6>
            <input type="hidden" id="equipment_type_id_setlocation_popup" value="<?php echo $equipments_list['equipment_type_id']; ?>">
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Employee Seating</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $equipments_list['employee_seating'] =="Y"?"Yes":"No"; ?></h6>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-lg-4 form-group  w-full">
            <span class="form-label">Building <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
            <select aria-label="Building" class="js-example-basic-single requried_field" name="building_id" name="building_id_setlocation_popup" onchange="setLocatonBuilding(this.value)">
                <option value="">Select Any One</option>
                <?php foreach($buildings as $stytpe){ ?>
                <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['building_id']?"selected":""; ?>><?php echo $stytpe['building_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-4 form-group  w-full">
            <span class="form-label">Building Floor <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
             <select aria-label="Building Floor" class="js-example-basic-single requried_field" name="building_floor_id" id="building_floor_id_setlocation_popup" onchange="setLocatonBuildingFloors(this.value)">
                <option value="">Select Any One</option>
            </select>
        </div>
        <div class="col-xl-4 col-md-4">
            <span class="form-label">Building Floor Space <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
            <select aria-label="Building Floor Space" class="js-example-basic-single requried_field" name="building_floor_space_id" id="building_floor_space_id_setlocation_popup">
                <option value="">Select Any One</option>
            </select>
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" onclick="getBuildingDynamicPage('IEQ')">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="setInsertEquipmentAllocation()"> Set Allocat Equipments</button>
</div>

<?php }else{
    $emp_id = $_POST['emp_id'];
    $url = API."?mode=getequipmentallocations&pagetype=setlocationemployeespopup&emp_id=".$emp_id;
    $response = callAPI($url);
    $result = json_decode($response,1);
    $employees_list = $result['employees_list'];
    $buildings = $result['buildings'];

    ?>

<div class="modal-header  ">
    <h5 class="modal-title"> Employee For Which Locations To Be Set</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="getBuildingDynamicPage('IEM')"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <div class="row">
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Emp Code</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $employees_list['emp_code']; ?></h6>
            <input type="hidden" id="employee_id_setlocation_popup" value="<?php echo $employees_list['id']; ?>">
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Employee Name</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $employees_list['display_name']; ?></h6>
        </div>

        <div class="col-xl-3 col-md-3 text-center">
            <h5>Email Id</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $employees_list['email_id']; ?></h6>
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Photo</h5>
            <?php if($employees_list['image'] !="") { ?>
                <img src="uploads/all/<?php echo $employees_list['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
            <?php } else { ?>
                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($employees_list['display_name']); ?></span>
            <?php } ?>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-lg-4 form-group  w-full">
            <span class="form-label">Building <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
            <select aria-label="Building" class="js-example-basic-single requried_field" name="building_id" name="building_id_setlocation_popup" onchange="setLocatonBuilding(this.value)">
                <option value="">Select Any One</option>
                <?php foreach($buildings as $stytpe){ ?>
                <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['building_id']?"selected":""; ?>><?php echo $stytpe['building_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-4 form-group  w-full">
            <span class="form-label">Building Floor <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
             <select aria-label="Building Floor" class="js-example-basic-single requried_field" name="building_floor_id" id="building_floor_id_setlocation_popup_2" onchange="setLocatonBuildingFloors(this.value)">
                <option value="">Select Any One</option>
            </select>
        </div>
        <div class="col-xl-4 col-md-4">
            <span class="form-label">Building Floor Space <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
            <select aria-label="Building Floor Space" class="js-example-basic-single requried_field" name="building_floor_space_id" id="building_floor_space_id_setlocation_popup_2">
                <option value="">Select Any One</option>
            </select>
        </div>

    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" onclick="getBuildingDynamicPage('IEM')">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="setInsertEmployeeAllocation()"> Allocat Employee Seat</button>
</div>

<?php } ?>
