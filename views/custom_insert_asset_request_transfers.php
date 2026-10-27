<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url1 = API."?mode=getassettransferrequests&admin_type=".$_SESSION['admin_type']."&rid=".$_POST['rid']."&categroy_list=".$_POST['categroy_list']."&asset_status=".$_POST['asset_status']."&asset_condition=".$_POST['asset_condition'];
$data_details1 = callAPI($url1);
$result = json_decode($data_details1,1);
$asset_requests = $result['asset_requests'];
$asset_categories = $result['asset_categories'];
$locations = $result['locations'];
$asset_status = $result['asset_status'];
$asset_condition_type = $result['asset_condition_type'];
$assets = $result['assets'];
$asset_request_transfers = $result['asset_request_transfers'];
$all_assets = $result['all_assets'];
$already_transfer_quantity = count((array)$asset_request_transfers);
$statuse = ViewStatusData();
?>
<script type="text/javascript">
function selctAssetID(check_assetid){
    var pending_qty = $("#pending_qty").val();
    pending_qty = pending_qty!=""?pending_qty:0;
    var asset_id = [];
    var selectcnt = 0;
    $('.select_asset_id').each(function(){
        if($(this).is(':checked')){
            selectcnt = selectcnt+1;
            asset_id.push($(this).val());
        }
    });
    if(pending_qty < selectcnt){ // check qty and uuncheck
        console.log("uncehckl");
        var select_asset_value = $('#allocate_asset_id_'+check_assetid).val();
        $('#allocate_asset_id_'+check_assetid).prop('checked', false);
        var asset_id = asset_id.filter(function(value) {
            return value !== select_asset_value;
        });
        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Assets <?php echo $_SESSION['error_message'][6030]; ?> Pending Quantity", "warning");
        selectcnt = selectcnt-1;
    }
    var asset_idjson = JSON.stringify(asset_id);
    $("#asset_id").val(asset_idjson);
    $("#transferred_qty").val(selectcnt);
}
function filetrassett(){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#assetlistid").html(loader);
    var rid = $("#asset_requests_id").val();
    var categroy_list = [];
    $('.categroy_list').each(function(){
        if($(this).is(':checked')){
            categroy_list.push($(this).val());
        }
    });
    var asset_status = [];
    $('.asset_status').each(function(){
        if($(this).is(':checked')){
            asset_status.push($(this).val());
        }
    });
    var asset_condition = [];
    $('.asset_condition').each(function(){
        if($(this).is(':checked')){
            asset_condition.push($(this).val());
        }
    });
    jQuery.ajax({
        url: "views/custom_insert_asset_request_transfers_ajax.php",
        data: "filterapply=Y&rid="+rid+"&categroy_list="+categroy_list+"&asset_status="+asset_status+"&asset_condition="+asset_condition,
        type: "POST",
        success:function(data){
                $('#assetlistid').html(data);
        }
    });
}

function submitAssetRequestTransfer(){
    var transferred_qty = $("#transferred_qty").val();
    console.log(transferred_qty);
    if(transferred_qty == "" || transferred_qty ==0){
        $("#transferred_qty_error").text('Please Enter Required Fields');
    }else{
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            var postarray = $("#assetrequesttransferform").serialize();
            console.log(postarray);
            jQuery.ajax({
                url: "ajax.php",
                data: 'page=insertassetrequesttransfers&'+postarray,
                type: "POST",
                async:false,
                success:function(data){
                    console.log(data);
                    var result = JSON.parse(data);
                    if(result.res.status == "sucess"){
                        $('#kt_modal_dynamic_popup2').modal("hide");
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000'];?>','');
                        location.reload();
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.res.message,'');
                    }
                }
            });
        }
    }
}
function getDeliveryType(){
        var delivery_type = $("input[name='delivery_type']:checked").val();
        if(delivery_type == "D"){
            $("#popup_asset_delivery_address").show();
        }else{
            $("#popup_asset_delivery_address").hide();
        }
        console.log(delivery_type);
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Asset Requests</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="card m-r-50 m-l-50">
        <div class="row">
            <div class="col-3 text-center">
                <div class="s-cont d-inline-block">
                    <span class="f-w-700 m-b-0">User</span>
                    <p class="m-b-0 f-10"> <?php echo $_SESSION['users'][$asset_requests['originator']]; ?></p>
                </div>
            </div>
            <div class="col-3 b-l-default text-center">
                <div class="s-cont d-inline-block">
                    <span class="f-w-700 m-b-0">Asset Category</span>
                    <p class="m-b-0 "> <?php echo $asset_categories[$asset_requests['category_id']]['category_description']; ?> </p>
                </div>
            </div>
            <div class="col-2 b-l-default text-center">
                <div class="s-cont d-inline-block">
                    <span class="f-w-700 m-b-0">Request Date</span>
                    <p class="m-b-0 f-10"> <?php echo $asset_requests['request_datetime'] !=""?fromDate($asset_requests['request_datetime']):"-"; ?></p>
                </div>
            </div>
            <div class="col-2 b-l-default text-center">
                <div class="s-cont d-inline-block">
                    <span class="f-w-700 m-b-0">Required From</span>
                    <p class="m-b-0 f-10"> <?php echo $asset_requests['asset_required_start_date'] !=""?fromDate($asset_requests['asset_required_start_date']):"-"; ?> </p>
                </div>
            </div>
            <div class="col-2 b-l-default text-center">
                <div class="s-cont d-inline-block">
                    <span class="f-w-700 m-b-0">Required To</span>
                    <p class="m-b-0 f-10"> <?php echo $asset_requests['asset_required_end_date'] !=""?fromDate($asset_requests['asset_required_end_date']):"-"; ?> </p>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-lg-6">
                <span class="f-w-700 mb-2 col-lg-12">Catgeory</span>
                <?php foreach ($asset_categories as $asset_category_id=>$asscat){ ?>
                    <div class="checkbox-fade fade-in-primary">
                        <label class="form-label">
                            <input aria-label="Checkbox" type="checkbox" class="categroy_list"  value="<?php echo $asset_category_id;?>" <?php echo in_array($asset_category_id,(array)$filterassetcategory)?"checked":"";?> onclick="filetrassett()" onkeydown="">
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                            <span><?php echo ucwords($asscat['category_description']); ?></span>
                        </label>
                    </div>
                <?php } ?>
            </div>
            <div class="col-lg-3">
                <span class="f-w-700 mb-2 col-lg-12">Status</span>
                <?php foreach ($asset_status as $ass_statu){ ?>
                    <div class="checkbox-fade fade-in-primary">
                        <label class="form-label">
                            <input aria-label="Checkbox" type="checkbox" class="asset_status" <?php echo in_array($ass_statu['id'],(array)$filterassetstatus)?"checked":"";?> value="<?php echo $ass_statu['id'];?>" onclick="filetrassett()" onkeydown="">
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                            <span><?php echo $ass_statu['name']; ?></span>
                        </label>
                    </div>

                <?php } ?>
            </div>
            <div class="col-lg-3">
                <span class="f-w-700 mb-2 col-lg-12">Condition</span>
                <?php foreach ($asset_condition_type as $ass_conditiokey=>$ass_conditio) { ?>
                    <div class="checkbox-fade fade-in-primary">
                        <label class="form-label">
                            <input aria-label="Checkbox" type="checkbox" <?php echo in_array($ass_conditiokey,(array)$filterassetcondition)?"checked":"";?>  class="asset_condition" value="<?php echo $ass_conditiokey;?>" onclick="filetrassett()" onkeydown="">
                            <span class="cr">
                              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                            <span><?php echo ucwords($ass_conditio); ?></span>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-8" >
                <div class="card card-custom card-sticky">
                    <div class="card-header mb-3">
                        <h5> Assets</h5>
                    </div>
                    <div class="card-body" id="assetlistid">
                        <?php include_once "custom_insert_asset_request_transfers_ajax.php"; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <ul class="nav md-tabs infisuite_tab mb-2">
                    <li class="nav-item">
                        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#assetsrequesttransfer" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Request Transfer</a>
                    </li>
                    <li class="nav-item float-right">
                        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#oldassetsrequesttransfer" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Old Transfer</a>
                    </li>
                </ul>
                <div class="tab-content tabs">
                    <div class="tab-pane card-body p-0 active" id="assetsrequesttransfer" role="tabpanel">
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                            <div class="card-header mb-3">
                                <h5> Assets Request Transfer</h5>
                            </div>
                            <div class="card-body">
                                <form id="assetrequesttransferform" method="post" >
                                    <input type="hidden" name="rid" id="asset_requests_id" value="<?php echo $_POST['rid']; ?>">
                                    <input type="hidden" name="asset_id" id="asset_id" value="">
                                    <div class="row">
                                        <div class="form-group col-lg-6">
                                            <span>Start date <span class="requried">*</span></span>
                                            <span class="d-block error-message text-danger"></span>
                                            <input aria-label="Start Date" type="text" class="form-control datepicker requried_field" id="start_date"  name="start_date" value="<?php echo fromDate($asset_requests['asset_required_start_date']); ?>">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span>End date <span class="requried">*</span></span>
                                            <span class="d-block error-message text-danger"></span>
                                            <input aria-label="End Date" type="text" class="form-control datepicker requried_field" id="end_date" name="end_date" value="<?php echo $asset_requests['asset_required_end_date'] !=""?fromDate($asset_requests['asset_required_end_date']):""; ?>">
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span>Request Quantity</span>
                                            <input aria-label="Requested Qty" type="number" class="form-control" id="requested_qty"  value="<?php echo $asset_requests['quantity']; ?>" readonly>
                                        </div>
                                        <div class="form-group col-lg-6">
                                            <span>Transfer Quantity <span class="requried">*</span></span>
                                            <span class="d-block error-message text-danger" id="transferred_qty_error"></span>
                                            <input aria-label="Quantity" type="number" class="form-control requried_field" id="transferred_qty" name="quantity" value=""  onblur="getquontiy(this.value)" readonly>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span class="f-w-700 col-lg-6">Pending Quantity</span>
                                            <span class="col-lg-6 pendinreq"><?php echo $asset_requests['quantity']-$already_transfer_quantity; ?></span>
                                            <input type="hidden" class="form-control" id="pending_qty"  value="<?php echo $asset_requests['quantity']-$already_transfer_quantity; ?>">
                                        </div>
                                        <div class="col-lg-12">
                                            <span>How Do You Want The Asset To be Delivered? <span class="requried">*</span></span>
                                            <div class="form-radio">
                                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Delivery Type" type="radio" name="delivery_type" value="S" style="display:none;" <?php echo ($asset_requests['delivery_type']=="S" || $asset_requests['delivery_type']=="")?"checked":"";?> onclick="getDeliveryType()" onkeydown="">
                                    <i class="helper"></i>Self Pickup
                                  </label>
                            </div>
                            <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Delivery Type" type="radio" name="delivery_type" value="D" style="display:none" <?php echo $asset_requests['delivery_type']=="D"?"checked":"";?> onclick="getDeliveryType()" onkeydown="">
                                    <i class="helper"></i>Deliver Through Logistics Partner
                                  </label>
                            </div>
                            </div>
                                        </div>
                                        <div class="form-group col-lg-12" id="popup_asset_delivery_address"  <?php echo $asset_requests['delivery_type']!="D"?"style='display:none'":"";?>>
                                            <span>Delivery Address</span>
                                            <textarea aria-label="Delivery Address" type="text" class="form-control" id="delivery_address" rows="4" cols="50" name="delivery_address" ><?php echo $asset_requests['delivery_address']; ?></textarea>
                                        </div>
                                        <div class="form-group col-lg-12">
                                            <span>Remarks</span>
                                            <textarea aria-label="Description" type="text" class="form-control" id="description" rows="4" cols="50" name="description" ></textarea>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitAssetRequestTransfer()">Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane card-body p-0" id="oldassetsrequesttransfer" role="tabpanel">
                        <div class="card card-custom card-sticky" id="kt_page_sticky_card_2">
                            <div class="card-header mb-3">
                                <h5>Transfer Request</h5>
                            </div>
                            <div class="card-body">
                                <?php if(count((array)$asset_request_transfers) !="0"){

                                    foreach($asset_request_transfers as $art){
                                    ?>

                                        <div class="card">
                                            <div class="card-body">
                                                <table>
                                                    <tr>
                                                        <th scope="col">Asset</th><td>&nbsp:&nbsp</td><td><?php echo $all_assets[$art['asset_id']]; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col" style="width: 30%;">Transfer Date</th><td>&nbsp:&nbsp</td><td style="width: 70%;"><?php echo $art['asset_transfer_date']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">Start Date</th><td>&nbsp:&nbsp</td><td><?php echo $art['start_date']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">End Date</th><td>&nbsp:&nbsp</td><td><?php echo $art['end_date']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">Accept/Reject Date</th><td>&nbsp:&nbsp</td><td><?php echo $art['asset_accept_or_reject_date']; ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="col">Status</th><td>&nbsp:&nbsp</td>
                                                        <td>
                                                            <span class="form-label label label-md" style="color:<?php echo $statuse[$art['current_status']]['color']; ?>; background-color:<?php echo $statuse[$art['current_status']]['background_color']; ?>;"><?php echo $statuse[$art['current_status']]['name']; ?></span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                <?php } ?>
                                <?php } else { ?>
                                    <div class="card-px text-center ">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert"></i> No Request</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
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
