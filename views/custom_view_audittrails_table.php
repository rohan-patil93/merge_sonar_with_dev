<?php
if($_POST['filter'] == "Y"){
  include_once '../includes/session.php';
  include_once '../dbconfig.php';
  include_once '../comm.php';
  $action_type = json_encode($_POST['action_type']);
  $url = API."?mode=getaudittrails&action_type=".$action_type."&type=".$_POST['type']."&from_date=".$_POST['from_date']."&to_date=".$_POST['to_date']."&user_profie_id=".$_POST['user_profie_id'];
  $apires = callAPI($url);
  $result = json_decode($apires,1);

$audittrailview = $result['audit_trails'];
$listaudtitrails = array();
foreach($audittrailview as $audittrailvie){
  $listaudtitrails[date("Y-m-d",strtotime($audittrailvie['created_date']))][]= $audittrailvie;
}

$login = $result['login'];
$listloginaudtitrails = array();
foreach($login as $loginaudittrailvie){
  $listloginaudtitrails[date("Y-m-d",strtotime($loginaudittrailvie['login_from']))][]= $loginaudittrailvie;
}

 $approval = $result['approval'];
$listapprovalaudtitrails = array();
foreach($approval as $approvalaudittrailvie){
  $listapprovalaudtitrails[date("Y-m-d",strtotime($approvalaudittrailvie['request_date']))][]= $approvalaudittrailvie;
}

}

if($_POST['type'] =="login"){
          if(count((array)$login) !='0'){ $mk=1; foreach($listloginaudtitrails as $datekey=> $listloginaudtitrail){
          $dayspan = '<span class="today-msg">'.getTheDay($datekey).'</span>';
          $i=1; foreach($listloginaudtitrail as $audittrailv){
              $icontype='<span class="ti-home f-18 d-block mt-2"></span>';
          ?>
        <div class="row p-t-20 p-b-20 border-top border-bottom today-border">
          <div class="col-lg-2 text-start"><p><?php echo date('h:i:s A',strtotime($audittrailv['login_from'])); ?></p></div>
          <div class="col-lg-1 text-center" style="z-index:1">
            <?php if($i == 1){ echo $dayspan;
} ?>
            <span class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon bg-white">
              <?php echo $icontype; ?>
            </span>
          </div>
          <div class="col-lg-9">
            <div class="align-top">
              <div class="d-inline-block align-top m-l-10">
                <a href="#!"><p class="mb-0 text-capitalize">
                  <?php
                  $workingmsg = $mk =="1"?"Till Working":"Auto Log-Out";
                  echo $_SESSION['users'][$audittrailv['user_id']]." logged in & ".$workingmsg;
                  ?>
                  </p></a>
              </div>
            </div>
          </div>
        </div>
      <?php $oldpage = $audittrailv['page_option']; $i++; $mk++;
}
}
}else{ ?>
              <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
        <?php }

}elseif($_POST['type'] =="approval"){

   if(count((array)$listapprovalaudtitrails) !='0'){ $mk=1; foreach($listapprovalaudtitrails as $datekey=> $listloginaudtitrail){
          $dayspan = '<span class="today-msg">'.getTheDay($datekey).'</span>';
          $i=1; foreach($listloginaudtitrail as $audittrailv){
              if($audittrailv['response_type'] !="0"){
                $icontype='<span class="ti-check"></span>';
              }else{
                $icontype='<span class="ti-help-alt"></span>';
              }
          ?>
        <div class="row p-t-20 p-b-20 border-top border-bottom today-border">
          <div class="col-lg-2 text-start"><p><?php echo date('h:i:s A',strtotime($audittrailv['request_date'])); ?></p></div>
          <div class="col-lg-1 text-center" style="z-index:1">
            <?php if($i == 1){ echo $dayspan;
} ?>
            <span class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon bg-white">
              <?php echo $icontype; ?>
            </span>
          </div>
          <div class="col-lg-9">
            <div class="align-top">
              <div class="d-inline-block align-top m-l-10">
                <p class="mb-0 text-capitalize">
                  <?php
                  $workingmsg = $audittrailv['response_type'] !="0"?"Approved : ".fromDate($audittrailv['response_date']):"Pending Approval";
                  echo viewTitlePage($audittrailv['transaction_table'])." - ".$audittrailv['transaction_id']." ".$_SESSION['users'][$audittrailv['user_id']]." <span class='f-w-600'>".$workingmsg."</span>";
                  ?>
                  </p>
              </div>
            </div>
          </div>
        </div>
      <?php $oldpage = $audittrailv['page_option']; $i++; $mk++;
}
}
}else{ ?>
              <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
        <?php }

}else{
          $oldpage = ""; if(count((array)$listaudtitrails) !='0'){ foreach($listaudtitrails as $datekey=> $listaudtitrail){
            $dayspan = '<span class="today-msg">'.getTheDay($datekey).'</span>';
            $i=1; foreach($listaudtitrail as $audittrailv){
              $afterdetails = json_decode(urldecode($audittrailv['after_transaction_details']),1);
              $befordetails = json_decode(urldecode($audittrailv['before_transaction_details']),1);

              $changedata = array();
              if(count((array)$afterdetails) !=0 && count((array)$befordetails) !=0){
                foreach($afterdetails as $fieldkey=>$afterdetail){
                    if($afterdetail != $befordetails[$fieldkey]){
                        $changedata[$fieldkey] = viewTitlePage($fieldkey);
                    }
                }
              }
              unset($changedata['updated_date']);
              $extratext = "";
                if($audittrailv['page_option'] ==""){
                  $icontype='<span class="ti-home f-18 d-block mt-2"></span>';
                }elseif($oldpage == $audittrailv['page_option']){
                  $icontype='<span class="ti-reload f-18 d-block mt-2"></span>';
                  $extratext = 'Reload';
                }elseif($audittrailv['type'] == "INSERT"){
                 $icontype='<span class="ti-plus"></span>';
              }elseif($audittrailv['type'] == "UPDATE"){
                 $icontype='<span class="ti-pencil"></span>';
              }elseif($audittrailv['type'] == "VIEW"){
                 $icontype='<span class="ti-eye"></span>';
              }elseif($audittrailv['type'] == "DELETE"){
                 $icontype='<span class="ti-trash"></span>';
              }
          ?>
        <div class="row p-t-20 p-b-20 border-top border-bottom today-border">

          <div class="col-lg-2 text-start"><p><?php echo date('h:i:s A',strtotime($audittrailv['created_date'])); ?></p></div>
          <div class="col-lg-1 text-center" style="z-index:1">
            <?php if($i == 1){ echo $dayspan;
} ?>
            <span class="btn waves-effect waves-dark btn-primary btn-outline-primary btn-icon bg-white">
              <?php echo $icontype; ?>
            </span>
          </div>
          <div class="col-lg-9">
            <div class="align-top">
              <div class=" align-top m-l-10">
                <a href="#!">

    <div class="align-middle">
  <?php if($_SESSION['user_image'][$audittrailv['user_id']] !=""){?>
    <img src="uploads/all/<?php echo $_SESSION['user_image'][$audittrailv['user_id']]; ?>"  class="img-radius img-30 align-top m-r-15" alt="User Profile">
  <?php } else { ?>
    <span  alt="user" class="img-radius img-30 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($_SESSION['users'][$audittrailv['user_id']]); ?></span>
  <?php } ?>
  <div class="d-inline-block width73 text-capitalize">
   <?php
if($audittrailv['type'] == "UPDATE" || $audittrailv['type'] == "INSERT"){ ?>
    <h6><?php echo $_SESSION['users'][$audittrailv['user_id']]." <span class='f-w-600'>".$audittrailv['type']."</span> ".viewTitlePage($audittrailv['transaction_table'])." -".$audittrailv['transaction_id']; ?>,</h6>
<?php }else{ ?>
    <h6><?php echo $_SESSION['users'][$audittrailv['user_id']]." <span class='f-w-600'>".$audittrailv['type']."</span> ";
    echo $audittrailv['page_option']!=""?$extratext." ".viewTitlePage($audittrailv['page_option']):"Home"; ?></h6>
<?php } ?>
    <?php if(count((array)$changedata) !=0){ ?>
    <p class="text-muted m-b-0"><?php  echo "Updated details : ".implode(",",(array)$changedata); ?></p>
    <?php } ?>
  </div>
</div>

                  </a>

              </div>
            </div>
          </div>
        </div>
      <?php $oldpage = $audittrailv['page_option']; $i++;
}
}
}else{ ?>
              <div class="card-px text-center py-20 m-t-30">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>

        <?php } ?>
        <?php } ?>
