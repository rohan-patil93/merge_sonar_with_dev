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

}else{
   $_POST['fromdate'] = date("Y-m-t");
}
$postdata = $_POST;
$url = API."?mode=togetemployeemployeeheadcount&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$emp_data = $result['emp_data'];
$sl=1;$_SESSION['download_data'] = array();
 $onba = date("Y-m-01", strtotime("-1 year", strtotime($_POST['fromdate'])));
 $oncu = 0;
foreach($emp_data as $emdata){
  $year[date('Y',strtotime($emdata['joining_date']))][] = $emdata;
  $yer_month[date('Y-m',strtotime($emdata['joining_date']))][] = $emdata;
  if(strtotime($emdata['joining_date']) < strtotime($onba)){
      $oncu++;
  }
}
if(count((array)$year) != 0 && $year !=""){
  ksort($year);
}
if(count((array)$yer_month) != 0 && $yer_month !=""){
 ksort($yer_month);
}
$cu =0;
foreach($year as $yearkey=>$yea){
    $cu = $cu+count((array)$year[$yearkey]);
    $countyear[$yearkey] = $cu;
}
$cu =0;
foreach($yer_month as $yearkey=>$yea){
    $cu = $cu+count((array)$yer_month[$yearkey]);
    $countyearmont[$yearkey] = $cu;
}
for ($i=1; $i < 13; $i++) {
    $lstym =  date("Y-m", strtotime("+ ".$i." month", strtotime($onba)));
    $grdata[$lstym] =  $oncu+count((array)$yer_month[$lstym]);
    $oncu = $oncu+count((array)$yer_month[$lstym]);
}
$_SESSION['apply_js']['headcount'] = "1";
$_SESSION['download_data'] = array();

?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Headcount</h5>
       <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Employment Head Count" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Employment Head Count" target="_blank">Excel</a>
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
  <div class="card">
    <div class="card-body">
      <div class="row">
        <div class="col-lg-9 peity-chart">
          <div class="card">

            <div class="card-block">
              <span class="bar-colours-2"><?php echo implode(",", (array)$grdata) ?></span>
            </div>
            <div class=" col-lg-12 row p-l-30  m-b-10">
              <?php foreach($grdata as $ymkey=>$ymm){ ?>
              <div class="col-lg-1"><?php echo date('M-y',strtotime($ymkey."-01")); ?>
                    <span class="f-12 text-muted"> <?php echo $ymm; ?></span>
              </div>
              <?php } ?>
            </div>
          </div>
        </div>

        <div class="col-lg-3">
          <div class="bg-gray">
            <h4 class="mb-3"><?php echo date("M d, Y"); $cy =date('Y')-1; ?></h4>
            <i class="fa fa-users f-30 text-success"></i>
            <span class="f-30 f-w-600"><?php echo $countyear[date('Y')]; ?></span><b class="f-12 text-muted">  &nbsp;&nbsp; +(<?php echo $countyear[date('Y')]- $countyear[$cy]; ?>) YTD</b>
            <h5 class="mt-3 mb-3">Total Employees</h5>
            <?php
            for($i=$cy; $i>$cy-4; $i--) {?>
            <div class="col-md-12 mt-2">
              <h5><?php echo $i; ?></h5>
              <div class="progress">
                <div class="progress-bar progress-bar-warning " role="progressbar" style="width: <?php echo $countyear[$i]; ?>%" aria-valuenow="87" aria-valuemin="0" aria-valuemax="100"><?php echo $countyear[$i]; ?></div>
              </div>
            </div>
            <?php  } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-4">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Hire Date</th>
            <th scope="col">Divison</th>
            <th scope="col">Department</th>
            <th scope="col">Locations</th>
            <th scope="col">Designations</th>
            <th scope="col">Employement</th>
          </tr>
          <?php foreach($emp_data as $emdata){
              $hire_date = $emdata['joining_date']!="" ? fromDate($emdata['joining_date']) : "";
              $cssdata[] = array('Sl No'=> $sl,'Name'=> $emp_data['display_name'], 'Hire Date'=> $hire_date, 'Division'=> $emp_data['division_name'],'Department'=> $emp_data['department_name'],'Location'=> $emp_data['location_name'], 'Designation'=> $emp_data['designation_name'],'Employee Status'=>$emp_data['employment_status_name']);
          ?>
            <tr>
              <td><?php echo $sl;  ?></td>
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
             <td><?php echo $hire_date; ?></td>
             <td><?php echo $emdata['division_name']; ?> </td>
             <td><?php echo $emdata['department_name']; ?></td>
             <td><?php echo $emdata['location_name']; ?></td>
             <td><?php echo $emdata['designation_name']; ?></td>
             <td><?php echo $emdata['employment_status_name']; ?></td>
           </tr>
        <?php $sl++;
} ?>

        </table>
      </div>
    </div>
    <?php if(count((array)$emp_data)==0){ ?>
      <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
      </div>
    <?php } ?>
  </div>
</div>
