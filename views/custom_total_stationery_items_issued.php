<?php
$_SESSION['apply_js']['multiselest'] = 1;

/* Default dates */
$from_date = date('Y-m-01');
$to_date   = date('Y-m-d');

$url = API . "?mode=getTotalItemsIssuedCategoryWise";
$che = callAPI($url);
$data = json_decode($che, 1);

$category_res = $data['category_res'] ?? array();
$item_res     = $data['item_res'] ?? array();
$location_res = $data['location_res'] ?? array();
?>
<div class="card">
    <div class="card-header">
        <h5>Total Items Issued (Category-wise)</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row align-items-end">
            <div class="col-lg-2">
                <span>Location</span>
                <select aria-label="Location" class="form-control js-example-basic-single" id="location_id">
                    <option value="">All Locations</option>
                    <?php
                    if(count((array)$location_res) > 0){
                        foreach((array)$location_res as $loc){
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

            <div class="col-lg-3">
                <span>Categories</span>
                <select aria-label="Category Ids" class="form-control js-example-basic-single" multiple id="category_ids">
                    <?php
                    if(count((array)$category_res) > 0){
                        foreach((array)$category_res as $cat){
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

            <div class="col-lg-3">
                <span>Items</span>
                <select aria-label="Item Ids" class="form-control js-example-basic-single" multiple id="item_ids">
                    <?php
                    if(count((array)$item_res) > 0){
                        foreach((array)$item_res as $item){
                            ?>
                            <option value="<?php echo $item['id']; ?>">
                                <?php echo htmlspecialchars($item['item_name']); ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-lg-2">
                <span>From Date</span>
                <input aria-label="From Date" type="date" class="form-control" id="from_date"
                       value="<?php echo $from_date; ?>">
            </div>

            <div class="col-lg-2">
                <span>To Date</span>
                <input aria-label="To Date" type="date" class="form-control" id="to_date"
                       value="<?php echo $to_date; ?>">
            </div>

            <div class="col-lg-2">
                <button type="button" class="btn btn-primary"
                        id="search_total_issued">
                    Search
                </button>
            </div>

        </div>
    </div>
</div>

<div id="dynamic_table_page"></div>
<script type="text/javascript">
    $('#search_total_issued').on('click', function() {
        var category_ids = $('#category_ids').val();
        var item_ids = $('#item_ids').val();
        var location_id = $('#location_id').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        if (from_date == '') {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select From Date.', '');
            return false;
        }
        if (to_date == '') {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please select To Date.', '');
            return false;
        }
        if (new Date(to_date) < new Date(from_date)) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'To Date cannot be earlier than From Date.', '');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "views/custom_total_stationery_items_issued_table.php",
            data: {
                category_ids: category_ids,
                item_ids: item_ids,
                location_id: location_id,
                from_date: from_date,
                to_date: to_date
            },
            success: function(response) {
                $('#dynamic_table_page').html(response);
            },
            error: function() {}
        });
    });
</script>
