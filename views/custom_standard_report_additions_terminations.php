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

}else{
  $_POST['fromdate'] = date("Y-m-01");
   $_POST['todate'] = date("Y-m-t");
}
$postdata = $_POST;
$url = API."?mode=togetemployeaddtionterminations&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$sl=1;$_SESSION['download_data'] = array();
foreach($result['emp_data'] as $empdata){
    if($empdata['date_of_leaving']!=""){
      $exitemp[] = $empdata;
    }else{
      $addemp[] = $empdata;
    }
    $hire_date = $emdata['joining_date']!="" ? fromDate($emdata['joining_date']) : "";
    $termination_date = $emdata['date_of_leaving']!="" ? fromDate($emdata['date_of_leaving']) : "";
    $cssdata[] = array('Sl No'=> $sl,'Employee Name'=> $empdata['display_name'], 'Employee Code'=> $empdata['emp_code'], 'Hire Date'=>$hire_date , 'Termination  Date'=> $termination_date,'Division'=> $empdata['division_name'],'Department'=> $empdata['department_name'],'Location'=> $empdata['location_name'],'Designation'=> $empdata['designation_name'], 'Employement Status'=> $empdata['employment_status_name']);
    $sl++;
}
$_SESSION['download_data'] = $cssdata;
?>
<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Additions & Terminations</h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Additions & Terminations Report" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Additions & Terminations Report" target="_blank">Excel</a>
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
  <div class="">
    <ul class="nav md-tabs infisuite_tab">
      <li class="nav-item">
        <a class="nav-link active" data-bs-toggle="tab" href="#tabb1" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
          Additions
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link " data-bs-toggle="tab" href="#tabb2" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
          Terminations
        </a>
      </li>
    </ul>
  </div>
  <div class="tab-content tabs mt-2">
    <div class="tab-pane active" id="tabb1" role="tabpanel">
      <div class="card">
        <div class="card-body card-header">
          <h5>Additions (<?php echo count((array)$addemp); ?>)</h5>
          <?php $sl=1; if(count((array)$addemp)>0){ ?>
            <div class="table-responsive mt-5">
              <table class="table table-hover">
                <tr style="background-color: #eeeeee;">
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Hire Date</th>
                  <th scope="col">Divison</th>
                  <th scope="col">Department</th>
                  <th scope="col">Locations</th>
                  <th scope="col">Designations</th>

                </tr>
                <?php foreach($addemp as $emdata){
                  $hire_date = $emdata['joining_date']!="" ? fromDate($emdata['joining_date']) : "";
                  $termination_date = $emdata['date_of_leaving']!="" ? fromDate($emdata['date_of_leaving']) : "";?>
                  <tr>
                    <td><?php echo $sl; $sl++; ?></td>
                    <td>

                      <div class="d-inline-block align-middle">
                <?php if($emdata['image']!=""){ ?>
                  <img src="uploads/module/hr.webp" alt="user" class="img-radius img-40 align-top m-r-15">
                <?php }else{ ?>
                  <span class="img-radius d-inline-block img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($emdata['display_name']); ?></span>
                <?php } ?>
                <div class="d-inline-block">
                 <?php echo $emdata['display_name']; ?>
                 <p class="text-muted m-b-0 f-12"><?php echo $emdata['emp_code']; ?></p>
               </div>
             </div>
                   </td>
                   <td><?php echo $hire_date; ?></td>
                   <td><?php echo $emdata['division_name']; ?> </td>
                   <td><?php echo $emdata['department_name']; ?></td>
                   <td><?php echo $emdata['location_name']; ?></td>
                   <td><?php echo $emdata['designation_name']; ?></td>

                 </tr>
               <?php } ?>
             </table>
           </div>
         <?php } else{ ?>
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
    <div class="tab-pane" id="tabb2" role="tabpanel">
      <div class="card">
        <div class="card-body card-header">
        <h5>Terminations (<?php echo count((array)$exitemp); ?>)</h5>
        <?php  if(count((array)$exitemp)>0){ ?>
          <div class="table-responsive mt-5">
            <table class="table table-hover">
              <tr style="background-color: #eeeeee;">
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Termination  Date</th>
                <th scope="col">Divison</th>
                <th scope="col">Department</th>
                <th scope="col">Locations</th>
                <th scope="col">Designations</th>
                <th scope="col">Emploement Status</th>
              </tr>
              <?php $sl=1; foreach($exitemp as $emdata){
                $hire_date = $emdata['joining_date']!="" ? fromDate($emdata['joining_date']) : "";
                $termination_date = $emdata['date_of_leaving']!="" ? fromDate($emdata['date_of_leaving']) : "";

                ?>
                <tr>
                  <td><?php echo $sl; $sl++; ?></td>
                  <td>
                    <div class="d-inline-block align-middle">
                      <?php if($emdata['image']!=""){ ?>
                        <img src="uploads/module/hr.webp" alt="user" class="img-radius img-40 align-top m-r-15">
                      <?php }else{ ?>
                        <span class="img-radius d-inline-block img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($emdata['display_name']); ?></span>
                      <?php } ?>
                      <div class="d-inline-block">
                       <?php echo $emdata['display_name']; ?>
                       <p class="text-muted m-b-0 f-12"><?php echo $emdata['emp_code']; ?></p>
                     </div>
                   </div>
                 </td>
                 <td><?php echo $termination_date; ?></td>
                 <td><?php echo $emdata['division_name']; ?> </td>
                 <td><?php echo $emdata['department_name']; ?></td>
                 <td><?php echo $emdata['location_name']; ?></td>
                 <td><?php echo $emdata['designation_name']; ?></td>
                 <td><?php echo $emdata['employment_status_name']; ?></td>
               </tr>
             <?php } ?>
           </table>
         </div>
       <?php } else{ ?>
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
</div>
