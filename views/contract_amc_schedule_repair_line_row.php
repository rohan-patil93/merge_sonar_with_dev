<?php

if (!function_exists('_contract_amc_repair_esc')) {
    function _contract_amc_repair_esc($value) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('_contract_amc_repair_render_item_cell')) {
    function _contract_amc_repair_render_item_cell($rowEditable, $lineIdx, $line, $itemsList, $lineItemId) {
        if (!$rowEditable) {
            $displayItem = trim(($line['sku'] ?? '') . ' ' . ($line['item_name'] ?? ''));
            if ($displayItem == '') {
                $displayItem = trim(($line['asset_code'] ?? '') . ' ' . ($line['asset_description'] ?? ''));
            }
            echo _contract_amc_repair_esc($displayItem != '' ? $displayItem : '—');
            return;
        }
?>
        <input type="hidden" name="repair_lines[<?php echo $lineIdx; ?>][line_id]" value="<?php echo (int) ($line['id'] ?? 0); ?>">
        <select class="form-select form-select-sm js-example-basic-single repair-line-item" name="repair_lines[<?php echo $lineIdx; ?>][item_id]" aria-label="<?= _contract_amc_repair_esc(__('Select')) ?>">
            <option value=""><?= __('Select') ?></option>
            <?php foreach ((array)$itemsList as $it) {
                $it = is_array($it) ? $it : (array) $it;
                $iid = (int) ($it['id'] ?? 0);
                $label = trim(($it['sku'] ?? '') . ' - ' . ($it['item_name'] ?? ''));
                if ($label == '') {
                    $label = $it['item_description'] ?? ('Item ' . $iid);
                }
            ?>
            <option value="<?php echo $iid; ?>" <?php echo ($lineItemId == $iid) ? 'selected' : ''; ?>><?php echo _contract_amc_repair_esc($label); ?></option>
            <?php } ?>
        </select>
<?php
    }
}

if (!function_exists('_contract_amc_repair_render_cell')) {
    function _contract_amc_repair_render_cell($rowEditable, $lineIdx, $field, $value, $fallback, $type = 'text', $extraClass = '', $placeholder = '', $ariaLabel = null) {
        if (!$rowEditable) {
            echo _contract_amc_repair_esc($value ?? $fallback);
            return;
        }
        $inputAria = $ariaLabel !== null ? $ariaLabel : __('Input');
        $stepAttr = ($type === 'number') ? ' step="0.01" min="0"' : '';
        $phAttr = ($placeholder !== '') ? ' placeholder="' . _contract_amc_repair_esc($placeholder) . '"' : '';
        $cls = trim('form-control form-control-sm ' . $extraClass);
        echo '<input type="' . $type . '"' . $stepAttr . ' class="' . $cls . '" name="repair_lines[' . $lineIdx . '][' . $field . ']" value="' . _contract_amc_repair_esc($value ?? '') . '"' . $phAttr . ' aria-label="' . _contract_amc_repair_esc($inputAria) . '">';
    }
}

if (!function_exists('render_contract_amc_schedule_repair_line_row')) {
    function render_contract_amc_schedule_repair_line_row($ctx) {
        $line = is_array($ctx['line'] ?? null) ? $ctx['line'] : array();
        $rowEditable = !empty($ctx['isRepairEditable']);
        $itemsList = $ctx['repairItems'] ?? array();
        $lineIdx = $ctx['lineIdx'] ?? 0;
        $lineItemId = (int) ($line['item_id'] ?? 0);
?>
<tr class="contract-amc-repair-line-row">
    <td>
        <?php _contract_amc_repair_render_item_cell($rowEditable, $lineIdx, $line, $itemsList, $lineItemId); ?>
    </td>
    <td>
        <?php _contract_amc_repair_render_cell($rowEditable, $lineIdx, 'part_description', $line['part_description'] ?? null, '—'); ?>
    </td>
    <td>
        <?php _contract_amc_repair_render_cell($rowEditable, $lineIdx, 'qty', $rowEditable ? ($line['qty'] ?? '1') : ($line['qty'] ?? null), '—', 'number'); ?>
    </td>
    <td>
        <?php $opt = __('Optional'); ?>
        <?php _contract_amc_repair_render_cell($rowEditable, $lineIdx, 'po_number', $line['po_number'] ?? null, '—', 'text', 'repair-line-po', $opt, $opt); ?>
    </td>
    <td>
        <?php _contract_amc_repair_render_cell($rowEditable, $lineIdx, 'amount', $line['amount'] ?? '0', '0', 'number'); ?>
    </td>
    <?php if ($rowEditable) { ?>
    <td class="text-center">
        <button type="button" class="btn btn-link text-danger btn-sm contract-amc-repair-remove-line p-0" title="<?= _contract_amc_repair_esc(__('Remove')) ?>"><i class="ti-trash"></i></button>
    </td>
    <?php } ?>
</tr>
<?php
    }
}
?>
