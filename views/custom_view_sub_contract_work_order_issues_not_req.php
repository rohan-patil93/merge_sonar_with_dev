<?php
$rid = '';
$parent = array();
$child_items = array();
$api_error_message = '';

if(isset($_GET['Rid']) && $_GET['Rid'] != ''){
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];

    $url = API . "?mode=getsubcontractworkorderissues&method=get&rid=" . $rid;
    $api_response = callAPI($url);
    $api_data = json_decode($api_response, 1);

    if($api_data && $api_data['status'] == 'success'){
        $parent = $api_data['parent'] ?? array();
        $child_items = $api_data['child_items'] ?? array();
    } else {
        $api_error_message = $api_data['message'] ?? 'Failed to load issue details';
    }
} else {
    $api_error_message = 'Invalid issue record';
}
?>

<link rel="stylesheet" href="assets/css/custom_view_sub_contract_work_order_issues.css">

<div class="page-body">
    <div class="card issue-view-card">
        <?php
        if($parent['current_status'] != ""){
            $workflowsteps = getWorkflowStatus('sub_contract_work_order_issues', $parent['id'], $parent['current_status']);
            echo $workflowsteps['current_status_ribbon'];
        }
        ?>
        <div class="card-header table-card-header">
            <h5><?= __('Sub Contract Work Order Issue Details'); ?></h5>
            <div class="card-header-right table-card-header ">
                <?php
                echo $workflowsteps['workflow_btn'];
                ?>

                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                    <i class="icofont icofont-reply"></i>
                    <?= __('Back'); ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php echo $workflowsteps['breadcrumb']; ?>
            <?php if($api_error_message != '') { ?>
                <div class="alert alert-danger mb-3">
                    <?php echo $api_error_message; ?>
                </div>
            <?php } ?>

            <?php if(count((array)$parent) > 0) { ?>
                <div class="issue-view-section issue-parent-section">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="section-title"><?= __('Issue Header'); ?></h6>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Issue No'); ?></span>
                                <p><?php echo $parent['transaction_number'] != '' ? $parent['transaction_number'] : 'ISSUE-' . ($parent['id'] ?? ''); ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Issue Date'); ?></span>
                                <p><?php echo $parent['transaction_date'] != '' ? fromDate($parent['transaction_date']) : ''; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Reference No'); ?></span>
                                <p><?php echo $parent['ref_no'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Reference Date'); ?></span>
                                <p><?php echo $parent['ref_date'] != '' ? fromDate($parent['ref_date']) : ''; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Subcontract Work Order'); ?></span>
                                <p><?php echo $parent['subcontract_work_order_number'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Vendor'); ?></span>
                                <p><?php echo $parent['party_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Product'); ?></span>
                                <p><?php echo $parent['product_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Product SKU'); ?></span>
                                <p><?php echo $parent['product_sku'] ?? ''; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Request Warehouse'); ?></span>
                                <p><?php echo $parent['request_warehouse_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Party Warehouse'); ?></span>
                                <p><?php echo $parent['party_warehouse_name'] ?? ''; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Total Qty'); ?></span>
                                <p><?php echo $parent['work_order_item_qty'] ?? '0'; ?></p>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="detail-group">
                                <span><?= __('Total Amount'); ?></span>
                                <p><?php echo number_format($parent['work_order_total_amount'] ?? 0, 2); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="issue-view-section issue-child-section mt-4">
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="section-title"><?= __('Issued Items'); ?></h6>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered issue-child-table nowrap">
                            <thead>
                                <tr>
                                    <th scope="col"><?= __('Sl No.'); ?></th>
                                    <th scope="col"><?= __('Item Name'); ?></th>
                                    <th scope="col"><?= __('SKU'); ?></th>
                                    <th scope="col"><?= __('UOM'); ?></th>
                                    <th scope="col"><?= __('Request Qty'); ?></th>
                                    <th scope="col"><?= __('Issued Qty'); ?></th>
                                    <th scope="col"><?= __('Price'); ?></th>
                                    <th scope="col"><?= __('Amount'); ?></th>
                                    <th scope="col"><?= __('Description'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count((array)$child_items) > 0) {
                                    $i = 1;
                                    foreach($child_items as $item) { ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $item['item_name'] ?? ''; ?></td>
                                            <td><?php echo $item['sku'] ?? ''; ?></td>
                                            <td><?php echo $item['uom_name'] ?? ''; ?></td>
                                            <td class="text-right"><?php echo $item['item_quantity'] ?? '0'; ?></td>
                                            <td class="text-right"><?php echo $item['issued_quantity'] ?? '0'; ?></td>
                                            <td class="text-right"><?php echo number_format($item['item_price'] ?? 0, 2); ?></td>
                                            <td class="text-right"><?php echo number_format($item['issued_amount'] ?? $item['total_amount'] ?? 0, 2); ?></td>
                                            <td><?php echo $item['description'] ?? ''; ?></td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted"><?= __('No child items found'); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script src="assets/js/custom_view_sub_contract_work_order_issues.js"></script>
<script>
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
</script>
