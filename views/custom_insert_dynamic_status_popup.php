<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$nm = ViewStatusValue($_POST['status']);

$statuslists  = getstatusprogresslist($_POST['table_name']);
foreach($statuslists as $statuslist){
    $statuslistlable[$statuslist['status']] = $statuslist['name'];
    $statuslistaction_label[$statuslist['status']] = $statuslist['action_label'];
}
?>

<input type="hidden" id="hide_mobileview_id" value="<?php echo $_POST['mobileview']; ?>">
<script type="text/javascript" src="../assets/js/custom_insert_dynamic_status_popup.js"></script>

  <div class="modal-header">
      <h5 class="modal-title">
          <small>&nbsp;&nbsp; <b class="f-w-600">Transaction Type: </b>&nbsp; <?php echo viewtitle($_POST['table_name']); ?> &nbsp;&nbsp;
              <b class="f-w-600">Status: </b>&nbsp; <?php echo $statuslistlable[$_POST['next_status']];  ?> </small>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="" method="post" id="dynamic_status_forms">
          <div class="modal-body" >
            <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                <div class="form-group row">
                    <div class="col-lg-12">
                        <span class="m-b-5">Comment</span>
                        <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="current_status" value="<?php echo $_POST['current_status']; ?>">
                <input type="hidden" name="next_status" value="<?php echo $_POST['next_status']; ?>">
                <input type="hidden" name="table_name" id="updatedstatusetransactiontable" value="<?php echo $_POST['table_name']; ?>">
                <input type="hidden" name="transaction_id" id="updatedstatusetransactionid" value="<?php echo $_POST['transaction_id']; ?>">
                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary mr-2 submitDynamiPopUpForm" id="statuspopupbtn">Submit</button>
            </div>
          </div>
      </form>
  </div>
