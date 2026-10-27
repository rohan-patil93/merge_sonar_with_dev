<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
}
$url = API."?mode=togetreportemployeewelbeing&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);

$menus = $result['menu_data'];
$emp_data = $result['emp_data'];
foreach($result['survey_response'] as $survey_response){
  $jsnval = json_decode($survey_response['response_option'],1);
  $disqueslist[$survey_response['question_id']][] = $survey_response;
  $quesscore[$survey_response['question_id']] = $quesscore[$survey_response['question_id']]+$jsnval['0'];
}
$questopic= array('29'=>'Happiness', '30'=>'Personal Motivators', '31'=>'Company Motivators', '32'=>'Relationships');
$keysurvey = array('Strongly Agree','Agree', 'Somewhat Agree', 'Disagree' , 'Strongly Disagree');
$age_range_arr = array('21-25','26-30','31-35','36-40','41-45','46-50','51-55','56-60');
$join_range_arr = array('0-1','2-3','4-5','6-10','11-15','16-25','26-90');
$colors = array('FFFFFF','095532' , '268c5d', '4c9171', '0c6f41','095532');

foreach($result['employees'] as $lemploye){
  $list_emp['department'][$lemploye['department_id']] = $list_emp['department'][$lemploye['department_id']]+1;
  $list_emp['division'][$lemploye['division_id']] = $list_emp['division'][$lemploye['division_id']]+1;
  $list_emp['locations'][$lemploye['location_id']] = $list_emp['locations'][$lemploye['location_id']]+1;
  foreach($age_range_arr as $age_arr){
      $listage = explode("-",$age_arr);
      if($listage['0'] <=$lemploye['age'] && $listage['1']>=$lemploye['age'] ){
        $list_emp['age'][$age_arr] = $list_emp['age'][$age_arr]+1;
      }
    }
    foreach($join_range_arr as $join_arr){
      $listjoin = explode("-",$join_arr);
      if($listjoin['0'] <=$lemploye['joinyears'] && $listjoin['1']>=$lemploye['joinyears'] ){
        $list_emp['joindate'][$join_arr] = $list_emp['joindate'][$join_arr]+1;
      }
    }
}
foreach($result['survey_response'] as $res){
    $ans = json_decode($res['response_option'],1);
    $out = $ans['0'];
    $listoutques[$res['question_id']][$out] = $listoutques[$res['question_id']][$out]+1;
    $listconutques[$res['question_id']][] = $res;
    $sumlistconutques[$res['question_id']] = $sumlistconutques[$res['question_id']]+$out;
    $department[$res['department_id']][$res['question_id']][] = $res;
    $departmentsum[$res['department_id']][$res['question_id']] = $departmentsum[$res['department_id']][$res['question_id']]+$out;
    $division[$res['division_id']][$res['question_id']][] = $res;
    $divisionsum[$res['division_id']][$res['question_id']] = $divisionsum[$res['division_id']][$res['question_id']]+$out;
    $location[$res['location_id']][$res['question_id']][] = $res;
    $locationsum[$res['location_id']][$res['question_id']] = $locationsum[$res['location_id']][$res['question_id']]+$out;
    foreach($age_range_arr as $age_arr){
      $listage = explode("-",$age_arr);
      if($listage['0'] <=$res['age'] && $listage['1']>=$res['age'] ){
        $agelist[$age_arr][$res['question_id']][] = $res;
        $sumlistage[$age_arr][$res['question_id']] = $sumlistage[$age_arr][$res['question_id']]+$out;
      }
    }
    foreach($join_range_arr as $join_arr){
      $listjoin = explode("-",$join_arr);
      if($listjoin['0'] <=$res['joinyears'] && $listjoin['1']>=$res['joinyears'] ){
        $joinlist[$join_arr][$res['question_id']][] = $res;
        $sumlistjoin[$join_arr][$res['question_id']] = $sumlistjoin[$join_arr][$res['question_id']]+$out;
      }
    }
    $resemp[$res['id']][] = $res;
}
foreach($agelist as $dkey=>$dep){
  foreach($dep as $quekey=>$que){
     $val = $sumlistage[$dkey][$quekey];
    if(count((array)$agelist[$dkey][$quekey]) != 0 && (count((array)$agelist[$dkey][$quekey]) !="")){
      $val = $sumlistage[$dkey][$quekey]/(count((array)$agelist[$dkey][$quekey]));
    }
    $roundval = round(6-$val,1);
    $genval[$roundval][$dkey][] = array('segment'=>'Age :'.$dkey,'topic'=>$quekey);
    $gage[$dkey][$quekey] = $val;
  }
}
foreach($joinlist as $dkey=>$dep){
  foreach($dep as $quekey=>$que){
    $val = $sumlistjoin[$dkey][$quekey];
    if(count((array)$joinlist[$dkey][$quekey]) != 0 && (count((array)$joinlist[$dkey][$quekey]) !="")){
    $val = $sumlistjoin[$dkey][$quekey]/(count((array)$joinlist[$dkey][$quekey]));
  }
    $roundval = round(6-$val,1);
    $genval[$roundval][$dkey][] = array('segment'=>'Length of service: :'.$dkey,'topic'=>$quekey);
    $gjoin[$dkey][$quekey] = $val;
  }
}
foreach($department as $dkey=>$dep){
  foreach($dep as $quekey=>$que){
     $val = $departmentsum[$dkey][$quekey];
    if(count((array)$department[$dkey][$quekey]) != 0 && (count((array)$department[$dkey][$quekey]) !="")){
    $val =  $departmentsum[$dkey][$quekey]/(count((array)$department[$dkey][$quekey]));
  }
     $roundval = round(6-$val,1);
    $genval[$roundval][$menus['departments']['data'][$dkey]][] = array('segment'=>$menus['departments']['data'][$dkey],'topic'=>$quekey);
    $gdepartmet[$dkey][$quekey] = $val;
  }
}
foreach($division as $dkey=>$dep){
  foreach($dep as $quekey=>$que){
    $val = $divisionsum[$dkey][$quekey];
    if(count((array)$division[$dkey][$quekey]) != 0 && (count((array)$division[$dkey][$quekey]) !="")){
      $val = $divisionsum[$dkey][$quekey]/(count((array)$division[$dkey][$quekey]));
    }
     $roundval = round(6-$val,1);
    $genval[$roundval][$menus['departments']['data'][$dkey]][] = array('segment'=>$menus['departments']['data'][$dkey],'topic'=>$quekey);
    $gdivison[$dkey][$quekey] =  $val;
  }
}
foreach($location as $dkey=>$dep){
  foreach($dep as $quekey=>$que){
    $val = $locationsum[$dkey][$quekey];
    if(count((array)$location[$dkey][$quekey]) != 0 && (count((array)$location[$dkey][$quekey]) !="")){
      $val = $locationsum[$dkey][$quekey]/(count((array)$location[$dkey][$quekey]));
    }
     $roundval = round(6-$val,1);
    $genval[$roundval][$menus['departments']['data'][$dkey]][] = array('segment'=>$menus['departments']['data'][$dkey],'topic'=>$quekey);
    $glocation[$dkey][$quekey] =  $val;
  }
}


$count_emp = count((array)$result['employees']);
$count_emp_res = count((array)$resemp);

     $_SESSION['apply_js']['standart_report_wellbeing']=1;

?>
<script type="text/javascript">
  function displatraito(id){
    var val  = $("#disqueid_"+id).val();
    if(val == 0){
      $("#raioqus_"+id).show();
      $("#disqueid_"+id).val('1');
    }else{
      $("#raioqus_"+id).hide();
      $("#disqueid_"+id).val('0');
    }

  }
</script>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Employee Wellbeing</h5>
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
      <div class="row col-lg-12 ">
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

      <div class="row mt-5">
        <div class="col-lg-8">
          <div class="card-block">
              <div class="ct-chart1 ct-perfect-fourth"></div>
          </div>

        </div>
        <div class="col-lg-4">
          <h4 class="bg-gray"><i class="fa fa-pencil m-r-10"></i><?php echo date('M Y'); ?></h4>
          <div class="p-2">
            <span class="f-12">Response Rate: <?php  $ppr = ($count_emp !="" && $count_emp !=0)?round(($count_emp_res/$count_emp)*100):0; echo $ppr; ?>%</span>
            <span class="float-end f-12"><?php echo $count_emp_res; ?>/<?php echo ($count_emp !="" && $count_emp !=0)?$count_emp:0; ?> Employees</span>
            <div class="progress">
              <div class="progress-bar mb-5" role="progressbar" style="width: <?php echo $ppr; ?>%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <?php $k=0;
            $lcolor = array('d70206','f05b4f','f4c63d','d17905'); $mn=5;
            foreach($questopic as $quekey=>$quec){ ?>
            <div class="align-middle m-b-25 mt-4 row" onclick="displatraito(<?php echo $quekey; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
              <div class="col-lg-3 text-center m-t-10"><span class="img-radius p-2" style="background-color: <?php echo "#".$lcolor[$k]; ?>;" ><?php echo $mn = $mn-0.3; ?></span></div>
              <div class="col-lg-9 p-0">
                <div class="d-inline-block align-middle">
                  <a href="#!"><h6 class="text-success f-w-600 f-14"><?php echo $quec; ?></h6></a>
                  <p class="text-muted m-b-0"><?php echo $result['survey_question'][$quekey]['survey_question']; ?></p>
                </div>
              </div>
            </div>
            <input type="hidden" name="" id="disqueid_<?php echo $quekey; ?>" value="0">
            <div class="" style="display: none;" id="raioqus_<?php echo $quekey; ?>">
              <?php foreach($keysurvey as $surveykey){ ?>
              <div class="col-lg-12 row">
                  <div class="col-lg-4 f-12">
                      <?php echo $surveykey; ?>
                  </div>
                  <div class="progress col-lg-8">
                  <div class="progress-bar mb-5" role="progressbar" style="width: 55%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
            <?php } ?>
            </div>

            <?php $k++;
} ?>

          </div>
        </div>
      </div>
      <div class="row mt-5">
        <div class="col-lg-6">
          <h3>Highest Average Scores</h3>
          <div class="table-responsive custom-scroll" style="height: 310px;">
            <table class="table table-hover table-sm">
              <tr style="background-color: #eeeeee;">
                <th scope="col">Score</th>
                <th scope="col">Company Segment</th>
                <th scope="col">Survey Topic</th>
                <th scope="col">Response Rate</th>
              </tr>
              <?php if(count((array)$genval) != 0){ ksort($genval);
}
              $hgenval = array_reverse((array)$genval, true);
               foreach($hgenval as $scor=>$genall){
                foreach($genall as $hig){
                $higs = $hig['0'];?>
              <tr>
                <td><?php echo $scor; ?></td>
                <td><?php echo $higs['segment']; ?></td>
                <td><?php echo $questopic[$higs['topic']]; ?></td>
                <td><?php echo  count((array)$hig)."/".count((array)$hig); ?> &nbsp 100%</td>
              </tr>
              <?php } ?>
              <?php } ?>
            </table>
          </div>
        </div>
        <div class="col-lg-6">
          <h3>Lowest Average Scores</h3>
          <div class="table-responsive custom-scroll" style="height: 310px;">
            <table class="table table-hover table-sm">
              <tr style="background-color: #eeeeee;">
                <th scope="col">Score</th>
                <th scope="col">Company Segment</th>
                <th scope="col">Survey Topic</th>
                <th scope="col">Response Rate</th>
              </tr>
               </tr>
              <?php
               foreach($genval as $scor=>$genall){
                foreach($genall as $hig){
                $higs = $hig['0'];?>
              <tr>
                <td><?php echo $scor; ?></td>
                <td><?php echo $higs['segment']; ?></td>
                <td><?php echo $questopic[$higs['topic']]; ?></td>
                <td><?php echo  count((array)$hig)."/".count((array)$hig); ?> &nbsp 100%</td>
              </tr>
              <?php } ?>
              <?php } ?>
            </table>
          </div>
        </div>
        <div class="col-lg-6 mt-5">
          <h3>Scores by Company Segments</h3>
          <p>Quickly compare average scores across demographics.</p>
        </div>

        <div class="col-lg-12">
          <div class="table-responsive">
            <table class="table table-hover table-sm table-bordered">
              <tr style="background-color: #eeeeee;">
                <th scope="col">Segment</th>
                <th scope="col">Response Rate</th>
                <th scope="col">Happiness</th>
                <th scope="col">Personal Motivators</th>
                <th scope="col">Company Motivators</th>
                <th scope="col">Relationships</th>
              </tr>
              <tr>
                <td style="background-color: #f5f6f7;" colspan="6"><i class="fa fa-birthday-cake text-muted"></i> Age</td>
              </tr>
              <?php foreach($gage as $keygage=>$sgage){ ?>
              <tr>
                <td><?php echo $keygage; ?></td>
                <td>3/3 100%</td>
                <?php foreach($questopic as $keyid=>$quec){  ?>
                <td class="bg-success" style="background-color: <?php echo; text-align: center;"#".$colors[round((6-$sgage[$keyid]))]." !important"; ?> "><?php echo round((6-$sgage[$keyid]) ,1); ?></td>
                <?php } ?>

              </tr>
              <?php } ?>
              <tr>
                <td style="background-color: #f5f6f7;" colspan="6"><i class="fa fa-birthday-cake text-muted"></i> Length of Service</td>
              </tr>
               <?php foreach($gjoin as $keygage=>$sgage){ ?>
              <tr>
                <td><?php echo $keygage; ?></td>
                <td>3/3 100%</td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgage['29']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgage['30']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgage['31']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgage['32']) ,1); ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td style="background-color: #f5f6f7;" colspan="6"><i class="fa fa-birthday-cake text-muted"></i> Department</td>
              </tr>
              <?php foreach($gdepartmet as $keydepart=>$sgdepartmet){ ?>
              <tr>
                <td><?php echo $menus['departments']['data'][$keydepart]; ?></td>
                <td>8/8 100%</td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['29']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['30']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['31']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['32']) ,1); ?></td>
              </tr>
              <?php } ?>
              <tr>
                <td style="background-color: #f5f6f7;" colspan="6"><i class="fa fa-birthday-cake text-muted"></i> Division</td>
              </tr>
              <?php foreach($gdivison as $keydepart=>$sgdepartmet){ ?>
              <tr>
                <td><?php echo $menus['divisions']['data'][$keydepart]; ?></td>
                <td>8/8 100%</td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['29']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['30']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['31']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['32']) ,1); ?></td>
              </tr>
              <?php } ?>

              <tr>
                <td style="background-color: #f5f6f7;" colspan="6"><i class="fa fa-birthday-cake text-muted"></i> Location</td>
              </tr>
            <?php foreach($glocation as $keydepart=>$sgdepartmet){ ?>
              <tr>
                <td><?php echo $menus['locations']['data'][$keydepart]; ?></td>
                <td>8/8 100%</td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['29']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['30']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['31']) ,1); ?></td>
                <td style="text-align: center;" class="bg-success"><?php echo round((6-$sgdepartmet['32']) ,1); ?></td>
              </tr>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane" id="survey" role="tabpanel">
      <div class="row">
        <div class="col-lg-3">
          <ul class="nav nav-tabs md-tabs b-none bg-gray w-100 d-block">
            <?php $i=1; foreach($result['survey_question'] as $survey_question){ ?>
              <li class="nav-item text-start">
               <a class="nav-link pt-3 pb-3 <?php if($i==1){ echo "active";
} ?> border-bottom" data-bs-toggle="tab" href="#happ<?php echo $survey_question['id']; ?>" role="tab">
                <?php echo $survey_question['question_title']; ?></a>
                <div class="slide"></div>
              </li>
            <?php $i++;
} ?>
            </ul>
          </div>
          <div class="col-lg-9">
            <div class="tab-content tabs-left-content card-block">
              <?php $i=1; foreach($result['survey_question'] as $survey_question){ ?>
              <div class="tab-pane <?php if($i==1){ echo "active";
} ?>" id="happ<?php echo $survey_question['id']; ?>" role="tabpanel">
                <div class="row">

                  <div class="col-lg-12">
                    <h4 class="f-w-600"><?php echo $survey_question['question_title']; ?></h4>
                    <p>Joy, satisfaction, contentment, and fulfillment pertaining to work. <a href="">Learn more</a></p>
                  </div>
                </div>
                <div class="table-responsive mt-4">
                  <table class="table table-hover table-sm">
                    <tr style="background-color: #eeeeee;">
                      <th scope="col" colspan="2">
                        <h4 class="f-w-600"><?php echo $survey_question['survey_question']; ?></h4>
                        <p><?php echo date("Y M") ?> - 4 responses</p>
                      </th>
                    </tr>
                    <?php $keyno = 0;  foreach($survey_question['options'] as $option){ ?>
                    <tr>
                      <td><?php echo $keysurvey[$keyno]; ?></td>
                      <td><?php echo $option; ?></td>
                    </tr>
                    <?php $keyno++;
} ?>
                  </table>
                </div>
              </div>
              <?php  $i++;
} ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
