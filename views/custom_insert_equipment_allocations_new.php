<?php
$_SESSION['apply_js']['multiselest'] = "1";
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$building_floor_id = $_POST['building_floor_id'];
$asset_allocation_id = $_POST['asset_allocation_id'];
$asset_id = $_POST['asset_id'];
$url = API."?mode=getequipmentallocations&pagetype=equipmentallocationspopup&building_floor_id=".$building_floor_id."&asset_allocation_id=".$asset_allocation_id;
$response = callAPI($url);
$result = json_decode($response,1);
$building_floors = $result['building_floors'];
$asset_allocations = $result['asset_allocations'];
$assets = $result['assets'];
?>
<style type="text/css">
   #floor_cabin{
        position: relative;
        cursor: alias;

    }
    #disable_crop_floor_image{
        width: 100%;
    }
    .cropper-crop-box{
        pointer-events: all !important;
    }
    .cropper-face{
        cursor: pointer !important;
    }
</style>
<script type="text/javascript">
function lockFloorSpace(cabinseq){
    var building_floor_id = '<?php echo $building_floor_id; ?>';
    var space_type_id = $("#space_type_id_"+cabinseq).val();
    var x_size = $("#x_size_"+cabinseq).val();
    var y_size = $("#y_size_"+cabinseq).val();
    var width = 0;
    var height = 0;
    var equipment_allocation_id = $("#equipment_allocation_id_"+cabinseq).val();
    $(".all_error").hide();
    $(".sub_error").text('');
    if(space_type_id ==""){
        $("#error_space_type_id_"+cabinseq).show();
        return false;
    }
    if(x_size ==""){
        $("#error_x_size_"+cabinseq).show();
        return false;
    }
    if(y_size ==""){
        $("#error_y_size_"+cabinseq).show();
        return false;
    }
    var building_floor_cabin_id = $("#building_floor_cabin_id_"+cabinseq).val();
    jQuery.ajax({
        url: "ajax.php",
        data: 'page=insertequipmentallocations&building_floor_id='+building_floor_id+'&top='+x_size+'&left='+y_size+'&asset_id='+space_type_id+'&equipment_allocation_id='+equipment_allocation_id,
       // data: 'page=insertbuildingfloorcabins&cabin_name='+cabin_name+'&x_size='+x_size+'&y_size='+y_size+'&width='+width+'&height='+height+'&building_floor_id='+building_floor_id+'&building_floor_cabin_id='+building_floor_cabin_id+'&space_type_id='+space_type_id,
        type: "POST",
        success:function(response){
            console.log(response);
            var result = JSON.parse(response);
            if(result.error != "0"){
                 $("#submit_error_"+cabinseq).text(result.error_message);
             }else{
                if(result.equipment_allocation_id == '0'){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                }else{
                    $("#equipment_allocation_id_"+cabinseq).val(result.equipment_allocation_id);
                    $("#equipment_allocation_btn_"+cabinseq).html('<button type="button" class="btn p-0 border-0 bg-transparent cursor-pointer m-l-20 text-danger" onclick="removeEquipmentAllocations(\''+result.equipment_allocation_id+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-trash"></i> Remove</button>');
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                }
                $("#save_btn_"+cabinseq).hide();
                $("#edit_btn_"+cabinseq).show('slow');
                $(".all_edit_field").addClass("event_disabled");
                $("#equipment_allocation_seq").val('');
            }
        }
    });
}

function editFloorSpace(cabinseq){
    $(".all_edit_btn").show('slow');
    $(".all_save_btn").hide();
    console.log(cabinseq);
    $("#save_btn_"+cabinseq).show('slow');
    $("#edit_btn_"+cabinseq).hide();
    $(".all_edit_field").addClass("event_disabled");
    $(".space_edit_field_"+cabinseq).removeClass("event_disabled");
    $("#active_floor_space_id").val(cabinseq);
    $("#equipment_allocation_seq").val(cabinseq);
}
function addMorefloorSpaces(){
        var max_seq_number = $("#max_seq_number").val();
        maxrows = parseInt(max_seq_number)+1;
        $("#max_seq_number").val(maxrows);
        $(".all_edit_btn").show('slow');
        $(".all_save_btn").hide();
        $(".all_edit_field").addClass("event_disabled");
        $("#active_floor_space_id").val(maxrows);
        $("#equipment_allocation_seq").val(maxrows);
        var htmlrows = '<div class="row mt-2" id="deleteid_'+maxrows+'"><input type="hidden" id="equipment_allocation_id_'+maxrows+'" value="">';

        htmlrows += '<div class="col-lg-4 w-full all_edit_field space_edit_field_'+maxrows+'"> <span id="error_space_type_id_'+maxrows+'" class="text-danger all_error" style="display:none">Please Enter Required Fields</span><select aria-label="Space Type" class="js-example-basic-single" id="space_type_id_'+maxrows+'"  name="space_type_id"> <?php foreach($assets as $asss){ ?><option value="<?php echo $asss['id']; ?>"><?php echo $asss['asset_description']."(".$asss['asset_code'].")"; ?></option><?php
} ?></select></div>';

        htmlrows += '<div class="col-lg-3"><input aria-label="X Size" class="form-control" type="text" id="x_size_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-3"><input aria-label="Y Size" class="form-control" type="text" id="y_size_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-2"><button type="button" class="all_save_btn cursor-pointer" id="save_btn_'+maxrows+'" onclick="lockFloorSpace('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-save"></i> Save</button><button type="button" class="all_edit_btn cursor-pointer" id="edit_btn_'+maxrows+'" onclick="editFloorSpace('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-edit"></i> Edit</button> <span id="allocateequipment_btn_'+maxrows+'"></span><span class="text-danger sub_error d-block" id="submit_error_'+maxrows+'" ></span></div>';
        $("#newrowadd").append(htmlrows);
        $(".js-example-basic-single").select2();

        var htmlequipment ='<div id="position_'+maxrows+'" class="equipment-allocation-icon     cursor-pointer free_allocation " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'+maxrows+' Cubicle" style="top: 0%; left: 0%;">'+maxrows+'</div>';

         $("#extra_equipments_icon").append(htmlequipment);

        editFloorSpace(maxrows);
    }

    function removeEquipmentAllocations(id){
        if(id !=""){
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: '<?php echo $_SESSION['error_message']['9120']; ?>',
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
                        data:'page=softdeletedrecord&id='+id+'&table=asset_allocations',
                        type: "POST",
                        success:function(response){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
                            closeEquipmentAllocations();
                            insertAssetsAllocation('','<?php echo $building_floor_id; ?>','','')
                        }
                    });
                }
            });
        }else{
            closeEquipmentAllocations();
            insertAssetsAllocation('','<?php echo $building_floor_id; ?>','','')
        }
    }

    function closeEquipmentAllocations(){
        var urlParams = new URLSearchParams(window.location.search);
        var param_value = "B";
        if (urlParams.has('TAB1')) {
            param_value = urlParams.get('TAB1');
            param_value = (param_value !=null || param_value !="")?param_value:"B";
        }
        getBuildingDynamicPage(param_value);
        $('#dynamic_popup1').html('');
        $('#kt_modal_dynamic_popup').modal("hide");
    }

</script>
<div class="modal-header">
    <h4 class="modal-title">Building Floor</h4>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close" onclick="closeEquipmentAllocations()"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <div class="col-lg-12 mb-4 courier ">
        <ul class="nav nav-tabs tabs dynamic_card_menu">
            <?php foreach($all_building_floors  as $abf){ ?>
            <li class="nav-item">
                <button type="button" class="nav-link position-relative text-center p-2 cursor-pointer border-0 bg-transparent <?php echo $abf['id'] == $building_floors['id']?"active":""; ?>" onclick="getBuildingFoolSpaceDetailsNew('<?php echo $abf['id']; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title=""  data-bs-original-title="Click To Open Building Floor Plans">
                   <img src="uploads/building_floors/<?php echo $abf['floor_image']; ?>" style="width: 100px; height: 100px;" alt="Attachment Preview">
                    <span class="text-info f-14 f-w-700"><?php echo $abf['floor_name']; ?></span>
                </button>
            </li>
            <?php } ?>
        </ul>
    </div>
    <div class="row">
        <div class="col-lg-4">
            <span class="form-label">Assets</span>
        </div>
        <div class="col-lg-3">
            <span class="form-label">X (PX-Top)</span>
        </div>
        <div class="col-lg-3">
            <span class="form-label">Y (PX-Left)</span>
        </div>
    </div>
    <div id="newrowadd">
         <?php
            if($asset_id != "" && $asset_allocation_id == ""){
                $asset_allocations = array();
                $asset_allocations[] = array('asset_id'=>$asset_id);
            }elseif($asset_allocation_id == ""){
                $asset_allocations[] = array();
            }
            $cabincnt = 0;
            foreach($asset_allocations as $cabin){  $cabincnt++;
                $event_disabled = "";
                if($asset_allocation_id == $cabin['id'] && $asset_allocation_id !=""){
                    $event_disabled ="event_disabled";
                    $editableseq = $cabincnt;
                    $savebtncss = "";
                    $editbtncss = "style='display:none'";
                    $addmorebtn = "style='display:none'";
                    $forecestopedit = "not";
                }elseif($asset_id != "" && $asset_allocation_id == ""){
                    $event_disabled ="event_disabled";
                    $editableseq = $cabincnt;
                    $savebtncss = "";
                    $editbtncss = "style='display:none'";
                    $addmorebtn = "style='display:none'";
                    $forecestopedit = "not";
                }elseif($cabin['id'] !=""){
                    $event_disabled ="event_disabled";
                    $savebtncss = "style='display:none'";
                    $editbtncss = "";
                }else{
                    $editableseq = $cabincnt;
                    $savebtncss = "";
                    $editbtncss = "style='display:none'";
                }

        ?>
         <div class="row mt-2">
            <input type="hidden" id="equipment_allocation_id_<?php echo $cabincnt; ?>" value="<?php echo $cabin['id']; ?>">
                <div class="col-lg-4 w-full all_edit_field <?php echo $forecestopedit; ?>space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_space_type_id_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Please Enter Required Fields</span>
                    <select aria-label="Select option" class="js-example-basic-single" id="space_type_id_<?php echo $cabincnt; ?>"  name="space_type_id">
                        <?php foreach($assets as $asss){ ?>
                        <option value="<?php echo $asss['id']; ?>" <?php echo $cabin['asset_id']==$asss['id']?"selected":""; ?>><?php echo $asss['asset_description']."(".$asss['asset_code'].")"; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-3 all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_x_size_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                    <input aria-label="Input field" class="form-control" type="text" id="x_size_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['top_size']; ?>" readonly>
                </div>
                <div class="col-lg-3 all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_y_size_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                    <input aria-label="Input field" class="form-control" type="text" id="y_size_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['left_size']; ?>" readonly>
                </div>
                <div class="col-lg-2">
                 <span class="all_save_btn cursor-pointer" id="save_btn_<?php echo $cabincnt; ?>" onclick="lockFloorSpace('<?php echo $cabincnt; ?>')" <?php echo $savebtncss; ?> data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-save"></i> Save</span>

                <span class="all_edit_btn cursor-pointer" id="edit_btn_<?php echo $cabincnt; ?>" onclick="editFloorSpace('<?php echo $cabincnt; ?>')" <?php echo $editbtncss; ?>  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-edit"></i> Edit</span>

                <span id="allocateequipment_btn_<?php echo $cabincnt; ?>">
                    <?php if($cabin['id']!=""){ ?>
                        <span class="cursor-pointer m-l-20 text-danger" onclick="removeEquipmentAllocations('<?php echo $cabin['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash"></i> Remove</span>
                    <?php } ?>
                </span>
                <span class="text-danger sub_error d-block" id="submit_error_<?php echo $cabincnt; ?>" ></span>
                </div>
            </div>
        <?php } ?>
        <input type="hidden" id="equipment_allocation_seq"  value="<?php echo $editableseq; ?>">
        <input type="hidden" id="max_seq_number"  value="<?php echo $cabincnt; ?>">
    </div>
    <input class="form-control" type="hidden" id="active_floor_space_id"  value="<?php echo $cabincnt; ?>">
        <button class="btn btn-success m-t-20 col-lg-3" <?php echo $addmorebtn; ?> id="addRows" type="button" onclick="addMorefloorSpaces()"><i class="fa fa-plus m-r-5"></i>Add More Cabin</button>

<div class="row mt-5">
    <div id="floor_cabin">
        <img id="disable_crop_floor_image" src="uploads/building_floors/<?php echo $building_floors['floor_image']; ?>" alt="Attachment Preview">
        <?php
        $cabincnt = 0;
        foreach($asset_allocations as $ealloc){  $cabincnt++;
            ?>
            <div id="position_<?php echo $cabincnt; ?>" class="equipment-allocation-icon     cursor-pointer free_allocation " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $cabincnt; ?> Cubicle" style="top: <?php echo $ealloc['top_size']; ?>%; left: <?php echo $ealloc['left_size']; ?>%;"><?php echo $cabincnt; ?></div>
        <?php } ?>
        <div id="extra_equipments_icon"></div>
    </div>
</div>
</div>
<script type="text/javascript">
    $('#floor_cabin').on('click', function(e) {
        var equipment_allocation_seq = $("#equipment_allocation_seq").val();
        var $this = $(this);
        console.log($this);
        var offset = $this.offset();
        var width = $this.width();
        var height = $this.height();
        var posX = offset.left;
        var posY = offset.top;
        var x = e.pageX-posX;
            x = parseFloat(x/width*100,10);
            x = x<0?0:x;
            x = x>100?100:x;
        var y = e.pageY-posY;
            y = parseFloat(y/height*100,10);
            y = y<0?0:y;
            y = y>100?100:y;
        console.log('Mouse click position - X:', x, 'Y:', y);
        $("#position_"+equipment_allocation_seq).css('left',x+'%');
        $("#position_"+equipment_allocation_seq).css('top',y+'%');
        $("#x_size_"+equipment_allocation_seq).val(y);
        $("#y_size_"+equipment_allocation_seq).val(x);
    });
</script>
