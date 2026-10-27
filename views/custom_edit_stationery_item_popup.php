<?php
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');

$item_id = isset($_POST['item_id']) ? $_POST['item_id'] : "";

$use_loc_id = $_SESSION['user']['location_id'] != "" ? $_SESSION['user']['location_id'] : $_SESSION['location_id'];

$locdata1["where"] = array('is_active' => '0', 'company_id' => $_SESSION['company_id']);
$locdata1["field"] = "id,location_name";
$locdata1['order'] = "location_name ASC";
$locdata = urlencode(json_encode($locdata1));
$locurl = API . "?mode=gettabledetailswithconditions&table=locations&data=" . $locdata;
$loc_details = callAPI($locurl);
$loc_data = json_decode($loc_details, 1);

$catdata1["where"] = array('is_active' => '0', 'company_id' => $_SESSION['company_id']);
$catdata1["field"] = "id,name";
$catdata1['order'] = "name ASC";
$catdata = urlencode(json_encode($catdata1));
$caturl = API . "?mode=gettabledetailswithconditions&table=stationery_categories&data=" . $catdata;
$cat_details = callAPI($caturl);
$cat_data = json_decode($cat_details, 1);

$item_details = array(
    'id' => '',
    'location_id' => '',
    'category_id' => '',
    'item_name' => '',
    'available_qty' => '',
    'cost' => '',
    'description' => ''
);
if ($item_id != "") {
    $idata1["where"] = array('is_active' => '0', 'company_id' => $_SESSION['company_id'], 'id' => $item_id);
    $idata1["field"] = "id,location_id,category_id,item_name,available_qty,cost,description";
    $idata = urlencode(json_encode($idata1));
    $iurl = API . "?mode=gettabledetailswithconditions&table=stationery_items&data=" . $idata;
    $item_response = callAPI($iurl);
    $item_data = json_decode($item_response, 1);
    if (count((array)$item_data) > 0) {
        $item_details = $item_data[0];
    }
}
?>
<script type="text/javascript">
    function submitupdatestationeryitemfrom() {
        var location_id = $("#edit_location_id").val();
        var category_id = $("#edit_category_id").val();
        var item_name = $("#edit_item_name").val();

        if (!location_id) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please select location', '');
            return false;
        }
        if (!category_id) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please select category', '');
            return false;
        }
        if (!item_name) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please enter product name', '');
            return false;
        }

        var post = $("#submitformeditstationeryitem").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=updatestationeryitem&' + post,
            type: "POST",
            success: function(data) {
                window.location.reload();
            },
            error: function() {}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">Edit Item</h5>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <form class="kt-form" id="submitformeditstationeryitem">
        <div class="row">
            <div class="form-group w-full col-md-6">
                <label>Location <span class="text-danger">*</span></label>
                <select class="form-select js-example-basic-single" name="location_id" id="edit_location_id">
                    <option value="">Select Location</option>
                    <?php if (count((array)$loc_data) > 0) {
                        foreach ((array)$loc_data as $loc) { ?>
                            <option <?php if ($item_details['location_id'] == $loc['id']) { echo "selected"; } ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
            <div class="form-group w-full col-md-6">
                <label>Category <span class="text-danger">*</span></label>
                <select class="form-select js-example-basic-single" name="category_id" id="edit_category_id">
                    <option value="">Select Category</option>
                    <?php if (count((array)$cat_data) > 0) {
                        foreach ((array)$cat_data as $cat) { ?>
                            <option <?php if ($item_details['category_id'] == $cat['id']) { echo "selected"; } ?> value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label>Product Name <span class="text-danger">*</span></label>
            <input placeholder="Please Enter Your Product Name" type="text" name="item_name" id="edit_item_name" class="form-control" value="<?php echo $item_details['item_name']; ?>">
        </div>
        <div class="form-group">
            <label>Quantity <span class="text-danger">*</span></label>
            <input placeholder="Please Enter Your Quantity" name="available_qty" id="edit_available_qty" type="text" class="form-control text-right fill" value="<?php echo $item_details['available_qty']; ?>" readonly disabled>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input name="cost" id="edit_cost" type="text" class="form-control text-right fill" value="<?php echo $item_details['cost']; ?>">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea class="form-control" name="description" id="edit_description" placeholder="Please describe your product"><?php echo $item_details['description']; ?></textarea>
        </div>
        <div class="modal-footer">
            <input type="hidden" name="id" id="edit_id" value="<?php echo $item_details['id']; ?>">
            <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitupdatestationeryitemfrom()">Save</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(".js-example-basic-single").select2({
        dropdownParent: $("#kt_modal_KTDatatable_remote")
    });
</script>
