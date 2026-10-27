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
      <h5 class="f-30">EEO-1</h5>
      <h6 class="mt-4"><?php echo count((array)$emp_details); ?> Active Employees</h6>
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
        <table class="table table-hover thnormal table-bordered">
          <tr style="text-align: center;" class="align-middle">
            <th scope="col" rowspan="6">EEO Categories</th>
            <th scope="col" colspan="15" class="text-center">
              <span>Number of Employees</span>
              <span class="d-block">(Report employees in only one category)</span>
            </th>
          </tr>
          <tr style="text-align: center;">
            <th scope="col" colspan="15">Race/Ethnicity</th>
          </tr>
          <tr class="center">
            <th scope="col" class="text-center" colspan="2" rowspan="2">Hispanic or Latino</th>
            <th scope="col" class="text-center" colspan="<?php echo count((array)$result['races'])*2; ?>">Not-Hispanic or Latino</th>
            <th scope="col" class="text-center align-middle" rowspan="3">Total Col A-N</th>
          </tr>
          <tr>
            <th scope="col" class="text-center" colspan="<?php echo count((array)$result['races']); ?>">Male</th>
            <th scope="col" class="text-center" colspan="<?php echo count((array)$result['races']); ?>">Female</th>
          </tr>
          <tr style="text-align: center;">
            <th scope="col" class="align-middle">Male</th>
            <th scope="col" class="align-middle">Female</th>
            <?php foreach($fmarray as $fmdd){
                  foreach($result['races'] as $racedata){
            ?>
            <th scope="col" class="align-middle"><?php echo  $racedata['race_name']; ?></th>
            <?php
              }
            } ?>
          </tr>
          <tr style="text-align: center;">
            <?php for($a=0; $a< ((count((array)$result['races'])*2)+3) ; $a++){ ?>
              <th scope="col"><?php echo $alpha[$a]; ?></th>
            <?php } ?>
          </tr>
          <?php foreach($raceemp as $jobkey=>$jobrace){ ?>
          <tr>
            <th scope="col"><?php echo $titles[$jobkey]; ?></th>
            <td style="text-align: center;"><?php echo count((array)$racegenemp[$jobkey]['M']); ?></td>
            <td style="text-align: center;"><?php echo count((array)$racegenemp[$jobkey]['F']); ?></td>
            <?php $total =0; foreach($fmarray as $fmkey=>$fmdd){
                  foreach($result['races'] as $racedata){
            ?>
              <td style="text-align: center;"><?php echo count((array)$raceemp[$jobkey][$fmkey][$racedata['id']]); ?></td>

            <?php  $total = $total+count((array)$raceemp[$jobkey][$fmkey][$racedata['id']]);
              }
            } ?>
            <td class="text-right"><?php echo $total; ?></td>
          </tr>
          <?php } ?>
        </table>
      </div>
    </div>
  </div>
  <!------- Detail Start----------->
   <div class="card">
    <div class="card-header">
      <h5 class="f-30">EEO Details</h5>

    </div>
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">Employee Name</th>

            <th scope="col">Gender</th>
            <th scope="col">Employee Ethnicity</th>
            <th scope="col">Job Title</th>
            <th scope="col">EEO Category</th>
          </tr>
          <?php $k=1; foreach($emp_details as $emdata){
             $cssdata[] = array('Sl No'=> $k,'Employee Name'=> $emdata['display_name'], 'Employee Code'=> $emdata['emp_code'], 'Gender'=> $fmarray[$emdata['gender']],'Employee Ethnicity'=> $race_name[$emdata['race_id']], 'Job Title'=> $job_tittle_name[$emdata['job_title_id']], 'EEO Category'=>$titles[$jobtitlecat[$emdata['job_title_id']]]);
            ?>
          <tr>
            <td><?php echo $k; ?></td>
            <td>
              <div class="d-inline-block align-middle">
                <?php if($emdata['image']!=""){ ?>
                  <img src="uploads/module/hr.webp" alt="user" class="img-radius img-40 align-top m-r-15">
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
            Please Link to Job title, EEO Categories</br>
            Please Link to Employee , Race
        <?php } ?>
      </div>
      </div>
    </div>
  </div>
  <!------- Detail End  ----------->
</div>
