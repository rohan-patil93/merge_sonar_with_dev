<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$nm = ViewStatusValue($_POST['status']);
?>
<link rel="stylesheet" href="../assets/css/custom_insert_status_popup.css">
<script src="../assets/js/custom_insert_status_popup.js"></script>

<div class="d-none status-popup-php-data"
     data-success-title="<?php echo $_SESSION['error_message']['9059']; ?>"
     data-success-text="<?php echo $_SESSION['error_message']['9058']; ?>">
</div>

    <div class="modal-header">
                        <h5 class="modal-title">

                            <small>&nbsp;&nbsp; <b class="f-w-600"><?php echo __('Transaction Type:'); ?> </b>&nbsp; <?php echo viewtitle($_POST['table']); ?> &nbsp;&nbsp;
                             <b class="f-w-600"><?php echo __('Status:'); ?> </b>&nbsp; <?php echo $nm['name']  ?> </small>
                        </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php echo __('Close'); ?>"></button>
                    </div>
                    <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                            <div class="form-group row">
                                <div class="col-lg-12">
                                    <span class="m-b-5"><?php echo __('Comment'); ?></span>
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
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?php echo __('Cancel'); ?></button>
                <button type="button" class="btn btn-primary mr-2 status-popup-submit-btn"><?php echo __('Submit'); ?></button>
            </div>

    </form>
</div>
