<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$transaction_table = $_POST['transaction_table'] ?? '';
$transaction_id = $_POST['transaction_id'] ?? '';
$party_debit = array('sales_invoices','purchase_invoice_cancellations','supplier_bill_cancellations','rental_invoices','utility_rental_invoices');
$party_credit = array('supplier_bills','purchase_invoices','sales_invoice_cancellations');

$vurl = API."?mode=getcancelinvoicepopup&transaction_table=".$transaction_table."&transaction_id=".$transaction_id; 
$vdata_details = callAPI($vurl);
$vdata_detail = json_decode($vdata_details,1);
$transaction = $vdata_detail['transaction'] ?? array();
$ref_transaction = $vdata_detail['ref_transaction'] ?? array();

$statuse = ViewStatusData();
$shownoterequired  = "style='display:none'";
$checked = "";
$debitcreditlable = "";
$debitcredittable = "";
if(($transaction['total_amount'] ?? 0) != ($transaction['pending_amount'] ?? 0)){ 
    $shownoterequired  = "style='display:block'";
    $checked = "checked";
    if(in_array($transaction_table, (array)$party_credit)){
        $debitcreditlable = "Debit note";
        $debitcredittable = "debit_notes";
    }elseif(in_array($transaction_table, (array)$party_debit)){
        $debitcreditlable = "Credit note";
        $debitcredittable = "credit_notes";
    }
}
$disabledbtn = "";
?>
<script type="text/javascript">
    function submitCancelInvoice(){
        $("#popupshowerror").hide();
        var error = 0; 
        var cancel_invoice_date = $("#cancel_invoice_date").val();
        var cancel_pending_amount = $("#cancel_pending_amount").val();
        var cancel_invoice_reason = $("#cancel_invoice_reason").val();
        if(cancel_invoice_date =="" || cancel_invoice_reason ==""){
            error = 1; 
        }
        if($("#debit_credit_note").is(":checked")){
            if(cancel_pending_amount ==""){
                error = 1; 
            }
        }
        if(error =="0"){
            var formdata = $("#creatdebitcreaditnotesubmitform").serialize();
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=insertdynamiccancelinvoice&'+formdata,
                success: function(response) {
                    console.log(response);
                    try {
                        var res = (typeof response === 'object') ? response : JSON.parse(response);
                        if(res.cancel_invoices && res.cancel_invoices.main && res.cancel_invoices.main.status === 'sucess'){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001'] ?? 'Saved successfully'; ?>','');
                            location.reload();
                        }else{
                            var errMsg = (res.cancel_invoices && res.cancel_invoices.main && res.cancel_invoices.main.message) ? res.cancel_invoices.main.message : 'Error occurred';
                            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX',errMsg,'');
                        }
                    } catch(e) {
                        console.error(e);
                    }
                }
            });
        }else{
            $("#popupshowerror").show();
        }
    }
    function creatDebitCreaditNote(){
        if($("#debit_credit_note").is(":checked")){
            $(".creatdebitcreaditnoteamount").show('slow');
        }else{
            $(".creatdebitcreaditnoteamount").hide('slow');
        }
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Invoice Cancellations</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body m-l-50 m-r-50">
    <div class="row">
        <div class="form-group col-lg-3 text-center">
            <span class="form-label d-block">Transaction No.</span>
            <span><?php echo $transaction['transaction_number'] ?? '';?></span>
        </div>
        <div class="form-group col-lg-3 text-center">
            <span class="form-label d-block">Transaction Date</span>
            <span><?php echo isset($transaction['transaction_date']) ? fromDate($transaction['transaction_date']) : '';?></span>
        </div>
        <div class="form-group col-lg-3 text-center">
            <span class="form-label d-block">Transaction Amount</span>
            <span><?php echo isset($transaction['total_amount']) ? customeummarmonyformate($transaction['total_amount']) : '';?></span>
        </div>
        <div class="form-group col-lg-3 text-center">
            <span class="form-label d-block">Pending Amount</span>
            <span><?php echo isset($transaction['pending_amount']) ? customeummarmonyformate($transaction['pending_amount']) : '';?></span>
        </div>
        <div class="form-group col-lg-3 text-center">
            <span class="form-label d-block">Status</span>
            <span class="form-label label label-md" style="color:<?php echo $statuse[$transaction['current_status']]['color'] ?? '#000'; ?>; background-color:<?php echo $statuse[$transaction['current_status']]['background_color'] ?? '#fff'; ?>;">
                <?php echo $statuse[$transaction['current_status']]['name'] ?? ''; ?>
            </span>
        </div>
        <?php if(!empty($ref_transaction) && count((array)$ref_transaction) != 0){ $disabledbtn = "disabled"; ?>
        <div class="form-group col-lg-9 text-center">
            <span class="form-label d-block">Message</span>
            <span class="text-danger">
                <?php     
                    echo "This transaction cannot be cancelled because it has a reference.</br>";
                    foreach($ref_transaction as $transa => $trans){
                        echo htmlspecialchars($transa, ENT_QUOTES)." - ".htmlspecialchars($trans['transaction_number'] ?? '', ENT_QUOTES)."(".(isset($trans['transaction_date']) ? fromDate($trans['transaction_date']) : '').")</br>";
                    }
                ?>
            </span>
        </div>
        <?php } ?>
    </div>
    <form id="creatdebitcreaditnotesubmitform">
        <div <?php echo $shownoterequired; ?>>
            <label class="text-danger text-capitalize event_disabled" style="cursor: pointer;">
                Invoice amount not settled please create 
                <?php echo in_array($transaction_table, CREDIT_TRANSACTION_WITH_PARTY)?"Debit note":"Credit note"; ?>? 
                <input aria-label="Debit Credit Note" class="js-primary-sm-popup" type="checkbox" name="debit_credit_note" value="Y" id="debit_credit_note" <?php echo $checked; ?> onchange="creatDebitCreaditNote()">
            </label>
        </div>
        <div class="row mt-2">
            <div class="col-lg-4">
                <div class="form-group">
                    <span class="form-label">Cancellation Date<span class="text-danger">*</span></span>
                    <input aria-label="Cancellation Date" type="text" class="form-control datepicker" name="cancel_invoice_date" id="cancel_invoice_date">
                </div>
                <div class="form-group creatdebitcreaditnoteamount" <?php echo $shownoterequired; ?>>
                    <span class="form-label"><?php echo in_array($transaction_table, CREDIT_TRANSACTION_WITH_PARTY)?"Debit":"Credit"; ?> Note Amount<span class="text-danger">*</span></span>
                    <input aria-label="Note Amount" type="number" class="form-control" name="cancel_pending_amount" id="cancel_pending_amount" value="<?php echo (($transaction['total_amount'] ?? 0) - ($transaction['pending_amount'] ?? 0));?>">
                </div>
            </div>
            <div class="form-group col-lg-8">
                <span class="form-label">Reasons<span class="text-danger">*</span></span>
                <textarea aria-label="Reasons" class="form-control" name="cancel_invoice_reason" id="cancel_invoice_reason"></textarea>
            </div>
        </div>
        <input type="hidden" name="debitcreditnotetable" id="debitcreditnotetable" value="<?php echo $debitcredittable; ?>"> 
        <input type="hidden" name="transaction_table" value="<?php echo htmlspecialchars($transaction_table, ENT_QUOTES); ?>"> 
        <input type="hidden" name="transaction_id" value="<?php echo htmlspecialchars($transaction_id, ENT_QUOTES); ?>"> 
    </form>
</div>
<div class="modal-footer">
    <span class="text-danger f-w-700" id="popupshowerror" style="display: none">Please fill out all required fields</span>
    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitCancelInvoice()" <?php echo $disabledbtn; ?>>Submit</button>
    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
</div>

