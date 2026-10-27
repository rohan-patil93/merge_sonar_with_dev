<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$asset_request_transfer_id = $_POST['asset_request_transfer_id'];
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=ASSETREQUESTTRANSFERSACCEPTORREJECT&asset_request_transfer_id=".$asset_request_transfer_id;
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$asset_request_transfers = $details['asset_request_transfers'];
$assets = $details['assets'];

?>
<script type="text/javascript">
    function selectAllocationType(){
        var selected = $("#allocation_type:checked").val();
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
function submitdetails(current_status){
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
</script>
<div class="modal-header">
    <h4 class="modal-title">Asset Accept/Reject</h4>
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
                <p class="m-b-0 "> <?php echo $asset_request_transfers['start_date'] !=""?fromDate($asset_request_transfers['start_date']):"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">End Date</span>
                <p class="m-b-0 f-10"> <?php echo $asset_request_transfers['end_date'] !=""?fromDate($asset_request_transfers['end_date']):"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Delivery Type</span>
                <p class="m-b-0 f-10"> <?php echo $asset_request_transfers['delivery_type']!="D"?"Delivery Partner":"Self"; ?> </p>
            </div>
        </div>

    </div>
    <div class="">
        <form id="assetsallocationform" method="post" >
            <input type="hidden" name="asset_request_transfer_id"  value="<?php echo $asset_request_transfer_id;?>">
            <div class="card p-2 mt-2">
                <div class="row">
                    <div class="form-group col-lg-3">
                        <span>Received date</span>
                        <input aria-label="Received Date" type="text" class="form-control datepicker" id="received_date"  name="received_date" value="">
                    </div>
                    <div class="form-group col-lg-9">
                        <span>Note</span>
                        <textarea aria-label="Received Note" type="text" class="form-control" id="received_note" rows="4" cols="50" name="received_note" ></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails('<?php echo ST_APPROVED; ?>')">Accept</button>
    <button type="button" class="btn btn-danger waves-effect waves-light"  onclick="submitdetails('<?php echo ST_REJECTED; ?>')">Reject</button>
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
