<?php
$_SESSION['apply_js']['crop_image']='1';
$_SESSION['apply_js']['multiselest'] = "1";
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['building_floor_id'];
$orderby = $_POST['ORDERBY'];
$url = API."?mode=getbuildingfloors&rid=".$rid."&ORDERBY=".$orderby;
$response = callAPI($url);
$result = json_decode($response,1);
$building_floors = $result['building_floors'];
$space_types = $result['space_types'];
$building_floor_spaces = $result['building_floor_spaces'];
$all_building_floors=$result['all_building_floors'];
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
    var building_floor_id = '<?php echo $rid; ?>';
    var cabin_name = $("#cabin_name_"+cabinseq).val();
    var space_type_id = $("#space_type_id_"+cabinseq).val();
    var x_size = $("#x_size_"+cabinseq).val();
    var y_size = $("#y_size_"+cabinseq).val();
    var width = 0;
    var height = 0;
    $(".all_error").hide();
    if(cabin_name ==""){
        $("#error_cabin_name_"+cabinseq).show();
        return false;
    }
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
            $("#equipment_allocation_seq").val('');
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
        var htmlrows = '<div class="row mt-2" id="deleteid_'+maxrows+'"> <div class="col-lg-1">'+maxrows+'</div>';
        htmlrows += '<div class="col-lg-3 all_edit_field space_edit_field_'+maxrows+'"> <span id="error_cabin_name_'+maxrows+'" class="text-danger all_error" style="display:none">Please Enter Required Fields</span> <input aria-label="Cabin Name" class="form-control" type="text" id="cabin_name_'+maxrows+'" value=""> <input class="form-control" type="hidden" id="building_floor_cabin_id_'+maxrows+'"  value=""></div>';

        htmlrows += '<div class="col-lg-4 w-full all_edit_field space_edit_field_'+maxrows+'"> <span id="error_space_type_id_'+maxrows+'" class="text-danger all_error" style="display:none">Please Enter Required Fields</span><select aria-label="Space Type" class="js-example-basic-single" id="space_type_id_'+maxrows+'"  name="space_type_id"> <?php foreach($space_types as $stytpe){ ?> <option value="<?php echo $stytpe['id']; ?>"><?php echo $stytpe['space_type_name']; ?></option><?php
} ?> </select></div>';

        htmlrows += '<div class="col-lg-1"><input aria-label="X Size" class="form-control" type="text" id="x_size_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-1"><input aria-label="Y Size" class="form-control" type="text" id="y_size_'+maxrows+'"  value="0" readonly> </div>';

        htmlrows += '<div class="col-lg-2"><button type="button" class="all_save_btn cursor-pointer" id="save_btn_'+maxrows+'" onclick="lockFloorSpace('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-save"></i> Save</button><button type="button" class="all_edit_btn cursor-pointer" id="edit_btn_'+maxrows+'" onclick="editFloorSpace('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-edit"></i> Edit</button> <span id="allocateequipment_btn_'+maxrows+'"></span></div>';
        $("#newrowadd").append(htmlrows);
        $(".js-example-basic-single").select2();

        var htmlequipment = '<div id="position_'+maxrows+'" class="equipment-allocation-icon" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'+maxrows+' Seat" >'+maxrows+'</div>';
         $("#extra_equipments_icon").append(htmlequipment);

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
                            getBuildingFoolSpaceDetailsNew('<?php echo $rid; ?>');
                        }
                    });
                }
            });
        }else{
            getBuildingFoolSpaceDetailsNew('<?php echo $rid; ?>');
        }
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Building Floor</h4>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <div class="col-lg-12 mb-4 courier building-floor">
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
    <div class="row mt-5">
        <div class="col-lg-2">
            <span class="form-label">Space Name </span>
        </div>
        <div class="col-lg-2 text-end btn-order">
            <button class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon <?php echo $orderby=="ASC"?"active":""; ?>" onclick="getBuildingFoolSpaceDetailsNew('<?php echo $rid; ?>','ASC')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Ascending Order">
                <i class="ti-angle-up m-0"></i>
            </button>
            <button class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon <?php echo $orderby=="DESC"?"active":""; ?>" onclick="getBuildingFoolSpaceDetailsNew('<?php echo $rid; ?>','DESC')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Descending Order">
                <i class="ti-angle-down m-0"></i>
            </button>
            <button class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon <?php echo $orderby==""?"active":""; ?>" onclick="getBuildingFoolSpaceDetailsNew('<?php echo $rid; ?>','')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="No Order">
                <i class="ti-arrows-horizontal m-0"></i>
            </button>
        </div>
        <div class="col-lg-4">
            <span class="form-label">Space Type</span>
        </div>
        <div class="col-lg-1">
            <span class="form-label">X (PX)</span>
        </div>
        <div class="col-lg-1">
            <span class="form-label">Y (PX)</span>
        </div>
    </div>
    <div id="newrowadd" class="mt-3">
         <?php
            $building_floor_spaces[] = array();
            $cabincnt = 0;
            foreach($building_floor_spaces as $cabin){  $cabincnt++;
                $event_disabled = "";
                if($cabin['id'] !=""){
                    $event_disabled ="event_disabled";
                }
                $editableseq = $cabincnt;
        ?>
         <div class="row mt-2">
            <div class="col-lg-1"><?php echo $cabincnt; ?></div>
                <div class="col-lg-3 all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_cabin_name_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Please Enter Required Fields</span>
                    <input aria-label="Input field" class="form-control" type="text" id="cabin_name_<?php echo $cabincnt; ?>" value="<?php echo $cabin['cabin_name']; ?>">
                    <input class="form-control" type="hidden" id="building_floor_cabin_id_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['id']; ?>">
                </div>
                <div class="col-lg-4 w-full all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_space_type_id_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Please Enter Required Fields</span>
                    <select aria-label="Select option" class="js-example-basic-single" id="space_type_id_<?php echo $cabincnt; ?>"  name="space_type_id">
                        <?php foreach($space_types as $stytpe){ ?>
                        <option value="<?php echo $stytpe['id']; ?>" <?php echo $cabin['space_type_id']==$stytpe['id']?"selected":""; ?>><?php echo $stytpe['space_type_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-lg-1 all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_x_size_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                    <input aria-label="Input field" class="form-control" type="text" id="x_size_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['x_size']; ?>" readonly>
                </div>
                <div class="col-lg-1 all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                    <span id="error_y_size_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                    <input aria-label="Input field" class="form-control" type="text" id="y_size_<?php echo $cabincnt; ?>"  value="<?php echo $cabin['y_size']; ?>" readonly>
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
        <?php } ?>
        <input type="hidden" id="equipment_allocation_seq"  value="<?php echo $editableseq; ?>">
        <input type="hidden" id="max_seq_number"  value="<?php echo $cabincnt; ?>">
    </div>
    <input class="form-control" type="hidden" id="active_floor_space_id"  value="<?php echo $cabincnt; ?>">
        <button class="btn btn-success m-t-20 col-lg-3" id="addRows" type="button" onclick="addMorefloorSpaces()"><i class="fa fa-plus m-r-5"></i>Add More Cabin</button>

<div class="row mt-5">
    <div id="floor_cabin">
        <img id="disable_crop_floor_image" src="uploads/building_floors/<?php echo $building_floors['floor_image']; ?>" alt="Attachment Preview">
        <?php
        $cabincnt = 0;
        foreach($building_floor_spaces as $ealloc){  $cabincnt++;
            ?>
            <div id="position_<?php echo $cabincnt; ?>" class="equipment-allocation-icon     cursor-pointer free_allocation " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $cabincnt; ?> Cubicle" style="top: <?php echo $ealloc['y_size']; ?>%; left: <?php echo $ealloc['x_size']; ?>%;"><?php echo $cabincnt; ?></div>
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
        $("#x_size_"+equipment_allocation_seq).val(x);
        $("#y_size_"+equipment_allocation_seq).val(y);
    });
</script>
