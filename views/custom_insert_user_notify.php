<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
?>
<script type="text/javascript">
    function submitrom(){
          var  str = '';

         str = 'send_id='+$('#user_id').val()+'&comment='+$('#comment').val();
          console.log(str);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=usernotify&'+str,
            success: function(response) {
                $('#kt_modal_KTDatatable_remote').modal('hide');
         //        swal.fire({
                        //     title: "User Notified ",
                        //     type: "success",
                        //     buttonsStyling: false,
                        //     confirmButtonClass: "btn btn-info",
                        //     showCancelButton: false,
                        //     cancelButtonClass: "btn btn-default"
            }
        });
    }

</script>

    <div class="modal-header">
                        <h5 class="modal-title">
                            <?= __('Notify'); ?>
                        </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
                    </div>
                    <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <span><?= __('Comment'); ?></span>
                                    <textarea aria-label="<?= htmlspecialchars(__('Comment'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" name="comment" id="comment"><?php echo $_POST['comments']; ?></textarea>
                                </div>

                            </div>
                    </div>
                </div>


            <div class="modal-footer">

                <input type="hidden" name="user_id" id='user_id' value="<?php echo $_POST['id']; ?>">

                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal"><?= __('Close'); ?></button>
                <button type="button" class="btn btn-primary mr-2" onclick="submitrom()" ><?= __('Submit'); ?></button>
            </div>

    </form>
</div>
