<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$nm = ViewStatusValue($_POST['status']);

    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" integrity="sha384-nLoOnA/BDh8A/jxqtckg4DumuCGOBYUnNJLZdQz/zfYNp3wcjGSoWTAzgko06G/2" crossorigin="anonymous"></script>
<script type="text/javascript">
function closebutnfunction(){
             $('#checkstausinput<?php echo $_POST['rid']; ?>').prop('checked', false);
             $("#checkstausid<?php echo $_POST['rid']; ?>").removeClass("done-task");
}

    function submitrom(){
          var  str = '';
          status = $('#status_value').val();
         str = 'table='+$('#table').val()+'&comment='+$('#comment').val()+'&status='+status+'&hid='+$('#rid').val()+'&extra='+$('#extra').val();
          console.log(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=dynamicchangestatus&'+str,
            success: function(response) {
                console.log(response);
                swal.fire({
                            title: <?php echo json_encode(__('Comments Updated')); ?>,
                            text: <?php echo json_encode(__('Do you want to close ?')); ?>,
                            type: "success",
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                            confirmButtonClass: "btn btn-success",
                            showCancelButton: true,
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                            cancelButtonClass: "btn btn-default"
                        }).then(function(result) {
                            if (result.value) {
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                                window.location.reload();
                            }
                        });
            }
        });
    }

</script>

    <div class="modal-header">
                        <h5 class="modal-title">
                            <?php echo __('Status'); ?>
                            <small>&nbsp;&nbsp; <b><?php echo __('Transaction Type:'); ?> </b>&nbsp; <?php echo viewtitle($_POST['table']); ?> &nbsp;&nbsp;
                             <b><?php echo __('Status:'); ?> </b>&nbsp; <?php echo $nm['name']  ?> </small>
                        </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('Close'); ?>" onclick="closebutnfunction()"></button>
                    </div>
                    <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <span><?php echo __('Comment'); ?></span>
                                    <textarea aria-label="<?php echo __('Comment'); ?>" class="form-control" name="comment" id="comment"></textarea>
                                </div>
                            </div>
                    </div>
                </div>


            <div class="modal-footer">
                <input type="hidden" name="status" id='status_value' value="<?php echo $_POST['status']; ?>">
                <input type="hidden" name="table" id='table' value="<?php echo $_POST['table']; ?>">
                <input type="hidden" name="rid" id='rid' value="<?php echo $_POST['rid']; ?>">
                <input type="hidden" name="extra" id='extra' value="<?php echo $_POST['extra']; ?>">
                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal" onclick="closebutnfunction()"><?php echo __('Close'); ?></button>
                <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" ><?php echo __('Submit'); ?></button>
            </div>

    </form>
</div>
