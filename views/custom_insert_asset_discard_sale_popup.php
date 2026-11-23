<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=DISCARDASSET&assetid=".$_POST['assetid'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$locations = $details['locations'];
$departments = $details['departments'];
$costcenters = $details['costcenter'];
$list_assets = $details['list_assets'];
$employees = $details['employees'];
?>
<script type="text/javascript">
    function selectAllocationType(){
        //$("#allocation_type).val();
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
function submitdetails(){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){
          var formData = new FormData();
        formData.append("page", "insertassetallocation");
        formData.append("discardasset", "Y");
        var dataArray = $("#assetsallocationform").serializeArray(),
        dataObj = {};
        $(dataArray).each(function(i, field){
          formData.append(field.name, field.value);
        });
      //  var popattachment = document.getElementById('popattachment').files[0];
      // formData.append("file",popattachment);
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                 $('#kt_modal_dynamic_popup2').modal("hide");
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"<?php echo $_SESSION['error_message'][3002]; ?>",'');
                location.reload();
            }
        });
    }
}

</script>

<div class="modal-header">
    <h4 class="modal-title"><?= __('Discard Or Sales'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body j-pro">
    <div class="j-content">
    <form id="assetsallocationform" method="post" >
        <div class="row ">
            <div class="dt-responsive table-responsive">
    <table  class="table table-striped table-bordered nowrap">
        <thead>
            <tr>
                <th scope="col"><?= __('Asset Code') ?></th>
                <th scope="col"><?= __('Asset Name') ?></th>
                <th scope="col"><?= __('Brand') ?></th>
                <th scope="col"><?= __('Model') ?></th>
                <th scope="col"><?= __('Serial Number') ?></th>
                
                
                
            </tr>
        </thead>
        <tbody>
            <?php foreach($list_assets as $list_asset){ ?>
                    <tr class="<?php echo $varianc_class; ?>">
                        <td>
                            <?php echo $list_asset['asset_code']; ?>
                            <input type="hidden" name="asset_id[]" value="<?php echo $list_asset['id']; ?>">
                        </td>
                        <td><?php echo $list_asset['asset_description']; ?></td>
                        <td><?php echo $list_asset['brand']; ?></td>
                        <td><?php echo $list_asset['model']; ?></td>
                        <td><?php echo $list_asset['serial_no']; ?></td>
                        
                        
                        
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
        </div>
    <div class="card p-2 mt-2">
        <div class="row">

            <div class="form-group col-lg-4">
                <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('Discard/ Sales date') ?> <span class="requried text-danger">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <input aria-label="<?= htmlspecialchars(__('Discard and sales date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker requried_field" id="discard_and_sales_date"  name="discard_and_sales_date" value="<?php echo fromDate(); ?>">
            </div>
            <div class="form-group col-lg-8">
                <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('Reason') ?> <span class="requried text-danger">*</span></span>
                <span class="d-block error-message text-danger"></span>
                <textarea aria-label="<?= htmlspecialchars(__('Discard and sales remarks'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control requried_field" id="remarks" rows="4" cols="50" name="discard_and_sales_remarks" ></textarea>
            </div>
        </div>
    </div>

    </form>
    </div>
</div>

<div class="modal-footer">
    <p><?= __('Permanant Remove The Assets'); ?></p>
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close') ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()"><?= __('Submit') ?></button>
</div>

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
