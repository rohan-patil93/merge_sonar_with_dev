<?php
if($_SESSION['admin_type'] == '1'){
  $chk["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
}else{
  $chk["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id'],'created_by'=>$_SESSION['userid']);
}
  $chks = urlencode(json_encode($chk));
  $sateurl = API."?mode=gettabledetailswithconditions&table=customers&data=".$chks;
  $satedata =  callAPI($sateurl);
  $admindashboardd = json_decode($satedata,1);
?>
<div class="page-body">
  <div class="row">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h5>Customer List</h5>
           <div class="card-header-right">
              <a href="index.php?option=COM_INSERT_CUSTOMERS" class="btn btn-success mb-2 desktop-view">
              <i class="icofont icofont-plus icon-white text-white"></i>
                Customer
            </a>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                    <i class="icofont icofont-reply"></i>
                    Back
                  </a>
          </div>
        </div>

        <div class="row p-4">
          <?php
          if(count((array)$admindashboardd)){
          $bg_color_array = array('bg-primary','bg-success','bg-info','bg-danger');
          $tven = 0;
          foreach($admindashboardd as $vendorslist){
            $stausresult = $_SESSION['status'][$vendorslist['current_Status']];
            $rid = deocdeId("id:".$vendorslist['id']);
            ?>
            <div class="col-xl-4 col-md-12 custom-ribbon-accordian">
              <div class="ribbon ribbon-clip ribbon-right custom_grid_vendor_ribban">
                <div class="ribbon-target" style= " top: 3px;  ">
                  <span class="ribbon-inner" style="background-color: <?php echo $stausresult['background_color'][0] == '#'?$stausresult['background_color']:"#".$stausresult['background_color']; ?>; color:<?php echo $stausresult['color'][0] =='#'?$stausresult['color']:'#'.$stausresult['color']; ?>"></span> <?php echo $stausresult['name']; ?>
                </div>
              </div>
              <div class="card   bg-white" style=" min-height:170px">
                <div class="card-body">
                  <div class="row m-b-10 align-items-center ">
                    <div class="col-auto col-lg-10">
                      <div class="align-middle">
                        <?php
                        if($vendorslist['image']!=""){ ?>
                         <img src="<?php echo "uploads/customer/".$vendorslist['image']; ?>" class="img-radius  img-50" alt="">
                       <?php }else{ ?>
                        <span class="img-radius img-50 custom_txt_round  p-2 f-14 m-r-15 text-center bg-white text-mute"><?php echo initials($vendorslist['display_name']); ?></span>
                      <?php } ?>

                      <div class="d-inline-block width73 m-l-2  align-top" >
                        <a href="#" class="text-white text-capitalize"><h6 class="mb-1 text-primary"><?php echo $vendorslist['display_name']; ?></h6></a>
                        <p class="text-white m-b-0  f-12"></p>
                      </div>
                    </div>
                  </div>
                  <div class="col text-end col-lg-2 mt-3">
                    <div class="dropdown-primary dropdown open mt-0 cursor-p cursor-pointer">
                      <i class="fa fa-ellipsis-h" id="dropdown-2" data-bs-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="true"></i>
                    <div class="dropdown-menu" aria-labelledby="dropdown-2"
                      data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                      <a class="dropdown-item waves-light waves-effect"
                      href="index.php?option=COM_INSERT_CUSTOMERS&Rid=<?php echo $rid; ?>">Edit</a>


                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <span class="col-lg-4 f-12">Contact Person </span>
                <span class="col-lg-8 f-12"><?php echo $vendorslist['first_name']; ?> </span>
                <span class="col-lg-4 f-12">Vendor Since</span>
                <span class="col-lg-8 f-12"><?php echo fromDate($vendorslist['vendor_since']); ?> </span>
                <span class="col-lg-4 f-12">Email </span>
                <span class="col-lg-8 f-12"><?php echo $vendorslist['email']; ?> </span>
                <span class="col-lg-4 f-12">Phone </span>
                <span class="col-lg-8 f-12"><?php echo $vendorslist['phone']; ?> </span>
              </div>
            </div>
          </div>
        </div>
        <?php $tven++; if($tven ==4){ $tven=0;
}
}
}else{ ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Customers</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
</div>

