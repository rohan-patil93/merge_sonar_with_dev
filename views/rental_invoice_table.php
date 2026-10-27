<?php
    $data2["field"] = "id,code";
    $data2["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=> '0');
    $getencod = urlencode(json_encode($data2));
    $url = API."?mode=gettabledetailswithconditions&table=hsn_and_sac_codes&data=".$getencod;
    $res11 = callAPI($url);
    $res11 = json_decode($res11, 1);
    foreach ((array)$res11 as  $hsnn) {
        $hsn_and_sac_codes[$hsnn['id']] = $hsnn['code'];
    }
?>
<table class=" table table-hover " id="invoiceItem">
<tr>
    <th scope="col" style="width: 30%;" class="p-1">Items/Service <span class="requried"></span></th>
    <th scope="col" class="thheader p-1" style="width: 10%;">HSN/Tax <span class="requried">*</span></th>
    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Qty <span class="requried">*</span></th>
    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">UOM <span class="requried">*</span></th>
    <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Rate <span class="requried">*</span></th>
    <th scope="col" class="thheader p-1 text-right" style="width: 20%;">Amount</th>
</tr>
<?php if(count((array)$table2)>0){ $i=1;
 foreach ($table2 as $tab2) {

    $taxabled_amount = $tab2['taxabled_amount'] !=""?$tab2['taxabled_amount']:$tab2['total_amount'];
  ?>
<tr>
    <td class="p-1" id='other_<?php echo $i; ?>' style="width: 150px;">
        <input type="hidden" class="itemRow">
        <input type="hidden" name="productCode[]" id="productCode_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_id']; ?>">
        <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off" readonly>
        <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control mt-2 " autocomplete="off"><?php echo $tab2['description']; ?></textarea>
    </td>
        <td class="p-1 w-full text-center" id="hsn_and_sac_code_id_<?php echo $i; ?>">
        <?php if($tab2['tax_value'] != "" && $tab2['tax_value'] != "0"){ ?>
            <span class="d-block">HSN - <?php echo $hsn_and_sac_codes[$tab2['tax_id']]; ?></span>
            <span class="d-block">Tax - <?php echo $tab2['tax_value']; ?></span>
      <?php }else{ echo " - ";
} ?>
    </td>

    <td class="p-1">
        <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off" readonly>
        <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control" value="<?php echo $tab2['quantity']; ?>">
    </td>
    <td class="p-1"><input aria-label="Uom[]" type="text" name= "uom[]"  id="uom_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['uom']; ?>" autocomplete="off" readonly></td>
    <td class="p-1">
        <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off" readonly>

        <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
    </td>

    <td class="p-1">
        <input type="hidden" name="tax[]" id="tax_<?php echo $i; ?>" value="<?php echo $tab2['tax_value']; ?>">
        <input type="hidden" name="taxid[]" id="taxid_<?php echo $i; ?>" value="<?php echo $tab2['tax_id']; ?>">
         <input type="hidden" name="tax_name[]" id="tax_name_<?php echo $i; ?>" value="<?php echo $tab2['tax_name']; ?>">
         <?php if($tab2['total_amount'] > 0){ $data_content = '<div class="dropdown-item"><i class="flaticon-coins"></i>&nbsp;&nbsp; A :'.customeummarmonyformate($response_code[$tab2['purchase_account']]['a']).'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i>&nbsp;&nbsp; B :'.customeummarmonyformate($response_code[$tab2['purchase_account']]['b']).'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i>&nbsp;&nbsp; C :'.customeummarmonyformate($response_code[$tab2['purchase_account']]['c']).'</div>'; if($tab2['total_amount'] > $response_code[$tab2['purchase_account']]['a']){ $sty = "style='color:#F00'";
}
}else{ $data_content = '<div class="dropdown-item">No Item Selected</div>';
}
             ?>
        <div> <div style="float: left;width: 90%;">

        <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">

        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">

        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">

        <input type="hidden" name="p_amount[]" value="<?php echo $tab2['item_price']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">

        <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($taxabled_amount); ?>" autocomplete="off">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['item_price']; ?>">
    </div>
    </div>
    <input type="hidden" class="form-control"  value="<?php echo reqDate($tab2['req_date']); ?>"  name="req_date[]"  id="kt_datepicker_3<?php echo $dateid; ?>" />
</td>

</tr>

<?php $i++; ?>
<?php } ?>
<?php } ?>
    </table>
