<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['building_id'];
$url = API."?mode=getbuildings&rid=".$rid;
$response = callAPI($url);
$result = json_decode($response,1);
$buildings = $result['buildings'];
$building_floors = $result['building_floors'];
$building_floor_spaces = $result['building_floor_spaces'];
$building_admin_application_users = $result['building_admin_application_users'];
$locations = $result['locations'];
?>
<script type="text/javascript">
    var maxrows = 1000;
    function addMoreFloorPlans(){
        maxrows = maxrows+1;
        rowid = "floor_row_id_"+maxrows;
        var htmlrows = '<div class="row" id="'+rowid+'">';
        htmlrows += '<div class="form-group col-lg-6"><input aria-label="Floor Name[]" class="form-control" type="text" name="floor_name[]" value=""> <input class="form-control" type="hidden" name="building_floors_id[]" value="" > </div>';
        htmlrows += '<div class="form-group col-lg-4"><input aria-label="Floor Image" class="form-control onchangeimagepreview" data-id="imagepreview_'+maxrows+'" type="file" name="floor_image" value="" accept=".jpg,.png"><input class="form-control" type="hidden" name="old_floor_image[]" value="" ></div>';

        htmlrows += '<div class="form-group col-lg-1"><img src="uploads/all/no-image.jpg" class="img-40" id="imagepreview__2"+maxrows+'" alt="Attachment Preview" />  </div>';

        htmlrows += '<div class="form-group col-lg-1 text-center"><button type="button" class="btn p-0 border-0 bg-transparent fa fa-trash text-danger cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeBuildingFloorPlans(\''+rowid+'\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>';
        $("#newrowadd").append(htmlrows);
    }
     function submitBuildingFloorPlans(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            var formData = new FormData();
            formData.append("page", "insertbuildings");
            var dataArray = $("#insertbuildings").serializeArray(),
            dataObj = {};
            $(dataArray).each(function(i, field){
                formData.append(field.name, field.value);
            });
            $.each($("input[name=floor_image]"), function(i, obj) {
                  $.each(obj.files,function(j, file){
                      formData.append('file'+i, file);
                  })
            });
            var building_image = document.getElementById('building_image').files[0];
            formData.append("building_image",building_image);
            jQuery.ajax({
                url: "ajax.php",
                data: formData,
                type: "POST",
                contentType: false,
                processData: false,
                success:function(response){
                    console.log(response);
                    var result = JSON.parse(response);
                    console.log(result);
                    if(result.rid == '0'){
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');

                    }
                    $('#kt_modal_dynamic_popup2').modal("hide");
                    getBuildingDynamicPage('B');
                },
                error: function (data) {
                    swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "<?php echo $_SESSION['error_message']['9135']; ?>", "error");
                }
            });
        }
    }
    function removeBuildingFloorPlans(idd,id){
        if(id !=""){
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: '<?php echo $_SESSION['error_message']['9118']; ?>',
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
                        data:'page=softdeletedrecord&id='+id+'&table=building_floors',
                        type: "POST",
                        success:function(response){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3002']; ?>','');
                            $("#"+idd).remove();
                        }
                    });
                }
            });
        }else{
            $("#"+idd).remove();
        }
    }
    function closeInsertBuilding(){
        var urlParams = new URLSearchParams(window.location.search);
        var param_value = "B";
        if (urlParams.has('TAB1')) {
            param_value = urlParams.get('TAB1');
            param_value = (param_value !=null || param_value !="")?param_value:"B";
        }
        getBuildingDynamicPage(param_value);
    }
</script>
<div class="modal-header  ">
    <h5 class="modal-title"> Building Floors</h5>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close" onclick="closeInsertBuilding('B')"></button>
</div>
<div class="modal-body p-l-50 p-r-50 p-t-50">
    <form method="post" id="insertbuildings" enctype="multipart/form-data">
    <div class="row">
        <div class="col-lg-10">
            <div class="row">
                <div class="form-group col-lg-6">
                    <span  class="form-label">Building Name <span class="text-danger">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <input aria-label="Building Name" class="form-control requried_field" type="text" id="building_name" name="building_name" value="<?php echo $buildings['building_name']; ?>">
                </div>
                <div class="form-group col-lg-6">
                    <span  class="form-label">Custodian <span class="text-danger">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <select aria-label="Originator[]" class="js-example-basic-multiple requried_field" name="originator[]" multiple>
                        <?php
                        $originator = json_decode($buildings['originator'],1);
                        foreach($building_admin_application_users as $baau){ ?>
                        <option value="<?php echo $baau['id']; ?>" <?php echo in_array($baau['id'], (array)$originator)?"selected":""; ?>><?php echo $baau['display_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <span  class="form-label">Location <span class="text-danger">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <select aria-label="Location" class="js-example-basic-single requried_field" name="location_id">
                        <option value="">Select Any One</option>
                        <?php foreach($locations as $loc){ ?>
                        <option value="<?php echo $loc['id']; ?>" <?php echo $loc['id']==$buildings['location_id']?"selected":""; ?>><?php echo $loc['location_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <span  class="form-label">Address <span class="text-danger">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <textarea aria-label="Building Address" class="form-control requried_field" id="building_address" name="building_address" ><?php echo $buildings['building_address']; ?></textarea>
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <span class="form-label">Featured Image</span>
            <div class="col-lg-9 col-xl-6">
                <div class="profile-pic">
                    <label class="-label" for="building_image">
                        <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                    </label>
                    <input type="file" name="building_image" id="building_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                    <img src="<?php echo "uploads/all/".$buildings['building_image'] ; ?>" id="outputbuilding_image" width="200" alt="Attachment Preview" />
                    <input type="hidden" name="old_image" value="<?php echo $buildings['building_image'] ;  ?>">
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-lg-6">
            <span  class="form-label">Floor Name</span>
        </div>
        <div class="form-group col-lg-5">
            <span  class="form-label">Floor Image</span>
        </div>
        <div class="form-group col-lg-1">
             <span  class="form-label">Action</span>
        </div>
    </div>
    <div id="newrowadd">
        <?php if(count((array)$building_floors) =='0'){ $building_floors[] = array();
} $mk=1; foreach($building_floors as $bf){ ?>
        <div class="row" id="floor_row_id_<?php echo $mk; ?>">
            <div class="form-group col-lg-6">
                <input class="form-control" type="hidden" name="building_floors_id[]" value="<?php echo $bf['id']; ?>" >
                <input aria-label="Floor Name[]" class="form-control" type="text" name="floor_name[]" value="<?php echo $bf['floor_name']; ?>">
            </div>
            <div class="form-group col-lg-4">
                <input aria-label="Input field" class="form-control onchangeimagepreview" data-id="imagepreview_<?php echo $mk; ?>" type="file" name="floor_image" value="" accept=".jpg,.png">
                <input class="form-control " type="hidden" name="old_floor_image[]" value="<?php echo $bf['floor_image']; ?>" >

            </div>
            <div class="form-group col-lg-1">
                <?php if($bf['floor_image'] !=""){ ?>
                <a href="uploads/building_floors/<?php echo $bf['floor_image']; ?>" class="text-primary" download>
                    <img src="uploads/building_floors/<?php echo $bf['floor_image']; ?>" class="img-40" id="imagepreview_<?php echo $mk; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download Floor plan"  alt="Download Floor plan" /></a>
                <?php }else{  ?>
                <img class="img-40" src="" id="imagepreview_<?php echo $mk; ?>" alt="Attachment Preview" />
                <?php } ?>
            </div>
            <div class="form-group col-lg-1 text-center">
                <i class="fa fa-trash text-danger cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeBuildingFloorPlans('floor_row_id_<?php echo $mk; ?>','<?php echo $bf['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
            </div>
        </div>
        <?php $mk++;
} ?>
    </div>
    <button class="btn btn-success m-t-20" id="addRows" type="button" onclick="addMoreFloorPlans()"><i class="fa fa-plus m-r-5"></i>Add More</button>
     <input type="hidden" name="rid" value="<?php echo $rid; ?>">
     </form>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" onclick="getBuildingDynamicPage('B')">Close</button>
    <button type="button" name="submit" onclick="submitBuildingFloorPlans()" class="input-group-text addon-style cursor-pointer float-left btn btn-primary" name="submitesopplanse"> <?php echo $rid !=""?"Update":"Submit";?></button>
</div>

