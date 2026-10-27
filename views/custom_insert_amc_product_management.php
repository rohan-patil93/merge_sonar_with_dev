<?php
$_SESSION['apply_js']['multiselest'] = 1;
$rid = "";

if (isset($_POST['submit'])) {
    $existingImage = $_POST['existing_image'] ?? '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $newImageName = $_FILES['image']['name'];
    } else {
        $newImageName = $existingImage;
    }
    $exp_date = str_replace('/"', '-', $_POST['exp_date']);
    $exp_date =  date('Y-m-d', strtotime($exp_date));
    $mfg_date = str_replace('/"', '-', $_POST['mfg_date']);
    $mfg_date =  date('Y-m-d', strtotime($mfg_date));
    $_POST['exp_date'] = $exp_date;
    $_POST['mfg_date'] = $mfg_date;
    $_POST['image'] = $newImageName;
    $table_name = 'amc_product_management';
    if ($_POST['submit'] == 'insert') {
        unset($_POST['submit']);
        $_POST['current_status'] = '0';
        $_POST['status_date'] = nowDate();
        $_POST['company_id'] = $_SESSION['company_id'];
        $_POST['created_date'] = nowDate();
        $_POST['created_by'] = $_SESSION['userid'];
        $url = API . "?mode=adddata&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
    }
    if ($_POST['submit'] == 'update') {
        unset($_POST['submit']);
        unset($_POST['existing_image']);
        $rid = $_POST['rid'];
        unset($_POST['rid']);
        $_POST['updated_date'] = nowDate();
        $_POST['updated_by'] = $_SESSION['userid'];
        $url = API . "?mode=updaterowtabledata&rid=" . $rid . "&table=" . $table_name . "&method=post";
        $response = file_post_contents($url, $_POST, 'update');
    }
    if ($response != ''){
         header("location:index.php?option=COM_LIST_AMC_PRODUCT_MANAGEMENT"); exit; 
    }
    // echo "<pre>"; print_r($response);
}
$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url = API . "?mode=getproductdetails&id=" . $rid;
$all_details_response = callAPI($url);
$all_details_response = json_decode($all_details_response, 1);
$gc_details = $ts_details = $buildings = $pm_details = array();
$gc_details = $all_details_response['gc_details'];
$ts_details = $all_details_response['ts_details'];
$buildings = $all_details_response['buildings'];
$manage_stock = $units = $item_type = array();
foreach ($gc_details as $row) {
    if ($row['genc_code'] == 'YESNO') {
        $manage_stock[] = $row;
    }
    if ($row['genc_code'] == 'UNIT') {
        $units[] = $row;
    }
    if ($row['genc_code'] == 'ITEMTYPE') {
        $item_type[] = $row;
    }
}
if ($rid != '') {
    $pm_details = $all_details_response['pm_details'];
}


?>
<div class="pcoded-inner-content">
    <div class="main-body">
        <div class="page-wrapper">
            <!-- Page body start -->
            <div class="page-body">
                <!-- Pradeep HTML Start here -->
                <div class="card">
                    <div class="card-header">
                        <h4>Add Product</h4>
                        <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                </div>
                <form method="post" action="" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-exclamation-circle m-r-5"></i> Product Informations</h5>
                                </div>

                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Product/Item Name</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="product_name" value="<?php echo $pm_details['product_name'] ?? ""; ?>" class="form-control" type="text" placeholder="Enter Product Name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Product Description</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <textarea name="product_description" class="form-control" placeholder="Product Description"> <?php echo $pm_details['product_description'] ?? ""; ?> </textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Product Part No/Short Code</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="product_part_no" value="<?php echo $pm_details['product_part_no'] ?? ""; ?>" class="form-control" type="text" placeholder="Enter Product Short Code">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">HSN/SAC Code</label>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="hsn_code" class="form-control js-example-basic-single">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($ts_details as $ts): ?>
                                                        <option value="<?php echo $ts['id']; ?>" <?php echo $pm_details['hsn_code'] == $ts['id'] ? 'selected' : ''; ?>>
                                                            <?php echo $ts['code']; ?>
                                                        </option>
                                                    <?php endforeach; ?>

                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Purchase Price</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="purchase_price" value="<?php echo $pm_details['purchase_price'] ?? ""; ?>" class="form-control" type="number" placeholder="Enter Purchase Price">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Sale Price</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="sale_price" value="<?php echo $pm_details['sale_price'] ?? ""; ?>" class="form-control" type="number" placeholder="Enter Sale Price">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">MRP</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="mrp" value="<?php echo $pm_details['mrp'] ?? ""; ?>" class="form-control" type="number" placeholder="Enter MRP">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Primary Unit</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="primary_unit" class="form-control js-example-basic-single">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($units as $row): ?>
                                                        <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $pm_details['primary_unit'] == $row['genc_detail_code'] ? 'selected' : ''; ?>>
                                                            <?php echo $row['genc_detail_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Alternative Unit</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="alternative_unit" class="form-control js-example-basic-single">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($units as $row): ?>
                                                        <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $pm_details['alternative_unit'] == $row['genc_detail_code'] ? 'selected' : ''; ?>>
                                                            <?php echo $row['genc_detail_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Convertion Factor</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <input name="convertion_factor" value="<?php echo $pm_details['convertion_factor'] ?? ""; ?>" type="number" class="form-control" type="text" placeholder="1">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer border-top">
                                    <div class="text-center">
                                        <?php if ($rid != "") {  ?>
                                            <button name='submit' value='update' class="btn waves-effect waves-light btn-primary">
                                                <i class="fa fa-file-text-o"></i> Update Product
                                            </button>
                                            <input name="rid" type="hidden" value="<?php echo $pm_details['id'] != "" ? $pm_details['id'] : ""; ?>">
                                            <input type="hidden" name="existing_image" value="<?php echo $pm_details['image'] ?? ''; ?>">
                                        <?php } else {  ?>
                                            <button name='submit' value='insert' class="btn waves-effect waves-light btn-primary">
                                                <i class="fa fa-file-text-o"></i> Save Product
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fa fa-exclamation-circle m-r-5"></i> Additional Informations</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Manage Stock</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="manage_stock" class="form-control js-example-basic-single">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($manage_stock as $row): ?>
                                                        <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $pm_details['manage_stock'] == $row['genc_detail_code'] ? 'selected' : ''; ?>>
                                                            <?php echo $row['genc_detail_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Item Type</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="item_type" class="form-control js-example-basic-single">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($item_type as $row): ?>
                                                        <option value="<?php echo $row['genc_detail_code']; ?>" <?php echo $pm_details['item_type'] == $row['genc_detail_code'] ? 'selected' : ''; ?>>
                                                            <?php echo $row['genc_detail_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Building</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="building" id='building' class="form-control js-example-basic-single" onchange="buildingUpdate(this)" data-selected-floor="<?php echo $pm_details['building_floor']; ?>" data-selected-space="<?php echo $pm_details['building_floor_space']; ?>">
                                                    <option value="0">--Select--</option>
                                                    <?php foreach ($buildings as $row): ?>
                                                        <option value="<?php echo $row['id']; ?>" <?php echo $pm_details['building'] == $row['id'] ? 'selected' : ''; ?>>
                                                            <?php echo $row['building_name']; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Building Floor</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="building_floor" id='building_floor' class="form-control js-example-basic-single" onchange="buildingUpdate(this)">
                                                    <option value="0">--Select--</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Building Floor Space</label>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <select name="building_floor_space" id='building_floor_space' class="form-control js-example-basic-single">
                                                    <option value="0">--Select--</option>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Product Image</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="image" value="<?php echo $pm_details['image'] ?? ""; ?>" class="form-control" type="file" name="">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Min Sale Price</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="min_sale_price" value="<?php echo $pm_details['min_sale_price'] ?? ""; ?>" class="form-control" type="number" placeholder="Min Sale Price">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Bar Code</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="bar_code" value="<?php echo $pm_details['bar_code'] ?? ""; ?>" class="form-control" type="text" placeholder="Enter Bar Code">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Openning Stock Pack</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="openning_stock_pack" value="<?php echo $pm_details['openning_stock_pack'] ?? ""; ?>" class="form-control" type="number" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Openning Stock Loose</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="openning_stock_loose" value="<?php echo $pm_details['openning_stock_loose'] ?? ""; ?>" class="form-control" type="number" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Openning Stock Value</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="openning_stock_value" value="<?php echo $pm_details['openning_stock_value'] ?? ""; ?>" class="form-control" type="number" placeholder="0">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Batch</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="batch" value="<?php echo $pm_details['batch'] ?? ""; ?>" class="form-control" type="text" placeholder="Batch">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Mfg. Date</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="mfg_date" id="mfg_date" value="<?php echo fromDate($pm_details['mfg_date']);""; ?>" class="form-control datepicker" type="text" placeholder="10-10-2024" onchange="setMinExpDate()">
                                            </div>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <label class="form-label">Exp. Date</label>
                                        </div>
                                        <div class="col-lg-6 text-end">
                                            <div class="form-group">
                                                <input name="exp_date" id="exp_date" value="<?php echo fromDate($pm_details['exp_date']); ?>" class="form-control datepicker" type="text" placeholder="10-10-2024">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </from>
                    <!-- Pradeep HTML End here -->
            </div>
            <!-- Page body end -->
        </div>
    </div>
</div>
<script>
    function setMinExpDate() {
        var mfgDate = document.getElementById('mfg_date').value;
        if (mfgDate) {
            document.getElementById('exp_date').min = mfgDate;
        }
    }

    function buildingUpdate(selectElement) {
        const selectedValue = selectElement.value;
        const elementName = selectElement.name;

        const selectedFloor = document.getElementById('building').dataset.selectedFloor;
        const selectedSpace = document.getElementById('building').dataset.selectedSpace;

        let table_name, field;

        if (elementName === 'building') {
            table_name = 'building_floors';
            field = 'building_id';
        } else if (elementName === 'building_floor') {
            table_name = 'building_floor_spaces';
            field = 'building_floor_id';
        }

        jQuery.ajax({
            url: "ajax.php",
            data: `page=getselectedlist&id=${selectedValue}&field=${field}&table_name=${table_name}`,
            type: "POST",
            success: function(data) {
                if (data) {
                    const response = JSON.parse(data);
                    // console.log(response);

                    if (table_name === 'building_floors') {
                        const floorSelect = document.getElementById('building_floor');
                        floorSelect.innerHTML = '<option value="0">--Select--</option>';

                        response.forEach(function(row) {
                            const option = document.createElement('option');
                            option.value = row.id;
                            option.textContent = row.floor_name;

                            // Set the option as selected if it matches the initial state
                            if (row.id == selectedFloor) {
                                option.selected = true;
                            }

                            floorSelect.appendChild(option);
                        });

                        // Trigger change event to load spaces if floor was pre-selected
                        if (selectedFloor && elementName === 'building') {
                            floorSelect.dispatchEvent(new Event('change'));
                        }
                    }

                    if (table_name === 'building_floor_spaces') {
                        const spaceSelect = document.getElementById('building_floor_space');
                        spaceSelect.innerHTML = '<option value="0">--Select--</option>';

                        response.forEach(function(row) {
                            const option = document.createElement('option');
                            option.value = row.id;
                            option.textContent = row.cabin_name;

                            // Set the option as selected if it matches the initial city
                            if (row.id == selectedSpace) {

                                option.selected = true;
                            }

                            spaceSelect.appendChild(option);
                        });
                    }
                } else {
                    console.error("No data received.");
                }
            },
            error: function() {
                console.error("Error occurred while fetching data.");
            }
        });
    }

    // Initial trigger to load states based on selected country
    document.addEventListener("DOMContentLoaded", function() {
        const buildingSelect = document.getElementById('building');
        if (buildingSelect.value !== '0') {
            buildingUpdate(buildingSelect);
        }
    });
</script>