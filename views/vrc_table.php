<?php
    $joburl = API."?mode=gettabledetailswithconditions&table=job_titles";
    $job_data = callAPI($joburl);
    $itemres = json_decode($job_data, 1);
    $data12["where"] = array('is_active'=> '0');
    $data = urlencode(json_encode($data12));
    $locurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$data;
    $locdata = callAPI($locurl);
    $locdata = json_decode($locdata, 1);
    foreach($locdata as $locda){
        $locs[$locda['id']] = $locda['location_name'];
    }

?>
<script type="text/javascript">
    function toopenpredim(val,rowid){
      var table_name = document.getElementById('table_name').value;
      jQuery.ajax({
            url: "views/custom_insert_predem_popup.php",
            data:'rid='+val+'&rowid='+rowid,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");
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


    //     ajax: {
    //        url: "views/custom_getcityautotype.php",
    //        dataType: 'json',
    //        delay: 250,

    //                 q: params.term, // search term

    //                 page: params.page,
    //                 stringids:stringids,
    //                 results: data.items,
    //                 pagination: {
    //                     more: (params.page * 30) < data.total_count
    //         cache: true
    //     minimumInputLength: 1,

 $(document).ready(function(){
    var count = $(".itemRow").length;
    $(document).on('click', '#addRows', function() {
        count++;
        var htmlRows = '';
        htmlRows += '<div class="col-md-6 itemRow cardid'+count+'"> <div class="card  p-5 "><button type="button" class="btn p-0 border-0 bg-transparent deleteiconcustom text-danger f-22" onclick="deleteitem('+count+')" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Delete VRC Card" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-trash"></i></button>';


 htmlRows += '<div class="row"><div class="col-md-5"><span>job Title</span></div><div class="col-md-7"> <select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselectss'+count+' col-lg-4 selectitemorservice"  name= "productCode[]" id="productCode__3"+count+'"  onchange="itemChangejob(this.value,'+count+')" data-tablename="job_titles"  data-fieldsname="job_title" > <option value="0"> Select Job Title </option></select><input type="hidden" name="item_name[]" id="item_name__3"+count+'" class="form-control" value="">  <input type="hidden" name="purchase_account[]" id="purchase_account__3"+count+'" class="form-control" value=""> <input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval__3"+count+'" class="form-control" value=""></div></div><hr>';
 htmlRows += '<div class="row"><div class="col-md-5"><span>Description</span></div><div class="col-md-7"> <input type="text" name="productName[]" id="productName__3"+count+'" value="" class="form-control " placeholder="Description" data-bs-toggle="tooltip" data-placement="bottom" title="Description"></div></div><hr>';
htmlRows += '<div class="row"><div class="col-md-5"><span>Min Experience</span></div><div class="col-md-7"> <div class="input-group input-group-button "> <input type="text" name="min_exp[]" id="min_exp__2"+count+'"  class="form-control form-control-sm width1" value="" placeholder="Min Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience" > <span class="input-group-text addon-style"><span class="">Years</span></span></div></div></div><hr>';
 htmlRows += '<div class="row"><div class="col-md-5"><span>Max Experience</span></div><div class="col-md-7"><div class="input-group input-group-button "> <input type="hidden" name="quantity_old[]" id="quantity_old__2"+count+'"  class="form-control amount" value=""><input type="text" name="max_exp[]" id="max_exp__2"+count+'" class="form-control form-control-sm  width1" value="" placeholder="Max Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience"> <span class="input-group-text addon-style"><span class="">Years</span></span></div> </div></div><hr>';
  htmlRows += '<div class="row"><div class="col-md-5"><span>Monthly Leaves</span></div><div class="col-md-7"><div class="input-group input-group-button"> <input type="text" name="min_leave[]" id="min_leave__2"+count+'" class="form-control width1" placeholder="Monthly Leaves" data-bs-toggle="tooltip" data-placement="bottom" title="Monthly Leaves"> <span class="input-group-text addon-style"><span class="">Days</span></span></div></div></div><hr>';
 htmlRows += '<div class="row"><div class="col-md-5"><span>Units *</span></div><div class="col-md-7"><select aria-label="Uom[]" class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom__2"+count+'"><option value="0"> Select Any Units </option> <?php foreach ($uomlist as $uom) {  ?>
                 <option    value ="<?php echo $uom[genc_detail_code]; ?>" > <?php echo $uom[genc_detail_name]; ?> </option> <?php
} ?>  </select> </div></div><hr>';


 htmlRows += '<div class="row"><div class="col-md-5"><span>Resources</span></div><div class="col-md-7"><input aria-label="Resources" type="text" name="quantity[]" id="quantity__2"+count+'"  onblur="quantityCheckold(this.id)"  class="form-control amount" value=""  autocomplete="off" placeholder="Resources"> </div></div><hr>';

  htmlRows += '<div class="row"><div class="col-md-5"><span>Base Rate*</span></div><div class="col-md-7"> <input aria-label="Rate" type="text" name="price[]" id="price__2"+count+'" class="form-control price amount" value="" autocomplete="off" placeholder="Rate"></div></div><hr>';
    htmlRows += '<div class="row"><div class="col-md-5"><span>Amount</span></div><div class="col-md-7"><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount__2"+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount__2"+count+'"><input type="hidden" name="ptax_amount[]" value="" readonly class="form-control price amount" id="ptax_amount__2"+count+'"> <input type="hidden" name="p_amount[]" value="" readonly class="form-control price amount" id="p_amount__2"+count+'"><input aria-label="Amount" type="text" name="dis_amount[]" readonly id="dis_amount__2"+count+'" class="form-control amount" placeholder="Amount" value="" autocomplete="off"> <input type="hidden" name="amount[]" readonly id="amount__2"+count+'" class="form-control amount" value=""></div></div><hr>';
     htmlRows += '<div class="row"> <div class="col-md-5"><div class="checkbox-fade fade-in-primary"><label class="form-label"><input aria-label="Experience Base[" type="checkbox"  name="experience_base['+(count-1)+']"> <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span> <span>Report Expe Base </span> </label>  </div> </div><div class="col-md-7"><div class="checkbox-fade fade-in-primary "> <label class="form-label"><input aria-label="Allow Overtime[" type="checkbox"  name="allow_overtime['+(count-1)+']"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span> <span>&nbsp;Allow Overtime </span></label></div></div></div>';

        htmlRows += '</div></div>';
        $('#invoiceItem').append(htmlRows);
        $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
        $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
        $(".js-data-example-ajax-multiselectss"+count).select2({

        ajax: {
           // url: "https://api.github.com/search/repositories",
           url: "views/custom_getcityautotype.php",
           dataType: 'json',
           delay: 250,
           data: function(params) {
            var stringids = "";
            $(".selectitemorservice").each(function(){
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

function deleteitem(cardid){

Swal.fire({
  title: 'Are you sure?',
  text: "You won't to Delete this!",
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#3085d6',
  cancelButtonColor: '#d33',
  confirmButtonText: 'Yes, delete it!'
}).then((result) => {
  if (result.isConfirmed) {
    $(".cardid"+cardid).remove();
  }
})


}

</script>
<style>
    .deleteiconcustom{
        position: absolute;
        top: 3px;
        z-index: 999;
        left:1px;
    }
</style>

<div class="row" id="invoiceItem">
    <?php if(count((array)$table2)>0){ $i=1;
 foreach ($table2 as $tab2) { $tab2['id']=0; ?>
    <div class="col-md-6 itemRow cardid<?php echo $i;?>"><div class="card  p-5 "><span class="deleteiconcustom text-danger f-22" onclick="deleteitem(<?php echo $i;?>)" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Delete VRC Card" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-trash"></i></span>
        <div class="row">
            <div class="col-md-5">
                <span>job Title</span>
            </div>
            <div class="col-md-7">
                  <select aria-label="Product Code[]" class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4"  data-tablename="job_titles"  data-fieldsname="job_title" name= "productCode[]"  id="productCode_<?php echo $i; ?>" onchange="itemChangejob(this.value,<?php echo $i; ?>)">
                     <option value="0"> Select Job Title  </option>
                    <?php $like_item_check = 0; foreach ($itemres as $item) {  if($tab2['item_id']== $item['id']){  ?>
                     <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['job_title']; ?> </option>
                        <?php } ?>
                        <?php } ?>
                </select>
        <input type="hidden" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>">
        <input type="hidden" name="purchase_account[]" id="purchase_account_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['purchase_account']; ?>">
        <input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_<?php echo $i; ?>" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']][a]; ?>">
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-md-5">
                <span>Description</span>
            </div>
            <div class="col-md-7">

        <input aria-label="Product Name[]" type="text" name="productName[]" id="productName_<?php echo $i; ?>" value="<?php echo $tab2['description']; ?>" class="form-control " placeholder="Description" data-bs-toggle="tooltip" data-placement="bottom" title="Description">

            </div>
        </div>
        <hr>
         <div class="row">
            <div class="col-md-5">
                <span>Min Experience</span>
            </div>
            <div class="col-md-7">
                 <div class="input-group input-group-button ">
                    <input aria-label="Min Exp[]" type="text" name="min_exp[]" id="min_exp_<?php echo $i; ?>"  class="form-control form-control-sm width1" value="<?php echo $tab2['min_exp']; ?>" placeholder="Min Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience">
                    <span class="input-group-text addon-style">
                        <span class="">Years</span>
                    </span>
                </div>
            </div>
        </div>
        <hr>
         <div class="row">
            <div class="col-md-5">
                <span>Max Experience</span>
            </div>
            <div class="col-md-7">
                <div class="input-group input-group-button ">
                    <input aria-label="Max Exp[]" type="text" name="max_exp[]" id="max_exp_<?php echo $i; ?>" class="form-control form-control-sm width1" value="<?php echo $tab2['max_exp']; ?>" placeholder="Max Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience">
                    <span class="input-group-text addon-style">
                        <span class="">Years</span>
                    </span>
                </div>
            </div>
        </div>
        <hr>
         <div class="row">
            <div class="col-md-5">
                <span>Monthly Leaves</span>
            </div>
            <div class="col-md-7">
                  <div class="input-group input-group-button">
        <input aria-label="Min Leave[]" type="text" class="form-control width1" name="min_leave[]" id="min_leave_<?php echo $i; ?>" placeholder="Monthly Leaves" data-bs-toggle="tooltip" value="<?php echo $tab2['min_leave']; ?>" data-placement="bottom" title="Monthly Leaves">
    <span class="input-group-text addon-style">
    <span class="">Days</span>
</span>
    </div>

            </div>
        </div>
        <hr>
                <div class="row">
            <div class="col-md-5">
                <span>Unit *</span>
            </div>
            <div class="col-md-7">
                <select aria-label="Uom[]" class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>">
                    <option value="0"> Select Any UOM  </option>
                    <?php foreach ($uomlist as $uom) {  ?>
                         <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?> selected <?php
} ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <span>Resources *</span>
            </div>
            <div class="col-md-7">
                      <input aria-label="Quantity[]" type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="quantityCheckold(this.id)"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off" placeholder="Resources">
        <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <span>Base Rate *</span>
            </div>
            <div class="col-md-7">
                  <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off" placeholder="Rate">

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <span>Amount</span>
            </div>
            <div class="col-md-7">
                <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
         <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
        <input aria-label="Dis Amount[]" type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount" placeholder="Amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-2">
                <?php if($_GET['Rid'] !="" && isset($_GET['Rid'])){ ?>
                 <div class="btn btn-icon btn-primary btn-circle mr-2" onclick="toopenpredim(<?php echo $i.",".$tab2['id']; ?>)" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Per-diem" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="fas fa-dollar-sign"></i></div>
             <?php } ?>
            </div>
            <div class="col-md-5">
                 <div class="checkbox-fade fade-in-primary">
              <label class="form-label">
                <input aria-label="Checkbox" type="checkbox" <?php if($tab2['experience_base']=='0'){ echo "checked";
} ?> name="experience_base[<?php echo $i; ?>]">
                <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                <span>Report Expe Base </span>
              </label>
            </div>
            </div>
            <div class="col-md-5">
                 <div class="checkbox-fade fade-in-primary">
              <label class="form-label">
                <input aria-label="Checkbox" type="checkbox" <?php if($tab2['allow_overtime']=='0'){ echo "checked";
} ?> name="allow_overtime[<?php echo $i; ?>]">
                <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                <span>Allow Overtime</span>
              </label>
            </div>
            </div>
        </div>
</div></div>
<?php $i++; ?>
<?php } ?>
<?php } ?>
</div>

