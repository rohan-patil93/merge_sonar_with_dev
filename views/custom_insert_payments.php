<?php
$_SESSION['apply_js']['multiselest'] = '1';
$_SESSION['apply_js']['from-validation'] = "1";
if($_POST['submit_value'] == 'allocatetransaction'){
    $i=0;
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertpayments&method=post";
   $response1 = file_post_contents($url, $_POST, 'insert');
    $response = json_decode($response1,1);
    $res = $response['payments'];
    if($res['status'] == 'sucess'){
        if($res['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_PAYMENTS");
        }
        ?>
   <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            }
        }
    </script>
<?php  }else{
        if(isset($_SESSION['error'][$res['error_no']])){
          $dis_err = explode(",", $res['other']);
          foreach ($dis_err as $va) {
            $dis_er [] = $dis_labels[$va];
          }
          $error_message = $res['error_no'] ." : ".$_SESSION['error'][$res['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
          $error_message = $res['message'];
        }
    }
}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$cusurl = API."?mode=getpayments&rid=".$rid;
$apicall = callAPI($cusurl);
$result = json_decode($apicall,1);
$bank_accounts = $result['bank_accounts'];
$vendorlist = $customers = $employees = $invoiceparty = array();
foreach($result['vendors'] as $custl){
    $vendorlist[$custl['id']] = $custl;
}
foreach($result['customers'] as $custl){
    $customers[$custl['id']] = $custl;
}
foreach($result['employees'] as $custl){
    $employees[$custl['id']] = $custl;
}
$payments = $result['payments'];
$payment_allocations = $result['payment_allocations'];
foreach($payment_allocations as $pal){
  $payment_allocations_list[$pal['transaction_table']][$pal['transaction_id']] = $pal;
}
$postdata=$payments;
$payment_transaction_type =$postdata['transaction_type']= $_SESSION['payment_transaction_type'];
if($postdata['transaction_type']=="customers"){
    $postdata['customer_id'] =json_decode($postdata['party_id'],1);
    $invoiceparty = $customers;
    $payment_transaction_type = "customers";
}elseif($postdata['transaction_type']=="employees"){
    $postdata['employee_id'] =json_decode($postdata['party_id'],1);
    $invoiceparty = $employees;
    $payment_transaction_type = "employees";
}else{
    $postdata['vendor_id'] = json_decode($postdata['party_id'],1);
    $invoiceparty = $vendorlist;
     $payment_transaction_type = "vendors";
}

$invdatares = $result['transaction_invoice'];
$invdata = $invdatares['transactions'];
$transaction_payment_allocations = $invdatares['transaction_payment_allocations'];
$payment_term_transactions = $invdatares['payment_term_transactions'];
$payment_term_transactions_due = $invdatares['payment_term_transactions_due'];
$statuse = ViewStatusData();

$postdata['transaction_type'] = $payment_transaction_type;
$amount = $payments['unallocated_amount'];
if($_POST['submit_value'] == 'gettransaction'){
    if($_POST['transaction_type']=="customers"){
        $party_id = urlencode(json_encode($_POST['customer_id']));
    }elseif($_POST['transaction_type']=="employees"){
        $party_id = urlencode(json_encode($_POST['employee_id']));
    }else{
        $party_id = urlencode(json_encode($_POST['vendor_id']));
    }
    $amount = $_POST['amount'];
    $cusurl = API."?mode=checkbilldetails&party_id=".$party_id."&due_date=".$_POST['due_date']."&transaction_type=".$_POST['transaction_type']."&payment_process=".$_POST['payment_process'];
    $invdata =  callAPI($cusurl);
    $invdataresult = json_decode($invdata,1);
    $invdata = $invdataresult['transactions'];
    $transaction_payment_allocations = $invdataresult['transaction_payment_allocations'];
    $payment_term_transactions = $invdataresult['payment_term_transactions'];
    $payment_term_transactions_due = $invdataresult['payment_term_transactions_due'];
    $postdata = $_POST;
}

$post_transaction_type = $postdata['transaction_type'];
 ?>
    <script type="text/javascript">

function addrecepitamount(fid){
        var unallocated = $("#unallocated").val();
        console.log(fid);
        var id = fid.split('_');
        id = id[1];
        var value = $('#'+fid).val();
        var val = '0';
        var transaction_table =$('#transaction_table_'+id).val();
        console.log("transaction_table "+transaction_table);

          if($('#'+fid).is(':checked')){
              if(transaction_table =="credit_notes" && "<?php echo $post_transaction_type; ?>" == "vendors"){
                var pending_amount = $("#pending_amount_"+id).val();
                $('#receipt_'+id).val(pending_amount);
                $('#tds_receipt_'+id).val(0)
                $("#total_amount_"+id).text(pending_amount);
                $("#total_amount_input_"+id).val(pending_amount);
              }else if(transaction_table =="debit_notes" && "<?php echo $post_transaction_type; ?>" == "customers"){
                var pending_amount = $("#pending_amount_"+id).val();
                $('#receipt_'+id).val(pending_amount);
                $('#tds_receipt_'+id).val(0)
                $("#total_amount_"+id).text(pending_amount);
                $("#total_amount_input_"+id).val(pending_amount);
              }else{
                $('#receipt_'+id).prop("readonly", false);
              }
          }else{
            if(transaction_table =="credit_notes" && "<?php echo $post_transaction_type; ?>" == "vendors"){
              $('#receipt_'+id).val(0);
              $('#tds_receipt_'+id).val(0)
              $("#total_amount_"+id).text(0);
              $("#total_amount_input_"+id).val(0);
            }else if(transaction_table =="debit_notes" && "<?php echo $post_transaction_type; ?>" == "customers"){
              $('#receipt_'+id).val(0);
              $('#tds_receipt_'+id).val(0)
              $("#total_amount_"+id).text(0);
               $("#total_amount_input_"+id).val(0);
            }else{
              $('#receipt_'+id).prop("readonly", true);
              $('#receipt_'+id).val(0);
              $('#tds_receipt_'+id).val(0)
            }
          }
        dynamicCalculations(id);
    }
   //          swal({
   //              text: 'Allocated amount is more than received amount',
   //              type: "warning",
   //              showCancelButton: false,
   //              confirmButtonClass: "btn-danger",
   //              confirmButtonText: "Yes",
   //              cancelButtonText: "No",
   //              closeOnConfirm: true,
   //              closeOnCancel: true


    function transactionType(type){
        anyChange();
        if(type =='customers'){
            $('#transaction_customers').show('slow');
            $('#transaction_vendors').hide('slow');
            $('#transaction_employess').hide('slow');
        }else if(type =='employees'){
            $('#transaction_customers').hide('slow');
            $('#transaction_vendors').hide('slow');
            $('#transaction_employess').show('slow');
        }else{
            $('#transaction_customers').hide('slow');
            $('#transaction_vendors').show('slow');
            $('#transaction_employess').hide('slow');

        }
    }
    function paymentType(type){
         if(type =='bank'){
            $('#bankaccount').show('slow');
            $('#bank_account_id').attr("required", true);
        }else{
            $('#bankaccount').hide('slow');
            $('#bank_account_id').attr("required", false);
        }
    }
    function anyChange(){
        $("#anychangeerror").html('<i class="ti-alert text-warning"></i> Please Re-Submit Payment Details');
        $('#anychange').css({'pointer-events' : 'none', 'opacity' : '0.7'});
    }

    function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
            text: "Please note the status of the document will change to "+new_status_name,
            type: "info",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes",
            cancelButtonText: "No",
            closeOnConfirm: false
        },
        function(){
            console.log("dataa aa");
            jQuery.ajax({
                url: "ajax.php",
                data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
                type: "POST",
                success:function(response){
                    console.log(response);
                    var res = JSON.parse(response);
                    window.location.reload();
                    if(res.transaction_update.status=='sucess'){
                         window.location.reload();
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.reload();
                        });
                    }else{
                        swal({
                            title: "Oops...",
                            text: res.transaction_update.message,
                            type: "error",
                            showCancelButton: true,
                            showConfirmButton: false,
                            cancelButtonText: "Ok",
                            confirmButtonClass: "btn-danger",
                            closeOnCancel: false
                        });
                    }
                },
                error:function (){}
            });
        });
}

function validCalucateamount(i,id,type){
    var pending_amount = parseFloat($("#pending_amount_"+i).val());
    var receptandtdsamt = parseFloat($('#receipt_'+i).val())+parseFloat($('#tds_receipt_'+i).val());
    transaction_table =$('#transaction_table_'+i).val();
    if(pending_amount >= receptandtdsamt){
        $("#total_amount_"+i).text(receptandtdsamt);
         $("#total_amount_input_"+i).val(receptandtdsamt);
         $("#allocatebtn").prop("disabled",false);
    }else{
        $("#"+id).val(0);
        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Amount Due is more than payment amount", "error");
        receptandtdsamt = parseFloat($('#receipt_'+i).val())+parseFloat($('#tds_receipt_'+i).val());
        $("#total_amount_"+i).text(receptandtdsamt);
        $("#total_amount_input_"+i).val(receptandtdsamt);

         $("#allocatebtn").prop("disabled",true);
    }
    dynamicCalculations(id);
}
function minimumcheck(){
    var check = 1;
    $('.itemRow').each(function(){
        if($(this).is(':checked')){
            check = 0;
        }
    });
    if(check == 0){
        $("#allocatebtn").prop("disabled",false);
    }else{
        $("#allocatebtn").prop("disabled",true);
    }
}
function dynamicCalculations(id){
    var count = $(".itemRow").length;
    var total = 0;
    var tds_receipt =0;
    for(var i=1; i< count+1;i++){
         var ckamount =0;
        ckamount = parseFloat($('#receipt_'+i).val());
        transaction_table =$('#transaction_table_'+i).val();
        if(ckamount != 0 && ckamount != '' && !isNaN(ckamount)){
            if(transaction_table == "credit_notes" && "<?php echo $post_transaction_type; ?>" == "vendors"){
              total -= ckamount;
              $("#total_paid_amonut_input_"+i).val(parseFloat(ckamount));
              console.log("creadit else transaction_table - vendors");
            }else if(transaction_table =="debit_notes" && "<?php echo $post_transaction_type; ?>" == "customers"){
              total -= ckamount;
              $("#total_paid_amonut_input_"+i).val(parseFloat(ckamount));
               console.log("debit else transaction_table - vendors");
            }else if("<?php echo $post_transaction_type; ?>" == "vendors"){
              console.log("else transaction_table - vendors");
                total += ckamount;
                $("#total_paid_amonut_input_"+i).val(parseFloat(ckamount));
            }else{
                total += ckamount;
                $("#total_paid_amonut_input_"+i).val(parseFloat(ckamount));
               console.log("outside transaction_table - <?php echo $post_transaction_type; ?> -- "+ckamount);
            }

        }
    }
    var unallocat = $('#amount').val();
    var unallocated = unallocat-total;
    if(unallocated < 0){
        $("#"+id).val(0);
        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", "Payment entered is more than un-allocated amount", "error");
        $("#allocatebtn").prop("disabled",true);
        return true;
    }else{
        $('#unallocated').val(unallocated);
        var unallocated = unallocated.toLocaleString('hi-IN');
        $('#unallocated_text').val(unallocated);
        $('#allocated_amount').val(total);
        var total = total.toLocaleString('hi-IN');
        $('#allocated_amount_text').val(total);
        $("#allocatebtn").prop("disabled",false);
        minimumcheck();
    }
}
function openPaymentDuePopup(seq){
  $(".close_popup_payment_due").hide();
  $(".popup_payment_due_"+seq).show();
}
function closePaymentDuePopup(){
   $(".close_popup_payment_due").hide();
}

function cancelPayments(){
  swal({
    title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
    text: 'Are you sure you want to cancel the payment? This action will cancel the payment and reverse all related transactions',
    type: "warning",
    showCancelButton: true,
    confirmButtonClass: "btn-danger",
    confirmButtonText: "Yes",
    cancelButtonText: "No",
    closeOnConfirm: true,
    closeOnCancel: true
  },
  function(isConfirm) {
    if (isConfirm) {
       jQuery.ajax({
          url: "ajax.php",
          data:"page=cancelpayments&transaction_id=<?php echo $rid; ?>&transaction_table=payments",
          type: "POST",
          success:function(data){
            console.log(data);
            location.reload();
          },
          error:function (){}
      });
    }
  });

}
</script>
 <?php if($payments['cancel_payment'] =="Y"){ ?>
<div id="dynamiccanceldiv"><span>Cancel Payment</span></div>
<?php } ?>
<?php if($content !=''){ ?>
    <div class="alert alert-custom alert-white shadow-lg fade show mb-5" role="alert">
        <div class="alert-icon"><i class="flaticon-warning kt-font-brand"></i></div>
        <div class="alert-text"><?php echo $content; ?></div>
        <div class="alert-close">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true"><i class="ki ki-close"></i></span>
            </button>
        </div>
    </div>
<?php } ?>
  <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<input type="hidden" name="mandatory_array" value='<?php echo json_encode($mandatorykeywithlabel); ?>' id="mandatory_array">
<?php
 if($payments['id'] !=""){
    $workflowsteps = getWorkflowStatus('payments',$payments['id'],$payments['current_status']);
    echo $workflowsteps['current_status_ribbon'];?>
<div class="card">
    <div class="card-header">
          <h5>Payments</h5>
          <div class="card-header-right table-card-header m-r-50">
<?php
$startworkflowbtn = 'N';
foreach($invdata as  $transationtable=>$resultdata) {
    foreach ($resultdata as  $value) {
        if(isset($payment_allocations_list[$transationtable][$value['id']])){
          $startworkflowbtn = 'Y';
        }
    }
}

    echo $startworkflowbtn == 'Y'?$workflowsteps['workflow_btn']:"";

    if($payments['current_status'] == ST_APPROVED && $payments['originator'] == $_SESSION['userid'] && $payments['cancel_payment'] =="N"){ ?>
        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="cancelPayments()">
            <i class="text-white fa fa-times-circle"></i> &nbsp;Cancel Payments
        </button>
   <?php } ?>
</div></div>
<div class="card-body">
    <?php echo $workflowsteps['breadcrumb']; ?>
</div>
</div>
<?php } ?>
<form  action="" method="post"  name="myForm"  id="form-validation" enctype="multipart/form-data">
  <input type="hidden" name="rid" value="<?php echo $rid; ?>">
    <div class="row" <?php if($payments['current_status'] !=ST_ACTIVE && $payments['current_status'] !=""){ ?>style="pointer-events: none; opacity: 0.7;" <?php
} ?>>
        <div class="col-lg-4" >
            <div class="card card-custom">
                <div class="card-header">
                  <h5 class="card-title">
                     Payments
                 </h5>
             </div>
             <div class="card-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <div class="col-lg-6 form-group form-validation-class">
                                <span class="form-label">Payment Date &nbsp;<span class="text-danger">*</span></span>
                                <input aria-label="Payment Date" type="text" class="form-control datepicker" name="payment_date"  id="payment_date" value="<?php echo fromDate($postdata['payment_date']); ?>">
                                <span class="messages"></span>
                            </div>
                            <div class="col-lg-6 form-validation-class" >
                                <?php
                                if($payment_transaction_type !="" && $payment_transaction_type =="employees"){
                                    $vendisable = "style='display:none'";
                                    $custdisable = "style='display:none'";
                                 }elseif($payment_transaction_type !="" && $payment_transaction_type =="vendors"){
                                    $empdisable = "style='display:none'";
                                    $custdisable = "style='display:none'";
                                 }elseif($payment_transaction_type !="" && $payment_transaction_type =="customers"){
                                    $empdisable = "style='display:none'";
                                    $vendisable = "style='display:none'";

                                 }
                                 ?>
                                <div class="form-group">
                                    <span class="form-label">Transaction Type &nbsp;<span class="text-danger">*</span></span>
                                    <div class="form-radio">
                                        <div class="radio radio-inline" <?php echo $vendisable; ?>>
                                          <label class="form-label">
                                            <input aria-label="Transaction Type" type="radio" name="transaction_type" id="transaction_type" value="vendors" <?php echo ($postdata['transaction_type']=='vendors' || $postdata['transaction_type'] =='')?"checked":""; ?> onchange="transactionType(this.value)">
                                            <i class="helper"></i>Vendor</label>
                                        </div>
                                        <div class="radio radio-inline" <?php echo $empdisable; ?>>
                                          <label class="form-label">
                                            <input aria-label="Transaction Type" type="radio" name="transaction_type" id="transaction_type_3" value="employees" <?php echo $postdata['transaction_type']=='employees'?"checked":""; ?> onchange="transactionType(this.value)">
                                            <i class="helper"></i>Employees</label>
                                        </div>

                                    </div>
                                    <span class="messages"></span>
                                </div>
                            </div>
                            <div class="form-group col-lg-12  w-full" id='transaction_vendors' style="<?php echo $postdata['transaction_type']=='customers' || $postdata['transaction_type']=='employees'?"display: none":""; ?>">
                                <span class="form-label">Vendor &nbsp;<span class="text-danger">*</span></span>
                                <select aria-label="Vendor []" class="form-control js-example-basic-multiple " id="vendor_id" name="vendor_id[]" onchange="anyChange()" multiple>

                                    <?php foreach ($vendorlist as $cus) {  ?>
                                        <option  <?php if(in_array($cus['id'], (array)$postdata['vendor_id'])){ ?> selected  <?php
} ?> value = <?php echo $cus['id']; ?>   > <?php echo $cus['display_name'].' ('.$cus['company'].')'; ?>
                                    </option>
                                <?php } ?>
                                </select>
                                <span class="messages"></span>
                            </div>
                            <div class="form-group col-lg-12 w-full" id='transaction_employess' style="<?php echo $postdata['transaction_type']!='employees'?"display: none":""; ?>">
                                <span class="form-label">Employees &nbsp;<span class="text-danger">*</span></span>
                                <select aria-label="Employee []" class="form-control form-select js-example-basic-multiple" id="employee_id" name="employee_id[]" onchange="anyChange()" multiple>

                                    <?php foreach ($employees as $cus){  ?>
                                        <option  <?php if(in_array($cus['id'], (array)$postdata['employee_id'])){ ?> selected  <?php
} ?> value = <?php echo $cus['id']; ?>   > <?php echo $cus['display_name']; ?>
                                    </option>
                                <?php } ?>
                                </select>
                                <span class="messages"></span>
                            </div>

                        <div class="col-lg-6 form-validation-class">
                            <div class="form-group">
                                <span class="form-label">Payment Type &nbsp;<span class="text-danger">*</span></span>
                                <div class="form-radio">
                                    <div class="radio radio-inline">
                                      <label class="form-label">
                                        <input aria-label="Payment Type" type="radio" name="payment_type" id="payment_type" value="cash" <?php echo ($postdata['payment_type']=='cash' || $postdata['payment_type'] =='')?"checked":""; ?> onchange="paymentType(this.value)">
                                        <i class="helper"></i>Cash
                                    </label>
                                </div>
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Payment Type" type="radio" name="payment_type" id="payment_type_2" value="bank" <?php echo $postdata['payment_type']=='bank'?"checked":""; ?> onchange="paymentType(this.value)">
                                    <i class="helper"></i>Bank
                                </label>
                            </div>
                        </div>
                        <span class="messages"></span>
                    </div>
                </div>

                <div class="col-lg-6 form-group form-validation-class">
                    <span class="form-label">Amount &nbsp;<span class="text-danger">*</span></span>
                    <input aria-label="Amount" class="form-control text-right" type="text" name="amount"  id="amount" value="<?php echo $postdata['amount']; ?>"  onblur="anyChange()" required>
                     <span class="messages"></span>
                </div>
                <div class="form-group col-lg-12 w-full" id="bankaccount" style="<?php echo $postdata['payment_type']=='bank'?"":"display: none"; ?>">
                        <select aria-label="Bank Account" class="form-control form-select js-example-basic-single" id="bank_account_id" name="bank_account_id" <?php echo $postdata['payment_type']=='bank'?"required":""; ?>>
                            <option value=""> Select Any Bank </option>
                            <?php foreach ($bank_accounts as $cus) { ?>
                                <option  <?php if($postdata['bank_account_id'] == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id']; ?>   > <?php echo $cus['bank_account_name']." (".$cus['account_code'].")"; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-lg-6 form-validation-class">
                            <div class="form-group">
                                <span class="form-label">Payment Process &nbsp;<span class="text-danger">*</span></span>
                                <div class="form-radio">
                                    <div class="radio radio-inline">
                                      <label class="form-label">
                                        <input aria-label="Payment Process" type="radio" name="payment_process" id="payment_process" value="NP" <?php echo ($postdata['payment_process']=='NP' || $postdata['payment_process'] =='')?"checked":""; ?> onchange="anyChange()">
                                        <i class="helper"></i>Invoice Payments
                                    </label>
                                </div>
                                <div class="radio radio-inline">
                                  <label class="form-label">
                                    <input aria-label="Payment Process" type="radio" name="payment_process" id="payment_process_2" value="AP" <?php echo $postdata['payment_process']=='AP'?"checked":""; ?> onchange="anyChange()">
                                    <i class="helper"></i>Advance Payments
                                  </label>
                                </div>
                        </div>
                        <span class="messages"></span>
                    </div>
                </div>

                <div class="col-lg-6 form-group form-validation-class">
                    <span class="form-label">Due Date &nbsp;<span class="text-danger">*</span></span>
                    <input aria-label="Due Date" class="form-control datepicker" type="text" name="due_date"  id="due_date"   value="<?php echo fromDate($postdata['due_date']); ?>" onblur=" anyChange()" required>
                     <span class="messages"></span>
                </div>
                <div class="col-lg-6 form-group">
                    <span class="form-label">Reference #</span>
                    <input aria-label="Reference number" type="text" class="form-control" placeholder="Reference number" name="ref_no" value="<?php echo $postdata['ref_no']; ?>">
                </div>

                <div class="col-lg-12 form-group">
                    <span class="form-label">Remarks</span>
                    <textarea aria-label="Remarks" class="form-control" name="remarks"><?php echo $postdata['remarks']; ?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-lg-12 text-right">

            <button id="kt_login_signin_submit_continue" class="btn btn-primary mr-2"   name="submit_value" value="gettransaction"><i class="la la-check"></i>Show Invoices</button>
        </div>
    </div>
</div>
</div>
</div>
    <div class="col-lg-8" id="anychange">

<?php if($payments['current_status'] == ST_ACTIVE || $payments['current_status'] ==""){ ?>
        <div class="card card-custom">
            <div class="card-header">
              <h5 class="card-title">
                Invoices
            </h5>
        </div>
          <?php if(count((array)$invdata) != '0'){ ?>
        <div class="card-body">
            <p class="text-danger f-20" id="anychangeerror"></p>
            <table class="table table-hover m-b-0" id="invoiceItem">
                  <tr>
                      <th scope="col" class="" style="width: 2%;">
                    </th>
                    <th scope="col" class="" style="width: 8%;">Transaction #</th>
                    <th scope="col" class="thheader" style="width: 10%;">Due Date</th>
                    <th scope="col" class="thheader text-right" style="width: 15%;">Amount</th>
                    <th scope="col" class="thheader text-right" style="width: 15%;">Due</th>
                    <th scope="col" class="thheader text-right" style="width: 20%;">Payment</th>
                    <th scope="col" class="thheader text-right" style="width: 20%;">TDS</th>
                    <th scope="col" class="thheader text-right" style="width: 10%;">Total</th>
                </tr>

                  <?php $i=1;
                  foreach ($invdata as  $transationtable=>$resultdata) {
                      foreach ($resultdata as  $value) {
                        $trclass = ""; $dueamt = 0;
                        if($transationtable == 'credit_notes' && $post_transaction_type == "vendors"){
                            $trclass = "bg-info";
                            $dueamt = $value['pending_amount'];
                        }elseif($transationtable == 'debit_notes' && $post_transaction_type == "customers"){
                            $trclass = "bg-info";
                            $dueamt = $value['pending_amount'];
                        }
                        $payamt = $tdsamt =$total_paid_amonut =  0;
                        $checkboxcheked = $payment_allocation_id = "";
                        $readonly = "readonly";
                        if(isset($payment_allocations_list[$transationtable][$value['id']])){
                            $dueamt = $payment_allocations_list[$transationtable][$value['id']]['total_amount'];
                            $payamt = $payment_allocations_list[$transationtable][$value['id']]['paid_amonut'];
                            $tdsamt = $payment_allocations_list[$transationtable][$value['id']]['tds_amount'];
                            $total_paid_amonut = $payment_allocations_list[$transationtable][$value['id']]['total_paid_amonut'];
                            $checkboxcheked = 'checked';
                            $readonly = "";
                            $payment_allocation_id =$payment_allocations_list[$transationtable][$value['id']]['id'];
                        }
                        $listpaymentterms =  $payment_term_transactions[$transationtable][$value['id']];
                        $pa_transaction = $transaction_payment_allocations[$transationtable][$value['id']];
                        if(in_array($transationtable,array('credit_notes','debit_notes'))){
                            $invdueamount =  $value['pending_amount'];
                        }else{
                            $invdueamount =  $payment_term_transactions_due[$transationtable][$value['id']];
                        }

                    ?>
                      <tr class="<?php echo $trclass; ?>">

                        <input type="hidden" name="total_tax_split_details[<?php echo $i; ?>]" value='<?php echo $value['total_tax_split_details']; ?>'>
                         <input type="hidden" name="transaction_party_id[<?php echo $i; ?>]" value="<?php echo $value['party_id']; ?>">
                          <input type="hidden" name="transaction_table[<?php echo $i; ?>]" id="transaction_table_<?php echo $i; ?>" value="<?php echo $transationtable; ?>">
                          <input type="hidden" name="invoice_number[<?php echo $i; ?>]" id="invoice_number_<?php echo $i; ?>" value="<?php echo $value['transaction_number']; ?>">
                          <input type="hidden" name="transaction_id[<?php echo $i; ?>]"  value="<?php echo $value['id']; ?>">


                         <input type="hidden" name="pending_amount[<?php echo $i; ?>]"  value="<?php echo $invdueamount; ?>" id="pending_amount_<?php echo $i; ?>">
                          <input type="hidden" name="amounts[<?php echo $i; ?>]" id="amount_<?php echo $i; ?>" value="<?php echo $value['total_amount']; ?>">

                          <input type="hidden" name="payment_allocation_id[<?php echo $i; ?>]" value="<?php echo $payment_allocation_id; ?>">
                          <input type="hidden" name="invoice_total[<?php echo $i; ?>]" value="<?php echo $dueamt; ?>" id="total_amount_input_<?php echo $i; ?>">

                          <input type="hidden" name="total_paid_amonut[<?php echo $i; ?>]" value="<?php echo $total_paid_amonut; ?>" id="total_paid_amonut_input_<?php echo $i; ?>">
                          <td class="p-2">
                              <div class="checkbox-fade fade-in-primary m-0">
                                  <label class="form-label m-0">
                                      <input aria-label="Checkbox" type="checkbox" class="itemRow" value="1" id="check_<?php echo $i; ?>" type="checkbox" name="check[<?php echo $i; ?>]" onchange="addrecepitamount(this.id)" <?php echo $checkboxcheked;  ?>>
                                      <span class="cr m-0">
                                        <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                    </span>
                                </label>
                            </div>
                        </td>
                        <td class="p-2" id="custom-popup">
                          <span class="d-block"><?php echo $value['transaction_number']; ?>
                          <?php if(count((array)$pa_transaction) != 0){ ?>
                            <i class="ti-info-alt text-primary float-end text-danger" onclick="openPaymentDuePopup('tran_<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                          <?php } ?>
                          </span>
                          <span class="d-block f-14"><?php echo ucwords($invoiceparty[$value['party_id']]['display_name']); ?></span>
                          <span class="d-block f-12"><?php echo dateFormate($value['transaction_date']); ?></span>
                           <div class="close_popup_payment_due custom-popup-open popup_payment_due_tran_<?php echo $i; ?> text-start" style="top:-40px; width: 670px; position: absolute; left :180px">
                              <div class="card latest-update-card" id="experience_details_div">
                                <div class="card-header">
                                    <h5>Payment Transaction</h5>
                                    <div class="card-header-right">
                                      <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closePaymentDuePopup()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                    </div>
                                </div>
                                <div class="card-body">
                                  <table>
                                    <thead>
                                      <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Tran No.</th>
                                         <th scope="col">payment_date</th>
                                         <th scope="col">Payment</th>
                                         <th scope="col">TDS Amount</th>
                                         <th scope="col">Total</th>
                                         <th scope="col">Status</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php $mk=1; foreach($pa_transaction as $patrans){ ?>
                                      <tr>
                                        <td><?php echo $mk; ?></td>
                                        <td><?php echo $patrans['transaction_no']; ?></td>
                                        <td><?php echo fromDate($patrans['payment_date']); ?></td>
                                        <td><?php echo customeummarmonyformateifine($patrans['paid_amonut']); ?></td>
                                         <td><?php echo customeummarmonyformateifine($patrans['tds_amount']); ?></td>
                                         <td><?php echo customeummarmonyformateifine($patrans['paid_amonut']+$patrans['tds_amount']); ?></td>
                                         <td>
                                           <span class="form-label label label-md" style="color:<?php echo $statuse[$patrans['current_status']]['color']; ?>; background-color:<?php echo $statuse[$patrans['current_status']]['background_color']; ?>;"><?php echo $statuse[$patrans['current_status']]['name']; ?></span>
                                         </td>
                                      </tr>
                                      <?php } ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>

                      </td>
                      <td class="p-2" id="custom-popup_2">
                          <?php echo dateFormate($value['due_date']); ?>
                          <?php if(count((array)$listpaymentterms) != 0){ ?>
                          <i class="ti-arrow-circle-right text-primary" onclick="openPaymentDuePopup('<?php echo $i; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                          <div class="close_popup_payment_due custom-popup-open popup_payment_due_<?php echo $i; ?> text-start" style="top:auto; left:auto; width: 300px; position: absolute; ">
                              <div class="card latest-update-card" id="experience_details_div_2">
                                <div class="card-header">
                                    <h5>Payment Terms</h5>
                                    <div class="card-header-right">
                                      <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closePaymentDuePopup()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                    </div>
                                </div>
                                    <div class="card-block">
                                      <div class="scroll-widget">
                                          <div class="latest-update-box">
                                            <?php foreach($listpaymentterms as $pterms){ ?>
                                            <div class="row p-t-10 p-b-10">
                                              <div class="col-auto text-end update-meta p-r-0">
                                                  <i class="b-primary update-icon ring"></i>
                                              </div>
                                              <div class="col p-l-5">
                                                  <a href="#!"><h7 class="f-w-600"><?php echo fromDate($pterms['due_date']); ?></h7></a>
                                                  <p class="text-muted m-b-0">
                                                    <?php echo customeummarmonyformateifine($pterms['due_amount']);
                                                  if($pterms['advance_offer_date'] !="" && $pterms['advance_offer_amount'] !=""){ ?>
                                            <span class='d-block f-10 f-w-600'>
                                              Before Date <?php echo fromDate($pterms['advance_offer_date'])." (".customeummarmonyformateifine($pterms['advance_offer_amount']).")"; ?>
                                            </span>
                                      <?php } ?>
                                            </p>
                                        </div>
                                      </div>
                                    <?php } ?>
                                    </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php } ?>
                      </td>
                      <td class="text-right p-2">
                          <?php echo customeummarmonyformateifine($value['total_amount']); ?>
                      </td>
                      <td class="text-right p-2">
                        <span class="d-block"><?php echo customeummarmonyformateifine($invdueamount); ?></span>
                        <span class="d-block f-10"><?php echo customeummarmonyformateifine($value['pending_amount']); ?></span>

                      </td>
                      <td class="p-2">
                          <input aria-label="Input field" class="form-control text-right" type="text" name="paid_amonut[<?php echo $i; ?>]" value="<?php echo $payamt; ?>" onblur="validCalucateamount('<?php echo $i; ?>',this.id,'PAYMENT')" id="receipt_<?php echo $i; ?>" <?php echo $readonly; ?>>
                      </td>
                      <td class="p-2">
                          <input aria-label="Input field" class="form-control text-right" type="text" name="tds_paid_amonut[<?php echo $i; ?>]" value="<?php echo $tdsamt; ?>" onblur="validCalucateamount('<?php echo $i; ?>',this.id,'TDS')" id="tds_receipt_<?php echo $i; ?>" readonly>
                      </td>
                      <td class="p-2 text-right">
                          <span id="total_amount_<?php echo $i; ?>"><?php echo customeummarmonyformateifine($dueamt); ?></span>

                      </td>
                  </tr>
                  <?php $i++; ?>
                  <?php } ?>
                  <?php } ?>
              <tr>
                  <td colspan="4" style="text-align: right; vertical-align: middle;">
                      Unallocated Amount
                  </td>
                  <td>
                      <input type="hidden" name="unallocated_amount" readonly class="form-control text-right" id="unallocated" value="<?php echo $amount; ?>">
                      <input aria-label="Unallocated Text" type="text" readonly class="form-control text-right" id="unallocated_text" value="<?php echo customeummarmonyformateifine($amount); ?>">
                  </td>
                  <td  style="text-align: right; vertical-align: middle;">
                      Allocated Amount
                  </td>
                  <td colspan="2">
                      <?php $allocated_amount = $payments['allocated_amount']!=""?$payments['allocated_amount']:0;?>
                      <input type="hidden" name="allocated_amount" readonly class="form-control text-right" id="allocated_amount" value="<?php echo $allocated_amount; ?>">
                      <input aria-label="Allocated Amount Text" type="text"  readonly class="form-control text-right" id="allocated_amount_text" value="<?php echo customeummarmonyformateifine($allocated_amount); ?>" required>
                  </td>
              </tr>
          </table>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-12 text-right">
                    <button id="allocatebtn11" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="allocatetransaction"><i class="la la-check"></i> <?php echo $rid!=""?"Update":"Submit"; ?></button>

                    <input type="hidden" id="allocatebtn">
                </div>
            </div>
        </div>
        <?php }else{ ?>
              <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Invoices</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
          <?php } ?>

<?php }else{  ?>
    <div class="card card-custom">
            <div class="card-header">
              <h5 class="card-title">
                Bill Allocations
            </h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-xl-4 col-md-6">
                  <div class="card ticket-card">
                      <div class="card-body">
                          <div class="text-center">
                              <h4 class="m-b-0 d-inline-block text-c-primary"><?php echo customeummarmonyformateifine($postdata['amount']); ?></h4>
                              <p class="m-b-0 m-t-15">Amount</p>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-xl-4 col-md-6">
                  <div class="card ticket-card">
                      <div class="card-body">
                          <div class="text-center">
                              <h4 class="m-b-0 d-inline-block text-c-success"><?php echo customeummarmonyformateifine($postdata['allocated_amount']); ?></h4>
                              <p class="m-b-0 m-t-15">Allocated Amount</p>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-xl-4 col-md-6">
                  <div class="card ticket-card">
                      <div class="card-body">
                          <div class="text-center">
                              <h4 class="m-b-0 d-inline-block text-c-red"><?php echo customeummarmonyformateifine($postdata['unallocated_amount']); ?></h4>
                              <p class="m-b-0 m-t-15">Un-Allocated Amount</p>
                          </div>
                      </div>
                  </div>
              </div>

          </div>
            <table class="table table-hover m-b-0" id="invoiceItem_2">
                <tr>
                  <th scope="col" class="" style="width: 10%;">Transaction #</th>
                  <th scope="col" class="thheader" style="width: 10%;">Date</th>

                  <th scope="col" class="thheader text-right" style="width: 20%;">Due Amount</th>
                  <th scope="col" class="thheader text-right" style="width: 20%;">Paid Amount</th>
                   <th scope="col" class="thheader text-right" style="width: 20%;">TDS Amount</th>
                   <th scope="col" class="thheader text-right" style="width: 20%;">Total</th>
              </tr>
              <?php foreach($payment_allocations as $paalocation){ ?>
                <tr>
                  <td class="p-2"><?php echo $paalocation['transaction_no']; ?></td>
                  <td class="p-2"><?php echo fromDate($paalocation['created_date']); ?></td>
                  <td class="text-right p-2"><?php echo customeummarmonyformateifine($paalocation['pending_amount']); ?></td>
                  <td class="text-right p-2"><?php echo customeummarmonyformateifine($paalocation['paid_amonut']); ?></td>
                  <td class="text-right p-2"><?php echo customeummarmonyformateifine($paalocation['tds_amount']); ?></td>
                  <td class="text-right p-2"><?php echo customeummarmonyformateifine($paalocation['total_amount']); ?></td>
                </tr>
              <?php }?>
          </table>
        </div>
    </div>

<?php } ?>

</div>
</div>
</div>
</form>
