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
        $("#kt_modal_dynamic_popup").modal({
        backdrop: 'static',
        keyboard: false
    });
        var validations = ajaxFormSubmitValidation('_extraerror');
        if(validations == 0){
            var asset_id = $("#asset_id_setlocation_popup").val();
            var building_id = $("#building_id_setlocation_popup").val();
            var building_floor_id = $("#building_floor_id_setlocation_popup").val();
            insertAssetsAllocation(building_id,building_floor_id,asset_id);
        }
    }


</script>
<?php
$eq_id = $_POST['eq_id'];
$url = API."?mode=getequipmentallocations&pagetype=setlocationequipmentspopup&eq_id=".$eq_id;
$response = callAPI($url);
$result = json_decode($response,1);
$assets = $result['assets'];
$buildings = $result['buildings'];
?>
<div class="modal-header">
    <h5 class="modal-title"> Assets For Which Locations To Be Set</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <div class="row">
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Asset Code</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $assets['asset_code']; ?></h6>
        </div>
        <div class="col-xl-3 col-md-3 text-center">
            <h5>Asset Name</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $assets['asset_description']; ?></h6>
            <input type="hidden" id="asset_id_setlocation_popup" value="<?php echo $assets['id']; ?>">
        </div>
        <div class="col-lg-3 form-group  w-full">
            <span class="form-label">Building <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
            <select aria-label="Building" class="js-example-basic-single requried_field_extraerror" name="building_id" id="building_id_setlocation_popup" onchange="setLocatonBuilding(this.value)">
                <option value="">Select Any One</option>
                <?php foreach($buildings as $stytpe){ ?>
                <option value="<?php echo $stytpe['id']; ?>" <?php echo $stytpe['id']==$_POST['building_id']?"selected":""; ?>><?php echo $stytpe['building_name']; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 form-group  w-full">
            <span class="form-label">Building Floor <span class="requried">*</span></span>
             <span class="d-block error-message text-danger"></span>
             <select aria-label="Building Floor" class="js-example-basic-single requried_field_extraerror" name="building_floor_id" id="building_floor_id_setlocation_popup" onchange="setLocatonBuildingFloors(this.value)">
                <option value="">Select Any One</option>
            </select>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="setInsertEquipmentAllocation()"> Set Allocat Equipments</button>
</div>

