<?php

    $wrdata["where"] = array('is_active' => '0');
    $wrdata["field"] = "id,job_title";
    $data = urlencode(json_encode($wrdata));
    $joburl = API."?mode=gettabledetailswithconditions&table=job_titles&data=".$data;

    $job_data = callAPI($joburl);
    $itemres = json_decode($job_data, 1);
    $wrdata = array();
    $wrdata["where"] = array('is_active' => '0');
    $data = urlencode(json_encode($wrdata));
    $locurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$data;
    $locdata = callAPI($locurl);
    $locdata = json_decode($locdata, 1);
    foreach((array)$locdata as $locda){
        $locs[$locda['id']] = $locda['location_name'];
    }
    if(count((array)$uomlist)==0){
        $uomurl =  API."?mode=gettabledetails&fid=genc_code&rid=JOBUOM&table=".TBL_GENRAL_CODE_DETAILS;
        $uomlist = callAPI($uomurl);
        $uomlist = json_decode($uomlist,1);
    }
?>
<style>
    .deleteiconcustom{
        position: absolute;
        top: 3px;
        z-index: 999;
    right :2px;
    cursor: pointer;
    }
</style>
<script type="text/javascript">

    function assignemployeems(trn_id, item_id){

        var table_name = document.getElementById('table_name').value;
        console.log(table_name);
        jQuery.ajax({
            url: "views/custom_insert_milestone_assign_employee_popup.php",
            data:'table='+table_name+'&transaction_id='+trn_id+'&item_id='+item_id,
            type: "POST",
            success:function(data){
                $('#dynamic_popupxxl').html(data);
                $('#kt_modal_dynamic_popupxxl').modal("show");
            },
            error:function (){}
        });
    }

    $(document).ready(function(){
        console.log("HHHHHHHHH");
        var count = $(".itemRow").length;

        $(document).on('click', '#addRows', function() {
            console.log("HHHHHHHHH");
            count++;
            var htmlRows = '';
            htmlRows += '<tr> <td class=" m-t-5" style="width: 1%;">  <div class="checkbox-fade fade-in-primary" style="display: inherit;margin: 0px;"><label class="kt-checkbox"><input aria-label="Checkbox" class="itemRow" type="checkbox"> <span class="cr"> <i class="cr-icon icofont icofont-ui-check txt-primary"></i></span> </label></div> </td>';
            htmlRows += '<td  class="p-1">  <input aria-label="Product Code[]" type="text"   name="productCode[]" id="productCode_'+count+'" class="form-control mt-2" autocomplete="off" value=""></td>';
           htmlRows += '<td id="other_'+count+'" class="p-1"> <textarea aria-label="Product Name[]"  name="productName[]" id="productName_'+count+'" class="form-control mt-2"></textarea></td>';
            htmlRows += '<td ><input type="text" class="form-control datepicker minw-140 effective_date "  value=""  name="effective_date[]"  id="kt_datepicker_3_'+count+'" data-bs-toggle="tooltip" data-placement="bottom" title="Planned Date" ></td>';
            htmlRows += '<td ><input type="text" class="form-control datepicker minw-140 expire_date'+count+'"  value=""  name="expire_date[]"  id="kt_datepicker_4_'+count+'" data-bs-toggle="tooltip" data-placement="bottom" title="" ></td>';

            htmlRows += '<td><input aria-label="Price[]" type="text" name="price[]" id="price_'+count+'" class="form-control price amount" value="0.00" autocomplete="off"></td>';
            htmlRows += '<td class="text-center"></td> </tr>';
            console.log(htmlRows);
            $('#sowinvoiceItem').append(htmlRows);
            $( ".datepicker" ).datepicker({
                showOn: "both",
                buttonText: "<i class='ti-calendar'></i>",
                dateFormat: date_format,
                changeMonth: true,
                changeYear: true
            });
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
<div id="showidsow" class="m-b-10" >
    <div class="row" id="" >
        <table class=" table table-hover " id="sowinvoiceItem">
            <tr>
                <th scope="col" class="thheader p-1" style="width: 1%;">
                    <div class="checkbox-fade fade-in-primary" style="display: inherit;margin: 0px;">
                        <label class="kt-checkbox">
                            <input aria-label="Check All" id="checkAll"  type="checkbox">
                            <span class="cr">
                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                            </span>
                        </label>
                    </div>
                </th>
                <th scope="col" class="p-1" style="width: 20%;">Phase</th>
                <th scope="col" class="p-1" style="width: 30%;">Description </th>
                <th scope="col" class="thheader p-1" style="width: 9%;">Planned Date</th>
                <th scope="col" class="thheader p-1" style="width: 9%;">Expire Date</th>
                <th scope="col" class="thheader p-1 text-right" style="width: 20%;">Amount</th>
                <th scope="col" class="thheader p-1 text-right" style="width: 15%;">Resource <span class="requried">*</span></th>

            </tr>
             <?php if(count((array)$table2)>0){ $i=1;
                foreach ($table2 as $tab2) {
                $item_details = json_decode($tab2['item_details'],1);
                ?>
                <tr>
                    <td class="  m-t-5" style="width: 1%;">
                        <div class="checkbox-fade fade-in-primary" style="display: inherit;margin: 0px;">
                            <label class="kt-checkbox">
                                <input aria-label="Checkbox" class="itemRow" type="checkbox">
                                <span class="cr">
                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                            </label>
                        </div>
                    </td>
                   <td id='' class="p-1">
                        <input aria-label="Product Code[]" type="text"   name="productCode[]" id="productCode_<?php echo $i; ?>" class="form-control mt-2" autocomplete="off" value="<?php echo $tab2['account_code']; ?>">
                    </td>
                    <td id='other_<?php echo $i; ?>' class="p-1">
                        <textarea aria-label="Product Name[]"  name="productName[]" id="productName_<?php echo $i; ?>" class="form-control mt-2" autocomplete="off"><?php echo $tab2['description']; ?></textarea>
                    </td>

                    <td >
                        <input aria-label="Input field" type="text" class="form-control datepicker minw-140 effective_date<?php echo $i; ?>"  value="<?php echo fromDate($tab2['effective_date']); ?>"  name="effective_date[]"  id="kt_datepicker_3_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Planned Date" >
                    </td>
                    <td >
                        <input aria-label="Input field" type="text" class="form-control datepicker minw-140 expire_date<?php echo $i; ?>"  value="<?php echo fromDate($tab2['expire_date']); ?>"  name="expire_date[]"  id="kt_datepicker_4_<?php echo $i; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="Planned Date" >
                    </td>

                    <td >
                      <input aria-label="Price[]" type="text" name="price[]" id="price_<?php echo $i; ?>" class="form-control price amount" value="<?php echo number_format($tab2['item_price'],2, '.', ''); ?>" autocomplete="off">
                    </td>
                    <td class="text-center">
                        <?php if($rid!=""){ ?>
                        <button type="button" class="btn-primary btn" onclick="assignemployeems(<?php echo $rid; ?>, <?php echo $tab2['id'];  ?>)"><i class="ti-plus"></i>Resource</button>
                        <?php } ?>
                    </td>
                </tr>
            <?php $i++;
}
            } ?>
        </table>
    </div>
</div>

</div>
