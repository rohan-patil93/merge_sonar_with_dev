<?PHP
$admintype=$_SESSION['admin_type'];

$vurl = API."?mode=getemployeehrservice&emp_id=".$_SESSION['emp_id']."&admin_type=".$admintype."&view=Y";
$vurl = API."?mode=getemployeehrservice&emp_id=".$_SESSION['emp_id']."&admin_type=".$admintype."&view=Y&month=".date('m')."&year=".date('Y');
$servicecall = callAPI($vurl);
$servicesres = json_decode($servicecall,1);
$services = $servicesres['all_services'];
$documents = $servicesres['company_documents'];
$emp_list = $servicesres['emp_list'];
$leavelist = array();
foreach($services as $loopdetails){
    if($loopdetails['current_status'] == ST_APPROVED){
        $leavelist[ST_APPROVED][] = $loopdetails;
    }elseif($loopdetails['current_status'] == ST_ACTIVE){
        $leavelist[ST_ACTIVE][] = $loopdetails;
    }else{
        $leavelist['inpro'][] = $loopdetails;
    }
}
$documentname = array();
foreach ($documents as $key => $dd) {
    $values[$dd['id']] = $dd['documnet_path'];
    $documentname[$dd['id']] = $dd['document_name'];
}
?>
<script type="text/javascript">
    function showHrServices(month,year){
        $("#hr_servicess").hide();
          $("#loader-show").show();
        $.ajax({
            url :'views/custom_list_hr_services_ajax.php',
            type:'post',
            data : 'month='+month+"&year="+year,
            success:function(respons){
                    $("#hr_servicess").show();
                      $("#loader-show").hide();
                $("#hr_servicess").html(respons);
            }
        })
    }
</script>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid">
        <div class="row j-content">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header">
                    <h5>HR Service Requests</h5>
                    <div class="card-header-right">
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>  Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-lg-12 col-xl-12">
                        <ul class="nav md-tabs infisuite_tab">
                            <?php
                            $thismonth = date('M Y');
                            $month = strtotime(date('Y').'-'.date('m').'-01 - 11 months');
                            $end = strtotime(date('Y').'-'.date('m').'-'.date('j').' + 1 months');
                            while($month <= $end){
                                $disss = date('M Y',$month);
                                $dis_year = date('Y',$month);
                                $dis_month = date('m',$month);
                                $month = strtotime("+1 month", $month);
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $disss==$thismonth?"active":""; ?>" data-bs-toggle="tab" href="#appraisal_setting_self" role="tab" onclick="showHrServices('<?php echo $dis_month; ?>','<?php echo $dis_year; ?>')"> <?php echo $disss; ?></a>
                                    <div class="slide"></div>
                                </li>
                            <?php  } ?>
                        </ul>
                        <div class="tab-content card-block" >
                            <div class="text-center" style="display: none;  margin-left: auto;  margin-right: auto;  width: 40%; " id="loader-show">
                                <img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview">
                             </div>
                            <div class="tab-pane  active" id="hr_servicess" role="tabpanel" >
                                    <?php include_once "views/custom_list_hr_services_ajax.php"; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

