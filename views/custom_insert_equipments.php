<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$eq_id = $_POST['eq_id'];
$url = API."?mode=getequipments&eq_id=".$eq_id;
$response = callAPI($url);
$result = json_decode($response,1);
$equipment_types = $result['equipment_types'];
$equipments = $result['equipments'];
$display_attributes = $result['display_attributes'];
$file_upload = json_decode($display_attributes['file_upload'],1);
$fileaccetformat = implode(',',(array)$file_upload['accepted_files']);
$accepted_files =$accepted_file_message= "";
if($fileaccetformat !=""){
    $accepted_files = 'accept="'.$fileaccetformat.'"';
    $accepted_file_message =  'Allowed File Types '.$fileaccetformat.'. ';
}
if($file_upload['file_size'] !=""){
    $accepted_files = 'accept="'.$fileaccetformat.'"';
    $accepted_file_message .=  '</br> Maximum Upload Size Is - '.$file_upload['file_size'].' Bytes';
}
?>
<script type="text/javascript">
function submitEquipmentdetailBTN(){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){

        var formData = new FormData();
        formData.append("page", "intertequipments");
        var dataArray = $("#intertequipment_form").serializeArray(),
        dataObj = {};
        $(dataArray).each(function(i, field){
            if(field.name == 'equipment_type_id'){
                if(field.value !=""){
                    var split_val = field.value.split("||");
                    formData.append(field.name, split_val[0]);
                }
            }else{
                formData.append(field.name, field.value);
            }
        });
        var equipment_image = document.getElementById('equipment_image').files[0];
        formData.append("equipment_image",equipment_image);
        $.ajax({
              url: "ajax.php",
              data: formData,
              type: "POST",
              contentType: false,
            processData: false,
              success:function(data){
                console.log(data);
                var datas = JSON.parse(data);
                if(datas['status'] == "fail"){
                  swal("<?php echo ucwords($_SESSION['config']['name']); ?>", datas['message'], "error");
                }else{
                  if(datas['LASTID'] == "0"){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                 }else{
                  tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                }
                closeInsertEquipment();
                $('#kt_modal_dynamic_popup2').modal("hide");
              }
        }
    });
    }
}
function getEquipmentTypePopup(eq_type_val){
    if(eq_type_val !=""){
        var eq_type = eq_type_val.split("||");
        if(eq_type[1] == 'Y'){
            $("#employee_seating_popup").text("Yes");
        }else{
            $("#employee_seating_popup").text("No");
        }
        $("#employee_seating_popup_val").val(eq_type[1]);
    }
}

function closeInsertEquipment(){
    var urlParams = new URLSearchParams(window.location.search);
    var param_value = "ASSETS";
    if (urlParams.has('TAB1')) {
        param_value = urlParams.get('TAB1');
        param_value = (param_value !=null || param_value !="")?param_value:"ASSETS";
    }
    getBuildingDynamicPage(param_value);
}
</script>

<div class="modal-header  ">
    <h5 class="modal-title"> Equipments</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <form id="intertequipment_form">
        <input type="hidden" name="eq_id" value="<?php echo $eq_id; ?>">
    <div class="row">
        <div class="col-lg-10">
            <div class="row">
                <div class=" form-group col-lg-5">
                    <span class="form-label">Equipment Name <span class="requried">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <input aria-label="Equipment Name" type="text" class="form-control requried_field" name="equipment_name" value="<?php echo $equipments['equipment_name']; ?>">
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Equipment Type <span class="requried">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <select aria-label="Equipment Type" class="form-select js-example-basic-single requried_field" id="equipment_type_id" name="equipment_type_id"  onchange="getEquipmentTypePopup(this.value)">
                        <option value="" > Select Any One</option>
                         <?php $employee_seating_text = "No"; $employee_seating_val = 'N'; foreach($equipment_types as $equtype){
                             $selected = "";
                                 if($equtype['id'] == $equipments['equipment_type_id']){
                                     $selected = "selected";
                                     if($equtype['seating_equipment'] == 'Y'){
                                         $employee_seating_text = "Yes";
                                         $employee_seating_val = 'Y';
                                     }
                                 }

                             ?>
                             <option value="<?php echo $equtype['id']."||".$equtype['seating_equipment']; ?>" <?php echo $selected;  ?>> <?php echo $equtype['equipment_types_name']; ?></option>
                         <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-3">
                    <span class="form-label d-block">Seating Equipment</span>
                    <span class="" id="employee_seating_popup"><?php echo $employee_seating_text; ?></span>
                    <input type="hidden" class="form-control" name="employee_seating" id="employee_seating_popup_val" value="<?php echo $employee_seating_val; ?>">
                </div>
                <div class="form-group col-lg-12">
                    <span class="form-label">Description</span>
                    <textarea aria-label="Description" class="form-control" name="description"><?php echo $equipments['description']; ?></textarea>
                </div>
                <?php if($equipments['qr_code'] !=""){ ?>
                <div class=" form-group col-lg-6">
                    <span class="form-label d-block">QR Code</span>
                    <img src="<?php echo "uploads/equipments/qr_code/".$equipments['qr_code'] ; ?>" class="img-100"  width="200" alt="QR Code" />
                </div>
                <?php }
if($equipments['id'] !=""){ ?>
                <div class=" form-group col-lg-6">
                    <span class="form-label d-block">Barcode </span>

                    <img src="<?php echo "barcode/barcode.php?text=".$equipments['id'].'-'.$equipments['equipment_code']; ?>" width="300" alt="Attachment Preview" />
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="form-group col-lg-2">
            <span class="form-label d-block">Equipment Image </span>
            <span class="fileinputvalidation_error text-danger d-block"></span>
            <div class="profile-pic">
                <label class="-label" for="equipment_image">
                    <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                </label>
                <input type="file" class="fileinputvalidation" name="equipment_image" id="equipment_image"  <?php echo $accepted_files; ?> data-size="<?php echo $file_upload['file_size']; ?>" onchange="loadFile(event,this.id)"/>
                <img src="<?php echo "uploads/equipments/".$equipments['equipment_image'] ; ?>" id="outputequipment_image" width="200" alt="Attachment Preview" />
                <input type="hidden" name="old_equipment_image" value="<?php echo $equipments['equipment_image'] ;  ?>">
            </div>
            <span class="text-danger d-block f-12"><?php echo $accepted_file_message; ?></span>
        </div>
    </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitEquipmentdetailBTN()">Submit</button>
</div>
