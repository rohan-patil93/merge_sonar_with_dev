<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=togetuserstationerycart&id=".$_POST['id'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$items = $data['item'];
?>
<script type="text/javascript">
    function submitincourierfrom(){
        var deliver_type = $("#deliver_type").val();
        var total_qty = $("#total_qty").val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=tocreateuserstationeryrequest&deliver_type='+deliver_type+'&total_qty='+total_qty,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.href = 'index.php?option=COM_VIEW_OFFICE_MANAGEMENT&TAB=2';
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">Stationery Request </h5>
    <button type="button" class="btn-close " data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div>
        <table class="table table-hover m-b-0">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Item Name</th>
                <th scope="col">Qty</th>
            </tr>
            </thead>
            <tbody>
                <?php $m=1; $total_qty=0; foreach((array)$data['carts'] as $cart){
                        if($cart['qty']>0){
                ?>
                    <tr>
                    <td><?php echo $m; ?></td>
                    <td><?php echo $items[$cart['item_id']]['item_name']; ?></td>
                    <td class="text-right"><?php echo $cart['qty']; ?></td>
                    </tr>
                <?php $m++; $total_qty = $total_qty+$cart['qty'];
}
                } ?>
                <input type="hidden" name="total_qty" id="total_qty" value="<?php echo $total_qty; ?>">
                <tr>
                    <td></td>
                    <td class="text-right">Total Qty</td>
                    <td class="text-right"><?php echo $total_qty; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <form class="kt-form " id="submitformadditionalservice" >

        <div class="form-group">
            <span> Delivery  </span>
            <select aria-label="Deliver Type"  name="deliver_type" id="deliver_type" class="form-select js-example-basic-single">
                <option value="DD">Delivery To Desk</option>
                <option value="HC">Hand Collect</option>
            </select>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="courier_type" id="courier_type" value="2">
            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light " onclick="submitincourierfrom()">Request</button>
        </div>
    </form>
</div>

<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
<script type="text/javascript">
  $(".js-example-basic-single").select2();
  $(".js-example-basic-multiple").select2();
  $(".js-example-disabled-results").select2();
  $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>
