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
}else{
  $fromdate = date("Y-m-01");
  $_POST['fromdate'] = $fromdate;
  $todate = date("Y-m-t");
  $_POST['todate'] = $todate;
}
$url = API."?mode=togetstandaredapprovedhours&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
foreach($result['job_titles'] as $jobtitle){
  $jobtitlecat[$jobtitle['id']] = $jobtitle['eeo_category_id'];
  $job_tittle_name[$jobtitle['id']] = $jobtitle['job_title'];
}
foreach($result['eeo_categories'] as $jobtit){
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
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Approved Hours</h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=EEO-1 Report" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=EEO-1 Report" target="_blank">Excel</a>
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
            <input aria-label="Fromdate" class="form-control datepicker" name="fromdate" type="text" <?php if($fromdate!=""){ ?>  value="<?php echo $fromdate; ?>" <?php
} ?> placeholder="From Date" >
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <input aria-label="Input field" class="form-control datepicker" <?php if($todate!=""){ ?>  value="<?php echo $todate; ?>" <?php
} ?> name="todate" type="text" placeholder="To Date">
          </div>
        </div>
        <?php include_once 'views/custom_standard_report_hr_employee_filter.php'; ?>
        <div class="col-lg-2 mt-1">
          <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
            Apply
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
        <table class="table tabe-hover table-sm">
          <tr style="background-color: #eeeeee;">
            <th scope=\"col\">Name</th>
            <th scope=\"col\">Date</th>
            <th scope=\"col\">Reg Rate</th>
            <th scope=\"col\">Reg Hours</th>
            <th scope=\"col\">OT Rate</th>
            <th scope=\"col\">OT Hours</th>
            <th scope=\"col\">Total</th>
          </tr>
          <tr>
            <td colspan="7"><a class="text-primary" href="">Barnet, Cheryl</a></td>
          </tr>
          <tr>
            <td></td>
            <td>04/15/2023</td>
            <td></td>
            <td>11.0000</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td>04/15/2023</td>
            <td></td>
            <td>11.0000</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td colspan="7"><a class="text-primary" href="">Barnet, Cheryl</a></td>
          </tr>
          <tr>
            <td></td>
            <td>04/15/2023</td>
            <td></td>
            <td>11.0000</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td>04/15/2023</td>
            <td></td>
            <td>11.0000</td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th scope=\"col\">Total29.0000 hours</th>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
