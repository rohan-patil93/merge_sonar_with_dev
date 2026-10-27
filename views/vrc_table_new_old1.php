<?php 
    $joburl = API."?mode=gettabledetailswithconditions&table=job_titles"; 
    $job_data = callAPI($joburl);
    $itemres = json_decode($job_data, 1);
    $data12 = array();
    $data12["where"] = array('is_active'=> '0');
    $data = urlencode(json_encode((array)$data12));
    $locurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$data; 
    $locdata = callAPI($locurl);
    $locdata = json_decode($locdata, 1);
    foreach((array)$locdata as $locda){
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
    $(document).ready(function(){
        var count = $(".itemRow").length;
        $(document).on('click', '#addRows', function() {
            count++;
            console.log("check val"+count);
            var htmlRows = '';
            htmlRows += '<div class="col-md-6 itemRow cardid'+count+'"> <div class="card  p-5 "><span class="deleteiconcustom text-right text-danger f-22" onclick="deleteitem('+count+')" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Delete VRC Card"><i class="ti-trash"></i></span>';  
            htmlRows += '<div class="row"><div class="col-md-5"><label>job Title</label></div><div class="col-md-7"> <select class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4 '+count+' col-lg-4 selectitemorservice"  name= "productCode[]" id="productCode_'+count+'"  onchange="itemChangejob(this.value,'+count+')" data-tablename="job_titles"  data-fieldsname="job_title" > <option value="0"> Select Job Title </option></select><input type="hidden" name="item_name[]" id="item_name_'+count+'" class="form-control" value="" autocomplete="off">  <input type="hidden" name="purchase_account[]" id="purchase_account_'+count+'" class="form-control" value="" autocomplete="off"> <input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_'+count+'" class="form-control" value="" autocomplete="off"></div></div><hr>';
             htmlRows += '<div class="row"><div class="col-md-5"><label>Description</label></div><div class="col-md-7"> <input type="text" name="productName[]" id="productName_'+count+'" value="" class="form-control " placeholder="Description" data-bs-toggle="tooltip" data-placement="bottom" title="Description"></div></div><hr>';
            htmlRows += '<div class="row"><div class="col-md-5"><label>Min Experience</label></div><div class="col-md-7"> <div class="input-group input-group-button "> <input type="text" name="min_exp[]" id="min_exp_'+count+'"  class="form-control form-control-sm width1" value="" placeholder="Min Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience" > <span class="input-group-text addon-style"><label class="">Years</label></span></div></div></div><hr>';
            htmlRows += '<div class="row"><div class="col-md-5"><label>Max Experience</label></div><div class="col-md-7"><div class="input-group input-group-button "> <input type="hidden" name="quantity_old[]" id="quantity_old_'+count+'"  class="form-control amount" value=""  autocomplete="off"><input type="text" name="max_exp[]" id="max_exp_'+count+'" class="form-control form-control-sm  width1" value="" placeholder="Max Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience"> <span class="input-group-text addon-style"><label class="">Years</label></span></div> </div></div><hr>';

            htmlRows += '<div class="row"><div class="col-md-5"><label>Monthly Leaves</label></div><div class="col-md-7"><div class="input-group input-group-button"> <input type="text" name="min_leave[]" id="min_leave_'+count+'" class="form-control width1" placeholder="Monthly Leaves" data-bs-toggle="tooltip" data-placement="bottom" title="Monthly Leaves"> <span class="input-group-text addon-style"><label class="">Days</label></span></div></div></div><hr>';

            htmlRows += '<div class="row"><div class="col-md-5"><label>Units *</label></div><div class="col-md-7"><select class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom_'+count+'"><option value="0"> Select Any Units </option> <?php foreach ((array)$uomlist as $uom) {   $name = htmlspecialchars($uom['genc_detail_name'], ENT_QUOTES); ?> <option    value ="" > {$name} </option> <?php } ?>  </select> </div></div><hr>';
            htmlRows += '<div class="row"><div class="col-md-5"><label>Resources</label></div><div class="col-md-7"><input type="text" name="quantity[]" id="quantity_'+count+'"  onblur="quantityCheckold(this.id)"  class="form-control amount" value=""  autocomplete="off" placeholder="Resources"> </div></div><hr>';

            htmlRows += '<div class="row"><div class="col-md-5"><label>Base Rate*</label></div><div class="col-md-7"> <input type="text" name="price[]" id="price_'+count+'" class="form-control price amount" value="" autocomplete="off" placeholder="Rate"></div></div><hr>';
            htmlRows += '<div class="row"><div class="col-md-5"><label>Amount</label></div><div class="col-md-7"><input type="hidden" name="dis_ptax_amount[]" value="" readonly class="form-control price amount" id="dis_ptax_amount_'+count+'"><input type="hidden" name="pdiscount_amount[]" value="" readonly class="form-control price amount" id="pdiscount_amount_'+count+'"><input type="hidden" name="ptax_amount[]" value="" readonly class="form-control price amount" id="ptax_amount_'+count+'"> <input type="hidden" name="p_amount[]" value="" readonly class="form-control price amount" id="p_amount_'+count+'"><input type="text" name="dis_amount[]" readonly id="dis_amount_'+count+'" class="form-control amount" placeholder="Amount" value="" autocomplete="off"> <input type="hidden" name="amount[]" readonly id="amount_'+count+'" class="form-control amount" value="" autocomplete="off"></div></div><hr>';        
            htmlRows += '<div class="row"><div class="col-md-2"></div><div class="col-md-5"><div class="checkbox-fade fade-in-primary "> <label class="form-label"><input type="checkbox"  name="experience_base['+count+']" checked><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span> <span>Report Expe Base </span></label></div></div><div class="col-md-5"><div class="checkbox-fade fade-in-primary "> <label class="form-label"><input type="checkbox"  name="allow_overtime['+count+']"><span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i> </span> <span>&nbsp;Allow Overtime </span></label></div></div></div>'; 
            htmlRows += '</div></div>';
            $('#invoiceItem').append(htmlRows);
            $(".js-data-example-ajax-multiselect").select2();
            //$(".js-example-disabled-results").select2();
            $(document).ready(function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
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
<div class="row" id="invoiceItem">
    <?php if(count((array)$table2)>0){ $i=1; 
 foreach ((array)$table2 as $tab2) { $tab2['id']=0; ?>   
    <div class="col-md-6 itemRow cardid<?php echo $i;?>"><div class="card  p-5 "><span class="deleteiconcustom text-danger  text-right f-22" onclick="deleteitem(<?php echo $i;?>)" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Delete VRC Card"><i class="ti-trash"></i></span>
        <div class="row">
            <div class="col-md-5">
                <label>job Title</label> 
            </div>
            <div class="col-md-7">
                  <select class="form-select js-data-example-ajax-multiselect selectitemorservice col-lg-4"  data-tablename="job_titles"  data-fieldsname="job_title" name= "productCode[]"  id="productCode_<?php echo $i; ?>" onchange="itemChangejob(this.value,<?php echo $i; ?>)">
                     <option value="0"> Select Job Title  </option>
                    <?php $like_item_check = 0; foreach ($itemres as $item) {  if($tab2['item_id']== $item['id']){  ?>
                     <option selected value ='<?php echo $item['id']; ?>'> <?php echo $item['job_title']; ?> </option>
                        <?php } } ?>
                </select>
                <input type="hidden" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off"> 
                <input type="hidden" name="purchase_account[]" id="purchase_account_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['purchase_account']; ?>" autocomplete="off">
                <input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_<?php echo $i; ?>" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']]['a']; ?>" autocomplete="off">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <label>Description</label> 
            </div>
            <div class="col-md-7">
                 <input type="hidden" name="item_name[]" id="item_name_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['item_name']; ?>" autocomplete="off"> 
                <input type="hidden" name="purchase_account[]" id="purchase_account_<?php echo $i; ?>" class="form-control" value="<?php echo $tab2['purchase_account']; ?>" autocomplete="off">
                <input type="hidden" name="purchase_account_aval[]" id="purchase_account_aval_<?php echo $i; ?>" class="form-control" value="<?php echo $response_code[$tab2['purchase_account']]['a']; ?>" autocomplete="off">

                <input type="text" name="productName[]" id="productName_<?php echo $i; ?>" value="<?php echo $tab2['description']; ?>" class="form-control " placeholder="Description" data-bs-toggle="tooltip" data-placement="bottom" title="Description">

            </div>
        </div>
        <hr>
         <div class="row">
            <div class="col-md-5">
                <label>Min Experience</label> 
            </div>
            <div class="col-md-7">
                 <div class="input-group input-group-button ">
                    <input type="text" name="min_exp[]" id="min_exp_<?php echo $i; ?>"  class="form-control form-control-sm width1" value="<?php echo $tab2['min_exp']; ?>" placeholder="Min Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Minmum Experience">
                    <span class="input-group-text addon-style">
                        <label class="">Years</label>
                    </span>
                </div> 
            </div>
        </div>
        <hr>
         <div class="row">
            <div class="col-md-5">
                <label>Max Experience</label> 
            </div>
            <div class="col-md-7">
                <div class="input-group input-group-button ">
                    <input type="text" name="max_exp[]" id="max_exp_<?php echo $i; ?>" class="form-control form-control-sm width1" value="<?php echo $tab2['max_exp']; ?>" placeholder="Max Exp" data-bs-toggle="tooltip" data-placement="bottom" title="Maxmum Experience"> 
                    <span class="input-group-text addon-style">
                        <label class="">Years</label>
                    </span>
                </div>
            </div>
        </div>
        <hr>
         <div class="row">
            <div class="col-md-5">
                <label>Monthly Leaves</label> 
            </div>
            <div class="col-md-7">
                  <div class="input-group input-group-button"> 
        <input type="text" class="form-control width1" name="min_leave[]" id="min_leave_<?php echo $i; ?>" placeholder="Monthly Leaves" data-bs-toggle="tooltip" value="<?php echo $tab2['min_leave']; ?>" data-placement="bottom" title="Monthly Leaves">
    <span class="input-group-text addon-style">
    <label class="">Days</label>
</span>
    </div>
               
            </div>
        </div>
        <hr>
                <div class="row">
            <div class="col-md-5">
                <label>Unit *</label> 
            </div>
            <div class="col-md-7">
                <select class="form-control form-select js-example-basic-single"  name= "uom[]"  id="uom_<?php echo $i; ?>">
                    <option value="0"> Select Any UOM  </option>
                    <?php foreach ($uomlist as $uom) {  ?>
                         <option   <?php if($tab2['uom'] == $uom['genc_detail_code']){ ?> selected <?php } ?>  value ="<?php echo $uom['genc_detail_code']; ?>" > <?php echo $uom['genc_detail_name']; ?> </option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <label>Resources *</label> 
            </div>
            <div class="col-md-7">
                      <input type="text" name="quantity[]" id="quantity_<?php echo $i; ?>"  onblur="quantityCheckold(this.id)"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off" placeholder="Resources"> 
        <input type="hidden" name="quantity_old[]" id="quantity_old_<?php echo $i; ?>"  class="form-control amount" value="<?php echo $tab2['quantity']; ?>"  autocomplete="off">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <label>Base Rate *</label> 
            </div>
            <div class="col-md-7">
                  <input type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off" placeholder="Rate">

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-5">
                <label>Amount</label> 
            </div>
            <div class="col-md-7">
                <input type="hidden" name="dis_ptax_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="dis_ptax_amount_<?php echo $i; ?>">
        <input type="hidden" name="pdiscount_amount[]" value="<?php echo customeummarmonyformate($tab2['tax_amount']); ?>" readonly class="form-control price amount" id="pdiscount_amount_<?php echo $i; ?>">
        <input type="hidden" name="ptax_amount[]" value="<?php echo $tab2['tax_amount']; ?>" readonly class="form-control price amount" id="ptax_amount_<?php echo $i; ?>">
         <input type="hidden" name="p_amount[]" value="<?php echo $tab2['amount']; ?>" readonly class="form-control price amount" id="p_amount_<?php echo $i; ?>">
        <input type="text" name="dis_amount[]" readonly <?php echo $sty; ?> id="dis_amount_<?php echo $i; ?>" class="form-control amount" placeholder="Amount" value="<?php echo customeummarmonyformate($tab2['total_amount']); ?>" autocomplete="off">
        <input type="hidden" name="amount[]" readonly id="amount_<?php echo $i; ?>" class="form-control amount" value="<?php echo $tab2['total_amount']; ?>" autocomplete="off">
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-2 ">
                <?php if($_GET['Rid'] !="" AND isset($_GET['Rid'])){ ?>
                 <div class="btn btn-icon btn-primary btn-circle mr-2" onclick="toopenpredim(<?php echo $i.",".$tab2['id']; ?>)" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Per-diem" ><i class="fas fa-dollar-sign"></i></div>
             <?php } ?>
            </div>
            <div class="col-md-5">
                 <div class="checkbox-fade fade-in-primary">
              <label class="form-label">
                <input type="checkbox" <?php if($tab2['experience_base']!='1'){ echo "checked"; } ?> name="experience_base[<?php echo $i; ?>]">
                <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                <span>Report Expe Base </span>
              </label>
            </div>
            </div>
            <div class="col-md-5 ">
                 <div class="checkbox-fade fade-in-primary">
              <label class="form-label">
                <input type="checkbox" <?php if($tab2['allow_overtime']=='0'){ echo "checked"; } ?> name="allow_overtime[<?php echo $i; ?>]">
                <span class="cr">
                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                </span>
                <span>&nbsp;Allow Overtime </span>
              </label>
            </div>
            </div>
        </div>
</div></div>
<?php $i++; } } ?> 
</div>