<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$url1 = API."?mode=getpropertytenantdetails&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$property_tenant_details = $result['property_tenant_details'];
$un_lock_tenant_log = json_decode($property_tenant_details['un_lock_tenant_log'],1);
?>
<script type="text/javascript">
    function submitUnlockTenant(){
        $("#popupshowerror").hide();
        var un_lock_date = $("#un_lock_date").val();
        if(un_lock_date ==""){
            $("#popupshowerror").show();
        }else{
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: '<?php echo $_SESSION['error_message']['9129']; ?>',
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: <?php echo json_encode(__('Yes'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                cancelButtonText: <?php echo json_encode(__('No'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                closeOnConfirm: true,
                closeOnCancel: true
            },
            function(isConfirm) {
                if(isConfirm){
                    var formdata = $("#unlocktenantform").serialize();
                    $.ajax({
                        url: "ajax.php",
                        type: "POST",
                        data: 'page=updatepropertytenantdetails&'+formdata,
                        success: function(response) {
                            console.log(response);
                            var res = JSON.parse(response);
                            console.log(res.result);
                            console.log(res.result.status);
                            if(res.result.status == "sucess"){
                                $('#kt_modal_KTDatatable_remotem').modal('hide');
                                swal({
                                    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                                    text: '<?php echo $_SESSION['error_message']['9107']; ?>',
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonClass: "btn-danger",
                                    confirmButtonText: <?php echo json_encode(__('Yes'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                    cancelButtonText: <?php echo json_encode(__('No'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                    closeOnConfirm: true,
                                    closeOnCancel: true
                                },
                                function(isConfirm) {
                                    if(isConfirm){
                                        window.location.href = "index?option=COM_INSERT_PROPERTY_TENANT_DETAILS&Rid="+res.rid;
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
    <h4 class="modal-title"><?= __('Un-Lock Tenant'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <div class="alert alert-danger alert-dismissible">
        <strong class="f-16"><i class="ti-alert "></i></strong> <?php echo ucwords($_SESSION['error_message']['9106']); ?>
        <button type="button" class="btn-close text-danger" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
    </div>
    <div class="row">
        <?php /** ?>
        <div class="col-lg-6">
            <?php if(count((array)$un_lock_tenant_log) !='0'){ ?>
            <table class="table mb-3">
                <thead>
                    <tr>
                        <th class="p-1">#</th>
                        <th class="p-1">Un-Lock Date</th>
                        <th class="p-1">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $mk=1; foreach($un_lock_tenant_log as $unlock){ ?>
                        <tr>
                            <td class="p-1"><?php echo $mk; ?></td>
                            <td class="p-1"><?php echo fromDate($unlock['un_lock_date'])?></td>
                            <td class="p-1"><?php echo $unlock['remarks']?></td>
                        </tr>
                    <?php $mk++; } ?>

                </tbody>
            </table>
        <?php }else{  ?>
            <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Un-lock Tenant Log</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
        </div>
        <?php **/ ?>
        <div class="col-lg-12">
            <form id="unlocktenantform" method="post">
                <div class="row">
                    <div class="form-group col-lg-6">
                        <span class="form-label d-inline-block fw-bold"><?= __('Un-Lock To Date'); ?> <span class="text-danger">*</span></span>
                        <input aria-label="<?= htmlspecialchars(__('Un lock date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker" name="un_lock_date" id="un_lock_date" onchange="GetDateToRentalInvoices()">
                    </div>
                    <div class="form-group col-lg-6">
                        <span class="form-label  d-inline-block fw-bold"><?= __('Remarks'); ?> <span class="text-danger">*</span></span>
                        <textarea aria-label="<?= htmlspecialchars(__('Remarks'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" name="remarks" id="remarks"></textarea>
                    </div>
                </div>
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid; ?>">
            </form>
            <div id="affectedinvoice"></div>
        </div>
    </div>
</div>
<div class="modal-footer" >
    <span class="text-danger f-w-700" id="popupshowerror" style="display: none"><?= __('Please fill out all required fields'); ?></span>
    <button type="button" class="btn btn-primary waves-effect waves-light disabled" id="popupsubmtbtn" onclick="submitUnlockTenant()"> <?= __('Un-Lock Tenant'); ?></button>
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
</div>
