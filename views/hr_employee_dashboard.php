 <?php
 $_SESSION['apply_js']['todo'] = '1';
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
 $total_tasks_achieve = 15;
 $completed_tasks_achieve = 30;
 $employee_id = $kpi_data[0]['emp_id'];
 $emp_dob_details = nearestDateFirstShow($kpi_tasks['emp_dob_details'],'date_of_birth');
 $emp_wed_details = nearestDateFirstShow($kpi_tasks['emp_wed_details'],'wedding_date');
 $wishes_count = count((array)$emp_dob_details) + count((array)$emp_wed_details);
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
           $holidaydata = nearestDateFirstShow($holidaydata,'holiday_date');
           $total_holidays = count((array)$holidaydata);


           $count_array['reviews']= $total_reviews;
           $count_array['tasks']= $total_tasks;
           $count_array['holidays']= $total_holidays;
           $count_array['wishes']= $wishes_count;
           $count_headings[] = $count_array;

           $reviewwrw["where"] = array('company_id'=>$_SESSION['company_id'],'id'=>$_SESSION['emp_id']);
           $reviewwrw["field"] = "image";
           $reviewconw = urlencode(json_encode($reviewwrw));
           $reviewurlw = API."?mode=gettabledetailswithconditions&table=employees&data=".$reviewconw;
           $reviewdataw= callAPI($reviewurlw);
           $reviewdataw = json_decode($reviewdataw, 1);

           $stausresult = ViewStatusData();

           $listcompliancesurl = API."?mode=getcompliancesdashbord&type=employeetask&emp_id=".$_SESSION['emp_id']."&date=".date('Y-m-d');
           $listcompliancesres = callAPI($listcompliancesurl);
           $listcompliances = json_decode($listcompliancesres, 1);
           $compliancetaskhistory = $listcompliances['compliance_task_histories'];

         $reviewurl = API."?mode=getriviewsdata&response_type=0";
           $reviewdata = callAPI($reviewurl);
           $reviewdata = json_decode($reviewdata, 1);
           $togetlist = API."?mode=togethremployeedetails&emp_id=".$_SESSION['emp_id'];
           $wedata = callAPI($togetlist);
           $data = json_decode($wedata, 1);
           foreach($data['leave_type'] as $leave_types){
               $leave_type[$leave_types['leave_category_id']] = $leave_types['leave_type_name'];
           }
           ?>
<style>

        #date-list {
            display: block;


        }

        .date-item {
            background-color: #ffffff;
            color: #000000;
            border: 1px solid #ccc;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 18px;
            font-size: 12px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-top{
            background-color: #ffa500;
            border: 1px solid #ffa500;
            color: #FFFFFF;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 20px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-danger{
            background-color: #ff0000;
            border: 1px solid #ff0000;
            color: #FFFFFF;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 18px;
            font-size: 12px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-att{
            background-color: #008000;
            border: 1px solid #008000;
            color: #FFFFFF;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 20px;
            display: inline-block;
            vertical-align: top;
        }
        .date-item-gray{
            background-color: #dcdcdc;
            border: 1px solid #dcdcdc;
            color: #000000;
            border-radius: 50%;
            margin: 1.5%;
            padding: 2px 0;
            text-align: center;
            min-width: 25px;
            height: 25px;
            line-height: 18px;
            font-size: 12px;
            display: inline-block;
            vertical-align: top;
        }

    </style>
           <script type="text/javascript">
               function showrecognitioncard(recogids) {
        jQuery.ajax({
            url: "views/custom_view_employee_recognition_popup.php",
            data:'recogid='+recogids,
            type: "GET",
            success:function(data){
                $('#popup5').html(data);
                 $(".updaterolemodal").modal("show");
            },
            error:function (){}
        });
    }
               function displaystausdrop(id,table,status_id){
                   $.ajax({
                       url: "ajax.php",
                       data:'page=current_status_drop_table&id='+id+'&current_status='+status_id+'&table='+table,
                       type: "POST",
                       success:function(data){
                           console.log(data);
                           $("#dropdownlist_"+id).html(data);
                       },
                       error:function (){}
                   });

               }
               function statuspopup(status,id,table_name,auth,task=""){
                   if(auth == 0){
                       if(task == "task"){
                           if($('#checkstausinput'+id).prop("checked") == true){
                               jQuery.ajax({
                                   url: "views/custom_insert_status_popup_task.php",
                                   data:'table='+table_name+'&status='+status+'&rid='+id,
                                   type: "POST",
                                   success:function(data){
                                       $('#popup1125').html(data);
                                       $("#kt_modal_KTDatatable_remotenewdemo").modal({
                                           backdrop: 'static',
                                           keyboard: false
                                       });

                                       $('#kt_modal_KTDatatable_remotenewdemo').modal("show");


                                   },
                                   error:function (){}
                               });
                           }

                       }else{
                           jQuery.ajax({
                               url: "views/custom_insert_status_popup.php",
                               data:'table='+table_name+'&status='+status+'&rid='+id,
                               type: "POST",
                               success:function(data){
                                   $('#popup1').html(data);
                                   $('#kt_modal_KTDatatable_remote').modal("show");
                               },
                               error:function (){}
                           });
                       }
                   }else{
                       if(task == "task"){
                           $('#checkstausinput'+id).prop('checked', false);
                           $("#checkstausid"+id).removeClass("done-task");
                       }
                       Swal.fire({
                           icon: 'error',
                           title: 'Oops...',
                           text: auth
                       })
                   }
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
             $('#kt_modal_dynamic_popup').modal("show");
         },
         error:function (){}
 });
               }

               function reviewsrelods(){
                   jQuery.ajax({
                       url: "views/custom_dashboard_reviews.php",
                       data:'',
                       type: "GET",
                       success:function(data){
                           $('.reviewtableaax').html(data);
                       },
                       error:function (){}
                   });
               }
               function dynamicinsertpopup(option){
                   jQuery.ajax({
                       url: "views/new_from.php",
                       data:'option='+option,
                       type: "POST",
                       success:function(data){
                           $('#dynamic_popup1').html(data);
                           $('#kt_modal_dynamic_popup').modal("show");
                       },
                       error:function (){}
                   });
               }
               function getTaskCheckList(id,aryposition){
                   jQuery.ajax({
                       url: "views/custom_insert_compliance_answer_popup.php",
                       data:'id='+id+_2'&aryposition="+aryposition,
                       type: "POST",
                       success:function(data){
                           $('#dynamic_popup1').html(data);
                           $('#kt_modal_dynamic_popup').modal("show");
                       },
                       error:function (){}
                   });
               }

               function statuspopupnew(id,table_name,status,transaction_id,workflow_id,type){
                   jQuery.ajax({
                       url: "views/custom_insert_status_popup_new.php",
                       data:'table='+table_name+'&status='+status+'&rid='+id+'&transaction_id='+transaction_id+'&workflow_id='+workflow_id+"&type="+type,
                       type: "POST",
                       success:function(data){
                           $('#popup1').html(data);
                           $('#kt_modal_KTDatatable_remote').modal("show");
                       },
                       error:function (){}
                   });
               }
           </script>
           <?php $awdith =3;
               if($_SESSION['admin_dash_list']['reports_to']>0){
                   $awdith =6;
               }
           ?>

           <div class="row" >
               <div class="col-lg-12">
                   <div class="row">
<div class="col-lg-<?php echo $awdith; ?> ">
    <div class="card social-card custom_hover_card position-relative custom-after" style="min-height:383px; background-image:url('../uploads/all/Announcements-bg.png'); background-repeat:no-repeat;background-size:cover; isplay: flex; flex-direction: column; align-items: center;
    justify-content: center;">
        <div class="card-body text-center" style="z-index: 1; flex:inherit;">
            <div class="text-facebook m-b-20 text-dark"><i class="ti-announcement "></i></div>
            <h3 class="text-facebook f-w-700 text-dark">Announcements</h3>
            <?php
            $anouncement =  API."?mode=togetlatestannouncement";
            $anouncement_list = callAPI($anouncement);
            $anouncement_list = json_decode($anouncement_list,1);
            ?>
            <p class="text-dark"><?php echo substr($anouncement_list['announcement_message'],0,250);?></p>
            <p class="m-b-0 m-t-15">
                <a  href="index.php?option=COM_VIEW_ANNOUNCEMENTS" class="btn btn-info waves-effect waves-light">
                    View
                </a>
            </p>
        </div>
    </div>
</div>
<div class="col-lg-<?php echo $awdith; ?>">
    <div class="card new-cust-card custom_hover_card pb-4">
        <div class="card-header">
            <h5>My Leaves</h5>
            <span class="form-label label label-success"><?php echo count($data['leave']); ?></span>
        </div>
        <div class="card-body custom-scroll" style=" height: 300px;">
            <?php
            if(!empty($data['leave'])){
                foreach ($data['leave'] as  $leave) {?>
                    <div class="align-middle m-b-35">
                        <?php if($data['employee']['image']!=""){ ?>
                        <img src="uploads/all/<?php echo $data['employee']['image']; ?>"   class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                        <?php }else{ ?>
                        <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($data['employee']['display_name']); ?></span>
                        <?php } ?>
                        <div class="d-inline-block">
                            <a class="f-14 f-w-600" href="index.php?option=COM_INSERT_LEAVE_REQUEST&Rid=<?php echo deocdeId('id:'.$leave['id']); ?>"><p class="f-15 mb-1 text-uppercase"><?php echo $leave_type[$leave['leave_type_id']]; ?></p></a>
                            <p class="text-muted m-b-0 f-13"><?php echo fromDate($leave['leave_from'])." To ".fromDate($leave['leave_to']); ?></p>
                        </div>
                    </div>
                <?php }
            }else{ ?>
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
         <div class="text-center px-4">
             <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
          </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="col-lg-<?php echo $awdith; ?>">
    <div class="card new-cust-card custom_hover_card">
        <div class="card-header">
            <h5>Recognition</h5>
            <span class="form-label label label-success"><?php echo count($data['recognitions']); ?></span>
        </div>
        <div class="card-body custom-scroll" style=" height: 320px;">
            <?php
            if(!empty($data['recognitions'])) {
                foreach ($data['recognitions'] as  $recognitions) { ?>
                    <div class="align-middle m-b-35">
                        <?php if($data['recognition_badges'][$recognitions['badge_id']]['image'] !=""){ ?>
                        <img src="uploads/all/<?php echo $data['recognition_badges'][$recognitions['badge_id']]['image']; ?>"   class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                        <?php }else{ ?>
                        <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($data['employee']['display_name']); ?></span>
                        <?php } ?>
                        <div class="d-inline-block">
                            <a class="f-15 f-w-600 mb-1 text-uppercase" href=""><span onclick="showrecognitioncard('<?php echo $recognitions['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $data['recognition_badges'][$recognitions['badge_id']]['badge_name']; ?></span></a>
                            <p class="text-muted m-b-0 f-13"><?php echo fromDate($recognitions['created_date']); ?></p>
                        </div>
                    </div>
                <?php }
            }else{ ?>
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
         <div class="text-center px-4">
             <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
          </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="col-lg-<?php echo $awdith; ?>">
    <div class="card new-cust-card custom_hover_card" style="min-height: 382px;">
        <div class="card-header">
            <h5>Attendance</h5>
            <span class="form-label label label-success m-l-5"><?php echo date("M"); ?></span>

        </div>
        <div class="card-body" style="height: 300px;">
            <div class="text-right">
                <span class="date-item-danger cursor-pointer f-12" style="width:10px;margin: 3px;" data-bs-toggle="tooltip" data-placement="bottom" title="Absent">A</span>
                <span class="date-item-att cursor-pointer f-12" style="width:10px;margin:4px 8px 0 12px;" data-bs-toggle="tooltip" data-placement="bottom" title="Present">P</span>
                <span class="date-item-gray cursor-pointer f-12" style="width:10px;margin-top:3px;margin-right: 11px;" data-bs-toggle="tooltip" data-placement="bottom" title="Week Off & Holidays">W</span>
            </div>
            <div id="date-list" class="d-block  mt-1" style="width: 250px;">
                <?php     $no_week = date('w', strtotime(date('Y')."-".date('m')."-01"));
                    $days_in_month = cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y'));
                    $listdays = array('M', 'T', 'W', 'T', 'F', 'S', 'S');
                    $no_dd = date('d');

                    $allweekdays = json_decode($data['weekatt']['working_days'],1);
                    foreach($data['attedance'] as $attedances){
                      $attdate[] = $attedances['attendace_date'];
                      $dat = date('d',strtotime($attedances['attendace_date']));
                      $attday[] = $dat;

                    }
                    foreach($listdays as $listday){?>
                        <div class="date-item-top f-w-600 f-10"><?php echo $listday; ?></div>
                    <?php  } ?>


                           <?php $cal =1; for($i=1;$i<$no_week;$i++){ ?>
                           <div class="date-item f-10" style="border-color: #FFFFFF;"></div>
                           <?php  $cal++;
} ?>
                           <?php
                               for($i=1;$i<=$days_in_month;$i++){
                               $dacss = "date-item";

                               if(in_array($i,(array)$attday)){
                                  $dacss = "date-item-att";
                               }
                                $wdate = date('Y')."-".date('m')."-".$i;
                                $ndate = date('Y-m-d',strtotime($wdate));
                                $wday = date('l',strtotime($wdate));
                               if( $no_dd>=$i && !in_array($i,(array)$attday) && in_array($wday, (array)$allweekdays) ){
                                  $dacss = "date-item-danger";
                               }
                               if( !in_array($wday, (array)$allweekdays) || in_array($ndate,(array)$data['month_holidays']) ){
                                  $dacss = "date-item-gray";
                               }
                               ?>
                               <div class="<?php echo $dacss; ?> f-10"><?php echo $i; ?></div>
                               <?php
                                   if($cal == 7){
                                       $cal = 0;
                                   }
                               ?>
                           <?php $cal++;
} ?>

                </div>
        </div>
    </div>
</div>


<div class="col-lg-<?php echo $awdith; ?> ">
    <div class="card new-cust-card custom_hover_card pb-4">
        <div class="card-header">
            <h5> Birthdays & Anniversaries
            </h5><span class="form-label label label-success"><?php echo $wishes_count; ?></span>
        </div>
        <div class="card-body custom-scroll" style=" height: 300px;">
            <?php
            if(!empty($emp_dob_details) || !empty($emp_wed_details)) {
                foreach ($emp_dob_details as  $dob) {
                    $display_name = $dob['display_name'];
                    $date_of_birth = $dob['date_of_birth'];
                    $display_photo = $dob['image'];
                    $receiver_emp_id = $dob['id'];
                    $sender_emp_id = $employee_id;
                    $dob = date('dS M',strtotime($date_of_birth));
                    $msg_title = 'Birthday on '.$dob;
                    $disp_text = $display_name ."\n" . "Birthday on ".$date_of_birth;
                    ?>
                    <div class="align-middle m-b-25">
                        <div class="d-inline-block" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <?php if($display_photo) {?>
                                <img src="uploads/all/<?php echo $display_photo; ?>"   class="img-radius img-40 align-top m-r-15" alt="User Profile">
                            <?php } else { ?>
                                <span class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-10 text-center"><?php echo initials($display_name); ?></span>
                            <?php } ?>
                        </div>
                        <div class="d-inline-block width73">
                            <a href="#!" class="text-danger text-capitalize"><h6 class="f-15 mb-1 text-uppercase f-w-600"><i class="fa fa-birthday-cake float-right text-primary"></i> <?php echo $display_name; ?></h6></a>
                            <p class="text-muted m-b-0 f-13"><?php echo $msg_title; ?></p>
                        </div>
                    </div>
                <?php  }   ?>
                <?php
                foreach ($emp_wed_details as  $wed) {
                    $wedding_date = $wed['wedding_date'];
                    $display_photo = $wed['image'];
                    $receiver_emp_id = $wed['id'];
                    $sender_emp_id = $employee_id;
                    $wed = date('d/m',strtotime($wedding_date))."/".date('y');
                    $msg_title = 'Anniversary on '.$wed;
                    $disp_text = $display_name ."\n" . "Birthday on ".$wedding_date;
                    ?>
                    <div class="align-middle m-b-35">
                        <div class="d-inline-block" onclick="popupfeedmsg(<?php echo $sender_emp_id;?>,<?php echo $receiver_emp_id;?>,'<?php echo $msg_title;?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <?php if($display_photo) {?>
                                <img src="uploads/all/<?php echo $display_photo; ?>"   class="img-radius img-40 align-top m-r-15" alt="User Profile">
                            <?php } else { ?>
                                <span class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($display_name); ?></span>
                            <?php } ?>
                        </div>

                        <div class="d-inline-block width73">
                            <a href="#!" class="text-danger text-capitalize"><h6 class="f-15 mb-1 text-uppercase f-w-600">  <i class="ti-gift float-right text-primary"></i>   <?php echo $display_name; ?></h6></a>
                            <p class="text-muted m-b-0 f-13"><?php echo $msg_title; ?></p>
                        </div>
                    </div>
                <?php }
            }else{
                echo "<h4><i class='fa fa-warning'></i> No Birthdays Available</h4>";
            } ?>
        </div>
    </div>
</div>
<div class="col-lg-<?php echo $awdith; ?>">
    <div class="card new-cust-card custom_hover_card">
        <div class="card-header">
            <h5>Welcome New Employees </h5>
            <span class="form-label label label-success"><?php echo count($data['joins']); ?></span>
        </div>
        <div class="card-body custom-scroll" style=" height: 320px;">
            <?php
            if(!empty($data['joins'])) {
                foreach ($data['joins'] as  $joins) { ?>
                    <div class="align-middle m-b-35">
                        <?php if($joins['image']!=""){ ?>
                        <img src="uploads/all/<?php echo $joins['image']; ?>"   class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                        <?php }else{ ?>
                        <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($joins['display_name']); ?></span>
                        <?php } ?>
                        <div class="d-inline-block">

                            <a href="#!"><h6 class="f-15 mb-1 text-uppercase f-w-600"><?php echo $joins['display_name']; ?></h6></a>
                            <p class="text-muted m-b-0 f-13"><?php echo fromDate($task['joining_date']); ?></p>
                        </div>
                    </div>
                <?php }
            }else{ ?>
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
         <div class="text-center px-4">
             <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
          </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="col-lg-<?php echo $awdith; ?>">
    <div class="card new-cust-card custom_hover_card">
        <div class="card-header">
            <h5>Tasks</h5>
            <span class="form-label label label-success"><?php echo count($data['task']); ?></span>
        </div>
        <div class="card-body custom-scroll" style=" height: 320px;">
            <?php if(!empty($data['task'])) {
                foreach ($data['task'] as  $task) { ?>
                    <div class="align-middle m-b-35">
                        <?php if($data['employee']['image']!=""){ ?>
                        <img src="uploads/all/<?php echo $data['employee']['image']; ?>"   class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                        <?php }else{ ?>
                        <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($data['employee']['display_name']); ?></span>
                        <?php } ?>
                        <div class="d-inline-block">
                            <a href="#!"><h6 class="f-15 mb-1 text-uppercase f-w-600"><?php echo $task['event_name']; ?></h6></a>
                            <p class="text-muted m-b-0 f-13"><?php echo fromDate($task['created_date']); ?></p>
                        </div>
                    </div>
                <?php }
            }else{ ?>
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
         <div class="text-center px-4">
             <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
          </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="col-lg-<?php echo $awdith; ?>">
    <div class="card new-cust-card custom_hover_card">
        <div class="card-header">
            <h5>Work From Home</h5>
            <span class="form-label label label-success"><?php echo count($data['wfh']); ?></span>
        </div>
        <div class="card-body custom-scroll" style=" height: 320px;">
            <?php
            if(!empty($data['wfh'])) {
                foreach ($data['wfh'] as  $wfh) { ?>
                    <div class="align-middle m-b-35">
                        <?php if($data['employee']['image']!=""){ ?>
                        <img src="uploads/all/<?php echo $data['employee']['image']; ?>"   class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                        <?php }else{ ?>
                        <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($data['employee']['display_name']); ?></span>
                        <?php } ?>
                        <div class="d-inline-block">
                            <a href="index.php?option=COM_INSERT_REQUEST_WORK_FROM_HOME&Rid=<?php echo deocdeId('id:'.$wfh['id']); ?>"><h6 class="f-15 mb-1 f-w-600 text-uppercase"><?php echo $wfh['display_name']; ?></h6></a>
                            <p class="text-muted m-b-0 f-13"><?php echo fromDate($wfh['effective_from_date']). " To ".fromDate($wfh['effective_to_date']); ?></p>
                        </div>
                    </div>
                <?php }
            }else{ ?>
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
         <div class="text-center px-4">
             <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
          </div>
            <?php } ?>
        </div>
    </div>
</div>
<div class="col-lg-<?php echo $awdith; ?>">
    <div class="card new-cust-card custom_hover_card pb-4">
        <div class="card-header">
            <h5>Holidays List</h5>
            <span class="form-label label label-success"><?php echo $total_holidays; ?></span>
        </div>
        <div class="card-body custom-scroll" style=" height: 300px;">
            <?php
            if(!empty($holidaydata)) {
                foreach ($holidaydata as  $holiday) {
                    $Notification_Message = $holiday['holiday_name'];
                    $Notification_DateTime = fromDate($holiday['holiday_date']);
                    ?>
                    <div class="align-middle m-b-20 m-l-20">

                        <div class="d-inline-block">
                            <a href="#!"><h6 class="f-15 mb-1 f-w-600 text-uppercase"><?php echo $Notification_Message; ?></h6></a>
                            <p class="text-muted m-b-0 f-13"><?php echo $Notification_DateTime; ?></p>
                        </div>
                    </div>
                <?php }
            }else{ ?>
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
         <div class="text-center px-4">
             <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
          </div>
            <?php } ?>
        </div>
    </div>
</div>


<div class="col-lg-6">
   <div class="col-lg-12">
      <div class="card table-card">
         <div class="card-header">
            <h5>Reviews</h5>
         </div>
         <div class="card-body custom-scroll" style="height: 600px;">
            <div>
               <div class="col-lg-12 col-xl-12">
                  <ul class="nav  md-tabs infisuite_tab mt-2 p-2">
                     <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#home7pending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-hourglass-2"></i>  Pending  <span class="form-label badge bg-danger"><?php echo count((array)$reviewdata['pending']); ?></span></a>
                        <div class="slide"></div>
                     </li>

                     <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#profile7approve" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="fa fa-thumbs-up"></i> Approve  <span class="form-label badge bg-warning ml-2"><?php echo count((array)$reviewdata['approve']); ?></span></a>
                        <div class="slide"></div>
                     </li>
                  </ul>
                  <div class="tab-content card-body">
                     <div class="tab-pane active" id="home7pending" role="tabpanel">
                        <table class="table table-hover m-b-0">
                           <thead>
                              <tr>
                                 <th scope="col">#</th>
                                 <th scope="col">Description</th>


                              </tr>
                           </thead>
                           <tbody>
                              <?php
                              if(!empty($reviewdata['pending'])){  $k=1;  foreach ($reviewdata['pending'] as  $reviewda){
                                 $Notification_DateTime = notificationdatesdiff($reviewda['request_date']) ." ago";
                                 $Notification_Table = $reviewda['table'];
                                 $Notification_Table_Id = $reviewda['transaction_id'];
                                 if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
                                    $class_text = 'text-warning';
                                 }else{
                                    $class_text = 'text-danger';
                                 }
                                 $getpagename = getPagenametotable($reviewda['table'],'approval_page_name');
                                 if($getpagename != ""){
                                    $pagename = $getpagename;
                                 }else{
                                    $pagename = "COM_INSERT_".ucwords($reviewda['table']);
                                 }
                                 $nrid = deocdeId("id:".$reviewda['transaction_id']);
                                 $link = "index.php?option=".$pagename."&Rid=".$nrid;
                                 $icon_initials = initials($_SESSION['users'][$reviewda['created_by']]);
                                 $icon_initials_name = $_SESSION['users'][$reviewda['created_by']];
                                 $current_status = $reviewda['current_status'];
                                 $Notification_Message = ucwords(str_replace("_"," ",($reviewda['table'])))." - ".$icon_initials_name;
                                 ?>
                                 <tr>

                                    <td><?php echo $k; ?></td>
                                    <td>
                                       <div class="d-inline-block align-middle display-flex">
                                          <span  alt="user" class="img-radius img-50 align-top m-r-15 custom_txt_round p-2 f-12 text-center" ><?php echo $icon_initials; ?></span>
                                          <div class="d-inline-block">
                                             <a href="<?php echo $link;?>" class="text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><h6 class="f-15 mb-1 f-w-600"><?php echo $Notification_Message; ?></h6></a>
                                             <p class="text-muted m-b-0 f-13 <?php echo $class_text; ?>"><?php echo $Notification_DateTime; ?></p>
                                          </div>
                                       </div>
                                    </td>

                                 </tr>
                                 <?php  $k++;
}
} else{
                                    echo "<tr><td colspan='3'><h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Reviews Available</h4></td></tr>";
                                 } ?>
                              </tbody>
                           </table>
                        </div>
                        <div class="tab-pane" id="profile7approve" role="tabpanel">
                           <table class="table table-hover m-b-0">
                              <thead>
                                 <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Description</th>


                                 </tr>
                              </thead>
                              <tbody>
                                 <?php
                                 if(!empty($reviewdata['approve'])){ $k = 1;  foreach ($reviewdata['approve'] as $reviewda){
                                    $Notification_DateTime = notificationdatesdiff($reviewda['request_date']) ." ago";
                                    $Notification_Table = $reviewda['table'];
                                    $Notification_Table_Id = $reviewda['transaction_id'];
                                    if(date('Y-m-d') == date('Y-m-d',strtotime($reviewda['request_date']))){
                                       $class_text = 'text-warning';
                                    }else{
                                       $class_text = 'text-danger';
                                    }
                                    $getpagename = getPagenametotable($reviewda['table'],'approval_page_name');
                                    if($getpagename != ""){
                                       $pagename = $getpagename;
                                    }else{
                                       $pagename = "COM_INSERT_".ucwords($reviewda['table']);
                                    }
                                    $nrid = deocdeId("id:".$reviewda['transaction_id']);
                                    $link = "index.php?option=".$pagename."&Rid=".$nrid;
                                    $icon_initials = initials($_SESSION['users'][$reviewda['created_by']]);
                                    $icon_initials_name = $_SESSION['users'][$reviewda['created_by']];
                                    $current_status = $reviewda['current_status'];
                                    $Notification_Message = ucwords(str_replace("_"," ",($reviewda['table'])))." - ".$icon_initials_name;
                                    ?>
                                    <tr>

                                        <td><?php echo $k; ?></td>
                                       <td>
                                          <div class="d-inline-block align-middle display-flex">
                                             <span  alt="user" class="img-radius img-50 align-top m-r-15 custom_txt_round p-2 f-12 text-center" ><?php echo $icon_initials; ?></span>
                                             <div class="d-inline-block">
                                                <a href="<?php echo $link;?>" class="text-hover-primary mb-0 font-size-sm <?php echo $class_text; ?>"><h6><?php echo $Notification_Message; ?></h6></a>
                                                <p class="text-muted m-b-0 <?php echo $class_text; ?>"><?php echo $Notification_DateTime; ?></p>
                                             </div>
                                          </div>
                                       </td>

                                    </tr>
                                    <?php  $k++;
}
} else{
                                       echo "<tr><td colspan='3'><h4 class='p-40 text-center'><i class='fa fa-warning'></i> No Reviews Available</h4></td></tr>";
                                    } ?>
                                 </tbody>
                              </table>
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


<div id="kt_modal_KTDatatable_remote1 " class="modal fade updaterolemodal" role="dialog" >
    <div class="modal-dialog  " id="modalsize">
        <div class="modal-content "  id="popup5">

        </div>
    </div>
</div>




