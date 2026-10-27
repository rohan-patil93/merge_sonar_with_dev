<?php

    $wh1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
    $data1 = urlencode(json_encode($wh1));
    $url1 = API."?mode=gettabledetailswithconditions&table=vendor_feedback_setup&data=".$data1;
  $complianceres = callAPI($url1);
  $detail = json_decode($complianceres,1);
  $main_ref =array();
  foreach($detail as $data){
      $res_refdata[$data['ref_id']] = $data['id'];
  }
  foreach($detail as $data){
      if($data['current_status'] =='52' || ( !isset($res_refdata[$data['id']]) ) ){
              $main_data[] = $data;
              $main_ref[$data['id']] =  $data['ref_id'];
              $main_pub[$data['id']] =  $data['current_status'];
      }
      $sub[$data['id']] =  $data;
      $refdata[$data['id']] = $data['ref_id'];
  }

  foreach($main_ref as $key=>$res){
      $askey = $key;
      for ($i=0; $i < count((array)$detail); $i++) {
                  if(isset($res_refdata[$askey]) && $res_refdata[$askey]!='' && $res_refdata[$askey]!='0' && $res_refdata[$askey]!=$key ){
                      $sub_ref[$key][$res_refdata[$askey]] = $res_refdata[$askey];
                  }
                  if(isset($refdata[$askey])&& $refdata[$askey]!='' && $refdata[$askey]!='0' && $refdata[$askey]!=$askey ){
                      $sub_ref[$key][$refdata[$askey]] = $refdata[$askey];

                      $askey = $refdata[$askey];
                  }else{
                           break;
                  }
      }
  }
?>
<style>
    tr{}
    td, th{border-bottom: 1px solid #EBEDF3; padding: 10px;}
    table.data-table tr th{color: #B5B5C3; font-weight: 500; font-size: 14px; letter-spacing: 1px;}
    table.data-table .custom-check{position: relative;}
    table.data-table .custom-check input[type="checkbox"]{width: 17px; height: 17px;}
    table.data-table .dropdown .btn{padding: 0px 8px 2px 8px;}

    span.online-text{width: 8px; height: 8px; display: inline-block; background: #dc3545; border-radius: 50%;}
    span.direct-text{width: 8px; height: 8px; display: inline-block; background: #007bff; border-radius: 50%;}
    table.data-table .dropdown-toggle:after{display: none;}
    table.data-table a.edit{display: inline-block; vertical-align: middle;}
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  <div class="pcoded-inner-content">
      <div class="main-body">
          <div class="page-wrapper">
              <!-- Page-body start -->
              <div class="page-body">
                  <div class="card">
                      <div class="card-header ">
                          <h5>Vendor Feedback Setup</h5>
                          <div class="card-header-right">
                              <a href="index.php?option=COM_INSERT_VENDOR_FEEDBACK_SETUP" class="btn btn-success mb-2 desktop-view">
              <i class="icofont icofont-plus icon-white "></i>
                Vendor Feedback Setup            </a>
                              <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                  <i class="icofont icofont-reply"></i> Back
                              </a>
                          </div>
                    </div>
                    <div class="card-block table-responsive">
                        <div class="row dt-responsive table-responsive">
                        <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
                            <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">#</th>
                                    <th scope="col">Vendor Review Name</th>
                                    <th scope="col">Version</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">End Date</th>
                                    <th scope="col">Multiple Times</th>
                                    <th scope="col">Status</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $k=1; foreach($main_data as $detail){ ?>
                                        <tr class="main">
                                            <td>
                                                <a href="javascript:void(0)" class="main">
                                                    <svg xmlns="http:
                                                      <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                                    </svg>
                                                </a>
                                            </td>
                                            <td> <?php echo $k; ?> </td>
                                            <td><?php echo $detail['feedback_name']; ?></td>
                                            <td><?php echo $detail['version_no']; ?></td>
                                            <td><?php echo fromDate($detail['start_date']); ?></td>
                                            <td><?php echo fromDate($detail['end_date']); ?></td>
                                            <td>
                                                <?php if($detail['allow_multiple_times']==0){ ?>
                                                    <span class="text-success f-14 f-w-600"> Allowed</span>
                                                <?php }else{?>
                                                    <span class="text-warning f-14 f-w-600"> Not Allowed</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="form-label label label-info  f-12 f-w-600" style="background-color:<?php echo $_SESSION['status'][$detail['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$detail['current_status']]['color']; ?>"><?php echo $_SESSION['status'][$detail['current_status']]['name']?></span>
                                            </td>
                                            <td>
                                                <a href="index.php?option=COM_INSERT_VENDOR_FEEDBACK_SETUP&Rid=<?php echo deocdeId('id:'.$detail['id']);?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
                                                <a href="index.php?option=COM_INSERT_APPLICATION_PAGES&amp;copy=1&amp;Rid=YVdRNk1RPT0=" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Copy Details"><i class="fa fa-clone"></i> </a>
                                                <a href="index.php?option=COM_INSERT_VENDOR_FEEDBACK_SETUP&VIEW=1&Rid=<?php echo deocdeId('id:'.$detail['id']);?>"  class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                            </td>
                                        </tr>
                                        <tr class="data<?php echo $expand; ?>">
                                            <td colspan="9">
                                                <table class="mb-3" style="width: 100%">
                                                    <tr>
                                                        <th scope="col">&nbsp;</th>
                                                        <th scope="col">#</th>
                                                        <th scope="col">Vendor Review Name</th>
                                                        <th scope="col">Version</th>
                                                        <th scope="col">Start Date</th>
                                                        <th scope="col">End Date</th>
                                                        <th scope="col">Multiple Times</th>
                                                        <th scope="col">Status</th>
                                                        <th scope="col"></th>
                                                    </tr>
                                                      <?php  if(count((array)$sub_ref[$detail['id']])>0){
                                                      $n=1; foreach($sub_ref[$detail['id']] as $detai){
                                                                  $deta = $sub[$detai];
                                                      ?>
                                                            <tr>
                                                                <td>&nbsp;</td>
                                                                <td> <?php echo $n; ?> </td>
                                                                <td><?php echo $deta['feedback_name']; ?></td>
                                                                <td><?php echo $deta['version_no']; ?></td>
                                                                <td><?php echo fromDate($deta['start_date']); ?></td>
                                                                <td><?php echo fromDate($deta['end_date']); ?></td>
                                                                <td>
                                                                    <?php if($deta['allow_multiple_times']==0){ ?>
                                                                        <span class="text-success f-14 f-w-600"> Allowed</span>
                                                                    <?php }else{?>
                                                                        <span class="text-warning f-14 f-w-600"> Not Allowed</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td>
                                                                    <span class="form-label label label-info  f-12 f-w-600" style="background-color:<?php echo $_SESSION['status'][$deta['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$deta['current_status']]['color']; ?>"><?php echo $_SESSION['status'][$deta['current_status']]['name']?></span>
                                                                </td>
                                                                <td>
                                                                    <a href="index.php?option=COM_INSERT_VENDOR_FEEDBACK_SETUP&Rid=<?php echo deocdeId('id:'.$deta['id']);?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Details"><i class="fa fa-edit"></i></a>
                                                                    <button type="button" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Copy Details"><i class="fa fa-clone"></i> </button>
                                                                    <a href="index.php?option=COM_INSERT_VENDOR_FEEDBACK_SETUP&VIEW=1&Rid=<?php echo deocdeId('id:'.$deta['id']);?>"  class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="View Details"><i class="fa fa-eye"></i></a>
                                                                </td>

                                                            </tr>
                                                        <?php $n++;
}
} else{ ?>
                                                                <tr>
                                                                    <td colspan="8" class="text-center text-danger f-16 f-w-600">
                                                                        No Data Found
                                                                    </td>
                                                                </tr>

                                                         <?php } ?>
                                                        </table>
                                            </td>
                                        </tr>
                                    <?php $k++;
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

<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
        jQuery(this).parent().parent().next(".data").toggle("fast");
    });
});
</script>
