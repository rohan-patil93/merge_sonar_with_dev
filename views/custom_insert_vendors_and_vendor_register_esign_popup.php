<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$nm = ViewStatusValue($_POST['status']);
?>

<div class="modal-header">
    <h5 class="modal-title">eSign</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form id="signature-popup" method="post">
    <div class="modal-body text-center">
        <img src="" id="my_sign" alt="Preview">
        <span class="form-lable m-l-20">Signature</span>
        <div id="signPadContainer"  style="text-align: center;">
            <canvas id="signPadCanvas" disabled="disabled"  style="border: 0px solid lightblue; box-shadow: rgba(0, 0, 0, 0.75) 3px 5px 5px 0px;touch-action: none;" height="300" width="300" class="rr"></canvas>
            <div class="mt-2">
                <button type="button" class="enableSignaturePad" >Reset</button>
                <button type="button" class="confirmSignature">Confirm</button>
            </div>
            <p>Please click here to confirm button after signature</p>
        </div>
        <input type="hidden" name="sign" id="pickedSignContent" value="">
        <div class="modal-footer">
            <input type="hidden" name="transaction_id"  value="<?php echo $_POST['transaction_id']; ?>">
            <input type="hidden" name="transaction_table" value="<?php echo $_POST['transaction_table']; ?>">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary mr-2 disabled submitromsignForm" id="submitpopup_btn" >Submit</button>
        </div>
    </div>
</form>
