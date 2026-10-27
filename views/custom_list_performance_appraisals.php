<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$admintype=$_SESSION['admin_type'];
$url = API."?mode=getperformanceappraisalreport&assessment_year=".$_POST['assessment_year']."&ajaxyearlist=Y&admin_type=".$admintype."&emp_id=".$_SESSION['emp_id'];
if($_POST['type']=="S"){
    $url = API."?mode=getperformanceappraisalreport&assessment_year=".$_POST['assessment_year']."&ajaxyearlist=Y&emp_id=".$_SESSION['emp_id']."&admin_type=".$admintype."&self=Y";
}
$response = callAPI($url);
$perapprreport = json_decode($response,1);
$employees = $perapprreport['employees'];
$employee_performance_appraisals  = $perapprreport['employee_performance_appraisals'];
$performance_appraisal_setups = $perapprreport['performance_appraisal_setups'];
$epahlresult = array();
foreach($employee_performance_appraisals as $epahlres){
    foreach($epahlres as $reslt){
        if($reslt['current_status'] == ST_APPROVED){
            $epahlresult[$reslt['appraisal_id']][ST_APPROVED][] = $reslt;
        }elseif($reslt['current_status'] == ST_ACTIVE){
            $epahlresult[$reslt['appraisal_id']][ST_ACTIVE][] = $reslt;
        }else{
            $epahlresult[$reslt['appraisal_id']]['inpro'][] = $reslt;
        }
    }
}
?>
<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
        <ul class="nav md-tabs infisuite_tab">
            <?php $act = 0; foreach($performance_appraisal_setups as $performance_setups){?>
            <li class="nav-item">
                <a class="nav-link <?php echo $act=='0'?'active':''; ?> text-capitalize" data-bs-toggle="tab" href="#appraisal_tab_<?php echo $performance_setups['id']; ?>" role="tab"><?php echo $performance_setups['performance_appraisal_name']; ?></a>
                <div class="slide"></div>
            </li>
            <?php $act++;
} ?>
        </ul>
        <div class="tab-content card-block" >
            <?php $activ = 0; foreach($performance_appraisal_setups as $performancesetup){
                $isactivelist = $epahlresult[$performancesetup['id']][ST_ACTIVE];

                ?>
            <div class="tab-pane <?php echo $activ=='0'?'active':''; ?>" id="appraisal_tab_<?php echo $performancesetup['id']; ?>" role="tabpanel">
                <div class="row">
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                        <div class="card table-card">
                            <div class="card-header">
                                <h5>Yet To Submit</h5>
                                <div class="card-header-right">
                                    <span class="form-label label label-primary"><?php echo count((array)$isactivelist);?></span>
                                </div>
                            </div>

                            <div class="card-block" >
                                <div class="table-responsive">
                                    <?php

                                    if(count((array)$isactivelist)!='0'){  ?>
                                    <table class="table table-hover m-b-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 60%;">Employee</th>
                                                <th scope="col" class="text-right" style="width: 20%;">Self %</th>
                                                <th scope="col" class="text-right" style="width: 20%;">RO %</th>
                                            </tr>
                                        </thead>
                                        <tbody class="custom-scroll" style="height: 300px; display: table-caption;">
                                            <?php $mk=1; foreach($isactivelist as $isactiveres){
                                            $id=deocdeId("id:".$isactiveres['id']);
                                            $empdetails = $employees[$isactiveres['employee_id']];
                                            $href = "index.php?option=COM_INSERT_EMPLOYEE_PERFORMANCE_APPRAISALS&Rid=".$id;
                                            ?>
                                            <tr class="w-100 display-table">
                                                <td class="p-2" style="width: 60%;">
                                                    <a href="<?php echo $href; ?>" target="_blank">
                                                    <div class="align-middle">
                                                        <?php if($empdetails['image']!="") {?>
                                                            <img src="uploads/all/<?php echo $empdetails['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                                                        <?php } else { ?>
                                                            <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 img-30 text-center" ><?php echo initials($empdetails['display_name']); ?></span>
                                                        <?php } ?>

                                                        <div class="d-inline-block width73">
                                                            <span class="text-danger text-capitalize f-12"><span class="f-14"><?php echo $empdetails['display_name']; ?></span></span>
                                                            <p class="text-muted m-b-0  f-12"><?php echo $empdetails['emp_code']; ?></p>
                                                        </div>
                                                    </div>
                                                    </a>
                                                </td>
                                                <td class="text-right p-2" style="width: 20%;">
                                                    <span class="form-label label label-primary p-2"><?php echo round($isactiveres['self_score']); ?></span>
                                                </td>
                                                <td class="text-right p-2" style="width: 20%;">
                                                    <span class="form-label label label-primary p-2"><?php echo round($isactiveres['approved_score']); ?></span>
                                                </td>
                                            </tr>
                                            <?php $mk++;
} ?>
                                        </tbody>
                                    </table>
                                    <?php    }else{ ?>
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-muted f-16 fw-bold m-b-30">
                                            <i class='fa fa-warning'></i> No Employees
                                        </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                    <?php     } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                        <div class="card table-card">
                            <div class="card-header">
                                <?php $isactivelist = $epahlresult[$performancesetup['id']]['inpro']; ?>
                                <h5>In-Process</h5>
                                <div class="card-header-right">
                                    <span class="form-label label label-primary"><?php echo count((array)$isactivelist);?></span>
                                </div>
                            </div>
                            <div class="card-block ">
                                <div class="table-responsive">
                                    <?php

                                    if(count((array)$isactivelist)!='0'){  ?>
                                    <table class="table table-hover m-b-0">
                                        <thead>
                                            <tr>
                                            <th scope="col" style="width: 60%;">Employee</th>
                                            <th scope="col" class="text-right" style="width: 20%;">Self %</th>
                                            <th scope="col" class="text-right" style="width: 20%;">RO %</th>
                                            </tr>
                                        </thead>
                                        <tbody classs="custom-scroll" style="height: 300px; display: table-caption;">
                                            <?php $mk=1; foreach($isactivelist as $isactiveres){
                                            $id=deocdeId("id:".$isactiveres['id']);
                                            $empdetails = $employees[$isactiveres['employee_id']];
                                            $href = "index.php?option=COM_INSERT_EMPLOYEE_PERFORMANCE_APPRAISALS&Rid=".$id;
                                            ?>
                                            <tr class="w-100 display-table">

                                                <td class="p-2" style="width: 60%;">
                                                    <a href="<?php echo $href; ?>" target="_blank">
                                                    <div class="align-middle">

                                                        <?php if($empdetails['image']!="") {?>
                                                            <img src="uploads/all/<?php echo $empdetails['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                                                        <?php } else { ?>
                                                            <span  alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($empdetails['display_name']); ?></span>
                                                        <?php } ?>

                                                        <div class="d-inline-block width73">
                                                            <a href="<?php echo $href; ?>" class="text-danger text-capitalize" target="_blank"><h6><?php echo $empdetails['display_name']; ?></h6></a>
                                                            <p class="text-muted m-b-0"><?php echo $empdetails['emp_code']; ?></p>

                                                        </div>
                                                    </div>
                                                    </a>
                                                </td>
                                                <td class="text-right p-2" style="width: 20%;">
                                                    <span class="form-label label label-primary p-2"><?php echo round($isactiveres['self_score']); ?></span>
                                                </td>
                                                <td class="text-right p-2" style="width: 20%;">
                                                    <span class="form-label label label-primary p-2"><?php echo round($isactiveres['approved_score']); ?></span>
                                                </td>
                                            </tr>
                                            <?php $mk++;
} ?>
                                        </tbody>
                                    </table>
                                    <?php    }else{ ?>
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Employees </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                    <?php     } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-12">
                        <div class="card table-card">
                            <?php $isactivelist = $epahlresult[$performancesetup['id']][ST_APPROVED]; ?>
                            <div class="card-header">
                                <h5>Approved</h5>
                                <div class="card-header-right">
                                    <span class="form-label label label-primary"><?php echo count((array)$isactivelist);?></span>
                                </div>
                            </div>
                            <div class="card-block" >
                                <div class="table-responsive">
                                    <?php

                                    if(count((array)$isactivelist)!='0'){  ?>
                                    <table class="table table-hover m-b-0">
                                        <thead>
                                            <tr>
                                                <th scope="col" style="width: 60%;">Employee</th>
                                                <th scope="col" class="text-right" style="width: 20%;">Self %</th>
                                                <th scope="col" class="text-right" style="width: 20%;">RO %</th>
                                            </tr>
                                        </thead>
                                        <tbody class="custom-scroll" style="height: 300px; display: table-caption;">
                                            <?php $mk=1; foreach($isactivelist as $isactiveres){
                                            $id=deocdeId("id:".$isactiveres['id']);
                                            $empdetails = $employees[$isactiveres['employee_id']];
                                        $href = "index.php?option=COM_INSERT_EMPLOYEE_PERFORMANCE_APPRAISALS&Rid=".$id;
                                            ?>
                                            <tr class="w-100 display-table">
                                                <td class="p-2" style="width: 60%;">
                                                <a href="<?php echo $href; ?>" target="_blank">
                                                    <div class="align-middle">

                                                        <?php if($empdetails['image']!="") {?>
                                                            <img src="uploads/all/<?php echo $empdetails['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                                                        <?php } else { ?>
                                                            <span  alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center" ><?php echo initials($empdetails['display_name']); ?></span>
                                                        <?php } ?>

                                                        <div class="d-inline-block width73">
                                                            <a href="<?php echo $href; ?>" class="text-danger text-capitalize" target="_blank"><h6><?php echo $empdetails['display_name']; ?></h6></a>
                                                            <p class="text-muted m-b-0"><?php echo $empdetails['emp_code']; ?></p>

                                                        </div>
                                                    </div>
                                                    </a>
                                                </td>
                                                <td class="text-right p-2" style="width: 20%;">
                                                    <span class="form-label label label-primary p-2"><?php echo round($isactiveres['self_score']); ?></span>
                                                </td>
                                                <td class="text-right p-2" style="width: 20%;">
                                                    <span class="form-label label label-primary p-2"><?php echo round($isactiveres['approved_score']); ?></span>
                                                </td>
                                            </tr>
                                            <?php $mk++;
} ?>
                                        </tbody>
                                    </table>
                                    <?php    }else{ ?>
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Employees </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                        </div>
                                    </div>
                                    <?php     } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $activ++;
} ?>
        </div>
    </div>
</div>
</div>
</div>

