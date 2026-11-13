<?php
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
    $item_data = callAPI($itemurl);
    $itemres = json_decode($item_data, 1);
    $tds_ratesurl = API."?mode=getchartofaccountdetailsuseglaccountuser";
?>

<div class="tab-content">


<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer " id="invoiceItem">
<tr>
    <th scope="col" class="thheader" style="width: 2%;">
            <label class="kt-checkbox">
                <input aria-label="<?= htmlspecialchars(__('Check All'), ENT_QUOTES, 'UTF-8'); ?>" id="checkAll"  type="checkbox">
                <span></span>
            </label>
    </th>
    <th scope="col" class="" style="width: 16%;"><?= __('Descrption'); ?> <span class="requried"></span></th>
    <th scope="col" class="" style="width: 8%;"><?= __('Code'); ?></th>
    <th scope="col" class="thheader" style="width: 8%;"><?= __('Gross WT'); ?></th>
    <th scope="col" class="thheader" style="width: 8%;"><?= __('Net WT'); ?> </th>
    <th scope="col" class="thheader" style="width: 8%;"><?= __('VA'); ?> </th>
    <th scope="col" class="thheader" style="width: 8%;"><?= __('Amount'); ?> </th>
</tr>
<tr>
    <td>
        <label class="kt-checkbox"><input aria-label="<?= htmlspecialchars(__('Checkbox'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control itemRow" type="checkbox"> <span></span> </label>
    </td>
    <td>
        <select aria-label="<?= htmlspecialchars(__('Product Code'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
            <option value="0"> <?= __('Select Any Category'); ?>  </option>
            <?php foreach ($itemres as $item) {  ?>
            <option <?php if($tab2['item_id'] == $item['id']){ ?>selected <?php
} ?> value = <?php echo $item['id']; ?>   > <?php echo $item['item_name']; ?>
            <?php } ?>
            </option> <option value="-1"><?= __('Other'); ?></option>
        </select>
    </td>
    <td>
        <input type="hidden"  name="product_name" value="">
         <input aria-label="<?= htmlspecialchars(__('Product Code'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" name="product_code" value="">
    </td>
    <td>
        <input aria-label="<?= htmlspecialchars(__('Product Grosss'), ENT_QUOTES, 'UTF-8'); ?>[]" type="text" name="product_grosss[]" id="gross_<?php echo $i; ?>"    class="form-control amount" value="1"  autocomplete="off">
    </td>
    <td>
       <input aria-label="<?= htmlspecialchars(__('Product Net'), ENT_QUOTES, 'UTF-8'); ?>[]" type="text" name="product_net[]" id="net_<?php echo $i; ?>"    class="form-control amount" value="1"  autocomplete="off">
    </td>
    <td>
        <select aria-label="<?= htmlspecialchars(__('Product Code'), ENT_QUOTES, 'UTF-8'); ?>[]" class="form-control"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode2_<?php echo $i; ?>">
            <option value="0"> <?= __('Select Any Category'); ?>  </option>
            <?php for($k=0;$k<21;$k++) {  ?>
                <option <?php if($tab2['item_id'] == $item['id']){ ?>selected <?php
} ?> value = <?php echo $k; ?>   > <?php echo $k."%"; ?>
            <?php } ?>
        </select>
    </td>
    <td>
        <input aria-label="<?= htmlspecialchars(__('Amount'), ENT_QUOTES, 'UTF-8'); ?>[]" type="text" name="amount[]" id="amount_<?php echo $i; ?>"  class="form-control amount" value="1"  autocomplete="off">
    </td>
</tr>

    </table>
