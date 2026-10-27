<?php
    $data2["where"] = array('is_active'=> '0');
   $costdata = urlencode(json_encode($data2));
   $costurl = API."?mode=gettabledetailswithconditions&table=application_widgets&data=".$costdata;
   $costdata = callAPI($costurl);
   $costress = json_decode($costdata, 1);
   $appwizdata = callAPI($appwizurl);
   $appwizdata = json_decode($appwizdata, 1);
   foreach ($costress as  $value) {
       $wizz[$value['id']] = $value['page_id'];
   }
   $stausresult = ViewStatusData();
   $current_year = date('Y');
   $yesterday_date = date('Y-m-d', strtotime('-1 days'));


         $vurl = API."?mode=togetkpitaskstatus&company_id=".$_SESSION['company_id']."&is_active=0&emp_id=".$_SESSION['emp_id']."&balance_date=".$yesterday_date."&assesment_year=".$_SESSION['config']['assessment_year'];
              $configesponse = callAPI($vurl);
              $kpi_tasks = json_decode($configesponse,1);

              $employee_id = $kpi_data[0]['emp_id'];


              $emp_dob_details = $kpi_tasks['emp_dob_details'];
              $emp_wed_details = $kpi_tasks['emp_wed_details'];

              $wishes_count = count((array)$emp_dob_details) + count((array)$emp_wed_details);


          /* Review Data Start */
           $reviewwr["where"] = array('company_id'=>$_SESSION['company_id'],'response_type'=>0,'user_id'=>$_SESSION['userid']);

           $reviewcon = urlencode(json_encode($reviewwr));
           $reviewurl = API."?mode=gettabledetailswithconditions&table=transaction_approval_logs&data=".$reviewcon;
           $reviewdata = callAPI($reviewurl);
           $reviewdata = json_decode($reviewdata, 1);
           $total_review_count= array();
           foreach ($reviewdata as  $reviewda) {
             if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
                  $total_review_count['now'][]=$reviewda;
              }else{
                  $total_review_count['due'][]=$reviewda;
              }
           }
            /* Review Data End */

              $todate = date('Y-m-d', strtotime('+6 days'));
              $fromdate = date('Y-m-d',strtotime('-120 days'));

              $vurl = API."?mode=togetkpitaskhistories&company_id=".$company_id."&is_active=0&user_id=".$_SESSION['userid']."&task_date_from=".$fromdate."&task_date_to=".$todate;
              $configesponse = callAPI($vurl);
              $kpi_data_list = json_decode($configesponse,1);

               foreach ($kpi_data_list as  $task) {
                  if($task['task_date'] == date('Y-m-d')){
                    $count_task['now'][] = $task;
                }elseif(strtotime($task['task_date']) < strtotime(date('Y-m-d'))){
                    $count_task['due'][] = $task;
                }else{
                   $count_task['after'][] = $task;
                }
               }


              $total_tasks = count((array)$kpi_data_list);




              $holiday_list = array();
              $holidaywr["where"] = array('company_id'=> $_SESSION['company_id'],'DATE_FORMAT(holiday_date,"%Y")'=>$current_year);
              $holidaycon = urlencode(json_encode($holidaywr));
              $holidayurl = API."?mode=gettabledetailswithconditions&table=holidays&data=".$holidaycon;
              $holidaydata = callAPI($holidayurl);
              $holidaydata = json_decode($holidaydata,1);
              $total_holidays = count((array)$holidaydata);


              $count_array['reviews']= $total_reviews;
              $count_array['tasks']= $total_tasks;
              $count_array['holidays']= $total_holidays;
              $count_array['wishes']= $wishes_count;
              $count_headings[] = $count_array;
              ?>
<script type="text/javascript">
   function displaystausdrop(id,table,status_id,prefix=''){
        var selector = prefix ? "#dropdownlist_"+prefix+"_"+id : "#dropdownlist_"+id;
        $.ajax({
        url: "ajax.php",
        data:'page=current_status_drop_table&id='+id+'&current_status='+status_id+'&table='+table,
        type: "POST",
        success:function(data){
        console.log(data);
        $(selector).html(data);
        },
        error:function (){}
        });

    }
   function statuspopup(status,id,table_name){
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal({ show: true});
            },
            error:function (){}
        });
   }

   function popupfeedmsg(sender,receiver,msgtitle){
   alert(sender);
   jQuery.ajax({
   url: "views/new_from.php",
   data:'option=COM_INSERT_FEEDS&display=1&sending_emp_id='+sender+'&receiving_emp_id='+receiver+'&msg_title='+msgtitle,
         //data:'option=COM_INSERT_FEEDS?display=1&sending_emp_id='+sender;
         type: "POST",
         success:function(data){
             $('#dynamic_popup1').html(data);
             $('#kt_modal_dynamic_popup').modal({ show: true});
         },
         error:function (){}
        });


   }

</script>

<div class="d-flex flex-column-fluid">
   <!--begin::Container-->
   <div class="container-fluid">
      <!--end::Row-->
      <!--begin::Row-->
      <div class="tab-content m-0 p-0">
         <div class="row">
            <div class="col-lg-4 draggable-zone">
               <div class="card card-custom gutter-b draggable" role="region" style="">
                  <div class="card-header">
                     <div class="card-title">
                        <h3 class="card-label">Announcements</h3>
                     </div>
                     <div class="card-toolbar">
                        <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                        <i class="ki ki-menu"></i>
                        </a>
                     </div>
                  </div>
                  <div class="card-body scroll-pull" data-scroll="true" style="height:250px;">
                     <div class="d-flex align-items-start justify-content-start flex-grow-1 bg-light-warning p-8 card-rounded flex-grow-1 position-relative">
                        <div class="d-flex flex-column align-items-start flex-grow-1 bgi-no-repeat" style="background-position: calc(100% + 0.5rem) bottom; background-size: auto 70%; background-image: url(assets/media/svg/humans/custom-3.svg)">
                           <div class="p-1 flex-grow-1">
                              <?php
                                 $anouncement =  API."?mode=togetlatestannouncement";
                                 $anouncement_list = callAPI($anouncement);
                                 $anouncement_list = json_decode($anouncement_list,1);
                                 ?>
                              <p class="text-dark-50 font-weight-bold mt-3"><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
                              <a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-danger font-weight-bold py-2 px-2"><?php echo $anouncement_list['button_label']; ?></a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
        <?php if($_SESSION['config']['kpi_mode']==1){ ?>
            <div class="col-lg-4 draggable-zone">
         <?php include_once 'custom_list_dash_board_tasks.php'; ?>
      </div>
      <?php } ?>
      <!-- reviews starts -->
            <div class="col-lg-4 draggable-zone">
               <div class="card card-custom gutter-b draggable" role="region" style="">
                  <div class="card-header">
                     <div class="card-title">
                        <h3 class="card-label">Reviews
                        <span class="label label-lg label-danger font-weight-bold" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Over Due  "><?php echo count((array)$total_review_count['due']); ?></span>
                        <span class="label label-lg label-warning font-weight-bold" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Due now"><?php echo count((array)$total_review_count['now']); ?></span>
                         </h3>
                     </div>
                     <div class="card-toolbar">
                        <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                        <i class="ki ki-menu"></i>
                        </a>
                     </div>
                  </div>
                  <div class="card-body scroll-pull" data-scroll="true" style="height:250px;">
                     <?php
                        foreach ($reviewdata as  $reviewda) {
                            $Notification_Message = $reviewda['note']!='' ? $reviewda['note'] : viewTitle($reviewda['transaction_table']);
                            $Notification_DateTime = notificationdatesdiff($reviewda['request_date']) ." ago";
                            $Notification_Table = $reviewda['transaction_table'];
                            $Notification_Table_Id = $reviewda['transaction_id'];
                            $transactionwr= array('is_active'=> '0','id'=>$Notification_Table_Id);
                            foreach ($reviewdata as  $reviewda) {
                             if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
                                  $class_text = 'text-warning';
                              }else{
                                   $class_text = 'text-danger';
                              }

                           }
                           $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name',$Notification_Table,1);
                             $nrid = deocdeId("id:".$Notification_Table_Id);
                             $link = "index.php?option=".$tabledata['insert_page_name']."&Rid=".$nrid;
                            $transactioncon = urlencode(json_encode($transactionwr));
                            $transactionurl = API."?mode=gettabledetailswithconditions&table=".$Notification_Table."&data=".$transactioncon;
                            $transactiondata = callAPI($transactionurl);
                            $transactiondata = json_decode($transactiondata, 1);
                            $transactiondata = $transactiondata['0'];
                            $icon_initials = initials($_SESSION['users'][$transactiondata['created_by']]);
                            $icon_initials_name = $_SESSION['users'][$transactiondata['created_by']];
                            $current_status = $transactiondata['current_status'];

                         ?>
                     <div style="box-shadow: 0px 1px 9px #cccccc;" class="alert alert-custom alert-white fade show" role="alert">
                        <div class="alert-text">
                           <div class="d-flex align-items-center mb-0">
                              <!--begin::Symbol-->
                              <div class="symbol symbol-35 symbol-success mr-3">
                                 <span class="label label-lg label-success"><?php echo $icon_initials; ?></span>
                              </div>
                              <!--end::Symbol-->
                              <!--begin::Text-->
                              <div class="d-flex flex-column font-weight-bold w-100">
                                 <a href="<?php echo $link;?>" class="text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><?php echo $Notification_Message; ?></a>
                                 <span class="font-size-sm <?php echo $class_text; ?>"><?php echo $Notification_DateTime; ?></span>
                              </div>
                              <div class="btn-group dropleft">
                                 <button onclick="displaystausdrop(<?php echo $Notification_Table_Id; ?>,<?php echo "'".$Notification_Table."'"; ?>,<?php echo $current_status; ?>,'rev')" class="label label-sm label-danger " style="color: <?php echo "#".$stausresult[$current_status]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$current_status]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$current_status]['background_color']); ?>;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button" data-content="<?php echo $stausresult[$current_status]['name']; ?>">
                                 </button>
                                 <div class="dropdown-menu" id="dropdownlist_rev_<?php echo $Notification_Table_Id; ?>">
                                 </div>
                              </div>
                              <!--end::Text-->
                           </div>
                        </div>
                        <div class="alert-close">
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true"><i class="ki ki-close"></i></span>
                           </button>
                        </div>
                     </div>
                     <?php  } ?>
                  </div>
               </div>
            </div>
            <!-- tasks starts -->
            <div class="col-lg-4 draggable-zone">
               <div class="card card-custom gutter-b draggable" role="region" style="">
                  <div class="card-header">
                     <div class="card-title">
                        <h3 class="card-label">Tasks due in next 7 days
                        <span class="label label-lg label-danger font-weight-bold" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Over Due  "><?php echo count((array)$count_task['due']); ?></span>
                        <span class="label label-lg label-warning font-weight-bold" data-container="body" data-toggle="tooltip" data-placement="bottom" title="Due now"><?php echo count((array)$count_task['now']); ?></span>
                        <span class="label label-lg label-primary font-weight-bold " data-container="body" data-toggle="tooltip" data-placement="bottom" title="Not Yet Due"><?php echo count((array)$count_task['after']); ?></span>
                        </h3>
                     </div>
                     <div class="card-toolbar">
                        <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                        <i class="ki ki-menu"></i>
                        </a>
                     </div>
                  </div>
                  <div class="card-body scroll-pull" data-scroll="true" style="height:250px;">
                     <?php
                        foreach ($kpi_data_list as  $task) {
                            $rtext ='xyz';
                            $Notification_Message = $task['id']."-".$task['name'];
                            $Notification_Table_Id = $task['id'];
                            $Notification_Table = "employee_kpi_task_histories";
                            $current_status = $task['current_status'];
                            $text_masege="";
                            if($task['task_date'] == date('Y-m-d')){
                                $class_text = 'text-warning';

                            }elseif(strtotime($task['task_date']) < strtotime(date('Y-m-d'))){
                                $class_text = 'text-danger';

                            }else{
                                $class_text = 'text-muted';
                                $text_masege= "Due in next";
                                $rtext ='ago';
                            }


                            $Notification_DateTime = notificationdatesdiff($task['task_date']) ." ago";
                                  $created_userid = $task['created_by'];
                                  $icon_initials = initials($_SESSION['users'][$created_userid]);
                                  ?>
                     <div style="box-shadow: 0px 1px 9px #cccccc;" class="alert alert-custom alert-white fade show" role="alert">
                        <div class="alert-text">
                           <div class="d-flex align-items-center mb-0">
                              <!--begin::Symbol-->
                              <div class="symbol symbol-35 symbol-success mr-3">
                                 <span class="label label-lg label-success"><?php echo $icon_initials; ?></span>
                              </div>
                              <!--end::Symbol-->
                              <!--begin::Text-->
                              <div class="d-flex flex-column font-weight-bold w-100">
                                 <a href="#" class=" text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><?php echo $Notification_Message; ?></a>
                                 <span class=" font-size-sm <?php echo $class_text; ?>"><?php echo $text_masege." ".str_replace($rtext,"",$Notification_DateTime); ?></span>
                              </div>
                              <div class="btn-group dropleft">
                                 <button onclick="displaystausdrop(<?php echo $Notification_Table_Id; ?>,<?php echo "'".$Notification_Table."'"; ?>,<?php echo $current_status; ?>,'task')" class="label label-sm label-danger " style="color: <?php echo "#".$stausresult[$current_status]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$current_status]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$current_status]['background_color']); ?>;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" type="button" data-content="<?php echo $stausresult[$current_status]['name']; ?>">
                                 </button>
                                 <div class="dropdown-menu" id="dropdownlist_task_<?php echo $Notification_Table_Id; ?>">
                                 </div>
                              </div>
                              <!--end::Text-->
                           </div>
                        </div>
                        <div class="alert-close">
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                           <span aria-hidden="true"><i class="ki ki-close"></i></span>
                           </button>
                        </div>
                     </div>
                     <?php }  ?>
                  </div>
               </div>
            </div>


      <div class="col-lg-4 draggable-zone">
         <div class="card card-custom gutter-b draggable" role="region" style="">
            <div class="card-header">
               <div class="card-title">
                  <h3 class="card-label">Holidays List <span class="label label-xl label-light-success label-rounded font-weight-bolder"><?php echo $total_holidays; ?></span></h3>
               </div>
               <div class="card-toolbar">
                  <a href="" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                  <i class="ki ki-menu"></i>
                  </a>
               </div>
            </div>
            <div class="card-body scroll-pull" data-scroll="true" style="height:250px;">
               <?php
                  foreach ($holidaydata as  $holiday) {

                      $Notification_Message = $holiday['holiday_name'];
                      $Notification_DateTime = $holiday['holiday_date'];
                      ?>
               <div style="box-shadow: 0px 1px 9px #cccccc;" class="alert alert-custom alert-white fade show" role="alert">
                  <div class="alert-text">
                     <div class="d-flex align-items-center mb-0">
                        <!--begin::Symbol-->
                        <div class="symbol symbol-35 symbol-success mr-3">
                           <span class="label label-lg label-success"><?php echo initials($Notification_Message); ?></span>
                        </div>
                        <!--end::Symbol-->
                        <!--begin::Text-->
                        <div class="d-flex flex-column font-weight-bold">
                           <a href="#" class="text-dark text-hover-primary mb-0 font-size-sm"><?php echo $Notification_Message; ?></a>
                           <span class="text-muted font-size-sm"><?php echo $Notification_DateTime; ?></span>
                        </div>
                        <!--end::Text-->
                     </div>
                  </div>
                  <div class="alert-close">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true"><i class="ki ki-close"></i></span>
                     </button>
                  </div>
               </div>
               <?php  } ?>
            </div>
         </div>
      </div>
      <!-- wishes starts-->
      <div class="col-lg-4 draggable-zone">
         <div class="card card-custom gutter-b draggable" role="region" style="">
            <div class="card-header">
               <div class="card-title">
                  <h3 class="card-label">
                     Birthdays & Anniversaries <span class="label label-xl label-light-success label-rounded font-weight-bolder"><?php echo $wishes_count; ?></span>
                  </h3>
               </div>
               <div class="card-toolbar">
                  <a href="#" class="btn btn-icon btn-sm btn-hover-light-primary draggable-handle">
                  <i class="ki ki-menu"></i>
                  </a>
               </div>
            </div>

            <div class="card-body scroll-pull" data-scroll="true" style="height:250px;">


            <?php
               if(!empty($emp_dob_details)) {

                  foreach ($emp_dob_details as  $dob) {

                     $display_name = $dob['display_name'];
                     $date_of_birth = $dob['date_of_birth'];
                     $display_photo = $dob['image'];
                     $receiver_emp_id = $dob['id'];
                     $sender_emp_id = $employee_id;
                     $dob = date('d/m',strtotime($date_of_birth))."/".date('Y');
                     $msg_title = 'Birthday on '.$dob;
                     $disp_text = $display_name ."\n" . "Birthday on ".$date_of_birth;
                     ?>
               <div style="box-shadow: 0px 1px 9px #cccccc;" class="alert alert-custom alert-white fade show" role="alert">
                  <div class="alert-text">
                     <div class="d-flex align-items-center mb-0">
                        <!--begin::Symbol-->
                       <div class="symbol symbol-35 symbol-success mr-3" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                 <?php if($display_photo) {?>
                                 <img src="uploads/all/<?php echo $display_photo; ?>" alt="Details">
                                 <?php } else { ?>
                                 <span class="symbol-label symbol-success font-weight-bold"><?php echo initials($display_name); ?></span>
                                 <?php } ?>
                              </div>
                        <!--end::Symbol-->
                        <!--begin::Text-->
                        <div class="d-flex flex-column font-weight-bold">
                           <a href="#" class="text-dark text-hover-primary mb-0 font-size-sm"><?php echo $display_name; ?></a>
                           <span class="text-muted font-size-sm"><?php echo $msg_title; ?></span>
                        </div>
                        <!--end::Text-->
                     </div>
                  </div>
                  <div class="alert-close">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true"><i class="ki ki-close"></i></span>
                     </button>
                  </div>
               </div>
               <?php  } ?>

            <?php } ?>
            <!--Anniversaries -->
            <?php
               if(!empty($emp_wed_details)) {
                  foreach ($emp_wed_details as  $wed) {

                     $display_name = $wed['display_name'];
                     $wedding_date = $wed['wedding_date'];
                     $display_photo = $wed['image'];
                     $receiver_emp_id = $wed['id'];
                     $sender_emp_id = $employee_id;
                     $wed = date('d/m',strtotime($wedding_date))."/".date('Y');
                     $msg_title = 'Birthday on '.$wed;
                     $disp_text = $display_name ."\n" . "Birthday on ".$wedding_date;
                     ?>
               <div style="box-shadow: 0px 1px 9px #cccccc;" class="alert alert-custom alert-white fade show" role="alert">
                  <div class="alert-text">
                     <div class="d-flex align-items-center mb-0">
                        <!--begin::Symbol-->
                       <div class="symbol symbol-35 symbol-success mr-3" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                 <?php if($display_photo) {?>
                                 <img src="uploads/all/<?php echo $display_photo; ?>" alt="Details">
                                 <?php } else { ?>
                                 <span class="symbol-label symbol-success font-weight-bold"><?php echo initials($display_name); ?></span>
                                 <?php } ?>
                              </div>
                        <!--end::Symbol-->
                        <!--begin::Text-->
                        <div class="d-flex flex-column font-weight-bold">
                           <a href="#" class="text-dark text-hover-primary mb-0 font-size-sm"><?php echo $display_name; ?></a>
                           <span class="text-muted font-size-sm"><?php echo $msg_title; ?></span>
                        </div>
                        <!--end::Text-->
                     </div>
                  </div>
                  <div class="alert-close">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                     <span aria-hidden="true"><i class="ki ki-close"></i></span>
                     </button>
                  </div>
               </div>
               <?php  } ?>

            <?php } ?>
         </div>

         </div>

      </div>



      <?php $roles = json_decode($_SESSION['role_id'],1);
            if(in_array(ADMIN_ROLE,$roles)){
      ?>
      <div class="col-lg-6 draggable-zone">
      </div>
      <?php } ?>
   </div>
</div>
<div class="tab-pane fade" id="tab_forms_widget_2" role="tabpanel" aria-labelledby="tab_forms_widget_2">Tab tab_forms_widget_2 2</div>
<div class="tab-pane fade" id="tab_forms_widget_3" role="tabpanel" aria-labelledby="tab_forms_widget_3">Tab tab_forms_widget_3 2</div>
<div class="tab-pane fade" id="tab_forms_widget_4" role="tabpanel" aria-labelledby="tab_forms_widget_4">Tab tab_forms_widget_4 2</div>
<div class="tab-pane fade" id="tab_forms_widget_5" role="tabpanel" aria-labelledby="tab_forms_widget_5">Tab tab_forms_widget_5 2</div>
<div class="tab-pane fade" id="tab_forms_widget_6" role="tabpanel" aria-labelledby="tab_forms_widget_6">Tab forms_widget_tab_6 2</div>
</div>
</div>
<!--end::Container-->



 <div id="kt_modal_KTDatatable_remote" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog " >
        <div class="modal-content"  id="popup1">

        </div>
    </div>
</div>