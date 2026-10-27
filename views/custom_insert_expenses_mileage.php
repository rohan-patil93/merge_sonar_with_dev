
<script type="text/javascript">
    function getcategorybymileage(categoryid) {
        $.ajax({
            url: "ajax.php",
            data:'page=getcategorybyfilter&categoryid='+categoryid,
            type: "POST",
            success:function(data){
                console.log(data);
                window.location.href = "index.php?option=COM_INSERT_EXPENSE_CATEGORIES&TYPE=2&DS="+categoryid+"&Rid="+data;
            },
            error:function (){}
        });
    }
    function addedurows(){
        var count = $(".itemRow").length;
        count = count + 1;
        jQuery(document).ready(function() {
         KTBootstrapDatepicker.init();
     });
        var htmlRows = '';
        htmlRows += '<tr class="">';

        htmlRows += '<td> <input type="hidden" name="exp_rowid[]" value="" >'+count+'</td>';

         htmlRows += '<td class="w-full"><select aria-label="Name[]" class="form-select js-example-basic-single mileagename" name="name[]">';
                <?php foreach($vehicleTypes as $vtype){ ?>
                htmlRows += '<option value="<?php echo $vtype['genc_detail_code']; ?>"><?php echo $vtype['genc_detail_name']; ?></option>';
                <?php } ?>
        htmlRows += '</select></td>';

        htmlRows += '<td><input aria-label="Amount[]" type="text" name="amount[]"  class="form-control text-right" autocomplete="off" required></td>';

       htmlRows += '</tr>';
       $('#edutable').append(htmlRows);
       $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
        $( ".datepicker" ).datepicker({
            showOn: "both",
            buttonText: "<i class='ti-calendar'></i>",
            dateFormat: date_format,
            changeMonth: true,
            changeYear: true
        });
   }

function cehckalredayavailablename(valu){
    var tempary = [];
    $(".mileagename").each(function(index) {
        if ($.inArray($(this).val(), tempary) != -1)
        {
          alert("already exist this mileage");
        }
        tempary.push($(this).val());

    });
}

</script>
<div class="card card-custom ">
    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
        <div class="card-body row">
        <div class="form-group col-lg-4">
            <span class="font-weight-bolder form-label">Default Unit<span class="text-danger">*</span></span></br>
            <div class="form-radio">
                <div class="radio-inline">
                  <label class="form-label">
                     <input aria-label="Default Unit" type="radio" name="default_unit" value="0"  checked style="opacity: 1;" required />
                     <i class="helper"></i> KM</label>
                 </div>
                 <div class="radio-inline">
                  <label class="form-label">
                   <input aria-label="Default Unit" type="radio" name="default_unit" value="1" <?php echo $expense_milelist['0']['default_unit']=='1' ? "checked":"";?>  style="opacity:1;" required /> <i class="helper"></i> Mile</label>
               </div>
           </div>
       </div>
       <div class="row ">
            <div class="form-group col-lg-6">
                <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer " id="edutable">
                    <tr>
                        <th scope="col" class="" style="width: 2%;">#</th>

                        <th scope="col" class="" style="width: 50%;">Vihicle Type</th>
                        <th scope="col" class="thheader" style="width: 40%;">Rate (Per KM Or Mile)</th>
                    </tr>
                    <?php  $k=0;
                    if(count((array)$expense_milelist) == 0){
                        foreach($vehicleTypes as $vtype){
                          $expense_milelist[] = array('name'=>$vtype['genc_detail_code'],'amount'=>0);
                        }
                    }
                        foreach ($expense_milelist as $inst) { $k++; $readonly = ""; if($inst['id']!=""){ $readonly="readonly";
}?>
                         <tr class="rowcount">
                             <td><?php echo $k; ?>
                             <input type="hidden" name="exp_rowid[]" class="form-control itemRow" value="<?php echo $inst['id']; ?>" <?php echo $readonly; ?> >
                         </td>

                        <td class="w-full event_disabled">
                            <select aria-label="Name[]" class="form-select js-example-basic-single mileagename" name="name[]">
                                <?php foreach($vehicleTypes as $vtype){ ?>
                                <option value="<?php echo $vtype['genc_detail_code']; ?>" <?php echo $inst['name']==$vtype['genc_detail_code']?"selected":""; ?>><?php echo $vtype['genc_detail_name']; ?></option>
                                <?php } ?>
                            </select>

                      </td>

                      <td>
                        <input aria-label="Amount[]" type="text" name="amount[]" class="form-control text-right decimalnumberallowe" value="<?php echo $inst['amount']; ?>" autocomplete="off">
                    </td>

                </tr>
            <?php }  ?>
    </table>
</div>
    <div class="row mb-5">
       <div class="col-xs-12 col-sm-3 col-md-3 col-lg-8">

    <input type="hidden" name="rid" value="<?php echo $rid; ?>" />

    </div>
</div>

    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold m-l-10 mt-10 col-md-2"   name="submit_continue" value="saveexpmileage"><i class="la la-check"></i>Save Mileages</button>
    </div>
</div>
</form>

</div>

