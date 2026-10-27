<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=togetuserrequestcartdetails&request_no=".$_POST['request_no'];
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$items = $data['item'];
$stationery_requests = $data['stationery_requests'];
$old_requests = $data['old_requests'];

?>
<script type="text/javascript">
    function savestationerypopnew(val){
        console.log("Checkkkkk");
        var id = $('input[name=current_status]:checked').attr('id');
        console.log("id:"+id);
      var status = $('#'+id).val();
          var comment = $('#comment').val();
          console.log("id:"+id+",status:"+status+",comment:"+comment);
          if(status!="" ){
            jQuery.ajax({
                url: "ajax.php",
                data:'page=updatestationeryrequest&update_id='+val+"&current_status="+status+"&comment="+comment,
                type: "POST",
                success:function(data){
                    console.log(data);
                    setTimeout(function(){
                    window.location.reload();
                    }, 500);
                },
                error:function (){}
            });
        }else{
            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger','animated flipInX','animated flipOutX','Please Select  status','');
        }
    }
</script>

<script type="text/javascript">

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
<div class="modal-header">
    <h5 class="modal-title">Stationery Details (<?php echo $_POST['request_no']; ?>) </h5>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal"  aria-label="Close"></button>
</div>
<div class="modal-body m-l-25 m-r-25">
    <div>

            <?php $workflowsteps = getWorkflowStatus('stationery_requests',$stationery_requests['id'],$stationery_requests['current_status']); ?>
            <div class="text-left"><?php echo $workflowsteps['breadcrumb']; ?></div>
            <div class="text-right m-r-50">
                <?php
                echo $workflowsteps['workflow_btn'];

                ?>
            </div>
            <?php echo $workflowsteps['current_status_ribbon']; ?>

    </div>
    <div class="col-lg-12 row ">
        <div class="col-lg-6">

            <?php if($_SESSION['st_show_type']!=1){ ?>
                <div class="col-lg-12 row">
                <div class="col-lg-6 f-w-600 mt-2">Transaction No :</div>
                <div class="col-lg-6 mt-2 text-muted"><?php echo $_POST['request_no']; ?></div>
                <div class="col-lg-6 f-w-600 mt-2">Employee :</div>
                <div class="col-lg-6 mt-2 text-muted"><?php echo $stationery_requests['display_name']; ?></div>

                <div class="col-lg-6 f-w-600 mt-2">Date of Issue :</div>
                <div class="col-lg-6 mt-2 text-muted"><?php echo displayDate($stationery_requests['require_date']); ?></div>
                <div class="col-lg-12  f-w-600 mt-2">Request Comment :</div>
                <div class="col-lg-12 mt-1 text-muted  "><?php echo $stationery_requests['request_comment']; ?></div>
                </div>
            <?php } ?>

            <table class="table table-hover m-b-0">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th scope="col">Item Name</th>
                    <th scope="col">Qty</th>
                    <?php if($_SESSION['admin_type']==1 && $stationery_requests['current_status'] != ST_APPROVED ){ ?>
                    <th scope="col"> Available Qty</th>
                    <?php } ?>
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
                    <?php if($_SESSION['admin_type']==1 && $stationery_requests['current_status'] != ST_APPROVED ){ ?>
                    <td class="text-right"> <?php echo $items[$cart['item_id']]['available_qty']; ?> </td>
                    <?php } ?>
                        </tr>
                    <?php $m++; $deliver_type = $cart['deliver_type']; $total_qty = $total_qty+$cart['qty'];
}
                    } ?>
                    <input type="hidden" name="total_qty" id="total_qty" value="<?php echo $total_qty; ?>">

                </tbody>
            </table>
        </div>
        <div class="col-lg-6">
            <div class="col-lg-12 row">
                <?php if($_SESSION['st_show_type']==1){ ?>
                <div class="col-lg-6  f-w-600 mt-2">Building :</div>
                <div class="col-lg-6  mt-1 text-muted "><?php echo $stationery_requests['building_name']; ?></div>
                <div class="col-lg-6 f-w-600 mt-2">Floor :</div>
                <div class="col-lg-6 mt-2 text-muted"><?php echo $stationery_requests['floor_name']; ?></div>
                <div class="col-lg-6 f-w-600 mt-2">Space :</div>
                <div class="col-lg-6 mt-2 text-muted"><?php echo $stationery_requests['cabin_name']; ?></div>

                <div class="col-lg-6  f-w-600 mt-2">Deliver Type :</div>
                <div class="col-lg-6  mt-1 text-muted "><?php echo $data['deliver_types'][$stationery_requests['deliver_type']]; ?></div>
                <div class="col-lg-6 f-w-600 mt-2">Request Date :</div>
                <div class="col-lg-6 mt-2 text-muted"><?php echo displayDate($stationery_requests['request_date']); ?></div>
                <div class="col-lg-6 f-w-600 mt-2">Required Date :</div>
                <div class="col-lg-6 mt-2 text-muted"><?php echo displayDate($stationery_requests['require_date']); ?></div>
                <div class="col-lg-12  f-w-600 mt-2">Request Comment :</div>
                <div class="col-lg-12 mt-1 text-muted  "><?php echo $stationery_requests['request_comment']; ?></div>

                <div class="col-lg-12  f-w-600 mt-2"> Comment</div>
                <div class="col-lg-12 mt-1">
                    <textarea aria-label="Comment" class="form-control" name="comment" id="comment"></textarea>
                </div>
                <?php if(($_SESSION['admin_type']==1 && $stationery_requests['current_status']==10)  ){ ?>

                <?php } ?>
                <?php } else { ?>

                <table class="table table-hover m-b-0" >
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">Request No</th>
                        <th scope="col">Delivery Type</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Require Date</th>
                    </tr>
                    </thead>
                    <tbody class="custom-scroll" style="height: 300px!important;">
                <?php
                if(count((array)$old_requests)>0){
                    $m=1;
                    foreach ($old_requests as $row) { ?>
                        <tr>
                            <td><?php echo $m++; ?></td>
                            <td><?php echo $row['request_no']; ?></td>
                            <td><?php echo $row['deliver_type']; ?></td>
                            <td><?php echo fromDate($row['request_date']); ?></td>
                            <td><?php echo fromDate($row['require_date']); ?></td>
                        </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="5">
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30">
                                <i class="ti-alert text-warning"></i> No Old Requests
                            </p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 200px!important;">
                            </div>
                        </div>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
            </table>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
