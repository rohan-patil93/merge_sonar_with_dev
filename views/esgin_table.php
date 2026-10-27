<?php
    $wrdata["field"] = "id,display_name";
    $data = urlencode(json_encode($wrdata));
    $joburl = API."?mode=gettabledetailswithconditions&table=vendors&data=".$data;
    $job_data = callAPI($joburl);
    $itemres = json_decode($job_data, 1);
?>
<script type="text/javascript">
    function assignemployeeitemlist(rid,eba){
        var table_name = document.getElementById('table_name').value;
        jQuery.ajax({
            url: "views/custom_insert_assign_esign_employee.php",
            data:'table='+table_name+'&rid='+rid+"&eba="+eba,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function toopenpredim(val){
      var table_name = document.getElementById('table_name').value;
      jQuery.ajax({
            url: "views/custom_insert_predem_popup.php",
            data:'table='+table_name+'&rid='+val,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal({ show: true});
            },
            error:function (){}
        });

    }
    function itemChangejob(item_id, id){
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
    $(document).ready(function(){
    var count = $(".itemRow").length;
    $(document).on('click', '#addRows', function() {
        count++;
        var htmlRows = '';
        htmlRows += '<tr>';
        htmlRows += '<td><div class="checkbox-fade fade-in-primary "><label class="form-label"> <input aria-label="Checkbox" type="checkbox" class="itemRow"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span></label></div></td>';
          htmlRows += '<td id="other_'+count+'"><div><select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselectss'+count+' col-lg-4 selectitemorservice"  name= "productCode[]" id="productCode_'+count+'"  onchange="itemChangejob(this.value,'+count+')" data-tablename="vendors"  data-fieldsname="display_name" > <option value="0"> Select Any Vendor </option><?php foreach($itemres as $item){ ?> <option value="<?php echo $item ['id']; ?>"> <?php echo $item ['display_name']; ?> </option> <?php
} ?></select>  </div> <input type="hidden" name="item_name[]" id="item_name_'+count+'" class="form-control"> <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value=""><input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value=""></td>';
        htmlRows += '<td><textarea aria-label="Product Name[]" name="productName[]" id="productName_'+count+'" class="form-control minw-160"></textarea><div class=" float-left mr-3" style="width:40%; float:left;">';

        htmlRows += '<td><input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_'+count+'" onblur="quantityCheckold(this.id)"  class="form-control quantity amount minw-100"  autocomplete="off"><div style="display: none;color: red;" id="quantity_err_'+count+'"> Enter QTY</div></td>';
        htmlRows += '<td><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount minw-140" autocomplete="off"><div style="display: none;color: red;" id="price_err_'+count+'"> Enter Rate</div></td>';

        htmlRows += '<td><div> <div width: 100%;"><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" readonly name="p_amount[]" id="p_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="ptax_amount[]" id="ptax_amount_'+count+'" class="form-control price amount"><input type="hidden" readonly name="amount[]" id="amount_'+count+'" class="form-control price amount">  <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount minw-140" value="" autocomplete="off"></div>';

        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $(".js-data-example-ajax-multiselectss"+count).select2({

        ajax: {
           // url: "https://api.github.com/search/repositories",
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
       templateResult: formatRepo,
        templateSelection: formatRepoSelection
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


    });
 });
</script>
<table class="table table-hover table-responsive" style="border-radius: 10px;" id="invoiceItem">
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
    <th scope="col" class="" style="width: 16%;">Vendor(s) <span class="requried"></span></th>
    <th scope="col" class="thheader" style="width: 20%;">Description</th>
    <th scope="col" class="thheader" style="width: 6%;"># Resources </th>
    <th scope="col" class="thheader" style="width: 8%;">Base Rate </th>
    <th scope="col" class="thheader" style="width: 10%;">Amount</th>
    <?php if(isset($_GET['Rid'])){ ?>
    <th scope="col" class="thheader" style="width: 2%;"></th>
    <?php } ?>
</tr>
<?php if(count((array)$table2)>0){ $i=1;
 foreach ((array)$table2 as $tab2) { //print_r($tab2); ?>
<tr>
    <td>
        <div class="checkbox-fade fade-in-primary ">
          <label class="form-label">
            <input aria-label="Checkbox" type="checkbox" class="itemRow">
            <span class="cr">
              <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
            </span>
          </label>
        </div>
    </td>

    <td id='other_<?php echo $i; ?>'>
        <div>
            <?php if($tab2['item_id'] >= 0){  $min = array();?>

             <select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4"  data-tablename="vendors"  data-fieldsname="display_name" name= "productCode[]"  id="productCode_<?php echo $i; ?>" onchange="itemChangejob(this.value,<?php echo $i; ?>)">
         <option value="0"> Select Any Vendor  </option>
        <?php $like_item_check = 0; foreach ((array)$itemres as $item){    ?>
         <option <?php if($tab2['item_id']== $item['id']){  echo "selected";
} ?> value ='<?php echo $item['id']; ?>'> <?php echo $item['display_name']; ?>
            <?php } ?>
    </select>
        </div>

        <input type="hidden" name="item_name[]" class="form-control" value="<?php echo $tab2['item_name']; ?>">
        <input type="hidden" name="purchase_account[]" class="form-control" value="<?php echo $tab2['purchase_account']; ?>">
        <input type="hidden" name="purchase_account_aval[]" class="form-control" value="<?php echo (array)$response_code[$tab2['purchase_account']]['a']; ?>">
    <?php }else { ?>
        <input aria-label="Item Name[]" type="text" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off">
        <input type="hidden" name="productCode[]" class="form-control" value="<?php echo $tab2['productCode']; ?>">
    <?php } ?>
    </td>
    <td>
        <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control minw-160" autocomplete="off"><?php echo $tab2['description']; ?></textarea>


    </td>

    <td>
        <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="quantityCheckold(this.id)"  class="form-control amount minw-100" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
        <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount " value="<?php echo $tab2['quantity']; ?>">
        <div style="display: none;color: red;" id="<?php echo 'quantity_err_'.$i; ?>"> Enter QTY</div>
    </td>
    <td>
        <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount minw-140" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off" >

        <div style="display: none;color: red;" id="<?php echo 'price_err_'.$i; ?>"> Enter Rate</div>
    </td>

    <td>

        <div> <div style="width: 100%;">
        <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
         <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
        <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount minw-140" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">
    </div>

  </div>
</td>
<?php if(isset($_GET['Rid'])){ ?>
    <td>
     <div class="btn btn-icon btn-primary btn-circle mr-2" onclick="assignemployeeitemlist(<?php echo $rid; ?>,'<?php echo $tab2['id']; ?>')" data-container="body" data-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Allocate Resources" aria-label="Allocate Resources" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-user"></i></div>

</td>
<?php }  ?>
</tr>

<?php $i++; ?>
<?php } ?>
<?php } ?>
    </table>
