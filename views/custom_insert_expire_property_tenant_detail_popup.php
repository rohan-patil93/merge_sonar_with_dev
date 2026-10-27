<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$url1 = API."?mode=getpropertytenantdetails&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$property_tenant_details = $result['property_tenant_details'];
?>
<script type="text/javascript">
    function submitUnlockTenant(){
        $("#popupsubmtbtn").text("Please Wait........");
        $("#popupsubmtbtn").addClass("disabled");
        $("#popupshowerror").hide();
        var un_lock_date = $("#un_lock_date").val();
        var remarks = $("#remarks").val();
        if(un_lock_date =="" || remarks == ""){
            $("#popupshowerror").show();
            $("#popupsubmtbtn").text("Expire Tenant Agreement");
            $("#popupsubmtbtn").removeClass("disabled");
        }else{
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: '<?php echo $_SESSION['error_message']['9130']; ?>',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if(isConfirm){
                    var formdata = $("#unlocktenantform").serialize();
                    $.ajax({
                        url: "ajax.php",
                        type: "POST",
                        data: 'page=updatepropertytenantdetails&formtype=EXPIRED&'+formdata,
                        success: function(response) {
                            console.log(response);
                            var res = JSON.parse(response);
                            console.log(res.result);
                            console.log(res.result.status);
                            if(res.result.status == "sucess"){
                                $('#kt_modal_KTDatatable_remotem').modal('hide');
                                swal({
                                    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                    text: '<?php echo $_SESSION['error_message']['9131']; ?>',
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonClass: "btn-danger",
                                    confirmButtonText: "Yes",
                                    cancelButtonText: "No",
                                    closeOnConfirm: true,
                                    closeOnCancel: true
                                },
                                function(isConfirm) {
                                    if(isConfirm){
                                        location.reload();
                                    }else{
                                        location.reload();
                                    }
                                });
                            }else{
                                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',res.result.message,'');
                            }
                        }
                    });
                }
            });
        }
    }
    function GetDateToRentalInvoices(){
        var un_lock_date = $("#un_lock_date").val();
        var rid = '<?php echo $rid; ?>';
        if(un_lock_date !=""){
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=getdatetorentalinvoices&un_lock_date='+un_lock_date+'&rid='+rid,
                success: function(response) {
                    var res = JSON.parse(response)
                    console.log(response);
                    if(res.affectedinvoice == "Y"){
                        $("#affectedinvoice").html(res.html);
                        $('#popupsubmtbtn').addClass('disabled');
                    }else{
                        $("#affectedinvoice").html('');
                        $('#popupsubmtbtn').removeClass('disabled');
                    }
                }
            });
        }

    }

</script>
<div class="modal-header">
    <h4 class="modal-title">Expire Tenant Agreement</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <div class="alert alert-danger alert-dismissible">
        <strong class="f-16"><i class="ti-alert "></i></strong> <?php echo ucwords($_SESSION['error_message']['9132']); ?>
        <button type="button" class="btn-close text-danger" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <form id="unlocktenantform" method="post">
                <div class="row">
                    <div class="form-group col-lg-6">
                        <span class="form-label">Tenant Agreement Expiry Date <span class="text-danger">*</span></span>
                        <input aria-label="Un Lock Date" type="text" class="form-control datepicker" name="un_lock_date" id="un_lock_date" onchange="GetDateToRentalInvoices()">
                    </div>
                    <div class="form-group col-lg-6">
                        <span class="form-label ">Remarks <span class="text-danger">*</span></span>
                        <textarea aria-label="Remarks" class="form-control" name="remarks" id="remarks"></textarea>
                    </div>
                </div>
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
            </form>
            <div id="affectedinvoice"></div>
        </div>
    </div>
</div>
<div class="modal-footer" >
    <span class="text-danger f-w-700" id="popupshowerror" style="display: none">Please fill out all required fields</span>
    <button type="button" class="btn btn-danger waves-effect waves-light disabled" id="popupsubmtbtn" onclick="submitUnlockTenant()"> Expire Tenant Agreement</button>
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
