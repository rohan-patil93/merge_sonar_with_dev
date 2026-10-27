<?php
$_SESSION['apply_js']['multiselest'] = 1;
$File_Uploads_Path = "uploads/reimbursement/";
if($_POST['submit_value'] == 'submit_expense'){
    $i=0;
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
     $_POST['emp_id'] = $_SESSION['emp_id'];
    foreach($_POST['row_sequince'] as $key=>$fil){

        if($_FILES['bill_file']['error'][$key] != "4"){
            $temp = explode(".", $_FILES['bill_file']['name'][$key]);
            $Filename=  "file_".time()."_".uniqid();
            $newfilename = $Filename.'.' .end($temp);
            if(move_uploaded_file($_FILES['bill_file']["tmp_name"][$key], $File_Uploads_Path.$newfilename)){
                $_POST['bill_file'][$key] = $newfilename;
            }
        }else{
            $_POST['bill_file'][$key] = $_POST['old_bill_file'][$key];
        }
    }

    $url = API."?mode=insertempexpenses&method=post";
       $response1 = file_post_contents($url, $_POST, 'insert');
    $response = json_decode($response1,1);
    $res = $response['res'];
    if($res['status'] == 'sucess'){
        if($res['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_EMP_EXPENSES");
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
$url = API . "?mode=getempexpenses&method=get&id=$rid";
$result = callAPI($url);
$result = json_decode($result, 1);
$trips = $result['trips'];

$projects_list = $result['projects'];
$emp_expenses = $result['emp_expenses'];
$emp_expenses_details = $result['emp_expenses_details'];
foreach($result['expense_categories'] as $catdat){
  $datacat[$catdat['category_id']]=$catdat;
}
foreach($result['expenses_mileages_perdiem'] as $catdat){
    if($catdat['expense_type']=='0'){
        $mileageslist[$catdat['id']]=$catdat;
    }else{
        $perdiamarray[$catdat['id']]=$catdat;
    }
}
$arraycurrncy = array("1"=>"INR","2"=>"USD");
foreach($result['general_code_details'] as $code){
    if($code['genc_code']=="PAYMETHOD"){
        $Payment_mode[$code['genc_detail_code']]  = $code['genc_detail_name'];
    }elseif($code['genc_code']=="EXPENSTYPE"){
        $expense_typelist[$code['genc_detail_code']]  = $code['genc_detail_name'];
    }
}

$trips_select = $result['trips_select'];
$domestic_round_trips = $result['domestic_round_trips'];
$trip_hotels = $result['trip_hotels'];
$local_trips = $result['local_trips'];
$cities = $result['cities'];
$vehicle_types = $result['vehicle_types'];

$countries = $result['countries'];

?>
<script type="text/javascript">
    <?php if($_GET['TRIPID'] !=""){ ?>
    window.addEventListener("load", function() {
        selectTrips();
    });
    <?php } ?>
    function selectTrips(){
        var trip_id = $("#trip_id").val();
        jQuery.ajax({
            url: "views/custom_insert_emp_expense_ajax.php",
            data: 'trip_id='+trip_id+'&expaince_list=N',
            type: "POST",
            success:function(data){
                $("#trip_details").html(data);
            }
        });

        jQuery.ajax({
            url: "views/custom_insert_emp_expense_ajax.php",
            data: 'trip_id='+trip_id+'&expaince_list=Y',
            type: "POST",
            //async: false,
            success:function(data){
                console.log(data);
                $(".trip_transaction_table").html(data);
            }
        });

    }
function checkFuntion(seq){
    expense_type = $('input[name="expense_type_'+seq+'"]:checked').val();
    $(".expens_css_"+seq).hide();
    $(".mileage_css_"+seq).hide();
    $(".Perdiem_css_"+seq).hide();
    if(expense_type=='1'){
        id =  "mileage";
        $(".mileage_css_"+seq).show();
    }else if(expense_type=='2'){
        id = "perd";
        $(".Perdiem_css_"+seq).show();
    }else{
        var id="exp";
        $(".expens_css_"+seq).show();
    }
    $("#amount_popup_span_"+id).text("");
    $("#tax_popup").val("0");
    $("#total_popup").val("");
}
function itemChange(valuu,val_id,seq){
        var expense_type = $('input[name="expense_type_'+seq+'"]:checked').val();

        var id="exp_3";
        if(expense_type=='1'){
        id =  "mileage";
            var mileageslistson = JSON.parse($("#mileageslistson").val());
            var newStringars = mileageslistson[val_id]['amount']
        }else if(expense_type=='2'){
           id = "perd";
          var perdiamarrayjson = JSON.parse($("#perdiamarrayjson").val());
          var newStringars = perdiamarrayjson[val_id]['amount']
        }
        $("#amount_popup_span_"+seq+"_"+id).text(" (Rs. "+newStringars+")");
        $("#amount_popup_"+seq+"_"+id).val(newStringars);
   }
   function calucateamount(seq){
        var expense_type = $('input[name="expense_type_'+seq+'"]:checked').val();
        var id="exp_4";
        if(expense_type=='1'){
            id =  "mileage";
        }else if(expense_type=='2'){
            id = "perd";
        }
        var total = 0;
        console.log(id);
        var amount = $("#amount_popup_"+seq+"_"+id).val();
        var qty = $("#qty_popup_"+seq+"_"+id).val();
        var tax = $("#tax_popup_"+seq).val();
        var sumamt = parseFloat(amount)*parseFloat(qty);
        var total = parseFloat(sumamt)+parseFloat(tax);
        if(!isNaN(total)){
          $("#total_popup_"+seq).val(total);
        }else{
            $("#total_popup_"+seq).val(0);
        }
        totalCaluclation();
    }
    function deleteExpenseRow(seq){
        $(".expenseRow_"+seq).remove();
        totalCaluclation();
    }

    var seqs = 100001;
    function addExpenseRow(){
        $("#add_expense").addClass('disabled');
        seqs= seqs+1;
        var htmlRows = '';
        htmlRows += '<tr class="expenseRow_'+seqs+'">';
        htmlRows += '<td>';
        htmlRows += '<input type="hidden" name="row_sequince[]"  class="row_sequince" value="'+seqs+'">';
        htmlRows += '<input type="hidden" name="emp_expenses_detail_id[]"  value="">';
        htmlRows += '<div class="d-block">';
        htmlRows += '<div class="form-radio">';
        <?php
        $sub_details['expense_type'] = (isset($sub_details['expense_type']) && $sub_details['expense_type'] != "") ? $sub_details['expense_type'] : '0';
        foreach($expense_typelist as $expense_typelkey => $expense_typel){ ?>
            htmlRows += '<div class="radio radio-outline radio-inline" onclick="checkFuntion('+seqs+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><label class="form-label"><input aria-label="Expense Type" type="radio" name="expense_type_'+seqs+'" id="gender-1" value="<?php echo $expense_typelkey; ?>" <?php echo $expense_typelkey == 0?"checked":""; ?>><i class="helper"></i><?php echo $expense_typel; ?></label></div>';
          <?php } ?>
    htmlRows += '</div>';
    htmlRows += '</div>';
    htmlRows += '<div class="row  m-t-15">';
    htmlRows += '<div class=" col-lg-5" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Expense Date"><label for="">Date of Spend <span class="text-danger">*</span></label><input aria-label="Expense Date[]" type="text" name="expense_date[]" class="form-control datepicker dateexpens expens_req mileage_req Perdiem_req" onchange="changedateexpense(this.value)" placeholder="" value="">';
    htmlRows += '</div>'


    htmlRows += '<div class="col-lg-7 expens_css_'+seqs+' w-full" style="" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Category"><label for="" class="d-block">Category <span class="text-danger">*</span></label>';
    htmlRows += '<select aria-label="Category Exp[]" class="form-control form-select js-example-basic-single expens_req" id="select_category_exp'+seqs+'" name="category_exp[]"  ><option value="">Select Any One</option>';
        <?php foreach($datacat as $dataca){ ?>
            htmlRows += '<option value="<?php echo $dataca['category_id']; ?>"><?php echo $dataca['category_name']; ?></option>';
           <?php } ?>
    htmlRows += '</select>';
    htmlRows += '</div>';
    htmlRows += '<div class="col-lg-7 mileage_css_'+seqs+' w-full" style="display:none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Mileage"><label for="" class="d-block">Mileage <span class="text-danger">*</span></label>';
    htmlRows += '<select aria-label="Category Mileage[]" class="form-control form-select js-example-basic-single mileage_req" id="select_category_mileage'+seqs+'" name="category_mileage[]" onchange="itemChange(this.options[this.selectedIndex].text,this.value,'+seqs+')" >';
     htmlRows += '<option value="">Select Any One</option>';
        <?php foreach($mileageslist as $mileageslists){ ?>
             htmlRows += '<option value="<?php echo $mileageslists['id']; ?>" <?php echo $sub_details['category_id']==$mileageslists['id'] ? "selected":"" ;?>><?php echo $vehicle_types[$mileageslists['name']]." (".$mileageslists['amount'].")"; ?></option>';
        <?php } ?>
    htmlRows += '</select>';
    htmlRows += '</div>';
    htmlRows += '<div class="col-lg-7 Perdiem_css_'+seqs+' w-full" style="display:none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Perdiem"><label for="" class="d-block">Per Diem <span class="text-danger">*</span></label>';
    htmlRows += '<select aria-label="Category Perd[]" class="form-control form-select js-example-basic-single Perdiem_req" id="select_category_perd'+seqs+'" name="category_perd[]" onchange="itemChange(this.options[this.selectedIndex].text,this.value,'+seqs+')"  >';
    htmlRows += '<option value="">Select Any One</option>';
        <?php foreach($perdiamarray as $perdieam){ ?>
            htmlRows += '<option value="<?php echo $perdieam['id']; ?>"><?php echo $countries[$perdieam['name']]." (".$perdieam['amount'].")"; ?></option>';
        <?php } ?>
    htmlRows += '</select>';
    htmlRows += '</div>';
    htmlRows += '</div>';

    htmlRows += '<div class="m-t-15">';
    htmlRows += '<label for="" class="d-block">Trip Slot<span class="text-danger">*</span></label>';
    htmlRows += '<select aria-label="Select option" class="form-control form-select js-example-basic-single trip_transaction_table" id="select_trip_transaction_table_<?php echo $seqs; ?>" name="trip_transaction_table[]"  >';
        var trip_id = $("#trip_id").val();
        if(trip_id !=""){
            jQuery.ajax({
                url: "views/custom_insert_emp_expense_ajax.php",
                data: 'trip_id='+trip_id+'&expaince_list=Y',
                type: "POST",
                async: false,
                success:function(data){
                    htmlRows += data;
                    $("#add_expense").removeClass('disabled');
                }
            });
        }

            htmlRows += '</select>';
            htmlRows += '</div>';

    htmlRows += '</td>';

    htmlRows += '<td>';
    htmlRows += '<input aria-label="Enter purpose" type="text" name="description[]" class="form-control expens_req mileage_req Perdiem_req" placeholder="Enter purpose" value="<?php echo $sub_details['description']; ?>" id="description_'+seqs+'">';
    htmlRows += '<div class="expens_css_'+seqs+' mt-2"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Enter Amount" placeholder="0.00">';
    htmlRows += '<label for="" class="mb-1">Amount <span class="text-danger">*</span></label>';

    htmlRows += '<input aria-label="Rate Exp[]" type="text" name="rate_exp[]" class="form-control text-right rate_exp_row decimalnumberallowe expens_req" value="" onblur="calucateamount('+seqs+')" id="amount_popup_'+seqs+'_exp" >';

    htmlRows += '<input type="hidden" name="qty_exp[]" value="1"  class="form-control text-right " readonly id="qty_popup_'+seqs+'_exp" >';
    htmlRows += '</div>';

    htmlRows += '<div class="mileage_css_'+seqs+'  mt-2" style="display:none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Enter KM" placeholder="0.00">';
    htmlRows += '<label for="" class="mb-1">KM <span class="text-danger">*</span><span id="amount_popup_span_'+seqs+'_mileage" class="text-primary f-10"><?php echo "Rs. ".customeummarmonyformate($sub_details['rate']);?></span></label>';
    htmlRows += '<input type="hidden" name="rate_mileage[]" class="form-control text-right " value="<?php echo $sub_details['rate'];?>"  id="amount_popup__2"+seqs+'_mileage" >';
    htmlRows += '<input aria-label="Qty Mileage[]" type="text" name="qty_mileage[]"  onblur="calucateamount('+seqs+')" class="form-control text-right decimalnumberallowe mileage_req"  id="qty_popup__2"+seqs+'_mileage" value="">';
    htmlRows += '</div>';

    htmlRows += '<div class="Perdiem_css_'+seqs+'  mt-2" style="display:none" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Days" placeholder="0.00">';
    htmlRows += '<label for="" class="mb-1">Days <span class="text-danger">*</span><span id="amount_popup_span__2"+seqs+'_perd" class="text-primary f-10"><?php echo "Rs. ".customeummarmonyformate($sub_details['rate']);?></span></label>';
    htmlRows += '<input type="hidden" name="rate_perd[]"  class="form-control text-right" value="<?php echo $sub_details['rate'];?>"  id="amount_popup__3"+seqs+'_perd" >';
    htmlRows += '<input aria-label="Qty Perd[]" type="text" name="qty_perd[]"  onblur="calucateamount('+seqs+')" class="form-control text-right decimalnumberallowe Perdiem_req"  id="qty_popup__3"+seqs+'_perd" value="">';
    htmlRows += '</div>';
    htmlRows += '</td>';


    htmlRows += '<td>';
    htmlRows += '<input aria-label="Walmart" type="text" name="merchant_name[]" class="form-control" placeholder="Walmart" value="<?php echo $sub_details['merchant_name']; ?>">';
    htmlRows += '<label for=""  class="mt-2">Tax </label><input type="text"  class="form-control text-right rowamt_mileage tax_row decimalnumberallowe" onblur="calucateamount('+seqs+')" value="0" name="tax[]" id="tax_popup_'+seqs+'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Enter Tax" placeholder="0.00">';
    htmlRows += '</td>';
    htmlRows += '<td>';
    htmlRows += '<input aria-label="Bill No[]" type="text" name="bill_no[]" class="form-control" value="">';
    htmlRows += '<label for="" class="mt-2">Total</label><input type="text"  class="form-control text-right rowamt_mileage" value="" name="amount[]" id="total_popup_'+seqs+'" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Total Expense" placeholder="0.00" readonly>';
    htmlRows += '</td>';

    htmlRows += '<td>';
    htmlRows += '<select aria-label="Payment Mode[]" class="form-control js-example-basic-single" name="payment_mode[]"><option value="">Select Any One</option>';
        <?php foreach($Payment_mode  as  $Paymentkey => $Payment_mode112){ ?>
            htmlRows += '<option value="<?php echo $Paymentkey; ?>" <?php echo $sub_details['payment_mode']==$Paymentkey?"selected":"" ;?>><?php echo $Payment_mode112; ?></option>';
        <?php } ?>
    htmlRows += '</select>';
    htmlRows += '<div class="mt-2">';
    htmlRows += '<label class="upload-btn cursor-pointer m-t-30">';
    htmlRows += '<i class="fa fa-upload"></i>';
    htmlRows += '<input aria-label="Bill File[]" type="file" accept="image


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
</script>
<div class="card">
      <?php
      if($emp_expenses['current_status'] !=""){
        $workflowsteps = getWorkflowStatus('emp_expenses',$emp_expenses['id'],$emp_expenses['current_status']);
        echo $workflowsteps['current_status_ribbon'];
    }
        ?>
    <div class="card-header">
        <h5>Expenses</h5>
        <div class="card-header-right table-card-header m-r-50">
            <?php
                echo $workflowsteps['workflow_btn'];
            ?>
            <?php
                $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
                if(in_array($emp_expenses['current_status'], (array)$check_array)){
                ?>
                  <?php
                }else{
                    $disable = "disabled";
                    $formdisable = "event_disabled";
                }

            ?>
            <a href="index.php?option=COM_VIEW_EMP_EXPENSES&Rid=<?php echo $_GET['Rid']; ?>" class="text-primary" target="_blank"><i class="ti ti-eye text-primary"></i> Report</a>
            <button type="button" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i> Back </button>
        </div>
    </div>
    <!-- m-r-50 m-l-50  -->
    <div class="card-body <?php echo $formdisable; ?>">
        <input type="hidden" id="mileageslistson" value='<?php echo json_encode($mileageslist); ?>'>

       <input type="hidden" id="perdiamarrayjson" value='<?php echo json_encode($perdiamarray); ?>'>

       <input type="hidden" id="catgeoryarrayjson" value='<?php echo json_encode($datacat); ?>'>
       <form method="post"  enctype="multipart/form-data" id="expesne_form">
        <input type="hidden" name="Rid" value='<?php echo $rid; ?>'>

        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <span class="form-label d-block">Trip Name</span>
                    <select aria-label="Trip" class="form-select js-example-basic-single" name="trip_id" id="trip_id" onchange="selectTrips()">
                        <option value="">Select Any One</option>
                        <?php foreach($trips as $trp){ ?>
                        <option value="<?php echo $trp['id'];?>" data-id="<?php echo $trp['trip_name']."||".$trp['transaction_no']."||".fromDate($trp['transaction_date'])."||".customeNumberFormate($trp['budget_amount']); ?>"   <?php echo (($_GET['TRIPID'] !="" && $_GET['TRIPID'] == $trp['id']) || $emp_expenses['trip_id']==$trp['id'])?"selected":"";?> ><?php echo $trp['transaction_no']." (".$trp['trip_name'].")"; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-lg-2">
                <span class="form-label">Transaction Date</span>
                <input aria-label="Trans Expense Date" class="form-control datepicker" type="text" name="trans_expense_date" value="<?php echo fromDate($emp_expenses['expense_date']);?>">
            </div>
            <div class="col-lg-2">
                <span class="form-label">Transaction Number</span>
                <input aria-label="Expense Code" class="form-control" type="text" name="expense_code" value="<?php echo $emp_expenses['expense_code'];?>" readonly>
            </div>


            <div class="col-lg-2">
                <span class="form-label text-right">Amount</span>
                <input aria-label="Parent Amount" class="form-control text-right" type="text" name="parent_amount" id="parent_amount" value="<?php echo $emp_expenses['amount'];?>" readonly>
            </div>
            <div class="col-lg-1">
                <span class="form-label text-right">Tax</span>
                <input aria-label="Parent Tax Amount" class="form-control text-right" type="text" name="parent_tax_amount" id="parent_tax_amount" value="<?php echo $emp_expenses['tax_amount'];?>" readonly>
            </div>
            <div class="col-lg-2">
                <span class="form-label text-right">Total</span>
                <input aria-label="Parent Total Amount" class="form-control text-right" type="text" name="parent_total_amount" id="parent_total_amount" value="<?php echo $emp_expenses['total_amount'];?>" readonly>
            </div>
            <div class="col-lg-8 trip_details" style="display: none;">
                <div class=" mt-3">
                    <table class="table table-hover table-sm">
                        <tr>
                            <th scope="col">Trip Name</th>
                            <td id="trip_name_id"></td>
                            <th scope="col">Transaction Number</th>
                            <td id="transaction_no_id"></td>
                        </tr>
                        <tr>
                            <th scope="col">Transaction Date</th>
                            <td id="transaction_date_id"></td>
                            <th scope="col">Budget</th>
                            <td id="budget_amount_id"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3" id="trip_details">
                <?php
                include_once 'custom_insert_emp_expense_ajax.php'; ?>
            </div>
            <div class="col-lg-9">
                <div class="table-responsive mt-5" style="">
                    <table class="table table-hover" id="expenseTable">
                        <thead style="">
                            <tr style="background-color: #eeeeee;">
                                <th scope="col" style="width: 38%;">Expense Type <span class="text-danger">*</span></th>
                                <th scope="col" style="width: 15%;">Purpose <span class="text-danger">*</span></th>
                                <th scope="col" style="width: 15%;">Merchant Name</th>
                                <th scope="col" style="width: 15%;">Bill Number</th>
                                <th scope="col" style="width: 15%;">Payment Mode</th>
                                <th scope="col" class="text-end" style="width: 2%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if(count((array)$emp_expenses_details) == 0){
                                $emp_expenses_details[] = array();
                            }
                            $seqs = 1;
                            foreach($emp_expenses_details as $sub_details){
                                $expens_css = $Perdiem_css = $mileage_css = "";
                                if($sub_details['expense_type']=='1'){
                                     $expens_css = $Perdiem_css = "display:none";
                                }elseif($sub_details['expense_type']=='2'){
                                    $expens_css = $mileage_css = "display:none";
                                }else{
                                     $Perdiem_css = $mileage_css = "display:none";
                                }

                                ?>
                            <tr class="expenseRow_<?php echo $seqs; ?>">
                                <td>
                                    <input type="hidden" name="row_sequince[]"  class="row_sequince" value="<?php echo $seqs; ?>">
                                    <input type="hidden" name="emp_expenses_detail_id[]"  value="<?php echo $sub_details['id']; ?>">

                                    <div class="d-block">
                                        <div class="form-radio">
                                          <?php
                                          $sub_details['expense_type'] = (isset($sub_details['expense_type']) && $sub_details['expense_type'] != "") ? $sub_details['expense_type'] : '0';
                                          foreach($expense_typelist as $expense_typelkey => $expense_typel){ ?>
                                            <div class="radio radio-outline radio-inline" onclick="checkFuntion('<?php echo $seqs; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                                <label class="form-label">
                                                    <input aria-label="Radio option" type="radio"  <?php echo ($sub_details['expense_type']==$expense_typelkey)?"checked":""; ?> name="expense_type_<?php echo $seqs; ?>" id="gender-1_2" value="<?php echo $expense_typelkey; ?>" >
                                                    <i class="helper"></i><?php echo $expense_typel; ?>
                                                </label>
                                            </div>
                                          <?php } ?>
                                        </div>
                                    </div>
                                    <div class="row  m-t-15">
                                        <div class=" col-lg-5" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Expense Date">
                                            <label for="">Date of Spend <span class="text-danger">*</span></label>
                                            <input aria-label="Expense Date[]" type="text" name="expense_date[]" class="form-control datepicker dateexpens expens_req mileage_req Perdiem_req" onchange="changedateexpense(this.value,'<?php echo $seqs; ?>')" placeholder="" value="<?php echo fromDate($sub_details['expense_date']); ?>" required>
                                        </div>
                                        <div class="col-lg-7 expens_css_<?php echo $seqs; ?> w-full" style="<?php echo $expens_css; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Category">
                                            <label for="" class="d-block">Category <span class="text-danger">*</span></label>
                                            <select aria-label="Select option" class="form-control form-select js-example-basic-single expens_req" id="select_category_exp_<?php echo $seqs; ?>" name="category_exp[]"  >
                                               <option value="">Select Any One</option>
                                                <?php foreach($datacat as $dataca){

                                                    if($dataca['parent_category_id'] == '0'){
                                                    ?>
                                                       <optgroup label="<?php echo $dataca['category_name']; ?>">
                                                    <?php }else{ ?>

                                                <option <?php echo $sub_details['category_id']==$dataca['category_id'] ? "selected":"" ;?> value="<?php echo $dataca['category_id']; ?>"><?php echo $dataca['category_name']; ?></option>
                                                <?php } ?>
                                                if($dataca['parent_category_id'] == '0'){
                                                    ?>
                                                       </optgroup>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-7 mileage_css_<?php echo $seqs; ?> w-full" style="<?php echo $mileage_css; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Mileage">
                                             <label for="" class="d-block">Mileage <span class="text-danger">*</span></label>
                                            <select aria-label="Select option" class="form-control form-select js-example-basic-single mileage_req" id="select_category_mileage_<?php echo $seqs; ?>" name="category_mileage[]" onchange="itemChange(this.options[this.selectedIndex].text,this.value,'<?php echo $seqs; ?>')" >
                                                   <option value="">Select Any One</option>
                                                   <?php foreach($mileageslist as $mileageslists){ ?>
                                                    <option value="<?php echo $mileageslists['id']; ?>" <?php echo $sub_details['category_id']==$mileageslists['id'] ? "selected":"" ;?>><?php echo $vehicle_types[$mileageslists['name']]." (".$mileageslists['amount'].")"; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-7 Perdiem_css_<?php echo $seqs; ?> w-full" style="<?php echo $Perdiem_css; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Select Perdiem">
                                            <label for="" class="d-block">Per Diem <span class="text-danger">*</span></label>
                                             <select aria-label="Select option" class="form-control form-select js-example-basic-single Perdiem_req" id="select_category_perd_<?php echo $seqs; ?>" name="category_perd[]" onchange="itemChange(this.options[this.selectedIndex].text,this.value,'<?php echo $seqs; ?>')"  >
                                                    <option value="">Select Any One</option>
                                                <?php foreach($perdiamarray as $perdieam){ ?>
                                                    <option value="<?php echo $perdieam['id']; ?>" <?php echo $sub_details['category_id']==$perdieam['id'] ? "selected":"" ;?>><?php echo $countries[$perdieam['name']]." (".$perdieam['amount'].")"; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="m-t-15 ">
                                        <label for="" class="d-block">Trip Slot</label>
                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single trip_transaction_table" id="select_trip_transaction_table_<?php echo $seqs; ?>" name="trip_transaction_table[]"  >
                                            <option value="">Select Any One</option>
                                           <?php  if(count((array)$local_trips) !=0){ ?>
                                            <optgroup label="Journey">
                                                <?php foreach($local_trips as $trplocal){   $selectedtrpplan = "";
                                                    if($sub_details['trip_transaction_table'] == "domestic_round_trips" && $sub_details['trip_transaction_id'] == $trplocal['id']){
                                                        $selectedtrpplan = "selected";
                                                    }
                                                    ?>
                                                <option value="domestic_round_trips||<?php echo $trplocal['id']; ?>"  <?php echo $selectedtrpplan; ?>><?php echo $cities[$trplocal['from_location']]['city_name']." (".$cities[$trplocal['from_location']]['country_code'].") - ".fromDate($trplocal['trip_date']); ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <?php } ?>
                                            <?php if(count((array)$trip_hotels) !=0){ ?>
                                            <optgroup label="Hotel">
                                                <?php foreach($trip_hotels as $trphotel){   $selectedtrpplan = "";
                                                    if($sub_details['trip_transaction_table'] == "trip_hotels" && $sub_details['trip_transaction_id'] == $trphotel['id']){
                                                        $selectedtrpplan = "selected";
                                                    }?>
                                                <option value="trip_hotels||<?php echo $trphotel['id']; ?>" <?php echo $selectedtrpplan; ?>><?php echo $cities[$trphotel['location']]['city_name']." (".$cities[$trphotel['location']]['country_code'].") - (IN :".$trphotel['check_in']." - Out :".$trphotel['check_out'].")"; ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <?php } ?>
                                            <?php  if(count((array)$local_trips) !=0){ ?>
                                            <optgroup label="Local">
                                                  <?php foreach($local_trips as $trplocal){  $selectedtrpplan = "";
                                                    if($sub_details['trip_transaction_table'] == "local_trips" && $sub_details['trip_transaction_id'] == $trplocal['id']){
                                                        $selectedtrpplan = "selected";
                                                    }?>
                                                <option value="local_trips||<?php echo $trplocal['id']; ?>" <?php echo $selectedtrpplan; ?>><?php echo $cities[$trplocal['from_location']]['city_name']." (".$cities[$trplocal['from_location']]['country_code'].") -".fromDate($trplocal['trip_date']); ?></option>
                                                <?php } ?>
                                            </optgroup>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <input aria-label="Enter purpose" type="text" name="description[]" class="form-control expens_req mileage_req Perdiem_req" placeholder="Enter purpose" value="<?php echo $sub_details['description']; ?>" id="description_<?php echo $seqs; ?>">

                                    <div class="expens_css_<?php echo $seqs; ?> mt-2" style="<?php echo $expens_css; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Enter Amount" placeholder="0.00">
                                        <label for="" class="mb-1">Amount <span class="text-danger">*</span></label>
                                        <input aria-label="Rate Exp[]" type="text" name="rate_exp[]" class="form-control text-right rate_exp_row decimalnumberallowe expens_req" value="<?php echo $sub_details['rate'];?>" onblur="calucateamount('<?php echo $seqs; ?>')" id="amount_popup_<?php echo $seqs; ?>_exp" >
                                        <input type="hidden" name="qty_exp[]" value="1"  class="form-control text-right " readonly id="qty_popup_<?php echo $seqs; ?>_exp" >
                                    </div>
                                    <div class="mileage_css_<?php echo $seqs; ?>  mt-2" style="<?php echo $mileage_css; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Enter KM" placeholder="0.00">
                                        <label for="" class="mb-1">KM <span class="text-danger">*</span><span id="amount_popup_span_<?php echo $seqs; ?>_mileage" class="text-primary f-10"><?php echo "Rs. ".customeummarmonyformate($sub_details['rate']);?></span></label>
                                        <input type="hidden" name="rate_mileage[]" class="form-control text-right " value="<?php echo $sub_details['rate'];?>"  id="amount_popup_<?php echo $seqs; ?>_mileage" >
                                        <input aria-label="Qty Mileage[]" type="text" name="qty_mileage[]"  onblur="calucateamount('<?php echo $seqs; ?>')" class="form-control text-right decimalnumberallowe mileage_req"  id="qty_popup_<?php echo $seqs; ?>_mileage" value="<?php echo $sub_details['qty'];?>">
                                    </div>
                                    <div class="Perdiem_css_<?php echo $seqs; ?>  mt-2" style="<?php echo $Perdiem_css; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Days" placeholder="0.00">
                                        <label for="" class="mb-1">Days <span class="text-danger">*</span><span id="amount_popup_span_<?php echo $seqs; ?>_perd" class="text-primary f-10"><?php echo "Rs. ".customeummarmonyformate($sub_details['rate']);?></span></label>
                                        <input type="hidden" name="rate_perd[]"  class="form-control text-right" value="<?php echo $sub_details['rate'];?>"  id="amount_popup_<?php echo $seqs; ?>_perd" >
                                        <input aria-label="Qty Perd[]" type="text" name="qty_perd[]"  onblur="calucateamount('<?php echo $seqs; ?>')" class="form-control text-right decimalnumberallowe Perdiem_req"  id="qty_popup_<?php echo $seqs; ?>_perd" value="<?php echo $sub_details['qty'];?>">
                                    </div>
                                </td>
                                <td>
                                    <input aria-label="Walmart" type="text" name="merchant_name[]" class="form-control" placeholder="Walmart" value="<?php echo $sub_details['merchant_name']; ?>">
                                    <label for=""  class="mt-2">Tax </label>
                                    <input aria-label="Input field" type="text"  class="form-control text-right rowamt_mileage tax_row decimalnumberallowe" onblur="calucateamount('<?php echo $seqs; ?>')" value="<?php echo $sub_details['tax_amount']!=""?$sub_details['tax_amount']:"0";?>" name="tax[]" id="tax_popup_<?php echo $seqs; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Please Enter Tax" placeholder="0.00">
                                </td>

                                <td>
                                    <input aria-label="Bill No[]" type="text" name="bill_no[]" class="form-control" value="<?php echo $sub_details['bill_no']; ?>">
                                    <label for="" class="mt-2">Total</label>
                                    <input aria-label="Input field" type="text"  class="form-control text-right rowamt_mileage event_disabled" value="<?php echo $sub_details['expense_amount'];?>" name="amount[]" id="total_popup_<?php echo $seqs; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Total Expense" placeholder="0.00"  required>
                                </td>
                                <td>
                                    <select aria-label="Payment Mode[]" class="form-control js-example-basic-single" name="payment_mode[]">
                                          <option value="">Select Any One</option>
                                        <?php foreach($Payment_mode  as  $Paymentkey => $Payment_mode11){ ?>
                                            <option value="<?php echo $Paymentkey; ?>" <?php echo $sub_details['payment_mode']==$Paymentkey?"selected":"" ;?>><?php echo $Payment_mode11; ?></option>
                                      <?php } ?>
                                    </select>
                                    <div class=" m-t-30">
                                        <label class="upload-btn cursor-pointer">
                                            <i class="fa fa-upload"></i>
                                            <input aria-label="Bill File[]" type="file" accept="image/*" name="bill_file[]" style="display:none" onchange="previewImage(this,'<?php echo $seqs; ?>')">
                                        </label>
                                        <a href="<?php echo $File_Uploads_Path.$sub_details['bill_file']; ?>" style="pointer-events: auto;font-weight: bold;opacity: 1;" download>
                                        <img src="<?php echo $File_Uploads_Path.$sub_details['bill_file']; ?>" alt="" class="preview_<?php echo $seqs; ?>" style="width:30px; height:30px; <?php echo $sub_details['bill_file']!=""?"":"display:none"?>"></a>
                                        <input type="hidden" name="old_bill_file[]" class="form-control" value="<?php echo $sub_details['bill_file']; ?>">
                                    </div>
                                </td>
                                <td style="text-align: right;">
                                    <i class="fa fa-trash text-danger" onclick="deleteExpenseRow('<?php echo $seqs; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                </td>
                            </tr>
                            <?php $seqs++;
} ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="12">
                                    <button type="button" class="btn btn-primary" id="add_expense" onclick="addExpenseRow()">
                                        <i class="fa fa-plus"></i> Add More
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="mt-2">
                    <input type="hidden" name="submit_value" value="submit_expense">
                    <button type="button" class="btn btn-primary"  <?php echo $disable; ?> onclick="submitempexpenseform()">Submit</button>
                </div>
            </div>
        </div>
    </form>
    </div>
</div>

<style>
    .preview{border-radius: 100px; margin-left:5px;}
</style>

<script>
function submitempexpenseform(){
    $(".expens_req").removeClass('bg-danger');
    $(".mileage_req").removeClass('bg-danger');
    $(".Perdiem_req").removeClass('bg-danger');
    var msg = "Please Select Or Enter ";
   var retunval = 0;
   $('.row_sequince').each(function(){
        var seq = $(this).val();
        var expense_type = $('input[name="expense_type_'+seq+'"]:checked').val();
        if(expense_type=='1'){
            var categ  = "#select_category_mileage_"+seq;
            var qty  = "#qty_popup_"+seq+"_mileage";
            var rate = "#amount_popup_"+seq+"_mileage";
        }else if(expense_type=='2'){
            var categ  = "#select_category_perd_"+seq;
            var qty  = "#qty_popup_"+seq+"_perd";
            var rate = "#amount_popup_"+seq+"_perd";
        }else{
            var categ  = "#select_category_exp_"+seq;
            var qty  = "#qty_popup_"+seq+"_exp";
            var rate = "#amount_popup_"+seq+"_exp";
        }

        if($(categ).val() ==""){
            console.log("1 - "+categ);
            $(categ).next(".select2").find(".select2-selection").css("border", "4px solid red");
            retunval = 1;
            msg +="Category, ";
        }else{
           $(categ).next(".select2").find(".select2-selection").css("border", "none");
        }
        if($(qty).val() ==""){
             console.log("2 - "+qty);
            $(qty).addClass('bg-danger');
            retunval = 1;
            msg +="Quantity, ";
        }
        if($("#description_"+seq).val() ==""){
            $("#description_"+seq).addClass('bg-danger');
            retunval = 1;
            msg +="Purpose, ";
        }

        if($(rate).val() ==""){
              console.log("3 - "+rate);
            $(rate).addClass('bg-danger');
            retunval = 1;
            msg +="Rate, ";
        }
    });
    if(retunval == 0){
        $("#expesne_form").submit();
    }else{
        swal("<?php echo ucwords($_SESSION['config']['name']); ?>", msg, "error");
    }
}

function addRow() {
    let tbody = document.querySelector("#expenseTable tbody");
    let row = document.querySelector(".expenseRow");
    let clone = row.cloneNode(true);

    // reset inputs
    clone.querySelectorAll("input").forEach(inp => {
        if (inp.type !== "radio") inp.value = "";
        if (inp.type === "file") inp.value = "";
    });
    clone.querySelectorAll("img.preview").forEach(img => {
        img.style.display = "none";
        img.src = "";
    });

    tbody.appendChild(clone); // add at bottom
}

function deleteRow(el) {
    let row = el.closest("tr");
    let tbody = document.querySelector("#expenseTable tbody");
    if (tbody.querySelectorAll(".expenseRow").length > 1) {
        row.remove();
    } else {
        alert("At least one row is required.");
    }
}

function previewImage(input,seq) {
    let file = input.files[0];
    if (file) {
        let reader = new FileReader();
        reader.onload = function (e) {
            let img = input.closest("td").querySelector(".preview_"+seq);
            img.src = e.target.result;
            img.style.display = "inline-block";
        }
        reader.readAsDataURL(file);
    }
}
</script>
