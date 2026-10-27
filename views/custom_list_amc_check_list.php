<?php
$_SESSION['apply_js']['multiselest'] = 1;

$url = API . "?mode=getallproductdetails";
$response = json_decode(callAPI($url), true)?? [];
$products = $response['pm_details'];
$customers = $response['cust_details'];
$ag_details = $response['ag_details'];
$gc_details = $response['gc_details'];
$buildings = $response['buildings'];

$payment_types = $services = [];
foreach ($gc_details as $row) {
    if ($row['genc_code'] == 'AMCPAYTYPE') { $payment_types[] = $row;
}
    if ($row['genc_code'] == 'AMCSERVICE') { $services[] = $row;
}
}

?>
<!-- Pradeep HTML Start here -->
<div class="card">
    <div class="card-header">
        <h4>AMC Check List</h4>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <!-- Product -->
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Product <span class="text-danger">*</span></span>
                    <select aria-label="Product" name="product_id" class="form-control js-example-basic-single" required>
                        <option value="">Select Any One</option>
                        <?php foreach ($products as $p) { ?>
                            <option value="<?php echo $p['id']; ?>" >
                                <?php echo htmlspecialchars($p['product_name']); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Service Name</span>
                    <select aria-label="Account Group" name="account_group_id" class="form-control js-example-basic-single" id="selectGroup">
                        <option value="">Select Any One</option>
                        <?php foreach ($ag_details as $k => $v) { ?>
                            <option value="<?php echo $k; ?>" >
                                <?php echo $v; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <!-- Customer -->
            <div class="col-md-4">
                <div class="form-group">
                    <span class="form-label">Customer </span>
                    <select aria-label="Customer" name="customer_id" class="form-control js-example-basic-single">
                        <option value="">Select Any One</option>
                        <?php foreach ($customers as $c) { ?>
                            <option value="<?php echo $c['id']; ?>" >
                                <?php echo $c['customer_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <span class="form-label">Building Name</span>
                    <select aria-label="Building" name="building_id" class="form-control js-example-basic-single"
                        id="selectBuilding" onchange="updateFloors(this)">
                        <option value="">Select Any One</option>
                        <?php foreach ($buildings as $row) { ?>
                            <option value="<?php echo $row['id']; ?>">
                                <?php echo $row['building_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Floor Dropdown -->
            <div class="col-md-3">
                <div class="form-group">
                    <span class="form-label">Floor </span>
                    <select aria-label="Building Floor" name="building_floor_id" class="form-control js-example-basic-single"
                        id="selectFloor" onchange="updateFloorSpaces(this)" disabled>
                        <option value="">Select Any One</option>
                    </select>
                </div>
            </div>

            <!-- Floor Spaces Dropdown -->
            <div class="col-md-3">
                <div class="form-group">
                    <span class="form-label">Floor Space </span>
                    <select aria-label="Building Floor Space" name="building_floor_space_id" class="form-control js-example-basic-single"
                        id="selectFloorSpace" disabled>
                        <option value="">Select Any One</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <span class="form-label">Date </span>
                    <input aria-label="Activation Date" type="date" name="activation_date" id="activationDate" class="form-control"
                        value="<?php echo date('Y-m-d'); ?>" />
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary mt-3" onclick="loadCheckListData()">Search</button>
    </div>
</div>

<div id="checkListTable"> Please Select At Least One Filter </div>

<script>
    function updateFloors(sel) {
        const floorSel = $('#selectFloor');
        const spaceSel = $('#selectFloorSpace');
        floorSel.empty().append('<option value="">Select Any One</option>').prop('disabled', true);
        spaceSel.empty().append('<option value="">Select Any One</option>').prop('disabled', true);
        if (!sel.value) return;

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'getselectedlist',
                id: sel.value,
                field: 'building_id',
                table_name: 'building_floors'
            },
            success: function(d) {
                if (d) {
                    JSON.parse(d).forEach(function(r) {
                        floorSel.append('<option value="' + r.id + '">' + r.floor_name + '</option>');
                    });
                    floorSel.prop('disabled', false);

                    <?php if ($isEdit && !empty($editData['building_floor_id'])) { ?>
                        floorSel.val("<?php echo $editData['building_floor_id']; ?>").trigger('change');
                        updateFloorSpaces(document.getElementById('selectFloor'), true);
                    <?php } ?>
                }
            }
        });
    }

    function updateFloorSpaces(sel, fromEdit = false) {
        const spaceSel = $('#selectFloorSpace');
        spaceSel.empty().append('<option value="">Select Any One</option>').prop('disabled', true);

        if (!sel.value) return;

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: {
                page: 'getselectedlist',
                id: sel.value,
                field: 'building_floor_id',
                table_name: 'building_floor_spaces'
            },
            success: function(d) {
                if (d) {
                    JSON.parse(d).forEach(function(r) {
                        spaceSel.append('<option value="' + r.id + '">' + r.cabin_name + '</option>');
                    });
                    spaceSel.prop('disabled', false);

                    <?php if ($isEdit && !empty($editData['building_floor_space_id'])) { ?>
                        spaceSel.val("<?php echo $editData['building_floor_space_id']; ?>");
                    <?php } ?>
                }
            }
        });
    }

    function loadCheckListData() {
        const productId = $('select[name="product_id"]').val();
        if(!productId){
            tostarnotify('top','right','fa fa-thumbs-down','form-label label label-danger',
                'animated flipInX','animated flipOutX','Please select Product','');
            return;
        }
        const accountGroupId = $('select[name="account_group_id"]').val();
        const customerId = $('select[name="customer_id"]').val();
        const buildingId = $('select[name="building_id"]').val();
        const buildingFloorId = $('select[name="building_floor_id"]').val();
        const buildingFloorSpaceId = $('select[name="building_floor_space_id"]').val();
        const activationDate = $('#activationDate').val();
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#checkListTable").html(loader);
        $.ajax({
            url: 'views/custom_list_amc_check_list_table.php',
            type: 'POST',
            data: {
                product_id: productId,
                account_group_id: accountGroupId,
                customer_id: customerId,
                building_id: buildingId,
                building_floor_id: buildingFloorId,
                building_floor_space_id: buildingFloorSpaceId,
                activation_date: activationDate
            },
            success: function(response) {
                $('#checkListTable').html(response);
            },
            error: function(xhr) {
                alert('Failed to load data');
            }
        });
    }
</script>
