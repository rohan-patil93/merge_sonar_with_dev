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
  $assessment_year = $_POST['assessment_year'];
  $postdata = $_POST;
}else{
  $_POST['fromdate'] = date("Y-m-01");
  $fromdate = fromDate($_POST['fromdate']);
  $_POST['todate'] = date("Y-m-t");
  $todate = fromDate($_POST['todate']);
  $_POST['assessment_year'] = date('Y');
  $assessment_year = $_POST['assessment_year'];
  $postdata = $_POST;
}
$url = API."?mode=togetstandaredtimeoffbalances&method=post";
$apires = file_post_contents($url, $postdata);
$result = json_decode($apires,1);
$menus = $result['menu_data'];
$emp_details = $result['emp_data'];
$_SESSION['download_data'] = array();
$_SESSION['apply_js']['multiselest'] = 1;
?>

<div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5 class="f-30">Time Off Balances</h5>
      <div class="card-header-right">
        <div class="dropdown-primary dropdown open">
          <button class="btn btn-primary dropdown-toggle waves-light" type="button" id="dropdown-2" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">More</button>
          <div class="dropdown-menu p-0" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="">

            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Time Off Balances Report" target="_blank" >CSV</a>
            <a class="dropdown-item waves-light waves-effect" href="custom_view_download.php?page=Time Off Balances Report" target="_blank">Excel</a>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <div class="row">
        <form method="post" action="">
          <div class="row col-lg-12 ">
        <div class="col-lg-2">

         <select aria-label="Assessment Year" class=" form-select js-example-basic-single  border p-1" name="assessment_year">
          <?php for($i=date('Y')-3; $i<date('Y')+2;$i++){ ?>
              <option <?php if($assessment_year == $i){ echo  "selected";
} ?> value="<?php echo  $i; ?>"><?php echo  $i; ?></option>
          <?php } ?>

        </select>
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
            <th scope="col">Beginning Balance</th>
            <th scope="col">Accrued </th>
            <th scope="col">Used</th>
            <th scope="col">Scheduled  </th>
            <th scope="col">Adjustments</th>
            <th scope="col">Ending Balance</th>
          </tr>
          <?php $old_id=0; $total =0; $sl=1;
          foreach($emp_details as $emdata ){
          $new_id = $emdata['id'];
          $cssdata[] = array('Sl No'=> $sl,'Employee Name'=> $emdata['display_name'], 'Employee Code'=> $emdata['emp_code'], 'Beginning Balance'=>$emdata['begining'], 'Accrued'=> $emdata['applicable_leaves'], 'Used'=>$emdata['consumed_leaves'], 'Scheduled'=> $emdata['schedule'],'Adjustments'=> $emdata['adjustment'],'Ending Balance'=>$emdata['applicable_leaves']- $emdata['consumed_leaves']);
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
            <td class="text-right"><?php echo $emdata['begining']; ?></td>
            <td class="text-right"><?php echo $emdata['applicable_leaves']; ?></td>
            <td class="text-right"><?php echo $emdata['consumed_leaves']; ?></td>
            <td class="text-right"><?php echo $emdata['schedule']; ?></td>
            <td class="text-right"><?php echo $emdata['adjustment']; ?></td>
            <td class="text-right"><?php echo $emdata['applicable_leaves']- $emdata['consumed_leaves']; ?></td>
          </tr>
          <?php $sl++; $total = $total+($emdata['applicable_leaves']- $emdata['consumed_leaves']);
} $_SESSION['download_data'] = $cssdata; ?>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>

            <td class="text-right">Total</td>
            <td class="text-right" ><?php echo $total; ?> </th>
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
