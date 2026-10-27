<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submit'])){
      $_POST['company_id'] = $_SESSION['company_id'];
     $_POST['user_id'] = $_SESSION['userid'];
      $url = API."?mode=insertdebitandcreditnotes&method=post";
      $response1 = file_post_contents($url, $_POST, 'insert');
      $res = json_decode($response1,1);
      $response = $res['res'];
      if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=".$option);
        }  ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
              if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
              }
            }
        </script>
    <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
              $dis_er[] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$apiurl = API."?mode=getdebitandcreditnotes&transaction_table=".$_GET['ref_type']."&transaction_id=".$_GET['ref_id']."&note_type=".$_GET['note_type']."&rid=".$rid;
$apires = callAPI($apiurl);
$result = json_decode($apires, 1);
$transactions = $result['transactions'];
$chart_of_accounts = $result['chart_of_accounts'];
$credit_notes = $result['credit_notes'];
$debit_notes = $result['debit_notes'];
$all_transactions = $result['all_transactions'];
$referencetypes = array('purchase_invoices','supplier_bills','sales_invoices');

$note_type_res = $result['note_type_res'];
$debitcreditnote = $result['note_type_items_res'];
if($note_type_res['id'] !=""){
    $_GET['ref_type'] =$note_type_res['ref_type'];
    $_GET['ref_id'] =$note_type_res['ref_id'];
    $_GET['transaction_date'] =$note_type_res['transaction_date'];
}

?>
<script type="text/javascript">
    function submitDebitNote(){
        var account_code =$("#popup_account_code").val();
        var comment=$("#popup_comment").val();
        var amount=$("#popup_amount").val();
        var transaction_table = $("#popup_transaction_table").val();
        var transaction_id = $("#popup_transaction_id").val();
        var note_type = $("#popup_note_type").val();
        var checkemtyary = ['popup_account_code','popup_comment', 'popup_amount'];
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
                data:'page=insertdebitandcreditnotes&transaction_id='+transaction_id+'&account_code='+account_code+'&comment='+comment+'&amount='+amount+'&transaction_table='+transaction_table+'&note_type='+note_type,
                type: "POST",
                success:function(result){
                    console.log(result);
                    var data = JSON.parse(result);
                    var respon = data.debit_notes;
                    if(respon.status =="fail"){
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>",respon.message, "error");
                    }else{
                        swal("<?php echo ucwords($_SESSION['config']['name']); ?>",'<?php echo $_SESSION['error_message']['3000']; ?>', "success");
                        window.location.href=window.location.href;
                    }
                }
            });
        }
    }
    function getRefranceTableList(select_type){
        var ref_type = $("#ref_type").val();
        var ref_id = $("#ref_id").val();
        var insertoption = $("#insertoption").val();
        if(select_type == 'ref_type'){
            $.ajax({
                url: "ajax.php",
                data:'page=getdebitcreditnotereftypelist&ref_type='+ref_type,
                type: "POST",
                success:function(result){
                    console.log(result);
                    var res = JSON.parse(result);
                    $("#ref_id").html(res.option);
                }
            });
        }
        if(ref_type ==""){
            $("#reference_type_error").text('Required This Field');
        }else if(ref_id ==""){
            $("#reference_invoice_error").text('Required This Field');
        }else{
            window.location.href='index.php?option='+insertoption+'&ref_type='+ref_type+'&ref_id='+ref_id;
        }
    }


var mxid = 1000;
function AddMorecdnote(){
    var html = "";
    mxid = mxid+1;
    html +='<div class="row" id="rowid_'+mxid+'">';

    html +='<input type="hidden" name="old_id[]" value=""><div class="form-group col-lg-4"><select aria-label="Account Code[]"  class="form-select js-example-basic-single" name="account_code[]" required><option value="">Select Any One</option><?php foreach ($chart_of_accounts as $coc) { ?><option  value="<?php echo $coc['account_code']; ?>" ><?php echo $coc['account_name']."(".$coc['account_code'].")"; ?></option><?php
} ?></select></div>';

    html +='<div class="col-lg-3 form-group"><input aria-label="Item Price[]" type="text" class="form-control text-right item_price" name="item_price[]"  value="<?php echo $dcn['item_price']; ?>" onblur="calculateTotal()" required></div>';

    html +='<div class="form-group col-lg-4"><textarea aria-label="Description[]" type="text" class="form-control" name="description[]" ><?php echo $dcn['description']; ?></textarea></div>';
    html +='<div class="form-group col-lg-1 text-center"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRow('+mxid+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div>';

    html +='</div>';

    $("#addmorerows").append(html);
    $(".js-example-basic-single").select2();
}
function deletRow(id){
        $("#rowid_"+id).remove();
      }

      function calculateTotal(){
         var totaldistribution = 0;
        $(".item_price").each(function(){
            if($(this).val() !=""){
                totaldistribution +=parseFloat($(this).val());
            }
        });
        if(isNaN(totaldistribution)){
            totaldistribution = 0;
        }
        $("#totalamounttext").text(totaldistribution);
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
                    window.location.href = window.location.href;
                    if(res.transaction_update.status=='sucess'){
                         window.location.href = window.location.href;
                        swal({
                            title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                            text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                            type: "success",
                            showCancelButton: false,
                            closeOnConfirm: false,
                            showLoaderOnConfirm: true
                        }, function () {
                            window.location.href = window.location.href;
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
</script>
<?php 
if($_SESSION['config']['debit_credit_note_creation_days'] !="" AND $_SESSION['config']['debit_credit_note_creation_days'] !="0"){
    $debit_credit_note_creation_days =   $_SESSION['config']['debit_credit_note_creation_days'];
    $expiryDate = date('Y-m-d', strtotime($transactions['transaction_date'] . " +{$debit_credit_note_creation_days} days"));
    if(date('Y-m-d') >= $expiryDate){
    	$dibcreclass =  'event_disabled';
    	$debitcreadmsg = "You cannot create a Debit/Credit Note. The allowed period of {$debit_credit_note_creation_days} days has expired.";
    	echo "<div class='alert alert-danger alert-dismissible'>
                <strong>{$debitcreadmsg}</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>";
    }
}
?>
<div class="card <?php echo $dibcreclass; ?>">
     <?php
     if($note_type_res['id'] !=""){
        $workflowsteps = getWorkflowStatus($_GET['note_type'],$note_type_res['id'],$note_type_res['current_status']);
        echo $workflowsteps['current_status_ribbon'];
    }
            ?>
    <div class="card-header">
        <h5><?php echo ucwords(str_replace("_"," ",($_GET['note_type']))); ?></h5>
        <div class="card-header-right table-card-header">
            <?php echo $workflowsteps['workflow_btn']; ?>
              <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
        </div>
      </div>
    <div class="card-body p-r-50 p-l-50">
        <div class="row">
        <?php echo $workflowsteps['breadcrumb']; ?>
    </div>
        <div class="row">
            <input type="hidden" class="form-control" id="insertoption"  value="<?php echo $insertoption; ?>" required>

            <div class="col-lg-6">
                <div class="row">
                    <span class="col-lg-6">Number <span class="float-end">:</span></span>
                    <span class="col-lg-6 text-left"><?php echo $note_type_res['transaction_number'];?></span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row">
                    <span class="col-lg-6">Reference Type <span class="float-end">:</span></span>
                    <span class="col-lg-6 text-left"><?php echo ucwords(str_replace("_"," ",($_GET['ref_type']))); ?></span>
                </div>
            </div>
            <div class="col-lg-6  mt-2">
                <div class="row">
                    <span class="col-lg-6">Date <span class="float-end">:</span></span>
                    <span class="col-lg-6 text-left"><?php echo fromDate($transactions['transaction_date']);?></span>
                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <div class="row">
                    <span class="col-lg-6">Reference Invoice<span class="float-end">:</span></span>
                    <span class="col-lg-6 text-left"><?php echo $transactions['transaction_number'];?></span>
                </div>
            </div>

            <div class="col-lg-6 mt-2">
                <div class="row">
                    <span class="col-lg-6">Originator <span class="float-end">:</span></span>
                    <span class="col-lg-6 text-left"><?php echo $_SESSION['users'][$transactions['originator']];?></span>
                </div>
            </div>
            <div class="col-lg-6 mt-2">
                <div class="row">
                    <span class="col-lg-6">Party Name <span class="float-end">:</span></span>
                    <span class="col-lg-6 text-left"><?php echo $transactions['party_name'];?></span>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if(count((array)$debit_notes) != 0){ ?>
            <div class="col-lg-6">
                <div class="card" >
                    <div class="card-header">
                        <h5>Debit Note</h5>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-hover m-b-0" style="font-size:13px;">
                             <thead>
                                 <tr><th scope="col">#</th><th scope="col">Number</th><th scope="col">Account</th><th scope="col" class="text-right">Amount</th></tr>
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
                </div>
            </div>
            <?php }
if(count((array)$credit_notes) != 0){ ?>
            <div class="col-lg-6">
                <div class="card" >
                    <div class="card-header">
                        <h5>Debit Note</h5>
                        <div class="card-header-right">
                        </div>
                    </div>
                    <div class="card-body">
                         <table class="table table-hover m-b-0" style="font-size:13px;">
                             <thead>
                                 <tr><th scope="col">#</th><th scope="col">Number</th><th scope="col">Account</th><th scope="col" class="text-right">Amount</th></tr>
                             </thead>
                            <tbody>
                                <?php $mk=1;foreach($credit_notes as $creditnotes){ ?>
                                <tr>
                                    <td  class="p-2"><?php echo $mk; ?></td>
                                    <td class="p-2"><a href="index?option=COM_INSERT_CREDIT_NOTES&Rid=<?php echo deocdeId("id:".$creditnotes['id']);?>" class="text-primary"><?php echo $creditnotes['transaction_number'];?></a></td>
                                    <td class="p-2"><?php echo fromDate($creditnotes['transaction_date']);?></td>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($creditnotes['total_amount']);?></td>
                                </tr>
                            <?php $mk++;
} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php
            $visibledfields = array(ST_DRAFT,ST_ACTIVE,ST_CALLBACK,ST_SEND_BACK);
            if(in_array($note_type_res['current_status'], (array)$visibledfields) && $note_type_res['created_by'] ==$_SESSION['userid'] || $note_type_res['created_by'] ==""){
            }else{
                $dyncss = "pointer-events: none; opacity: 0.7;";
            }

    ?>
    <form method="post" style="<?php echo $dyncss; ?>">
        <div class="row mt-5">
            <div class="form-group col-lg-4">
                <span class="form-label">Reference Type</span>
                <select aria-label="Ref Type"  class="form-select js-example-basic-single" name="ref_type" id="ref_type" onchange="getRefranceTableList('ref_type')">
                    <option value="">Select Any One</option>
                    <?php foreach ($referencetypes as $reftype) { ?>
                        <option  value="<?php echo $reftype; ?>" <?php echo $_GET['ref_type'] == $reftype?"selected":""; ?>><?php echo ucwords(str_replace("_"," ",($reftype))); ?></option>
                    <?php } ?>
                </select>
                <span class="text-danger" id="reference_type_error"></span>
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Reference invoice</span>
                <select aria-label="Ref"  class="form-select js-example-basic-single" name="ref_id" id="ref_id" onchange="getRefranceTableList('ref_id')">
                    <option value="">Select Any One</option>
                    <?php foreach ($all_transactions as $coc) { ?>
                        <option  value="<?php echo $coc['id']; ?>"  <?php echo $_GET['ref_id'] == $coc['id']?"selected":""; ?>><?php echo $coc['transaction_number']; ?></option>
                    <?php } ?>
                </select>
                <span class="text-danger" id="reference_invoice_error"></span>
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Transaction Date</span>
                 <input aria-label="Transaction Date" type="text" class="form-control datepicker" name="transaction_date"  value="<?php echo $_GET['transaction_date']; ?>" required>

            </div>
        </div>


        <?php if($_GET['ref_id'] !=""){ ?>
            <div class="row" >
           <div class="form-group col-lg-4">
             <span class="form-label">Account <span class="text-danger">*</span></span>
           </div>
           <div class="form-group col-lg-3 w-full" >
             <span class="form-label d-block">Amount<span class="text-danger">*</span></span>
           </div>
           <div class="form-group col-lg-4 w-full" >
             <span class="form-label d-block">Comment<span class="text-danger">*</span></span>
           </div>
           <div class="form-group col-lg-1 w-full" >
              <span class="form-label d-block"> Remove </span>
            </div>
        </div>
            <div id="addmorerows">
                <?php
                $totalamt = 0;
                if(count((array)$debitcreditnote) ==0){
                  $debitcreditnote[] = array();
                }
                $mk=1; foreach($debitcreditnote as $dcn){  $totalamt +=$dcn['item_price'];  ?>
                <div class="row" id="rowid_<?php echo $mk; ?>">
                    <input type="hidden" name="old_id[]" value="<?php echo $dcn['id']; ?>">
                    <div class="form-group col-lg-4">
                        <select aria-label="Account Code[]"  class="form-select js-example-basic-single" name="account_code[]" required>
                            <option value="">Select Any One</option>
                            <?php foreach ($chart_of_accounts as $coc) { ?>
                                <option  value="<?php echo $coc['account_code']; ?>" <?php echo $dcn['account_code'] == $coc['account_code']?"selected":""; ?>><?php echo $coc['account_name']."(".$coc['account_code'].")"; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-3 form-group">
                        <input aria-label="Item Price[]" type="text" class="form-control text-right item_price" name="item_price[]"  value="<?php echo $dcn['item_price']; ?>" onblur="calculateTotal()" required>
                    </div>
                    <div class="form-group col-lg-4">
                        <textarea aria-label="Description[]" type="text" class="form-control" name="description[]" ><?php echo $dcn['description']; ?></textarea>
                    </div>
                     <div class="form-group col-lg-1 text-center">
                        <i class="ti-trash"  onclick="deletRow('<?php echo $mk; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                      </div>
                </div>
                <?php $mk++;
}  ?>
            </div>
        <div class="row mt-3">
            <div class="col-lg-4">
                <button class="btn btn-success" type="button" onclick="AddMorecdnote()">+ Add More</button>
            </div>
            <div class="col-lg-3 text-right f-w-700">Total</div>
            <div class="col-lg-4 text-right f-w-700" id="totalamounttext"><?php echo $totalamt; ?></div>
        </div>
        <div class="p-0 mt-5">
              <?php
                  if($rid ==""){
                      $disbledbtn = "disabled btn-danger";
                  }
              ?>
              <input type="hidden" class="form-control text-right" name="note_type" value="<?php echo $_GET['note_type']; ?>">
            <?php if(isset($_GET['Rid']) && $_GET['Rid'] !=''){ ?>
                    <input type="hidden" id="rid"name="rid" value="<?php echo $rid; ?>" />
                    <button class="btn btn-primary mr-2 mb-2 submit_btn <?php echo $disbledbtn; ?>"  type="submit" name="submit" value="submit" ><i class="la la-check"></i>Update</button>

            <?php }else{ ?>
                     <button class="btn btn-primary mr-2 mb-2" type="submit" name="submit" value="submit" >
                        <i class="la la-check-circle"></i>Submit</button>
                    <button type="reset" class="btn btn-secondary mb-2" ><i class="la la-close"></i>Cancel</button>
            <?php } ?>
        </div>
    <?php } ?>
    </div>
