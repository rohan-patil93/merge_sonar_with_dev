<?php
$_SESSION['apply_js']['multiselest'] = "1";

if (isset($_POST['sbt_subcontractworkorders'])){
    $url = API . "?mode=insertsubcontractworkorders&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response2 = json_decode($response1, 1);
    $response = $response2['parent_insert_update'];

    if($response['status'] == 'success'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
            $_SESSION['insert_msg'] = $msgcode;
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
        }
        header("location:index?option=COM_LIST_SUBCONTRACT_WORK_ORDERS");
        exit;
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

$rid1 = isset($_GET['Rid']) ? $_GET['Rid'] : '';
$rid2 = $rid1 != '' ? encodeId($rid1) : '';
$rid3 = $rid2 != '' ? urldecode($rid2) : '';
$rrid = $rid3 != '' ? explode(":", $rid3) : array();
$rid = isset($rrid[1]) ? $rrid[1] : '';
$url = API."?mode=getsubcontractworkorders&rid=".$rid;
$apires = callAPI($url);
$result = json_decode($apires, 1);

if(!$result){
    $error_message = "Failed to load data. Please try again.";
    $vendors = array();
    $warehouses = array('OWN' => array(), 'VEN' => array());
    $items = array();
    $unit_of_measurements = array();
    $subcontract_work_order = array();
    $subcontract_work_order_items = array();
} else {
    $vendors = isset($result['vendors']) ? $result['vendors'] : array();
    $warehouses = isset($result['warehouses']) ? $result['warehouses'] : array('OWN' => array(), 'VEN' => array());
    $items = isset($result['items']) ? $result['items'] : array();
    $unit_of_measurements = isset($result['unit_of_measurements']) ? $result['unit_of_measurements'] : array();
    $subcontract_work_order = isset($result['subcontract_work_order']) ? $result['subcontract_work_order'] : array();
    $subcontract_work_order_items = isset($result['subcontract_work_order_items']) ? $result['subcontract_work_order_items'] : array();
    $itemworktype = $result['itemworktype'];
}

?>

<?php if ($error_message != '') { ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<script type="text/javascript" src="assets/js/custom_insert_subcontract_work_orders.js"></script>
<?php
    $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
    if(in_array($subcontract_work_order['current_status'], (array)$check_array)){
    ?>
      <?php
    }else{
        $disable = "disabled";
        $formdisable = "event_disabled";
    }

?>
<form method="POST" action="" id="subcontractWorkOrderForm" class="<?php echo $formdisable; ?>">
    <input type="hidden" name="rid" value="<?php echo $rid; ?>">
<div class="card">
        <?php
      if($subcontract_work_order['current_status'] !=""){
        $workflowsteps = getWorkflowStatus('subcontract_work_orders',$subcontract_work_order['id'],$subcontract_work_order['current_status']);
        echo $workflowsteps['current_status_ribbon'];
    }
        ?>
    <div class="card-header table-card-header">
        <h5> Subcontract Work Order</h5>
        <div class="card-header-right table-card-header ">
            <?php
                echo $workflowsteps['workflow_btn'];
            ?>

            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php  echo $workflowsteps['breadcrumb']; ?>
         <div class="row">
            <div class="form-group col-lg-4">
                <span class="form-label">Work Order No</span>
                <input aria-label="Transaction Number" type="text" class="form-control" name="transaction_number" value="<?php echo !empty($subcontract_work_order['transaction_number']) ? $subcontract_work_order['transaction_number'] : ''; ?>" readonly>
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Work Order Date<span class="text-danger">*</span></span>
                <input aria-label="Transaction Date" type="text" class="form-control datepicker" name="transaction_date" value="<?php echo $subcontract_work_order['transaction_date'] != "" ? $subcontract_work_order['transaction_date'] : fromDate(); ?>" required>
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Warehouse<span class="text-danger">*</span></span>
                <select aria-label="Request Warehouse" name="request_warehouse_id" class="form-control form-select js-example-basic-single" required>
                    <option value="">Select Any One </option>
                    <?php foreach($warehouses['OWN'] as $wareh){  ?>
                         <option value="<?php echo $wareh['id']; ?>" <?php echo $subcontract_work_order['request_warehouse_id'] == $wareh['id']?"selected":""; ?>><?php echo $wareh['warehouse_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Sub Contractor <span class="text-danger">*</span></span>
                <select aria-label="Party" name="party_id" class="form-control form-select js-example-basic-single OnChangeSelectpaty" required>
                    <option value="">Select Any One </option>
                    <?php foreach($vendors as $ven){  ?>
                         <option value="<?php echo $ven['id']; ?>" <?php echo $subcontract_work_order['party_id'] == $ven['id']?"selected":""; ?>><?php echo $ven['company']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-lg-4">
                <span class="form-label">Sub Contractor Warehouse<span class="text-danger">*</span></span>
                <select aria-label="Party Warehouse" name="party_warehouse_id" id="party_warehouse_id" class="form-control form-select js-example-basic-single" required>
                    <option value="">Select Any One </option>
                    <?php foreach($warehouses['VEN'] as $wareh){  ?>
                         <option value="<?php echo $wareh['id']; ?>" <?php echo $subcontract_work_order['party_warehouse_id'] == $wareh['id']?"selected":""; ?>><?php echo $wareh['warehouse_name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4 w-full">
                <span  class="form-label d-block">Product <span class="text-danger">*</span></span>
                <select aria-label="Product" class="js-example-basic-single selectWorkoOderItemID" name="product_id" id="product_id"   required>
                    <option value="">Select Any one</option>
                    <?php
                        foreach ($items as $item){
                            if($item['stocking_type'] == "F" || $item['stocking_type'] == "W"){
                                $item_price = isset($item['purchase_price'])  ? round((float)$item['purchase_price'], 2) : 0;
                                ?>
                                <option value="<?php echo $item['id']; ?>" data-price="<?php echo $item_price; ?>" <?php echo $subcontract_work_order['product_id'] == $item['id']?"selected":""; ?>><?php echo $item['sku']." - ".$item['item_name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-4 form-group ">
                <span class="form-label">Quantity <span class="text-danger">*</span></span>
                <input aria-label="Work Order Item Qty" type="number" class="form-control text-end" id="work_order_item_qty" name="work_order_item_qty" min="1"
                       value="<?php echo $subcontract_work_order['work_order_item_qty'] != "" ? $subcontract_work_order['work_order_item_qty'] : 1; ?>"  required>
            </div>
            <div class="col-md-4 form-group">
                <span class="form-label">Price <span class="text-danger">*</span></span>
                <input aria-label="Work Order Item Price" type="number" class="form-control text-end" id="work_order_item_price" name="work_order_item_price" min="0" step="0.01"
                       value="<?php echo $subcontract_work_order['work_order_item_price'] != "" ? $subcontract_work_order['work_order_item_price'] : 0; ?>" required>
            </div>
            <div class="col-md-4 form-group">
                <span class="form-label">Total Amount</span>
                <input aria-label="Work Order Total Amount" type="number" class="form-control text-end" id="work_order_total_amount" name="work_order_total_amount" min="0" step="0.01"
                       value="<?php echo $subcontract_work_order['work_order_total_amount'] != "" ? $subcontract_work_order['work_order_total_amount'] : 0; ?>" readonly>
            </div>

            <div class="col-md-4 form-group w-full">
                <span class="form-label d-block"> BOM </span>
                <select aria-label="Manufacturing Bom" class="js-example-basic-single item-selection selectmanufacturingBOM" name="manufacturing_bom_id" id="manufacturing_bom_id" >
                    <option value="">Select Any one</option>
                </select>
            </div>


        </div>

          <div class="row" id="Manufacturing_Work_Orders_ajax">
            <?php include_once 'custom_insert_subcontract_work_order_ajax.php'; ?>
            </div>

        <div class="row mt-4">
            <div class="col-md-12 text-left">
                <button type="submit" class="btn btn-primary" name="sbt_subcontractworkorders" <?php echo $disable; ?>>
                    <i class="la la-check"></i> Submit
                </button>
            </div>
        </div>
    </div>
</div>
</form>

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
