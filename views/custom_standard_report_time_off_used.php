<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();

if(isset($_POST['applyform'])) {
  $fromdate = $_POST['fromdate']!="" ? $_POST['fromdate'] : "";
  $todate = $_POST['todate']!="" ? $_POST['todate'] : "";
  if($_POST['fromdate']!=""){
    $_POST['fromdate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['fromdate'])));
  }
  if($_POST['todate']!=""){
     $_POST['todate'] =  date('Y-m-d', strtotime(str_replace('/"', '-', $_POST['todate'])));
  }


  $postdata = $_POST;
}else{

  $_POST['fromdate'] = date("Y-m-01");
  $fromdate = fromDate($_POST['fromdate']);
  $_POST['todate'] = date("Y-m-t");
  $todate = fromDate($_POST['todate']);
  $postdata = $_POST;
}
$url = API."?mode=togetstandaredtimeoffuser&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$emp_details = $result['emp_data'];
$_SESSION['download_data'] = array();
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Time Off Used</h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Time Off Used Report" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Time Off Used Report" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <form method="post" action="">
          <div class="row col-lg-12 ">
        <div class="col-lg-2">
          <div class="form-group">
            <input aria-label="Fromdate" class="form-control datepicker" name="fromdate" type="text" <?php if($fromdate!=""){ ?>  value="<?php echo $fromdate; ?>" <?php
} ?> placeholder="From Date" >
          </div>
        </div>
        <div class="col-lg-2">
          <div class="form-group">
            <input aria-label="Input field" class="form-control datepicker" <?php if($todate!=""){ ?>  value="<?php echo $todate; ?>" <?php
} ?> name="todate" type="text" placeholder="To Date">
          </div>
        </div>
        <?php include_once 'views/custom_standard_report_hr_employee_filter.php'; ?>
        <div class="col-lg-2 mt-1">
          <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
            Apply
          </button>
          <input type="hidden" name="submit" value="applyform">
        </div>

      </div>
        </form>
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-body">
      <div class="table-responsive mt-3">
        <?php if(count((array)$emp_details)>0){ ?>
        <table class="table tabe-hover table-sm">
          <tr style="background-color: #eeeeee;">
            <th scope="col">Name</th>
            <th scope="col">Category</th>
            <th scope="col">Date(s) Off</th>
            <th scope="col">Requested</th>
            <th scope="col">Approved  </th>
            <th scope="col">Notes</th>
            <th scope="col">Time Off Days</th>
          </tr>
          <?php $old_id=0; $total =0;
          foreach($emp_details as $emdata ){
          $new_id = $emdata['id'];
          $cssdata[] = array('Sl No'=> $sl,'Employee Name'=> $emdata['display_name'], 'Employee Code'=> $emdata['emp_code'], 'Category'=>$result['leave_type'][$emdata['leave_type_id']], 'Date(s) Off'=> fromDate($emdata['leave_from']) ." - ". fromDate($emdata['leave_to']), 'Requested'=>fromDate($emdata['application_date']),'Approved'=> fromDate($emdata['updated_date']),'Notes'=> $emdata['special_request'],'Time Off Days'=>$emdata['leave_days']);
           ?>
          <tr>
            <td>
              <?php if($old_id != $new_id){ $old_id = $new_id;  ?>
              <div class="d-inline-block align-middle">
                <?php if($emdata['image']!=""){ ?>
                  <img src="uploads/module/hr.webp" alt="user" class="img-radius img-40 align-top m-r-15">
                <?php }else{ ?>
                  <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($emdata['display_name']); ?></span>
                <?php } ?>
                <div class="d-inline-block">
                 <?php echo $emdata['display_name']; ?>
                 <p class="text-muted m-b-0 f-12"><?php echo $emdata['emp_code']; ?></p>
               </div>
             </div>
             <?php } ?>
            </td>
            <td><?php echo $result['leave_type'][$emdata['leave_type_id']]; ?></td>

            <td><?php echo fromDate($emdata['leave_from']) ." - ". fromDate($emdata['leave_to']); ?></td>
            <td><?php echo fromDate($emdata['application_date']); ?></td>
            <td><?php echo fromDate($emdata['updated_date']); ?></td>
            <td><?php echo $emdata['special_request']; ?></td>
            <td class="text-right"><?php echo $emdata['leave_days']; ?></td>
          </tr>
          <?php $total = $total+$emdata['leave_days'];
} $_SESSION['download_data'] = $cssdata; ?>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td>Total</td>
            <th scope="col" class="text-right"><?php echo number_format($total, 2, '.', ','); ?> </th>
          </tr>
        </table>
      <?php }else{ ?>
        <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
      </div>
      <?php } ?>
      </div>
    </div>
  </div>
</div>
