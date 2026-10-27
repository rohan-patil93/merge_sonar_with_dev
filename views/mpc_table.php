<?php
if($_GET['table']!="" && $_GET['Rid']!=""){
    $vid = $_GET['Rid'];
}else{
    $vid= $table1['ref_no'];
}
$vrcdetailurl = API."?mode=getvendorratecardidordetailsid&old_ref_no=".$table1['old_ref_no']."&vrc_id=".$vid;
$vrcdetailrespons = callAPI($vrcdetailurl);
$vrcdetailrespons = json_decode($vrcdetailrespons,1);
$wwdata = array();
$wwdata["where"] = array('is_active'=> '0');
$data = urlencode(json_encode($wwdata));
$locurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$data;
$locdata = callAPI($locurl);
$locdata = json_decode($locdata, 1);
foreach($locdata as $locda){
    $locs[$locda['id']] = $locda['location_name'];
}
if(count((array)$uomlist)==0){
        $uomurl =  API."?mode=gettabledetails&fid=genc_code&rid=JOBUOM&table=".TBL_GENRAL_CODE_DETAILS;
        $uomlist = callAPI($uomurl);
        $uomlist = json_decode($uomlist,1);
    }
?>
<script type="text/javascript">
    function changeprice(val,id){
        var mval =$("#price_"+id).val();
        var dval =$("#ref_rate_"+id).val();
        if(dval < mval){
            $('#price_'+id).css('color', '#f00');
        }
    }

    function itemChangejob(item_id, id,sel){
        if(item_id != "0"){
            var select_lable = sel.options[sel.selectedIndex].text;
            var noSpacesString= select_lable.replace(/ /g,'');
            var splite_name = noSpacesString.split("(");
            var splite_exp = splite_name[1].split("-");
            var splite_maxand = splite_exp[1].split("To");
            var min_exp = splite_maxand[0];
            var  splite_maxandexp = splite_maxand[1].split(")");
            var  max_exp = splite_maxandexp[0];
            document.getElementById('min_exp_'+id).value=min_exp;
            document.getElementById('max_exp_'+id).value=max_exp;
            document.getElementById('item_name_'+id).value=splite_name;
        }else{
            document.getElementById('min_exp_'+id).value="0.00";
            document.getElementById('max_exp_'+id).value="0.00";
            document.getElementById('item_name_'+id).value="";
        }

    }

    function itemChangejob123(item_id, id){
        var data1 = 'page=dynamictablerowdata&item_id='+item_id+'&company_id='+$('#company_id').val()+'&table_name=manpower_requisitions';
        $.ajax({
            url: "ajax.php",
            data: data1,
            type: "POST",
            success:function(data){
                const words = data.split(',');
                console.log(words);
                console.log(words[1]);
                document.getElementById('min_exp_'+id).value=words[0];
                document.getElementById('max_exp_'+id).value=words[1];
            },
            error:function (){}
        });
    }

    function itemChange(item_id, id){
        if(item_id == '-1'){
            $("#other_"+id).html('<input aria-label="Item Name[]" type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'"><input type="hidden" class="form-control" value="-'+id+'" name= "productCode[]" id="productCode_'+id+'">');

        }

        $.ajax({
            url: "ajax.php",
            data:'page=ItemDetails&item_id='+item_id+'&company_id='+$('#company_id').val()+'&table_name='+$('#table_name').val()+'&ddate='+$('#transaction_date').val()+'&cost_center_id='+$('#cost_center').val()+'&fiscal_year='+$('#transaction_date').val(),
            type: "POST",
            success:function(data){
                console.log(data);
                var obj = $.parseJSON(data);
                var price =  parseFloat(obj.purchase_price).toFixed(2);
            document.getElementById('productName_'+id).value=obj.item_description;
            document.getElementById('uom_'+id).value=obj.purchase_uom;
            document.getElementById('price_'+id).value=price;
            document.getElementById('item_name_'+id).value=obj.item_name;
            document.getElementById('purchase_account_'+id).value=obj.purchase_account;
            var dd = 'A : '+obj.a+', B : '+obj.b+' C : '+obj.c;
            var text = '<div class="dropdown-item"><i class="flaticon-coins"></i> &nbsp;&nbsp; A :'+obj.a+'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> &nbsp; &nbsp; B :'+obj.b+'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> &nbsp;&nbsp; C :'+obj.c+'</div>';

            document.getElementById('budget_'+id).innerHTML = text;
            document.getElementById('purchase_account_aval_'+id).value=obj.am;
        },
        error:function (){}
    });
    }
    $(document).ready(function(){
        var count = $(".itemRow").length;
        $(document).on('click', '#addRows', function() {
            count++;
            var htmlRows = '';
            htmlRows += '<tr>';
            htmlRows += '<td><div class="checkbox-fade fade-in-primary "><label class="form-label"> <input aria-label="Checkbox" type="checkbox" class="itemRow"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div></td>';

            htmlRows +=  '<td id="other_'+count+'"><select aria-label="Product Code[]" class="form-control form-select js-example-basic-single"  onchange="itemChangejob(this.value,'+count+',this)"  name= "productCode[]" id="productCode__2"+count+'"> <option value="0"> Select Any Prodcut </option> <?php foreach($vrcdetailrespons as $vrcdetailrespone){$selected = ""; if($tab2['item_id']== $vrcdetailrespone['item_id']){ $selected = "selected";
}?> <option value="<?php echo $vrcdetailrespone['item_id']; ?>" <?php echo $selected;  ?> ><?php echo $vrcdetailrespone['job_title']." (".$vrcdetailrespone['transaction_number']." - ".$vrcdetailrespone['min_exp']." To ".$vrcdetailrespone['max_exp'].")"; ?></option> <?php
} ?> </select>  <div class="row"><div class="col-lg-6"><div class="input-group input-group-button mt-2"><input type="text" name="min_exp[]" id="min_exp_'+count+'"  class="form-control form-control-sm width1" value="" placeholder="Min Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience" >  <span class="input-group-text addon-style"> <span class="">Years</span></span></div></div><div class="col-lg-6"><div class="input-group input-group-button mt-2">  <input type="text" name="max_exp[]" id="max_exp_'+count+'" class="form-control form-control-sm width1" value="" placeholder="Max Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience">  <span class="input-group-text addon-style"><span class="">Years</span></span></div></div></div>        </td>';

            htmlRows += '<td><select aria-label="Uom[]" class="form-control form-select js-example-basic-single"   name= "uom[]" id="uom_'+count+'"><option value="0"> Select Any UOM  </option><?php foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?>selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option> <?php
} ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div><div class="checkbox-fade fade-in-primary  mt-3 float-left"> <label class="form-label"><input aria-label="Allow Overtime[" type="checkbox"  name="allow_overtime['+count+']"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span> <span>&nbsp;Overtime </span></label></div></td> ';
            htmlRows += '<td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount minw-100"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div><input type="text" name="billable[]" id="billable_'+count+'"  value="100" class="form-control price amount minw-100" autocomplete="off" data-bs-toggle="tooltip" data-placement="bottom" title="Bilable Percentage"> </td>';

            htmlRows += '<td><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount minw-140" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div> </td>';

            htmlRows += '<td><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount">  <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount minw-140" value="" autocomplete="off"> <input type="text" name="ref_rate[]" id="ref_rate_'+count+'"  class="form-control form-control-sm price amount mt-2 minw-140" readonly   value="" data-bs-toggle="tooltip" data-placement="bottom" title="Vendor Rate Card"><input type="hidden" name="item_name[]" id="item_name__2"+count+'" class="form-control"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value=""><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value=""></td>';

            htmlRows += '<td> <input type="text" class="form-control datepicker minw-140 effective_date'+count+'"  value=""  name="effective_date[]"   id="kt_datepicker_3_'+count+'" data-bs-toggle="tooltip" data-placement="bottom" title="Effective Date" > <input type="text" class="minw-140 form-control mt-2 datepicker expire_date'+count+'"  value=""  name="expire_date[]"  id="kt_datepicker_4_'+count+'" data-bs-toggle="tooltip" data-placement="bottom" title="Expire Date"  /></td>';

            htmlRows += '</tr>';
            $('#invoiceItem').append(htmlRows);
            $(".js-example-basic-single").select2();
            $(".js-example-disabled-results").select2();
            $( ".datepicker" ).datepicker({
                showOn: "both",
                buttonText: "<i class='ti-calendar'></i>",
                dateFormat: date_format,
                changeMonth: true,
                changeYear: true
            });
        });
});

function assignemployeelistnew(workorder_id,workorder_detail_id,vrc_id,item_id,item_price,effective_date,expire_date,old_ref_number,min_exp,max_exp){
    console.log(workorder_id+" -- "+workorder_detail_id+" -- "+vrc_id+" -- "+item_id+" -- "+item_price+" -- "+effective_date+"--"+expire_date);
    var table_name = document.getElementById('table_name').value;
    jQuery.ajax({
        url: "views/custom_insert_mpc_assign_employee_popup.php",
        data:'table='+table_name+'&workorder_id='+workorder_id+'&workorder_detail_id='+workorder_detail_id+'&vrc_id='+vrc_id+'&item_id='+item_id+'&item_price='+item_price+'&effective_date='+effective_date+'&expire_date='+expire_date+'&old_ref_number='+old_ref_number+"&min_exp="+min_exp+"&max_exp="+max_exp,
        type: "POST",
        success:function(data){
            $('#dynamic_popupxxl').html(data);
            $('#kt_modal_dynamic_popupxxl').modal("show");
        },
        error:function (){}
    });

}
</script>
<div class="dt-responsive table-responsive">
    <table class="table table-hover table-custom-responsive"  id="invoiceItem">
        <tr>
            <th scope="col" class="thheader" style="width: 1%;">
               <div class="checkbox-fade fade-in-primary">
                <label class="kt-checkbox">
                    <input aria-label="Check All" id="checkAll"  type="checkbox">
                    <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                  </span>
              </label>
          </div>
      </th>
      <th scope="col" class="" style="width: 30%;">job Title </th>

      <th scope="col" class="thheader" style="width: 5%;">Units</th>

      <th scope="col" class="thheader" style="width: 5%;">Vacancy</th>
      <th scope="col" class="thheader" style="width: 20%;">Base Rate  </th>

      <th scope="col" class="thheader" style="width: 20%;">Amount</th>
      <th scope="col" class="thheader" style="width: 15%;">Effective date</th>
      <th scope="col" class="thheader" style="width: 5%;"></th>
  </tr>
  <?php if(count((array)$table2)>0){ $i=1;
 foreach ($table2 as $tab2) {
     $vendor_rate = $tab2['ref_rate']!="" ? $tab2['ref_rate'] :$tab2['item_price'];
     $sty = "";
     if($tab2['item_price']>$vendor_rate){ $sty = "style='color:#F00'";
}
     ?>
     <tr>
        <td style="width: 1%;">
            <div class="checkbox-fade fade-in-primary ml-2">
                <label class="form-label">
                    <input aria-label="Checkbox" type="checkbox" class="itemRow">
                    <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                </label>
            </div>
        </td>
        <td id='other_<?php echo $i; ?>'>
            <?php if($tab2['item_id'] >= 0){  $min = array();?>
               <select aria-label="Product Code[]" class="form-control form-select js-example-basic-single"  name= "productCode[]"  id="productCode_<?php echo $i; ?>" onchange="itemChangejob(this.value,<?php echo $i; ?>,this)" >
                <option value="0"> Select Any Item  </option>
                <?php foreach($vrcdetailrespons as $vrcdetailrespone){
                    $selected = "";
                    if($tab2['item_id']== $vrcdetailrespone['item_id']&& $vrcdetailrespone['min_exp'] == $tab2['min_exp'] && $vrcdetailrespone['max_exp'] == $tab2['max_exp']){
                        $selected = "selected";
                    }
                    ?>
                    <option value="<?php echo $vrcdetailrespone['item_id']; ?>" <?php echo $selected;  ?> ><?php echo $vrcdetailrespone['job_title']." (".$vrcdetailrespone['transaction_number']." - ".$vrcdetailrespone['min_exp']." To ".$vrcdetailrespone['max_exp'].")"; ?></option>
                <?php } ?>
            </select>

            <div class="row">
                <div class="col-lg-6">
                  <div class="input-group input-group-button mt-2 ">
                    <input aria-label="Min Exp[]" type="text" name="min_exp[]" id="min_exp_<?php echo $i; ?>"  class="form-control  width1" readonly value="<?php echo $tab2['min_exp']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience" placeholder="Min Exp">
                    <span class="input-group-text addon-style">
                        <span class="">Years</span>
                    </span></div>
                </div><div class="col-lg-6">
                  <div class="input-group input-group-button mt-2 ">
                   <input aria-label="Max Exp[]" type="text" name="max_exp[]" id="max_exp_<?php echo $i; ?>" class="form-control form-control-sm width1 " readonly value="<?php echo $tab2['max_exp']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience" placeholder="Max Exp">  <span class="input-group-text addon-style">
                    <span class="">Years</span>
                </span> </div>
            </div></div>

            <input type="hidden" name="item_name[]" class="form-control" value="<?php echo $tab2['item_name']; ?>">
            <input type="hidden" name="purchase_account[]" class="form-control" value="<?php echo $tab2['purchase_account']; ?>">
            <input type="hidden" name="purchase_account_aval[]" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']]['a']; ?>">
        <?php }else { ?>
            <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
            <input type="hidden" name="productCode[]" class="form-control" value="<?php echo $i; ?>">
        <?php } ?>

    </td>
    <td >
        <select aria-label="Uom[]" class="form-control form-select js-example-basic-single "   name="uom[]"  id="uom_<?php echo $i; ?>">
            <option value="0"> Select Any UOM  </option>
            <?php foreach ($uomlist as $uom){  ?>
             <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?> selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
         <?php } ?>
     </select>
     <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
     <div class="checkbox-fade fade-in-primary  mt-3 float-left ">
         <label class="form-label">
           <input aria-label="Checkbox" type="checkbox" <?php if($tab2['allow_overtime']=='0'){ echo "checked";
} ?> name="allow_overtime[<?php echo $i; ?>]">
           <span class="cr">
             <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
         </span>
         <span>&nbsp;Overtime </span>
     </label>
        </div>
    </td>
    <td >
     <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"   class="form-control amount minw-100" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
    <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>">
    <div style="display: none;color: red;" id="<?php echo 'quantity_err_'.$i; ?>"> Enter QTY</div>

    <input aria-label="Billable[]" type="text" name="billable[]" id="billable__2"+count+'"   value="<?php echo $tab2['billable']!="" ? $tab2['billable'] : 100 ?>" class="form-control price amount mt-2 minw-100" autocomplete="off" data-bs-toggle="tooltip" data-placement="bottom" title="Bilable Percentage">

</td>
<td >
    <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount minw-140" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" onblur="changeprice(this.value,<?php echo $i; ?>)"   autocomplete="off">

</td>
<td >

    <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">

    <input type="hidden" name="per_hrs_rate[]" value="" readonly class="form-control price amount" id="per_hrs_rate<?php echo $i; ?>">

    <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
    <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
    <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
    <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly  id="dis_amount_<?php echo $i; ?>" class="form-control amount minw-140" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
    <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">

    <input aria-label="Ref Rate[]" type="text" name="ref_rate[]" id="ref_rate_<?php echo $i; ?>"  class="form-control form-control-sm minw-140 price amount mt-2" readonly  <?php echo $sty; ?> value="<?php  echo $vendor_rate; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Vendor Rate Card">
    <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
</td>
<td >
       <input aria-label="Input field" type="text" class="form-control datepicker minw-140 effective_date<?php echo $i; ?>"  value="<?php echo fromDate($tab2['effective_date']); ?>"  name="effective_date[]"  id="kt_datepicker_3_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Effective Date" >

       <input aria-label="Input field" type="text" class="form-control  mt-2 datepicker minw-140 expire_date<?php echo $i; ?>"  value="<?php echo fromDate($tab2['expire_date']); ?>"  name="expire_date[]"  id="kt_datepicker_4_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Expire Date"  />
</td>

<?php if(isset($_GET['Rid'])&& (!isset($_GET['table']) && $_GET['table'] =='') ){ ?>
    <td>
     <div class="btn btn-icon btn-primary btn-circle mr-2" onclick="assignemployeelistnew(<?php echo $rid; ?>,'<?php echo $tab2['id']; ?>','<?php echo $role['ref_no']; ?>','<?php echo $tab2['item_id']; ?>','<?php echo $tab2['item_price']; ?>','<?php echo $tab2['effective_date']; ?>','<?php echo $tab2['expire_date']; ?>','<?php echo $table1['old_ref_no']; ?>','<?php echo $tab2['min_exp']; ?>','<?php echo $tab2['max_exp']; ?>')" data-container="body" data-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Allocate Resources" aria-label="Allocate Resources" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-user"></i></div>
</td>
<?php }  ?>

    </tr>

<?php $i++;
}
} ?>
</table>
</div>

