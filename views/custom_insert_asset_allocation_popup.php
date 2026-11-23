<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API . "?mode=getassetallocation&admin_type=" . $_SESSION['admin_type'] . "&resultgettype=DIRECTASSETALLOCATE&assetid=" . $_POST['assetid'];
$data_details = callAPI($url);
$details = json_decode($data_details, 1);





$already_assets_list = $details['already_assets_list'];
$already_assets_list_ary = explode(",", $already_assets_list);
$locations = $details['locations'];
$departments = $details['departments'];
$costcenters = $details['costcenter'];
$application_users = $details['application_users'];
$list_assets = $details['list_assets'];
$repeateasset = '0';
foreach ($list_assets as $asslist) {
    if (in_array($asslist['id'], (array) $already_assets_list_ary)) {
        $repeateasset = '1';
    } else {
        $assetlits1[] = $asslist;
    }
}

if ($repeateasset == "1") { ?>
    <script>
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', "Oops Asset Already Alocated Please Unselect And re-Allocate Asset", '');
        setTimeout(function () { $('#kt_modal_dynamic_popup2').modal('hide'); }, 1000);

    </script>
<?php }


foreach ($details['asset_categories'] as $asset_category_list) {
    $asset_category_lists[$asset_category_list['id']] = $asset_category_list['category_description'];
}
foreach ($details['locations'] as $locations_list) {
    $locations_lists[$locations_list['id']] = $locations_list['location_name'];
}
?>
<script type="text/javascript">
    function selectAllocationType() {
        //$("#allocation_type).val();
        var selected = $("input[name='allocation_type']:checked").val();
        $(".select_caost_center").hide();
        $(".select_dept").hide();
        //$(".select_emp").hide();
        if (selected == "CC") {
            $(".select_caost_center").show();
        } else if (selected == "D") {
            $(".select_dept").show();
        } else {
            //$(".select_emp").show();
        }

    }
    function submitdetails() {
        var validations = ajaxFormSubmitValidation();
        if (validations == 0) {
            var formData = new FormData();
            formData.append("page", "insertassetallocation");
            var dataArray = $("#assetsallocationform1").serializeArray(),
                dataObj = {};
            $(dataArray).each(function (i, field) {
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
                success: function (data) {
                    console.log(data);
                    var res = JSON.parse(data);
                    if (res.error == "404") {
                        alert(<?php echo json_encode(__('oops Employee not found'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
                    } else {
                        //$('#kt_modal_KTDatatable_remote').modal("hide");
                        $('#kt_modal_dynamic_popup2').modal("hide");
                        tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success  pt-2 pb-2', 'animated flipInX', 'animated flipOutX', "Record Successfully Insert", '');
                        location.reload();
                    }
                }
            });
        }
    }

</script>

<div class="modal-header">
    <h4 class="modal-title"><?= __('Asset Allocation'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal"
        aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body">
    <div class="">
        <form id="assetsallocationform1" method="post">
            <div class="row ">
                <div class="dt-responsive table-responsive">
                    <table class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('Asset Code') ?></th>
                                <th scope="col"><?= __('Asset Name') ?></th>
                                <th scope="col"><?= __('Brand') ?></th>
                                <th scope="col"><?= __('Model') ?></th>
                                <th scope="col"><?= __('Serial Number') ?></th>
                                <!-- <th scope="col"><?= __('Location') ?></th>
                <th scope="col"><?= __('Status') ?></th>
                <th scope="col">Condition</th>  -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($assetlits1 as $list_asset) { ?>
                                <tr class="<?php echo $varianc_class; ?>">
                                    <td>
                                        <?php echo $list_asset['asset_code']; ?>
                                        <input type="hidden" name="asset_id[]" value="<?php echo $list_asset['id']; ?>">
                                        <input type="hidden" name="transferred_qty" value="1">
                                    </td>
                                    <td><?php echo $list_asset['asset_description']; ?></td>
                                    <td><?php echo $list_asset['brand']; ?></td>
                                    <td><?php echo $list_asset['model']; ?></td>
                                    <td><?php echo $list_asset['serial_no']; ?></td>
                                    <!-- <td class="w-full">
                            <select  class="form-select js-example-basic-single " name="asset_location[]" aria-label="Asset Location">
                                <option value=""><?= __('Select Any One') ?></option>
                                <?php foreach ($locations as $location) { ?>
                                    <option location value="<?php echo $location['id']; ?>" <?php echo $location['id'] == $list_asset['asset_location'] ? "selected" : ""; ?>><?php echo ucwords($location['location_name']); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td class="w-full"><?php ?>
                            <select  class="form-select js-example-basic-single" name="asset_status[]" aria-label="Asset Status">
                                <option value=""><?= __('Select Any One') ?></option>
                                <?php foreach ($ass_status as $ass_statuskey => $ass_statuss) { ?>
                                    <option  value="<?php echo $ass_statuskey; ?>" <?php echo $ass_statuskey == $list_asset['asset_status'] ? "selected" : ""; ?>><?php echo ucwords($ass_statuss); ?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td class="w-full">
                            <select  class="form-select js-example-basic-single " name="conditions[]" aria-label="Conditions">
                                <option value=""><?= __('Select Any One') ?></option>
                                <?php foreach ($ass_condition as $ass_conditiokey => $ass_conditio) { ?>
                                    <option  value="<?php echo $ass_conditiokey; ?>" <?php echo $ass_conditiokey == $list_asset['conditions'] ? "selected" : ""; ?>><?php echo ucwords($ass_conditio); ?></option>
                                <?php } ?>
                            </select>

                        </td> -->
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card p-2 mt-2">
                <div class="row">
                    <div class="col-lg-6">
                        <span class="f-w-700 d-inline-block" style="margin-bottom:5px"><?= __('Allocation Type') ?>
                            <span class="requried text-danger">*</span></span>
                        <div class="form-radio">
                            <div class="radio radio-inline">
                                <label class="form-label" for="allocation_type_e">
                                    <input type="radio" id="allocation_type_e" name="allocation_type" value="E"
                                        onclick="selectAllocationType()"
                                        onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
                                        style="display: none;" checked
                                        aria-label="<?= htmlspecialchars(__('Allocation Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="helper"></i><?= __('User') ?></label>
                            </div>
                            <div class="radio radio-inline">
                                <label class="form-label" for="allocation_type_cc">
                                    <input type="radio" id="allocation_type_cc" name="allocation_type" value="CC"
                                        onclick="selectAllocationType()"
                                        onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
                                        style="display: none;"
                                        aria-label="<?= htmlspecialchars(__('Allocation Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="helper"></i><?= __('Cost Centers') ?>
                                </label>
                            </div>
                            <div class="radio radio-inline">
                                <label class="form-label" for="allocation_type_d">
                                    <input type="radio" id="allocation_type_d" name="allocation_type" value="D"
                                        onclick="selectAllocationType()"
                                        onkeydown="if(event.key==='Enter'){event.preventDefault();this.click();}"
                                        style="display: none;"
                                        aria-label="<?= htmlspecialchars(__('Allocation Type'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="helper"></i><?= __('Departments') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-4 select_caost_center w-full" style="display:none">
                        <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('Cost Center') ?> <span
                                class="requried">*</span></span>
                        <select class="form-select js-example-basic-single " name="coast_center_id"
                            aria-label="<?= htmlspecialchars(__('Coast Center Id'), ENT_QUOTES, 'UTF-8'); ?>">
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach ($costcenters as $costcenter) { ?>
                                <option value="<?php echo $costcenter['id']; ?>">
                                    <?php echo ucwords($costcenter['cost_center_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-4 select_dept w-full" style="display:none">
                        <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('Department') ?> <span
                                class="requried">*</span></span>
                        <select class="form-select js-example-basic-single " name="departent_id"
                            aria-label="<?= htmlspecialchars(__('Departent Id'), ENT_QUOTES, 'UTF-8'); ?>">
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach ($departments as $departments) { ?>
                                <option value="<?php echo $departments['id']; ?>">
                                    <?php echo ucwords($departments['department_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-4 w-full">
                        <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('User') ?> <span
                                class="requried">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <select class="form-select js-example-basic-single requried_field" name="select_user_id"
                            aria-label="<?= htmlspecialchars(__('Select User Id'), ENT_QUOTES, 'UTF-8'); ?>">
                            <option value=""><?= __('Select Any One') ?></option>
                            <?php foreach ($application_users as $ausr) { ?>
                                <option value="<?php echo $ausr['id']; ?>"><?php echo ucwords($ausr['display_name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('Start date') ?> <span
                                class="requried text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input type="text" class="form-control datepicker requried_field" id="start_date"
                            name="start_date" value="<?php echo fromDate(); ?>"
                            aria-label="<?= htmlspecialchars(__('Start Date'), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class="form-group col-lg-4">
                        <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('End date') ?> <span
                                class="requried text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input type="text" class="form-control datepicker requried_field" id="end_date" name="end_date"
                            aria-label="<?= htmlspecialchars(__('End Date'), ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <div class=" col-lg-5">
                        <span class="d-inline-block fw-bold"
                            style="margin-bottom:5px"><?= __('How Do You Want The Asset To be Delivered?') ?><span
                                class="requried">*</span></span>
                        <div class="form-radio">
                            <div class="radio radio-inline">
                                <label class="form-label">
                                    <input type="radio" name="asset_deliver" value="S" style="display:none;" <?php echo ($request['asset_deliver'] == "S" || $request['asset_deliver'] == "") ? "checked" : ""; ?>
                                        aria-label="<?= htmlspecialchars(__('Asset Deliver'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="helper"></i><?= __('Self Pickup') ?>
                                </label>
                            </div>
                            <div class="radio radio-inline">
                                <label class="form-label">
                                    <input type="radio" name="asset_deliver" value="D" style="display:none" <?php echo $request['asset_deliver'] == "D" ? "checked" : ""; ?>
                                        aria-label="<?= htmlspecialchars(__('Asset Deliver'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <i class="helper"></i><?= __('Deliver Through Logistics Partner') ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-lg-7">
                        <span class="d-inline-block fw-bold" style="margin-bottom:5px"><?= __('Remarks') ?> </span>
                        <textarea type="text" class="form-control" id="remarks" rows="4" cols="50" name="remarks"
                            aria-label="<?= htmlspecialchars(__('Remarks'), ENT_QUOTES, 'UTF-8'); ?>"></textarea>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close') ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"
        onclick="submitdetails()"><?= __('Submit') ?></button>
</div>

<script type="text/javascript">
    // $(".js-example-basic-single").select2();
    // $(".js-example-disabled-results").select2();
    // $( ".datepicker" ).datepicker({
    //     showOn: "both",
    //     buttonText: "<i class='ti-calendar'></i>",
    //     dateFormat: date_format,
    //     changeMonth: true,
    //     changeYear: true
    // });
</script>
