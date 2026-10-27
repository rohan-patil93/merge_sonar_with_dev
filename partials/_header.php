 <script type="text/javascript">
    function clicktransport(){
        //alert("hai");
        jQuery.ajax({
            url: "views/customtransportrequestpopup.php",
            data:'option=1&emp_id='+$('#emp_id').val(),
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
    }
    function checkinattendence(msg){
        //alert(dat+'/'+tim);
        //str = 'date='+dat+'&time='+tim;
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=employeeclocks',
            success: function(response) {
                console.log(response);
                window.location.reload();
                //alert(response);
                //$('.success').html(response);
                //document.getElementById("myForm").submit();
                /*swal.fire({              
                    title: msg,              
                            //text: "Do you want to close ? ",              
                            type: "success",           
                            buttonsStyling: false,
                            confirmButtonText: "<i class='la la-hand-o-up'></i>Yes, close!",    
                            confirmButtonClass: "btn btn-danger", 
                            showCancelButton: false,     
                            cancelButtonText: "<i class='la la-hand-o-right'></i> No, continue",      
                            cancelButtonClass: "btn btn-default"        
                        }).then(function(result) {
                            if (result.value){
                                $('#kt_modal_KTDatatable_remote').modal('hide');
                                window.location.reload();
                            }
                        }); */
                    }
                });
    }
    function checkemptransportrequest(emp_id,val){
        if(val !=""){
            $.ajax({
                url: "ajax.php",
                data:'page=updatedynamicfied&table=employee_transport_requests&hid='+val+'&fid=is_active&rid=1',
                type: "POST",
                success:function(data){
                    console.log(data);
                },
                error:function (){}
            });
        }else{
            jQuery.ajax({
                url: "views/new_from.php",
                data:'option=COM_INSERT_EMPLOYEE_TRANSPORT_REQUESTS&emp_id='+emp_id+'&location_id='+$('#location_id').val(),
                type: "POST",
                success:function(data){
                    $('#popup2').html(data);
                    $('#kt_modal_KTDatatable_remote1').modal("show");
                },
                error:function (){}
            });
        }
    }
    function checkempfoodoption(val,emp_id){
        $.ajax({
            url: "ajax.php",
            data:'page=insertanupdateempfoodoption&food_option='+val+'&emp_id='+emp_id+'&location_id='+$('#location_id').val()+'&start_date='+$('#start_date').val(),
            type: "POST",
            success:function(data){
                console.log(data);
            },
            error:function (){}
        });

    }
    function dynamicinsertpopup(option){
        jQuery.ajax({
            url: "views/new_from.php",
            data:'option='+option+'&emp_id=1',
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal("show");
            },
            error:function (){}
        });
    }

    function divFunction(){
        $('.profile-close').toggle("slide", { direction: "right" }, 1000);
        $('.basic_info_other').hide('slow');
    }

    function div2Function(){
        $('.basic_info_other').toggle("slide", { direction: "left" }, 1000);
        $('.profile-close').hide('slow');
    }
    function closenotificationinfo(id){
     $.ajax({
            url: "ajax.php",
            data:'page=closenotificationinfo&id='+id,
            type: "POST",
            success:function(data){
                console.log(data);
                $("#infoclose"+id).remove();
            },
            error:function (){}
        });
    }
    function closeallnotifications(type,user_id){
        $.ajax({
            url: "ajax.php",
            data:'page=closeallnotifications&type='+type+'&user_id='+user_id,
            type: "POST",
            success:function(data){
                console.log(data);
                //$("#infoclose"+id).remove();
            },
            error:function (){}
        });
    }


    function getNotificationList(){
        //$(".popnotifictionspeen").show();
        //$(".popnotifiction").hide();
        //$(".iconshownotifiction").html("");
        //$("#iconshownotifiction_"+grp_mid).html('<i class="fa fa-check-circle text-warning f-16"></i>');
        jQuery.ajax({
                url: "views/custom_list_notification_popup.php",
                data:'page=page',
                type: "POST",
                success:function(data){
                    console.log(data);
                    $('#popnotifiction').html(data);
                    //$(".popnotifictionspeen").hide();
                    //$(".popnotifiction").show();
                },
                error:function (){}
            });
    }

    function changeAdminToEmployee(){
        var admin_access_employee = "";
        var msg="";
        if($("#admin_access_employee").is(':checked')){
            admin_access_employee = "Y";
            msg="<?php echo $_SESSION['error_message']['9071']; ?>";
        }else{
            admin_access_employee = "N";
            msg="<?php echo $_SESSION['error_message']['9072']; ?>";
        }
        jQuery.ajax({
                url: "views/custom_change_admin_to_employees.php",
                data:'admin_access_employee='+admin_access_employee,
                type: "POST",
                success:function(data){
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',msg,'');
                    window.location.reload();
                },
                error:function (){}
            });
    }

    function getApplicationTickets(page_type){
        jQuery.ajax({
            url: "views/custom_insert_application_tickets_popup.php",
            data:'page_type='+page_type,
            type: "POST",
            success:function(data){
                if(page_type == 'NT'){
                    $('#popupm').html(data);
                    $('#kt_modal_KTDatatable_remotem').modal("show");
                }else{  
                    $('#dynamic_popup1').html(data);
                    $('#kt_modal_dynamic_popup').modal("show");
                }
                
                $(".js-example-basic-single").select2();
            },
            error:function (){}
        });
    }

</script>
<script>
    let startTimedash, timerIntervaldash, pausedTimedash;

    function startTimerdash() {
        const timerDisplaydash = document.getElementById('timerdash');
        const startButtondash = document.getElementById('startButtondash');
        const pauseButtondash = document.getElementById('pauseButtondash');
        var oldtimedash = $("#oldtimedash").val();
        var currenttimelogdash = $("#currenttimelogdash").val();
        console.log('nowqq'+Date.now());
        if(oldtimedash !=0 && !startTimedash){
            if(currenttimelogdash!="" ){
            var noldtimedash = Date.now() - currenttimelogdash;
            oldtimedash = parseInt(oldtimedash) + parseInt(noldtimedash);
            console.log('currenttimelog'+oldtimedash);
            }
            startTimedash =  Date.now() - oldtimedash;
            console.log('restart'+startTimedash);
            pausedTimedash = Date.now();
            console.log('now pase'+pausedTimedash);
        }
        if (!startTimedash) {
            // Start the timer
            console.log('startTime');
            startTimedash = Date.now();
            $('#btntimedash').hide();
            $('#btnpasdash').show();
            sessionstartandinsertimelog('start',startTimedash,'');
            //  document.getElementById('btntime').innerHTML  = '<button id="pauseButton" class="btn-danger"> <i class="ti-control-record" > </i> </button>';
            timerIntervaldash = setInterval(updateTimerdash, 1000);
        } else if (pausedTimedash) {
          console.log('pay Time:'+Date.now());
          console.log('pausedTime:'+pausedTimedash);
            // Resume the timer after pause
            startTimedash += Date.now() - pausedTimedash;
            pausedTimedash = null;
           // pauseButton.textContent = 'Pause';
           timerIntervaldash = setInterval(updateTimerdash, 1000);
           $('#btntimedash').hide();
           $('#btnpasdash').show();
        }
        function updateTimerdash() {
             //console.log('updateTimer');
            const currentTimedash = Date.now();
            const elapsedMilliseconds = currentTimedash - startTimedash;

            const hours = Math.floor(elapsedMilliseconds / 3600000);
            const minutes = Math.floor((elapsedMilliseconds % 3600000) / 60000);
            const seconds = Math.floor((elapsedMilliseconds % 60000) / 1000);

            const formattedTime = `${padZero(hours)}:${padZero(minutes)}:${padZero(seconds)}`;
            //console.log('updatetime:'+formattedTime);
            timerDisplaydash.textContent = formattedTime;
        }
        function padZero(number) {
            //console.log('padZero');
            return number.toString().padStart(2, '0');
        }
    }
    function pauseTimerdash() {
        console.log('padZero paused');
        if (startTimedash && !pausedTimedash) {
            clearInterval(timerIntervaldash);
            pausedTimedash = Date.now();
            console.log(pausedTimedash);
            //document.getElementById('pauseButton').textContent = 'Resume';
            
            $('#dislogclose').hide();
            sessionstartandinsertimelog('pause',startTimedash,pausedTimedash);
        }
    }
    //rohan Commented?
    //document.getElementById('startButtondash').addEventListener('click', startTimerdash);
    //document.getElementById('pauseButtondash').addEventListener('click', pauseTimerdash);
   

    function sessionstartandinsertimelog(type,starttime,endtime){
        var task_id = $('#task_id_dash').val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=toinsertupdateprojecttasktimlog&task_id='+task_id+'&type='+type+'&starttime='+starttime+'&endtime='+endtime,
            type: "POST",
            success:function(data){
                console.log(data);
                //$("#rem_"+val).hide();
            },
        error:function (){}
        });
    }

    function chnageModuleWithRole(module_id ="",role_short_code=""){
        var working_role_short_code = $("#working_role_short_code").val();
        var work_module_setup_id = $("#work_module_setup_id").val();
        var selectmodule = '';
        var selectrole = '';
        
        if(module_id !=""){
            selectmodule = module_id;
        }else{
            selectmodule = work_module_setup_id; 
        }
        if(role_short_code !=""){
            selectrole = role_short_code;
        }else{
            selectrole = working_role_short_code; 
        }
        window.location.href = "index.php?option=COM_CHANGE_MODULE_GROUPS&Rid="+selectmodule+"&working_role_short_code="+selectrole;
        // $href = "index.php?option=COM_CHANGE_MODULE_GROUPS&Rid=".base64_encode($module_list['module_id']);               
    }


</script>
<?php //echo "<pre>Goodddd";print_r($_SESSION['application_menu_TNC']); echo "</pre>"; 
    if($timeprint == '1' AND $maxtimeshow >= '18'){ echo "Start Header after script  - ".date('m/d/Y h:i:s a')."</br>"; }
?>


<!-- [ Header ] start -->
<nav class="navbar header-navbar pcoded-header">
    <div class="navbar-wrapper">
        <div class="navbar-logo">
            <a href="index.php">
                <?php if($_SESSION['config']['display_logo']!=""){ ?>
                    <img class="img-fluid" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                <?php }else{ echo $_SESSION['config']['display_logo']; } ?>
            </a>
            <a class="mobile-menu" id="mobile-collapse" href="#!">
                <i class="ti-flickr"></i>
            </a>
            <a class="mobile-options waves-effect waves-light">
                <i class="ti-more-alt"></i>
            </a>
        </div>
     <?php //$ptl = togetprojecttasktimelog();  ?>
        <div class="navbar-container container-fluid">
            <ul class="nav-right">
                <?php if($ptl['id']!=""){ ?>
                <li class="header-notification m-r-20">
                    <div class="my-lg-0 my-3" id="dislogclose">
                          <div class="float-left m-r-2" id="btntimedash">
                            <button id="startButtondash" class="btn-primary" style="line-height: normal;">
                                <i class="ti-control-play"></i>
                            </button>
                        </div>
                        <div class="float-left m-r-2" id="btnpasdash" style="display:none;">
                            <button id="pauseButtondash" onclick="pauseTimerdash()"  style="line-height: normal;" class="btn-danger"><i class="ti-control-record"></i></button>
                        </div>

                        <span id="timerdash" class="m-l-5"><?php echo convertSecondsToHMS($ptl['work_time']); ?></span>
                    
                    <input type="hidden" name="oldtimedash" id="oldtimedash" value="<?php echo $ptl['work_time']*1000;  ?>">
                    <input type="hidden" name="currenttimelogdash" id="currenttimelogdash" value="<?php echo  $ptl['task_start'];  ?>">
                    <input type="hidden" name="task_id_dash" id="task_id_dash" value="<?php echo  $ptl['task_id'];  ?>">

                </div>
            </li>
        <?php } ?>
<?php if($_SESSION['user_activated'] == 'Y'){ ?>                
         <?php  ?>
                <li class="header-notification m-r-20">
                 <?php //if(in_array($_SESSION['config']['application_module'],$menu_check_array)){ 
                     if(in_array($_SESSION['work_module_setup_id'], array(MODUL_HR,MODUL_ATTENDANCE))  && (/*$_SESSION['emp_pay_area']['attendance_by'] == 1*/ $_SESSION['user_clock_status'] == 'Y' || $_SESSION['admin_type'] ==1 )&& (in_array(EMPLOYEE_ROLE,(array)$_SESSION['role_ids'])) ){     ?>
                    <div class="my-lg-0 my-3">
                        <?php 
                        $check_in_display = $clock_data['clock_in'] !="" ? $clock_data['clock_in'] : "00:00:00" ;
                        $check_out_display = $clock_data['clock_out'] !="" ? $clock_data['clock_out'] : "00:00:00" ;
                        if($clock_data['clock_out'] !='' || count((array)$clock_data) == 0){

                            ?>
                            <button type="button" onclick="checkinattendence('You have clocked in')"  class="btn btn-primary font-weight-bold btn-pill btn-round" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in  &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;<?php echo $check_out_display; ?> </div> </div> "> <i class="ti-alarm-clock"></i> Check-in &nbsp; </button>
                        <?php }else{?> 
                            <button type="button" onclick="checkinattendence('You have clocked out')" class="btn btn-success font-weight-bold btn-pill btn-round" data-toggle="popover" data-placement="bottom"  data-html="true" data-content="<div class='p-4 font-weight-bold'> <div>  Check-in  &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;<?php echo $check_in_display; ?>  </div> <div>Check-out &nbsp;-&nbsp;00:00:00  </div> </div> "> <i class="ti-alarm-clock"></i> Check-Out &nbsp; </button>
                        <?php } ?> 
                    </div>
                    <?php } ?>
                </li>

            <?php  foreach($_SESSION['application_menu_TNR'] as $tnrmenuid=>$tnrmenu) { ?>
                <li class="header-notification m-r-10"  data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $tnrmenu['Label'];?>">
                    <div class="dropdown-primary" onclick="location.href='index.php?option=<?php echo $tnrmenu['Page_Name'];?>';">
                        <div class="" data-bs-toggle="dropdown" >
                            <i class="<?php echo $tnrmenu['Menu_Icon'];?>"></i>
                        </div>
                    </div>
                </li>
            <?php } ?>

             <?php /**if( in_array($_SESSION['work_module_setup_id'], array(MODUL_HR,MODUL_PAYROLL,MODUL_ATTENDANCE)) && ($_SESSION['admin_type'] == '1' || $_SESSION['pay_admin_emp'] > 0)){ ?>   
                <li class="header-notification m-r-10"  data-bs-toggle="tooltip" data-placement="bottom" title="Work Attendance">
                    <div class="dropdown-primary" onclick="location.href='index.php?option=COM_LIST_WORK_ATTENDANCES';">
                        <div class="" data-bs-toggle="dropdown" >
                            <i class="ti-notepad"></i>
                        </div>
                    </div>
                </li>
             <?php } ?>
        <?php if( in_array($_SESSION['work_module_setup_id'], array(MODUL_HR,MODUL_PAYROLL)) ){
                $vkv = "COM_VIEW_DOCUMENTS";
                if($_SESSION['admin_type'] == '1' || $_SESSION['pay_admin_emp'] > 0){
                    $vkv = "COM_VIEW_DOCUMENT_DASH_BOARD";
                }

        ?>   
                <li class="header-notification m-r-10"  data-bs-toggle="tooltip" data-placement="bottom" title="Document Dashboard">
                    <div class="dropdown-primary" onclick="location.href='index.php?option=<?php echo $vkv; ?>';">
                        <div class="" data-bs-toggle="dropdown" >
                            <i class="ti-envelope"></i>
                        </div>
                    </div>
                </li>
        <?php } **/?>
         <!-- <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title="Office Management">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_VIEW_OFFICE_MANAGEMENT';">
                <div class="" data-bs-toggle="dropdown">
                    <i class="ti-agenda" ></i>
                </div>
            </div>
        </li>  -->
        <?php if(in_array(ADMIN_ROLE,(array)$_SESSION['role_ids'])){ ?>
        <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title="Display Attributes">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_LIST_DISPLAY_ATTRIBUTES';">
                <div class="" data-bs-toggle="dropdown">
                    <i class="ti-tablet"></i>
                </div>
            </div>
        </li>
        <!-- <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title="Report Builder">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_VIEW_REPORT';">
                <div class="" data-bs-toggle="dropdown">
                    <i class="ti-panel"></i>
                </div>
            </div>
        </li> -->
        <!-- <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title="My Reports">
                 <div class="dropdown-primary" onclick="location.href='index.php?option=COM_LIST_MY_REPORTS';">
                    <div class="" data-bs-toggle="dropdown">
                        <i class="ti-pie-chart"></i>
                 </div>
             </div>
         </li>  -->
        <!-- <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title="Project Dashboard">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_VIEW_PROJECT_DASH_BOARD';">
                <div class="" data-bs-toggle="dropdown">
                    <i class="fa fa-cube" ></i>
                </div>
            </div>
        </li> -->
        
        
        
        <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title="Settings">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_LIST_APPLICATION_MENU_SETTINGS';">
                <div class="" data-bs-toggle="dropdown">
                    <i class="ti-settings" ></i>
                </div>
             </div>
        </li>
    <?php } ?>
    <?php /**if(in_array(ADMIN_ROLE,(array)$_SESSION['role_ids']) || in_array( $_SESSION['module_setp'][$_SESSION['work_module_setup_id']]['role_admin_short_code'],(array)$_SESSION['module_code']) ){ ?>
    <li class="header-notification m-r-10" data-bs-toggle="tooltip" data-placement="bottom" title="Reports">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_LIST_MY_REPORTS';">
                <div class="" data-bs-toggle="dropdown">
                    <!-- <i class="ti-stats-up" ></i> -->
                    <i class="ti-pie-chart" ></i> 
                </div>
            </div>
        </li>
    <?php } **/ /**?>
        <li class="header-notification  m-r-10"  data-bs-toggle="tooltip" data-placement="bottom" title="Inbox">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_LIST_INBOX';">
                <div class="" data-bs-toggle="dropdown" >
                    <i class="ti-email"></i>
                    <span class="badge bg-c-green customcount"><?php echo $inboxcount['index_cnt']; ?></span>
                </div>
            </div>
        </li> 
        <?php **/?>
         <li class="header-notification  m-r-10"  data-bs-toggle="tooltip" data-placement="bottom" title="Announcements">
            <div class="dropdown-primary" onclick="location.href='index.php?option=COM_VIEW_ANNOUNCEMENTS';">
                <div class="" data-bs-toggle="dropdown" >
                    <i class="ti-announcement"></i>
                </div>
            </div>
        </li> 


    <li class="header-notification  m-r-10"  data-bs-toggle="tooltip" data-placement="bottom" title="Notifications" onclick="getNotificationList()">
        <div class="dropdown-primary dropdown">
            <div class="displayChatbox dropdown-toggle" data-bs-toggle="dropdown">
                <i class="ti-bell"></i>
                <span class="badge bg-c-red"><?php echo $inboxcount['notificaton_cnt']; ?></span>
            </div>
        </div>
    </li>

     <li class="header-notification  m-r-10"  data-bs-toggle="tooltip" data-placement="bottom" title="Support" >
        <div class="dropdown-primary dropdown">
            <span class="" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="ti-headphone-alt"></i></span>
            <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                <?php if($_SESSION['admin_type'] !='1'){ ?>
                    <a class="dropdown-item waves-light waves-effect"  onclick="getApplicationTickets('NT')">New Ticket</a>
               
                <a class="dropdown-item waves-light waves-effect"  onclick="getApplicationTickets('LT')">My Tickets</a>
                <?php  } ?>
                <?php if($_SESSION['admin_type'] =='1'){ ?>
                <a class="dropdown-item waves-light waves-effect"  onclick="getApplicationTickets('PT')">Tickets Submited To Me</a>
                <?php } ?>
            </div>
        </div>
    </li>
 <!--   <li class="header-notification">
            <div class="dropdown-primary dropdown">
                <div class="displayChatbox dropdown-toggle" data-bs-toggle="dropdown">
                   <i class="feather icon-bell"></i>
                    <span class="badge bg-c-green customcount"><?php // count($notificationlist);?></span>
                </div>
            </div>
        </li> -->



<?php /** ?>
                <li class="header-notification">
                    <div class="dropdown-primary dropdown">
                        <div class="displayChatbox dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="feather icon-message-square"></i>
                            <span class="badge bg-c-green">3</span>
                        </div>
                    </div>
                </li>
                <?php **/?>
<?php } ?> <!--  END ACtiv Check--->

                <li class="user-profile header-notification">

                    <div class="dropdown-primary dropdown">
                        <div class="dropdown-toggle" data-bs-toggle="dropdown">

                          <?php 
                          $dis_name = $_SESSION['user']['display_name']!=""?$_SESSION['user']['display_name']:$_SESSION['display_name'];
                        if($_SESSION['user']['user_photo']!=""){ ?>
                           <img src="<?php echo "uploads/all/".$_SESSION['user']['user_photo']; ?>" class="img-radius img-50 align-top m-r-15  mt-3" alt="User-Profile-Image">
                       <?php }elseif($_SESSION['employee']['gender'] !=""){ ?>
                                <img src="uploads/all/<?php echo $_SESSION['employee']['gender'].".png"; ?>" class="img-radius img-50 align-top m-r-15  mt-3" alt="image" />
                        <?php }else{ ?>
                          <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($dis_name); ?></span>
                      <?php } ?>

                      <span><?php 
                      echo ucwords($dis_name); ?>

                  </span>
                  <i class="ti-angle-down"></i>
              </div>
              <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
             <!--   <li>
                    <a href="#!">
                        <i class="ti-settings"></i> Settings
                    </a>
                </li>  --->
                <li>
                    <?php  
                        if($user['user_type'] ==3){
                            $profileurl = "index.php?option=COM_INSERT_VENDORS";
                        }elseif($user['user_type'] ==4){ 
                            $profileurl = "index.php?option=COM_INSERT_CANDIDATES";
                        }else{
                            $profileurl = "index.php?option=COM_VIEW_PROFILE_APPLICATION_USERS";
                        }
                        ?>
                    <a href="<?php echo $profileurl; ?>">
                        <i class="ti-user"></i> Profile

                    </a>
                </li>
               <!--  <li>    
                    <a href="?option=COM_LIST_NOT_EMPLOYEE&tab=cp"> 
                        <i class="ti-key"></i> Change Password  
                    </a>    
                </li> -->
                <li>
                    <a href="index.php?option=COM_LIST_INBOX">
                        <i class="ti-email"></i> My Messages

                    </a>
                </li> 
                <?php if(in_array(SUPERADMIN_ROLE,(array)$_SESSION['role_ids']) || in_array(ADMIN_ROLE,(array)$_SESSION['role_ids'])){ ?>
                    <li>
                        <a href="index.php?option=COM_INSERT_CONFIG&Rid=YVdRNk1RPT0=">
                            <i class="ti-settings"></i> Config

                        </a>
                    </li> 
                <?php } ?>
                <li onclick="location.href='index.php?option=com_logout';">
                    <a href="#!" class="text-danger">
                        <i class="ti-power-off"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <?php if($user['user_type'] !=4 AND $user['user_type'] !=3 AND !in_array(SUPERADMIN_ROLE,(array)$_SESSION['role_ids'])){ ?>
    <li class="header-notification">
        <div class="dropdown-primary dropdown">
            <div class="dropdown-toggle" data-bs-toggle="dropdown">
                <!-- <i class="ti-view-grid"></i> -->
                <img src="assets/images/favicon.ico" class="img-30">
            </div>
            <div class="show-notification notification-view dropdown-menu pt-0 pb-0" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" 
                style="">
                <div class="row m-0" style="background: url('assets/images/bg-6.jpg');">
                    <?php 
                    $showswitch = 0; //1
                    /*foreach($_SESSION['module_setp'] as $module_list12){ 
                        if(in_array($module_list12['role_admin_short_code'],(array)$_SESSION['role_ids']) AND $_SESSION['work_module_setup_id'] == $module_list12['module_id']){
                            $showswitch = 0;
                        }
                    } */
                    ?>

                    <div class="<?php echo (in_array(ADMIN_ROLES,(array)$_SESSION['role_ids']) OR $showswitch == '0')?"col-lg-6 col-md-6 col-sm-6 col-6 text-left":"col-lg-12 text-center"; ?>" >
                        <h4 class="mb-0 text-white p-3">Modules</h4>
                    </div>
                    <?php 
                    if(in_array(ADMIN_ROLES,(array)$_SESSION['role_ids']) OR $showswitch == '0'){
                   // if(in_array(ADMIN_ROLES,(array)$_SESSION['role_ids'])){ ?>
                    <!-- <div class="col-lg-3 text-white">Employee</div> -->
                    <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                        <!-- <input type="checkbox" class="js-switch" id="admin_access_employee" onchange="changeAdminToEmployee()" <?php // echo $_SESSION['admin_access_employee']=="Y"?"checked":"";?>> -->
                        <?php /** ?><div class="toggle-button-cover mt-2">
                            <div class="button-cover">
                                <div class="button b2" id="button-16">
                                    <input type="checkbox" class="checkbox" id="admin_access_employee" onchange="changeAdminToEmployee()" <?php echo $_SESSION['admin_access_employee']=="Y"?"checked":"";?> />
                                    <div class="knobs"></div>
                                    <div class="layer"></div>
                                </div>
                            </div>
                        </div> <?php **/ ?>
                        <select id="working_role_short_code" onchange="chnageModuleWithRole()">
                            <?php foreach($_SESSION['modulewithroledetails'][$_SESSION['work_module_setup_id']] as $mwithrole){ ?>
                            <option value="<?php echo $mwithrole['role_short_code']; ?>" <?php echo $mwithrole['role_short_code'] == $_SESSION['working_role_short_code']?"selected":""; ?>><?php echo $mwithrole['display_name']; ?></option>
                        <?php } ?>
                        </select>
                    </div>
                    
                <?php } ?>
                </div>
                <div class="row text-center custom-notification m-0 module-width <?php //echo count((array)$_SESSION['module_setp']) >=8?"custom-scroll":""; ?>" <?php //echo //count((array)$_SESSION['module_setp']) >=8?'style="height: 610px;"':''; ?>>
                    <?php  
                    //echo "<pre>"; print_r($_SESSION['module_setp']);  echo "</pre>";
                    foreach($_SESSION['module_setp'] as $module_list11){ 
                        if($module_list11['linking'] == 1){
                            if(!isset($_SESSION['application_user_module_roles'][$module_list11['module_id']]) AND !in_array(ADMIN_ROLES,(array)$_SESSION['role_ids'])){
                                $notaccesmodule[] = $module_list11;
                            }else{
                                $accesmodule[] = $module_list11;
                            }
                        }
                    }
                    $modulelists = $accesmodule;
                    if(count((array)$accesmodule) != 0 AND count((array)$notaccesmodule) != 0){
                       $modulelists = array_merge($accesmodule,$notaccesmodule);
                    }
                    //echo "dfdfbv";
                    //echo "<pre>"; print_r($accesmodule); echo "</pre>";
                    foreach($modulelists as $module_list){ 
                    if($module_list['linking'] == 1){
                        $style =  "";
                        $modultitle = "";
                        $pointrernone="";
                        $modulerolename = "";
                        if($_SESSION['admin_type'] == '1'){
                            $modulerolename = $_SESSION['modulewithroledetails'][$module_list['module_id']][0]['role_short_code'] !=""?$_SESSION['modulewithroledetails'][$module_list['module_id']][0]['role_short_code']:$_SESSION['module_setp'][$module_list['module_id']]['role_admin_short_code']; 
                        }else{
                            $modulerolename = $_SESSION['modulewithroledetails'][$module_list['module_id']][0]['role_short_code'];
                        
                        }
                        $modulfunction = "chnageModuleWithRole('".base64_encode($module_list['module_id'])."','".$modulerolename."')";
                        $icon = "";
                       // $href = "index.php?option=COM_CHANGE_MODULE_GROUPS&Rid=".base64_encode($module_list['module_id']);
                        if(!isset($_SESSION['application_user_module_roles'][$module_list['module_id']]) AND !in_array(ADMIN_ROLES,(array)$_SESSION['role_ids'])){
                            $style = "background-color: #cccc;'";
                            $href = "#";
                            $modultitle ='data-bs-toggle="tooltip" data-placement="bottom" title="This Module Is Not Activated"';
                           // $pointrernone = "style='pointer-events: none'"; 
                            $modulfunction = "";
                        }
                            if($_SESSION['work_module_setup_id'] == $module_list['module_id']){
                                 $icon = '<i class="fa fa-check-circle text-warning f-20"></i>';
                                 $style .= "box-shadow: 0px 0px 10px grey;";
                            } 
                            ?>
                        <div class="col-lg-3 p-1 border" style="<?php echo $style;?> " <?php echo $modultitle; ?>  onclick="<?php echo $modulfunction;?>">
                            <?php echo $icon; ?>
                            <img src="uploads/all/<?php echo $module_list['module_logo']; ?>" class="img-40" > 
                            <h5 class=" f-12"><?php echo $module_list['module_name']; ?></h5>
                        </div>
                    <?php } } ?>

                </div>
                <div class="col-lg-12" style="height:10px;"></div>
            </div>
        </div>
    </li>
    <?php } ?>
    <input type="hidden" id="work_module_setup_id" value="<?php echo base64_encode($_SESSION['work_module_setup_id']); ?>"> 
</ul>
<?php //echo "<pre>Goodddd";print_r($_SESSION['application_menu_TNC']); echo "</pre>"; 
    if($timeprint == '1' AND $maxtimeshow >= '18'){ echo "After Header Menu  - ".date('m/d/Y h:i:s a')."</br>"; }
?>
<?php 
//echo "<pre>Goodddd";print_r($_SESSION['role_nav']); print_r($topnavcenter); echo "</pre>";

if($_SESSION['user_activated'] == 'Y'){ ?>     
<!------ Add allin one menu  End--->
<ul class="<!--nav-left--> nav-left">
         <?php if($user['user_type'] !=4 AND $user['user_type'] !=3 AND $_SESSION['module_setp'][$_SESSION['work_module_setup_id']]['module_logo'] !=""){ ?>
    <li class="nav-item has-megamenu">
    <a class="nav-link  text-primary f-w-600" href="index.php" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $_SESSION['module_setp'][$_SESSION['work_module_setup_id']]['module_name'];?> Home"> 
       <!-- <i class="ti-home btn waves-effect waves-light btn-success btn-icon"></i>  -->
       <img src="uploads/all/<?php echo $_SESSION['module_setp'][$_SESSION['work_module_setup_id']]['module_logo'];?>" class="img-40">

    </a>
    </li>
<?php } ?>

<?php 
if (count((array)$topnavcenter) >0 && in_array('TNC', (array)$_SESSION['role_nav'])){  //echo "<pre>"; print_r($topnavcenter); echo "</pre>";
    foreach ($topnavcenter as  $listnav) {?>
        <li class="nav-item dropdown has-megamenu">
            <a class="nav-link dropdown-toggle text-primary" href="#" data-bs-toggle="dropdown"> 
                <?php ///echo $listnav['Label']."q".$listnav['id']; ?> 
                <?php if(strpos($listnav['Menu_Icon'], "svg")){ ?>
                    <span class="svg-icon menu-icon f-12">
                        <?php echo $listnav['Menu_Icon']; ?>
                    </span>
            <?php echo ucwords($listnav['Label']); ?>
                <?php }else{ ?>                                 
                    <i class="<?php echo $listnav['Menu_Icon']; ?> btn waves-effect waves-light btn-success btn-icon f-12"></i> 
                    <span style="font-size:14px;"> <?php echo ucwords($listnav['Label']); ?> </span>
                <?php } ?>
            </a>
            <?php
            $cntrow = count((array)$listnav['child']); 
            if($cntrow >= 6){
                $cntrow = 6;
                $collg = 12/6;
            }else{
                $multirow = $cntrow;
                if($cntrow == 5){
                    $multirow = 6;
                }
                $collg = 12/$multirow;
                $collgwidth = round(100/$cntrow);
            }
            $menucheck=1;  $menuwidth=17*$cntrow;
                  foreach ($listnav['child'] as  $list) {
                    if(count((array)$list['child'])>0){
                         $menucheck=1;   $menuwidth=17*$cntrow;
                         break;
                    }
                  }

                  if($listnav['Label']=='Project Timesheets'){
                    $menuwidth = $menuwidth*2;
                  }
             ?>
            <div class="dropdown-menu megamenu" role="menu" style="left: 15px; width: <?php echo $menuwidth;  ?>% !important;">
                <div class="row g-3" style = "margin-bottom: calc(var(--bs-gutter-y) * -1);">
                    <?php if($menucheck==1){ ?>
                     
                    

<?php foreach ($listnav['child'] as  $chieldlist) {  //echo  "<pre>";print_r($chieldlist); echo "</pre>";?> 
  <div class="col-lg-<?php echo $collgw; ?> float-left" style=" width: <?php echo  $collgwidth; ?>%">
    <?php foreach ($chieldlist as  $list) { ?>    
            <div class="col-megamenu">
                <ul class="list-unstyled">
                    <h6 class="title"><?php echo ucwords($list['Label']); ?></h6>
                    <ul class="list-unstyled">
                        <?php foreach ($list['child'] as  $listside) {?>
                            <li style="padding: 0px !important;"><a href="index.php?option=<?php echo $listside['Page_Name']; ?>">
                              <?php echo ucwords($listside['Label']); ?>
                          </a></li>
                        <?php } ?>
                    </ul>
                </ul>
            </div>
        <?php }  ?>
        </div>
    <?php } ?>
                    <?php }else{ ?>
                        <div class="col-lg-12">
                            <div class="col-megamenu">
                                <ul class="list-unstyled">
                                    <?php foreach ($listnav['child'] as  $list) {?>
                                        <li style="padding: 0px !important;"><a href="index.php?option=<?php echo $list['Page_Name']; ?>">
                                          <?php echo ucwords($list['Label']); ?>
                                      </a></li>
                                  <?php } ?>
                              </ul>
                          </div> <!-- col-megamenu.// -->
                      </div><!-- end col-3 -->
                    <?php } ?>
                  <?php  // } ?>
              </div><!-- end row -->
          </div> <!-- dropdown-mega-menu.// -->
      </li>
  <?php } } ?>
    <?php  if (count((array)$toplistnav) >0 && in_array('TNL', (array)$_SESSION['role_nav'])){ foreach ($toplistnav as  $listnav) {?>
        <li class="nav-item dropdown has-megamenu">
            <a class="nav-link dropdown-toggle text-primary" href="#" data-bs-toggle="dropdown"> 
                <?php ///echo $listnav['Label']."q".$listnav['id']; ?> 
                <?php if(strpos($listnav['Menu_Icon'], "svg")){ ?>
                    <span class="svg-icon menu-icon f-12">
                        <?php echo $listnav['Menu_Icon']; ?>
                    </span>
            <?php echo ucwords($listnav['Label']); ?>
                <?php }else{ ?>                                 
                    <i class="<?php echo $listnav['Menu_Icon']; ?> btn waves-effect waves-light btn-success btn-icon f-12"></i> 
                    <span style="font-size:14px;letter-spacing: 1px;"> <?php echo ucwords($listnav['Label']); ?> </span>
                <?php } ?>
            </a>
            <?php $menucheck=0;  $menuwidth=350;
                  foreach ($listnav['child'] as  $list) {
                    if(count((array)$list['child'])>0){
                         $menucheck=1;   $menuwidth=700;
                         break;
                    }
                  }
             ?>
            <div class="dropdown-menu megamenu" role="menu" style="left: auto; width: <?php echo $menuwidth;  ?>px !important;">
                <div class="row g-3" style = "margin-bottom: calc(var(--bs-gutter-y) * -1);">
                    <?php if($menucheck==1){ ?>
                     <?php foreach ($listnav['child'] as  $list) { ?>
                    <div class="col-lg-6">
                        <div class="col-megamenu">
                            <ul class="list-unstyled">
                            <h6 class="title"><?php echo $list['Label']; ?></h6>
                            <ul class="list-unstyled">
                                <?php foreach ($list['child'] as  $listside) {?>
                                    <li style="padding: 0px !important;"><a href="index.php?option=<?php echo $listside['Page_Name']; ?>">
                                      <?php echo ucwords($listside['Label']); ?>
                                  </a></li>
                                <?php } ?>
                            </ul>
                        </ul>
                        </div>
                    </div>
                <?php } ?>
                    <?php }else{ ?>
                        <div class="col-lg-12">
                            <div class="col-megamenu">
                                <ul class="list-unstyled">
                                    <?php foreach ($listnav['child'] as  $list) {?>
                                        <li style="padding: 0px !important;"><a href="index.php?option=<?php echo $list['Page_Name']; ?>">
                                          <?php echo ucwords($list['Label']); ?>
                                      </a></li>
                                  <?php } ?>
                              </ul>
                          </div> <!-- col-megamenu.// -->
                      </div><!-- end col-3 -->
                    <?php } ?>                       
              </div><!-- end row -->
          </div> <!-- dropdown-mega-menu.// -->
      </li>
  <?php } } ?>

<?php 
        $ttimesheet ="";
        if(in_array('19',(array)$_SESSION['module_list'])){
        if(in_array('4',(array)$_SESSION['role_ids'])){
            $ttimesheet = "COM_BULK_HR_PROJECT_TIMESHEET";
            }if(in_array('7',(array)$_SESSION['role_ids'])){
            $ttimesheet = "COM_BULK_FINANCE_PROJECT_TIMESHEET";
        }if(in_array('3',(array)$_SESSION['role_ids'])){
            $ttimesheet = "COM_BULK_PROJECT_TIMESHEET";
        }
        }
?>
<?php if($ttimesheet !=""){ ?>
    <li class="nav-item has-megamenu">
            <a href="index.php?option=<?php echo $ttimesheet; ?>" class="nav-link  text-white f-w-600   btn btn-primary btn-shadow-hover font-weight-bold  " style="border-radius: 25px;margin-top:12px;padding: 5px 10px;"> 
        <span style="font-size:14px;"> Bulk Timesheet Approval </span> 
    </a>
       </li>
<?php } ?>
<?php if(ESOP_ADMIN_ROLE == $_SESSION['working_role_short_code']){ ?>
    <li class="nav-item has-megamenu">
            <a href="index.php?option=COM_VIEW_ESOP_ALLOCATIONS" class="nav-link  text-white f-w-600   btn btn-primary btn-shadow-hover font-weight-bold  " style="border-radius: 25px;margin-top:12px;padding: 6px 35px;"> 
        <span style="font-size:14px;"> ESOP Overview </span> 
    </a>
       </li>
<?php } ?>

   <!---  <li class="nav-item has-megamenu">
    <a href="<?php echo $link; ?>" class="nav-link  text-white f-w-600   btn btn-primary btn-shadow-hover font-weight-bold  " style="border-radius: 25px;margin-top:12px;padding: 5px 10px;"> 
        Bulk Timesheet Approval 
    </a>
    </li>
   <li class="header-search p-custom-search">
    <div class="main-search morphsearch-search">
        <div class="input-group">
            <span class="input-group-text search-close">
                <i class="ti-close input-group-text text-dark"></i>
            </span>
            <input type="text" class="form-control" placeholder="Enter Keyword">
            <span class="input-group-text search-btn">
                <i class="ti-search input-group-text"></i>
            </span>
        </div>
    </div>
</li>
<li>
    <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
        <i class="full-screen feather icon-maximize"></i>
    </a>
</li> -->
</ul>
<?php } ?> <!--  END ACtiv Check--->
</div>
</div>
</nav>
<!-- [ chat user list ] start -->





<div id="sidebar" class="users p-chat-user notificationsidebar showChat width25">
    <div class="had-container">
        <div class="p-fixed users-main width25">
            <div class="user-box">
                <div class="chat-search-box">
                    <a class="back_friendlist">
                        <i class="ti-close"></i>
                    </a>
                    <div class="right-icon-control">
                        <div class="input-group input-group-button">
                            <input type="text" id="search-friends" name="footer-email" class="form-control" placeholder="Search a notiffication">
                         <!--   <button class="btn btn-primary waves-effect waves-light" type="button">
                                <i class="ti-search"></i>
                            </button>  -->
                        </div>
                    </div>
                </div>
                <div class="main-friend-list p-2" id="popnotifiction"> 
                   
                  
</div>
</div>
</div>
</div>
</div>
<!-- [ chat user list ] end -->


<!-- 
<div id="sidebar" class="users p-chat-user showChat">
    <div class="had-container">
        <div class="p-fixed users-main">
            <div class="user-box">
                <div class="chat-search-box">
                    <a class="back_friendlist">
                        <i class="feather icon-x"></i>
                    </a>
                    <div class="right-icon-control">
                        <div class="input-group input-group-button">
                            <input type="text" id="search-friends" name="footer-email" class="form-control" placeholder="Search Friend">
                            <button class="btn btn-primary waves-effect waves-light" type="button">
                                <i class="feather icon-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="main-friend-list">
                    <div class="media userlist-box waves-effect waves-light" data-id="1" data-status="online" data-username="Josephin Doe">
                        <a class="media-left" href="#!">
                            <img class="media-object img-radius img-radius" src="../files/assets/images/avatar-3.jpg" alt="Generic placeholder image ">
                            <div class="live-status bg-success"></div>
                        </a>
                        <div class="media-body">
                            <div class="chat-header">Josephin Doe</div>
                        </div>
                    </div>
                    <div class="media userlist-box waves-effect waves-light" data-id="2" data-status="online" data-username="Lary Doe">
                        <a class="media-left" href="#!">
                            <img class="media-object img-radius" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder image">
                            <div class="live-status bg-success"></div>
                        </a>
                        <div class="media-body">
                            <div class="f-13 chat-header">Lary Doe</div>
                        </div>
                    </div>
                    <div class="media userlist-box waves-effect waves-light" data-id="3" data-status="online" data-username="Alice">
                        <a class="media-left" href="#!">
                            <img class="media-object img-radius" src="../files/assets/images/avatar-4.jpg" alt="Generic placeholder image">
                            <div class="live-status bg-success"></div>
                        </a>
                        <div class="media-body">
                            <div class="f-13 chat-header">Alice</div>
                        </div>
                    </div>
                    <div class="media userlist-box waves-effect waves-light" data-id="4" data-status="offline" data-username="Alia">
                        <a class="media-left" href="#!">
                            <img class="media-object img-radius" src="../files/assets/images/avatar-3.jpg" alt="Generic placeholder image">
                            <div class="live-status bg-default"></div>
                        </a>
                        <div class="media-body">
                            <div class="f-13 chat-header">Alia<small class="d-block text-muted">10 min ago</small></div>
                        </div>
                    </div>
                    <div class="media userlist-box waves-effect waves-light" data-id="5" data-status="offline" data-username="Suzen">
                        <a class="media-left" href="#!">
                            <img class="media-object img-radius" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder image">
                            <div class="live-status bg-default"></div>
                        </a>
                        <div class="media-body">
                            <div class="f-13 chat-header">Suzen<small class="d-block text-muted">15 min ago</small></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- [ chat user list ] end -->

<!-- [ chat message ] start 
<div class="showChat_inner">
    <div class="d-flex chat-inner-header">
        <a class="back_chatBox">
            <i class="feather icon-x"></i> Josephin Doe
        </a>
    </div>
    <div class="main-friend-chat">
        <div class="d-flex chat-messages">
            <a class="media-left photo-table" href="#!">
                <div class="flex-shrink-0">
                    <img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder image">
                </div>
            </a>
            <div class="flex-grow-1 chat-menu-content">
                <div class="">
                    <p class="chat-cont">I'm just looking around. Will you tell me something about yourself?</p>
                </div>
                <p class="chat-time">8:20 a.m.</p>
            </div>
        </div>
        <div class="d-flex chat-messages">
            <div class="flex-grow-1 chat-menu-reply">
                <div class="">
                    <p class="chat-cont">Ohh! very nice</p>
                </div>
                <p class="chat-time">8:22 a.m.</p>
            </div>
        </div>
        <div class="d-flex chat-messages">
            <a class="media-left photo-table" href="#!">
                <div class="flex-shrink-0">
                    <img class="media-object img-radius img-radius m-t-5" src="../files/assets/images/avatar-2.jpg" alt="Generic placeholder image">
                </div>
            </a>
            <div class="media-body chat-menu-content">
                <div class="">
                    <p class="chat-cont">can you come with me?</p>
                </div>
                <p class="chat-time">8:20 a.m.</p>
            </div>
        </div>
    </div>
    <div class="chat-reply-box">
        <div class="right-icon-control">
            <div class="input-group input-group-button">
                <input type="text" class="form-control" placeholder="Write hear . . ">
                <button class="btn btn-primary waves-effect waves-light" type="button">
                    <i class="feather icon-message-circle"></i>
                </button>
            </div>
        </div>
    </div>
</div>
         [ chat message ] end -->

         