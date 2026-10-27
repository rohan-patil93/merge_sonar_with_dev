<?php
$_SESSION['apply_js']['crop_image']='1';
$_SESSION['apply_js']['multiselest'] = "1";
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['building_floor_id'];
$url = API."?mode=getbuildingfloors&rid=".$rid;
$response = callAPI($url);
$result = json_decode($response,1);
$building_floors = $result['building_floors'];
$space_types = $result['space_types'];
$building_floor_spaces = $result['building_floor_spaces'];
$all_building_floors=$result['all_building_floors'];
?>
<style type="text/css">

    #floor_cabin img{
        width: 100%;
    }
</style>
<link rel="stylesheet" type="text/css" href="assets/bower_components/cropper/css/cropper.css">
<script type="text/javascript">
    function lockFloorSpace(cabinseq){
        var building_floor_id = '<?php echo $rid; ?>';
        var cabin_name = $("#cabin_name_"+cabinseq).val();
        var space_type_id = $("#space_type_id_"+cabinseq).val();
        var x_size = $("#x_size_"+cabinseq).val();
        var y_size = $("#y_size_"+cabinseq).val();
        var width = $("#width_"+cabinseq).val();
        var height = $("#height_"+cabinseq).val();
        $(".all_error").hide();
        if(cabin_name ==""){
            $("#error_cabin_name_"+cabinseq).show();
            return false;
        }
        if(space_type_id ==""){
            $("#error_space_type_id_"+cabinseq).show();
            return false;
        }
        var building_floor_cabin_id = $("#building_floor_cabin_id_"+cabinseq).val();

        jQuery.ajax({
            url: "ajax.php",
            data: 'page=insertbuildingfloorcabins&cabin_name='+cabin_name+'&x_size='+x_size+'&y_size='+y_size+'&width='+width+'&height='+height+'&building_floor_id='+building_floor_id+'&building_floor_cabin_id='+building_floor_cabin_id+'&space_type_id='+space_type_id,
            type: "POST",
            success:function(response){
                console.log(response);
                var result = JSON.parse(response);
                console.log(result);
                if(result.building_floor_cabin_id == '0'){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                }else{
                    $("#building_floor_cabin_id_"+cabinseq).val(result.building_floor_cabin_id);
                    $("#allocateequipment_btn_"+cabinseq).html('<button type="button" class="btn p-0 border-0 bg-transparent cursor-pointer m-l-20 text-danger" onclick="removeBuildingFloorSpace(\''+result.building_floor_cabin_id+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i> Remove</button>');

                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                }
                $("#save_btn_"+cabinseq).hide();
                $("#edit_btn_"+cabinseq).show('slow');
                $(".all_edit_field").addClass("event_disabled");
                $("#active_floor_space_id").val('');
            }
        });

    }
    function editFloorSpace(cabinseq){
        $('#kt_modal_dynamic_popup2').animate({
            scrollTop: $("#floor_cabin").offset().top
        }, 100);
        $(".all_edit_btn").show('slow');
        $(".all_save_btn").hide();
        console.log(cabinseq);
        $("#save_btn_"+cabinseq).show('slow');
        $("#edit_btn_"+cabinseq).hide();
        $(".all_edit_field").addClass("event_disabled");
        $(".space_edit_field_"+cabinseq).removeClass("event_disabled");
        $("#active_floor_space_id").val(cabinseq);
        $("#floor_cabin").html('');
        $("#floor_cabin").html('<img id="image" src="uploads/building_floors/<?php echo $building_floors['floor_image']; ?>" alt="Details">');
        var defaultCropData = {
          x: parseInt($("#x_size_"+cabinseq).val()),   // Default x-coordinate
          y: parseInt($("#y_size_"+cabinseq).val()),   // Default y-coordinate
          width: parseInt($("#width_"+cabinseq).val()),   // Default width
          height: parseInt($("#height_"+cabinseq).val())   // Default height
        };
        var cropper = new Cropper(document.getElementById('image'), {
            zoomable: true,
            viewMode: 3,
            responsive: true,
            toggleDragModeOnDblclick: true,
            crop: function(event) {
                var active_floor_space_id = $("#active_floor_space_id").val();
                $("#x_size_"+active_floor_space_id).val(parseInt(event.detail.x));
                $("#y_size_"+active_floor_space_id).val(parseInt(event.detail.y));
                $("#width_"+active_floor_space_id).val(parseInt(event.detail.width));
                $("#height_"+active_floor_space_id).val(parseInt(event.detail.height));
                console.log(" width - "+event.detail.width+" height - "+event.detail.height+" x - "+event.detail.x+" y - "+event.detail.y);
            },
            data: defaultCropData
        });
        cropper.zoomTo(1);
    }
    var maxrows = 1000;
    function addMorefloorSpaces(){
        maxrows = maxrows+1;
        $(".all_edit_btn").show('slow');
        $(".all_save_btn").hide();
        $(".all_edit_field").addClass("event_disabled");
        $("#active_floor_space_id").val(maxrows);
        var htmlrows = '<div class="row mt-2" id="deleteid_'+maxrows+'">';
        htmlrows += '<div class="col-lg-3 all_edit_field space_edit_field_'+maxrows+'"> <span id="error_cabin_name_'+maxrows+'" class="text-danger all_error" style="display:none">Please Enter Required Fields</span> <input aria-label="Cabin Name" class="form-control" type="text" id="cabin_name_'+maxrows+'" value=""> <input class="form-control" type="hidden" id="building_floor_cabin_id_'+maxrows+'"  value=""></div>';

        htmlrows += '<div class="col-lg-3 w-full all_edit_field space_edit_field_'+maxrows+'"> <span id="error_space_type_id_'+maxrows+'" class="text-danger all_error" style="display:none">Please Enter Required Fields</span><select aria-label="Space Type" class="js-example-basic-single" id="space_type_id_'+maxrows+'"  name="space_type_id"> <?php foreach($space_types as $stytpe){ ?> <option value="<?php echo $stytpe['id']; ?>"><?php echo $stytpe['space_type_name']; ?></option><?php
} ?> </select></div>';

        htmlrows += '<div class="col-lg-1"><input aria-label="X Size" class="form-control" type="text" id="x_size_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-1"><input aria-label="Y Size" class="form-control" type="text" id="y_size_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-1"><input aria-label="Width" class="form-control" type="text" id="width_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-1"><input aria-label="Height" class="form-control" type="text" id="height_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-2"><button type="button" class="all_save_btn cursor-pointer" id="save_btn_'+maxrows+'" onclick="lockFloorSpace('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-save"></i> Save</button><button type="button" class="all_edit_btn cursor-pointer" id="edit_btn_'+maxrows+'" onclick="editFloorSpace('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-edit"></i> Edit</button> <span id="allocateequipment_btn_'+maxrows+'"></span></div>';

        $("#newrowadd").append(htmlrows);
        $(".js-example-basic-single").select2();
        editFloorSpace(maxrows);

    }
     function removeBuildingFloorSpace(id){
        if(id !=""){
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: '<?php echo $_SESSION['error_message']['9119']; ?>',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if(isConfirm) {
                    jQuery.ajax({
                        url: "ajax.php",
                        data:'page=softdeletedrecord&id='+id+'&table=building_floor_spaces',
                        type: "POST",
                        success:function(response){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
                            getBuildingFoolSpaceDetails('<?php echo $rid; ?>');
                        }
                    });
                }
            });
        }else{
            getBuildingFoolSpaceDetails('<?php echo $rid; ?>');
        }
    }

</script>
<div class="modal-header">
    <h4 class="modal-title">Building Floor</h4>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <div class="col-lg-12 mb-4 courier ">
        <ul class="nav nav-tabs tabs dynamic_card_menu">
            <?php foreach($all_building_floors  as $abf){ ?>
            <li class="nav-item">
                <button type="button" class="nav-link position-relative text-center p-2 cursor-pointer border-0 bg-transparent <?php echo $abf['id'] == $building_floors['id']?"active":""; ?>" onclick="getBuildingFoolSpaceDetails('<?php echo $abf['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open Building Floor Plans">
                   <img src="uploads/building_floors/<?php echo $abf['floor_image']; ?>" style="width: 100px; height: 100px;" alt="Attachment Preview">
                    <span class="text-info f-14 f-w-700"><?php echo $abf['floor_name']; ?></span>
                </button>
            </li>
            <?php } ?>
        </ul>
    </div>
    <div class="card">
        <div class="row">
            <div class="col-lg-3">
                <span class="form-label">Space Name</span>
            </div>
            <div class="col-lg-3">
                <span class="form-label">Space Type</span>
            </div>
            <div class="col-lg-1">
                <span class="form-label">X (PX)</span>
            </div>
            <div class="col-lg-1">
                <span class="form-label">Y (PX)</span>
            </div>
            <div class="col-lg-1">
                <span class="form-label">Width (PX)</span>
            </div>
            <div class="col-lg-1">
                <span class="form-label">Height (PX)</span>
            </div>
        </div>
        <div id="newrowadd">
        <?php
            $building_floor_spaces[] = array();
            $cabincnt = 0;
            foreach($building_floor_spaces as $cabin){  $cabincnt++;
                $event_disabled = "";
                if($cabin['id'] !=""){
                    $event_disabled ="event_disabled";
                }
        ?>
            <div class="row mt-2">
                <div class="col-lg-3 all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_cabin_name_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Please Enter Required Fields</span>
                    <input aria-label="Input field" class="form-control" type="text" id="cabin_name_<?php echo $cabincnt; ?>" value="<?php echo $cabin['cabin_name']; ?>">
                    <input class="form-control" type="hidden" id="building_floor_cabin_id_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['id']; ?>">
                </div>
                <div class="col-lg-3 w-full all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_space_type_id_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Please Enter Required Fields</span>
                    <select aria-label="Select option" class="js-example-basic-single" id="space_type_id_<?php echo $cabincnt; ?>"  name="space_type_id">
                        <?php foreach($space_types as $stytpe){ ?>
                        <option value="<?php echo $stytpe['id']; ?>" <?php echo $cabin['space_type_id']==$stytpe['id']?"selected":""; ?>><?php echo $stytpe['space_type_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-1">
                    <input aria-label="Input field" class="form-control" type="text" id="x_size_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['x_size']; ?>" readonly>
                </div>
                <div class="col-lg-1">
                    <input aria-label="Input field" class="form-control" type="text" id="y_size_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['y_size']; ?>" readonly>
                </div>
                <div class="col-lg-1">
                    <input aria-label="Input field" class="form-control" type="text" id="width_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['width']; ?>" readonly>
                </div>
                <div class="col-lg-1">
                    <input aria-label="Input field" class="form-control" type="text" id="height_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['height']; ?>" readonly>
                </div>
                <div class="col-lg-2">
                 <span class="all_save_btn cursor-pointer" id="save_btn_<?php echo $cabincnt; ?>" onclick="lockFloorSpace('<?php echo $cabincnt; ?>')" <?php echo $cabin['id']!=""?"style='display:none'":""; ?> data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-save"></i> Save</span>

                <span class="all_edit_btn cursor-pointer" id="edit_btn_<?php echo $cabincnt; ?>" onclick="editFloorSpace('<?php echo $cabincnt; ?>')" <?php echo $cabin['id']!=""?"":"style='display:none'"; ?> data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-edit"></i> Edit</span>

                <span id="allocateequipment_btn_<?php echo $cabincnt; ?>">
                    <?php if($cabin['id']!=""){ ?>
                        <span class="cursor-pointer m-l-20 text-danger" onclick="removeBuildingFloorSpace('<?php echo $cabin['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i> Remove</span>
                    <?php } ?>
                </span>


                </div>
            </div>
        <?php   } ?>

        </div>
        <input class="form-control" type="hidden" id="active_floor_space_id"  value="<?php echo $cabincnt; ?>">
        <button class="btn btn-success m-t-20 col-lg-2" id="addRows" type="button" onclick="addMorefloorSpaces()"><i class="fa fa-plus m-r-5"></i>Add More Cabin</button>
        <div class="row mt-5">
            <div id="floor_cabin">
                <img id="image_2" src="uploads/building_floors/<?php echo $building_floors['floor_image']; ?>" alt="Details">
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
<?php if($_POST['ajaxpage'] =="Y"){ ?>
    <script src="assets/bower_components/cropper/js/custom_cropper.js"></script>
    <script type="text/javascript" src="assets/js/custom.js"></script>
    <script type="text/javascript">

     </script>
<?php } ?>
