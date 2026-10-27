<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
}
$url = API."?mode=togetreportemploymentstatushistory&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$emp_data = $result['emp_data'];
$_SESSION['download_data'] = array();

?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Employment Status History</h5>
      <h6 class="mt-4"><?php echo count((array)$result['emp_data']); ?> People</h6>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Employment Status History" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Employment Status History" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="row col-lg-12 ">
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
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">Employee</th>
            <th scope="col">Date</th>
            <th scope="col">Employment Status</th>
            <th scope="col">Comment</th>
          </tr>
          <?php $sl=1;$chekcount =array(); $datacheckid = array();
          $checkfiled = array('employee_type');
          foreach($emp_data as $emdata){
            $new_jsondata = json_decode($emdata['new_data'],1);
            $old_jsondata = json_decode($emdata['old_data'],1);
            $checkdd =0;

            foreach($checkfiled as $checkfi){
              if($new_jsondata[$checkfi]!=$old_jsondata[$checkfi]){
                $checkdd = 1;
                $chekcount[] = $emdata;
                $datacheckid[$checkfi] = $new_jsondata[$checkfi];
              }
            }
            if($checkdd>0){
              $cssdata[] = array('Sl No'=> $sl,'Employee Name'=> $emp_data['display_name'], 'Employee Code'=> $emp_data['emp_code'], 'Date'=> fromDate($emdata['created_date ']),'Employment Status'=> $menus['employment_statuses']['data'][$datacheckid['employee_type']], 'Comment'=> 'NA');
          ?>
          <tr>
            <td><?php echo $sl; ?></td>
            <td>
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
            </td>
            <td><?php echo fromDate($emdata['created_date ']); ?></td>
            <td><?php echo $menus['employment_statuses']['data'][$datacheckid['employee_type']]; ?></td>
            <td>NA</td>
          </tr>
          <?php $sl++;
}
} $_SESSION['download_data'] = $cssdata; ?>
        </table>
      </div>
    </div>
    <?php if(count((array)$chekcount)==0){ ?>
      <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
        <div class="text-center px-4">
          <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
        </div>
      </div>
    <?php } ?>
  </div>
</div>
