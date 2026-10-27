<?php
$_SESSION['apply_js']['multiselest'] = "1";
if (isset($_POST['kitting_name'])) {
    // Collect form data
    $rid = $_POST['rid'];
    $kitting_name = $_POST['kitting_name'];
    $parent_item_id = $_POST['parent_item_id'];
    $parent_quantity = $_POST['parent_quantity'];
    $transaction_date = $_POST['transaction_date'];
    $parent_item_day_rate = $_POST['parent_item_day_rate'];
    $component_items = $_POST['component_items'];
    $component_uoms = $_POST['component_uoms'];
    $component_quantities = $_POST['component_quantities'];
    $component_items_ids = $_POST['component_items_ids'];

    $parent_item_total = $_POST['parent_item_total'];
    $parent_total = $_POST['parent_total'];
    $child_total = $_POST['child_total'];
    $suspense_total = $_POST['suspense_total'];

    $component_day_rate = $_POST['component_day_rate'];
    $component_total = $_POST['component_total'];

    // Prepare components as an array of objects
    $components = [];
    for ($i = 0; $i < count($component_items); $i++) {
        $components[] = [
            'item_id' => $component_items[$i],
            'uom_id' => $component_uoms[$i],
            'quantity' => $component_quantities[$i],
            'component_day_rate' => $component_day_rate[$i],
            'component_total' => $component_total[$i],
            'component_items_ids' => $component_items_ids[$i]
        ];
    }
    // Prepare data to send to API
    $postData = [
        'rid' => $rid,
        'kitting_name' => $kitting_name,
        'parent_item_id' => $parent_item_id,
        'parent_quantity' => $parent_quantity,
        'parent_item_day_rate' => $parent_item_day_rate,
        'parent_item_total' => $parent_item_total,
        'parent_total' => $parent_total,
        'child_total' => $child_total,
        'suspense_total' => $suspense_total,
        'transaction_date' => $transaction_date,
        'components' => $components
    ];
    $url = API . "?mode=insertkittings&method=post";
    $response1 = file_post_contents($url, $postData, 'insert');
    $response2 = json_decode($response1, 1);
    $response = $response2['parent_insert_update'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_KITTINGS");
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

?>

<?php

$rid1 = $_GET['Rid'];
$rid = encodeId($rid1);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$rid = $rid['1'];
$url3 = API . "?mode=getkittings&method=get&rid=" . $rid;
$che3 = callAPI($url3);
$return = json_decode($che3, 1);
$items = $return['item'] ?? $return;
$uom = $return['uom'] ?? $return;
$kitting = $return['kittings'] ?? [];
$kittingItems = $return['items'] ?? [];
$hasExistingData = !empty($kitting) && !empty($kittingItems);
function findItemName($id, $items) {
    $item = $item ?? [];
    foreach ($items as $item) {
        if ($item['id'] == $id) {
            return $item['item_name'];
        }
    }
    return 'Unknown Item';
}
function findUomName($id, $uoms) {
    $uom_item = $uom_item ?? [];
    foreach ($uoms as $uom_item) {
        if ($uom_item['id'] == $id) {
            return $uom_item['uom_short_code'];
        }
    }
    return 'Unknown UOM';
}
?>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<?php if($component['suspense_total'] !='0' && $rid!=""){ ?>
<div class="alert alert-danger alert-dismissible">
    <strong><?php echo $_SESSION['error_message']['9179']; ?></strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php } ?>
<?php
if($kitting['current_status'] !=""){
    $workflowsteps = getWorkflowStatus('kittings',$rid,$kitting['current_status']);
}
?>

<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <?php echo $workflowsteps['current_status_ribbon']; ?>
    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Kitting Process</h5>
        <div class="card-header-right">
        <?php echo $workflowsteps['workflow_btn']; ?>
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class=" m-l-50 m-r-50">
    <?php echo $workflowsteps['breadcrumb']; ?>
    </div>
    <div class="card-body m-l-50 m-r-50">

    <?php
            $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,'');
            if(in_array($kitting['current_status'], (array)$check_array) || ($kitting['current_status'] == ST_CALLBACK && $kitting['originator'] ==$_SESSION['userid'])){
                    $disable = "";
            }else{
                    $disable = "disabled";
                    $dyncss = "style='pointer-events: none; opacity: 0.7;'";
            }

            ?>
        <form class="kt-form" id="kittingForm" method="post" action="" name="kittingForm" <?php echo $dyncss; ?>>

            <input type="hidden" name="rid" value="<?=$rid?>">
            <div class="form-group row" >
                <div class="col-md-6 mb-3">
                    <label for="kitting_name" class="form-label">Kitting Process Name <span class="text-danger">*</span></label>
                    <input aria-label="Kitting Name" type="text" class="form-control" id="process_name" name="kitting_name"
                           value="<?= $hasExistingData ? $kitting['kitting_name'] : '' ?>" required>
                </div>
                <div class="col-md-6 mb-3 w-full">
                    <label for="final_item_name" class="form-label d-block">Final Product Name <span class="text-danger">*</span></label>
                    <select class="js-example-basic-single" id="final_item_name" name="parent_item_id" onchange="updateItemOptions()" required>
                        <option value="">Select Any one</option>
                        <?php foreach ($items as $item){
                            if($item['sold_in_groups'] == 0){ ?>
                            <option value="<?= $item['id'] ?>" <?= ($hasExistingData && $kitting['parent_item_id'] == $item['id']) ? 'selected' : '' ?>>
                                <?= $item['sku']." - ".$item['item_name'] ?>
                            </option>
                        <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="quantity" class="form-label">Final Product Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="quantity" name="parent_quantity" min="1"
                           value="<?= $hasExistingData ? $kitting['parent_quantity'] : '1' ?>"  onblur="updateCalculation()" required>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="parent_item_day_rate" class="form-label">Day Rate <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="parent_item_day_rate" name="parent_item_day_rate" min="0" step="any" value="<?= $hasExistingData ? $kitting['parent_item_day_rate'] : '0' ?>" onblur="updateCalculation()" readonly>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="parent_item_total" class="form-label">Total <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="parent_item_total" name="parent_item_total" min="0" step="any" value="<?= $hasExistingData ? $kitting['parent_item_total'] : '0' ?>" readonly readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="date" class="form-label">Kitting Date <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control datepicker" id="date" name="transaction_date"
                           value="<?php echo fromDate($kitting['transaction_date']); ?>" required>
                </div>
            </div>

            <hr class="my-4">

            <!-- Components Section -->
            <div class="components-section " <?php if($kitting['parent_item_id'] ==""){ ?>style="display: none;" <?php
} ?>>
                <h6 class="mb-3">Components to Kit</h6>

                <div class="table-responsive">
                    <table class="table table-bordered" id="components-table">
                        <thead class="bg-light">
                            <tr>
                                <th scope="col" style="width: 50%;">Component Item Name</th>
                                <th scope="col" style="width: 10%;">Component UOM</th>
                                <th scope="col" style="width: 10%;">Component Quantity</th>
                                <th scope="col" style="width: 10%;">Day Rate</th>
                                <th scope="col" style="width: 10%;">Total</th>
                                <th scope="col" style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($hasExistingData && !empty($kittingItems)): ?>
                            <?php foreach ($kittingItems as $index => $component): ?>
                            <tr class="item-row">
                                <input type="hidden" class="form-control" name="component_items_ids[]" value="<?= $component['id'] ?>" >
                                <td class="w-full">
                                    <select aria-label="Component Items[]" class="js-example-basic-single component-item" name="component_items[]" required onchange="setUomForRow(this.closest('tr')); updateItemOptions()">
                                        <option value="">Select Any one</option>
                                        <?php foreach ($items as $item){
                                            if($item['sold_individually'] == '0'){
                                                ?>
                                            <option value="<?= $item['id'] ?>" <?= ($component['item_id'] == $item['id']) ? 'selected' : '' ?>>
                                                <?= $item['sku']." - ".$item['item_name'] ?>
                                            </option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select aria-label="Component Uoms[]" class="js-example-basic-single component-uom" name="component_uoms[]" required>
                                        <option value="">Select Any one</option>
                                        <?php foreach ($uom as $value): ?>
                                            <option value="<?= $value['id'] ?>" <?= ($component['uom_id'] == $value['id']) ? 'selected' : '' ?>>
                                                <?= $value['uom_short_code'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input aria-label="Component Quantities[]" type="number" class="form-control component-quantity" name="component_quantities[]" min="0" step="any" value="<?= $component['quantity'] ?>" onblur="updateCalculation()" required>
                                </td>
                                <td>
                                    <input aria-label="Component Day Rate[]" type="number" class="form-control component-day-rate" name="component_day_rate[]" min="0" step="any" value="<?= $component['component_day_rate'] ?>" onblur="updateCalculation()" required>
                                </td>
                                <td>
                                    <input aria-label="Component Total[]" type="number" class="form-control component-total" name="component_total[]" min="0" step="any" value="<?= $component['component_total'] ?>" readonly required>
                                </td>
                                <td class="text-center">
                                    <i class="fa fa-trash text-danger delete-row"></i>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr class="item-row">
                                <td class="w-full">
                                    <select aria-label="Component Items[]" class="js-example-basic-single component-item" name="component_items[]" required onchange="setUomForRow(this.closest('tr')); updateItemOptions()">
                                        <option value="">Select Any one</option>
                                        <?php foreach ($items as $item){
                                        if($item['sold_individually'] == '0'){
                                         ?>
                                            <option value="<?= $item['id'] ?>"><?= $item['sku']." - ".$item['item_name'] ?></option>
                                        <?php } ?>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select aria-label="Component Uoms[]" class="js-example-basic-single component-uom" name="component_uoms[]" required>
                                        <option value="">Select Any one</option>
                                        <?php foreach ($uom as $value): ?>
                                            <option value="<?= $value['id'] ?>">
                                                <?= $value['uom_short_code'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <input aria-label="Component Quantities[]" type="number" class="form-control component-quantity" name="component_quantities[]" min="0" step="any" value="1" onblur="updateCalculation()" required>
                                </td>
                                <td>
                                    <input aria-label="Component Day Rate[]" type="number" class="form-control component-day-rate" name="component_day_rate[]" min="0" step="any" value="<?= $component['component_day_rate'] ?>" onblur="updateCalculation()" required>
                                </td>
                                <td>
                                    <input aria-label="Component Total[]" type="number" class="form-control component-total" name="component_total[]" min="0" step="any" value="<?= $component['component_total'] ?>" readonly required>
                                </td>
                                <td class="text-center">
                                    <i class="fa fa-trash text-danger delete-row"></i>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <button type="button" id="add-row-btn" class="btn btn-sm btn-primary">
                            <i class="fa fa-plus mr-1"></i> Add Component
                        </button>
                        <div id="error-message" class="text-danger mt-2" style="display: none;">
                            Please fill all fields in the existing row before adding a new one.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-lg-4">
                                <label for="date" class="form-label">Parent Total </label>
                                <input aria-label="Parent Total" type="number" class="form-control" id="parent_total" name="parent_total" min="0" value="<?= $component['parent_total'] ?>" readonly required>
                            </div>
                            <div class="col-lg-4">
                                <label for="date" class="form-label">Child Total </label>
                                <input aria-label="Child Total" type="number" class="form-control" id="child_total" name="child_total" min="0" value="<?= $component['child_total'] ?>" readonly required>
                            </div>
                            <div class="col-lg-4">
                                <label for="date" class="form-label">Suspense Amount </label>
                                <input aria-label="Suspense Total" type="number" class="form-control" id="suspense_total" name="suspense_total" min="0" value="<?= $component['suspense_total']; ?>" readonly required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button type="submit" class="btn btn-primary" <?php echo $disable; ?>>
                        <i class="la la-check"></i> Submit
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>
<script>
    function get_item_name(){
        var final_item_name = $("#final_item_name").val();
        console.log(final_item_name);
    }
    var itemsData = <?php echo json_encode($items); ?>;
    function updateParentAmount(par) {
        if(par != 'autoload'){
            var finalItemId = document.getElementById('final_item_name').value;
            var parentAmountInput = document.getElementById('parent_item_day_rate');
            var item = itemsData.find(function(i) {
                return i.id == finalItemId;
            });
            if (item && item.day_rate) {
            } else {
            }
        }
        updateCalculation();
    }
    function setUomForRow(row) {
        console.log('setUomForRow called for row:', row);
        var selectItem = row.querySelector('.component-item');
        console.log('component-item select element:', selectItem);
        var selectUom = row.querySelector('.component-uom');
        var itemId = selectItem.value;
        console.log('Selected itemId:', itemId);
        var item = itemsData.find(function(i) { return i.id == itemId; });
        console.log('Matched item data:', item);
        if (item) {
            console.log('Setting UOM to:', item.purchase_uom);
            selectUom.value = item.purchase_uom;
            if (typeof $ !== 'undefined' && $(selectUom).data('select2')) {
                $(selectUom).val(item.purchase_uom).trigger('change');
                console.log('Triggered change on UOM select via Select2');
            }
            console.log('its working - 1');
            var amountInput = row.querySelector('.component-day-rate');
            if (amountInput && item.day_rate) {
                console.log('sale prices - '+item.day_rate);
                amountInput.value = item.day_rate;
            }
            updateCalculation();

        } else {
            console.warn('setUomForRow: no purchase_uom for selected item or item not found');
        }
    }

    function updateItemOptions(par="") {
        var selects = document.querySelectorAll('.component-item');
        var selectedValues = Array.from(selects).map(function(sel) { return sel.value; }).filter(function(val) { return val; });


        var finalProductValue = document.getElementById('final_item_name').value;
        console.log(finalProductValue);
        if(finalProductValue !=""){
            $(".components-section").show('slow');
        }else{
            $(".components-section").hide('slow');
        }
        if (finalProductValue) selectedValues.push(finalProductValue);

        selects.forEach(function(sel) {
            console.log(sel);
            var currentValue = sel.value;
            Array.from(sel.options).forEach(function(opt) {
                if (!opt.value) {
                    // allow placeholder
                    opt.disabled = false;
                } else if (opt.value !== currentValue && selectedValues.includes(opt.value)) {
                    opt.disabled = true;
                } else {
                    opt.disabled = false;
                }
            });
        });

        updateParentAmount(par);

    }

    // Attach change listener to all existing component-item selects
    document.querySelectorAll('.component-item').forEach(function(sel) {
        sel.addEventListener('change', function() {
            var row = sel.closest('tr');
            setUomForRow(row);
            updateItemOptions();
        });
    });
window.addEventListener("load", function() {
    updateItemOptions('autoload');
});

    document.getElementById('final_item_name').addEventListener('change', function () {
        updateItemOptions();
        updateParentAmount(); // NEW
    });

    document.getElementById('components-table').addEventListener('change', function (e) {
        if (e.target && e.target.classList.contains('component-item')) {
            var row = e.target.closest('tr');
            console.log('Delegated change for component-item in row:', row);
            setUomForRow(row);
            updateItemOptions();
        }
    });

    if (!document.getElementById('date').value) {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
    }

    // Add row functionality
    document.getElementById('add-row-btn').addEventListener('click', function() {
        const rows = document.querySelectorAll('.item-row');
        let allFilled = true;

        rows.forEach(row => {
            const item = row.querySelector('.component-item').value;
            const uom = row.querySelector('.component-uom').value;
            const quantity = row.querySelector('.component-quantity').value;
            const day_rate = row.querySelector('.component-day-rate').value;
            if (!item || !uom || !quantity || !day_rate) {
                allFilled = false;
            }
        });

        if (!allFilled) {
            document.getElementById('error-message').style.display = 'block';
            return;
        }

        document.getElementById('error-message').style.display = 'none';

        // Get a reference to the table body
        const tbody = document.querySelector('#components-table tbody');

        const newRow = document.createElement('tr');
        newRow.className = 'item-row w-full';

        const itemCell = document.createElement('td');
        let itemOptionsHtml = '<option value="">Select Any one</option>';

        itemsData.forEach(function(item) {
            itemOptionsHtml += `<option value="${item.id}">`+item.sku+` - `+item.item_name+`</option>`;
        });

        itemCell.innerHTML = `
            <select aria-label="Component Items[]" class="js-example-basic-single component-item" name="component_items[]" required onchange="setUomForRow(this.closest('tr')); updateItemOptions()">
                ${itemOptionsHtml}
            </select>
        `;

        // Create the UOM cell with dropdown options
        const uomCell = document.createElement('td');
        let uomOptionsHtml = '<option value="">Select Any one</option>';

        const uomOptions = Array.from(document.querySelector('.component-uom').options);
        uomOptions.forEach(function(option) {
            if (option.value) { // Skip the empty option
                uomOptionsHtml += `<option value="${option.value}">${option.textContent}</option>`;
            }
        });

        uomCell.innerHTML = `
            <select aria-label="Component Uoms[]" class="js-example-basic-single component-uom" name="component_uoms[]" required>
                ${uomOptionsHtml}
            </select>
        `;

        // Create the quantity cell
        const quantityCell = document.createElement('td');
        quantityCell.innerHTML = `<input aria-label="Component Quantities[]" type="number" class="form-control component-quantity" name="component_quantities[]" min="1" value="1" onblur="updateCalculation()" required>`;

        const dayrateCell = document.createElement('td');
        dayrateCell.innerHTML = `<td>
            <input aria-label="Component Day Rate[]" type="number" class="form-control component-day-rate" name="component_day_rate[]" min="0" step="any" value="0" onblur="updateCalculation()" required>
        </td>`;
        const totalcompCell = document.createElement('td');
        totalcompCell.innerHTML = `<td>
            <input aria-label="Component Total[]" type="number" class="form-control component-total" name="component_total[]" min="0" step="any" value="0" readonly required>
        </td>`;

        // Create the action cell
        const actionCell = document.createElement('td');
        actionCell.className = 'text-center';
        actionCell.innerHTML = `<i class="fa fa-trash text-danger delete-row"></i>`;

        // Add all cells to the row
        newRow.appendChild(itemCell);
        newRow.appendChild(uomCell);
        newRow.appendChild(quantityCell);
        newRow.appendChild(dayrateCell);
        newRow.appendChild(totalcompCell);
        newRow.appendChild(actionCell);

        // Add the row to the table
        tbody.appendChild(newRow);

        $(newRow).find('.js-example-basic-single').select2();

        // Add event listeners
        newRow.querySelector('.delete-row').addEventListener('click', function () {
            if (tbody.querySelectorAll('tr').length > 1) {
                this.closest('tr').remove();
                updateItemOptions();
            }
        });

        updateItemOptions();
    });

    document.querySelectorAll('.delete-row').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const tbody = document.querySelector('#components-table tbody');
            if (tbody.querySelectorAll('tr').length > 1) {
                this.closest('tr').remove();
                updateItemOptions();
            }
        });
    });

function updateCalculation(){
    var parent_item_day_rate = $('#parent_item_day_rate').val();
    var quantity1 = $('#quantity').val();
    quantity1 = (quantity1!="" || quantity1!="0")?quantity1:1;

    const rows = document.querySelectorAll('.item-row');
    var child_total = 0;
    rows.forEach(row => {
        const item = row.querySelector('.component-item').value;
        const uom = row.querySelector('.component-uom').value;
        const quantity = row.querySelector('.component-quantity').value;
        const day_rate = row.querySelector('.component-day-rate').value;
        var componenttotal = row.querySelector('.component-total');
        componenttotal.value = day_rate*quantity;
        child_total += day_rate*quantity;
    });

    $('#parent_total').val(child_total);
    $('#child_total').val(child_total);
    $('#suspense_total').val(0);
    $('#parent_item_day_rate').val(child_total/quantity1);
    $('#parent_item_total').val(child_total);

}

function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#popup1").html(loader);
    $('#kt_modal_KTDatatable_remote').modal("show");
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
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
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                console.log(response);
                var res = JSON.parse(response);
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
