<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$appraisal_id  = $_POST['appraisal_id'];
$loc_id  = $_POST['loc_id'];
$dep_id  = $_POST['dep_id'];
$des_id  = $_POST['des_id'];
$url = API."?mode=getorganizationgoals&loc_id=".$loc_id."&dep_id=".$dep_id."&des_id=".$des_id."&appraisal_id=".$appraisal_id."&popup=Y";
$orggoarcall = callAPI($url);
$result = json_decode($orggoarcall,1);
$organization_goals = $result['organization_goals'];
$ogarray = array();
foreach($organization_goals as $ogg){
  $ogarray[$ogg['key_reslut_id']][] = $ogg;
}
$kralist = $result['appraisal_key_result_areas'];
$kpilist = $result['appraisal_key_performance_indicators'];
?>
<div class="modal-header ">
  <h5 class="modal-title"> View Target </h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
  <div class="modal-body" >
    <div class="">
      <div class="row">

<?php foreach($ogarray as $krais=>$ogoalval){ ?>
         <div class="col-xl-6 col-md-6">
              <div class="card social-res-card">
                  <div class="card-header">
                      <h5><?php echo $kralist[$krais]['area_name']; ?></h5>
                  </div>
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col-lg-4 f-w-700">
                        KRI Name
                      </div>
                      <div class="col-lg-3 f-w-700">
                        Frequency
                      </div>
                      <div class="col-lg-3 f-w-700 text-right">
                        Weightage
                      </div>
                      <div class="col-lg-2 f-w-700 text-right">
                        Taraget
                      </div>
                  </div>
                 <?php foreach($ogoalval as $kpilit){ ?>
                  <div class="row">
                      <div class="col-lg-4">
                        <p><?php echo $kpilist[$kpilit['key_performance_id']]['indicator_name'];?></p>
                      </div>
                      <div class="col-lg-3">
                        <p><?php echo $kpilit['assessment_frequency'];?></p>
                      </div>
                      <div class="col-lg-3 text-right">
                        <p><?php echo $kpilist[$kpilit['key_performance_id']]['weightage'];?></p>
                      </div>
                      <div class="col-lg-2 text-right">
                        <p><?php echo $kpilit['traget'];?></p>
                      </div>
                  </div>
                <?php } ?>

                </div>
              </div>
          </div>
        <?php } ?>

          </div>
        </div>
      </div>
<div class="modal-footer mt-5" >
  <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Cancel</button>
</div>
