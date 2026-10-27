<?php
$empid = 1;
$url = API."?mode=togetpayadminpayrealist&empid=".$empid;
$cudata = callAPI($url);
$payrr = json_decode($cudata,1);
$pay_data = $attdetails['attendance_data'];
$paydata = $empalldata['pay_area_employees'];

?>
<script type="text/javascript">
   function savepayrollemployee(){
    var newempidcode = $('#newempidcode').val();
    var newempid = $('#newempid').val();
    var pay_area_id = $('#pay_area_id').val();
    jQuery.ajax({
        url: "ajax.php",
        data:'page=setupcomponents&table_name=pay_area_employees&pay_area_id='+pay_area_id+'&emp_id='+newempid,
        type: "POST",
        success:function(data){
          console.log(data);

        },
        error:function (){}
      });

    window.location = "index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=UMA&EMPID="+newempidcode;
  }
</script>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <div class="card card-custom mb-5 ">
      <div class="card-header">
        <h5>Link Payroll</h5>
        <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>
            Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row col-lg-12">
         <div class="form-group col-lg-6">
              <span>Pay Area </span>
              <select aria-label="Pay Area" class="js-example-basic-single" name="pay_area_id" id="pay_area_id">
                <option value=""> Select Role</option>
                <?php foreach($payrr['pay_areas'] as $pay_areas){ ?>
                  <option <?php if($paydata['pay_area_id'] == $pay_areas['id']){ echo "selected";
}  ?> value="<?php echo $pay_areas['id'] ?>"><?php echo $pay_areas['name'] ?></option>
                <?php } ?>
              </select>
            </div>

        </div>
      </div>
      <div class="text-right p-3">
        <button class="btn btn-primary" onclick="savepayrollemployee(<?php echo $shift['id']; ?>)" >
          Save
        </button>
      </div>
    </div>
  </div>
</div>
