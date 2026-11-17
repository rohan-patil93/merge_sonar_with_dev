<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table = $_POST['table'];
$field= $_POST['field'];
$rid = $_POST['rid'];
$tranid = $_POST['tranid'];
$user = toGetUserDetais();
$status = ViewStatusValue($field);
?>

<script type="text/javascript">
    function submitrom(){
          var  str = '';
         str = 'table='+$('#table').val()+'&field='+$('#field').val()+'&comment='+$('#comment').val()+'&user_id='+$('#user_id').val()+'&date='+$('#dates').val()+'&hid='+$('#rid').val()+'&tranid='+$('#tranid').val();
          console.log(str);
          alert(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=changestatus&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: <?php echo json_encode(__('Comments Updated').' ', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                            text: <?php echo json_encode(__('Do you want to close ?').' ', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i><?= htmlspecialchars(__('Yes, close!'), ENT_QUOTES, 'UTF-8'); ?>",
                            confirmButtonClass: "btn btn-danger",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> <?= htmlspecialchars(__('No, continue'), ENT_QUOTES, 'UTF-8'); ?>",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                            }
                        });
            }
        });
    }

</script>

<!-- remote datatable modal -->

    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= __('Status Comments'); ?>
                            <small>&nbsp;&nbsp; <b><?= __('Transaction Type'); ?>: </b>&nbsp; <?php echo viewtitle($table); ?>  </small>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="col-lg-6">
                                    <span><?= __('Status'); ?></span>
                                     <input aria-label="<?= htmlspecialchars(__('Field Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" readonly name="field_name" value="<?php echo $status['name']; ?>" >

                                </div>
                                <div class="form-group col-lg-6">
                                    <span><?= __('User Name'); ?></span>
                                    <input type="hidden" class="form-control" id='user_id' readonly name="user_id" value="<?php echo $_SESSION['userid']; ?>" >
                                     <input aria-label="<?= htmlspecialchars(__('User Name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" readonly name="user_name" value="<?php echo $user['first_name']; ?>" >
                                </div>
                                <input type="hidden" id='dates' class="form-control" readonly name="dates" value="<?php echo date("Y-m-d H:i:s"); ?>" >

                                <div class="form-group col-lg-12">
                                    <span><?= __('Comments'); ?></span>
                                    <div class="kt-radio-inline">
                                        <textarea aria-label="<?= htmlspecialchars(__('Comments'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id ='comment'class="form-control" style="height: 100px;" name="comments" ><?php echo $role[$key];?></textarea>
                                    </div>
                                </div>

                            </div>
                    </div>
                </div>


            <div class="modal-footer">
                <input type="hidden" name="table" id='table' value="<?php echo $table; ?>">
                <input type="hidden" name="field" id='field' value="<?php echo $field; ?>">
                <input type="hidden" name="rid" id='rid' value="<?php echo $rid; ?>">
                <input type="hidden" name="tranid" id='tranid' value="<?php echo $tranid; ?>">
                <button type="button" class="btn btn-light-primary font-weight-bolder mr-2" data-dismiss="modal"><?= __('Close'); ?></button>
                <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" onclick="submitrom()" ><?= __('Submit'); ?></button>
            </div>

    </form>
</div>
<!-- local datatable modal -->
