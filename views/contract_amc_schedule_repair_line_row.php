<?php

$line = is_array($line ?? null) ? $line : array();

$rowEditable = !empty($isRepairEditable);

$itemsList = $repairItems ?? array();

$lineItemId = (int) ($line['item_id'] ?? 0);

?>

<tr class="contract-amc-repair-line-row">

    <td>

        <?php if ($rowEditable) { ?>

        <input type="hidden" name="repair_lines[<?php echo $lineIdx; ?>][line_id]" value="<?php echo (int) ($line['id'] ?? 0); ?>">

        <select class="form-select form-select-sm js-example-basic-single repair-line-item" name="repair_lines[<?php echo $lineIdx; ?>][item_id]">

            <option value="">Select</option>

            <?php if (count((array)$itemsList) > 0) {

                foreach ($itemsList as $it) {

                    $it = is_array($it) ? $it : (array) $it;

                    $iid = (int) ($it['id'] ?? 0);

                    $label = trim(($it['sku'] ?? '') . ' - ' . ($it['item_name'] ?? ''));

                    if ($label == '') {
                        $label = $it['item_description'] ?? ('Item ' . $iid);
                    }

                    ?>

            <option value="<?php echo $iid; ?>" <?php echo ($lineItemId == $iid) ? 'selected' : ''; ?>><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>

            <?php }

            } ?>

        </select>

        <?php } else {

            $displayItem = trim(($line['sku'] ?? '') . ' ' . ($line['item_name'] ?? ''));

            if ($displayItem == '') {
                $displayItem = trim(($line['asset_code'] ?? '') . ' ' . ($line['asset_description'] ?? ''));
            }

            echo htmlspecialchars($displayItem != '' ? $displayItem : '—', ENT_QUOTES, 'UTF-8');

        } ?>

    </td>

    <td>

        <?php if ($rowEditable) { ?>

        <input type="text" class="form-control form-control-sm" name="repair_lines[<?php echo $lineIdx; ?>][part_description]" value="<?php echo htmlspecialchars($line['part_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <?php } else {

            echo htmlspecialchars($line['part_description'] ?? '—', ENT_QUOTES, 'UTF-8');

        } ?>

    </td>

    <td>

        <?php if ($rowEditable) { ?>

        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="repair_lines[<?php echo $lineIdx; ?>][qty]" value="<?php echo htmlspecialchars($line['qty'] ?? '1', ENT_QUOTES, 'UTF-8'); ?>">

        <?php } else {

            echo htmlspecialchars($line['qty'] ?? '—', ENT_QUOTES, 'UTF-8');

        } ?>

    </td>

    <td>

        <?php if ($rowEditable) { ?>

        <input type="text" class="form-control form-control-sm repair-line-po" name="repair_lines[<?php echo $lineIdx; ?>][po_number]" value="<?php echo htmlspecialchars($line['po_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Optional">

        <?php } else {

            echo htmlspecialchars($line['po_number'] ?? '—', ENT_QUOTES, 'UTF-8');

        } ?>

    </td>

    <td>

        <?php if ($rowEditable) { ?>

        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="repair_lines[<?php echo $lineIdx; ?>][amount]" value="<?php echo htmlspecialchars($line['amount'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>">

        <?php } else {

            echo htmlspecialchars($line['amount'] ?? '0', ENT_QUOTES, 'UTF-8');

        } ?>

    </td>

    <?php if ($rowEditable) { ?>

    <td class="text-center">

        <button type="button" class="btn btn-link text-danger btn-sm contract-amc-repair-remove-line p-0" title="Remove"><i class="ti-trash"></i></button>

    </td>

    <?php } ?>

</tr>
