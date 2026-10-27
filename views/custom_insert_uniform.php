<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = 1;
$location_id = isset($_SESSION['location_id']) ? $_SESSION['location_id'] : 1;
$rid = $_GET['rid'] ?? '435';
$url = API . "?mode=getUniformStockData" . "&company_id=" . $_SESSION['company_id'] . "&location_id=" . $location_id."&rid=".$rid;

$response = callAPI($url);
$data = json_decode($response, true);
$editData = $data['item'] ?? array();

?>

<style>
    .section-title {
        font-size: 17px;
        font-weight: 700;
        color: #4B49AC;
        margin-bottom: 15px;
        border-left: 4px solid #4B49AC;
        padding-left: 10px;
    }

    .form-label {
        font-weight: 600;
    }

</style>

    <div class="card ">
        <div class="card-header"><h5 class="mb-4 text-primary">Add Uniform Stock</h5></div>
        <div class="card-body">

            <form method="post" id="uniformForm">

                <div class="row">
                    <!-- Location -->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Location</span>
                            <select aria-label="Location" name="location_id" id="location_id" class="form-control select2" required>
                                <?php foreach ((array)$data['locations'] as $loc) { ?>
                                    <option value="<?php echo $loc['id']; ?>"
                                        <?php
                                            if($rid=="" && $loc['id']==$_SESSION['location_id']) { echo "selected";
}
                                            if($rid!="" && $loc['id']==$editData['location_id']) { echo "selected";
}
                                        ?>>
                                        <?php echo $loc['loc_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <!-- Category (readonly) -->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Category</span>
                            <input aria-label="Input field" type="text" class="form-control" value="<?php echo $data['category_name']; ?>" readonly>
                            <input type="hidden" name="category_id" id="category_id" value="<?php echo $data['category_id']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Item Name</span>
                            <input aria-label="Enter item name" type="text" name="item_name" id="item_name" class="form-control" placeholder="Enter item name" value="<?php echo $rid!="" ? $editData['item_name'] : ""; ?>" required onblur="checkItemName()">
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Description</span>
                            <textarea aria-label="Description" type="text" name="description" class="form-control" placeholder="Description"><?php echo $editData['description'] ?? ''; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Quantity</span>
                            <input aria-label="Available Qty" type="number" name="available_qty" class="form-control" min="0" value="<?php echo $editData['available_qty'] ?? ''; ?>" required placeholder="0">
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Minimum Quantity</span>
                            <input aria-label="Minmum Qty" type="number" name="minmum_qty" class="form-control" min="0" placeholder="0" value="<?php echo $editData['minmum_qty'] ?? ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Color</span>
                            <select aria-label="Color" name="color" class="form-control select2" required>
                                <option value="">Select Color</option>
                                <?php foreach ($data['colors'] as $c) { ?>
                                    <option value="<?php echo $c['genc_detail_code']; ?>"
                                        <?php echo ($rid!="" && $editData['color']==$c['genc_detail_code']) ? "selected" : ""; ?>>
                                        <?php echo $c['color_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <span class="form-label">Size</span>
                            <select aria-label="Size" name="size" class="form-control select2" required>
                                <option value="">Select Size</option>
                                <?php foreach ($data['sizes'] as $s) { ?>
                                    <option value="<?php echo $s['genc_detail_code']; ?>"
                                        <?php echo ($rid!="" && $editData['size']==$s['genc_detail_code']) ? "selected" : ""; ?>>
                                        <?php echo $s['size_name']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="rid" id="rid" value="<?php echo $rid ?? ''; ?>">

                <div class="text-start">
                    <button type="submit" class="btn btn-primary px-5 py-2">
                        <i class="fa fa-check-circle me-2"></i> Save Stock
                    </button>
                </div>

            </form>
        </div>
    </div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: "100%"
        });
    });
</script>
<script>
    $("#uniformForm").on("submit", function(e) {
        e.preventDefault();

        $.ajax({
            url: "ajax/uniform_ajax.php?page=saveUniformStock",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                var r = JSON.parse(res);
                if (r.status == "success") {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX',
                    'Data Saved Successfully', '');
                    location.reload();
                } else {
                    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX',
                    'Unknown Error', '');
                }
            },
            error: function() {
                alert("Failed to save stock");
            }
        });
    });
function checkItemName() {
    let item_name   = $("#item_name").val().trim();
    let location_id = $("select[name='location_id']").val();
    let category_id = $("#category_id").val();
    let rid         = $("#rid").val(); // edit mode item ID

    if(item_name === '') return;

    $.ajax({
        url: 'ajax/uniform_ajax.php',
        type: 'POST',
        data: {
            page: 'checkUniformItemNameExists',
            item_name: item_name,
            location_id: location_id,
            category_id: category_id,
            exclude_id: rid // important
        },
        success: function(res){
            let r = JSON.parse(res);
            if(r.exists == 1){
                tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX',
                    item_name+' Item name already exists for this location & category!', '');
                $("#item_name").val('').focus();
            }
        }
    });
}

</script>
