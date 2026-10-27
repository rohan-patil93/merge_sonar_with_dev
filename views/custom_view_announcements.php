<?php
$fileupload_path = "uploads/all/";
$emp_role_id = json_decode($_SESSION['role_id']);
$addcondition = '';
if($_GET['CID']!=''){
 $addcondition = '&category_id='.$_GET['CID'];
}

if($_GET['AID']!=''){
 $addcondition = '&announcement_id='.$_GET['AID'];
}

$url = API."?mode=getannouncements&company_id=".$_SESSION['company_id']."&userid=".$_SESSION['userid'].$addcondition;
$response = callAPI($url);
$announcement_details = json_decode($response,1);
$configdata = $announcement_details['config'];
$announcementdata = $announcement_details['announcements'];
$totalannounces = count((array)$announcementdata);
$announcement_categories = $announcement_details['announcement_categories'];
$userannounces = $announcement_details['userannounces'];
$live_my_announcemnts = count((array)$userannounces);
$annoucements_rights_roleid = $announcement_details['annoucements_rights_roleid'];
$current_year_announcements = $announcement_details['current_year_announcements'];
$current_month_announcements = $announcement_details['current_month_announcements'];

$total_current_year_announces = count((array)$current_year_announcements);

$vurl = API."?mode=getrecognitionsettings&table=".TBL_EMPLOYEE_RECOGNITIONS."&userid=".$_SESSION['userid'];
$recog_data = callAPI($vurl);
$recogdata = json_decode($recog_data,1);
$emplist = $recogdata['employees'];
$badges = $recogdata['badges'];
$announcement_reslut_view = 'Y';
?>
<link rel="stylesheet" href="assets/css/custom_view_announcements.css">
<script src="assets/js/custom_view_announcements.js"></script>
<div class="d-flex flex-column flex-column-fluid" id="kt_content">
   <!--begin::Subheader-->
   <div class="container-fluid">
      <div class="card card-custom">
         <div class="card-header">
            <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
               <div class="d-flex align-items-center flex-wrap mr-2">
                  <h5 class="text-dark font-weight-bolder mt-2 mb-5 mr-5">ANNOUNCEMENTS</h5>
                  <div class="subheader-separator subheader-separator-ver mt-2 mb-5 mr-5 bg-gray-200"></div>
               </div>
               <?php if(in_array(ST_ADMIN_ANNOUNCEMENT, $emp_role_id)) { ?>
                  <div class="d-flex align-items-center">
                     <a href="#" class="btn btn-primary font-weight-bolder mr-2 announcements-new-btn">
                        + NEW ANNOUNCEMENTS
                     </a>
                  </div>
               <?php } ?>
            </div>
         </div>
      </div>

      <div class="">
         <!--begin::Container-->
         <!--begin::Education-->
         <div class="row">
            <!--begin::Aside-->
            <div class="col-lg-3" style="" id="">
               <div class="col-xxl-12">
                  <!--begin::Nav Panel Widget 4-->
                  <div class="card card-custom gutter-b">
                     <!--begin::Body-->
                     <div class="card-body">
                        <!--begin::Wrapper-->
                        <div class="d-flex justify-content-between flex-column h-100">
                           <!--begin::Container-->
                           <div class="h-100 row">
                              <!--begin::Header-->
                              <div class="d-flex flex-column flex-center">
                                 <!--begin::Image-->
                                 <div class="bgi-no-repeat rounded text-center" >
                                    <img src="<?php echo $fileupload_path.$configdata['display_logo'];?>" class="max-w-200px" alt="Logo">
                                 </div>
                                 <!--end::Image-->
                                 <!--begin::Title-->
                                 <a href="#" class="card-title font-weight-bolder text-dark-75 text-hover-primary font-size-h4 m-0 pt-7 pb-1 mt-2"><?php echo $configdata['name'];?></a>
                                 <!--end::Title-->
                                 <!--begin::Text-->
                                 <div class="font-weight-bold text-dark-50 font-size-sm pb-7"><?php echo $configdata['location'];?></div>
                                 <!--end::Text-->
                              </div>
                              <!--end::Header-->
                              <!--begin::Body-->
                              <div class="pt-1">
                                 <!--begin::Item-->
                                 <?php foreach ($announcement_categories as $ackey => $acvalue) { ?>
                                    <div class="d-flex align-items-center pb-9">
                                       <!--begin::Symbol-->
                                       <div class="symbol symbol-45 symbol-light mr-4">
                                          <span class="symbol-label">
                                             <span class="svg-icon svg-icon-2x svg-icon-dark-50">

                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                   <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <rect x="0" y="0" width="24" height="24" />
                                                      <rect fill="#000000" opacity="0.3" x="13" y="4" width="3" height="16" rx="1.5" />
                                                      <rect fill="#000000" x="8" y="9" width="3" height="11" rx="1.5" />
                                                      <rect fill="#000000" x="18" y="11" width="3" height="9" rx="1.5" />
                                                      <rect fill="#000000" x="3" y="13" width="3" height="7" rx="1.5" />
                                                   </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                             </span>
                                          </span>
                                       </div>
                                       <!--end::Symbol-->
                                       <!--begin::Text-->
                                       <div class="d-flex flex-column flex-grow-1 p-10">
                                          <a href="index.php?option=<?php echo $_GET['option'];?>&CID=<?php echo $acvalue['id']; ?>" class="text-dark-75 text-hover-primary mb-1 font-size-lg font-weight-bolder"><?php echo $acvalue['announcement_category_name']; ?></a>

                                       </div>
                                       <!--end::Text-->
                                       <!--begin::label-->
                                       <span class="form-label badge badge-lg bg-info" data-toggle="tooltip" title="Number of Announcements"><?php echo $acvalue['category_count'];?></span>
                                       <!--end::label-->
                                    </div>
                                 <?php } ?>
                                 <!--end::Item-->
                              </div>
                              <!--end::Body-->
                           </div>
                           <!--eng::Container-->
                           <!--begin::Footer-->

                        <!--end::Footer-->
                     </div>
                     <!--end::Wrapper-->
                  </div>
                  <!--end::Body-->
               </div>
               <!--end::Nav Panel Widget 4-->

               <div class="card card-custom gutter-b">
                  <!--begin::Header-->
                  <div class="card-header border-0 pt-5">
                     <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-dark">My Announcements</span>
                        <span class="text-muted mt-3 font-weight-bold font-size-sm"><?php echo $live_my_announcemnts; ?> Announcements</span>
                     </h3>

                  </div>
                  <!--end::Header-->
                  <!--begin::Body-->
                  <div class="card-body pt-4">
                     <!--begin::Container-->
                     <div>
                        <?php
                        foreach ($userannounces as $myannounce) {

                         ?>
                           <div class="mt-5">
                              <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary font-size-xl"><?php echo $myannounce['announcement_heading']; ?></a>
                              <!--begin::Item-->
                              <div class="d-flex align-items-center mb-2">
                                 <!--begin::Symbol-->
                                 <?php if($myannounce['image_url']!='') { ?>
                                    <div class="symbol mr-5 pt-1">
                                       <div class="symbol-label min-w-65px min-h-100px" style="background-image: url(<?php echo $fileupload_path.$myannounce['image_url'];?>)"></div>
                                    </div>
                                 <?php } ?>
                                 <!--end::Symbol-->
                                 <!--begin::Info-->
                                 <div class="d-flex flex-column">
                                    <!--begin::Title-->
                                    <!--end::Title-->
                                    <!--begin::Text-->
                                    <span><?php echo substr($myannounce['announcement_message'],0,120).'...';?>
                                    <a href="index.php?option=<?php echo $_GET['option'];?>&AID=<?php echo $myannounce['id']; ?>" >More
                                    </a>
                                 </span>
                                 <!--end::Action-->
                              </div>
                              <!--end::Info-->
                           </div>
                           <button type="button" class=" btn btn-light font-weight-bolder font-size-sm ">Expires: <?php echo date('d M, Y', strtotime($myannounce['expiry_date']));?></button>
                           <div class="text-dark-50 font-weight-bolder font-size-sm mb-3 mt-2">
                                <i class="ti-comments text-primary m-r-10 text-dark f-w-600"></i>
                              <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $myannounce['id'];?>" data-view-type="comment">
                                 <span class="btn font-weight-bolder label label-xl label-success px-3 py-2 mr-5" style="vertical-align: middle;" data-toggle="tooltip" title="Comments">
                                    <?php echo $myannounce['totalcomments'];?>  </span>
                                 </a>
                                 <i class="icon-xxl fa fa-thumbs-up"></i>
                                 <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $myannounce['id'];?>" data-view-type="likes">
                                    <span class="btn font-weight-bolder label label-xl  label-info px-3 py-2 mr-5" style="vertical-align: middle;" data-toggle="tooltip" title="Likes">
                                       <?php echo $myannounce['totallikes'];?>  </span>
                                    </a>
                                    <i class="icon-xxl  fa fa-thumbs-down"></i>
                                    <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $myannounce['id'];?>" data-view-type="dislikes">
                                       <span class="btn font-weight-bolder label label-xl label-warning px-3 py-2 mr-5" style="vertical-align: middle" data-toggle="tooltip" title="Dislikes">
                                          <?php echo $myannounce['totaldislikes'];?> </span>
                                       </a>
                                    </div>
                                 </div>
                                 <div class="separator separator-solid mt-5 mb-4"></div>
                                 <!--end::Item-->
                              <?php } ?>
                           </div>
                           <!--end::Container-->
                        </div>
                        <!--end::Body-->
                     </div>



                  </div>
               </div>
               <!--end::Aside-->
               <!--begin::Content-->
               <div class="col-lg-6" style="">
                  <div class="row">
                     <div class="col-xxl-12">




                        <?php
                        foreach ($announcementdata as  $annvalue) { ?>
                           <div class="card card-custom gutter-b">
                              <!--begin::Body-->
                              <div class="card-body">
                                 <!--begin::Top-->
                                 <div class="d-flex align-items-center">
                                    <!--begin::Symbol-->
                                    <div class="symbol symbol-75 symbol-light mr-5">
                                       <div class="symbol-group symbol-hover">
                                          <?php if($annvalue['created_by_image']!=""){ ?>
                                             <div class="symbol symbol-45 symbol-circle" data-toggle="tooltip" title="<?php echo $annvalue['display_name'];?>">
                                                <img src="uploads/all/<?php echo $annvalue['created_by_image']; ?>" class="img-50" alt="Details">
                                             </div>
                                          <?php }else{ ?>
                                             <div class="symbol symbol-45 symbol-circle symbol-primary" data-toggle="tooltip" title="<?php echo $annvalue['display_name'];?>">
                                                <span class="symbol-label font-size-h3 font-weight-boldest"><?php echo initials($display_name); ?></span>
                                             </div>
                                          <?php } ?>
                                       </div>
                                    </div>
                                    <!--end::Symbol-->
                                    <!--begin::Info-->
                                    <div class="d-flex flex-column flex-grow-1">
                                       <?php if(ST_RECOGINITION_ANNOUNCEMENT_CATEGORY_ID== $annvalue['announcement_category_id']) { ?>
                                          <a href="#" class="text-dark-75 text-hover-primary mb-1 font-size-lg font-weight-bolder"><?php echo ST_RECOGNITION_MSG.' '.$annvalue['announcement_heading']; ?></a>
                                       <?php } else { ?>
                                          <a href="#" class="text-dark-75 text-hover-primary mb-1 font-size-lg font-weight-bolder"><?php echo $annvalue['announcement_heading']; ?></a>
                                       <?php } ?>
                                       <div class="d-flex">
                                          <div class="d-flex align-items-center pr-5">
                                             <span class="svg-icon svg-icon-md svg-icon-primary pr-1">

                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                   <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <rect x="0" y="0" width="24" height="24" />
                                                      <path d="M5.5,4 L9.5,4 C10.3284271,4 11,4.67157288 11,5.5 L11,6.5 C11,7.32842712 10.3284271,8 9.5,8 L5.5,8 C4.67157288,8 4,7.32842712 4,6.5 L4,5.5 C4,4.67157288 4.67157288,4 5.5,4 Z M14.5,16 L18.5,16 C19.3284271,16 20,16.6715729 20,17.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,17.5 C13,16.6715729 13.6715729,16 14.5,16 Z" fill="#000000" />
                                                      <path d="M5.5,10 L9.5,10 C10.3284271,10 11,10.6715729 11,11.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,11.5 C4,10.6715729 4.67157288,10 5.5,10 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,12.5 C20,13.3284271 19.3284271,14 18.5,14 L14.5,14 C13.6715729,14 13,13.3284271 13,12.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z" fill="#000000" opacity="0.3" />
                                                   </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                             </span>
                                             <span class="text-muted font-weight-bold">Posted: <?php echo date('d M, Y', strtotime($annvalue['start_date']));?></span>
                                          </div>
                                          <div class="d-flex align-items-center">
                                             <span class="svg-icon svg-icon-md svg-icon-primary pr-1">

                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                   <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                      <rect x="0" y="0" width="24" height="24" />
                                                      <path d="M12,22 C7.02943725,22 3,17.9705627 3,13 C3,8.02943725 7.02943725,4 12,4 C16.9705627,4 21,8.02943725 21,13 C21,17.9705627 16.9705627,22 12,22 Z" fill="#000000" opacity="0.3" />
                                                      <path d="M11.9630156,7.5 L12.0475062,7.5 C12.3043819,7.5 12.5194647,7.69464724 12.5450248,7.95024814 L13,12.5 L16.2480695,14.3560397 C16.403857,14.4450611 16.5,14.6107328 16.5,14.7901613 L16.5,15 C16.5,15.2109164 16.3290185,15.3818979 16.1181021,15.3818979 C16.0841582,15.3818979 16.0503659,15.3773725 16.0176181,15.3684413 L11.3986612,14.1087258 C11.1672824,14.0456225 11.0132986,13.8271186 11.0316926,13.5879956 L11.4644883,7.96165175 C11.4845267,7.70115317 11.7017474,7.5 11.9630156,7.5 Z" fill="#000000" />
                                                   </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                             </span>
                                             <span class="text-muted font-weight-bold">Expires: <?php echo date('d M, Y', strtotime($annvalue['expiry_date']));?> </span>
                                          </div>
                                       </div>
                                    </div>
                                    <!--end::Info-->
                                    <!--begin::Dropdown-->
                                    <!--end::Dropdown-->
                                 </div>
                                 <!--end::Top-->
                                 <!--begin::Bottom-->
                                 <div class="pt-3">
                                    <!--begin::Text-->
                                    <?php if(ST_RECOGINITION_ANNOUNCEMENT_CATEGORY_ID== $annvalue['announcement_category_id']) {
                                       $remp = json_decode($annvalue['remarks'],1);
                                       $first_emp = $remp['recognition_to'];
                                       $given_emp = $remp['recognition_from'];
                                       $badgeid = $remp['badge_id'];
                                       ?>
                                       <div class="col-lg-7 mt-5">
                                          <div class="card-stretch gutter-b">
                                             <div class="newcrds card card-custom custom-ribbon">
                                                <div class="card-header card-header-right ribbon ribbon-top ribbon-ver">
                                                   <div class="ribbon-target custom" style="top: -2px; left:0px;">
                                                    <?php if($emplist[$first_emp]['image']!="") { ?>
                                                      <img src="uploads/all/<?php echo $emplist[$first_emp]['image']; ?>"  class="img-radius img-50 align-top m-r-15" style="z-index: 9;" alt="Attachment Preview">
                                                   <?php } else { ?>
                                                      <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"  style="width: 51px; left: 4px; top: 4px; background-color: white;color: black;z-index: 9;"><?php echo initials($emplist[$first_emp]['display_name']); ?></span>
                                                   <?php } ?>
                                                </div>

                                             <h4 class="card-title">
                                               <?php echo $badges[$badgeid]['badge_name']; ?>&nbsp; <img class="ml-3" src="uploads/all/<?php echo $badges[$badgeid]['image']; ?>" style="width: 60px;height: auto;border-radius: 50%;z-index: 9;margin: -2px 0 0 -9px;
                                               " alt="Attachment Preview">
                                            </h4>
                                         </div>
                                         <div class="custom-text">
                                          <?php echo $emplist[$first_emp]['display_name']; ?>
                                       </div>
                                       <div class="card-body">
                                          <?php echo $annvalue['announcement_message']; ?>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           <?php } else { ?>
                              <p class="text-dark-75 font-size-lg font-weight-normal ">
                                <?php echo $annvalue['announcement_message'];  ?></p>
                              <!--end::Text-->
                              <!--begin::Video-->
                              <?php if($annvalue['image_url']!='') { ?>
                                 <div class="bgi-no-repeat bgi-size-cover rounded min-h-295px text-center" > <img src="uploads/all/<?php echo $annvalue['image_url'];?>" style = "max-width:680px;" alt="Attachment Preview" /></div>
                              <?php } elseif($annvalue['video_url']!='')  { ?>
                                 <div class="embed-responsive embed-responsive-16by9">

                                    <iframe width="640" height="360" src="https://www.youtube.com/embed/h5H_JzGhENc" title="Taskda |  Manage Your Tasks 4 Easy Steps" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                 </div>
                           } ?>
                           <?php } ?>

                           <?php
                              if($annvalue['survey_type']=='Y'){
                                    $survy_data = json_decode($annvalue['survey_details'],1);
                                    if($survy_data['type']=='POLIS'){  ?>
                                       <div class="survey-container" style="width: 80%;">


                                         <div class="options">
                                           <button class="option-btn option-yes announcements-polis-btn <?php if($annvalue['survey_result']==1){ echo "option-yes-hover";
} ?>" data-anu-id="<?php echo $annvalue['id']; ?>" data-mes-type="3" data-like-type="1">👍 <?php echo $survy_data['data']['0']['label']; ?> </button>
                                           <button class="option-btn option-no announcements-polis-btn <?php if($annvalue['survey_result']==2){ echo "option-no-hover";
} ?>" data-anu-id="<?php echo $annvalue['id']; ?>" data-mes-type="3" data-like-type="2">👎 <?php echo $survy_data['data']['1']['label']; ?></button>
                                        </div>

                                        <div class="result-bar">
                                           <div class="yes-fill" style="width: 65%;"></div>
                                        </div>
                                        <div class="counts">
                                           <div><?php echo $survy_data['data']['0']['label']; ?>: 65%</div>
                                           <div><?php echo $survy_data['data']['1']['label']; ?>: 35%</div>
                                        </div>
                                     </div>
                                    <?php }


                                    if($survy_data['type']=='RANKING'){
                                        $da_va =0;
                                            if($annvalue['survey_result']!=0){
                                            $da_va = 10 * ($annvalue['survey_result']);
                                            }
                                      ?>
                                       <div class="mt-5" style="width: 80%;">
                                         <div class="rating-group">
                                            <span class="rating-label">Kindly provide your ranking using the slider below</span>
                                            <input aria-label="Input field" type="range" class="rating-range announcements-ranking-slider" min="<?php echo $survy_data['data']['start']; ?>" max="<?php echo $survy_data['data']['end']; ?>" id="ran_<?php echo $annvalue['id']; ?>" value="<?php echo $annvalue['survey_result']; ?>" data-anu-id="<?php echo $annvalue['id']; ?>" data-mes-type="3" style="background: linear-gradient(90deg, rgb(34, 197, 94) <?php echo $da_va; ?>%, rgb(229, 231, 235) <?php echo $da_va; ?>%);">
                                            <div class="rating-value">Rating: <span id="ran_<?php echo $annvalue['id']; ?>_spn"><?php echo $annvalue['survey_result']; ?></span> / <?php echo $survy_data['data']['end']; ?></div>
                                         </div>

                                      </div>
                                    <?php }
                                    if($survy_data['type']=='CHOICES'){  ?>
                                        <div class="question">Choices Any One
                                          <ul class="answer-list">
                                             <?php $i=1; foreach((array)$survy_data['data'] as $survy_da){ ?>
                                                <li class="answer-row">
                                                  <span class="number"><?php echo $i; ?>.</span>
                                                  <div class="answer-item announcements-select-answer <?php if($annvalue['survey_result']==$survy_da['value']){ echo "selected";
} ?>" id="che_<?php echo $annvalue['id']; ?>" data-anu-id="<?php echo $annvalue['id']; ?>" data-mes-type="3" data-like-type="<?php echo $survy_da['value']; ?>"><?php echo $survy_da['label']; ?></div>
                                              </li>
                                              <?php  $i++;
} ?>
                                          </ul>
                                      </div>
                                    <?php }
                                }
                           ?>
                           <!--end::Video-->
                           <!--begin::Action-->
                           <input type="hidden" id="totalcomments<?php echo $annvalue['id'];?>" value="<?php echo $annvalue['totalcomments'];?>">
                           <input type="hidden" id="totallikes<?php echo $annvalue['id'];?>" value="<?php echo $annvalue['totallikes'];?>">
                           <input type="hidden" id="totaldislikes<?php echo $annvalue['id'];?>" value="<?php echo $annvalue['totaldislikes'];?>">
                           <div class="mt-3">

                              <a href="#" class="mt-0 announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="comment" data-toggle="tooltip" title="Comments">
                                 <i class="icon-xxl fa fa-comment <?php echo $annvalue['highlight_comment']=='1'? 'text-primary':'';?>"></i>
                                 <span class="btn font-weight-bolder label label-xl label-success px-3 py-2" style="vertical-align: middle;"  id="spantotalcomments<?php echo $annvalue['id'];?>">
                                    <?php echo $annvalue['totalcomments'];?> </span> <span class="btn btn-hover-text-primary btn-hover-icon-primary btn-sm btn-text-dark-50 bg-hover-light-primary rounded font-weight-bolder font-size-sm p-2"> Comments </span>
                                 </a>
                                 <a href="#" class="announcements-save-summary-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-field-name="like">
                                    <i id="iconspantotallikes<?php echo $annvalue['id'];?>" class="cursor-pointer icon-xxl  fa fa-thumbs-up <?php echo $annvalue['highlight_like']=='1'? 'text-primary':'';?> ml-5" data-toggle="tooltip" title="Like this"></i>
                                 </a>
                                 <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="likes">
                                    <span class="btn font-weight-bolder label label-xl label-info px-3 py-2" style="vertical-align: middle;" id="spantotallikes<?php echo $annvalue['id'];?>" data-toggle="tooltip" title="Likes">
                                       <?php echo $annvalue['totallikes'];?> </span>
                                       <span class="btn btn-hover-text-primary btn-hover-icon-primary btn-sm btn-text-dark-50 bg-hover-light-primary rounded font-weight-bolder font-size-sm p-2"> Likes </span>
                                    </a>
                                    <a href="#" class="announcements-save-summary-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-field-name="dislike">
                                       <i id="iconspantotaldislikes<?php echo $annvalue['id'];?>" class="cursor-pointer icon-xxl <?php echo $annvalue['highlight_dislike']=='1'? 'text-primary':'';?> fa fa-thumbs-down ml-5" data-toggle="tooltip" title="Dislike this"></i>
                                    </a>
                                    <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="dislikes">
                                       <span class="btn font-weight-boldest label label-xl label-warning px-3 py-2" style="vertical-align: middle;" id="spantotaldislikes<?php echo $annvalue['id'];?>" data-toggle="tooltip" title="Dislikes">
                                          <?php echo $annvalue['totaldislikes'];?> </span>
                                          <span class="btn btn-hover-text-primary btn-hover-icon-primary btn-sm btn-text-dark-50 bg-hover-light-primary rounded font-weight-bolder font-size-sm p-2"> Dislikes </span>
                                       </a>
                                    </div>
                                 </div>
                                 <!--end::Bottom-->
                                 <!--begin::Separator-->
                                 <div class="separator separator-solid mt-3"></div>
                                 <!--end::Separator-->
                                 <!--begin::Editor-->





                                 <form class="position-relative">
                                    <input type="hidden" id="userid" value="<?php echo $_SESSION['userid']; ?>">
                                    <div class="input-group input-group-button mt-2">
                                       <textarea aria-label="Text area" id="input<?php echo $annvalue['id'];?>" style="overflow:hidden" class="form-control p-2 pr-10 resize-none width1" rows="2" placeholder="Any Comments?"></textarea>
                                       <button class="input-group-text addon-style announcements-save-summary-btn" type="button" data-announcement-id="<?php echo $annvalue['id'];?>" data-field-name="comments">
                                          <span class=""><i class="ti ti-send"></i> Save</span>
                                       </button>
                                    </div>


                                 </a>
                              </form>
                           </div>
                        </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
                  <div class="col-lg-3">
                     <div class="card card-custom gutter-b">
                        <div class="">
                           <div class="card-header border-0 pt-7">
                              <h3 class="card-title align-items-start flex-column float-left float-left-none">
                                 <span class="card-label font-weight-bold font-size-h4 text-dark-75">History</span>
                                 <span class="text-muted mt-3 font-weight-bold font-size-sm">
                                    <span class="text-primary font-weight-bolder"><?php echo $total_current_year_announces;?> Announcements</span>
                                 </span>
                              </h3>
                              <div class="card-toolbar float-right mb-3 float-right-none">
                                 <ul class="nav md-tabs history_custom_tab">
                                    <li class="nav-item ml-0">
                                       <a class="nav-link py-2 px-4 active font-weight-bolder font-size-sm" data-bs-toggle="tab" href="#kt_tab_pane_1_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Year</a>
                                    </li>
                                    <li class="nav-item">
                                       <a class="nav-link py-2 px-4 font-weight-bolder font-size-sm" data-bs-toggle="tab" href="#kt_tab_pane_2_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Month</a>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                           <div class="card-body pt-1">
                              <div class="tab-content mt-5" id="myTabLIist18">
                                 <div class="tab-pane fade show active" id="kt_tab_pane_1_1" role="tabpanel" aria-labelledby="kt_tab_pane_1_1">
                                    <div class="form">
                                       <?php foreach ($current_year_announcements as  $annvalue) { ?>
                                       <div class="d-flex align-items-center">
                                          <div class="d-flex flex-column flex-grow-1">
                                             <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary font-size-lg mb-1"><?php echo $annvalue['announcement_heading']; ?></a>
                                             <span class="text-dark-50 font-weight-normal font-size-sm">
                                                <?php echo substr($annvalue['announcement_message'],0,120).'...'; ?>
                                                <a href="index.php?option=<?php echo $_GET['option'];?>&AID=<?php echo $annvalue['id']; ?>" >More
                                                </a>
                                             </span>
                                          </div>
                                       </div>
                                       <button type="button" class=" btn btn-light font-weight-bolder font-size-sm ">Expires: <?php echo date('d M, Y', strtotime($annvalue['expiry_date']));?></button>
                                       <div class="text-dark-50 font-weight-bolder font-size-sm pb-7 pt-3">
                                          <i class="icon-xxl la la-comment-o" ></i>
                                          <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="comment">
                                             <span class="btn font-weight-bolder label label-lg label-success px-3 py-3 mr-5 mb-4" style="vertical-align:middle" data-toggle="tooltip" title="Comments">
                                                <?php echo $annvalue['totalcomments'];?>  </span>
                                          </a>
                                          <i class="icon-xxl la la-thumbs-up"></i>
                                          <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="likes">
                                             <span class="btn font-weight-bolder label label-lg label-info px-3 py-3 mr-5 mb-4" style="vertical-align:middle" data-toggle="tooltip" title="Likes">
                                                <?php echo $annvalue['totallikes'];?>
                                             </span>
                                          </a>
                                          <i class="icon-xxl la la-thumbs-down"></i>
                                          <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="dislikes">
                                             <span class="btn font-weight-bolder label label-lg label-warning px-3 py-3 mr-5 mb-4" style="vertical-align:middle" data-toggle="tooltip" title="Dislikes">
                                                <?php echo $annvalue['totaldislikes'];?> </span>
                                          </a>
                                       </div>
                                       <?php } ?>
                                    </div>
                                 </div>
                                 <div class="tab-pane fade" id="kt_tab_pane_2_2" role="tabpanel" aria-labelledby="kt_tab_pane_2_2">
                                    <div class="form">
                                       <?php foreach ($current_month_announcements as  $annvalue) { ?>
                                       <div class="d-flex align-items-center">
                                          <div class="d-flex flex-column flex-grow-1">
                                             <a href="#" class="text-dark-75 font-weight-bolder font-size-lg text-hover-primary mb-1"><?php echo $annvalue['announcement_heading']; ?></a>
                                             <span class="text-dark-50 font-weight-normal font-size-sm">
                                                <?php echo substr($annvalue['announcement_message'],0,120).'...'; ?>
                                                <a href="index.php?option=<?php echo $_GET['option'];?>&AID=<?php echo $annvalue['id']; ?>" > More
                                                </a>
                                             </span>
                                          </div>
                                       </div>
                                       <button type="button" class=" btn btn-light font-weight-bolder font-size-sm ">Expires: <?php echo date('d M, Y', strtotime($annvalue['expiry_date']));?></button>
                                       <div class="text-dark-50 font-weight-bolder font-size-sm pb-7 pt-3">
                                          <i class="icon-xxl la la-comment-o"></i>
                                          <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="comment">
                                             <span class="btn font-weight-bolder label label-lg label-success px-3 py-3 mr-5 mb-4" style="vertical-align:middle" data-toggle="tooltip" title="Comments">
                                                   <?php echo $annvalue['totalcomments'];?>  </span>
                                          </a>
                                          <i class="icon-xxl la la-thumbs-up"></i>
                                          <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="likes">
                                             <span class="btn font-weight-bolder label label-lg label-info px-3 py-3 mr-5 mb-4" style="vertical-align:middle" data-toggle="tooltip" title="Likes">
                                                      <?php echo $annvalue['totallikes'];?>  </span>
                                          </a>
                                          <i class="icon-xxl la la-thumbs-down"></i>
                                          <a href="#" class="announcements-popup-btn" data-announcement-id="<?php echo $annvalue['id'];?>" data-view-type="dislikes">
                                             <span class="btn font-weight-bolder label label-lg label-warning px-3 py-3 mr-5 mb-4" style="vertical-align:middle" data-toggle="tooltip" title="Dislikes">
                                                         <?php echo $annvalue['totaldislikes'];?> </span>
                                          </a>
                                       </div>
                                       <?php } ?>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
                                 </div>
                              </div>
                           </div>
                           </div>
                        </div>
                        <div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
                           <div class="modal-dialog modal-lg modal-dialog-scrollable"  >
                              <div class="modal-content"  id="popup2">
                              </div>
                           </div>
                        </div>
