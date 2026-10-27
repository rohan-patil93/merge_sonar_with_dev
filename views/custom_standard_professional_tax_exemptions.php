<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
}
$url = API."?mode=togetemployeeprofessionaltaxexemptions&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$states = $result['states'];
$locations = $result['locations'];
$listdata = $result['list'];
$cdaa = array();
foreach($result['emp_data'] as $datemp){
  if($datemp['date_of_birth']!=""){
    $mon = date('n',strtotime($datemp['date_of_birth']));
    $dis_data[$mon][] = $datemp;
  }
  if($datemp['wedding_date']!=""){
    $wmon = date('n',strtotime($datemp['wedding_date']));
    $wdis_data[$wmon][] = $datemp;
  }
  if($datemp['date_of_birth']!="" || $datemp['wedding_date']!=""){
      $cssdata[] = array('Sl No'=> $sl,'Employee Name'=> $emdata['display_name'], 'Employee Code'=> $emdata['emp_code'], 'Date of Birth'=> $emdata['date_of_birth'], 'Wedding Day'=> $emdata['wedding_date']);
  }
}
$_SESSION['download_data'] = array();
?>
<style type="text/css">
.ontable tr td{
  vertical-align: middle;
  padding: 5px;
  font-size: 12px;
}
</style>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Professional Tax Exemptions</h5>


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

      </div>
    </div>
  </div>


  <div class="">
    <div class="">
      <ul class="nav md-tabs infisuite_tab">
        <?php $i=1; foreach($result['professional_tax'] as $professional_tax){ ?>
        <li class="nav-item">
          <a class="nav-link <?php if($i==1){ echo "active";
} ?> " data-bs-toggle="tab" href="#tabb<?php echo $professional_tax['id']; ?>" role="tab" aria-selected="true">
            <?php echo $states[$professional_tax['state_id']]." - ".$locations[$professional_tax['work_location_id']]; ?>
          </a>
        </li>

        <?php $i++;
} ?>
      </ul>
    </div>

    <div class="tab-content tabs mt-2">
      <?php $i=1; foreach($result['professional_tax'] as $professional_tax){ ?>
      <div class="tab-pane <?php if($i==1){ echo "active";
} ?>" id="tabb<?php echo $professional_tax['id']; ?>" role="tabpanel">
        <div class="row col-lg-12" style="width: auto;">
        <?php
        $sl =1 ;
        $location_id = $professional_tax['work_location_id'];
        $exmptiondata =  json_decode($professional_tax['personal_tax_exemptions'],1);
        foreach($exmptiondata as $exmpi){
          ?>
          <div class="col-lg-4">
            <div class="card table-card">
              <div class="card-header">
                <h5><?php echo $exmpi['exemption_name']." (". $exmpi['exemption_code']." )"; ?></h5>
              </div>
              <div class="p-b-10">
              <div class="card-block p-b-0 custom-scroll p-" style="min-height: 350px;max-height: 350px;">
                 if(count((array)$listdata[$location_id][$exmpi['id']])>0){ ?>
                  <div class="table-responsive">
                    <table class="table ontable table-hover m-b-0" style="max-height:300px;">
                      <thead>
                        <th scope="col">Name </th>
                        <th scope="col">Joining Date</th>
                      </thead>
                      <tbody>
                        <?php
                        foreach((array)$listdata[$location_id][$exmpi['id']] as $emdata){
                          ?>
                          <tr>
                            <td>
                              <div class="d-inline-block align-middle">
                                <?php  if($emdata['image']!=""){ ?>
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
                           <td>
                            <?php echo date('dS M Y',strtotime($emdata['joining_date'])); ?>
                          </td>
                        </tr>
                        <?php $sl++;
}
                      </tbody>
                    </table>
                  </div>
                <?php
}else{ ?>

                  <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                    <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                  </div>

                <?php  } ?>
              </div>
              </div>
            </div>
          </div>
        <?php } ?>
        </div>
      </div>
      <?php $i++;
} ?>
    </div>

  </div>
</div>
</div>
