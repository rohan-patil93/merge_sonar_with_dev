<?php
if($_POST['check']=='data'){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
}
$url = API."?mode=employeedetails&emp_id=".$_SESSION['emp_id'];
$response = callAPI($url);
$empdata = json_decode($response,1);
$data = $empdata['emp_data'];
$teams =  $empdata['teams'];
foreach($empdata['departments'] as $department){
  $emp_department[$department['id']] = $department['department_name'];
}
foreach($empdata['designations'] as $designation){
  $emp_designation[$designation['id']] = $designation['designation_name'];
}

?>
<div class="d-flex flex-column-fluid ">
   <div class="container-fluid j-pro pt-0">
      <div class="card-sticky j-content" id="kt_page_sticky_card">
        <div class="card-header">
            <h5>My Team</h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                    <i class="icofont icofont-reply"></i>Back
                </a>
            </div>
        </div>
        <div class="">
            <div class="card-body">
                <?php if(isset($teams['reproter']['id'])){  $report =$teams['reproter'] ; ?>
                <div class="row">
                           <h4>Reporting Manager</h4>
                           <div class="col-xl-3 col-md-6">
                              <div class="card sale-card ">
                                 <div class="card-header">
                                    <h5><a href="#" class="text-dark font-weight-bold text-hover-primary font-size-h4 mb-0"><?php echo ucwords($report['display_name']);?></a></h5>
                                 </div>
                                 <div class="card-block text-center">
                                    <div data-label="40%" class="radial-bar custom-radial-bar radial-bar-40 radial-bar-lg radial-bar-danger mt-0 mb-2">
                                       <?php if($report['image']!=""){ ?>
                                       <img src="uploads/all/<?php echo $report['image']; ?>" alt="Details" class="img-radius img-80">
                                          <?php } ?>
                                    </div>
                                    <div class="row">
                                          <span class="text-muted font-weight-bold"> <?php  echo $emp_department[$report['department_id']]; ?></span>
                                          <?php if($report['email_id'] !=""){?>
                                          <span class="text-muted font-weight-bold"><?php echo '<i class="ti-email"></i> '.$report['email_id'];?></span>
                                        <?php } ?>
                                         <?php if($report['phone'] !=""){?>
                            <span class="text-muted font-weight-bold"><?php echo $report['phone'] !=""?'<i class="fa fa-phone"></i> '.$report['phone']:"";?></span>
                          <?php } ?>
                                          <span class="text-muted font-weight-bold"><?php  echo $emp_designation[$team['designation_id']]; ?></span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
            <?php }
if(count((array)$teams['teams']) != 0){ ?>
                        <div class="mt-10">
                          <h4>Teams</h4>
                           <div class="row">
<?php foreach ($teams['teams'] as $team) {  ?>

   <div class="col-xl-3 col-md-6">
        <div class="card sale-card ">
            <div class="card-header">
                <h5><?php echo ucwords($team['display_name']);?></h5>
            </div>
            <div class="card-block text-center">
                <div data-label="<?php echo initials($team['display_name']); ?>" class="radial-bar custom-radial-bar radial-bar-40 radial-bar-lg radial-bar-danger mt-0 mb-2">
            <?php if($team['image']!=""){ ?>
              <img src="uploads/all/<?php echo $team['image']; ?>" alt="Attachment Preview">
            <?php } ?>

                </div>
                  <div class="row  mb-3">

   <?php if($report['designation_id'] !=""){?>
       <span class="text-muted font-weight-bold">&nbsp;<?php  echo $emp_designation[$team['designation_id']]; ?></span>
   <?php }
if($report['email_id'] !=""){?>

       <span class="text-muted font-weight-bold"><?php echo $team['email_id']!=""?'<i class="ti-email"></i> '.$team['email_id']:"";?></span>
       <?php }
if($report['phone'] !=""){ ?>
       <span class="text-muted font-weight-bold">&nbsp;<?php echo $team['phone']!=""?'<i class="fa fa-phone"></i> '.$team['phone']:"";?></span>
     <?php } ?>

                </div>

            </div>
        </div>
    </div>
<?php } ?>
      </div>
                        </div>
            <?php }else{ ?>
              <div class="accordion-desc custom-scroll p-0 ui-accordion-content ui-corner-bottom ui-helper-reset ui-widget-content ui-accordion-content-active" style="height: 400px;" id="ui-id-2" aria-labelledby="ui-id-1" role="tabpanel" aria-hidden="false">
                <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Team</p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>
              </div>
            <?php } ?>
         </div>
      </div>
   </div>
</div>
</div>
