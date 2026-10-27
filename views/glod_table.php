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
                <input aria-label="Check All" id="checkAll"  type="checkbox">
                <span></span>
            </label>
    </th>
    <th scope="col" class="" style="width: 16%;">Descrption <span class="requried"></span></th>
    <th scope="col" class="" style="width: 8%;">Code</th>
    <th scope="col" class="thheader" style="width: 8%;">Gross WT</th>
    <th scope="col" class="thheader" style="width: 8%;">Net WT </th>
    <th scope="col" class="thheader" style="width: 8%;">VA </th>
    <th scope="col" class="thheader" style="width: 8%;">Amount </th>
</tr>
<tr>
    <td>
        <label class="kt-checkbox"><input aria-label="Checkbox" class="form-control itemRow" type="checkbox"> <span></span> </label>
    </td>
    <td>
        <select aria-label="Product Code[]" class="form-control"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
            <option value="0"> Select Any Category  </option>
            <?php foreach ($itemres as $item) {  ?>
            <option <?php if($tab2['item_id'] == $item['id']){ ?>selected <?php
} ?> value = <?php echo $item['id']; ?>   > <?php echo $item['item_name']; ?>
            <?php } ?>
            </option> <option value="-1">Other</option>
        </select>
    </td>
    <td>
        <input type="hidden"  name="product_name" value="">
         <input aria-label="Product Code" type="text" class="form-control" name="product_code" value="">
    </td>
    <td>
        <input aria-label="Product Grosss[]" type="text" name="product_grosss[]" id="gross_<?php echo $i; ?>"    class="form-control amount" value="1"  autocomplete="off">
    </td>
    <td>
       <input aria-label="Product Net[]" type="text" name="product_net[]" id="net_<?php echo $i; ?>"    class="form-control amount" value="1"  autocomplete="off">
    </td>
    <td>
        <select aria-label="Product Code[]" class="form-control"  name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode2_<?php echo $i; ?>">
            <option value="0"> Select Any Category  </option>
            <?php for($k=0;$k<21;$k++) {  ?>
                <option <?php if($tab2['item_id'] == $item['id']){ ?>selected <?php
} ?> value = <?php echo $k; ?>   > <?php echo $k."%"; ?>
            <?php } ?>
        </select>
    </td>
    <td>
        <input aria-label="Amount[]" type="text" name="amount[]" id="amount_<?php echo $i; ?>"  class="form-control amount" value="1"  autocomplete="off">
    </td>
</tr>

    </table>
