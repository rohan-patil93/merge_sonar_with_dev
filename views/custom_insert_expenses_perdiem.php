<script type="text/javascript">
  function getcategorybyperdiem(categoryid) {
    $.ajax({
      url: "ajax.php",
      data:'page=getcategorybyfilter&categoryid='+categoryid,
      type: "POST",
      success:function(data){
        console.log(data);
        window.location.href = "index.php?option=COM_INSERT_EXPENSE_CATEGORIES&TYPE=3&DS="+categoryid+"&Rid="+data;
      },
      error:function (){}
    });
  }
  function addperdiemrows(){
    var count = $(".itemRow_perdiem").length;
    count = count + 1;
    var htmlRows = '';
    htmlRows += '<tr class="">';
    htmlRows += '<td> <input type="hidden" name="perdiem_rowid[]" value="" >'+count+'</td>';

    htmlRows += '<td class="w-full"><select aria-label="Perdiem Name[]" class="form-select js-example-basic-single" name="perdiem_name[]">';
    <?php foreach($countries as $countr){ ?>
        htmlRows += '<option value="<?php echo $countr['id']; ?>"><?php echo preg_replace("/[\r\n'\"]/", '', $countr['country_name']); ?></option>';
    <?php } ?>
    htmlRows += '</select></td>';
    htmlRows += '<td><input aria-label="Perdiem Amount[]" type="text" name="perdiem_amount[]"  class="form-control text-right decimalnumberallowe" autocomplete="off" required></td>';

       htmlRows += '</tr>';
       $('#perdiemtable').append(htmlRows);
       $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
     }

     function cehckalredayavailablenameperdiem(valu){
      var tempary = [];
      $(".perdiemname").each(function(index) {
        if ($.inArray($(this).val(), tempary) != -1)
        {
          alert("already exist this mileage");
          $(this).val("");
        }
        tempary.push($(this).val());

      });
    }
  </script>
  <div class="card card-custom ">
    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
      <input type="hidden" name="default_unit" value="2" />
      <div class="card-body ">
        <div class="row ">
          <div class="form-group col-lg-6">
            <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer" id="perdiemtable">
              <tr>
                <th scope="col" class="" style="width: 2%;">#</th>
                <th scope="col" class="" style="width: 25%;">Name</th>
                <th scope="col" class="thheader" style="width: 20%;">Rate Per Diem</th>
              </tr>
              <?php $k=0;
              if(count((array)$expense_perdiemlist) == 0){
                $expense_perdiemlist[] = array('id'=>'');
              }
              foreach ($expense_perdiemlist as $inst) { $k++;  $readonly = ""; if($inst['id']!=""){ $readonly="readonly";
}?>
              <tr class="rowcount">
               <td><?php echo $k; ?>
               <input type="hidden" name="perdiem_rowid[]" class="form-control itemRow_perdiem" value="<?php echo $inst['id']; ?>" <?php echo $readonly; ?>>
             </td>
             <td class="w-full">
              <select aria-label="Perdiem Name[]" class="form-select js-example-basic-single" name="perdiem_name[]">
                    <?php foreach($countries as $countr){ ?>
                    <option value="<?php echo $countr['id']; ?>" <?php echo $inst['name']==$countr['id']?"selected":""; ?>><?php echo $countr['country_name']; ?></option>
                    <?php } ?>
                </select>

            </td>
            <td>
                <input aria-label="Perdiem Amount[]" type="text" name="perdiem_amount[]" class="form-control text-right decimalnumberallowe" value="<?php echo $inst['amount']; ?>" autocomplete="off" <?php echo $readonly; ?> required>
            </td>
          </tr>
        <?php }  ?>
      </table>
    </div>
    <div class="row mb-5">
      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-6">
        <button class="btn btn-success "  onclick="addperdiemrows()" id="addRows" type="button">+ Add More</button>
      </div>
    </div>
    <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
    <button id="kt_login_signin_submit" class="btn btn-primary m-l-10 mt-10 col-lg-2"   name="submit_continue" value="saveexpperdiem"><i class="la la-check"></i> Save Per Diem</button>
  </div>
</div>
</form>
</div>
</div>
