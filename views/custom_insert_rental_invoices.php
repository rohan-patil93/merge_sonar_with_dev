<?php $_SESSION['apply_js']['table'] = 1;
$_SESSION['apply_js']['multiselest'] = 1;
 ?>
<?php
if (isset($_POST['submitrentalinvoice'])) {
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API . "?mode=updateRentalInvoiceData&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response1 = json_decode($response1, 1);
    $response = $response1['res'];
   //echo "<pre>"; print_r($response); echo "</pre>";
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
$rid = $rrid['1'];

$cusurl = API . "?mode=getrentalinvoice&rid=" . $rid;
$apicall = callAPI($cusurl);
$result = json_decode($apicall, 1);
$invoiceData = $result['rental_invoices'];
$invoiceItemsData = $result['rental_invoice_items'];
$paymentTermsData = $result['payment_terms'];
$generalCodeDetailsData = $result['general_code_details'];
$invoiceItemsTaxData = $result['rental_invoice_item_taxes'];
$invoiceallItemsTaxData = $result['rental_invoice_all_item_taxes'];
$taxdatawithitemdataid = array();
foreach($invoiceallItemsTaxData as $taxdata){
    $taxdatawithitemdataid[$taxdata['transaction_item_id']] = $taxdata;
}
$hsnSacCodesData = $result['hsn_and_sac_codes'];
$userData = $result['application_users'];
$dynamic_mapping_tables = $result['dynamic_mapping_tables'];
//echo "<pre>"; print_r($taxdatawithitemdataid); echo "</pre>";

$check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,'');
if(in_array($invoiceData['current_status'], (array)$check_array) || ($invoiceData['current_status'] == ST_CALLBACK && $invoiceData['originator'] == $_SESSION['userid'])){
        $disable = "" ;
}else{
    $disable = "event_disabled" ;
}
?>
<script type="text/javascript">
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
                text: <?php echo json_encode(__('Please note the status of the document will change to')); ?>+new_status_name,
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: <?php echo json_encode(__('Yes')); ?>,
                cancelButtonText: <?php echo json_encode(__('No')); ?>,
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
                                title: <?php echo json_encode(__('Oops...')); ?>,
                                text: res.transaction_update.message,
                                type: "error",
                                showCancelButton: true,
                                showConfirmButton: false,
                                cancelButtonText: <?php echo json_encode(__('Ok')); ?>,
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
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?php echo __('Close'); ?>"></button>
        </div>
<?php } ?>
<div class="card">
   <?php if($invoiceData['cancel_invoice'] =="Y"){ ?>
<div id="dynamiccanceldiv"><span><?php echo __('Cancel Invoice'); ?></span></div>
<?php } ?>
     <?php
        $workflowsteps = getWorkflowStatus('rental_invoices',$invoiceData['id'],$invoiceData['current_status']);
            ?>
     <div class="card-header table-card-header">
        <h5> <?php echo __('Rental Invoice'); ?></h5>
        <div class="card-header-right table-card-header ">

             <?php
                if($dynamic_mapping_tables['cancellation_application_table_name'] !="" && $invoiceData['current_status'] == ST_APPROVED && $invoiceData['cancel_invoice'] !="Y" && $invoiceData['originator'] ==$_SESSION['userid']){ ?>
                        <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="cancelInvoices('rental_invoices','<?php echo $rid; ?>')">
                            <i class="text-white fa fa-times-circle"></i> <?php echo __('&nbsp;Cancel Invoice'); ?>
                        </button>
                <?php  }  ?>

                <?php echo $workflowsteps['workflow_btn'];
                 $printurl = "index.php?option=COM_VIEW_RENTAL_INVOICES&rid=".$rid."&table=rental_invoices&title=".urlencode('Rental Invoices');

                ?>

                <?php
               // $invoicetables = array('sales_invoices','purchase_invoices','supplier_bills','rental_invoices','utility_rental_invoices');
                if($invoiceData['current_status'] == ST_APPROVED){  ?>
                    <a href="index.php?option=COM_INSERT_DEBIT_NOTES&ref_type=rental_invoices&ref_id=<?php echo $rid; ?>"  class=" cursor-pointer  btn btn-success" > <i class="icofont icofont-plus icon-white"></i> <?php echo __('Debit Note'); ?> </a>
                    <a href="index.php?option=COM_INSERT_CREDIT_NOTES&ref_type=rental_invoices&ref_id=<?php echo $rid; ?>" class="cursor-pointer  btn btn-danger" ><i class="icofont icofont-plus icon-white"></i> <?php echo __('Credit Note'); ?> </a>
                <?php } ?>


             <a target="_blank" href="<?php echo $printurl; ?>" class="btn btn-clean kt-margin-r-10" <?php echo $permission_listbtn; ?>><i class="ti-printer"></i> </a>

             <?php
             if($invoiceData['cancel_invoice'] =="N"){ ?>
                 <button type="button" class="btn btn-danger btn-sm waves-effect waves-light" onclick="cancelInvoices('rental_invoices','<?php echo $rid; ?>')">
                    <i class="text-white fa fa-times-circle"></i> <?php echo __('&nbsp;Cancel Invoice'); ?>
                </button>
          <?php  }  ?>

            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply"></i>
                <?php echo __('Back'); ?>
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php echo $workflowsteps['current_status_ribbon']; ?>
        <form method="POST">
            <div class="row <?php echo $disable; ?>" >
                <div class="col-lg-3 form-group">
                    <label for="transaction_number" class="inline-block mb-1 f-w-600"><?php echo __('Transaction
                        Number'); ?></label>
                    <input type="hidden" name="rid" value="<?= htmlspecialchars($invoiceData['id'] ?? '') ?>">
                    <input type="text" id="transaction_number" name="transaction_number"
                        class="form-control" placeholder="<?php echo __('Enter Transaction no.'); ?>"
                        value="<?= htmlspecialchars($invoiceData['transaction_number'] ?? '') ?>" disabled
                        readonly>
                </div>
                <div class="col-lg-3 form-group">
                    <label for="transaction_date" class="inline-block mb-1 f-w-600"><?php echo __('Transaction Date'); ?></label>
                    <input type="text" id="transaction_date" value="<?= htmlspecialchars(fromDate($invoiceData['transaction_date']) ?? '') ?>" name="transaction_date" class="form-control datepicker" placeholder="<?php echo __('Transaction Date'); ?>">
                </div>

                <div class="col-lg-3 form-group">
                    <label for="payment_terms" class="inline-block mb-1 f-w-600"><?php echo __('Payment Terms'); ?></label>
                    <select name="payment_terms" id="payment_terms" disabled class="form-control js-example-basic-single">
                        <?php foreach ($paymentTermsData as $payment_term): ?>
                            <option value="<?= $payment_term['id'] ?>">
                                <?= $payment_term['payment_term_name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label for="due_date" class="inline-block mb-1 f-w-600"><?php echo __('Due Date'); ?> </label>
                    <input type="text" id="due_date" name="due_date" class="form-control datepicker"
                        placeholder="<?php echo __('Due Date'); ?>"
                        value="<?= htmlspecialchars(fromDate($invoiceData['due_date']) ?? '') ?>">
                </div>
                <div class="col-lg-3 form-group">
                    <label for="originator" class="inline-block mb-1 f-w-600"><?php echo __('Originator'); ?></label>
                    <input type="text" id="originator" name="originator" class="form-control"
                        placeholder="<?php echo __('originator'); ?>" value="<?= $userData['user_name'] ?>" disabled readonly>
                </div>
                <div class="col-lg-3 form-group">
                    <label for="parent_ref_id" class="inline-block mb-1 f-w-600"><?php echo __('Property'); ?></label>
                    <select name="parent_ref_id" id="parent_ref_id" disabled class="form-control">
                        <option value="1"><?php echo __('Kingston Business Park'); ?></option>
                    </select>
                </div>
                <div class="col-lg-3 form-group">
                    <label for="ref_no" class="inline-block mb-1 f-w-600"><?php echo __('Refrence No.'); ?></label>
                    <input type="text" id="ref_no" name="ref_no" class="form-control"
                        placeholder="<?php echo __('Refrence Number'); ?>"
                        value="<?= htmlspecialchars($invoiceData['ref_no'] ?? '') ?>">
                </div>
                <div class="col-lg-3 form-group">
                    <label for="refrence_bill" class="inline-block mb-1 f-w-600"><?php echo __('Refrence Bill'); ?></label>
                    <input type="text" id="refrence_bill" name="ref_date" class="form-control datepicker"                        placeholder="<?php echo __('Refrence Bill'); ?>"  value="<?= htmlspecialchars(fromDate($invoiceData['ref_date']) ?? '') ?>">
                </div>

                <div class="col-lg-3 form-group">
                    <label for="party" class="inline-block mb-1 f-w-600"><?php echo __('Party'); ?></label>
                    <input type="text" id="party" name="party_name" class="form-control"
                        placeholder="<?php echo __('Party Name'); ?>"
                        value="<?= htmlspecialchars($invoiceData['party_name'] ?? '') ?>" disabled readonly>
                </div>
                <div class="col-lg-3 form-group">
                    <label for="party_email" class="inline-block mb-1 f-w-600"><?php echo __('Party
                        Email'); ?></label>
                    <input type="text" id="party_email" name="party_email" class="form-control"
                        placeholder="<?php echo __('Enter Transaction no.'); ?>"
                        value="<?= htmlspecialchars($invoiceData['party_email'] ?? '') ?>" disabled
                        readonly>
                </div>
                <div class="col-lg-3 form-group">
                    <label for="party_phone" class="inline-block mb-1 f-w-600"><?php echo __('Party Contact
                        Number'); ?></label>
                    <input type="text" id="party_phone"
                        value="<?= htmlspecialchars($invoiceData['party_phone'] ?? '') ?>"
                        name="party_phone" class="form-control" placeholder="<?php echo __('Transaction Date'); ?>" disabled
                        readonly>
                </div>
                <div class="col-lg-3 form-group">
                    <label for="party_address" class="inline-block mb-1 f-w-600"><?php echo __('Party Address'); ?></label>
                    <textarea name="party_address" disabled id="party_address"
                        class="form-control"><?= htmlspecialchars($invoiceData['party_address'] ?? '') ?> </textarea>
                </div>
            </div>
            <div class="row m-t-40 <?php echo $disable; ?>" style="padding: 0px 50px 0px 50px;">
                <table class="col-lg-9 col-md-9">
                    <thead>
                        <tr>
                            <th class="border px-3 py-2">
                                <?php echo __('Items / Services'); ?>
                            </th>
                            <th class="border px-3 py-2">
                                <?php echo __('HSN/Tax *'); ?>
                            </th>
                            <th class="border px-3 py-2 text-end">
                                <?php echo __('Qty *'); ?>
                            </th>
                            <th class="border px-3 py-2">
                                <?php echo __('UOM *'); ?>
                            </th>
                            <th class="border px-3 py-2 text-end">
                                <?php echo __('Rate *'); ?>
                            </th>
                            <th class="border px-3 py-2 text-end">
                                <?php echo __('Tax'); ?>
                            </th>
                            <th class="border px-3 py-2 text-end">
                                <?php echo __('Amount'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="before:block before:h-3 item-list">
                        <?php if (!empty($invoiceItemsData)): ?>
                            <?php foreach ($invoiceItemsData as $rental_item):
                                $tax_row_detail = $taxdatawithitemdataid[$rental_item['item_id']];
                                ?>
                                <tr class="item">
                                    <td class="border p-1">
                                        <input type="hidden" name="id[]"
                                            value="<?= htmlspecialchars($rental_item['id']) ?>">
                                            <input type="hidden" id="originator_state_id"
                                            value="<?= htmlspecialchars($rental_item['originator_state_id']) ?>">
                                        <input type="hidden" id="supplier_state_id"
                                            value="<?= htmlspecialchars($rental_item['supplier_state_id']) ?>">
                                        <input aria-label="<?= htmlspecialchars(__('Item name'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="itemName" class="form-control"
                                            placeholder="<?php echo __('Item Name'); ?>" name="item_name[]"
                                            value="<?= htmlspecialchars($rental_item['item_name']) ?>" readonly>
                                    </td>
                                    <td class="w-52 border">
                                        <input aria-label="<?= htmlspecialchars(__('Hsntax'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="hsntax" name="hsntax[]"
                                            value="HSN - <?= htmlspecialchars($hsnSacCodesData['code'] ?? 'N/A') ?> / Tax - <?= htmlspecialchars($invoiceItemsTaxData['tax_value'] ?? 'N/A') ?>"
                                            class="form-control" placeholder="<?php echo __('Enter HSN TAX'); ?>" readonly>
                                    </td>
                                    <td class="w-28 border text-end">
                                        <input aria-label="<?= htmlspecialchars(__('Item quantity'), ENT_QUOTES, 'UTF-8'); ?>" type="number" id="item_qty" name="item_quantity[]"
                                            value="<?= htmlspecialchars($rental_item['item_quantity'] ?? 0) ?>"
                                            class="form-control text-end" placeholder="<?php echo __('Enter QTY'); ?>">
                                    </td>
                                    <td class="w-28 border">
                                        <select aria-label="<?= htmlspecialchars(__('Purchase uom'), ENT_QUOTES, 'UTF-8'); ?>" name="purchase_uom[]"  class="form-control js-example-basic-single">
                                            <?php foreach ($generalCodeDetailsData as $uom): ?>
                                                <option value="<?= htmlspecialchars($uom['genc_detail_code']) ?>">
                                                    <?= htmlspecialchars($uom['genc_detail_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="w-28 border text-end">
                                        <input aria-label="<?= htmlspecialchars(__('Item price'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="item_rate" name="item_price[]"
                                            value="<?= $rental_item['item_price']; ?>"
                                            class="form-control text-end" placeholder="<?php echo __('Enter Rate'); ?>">
                                    </td>
                                    <td class="w-28 border text-end">
                                        <input type="hidden" id="tax_percentage" name="tax_percentage[]" value="<?php echo $tax_row_detail['tax_value']; ?>">
                                        <input aria-label="<?= htmlspecialchars(__('Tax amount'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control text-end" id="tax_amount" name="tax_amount[]"
                                            value="<?= htmlspecialchars($rental_item['tax_amount'] ?? '') ?>" readonly>
                                    </td>
                                    <td class="w-40 border text-end">
                                        <input aria-label="<?= htmlspecialchars(__('Item amount'), ENT_QUOTES, 'UTF-8'); ?>" type="text" id="item_amount" name="item_amount[]"
                                            value="<?= $rental_item['item_amount'] ?>"
                                            class="form-control text-end" placeholder="<?php echo __('Enter Amount'); ?>" readonly>

                                        <input type="hidden" id="total_amount" name="total_amount[]"
                                            value="<?= htmlspecialchars($rental_item['total_amount'] ?? '') ?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border border-slate-200">
                                        <textarea aria-label="<?= htmlspecialchars(__('Description'), ENT_QUOTES, 'UTF-8'); ?>" id="itemdescription1" name="description[]" rows="1"
                                            style="height: 15vh;" class="form-control"
                                            placeholder="<?php echo __('Enter Description'); ?>"><?= htmlspecialchars($rental_item['description'] ?? '') ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center"><?php echo __('No items found for this invoice.'); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($invoiceallItemsTaxData as $tax_item): ?>
                            <div>
                                <input type="hidden" name="tax_ids[]"
                                    value="<?= htmlspecialchars($tax_item['id'] ?? 'N/A') ?>"
                                    class="form-control" placeholder="<?php echo __('Tax Value'); ?>" readonly>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="col-lg-3 col-md-3">
                    <div class="py-2 text-center border bg-primary">
                        <?php echo __('Order Summary'); ?>
                    </div>
                    <table class="w-100">
                        <tbody class="item-list">
                            <tr>
                                <th class="text-start py-2 border">
                                    <span><?php echo __('Sub Total'); ?></span>
                                </th>
                                <th class="text-end py-2 border">
                                    <span class="sub_amount_display">
                                        ₹<?= number_format($rental_item['item_amount'], 2) ?>
                                    </span>
                                    <input type="hidden" class="sub_amount_input" name="sub_amount"
                                        value="">
                                </th>
                            </tr>

                            <tr>
                                <td colspan="2" class="border p-2">
                                    <table class="w-100">
                                     <?php
                                     if($invoiceData['originator_state_id'] == $invoiceData['supplier_state_id']){ ?>
                                    <tr>
                                            <th class="text-start p-2 border">
                                                <span>
                                                    <?php echo __('CGST @'); ?>
                                                    <span
                                                        class="tax_value"><?= number_format(($invoiceItemsTaxData['tax_value']/2), 2) ?></span>
                                                    <?php echo __('%
                                                    on'); ?> <br>
                                                    <span class="sub_amount_display">
                                                        ₹<?= number_format(($rental_item['item_amount']/2), 2) ?>
                                                    </span>
                                                </span>
                                            </th>
                                            <th class="p-2 text-end border">
                                                <span class="tax_amount_display">
                                                    ₹<?= number_format($rental_item['tax_amount'], 2) ?>
                                                </span>
                                            </th>
                                        </tr>
                                        <tr>
                                            <th class="text-start p-2 border">
                                                <span>
                                                    <?php echo __('SGST @'); ?>
                                                    <span
                                                        class="tax_value"><?= number_format(($invoiceItemsTaxData['tax_value']/2), 2) ?></span>
                                                    <?php echo __('%
                                                    on'); ?> <br>
                                                    <span class="sub_amount_display">
                                                        ₹<?= number_format(($rental_item['item_amount']/2), 2) ?>
                                                    </span>
                                                </span>
                                            </th>
                                            <th scope="col" class="p-2 text-end border">
                                                <span class="tax_amount_display">
                                                    ₹<?= number_format($rental_item['tax_amount'], 2) ?>
                                                </span>
                                            </th>
                                        </tr>
                                <?php } else{  ?>
                                        <tr>
                                            <th class="text-start p-2 border">
                                                <span>
                                                    <?php echo __('IGST @'); ?>
                                                    <span
                                                        class="tax_value"><?= number_format($invoiceItemsTaxData['tax_value'] ?? 0, 2) ?></span>
                                                    <?php echo __('%
                                                    on'); ?> <br>
                                                    <span class="sub_amount_display">
                                                        ₹<?= number_format($rental_item['item_amount'] ?? 0, 2) ?>
                                                    </span>
                                                </span>
                                            </th>
                                            <th scope="col" class="p-2 text-end border">
                                                <span class="tax_amount_display">
                                                    ₹<?= number_format($rental_item['tax_amount'], 2) ?>
                                                </span>
                                            </th>
                                        </tr>
                                    <?php } ?>
                                        <tr>
                                            <th class="text-start p-2 border">
                                                <span><?php echo __('Total Tax'); ?></span>
                                            </th>
                                            <th scope="col" class="p-2 text-end border">
                                                <span class="tax_amount_display_total">
                                                    ₹<?= number_format($rental_item['tax_amount'], 2) ?>
                                                </span>
                                                <input type="hidden" class="tax_amount_input"
                                                    name="total_tax_amount" value="">
                                            </th>
                                        </tr>
                                    </table>
                                </td>
                            </tr>

                            <tr>
                                <th class="text-start p-2 border">
                                    <span><?php echo __('Grand Total'); ?></span>
                                </th>
                                <th scope="col" class="p-2 text-end border">
                                    <span class="total_amount_display">
                                        ₹<?= number_format($invoiceData['total_amount'], 2) ?>
                                    </span>
                                    <input type="hidden" class="total_amount_input"
                                        name="grand_total_amount" value="<?= $invoiceData['total_amount'] ?>">
                                </th>
                            </tr>
                            <tr>
                                <th class="text-start p-2 border">
                                    <span><?php echo __('Rounding Off'); ?></span>
                                </th>
                                <th scope="col" class="p-2 text-end border">
                                    <input type="hidden" class="rounding_off" name="rounding_off" value="<?php echo $invoiceData['rounding_off']!=""?$invoiceData['rounding_off']:0; ?>">
                                    <span id="rounding_off">
                                        ₹<?= number_format($invoiceData['rounding_off'], 2) ?>
                                    </span>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-lg-12 m-t-40">
                    <div class="row">
                        <div class="col-lg-4 form-group">
                            <label for="transaction_number" class="inline-block mb-1 f-w-600"><?php echo __('Internal
                                Note(s)'); ?> </label>
                            <textarea aria-label="<?= htmlspecialchars(__('Notes'), ENT_QUOTES, 'UTF-8'); ?>" id="notes" name="notes" rows="4" class="form-control border"
                                placeholder="<?php echo __('Enter Description'); ?>"><?= htmlspecialchars($invoiceData['notes'] ?? '') ?></textarea>
                        </div>
                        <div class="col-lg-4 form-group">
                            <label for="transaction_number" class="inline-block mb-1 f-w-600"><?php echo __('Printing
                                Note(s)'); ?> </label>
                            <textarea aria-label="<?= htmlspecialchars(__('Printing notes'), ENT_QUOTES, 'UTF-8'); ?>" id="printing_notes" name="printing_notes" rows="4"
                                class="form-control border"
                                placeholder="<?php echo __('Enter Description'); ?>"><?= htmlspecialchars($invoiceData['printing_notes'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex gap-2 mt-4 <?php echo $disable; ?>">
                <?php if($invoiceData['id'] !=""){ ?>
                <button type="submit" name="submitrentalinvoice" class="btn btn-primary"><?php echo __('Update'); ?></button>
                <?php }else{ echo __('Record Not Fund');
                } ?>
            </div>
        </form>
    </div>
</div>
<script>
    (function () {
        const taxValueElement = document.querySelector('.tax_value');
        const taxValue = parseFloat(taxValueElement?.textContent || 0) / 100;
        //console.log("taxValueElement  -----",taxValueElement);
        //console.log("taxValue  -----",taxValue);



        function calculateRow(row) {
            const qtyInput = row.querySelector('#item_qty');
            const rateInput = row.querySelector('#item_rate');
            const amountInput = row.querySelector('#item_amount');
            const taxAmountInput = row.querySelector('#tax_amount');
            const totalAmountInput = row.querySelector('#total_amount');
            const tax_percentage = row.querySelector('#tax_percentage');

            if (!qtyInput || !rateInput || !amountInput || !taxAmountInput || !totalAmountInput) {
                console.error("Critical input elements not found for a row.");
                return { amount: 0, tax: 0, total: 0 };
            }

            const qty = parseFloat(qtyInput.value) || 0;
            const rate = parseFloat(rateInput.value) || 0;
            const amount = qty * rate;
            if(tax_percentage.value !="" || tax_percentage.value !="0"){
                var tax = ((tax_percentage.value/100)*amount);
            }else{
                var tax = 0;
            }
            const total = amount + tax;
            amountInput.value = amount.toFixed(2);
            taxAmountInput.value = tax.toFixed(2);
            totalAmountInput.value = total.toFixed(2);

            return { amount, tax, total };
        }

        function calculateTotals() {
            const originator_state_id = document.querySelector("#originator_state_id");
            const supplier_state_id = document.querySelector("#supplier_state_id");
            const rows = document.querySelectorAll('.item');
            let subtotal = 0;
            let totalTax = 0;
            let grandTotal = 0;

            rows.forEach(row => {
                const { amount, tax, total } = calculateRow(row);
                subtotal += amount;
                totalTax += tax;
                grandTotal += total;
            });
            //console.log("totalTax ---- ",totalTax);
            // Update Sub Total

            document.querySelectorAll('.tax_amount_display_total').forEach(el => el.textContent = `₹${totalTax.toFixed(2)}`);

            if(originator_state_id.value == supplier_state_id.value){
                totalTax = (totalTax/2);
            }
            document.querySelectorAll('.sub_amount_display').forEach(el => el.textContent = `₹${subtotal.toFixed(2)}`);
            document.querySelectorAll('.sub_amount_input').forEach(el => el.value = subtotal.toFixed(2));

            // Update Tax Amount
            document.querySelectorAll('.tax_amount_display').forEach(el => el.textContent = `₹${totalTax.toFixed(2)}`);
            document.querySelectorAll('.tax_amount_input').forEach(el => el.value = totalTax.toFixed(2));

            grandTotal = roundingOffVal(grandTotal);
            // Update Grand Total
            document.querySelectorAll('.total_amount_display').forEach(el => el.textContent = `₹${grandTotal.toFixed(2)}`);
            document.querySelectorAll('.total_amount_input').forEach(el => el.value = grandTotal.toFixed(2));


        }

        const rows = document.querySelectorAll('.item');
        rows.forEach(row => {
            const qtyInput = row.querySelector('#item_qty');
            const rateInput = row.querySelector('#item_rate');

            if (qtyInput) qtyInput.addEventListener('input', calculateTotals);
            if (rateInput) rateInput.addEventListener('input', calculateTotals);
        });

        // Initial calculation
        calculateTotals();
    })();

    function roundingOffVal(grandtotal){
        console.log("-----------------------------",grandtotal);
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
</script>