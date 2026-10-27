<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$transaction_table = $_POST['transaction_table'];
$transaction_id= $_POST['transaction_id'];
$clone_table = $_POST['clone_table'];
$party_table =  $_POST['party_table'];
$redirecturl = getPagenametotable($clone_table,'list_page_name');
$vurl = API."?mode=getclonetransactions&transaction_table=".$transaction_table."&transaction_id=".$transaction_id."&clone_table=".$clone_table."&party_table=".$party_table;
$resapi = callAPI($vurl);
$result = json_decode($resapi,1);
$party_list = $result['party_list'];
$transaction_table_detail = $result['transaction_table_detail'];
$transaction_table_item_detail = $result['transaction_table_item_detail'];
$btndisabled = "disabled";
?>
<script type="text/javascript">
function submitCloneTransaction(){
    var party_id_popup = $('#party_id_popup').val();
    if(party_id_popup !=""){
        $("#statuspopupbtn").text("Please Wait........");
        $("#statuspopupbtn").addClass("disabled");
        $("#party_id_popup_error").hide();
        var poststr = $('#clone_transaction_form').serialize();
        $.ajax({
              url: "ajax.php",
              type: "POST",
              data: 'page=insertclonetransactions&'+poststr,
              success: function(response){
                  $('#kt_modal_dynamic_popup').modal('hide');
                swal({
                    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                    text: "<?php echo ucwords(str_replace("_"," ",($clone_table))).' '.$_SESSION['error_message']['9059']; ?>",
                    type: "success",
                    showCancelButton: false,
                    closeOnConfirm: true,
                    showLoaderOnConfirm: true
                }, function () {
                    $('#kt_modal_dynamic_popup').modal('hide');
                    window.location.replace("index.php?option=<?php echo $redirecturl; ?>");
                });
            }
        });
    }else{
        $("#party_id_popup_error").show();
    }
}
</script>
<div class="modal-header">
    <h5 class="modal-title"><?php echo viewtitle($transaction_table); ?> - <?php echo $transaction_table_detail['transaction_number']; ?> (<?php echo $transaction_table_detail['transaction_date']; ?>) Clone To  <?php echo viewtitle($clone_table); ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form  method="post" id="clone_transaction_form">
    <div class="modal-body" >
        <div class="form-group row">
            <div class="col-lg-6 w-full" >
                <span class="form-label">Vendors <span class="text-danger">*</span></span>
                <select aria-label="Party" class="form-control js-example-basic-single" name="party_id" id="party_id_popup" >
                    <?php foreach ($party_list as $plist) {  ?>
                        <option value = <?php echo $plist['id'] ?>><?php echo $plist['display_name']; ?></option>
                    <?php } ?>
                </select>
                <span id="party_id_popup_error" class="text-danger" style="display: none">Please Select Any One Vendor</span>
            </div>
            <div class="col-lg-6 ">
                <div class="card">
                    <div class="card-header">
                        <h5>Items</h5>
                    </div>
                    <div class="card-body ">
                        <table style="width: 100%;">
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col" class="text-right">Qty</th>
                                <th scope="col" class="text-right text-danger">Pending Qty</th>
                            </tr>
                        <?php foreach($transaction_table_item_detail as $ttid){
                            if($ttid['pending_qty'] > 0){
                                $btndisabled = "";
                            }
                            ?>
                            <tr>
                                <td class="w-b-w-s"><?php echo $ttid['item_name']; ?></td>
                                <td class="text-right"><?php echo $ttid['item_quantity']; ?></td>
                                <td class="text-right text-danger"><?php echo $ttid['pending_qty']; ?></td>
                            </tr>
                        <?php } ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="hidden" name="transaction_table" id='transaction_table' value="<?php echo $transaction_table; ?>">
        <input type="hidden" name="transaction_id" id='transaction_id' value="<?php echo $transaction_id; ?>">
        <input type="hidden" name="clone_table" id='clone_table' value="<?php echo $clone_table; ?>">
        <input type="hidden" name="party_table" id='party_table' value="<?php echo $party_table; ?>">
        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary waves-effect waves-light <?php echo $btndisabled; ?>" id="statuspopupbtn"  onclick="submitCloneTransaction()" <?php echo $btndisbale; ?>>Submit</button>
    </div>
</form>
<!-- local datatable modal -->
    <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
