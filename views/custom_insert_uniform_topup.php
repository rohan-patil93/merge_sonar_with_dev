<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$company_id   = $_SESSION['company_id'];
$location_id  = $_SESSION['location_id'] ?? 1;
$roles        = $_SESSION['role_ids'] ?? array();

$url = API . "?mode=getUniformItemsForTopup&company_id={$company_id
}&location_id={$location_id
}";

$response = callAPI($url);
$data = json_decode($response, true);

$locations = $data['locations'];
$items     = $data['items'];
$colors    = $data['colors'];
$sizes     = $data['sizes'];
$category_id = $data['category_id'];
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-4 text-primary">Top-Up Uniform Stock</h5>
    </div>

    <div class="card-body">
        <form id="uniformTopupForm">

            <input type="hidden" name="category_id" id="category_id" value="<?= $category_id ?>">

            <div class="row">

                <!-- Location -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="form-group">
                        <span class="fw-bold">Location</span>
                        <select aria-label="Location" name="location_id" id="location_id" class="form-select select2"
                            <?= (!in_array("UNIFORM_ADMIN", (array)$roles)) ? "disabled" : "" ?>>
                            <?php foreach($locations as $loc){ ?>
                                <option value="<?= $loc['id']; ?>"
                                    <?= ($loc['id'] == $location_id) ? "selected" : "" ?>>
                                    <?= $loc['loc_name']; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Item -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="form-group">
                        <span class="fw-bold">Item</span>
                        <select aria-label="Item" name="item_id" id="item_id" class="form-select select2" required>
                            <option value="">Select Item</option>
                            <?php foreach($items as $i){ ?>
                                <option value="<?= $i['item_name']; ?>"><?= $i['item_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Current Quantity -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="form-group">
                        <span class="fw-bold">Current Quantity</span>
                        <input aria-label="Current Qty" type="text" id="current_qty" class="form-control" readonly />
                    </div>
                </div>

            </div>

            <div class="row">

                <!-- Color -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="form-group">
                        <span class="fw-bold">Color</span>
                        <select aria-label="Color" name="color" id="color" class="form-select select2" required>
                            <option value="">Select Color</option>
                            <?php foreach($colors as $c){ ?>
                                <option value="<?= $c['genc_detail_code']; ?>"><?= $c['color_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Size -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="form-group">
                        <span class="fw-bold">Size</span>
                        <select aria-label="Size" name="size" id="size" class="form-select select2" required>
                            <option value="">Select Size</option>
                            <?php foreach($sizes as $s){ ?>
                                <option value="<?= $s['genc_detail_code']; ?>"><?= $s['size_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>

                <!-- Top-Up Quantity -->
                <div class="col-lg-4 col-md-4 col-sm-4 col-12">
                    <div class="form-group">
                        <span class="fw-bold">Top-Up Quantity</span>
                        <input aria-label="Topup Qty" type="number" name="topup_qty" id="topup_qty" class="form-control" min="1" required />
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary px-5">
                <i class="fa fa-plus"></i> Add Stock
            </button>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: "100%"
        });

        /* Load items again when location changed (Admin only) */
$("#location_id").on("change", function () {
    $.ajax({
        url: "ajax/uniform_ajax.php",
        type: "POST",
        data: {
            page: "loadUniformItemsByLocation",
            location_id: $(this).val(),
            category_id: $("#category_id").val()
        },
        success: function(res){
            let r = JSON.parse(res);
            let h = '<option value="">Select Item</option>';

            r.items.forEach(i => {
                h += `<option value="${i.item_name}">${i.item_name}</option>`;
            });

            $("#item_id").html(h).trigger("change");
        }
    });
});

$("#item_id, #color, #size").on("change", function () {
    let item_name  = $("#item_id").val();
    let color      = $("#color").val();
    let size       = $("#size").val();
    let location_id = $("#location_id").val();
    let category_id = $("#category_id").val();

    if(item_name === "" || color === "" || size === "") return;

    $.ajax({
        url: "ajax/uniform_ajax.php",
        type: "POST",
        data: {
            page: "checkUniformCombination",
            item_name: item_name,
            color: color,
            size: size,
            location_id: location_id,
            category_id: category_id
        },
        success: function(res){
            let r = JSON.parse(res);
            $("#current_qty").val(r.qty);
            $("#existing_item_id").val(r.item_id);
        }
    });
});
    });
</script>
<script>

$("#uniformTopupForm").on("submit", function(e) {
        e.preventDefault();
        var formData = $("#uniformTopupForm").serialize();

        $.ajax({
            url: "ajax/uniform_ajax.php?page=saveUniformTopup",
            type: "POST",
            data: formData,
            success: function(res) {
                var r = JSON.parse(res);
                if (r.status == "success") {
                    tostarnotify('top', 'right', 'fa fa-thumbs-up', 'form-label label label-success', 'animated flipInX', 'animated flipOutX',
                    'Top-Up Added Successfully!', '');
                    location.reload();
                } else {
                    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX',
                    'Unknown Error', '');
                }
            }
        });
    });
</script>
