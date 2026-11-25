<?php
if (!defined('STR_DAYS')) {
    define('STR_DAYS', ' Days');
}
$empid = $_GET['newempid'];
$url = API."?mode=employeeleaveapplyattendance&empid=".$empid;
$cudata = callAPI($url);
$leavedetails = json_decode($cudata,1);
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['Switch'] = 1;
?>
<script type="text/javascript">
  function saveleaveemployee(){
    var newempidcode = $('#newempidcode').val();
    var newempid = $('#newempid').val();
    var formvalue =  $("#leaveapply").serialize();
    console.log(formvalue);


    $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=leaveapplycreateemployee&emp_id='+newempid+'&'+formvalue,
            success: function(response) {
                console.log(response);
                window.location = "index.php?option=COM_INSERT_NEW_EMPLOYEE_FULL_DETAILS&PR=PAR&EMPID="+newempidcode;
            }
        });
  }
</script>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid">
    <div class="card card-custom mb-5 ">
      <div class="card-header">
        <h5><?= __('Work Attendance'); ?></h5>
        <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
            <i class="icofont icofont-reply"></i>
            <?= __('Back'); ?>
          </a>
        </div>
      </div>
      <div class="card-body">
        <div class="row col-lg-12">
          <form method="post" action="" enctype="multipart" id="leaveapply">
          <div class="table-responsive">
              <table class="table table-striped table-bordered nowrap dataTable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                     <th scope="col"><?= __('Leave Policy Name'); ?> </th>
                    <th scope="col"><?= __('Leave Day'); ?> </th>
                    <th scope="col"><?= __('Accured Action'); ?></th>
                    <th scope="col"><?= __('Joining'); ?> </th>
                    <th scope="col"><?= __('Action Days'); ?> </th>
                    <th scope="col"><?= __('Max Carry Leaves'); ?></th>
                   <
                  </tr>
                </thead>
                <tbody>
                  <?php $l=1; foreach((array)$leavedetails['main'] as $main){
                      $sub = $leavedetails['sub'][$main['id']];
                   ?>
                  <tr>
                    <td><?php echo $l; ?></td>
                    <td>
                      <?php echo $main['leave_type_name']; ?>
                    </td>

                    <td><?php echo $sub['base_on'].STR_DAYS; ?></td>
                    <td><?php echo $sub['accured_action_based']=='Y' ? 'Yearly' : 'Monthly' ; ?></td>
                    <td><?php echo $sub['before_or_after']=='A' ? 'After' : 'Before' ; ?></td>
                    <td><?php echo $sub['action_number'].STR_DAYS; ?></td>
                    <td><?php echo $sub['max_accured_limit'].STR_DAYS; ?></td>

                  </tr>
                  <input type="hidden" name="leave[<?php echo $main['id']; ?>]" value="<?php echo $sub['action_number']; ?>">
                  <input type="hidden" name="leave[<?php echo $main['id']; ?>]" value="<?php echo $sub['action_number']; ?>">
                  <?php $l++;
} ?>
                </tbody>
              </table>
          </div>
        </div>
        </form>
      </div>
      <div class="text-right p-3">
        <button class="btn btn-primary" onclick="saveleaveemployee()" >
          <?= __('Save'); ?>
        </button>
      </div>
    </div>
  </div>
</div>
