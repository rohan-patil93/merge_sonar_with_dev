<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$use_loc_id = $_SESSION['user']['location_id'] != "" ? $_SESSION['user']['location_id'] : $_SESSION['location_id'];

$locdata1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$locdata1["field"] = "id,location_name";
$locdata1['order'] = "location_name ASC";
$locdata = urlencode(json_encode($locdata1));
$locurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$locdata;
$loc_details = callAPI($locurl);
$loc_data = json_decode($loc_details,1);

$catdata1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$catdata1["field"] = "id,name";
$catdata1['order'] = "name ASC";
$catdata = urlencode(json_encode($catdata1));
$caturl = API."?mode=gettabledetailswithconditions&table=stationery_categories&data=".$catdata;
$cat_details = callAPI($caturl);
$cat_data = json_decode($cat_details,1);

$type = isset($_POST['type']) ? $_POST['type'] : "";
$item_id = isset($_POST['item_id']) ? $_POST['item_id'] : "";
$cat_id = isset($_POST['cat_id']) ? $_POST['cat_id'] : "";
?>
<script type="text/javascript">
    function loadTopupStationeryItems() {
        var category_id = $("#topupdata #topup_category_id").val();
        var location_id = $("#topupdata #topup_location_id").val();
        var item_id = "<?php echo $item_id; ?>";
        $("#topupdata #topup_item_id").html('<option value="">Select Item</option>');
        if (category_id == "" || location_id == "") {
            $("#topupdata #topup_item_id").trigger('change');
            return;
        }
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=getstationeryitemsbycategory&category_id=' + category_id + '&location_id=' + location_id,
            type: "POST",
            success: function(data) {
                $("#topupdata #topup_item_id").html(data);
                if (item_id != "") {
                    $("#topupdata #topup_item_id").val(item_id);
                }
                $("#topupdata #topup_item_id").trigger('change');
            },
            error: function() {}
        });
    }

    function submitcreateproductrfrom() {
        var location_id = $("#adddata #add_location_id").val();
        var category_id = $("#adddata #add_category_id").val();
        var item_name = $("#adddata #item_name").val();
        var available_qty = $("#adddata #add_available_qty").val();

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
        if (!available_qty) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please enter quantity', '');
            return false;
        }

        var post = $("#submitformadditionalservice").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=addstationery&' + post,
            type: "POST",
            success: function(data) {
                window.location.href = 'index.php?option=COM_VIEW_OFFICE_MANAGEMENT&TAB=2';
            },
            error: function() {}
        });
    }

    function viewaddupdate(val) {
        if (val == 'add') {
            $("#checkval").hide();
            $("#adddata").show();
            $("#topupdata").hide();
        }
        if (val == 'topup') {
            $("#checkval").hide();
            $("#adddata").hide();
            $("#topupdata").show();
        }
    }

    function submittopupproductrfrom() {
        var location_id = $("#topupdata #topup_location_id").val();
        var category_id = $("#topupdata #topup_category_id").val();
        var item_id = $("#topupdata #topup_item_id").val();
        var available_qty = $("#topupdata #topup_available_qty").val();

        if (!location_id) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please select location', '');
            return false;
        }
        if (!category_id) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please select category', '');
            return false;
        }
        if (!item_id) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please select item', '');
            return false;
        }
        if (!available_qty) {
            tostarnotify('top', 'right', 'fa fa-warning', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'Please enter quantity', '');
            return false;
        }

        var post = $("#submitformupdatestopupervice").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=topupstationery&' + post,
            type: "POST",
            success: function(data) {
                window.location.href = 'index.php?option=COM_VIEW_OFFICE_MANAGEMENT&TAB=2';
            },
            error: function() {}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title"> <?php if($_POST['id']!=""){ echo "Edit";
}else{ echo "Add";
} ?> / Topup Item </h5>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div id="checkval" <?php if($type!=""){ ?> style="display:none;" <?php
} ?>>
        <div class="col-lg-12 row p-2 text-center">
            <div class="col-lg-12">
                <button class="btn btn-primary p-3" type="button" onclick="viewaddupdate('add')"> Add New Product </button>
            </div>
            <div class="col-lg-12 p-3"> OR</div>
            <div class="col-lg-12">
                <button class="btn btn-primary p-3" type="button" onclick="viewaddupdate('topup')"> Topup Product </button>
            </div>
        </div>
    </div>

    <div style="display:none;" id="adddata">
        <form class="kt-form" id="submitformadditionalservice">
            <div class="row">
            <div class="form-group w-full  col-md-6">
                <span>Location <span class="text-danger">*</span></span>
                <select aria-label="Location" class="form-select js-example-basic-single" name="location_id" id="add_location_id">
                    <option value="">Select Location</option>
                    <?php if(count((array)$loc_data) > 0) {
                        foreach((array)$loc_data as $loc) { ?>
                            <option <?php if($use_loc_id == $loc['id']){ echo "selected";
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
            <div class="form-group w-full col-md-6">
                <span>Category <span class="text-danger">*</span></span>
                <select aria-label="Category" class="form-select js-example-basic-single" name="category_id" id="add_category_id">
                    <option value="">Select Category</option>
                    <?php if(count((array)$cat_data) > 0) {
                        foreach((array)$cat_data as $cat) { ?>
                            <option <?php if($cat_id == $cat['id']){ echo "selected";
} ?> value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
        </div>
            <div class="form-group">
                <span>Product Name <span class="text-danger">*</span></span>
                <input aria-label="Please Enter Your Product Name" placeholder="Please Enter Your Product Name" type="text" name="item_name" id="item_name" class="form-control">
            </div>
            <div class="form-group">
                <span>Quantity <span class="text-danger">*</span></span>
                <input aria-label="Please Enter Your Quantity" placeholder="Please Enter Your Quantity" name="available_qty" id="add_available_qty" type="text" class="form-control text-right fill">
            </div>
            <div class="form-group">
                <span>Price</span>
                <input aria-label="Cost" name="cost" id="cost" type="text" class="form-control text-right fill">
            </div>
            <div class="form-group">
                <span>Description</span>
                <textarea aria-label="Please describe your product" class="form-control" name="description" id="description" placeholder="Please describe your product"></textarea>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="id" id="id" value="<?php echo $_POST['id']; ?>">
                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitcreateproductrfrom()">Save</button>
            </div>
        </form>
    </div>

    <div <?php if($type==""){ ?> style="display:none;" <?php
} ?> id="topupdata">
        <form class="kt-form" id="submitformupdatestopupervice">
            <div class="row">
            <div class="form-group w-full col-md-6">
                <span>Location <span class="text-danger">*</span></span>
                <select aria-label="Location" class="form-select js-example-basic-single" name="location_id" id="topup_location_id" onchange="loadTopupStationeryItems()">
                    <option value="">Select Location</option>
                    <?php if(count((array)$loc_data) > 0) {
                        foreach((array)$loc_data as $loc) { ?>
                            <option <?php if($use_loc_id == $loc['id']){ echo "selected";
} ?> value="<?php echo $loc['id']; ?>"><?php echo $loc['location_name']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
            <div class="form-group w-full col-md-6">
                <span>Category <span class="text-danger">*</span></span>
                <select aria-label="Category" class="form-select js-example-basic-single" name="category_id" id="topup_category_id" onchange="loadTopupStationeryItems()">
                    <option value="">Select Category</option>
                    <?php if(count((array)$cat_data) > 0) {
                        foreach((array)$cat_data as $cat) { ?>
                            <option <?php if($cat_id == $cat['id']){ echo "selected";
} ?> value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                    <?php }
                    } ?>
                </select>
            </div>
            <div class="form-group w-full col-md-6">
                <span>Product <span class="text-danger">*</span></span>
                <select aria-label="Item" class="form-select js-example-basic-single" name="item_id" id="topup_item_id">
                    <option value="">Select Item</option>
                </select>
            </div>

            <div class="form-group col-md-6">
                <span>Quantity <span class="text-danger">*</span></span>
                <input aria-label="Please Enter Your Quantity" placeholder="Please Enter Your Quantity" name="available_qty" id="topup_available_qty" type="text" class="form-control text-right fill">
            </div>
            <div class="form-group col-md-6">
                <span>Date</span>
                <input aria-label="Request Date"  name="request_date" id="request_date" type="date" class="form-control text-right fill">
            </div>
        </div>
            <div class="form-group">
                <span>Comments</span>
                <textarea aria-label="Please describe your product" class="form-control" name="comment" id="comment" placeholder="Please describe your product"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submittopupproductrfrom()">Save</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
<script type="text/javascript">
    $(".js-example-basic-single").select2({
      dropdownParent: $("#kt_modal_KTDatatable_remote")
  });
    $(".js-example-basic-multiple").select2();
    $(".js-example-disabled-results").select2();
    $(".datepicker").datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
    <?php if($cat_id != ""){ ?>
    loadTopupStationeryItems();
    <?php } ?>
</script>
