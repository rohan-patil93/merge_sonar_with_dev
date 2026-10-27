<?php
$url = API."?mode=toinsertreportviewdata&myreport_id=".$_GET['RID'];
$apires = callAPI($url);
$postdata = array();
if(isset($_POST['applyform'])) {
  $postdata = $_POST;
}
 $url = API."?mode=togetreportlastpaychange&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$emp_data = $result['emp_data'];
$_SESSION['download_data'] = array();
foreach($emp_data as $empdata){
  $listempdata[$empdata['id']][] = $empdata;
}
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Last Pay Change</h5>
      <h6 class="mt-4"><?php echo count((array)$listempdata); ?> People</h6>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Last Pay Change" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Last Pay Change" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <form method="post">
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
      <div class="table-responsive mt-4">
        <table class="table table-hover">
          <tr style="background-color: #eeeeee;">
            <th scope="col">#</th>
            <th scope="col">Employee Name</th>
            <th scope="col">Hire Date</th>
            <th scope="col">Department</th>
            <th scope="col">Designation</th>
            <th scope="col">Previous Pay</th>
            <th scope="col">Current Pay</th>
            <th scope="col">Date of Change</th>
          </tr>
           <?php $sl=1;
          foreach($listempdata as $listemdata){
              $emdata = $listemdata['0'];

              $cssdata[] = array('Sl No'=> $sl,'Employee Name'=> $emdata['display_name'], 'Employee Code'=> $emdata['emp_code'], 'Department'=>$menus['departments']['data'][$emdata['department_id']],'Designation'=>$menus['designations']['data'][$emdata['designation_id']],'Previous Pay'=>$listemdata['1']['ctc_yearly'],'Current Pay'=>$emdata['ctc_yearly'],'Date of Change'=> fromDate($emdata['affected_date']));
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
            <td><?php echo fromDate($emdata['joining_date']); ?></td>
            <td><?php echo $menus['departments']['data'][$emdata['department_id']]; ?></td>
            <td><?php echo $menus['designations']['data'][$emdata['designation_id']]; ?></td>
            <td><?php echo $listemdata['1']['ctc_yearly']; ?></td>
            <td><?php echo $emdata['ctc_yearly']; ?></td>
            <td><?php echo fromDate($emdata['affected_date']); ?></td>
          </tr>
          <?php $sl++;
}  $_SESSION['download_data'] = $cssdata; ?>
        </table>
      </div>
    </div>
  </div>
</div>
