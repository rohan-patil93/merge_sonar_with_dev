<?php
$_SESSION['apply_js']['multiselest'] = 1;
$url = API . "?mode=getStationeryAvailableItemsData";
$che = callAPI($url);
$data = json_decode($che, 1);
$category_res = $data['category_res'] ?? array();
$location_res = $data['location_res'] ?? array();
?>
<div class="card">
    <div class="card-header">
        <h5>Available Stationery Items</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-lg-4">
                <span>Location</span>
                <select aria-label="Location" class="form-control js-example-basic-single" id="location_id">
                    <option value="">All Locations</option>
                    <?php
                    if (count((array)$location_res) > 0) {
                        foreach ((array)$location_res as $loc) {
                    ?>
                            <option value="<?php echo $loc['id']; ?>">
                                <?php echo htmlspecialchars($loc['location_name']); ?>
                            </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="col-lg-6">
                <span>Category</span>
                <select aria-label="Category Ids" class="form-control js-example-basic-single" multiple id="category_ids">
                    <?php
                    if (count((array)$category_res) > 0) {
                        foreach ((array)$category_res as $cat) {
                    ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                    <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-lg-2">
                <button type="button" class="btn btn-primary" id="search_available_items">
                    Search
                </button>

            </div>

        </div>
    </div>
</div>

<div id="dynamic_table_page">

</div>

<script type="text/javascript">
    $('#search_available_items').on('click', function() {
        var category_ids = $('#category_ids').val();
        var location_id = $('#location_id').val();
        $.ajax({
            type: "POST",
            url: "views/custom_stationery_available_items_table.php",
            data: {
                category_ids: category_ids,
                location_id: location_id
            },
            success: function(response) {
                $('#dynamic_table_page').html(response);
            },
            error: function() {}
        });
    });
</script>

