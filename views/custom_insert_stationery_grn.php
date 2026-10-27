<?php
$_SESSION['apply_js']['multiselest'] = 1;

if ($_POST['submit'] == 'insert') {
    $url = API . "?mode=insertstationerydata&method=post";
    $response = file_post_contents($url, $_POST, 'insert');
    $result = json_decode($response, true);
}

if ($_GET['Rid']) {
    $rid = encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $grn_id = $rid['1'];
}
$url = API . "?mode=getstationeryrelateddata&method=get&grn_id=" . $grn_id;
$result = callAPI($url);
$result = json_decode($result, true);
$items       = $result['items_res'] ?? array();
$grn_master  = $result['grn_master'] ?? array();
$grn_items   = $result['grn_items'] ?? array();
?>

<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Stationery GRN</h5>
            <div class="card-header-right">
                <a href="index.php?option=COM_LIST_STATIONERY_GRN" class="btn btn-success mb-2 desktop-view">Stationery GRN List</a>
                <button type="button" onclick="history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i> Back</button>
            </div>
        </div>

        <div class="card-infisuite mt-2">
            <div class="card-body p-4">
                <form id="grnForm" method="post" action="">
                    <input type="hidden" name="grn_id" value="<?php echo $grn_id; ?>">
                    <div class="row">
                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Transaction No.</span>
                            <input aria-label="Transaction No" name="transaction_no" id="transaction_no" class="form-control" type="text"
                                value="<?php echo $grn_master['transaction_no'] ?? ''; ?>" readonly />
                        </div>
                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Transaction Date</span>
                            <input aria-label="Transaction Date" name="transaction_date" id="transaction_date" class="form-control" type="date"
                                value="<?php echo $grn_master['transaction_date'] ?? date('Y-m-d'); ?>" required />
                        </div>
                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Supplier Name</span>
                            <input aria-label="Supplier" class="form-control" type="text" name="supplier" id="supplier"
                                value="<?php echo $grn_master['supplier'] ?? ''; ?>" required />
                        </div>
                        <div class="col-lg-6 col-xs-12 form-group">
                            <span>Supplier Invoice No</span>
                            <input aria-label="Supplier Invoice No" class="form-control" type="text" name="supplier_invoice_no" id="supplier_invoice_no"
                                value="<?php echo $grn_master['supplier_invoice_no'] ?? ''; ?>" />
                        </div>

                    </div>

                    <hr>
                    <h6>GRN Items</h6>
                    <table class="table table-bordered" id="grnItemsTable">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Amount</th>
                                <th scope="col" style="width: 5%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="grnItemsBody">
                            <?php if (!empty($grn_items)) { ?>
                                <?php foreach ($grn_items as $index => $item) { ?>

                                    <tr data-row="<?php echo $index + 1; ?>">
                                        <input aria-label="Grn Items []" name="grn_items_id[]" value="<?php echo $item['id'] ?? ''; ?>" type="hidden">

                                        <td class="w-full">
                                            <select aria-label="Item []" name="item_id[]" class="form-control js-example-basic-single" required>
                                                <option value="">Select Item</option>
                                                <?php foreach ($items as $it) { ?>
                                                    <option value="<?php echo $it['id']; ?>"
                                                        <?php echo ($it['id'] == $item['item_id']) ? 'selected' : ''; ?>>
                                                        <?php echo $it['item_name']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                        <td><input aria-label="Qty[]" type="number" name="qty[]" class="form-control qty" value="<?php echo $item['qty']; ?>" min="1" required></td>
                                        <td><input aria-label="Amount[]" type="number" name="amount[]" class="form-control amount" value="<?php echo $item['amount']; ?>" min="0" step="0.01" required></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                                <?php } ?>
                            <?php } else { ?>
                                <tr data-row="1">
                                    <td class="w-full">
                                        <input name="grn_items_id[]" value="" type="hidden">

                                        <select aria-label="Item []" name="item_id[]" class="form-control js-example-basic-single" required>
                                            <option value="">Select Item</option>
                                            <?php foreach ($items as $it) { ?>
                                                <option value="<?php echo $it['id']; ?>"><?php echo $it['item_name']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td><input aria-label="Qty[]" type="number" name="qty[]" class="form-control qty" min="1" required></td>
                                    <td><input aria-label="Amount[]" type="number" name="amount[]" class="form-control amount" min="0" step="0.01" required></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end">Total Amount</td>
                                <td>
                                    <input aria-label="Total Amount" class="form-control" type="number" name="total_amount" id="total_amount"
                                        value="<?php echo $grn_master['total_amount'] ?? ''; ?>" readonly />
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <button type="button" id="addRow" class="btn btn-secondary mb-2"><i class="icofont icofont-plus"></i> Add More</button>

                    <div class="border-top pt-3">
                        <input name="submit" value="insert" type="hidden">
                        <input aria-label="Grn" name="grn_id" value="<?php echo $grn_master['id'] ?? ''; ?>" type="hidden">
                        <button type="submit" class="btn waves-effect waves-light btn-primary mt-2">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let rowIndex = document.querySelectorAll('#grnItemsBody tr').length;

    function getRowHtml(index) {
        return `
        <tr data-row="${index}">
        <input name="grn_items_id[]" value="" type="hidden">
            <td class="w-full">
                <select aria-label="Item []" name="item_id[]" class="form-control js-example-basic-single" required>
                    <option value="">Select Item</option>
                    <?php foreach ($items as $item) { ?>
                        <option value="<?php echo $item['id']; ?>"><?php echo $item['item_name']; ?></option>
                    <?php } ?>
                </select>
            </td>
            <td><input aria-label="Qty[]" type="number" name="qty[]" class="form-control qty" min="1" required></td>
            <td><input aria-label="Amount[]" type="number" name="amount[]" class="form-control amount" min="0" step="0.01" required></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRowBtn"><i class="fa fa-trash"></i></button></td>
        </tr>`;
    }

    document.getElementById('addRow').addEventListener('click', function() {
        rowIndex++;
        const newRow = getRowHtml(rowIndex);
        document.querySelector('#grnItemsBody').insertAdjacentHTML('beforeend', newRow);
        $('.js-example-basic-single').select2();
    });

    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeRowBtn')) {
            const allRows = document.querySelectorAll('#grnItemsBody tr');
            if (allRows.length > 1) {
                e.target.closest('tr').remove();
                updateTotal();
            }
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('amount')) {
            updateTotal();
        }
    });

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.amount').forEach(el => {
            total += parseFloat(el.value) || 0;
        });
        document.getElementById('total_amount').value = total.toFixed(2);
    }

    window.addEventListener('load', updateTotal);
</script>
