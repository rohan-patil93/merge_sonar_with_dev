<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$apiurl = API."?mode=getrentalinvoices&rid=".$_POST['rental_inv_id'];
$apires = callAPI($apiurl);
$result = json_decode($apires, 1);
$rental_invoices = $result['rental_invoices'];
$chart_of_accounts = $result['chart_of_accounts'];
$debit_notes = $result['debit_notes'];
?>
<script type="text/javascript">
    function submitDebitNote(rental_inv_id){
        var popup_account_code =$("#popup_account_code").val();
        var popup_comment=$("#popup_comment").val();
        var popup_amount=$("#popup_amount").val();
        var popup_transaction_table = $("#popup_transaction_table").val();
        var checkemtyary = ['popup_comment', 'popup_amount'];
        var error = 0;
        $.each(checkemtyary , function(index, val) {
             if($("#"+val).val() ==""){
                 $("#"+val+"_error").text('Required This Field');
                 error = 1;
             }else{
                 $("#"+val+"_error").text(' ');
             }
        });
        if(error == '0'){
            $.ajax({
                url: "ajax.php",
                data:'page=insertdebitnote&transaction_id='+rental_inv_id+'&popup_account_code='+popup_account_code+'&popup_comment='+popup_comment+'&popup_amount='+popup_amount+'&popup_transaction_table='+popup_transaction_table,
                type: "POST",
                success:function(result){
                    console.log(result);
                    var data = JSON.parse(result);
                    var respon = data.debit_notes;
                    if(respon.status =="fail"){
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>",respon.message, "error");
                    }else{
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['3000']; ?>', "success");
                    }
                    location.reload();
                }
            });
        }
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Debit Note</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-r-50 p-l-50">
    <div class="row">
        <div class="col-lg-6">
            <div class="row">
                <span class="col-lg-6">Number <span class="float-end">:</span></span>
                <span class="col-lg-6 text-left"><?php echo $rental_invoices['transaction_number'];?></span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="row">
                <span class="col-lg-6">Reference Type <span class="float-end">:</span></span>
                <span class="col-lg-6 text-left">Rental Invoices</span>
            </div>
        </div>
        <div class="col-lg-6  mt-2">
            <div class="row">
                <span class="col-lg-6">Date <span class="float-end">:</span></span>
                <span class="col-lg-6 text-left"><?php echo fromDate($rental_invoices['transaction_date']);?></span>
            </div>
        </div>

        <div class="col-lg-6 mt-2">
            <div class="row">
                <span class="col-lg-6">Due Date <span class="float-end">:</span></span>
                <span class="col-lg-6 text-left"><?php echo fromDate();?></span>
            </div>
        </div>

        <div class="col-lg-6 mt-2">
            <div class="row">
                <span class="col-lg-6">Originater <span class="float-end">:</span></span>
                <span class="col-lg-6 text-left"><?php echo $_SESSION['users'][$rental_invoices['originator']];?></span>
            </div>
        </div>
        <div class="col-lg-6 mt-2">
            <div class="row">
                <span class="col-lg-6">Tenant Name <span class="float-end">:</span></span>
                <span class="col-lg-6 text-left"><?php echo $rental_invoices['party_name'];?></span>
            </div>
        </div>
    </div>
        <?php if(count((array)$debit_notes) !=0){ ?>
    <div class="row">
         <table class="table table-hover m-b-0" style="font-size:13px;">
             <thead>
                 <tr><th scope="col">#</th><th scope="col">Number</th><th scope="col">Date</th><th scope="col" class="text-right">Amount</th></tr>
             </thead>
            <tbody>
                <?php $mk=1;foreach($debit_notes as $debitnotes){ ?>
                <tr>
                    <td  class="p-2"><?php echo $mk; ?></td>
                     <td class="p-2"><a href="index?option=COM_INSERT_DEBIT_NOTES&Rid=<?php echo deocdeId("id:".$debitnotes['id']);?>" class="text-primary"><?php echo $debitnotes['transaction_number'];?></a></td>
                    <td class="p-2"><?php echo fromDate($debitnotes['transaction_date']);?></td>
                    <td class="text-right p-2"><?php echo customeummarmonyformate($debitnotes['total_amount']);?></td>
                </tr>
            <?php $mk++;
} ?>
            </tbody>
        </table>
    </div>
<?php } ?>
    <div class="row mt-5">
        <div class="form-group col-lg-6">
            <span class="col-lg-4">Account</span>
            <select aria-label="Account Code"  class="form-select js-example-basic-single" name="account_code" id="popup_account_code" >
                <option value="">Select Any One</option>
                <?php foreach ($chart_of_accounts as $coc) { ?>
                    <option  value="<?php echo $coc['account_code']; ?>"><?php echo $coc['account_name']."(".$coc['account_code'].")"; ?></option>
                <?php } ?>
            </select>
            <span class="text-danger" id="popup_account_code_error"></span>
        </div>

        <div class="col-lg-6 form-group">
            <span class="form-label">Amount</span>
            <input aria-label="Amount" type="text" class="form-control text-right" name="amount"  id="popup_amount" value="">
            <span class="text-danger" id="popup_amount_error"></span>
        </div>
        <div class="form-group col-lg-12">
            <span class="col-lg-4">Comment</span>
            <textarea aria-label="Comment" type="text" class="form-control" name="comment" id="popup_comment" ></textarea>
            <span class="text-danger" id="popup_comment_error"></span>
        </div>
    </div>
</div>
<div class="modal-footer" >
     <input type="hidden" class="form-control text-right" name="amount"  id="popup_transaction_table" value="rental_invoices">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-success waves-effect waves-light" onclick="submitDebitNote('<?php echo $_POST['rental_inv_id']; ?>')"> Generate Debit Note</button>
</div>
