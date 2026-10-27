<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$asset_return_id = $_POST['asset_return_id'];
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=ASSETRETURNRECEIVEACCEPTSORREJECT&asset_return_id=".$asset_return_id;
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$asset_returns = $details['asset_returns'];
$assets = $details['assets'];

?>
<script type="text/javascript">
function submitdetails(current_status){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){

        if(current_status == 199){
            var msg  = '<?php echo $_SESSION['error_message']['9173']; ?>'; //accept
        }else{
            var msg  = '<?php echo $_SESSION['error_message']['9172']; ?>'; //reject
        }
        swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: msg, //accept
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: true,
            closeOnCancel: true
        },
        function(isConfirm) {
            if (isConfirm) {
                var formData = new FormData();
                formData.append("page", "assetaccepts");
                formData.append("accept_status", "Y");
                formData.append("current_status", current_status);
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
                        getAssetPage('REQUEST_INPROCESS')

                    }
                });
            }
        });
    }
}
</script>
<div class="modal-header">
    <h4 class="modal-title">Asset Receive</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-2 text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Asset/Code</span>
                <p class="m-b-0 f-10"> <?php echo $assets['asset_description']."/".$assets['asset_code']; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Brand</span>
                <p class="m-b-0 f-10"> <?php echo $assets['brand'] !=""?$assets['brand']:"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Model/Serial No</span>
                <p class="m-b-0 f-10"> <?php echo $assets['model'] !=""?$assets['model']:"-"; ?>/<?php echo $assets['serial_no'] !=""?$assets['serial_no']:"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Start date</span>
                <p class="m-b-0 "> <?php echo $asset_returns['start_date'] !=""?fromDate($asset_returns['start_date']):"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Return Date</span>
                <p class="m-b-0 f-10"> <?php echo $asset_returns['end_date'] !=""?fromDate($asset_returns['end_date']):"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Description</span>
                <p class="m-b-0 f-10"> <?php echo $asset_returns['delivery_type']!="D"?"Delivery Partner":"Self"; ?> </p>
            </div>
        </div>

    </div>
    <div class="">
        <form id="assetsallocationform" method="post" >
            <input type="hidden" name="asset_return_id"  value="<?php echo $asset_return_id;?>">
            <input type="hidden" name="user_asset_id"  value="<?php echo $asset_returns['user_asset_id'];?>">
            <div class="card p-2 mt-2">
                <div class="row">
                    <div class="form-group col-lg-3">
                        <span>Asset Name <span class="requried text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Asset Name" type="text" class="form-control requried_field" id="asset_name"  name="asset_name" value="<?php echo $assets['asset_description']."/".$assets['asset_code']; ?>" readonly>
                    </div>
                    <div class="form-group col-lg-3">
                        <span>Receive date <span class="requried text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Received Date" type="text" class="form-control datepicker requried_field" id="received_date"  name="received_date" value="">
                    </div>
                    <div class="form-group col-lg-6">
                        <span>Note <span class="requried text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <textarea aria-label="Received Note" type="text" class="form-control requried_field" id="received_note" rows="4" cols="50" name="received_note" ></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails('<?php echo ST_APPROVED; ?>')">Receive</button>

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
