<?php
$url = API."?mode=togetlocationemployeeattendance&empid=".$_GET['newempid'];
$cudata = callAPI($url);
$attdetails = json_decode($cudata,1);
$attendance_data = $attdetails['attendance_data'];
$fields = array('maximum_hours_cap', 'full_day_hours', 'half_day_hours', 'break_time',
'grace_perieod', 'entry_grace_perieod', 'exit_perieod');
?>
<script type="text/javascript">
  function saveshiftemployee(){
    var newempidcode = $('#newempidcode').val();
    var newempid = $('#newempid').val();
    var shift_id = $('#shift_id').val();
    var radioButtons = document.getElementsByName('shift_setting_ids');

    for (var i = 0; i < radioButtons.length; i++) {
        if (radioButtons[i].checked) {
            var shift_setting_id =  radioButtons[i].value;
        }
    }
    shift_setting_id =3;
    console.log('page=insertempshiftsdata&newempid='+newempid+'&shift_id='+shift_id+'&shift_setting_id='+shift_setting_id);
    $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertempshiftsdata&newempid='+newempid+'&shift_id='+shift_id+'&shift_setting_id='+shift_setting_id,
            success: function(response) {
                console.log(response);
                window.location = "index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=LEA&EMPID="+newempidcode;
            }
        });
  }
</script>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <div class="card card-custom mb-5 ">
      <div class="card-header">
        <h5>Work Attendance</h5>
        <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>
            Back
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row col-lg-12">
           <div class="col-lg-4 mt-3">
            <span> Location : </span>
            </div>
            <div class="col-lg-2 mt-3">
            <span>
              <?php echo $attdetails['location_data']['location_name']; ?>
            </span>
            </div>

          <?php foreach($fields as $field){ ?>
          <div class="col-lg-4 mt-3">
            <span> <?php echo  viewTitle($field); ?> : </span>

          </div>
          <div class="col-lg-2 mt-3">
            <span>
              <?php echo $attendance_data[$field];
                if(strpos($field,"hours")){
                   echo "  Hours";
                }else{
                  echo "  Minutes";
                }

              ?>

            </span>
          </div>
          <?php } ?>
          <div class="col-lg-4 mt-3">
            <span> Working Days : </span>
            </div>
            <div class="col-lg-8 mt-3">
            <span>
              <?php echo implode(",", json_decode($attendance_data['working_days'],1)); ?>
            </span>
            </div>
        </div>
        <?php if($_SESSION['config']['shift_mode']==0){ ?>
          <div>
            <div class="row col-lg-12 mt-3">
              <div class="col-lg-3">
                <span>Shift:</span>
              </div>
              <div class="col-lg-3">
                <select aria-label="Shift" class="js-example-basic-single" name="shift_id" id="shift_id">
                  <option value="0">Select Any One </option>
                  <?php foreach((array)$attdetails['shifts'] as $sht){ ?>
                    <option value="<?php echo $sht['id']; ?>"><?php echo $sht['shift_name']; ?> </option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          <div class="table-responsive">
              <table class="table table-striped table-bordered nowrap dataTable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Shift Setting Name </th>
                    <th scope="col">Minimum </th>
                    <th scope="col">Full Day </th>
                    <th scope="col">Half Day </th>
                    <th scope="col">Working Days </th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach((array)$attdetails['shift_settings'] as $shiftdata){

                   ?>
                  <tr>
                    <td>
                      <label class="radio radio-inline">
                        <input aria-label="Shift Setting Ids" type="radio"  id="shift_setting_ids" name="shift_setting_ids" value='<?php echo $shift['id']; ?>'> <span></span>
                      </label>
                    </td>
                    <td><?php echo $shiftdata['name']; ?></td>
                    <td><?php echo $shiftdata['minimum_hours_required']." Hours"; ?></td>
                    <td><?php echo $shiftdata['full_day_hours']." Hours"; ?></td>
                    <td><?php echo $shiftdata['half_day_hours']." Hours"; ?></td>
                    <td><?php echo implode(",", json_decode($shiftdata['working_days'],1)); ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
              </table>
          </div>
        <?php } ?>
      </div>
      <div class="text-right p-3">
        <button class="btn btn-primary" onclick="saveshiftemployee(<?php echo $shift['id']; ?>)" >
          Save
        </button>
      </div>
    </div>
  </div>
</div>
