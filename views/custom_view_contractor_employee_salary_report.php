
<?php
$_SESSION['apply_js']['table']='1';
if(isset($_POST['get_ecr_summary'])) {
    $pay_month_year = $_POST['pay_month_year'];
} else {
    $pay_month_year = date('F, Y');
}
$company_name = $_SESSION['config']['name'];
$pay_year = date('Y',strtotime($pay_month_year));
$pay_month = date('m',strtotime($pay_month_year));
 $vurl = API."?mode=togetcontractorctcreportdetails&pay_year=".$pay_year."&pay_month=".$pay_month;
$configesponse = callAPI($vurl);
$presulta = json_decode($configesponse,1);

$unique = $presulta['componant'];
$code_com = $presulta['code_com'];
if(count((array)$unique) !=0){
    ksort($unique);
}
function valueemtpydash($val){
    if($val==0 || $val==""){
        $result = "-";
    }elseif(!is_numeric($val)){
        $result = $val;
    }else{
        $result = customeNumberFormate(round($val));
    }
    return $result;
}

?>
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card ">
                <div class="card-block">
                <div class="card-title">
                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-12 col-12 mb-3">
                            <div class="col bg-light-primary ">
                                <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                    <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                                </span>
                                <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-9 col-sm-12 col-12">
                            <h4 class="card-label text-center f-w-700">Contractor Employee's Salary Details <?php echo date("M Y",strtotime($pay_month_year)); ?>
                                <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small>
                            </h4>
                        </div>
                        <div class="col-lg-3 col-md-12 col-sm-12 col-12">
                            <form method="post" action="#">
                                <div class="input-group input-group-button">
                                    <input aria-label="Pay Month Year" type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input"  class="form-control form-control-sm width1" style="font-size: 18px;height: 48px !important;padding: 10px 15px;">
                                    <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="dt-responsive table-responsive mt-2">

                    <table id="basic-btn" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col"> SL NO </th>
                                <th scope="col">Employee Code</th>
                                <th scope="col">Name</th>
                                <th scope="col">Data of Joining</th>
                                <th scope="col">GST</th>
                                <th scope="col">Monthly</th>
                                <th scope="col">One Time</th>
                                <th scope="col">Total</th>
                                <th scope="col">GST</th>
                                <th scope="col">Total Inv Amount</th>
                                <th scope="col">TDS</th>
                                <th scope="col">Net Payable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach((array)$presulta['data'] as $key=>$rows){ ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $rows['emp_code']; ?></td>
                                    <td><?php echo valueemtpydash(ucwords($rows['employee_name'])); ?></td>
                                    <td><?php echo valueemtpydash(fromDate($rows['joining_date'])); ?></td>
                                    <td><?php if($rows['gst_type']==1){ echo "No";
}else{ echo "Yes";
} ?></td>
                                    <td class="text-right"><?php echo valueemtpydash($rows['salary_actual_amount']); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash($rows['one_time_amount']); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash($rows['salary_actual_amount']+$rows['one_time_amount']); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash($rows['gst_amount']); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash($rows['salary_actual_amount']+$rows['one_time_amount']+$rows['gst_amount']); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash($rows['tds_amount']); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash($rows['salary_actual_amount']+$rows['one_time_amount']+$rows['gst_amount']-$rows['tds_amount']); ?></td>
                                </tr>
                            <?php $i++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
