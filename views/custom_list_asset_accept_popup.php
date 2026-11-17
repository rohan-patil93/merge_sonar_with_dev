include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$emp_assets_id = $_POST['id'];
$url = API."?mode=getassetallocation&list=Y&acceptreq=Y&assetid=".$_POST['assetid']."&employee=".$_SESSION['emp_id'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$locations = $details['locations'];
$departments = $details['departments'];
$costcenters = $details['costcenter'];
$list_assets = $details['list_assets'];
$employees = $details['employees'];
$ass_condition = array('1'=>'Used','2'=>'Good','3'=>'Poor','4'=>'Damaged','New');
foreach($details['asset_categories'] as $asset_category_list){
    $asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
}
foreach($details['locations'] as $locations_list){
    $locations_lists[$locations_list['id']] = $locations_list['location_name'];
}
foreach($details['asset_status'] as $asset_status){
    $ass_status[$asset_status['id']] = $asset_status['name'];
}
foreach($details['emp_assetres'] as $emp_assetr){
    $emp_asset[$emp_assetr['asset_id']] = $emp_assetr;
}
$wh1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'category_id'=>$_POST['cat_id']);
$wh1['field'] = "id,name";
$data1 = urlencode(json_encode($wh1));
$url1 = API."?mode=gettabledetailswithconditions&table=asset_attributes&data=".$data1;
$data_details1 = callAPI($url1);
$attributes = json_decode($data_details1,1);

?>
<script type="text/javascript">
    function selectAllocationType(){
        var selected = $("input[name='allocation_type']:checked").val();
        $(".select_caost_center").hide();
        $(".select_dept").hide();
        $(".select_emp").hide();
        if(selected=="CC"){
            $(".select_caost_center").show();
        }else if(selected=="D"){
            $(".select_dept").show();
        }else{
            $(".select_emp").show();
        }
    }
function submitdetails(allocation_type){

      var formData = new FormData();
    formData.append("page", "assetaccepts");
    formData.append("allocation_type", allocation_type);
    var dataArray = $("#assetsallocationform").serializeArray(),
    dataObj = {};
    $(dataArray).each(function(i, field){
      formData.append(field.name, field.value);
    });
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                $('#kt_modal_dynamic_popup2').modal("hide");
                console.log(data);
                location.reload();

            }
        });
}

function checkextendtype(){
    var selected = $(".extension_type:checked").val();
    $(".ext_date").hide();
    $(".ext_attr").hide();
    if(selected=="D"){
        $(".ext_date").show();
    }else{
        $(".ext_attr").show();
    }
}
function submitextension(){
    var formData = $("#assetextensionform").serialize();
    jQuery.ajax({
        url: "ajax.php",
        data: "page=insertassetextension&"+formData,
        type: "POST",
        success:function(data){
            console.log(data);
                var res = JSON.parse(data);
                if(res.error=="404"){
                    alert(<?php echo json_encode(__('Oops Admin buddy Not Found'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
                }else{
                    $('#kt_modal_dynamic_popup2').modal("hide");
                }
        }
    });
}
</script>
<?php if($_POST['extra']=='Y'){?>

<div class="modal-header">
    <h4 class="modal-title"><?= __('Asset Extension'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body ">
    <div >
    <form id="assetextensionform" method="post" >
        <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
        <input type="hidden" name="assetid" value="<?php echo $_POST['assetid']; ?>">
        <div class="card p-2 mt-2">
            <div class="row">
        <div class=" col-lg-4 " >
                <span><?= __('Extension Type'); ?></span>
                <div class="form-radio">
                <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="<?= htmlspecialchars(__('Extension Type'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="extension_type" class="extension_type" value="D" style="display:none;" onclick="checkextendtype()"  checked onkeydown="">
                        <i class="helper"></i><?= __('Extend Date'); ?>
                      </label>
                    </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="<?= htmlspecialchars(__('Extension Type'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="extension_type" value="A" class="extension_type" onclick="checkextendtype()" style="display:none" onkeydown="">
                        <i class="helper"></i><?= __('Extend Attributes'); ?>
                      </label>
                    </div>
                 </div>
            </div>
    <div class=" col-lg-4 " >
                <span><?= __('How Do You Want The Asset To be Delivered?'); ?><span class="requried">*</span></span>
                <div class="form-radio">
                <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="<?= htmlspecialchars(__('Asset Deliver'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="asset_deliver" value="S" style="display:none;" checked>
                        <i class="helper"></i><?= __('Self Pickup'); ?>
                      </label>
                    </div>
                    <div class="radio radio-inline">
                      <label class="form-label">
                        <input aria-label="<?= htmlspecialchars(__('Asset Deliver'), ENT_QUOTES, 'UTF-8'); ?>" type="radio" name="asset_deliver" value="D" style="display:none" >
                        <i class="helper"></i><?= __('Deliver Through Logistics Partner'); ?>
                      </label>
                    </div>
                 </div>
            </div>
                <div class="form-group col-lg-4 ext_date">
                    <span><?= __('Extend date'); ?></span>
                    <input aria-label="<?= htmlspecialchars(__('Extension Date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker" id="extension_date"  name="extension_date" value="">
                </div>
                <div class="form-group col-lg-4 ext_attr" style="display: none">
                    <span><?= __('Extend Attributes'); ?></span></br>
                        <?php
                    $explodeattribute = explode(",",$_POST['attribute']);
                    foreach($attributes as $asset_attributes){
                        $checked = in_array($asset_attributes['id'],$explodeattribute)?"checked disabled":"";
                        echo $checkattributes='<div class="checkbox-fade fade-in-primary">
                                              <label class="form-label">
                                                <input aria-label="Attribute[]" type="checkbox" name="attribute[]" '.$checked.' value="'.$asset_attributes['id'].'" >
                                                <span class="cr">
                                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                                <span>'.$asset_attributes['name'].'</span>
                                              </label>
                                            </div>';
                    }
                ?>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitextension()"><?= __('Submit'); ?></button>
</div>

<?php }else{  ?>
<div class="modal-header">
    <h4 class="modal-title"><?= __('Asset Allocation'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body">
    <div class="">
    <form id="assetsallocationform" method="post" >
        <div class="row ">
            <div class="dt-responsive table-responsive">
    <table  class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th scope="col"><?= __('Asset Code'); ?></th>
                <th scope="col"><?= __('Asset Name'); ?></th>
                <th scope="col"><?= __('Brand'); ?></th>
                <th scope="col"><?= __('Model'); ?></th>
                <th scope="col"><?= __('Serial Number'); ?></th>
                <th scope="col"><?= __('Location'); ?> </th>

                <th scope="col"><?= __('Satus'); ?></th>
                <th scope="col"><?= __('Condition'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($list_assets as $list_asset){ ?>
                    <tr class="<?php echo $varianc_class; ?>">
                        <td>
                            <?php echo $list_asset['asset_code']; ?>
                            <input type="hidden" name="asset_id" value="<?php echo $list_asset['id']; ?>">
                            <input type="hidden" name="emp_assets_id" value="<?php echo $emp_assets_id; ?>">
                            <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
                        </td>
                        <td><?php echo $list_asset['asset_description']; ?></td>
                        <td><?php echo $list_asset['brand']; ?></td>
                        <td><?php echo $list_asset['model']; ?></td>
                        <td><?php echo $list_asset['serial_no']; ?></td>
                        <td>
                            <?php echo $locations_lists[$list_asset['asset_location']]; ?></td>
                        <td><?php echo $ass_status[$list_asset['asset_status']]; ?></td>

                        <td>
                            <select aria-label="<?= htmlspecialchars(__('Conditions'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-select js-example-basic-single " name="conditions">
                                <option value=""><?= __('Select Any One'); ?></option>
                                <?php foreach ($ass_condition as $ass_conditiokey=>$ass_conditio) { ?>
                                    <option  value="<?php echo $ass_conditiokey; ?>" <?php echo $ass_conditiokey == $list_asset['conditions']?"selected":""; ?>><?php echo ucwords($ass_conditio); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
        </div>
    <div class="card p-2 mt-2">
        <div class="row">
            <div class="form-group col-lg-3">
                <span><?= __('Received date'); ?></span>
                <input aria-label="<?= htmlspecialchars(__('Received Date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker" id="received_date"  name="received_date" value="">
            </div>
            <div class="form-group col-lg-9">
                <span><?= __('Note'); ?></span>
                <textarea aria-label="<?= htmlspecialchars(__('Received Note'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" id="received_note" rows="4" cols="50" name="received_note" ></textarea>
            </div>
        </div>
    </div>
    </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails('1')"><?= __('Accept'); ?></button>
    <button type="button" class="btn btn-danger waves-effect waves-light"  onclick="submitdetails('2')"><?= __('Reject'); ?></button>
</div>
<?php } ?>

<script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
<script type="text/javascript">
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
</script>
