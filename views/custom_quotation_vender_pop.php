<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table = $_POST['table'];
$field= $_POST['field'];
$tranid = $_POST['tranid'];
$party_type =  $_POST['party_type'];
$new_table = $field;
$allowitemid = explode(',',$_POST['allowitemid']);
$redirecturl = getPagenametotable($field,'list_page_name');

$vurl = API."?mode=getdynamicpopupvendorsassignrequisition&table=".$table."&new_table=".$new_table."&tranid=".$tranid."&party_type=".$party_type;
$resapi = callAPI($vurl);
$result = json_decode($resapi,1);
$vendor = $result['vendors'];
foreach($vendor as $vend){
    $vendors[$vend['id']] = $vend;
}
$quotationlist = $result['quotations'];
foreach($quotationlist as $quotationlists){
    $quotations[$quotationlists['id']][] = $quotationlists;
}
$purchase_orderslist = $result['purchase_orders'];
foreach($purchase_orderslist as $purchase_orderslists){
    $purchase_orders[$purchase_orderslists['id']][] = $purchase_orderslists;
}
$requisitions = $result['requisitions'];
$selected_documents = $result['selected_documents'];

$qt_status_progress = $result['qt_status_progress'];
$po_status_progress = $result['po_status_progress'];
$stausresult = ViewStatusData();

?>

<script type="text/javascript">
function submitrom(){
    if($('#kt_select2_41').val() !=""){
          <?php if(count((array)$quotationlist) !=0 || count((array)$purchase_orderslist) !=0){ ?>
            swal({
                title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                text: "Already Created documents",
                type: "info",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                closeOnConfirm: true
            },
            function(){
                submistionsuccess();
            });
        <?php }else{ ?>
            submistionsuccess();
        <?php } ?>
    }
}

    function submistionsuccess(){
        $("#statuspopupbtn").text("Please Wait........");
        $("#statuspopupbtn").addClass("disabled");
        var  str = '';
          str = 'table='+$('#table').val()+'&new_table='+$('#field').val()+'&vender_id='+$('#kt_select2_41').val()+'&tranid='+$('#tranid').val()+'&hid='+$('#tranid').val()+'&tranid='+$('#tranid').val()+'&user_id='+$('#user_id').val()+'&allowitemid='+$("#allowitemid").val()+'&party_type='+$("#party_type").val();
         $.ajax({
              url: "ajax.php",
              type: "POST",
              data: 'page=insertvenderquatation&'+str,
              success: function(response){
                  var res = JSON.parse(response);
                if(res.main_res.status=='sucess'){
                    $('#kt_modal_dynamic_popup').modal('hide');
                    swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: "<?php echo ucwords(str_replace("_"," ",($new_table))).' '.$_SESSION['error_message']['9059']; ?>",
                        type: "success",
                        showCancelButton: false,
                        closeOnConfirm: true,
                        showLoaderOnConfirm: true
                    }, function () {
                        $('#kt_modal_dynamic_popup').modal('hide');
                        window.location.replace("index.php?option=<?php echo $redirecturl; ?>");
                    });
                }else{
                    $("#statuspopupbtn").text("Submit");
                    $("#statuspopupbtn").removeClass("disabled");
                    swal({
                        title: "Oops...",
                        text: res.main_res.message,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                }

            }
        });
    }
</script>

        <div class="modal-header">
            <h5 class="modal-title"><?php echo viewtitle($new_table); ?> </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body" >

            <form class="kt-form kt-form--label-right" action="" method="post">
                <?php if($_POST['party_type'] =="customers"){  ?>
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="form-group row" >
                        <div class="col-lg-6 w-full" <?php echo $table == "estimates"?"style='pointer-events: none; opacity: 0.7;'":""; ?>>
                            <span>Customers</span>
                            <select aria-label="Vender" class="form-control js-example-basic-multiple"  id="kt_select2_41" name="vender" multiple="multiple">
                                <?php foreach ($vendors as $key => $cus) {  ?>
                                    <option  <?php if($selected_documents['party_id'] == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['display_name']; ?> </option>
                                <?php } ?>
                            </select>
                            <?php if(count((array)$quotationlist) !=0 || count((array)$purchase_orderslist) !=0){ ?>
                            <span class="text-danger f-14">Already Created documents</span>
                            <?php } ?>
                        </div>
                    <div class="col-lg-6 ">
                        <div class="card">
                            <div class="card-header">
                                <h5>Enquiries</h5>
                            </div>

                        <div class="card-body">
                            <table class="f-12" style="width: 100%;">
                                <tr><th scope="col">Item</th><th scope="col" class="text-right">Order Qty</th> <th scope="col" class="text-right">Pending Qty</th></tr>
                            <?php $btndisbale ="disabled"; foreach($requisitions as $qt_item){
                                if($qt_item['pending_qty'] > 0){
                                    $btndisabled ="";
                                }

                                ?>
                                <tr style="<?php echo (count((array)$allowitemid) !=0 && !in_array($qt_item['id'], $allowitemid) || $qt_item['pending_qty'] <= 0)?"text-decoration: line-through;":"";?>">
                                    <td><?php echo $qt_item['item_name']; ?></td>
                                    <td class="text-right"><?php echo $qt_item['item_quantity']; ?></td>
                                    <td class="text-right"><?php echo $qt_item['pending_qty']; ?>

                                    </td>
                                </tr>
                            <?php } ?>
                            </table>
                        </div>
                        </div>
                    </div>

                </div>
                <div class="form-group row">

                        <?php if(count((array)$quotations) !=0){?>
                            <div class="col-lg-6 ">
                        <div class="card">
                            <div class="card-header">
                                <h5>Estimates</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion">

                                <?php foreach($quotations as  $quotation){ ?>
                                    <a class="accordion-msg b-none waves-effect waves-light"><?php echo $quotation[0]['transaction_number']." - ".$vendors[$quotation[0]['party_id']]['display_name']; ?>
                                    <span class="form-label label label-md float-end" style="color:<?php echo $stausresult[$quotation[0]['current_status']]['color']; ?>; background-color:<?php echo $stausresult[$quotation[0]['current_status']]['background_color']; ?>;"><?php echo $qt_status_progress[$quotation[0]['current_status']]['name']; ?></span>
                                    </a>
                                    <div class="accordion-desc">
                                        <table style="width: 100%;">
                                        <?php foreach($quotation as $qt_item){?>
                                            <tr>
                                                <th scope="col"><?php echo $qt_item['item_name']; ?></th>
                                                <th scope="col" class="text-right"><?php echo $qt_item['item_quantity']; ?></th>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </div>
                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <?php } ?>


                        <?php if(count((array)$purchase_orders) !=0){?>
                    <div class="col-lg-6 ">
                        <div class="card">
                            <div class="card-header">
                                <h5>Sales Orders</h5>
                            </div>
                            <div class="card-body">
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion_2">
                                <?php foreach($purchase_orders as  $purchase_order){ ?>
                                    <a class="accordion-msg b-none waves-effect waves-light"><?php echo $purchase_order[0]['transaction_number']." - ".$vendors[$purchase_order[0]['party_id']]['display_name']; ?>
                                    <span class="form-label label label-md float-end" style="color:<?php echo $stausresult[$purchase_order[0]['current_status']]['color']; ?>; background-color:<?php echo $stausresult[$purchase_order[0]['current_status']]['background_color']; ?>;"><?php echo $po_status_progress[$purchase_order[0]['current_status']]['name']; ?></span>
                                    </a>
                                    <div class="accordion-desc">
                                        <table style="width: 100%;">
                                        <?php foreach($purchase_order as $pt_item){?>
                                            <tr>
                                                <th scope="col"><?php echo $pt_item['item_name']; ?></th>
                                                <th scope="col" class="text-right"><?php echo $pt_item['item_quantity']; ?></th>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </div>
                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <?php } ?>
                    </div>
                </div>
<?php }else{ ?>
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <div class="form-group row" >
                        <div class="col-lg-6 w-full" <?php echo $table == "quotations"?"style='pointer-events: none; opacity: 0.7;'":""; ?>>
                            <span>Vendors</span>
                            <select aria-label="Vender" class="form-control js-example-basic-multiple"  id="kt_select2_41_2" name="vender" multiple="multiple">
                                <?php foreach ($vendors as $key => $cus) {  ?>
                                    <option  <?php if($selected_documents['party_id'] == $cus['id']){ ?> selected  <?php
} ?> value = <?php echo $cus['id'] ?>   >      <?php echo $cus['display_name']; ?> </option>
                                <?php } ?>
                            </select>
                            <?php if(count((array)$quotationlist) !=0 || count((array)$purchase_orderslist) !=0){ ?>
                            <span class="text-danger f-14">Already Created documents</span>
                            <?php } ?>
                        </div>
                    <div class="col-lg-6 ">
                        <div class="card">
                            <div class="card-header">
                                <h5>Requisitions</h5>
                            </div>

                        <div class="card-body">
                            <table class="f-12" style="width: 100%;">
                                <tr><th scope="col">Item</th><th scope="col" class="text-right">Order Qty</th> <th scope="col" class="text-right">Pending Qty</th></tr>
                            <?php $btndisbale ="disabled"; foreach($requisitions as $qt_item){
                                if($qt_item['pending_qty'] > 0){
                                    $btndisbale ="";
                                }
                                ?>
                                <tr style="<?php echo (count((array)$allowitemid) !=0 && !in_array($qt_item['id'], $allowitemid) || $qt_item['pending_qty'] <= 0)?"text-decoration: line-through;":"";?>">
                                    <td><?php echo $qt_item['item_name']; ?></td>
                                    <td class="text-right"><?php echo $qt_item['item_quantity']; ?></td>
                                    <td class="text-right"><?php echo $qt_item['pending_qty']; ?>

                                    </td>
                                </tr>
                            <?php } ?>
                            </table>
                        </div>
                        </div>
                    </div>

                </div>
                <div class="form-group row">

                        <?php if(count((array)$quotations) !=0){?>
                            <div class="col-lg-6 ">
                        <div class="card">
                            <div class="card-header">
                                <h5>Quotations</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion-quotations">

                                <?php foreach($quotations as  $quotation){ ?>
                                    <a class="accordion-msg b-none waves-effect waves-light"><?php echo $quotation[0]['transaction_number']." - ".$vendors[$quotation[0]['party_id']]['display_name']; ?>
                                    <span class="form-label label label-md float-end" style="color:<?php echo $stausresult[$quotation[0]['current_status']]['color']; ?>; background-color:<?php echo $stausresult[$quotation[0]['current_status']]['background_color']; ?>;"><?php echo $qt_status_progress[$quotation[0]['current_status']]['name']; ?></span>
                                    </a>
                                    <div class="accordion-desc">
                                        <table style="width: 100%;">
                                        <?php foreach($quotation as $qt_item){?>
                                            <tr>
                                                <th scope="col"><?php echo $qt_item['item_name']; ?></th>
                                                <th scope="col" class="text-right"><?php echo $qt_item['item_quantity']; ?></th>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </div>
                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <?php } ?>


                        <?php if(count((array)$purchase_orders) !=0){?>
                    <div class="col-lg-6 ">
                        <div class="card">
                            <div class="card-header">
                                <h5>Purchase Orders</h5>
                            </div>
                            <div class="card-body">
                                <div class="card-block accordion-block color-accordion-block">
                                    <div class="color-accordion" id="color-accordion-purchase-orders">
                                <?php foreach($purchase_orders as  $purchase_order){ ?>
                                    <a class="accordion-msg b-none waves-effect waves-light"><?php echo $purchase_order[0]['transaction_number']." - ".$vendors[$purchase_order[0]['party_id']]['display_name']; ?>
                                    <span class="form-label label label-md float-end" style="color:<?php echo $stausresult[$purchase_order[0]['current_status']]['color']; ?>; background-color:<?php echo $stausresult[$purchase_order[0]['current_status']]['background_color']; ?>;"><?php echo $po_status_progress[$purchase_order[0]['current_status']]['name']; ?></span>
                                    </a>
                                    <div class="accordion-desc">
                                        <table style="width: 100%;">
                                        <?php foreach($purchase_order as $pt_item){?>
                                            <tr>
                                                <th scope="col"><?php echo $pt_item['item_name']; ?></th>
                                                <th scope="col" class="text-right"><?php echo $pt_item['item_quantity']; ?></th>
                                            </tr>
                                        <?php } ?>
                                        </table>
                                    </div>
                            <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            </div>
        </div>

        <div class="modal-footer">
            <input type="hidden" name="allowitemid" id='allowitemid' value="<?php echo $_POST['allowitemid']; ?>">
            <input type="hidden" name="table" id='table' value="<?php echo $table; ?>">
            <input type="hidden" name="field" id='field' value="<?php echo $new_table; ?>">
            <input type="hidden" name="rid" id='rid' value="<?php echo $rid; ?>">
            <input type="hidden" name="tranid" id='tranid' value="<?php echo $tranid; ?>">
            <input type="hidden" name="user_id" id='user_id' value="<?php echo $_SESSION['userid']; ?>">
            <input type="hidden" name="party_type" id='party_type' value="<?php echo $_POST['party_type']; ?>">

            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light <?php echo $btndisbale; ?>"  id="statuspopupbtn" onclick="submitrom()" >Submit</button>
        </div>

    </form>
</div>
<!-- local datatable modal -->
    <script type="text/javascript" src="assets/pages/accordion/accordion.js"></script>
