<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = "1";
$_SESSION['apply_js']['asset_allocations_js']="1";
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=USERDASH&pagetype=SELF_ASSETS";
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assets = $details['assets'];
$application_users = $details['application_users'];
$asset_status= $details['asset_status'];
$ass_condition = $details['asset_condition_type'];
$dashboard_cnt = $details['dashboard_cnt'];

?>
<script type="text/javascript">
    function getAssetPage(pagetype){
        var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $('#assetstable').html(htmloader);
        $.ajax({
            url: "views/custom_view_asset_user_dashboard_ajax.php",
            data:'pagetype='+pagetype+'&ajaxpagecall=Y',
            type: "POST",
            success:function(data){
                $("#assetstable").html(data);
            }
        })
    }
    function getAssetRequest(){
        jQuery.ajax({
            url: "views/custom_insert_asset_requests.php",
            data: "",
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                        var rect = input.getBoundingClientRect();
                        setTimeout(function () {
                            inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                        }, 0);
                    }
                });
            }
        });
    }
    function assetRequestTransfersAcceptOrReject(asset_request_transfer_id){
        jQuery.ajax({
            //url: "views/custom_list_asset_accept_popup.php",
            url: "views/custom_insert_asset_request_transfer_accepts_or_reject.php",
            data: "asset_request_transfer_id="+asset_request_transfer_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                        var rect = input.getBoundingClientRect();
                        setTimeout(function () {
                            inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                        }, 0);
                    }
                });
            }
        });
    }

    function assetReturnRequest(user_asset_id){
        jQuery.ajax({
            //url: "views/custom_list_asset_accept_popup.php",
            url: "views/custom_insert_asset_return_popup.php",
            data: "user_asset_id="+user_asset_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popup11').html(data);
                $('#kt_modal_dynamic_popup2').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
                $( ".datepicker" ).datepicker({
                    showOn: "both",
                    buttonText: "<i class='ti-calendar'></i>",
                    dateFormat: date_format,
                    changeMonth: true,
                    changeYear: true,
                    beforeShow: function (input, inst) {
                        var rect = input.getBoundingClientRect();
                        setTimeout(function () {
                            inst.dpDiv.css({ top: rect.top + 40, left: rect.left + 0 });
                        }, 0);
                    }
                });
            }
        });
    }

</script>
<div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header">
                <h5>Filter</h5>

            </div>
            <div class="card-body">
                <button type="button" class="btn btn-primary waves-effect waves-light col-lg-12" onclick="getAssetRequest()"><i class="fa fa-plus"></i> Asset Request</button>
            </div>
        </div>
    </div>
    <div class="col-lg-9 ">
        <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" role="tab" aria-selected="true" onclick="getAssetPage('SELF_ASSETS')">
                    Self Assets <span class="form-label label label-xs label-success m-l-10"><?php echo $dashboard_cnt['self_asset_cnt']; ?></span></button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12" data-bs-toggle="tab" role="tab" aria-selected="true" onclick="getAssetPage('ASSETS_REQUEST')">Assets Request <span class="form-label label label-xs label-primary m-l-10"> <?php echo $dashboard_cnt['asset_requests_cnt']; ?></span></button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12" data-bs-toggle="tab" role="tab" aria-selected="true" onclick="getAssetPage('REQUEST_INPROCESS')">Request In-Process<span class="form-label label label-xs label-primary m-l-10">  <?php echo $dashboard_cnt['asset_request_transfers_cnt']; ?></span></button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12" data-bs-toggle="tab" role="tab" aria-selected="true" onclick="getAssetPage('ASSET_RETURNS')">Return Assets <span class="form-label label label-xs label-danger m-l-10">-</span></button>
            </li>
            <li class="nav-item">
                <button type="button" class="nav-link f-w-600 f-12  disabled" data-bs-toggle="tab" role="tab" aria-selected="true" onclick="getAssetPage('QUERIES')">Queries <span class="form-label label label-xs label-warning m-l-10">-</span></button>
            </li>
        </ul>
        <div id="assetstable">
            <?php
                if(file_exists("views/custom_view_asset_user_dashboard_ajax.php")){
                    include_once "views/custom_view_asset_user_dashboard_ajax.php";
                }else{
                    include_once "custom_view_asset_user_dashboard_ajax.php";
                }
            ?>
        </div>
    </div>
</div>
