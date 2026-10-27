<?php

$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$appraisal_id  = $erid['1'];
$assessment_year = $_GET['AY'];

if($_POST['submit']=='savepeerfeedback'){
    $data['peer_feedback_date'] = date('Y-m-d',strtotime($_POST['peer_feedback_date']));
    $data['peer_feedback_month'] = $_POST['peer_feedback_month'];
    $url = API."?mode=insertandupdateperformancesetupquestions&method=post";
    $response = file_post_contents($url, $_POST, 'update');
    $url = API."?mode=updaterowtabledata&table=performance_setup&rid=".$appraisal_id."&method=post";
    $response = file_post_contents($url, $data, 'update');
}
$url = API."?mode=getpeerfeedback&assessment_year=".$assessment_year."&appraisal_id=".$appraisal_id."&assign_type=3";
$response = callAPI($url);
$peerfeedbackres = json_decode($response,1);
$performance_setup11 = $peerfeedbackres['performance_setup'];
foreach($peerfeedbackres['performance_setup_questions'] as $return){
        $data[$return['assign_type']][] = $return;
    }

?>
<script type="text/javascript">

</script>
<div class="flex-column-fluid ">
    <div class="">
        <div class="row j-content">  <!-- j-content -->
            <div class="col-lg-12">
                <?php
                if($error_message !=''){ ?>
                    <div class="alert alert-danger background-danger alert-dismissible">
                        <strong><?php echo $error_message; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header">
                        <h5>Next Peer Feedback</h5>
                        <div class="card-header-right">
                            <?php if($_GET['option'] != "COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS"){ ?>
                     <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                  <i class="icofont icofont-reply"></i>  Back
                     </a>
                    <?php } ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" id="headerformname">
                            <div class="row">
                                <div class="col-lg-6">

                                <div class="form-group col-lg-8">
                                    <div class=" mb-2"  >
                                        <span class="orm-label">Aassessment Year</span>
                                        <input type="hidden" name="appraisal_id" value="<?php echo $appraisal_id ?>">
                                        <input aria-label="Assessment Year" type="text" class="form-control" name="assessment_year" value="<?php echo $assessment_year ?>" readonly>

                                    </div>
                                </div>
                                <div class="form-group col-lg-8" >
                                    <span>When should we prompt managers to request peer feedback?</span>
                                    <?php $date_pic3 = fromDate($performance_setup11['peer_feedback_date']); ?>
                                    <input aria-label="Input field" type="text" class="form-control datepicker"  value="<?php echo  $date_pic3; ?>"  name="peer_feedback_date" onblur="perrfeedbackchange()" id="kt_datepicker_32" />

                                </div>
                                <div class="form-group col-lg-8 w-full" >
                                    <span>How often do you want managers to get peer feedback on their direct reports?</span>
                                    <select aria-label="Peer Feedback Month" class="form-control form-select js-example-basic-single" onclick="perrfeedbackchange()" onkeyup="perrfeedbackchange()" name="peer_feedback_month" id="peer_feedback_month">
                                        <option <?php if($performance_setup11['peer_feedback_month']==3){ echo "selected";
}  ?> value="3" >Every 3 Months</option>
                                        <option <?php if($performance_setup11['peer_feedback_month']==4){ echo "selected";
}  ?> value="4" >Every 4 Months</option>
                                        <option <?php if($performance_setup11['peer_feedback_month']==6){ echo "selected";
}  ?> value="6" >Every 6 Months</option>
                                        <option <?php if($performance_setup11['peer_feedback_month']==12){ echo "selected";
}  ?> value="12" >Every Year</option>
                                    </select>
                                </div>

                                <div class="form-group col-lg-8" >
                                    <div id="peerfeedback_date">
                                        <?php if($performance_setup11['peer_feedback_date']!=""){
                                            $sdate = $performance_setup11['peer_feedback_date'];
                                            for($i=0;$i<4;$i++){
                                                echo "<br>";
                                                $sdate = date("Y-m-d", strtotime($sdate."+".$performance_setup11['peer_feedback_month']." month"));
                                                echo  "-> ".date("M d, Y", strtotime($sdate));
                                            }
                                        } ?>
                                    </div>
                                </div>
                                <div class="col-lg-12"><span class="text-danger f-14 error_messgae_apriid_year"></span></div>
                            </div>
                            <div class="col-lg-6">

                                <?php include_once 'custom_insert_performance_peer_feedback.php'; ?>
                            </div>
                            </div>
                            <input type="hidden" name="submit" value="savepeerfeedback" />
                            <input type="hidden" name="rid" value="<?php echo $appraisal_id; ?>" />
                            <input type="hidden" name="assessment_id" value="<?php echo $appraisal_id; ?>" />

                            <input type="hidden" name="assign_type" value="3" />
                            <input type="hidden" name="display_questions" value="0" />
                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="savepeerfeedback1" value="exit"><i class="la la-check"></i>Submit Peer Feedback</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
