<?php
if($_GET['table']!="" && $_GET['Rid']!=""){
    $vid = $_GET['Rid'];
}else{
    $vid= $table1['ref_no'];
}
    
    $data12["where"] = array('is_active'=> '0','transaction_id'=>$vid);
    $data = urlencode(json_encode($data12));
    $itemurl = API."?mode=gettabledetailswithconditions&table=vrc_items&data=".$data; 
    $item_data = callAPI($itemurl);
    $itemres1 = json_decode($item_data, 1);

    /*$data12["where"] = array('is_active'=> '0','location_id'=>$_SESSION['user']['location_id']);
    $data = urlencode(json_encode($data12));
    $itemurl = API."?mode=gettabledetailswithconditions&table=manpower_requisitions&data=".$data; 
    $item_data = callAPI($itemurl);
    $itemres1 = json_decode($item_data, 1);*/
    $joburl = API."?mode=gettabledetailswithconditions&table=job_titles"; 
    $job_data = callAPI($joburl);
    $job_data = json_decode($job_data, 1);
    foreach($job_data as $job_da){
        $jobdata[$job_da['id']] = $job_da;
    }
    foreach($itemres1 as $item){
        $item['item_name'] = $jobdata[$item['item_id']]['job_title'];
	$item['item_id'] = $jobdata[$item['item_id']]['id'];
        $itemres[]=$item;
    }
    //print_r($itemres);
    $data12["where"] = array('is_active'=> '0');
    $data = urlencode(json_encode($data12));
    $locurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$data; 
    //echo $itemurl;
    $locdata = callAPI($locurl);
    $locdata = json_decode($locdata, 1);
    foreach($locdata as $locda){
        $locs[$locda['id']] = $locda['location_name'];
    }
  //echo "rai"; print_r($locs);
   /* $uomlist = array('1'=>'Numbers');*/
?>
<script type="text/javascript">
    function changeprice(val,id){
        var mval =$("#price_"+id).val();
        var dval =$("#ref_rate_"+id).val();
        if(dval < mval){
            $('#price_'+id).css('color', '#f00');
        }
    }
    function itemChangejob(item_id, id){
        var data1 = 'page=vrcdata&item_id='+item_id+'&vid='+$('#vid').val()+'&table_name=vrc_items';
        //alert(data1);
        $.ajax({
        url: "ajax.php",
        data: data1,
        type: "POST",
        success:function(data){
	    console.log(data);
           var obj = $.parseJSON(data);
            /*const words = data.split(',');
            console.log(words);
            console.log(words[1]);*/
            document.getElementById('min_exp_'+id).value=obj.min_exp;
            document.getElementById('max_exp_'+id).value=obj.max_exp;
            document.getElementById('item_name_'+id).value=obj.item_name;  
        },
        error:function (){}
        });
    }
    function itemChangejob123(item_id, id){
        var data1 = 'page=dynamictablerowdata&item_id='+item_id+'&company_id='+$('#company_id').val()+'&table_name=manpower_requisitions';
        //alert(data1);
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
        //alert(item_id+'##'+id);
        if(item_id == '-1'){
            //alert('check');
            $("#other_"+id).html('<input type="text" class="form-control" name= "item_name[]" id="item_name_'+id+'"><input type="hidden" class="form-control" value="-'+id+'" name= "productCode[]" id="productCode_'+id+'">');

        }
       // alert(item_id+'##'+id);
        $.ajax({
        url: "ajax.php",
        data:'page=ItemDetails&item_id='+item_id+'&company_id='+$('#company_id').val()+'&table_name='+$('#table_name').val()+'&ddate='+$('#transaction_date').val()+'&cost_center_id='+$('#cost_center').val()+'&fiscal_year='+$('#transaction_date').val(),
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            var price =  parseFloat(obj.purchase_price).toFixed(2);
            //price = price.toPrecision(2);
            document.getElementById('productName_'+id).value=obj.item_description;
            document.getElementById('uom_'+id).value=obj.purchase_uom;
            document.getElementById('price_'+id).value=price;
            document.getElementById('item_name_'+id).value=obj.item_name;
            document.getElementById('purchase_account_'+id).value=obj.purchase_account;
            //document.getElementById('budget_'+id).data-content=obj.a;
            var dd = 'A : '+obj.a+', B : '+obj.b+' C : '+obj.c;
            var text = '<div class="dropdown-item"><i class="flaticon-coins"></i> &nbsp;&nbsp; A :'+obj.a+'</div> <div class="dropdown-item" ><i class="flaticon-coins"></i> &nbsp; &nbsp; B :'+obj.b+'</div> <div class="dropdown-item" href="#"><i class="flaticon-coins"></i> &nbsp;&nbsp; C :'+obj.c+'</div>';
            //document.getElementById('budget_'+id).setAttribute("data-content",dd);
            
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
        htmlRows += '<td><div class="checkbox-fade fade-in-primary "><label class="form-label"> <input type="checkbox" class="itemRow"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div></td>';
        htmlRows += '<td id="other_'+count+'"><select class="form-control form-select js-example-basic-single"  onchange="itemChangejob(this.value,'+count+')"  name= "productCode[]" id="productCode_'+count+'"> <option value="0"> Select Any Prodcut </option><?php foreach ($itemres as  $item) {  ?>      <option  value = <?php echo $item[item_id]; ?>> <?php echo trim($item[item_name])."(".$item[min_exp]." to ".$item[max_exp]." years)"; ?>  </option> <?php } ?> </select> <input type="text" name="ref_rate[]" id="ref_rate_'+count+'"  class="form-control form-control-sm price amount mt-2" readonly   value="" data-bs-toggle="tooltip" data-placement="bottom" title="Vendor Rate Card">  </td>';
        
        htmlRows += '<td><select class="form-control form-select js-example-basic-single"  name= "uom[]" id="uom_'+count+'"><option value="0"> Select Any UOM  </option><?php foreach ($uomlist as $uom) {  ?> <option   <?php if($tab2[uom] == $uom[genc_detail_code]){ ?>selected <?php } ?>  value ="<?php echo $uom[genc_detail_code]; ?>" > <?php echo $uom[genc_detail_name]; ?> </option> <?php } ?></select><div style="display: none;color: red;" id="uom_err_'+count+'"> Enter UOM</div><div class="input-group input-group-button mt-2"><input type="text" name="min_exp[]" id="min_exp_'+count+'"  class="form-control form-control-sm width1" value="" placeholder="Min Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience" >  <span class="input-group-text addon-style"> <label class="">Years</label></span></div></td> '; 
          htmlRows += '<td><input type="text" name="billable[]" id="billable_'+count+'"  value="100" class="form-control price amount" autocomplete="off"> <div class="input-group input-group-button mt-2">  <input type="text" name="max_exp[]" id="max_exp_'+count+'" class="form-control form-control-sm width1" value="" placeholder="Max Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience">  <span class="input-group-text addon-style"><label class="">Years</label></span></div></td>'; 

        htmlRows += '<td><input type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div> <input type="date" class="form-control mt-2"  value=""  name="effective_date[]"  id="kt_datepicker_3'+count+'" data-bs-toggle="tooltip" data-placement="bottom" title="Effective Date" > </td>';           
        htmlRows += '<td><input type="text" name="price[]" id="price_'+count+'" class="form-control price amount" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div><input type="date" class="form-control mt-2"  value=""  name="expire_date[]"  id="kt_datepicker_4_'+count+'" data-bs-toggle="tooltip" data-placement="bottom" title="Expire Date"  /> </td>';       
      

        htmlRows += '<td><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount" autocomplete="off"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount" autocomplete="off"><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount" autocomplete="off">  <input type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" value="" autocomplete="off"> <div class="checkbox-fade fade-in-primary  mt-3 float-left"> <label class="form-label"><input type="checkbox"  name="allow_overtime['+count+']"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span> <span>&nbsp;Allow Overtime </span></label></div><input type="hidden" name="item_name[]" id="item_name_'+count+'" class="form-control"  autocomplete="off"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value="" autocomplete="off"><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value="" autocomplete="off"></td>';
                 
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
    }); 
 });
</script>
<table class="table table-hover table-responsive"  id="invoiceItem"> 
<input type="hidden" name="" id="vid" value="<?php echo $vid ?>"> 
<tr>
    <th class="thheader" width="1%"> 
           <div class="checkbox-fade fade-in-primary">
            <label class="kt-checkbox">
                <input id="checkAll"  type="checkbox">
                <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
            </label>
        </div>
    </th>
    <th class="" width="20%">job Title</th>
    <!-- <th class="" width="20%">Description</th> -->
    <th class="thheader" width="10%">Units</th>
      <th class="thheader" width="8%">Billable %</th>
    <th class="thheader" width="10%">Vacancy</th>
    <th class="thheader" width="8%">Base Rate</th>
  
    <th class="thheader" width="12%">Amount</th>   
</tr>    
<?php if(count((array)$table2)>0){ $i=1; 
 foreach ($table2 as $tab2) { //print_r($tab2); 
 $vendor_rate = $tab2['ref_rate']!="" ? $tab2['ref_rate'] :$tab2['item_price'];
 $sty = "";
 if($tab2['item_price']>$vendor_rate){ $sty = "style='color:#F00'"; }
 ?>
<tr>
    <td width="1%">
        <div class="checkbox-fade fade-in-primary ">
          <label class="form-label">
            <input type="checkbox" class="itemRow">
            <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
          </label>
        </div>
    </td>
    <td id='other_<?php echo $i; ?>' style="width:300px"> 
       
            <?php if($tab2['item_id'] >= 0){  $min = array();?>
            <select class="form-control form-select js-example-basic-single"  name= "productCode[]"  id="productCode_<?php echo $i; ?>" onchange="itemChangejob(this.value,<?php echo $i; ?>)" >
                <option value="0"> Select Any Item  </option>
                <?php $like_item_check = 0; foreach ($itemres as $item) { ?>
                 <option <?php if($tab2['item_id']== $item['item_id']){ ?>selected <?php } ?> value = <?php echo $item['item_id']; ?>   > <?php echo $item['item_name']."(".$item['min_exp']." to ".$item['max_exp']." years)"; ?> 
               
                <?php if($tab2['manpower_requisition_id']== $item['id']){ $min = $item; }  } ?>
            </select>
             <input type="text" name="ref_rate[]" id="ref_rate_<?php echo $i; ?>"  class="form-control form-control-sm price amount mt-2" readonly  <?php echo $sty; ?> value="<?php echo number_format($vendor_rate,2, '.', ''); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Vendor Rate Card"> 
        <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
            
        <input type="hidden" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off"> 
        <input type="hidden" name="purchase_account[]" id="purchase_account_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['purchase_account']; ?>" autocomplete="off">
        <input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_<?php echo $i; ?>" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']][a]; ?>" autocomplete="off">
    <?php }else { ?>
        <input type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
        <input type="hidden" name="productCode[]" id="productCode_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['productCode']; ?>" autocomplete="off">
    <?php } ?>

    </td>
     <td width="10%">
        <select class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>">
            <option value="0"> Select Any UOM  </option>
            <?php foreach ($uomlist as $uom) {  ?>
                 <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?> selected <?php } ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
            <?php } ?>
        </select>
         
        <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
        <div class="input-group input-group-button mt-2 "> 
        <input type="text" name="min_exp[]" id="min_exp_<?php echo $i; ?>"  class="form-control  width1" readonly value="<?php echo $tab2['min_exp']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience" placeholder="Min Exp">
        <span class="input-group-text addon-style">
    <label class="">Years</label>
  </span></div>
    </td>  
        <td width="8%">
        <input type="text" name="billable[]" id="billable_'+count+'"  value="<?php echo $tab2['billable']!="" ? $tab2['billable'] : 100 ?>" class="form-control price amount" autocomplete="off">
<div class="input-group input-group-button mt-2 "> 
   <input type="text" name="max_exp[]" id="max_exp_<?php echo $i; ?>" class="form-control form-control-sm width1" readonly value="<?php echo $tab2['max_exp']; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience" placeholder="Max Exp">  <span class="input-group-text addon-style">
    <label class="">Years</label>
  </span> </div>
    </td>



    <td width="10%">
        <input type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"   class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off"> 
        <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
        <div style="display: none;color: red;" id="<?php echo 'quantity_err_'.$i; ?>"> Enter QTY</div>
       
       
  
             <input type="date" class="form-control mt-2"  value="<?php echo fromDate($tab2['effective_date']); ?>"  name="effective_date[]"  id="kt_datepicker_3<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Effective Date" >

      
    </td>
    <td width="8%">
        <input type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" onblur="changeprice(this.value,<?php echo $i; ?>)"   autocomplete="off">
       <input type="date" class="form-control mt-2"  value="<?php echo fromDate($tab2['expire_date']); ?>"  name="expire_date[]"  id="kt_datepicker_4_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Expire Date"  />
          
    </td>

    <td width="12%">
         
        
        <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
         <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
        <input type="text" name="dis_amount[]" readonly  id="dis_amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>" autocomplete="off">
         <div class="checkbox-fade fade-in-primary  mt-3 float-left ">
         <label class="form-label">
           <input type="checkbox" <?php if($tab2['allow_overtime']=='0'){ echo "checked"; } ?> name="allow_overtime[<?php echo $i; ?>]">
           <span class="cr">
             <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
           </span>
           <span>&nbsp;Allow Overtime </span>
         </label>
       </div>
        </td>
    
</tr>

<?php $i++; } } ?>                      
    </table>