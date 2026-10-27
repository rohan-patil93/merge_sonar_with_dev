<?php
if(isset($_POST['log_id'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $approval_url = API."?mode=togettransactionLoginbox&status=".$_GET['status']."&log_id=".$_POST['log_id'];
    $approval_data = callAPI($approval_url);
    $approval_data = json_decode($approval_data,1);
    $category = $approval_data['transactiores'];
    $employee = $approval_data['emp'];
    $alltransactiores = $approval_data['alltransactiores'];
    $transactionlog = $approval_data['transactionlog'];
    $notificationresult = $approval_data['notificationresult'];
    $stausresult = ViewStatusData();
    $display_attributes = $approval_data['display_attributes'];
}
?>
<?php if(count((array)$alltransactiores) !=0){ ?>
<div class="row" >
    <div class="col-lg-12" id="tablshrink">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th scope="col" class="p-1" style="width: 10%;"></th>
                        <th scope="col" class="p-1" style="width: 30%;">Message</th>
                        <th scope="col" class="p-1" style="width: 30%;">Status</th>
                        <th scope="col" class="p-1" style="width: 30%;">Request Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    foreach ($alltransactiores as  $value) {
                        $notification = $notificationresult[$value['transaction_table']][$value['id']];
                        $translog = $transactionlog[$value['transaction_table']][$value['id']];
                        $disname = $employee[$value['created_by']]['display_name'];
                        $disname = ($disname !="") ? $disname : $_SESSION['users'][$value['created_by']];
                        $disimage =  $employee[$value['created_by']]['image'];
                        $class_name = ""; $dis_class="d-flex";
                        $class_name =  ($translog['response_type'] != 0) ? "text-muted" : "";
                        if(date('Y-m-d') == date('Y-m-d',strtotime($translog['request_date']))){
                            $date_ime = date('h:i A',strtotime($translog['request_date']));
                        }else{
                            $date_ime = date('d M Y',strtotime($translog['request_date']));
                        }
                        $trnsatable = $display_attributes[$value['transaction_table']] !=""?$display_attributes[$value['transaction_table']]:$value['transaction_table'];
                        $mesg = $notification['notification_message'] != ""?$notification['notification_message']:$trnsatable;
                        $jsonencode =  json_decode($notification['notification_details'],1);
                        $url = $jsonencode['option'];
                        $reqestdays = notificationdatesdiff($date_ime);

                        ?>
                        <tr class="unread" onclick="loadPage('<?php echo $jsonencode['option'];?>','<?php echo deocdeId("id:".$jsonencode['id']); ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                            <td class="p-1" style="width: 10%;">
                                <div class="check-star">
                                    <?php if($disimage != ""){ ?>
                                        <div class=" m-r-15 text-center userimageround">
                                            <img src="uploads/all/<?php echo $disimage; ?>" alt="Details" class="img-radius img-40">
                                        </div>
                                    <?php }else{ ?>
                                        <div class="img-radius img-50 align-top m-r-15 text-center userimageround">
                                            <span class="font-size-h3 font-weight-boldest"><?php echo initials($disname); ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                            <td class="p-1" style="width: 30%;">
                                <a  class="email-name waves-effect"><?php echo $disname; ?></a>
                                <p><?php echo viewTitle($mesg); ?> &nbsp; <?php echo viewTitle($translog['transaction_id']); ?>
                                <?php if($value['response_type'] != 0 && $value['comments']!=""){ ?>
                                    (<?php echo viewTitle($value['comments']); ?>)
                                <?php } ?>
                            </td>
                            <td class="p-1" style="width: 30%;"> <?php if($translog['response_type'] != 0){ ?>
                                <span class="form-label label label-md label-primary mt-2" style="color: <?php echo "#".$stausresult[$value['current_status']]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$value['current_status']]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$value['current_status']]['background_color']); ?>;"><?php echo $stausresult[$value['current_status']]['name']; ?> </span>
                            <?php }else{ ?>
                                <span class="form-label label label-md label-primary mt-2">Waiting For Approval</span>
                            <?php } ?>

                        </td>
                        <td class="email-time p-1 <?php echo $class_text; ?>" width="30%">
                            <?php echo $date_ime; ?>
                            <p ><?php echo $reqestdays;  ?></p>

                        </td>
                    </tr>

                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<div class="col-lg-9 custom-section custom-scroll"  style="zoom: 0.7; height: 800px;" id="showdivpage">
    <div class="card">
        <div class="card-header mb-4">
            <div class="card-header-right" id="closebtnredirect">

            </div>
        </div>

        <div id="showdivpagehtml">

        </div>
    </div>

</div>
</div>
<?php }else{ ?>
    <div class="card-body">
        <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Transaction</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
    </div>
<?php } ?>
