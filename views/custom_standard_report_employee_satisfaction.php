<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
}
$url = API."?mode=togetreportemployeesatisfaction&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$emp_data = $result['emp_data'];
foreach($result['survey_response'] as $survey_response){
  $jsnval = json_decode($survey_response['response_option'],1);
  $disqueslist[$survey_response['question_id']][] = $survey_response;
  $quesscore[$survey_response['question_id']] = $quesscore[$survey_response['question_id']]+$jsnval['0'];
}
$scoree =0; $promoters=0; $neutrals =0; $detractors =0;
foreach($quesscore as $keyscore=>$quessco){
    $scoree = ($quessco/(count((array)$disqueslist[$keyscore])));
    if($scoree>=9){
        $uniquesscore['promoters'][$keyscore] = $scoree;
        $ccdata['Promoters'] = $ccdata['Promoters']+$scoree;
    }elseif($scoree<9 && $scoree>=7){
      $uniquesscore['Neutrals'][$keyscore] = $scoree;
      $ccdata['Neutrals'] = $ccdata['Neutrals']+$scoree;
    }else{
      $uniquesscore['detractors'][$keyscore] = $scoree;
      $ccdata['Detractors'] = $ccdata['Detractors']+$scoree;
    }
}
foreach($ccdata as  $gkey=>$genres){
  $cdaa[] = $gkey.":".count((array)$genres);
}
$_SESSION['apply_js']['employee_satisfaction']=1;
$_SESSION['download_data'] = array();
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Employee Satisfaction</h5>
       <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Salary History" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Salary History" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <form method="post">
        <div class="row col-lg-12 ">
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
    <div class="card-body">
      <div class="row">
        <div class="col-lg-5">
          <ul class="nav md-tabs infisuite_tab mt-4">
            <li class="nav-item">
              <a class="nav-link f-16 active" data-bs-toggle="tab" href="#overview" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <i class="fa fa-bar-chart-o m-r-10"></i>Overview
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link f-16" data-bs-toggle="tab" href="#survey" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                <i class="fa fa-users m-r-10"></i>Survey Responses
              </a>
            </li>
          </ul>
        </div>

      </div>
      <div class="tab-content tabs card-block">
        <div class="tab-pane active" id="overview" role="tabpanel">

         <div class="col-lg-12">
          <div class="border mt-4">
            <h3 class="bg-gray p-2 border-bottom f-16 f-w-600">Breakdown By Gender</h3>
            <div class="col-lg-12 row">
            <div class="col-lg-8">
              <div id="chart_Donut_employee_satisfaction" style="width: 100%; height: 400px;"></div>
              <input type="hidden" name="chart_data_employee_satisfaction" id="chart_data_employee_satisfaction" value="<?php echo implode("||",(array)$cdaa); ?>" >
            </div>

            <div class="col-lg-4">
                <div class=" table-card">

                  <div class="card-block p-b-0">
                    <div class="table-responsive">
                      <table class="table ontable table-hover m-b-0">
                        <thead>
                          <th scope="col"> Gender Group</th>
                          <th scope="col">Precentage </th>
                          <th scope="col">Count</th>
                        </thead>
                        <tbody>
                          <?php
                          $chart_count = count((array)$cdaa);
                          foreach($cdaa as $chart_data){
                            $chart_val = explode(":",$chart_data);
                            $per =0;
                            if($chart_count !=0){
                              $per = round(($chart_val['1']/$chart_count)*100);
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
        <div class="tab-pane" id="survey" role="tabpanel">
          <div class="table-responsive mt-3">
            <table class="table table-hover">
              <tr style="background-color: #cccccc;">
                <th scope="col">Score</th>
                <th scope="col">Text Response</th>
                <th scope="col">  Tags - Likes</th>
                <th scope="col">  Tags - Dislikes</th>
              </tr>
              <?php foreach($uniquesscore as $typekey=>$quescore){ ?>
              <tr style="background-color: #eeeeee;">
                <th scope="col" class="text-center"> <i class="ti-pie-chart"></i></th>
                <th scope="col"><?php echo $typekey;  ?></th>
                <th scope="col"></th>
                <th scope="col"></th>
              </tr>
              <?php foreach($quescore as $quekey=>$quesco){ ?>
                <tr>
                  <td class="text-center"><?php echo $quesco; ?></td>
                  <td style="white-space: break-spaces;"><?php echo $result['survey_question'][$quekey]; ?></td>
                  <td></td>
                  <td></td>
                </tr>
              <?php } ?>
            <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="text-center mt-5 mb-5">
        <img src="../files/assets/images/payroll-change.jpg" class="img-100 mb-5" alt="">
        <p>Before using the Payroll Change Report for the first time, please confirm<br />that employee payroll information in BambooHR matches<br />the information in your payroll system.</p>
        <button class="btn waves-effect waves-light btn-grd-primary mt-3">Start Tracking Changes</button>
      </div>
    </div>
  </div>
</div>
