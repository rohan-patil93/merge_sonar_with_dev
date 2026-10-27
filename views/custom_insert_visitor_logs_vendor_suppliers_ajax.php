<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$transaction_id = isset($_POST['transaction_id']) ? (int)$_POST['transaction_id'] : 0;
$transaction_table = isset($_POST['transaction_table']) ? trim($_POST['transaction_table']) : '';
$url = API . "?mode=getVisitorInsertRelatedData&method=get&transaction_id=".$transaction_id."&transaction_table=".$transaction_table;
$result1 = callAPI($url);
$result = json_decode($result1, 1);
print_r($result);
$transaction = $result['transaction'];
$txnItems = $transaction_items = $result['transaction_items'];
$transaction_item_taxes = $result['transaction_item_taxes'];
$allUoms = $unit_of_measurements = $result['unit_of_measurements'];
$vendor_id = $transaction['party_id'];
$allItems = $result['items'];
$htmlview = '';

if(count((array)$transaction_items) > 0){
    // 5) Build HTML view table
    $htmlview = '
    <div class="table-responsive">
        <table class="table table-hover" id="invoiceItem">
            <thead>
                <tr>
                    <th scope="col" class="p-1" style="width: 35%;">Items/Service</th>
                    <th scope="col" class="thheader p-1" style="width: 30%;">Description</th>
                    <th scope="col" class="thheader p-1 text-right" style="width: 10%;">QTY</th>
                    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Received Qty <span class="requried">*</span></th>
                    <th scope="col" class="thheader p-1" style="width: 5%;">UOM <span class="requried">*</span></th>
                </tr>
            </thead>
            <tbody>';

    if ($txnItems) {
        foreach ($txnItems as $idx => $row) {
            $rowOptions = '<option value="">Select Any one</option>';
            if ($allItems) {
                foreach ($allItems as $it) {
                    $selected = ($it['id'] == $row['item_id']) ? 'selected' : '';
                    $label = htmlspecialchars(($it['sku'] ? $it['sku'] . ' - ' : '') . $it['item_name']);
                    $rowOptions .= '<option value="' . $it['id'] . '" ' . $selected . ' data-uom="' . htmlspecialchars($it['purchase_uom'] ?? '') . '">' . $label . '</option>';
                }
            }

            $desc = htmlspecialchars($row['description'] ?? $row['item_name'] ?? '');
            $qty = (float)($row['item_quantity'] ?? 0);
            $uomId = $row['purchase_uom'] ?? '';

            $uomOptions = '<option value="">-- UOM --</option>';
            if ($allUoms) {
                foreach ($allUoms as $u) {
                    $sel = ($u['id'] == $uomId) ? 'selected' : '';
                    $label = htmlspecialchars($u['uom_name'] . ($u['uom_short_code'] ? ' (' . $u['uom_short_code'] . ')' : ''));
                    $uomOptions .= '<option value="' . $u['id'] . '" ' . $sel . '>' . $label . '</option>';
                }
            }

            $htmlview .= '
                <tr>
                    <td class="p-1 w-full">
                        <select aria-label="Item [" class="form-select js-example-basic-single js-ref-item" name="item_id[' . $idx . ']" id="item_id_' . $idx . '">
                            ' . $rowOptions . '
                        </select>
                        <input type="hidden" name="item_name[' . $idx . ']" value="' . htmlspecialchars($row['item_name'] ?? '') . '">
                    </td>
                    <td class="p-1">
                        <input aria-label="Description[" class="form-control" type="text" name="description[' . $idx . ']" value="' . $desc . '">
                    </td>
                    <td class="p-1">
                        <input aria-label="Qty[" class="form-control text-right" type="number" step="0.01" name="qty[' . $idx . ']" value="' . $qty . '" readonly>
                    </td>
                    <td class="p-1">
                        <input aria-label="Received Qty[" class="form-control text-right" type="number" step="0.01" name="received_qty[' . $idx . ']" value="' . $qty . '">
                    </td>
                    <td class="p-1">
                        <select aria-label="Purchase Uom[" class="form-select" name="purchase_uom[' . $idx . ']">
                            ' . $uomOptions . '
                        </select>
                    </td>
                </tr>';
        }
    } else {
        $htmlview .= '<tr><td colspan="5" class="text-center p-3 text-muted">No items found in transaction</td></tr>';
    }

    $htmlview .= '
            </tbody>
        </table>
    </div>';
} else {
    $htmlview = '
    <div class="table-responsive">
        <table class="table table-hover" id="invoiceItem_2">
            <thead>
                <tr>
                    <th scope="col" class="p-1" style="width: 35%;">Items/Service</th>
                    <th scope="col" class="thheader p-1" style="width: 30%;">Description</th>
                    <th scope="col" class="thheader p-1 text-right" style="width: 10%;">QTY</th>
                    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Received Qty <span class="requried">*</span></th>
                    <th scope="col" class="thheader p-1" style="width: 5%;">UOM <span class="requried">*</span></th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="5" class="text-center p-3 text-muted">No Reference Document selected</td></tr>
            </tbody>
        </table>
    </div>';
}

echo json_encode([
    'vendor_id' => $vendor_id,
    'htmlview' => $htmlview
]);
exit;
