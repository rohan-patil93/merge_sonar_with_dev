<?php
$_SESSION['apply_js']['multiselest'] = "1";
$pagingrecord = 20;
$_POST['year_filter'] = $_POST['year_filter'] !=""?$_POST['year_filter']:date('Y');
$url = API."?mode=getemployeeleavebalancereports&employee_id_filter=".$_POST['employee_id_filter']."&employee_filter=".$_POST['employee_filter']."&row_filter=".$row_filter."&year_filter=".$_POST['year_filter'];
$api = callAPI($url);
$result = json_decode($api,1);
$emp_leave_balances = $result['emp_leave_balances'];
foreach($emp_leave_balances as $elb){
    $emp_leave_balance[$elb['employee_id']][] = $elb;
}
foreach($result['employees'] as $empd){
    $employees[$empd['id']] = $empd;
}
$pagecnt = ceil(count((array)$emp_leave_balance)/$pagingrecord);
?>
<script type="text/javascript">
function printReport(divId) {
    var printContents = $("#" + divId).html();
  var printWindow = window.open('', '_blank');
  printWindow.document.write('<html lang="en"><head><title>Employee Leave Balance Report</title></head><body>' + printContents + '</body></html>');
  printWindow.document.close(); // close the document stream
  printWindow.print();
  printWindow.onafterprint = function() {
      printWindow.close();
  };
}
function showRecord(page){
    $(".remove_active_page").removeClass('active');
    $("#activepage_"+page).addClass('active');
    $(".all_hide_record").hide();
    $(".paging_row_"+page).show('slow');
    $("#show_paging_no").val(page);
}
function showPrevious(){
    var showpageno = parseInt($("#show_paging_no").val());
    if(showpageno!=1){
        showpageno = showpageno-1;
        showRecord(showpageno);
    }
}
function showNext(){
    var showpageno = parseInt($("#show_paging_no").val());
    if(showpageno!=<?php echo $pagecnt; ?>){
        showpageno = showpageno+1;
    }
    showRecord(showpageno);
}

</script>
<div class="card">
    <div class="card-header">
        <h5>Employee Leaves</h5>
        <div class="card-header-right table-card-header ">
      <button type="button" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</button>
      </div>
    </div>
    <div class="card-body">
        <div class="row">
        <form method="post">
            <div class="row">
                <div class="col-lg-2">
                    <div class=" form-group">
                        <span class="form-label">Employee</span>
                        <select aria-label="Employee  Filter" class="js-example-basic-single form-control"  name="employee_id_filter">
                            <option value="">All Employees</option>
                            <?php foreach($employees as $cust){ ?>
                                <option value="<?php echo $cust['id'];?>" <?php echo $_POST['employee_id_filter'] == $cust['id']?"selected":""; ?>><?php echo $cust['display_name'];?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class="form-group w-full">
                        <span class="form-label ">Employee Name/Emp Code </span>
                        <input aria-label="Employee Filter" type="text" class="form-control" name="employee_filter"  value="<?php echo $_POST['employee_filter'];?>" >
                    </div>
                </div>
                <div class="col-lg-2">
                    <div class=" form-group">
                        <span class="form-label">Year</span>
                        <select aria-label="Year Filter" class="js-example-basic-single form-control"  name="year_filter">
                            <?php
                            $year = date('Y')-10;
                             for($y=1; $y <= 11; $y++){  ?>
                                <option value="<?php echo $year; ?>" <?php echo $_POST['year_filter'] == $year?"selected":""; ?>><?php echo $year; ?></option>
                            <?php $year++;
} ?>
                        </select>
                    </div>
                </div>

                <div class="col-lg-2 text-right">
                    <button type="submit" class="input-group-text addon-style cursor-pointer float-left btn btn-primary m-t-30" name="searchfilter"> <i class="ti-search"></i> Search</button>
                </div>
            </div>
        </form>
    </div>
        <div id="print_report_div">
            <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
              <link rel="stylesheet" type="text/css" href="assets/css/style.css">
              <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
              <div class="row mt-2">
                  <h3 class="text-center text-primary">
                Employee Leaves (<?php echo date("Y"); ?>)
                <h4 class="f-14 d-block text-dark mt-1 text-center">View By : <?php echo $_SESSION['display_name']; ?></h4>
                <span class="f-10 text-center">Date & Time : <?php echo date("Y-m-d H:i:s"); ?></span>
                  </h3>
            </div>
            <?php $emc = 1; $pagerow=1;
            if(count((array)$emp_leave_balance) !="0"){
            foreach($emp_leave_balance as $emp_id=>$elbal){
                if($emc == $pagingrecord){
                    $pagerow++;
                    $emc = 0;
                }

             ?>
            <div class="paging_row_<?php echo $pagerow; ?> all_hide_record" <?php echo $pagerow =="1"?"":"style='display:none'"; ?>>
              <h4 class="text-start text-primary mt-5">
                <?php echo $employees[$emp_id]['display_name']; ?>
                <span class="f-14 d-block text-dark mt-1"><?php echo $employees[$emp_id]['emp_code'].' - ('.fromDate($employees[$emp_id]['joining_date']).")"; ?></span>

              </h4>
              <table class="table m-b-0" style="font-size:13px;">
                  <thead>
                      <tr>
                          <th scope="col" rowspan="2">#</th>
                          <th scope="col" rowspan="2">Leave type</th>
                          <th scope="col" rowspan="2" class="text-center">Open</th>
                          <th scope="col" colspan="2" class="text-center">Jan</th>
                          <th scope="col" colspan="2" class="text-center">Feb</th>
                          <th scope="col" colspan="2" class="text-center">Mar</th>
                          <th scope="col" colspan="2" class="text-center">Apr</th>
                          <th scope="col" colspan="2" class="text-center">May</th>
                          <th scope="col" colspan="2" class="text-center">Jun</th>
                          <th scope="col" colspan="2" class="text-center">Jul</th>
                          <th scope="col" colspan="2" class="text-center">Aug</th>
                          <th scope="col" colspan="2" class="text-center">Sep</th>
                          <th scope="col" colspan="2" class="text-center">Oct</th>
                          <th scope="col" colspan="2" class="text-center">Nov</th>
                          <th scope="col" colspan="2" class="text-center">Dec</th>
                          <th scope="col" rowspan="2" class="text-right">Allocated</th>
                          <th scope="col" rowspan="2" class="text-right text-danger">Consumed</th>
                          <th scope="col" rowspan="2" class="text-right">Balance</th>
                      </tr>
                      <tr>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>
                          <td class="text-right">A</td>
                          <td class="text-right text-danger">C</td>

                      </tr>
                   </thead>
                <tbody>
                    <?php $i=1; foreach($elbal as $emp_bal){ ?>
                    <tr>
                          <td class="p-2"><?php echo $i; ?></td>
                          <td class="p-2"><?php echo $emp_bal['leave_type_name']; ?></td>
                          <td class="text-right p-2 "><?php echo customePayslipNumberFormate($emp_bal['open_balance']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Jan']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Jan_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Feb']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Feb_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Mar']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Mar_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Apr']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Apr_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['May']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['May_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Jun']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Jun_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Jul']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Jul_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Aug']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Aug_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Sep']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Sep_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Oct']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Oct_c']); ?></td>
                          <td class="text-right p-2 "><?php echo customePayslipNumberFormate($emp_bal['Nov']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Nov_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['Dec']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['Dec_c']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['actual_allocated']); ?></td>
                          <td class="text-right p-2 text-danger"><?php echo customePayslipNumberFormate($emp_bal['consumed_leave']); ?></td>
                          <td class="text-right p-2"><?php echo customePayslipNumberFormate($emp_bal['close_balance']); ?></td>
                      </tr>
                    <?php $i++;
} ?>
                </tbody>
              </table>
          </div>
              <?php $emc++;
} ?>

        </div>
        <nav aria-label="Page navigation example ">

            <ul class="pagination justify-content-end mt-4">
                <li class="page-item">
                    <button type="button" class="page-link" tabindex="-1" onclick="showPrevious()">Previous</button>
                </li>
                <?php for($c=1; $c <= $pagecnt; $c++){ ?>
                <li class="page-item remove_active_page <?php echo $c == '1'?"active":""; ?>" id="activepage_<?php echo $c; ?>"><button type="button" class="page-link" onclick="showRecord(<?php echo $c; ?>)"><?php echo $c; ?></button></li>
                <?php } ?>
                <li class="page-item">
                    <button type="button" class="page-link" onclick="showNext()">Next</button>
                </li>
                <input type="hidden" id="show_paging_no" value="1">
            </ul>
        </nav>
        <div class="row mt-5 m-l-5">
            <button class="btn waves-effect waves-light btn-grd-success col-lg-2" onclick="printReport('print_report_div');"><i class="fa fa-print"></i> &nbsp Print Report</button>
          </div>
      <?php }else{ ?>
          <div class="card-px text-center ">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-alert"></i> No Employee Leaves</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
            </div>
        </div>
      <?php } ?>
    </div>
</div>
