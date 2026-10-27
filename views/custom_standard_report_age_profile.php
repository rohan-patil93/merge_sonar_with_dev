<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
}
$url = API."?mode=togetemployeeageprofile&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$cdaa = array();
$age_range_arr = array('21-25','26-30','31-35','36-40','41-45','46-50','51-55','56-60');
$colors = array('#448aff', '#11c15b', '#ffe100', '#FE8A7D','#44ffff', '#11cffb', '#ffcc00', '#FE007D');
$check_value = 1;

foreach($result['age_range'] as $agerr){
  $data_age_ranage[$agerr['age_range']] = $agerr['employee_count'];
  if($agerr['employee_count'] > 0){
    $check_value = 0;
  }
}
$m=0;
foreach($age_range_arr as $agerange){
  $age_range_color[$agerange] = $colors[$m];
  $cdaa[] = $agerange.":".($data_age_ranage[$agerange] !="" ? $data_age_ranage[$agerange] : 0);
  $chart_count = $chart_count+$data_age_ranage[$agerange];
$m++;
}
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['standart_report_hrr']=1;

?>
<style type="text/css">
  .ontable tr td{
    vertical-align: middle;
    padding: 5px;
  }
</style>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Age Profile</h5>
      <h6 class="mt-4"><?php echo count((array)$result['emp_data']); ?> People</h6>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Age Profile" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <form method="post" action="">
      <div class="row col-lg-12 ">
        <?php include_once 'views/custom_standard_report_hr_employee_filter.php'; ?>
        <div class="col-lg-2 mt-1">
            <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                Apply
              </button>
            <input type="hidden" name="submit" value="applyform">
          </div>
        </form>
        <div class="col-lg-12">
          <div class="border mt-4">
            <h3 class="bg-gray p-2 border-bottom f-16 f-w-600">Breakdown By Age</h3>
            <div class="col-lg-12 row">
            <div class="col-lg-8">
              <div id="chart_Donut_age" style="width: 100%; height: 400px;"></div>
              <?php if($check_value == 0){ ?>
              <input type="hidden" name="chart_data_age" id="chart_data_age" value="<?php echo implode("||",(array)$cdaa); ?>" >
               <?php }else{ ?>
                    <input type="hidden" name="chart_data_age" id="chart_data_age_2" value="<?php echo "Age Not Defined:100||26-30:0||31-35:0||36-40:0||41-45:0||46-50:0||51-55:0||56-60:0"; ?>" >
              <?php } ?>

            </div>

            <div class="col-lg-4">
                <div class=" table-card">

                  <div class="card-block p-b-0">
                    <div class="table-responsive">
                      <table class="table ontable table-hover m-b-0">
                        <thead>
                          <th scope="col"> Age Group</th>
                          <th scope="col">Precentage </th>
                          <th scope="col">Count</th>
                        </thead>
                        <tbody>
                          <?php foreach($cdaa as $chart_data){
                            $chart_val = explode(":",$chart_data);
                            $per =0;
                            if($chart_count !=0 && $chart_count !=""){
                              $per = round(($chart_val['1']/$chart_count)*100,1);
                            }
                            if($chart_val['1']>0){
                          ?>
                          <tr>
                            <td>
                              <h6><?php echo $chart_val['0']; ?></h6>
                            </td>
                            <td>
                              <p><?php echo $per; ?>%</p>
                            </td>
                            <td class="text-right">
                              <span class="label label-warning" style="background-color:<?php echo $age_range_color[$chart_val['0']]; ?>;width: 35px;text-align: center;"><?php echo $chart_val['1']; ?></span>
                            </td>
                          </tr>
                          <?php } ?>
                          <?php } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">Name</th>
            <th scope="col">Age</th>
            <th scope="col">Division</th>
            <th scope="col">Department</th>
            <th scope="col">Location</th>
            <th scope="col">Designation</th>
            <th scope="col">Employee Status</th>
          </tr>
          <?php $sl=1; if(count((array)$result['emp_data'])>0){
           foreach($result['emp_data'] as $emp_data ){
            $cssdata[] = array('Sl No'=> $sl,'Name'=> $emp_data['display_name'], 'Age'=> $emp_data['age'], 'Division'=> $emp_data['division_name'],'Department'=> $emp_data['department_name'],'Location'=> $emp_data['location_name'], 'Designation'=> $emp_data['designation_name'],'Employee Status'=>$emp_data['employment_status_name']);
          ?>
          <tr>
            <td><?php echo $sl; ?></td>
            <td><span class="" href=""><?php echo $emp_data['display_name']; ?></span></td>
            <td><?php echo $emp_data['age']; ?></td>
            <td><?php echo $emp_data['division_name']; ?></td>
            <td><?php echo $emp_data['department_name']; ?></td>
            <td><?php echo $emp_data['location_name']; ?></td>
            <td><?php echo $emp_data['designation_name']; ?></td>
            <td><?php echo $emp_data['employment_status_name']; ?></td>
          </tr>
          <?php $sl++;
}
            $_SESSION['download_data'] = $cssdata;
            ?>
            </table>
            <?php
          }else{ ?>
          <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Candidates</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
        <?php } ?>

      </div>
    </div>
  </div>
</div>
