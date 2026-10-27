<?PHP

if(isset($_POST['month'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $admintype=$_SESSION['admin_type'];
    $vurl = API."?mode=getemployeehrservice&emp_id=".$_SESSION['emp_id']."&admin_type=".$admintype."&view=Y&month=".$_POST['month']."&year=".$_POST['year'];
    $servicecall = callAPI($vurl);
    $servicesres = json_decode($servicecall,1);
    $services = $servicesres['all_services'];
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
}
?>
<div class="row ">
    <div class="col-xl-4 col-md-4">
        <div class="card table-card">
            <div class="card-header">
                <h5>Yet To Submit</h5>
                <div class="card-header-right">
                    <span class="form-label label label-primary"><?php echo count((array)$isactivelist);?></span>
                </div>
            </div>

            <div class="card-block" >
                <div class="table-responsive">
                    <?php if(count((array)$leavelist[ST_ACTIVE])!='0'){  ?>
                        <table class="table table-hover m-b-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="p-2" style="width: 10%;">#</th>
                                    <th scope="col" class="p-2" style="width: 45%;">Name</th>
                                    <th scope="col" class="p-2" style="width: 45%;">Request</th>
                                </tr>
                            </thead>
                            <tbody class="custom-scroll" style="height: 300px; display: table-caption; ">
                                <?php $mk=1; foreach($leavelist[ST_ACTIVE] as $leavel1){ $isactiveres = $emp_list[$leavel1['emp_id']]; ?>
                                    <tr style="display:flex;" class="pb-0">
                                        <td class="p-2" style="width: 10%;"><?php echo $mk; ?></td>
                                        <td class=" p-2 w-b-w-s" style="width: 45%;">

<a href="index.php?option=COM_INSERT_HR_SERVICES&Rid=<?php  echo deocdeId("id:".$leavel1['id']); ?> target="_blank">
    <div class="align-middle">
    <?php if($isactiveres['image']!="") {?>
        <img src="uploads/all/<?php echo $isactiveres['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
    <?php } else { ?>
        <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($isactiveres['display_name']); ?></span>
    <?php } ?>

    <div class="d-inline-block width73">
        <span class="text-danger text-capitalize f-12"><span class="f-14"><?php echo $isactiveres['display_name']; ?></span></span>
        <p class="text-muted m-b-0  f-12"><?php echo $isactiveres['emp_code']; ?></p>
    </div>
    </div>
</a>

                                        </td>
                                        <td class=" p-2 w-b-w-s" style="width: 45%;"><p><?php echo $leavel1['request_date']; ?></p></td>

                                    </tr>
                                    <?php $mk++;
} ?>
                                </tbody>
                            </table>
                        <?php    }else{ ?>
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-warning f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Service Requests </p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                </div>
                            </div>

                        <?php     } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4">
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
                        <?php if(count((array)$leavelist['inpro'])!='0'){  ?>
                            <table class="table table-hover m-b-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="p-2" style="width: 10%;">#</th>
                                    <th scope="col" class="p-2" style="width: 45%;">Name</th>
                                    <th scope="col" class="p-2" style="width: 45%;">Request</th>
                                    </tr>
                                </thead>
                                <tbody class="custom-scroll" style="height: 300px; display: table-caption; ">
                                    <?php $mk=1; foreach($leavelist['inpro'] as $leavel1){ $isactiveres = $emp_list[$leavel1['emp_id']]; ?>
                                        <tr style="display:flex;" class="pb-0">
                                            <td class="p-2" style="width: 10%;"><?php echo $mk; ?></td>
                                        <td class=" p-2 w-b-w-s" style="width: 45%;">
                                            <a href="index.php?option=COM_INSERT_HR_SERVICES&Rid=<?php  echo deocdeId("id:".$leavel1['id']); ?>" target="_blank">
    <div class="align-middle">
    <?php if($isactiveres['image']!="") {?>
        <img src="uploads/all/<?php echo $isactiveres['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
    <?php } else { ?>
        <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($isactiveres['display_name']); ?></span>
    <?php } ?>

    <div class="d-inline-block width73">
        <span class="text-danger text-capitalize f-12"><span class="f-14"><?php echo $isactiveres['display_name']; ?></span></span>
        <p class="text-muted m-b-0  f-12"><?php echo $isactiveres['emp_code']; ?></p>
    </div>
    </div>
</a>

                                        </td>
                                        <td class=" p-2 w-b-w-s" style="width: 45%;"><p><?php echo $leavel1['request_date']; ?></p></td>
                                        </tr>
                                        <?php $mk++;
} ?>
                                    </tbody>
                                </table>
                            <?php    }else{ ?>
                                <div class="card-px text-center py-20 m-t-30">
                                    <p class="text-warning f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Service Requests </p>
                                    <div class="text-center px-4">
                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;" />
                                    </div>
                                </div>

                            <?php     } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-4">
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
                            <?php if(count((array)$leavelist[ST_APPROVED])!='0'){  ?>
                                <table class="table table-hover m-b-0">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="p-2" style="width: 10%;">#</th>
                                    <th scope="col" class="p-2" style="width: 45%;">Name</th>
                                    <th scope="col" class="p-2" style="width: 45%;">Response</th>
                                        </tr>
                                    </thead>
                                    <tbody class="custom-scroll" style="height: 300px; display: table-caption; ">
                                        <?php $mk=1; foreach($leavelist[ST_APPROVED] as $leavel1){ $isactiveres = $emp_list[$leavel1['emp_id']]; ?>
                                            <tr style="display:flex;" class="pb-0">
                                                <td class="p-2" style="width: 10%;"><?php echo $mk; ?></td>
                                        <td class=" p-2 w-b-w-s" style="width: 45%;">
                                            <a href="index.php?option=COM_INSERT_HR_SERVICES&Rid=<?php  echo deocdeId("id:".$leavel1['id']); ?>" target="_blank">
                                                <div class="align-middle">
                                                <?php if($isactiveres['image']!="") {?>
                                                    <img src="uploads/all/<?php echo $isactiveres['image']; ?>"   class="img-radius img-30 align-top m-r-15" alt="Attachment Preview">
                                                <?php } else { ?>
                                                    <span  alt="user" class="img-radius align-top m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($isactiveres['display_name']); ?></span>
                                                <?php } ?>

                                                <div class="d-inline-block width73">
                                                    <span class="text-danger text-capitalize f-12"><span class="f-14"><?php echo $isactiveres['display_name']; ?></span></span>
                                                    <p class="text-muted m-b-0  f-12"><?php echo $isactiveres['emp_code']; ?></p>
                                                </div>
                                                </div>
                                                </a>

                                        </td>
                                        <td class=" p-2 w-b-w-s" style="width: 45%;"><p><?php echo $leavel1['response_date']; ?></p></td>
                                            </tr>
                                            <?php $mk++;
} ?>
                                        </tbody>
                                    </table>
                                <?php    }else{ ?>
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-warning f-16 fw-bold m-b-30"><i class='fa fa-warning'></i> No Service Requests </p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                        </div>
                                    </div>

                                <?php     } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
