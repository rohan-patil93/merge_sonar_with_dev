<?php
$_SESSION['apply_js']['multiselest'] = "1";
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$building_floor_id = $_POST['building_floor_id'];
$building_floor_space_id = $_POST['building_floor_space_id'];
$url = API."?mode=getequipmentallocations&pagetype=equipmentallocationspopup&building_floor_space_id=".$building_floor_space_id."&building_floor_id=".$building_floor_id;
$response = callAPI($url);
$result = json_decode($response,1);
$all_building_floor_spaces =  $result['all_building_floor_spaces'];
$building_floor_spaces = $result['building_floor_spaces'];
$building_floor_space_id = $building_floor_spaces['id'];
$building_floors = $result['building_floors'];
$equipment_allocations =$equipment_allocations_loop= $result['equipment_allocations'];
$equipment_categories = $result['equipment_categories'];
$equipment_types_res = $result['equipment_types'];
$equipments_res = $result['equipments_list'];
$employees = $result['employees_list'];
$remaining_equipment_allocations = $result['remaining_equipment_allocations'];
$equipment_types = $equipments = array();
foreach($equipment_types_res as $etr){
    $equipment_types[$etr['equipment_category_id']][] = $etr;
}
foreach($equipments_res as $etr){
    $equipments[$etr['equipment_type_id']][] = $etr;
}
?>
<style type="text/css">
   #floor_cabin{
        position: relative;
        pointer-events: none;
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
<link rel="stylesheet" type="text/css" href="assets/bower_components/cropper/css/cropper.css">
<script type="text/javascript">
    function getEquipmentCategorie(seq){
        var equipment_category_id = $("#equipment_category_id_"+seq).val();
        $.ajax({
            url: "ajax.php",
            data:'page=getequipmenttypespopup&equipment_category_id='+equipment_category_id,
            type: "POST",
            success: function(response) {
                console.log(response);
                $("#equipment_type_id_"+seq).html(response);
            }
        });
    }
    function getEquipmentType(seq){
        var equipment_type_id = $("#equipment_type_id_"+seq).val();
        if(equipment_type_id !=""){
            var select_equipment_type_id = equipment_type_id.split("||");
            if(select_equipment_type_id[1] =='Y'){
                $("#div_emp_id_"+seq).show();
            }else{
                $("#div_emp_id_"+seq).hide();
            }
            $.ajax({
                url: "ajax.php",
                data:'page=getremaingequipmentslist&equipment_type_id='+select_equipment_type_id[0],
                type: "POST",
                success: function(response) {
                    console.log(response);
                    $("#equipment_id_"+seq).html(response);
                }
            });
        }
    }

    function submitEquipmentAllocations(cabinseq){
        var building_floor_space_id = '<?php echo $building_floor_space_id; ?>';
        var top = $("#top_"+cabinseq).val();
        var left = $("#left_"+cabinseq).val();
        var equipment_category_id = $("#equipment_category_id_"+cabinseq).val();
        var equipment_type_id = $("#equipment_type_id_"+cabinseq).val();
        var equipment_id = $("#equipment_id_"+cabinseq).val();
        var emp_id = $("#emp_id_"+cabinseq).val();
        var equipment_allocation_id = $("#equipment_allocation_id_"+cabinseq).val();
        $(".sub_error").text(' ');
        $(".all_error").hide();
        if(left ==""){
            $("#error_left_"+cabinseq).show();
            return false;
        }
        if(top ==""){
            $("#error_top_"+cabinseq).show();
            return false;
        }
        if(equipment_category_id ==""){
            $("#error_equipment_category_id_"+cabinseq).show();
            return false;
        }
        if(equipment_type_id ==""){
            $("#error_equipment_type_id_"+cabinseq).show();
            return false;
        }
        if(equipment_id ==""){
            $("#error_equipment_id_"+cabinseq).show();
            return false;
        }

        jQuery.ajax({
            url: "ajax.php",
            data: 'page=insertequipmentallocations&building_floor_space_id='+building_floor_space_id+'&top='+top+'&left='+left+'&equipment_category_id='+equipment_category_id+'&equipment_type_id='+equipment_type_id+'&equipment_id='+equipment_id+'&emp_id='+emp_id+'&equipment_allocation_id='+equipment_allocation_id,
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
    function editEquipmentAllocations(cabinseq){
        $(".all_edit_btn").show('slow');
        $(".all_save_btn").hide();
        $("#save_btn_"+cabinseq).show('slow');
        $("#edit_btn_"+cabinseq).hide();
        $(".all_edit_field").addClass("event_disabled");
        $(".space_edit_field_"+cabinseq).removeClass("event_disabled");
        $("#equipment_allocation_seq").val(cabinseq);
    }

    function addMoreEquipmentAllocations(){
        var max_seq_number = $("#max_seq_number").val();
        maxrows = parseInt(max_seq_number)+1;
        $("#max_seq_number").val(maxrows);
        $(".all_edit_btn").show('slow');
        $(".all_save_btn").hide();
        $(".all_edit_field").addClass("event_disabled");
        $("#equipment_allocation_seq").val(maxrows);

        var htmlrows = '<div class="row mt-2" id="deleteid_'+maxrows+'">';

        htmlrows += '<div class="col-lg-1"><span id="error_left_'+maxrows+'" class="text-danger all_error" style="display:none">Required</span><input aria-label="Left" class="form-control" type="text" id="left_'+maxrows+'"  value="" readonly></div>';
        htmlrows += '<div class="col-lg-1"> <span id="error_top_'+maxrows+'" class="text-danger all_error" style="display:none">Required</span><input aria-label="Top" class="form-control" type="text" id="top_'+maxrows+'"  value="" readonly></div>';

        htmlrows += '<div class="col-lg-2 w-full all_edit_field space_edit_field_'+maxrows+'"> <span id="error_equipment_category_id_'+maxrows+'" class="text-danger all_error" style="display:none">Required</span> <select aria-label="Equipment Category" class="js-example-basic-single" id="equipment_category_id_'+maxrows+'" onchange="getEquipmentCategorie('+maxrows+')"><option value="">Select Any One </option><?php foreach($equipment_categories as $eqcat){ ?><option value="<?php echo $eqcat['id']; ?>"><?php echo $eqcat['category_name']; ?></option> <?php
} ?></select></div>';

        htmlrows += '<div class="col-lg-2 w-full all_edit_field space_edit_field_'+maxrows+'"> <span id="error_equipment_type_id_'+maxrows+'" class="text-danger all_error" style="display:none">Required</span><select aria-label="Equipment Type" class="js-example-basic-single" id="equipment_type_id_'+maxrows+'" onchange="getEquipmentType('+maxrows+')"> <option value="">Select Any One </option></select> </div>';

        htmlrows += '<div class="col-lg-2 w-full all_edit_field space_edit_field_'+maxrows+' <?php echo $event_disabled; ?>"><span id="error_equipment_id_'+maxrows+'" class="text-danger all_error" style="display:none">Required</span><select aria-label="Equipment" class="js-example-basic-single" id="equipment_id_'+maxrows+'" ><option value="">Select Any One </option></select></div>';

        htmlrows += ' <div class="col-lg-2 w-full all_edit_field space_edit_field_'+maxrows+'"><div id="div_emp_id_'+maxrows+'" style="display: none";> <select aria-label="Emp" class="js-example-basic-single" id="emp_id_'+maxrows+'"> <option value="">Select Any One </option> <?php foreach($employees as $emp){ ?> <option value="<?php echo $emp['id']; ?>" <?php echo $ealloc['emp_id']==$emp['id']?"selected":""; ?>><?php echo str_replace("'","",$emp['display_name']); ?></option> <?php
} ?></select></div></div>';

        htmlrows += '<div class="col-lg-2"> <input type="hidden" id="equipment_allocation_id_'+maxrows+'" value=""> <button type="button" class="all_save_btn cursor-pointer" id="save_btn_'+maxrows+'" onclick="submitEquipmentAllocations('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-save "></i>  Save</button> <button type="button" class="all_edit_btn cursor-pointer" id="edit_btn_'+maxrows+'" onclick="editEquipmentAllocations('+maxrows+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-edit "></i>  Edit</button> <span id="equipment_allocation_btn_'+maxrows+'" ></span>  <span class="text-danger sub_error d-block" id="submit_error_'+maxrows+'" ></span></div>';

        $("#newrowadd").append(htmlrows);
        $(".js-example-basic-single").select2();

        var htmlequipment = '<div id="position_'+maxrows+'" class="equipment-allocation-icon" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="'+maxrows+' Seat" >'+maxrows+'</div>';
         $("#extra_equipments_icon").append(htmlequipment);

        editEquipmentAllocations(maxrows);
    }

    function getBuildingFloorSpacesPopUp(building_floor_space_id){
        var transaction_type_popup = $("#transaction_type_popup").val();
        jQuery.ajax({
            url: "views/custom_insert_equipment_allocations.php",
            data: "building_floor_space_id="+building_floor_space_id+"&transaction_type="+transaction_type_popup+'&ajaxpage=Y',
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $('[data-bs-toggle="tooltip"]').tooltip();
            }
        });
    }
//editEquipmentAllocations
   function removeEquipmentAllocations(id){
        var urlParams = new URLSearchParams(window.location.search);
        var param_value = "";
        if (urlParams.has('TAB1')) {
            param_value = urlParams.get('TAB1');
            param_value = (param_value !=null || param_value !="")?param_value:"B";
        }
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
                        data:'page=softdeletedrecord&id='+id+'&table=equipment_allocations',
                        type: "POST",
                        success:function(response){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
                            getBuildingDynamicPage(param_value);
                            insertEquipmentAllocation('<?php echo $building_floor_id; ?>','<?php echo $building_floor_space_id; ?>','<?php echo $_POST['eaid']; ?>','<?php echo $_POST['eq_id']; ?>','<?php echo $_POST['eq_cat_id']; ?>','<?php echo $_POST['eq_type_id']; ?>','<?php echo $_POST['emp_id']; ?>','<?php echo $_POST['transaction_type']; ?>');
                        }
                    });
                }
            });
        }else{
            getBuildingDynamicPage(param_value);
            insertEquipmentAllocation('<?php echo $building_floor_id; ?>','<?php echo $building_floor_space_id; ?>','<?php echo $_POST['eaid']; ?>','<?php echo $_POST['eq_id']; ?>','<?php echo $_POST['eq_cat_id']; ?>','<?php echo $_POST['eq_type_id']; ?>','<?php echo $_POST['emp_id']; ?>','<?php echo $_POST['transaction_type']; ?>');
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
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Equipment Allocations</h4>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close" onclick="closeEquipmentAllocations()"></button>
    <input type="hidden" id="transaction_type_popup" value="<?php echo $_POST['transaction_type']; ?>">
</div>
<div class="modal-body m-l-50 m-r-50">
    <?php if($_REQUEST['eaid'] =="" && $_REQUEST['eq_id'] =="" && $_REQUEST['emp_id'] ==""){ ?>
     <div class="col-lg-12 mb-4 courier ">
        <ul class="nav nav-tabs tabs dynamic_card_menu">
            <?php foreach($all_building_floor_spaces  as $abfs){ ?>
            <li class="nav-item cursor-pointer">
                <button type="button" class="nav-link position-relative text-center p-2 border-0 bg-transparent <?php echo $abfs['id'] == $building_floor_spaces['id']?"active":""; ?>" onclick="getBuildingFloorSpacesPopUp('<?php echo $abfs['id']; ?>')">
                    <span class="text-info f-14 f-w-700"><?php echo $abfs['cabin_name']; ?></span>
                </button>
            </li>
            <?php } ?>
        </ul>
    </div>
    <?php } ?>
    <div class="row mt-4">
        <div class="col-lg-1">
            <span class="form-label">Left (%)</span>
        </div>
        <div class="col-lg-1">
            <span class="form-label">Top (%)</span>
        </div>
        <div class="col-lg-2">
            <span class="form-label">Equipment Categories</span>
        </div>
        <div class="col-lg-2">
            <span class="form-label">Equipment Type</span>
        </div>
        <div class="col-lg-2">
            <span class="form-label">Equipment</span>
        </div>
        <div class="col-lg-2">
            <span class="form-label">Employee</span>
        </div>
    </div>
    <div id="newrowadd">
    <?php
    if($_REQUEST['eaid'] =="" && $_REQUEST['eq_id'] =="" && $_REQUEST['emp_id'] =="" && $_POST['transaction_type']!="AS"){
        $equipment_allocations[] = array();
    }
    if($_REQUEST['eaid'] !=""){ //Only Show Equipment Allocation ID Get
        $equipment_allocations = array();
        foreach($equipment_allocations_loop as $ealloc){
            if($ealloc['id'] == $_REQUEST['eaid']){

                $equipment_allocations[] = $ealloc;
            }
        }
    }
    if($_REQUEST['eaid'] =="" && $_REQUEST['eq_id'] !="" && $_REQUEST['eq_cat_id'] !="" && $_REQUEST['eq_type_id'] !=""){
        $equipment_allocations[] = array('equipment_category_id'=>$_REQUEST['eq_cat_id'],'equipment_type_id'=>$_REQUEST['eq_type_id'],'equipment_id'=>$_REQUEST['eq_id']);
    }
    if($_REQUEST['eaid'] =="" && $_REQUEST['emp_id'] !=""){
        $equipment_allocations = array();
        $equipment_allocations[] = array('emp_id'=>$_REQUEST['emp_id']);
    }
    $cabincnt = 0;
    foreach($equipment_allocations as $ealloc){  $cabincnt++;
        $event_disabled = "";
        if($_REQUEST['emp_id'] !=""){
            $empselect = 'Y';
        }else{
            $empselect = 'N';
        }
        if($ealloc['id'] !="" && $_REQUEST['eaid'] !=$ealloc['id']){
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
            <div class="col-lg-1">
                <span id="error_left_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                <input aria-label="Input field" class="form-control" type="text" id="left_<?php echo $cabincnt; ?>"  value="<?php echo $ealloc['left_size']; ?>" readonly>
            </div>
            <div class="col-lg-1">
                <span id="error_top_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                <input aria-label="Input field" class="form-control" type="text" id="top_<?php echo $cabincnt; ?>"  value="<?php echo $ealloc['top_size']; ?>" readonly>
            </div>
            <div class="col-lg-2 w-full all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                 <span id="error_equipment_category_id_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                <select aria-label="Select option" class="js-example-basic-single" id="equipment_category_id_<?php echo $cabincnt; ?>" onchange="getEquipmentCategorie('<?php echo $cabincnt; ?>')">
                    <option value=''>Select Any One </option>
                    <?php foreach($equipment_categories as $eqcat){ ?>
                    <option value="<?php echo $eqcat['id']; ?>" <?php echo $ealloc['equipment_category_id']==$eqcat['id']?"selected":""; ?>><?php echo $eqcat['category_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-lg-2 w-full all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                <span id="error_equipment_type_id_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                <select aria-label="Select option" class="js-example-basic-single" id="equipment_type_id_<?php echo $cabincnt; ?>" onchange="getEquipmentType('<?php echo $cabincnt; ?>')">
                    <option value=''>Select Any One </option>
                    <?php foreach($equipment_types[$ealloc['equipment_category_id']] as $stytpe){
                        if($ealloc['equipment_type_id']==$stytpe['id']){
                            $empselect = $stytpe['seating_equipment'];
                        }
                     ?>
                    <option value="<?php echo $stytpe['id']; ?>" <?php echo $ealloc['equipment_type_id']==$stytpe['id']?"selected":""; ?>><?php echo $stytpe['equipment_types_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-lg-2 w-full all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">
                <span id="error_equipment_id_<?php echo $cabincnt; ?>" class="text-danger all_error" style="display:none">Required</span>
                <select aria-label="Select option" class="js-example-basic-single" id="equipment_id_<?php echo $cabincnt; ?>" >
                    <option value=''>Select Any One </option>
                    <?php foreach($equipments[$ealloc['equipment_type_id']] as $stytpe){

                        ?>
                    <option value="<?php echo $stytpe['id']; ?>" <?php echo $ealloc['equipment_id']==$stytpe['id']?"selected":""; ?>><?php echo $stytpe['equipment_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-lg-2 w-full all_edit_field space_edit_field_<?php echo $cabincnt; ?> <?php echo $event_disabled; ?>">

                <div id="div_emp_id_<?php echo $cabincnt; ?>" <?php if($empselect == 'N'){ ?> style="display: none"; <?php
} ?>>
                    <select aria-label="Select option" class="js-example-basic-single" id="emp_id_<?php echo $cabincnt; ?>">
                        <option value=''>Select Any One </option>
                        <?php foreach($employees as $emp){ ?>
                        <option value="<?php echo $emp['id']; ?>" <?php echo $ealloc['emp_id']==$emp['id']?"selected":""; ?>><?php echo $emp['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <input type="hidden" id="equipment_allocation_id_<?php echo $cabincnt; ?>" value="<?php echo $ealloc['id']; ?>">

                 <span class="all_save_btn cursor-pointer" id="save_btn_<?php echo $cabincnt; ?>" onclick="submitEquipmentAllocations('<?php echo $cabincnt; ?>')" <?php echo $savebtncss; ?> data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Save" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-save "></i>  Save</span>
                <span class=" all_edit_btn cursor-pointer" id="edit_btn_<?php echo $cabincnt; ?>" onclick="editEquipmentAllocations('<?php echo $cabincnt; ?>')" <?php echo $editbtncss; ?> data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Edit" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-edit "></i>  Edit</span>

                <span id="equipment_allocation_btn_<?php echo $cabincnt; ?>" >
                    <?php if($ealloc['id']!=""){ ?>
                        <span class="cursor-pointer text-danger"  onclick="removeEquipmentAllocations('<?php echo $ealloc['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fa fa-trash m-l-10"></i> Remove</span>
                    <?php } ?>
                </span>


                <span class="text-danger sub_error d-block" id="submit_error_<?php echo $cabincnt; ?>" ></span>
            </div>
        </div>
    <?php   } ?>
    </div>
    <input type="hidden" id="equipment_allocation_seq"  value="<?php echo $editableseq; ?>">
    <input type="hidden" id="max_seq_number"  value="<?php echo $cabincnt; ?>">
    <?php if($_POST['transaction_type']!="AS" && $_REQUEST['eaid'] =="" && $_REQUEST['eq_id'] =="" && $_REQUEST['emp_id'] ==""){ ?>
        <button class="btn btn-success m-t-20" id="addRows" type="button" onclick="addMoreEquipmentAllocations()"><i class="fa fa-plus m-r-5"></i>Add More Equipment</button>
    <?php } ?>

<div class="row mt-5">
    <div id="floor_cabin_2">
        <img  src="uploads/building_floors/<?php echo $building_floors['floor_image']; ?>" alt="Attachment Preview">
        <?php
        $cabincnt = 0;
        foreach($equipment_allocations as $ealloc){  $cabincnt++;
            ?>
            <div id="position_<?php echo $cabincnt; ?>" class="equipment-allocation-icon " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $cabincnt; ?> Seat" style="top: <?php echo $ealloc['top_size']; ?>%; left: <?php echo $ealloc['left_size']; ?>%;"><?php echo $cabincnt; ?></div>
        <?php } ?>
        <?php
        $cabincnt = 0;
        foreach($remaining_equipment_allocations as $rea){  $cabincnt++;
            ?>
            <div class="equipment-allocation-icon " style="top: <?php echo $rea['top_size']; ?>%; left: <?php echo $rea['left_size']; ?>%;"><i class="ti-location-pin"></i></div>
        <?php } ?>
        <div id="extra_equipments_icon_3"></div>
        <input type="hidden" name="building_floor_space_id"  value="<?php echo $building_floor_space_id; ?>">
    </div>
</div>

</div>
<?php if($_POST['ajaxpage'] =="Y"){ ?>
    <script src="assets/bower_components/cropper/js/custom_cropper.js"></script>
    <script type="text/javascript">
          var defaultCropData = {
            x: <?php echo $building_floor_spaces['x_size']; ?>,   // Default x-coordinate
            y: <?php echo $building_floor_spaces['y_size']; ?>,   // Default y-coordinate
            width: <?php echo $building_floor_spaces['width']; ?>,   // Default width
            height: <?php echo $building_floor_spaces['height']; ?>   // Default height
          };
          var cropper = new Cropper(document.getElementById('disable_crop_floor_image'), {
            zoomable: false,
            viewMode: 3,
            cropper :false,
              crop: function(event) {    },
              data: defaultCropData
          });
          setTimeout(function () {
         cropper.disable();
         console.log('work');
     }, 1500);

          $('#floor_cabin').on('click', function(e) {

            var building_floor_space_id = $("#building_floor_space_id").val();
              var equipment_allocation_seq = $("#equipment_allocation_seq").val();
            var $this = $(this);
            console.log($this);
            var offset = $this.offset();
            var width = $this.width();
            var height = $this.height();
            var posX = offset.left;
            var posY = offset.top;
            var x = e.pageX-posX;
                x = parseInt(x/width*100,10);
                x = x<0?0:x;
                x = x>100?100:x;
            var y = e.pageY-posY;
                y = parseInt(y/height*100,10);
                y = y<0?0:y;
                y = y>100?100:y;
            console.log('Mouse click position - X:', x, 'Y:', y);

            $("#position_"+equipment_allocation_seq).css('left',x+'%');
            $("#position_"+equipment_allocation_seq).css('top',y+'%');
            $("#left_"+equipment_allocation_seq).val(x);
            $("#top_"+equipment_allocation_seq).val(y);

        });

    </script>

<?php } ?>

