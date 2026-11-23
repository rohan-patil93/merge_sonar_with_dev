<?php
defined('KEY_CURRENT_STATUS') || define('KEY_CURRENT_STATUS', 'current_status');
defined('LBL_INPUT_FIELD') || define('LBL_INPUT_FIELD', 'Input field');
defined('KEY_ORIGINATOR_STATE_ID') || define('KEY_ORIGINATOR_STATE_ID', 'originator_state_id');
defined('KEY_SUPPLIER_STATE_ID') || define('KEY_SUPPLIER_STATE_ID', 'supplier_state_id');
$_SESSION['apply_js']['multiselest'] = 1;

if (isset($_POST['submitrentalinvoice'])) {
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API . "?mode=updateutilityrentalinvoices&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response1, 1);
    $response = $response1['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_RENTAL_INVOICES");
        }
        ?>
          <script type="text/javascript">
            document.onreadystatechange = function(){
              if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
              }
            }
          </script>
          <?php
    }else{
        if($response['status'] == 'fail'){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
              $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
          }else{
            $error_message = $response['message'];
          }
    }
}




$rid1 =$_GET['Rid'];
$rid2 =  encodeId($rid1);
$rid3 = urldecode($rid2);
$rrid = explode(":", $rid3);
$transaction_id = $rrid['1'];
$cusurl = API."?mode=getutilityrentalinvoices&transaction_id=" . $transaction_id;
$apicall = callAPI($cusurl);
$result = json_decode($apicall, 1);
$transaction_data = $result['utility_rental_invoices'];
$utility_rental_invoice_items = $result['utility_rental_invoice_items'];
$utility_rental_invoice_item_taxes = $result['utility_rental_invoice_item_taxes'];
$utilitytaxtable = array();
foreach($utility_rental_invoice_item_taxes as $utrent){
    $utilitytaxtable[$utrent['transaction_item_id']] = $utrent;
}
$party_additional_details = json_decode($transaction_data['party_additional_details'],1);
$owner_additional_details = json_decode($transaction_data['owner_additional_details'],1);
$utility_uom = $result['utility_uom'];
$states= $result['states'];
$operation_symbol = array('+','/','*','%','=');


$check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,'');
if(in_array($transaction_data[KEY_CURRENT_STATUS], (array)$check_array) || ($transaction_data[KEY_CURRENT_STATUS] == ST_CALLBACK && $transaction_data['originator'] == $_SESSION['userid'])){
        $disable = "" ;
}else{
    $disable = "event_disabled" ;
}

?>

<script>
<?php if($transaction_data[KEY_CURRENT_STATUS] != ST_APPROVED){ ?>
window.addEventListener("load", function() {
    $('.allitemsid').each(function(index) {
        var item_id = $(this).val();
        var parent_id = $('.allparenid').eq(index).val();
        console.log(item_id,'-------------------------',parent_id);
       rentalInvoicePopupCalculattion(item_id, parent_id);
    });
});
<?php } ?>


function rentalInvoicePopupCalculattion(ruid,parent_id){

    var originator_state_id = $("#originator_state_id").val();
    var supplier_state_id = $("#supplier_state_id").val();
    var rentinvpopup_qty = $("#rentinvpopup_qty_"+ruid).val();
    var rentinvpopup_rate = $("#rentinvpopup_rate_"+ruid).val();
    var rentinvpopup_condition = $("#rentinvpopup_condition_"+ruid).val();
    var calculate = 0;
    if(rentinvpopup_qty !="" && rentinvpopup_condition !=""  && rentinvpopup_rate !=""){
        if(rentinvpopup_condition == "="){
            calculate = parseFloat(rentinvpopup_rate);
        }else if(rentinvpopup_condition == "%"){
            calculate = (parseFloat(rentinvpopup_qty) / 100) * parseFloat(rentinvpopup_rate);
        }else{
            calculate = parseFloat(rentinvpopup_qty)+rentinvpopup_condition+parseFloat(rentinvpopup_rate);
        }
    }
    var returnvalue = Math.round(eval(calculate));
    $("#rentinvpopup_rent_"+ruid).val(returnvalue.toFixed(2));
    var totalamt = 0;
    $(".rentinvpopup_child_rent_"+parent_id).each(function() {
        totalamt = parseFloat(totalamt)+parseFloat(this.value);
        //console.log(this.value);
    });
    $("#rentinvpopup_parent_rent_"+parent_id).val(totalamt.toFixed(2));
    $("#rentinvpopup_parent_rent_item_price_"+parent_id).val(totalamt.toFixed(2));

    var total_percentage = percentage = 0

    if ($('#utility_gst_'+parent_id).is(':checked')){
        $("#utility_gst_utility_text_"+parent_id).text(totalamt.toFixed(2));
        $("#utility_gst_utility_"+parent_id).val(totalamt.toFixed(2));

        var percentage = $('#utility_gst_percentage_'+parent_id).val();
        total_percentage = ((totalamt * percentage) / 100);
        totalamt = totalamt+total_percentage;

       // $("#utility_gst_total_tax_text_"+parent_id).text(total_percentage.toFixed(2)+" ("+percentage+"%)");

       $("#utility_gst_total_tax_text_"+parent_id).text(total_percentage.toFixed(2));
        $("#utility_gst_total_tax_"+parent_id).val(total_percentage.toFixed(2));
        console.log('gst -',total_percentage);
        $("#tax_html_table").show();
    } else {
        $("#utility_gst_utility_text_"+parent_id).text(totalamt.toFixed(2));
        $("#utility_gst_utility_"+parent_id).val(totalamt.toFixed(2));
        $("#utility_gst_total_tax_text_"+parent_id).text("0");
        $("#utility_gst_total_tax_"+parent_id).val(0);
        $("#tax_html_table").hide();
        console.log('gst 8888-',0);
    }
    $("#rentinvpopup_parent_rent_with_tax_"+parent_id).val(totalamt.toFixed(2));

    calculatetotals();

}


function calculatetotals(){

    var originator_state_id = $("#originator_state_id").val();
    var supplier_state_id = $("#supplier_state_id").val();
    var totalallparentamount = 0;
    $(".all_parent_amount_with_tax").each(function() {
        totalallparentamount = parseFloat(totalallparentamount)+parseFloat(this.value);
        console.log(this.value);
    });
    grandTotal = roundingOffVal(totalallparentamount);
    $(".total_amount_display").text(grandTotal.toFixed(2));
    $(".total_amount_input").val(grandTotal.toFixed(2));

    var taxtotal = 0;
    $(".utility_gst_total_tax").each(function() {
        taxtotal = parseFloat(taxtotal)+parseFloat(this.value);
    });
    $("#tax_amount_display_total").text(taxtotal.toFixed(2));
    $("#tax_amount_input").val(taxtotal.toFixed(2));

    var withouttaxtotal = 0;
    $(".utility_gst_without_tax").each(function() {
        withouttaxtotal = parseFloat(withouttaxtotal)+parseFloat(this.value);
    });
    $("#totalallparentamount_text").text(withouttaxtotal.toFixed(2));
    $("#totalallparentamount_input").val(withouttaxtotal.toFixed(2));
    var html = ``;
    if(originator_state_id == supplier_state_id){
         html = `<tr>
                        <th class="text-start p-2 border">
                            <span>
                                <?= htmlspecialchars(__('CGST @'), ENT_QUOTES, 'UTF-8'); ?>
                                <span
                                    class="tax_value">9</span>
                                <?= htmlspecialchars(__('% on'), ENT_QUOTES, 'UTF-8'); ?> <br>
                                <span class="sub_amount_display">
                                    `+(withouttaxtotal.toFixed(2))+`
                                </span>
                            </span>
                        </th>
                        <th class="p-2 text-end border">
                            <span class="tax_amount_display">
                                `+(taxtotal.toFixed(2)/2)+`
                            </span>
                        </th>
                    </tr>
                    <tr>
                        <th class="text-start p-2 border">
                            <span>
                                <?= htmlspecialchars(__('SGST @'), ENT_QUOTES, 'UTF-8'); ?>
                                <span
                                    class="tax_value">9</span>
                                <?= htmlspecialchars(__('% on'), ENT_QUOTES, 'UTF-8'); ?> <br>
                                <span class="sub_amount_display">
                                    `+(withouttaxtotal.toFixed(2))+`
                                </span>
                            </span>
                        </th>
                        <th class="p-2 text-end border">
                            <span class="tax_amount_display">
                                 `+(taxtotal.toFixed(2)/2)+`
                            </span>
                        </th>
                    </tr>`;
    }else{
        html = `<tr>
                <th class="text-start p-2 border">
                    <span>
                        <?= htmlspecialchars(__('IGST @'), ENT_QUOTES, 'UTF-8'); ?>
                        <span
                            class="tax_value">18</span>
                        <?= htmlspecialchars(__('% on'), ENT_QUOTES, 'UTF-8'); ?> <br>
                        <span class="sub_amount_display">
                           `+(withouttaxtotal.toFixed(2))+`
                        </span>
                    </span>
                </th>
                <th class="p-2 text-end border">
                    <span class="tax_amount_display">
                        `+(taxtotal.toFixed(2))+`
                    </span>
                </th>
            </tr>`;
    }

    $("#tax_html_table").html(html);
}

function roundingOffVal(grandtotal){
    var rounding_off = Math.round(parseFloat(grandtotal)) - parseFloat(grandtotal);
    if(isNaN(grandtotal)){
        $(".rounding_off").val(0)
    }else{
        grandtotal = parseFloat(grandtotal) + parseFloat(rounding_off);
        document.querySelector("#rounding_off").textContent = rounding_off.toFixed(2);
        $(".rounding_off").val(rounding_off.toFixed(2))
    }
    return grandtotal;
}

    function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    //var validate = checkMandatoryArray();
    //if(validate == 0){
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
    //}
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    var validate = checkMandatoryArray();
    if(validate == 0){
        swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: <?php echo json_encode(__('Please note the status of the document will change to').' ', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>+new_status_name,
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: <?php echo json_encode(__('Yes'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                cancelButtonText: <?php echo json_encode(__('No'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
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
                        //console.log(res);
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
                                title: <?php echo json_encode(__('Oops...'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                text: res.transaction_update.message,
                                type: "error",
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: <?php echo json_encode(__('Ok'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>,
                                confirmButtonClass: "btn-danger",
                                closeOnCancel: false
                            });
                        }
                    },
                    error:function (){}
                });
            });
    }
}

function cancelInvoices(transaction_table,transaction_id){
    jQuery.ajax({
        url: "views/custom_insert_invoice_cancellations_popup.php",
        data:'transaction_table='+transaction_table+'&transaction_id='+transaction_id,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
            $('#kt_modal_KTDatatable_remote').modal("show");
            $( ".datepicker" ).datepicker({
                showOn: "both",
                buttonText: "<i class='ti-calendar text-dark f-20 f-w-800 m-r-10'></i>",
                dateFormat: date_format,
                minDate: 0,
                maxDate: "+30D",
                setDate: new Date(),
                changeMonth: true,
                changeYear: true,
            });
            var elemdefault = document.querySelector('.js-primary-sm-popup');
            var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff',size: 'small' });

        },
        error:function (){}
    });
}
</script>

<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
<?php } ?>
<div class="card">
     <?php if($transaction_data['cancel_invoice'] =="Y"){ ?>
<div id="dynamiccanceldiv"><span><?= __('Cancelled Invoice'); ?></span></div>
<?php } ?>
     <?php
        $workflowsteps = getWorkflowStatus('utility_rental_invoices',$transaction_data['id'],$transaction_data[KEY_CURRENT_STATUS]);
            ?>
    <div class="card-header table-card-header">
        <h5> <?= __('Rental Invoice'); ?></h5>
        <div class="card-header-right table-card-header ">
            <?php
                if($transaction_data[KEY_CURRENT_STATUS] == ST_APPROVED && $transaction_data['cancel_invoice'] !="Y" && $transaction_data['originator'] ==$_SESSION['userid']){ ?>
                        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="cancelInvoices('utility_rental_invoices','<?php echo $transaction_id; ?>')">
                            <i class="text-white fa fa-times-circle"></i> &nbsp;<?= __('Cancel Invoice'); ?>
                        </button>
                <?php  }
                    echo $workflowsteps['workflow_btn'];

                    $printurl = "index.php?option=COM_VIEW_RENTAL_INVOICES&rid=".$transaction_id."&table=utility_rental_invoices&title=".urlencode('Utility Invoices');
                     ?>
                 <a target="_blank" href="<?php echo $printurl; ?>" class="btn btn-clean kt-margin-r-10"><i class="ti-printer"></i> </a>

            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply"></i>
                <?= __('Back'); ?>
            </a>
        </div>
    </div>

    <div class="card-body">
        <?php echo $workflowsteps['current_status_ribbon']; ?>
        <form method="post" class="<?php echo $disable; ?>">
        <div class="row">
            <input type="hidden" name="rid" value="<?php echo $transaction_id; ?>">

            <div class="col-lg-3 form-group">
                <label for="transaction_number" class="inline-block mb-1 f-w-600"><?= __('Transaction Number'); ?></label>

                <input type="text" id="transaction_number" name="transaction_number" class="form-control" placeholder="<?= htmlspecialchars(__('Enter Transaction no.'), ENT_QUOTES, 'UTF-8'); ?>" value="<?= htmlspecialchars($transaction_data['transaction_number'] ?? '') ?>" readonly>
            </div>
            <div class="col-lg-3 form-group">
                <label for="transaction_date" class="inline-block mb-1 f-w-600"><?= __('Transaction Date'); ?></label>
                <input type="text" id="transaction_date" value="<?= htmlspecialchars(fromDate($transaction_data['transaction_date']) ?? '') ?>" name="transaction_date" class="form-control datepicker" placeholder="<?= htmlspecialchars(__('Transaction Date'), ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-lg-3 form-group">
                <label for="due_date" class="inline-block mb-1 f-w-600"><?= __('Due Date'); ?> </label>
                <input type="text" id="due_date" name="due_date" class="form-control datepicker" placeholder="<?= htmlspecialchars(__('Due Date'), ENT_QUOTES, 'UTF-8'); ?>" value="<?= htmlspecialchars(fromDate($transaction_data['due_date']) ?? '') ?>">
            </div>
            <div class="col-lg-3 form-group">
                <label for="originator" class="inline-block mb-1 f-w-600"><?= __('Originator'); ?></label>
                <input aria-label="<?= htmlspecialchars(__(LBL_INPUT_FIELD), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="<?= $_SESSION['users'][$transaction_data['originator']]; ?>"  readonly>
            </div>
            <div class="col-lg-3 form-group">
                <label for="parent_ref_id" class="inline-block mb-1 f-w-600"><?= __('Property Owner'); ?></label>
                 <input aria-label="<?= htmlspecialchars(__(LBL_INPUT_FIELD), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" value="<?php echo  $owner_additional_details['owner_company_name']; ?>"  readonly>

            </div>
            <div class="col-lg-3 form-group">
                <label for="ref_no" class="inline-block mb-1 f-w-600"><?= __('Refrence No.'); ?></label>
                <input type="text" id="ref_no" name="ref_no" class="form-control" placeholder="<?= htmlspecialchars(__('Refrence Number'), ENT_QUOTES, 'UTF-8'); ?>" value="<?= htmlspecialchars($transaction_data['ref_no'] ?? '') ?>">
            </div>
            <div class="col-lg-3 form-group">
                <label for="refrence_bill" class="inline-block mb-1 f-w-600"><?= __('Refrence Date'); ?></label>
                <input type="text" id="refrence_bill" name="ref_date" class="form-control datepicker" placeholder="<?= htmlspecialchars(__('Refrence Bill'), ENT_QUOTES, 'UTF-8'); ?>"  value="<?= htmlspecialchars(fromDate($transaction_data['ref_date']) ?? '') ?>">
            </div>
            <div class="col-lg-3 form-group">
                <label for="party" class="inline-block mb-1 f-w-600"><?= __('Party/Tenant'); ?></label>
                <input aria-label="<?= htmlspecialchars(__(LBL_INPUT_FIELD), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control"  value="<?= htmlspecialchars($transaction_data['party_name'] ?? '') ?>" readonly>
            </div>
            <div class="col-lg-3 form-group">
                <label for="party_email" class="inline-block mb-1 f-w-600"><?= __('Party Email'); ?></label>
                <input type="text" id="party_email" name="party_email" class="form-control" value="<?= htmlspecialchars($transaction_data['party_email'] ?? '') ?>"  readonly>
            </div>
            <div class="col-lg-3 form-group">
                <label for="party_phone" class="inline-block mb-1 f-w-600"><?= __('Party Contact Number'); ?></label>
                <input aria-label="<?= htmlspecialchars(__('Party phone'), ENT_QUOTES, 'UTF-8'); ?>" type="text" value="<?= htmlspecialchars($transaction_data['party_phone'] ?? '') ?>" name="party_phone" class="form-control"  readonly>
            </div>
            <div class="col-lg-3 form-group">
                <label for="party_address" class="inline-block mb-1 f-w-600"><?= __('Billing Address'); ?></label>
                <textarea name="party_address" disabled id="party_address" Class="form-control"><?= htmlspecialchars($transaction_data['party_address'] ?? '') ?> </textarea>
            </div>
            <div class="col-lg-3 form-group">
                <label for="party_address" class="inline-block mb-1 f-w-600"><?= __('Shiping Address'); ?></label>
                <textarea aria-label="<?= htmlspecialchars(__('Party ship adderss'), ENT_QUOTES, 'UTF-8'); ?>" name="party_ship_adderss" disabled id="party_ship_adderss" Class="form-control"><?= htmlspecialchars($transaction_data['party_ship_adderss'] ?? '') ?> </textarea>
            </div>

           
            <div class="col-lg-3 form-group">
                <label for="party_address" class="inline-block mb-1 f-w-600"><?= __('Billing State'); ?></label>
                <select aria-label="<?= htmlspecialchars(__('Originator state id'), ENT_QUOTES, 'UTF-8'); ?>" name="originator_state_id" id="originator_state_id" class="form-control js-example-basic-single" onchange="calculatetotals()">
                    <option value=""><?= __('Select Any One'); ?></option>
                    <?php foreach($states as $stn){ ?>
                        <option value="<?php echo $stn['id']; ?>" <?php echo $transaction_data[KEY_ORIGINATOR_STATE_ID] == $stn['id']?"SELECTED":"";?> ><?php echo $stn['state_name']; ?></option>
                    <?php } ?>
                </select>

            </div>
            <div class="col-lg-3 form-group">
                <label for="party_address" class="inline-block mb-1 f-w-600"><?= __('Party/Tenant State'); ?></label>
                <select aria-label="<?= htmlspecialchars(__('Supplier state id'), ENT_QUOTES, 'UTF-8'); ?>" name="supplier_state_id"  id="supplier_state_id"  class="form-control js-example-basic-single" onchange="calculatetotals()">
                    <option value=""><?= __('Select Any One'); ?></option>
                    <?php foreach($states as $stn){ ?>
                        <option value="<?php echo $stn['id']; ?>" <?php echo $transaction_data[KEY_SUPPLIER_STATE_ID] == $stn['id']?"SELECTED":"";?> ><?php echo $stn['state_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>


        <div class="row">
            <div class="col-lg-9">
        <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                    <thead>
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 15%;"><?= __('Utility'); ?></th>
                            <th style="width: 15%;"><?= __('Remark'); ?></th>
                            <th style="width: 5%;"><?= __('Tax'); ?></th>
                            <th style="width: 5%;"><?= __('UOM'); ?></th>
                            <th style="width: 10%;"><?= __('QTY'); ?></th>
                            <th style="width: 10%;"><?= __('Formula'); ?></th>
                            <th style="width: 10%;"><?= __('Rate'); ?></th>
                            <th style="width: 10%;" class="text-right" colspan="5"><?= __('Amount'); ?></th>

                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        $i=1;
                        foreach($utility_rental_invoice_items as $bd){
                            $utility_rental_invoice_items_id = $utilitytaxtable[$bd['item_id']];
                        ?>
                        <tr>
                            <input type="hidden" name="item_id[]" class="allitemsid" value="<?php echo $bd['item_id']; ?>">
                            <input type="hidden" name="utility_rental_invoice_items_id[]" value="<?php echo $bd['id']; ?>">
                            <input type="hidden" name="parent_id[]" class="allparenid" value="<?php echo $bd['parent_id']; ?>">
                            <td  class="p-1"><?php echo $bd['parent_id'] == 0?$i:"-"; ?></td>
                            <td class="p-1"><?php echo $bd['item_name']; ?></td>
                            <td class="p-1">
                                <textarea aria-label="<?= htmlspecialchars(__('Description'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" name="description[]"><?php echo $bd['description']; ?></textarea>
                            </td>
                            <td class="p-1">


                                <input type="hidden" name="utility_rental_invoice_item_taxes_id[]" value="<?php echo $utility_rental_invoice_items_id['id']; ?>">

                                <input type="hidden" name="tax_name[]" value="<?php echo $utility_rental_invoice_items_id['tax_name']!=""?$utility_rental_invoice_items_id['tax_name']:'997212'; ?>"  >

                                <input type="hidden" name="tax_id[]" value="<?php echo $utility_rental_invoice_items_id['tax_id']!=""?$utility_rental_invoice_items_id['tax_id']:'9015'; ?>"  >

                                <input type="hidden" name="utility_gst_switch[]" value="<?php echo $bd['item_id']; ?>">

                                <input type="hidden" name="utility_gst_percentage[]" value="<?php echo $utility_rental_invoice_items_id['tax_value']!=""?$utility_rental_invoice_items_id['tax_value']:18; ?>" id="utility_gst_percentage_<?php echo $bd['item_id']; ?>" >

                                <?php if($bd['parent_id'] == 0){
                                    $checkedswitch = ""; $onetimecheck = "0";
                                    if($bd['tax_amount'] !="" && $bd['tax_amount'] !="0"){
                                        $checkedswitch = "checked"; $onetimecheck = "1";
                                    }
                                    ?>
                                <input type="checkbox" class="js-switch" name="utility_gst[<?php echo $bd['item_id']; ?>]" value="Y" id="utility_gst_<?php echo $bd['item_id']; ?>" onchange="rentalInvoicePopupCalculattion('<?php echo $bd['item_id']; ?>','<?php echo $bd['item_id']; ?>')" <?php echo $checkedswitch; ?>>

                                <input type="hidden" name="utility_gst_total_tax[]" class="utility_gst_total_tax" value="<?php echo $bd['tax_amount']; ?>" id="utility_gst_total_tax_<?php echo $bd['item_id']; ?>" >
                                <input type="hidden" name="utility_gst_without_tax[]" value="<?php echo $bd['item_amount']; ?>" id="utility_gst_utility_<?php echo $bd['item_id']; ?>" class="utility_gst_without_tax">
                                <?php } ?>

                            </td>
                            <?php if($bd['parent_id'] != 0){ ?>
                            <td class="p-1">
                                <select aria-label="<?= htmlspecialchars(__('Purchase uom'), ENT_QUOTES, 'UTF-8'); ?>" name="purchase_uom[]"  class="form-control js-example-basic-single">
                                    <option value=""><?= __('N/A'); ?></option>
                                    <?php foreach($utility_uom as $utiuom){ ?>
                                        <option value="<?php echo __($utiuom['genc_detail_name']); ?>" <?php echo $bd['purchase_uom'] == $utiuom['genc_detail_name']?"SELECTED":"";?> ><?php echo $utiuom['genc_detail_code']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="p-1">
                                <input type="text" class="form-control" id="rentinvpopup_qty_<?php echo $bd['item_id']; ?>"  name="item_quantity[]" value="<?php echo $bd['item_quantity']!=""?round($bd['item_quantity'],2):0; ?>" onblur="rentalInvoicePopupCalculattion('<?php echo $bd['item_id']; ?>','<?php echo $bd['parent_id']; ?>')">
                            </td>
                            <td class="p-1">
                                <select name="operation_symbol[]"  class="form-control js-example-basic-single" id="rentinvpopup_condition_<?php echo $bd['item_id']; ?>"  onchange="rentalInvoicePopupCalculattion('<?php echo $bd['item_id']; ?>','<?php echo $bd['parent_id']; ?>')">
                                    <?php foreach($operation_symbol as $os){ ?>
                                        <option value="<?php echo $os; ?>" <?php echo $bd['operation_symbol'] == $os?"SELECTED":"";?> ><?php echo $os; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="p-1">
                                <input type="text" class="form-control" name="item_price[]" id="rentinvpopup_rate_<?php echo $bd['item_id']; ?>" value="<?php echo $bd['item_price']!=""?round($bd['item_price'],2):0; ?>" onblur="rentalInvoicePopupCalculattion('<?php echo $bd['item_id']; ?>','<?php echo $bd['parent_id']; ?>')">
                            </td>
                            <td class="p-1">
                                <input type="text" class="form-control rentinvpopup_child_rent_<?php echo  $bd['parent_id']; ?>" id="rentinvpopup_rent_<?php echo $bd['item_id']; ?>" name="total_amount[]" value="<?php echo $bd['total_amount']!=""?round($bd['total_amount'],2):0; ?>" readonly>
                            </td>
                            <?php }else{ $i++; ?>
                                <td colspan="4" class="p-1">
                                    <input type="hidden" name="purchase_uom[]" value="">
                                    <input type="hidden" name="item_quantity[]" value="1">
                                    <input type="hidden" name="operation_symbol[]" value="*">
                                    <input type="hidden" name="item_price[]" id="rentinvpopup_parent_rent_item_price_<?php echo $bd['item_id']; ?>" value="<?php echo $bd['total_amount']!=""?$bd['total_amount']:0; ?>">
                                </td>
                            <?php /** ?>
                            <td colspan="4" class="p-1">
                                <p class="text-center">Utility (<span id="utility_gst_utility_text_<?php echo $bd['item_id']; ?>"><?php echo customeummarmonyformate($bd['item_amount']); ?></span>) + Tax (<span id="utility_gst_total_tax_text_<?php echo $bd['item_id']; ?>"><?php echo customeummarmonyformate($bd['tax_amount']); ?></span>)</p>
                            </td>
                            <?php **/ ?>
                            <td class="p-1">
                                <input type="hidden" class="form-control all_parent_amount_with_tax" id="rentinvpopup_parent_rent_with_tax_<?php echo $bd['item_id']; ?>" name="total_amount_with_tax[]" value="<?php echo $bd['total_amount']!=""?round(($bd['total_amount']+$bd['tax_amount']),2):0; ?>" readonly>

                                <input type="text" class="form-control all_parent_amount" id="rentinvpopup_parent_rent_<?php echo $bd['item_id']; ?>" name="total_amount[]" value="<?php echo $bd['total_amount']!=""?round($bd['total_amount'],2):0; ?>" readonly>
                                <p class="text-center">+ <?= __('Tax ('); ?><span id="utility_gst_total_tax_text_<?php echo $bd['item_id']; ?>"><?php echo customeummarmonyformate($bd['tax_amount']); ?></span>) </p>

                            </td>
                        <?php } ?>
                        </tr>
                        <?php } ?>
                     </tbody>
                 </table>

            </div>
            <div class="col-lg-3">
                <div class="py-2 text-center border bg-primary">
                    <?= __('Summary'); ?>
                </div>
                <table class="w-100">
                    <tbody class="item-list">
                        <tr>
                            <th class="text-start py-2 border">
                                <span><?= __('Sub Total'); ?></span>
                            </th>
                            <th class="text-end py-2 border">
                                <span id="totalallparentamount_text"><?php echo round($transaction_data['amount'], 2); ?></span>
                                <input type="hidden" id="totalallparentamount_input" name="sub_amount" value="">
                            </th>
                        </tr>
                        <tr>
                            <td colspan="2" class="border p-2">
                                <table class="w-100" id="tax_html_table" <?php echo $onetimecheck == "0"?"style='display:none'":""; ?>>
                                 <?php
                                 if($transaction_data[KEY_ORIGINATOR_STATE_ID] == $transaction_data[KEY_SUPPLIER_STATE_ID]){ ?>
                                        <tr>
                                        <th class="text-start p-2 border">
                                            <span>
                                                <?= __('CGST @'); ?>
                                                <span
                                                    class="tax_value">9</span>
                                                <?= __('% on'); ?> <br>
                                                <span class="sub_amount_display">
                                                    ₹<?= number_format(($transaction_data['amount']), 2) ?>
                                                </span>
                                            </span>
                                        </th>
                                        <th class="p-2 text-end border">
                                            <span class="tax_amount_display">
                                                ₹<?= number_format($transaction_data['tax_amount']/2, 2) ?>
                                            </span>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th class="text-start p-2 border">
                                            <span>
                                                <?= __('SGST @'); ?>
                                                <span
                                                    class="tax_value">9</span>
                                                <?= __('% on'); ?> <br>
                                                <span class="sub_amount_display">
                                                    ₹<?= number_format(($transaction_data['amount']), 2) ?>
                                                </span>
                                            </span>
                                        </th>
                                        <th scope="col" class="p-2 text-end border">
                                            <span class="tax_amount_display">
                                                ₹<?= number_format($transaction_data['tax_amount']/2, 2) ?>
                                            </span>
                                        </th>
                                    </tr>
                            <?php } else{  ?>
                                    <tr>
                                        <th class="text-start p-2 border">
                                            <span>
                                                <?= __('IGST @'); ?>
                                                <span
                                                    class="tax_value">18</span>
                                                <?= __('% on'); ?> <br>
                                                <span class="sub_amount_display">
                                                    ₹<?= number_format($transaction_data['amount'] ?? 0, 2) ?>
                                                </span>
                                            </span>
                                        </th>
                                        <th scope="col" class="p-2 text-end border">
                                            <span class="tax_amount_display">
                                                ₹<?= number_format($transaction_data['tax_amount'], 2) ?>
                                            </span>
                                        </th>
                                    </tr>
                                <?php } ?>
                                </table>
                               <table class="w-100" >
                                    <tr>
                                        <th class="text-start p-2 border">
                                            <span><?= __('Total Tax'); ?></span>
                                        </th>
                                        <th class="p-2 text-end border">
                                            <span id="tax_amount_display_total">
                                                ₹<?= number_format($transaction_data['tax_amount'], 2) ?>
                                            </span>
                                            <input type="hidden" id="tax_amount_input" name="total_tax_amount" value="">
                                        </th>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <th class="text-start p-2 border">
                                <span><?= __('Grand Total'); ?></span>
                            </th>
                            <th scope="col" class="p-2 text-end border">
                                <span class="total_amount_display"> <?= number_format($transaction_data['total_amount'], 2) ?>
                                </span>
                                <input type="hidden" class="total_amount_input" name="grand_total_amount" value="<?= $transaction_data['total_amount'] ?>">
                            </th>
                        </tr>
                        <tr>
                            <th class="text-start p-2 border">
                                <span><?= __('Rounding Off'); ?></span>
                            </th>
                            <th scope="col" class="p-2 text-end border">
                                <input type="hidden" class="rounding_off" name="rounding_off" value="<?php echo $transaction_data['rounding_off']!=""?$transaction_data['rounding_off']:0; ?>">
                                <span id="rounding_off">
                                    ₹<?= number_format($transaction_data['rounding_off'], 2) ?>
                                </span>
                            </th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <div class="col-lg-12 m-t-40">
            <div class="row">
                <div class="col-lg-4 form-group">
                    <label for="transaction_number" class="inline-block mb-1 f-w-600"><?= __('Internal Note(s)'); ?> </label>
                    <textarea aria-label="<?= htmlspecialchars(__('Enter Description'), ENT_QUOTES, 'UTF-8'); ?>" id="notes" name="notes" rows="4" class="form-control border" placeholder="<?= htmlspecialchars(__('Enter Description'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($transaction_data['notes'] ?? '') ?></textarea>
                </div>
                <div class="col-lg-4 form-group">
                    <label for="transaction_number" class="inline-block mb-1 f-w-600"><?= __('Printing Note(s)'); ?> </label>
                    <textarea aria-label="<?= htmlspecialchars(__('Enter Description'), ENT_QUOTES, 'UTF-8'); ?>" id="printing_notes" name="printing_notes" rows="4" class="form-control border" placeholder="<?= htmlspecialchars(__('Enter Description'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($transaction_data['printing_notes'] ?? '') ?></textarea>
                </div>
            </div>
        </div>


        <div class="flex gap-2 mt-4 <?php echo $disable; ?>">
            <?php if($transaction_data['id'] !=""){ ?>
            <button type="submit" name="submitrentalinvoice" class="btn btn-primary"><?= __('Update'); ?></button>
            <?php }else{ echo __('Record Not Fund');
            } ?>
        </div>



    </form>






    </div>


</div>
