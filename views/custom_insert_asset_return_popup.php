include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=INSERTASSETRETURN&user_asset_id=".$_POST['user_asset_id'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assets = $details['assets'];
$user_assets = $details['user_assets'];

?>
<script type="text/javascript">

function submitdetails(){
    var validations = ajaxFormSubmitValidation();
    if(validations == 0){
          var formData = new FormData();
        formData.append("page", "insertassetreturn");
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

</script>

<div class="modal-header">
    <h4 class="modal-title">Asset Return</h4>
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
                <p class="m-b-0 "> <?php echo $user_assets['start_date'] !=""?fromDate($user_assets['start_date']):"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">End Date</span>
                <p class="m-b-0 f-10"> <?php echo $user_assets['end_date'] !=""?fromDate($user_assets['end_date']):"-"; ?></p>
            </div>
        </div>
        <div class="col-2 b-l-default text-center">
            <div class="s-cont d-inline-block">
                <span class="f-w-700 m-b-0">Received Date</span>
                <p class="m-b-0 f-10"> <?php echo $user_assets['received_date'] !=""?fromDate($user_assets['received_date']):"-"; ?> </p>
            </div>
        </div>
    </div>
    <form id="assetsallocationform" method="post" >
        <input type="hidden" name="user_asset_id" value="<?php echo $_POST['user_asset_id']; ?>">
        <div class="card p-2 mt-2">
            <div class="row">
                <div class="form-group col-lg-3">
                        <span>Asset Name <span class="requried text-danger">*</span></span>
                        <span class="d-block error-message text-danger"></span>
                        <input aria-label="Asset Name" type="text" class="form-control requried_field" id="asset_name"  name="asset_name" value="<?php echo $assets['asset_description']."/".$assets['asset_code']; ?>" readonly>
                    </div>
                <div class="form-group col-lg-3">
                    <span>Return date <span class="requried text-danger">*</span></span>
                    <span class="d-block error-message text-danger"></span>
                    <input aria-label="Return Date" type="text" class="form-control datepicker requried_field" id="return_date"  name="return_date" value="">
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

<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Return</button>
</div>

