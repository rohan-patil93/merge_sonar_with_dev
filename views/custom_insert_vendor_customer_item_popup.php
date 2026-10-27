<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url1 = API."?mode=getvendorcustomeritems&item_id=".$_POST['item_id']."&transaction_table=".$_POST['transaction_table']."&transaction_id=".$_POST['transaction_id'];
$data_details1 = callAPI($url1);
$result = json_decode($data_details1,1);
$items = $result['items'];
$party_table_detail = $result['party_table_detail'];
?>
<script type="text/javascript">
    function insertVendorCustomerItems(){
        var un_lock_date = "00";
        if(un_lock_date ==""){
        }else{
            var formdata = $("#item_form").serialize();
            $.ajax({
                url: "ajax.php",
                type: "POST",
                data: 'page=insertvendorcustomeritems&'+formdata,
                success: function(response) {
                    console.log(response);
                    var res = JSON.parse(response);
                    if(res.result.status == "sucess"){
                        $('#kt_modal_dynamic_popup').modal('hide');
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['9166']; ?>','');
                            getVendorCustomerItems();
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',res.result.message,'');
                    }
                }
            });
        }
    }
</script>
<div class="modal-header">
    <h5 class="modal-title"> Vendor Items </h5>
    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >
    <form id="item_form" method="post">
        <div class="row">
            <div class="form-group col-lg-6">
                <div>
                    <span class="form-label">Item SKU <span class="text-danger">*</span></span>
                    <span class="col-lg-6 float-end  text-right"><?php echo $items['sku'];?></span>
                </div>
                <input aria-label="Sku" type="text" class="form-control datepicker" name="sku" id="sku" value="<?php echo $party_table_detail['vendor_part_no'] !=""?$party_table_detail['vendor_part_no']:$items['sku'];?>">
            </div>
            <div class="form-group col-lg-6">
                <div>
                    <span class="form-label">Item Name <span class="text-danger">*</span></span>
                    <span class="col-lg-6 float-end text-right"><?php echo $items['item_name'];?></span>
                </div>
                <input aria-label="Item Name" type="text" class="form-control datepicker" name="item_name" id="item_name" value="<?php echo $party_table_detail['vendor_part_name'] !=""?$party_table_detail['vendor_part_name']:$items['item_name'];?>">
            </div>
        </div>
        <input type="hidden" name="transaction_table" value="<?php echo $_POST['transaction_table']; ?>">
        <input type="hidden" name="transaction_id" value="<?php echo $_POST['transaction_id']; ?>">
        <input type="hidden" name="item_id" value="<?php echo $_POST['item_id']; ?>">
        <input type="hidden" name="type" value="<?php echo $_POST['type']; ?>">
        <input type="hidden" name="Rid" value="<?php echo $party_table_detail['id']; ?>">

    </form>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light " onclick="insertVendorCustomerItems()">Submit</button>
</div>
