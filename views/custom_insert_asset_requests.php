<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=INSERTASSETREQUESTS";
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assetcategory = $details['asset_categories'];
$locations = $details['locations'];
$attributes = $details['attributes'];
$reporting_manager = $details['reporting_manager'];
?>
<script type="text/javascript">
    function submitdetailsRequest(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
               var postarray = $("#assetsrequestform").serialize();
            jQuery.ajax({
                url: "ajax.php",
                data: postarray+'&page=insertassetrequest',
                type: "POST",
                success:function(data){
                    console.log(data);
                    var result = JSON.parse(data);
                    if(result.res.status == "sucess"){
                        $('#kt_modal_dynamic_popup2').modal("hide");
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000'];?>','');
                        getAssetPage('ASSETS_REQUEST')
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',result.res.message,'');
                    }
                }
            });
        }
    }
    function selectAssetCategory(cat_id =""){
        var category_id = $("#category_id").val();
        if(cat_id !=""){
            var category_id = cat_id;
        }
         jQuery.ajax({
            url: "ajax.php",
            data: 'page=selectAssetCategory&catgoryid='+category_id,
            type: "POST",
            success:function(data){
                var res = JSON.parse(data);
                if(cat_id !=""){
                    $("#listattributesshow").html(res.asset_attributes);
                }else{
                    $("#listattributes").html(res.asset_attributes);
                }
                   $("#asset_list_id").html(res.option);

            }
        });
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
        <form id="assetsrequestform" method="post" >
            <input type="hidden" name="rid" id="rid" value="<?php echo $_POST['rid']; ?>">
            <input type="hidden" name="approval_user_id" id="approval_user_id" value="<?php echo $reporting_manager['reports_to'] ; ?>">
            <div class="row">
                <div class="form-group col-lg-6 select_emp w-full" >
                    <span>Asset Category <span class="requried">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <select aria-label="Category" class="form-select js-example-basic-single requried_field" name="category_id" id="category_id" onchange="selectAssetCategory()">
                            <option value="">Select Any One</option>
                    <?php foreach ($assetcategory as $ac){ ?>
                        <option  value="<?php echo $ac['id']; ?>" <?php echo $request['category_id']==$ac['id']?"selected":"";?>><?php echo ucwords($ac['category_description']); ?></option>
                    <?php } ?>
                    </select>
                </div>
                <div class="form-group col-lg-6 " >
                    <span>Attributes </span>
                    <div id="listattributes">
                        <?php
                        $explodeattribute = explode(",",$request['attribute']);
                        foreach($attributes as $asset_attributes){
                            $checked = in_array($asset_attributes['id'],(array)$explodeattribute)?"checked":"";
                        ?>
                            <div class="checkbox-fade fade-in-primary">
                                  <label class="form-label">
                                    <input aria-label="Attribute[]" type="checkbox" name="attribute[]" <?php echo $checked; ?> value="<?php echo $asset_attributes['id']; ?>" >
                                    <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                    <span><?php echo $asset_attributes['name']; ?></span>
                                  </label>
                            </div>
                    <?php } ?>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span>Quantity <span class="text-danger">*</span> </span>
                    <span class="d-block error-message text-danger"></span>
                    <input aria-label="Quantity" type="number" class="form-control requried_field" id="quantity" name="quantity" value="<?php echo $request['quantity']; ?>">
                </div>
                <div class="form-group col-lg-6">
                    <span>Description</span>
                    <textarea aria-label="Description"  class="form-control" id="description" rows="4" cols="50" name="description" ><?php echo $request['description']; ?></textarea>
                </div>
                <div class="col-lg-6">
                    <span>How Do You Want The Asset To be Delivered? <span class="requried">*</span></span>
                    <div class="form-radio">
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Delivery Type" type="radio" name="delivery_type" value="S" style="display:none;" <?php echo ($request['delivery_type']=="S" || $request['delivery_type']=="")?"checked":"";?> onclick="getDeliveryType()" onkeydown="">
                                <i class="helper"></i>Self Pickup
                              </label>
                        </div>
                        <div class="radio radio-inline">
                              <label class="form-label">
                                <input aria-label="Delivery Type" type="radio" name="delivery_type" value="D" style="display:none" <?php echo $request['delivery_type']=="D"?"checked":"";?> onclick="getDeliveryType()" onkeydown="">
                                <i class="helper"></i>Deliver Through Logistics Partner
                              </label>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" >
                    <div class=" form-group " id="popup_asset_delivery_address"  <?php echo $request['delivery_type']!="D"?"style='display:none'":"";?>>
                        <span>Delivered Address </span>
                        <textarea aria-label="Delivery Address"  class="form-control" id="delivery_address" rows="4" cols="50" name="delivery_address" ><?php echo $request['delivery_address']; ?></textarea>
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <span>Start date <span class="text-danger">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <input aria-label="Asset Required Start Date" type="text" class="form-control datepicker requried_field" id="asset_required_start_date"  name="asset_required_start_date" value="<?php echo fromDate($request['asset_required_start_date']); ?>">
                </div>
                <div class="form-group col-lg-4">
                    <span>End date</span>
                    <input aria-label="Asset Required End Date" type="text" class="form-control datepicker" id="asset_required_end_date" name="asset_required_end_date" value="<?php echo $request['asset_required_end_date']!=""?fromDate($request['asset_required_end_date']):""; ?>">
                </div>
                <div class="form-group col-lg-4 w-full">
                    <span>Location <span class="text-danger">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <select aria-label="Location"  class="form-select js-example-basic-single requried_field" name="location_id">
                        <option value="">Select Any One</option>
                        <?php foreach ($locations as $location) { ?>
                            <option location value="<?php echo $location['id']; ?>"  <?php echo $request['location_id']==$location['id']?"selected":"";?>><?php echo ucwords($location['location_name']); ?></option>
                        <?php } ?>
                    </select>
                </div>

            </div>
        </form>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <?php if($reporting_manager['reports_to'] !=""){ ?>
        <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetailsRequest()">Submit</button>
    <?php }else{ ?>
        <span class="text-danger f-12"><?php echo $_SESSION['error_message']['9001']; ?></span>
    <?php } ?>
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
