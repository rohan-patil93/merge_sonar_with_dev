<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=togetuserliststationerycart&type=".$_SESSION['admin_type'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$items = $data['item'];
?>
<script type="text/javascript">
    function cartviewreqlist(val){
        jQuery.ajax({
            url: "views/custom_view_stationery_cart_details_popup.php",
            data:'option=noption&request_no='+val,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">Stationery Request </h5>
    <button type="button" class="btn-close "  aria-label="Close"></button>
</div>
<div class="modal-body">
    <div>
        <table class="table table-hover m-b-0">
            <thead>
                <tr>
                <th scope="col">#</th>
                <th scope="col">Request No</th>
                <th scope="col">Qty</th>
                <th scope="col">Status</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
                <?php $m=1; $total_qty=0; foreach((array)$data['carts'] as $cart){
                        if($cart['current_status']!=199){
                ?>
                    <tr>
                        <td><?php echo $m; ?></td>
                        <td><?php echo $cart['request_no']; ?></td>
                        <td class="text-right"><?php echo $cart['total_qty']; ?></td>
                        <td>
                            <span class="form-label label label-warning"><?php echo $data['status'][$cart['current_status']]; ?></span>
                            </td>
                        <td>
                            <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $cart['request_no']; ?>')" >
                                <i class="ti-pencil-alt"></i>
                            </button>
                        </td>
                    </tr>
                <?php $m++;
}
                } ?>
            </tbody>
        </table>
    </div>
</div>
