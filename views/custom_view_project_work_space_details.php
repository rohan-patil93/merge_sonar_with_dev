<?php
$curl1 = API."?mode=togettopprojectworkspacelist";
$cudata1 = callAPI($curl1);
$result = json_decode($cudata1,1);
$statuslist  = getstatusprogresslist('projects');
foreach($statuslist as $statuslist){
    $statusnamelist[$statuslist['status']] = $statuslist['name'];
}
$aciveid = $_GET['TAB']!="" ? $_GET['TAB'] : 1 ;
?>
<div class="d-flex flex-column-fluid card">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="" id="kt_page_sticky_card">
                        <div class="card-header mb-3">
                            <h5> Projects </h5>
                            <div class="card-header-right">

                                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                    <i class="icofont icofont-reply"></i>
                                    Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row" >
                                <div class="col-lg-12">
                                    <ul class="nav md-tabs infisuite_tab">
                                        <?php foreach($result['project_work_spaces'] as $key=>$tabs){ ?>
                                        <li class="nav-item">
                                            <a class="nav-link f-w-600 <?php if($aciveid==$key){ echo "active";
} ?>" data-bs-toggle="tab" href="#tab<?php echo $key; ?>" role="tab" aria-selected="true"><?php echo $tabs['name']; ?></a>
                                            <div class="slide"></div>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                                <?php $checkjobs = array('0'=>'Features','1'=>'Support / Operations','2'=>'Bug tracking'); ?>
                                <div class="tab-content tabs card-body">
                                    <?php foreach($result['project_work_spaces'] as $key=>$tabs){   ?>
                                    <div class="tab-pane <?php if($aciveid==$key){ echo "active";
} ?> " id="tab<?php echo $key; ?>" role="tabpanel">
                                        <div class="col-lg-12 row">
                                            <?php if(!empty($result)){ $m=1;
                                                foreach ($result['projects'][$key] as  $res) {  $check=1;

                                                    $nridw = "id:".$res['id'];
                                                    $uimg = $res['project_logo']!=""?$res['project_logo']:"no-image.jpg";
                                                    if($check==1){
                                                    ?>
                                                    <div class="col-xl-3 col-md-6">
                                                        <div class="card sale-card ">
                                                            <?php
                                                            $statucolorname = $stausresult[$res['current_status']];
                                                            ?>
                                                            <div class="ribbon ribbon-top-right" >
                                                                <span class="color-primary" style="color: <?php echo $_SESSION['status'][$res['current_status']]['color'] ?>;    background:  <?php echo $_SESSION['status'][$res['current_status']]['background_color']; ?>; border-color: <?php echo $_SESSION['status'][$res['current_status']]['background_color']; ?>;"><?php echo $statusnamelist[$res['current_status']]; ?>
                                                                </span>
                                                            </div>
                                                            <div class="card-header mt-2">
                                                                <h5><?php echo $res['project_name']; ?></h5>
                                                            </div>
                                                            <div class="card-block text-center">
                                                                <div data-label="40%" class="radial-bar radial-bar-40 radial-bar-lg radial-bar-danger">
                                                                    <img src="uploads/all/<?php echo $uimg; ?>" alt="User">
                                                                </div>
                                                                <div class="row">
                                                                    <div class="col-6">
                                                                        <a href="index.php?option=COM_VIEW_PROJECT&RID=<?php echo  $res['id']; ?>" class="btn btn-info waves-effect waves-light  f-12">Over View</a>
                                                                    </div>
                                                                    <div class="col-6 b-l-default">
                                                                        <a href="index.php?option=COM_VIEW_PROJECT_KANBAN&RID=<?php echo  $res['id']; ?>" class="btn btn-info waves-effect waves-light  f-12">Task View</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php  $m++;
}
}
                                            }else{ ?>
                                                <div class="card-px text-center py-20 m-t-30">
                                                    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Jobs Posting </p>
                                                    <div class="text-center px-4">
                                                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
