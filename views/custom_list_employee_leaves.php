<?php

$emp_id = $_SESSION['emp_id'];
$leavecriterialisturl = API."?mode=gettocheckleavetypecriteria&emp_id=".$emp_id;
$leavecriterialistres = callAPI($leavecriterialisturl);
$leavecriterialistres = json_decode($leavecriterialistres,1);
$leavetype_detrails  = $leavecriterialistres['leavetype_detrails'];
$buddy_details  = $leavecriterialistres['buddy_details'];
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="">
            <div class="card-header table-card-header">
              <h5><?php echo "Leaves";  ?></h5>
                            <div class="card-header-right table-card-header cutm-c-h-r">
                              <?php $option_url = "COM_INSERT_LEAVE_REQUEST";
                                      if($_SESSION['work_module_setup_id']==19){
                                          $option_url = "COM_INSERT_VMS_LEAVE_REQUEST";
                                      }

                              ?>
                              <a href="index.php?option=<?php echo $option_url;  ?>" target="_blank" class="btn btn-success float-left">
                                        <i class="icofont icofont-plus icon-white "></i> Apply Leave </a>
                            </div>
                        </div>

        <?php if($_GET['TEAM']==1){ ?>
          <input type="hidden" name="calendar_cat" id="calendar_cat" value="56">
      <?php }else{ ?>
        <input type="hidden" name="calendar_cat" id="calendar_cat_2" value="6">
       <?php } ?>
                <div class="card-body">
                    <div class="d-flex flex-column">
                        <div class="row m-b-30">
<?php
if($_SESSION['work_module_setup_id']!=19){
    foreach($leavecriterialistres['available_category'] as $key=>$values){
    $leave_details  = $leavetype_detrails[$key]['leavedetails'];
?>

                <div class="col-lg-2">
                    <div class="card card-custom gutter-b mb-0" >
                        <div class="p-3">
                            <span class="text-inverse-success font-weight-bolder  font-size-sm mt-1 f-14 ">
                                <?php echo $values; ?>
                            </span>
                            <div class="text-inverse-success font-weight-bolder font-size-h3 mt-3 d-none"><?php echo $leave_details['applicable_leaves']; ?></div>
                            <div class="row mt-3">
                                <div class="col-6 text-center">
                                    <h5 class="f-w-700 m-b-0"><?php echo $leave_details['consumed_leaves']; ?></h5>
                                    <p class="m-b-0">Consumed</p>
                                </div>
                                <div class="col-6 b-l-default text-center">
                                    <h5 class="f-w-700 m-b-0"><?php echo $leave_details['applicable_leaves']-$leave_details['consumed_leaves']; ?></h5>
                                    <p class="m-b-0">Available</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php $mk++;
} ?>

<?php } ?>



                        </div>
                    </div>

                     <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if($_POST['ajaxpage'] == 'Y'){?>

<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/bower_components/fullcalendar/js/fullcalendar.min.js"></script>
<script type="text/javascript" src="assets/pages/full-calender/calendar.js"></script>
<?php } ?>
