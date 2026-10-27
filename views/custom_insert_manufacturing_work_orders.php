<?php
$_SESSION['apply_js']['multiselest'] = "1";
$toastMessage = '';
if (isset($_POST['btn_workorder'])) {
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $url = API . "?mode=insertmanufacturingworkorders&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response2 = json_decode($response1, 1);
    $res = $response2['manufacturing_work_orders'];
if($res['status'] == 'sucess'){
    if($res['LASTID'] == 0){
        $msgcode = 3001; //update
        $toastMessage = $_SESSION['error_message']['3001'] ?? '';
    }else{
        $msgcode = 3000; //insert
        $_SESSION['insert_msg'] = $msgcode;
        header("location:index?option=COM_LIST_MANUFACTURING_WORK_ORDERS");
    }
}else{
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
$rid1 = $_GET['Rid'];
$rid = encodeId($rid1);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$rid = $rid['1'];
$url3 = API . "?mode=getmanufacturingworkorders&method=get&rid=" . $rid;
$che3 = callAPI($url3);
$result = json_decode($che3, 1);

$manufacturing_work_orders = $result['manufacturing_work_orders'];
$manufacturing_work_order_items = $result['manufacturing_work_order_items'];
$manufacturing_work_order_operations = $result['manufacturing_work_order_operations'];

$manufacturing_firm_orders = $result['manufacturing_firm_orders'];
$items = $result['items'];
$manufacturing_boms = $result['manufacturing_boms'];
$manufacturing_operations = $result['manufacturing_operations'];
$unit_of_measurements = $result['unit_of_measurements'];
$manufacturing_boms_list = $result['manufacturing_boms_list'];
$manufacturing_bom_items = $result['manufacturing_work_order_items'];
$manufacturing_work_order_items_by_product = $result['manufacturing_work_order_items_by_product'];
$manufacturing_bom_operations = $result['manufacturing_work_order_operations'];
$itemworktype = $result['itemworktype'];
$vendors = $result['vendors'];
$warehouses = $result['warehouses'];

$uomNameById = array();
foreach ((array)$unit_of_measurements as $uom) {
    $uomNameById[$uom['id']] = $uom['uom_name'];
}
$itemUomById = array();
foreach ((array)$items as $item) {
    $itemUomById[$item['id']] = $uomNameById[$item['sale_uom']] ?? '';
}
$workOrderItemUom = $itemUomById[$manufacturing_work_orders['work_order_item_id'] ?? ''] ?? '';

?>
<link rel="stylesheet" href="assets/css/custom_insert_manufacturing_work_orders.css">

 <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<div class="page-body" data-config-name="<?= htmlspecialchars(ucwords($_SESSION['config']['name']), ENT_QUOTES, 'UTF-8'); ?>" data-error-9059="<?= htmlspecialchars($_SESSION['error_message']['9059'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" data-toast-message="<?= htmlspecialchars($toastMessage, ENT_QUOTES, 'UTF-8'); ?>">
<div class="card">
    <?php
      if($manufacturing_work_orders['current_status'] !=""){
        $workflowsteps = getWorkflowStatus('manufacturing_work_orders',$manufacturing_work_orders['id'],$manufacturing_work_orders['current_status']);
        echo $workflowsteps['current_status_ribbon'];
    }
        ?>
    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Manufacturing Work Orders</h5>
        <div class="card-header-right">
             <?php
                echo $workflowsteps['workflow_btn'];
            ?>
            <?php
                $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
                if(in_array($manufacturing_work_orders['current_status'], (array)$check_array)){
                ?>
                  <?php
                }else{
                    $disable = "disabled";
                    $formdisable = "event_disabled";
                }

            ?>

            <?php if ($rid != "" && $manufacturing_work_orders['current_status'] == 199) {
                $work_log_status = $manufacturing_work_orders['work_log_status'] ?? '';

                // Determine which buttons to show based on work_log_status

                $show_start = ($work_log_status === '' || $work_log_status === null);
                $show_hold = ($work_log_status === '0' || $work_log_status === 0);
                $show_resume = ($work_log_status === '1' || $work_log_status === 1);
                $show_complete_active = ($work_log_status === '0' || $work_log_status === 0 || $work_log_status === '1' || $work_log_status === 1);
                $show_complete_disabled = ($work_log_status === '199' || $work_log_status === 199);
            ?>
                <?php if ($show_start) { ?>
                    <button type="button" class="btn btn-success" id="production_start_btn" data-work-order-id="<?php echo htmlspecialchars($rid); ?>">
                        <i class="la la-play"></i> Start Production
                    </button>
                <?php } ?>

                <?php if ($show_hold) { ?>
                    <span class="badge bg-secondary  rounded-pill px-3 py-2">
                        In Progress
                    </span>
                    <button type="button" class="btn btn-warning" id="production_hold_btn" data-work-order-id="<?php echo htmlspecialchars($rid); ?>">
                        <i class="la la-pause"></i> Hold
                    </button>
                <?php } ?>

                <?php if ($show_resume) { ?>
                    <span class="badge bg-secondary  rounded-pill px-3 py-2">
                        Hold
                    </span>
                    <button type="button" class="btn btn-primary" id="production_resume_btn" data-work-order-id="<?php echo htmlspecialchars($rid); ?>">
                        <i class="la la-play-circle"></i> Resume
                    </button>
                <?php } ?>

                <?php if ($show_complete_active) { ?>
                    <button type="button" class="btn btn-info" id="production_complete_btn" data-work-order-id="<?php echo htmlspecialchars($rid); ?>">
                        <i class="la la-check-circle"></i> Complete
                    </button>
                <?php } ?>

                <?php if ($show_complete_disabled) { ?>
                    <button type="button" class="btn btn-success" disabled>
                        <i class="la la-check-circle"></i> Completed
                    </button>
                <?php } ?>
            <?php } ?>

            <a href="index?option=COM_LIST_MANUFACTURING_WORK_ORDERS" class="btn btn-light-primary waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <form method="post" action="" class="<?php echo $formdisable; ?>" name="kittingForm">
        <div class="card-body m-l-50 m-r-50">
            <input type="hidden" name="rid" value="<?php echo $rid; ?>">
            <?php  echo $workflowsteps['breadcrumb']; ?>
            <div class="row" >

                 <div class="col-md-4 form-group ">
                    <span class="form-label">Transaction No </span>
                    <input aria-label="Transaction No" type="text" class="form-control"  name="transaction_no" value="<?php echo $manufacturing_work_orders['transaction_no']; ?>"  readonly>
                </div>
                <div class="col-md-4 form-group ">
                    <span class="form-label">Transaction Date <span class="text-danger">*</span></span>
                    <input aria-label="Transaction Date" type="text" class="form-control datepicker" id="transaction_date" name="transaction_date" value="<?php echo fromDate($manufacturing_work_orders['transaction_date']); ?>" required>
                </div>
                <div class="col-md-4 form-group  w-full">
                    <label for="final_item_name" class="form-label d-block">Firm Order</label>
                    <select aria-label="Manufacturing Firm Order" class="js-example-basic-single item-selection js-manufacturing-firm-order" name="manufacturing_firm_order_id" id="manufacturing_firm_order_id" data-action="select-manufacturing-firm-order">
                        <option value="">Select Any one</option>
                        <?php foreach ($manufacturing_firm_orders as $mfo){ ?>
                            <?php $firmOrderUom = $mfo['uom_name'] ?? ($itemUomById[$mfo['product_id']] ?? ''); ?>
                            <option value="<?php echo $mfo['id']; ?>" data-uom="<?php echo htmlspecialchars($firmOrderUom, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $manufacturing_work_orders['manufacturing_firm_order_id'] == $mfo['id']?"selected":""; ?>><?php echo ($mfo['firm_order_no'] != '' ? $mfo['firm_order_no']." - " : "").$mfo['item_name']." - ".$mfo['year_month']." (".$mfo['firm_qty_1']." ".$firmOrderUom.")"; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-md-4 form-group w-full">
                    <span class="form-label d-block">Product <span class="text-danger">*</span></span>
                    <select aria-label="Work Order Item" class="js-example-basic-single item-selection js-work-order-item" name="work_order_item_id" id="work_order_item_id" data-action="select-work-order-item" required>
                        <option value="">Select Any one</option>
                        <?php foreach ($items as $item){ if($item['stocking_type'] == "F" || $item['stocking_type'] == "W"){ ?>
                            <?php $itemUomName = $uomNameById[$item['sale_uom']] ?? ''; ?>
                            <option value="<?php echo $item['id']; ?>" data-uom="<?php echo htmlspecialchars($itemUomName, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $manufacturing_work_orders['work_order_item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']." (".$itemworktype[$item['stocking_type']].")"; ?></option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4 form-group ">
                    <span class="form-label">Quantity <span class="text-danger">*</span></span>
                    <div class="input-group">
                        <input aria-label="Work Order Item Qty" type="number" class="form-control text-end" id="work_order_item_qty" name="work_order_item_qty" min="0.01" step="0.01"
                               value="<?php echo $manufacturing_work_orders['work_order_item_qty'] ?? 1; ?>" required>
                        <span class="input-group-text manufacturing-uom-addon" id="work_order_item_uom"><?php echo htmlspecialchars($workOrderItemUom != '' ? $workOrderItemUom : 'UOM'); ?></span>
                    </div>
                </div>
                <div class="col-md-4 form-group w-full">
                    <span class="form-label d-block"> BOM <span class="text-danger">*</span></span>
                    <select aria-label="Manufacturing Bom" class="js-example-basic-single item-selection js-manufacturing-bom" name="manufacturing_bom_id" id="manufacturing_bom_id" data-action="select-manufacturing-bom" required>
                        <option value="">Select Any one</option>
                        <?php $mk=1; foreach ($manufacturing_boms_list as $mbl){ ?>

                            <option value="<?php echo $mbl['id']; ?>" <?php echo $manufacturing_work_orders['manufacturing_bom_id'] == $mbl['id']?"selected":""; ?>>V<?php echo $mk." - ".$mbl['bom_quantity']." ".$mbl['uom_name']; ?></option>
                        <?php $mk++;
}  ?>
                    </select>
                </div>
                <div class="col-md-4 form-group ">
                    <span class="form-label"> Planned Start <span class="text-danger">*</span></span>
                    <input aria-label="Planned Start Date" type="text" class="form-control startdatepicker_1 rangedatepicker" data-id ="1" id="planned_start_date" name="planned_start_date" value="<?php echo fromDate($manufacturing_work_orders['planned_start_date']); ?>" required>
                </div>
                <div class="col-md-4 form-group ">
                    <span class="form-label"> Planned Due <span class="text-danger">*</span></span>
                    <input aria-label="Planned End Date" type="text" class="form-control enddatepicker_1" id="planned_end_date" name="planned_end_date" value="<?php echo fromDate($manufacturing_work_orders['planned_end_date']); ?>" required>
                </div>
                <div class="col-md-4 form-group w-full">
                    <span class="form-label d-block">Warehouse <span class="text-danger">*</span></span>
                    <select aria-label="Warehouse" class="js-example-basic-single" name="warehouse" id="warehouse" required>
                        <option value="">Select Warehouse</option>
                        <?php foreach ($warehouses as $warehouse){ ?>
                            <option value="<?php echo $warehouse['id']; ?>" <?php echo $manufacturing_work_orders['warehouse'] == $warehouse['id']?"selected":""; ?>><?php echo $warehouse['warehouse_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div class="row" id="Manufacturing_Work_Orders_ajax">
            <?php if($rid != ""){
             include_once 'custom_insert_manufacturing_work_orders_ajax_new.php';
             }?>
            </div>

            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button type="submit" class="btn btn-primary" name="btn_workorder" <?php echo $disable; ?>>
                        <i class="la la-check"></i> Submit
                    </button>

                </div>

            </div>

        </form>
    </div>

</div>

<!-- Production Modal -->
<div class="modal fade" id="productionModal" tabindex="-1" aria-labelledby="productionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content" id="productionModalContent">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<script src="assets/js/custom_insert_manufacturing_work_orders.js"></script>
