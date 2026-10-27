<?php

$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$_SESSION['apply_js']['report_candidate_resoures']=1;
$postids ="";
$hiring_source_id="";
$todate = date("Y-m-d");
$fromdate =  date("Y-m-d",strtotime("-1 months"));
$view_post = array(); $view_source = array();
if(isset($_POST['applyform'])) {
$fromdate = date('Y-m-d',strtotime(str_replace('/"', '-', $_POST['fromdate'])));
$todate = date('Y-m-d',strtotime(str_replace('/"', '-', $_POST['todate'])));
$view_post = $_POST['job_post'];
$postids = implode(",", (array)$_POST['job_post']);
$view_source = $_POST['hiring_source_id'];
$hiring_source_id  = implode(",", (array)$_POST['hiring_source_id']);
}
 $url = API."?mode=togetcandidatesources&post_id=".$postids."&hiring_source_id=".$hiring_source_id."&fromdate=".$fromdate."&todate=".$todate;
 $apires = callAPI($url);
$result = json_decode($apires,1);
$job_postings = $result['job_postings'];
$jobpost = $result['jobpost'];
$cand_count = count((array)$result['candidates']);
foreach($result['status'] as $casta){
    $statusval[] = $casta['status'];
    $status_name[$casta['status']] = $casta['name'];
}
$lastcdays = date("Y-m-d",strtotime("-7 days"));
$listholdstatus = array(); $listnorstatus = array(); $liststatus = array(); $rejectondata = array();
$rejec_source_reason = array(); $rejec_reason_source = array();
$hirec=0; $lastcancount=0; $source_rej=0;
foreach($result['candidates'] as $candida){
    $candida['job_hiring_source_id'] = $candida['job_hiring_source_id']!="" ? $candida['job_hiring_source_id'] : 999;
    $liststatus[$candida['current_status']][] = $candida;
    $listsourcewise[$candida['current_status']][$candida['job_hiring_source_id']][] = $candida;
    $listsourceonly[$candida['job_hiring_source_id']][] = $candida;
    foreach($statusval as $stval){
      if($stval<$candida['current_status']){
        $listsourcewise[$stval][$candida['job_hiring_source_id']][] = $candida;
        $liststatus[$stval][] = $candida;
      }
    }
    if($candida['current_status']==ST_LOIACCEPTED){
      $hirec = $hirec+1;
      $loiaccepted[$candida['job_hiring_source_id']]=$loiaccepted[$candida['job_hiring_source_id']]+1;
      if(strtotime($lastcdays) < strtotime($candida['ca_updated_date']) ){
        $lastcancount =  $lastcancount+1;
      }
    }
    if($candida['rejection_yes_no']=='0'){
      $source_rej = $source_rej+1;
      $rejec_source_reason[$candida['job_hiring_source_id']][$candida['rejection_reason_id']] = $rejec_source_reason[$candida['job_hiring_source_id']][$candida['rejection_reason_id']]+1;
      $rejec_reason_source[$candida['rejection_reason_id']][$candida['job_hiring_source_id']] = $rejec_reason_source[$candida['rejection_reason_id']][$candida['job_hiring_source_id']]+1;
      if($candida['rejection_reason_id']!=0){
        $reasons[$candida['rejection_reason_id']] = $candida['rejection_reason_id'];
      }
    }
}

$job_hiring_source_other = $result['job_hiring_source'];
$count_hir = count((array)$result['job_hiring_source']);
$job_hiring_source_other['999'] = array('id'=>999,'hiring_source_name'=>'Other','color_code'=> '#3699FF');
$mostraidos = "0";
foreach($loiaccepted as $accsorce=>$acc){
    if($mostraidos < $acc){
      $mostraidos =$acc;
      $mostraido_sourec_id = $accsorce;
    }
}

$loiaccepted[$mostraido_sourec_id] = $loiaccepted[$mostraido_sourec_id]!="" ? $loiaccepted[$mostraido_sourec_id]: 0;
$lastcancount = $lastcancount!=""? $lastcancount :0;
$most_convert =0; $convert=0; $lastconvert=0;
if(count((array)$listsourceonly[$mostraido_sourec_id])!=0){
$most_convert =  round(($loiaccepted[$mostraido_sourec_id]/count((array)$listsourceonly[$mostraido_sourec_id]))*100);
}
if($cand_count!=0 && $cand_count !=""){
$convert = round(($loiaccepted[$mostraido_sourec_id]/$cand_count)*100);
$lastconvert = round(($lastcancount/$cand_count)*100);
}

?>
<script type="text/javascript">
  function selectothereason(disid,reason_id){
    var reason_data = $("#raphval").val();
    $.ajax({
        url: "views/report_candidate_sources_ajax.php",
        type: "POST",
        data: "page=remove_file&disid="+disid+"&reason_id="+reason_id+"&reason_data="+reason_data,
        success: function(response) {
            console.log(response);
            $('#chart_'+disid).html(response);
        }
    });
  }
</script>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="">Candidate Sources</h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate Sources" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Candidate Sources" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
       <form method="post" action="">
      <div class="row align-items-center">
        <div class="col-lg-10">
          <div class="row">
            <div class="col-lg-2">
              <div class="form-group">
                <div class="date">
                   <input aria-label="Fromdate" type="text" class="form-control datepicker"  name="fromdate"  value="<?php echo fromDate($fromdate); ?>" />
                </div>
              </div>
            </div>
            <div class="col-lg-2">
              <div class="form-group">
                <div class="date">
                  <input aria-label="Todate" type="text" class="form-control datepicker"  name="todate"  value="<?php echo fromDate($todate); ?>" />
                </div>
              </div>
            </div>
            <div class="col-lg-3">
               <div class="dropdown border w-100">
                  <button type="button" class="dropbtn w-100 text-start form-control">All Sources</button>
                  <div class="dropdown-content">
                  <?php foreach($result['job_hiring_source'] as $source){ ?>
                    <div class="border-checkbox-section p-2">
                      <div class="border-checkbox-group border-checkbox-group-primary">
                        <input aria-label="Input field"  <?php if(in_array($source['id'],(array)$view_source)){ echo "checked";
} ?> class="border-checkbox" type="checkbox" name="hiring_source_id[]"  value="<?php echo $source['id']; ?>" id="<?php echo $source['hiring_source_name']; ?>">
                        <label class="form-label border-checkbox-label mb-0" for="<?php echo $source['hiring_source_name']; ?>"><?php echo $source['hiring_source_name']; ?></label>
                      </div>
                    </div>

                  <?php } ?>

                  </div>
                </div>
            </div>
            <div class="col-lg-3">
              <nav class="all-employee">
                <ul>
                  <li class="d-block border"><a href="#">All Jobs</a>
                    <ul>
                      <?php foreach($job_postings as $jobtitle){ ?>
                      <li><a href="#"><?php echo $jobtitle['name']; ?> <i class="ti-angle-right float-end f-12 m-t-5"></i> </a>
                        <ul>
                           <?php foreach($jobtitle['data'] as $jobpost){ ?>
                          <li>
                            <div class="border-checkbox-section">
                              <div class="border-checkbox-group border-checkbox-group-primary">
                                <input aria-label="Input field"  <?php if(in_array($jobpost['id'],(array)$view_post)){ echo "checked";
} ?> class="border-checkbox" type="checkbox" name="job_post[]" value="<?php echo $jobpost['id']; ?>" id="<?php echo $jobpost['job_number']; ?>" >
                                <label class="form-label border-checkbox-label" for="<?php echo $jobpost['job_number']; ?>">
                                  <?php echo $jobpost['job_name']." (".$jobpost['job_number'].")"; ?>
                                </label>
                              </div>
                            </div>
                          </li>
                          <?php } ?>
                        </ul>
                      </li>
                      <?php } ?>
                    </ul>
                  </li>
                </ul>
              </nav>
          </div>
            <div class="col-lg-2">
              <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                Apply
              </button>
            <input type="hidden" name="submit" value="applyform">
            </div>
          </div>
          </form>
        </div>
        <div class="col-lg-2">
          <ul class="nav nav-tabs md-tabs">
            <li class="nav-item">
              <a class="nav-link active" data-bs-toggle="tab" href="#graph" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-pie-chart m-r-10"></i>Graph</a>
              <div class="slide"></div>
            </li>
            <li class="nav-item">
              <a class="nav-link" data-bs-toggle="tab" href="#table" role="tab" aria-selected="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-view-list-alt m-r-10 f-w-600 f-14 text-muted"></i>Table</a>
              <div class="slide"></div>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="tab-content card-block">
        <div class="tab-pane active" id="graph" role="tabpanel">
          <div class="row">
            <div class="col-lg-5">
              <h4 class="f-w-600 text-success">Conversion by Source</h4>
              <p class="mb-0 f-12">This shows how effective sources are in converting applicants into hires.</p>
            </div>
            <div class="col-lg-7">
              <div class="row">
                <div class="col-lg-1 text-center">
                  <i  class="fa fa-trophy f-24 text-success border p-2 img-radius"></i>
                </div>
                <div class="col-lg-3">
                  <h4 class="f-w-600 text-success mb-2"><?php echo $most_convert; ?>%<span class="f-w-300 text-muted f-14">&nbsp; <?php echo $job_hiring_source_other[$mostraido_sourec_id]['hiring_source_name']; ?></span></h4>
                  <p class="f-12 mb-0">MOST CONVERTED</p>
                </div>
                <div class="col-lg-1 text-center">
                  <i  class="fa fa-filter f-24 text-success border p-2 img-radius"></i>
                </div>
                <div class="col-lg-3">
                  <h4 class="f-w-600 text-success mb-2"><?php echo $convert; ?>%</h4>
                  <p class="f-12 mb-0">CONVERSION RATE</p>
                </div>
                <div class="col-lg-1 text-center">
                  <i  class="fa fa-calendar f-24 text-success border p-2 img-radius"></i>
                </div>
                <div class="col-lg-3">
                  <h4 class="f-w-600 text-danger mb-2"><?php echo $lastconvert; ?>%</h4>
                  <p class="f-12 mb-0">LAST 7 DAYS</p>
                </div>
              </div>
            </div>
          </div>

          <div class="row mt-5">
            <?php $totlcan = count((array)$result['status']); $j=1; foreach($result['status'] as $castatus){   ?>
        <div class="row mt-3">
          <div class="col-lg-3">
            <div class="bg-gray p-4">
              <h5><?php echo $castatus['action_label']; ?></h5>
              <p class="mb-0"><i class="fa fa-clock-o"></i> Candidates <?php echo count((array)$liststatus[$castatus['status']]); ?></p>
            </div>
            <?php if($totlcan !=$j){?>
            <div class="text-center">
              <div class="dropdown-primary dropdown open">
                <i class="ti-arrow-down d-block mb-1"></i>
              </div>
            </div>
          <?php } ?>
          </div>
          <div class="col-lg-9">
            <div class="bar multi-color">

              <?php foreach($job_hiring_source_other as $source){
                $lscount = count((array)$listsourcewise[$castatus['status']][$source['id']]);
                $ls_p = $cand_count !=0?($lscount/$cand_count)*100:0;
               ?>
              <?php if($lscount>0){ ?>
              <div class="color bg-success" style="width: <?php echo $ls_p; ?>%;background-color:<?php echo $source['color_code']; ?> !important; ">
              </div>
              <?php } ?>
              <?php } ?>
            </div>
          </div>
        </div>
          <?php $j++;
} ?>
        </div>
          <div class="row mt-3 border-bottom pb-3">
            <?php foreach($job_hiring_source_other as $source){ ?>
            <div class="col-lg-1">
              <span class="bg-success" style="padding:0 3px;background-color:<?php echo $source['color_code']; ?> !important;"></span>
              <b class="d-inline-block m-l-5"><?php echo $source['hiring_source_name']; ?></b>
            </div>
            <?php } ?>

          </div>
          <?php
          $rgar = array();
          foreach($result['hold_rejection'] as $hrkey=>$hold_rejection){
            $gaa = array();
            foreach($job_hiring_source_other as $gsource ){
              $gara = array();
              $gara['name'] = $gsource['hiring_source_name'];
              $gara['val'] = $rejec_reason_source[$hrkey][$gsource['id']]!=""?$rejec_reason_source[$hrkey][$gsource['id']]:0;
              $gara['color'] = $gsource['color_code'];
              $gaa[]= implode(':',(array)$gara);
            }
            $rgar[$hrkey] = implode('||',(array)$gaa);
          }
           ?>
          <div class="mt-4">
            <div class="d-inline-block m-r-10 align-top"><i class="fa fa-times-circle text-danger f-24"></i></div>
            <div class="d-inline-block">
              <h4 class="text-danger f-w-600">'Not Hired Because...'</h4>
              <p>This shows reasons why all inactive candidates ended up in your status workflow.</p>
            </div>
          </div>
          <input type="hidden" name="raphval" id="raphval" value="<?php echo urlencode(json_encode($rgar)); ?>">
          <div class="row mt-4">
            <?php $i=1;
            foreach($result['hold_rejection'] as $reas=>$gsource){
              if($i<4){ ?>
            <div class="col-lg-4" >
              <div>
                <select aria-label="Select option" class="form-select js-example-basic-single mb-2"  onchange="selectothereason(<?php echo $i; ?>,this.value)">
                 <?php foreach($result['hold_rejection'] as $gsourkey=>$gsour){ ?>
                  <option <?php if($gsourkey==$reas){ echo "selected";
} ?> value="<?php echo $gsourkey; ?>"><?php echo $gsour; ?></option>
                <?php } ?>
                </select>
              </div>
              <?php
                $checkvalue =0;
                $ckgo = explode("||",$rgar[$reas]);
                  foreach($ckgo as $ckg){
                    $ckn = explode(":",$ckg);
                    if($ckn['1']>0){

                      $checkvalue = $ckn['1'];
                      break;
                    }
                  }
              ?>

              <div id="chart_<?php echo $i; ?>">
                <?php if($checkvalue>0){ ?>

                <input type="hidden" name="" id="dchart<?php echo $i; ?>" value="<?php echo $rgar[$reas]; ?>">
                <div class="card-block">
                    <div id="chart_Donut_hiring_recejtion_<?php echo $i; ?>" style="width: 100%; height: 300px;"></div>
                </div>
                <?php }else{?>
                  <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Candidates</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
                <?php } ?>
             </div>
            </div>
            <?php $i++; ?>
            <?php } ?>
            <?php } ?>


          </div>
        </div>
        <div class="tab-pane" id="table" role="tabpanel">
          <div class="table-responsive">
            <table class="table table-hover">
              <tr>
                <th scope="col">Source</th>
                <?php  foreach($result['status'] as $castatus){ ?>
                  <th scope="col"> <?php echo $castatus['action_label']; ?></th>
                <?php } ?>
                <th scope="col">Conversion Rate</th>

              </tr>
              <?php $csv = array();
              foreach($job_hiring_source_other as $source){
                    $csvdata = array();
                    $csvdata['Source'] = $source['hiring_source_name'];
              ?>
              <tr>
                  <th scope="col"><?php echo $source['hiring_source_name']; ?></th>
                  <?php foreach($result['status'] as $castatus){
                        $val =count((array)$listsourcewise[$castatus['status']][$source['id']]);
                        $csvdata[$castatus['action_label']] = $val;
                        if($castatus['status']==ST_ACTIVE){ $apli = $val;
}
                        if($castatus['status']==ST_LOIACCEPTED){ $acep = $val;
}
                        $sttotal[$castatus['status']] = $sttotal[$castatus['status']]+$val;
                   ?>
                    <td class="text-right"> <?php echo $val; ?></td>
                  <?php }  ?>
                  <td class="text-right">
                    <?php  $aplicc =0;
                      if($apli!=0){
                        $aplicc = round(($acep/$apli)*100);
                      }
                      $csvdata['Conversion Rate'] = $aplicc;
                    echo $aplicc; ?>%
                </td>

                </tr>
              <?php $csv[] =$csvdata;
} ?>

              <tr>
                <th scope="col">Total</th>
                <?php $csvdata = array();  $csvdata['Source'] = 'Total';
                 foreach($result['status'] as $castatus){
                  if($castatus['status']==ST_ACTIVE){ $apli = $sttotal[$castatus['status']];
}
                   if($castatus['status']==ST_LOIACCEPTED){ $acep = $sttotal[$castatus['status']];
}?>
                  <th scope="col" class="text-right"><?php
                    $csvdata[$castatus['action_label']] = $sttotal[$castatus['status']];
                  echo $sttotal[$castatus['status']]; ?></th>
                <?php }  ?>
                <th scope="col" class="text-right">
                  <?php  $aplicc =0;
                      if($apli!=0){
                        $aplicc = round(($acep/$apli)*100);
                      }
                      $csvdata['Conversion Rate'] = $aplicc;
                    echo $aplicc; ?>%

              </tr>
            </table>
          </div>
          <?php $csv[] =$csvdata;
           $_SESSION['download_data'] = $csv; ?>
          <div class="mt-4">
            <div class="d-inline-block m-r-10 align-top"><i class="fa fa-times-circle text-danger f-24"></i></div>
            <div class="d-inline-block">
              <h4 class="text-danger f-w-600">'Not Hired Because...' </h4>
              <p>This shows reasons why all inactive candidates ended up in your status workflow.</p>
            </div>
          </div>
          <?php if($source_rej>0){ ?>
          <div class="table-responsive">
            <table class="table table-hover">

              <tr>
                <th scope="col">Source</th>
                <?php foreach($reasons as $reas){ ?>
                <th scope="col" class="text-right"><?php echo $result['hold_rejection'][$reas]; ?></th>
                <?php } ?>
              </tr>
              <?php foreach($rejec_source_reason as $rkey=>$resource){ ?>
                <tr>
                  <th scope="col"><?php echo $job_hiring_source_other[$rkey]['hiring_source_name']; ?></th>
                  <?php foreach($reasons as $reas){
                    $val = $resource[$reas]!="" ? $resource[$reas] : '0';
                    $total_re[$reas] = $total_re[$reas]+$val;
                   ?>
                  <th scope="col" class="text-right"><?php echo $val; ?></th>
                  <?php } ?>
                </tr>
              <?php } ?>
              <tr>
                <th scope="col">Total</th>
                <?php foreach($reasons as $reas){ ?>
                  <th scope="col" class="text-right"><?php echo $total_re[$reas]; ?></th>
                <?php } ?>
              </tr>
            </table>
          </div>
        <?php }else{ ?>
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
</div>
