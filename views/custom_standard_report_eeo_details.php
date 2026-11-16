<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $fromdate = $_POST['fromdate']!="" ? $_POST['fromdate'] : "";
  $todate = $_POST['todate']!="" ? $_POST['todate'] : "";
  if($_POST['fromdate']!=""){
    $_POST['fromdate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['fromdate'])));
  }
  if($_POST['todate']!=""){
     $_POST['todate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['todate'])));
  }


  $postdata = $_POST;
}
$url = API."?mode=togetemployeeeodetails&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
foreach((array)$result['job_titles'] as $jobtitle){
  $jobtitlecat[$jobtitle['id']] = $jobtitle['eeo_category_id'];
  $job_tittle_name[$jobtitle['id']] = $jobtitle['job_title'];
}
foreach((array)$result['eeo_categories'] as $jobtit){
  $titles[$jobtit['id']] = $jobtit['eeo_category_name'];
}
foreach($result['races'] as $jobrace){
  $race_name[$jobrace['id']] = $jobrace['race_name'];
}

foreach($result['emp_data'] as $emp_data){
  if($emp_data['race_id']!="" && $emp_data['gender']!=""){
    $raceemp[$jobtitlecat[$emp_data['job_title_id']]][$emp_data['gender']][$emp_data['race_id']][] = $emp_data;
    $racegenemp[$jobtitlecat[$emp_data['job_title_id']]][$emp_data['gender']][] = $emp_data;
    $emp_details[] = $emp_data;
  }
}
$fmarray = array('M'=>'Male','F'=>'Female');
$alpha =  array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

?>
<style type="text/css">
 .thnormal th {
    white-space: normal;
  }
</style>
<div class="page-body">
   <div class="card">
    <div class="card-header">
      <h5 class="f-30"><?= __('EEO Details'); ?></h5>
      <h6 class="mt-4"><?php echo count((array)$emp_details); ?> <?= __('Active Employees'); ?></h6>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?= __('More'); ?></button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=EEO-1 Report" target="_blank" ><?= __('CSV'); ?></a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=EEO-1 Report" target="_blank"><?= __('Excel'); ?></a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <form method="post" action="">
          <div class="row col-lg-12 ">
        <div class="col-lg-2">
          <div class="form-group">
            <input aria-label="<?= htmlspecialchars(__('Fromdate'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker" name="fromdate" type="text" <?php if($fromdate!=""){ ?>  value="<?php echo $fromdate; ?>" <?php
} ?> placeholder="<?= htmlspecialchars(__('From Date'), ENT_QUOTES, 'UTF-8'); ?>" >
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <input aria-label="<?= htmlspecialchars(__('Input field'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control datepicker" <?php if($todate!=""){ ?>  value="<?php echo $todate; ?>" <?php
} ?> name="todate" type="text" placeholder="<?= htmlspecialchars(__('To Date'), ENT_QUOTES, 'UTF-8'); ?>">
          </div>
        </div>
        <?php include_once 'views/custom_standard_report_hr_employee_filter.php'; ?>
        <div class="col-lg-2 mt-1">
          <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
            <?= __('Apply'); ?>
          </button>
          <input type="hidden" name="submit" value="applyform">
        </div>

      </div>
        </form>
      </div>
    </div>
  </div>
  <div class="card">
   <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col"><?= __('Employee Name'); ?></th>

            <th scope="col"><?= __('Gender'); ?></th>
            <th scope="col"><?= __('Employee Ethnicity'); ?></th>
            <th scope="col"><?= __('Job Title'); ?></th>
            <th scope="col"><?= __('EEO Category'); ?></th>
          </tr>
          <?php $k=1; foreach($emp_details as $emdata){
             $cssdata[] = array('Sl No'=> $k,'Employee Name'=> $emdata['display_name'], 'Employee Code'=> $emdata['emp_code'], 'Gender'=> $fmarray[$emdata['gender']],'Employee Ethnicity'=> $race_name[$emdata['race_id']], 'Job Title'=> $job_tittle_name[$emdata['job_title_id']], 'EEO Category'=>$titles[$jobtitlecat[$emdata['job_title_id']]]);
            ?>
          <tr>
            <td><?php echo $k; ?></td>
            <td>
              <div class="d-inline-block align-middle">
                <?php if($emdata['image']!=""){ ?>
                  <img src="uploads/module/hr.webp" alt="<?= htmlspecialchars(__('user'), ENT_QUOTES, 'UTF-8'); ?>" class="img-radius img-40 align-top m-r-15">
                <?php }else{ ?>
                  <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($emdata['display_name']); ?></span>
                <?php } ?>
                <div class="d-inline-block">
                 <?php echo $emdata['display_name']; ?>
                 <p class="text-muted m-b-0 f-12"><?php echo $emdata['emp_code']; ?></p>
               </div>
             </div>
            </td>
            <td><?php echo $fmarray[$emdata['gender']]; ?></td>
            <td><?php echo $race_name[$emdata['race_id']]; ?></td>
            <td><?php echo $job_tittle_name[$emdata['job_title_id']]; ?></td>
            <td><?php echo $titles[$jobtitlecat[$emdata['job_title_id']]]; ?></td>
          </tr>
          <?php $k++;
} $_SESSION['download_data'] = $cssdata; ?>

        </table>
        <div class="f-20 text-danger text-center">
        <?php if(count((array)$emp_details)==0){ ?>
            <?= __('Please Link to Job title, EEO Categories'); ?></br>
            <?= __('Please Link to Employee , Race'); ?>
        <?php } ?>
      </div>
      </div>
    </div>
  </div>
</div>
