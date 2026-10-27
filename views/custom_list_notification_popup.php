<?php
if(isset($_POST['page']) && $_POST['page']!=""){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $notificationlist = toGetNotigficationListByUsingUserId($_SESSION['userid']);
    $action_log = toGETactionloglastetrecards();
}
?>
                  <ul class="nav md-tabs infisuite_tab tabs mb-2">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#home1" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Action</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#information1" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Information</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#logs1" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Logs</a>
                        </li>
                    </ul>

                    <div class="tab-content tabs card-block ">
                        <div class="tab-pane active" id="home1" role="tabpanel">


                            <?php $aci = 0; foreach ($notificationlist as  $value) {

    if($value['Notification_Type'] == 'ACTION'){
        $option_url = $value['option'];
        if($value['option']==""|| $value['option']==null ){
            $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name',$value['Notification_Table'],1);
            $option_url = $tabledata['insert_page_name'];
        }
        $nrid = "id:".$value['option_id'];
        $link = "index.php?option=".$option_url."&Rid=". deocdeId($nrid)."&noti_id=".deocdeId($value['id']); ?>
        <div class="media userlist-box waves-effect waves-light notification-box-shadow p-2 bg-white mb-1" >
            <a class="" href="<?php echo $link;?>">
                <div class="img-radius img-50 align-top m-r-15 text-center userimageround pt-3">
                    <span class="font-size-h3 font-weight-boldest"><?php echo initials(str_replace('_', ' ',$value['Notification_Table'])); ?></span>
                </div>
            </a>
            <a class="" href="<?php echo $link;?>">
                <div class="media-body">
                    <div class="chat-header"><?php echo ucwords($value['Notification_Message']); ?></br>
                        <small class="f-w-200 m-t-20 d-block"><?php echo notificationdatesdiff($value['Notification_DateTime']); ?></small>
                    </div>
                </div>
            </a>
        </div>
        <?php $aci++;
}
} if($aci == 0){?>
            <a href="javascript:;" class="kt-notification__item">
                <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title">
                        All caught up!  No new notifications.
                    </div>

                </div>
            </a>
        <?php } ?>

    </div>
    <div class="tab-pane" id="information1" role="tabpanel">

        <?php $aci = 0; foreach ($notificationlist as  $value) {

    if($value['Notification_Type'] == 'INFORMATION'){
        $option_url = $value['option'];
        if($value['option']==""|| $value['option']==null ){
            $tabledata = toGetTableNameBaseApplicationPagesList('application_table_name',$value['Notification_Table'],1);
            $option_url = $tabledata['insert_page_name'];
        }
        $nrid = "id:".$value['option_id'];
        $link = "index.php?option=".$option_url."&Rid=". deocdeId($nrid)."&noti_id=".deocdeId($value['id']); ?>
        <div class="media userlist-box waves-effect waves-light notification-box-shadow bg-white p-2 mb-1"  id="infoclose<?php echo $value['id']; ?>">
            <span class="crossbutn" onclick="closenotificationinfo(<?php echo $value['id']; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><i class="ti-close text-danger f-12"></i></span>
            <a class="" >
                <div class="img-radius img-50 align-top m-r-15 text-center userimageround pt-3">
                    <span class="font-size-h3 font-weight-boldest"><?php echo initials(str_replace('_', ' ',$value['Notification_Table'])); ?></span>
                </div>
            </a>
            <a class="" >
                <div class="media-body">
                    <div class="chat-header"><?php echo ucwords($value['Notification_Message']); ?></br>
                        <small class="f-w-200 m-t-20 d-block"><?php echo notificationdatesdiff($value['Notification_DateTime']); ?></small>
                    </div>
                </div>
            </a>
        </div>
        <?php $aci++;
}
} if($aci == 0){?>
            <a href="javascript:;" class="kt-notification__item">
                <div class="kt-notification__item-details">
                    <div class="kt-notification__item-title">
                        All caught up!  No new notifications.
                    </div>

                </div>
            </a>
        <?php } ?>
    </div>
    <div class="tab-pane" id="logs1" role="tabpanel">
        <?php foreach ($action_log as  $vdvalue) {?>
           <div class="media userlist-box waves-effect waves-light notification-box-shadow bg-white p-2 mb-1"  >

            <?php if($vdvalue['transaction_type'] == 'INSERT'){ ?>
                <a class="" >
                    <div class="img-radius img-50 align-top m-r-15 text-center userimageround">
                        <span class="font-size-h3 font-weight-boldest">
                         <i class="fa fa-history text-success mt-2"></i>
                     </div>
                 </a>
             <?php } ?>
             <?php if($vdvalue['transaction_type'] == 'UPDATE'){ ?>
                <a class="" >
                    <div class="img-radius img-50 align-top m-r-15 text-center userimageround">
                        <span class="font-size-h3 font-weight-boldest"> <i class="fa fa-history text-warning mt-2"></i>
                        </span></div>
                    </a>
                <?php } ?>
                <a class="" >
                    <div class="media-body">
                        <div class="chat-header"> <?php echo " ".viewTitle($vdvalue['transaction_table'])." ".$vdvalue['action_log']. " ".$_SESSION['users'][$vdvalue['user_id']]." with Status ".$_SESSION['status'][$vdvalue['status_value']]['name']; ?></br>
                            <small class="f-w-200 m-t-20 d-block"><?php echo date("M d, Y g:i A", strtotime($vdvalue['action_date'])); ?></small>
                        </div>
                    </div>
                </a>
            </div>

        <?php } ?>

    </div>
</div>

