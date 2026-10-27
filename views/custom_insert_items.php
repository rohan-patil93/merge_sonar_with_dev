<?php
$_SESSION['apply_js']['multiselest'] = 1;
if (isset($_POST['submititem'])) {
    unset($_POST['submititem']);
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $image = imageupload('all', 'item_image', $_POST['hid_item_image']);
    unset($_POST['hid_item_image']);
    $_POST['item_image'] = $image;
    $image = imageupload('all', 'item_image2', $_POST['hid_item_image2']);
    unset($_POST['hid_item_image2']);
    $_POST['item_image2'] = $image;
    $image = imageupload('all', 'item_image3', $_POST['hid_item_image3']);
    unset($_POST['hid_item_image3']);
    $_POST['item_image3'] = $image;
    $_POST['sold_individually'] = $_POST['sold_individually'] == "0" ? "0" : "1";
    $_POST['sold_in_groups'] = $_POST['sold_in_groups'] == "0" ? "0" : "1";
    $_POST['quality_check'] = $_POST['quality_check'] == "0" ? "0" : "1";
    $_POST['negative_stock'] = $_POST['negative_stock'] == "0" ? "0" : "1";
    $_POST['allow_back_order'] = $_POST['allow_back_order'] == "0" ? "0" : "1";


    foreach ($_POST['old_attachment'] as $key => $image) {
        $attachedfiles = array();
        $attachedfiles['attachment'] = $_POST['old_attachment'][$key];
        $attachedfiles['attachment_name'] = $_POST['old_attachment_name'][$key];
        $_POST['all_attachment'][] = $attachedfiles;
    }
    unset($_POST['old_attachment']);
    unset($_POST['old_attachment_name']);
    foreach ($_FILES['attachment']['name'] as $key => $image) {
        if ($_FILES['attachment']['error'][$key] != 4) {
            $temp = explode(".", basename($_FILES['attachment']['name'][$key]));
            $Filename = "file_" . time() . "_" . uniqid();
            $newfilename = $Filename . '.' . end($temp);
            $attachedfiles = array();
            $attachedfiles['attachment'] = $newfilename;
            $attachedfiles['attachment_name'] = $_POST['attachment_name'][$key] != "" ? $_POST['attachment_name'][$key] : "file_" . $key;
            $_POST['all_attachment'][] = $attachedfiles;
            move_uploaded_file($_FILES['attachment']['tmp_name'][$key], 'uploads/compliance_specification_documents/' . $newfilename);
        }
    }
    $url = API . "?mode=insertitems&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1, 1);
    $response = $res['result'];
    if ($response['status'] == 'sucess') {
        if ($response['LASTID'] == 0) {
            $msgcode = 3001; //update
        } else {
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_ITEMS");
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function () {
                if (document.readyState == "complete") {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX', '<?php echo $_SESSION['error_message'][$msgcode]; ?>', '');
                }
            }
        </script>
        <?php
    } else {
        if ($response['status'] == 'fail') {
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er[] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] . " : " . $_SESSION['error'][$response['error_no']] . " : " . implode(", ", (array) $dis_er);
        } else {
            $error_message = $response['message'];
        }
    }
}
$rid1 = $_GET['Rid'];
$rid2 = encodeId($rid1);
$rid3 = urldecode($rid2);
$rrid = explode(":", $rid3);
$rid = $rrid['1'];

$sku = $_SESSION['config']['sku_generators_id'];

$url = API . "?mode=getitems&rid=" . $rid;
$apires = callAPI($url);
$result = json_decode($apires, 1);
$customers = $result['customers'];
$vendors = $result['vendors'];
$items = $result['items'];
$vendor_items = $result['vendor_items'];
$customer_items = $result['customer_items'];
$display_attributes = $result['display_attributes'];
$lists = json_decode($display_attributes['list_attributes'], 1);

$locations = $result['locations'];
$location = array();
foreach ($locations as $lloc) {
    $location[$lloc['id']] = $lloc['location_name'];
}
$warehouses = $result['warehouses'];
$item_specification_fields = json_decode(urldecode($result['item_specification_fields']), 1);



$categroylist = $result['category'];
$parentcategries = $childcategories = array();
foreach($categroylist as $catlist){
    if($catlist['parent_item_category_id'] != '0'){
        $childcategories[$catlist['parent_item_category_id']][]  = $catlist;
    }else{
        $parentcategries[]  = $catlist;
    }
}
?>

<?php if ($error_message != '') { ?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong><?php echo $error_message; ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php } ?>

<div class="card">
    <div class="card-header table-card-header">
        <h5> Items</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <!--begin::Portlet-->



    <div class="card-body">
        <form method="POST" enctype="multipart/form-data" id="itemform">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header table-card-header">
                            <h5>Items</h5>
                            <div class="card-header-right table-card-header ">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="form-group col-lg-6">
                                    <span class="">Item Name&nbsp;<span class="text-danger">*</span></span>
                                    <span class="d-block error-message text-danger"></span>
                                    <input aria-label="Item Name" class="form-control requried_field" type="text" id="item_name"
                                        name="item_name"
                                        value="<?php echo $items['item_name'] != "" ? $items['item_name'] : $_GET['ITMNAME']; ?>">
                                </div>
                                <div class="form-group col-lg-3">
                                    <span class="">SKU</span>
                                    <input aria-label="Sku" class="form-control" type="text" id="sku" name="sku"
                                        value="<?php echo isset($items['sku']) ? $items['sku'] : ''; ?>" <?php echo !empty($sku) ? 'readonly' : ''; ?>>
                                </div>

                                <?php if ($lists['ref_sku']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-3">
                                        <span class="">Ref SKU</span>
                                        <input aria-label="Ref Sku" class="form-control" type="text" id="ref_sku" name="ref_sku"
                                            value="<?php echo $items['ref_sku'] ?>">
                                    </div>
                                <?php }
                                if ($lists['item_description']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Item Description</span>
                                        <textarea aria-label="Item Description" class="form-control" rows="5" cols="5" id="item_description"
                                            name="item_description"><?php echo $items['item_description'] ?></textarea>
                                    </div>
                                <?php }
                                if ($lists['specifications']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="">Specifications</span>
                                        <input aria-label="Specifications" class="form-control" type="text" id="specifications" name="specifications"
                                            value="<?php echo $items['specifications']; ?>">
                                    </div>
                                <?php }
                                if ($lists['category_id']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-4">
                                        <span class="form-label">Category <span class="text-danger">*</span></span>
                                        <span class="d-block error-message text-danger"></span>
                                        <select aria-label="Category" class="form-select border js-example-basic-single requried_field"
                                            id="category_id" name="category_id" onchange="setCategoryAccounts(this.value)">
                                            <option value="">Select Any one</option>
                                            <?php foreach ($parentcategries as $category) { ?>
                                                <option value="<?php echo $category['id']; ?>" <?php if ($items['category_id'] == $category['id'])
                                                       { echo 'selected';
} ?>>
                                                    <?php echo $category['category_description'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                                    <div class="form-group col-lg-4">
                                        <span class="form-label">Child Category</span>
                                        <select aria-label="Child Category" class="form-select border js-example-basic-single" id="child_category_id" name="child_category_id" onchange="setChildCategoryAccounts(this.value)">
                                            <option value="">Select Any One</option>
                                            <?php  foreach($childcategories[$items['category_id']] as $category) {  ?>
                                                <option value="<?php echo $category['id']; ?>" <?php if ($items['child_category_id'] == $category['id'])
                                                       { echo 'selected';
} ?>>
                                                    <?php echo $category['category_description'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <span class="form-label">Sub-Child Category</span>
                                        <select aria-label="Sub Child Category" class="form-select border js-example-basic-single" id="sub_child_category_id" name="sub_child_category_id">
                                            <option value="">Select Any One</option>
                                            <?php  foreach($childcategories[$items['child_category_id']] as $category) {  ?>
                                                <option value="<?php echo $category['id']; ?>" <?php if ($items['sub_child_category_id'] == $category['id'])
                                                       { echo 'selected';
} ?>>
                                                    <?php echo $category['category_description'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php if ($lists['item_type']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-4">
                                        <span class="form-label">Item Type </span>
                                        <select aria-label="Item Type" class="form-select border js-example-basic-single" id="item_type"
                                            name="item_type">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['item_types'] as $item_type) { ?>
                                                <option value="<?php echo $item_type['id']; ?>" <?php if ($items['item_type'] == $item_type['id']) {
                                                       echo "selected";
                                                   } ?>>
                                                    <?php echo $item_type['item_type_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['default_costing_type']['insert_display_list'] == 1) { ?>

                                    <div class="form-group col-lg-4">
                                        <span class="form-label">Costing Type</span>
                                        <select aria-label="Default Costing Type" class="form-select border js-example-basic-single" id="default_costing_type"
                                            name="default_costing_type">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['costing_type'] as $ctype) { ?>
                                                <option value="<?php echo $ctype['genc_detail_code']; ?>" <?php if ($items['default_costing_type'] == $ctype['genc_detail_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $ctype['genc_detail_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['sold_individually']['insert_display_list'] == 1) { ?>

                                    <div class="form-group col-lg-4">
                                        <span class="form-label d-block">Sold Individually</span>
                                        <input aria-label="Checkbox" type="checkbox" class="js-switch" <?php echo $items['sold_individually'] == "0" ? "checked" : ""; ?> name="sold_individually" value="0" />
                                    </div>
                                <?php }
                                if ($lists['sold_in_groups']['insert_display_list'] == 1) { ?>

                                    <div class="form-group col-lg-4">
                                        <span class="form-label d-block">Sold In Groups
                                        </span>
                                        <input aria-label="Sold In Groups" type="checkbox" class="js-switch" name="sold_in_groups" value="0" <?php echo $items['sold_in_groups'] == "0" ? "checked" : ""; ?> />
                                    </div>
                                <?php }
                                if ($lists['quality_check']['insert_display_list'] == 1) { ?>

                                    <div class="form-group col-lg-4">
                                        <span class="form-label d-block">Quality Check
                                        </span>
                                        <input aria-label="Quality Check" type="checkbox" class="js-switch" name="quality_check" value="0" <?php echo $items['quality_check'] == "0" ? "checked" : ""; ?> />
                                    </div>
                                <?php }
                                if ($lists['item_image']['insert_display_list'] == 1) { ?>

                                    <div class="form-group col-lg-4">
                                        <span class="form-label d-block">Item Image</span>
                                        <div class="profile-pic">
                                            <label class="-label" for="item_image">
                                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                            </label>
                                            <input type="file" name="item_image" id="item_image" accept=".png, .jpg, .jpeg"
                                                onchange="loadFile(event,this.id)" />
                                            <img src="<?php echo "uploads/all/" . $items['item_image']; ?>"
                                                id="outputitem_image" width="200"  alt="Attachment Preview" />
                                            <input type="hidden" name="hid_item_image"
                                                value="<?php echo $items['item_image']; ?>">
                                        </div>

                                    </div>
                                <?php }
                                if ($lists['item_image2']['insert_display_list'] == 1) { ?>

                                    <div class="form-group col-lg-4">
                                        <span class="form-label d-block">Item Image2</span>
                                        <div class="profile-pic">
                                            <label class="-label" for="item_image2">
                                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                            </label>
                                            <input type="file" name="item_image2" id="item_image2"
                                                accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)" />
                                            <img src="<?php echo "uploads/all/" . $items['item_image2']; ?>"
                                                id="outputitem_image2" width="200"  alt="Attachment Preview" />
                                            <input type="hidden" name="hid_item_image2"
                                                value="<?php echo $items['item_image2']; ?>">
                                        </div>

                                    </div>
                                <?php }
                                if ($lists['item_image3']['insert_display_list'] == 1) { ?>

                                    <div class="form-group col-lg-4">
                                        <span class="form-label d-block">Item Image3</span>
                                        <div class="profile-pic">
                                            <label class="-label" for="item_image3">
                                                <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                            </label>
                                            <input type="file" name="item_image3" id="item_image3"
                                                accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)" />
                                            <img src="<?php echo "uploads/all/" . $items['item_image3']; ?>"
                                                id="outputitem_image3" width="200"  alt="Attachment Preview" />
                                            <input type="hidden" name="hid_item_image3"
                                                value="<?php echo $items['item_image3']; ?>">
                                        </div>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                    <div class="card mt-2">
                        <div class="card-header table-card-header">
                            <h5>Price</h5>
                            <div class="card-header-right table-card-header ">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if ($lists['purchase_uom']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Purchase UoM</span>
                                        <select aria-label="Purchase Uom" class="form-select border js-example-basic-single" id="purchase_uom"
                                            name="purchase_uom">
                                            <option value="0">Select Any one Purchase UoM</option>
                                            <?php foreach ($result['uom'] as $uom) { ?>
                                                <option value="<?php echo $uom['id']; ?>" <?php if ($items['purchase_uom'] == $uom['id']) {
                                                       echo "selected=selected";
                                                   } ?>>
                                                    <?php echo $uom['uom_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['purchase_price']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Purchase Price</span>
                                        <input aria-label="Purchase Price" type="text" class="form-control autonumber text-right" data-a-sign="₹"
                                            name="purchase_price" id="purchase_price"
                                            value="<?php echo $items['purchase_price']; ?>">
                                    </div>
                                <?php }
                                if ($lists['sale_uom']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Sale UoM</span>
                                        <select aria-label="Sale Uom" class="form-select border js-example-basic-single" id="sale_uom"
                                            name="sale_uom">
                                            <option value="0">Select Any one Sale UoM</option>
                                            <?php foreach ($result['uom'] as $uom) { ?>
                                                <option value="<?php echo $uom['id']; ?>" <?php if ($items['sale_uom'] == $uom['id']) {
                                                       echo "selected";
                                                   } ?>>
                                                    <?php echo $uom['uom_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['sale_price']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Sale Price</span>
                                        <input aria-label="Sale Price" type="text" class="form-control autonumber text-right fill" data-a-sign="₹"
                                            name="sale_price" id="sale_price" value="<?php echo $items['sale_price'] ?>">
                                    </div>
                                <?php }
                                if ($lists['minimum_sale_price']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Minimum Sale Price</span>
                                        <input aria-label="Minimum Sale Price" type="text" readonly class="form-control text-right"
                                            name="minimum_sale_price" id="minimum_sale_price"
                                            value="<?php echo $items['minimum_sale_price'] ?>">
                                    </div>
                                <?php }
                                if ($lists['minimum_discount']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Maximum Discount(%)</span>
                                        <input aria-label="Minimum Discount" type="text" class="form-control text-right" name="minimum_discount"
                                            id="minimum_discount" value="<?php echo $items['minimum_discount'] ?>">
                                    </div>
                                <?php }
                                if ($lists['minimum_margin']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Minimum Margin(%)</span>
                                        <input aria-label="Minimum Margin" type="number" class="form-control text-right" name="minimum_margin"
                                            id="minimum_margin" max="100" value="<?php echo $items['minimum_margin'] ?>">
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header table-card-header">
                            <h5>Inventory</h5>
                            <div class="card-header-right table-card-header ">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if ($lists['opening_quantity']['insert_display_list'] == 1) { ?>
                                    <input type="hidden" name="opening_quantity_added_or_not"
                                        values="<?php echo ($items['opening_quantity'] == "" || $items['opening_quantity'] == "0") ? "N" : "Y"; ?>">
                                    <div
                                        class="form-group col-lg-6 <?php echo ($items['opening_quantity'] == "" || $items['opening_quantity'] == "0") ? "" : "custom_readonly"; ?>">
                                        <span class="form-label">Opening Quantity</span>
                                        <input aria-label="Opening Quantity" class="form-control text-right" type="text" id="opening_quantity"
                                            name="opening_quantity" value="<?php echo $items['opening_quantity']; ?>">
                                    </div>
                                    <div
                                        class="form-group col-lg-6 <?php echo ($items['opening_quantity'] == "" || $items['opening_quantity'] == "0") ? "" : "custom_readonly"; ?>">
                                        <span class="form-label">Warehouses</span>
                                        <select aria-label="Warehouse" class="form-select js-example-basic-single" id="warehouse_id"
                                            name="warehouse_id">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($warehouses as $waro) { ?>
                                                <option value="<?php echo $waro['id']; ?>" <?php if ($items['warehouse_id'] == $waro['id']) {
                                                       echo "selected";
                                                   } ?>>
                                                    <?php echo $waro['warehouse_name'] . "(" . $location[$waro['location_id']] . ")"; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div
                                        class="form-group col-lg-6 <?php echo ($items['opening_quantity'] == "" || $items['opening_quantity'] == "0") ? "" : "custom_readonly"; ?>">
                                        <span class="form-label">Opening Quantity Date</span>
                                        <input aria-label="Opening Quantity Date" class="form-control datepicker" type="text" id="opening_quantity_date"
                                            name="opening_quantity_date"
                                            value="<?php echo fromDate($items['opening_quantity_date']); ?>">
                                    </div>
                                <?php }
                                if ($lists['quantity_check']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Quantity Check</span>
                                        <div class="radio radio-outline  mt-1">
                                            <label class="radio radio-inline">
                                                <input aria-label="Radio option" type="radio" <?php echo $items['quantity_check'] == "1" ? "checked" : ""; ?> name="quantity_check" value="1"> <span></span>Yes
                                            </label>

                                            <label class="radio radio-inline">
                                                <input aria-label="Quantity Check" type="radio" name="quantity_check" value="2" <?php echo $items['quantity_check'] == "2" ? "checked" : ""; ?>> <span></span>No
                                            </label>

                                            <label class="radio radio-inline">
                                                <input aria-label="Quantity Check" type="radio" name="quantity_check" value="3" <?php echo $items['quantity_check'] == "3" ? "checked" : ""; ?>> <span></span>Not
                                                Applicable
                                            </label>
                                        </div>
                                    </div>
                                <?php }
                                if ($lists['minimum_level']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Minimum Level</span>
                                        <div>
                                            <input aria-label="Minimum Level" class="form-control text-right" type="text" id="minimum_level"
                                                name="minimum_level" value="<?php echo $items['minimum_level'] ?>">
                                        </div>
                                    </div>
                                <?php }
                                if ($lists['danger_level']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Danger Level</span>
                                        <div>
                                            <input aria-label="Danger Level" class="form-control text-right" type="text" id="danger_level"
                                                name="danger_level" value="<?php echo $items['danger_level'] ?>">
                                        </div>
                                    </div>
                                <?php }
                                if ($lists['maximum_capacity']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Maximum Capacity</span>
                                        <div>
                                            <input aria-label="Maximum Capacity" class="form-control text-right fill" type="text" id="maximum_capacity"
                                                name="maximum_capacity" value="<?php echo $items['maximum_capacity'] ?>">
                                        </div>
                                    </div>
                                <?php }
                                if ($lists['reorder_qty']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span>Reorder Qty</span>
                                        <div>
                                            <input aria-label="Reorder Qty" class="form-control text-right fill" type="text" id="reorder_qty"
                                                name="reorder_qty" value="<?php echo $items['reorder_qty'] ?>">
                                        </div>
                                    </div>
                                <?php }
                                if ($lists['tracking_type']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label">Tracking Type</span>
                                        <div class="radio radio-outline  mt-1">
                                            <label class="radio radio-inline">
                                                <input aria-label="Tracking Type" type="radio" checked="" name="tracking_type" value="0" <?php echo $items['tracking_type'] == "0" ? "checked" : ""; ?>> <span></span>None
                                            </label>

                                            <label class="radio radio-inline">
                                                <input aria-label="Tracking Type" type="radio" name="tracking_type" value="1" <?php echo $items['tracking_type'] == "1" ? "checked" : ""; ?>> <span></span>Serial
                                            </label>

                                            <label class="radio radio-inline">
                                                <input aria-label="Tracking Type" type="radio" name="tracking_type" value="2" <?php echo $items['tracking_type'] == "2" ? "checked" : ""; ?>> <span></span>Lot
                                            </label>
                                        </div>
                                    </div>
                                <?php }
                                if ($lists['negative_stock']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label d-block">Negative Stock </span>
                                        <input aria-label="Negative Stock" type="checkbox" class="js-switch" name="negative_stock" value="0" <?php echo $items['negative_stock'] == "0" ? "checked" : ""; ?> />
                                    </div>
                                <?php }
                                if ($lists['allow_back_order']['insert_display_list'] == 1) { ?>
                                    <div class="form-group col-lg-6">
                                        <span class="form-label d-block">Allow Back Order </span>
                                        <input aria-label="Allow Back Order" type="checkbox" class="js-switch" name="allow_back_order" value="0" <?php echo $items['allow_back_order'] == "0" ? "checked" : ""; ?> />
                                    </div>
                                <?php } ?>
                            </div>


                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header table-card-header">
                            <h5>Account</h5>
                            <div class="card-header-right table-card-header ">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if ($lists['inventory_asset_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Inventory Asset Account</span>
                                        <select aria-label="Inventory Asset Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="inventory_asset_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['inventory_asset_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['inventory_adjustment_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Inventory Adjustment Account </span>

                                        <select aria-label="Inventory Adjustment Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="inventory_adjustment_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['inventory_adjustment_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['inventory_cost_variance_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Inventory Cost Variance Account </span>
                                        <select aria-label="Inventory Cost Variance Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="inventory_cost_variance_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['inventory_cost_variance_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['cogs_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Cogs Account</span>
                                        <select aria-label="Cogs Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="cogs_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['cogs_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>>
                                                    <?php echo $acc['account_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['purchase_expense_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Purchase Expense Account </span>

                                        <select aria-label="Purchase Expense Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="purchase_expense_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['purchase_expense_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                <?php }
                                if ($lists['sales_revenue_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Sales Revenue Account</span>
                                        <select aria-label="Sales Revenue Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="sales_revenue_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['sales_revenue_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                <?php }
                                if ($lists['sales_discount_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Sales Discount Account </span>
                                        <select aria-label="Sales Discount Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="sales_discount_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['sales_discount_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                <?php }
                                if ($lists['sales_return_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Sales Return Account </span>
                                        <select aria-label="Sales Return Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="sales_return_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['sales_return_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['purchase_price_variance_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Purchase Price Variance Account </span>
                                        <select aria-label="Purchase Price Variance Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="purchase_price_variance_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['purchase_price_variance_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                <?php }
                                if ($lists['freight_expense_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Freight Expense Account </span>
                                        <select aria-label="Freight Expense Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="freight_expense_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['freight_expense_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['packaging_expense_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Packaging Expense Account </span>
                                        <select aria-label="Packaging Expense Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="packaging_expense_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['packaging_expense_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['sales_tax_payable_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Sales Tax Payable Account </span>
                                        <select aria-label="Sales Tax Payable Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="sales_tax_payable_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['sales_tax_payable_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['purchase_tax_payable_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Purchase Tax Payable Account </span>
                                        <select aria-label="Purchase Tax Payable Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="purchase_tax_payable_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['purchase_tax_payable_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['promotions_and_discounts_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Promotions And Discounts Account </span>
                                        <select aria-label="Promotions And Discounts Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="promotions_and_discounts_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['promotions_and_discounts_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['sales_allowances_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Sales Allowances Account </span>
                                        <select aria-label="Sales Allowances Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="sales_allowances_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['sales_allowances_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['deferred_revenue_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Deferred Revenue Account </span>
                                        <select aria-label="Deferred Revenue Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="deferred_revenue_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['deferred_revenue_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                <?php }
                                if ($lists['ap_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Ap Account </span>
                                        <select aria-label="Ap Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="ap_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['ap_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>>
                                                    <?php echo $acc['account_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                <?php }
                                if ($lists['ar_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Ar Account </span>
                                        <select aria-label="Ar Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="ar_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['ar_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>>
                                                    <?php echo $acc['account_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['accrued_expenses_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Accrued Expenses Account </span>
                                        <select aria-label="Accrued Expenses Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="accrued_expenses_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['accrued_expenses_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['prepaid_expenses_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Prepaid Expenses Account </span>
                                        <select aria-label="Prepaid Expenses Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="prepaid_expenses_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['prepaid_expenses_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['wip_asset_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Wip Asset Account </span>
                                        <select aria-label="Wip Asset Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="wip_asset_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['wip_asset_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>>
                                                    <?php echo $acc['account_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['goods_received_not_invoiced_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Goods Received Not Invoiced Account </span>
                                        <select aria-label="Goods Received Not Invoiced Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="goods_received_not_invoiced_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['goods_received_not_invoiced_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>

                                    </div>
                                <?php }
                                if ($lists['invoiced_not_received_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Invoiced Not Received Account </span>
                                        <select aria-label="Invoiced Not Received Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="invoiced_not_received_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['invoiced_not_received_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }
                                if ($lists['invoiced_not_received_account']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Goods Sold Not Invoiced Account </span>
                                        <select aria-label="Goods Sold Not Invoiced Account" class="form-select js-data-example-ajax-Comman-form  col-lg-4"
                                            data-tablename="chart_of_accounts" data-fieldsname="account_name" data-value=""
                                            data-pagename="chart_of_accounts" name="goods_sold_not_invoiced_account">
                                            <option value=""> Select Any one</option>
                                            <?php foreach ($result['account'] as $acc) { ?>
                                                <option value="<?php echo $acc['account_code']; ?>" <?php if ($items['goods_sold_not_invoiced_account'] == $acc['account_code']) {
                                                       echo "selected";
                                                   } ?>><?php echo $acc['account_name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>


                                <?php }
                                if ($lists['base_uom']['insert_display_list'] == 1) { ?>
                                    <div class="col-lg-6 form-group">
                                        <span class="form-label">Base Uom </span>
                                        <select aria-label="Base Uom" class="form-select js-example-basic-single  col-lg-4" name="base_uom">
                                            <?php foreach ($result['uom'] as $uom) { ?>
                                                <option value="<?php echo $uom['id']; ?>" <?php if ($items['base_uom'] == $uom['id']) {
                                                       echo "selected=selected";
                                                   } ?>>
                                                    <?php echo $uom['uom_name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header table-card-header">
                            <h5>Tax</h5>
                            <div class="card-header-right table-card-header ">
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="form-group col-lg-6 <?php echo $_SESSION['config']['gst_number']==""? 'event-disabled' : ''; ?>">
                                    <span class="form-label">HSN/SAC Code</span>
                                    <select aria-label="Hsn And Sac Code" name="hsn_and_sac_code_id"
                                        class="form-control js-data-example-ajax-hsncodes"
                                        data-tablename="hsn_and_sac_codes" data-fieldsname="code"
                                        data-fieldsname1="code" data-fieldsname2="code_description"
                                        data-fieldsname3="gst" data-category="id" >
                                        <option value="">Select Any One </option>
                                        <?php foreach ($result['hsnsac'] as $hsn) { ?>
                                            <option value="<?php echo $hsn['id']; ?>" <?php if ($items['hsn_and_sac_code_id'] == $hsn['id'])
                                                   { echo "selected";
} ?>>
                                                <?php echo $hsn['code']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group col-lg-6">
                                    <span class="form-label">HS Code</span>
                                    <select aria-label="Hs Code" class="form-select js-example-basic-single" id="hs_code" name="hs_code">
                                        <option value=""> Select Any one</option>
                                        <?php foreach ($result['hscode'] as $hscode) { ?>
                                            <option value="<?php echo $hscode['id']; ?>" <?php if ($items['hs_code'] == $hscode['id']) {
                                                   echo "selected";
                                               } ?>>
                                                <?php echo $hscode['code_description'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>


                            </div>

                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header table-card-header">
                            <h5>Specification Documents</h5>
                            <div class="card-header-right table-card-header ">
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-lg-8">
                                    <span class="form-label">File Name</span>
                                </div>
                                <div class="col-lg-2">
                                    <span class="form-label">Download/Upload</span>
                                </div>
                                <div class="col-lg-2"></div>
                            </div>
                            <?php
                            $attachments = json_decode($items['compliance_specification_documents'], 1);
                            $attchmentseq = 1;
                            $i = 100;
                            foreach ($attachments as $attachment) { ?>
                                <div class="row mb-2" id="remove_attache_<?php echo $attchmentseq; ?>">

                                    <div class="col-lg-8">
                                        <input aria-label="Old Attachment Name[]" type="text" name="old_attachment_name[]" class="form-control"
                                            value="<?php echo $attachment['attachment_name']; ?>" readonly>
                                    </div>
                                    <div class="col-lg-2 text-center">
                                        <a href="uploads/compliance_specification_documents/<?php echo $attachment['attachment']; ?>"
                                            class="text-primary" target="_blank"><i class="fa fa-download"></i></a>
                                    </div>
                                    <div class="col-lg-2">

                                        <i class="ti-trash float-end text-danger" data-bs-toggle="tooltip"
                                            data-placement="bottom" title="" data-bs-original-title="Remove"
                                            onclick="deletRowjoinOwner('remove_attache_<?php echo $attchmentseq; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>

                                        <input type="hidden" name="old_attachment[]" class="form-control"
                                            value="<?php echo $attachment['attachment']; ?>">

                                    </div>
                                </div>
                                <?php $attchmentseq++;
                            } ?>

                            <div id="addmoreattachements_id">
                                <div class="row mb-2" id="remove_attache_new">
                                    <div class="form-group col-lg-8">
                                        <input aria-label="Attachment Name[]" type="text" class="form-control" name="attachment_name[]" value="">
                                    </div>
                                    <div class="form-group col-lg-2 text-center">

                                        <div class="image-input image-input-empty image-input-outline" id="kt_image_5">
                                            <label class="btn-hover-text-primary text-dark" data-action="remove"
                                                data-bs-toggle="tooltip" title="" style="left: -9px;top: 0px;"
                                                data-bs-original-title="Upload File">
                                                <span> <i class="fa fa-upload"></i></span>
                                                <input aria-label="Attachment[]" type="file" name="attachment[]" accept=".pdf, image/*">
                                            </label>
                                        </div>

                                    </div>
                                    <div class="form-group col-lg-2">
                                        <button type="button" class="btn p-0 border-0 bg-transparent ti-trash float-end text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="deletRowjoinOwner('remove_attache_new')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                                    </div>
                                </div>
                            </div>
                            <div class="">
                                <button type="button" class="btn btn-success waves-effect waves-light float-left"
                                    onclick="addMoreAttachment()"><i
                                        class="icofont icofont-plus icon-white  m-r-10"></i>Add More</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-lg-6">
                        <div class="card">
                            <div class="card-header table-card-header">
                                <h5>Vendor Items</h5>
                                <div class="card-header-right table-card-header ">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <span class="form-label">Vendors</span>
                                    </div>
                                    <div class="col-lg-3">
                                        <span class="form-label">Part No</span>
                                    </div>
                                    <div class="col-lg-3">
                                        <span class="form-label">Part Name</span>
                                    </div>
                                    <div class="col-lg-1"></div>
                                </div>
                                <div id="addmorevendoritem">
                                    <?php
                                    $mk = 1;
                                    if (count((array) $vendor_items) == 0) {
                                        $vendor_items[] = array();
                                    }
                                    foreach ($vendor_items as $vi) {
                                        $deletrowid = "vendoritemrow_" . $mk;
                                        ?>
                                        <div class="row mt-2" id="<?php echo $deletrowid; ?>">
                                            <input type="hidden" name="vendor_items[vendor_item_id][]"
                                                value="<?php echo $vi['id']; ?>">
                                            <div class="col-lg-5">
                                                <select aria-label="Vendor Items[vendor ][]" class="form-select js-example-basic-single"
                                                    name="vendor_items[vendor_id][]">
                                                    <?php foreach ($vendors as $ven) { ?>
                                                        <option value="<?php echo $ven['id']; ?>" <?php if ($ven['id'] == $vi['vendor_id']) {
                                                               echo "selected";
                                                           } ?>>
                                                            <?php echo $ven['display_name'] . "(" . $ven['company'] . ")" ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <input aria-label="Vendor Items[vendor Part No][]" class="form-control text-right" type="text"
                                                    name="vendor_items[vendor_part_no][]"
                                                    value="<?php echo $vi['vendor_part_no']; ?>">
                                            </div>
                                            <div class="col-lg-3">
                                                <input aria-label="Vendor Items[vendor Part Name][]" class="form-control text-right" type="text"
                                                    name="vendor_items[vendor_part_name][]"
                                                    value="<?php echo $vi['vendor_part_name']; ?>">
                                            </div>
                                            <div class="col-lg-1">
                                                <i class="ti-trash text-danger"
                                                    onclick="removeItem('<?php echo $deletrowid; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                            </div>
                                        </div>
                                        <?php $mk++;
                                    } ?>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xs-12">
                                        <button class="btn btn-success" type="button" onclick="addMoreVendorItems()">+
                                            Add More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group col-lg-6">
                        <div class="card">
                            <div class="card-header table-card-header">
                                <h5>Customers Items</h5>
                                <div class="card-header-right table-card-header ">
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <span class="form-label">Customers</span>
                                    </div>
                                    <div class="col-lg-3">
                                        <span class="form-label">Part No</span>
                                    </div>
                                    <div class="col-lg-3">
                                        <span class="form-label">Part Name</span>
                                    </div>
                                    <div class="col-lg-1"></div>
                                </div>
                                <div id="addmorecustomeritem">
                                    <?php
                                    $mks = 1;
                                    if (count((array) $customer_items) == 0) {
                                        $customer_items[] = array();
                                    }
                                    foreach ($customer_items as $vi) {
                                        $deletrowid = "customeritemrow_" . $mks;
                                        ?>
                                        <div class="row mt-2" id="<?php echo $deletrowid; ?>">
                                            <input type="hidden" name="customer_items[customer_item_id][]"
                                                value="<?php echo $vi['id']; ?>">
                                            <div class="col-lg-5">
                                                <select aria-label="Customer Items[customer ][]" class="form-select js-example-basic-single"
                                                    name="customer_items[customer_id][]">
                                                    <?php foreach ($customers as $ven) { ?>
                                                        <option value="<?php echo $ven['id']; ?>" <?php if ($ven['id'] == $vi['customer_id']) {
                                                               echo "selected";
                                                           } ?>>
                                                            <?php echo $ven['display_name'] . "(" . $ven['company'] . ")" ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-lg-3">
                                                <input aria-label="Customer Items[customer Part No][]" class="form-control text-right" type="text"
                                                    name="customer_items[customer_part_no][]"
                                                    value="<?php echo $vi['customer_part_no']; ?>">
                                            </div>
                                            <div class="col-lg-3">
                                                <input aria-label="Customer Items[customer Part Name][]" class="form-control text-right" type="text"
                                                    name="customer_items[customer_part_name][]"
                                                    value="<?php echo $vi['customer_part_name']; ?>">
                                            </div>
                                            <div class="col-lg-1">
                                                <i class="ti-trash text-danger"
                                                    onclick="removeItem('<?php echo $deletrowid; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                            </div>
                                        </div>
                                        <?php $mks++;
                                    } ?>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-xs-12">
                                        <button class="btn btn-success" type="button" onclick="addMoreCustomerItems()">+
                                            Add More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-3">

                    <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                    <input type="hidden" id="skuvalidation" value="<?php echo $sku; ?>">
                    <input type="hidden" name="submititem" value="">
                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submititemform()"><i
                            class="fa fa-check m-r-10"></i> <?php echo $rid == "" ? "Submit" : "Update"; ?></button>
                </div>
        </form>
    </div>
</div>



<div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="validationModalLabel">Missing Required Fields</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-bold text-danger" id="validationMessage"></p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="proceedAnywayBtn">Proceed Anyway</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="skuChangeModal" tabindex="-1" aria-labelledby="skuChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="skuChangeModalLabel">SKU Change Warning</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="fw-bold text-danger" id="skuChangeMessage"></p>
                <p class="text-muted">
                    Because SKU codes are linked in multiple places, you cannot directly update these fields.<br>
                    You can either correct your selection or create a <strong>duplicate item</strong> with the new
                    values.
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Go Back</button>
                <button type="button" class="btn btn-primary" id="duplicateItemBtn">Make Duplicate</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    var categoryData = <?php echo json_encode($result['category']); ?>;
    var skuConfig = '<?php echo $result['sku']['sku_generator_detail']; ?>';
    var maxid = 10000;

    function addMoreVendorItems() {
        maxid = maxid + 1;
        var deletrowid = "vendoritemrow_" + maxid;
        var html = '';
        html = '<div class="row mt-2" id="' + deletrowid + '"><input type="hidden" name="vendor_items[vendor_item_id][]" value="">';
        html += '<div class="col-lg-5"><select aria-label="Vendor Items[vendor ][]" class="form-select js-example-basic-single" name="vendor_items[vendor_id][]"><?php foreach ($vendors as $ven) { ?><option value="<?php echo $ven['id']; ?>" ><?php echo trim(preg_replace('/\s+/', ' ', $ven['display_name'] . "(" . $ven['company'] . ")")); ?></option><?php
} ?></select></div>';
        html += '<div class="col-lg-3"><input aria-label="Vendor Items[vendor Part No][]" class="form-control text-right" type="text" name="vendor_items[vendor_part_no][]" value=""></div>';
        html += '<div class="col-lg-3"><input aria-label="Vendor Items[vendor Part Name][]" class="form-control text-right" type="text" name="vendor_items[vendor_part_name][]" value=""></div>';
        html += '<div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" onclick="removeItem(\'' + deletrowid + '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div>';
        html += '</div>';
        $("#addmorevendoritem").append(html);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
    function removeItem(id) {
        $("#" + id).remove();
    }

    function submititemform() {
        var ridField = document.querySelector("input[name='rid']");
        var rid = ridField ? ridField.value : "";

        var skuField = document.getElementById("skuvalidation");
        var skuValue = skuField ? skuField.value.trim() : "";

        var missingFields = [];

    if(skuValue !== ""){
             // Only check enabled fields
        if (skuConfig.item_type && skuConfig.item_type.enabled === "1") {
            var itemType = document.getElementById("item_type").value;
            if (!itemType) missingFields.push("Item Type");
        }

        if (skuConfig.warehouse_name && skuConfig.warehouse_name.enabled === "1") {
            var warehouseId = document.getElementById("warehouse_id").value;
            if (!warehouseId) missingFields.push("Warehouse");
        }

        if (skuConfig.category_name && skuConfig.category_name.enabled === "1") {
            var categoryId = document.getElementById("category_id").value;
            if (!categoryId) missingFields.push("Category");
        }

        if (skuConfig.location_name && skuConfig.location_name.enabled === "1") {
            var locationId = document.getElementById("location_id") ? document.getElementById("location_id").value : "";
            if (!locationId) missingFields.push("Location");
        }

        // Step 1: Missing field popup
        if (missingFields.length > 0) {
            var message = "Please select <strong>" + missingFields.join("</strong> and <strong>") + "</strong> before submitting.<br>Otherwise your SKU code will not be generated correctly.";
            document.getElementById("validationMessage").innerHTML = message;

            var validationModal = new bootstrap.Modal(document.getElementById('validationModal'));
            validationModal.show();

            document.getElementById("proceedAnywayBtn").onclick = function () {
                $('#itemform').submit();
                validationModal.hide();
            };
            return;
        }

        if (rid !== "") {
            var changedFields = [];

            if (skuConfig.category_name && skuConfig.category_name.enabled === "1") {
                var originalCategory = "<?php echo $items['category_id']; ?>";
                if (categoryId !== originalCategory) changedFields.push("Category");
            }

            if (skuConfig.item_type && skuConfig.item_type.enabled === "1") {
                var originalItemType = "<?php echo $items['item_type']; ?>";
                if (itemType !== originalItemType) changedFields.push("Item Type");
            }

            if (skuConfig.warehouse_name && skuConfig.warehouse_name.enabled === "1") {
                var originalWarehouse = "<?php echo $items['warehouse_id']; ?>";
                if (warehouseId !== originalWarehouse) changedFields.push("Warehouse");
            }

            if (skuConfig.location_name && skuConfig.location_name.enabled === "1") {
                var originalLocation = "<?php echo $items['location_id']; ?>";
                if (locationId !== originalLocation) changedFields.push("Location");
            }

            if (changedFields.length > 0) {
                var msg = "You changed <strong>" + changedFields.join("</strong>, <strong>") + "</strong>.<br>Your SKU will be different.";
                document.getElementById("skuChangeMessage").innerHTML = msg;

                var skuChangeModal = new bootstrap.Modal(document.getElementById('skuChangeModal'));
                skuChangeModal.show();

                document.getElementById("duplicateItemBtn").onclick = function () {
                    ridField.value = ""; // clear rid → becomes new item
                    $('#itemform').submit();
                    skuChangeModal.hide();
                };

                return;
            }
        }
    }

        // Step 3: Normal validation
        var validations = ajaxFormSubmitValidation();
        if (validations == 0) {
            $('#itemform').submit();
        }
    }


    function deletRowjoinOwner(rowid) {
        $("#" + rowid).remove();
        $('.tooltip').remove();
    }
    var maxrowsattachemets = 36980;
    function addMoreAttachment(rowid) {
        maxrowsattachemets = maxrowsattachemets + 1;
        var deleteid = "deleteattachementsid_" + maxrowsattachemets + "";
        var html = '';
        html += '<div class="row" id="' + deleteid + '">';
        html += '<div class="form-group col-lg-8"><input aria-label="Attachment Name[]" type="text" class="form-control" name="attachment_name[]" value=""></div>';
        html += '<div class="form-group col-lg-2 text-center"><div class="image-input image-input-empty image-input-outline" id="kt_image_5_2"><label class="btn-hover-text-primary text-dark" data-action="remove" data-bs-toggle="tooltip" title="" style="left: -9px;top: 0px;" data-bs-original-title="Upload File"><span> <i class="fa fa-upload"></i></span><input aria-label="Attachment[]" type="file" name="attachment[]" accept=".pdf, image/*"></label></div></div>';
        html += '<div class="form-group col-lg-2"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash float-end text-danger" onclick="deletRowjoinOwner(\'' + deleteid + '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button> </div>';
        html += '</div>';
        $("#addmoreattachements_id").append(html);
    }

    function showPriceNotification(msg) {
        if (typeof tostarnotify === 'function') {
            tostarnotify(
                'top',
                'right',
                'fa fa-exclamation-circle',
                'form-label label label-warning',
                'animated flipInX',
                'animated flipOutX',
                msg,
                ''
            );
        } else {
            alert(msg);
        }
    }

    function updatePrices() {
        let saleInput = document.getElementById('sale_price');
        let minSaleInput = document.getElementById('minimum_sale_price');
        let discountInput = document.getElementById('minimum_discount');

        let sale = parseFloat(saleInput.value.replace(/,/g, '')) || 0;
        let discount = parseFloat(discountInput.value.replace(/,/g, '')) || 0;

        // Default: min = sale
        let newMin = sale;

        if (discount > 0) {
            newMin = sale - (sale * discount / 100);
        }

        if (minSaleInput.value != newMin.toFixed(2)) {
            minSaleInput.value = newMin.toFixed(2);
            showPriceNotification("Minimum Sale Price updated.");
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const saleInput = document.getElementById('sale_price');
        const discountInput = document.getElementById('minimum_discount');

        if (saleInput) saleInput.addEventListener('input', updatePrices);
        if (discountInput) discountInput.addEventListener('input', updatePrices);
    });

function setCategoryAccounts(categoryId) {
    if (!categoryId) {
        console.warn("⚠️ No categoryId provided");
        return;
    }
    let selectedCategory = categoryData.find(cat => cat.id == categoryId);
    if (!selectedCategory) {
        console.warn("⚠️ No matching category found for ID:", categoryId);
        return;
    }

    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=checkitemcategoryaccountdata&categoryId=' + categoryId,
        success: function (response) {
            console.log(response);
            let data;
            try {
                data = typeof response === "string" ? JSON.parse(response) : response;
            } catch (err) {
                console.error("❌ JSON parse error:", err, "Raw response:", response);
                return;
            }

            if (data.category && data.category.hsn_and_sac_code_id) {
                 let hsnSelect = document.querySelector(`select[name="hsn_and_sac_code_id"]`);

                if (hsnSelect) {
                    $(hsnSelect).find("option:not(:first)").remove(); // remove old options
                    let hsnText = `${data.category.hsn_code} - ${data.category.hsn_description}`;
                    console.log(`📌 Setting HSN option: ${hsnText}`);
                    $(hsnSelect).append(
                        new Option(hsnText, data.category.hsn_and_sac_code_id, false, true)
                    );
                    $(hsnSelect).val(value).trigger("change");
                }
                //     );

            }

            let matchedAccounts = data.matched_accounts || [];
            console.log("🔗 matched_accounts:", matchedAccounts);

            let accountFields = [
                "inventory_asset_account", "inventory_adjustment_account", "inventory_cost_variance_account",
                "cogs_account", "purchase_expense_account", "sales_revenue_account", "sales_discount_account",
                "sales_return_account", "purchase_price_variance_account", "freight_expense_account",
                "packaging_expense_account", "sales_tax_payable_account", "purchase_tax_payable_account",
                "promotions_and_discounts_account", "sales_allowances_account", "deferred_revenue_account",
                "ap_account", "ar_account", "accrued_expenses_account", "prepaid_expenses_account",
                "wip_asset_account", "goods_received_not_invoiced_account", "invoiced_not_received_account",
                "goods_sold_not_invoiced_account"
            ];

            accountFields.forEach(field => {
                console.log(`⚙️ Processing field: ${field}`);
                let select = document.querySelector(`select[name="${field}"]`);
                if (!select) {
                    console.warn(`⚠️ Select element not found for field: ${field}`);
                    return;
                }
                let value = (data.category && data.category[field]) ? data.category[field] : "";
                console.log(`Category value: "${value}" for ${field}`);
                $(select).find("option:not(:first)").remove();
                if (data.accounts && data.accounts[field]) {
                    let acc = data.accounts[field];
                    console.log(`   ➕ Adding option: ${acc.account_name} (${acc.account_code})`);
                    $(select).append(
                        new Option(acc.account_name, acc.account_code, false, acc.account_code == value)
                    );
                }
                $(select).val(value).trigger("change");
            });




            var child_category_id = document.getElementById("child_category_id");
            child_category_id.innerHTML = '<option value="">Select Any One</option>';
            if (data && Array.isArray(data.child_categroy) && data.child_categroy.length > 0) {
              data.child_categroy.forEach(function(item) {
                var option = document.createElement("option");
                option.value = item.id;
                option.textContent = item.category_description;
                child_category_id.appendChild(option);
              });
            } else {
              child_category_id.innerHTML = '<option value="">Select Any One</option>';
            }
               $(".js-example-basic-single").select2();



        }
    });
}


function setChildCategoryAccounts(categoryId){
    if (!categoryId) {
        console.warn("⚠️ No categoryId provided");
        return;
    }
    $.ajax({
        url: "ajax.php",
        type: "POST",
        data: 'page=checkitemcategoryaccountdata&categoryId=' + categoryId,
        success: function (response) {
            console.log(response);
            let data;
            try {
                data = typeof response === "string" ? JSON.parse(response) : response;
            } catch (err) {
                console.error("❌ JSON parse error:", err, "Raw response:", response);
                return;
            }
            var sub_child_category_id = document.getElementById("sub_child_category_id");
            sub_child_category_id.innerHTML = '<option value="">Select Any One</option>';
            if (data && Array.isArray(data.child_categroy) && data.child_categroy.length > 0) {
                data.child_categroy.forEach(function(item) {
                    var option = document.createElement("option");
                    option.value = item.id;
                    option.textContent = item.category_description;
                    sub_child_category_id.appendChild(option);
                });
            } else {
                sub_child_category_id.innerHTML = '<option value="">Select Any One</option>';
            }
            $(".js-example-basic-single").select2();
        }
    });



}

var maxiddsdc = 10000;
function addMoreCustomerItems() {
    maxiddsdc = maxiddsdc + 1;
    var hid_customer_list = $("#hid_customer_list").html();
    var deletrowid = "customeritemrow_" + maxiddsdc;
    var html = ``;

     html += `<div class="row mt-2" id="` + deletrowid + `">
                <input type="hidden" name="customer_items[customer_item_id][]"
                    value="">`;

             html += `<div class="col-lg-5"><select aria-label="Customer Items[customer ][]" class="form-select js-example-basic-single" name="customer_items[customer_id][]"><?php foreach ($customers as $ven) { ?><option value="<?php echo $ven['id']; ?>" ><?php echo trim(preg_replace('/\s+/', ' ', $ven['display_name'] . "(" . $ven['company'] . ")")); ?></option><?php
} ?></select></div>`;


                html += `<div class="col-lg-3">
                    <input aria-label="Customer Items[customer Part No][]" class="form-control text-right" type="text"
                        name="customer_items[customer_part_no][]"
                        value="">
                </div>
                <div class="col-lg-3">
                    <input aria-label="Customer Items[customer Part Name][]" class="form-control text-right" type="text"
                        name="customer_items[customer_part_name][]"
                        value="">
                </div>
                <div class="col-lg-1">
                    <i class="ti-trash text-danger removeItem" data-removeid="` + deletrowid + `"></i>
                </div>
            </div>`;


    $("#addmorecustomeritem").append(html);
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
    $('[data-bs-toggle="tooltip"]').tooltip();
}

</script>
