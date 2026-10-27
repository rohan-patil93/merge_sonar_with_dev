<?php
    $itemurl = API."?mode=gettabledetails&table=".TBL_CHART_OF_ACCOUNTS;
    $item_data = callAPI($itemurl);
    $itemres = json_decode($item_data,1);
    $locations_url = API."?mode=gettabledetails&table=".TBL_LOCATIONS;
    $locations_data = callAPI($locations_url);
    $locations_data = json_decode($locations_data, 1); 
    $cusurl = API."?mode=gettabledetails&fid=company_id&rid=".$_SESSION['company_id']."&table=".TBL_USERS; 
    $cusresponse_dump = callAPI($cusurl); 
    $cusresponse_dump = json_decode($cusresponse_dump,1);
    $l=0;
    foreach ($cusresponse as  $custt) {
       $name_li[$l]['id'] = $custt['id'];
        $name_li[$l]['display_name'] = $custt['display_name']."<span >&nbsp;&nbsp;&nbsp;&nbsp;[Vendor]</span>";
        $name_li[$l]['type'] = 'V';

    $l++;}
    foreach ($cusresponse_dump as  $custt) {
        $name_li[$l]['id'] = $custt['id'];
        $name_li[$l]['display_name'] = $custt['display_name']."<span>&nbsp;&nbsp;&nbsp;&nbsp;[User]</span>";
        $name_li[$l]['type'] = 'C';
    $l++;}
?>
<script type="text/javascript">
    function checkdebitandcredit(id, type){
        var price = $('#'+type+'_'+id).val(); 
        if(price != 0){
            if(type == 'credit'){
                $('#debit_'+id).val(0);
            }else{
                $('#credit_'+id).val(0);
            }
        }
        calucateaccountsum();
    }
    function calucateaccountsum(){
        var totalAmount = 0; 
        var d_subamount = 0;
        var c_subamount = 0;
        var d_amount = 0;
        var c_amount = 0;
        var total_tax_c = 0;
        var total_tax_d = 0;
        var formatter = new Intl.NumberFormat('en-IN', {
             minimumFractionDigits: 2,
        });
        $("[id^='credit_']").each(function() {
            var id = $(this).attr('id');
            id = id.replace("credit_",'');
           //alert(id);
            var credit = $('#credit_'+id).val();
            var debit = $('#debit_'+id).val();
            var quantity  = 1;
            var tax = $('#taxes_'+id).val();
            console.log('credit'+credit+'debit'+debit);
            d_amount = debit*quantity;
            c_amount = credit*quantity;
            var ptaxRate = 0;
            if(tax != '0'){
                var tax_cal = parseFloat(tax)/parseFloat(100);
                if(d_amount !=0){
                    var tax_cal1 = d_amount*tax_cal;
                    total_tax_d +=tax_cal1;
                }
                if(c_amount !=0){
                    var tax_cal1 = c_amount*tax_cal;
                    total_tax_c +=tax_cal1;
                }
               console.log("taxrate"+ptaxRate);
            }
            $('#taxes_amount_'+id).val(tax_cal1);
            c_subamount += c_amount ;
            d_subamount += d_amount ;
        });
        console.log('credit'+c_subamount+'debit'+d_subamount+'taxb'+total_tax_d+'taxc'+total_tax_c);
        $('#dis_subtotal_credit').val(parseFloat(c_subamount).toFixed(2));
        $('#dis_subtotal_debit').val(parseFloat(d_subamount).toFixed(2));
        $('#subtotal_credit').val(parseFloat(c_subamount));
        $('#subtotal_debit').val(parseFloat(d_subamount));
        var grandtotal_credit = c_subamount+total_tax_c;
        var grandtotal_debit  = d_subamount+total_tax_d;
        $('#dis_grandtotal_credit').val(parseFloat(grandtotal_credit).toFixed(2));
        $('#dis_grandtotal_debit').val(parseFloat(grandtotal_debit).toFixed(2));
        $('#grandtotal_credit').val(parseFloat(grandtotal_credit));
        $('#grandtotal_debit').val(parseFloat(grandtotal_debit));
       displaytaxe();
    }
    function displaytaxe(){
        var count = $(".itemRow").length;
        console.log(count);
        var str ='';
        var check_arr = [];
        var arr2 = [];
    for(var i=1; i< count+1; i++) { 
        var id = i;
        var taxtype = $('#taxes_type_'+id).val();
        var tax = $('#taxes_'+id).val();
        var tax_amount = $('#taxes_amount_'+id).val();
        var amount_val = $('#credit_'+id).val();
        var amount_type = 'd';
        if(amount_val !=0){
            amount_type = 'c';
        }
        if(tax_amount !=0){
            console.log("RAVi");
            if(check_arr.indexOf(tax)!=-1 ){
                console.log("RAVi123");
                for(var j=0; j< arr2.length ; j++){
                   if(arr2[j]['id'] == tax){
                        if(amount_type == 'c'){
                            arr2[j]['c_tax_amount'] =  parseFloat(arr2[j]['c_tax_amount']) + parseFloat(tax_amount);
                        }
                        if(amount_type == 'd'){
                            arr2[j]['d_tax_amount'] =  parseFloat(arr2[j]['d_tax_amount']) + parseFloat(tax_amount);
                        }
                        break;
                   }     
                }
            }else{
                var  arr = {};
                check_arr.push(tax);
                var c_tax=0;
                var d_tax=0;
                arr['id'] = tax;
                if(amount_type == 'c'){
                    c_tax = tax_amount;
                }else{
                    d_tax = tax_amount;
                }
                arr['c_tax_amount'] = c_tax;
                arr['d_tax_amount'] = d_tax;
                arr2.push(arr);
            }
        }
       
    }
    console.log(JSON.stringify(arr2));
    for( var k =0 ; k < arr2.length; k++){

                str+= "<tr><td style='text-align:right;'> IGST @ "+arr2[k]['id']+"% </td>";
                str+= "<td class='mob_amount' width='18%'>  <input  type='text' class='form-control amount'   readonly value='"+parseFloat(arr2[k]['d_tax_amount']).toFixed(2)+"''> </td><td class='mob_amount' width='18%'>  <input  type='text' class='form-control amount' readonly  value='"+parseFloat(arr2[k]['c_tax_amount']).toFixed(2)+"''> </td></tr>";
         
    }
    $("#display_tax").html(str);
 }
    function itemChange(item_id, id){
        //alert(item_id+'##'+id);
       // alert(item_id+'##'+id);
        $.ajax({
        url: "ajax.php",
        data:'page=chartofaccount&item_id='+item_id,
        type: "POST",
        success:function(data){
            console.log(data);
            var obj = $.parseJSON(data);
            document.getElementById('account_name_'+id).value=obj.account_name;
            document.getElementById('account_code_'+id).value=obj.account_code;
            document.getElementById('account_type_'+id).value=obj.account_type;
        },
        error:function (){}
        });
    }
    $(document).ready(function(){
        console.log('workng');
    var count = $(".itemRow").length;
    $(document).on('click', '#addRows', function() { 
        count++;
        var cerdit = "'credit'";
        var debit = "'debit'";
        var credit_send = 0.00; var debit_send = 0.00;
        var htmlRows = '';
        htmlRows += '<tr>';  
        htmlRows += '<td><div class="checkbox-fade fade-in-primary "><label class="form-label"> <input type="checkbox" class="itemRow" data-rowid="'+count+'"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div> </td>';      
        
 htmlRows += '<td id="other_'+count+'"><select class="form-select js-data-example-ajax-multiselectss'+count+' col-lg-4 selectitemorservice"   onchange="itemChange(this.value,'+count+')" name= "accountcode[]"  data-tablename="chart_of_accounts"  data-fieldsname="account_name" > <option value="0"> Select Any Account </option></select> <input type="hidden" name="account_name[]" id="account_name_'+count+'" class="form-control"><input type="hidden" name="account_code[]" id="account_code_'+count+'" class="form-control">        <input type="hidden" name="account_type[]" id="account_type_'+count+'" class="form-control"></td>';      
        

        htmlRows += '<td> <textarea  name="description[]"  class="form-control" autocomplete="off"></textarea></textarea></td> ';  
        htmlRows += '<td><select class="form-control form-select js-example-basic-single"  name= "name[]"  >option value="0"> Select Any Name  </option> <?php foreach ($name_li as $nam) {  ?><option  value ="<?php echo $nam['id']."#".$nam['type']; ?>" > <?php echo $nam['display_name']; ?> </option> <?php } ?>  </select></td> '; 
       <?php /** ?> 
        htmlRows += '<td><select class="form-control form-select js-example-basic-single"  name= "taxes[]" onchange="calucateaccountsum()" id="taxes_'+count+'"><option value="0"> Select Any Tax Rate </option>   <?php foreach ($taxres as $key => $tax) {  ?> <option  value = "<?php echo $key ?>"   > <?php echo $tax; ?> </option> <?php } ?> </select><input type="hidden" name="taxes_amount[]" value="0" id="taxes_amount_'+count+'">   <input type="hidden" name="taxes_type[]" value="i" id="taxes_type_'+count+'"></td>';  
       <?php **/ ?>  
        htmlRows += '<td><select class="form-control form-select js-example-basic-single"  name= "location[]"> <option value="0"> Select Any location  </option> <?php foreach ($locations_data as $locationn) {  ?><option value ="<?php echo $locationn['id']; ?>" ><?php echo $locationn['location_name']; ?></option><?php } ?></select></td>';
        htmlRows += '<td><input type="text" name="debit[]" onblur="checkdebitandcredit('+count+',  '+debit+')" id="debit_'+count+'" class="form-control price amount" value="'+debit_send+'" autocomplete="off"></td>';  
       htmlRows += '<td><input type="text" name="credit[]" onblur="checkdebitandcredit('+count+', '+cerdit+')" id="credit_'+count+'" class="form-control price amount" value="'+credit_send+'" autocomplete="off"></td>';    
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
         $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
         $(".js-data-example-ajax-multiselectss"+count).select2({
            ajax: {
           url: "views/custom_getcityautotype.php",
           dataType: 'json',
           delay: 250,
           data: function(params) {
            console.log($(".selectitemorservice").length);
            var stringids = "";
            $(".selectitemorservice").each(function(){
                stringids +=$(this).val()+",";
            });
            return {
                    q: params.term, // search term
                      page: params.page,
                    tablename: $(this)[0].dataset.tablename,
                    fieldsname: $(this)[0].dataset.fieldsname,
                    stringids:stringids,
                };
            },
            processResults: function(data, params){
                params.page = params.page || 1;
                return {
                    results: data.items,
                    pagination: {
                        more: (params.page * 30) < data.total_count
                    }
                };
            },
            cache: true
        },
        escapeMarkup: function(markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1,
       templateResult: formatRepo, // omitted for brevity, see the source of this page
        templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
    });
       function formatRepo(repo) {
        if (repo.loading) return repo.text;
        var markup = "<div class='select2-result-repository clearfix'>" +
        "<div class='select2-result-repository__meta'>" +
        "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";
        if (repo.description) {
            markup += "<div class='select2-result-repository__description'>" + repo.description + "</div>";
        }
        markup += "<div class='select2-result-repository__statistics'>" +
        "</div>" +
        "</div></div>";
        return markup;
    }
    function formatRepoSelection(repo) {
        return repo.full_name || repo.text;
    }
        //calucateaccountsum();
    }); 
 });
</script>
<div class="table-responsive mb-3">
<table class="table table-hover"  id="invoiceItem"> 
        <tr>
            <th class="thheader" width="2%"> 
                   <div class="checkbox-fade fade-in-primary">
                    <label class="kt-checkbox">
                        <input id="checkAll"  type="checkbox">
                        <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                    </label>
                </div>
            </th>
            <th class="" width="15%">Account <span class="requried"></span></th>
            <th class="" width="15%">Description</th>
            <th class="thheader text-start" width="15%">Name <!-- <span class="requried">*</span> --></th>
           <!-- <th class="thheader" width="10%">Tax  <span class="requried">*</span> </th> -->
            <th class="thheader text-start" width="15%">Location <!-- <span class="requried">*</span> --></th>
            <th class="thheader text-start" width="15%">Debits<!--  <span class="requried">*</span> --></th>
            <th class="thheader text-start" width="15%">Credits<!--  <span class="requried">*</span> --></th>
        </tr>    
        <?php if(count((array)$table2)>0){ $i=1; 
        foreach ($table2 as $tab2) { //print_r($tab2); ?>
        <tr>
            <td>
                <div class="checkbox-fade fade-in-primary ">
                  <label class="form-label">
                    <input type="checkbox" class="itemRow" data-rowid="<?php echo $i; ?>">
                    <span class="cr">
                      <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                    </span>
                  </label>
                </div>
            </td>

            <td id='other_<?php echo $i; ?>'> 
                <?php if($tab2['item_id'] >= 0){ ?>
                <select class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4"  data-tablename="chart_of_accounts"  data-fieldsname="account_name"name= "productCode[]" onchange="itemChange(this.value,<?php echo $i; ?>)" id="productCode_<?php echo $i; ?>">
                 <option value="0"> Select Any Account  </option>
                <?php $like_item_check = 0; foreach ($itemres as $item) {  if($tab2['item_id']== $item['id']) { $like_item_check = 1; }   if($tab2['item_id']== $item['id']){  ?>
                 <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['account_name']; ?> 
                    <?php } } ?>
                    <option value="-1">Other</option>
                </select>
                <input type="hidden" name="account_name[]" id="account_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>"> 
                <input type="hidden" name="account_code[]" id="account_code_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['purchase_account']; ?>" >
                <input type="hidden" name="account_type[]" id="account_type_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['purchase_account']; ?>" >
               
                <?php }else { ?>
                <input type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
                <input type="hidden" name="productCode[]" id="productCode_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['productCode']; ?>" autocomplete="off">
                <?php } ?>
            </td>
            <td>
                <textarea name="description[]" class="form-control mob_width_txtarea" autocomplete="off"><?php echo $tab2['description']; ?></textarea>
            </td>  
            <td class="mob_name">
                <select class="form-control form-select js-example-basic-single"  name= "name[]"  >
                    <option value="0">Select Any Name</option>
                    <?php foreach ($name_li as $nam) {  ?>
                         <option   <?php if($tab2['name_id']."#".$tab2['name_type'] ==  $nam['id']."#".$nam['type']){ ?>selected <?php } ?>  value ="<?php echo $nam['id']."#".$nam['type'];  ?>" > <?php echo $nam['display_name']; ?> </option>
                    <?php } ?>
                </select>
                <div style="display: none;color: red;" id="<?php echo 'uom_err_'.$i;  ?>"> Enter UOM</div>
            </td>  
            <?php /** ?>
             <td>
                <select class="form-control form-select js-example-basic-single"  name= "taxes[]" onchange="calucateaccountsum()" id="taxes_<?php echo $i; ?>" >
                    <option value="0"> Select Any Tax Rate </option>
                    <?php foreach ($taxres as $key => $tax) {  ?>
                    <option  <?php if($tab2['tax_id'] == $key){ ?> selected  <?php } ?> value = <?php echo $key ?>   > <?php echo $tax; ?> </option>
                    <?php } ?>
                </select>
                <input type="hidden" name="taxes_amount[]" value="0" id="taxes_amount_<?php echo $i; ?>">
                <input type="hidden" name="taxes_type[]" value="i" id="taxes_type_<?php echo $i; ?>">
                <div style="display: none;color: red;" id="<?php echo 'tax_err_'.$i; ?>"> Enter Tax</div>
            </td> 
            <?php **/ ?>  
             <td class="mob_location">
                 <select class="form-control form-select js-example-basic-single"  name= "location[]"  >
                    <option value="0"> Select Any location  </option>
                    <?php foreach ($locations_data as $locationn) {  ?>
                         <option   <?php if($tab2['location'] == $locationn['id']){ ?>selected <?php } ?>  value ="<?php echo $locationn['id']; ?>" > <?php echo $locationn['location_name']; ?> </option>
                    <?php } ?>
                </select>
                <div style="display: none;color: red;" id="<?php echo 'tax_err_'.$i; ?>"> Enter Tax</div>
            </td>  
            <td class="mob_amount">
                <input type="text" name="debit[]" onblur="checkdebitandcredit(<?php echo $i; ?>, 'debit')" id="debit_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['debit_amount'],2, '.', ''); ?>" autocomplete="off">
            </td>
           
            <td class="mob_amount">
                <input type="text" name="credit[]" onblur="checkdebitandcredit(<?php echo $i; ?>, 'credit')" value="<?php echo customeummarmonyformate($tab2['credit_amount']); ?>"  class="form-control price amount" id="credit_<?php echo $i; ?>">
            </td>
        </tr>
        <?php $i++; } } ?>                      
    </table>
</div>