<?php 
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
$fileupload_path = "uploads/all/";
$announcement_id = $_POST['announcement_id'];
$whrcondion = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','announcement_id'=>$announcement_id);
$viewtab = $_POST['view'];
$data11["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','id'=>$announcement_id);
$datw = urlencode(json_encode($data11));
$curl1 = API."?mode=gettabledetailswithconditions&table=company_announcements&data=".$datw; 
//print_r($_POST); exit;
$cudata1 = callAPI($curl1);
$announce_detail = json_decode($cudata1,1);
$announce_details = $announce_detail[0];
$data11["where"] = $whrcondion;
    //print_r($data11);
$datw = urlencode(json_encode($data11));
$curl1 = API."?mode=gettabledetailswithconditions&table=summary_announcements&data=".$datw; 
$cudata1 = callAPI($curl1);
$announce_data = json_decode($cudata1,1);
//print_r($announce_data);
foreach ($announce_data as $data) {
    $userlist[] = $data['userid'];
    $data11["field"] = 'image,display_name';
    $data11["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','user_id'=>$data['userid']);
    $datw = urlencode(json_encode($data11));
    $curl1 = API."?mode=gettabledetailswithconditions&table=employees&data=".$datw; 
    $cudata1 = callAPI($curl1);
    $empdatas = json_decode($cudata1,1);
    $empdata = $empdatas[0];

    if($data['comments']!=''){
        $commentslist[] = array('userid'=>$data['userid'],'comments'=>$data['comments'],'created_by_image'=>$empdata['image'],'display_name'=>$empdata['display_name']);
    }
    if($data['like_type']=='1'){
        $likelist[] = array('userid'=>$data['userid'],'created_by_image'=>$empdata['image'],'display_name'=>$empdata['display_name']);
    }
    if($data['like_type']=='2'){
        $dislikelist[] = array('userid'=>$data['userid'],'created_by_image'=>$empdata['image'],'display_name'=>$empdata['display_name']);
    }

}

$totalcomments = count((array)$commentslist);
$totallikes = count((array)$likelist);
$totaldislikes= count((array)$dislikelist);
//print_r($commentslist);
?>
<div class="modal-header">
        <h5 class="modal-title text-center">

            <?php echo $announce_details['announcement_heading']; ?> 
        </h5>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
   
   </div>
    <div class="modal-body announcement-popup-body" data-scroll="true" data-height="100"> 

        <form class="kt-form kt-form--label-right" action="" method="post" >
            <div class="form-group row" >
                <div class="col-lg-12">

                    <ul class="nav md-tabs">
                        <li class="nav-item" >
                            <a class="nav-link <?php echo $viewtab == 'comment'? 'active':'';?>" id="home-tab-4" data-bs-toggle="tab" href="#home-4">
                                <span class="nav-icon">
                                    <i class="icon-xl la la-comment-o"></i> 
                                </span>
                                <span class="nav-text">Comments ( <?php echo $totalcomments;  ?> )</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $viewtab == 'likes'? 'active':'';?>" id="profile-tab-4" data-bs-toggle="tab" href="#profile-4" aria-controls="profile">
                                <span class="nav-icon">
                                    <i class="icon-xl la la-thumbs-up"></i> 
                                </span>
                                <span class="nav-text">Likes ( <?php echo $totallikes;  ?> )</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link <?php echo $viewtab == 'dislikes'? 'active':'';?>" id="contact-tab-4" data-bs-toggle="tab" href="#contact-4" aria-controls="contact">
                                <span class="nav-icon">
                                    <i class="icon-xl la la-thumbs-down"></i> 
                                </span>
                                <span class="nav-text">Dislikes ( <?php echo $totaldislikes;  ?> )</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-5" id="myTabContent4">
                        <div class="tab-pane fade <?php echo $viewtab == 'comment'? 'show active':'';?>" id="home-4" role="tabpanel" aria-labelledby="home-tab-4">

                            <?php
                            foreach ($commentslist as $cmtdata) { 
                                ?>
                                <div class="d-flex align-items-center pb-2">
                                    <div class="symbol symbol-75 symbol-light mr-5">
                                        <div class="symbol-group symbol-hover">
                                            <?php if($cmtdata['created_by_image']!=""){ ?>
                                                <div class="m-r-15 text-center userimageround">
                                                    <img src="<?php echo $fileupload_path.$cmtdata['created_by_image']; ?>" alt="image" class="img-radius img-50">
                                                </div>
                                            <?php }else{ ?>
                                                <div class="img-radius img-50 align-top m-r-15 text-center userimageround symbol-primary" title="">
                                                    <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($cmtdata['display_name']); ?></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div> 
                                    <!--begin::Section-->
                                    <div class="d-flex flex-column flex-grow-1">
                                        <!--begin::Title-->

                                        <!--end::Title-->
                                        <!--begin::Desc-->
                                        <span class="text-dark-50 font-weight-normal font-size-lg"><?php echo $cmtdata['display_name'];?></span>
                                        <!--begin::Desc-->
                                    </div>
                                    <!--end::Section-->
                                </div>

                                <div class="d-flex align-items-center p-b-10">
                                    <span class="text-dark-50 font-weight-normal font-size-sm pl-9"> Comments : </span>
                                    <!--begin::Section-->
                                    <div class="d-flex flex-column flex-grow-1">
                                        <!--begin::Title-->

                                        <!--end::Title-->
                                        <!--begin::Desc-->
                                        <span class="font-weight-normal font-size-lg pl-9"><?php echo $cmtdata['comments'];?></span>
                                        <!--begin::Desc-->
                                    </div>
                                    <!--end::Section-->
                                </div>

                            <?php } ?>
                        </div>
                        <div class="tab-pane fade <?php echo $viewtab == 'likes'? 'show active':'';?>"" id="profile-4" role="tabpanel" aria-labelledby="profile-tab-4"> <?php
                        foreach ($likelist as $cmtdata) { 
                            ?>
                            <div class="d-flex align-items-center p-b-10">
                                <div class="symbol symbol-75 symbol-light mr-5">
                                    <div class="symbol-group symbol-hover">
                                        <?php if($cmtdata['created_by_image']!=""){ ?>
                                            <div class="m-r-15 text-center userimageround">
                                                <img src="<?php echo $fileupload_path.$cmtdata['created_by_image']; ?>" alt="image" class="img-radius img-50">
                                            </div>
                                        <?php }else{ ?>
                                            <div class="img-radius img-50 align-top m-r-15 text-center userimageround symbol-primary" title="">
                                                <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($cmtdata['display_name']); ?></span>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div> 
                                <!--begin::Section-->
                                <div class="d-flex flex-column flex-grow-1">
                                    <!--begin::Title-->

                                    <!--end::Title-->
                                    <!--begin::Desc-->
                                    <span class="text-dark-50 font-weight-normal font-size-lg"><?php echo $cmtdata['display_name'];?></span>
                                    <!--begin::Desc-->
                                </div>
                                <!--end::Section-->
                            </div> <?php } ?></div>
                            <div class="tab-pane fade <?php echo $viewtab == 'dislikes'? 'show active':'';?>"" id="contact-4" role="tabpanel" aria-labelledby="contact-tab-4"> <?php
                            foreach ($dislikelist as $cmtdata) { 
                                ?>
                                <div class="d-flex align-items-center p-b-10">
                                    <div class="symbol symbol-75 symbol-light mr-5">
                                        <div class="symbol-group symbol-hover">
                                            <?php if($cmtdata['created_by_image']!=""){ ?>
                                                <div class="m-r-15 text-center userimageround">
                                                    <img src="<?php echo $fileupload_path.$cmtdata['created_by_image']; ?>" alt="image" class="img-radius img-50">
                                                </div>
                                            <?php }else{ ?>
                                                <div class="img-radius img-50 align-top m-r-15 text-center userimageround symbol-primary" title="">
                                                    <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($cmtdata['display_name']); ?></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div> 
                                    <!--begin::Section-->
                                    <div class="d-flex flex-column flex-grow-1">
                                        <!--begin::Title-->

                                        <!--end::Title-->
                                        <!--begin::Desc-->
                                        <span class="text-dark-50 font-weight-normal font-size-lg"><?php echo $cmtdata['display_name'];?></span>
                                        <!--begin::Desc-->
                                    </div>
                                    <!--end::Section-->
                                </div> <?php } ?></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!--end: Search Form -->

            <div class="modal-footer">
                <button type="button" class="btn btn-primary mr-2"  data-bs-dismiss="modal" >Close</button>
            </div>
            <!-- </div> -->
        </form>
    </div>