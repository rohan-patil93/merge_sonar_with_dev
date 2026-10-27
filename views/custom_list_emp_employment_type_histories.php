
<?php
if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $month = $rid['1'];
}
?>
<style>
    .month-input {
        max-width: 200px;
    }
</style>

<div class="page-body">
    <div class="card proj-progress-card">
        <div class="card-header">
            <h5>Customer Billing Summary</h5>
        </div>
        <div class="card-block">
            <div class="row">
                <div class="col-md-4 form-group mr-3">
                    <label for="monthPicker">Select Year-Month:</label>
                    <input type="month" id="monthPicker" class="form-control" value="<?= $month ?? date('Y-m') ?>">
                </div>
            </div>
            <div id="dataTableWrapper" class="mt-4">
            </div>
        </div>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
<script>
    function loadTableData(month) {
        $.ajax({
            url: 'views/get_customer_billing_data.php',
            type: 'POST',
            data: {
                month: month
            },
            success: function(response) {
                $('#dataTableWrapper').html(response);
            },
            error: function() {
                $('#dataTableWrapper').html('<tr><td colspan="7" class="text-danger">Error loading data</td></tr>');
            }
        });
    }

    $(document).ready(function() {
        const defaultMonth = $('#monthPicker').val();
        loadTableData(defaultMonth);

        $('#monthPicker').on('change', function() {
            loadTableData($(this).val());
        });
    });
</script>
