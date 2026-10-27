<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$table = $_POST['table'];
$qty= $_POST['qty'];
$item_id= $_POST['item_id'];
$tranid = $_POST['tranid'];
$list_table = array('grn_returns'=>'goods_receipt_notes','stock_transfers'=>'goods_receipt_notes','stock_transfer_receipts'=>'stock_transfers','stock_transfer_returns'=>'stock_transfers');
$vurl = API."?mode=togetdatawithseriallotnumber&table=".$table."&item_id=".$item_id."&gettable=".$list_table[$table]."&tranid=".$tranid;
$item_daata = callAPI($vurl);
$item_daata = json_decode($item_daata,1);
echo "<pre>"; print_r($item_daata); echo "</pre>";
if($item_daata['tracking_type'] == 1){
    if($tranid!="0"){
        $vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'transaction_table'=>$table,'transaction_id'=>$tranid,'item_id'=>$item_id,'is_active'=>'0');
    }else{
        $vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'transaction_table'=>$table,'random_no'=>$_POST['random'],'item_id'=>$item_id,'is_active'=>'0');
    }
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=item_serial_nos&data=".$vdata;
    $configesponse = callAPI($vurl);
    $res_data = json_decode($configesponse,1);
    foreach($res_data as $res){
        $serialnos[] = $res['serial_no'];
    }
}
if($item_daata['tracking_type'] == 2){
    if($tranid!="0"){
        $vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'transaction_table'=>$table,'transaction_id'=>$tranid,'item_id'=>$item_id,'is_active'=>'0');
    }else{
        $vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'transaction_table'=>$table,'random_no'=>$_POST['random'],'item_id'=>$item_id,'is_active'=>'0');
    }

    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=item_lot_nos&data=".$vdata;
    $configesponse = callAPI($vurl);
    $res_data = json_decode($configesponse,1);
    foreach($res_data as $res){
        $serialnos[] = array('lot'=>$res['lot_no'], 'lot_value'=>$res['lot_quantity']);
    }
}
?>

<script type="text/javascript">

    function submitrom(){
        var qty = $('#qty').val();
        var type = $('#type').val();
        var check = 'P';
        var message = "";
        var data = '';
        var data_count = 0;
        if(type == 1){
            for(var k=1; k < qty+1; k++){
                var value = $('#serial_no_'+k).val();
                if( value == ""){
                    check = 'F';
                    message = "Please fill the serial number ";
                    break;
                }
                data += '&serial_'+k+'='+value;
            }
            data_count = qty;
        }
        if(type == 2){
            var count = 0; var lot=""; var value="";
            var loopcount = $(".sfitemRow").length;
            for(var k=1; k < loopcount+1 ; k++){
                lot = $('#static_value_'+k).val();
                value = $('#qty_val_'+k).val();
                if( value == "" && lot ==""){
                    check = 'F';
                    message = "Please fill the Lot number ";
                    break;
                }
                data += '&lot_'+k+'='+lot+'&lotvalue_'+k+'='+value;
                count = parseFloat(count)+parseFloat(value);
            }
            if(count != qty){
                check = 'F';
                 message = "Please check qty is qual to lot qty and requried lot name";
            }
            data_count = loopcount;
        }
        if(check == 'P'){
              var  str = '';
             str = 'table='+$('#table').val()+'&new_table='+$('#field').val()+'&vender_id='+$('#kt_select2_41').val()+'&tranid='+$('#tranid').val()+'&hid='+$('#tranid').val()+'&tranid='+$('#tranid').val()+'&item_id='+$('#item_id').val()+'&user_id='+$('#user_id').val()+'&count='+data_count+data+'&type='+type+'&random='+$('#random').val();

              console.log(str);
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=insertseriallot&'+str,
                success: function(response) {
                    console.log(response);
                    $('#kt_modal_KTDatatable_remote').modal('hide');
                    Swal.fire({
                        title: "Comments Updated ",
                        type: "success",
                        buttonsStyling: false,
                        confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",
                        confirmButtonClass: "btn btn-danger",
                        showCancelButton: true,
                        cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",
                        cancelButtonClass: "btn btn-default"
                    }).then(function(result) {
                        if (result.value) {
                            $('#kt_modal_KTDatatable_remote').modal('hide');
                        }
                    });
                }
            });
        }else{
             Swal.fire({
                title: message,
                text: "",
                type: "warning"
            });
        }
    }

</script>
    <div class="modal-header">
                        <h5 class="modal-title">
                            Serial No
                            <small>&nbsp;&nbsp; <b>Transaction Type: </b>&nbsp; <?php echo viewtitle($table); ?>  </small>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">

                        </button>
                    </div>
                    <div class="modal-body" style="overflow-y: scroll;" >

            <form class="" action="" method="post">
            <div class="col-lg-12" style="max-height: 400px;float: left;">
                <?php if($item_daata['tracking_type'] == 1){ ?>
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <?php for($i=1; $i<$qty+1; $i++){?>
                        <div class="form-group row">
                            <label for="example-text-input" class="col-3 col-form-label">Serial Number <?php echo $i; ?></label>
                            <div class="col-6">
                                <input aria-label="Input field" type="text" class="form-control"  id="serial_no_<?php echo $i; ?>" name="serial_no" value="<?php echo $serialnos[$i-1]; ?>">
                            </div>

                        </div>
                    <?php } ?>
                </div>
                <?php }
if($item_daata['tracking_type'] == 2){ ?>
                <div id='append_statick'>
                <div class="row align-items-center" style="margin-top: 10px;">
                    <div class="col-lg-3">
                        <span><b>Lot Number</b></span>
                    </div>
                    <div class="col-lg-3">
                        <span><b>Qty</b></span>
                    </div>
                    <div class="col-lg-3">
                        <span><b></b></span>
                    </div>
                </div>
                <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
                    <?php for($i=1; $i<count((array)$serialnos)+1; $i++){ ?>
                    <div class="row align-items-center sfitemRow" id="static_div_<?php echo $i; ?>" style="margin-top: 10px;">
                        <div class="col-lg-3">
                            <input aria-label="Input field" type="text" class="form-control"  id="static_value_<?php echo $i; ?>"  value="<?php echo $serialnos[$i-1]['lot']; ?>"  name="type">
                        </div>
                        <div class="col-lg-3">
                            <input aria-label="Input field" type="text"class="form-control"  id="qty_val_<?php echo $i; ?>"  value="<?php echo $serialnos[$i-1]['lot_value']; ?>"  name="type">
                        </div>
                        <div class="col-lg-3">
                            <input type="hidden" value="0" id="static_delete_value_<?php echo $i; ?>" >
                            <button class="btn btn-warning" id="<?php echo $i; ?>" type="button" onclick="deleteRows(this.id)">Delete</button>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                </div>
                <div class="m-l-5 m-t-5" style="margin-top: 10px;">
                    <button class="btn btn-success" id="addstatickRows" type="button" onclick="addstatickRows123()">+ Add More</button>&nbsp;&nbsp;
                </div>

                <?php } ?>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="table" id='table' value="<?php echo $table; ?>">
                <input type="hidden" name="field" id='field' value="<?php echo $new_table; ?>">
                <input type="hidden" name="rid" id='rid' value="<?php echo $rid; ?>">
                <input type="hidden" name="tranid" id='tranid' value="<?php echo $tranid; ?>">
                <input type="hidden" name="user_id" id='user_id' value="<?php echo $_SESSION['userid']; ?>">
                <input type="hidden" name="qty" id='qty' value="<?php echo $qty; ?>">
                <input type="hidden" name="item_id" id='item_id' value="<?php echo $item_id; ?>">
                <input type="hidden" name="type" id='type' value="<?php echo $item_daata['tracking_type']; ?>">
                <input type="hidden" name="random" id='random' value="<?php echo $_POST['random']; ?>">
                <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-bold btn-upper btn-font-sm" onclick="submitrom()" >Submit</button>
            </div>

    </form>
</div>
<!-- local datatable modal -->
<script src="assets/js/pages/crud/forms/widgets/tagify.js" type="text/javascript"></script>
