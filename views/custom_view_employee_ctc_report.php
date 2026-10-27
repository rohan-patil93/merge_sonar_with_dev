
<?php
$_SESSION['apply_js']['table']='1';
if(isset($_POST['get_ecr_summary'])) {
    $pay_month_year = $_POST['pay_month_year'];
} else {
    $pay_month_year = date('F, Y');
}
$company_name = $_SESSION['config']['name'];
$company_id = $_SESSION['company_id'];
$pay_year = date('Y',strtotime($pay_month_year));
$pay_month = date('m',strtotime($pay_month_year));
$vurl = API."?mode=getmonthlyctcreports&company_id=".$company_id."&pay_year=".$pay_year."&pay_month=".$pay_month;
$configesponse = callAPI($vurl);
$presulta = json_decode($configesponse,1);

$unique = $presulta['componant'];
$code_com = $presulta['code_com'];
if(count((array)$unique) !=0){
    ksort($unique);
}

$all_ctc_list = !empty((array)$presulta['list']) ? $presulta['list'] : array();
$pay_area_wise_list = array();
$pay_area_ids = array();
foreach((array)$all_ctc_list as $emp_code => $emp_rows){
    $pay_area_id = '';
    if(!empty((array)$emp_rows[0]['pay_area_id'])){
        $pay_area_id = trim((string)$emp_rows[0]['pay_area_id']);
    }
    if($pay_area_id !== ''){
        $pay_area_ids[$pay_area_id] = $pay_area_id;
        $pay_area_wise_list[$pay_area_id][$emp_code] = $emp_rows;
    }
}
ksort($pay_area_ids);
$pay_area_names = array();
if(!empty((array)$presulta['pay_areas'])){
    foreach((array)$presulta['pay_areas'] as $pay_area){
        $map_id = trim((string)($pay_area['id'] ?? ''));
        $map_name = trim((string)($pay_area['name'] ?? ''));
        if($map_id != '' && $map_name != ''){
            $pay_area_names[$map_id] = $map_name;
        }
    }
}
function valueemtpydash($val){
    $val = $val ?? [];
    return $val==""?" - ":$val;
}





foreach((array)$presulta['list'] as $key=>$rows){
    $sal =0;
    foreach((array)$unique as $names1){

        if($names1=='Net Salary'){
            $listvalue = filterArrayValue($presulta['list'][$key],'name',$names1);
            $avall = $listvalue[0]['salary_component_actual']==""?"0":$listvalue[0]['salary_component_actual'];
            $sal= $sal+ $avall;

        }
        if($names1=='Gross Salary'){
            $listvalue = filterArrayValue($presulta['list'][$key],'name',$names1);
            $avall = $listvalue[0]['salary_component_actual']==""?"0":$listvalue[0]['salary_component_actual'];
            $grosssal= $grosssal+ $avall;

        }
    }

    $grandtotal[$rows[0]['id']] = $sal;
    $grandgrosssal[$rows[0]['id']] = $grosssal;

}
//echo "<pre>"; print_r($grandtotal); exit;
$teremp = array();
if(!empty((array)$resulta['terminations'])){
    foreach((array)$resulta['terminations'] as $termination){
        $teremp[]=$termination['emp_id'];
    }
}
$banks = $presulta['banks'];

if(!function_exists('render_employee_ctc_table')){
    function render_employee_ctc_table($report_list, $unique, $banks, $teremp){
        $viewlist = array('Pay CTC', 'TOTAL LTA', 'Total Medical Insurance Premium', 'CTC', 'Variable Pay Per Year', 'ITAX Arrear');
        ?>
        <div class="table-responsive">
            <table class="table table-bordered custom-table-border display nowrap " style="width: 100%;">
            <thead>
                <tr>
                    <th scope="col">S.No.</th>
                    <th scope="col">Employee Code</th>
                    <th scope="col">Employee Name</th>
                    <th scope="col">DOJ</th>
                    <th scope="col">Location </th>
                    <th scope="col">Department</th>
                    <th scope="col">Designation</th>
                    <th scope="col">Job Title</th>
                    <th scope="col">PAN No.</th>
                    <th scope="col">UAN No.</th>
                    <th scope="col">Bank Name</th>
                    <th scope="col">IFSC Code</th>
                    <th scope="col">Bank Ac</th>
                    <th scope="col">Working Days</th>
                    <th scope="col">Payable Days</th>
                    <?php foreach((array)$unique as $aaarray ){ if(!in_array($aaarray,(array)$viewlist)){ ?>
                        <th scope="col"><?php echo $aaarray; ?></th>
                    <?php } ?>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($report_list)){ ?>
                    <?php $i=1; foreach((array)$report_list as $key=>$rows){
                        $rows = $rows[0];
                        $femp_id = $rows['id'];
                        ?>
                        <tr <?php if(in_array($femp_id,(array)$teremp)){ ?> class="bg-warning" <?php
}?> >
                            <td><?php echo $i; ?></td>
                            <td><?php echo valueemtpydash($rows['emp_code']); ?></td>
                            <td><?php echo valueemtpydash(ucwords($rows['Employee Name'])); ?></td>
                            <td><?php echo valueemtpydash(ucwords($rows['DOJ'])); ?></td>
                            <td><?php echo valueemtpydash($rows['Location']); ?></td>
                            <td><?php echo valueemtpydash($rows['Department']); ?></td>
                            <td><?php echo valueemtpydash($rows['Designation']); ?></td>
                            <td><?php echo valueemtpydash($rows['Job Title']); ?></td>
                            <td><?php echo $rows['pan_number']; ?></td>
                            <td><?php echo $rows['pf_uan']; ?></td>
                            <td><?php echo $banks[$rows['bank_id']]; ?></td>
                            <td><?php echo $rows['bank_link']; ?></td>
                            <td><?php echo $rows['bank_account_no']; ?></td>
                            <td class="text-right"><?php echo valueemtpydash($rows['Total Days']); ?></td>
                            <td class="text-right"><?php echo valueemtpydash($rows['Payable Days']); ?></td>
                            <?php foreach((array)$unique as $names1){
                                if(!in_array($names1,(array)$viewlist)){
                                    $listvalue = filterArrayValue($report_list[$key],'name',$names1);
                                    $aval =0;
                                    if(count((array)$listvalue)>0){
                                        $aval = $listvalue[0]['salary_component_actual']==""?"-":$listvalue[0]['salary_component_actual'];
                                    }
                                    ?>
                                    <td class="text-right">
                                    <?php
                                    if(isset($_GET['ROUND'])){
                                        echo customeNumberFormate(round($aval,$_GET['ROUND']));
                                    }else{
                                        echo customeNumberFormate(round($aval));
                                    }
                                    ?>
                                    </td>
                                <?php } ?>
                                <?php } ?>
                        </tr>
                    <?php $i++;
} ?>
                <?php }else{ ?>
                    <tr><td colspan="16" class="text-center"><h4 class="mt-4"><i class="fa fa-warning"></i> No Employee CTC Details</h4></td></tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
    }
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
                            <h4 class="card-label text-center f-w-700">Employee's CTC Details <?php echo date("M Y",strtotime($pay_month_year)); ?>
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
                    <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <?php if(count((array)$pay_area_ids) > 0){ ?>
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#ctc-tab-all" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">All</a>
                            </li>
                            <?php foreach((array)$pay_area_ids as $pay_area_id){ ?>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#ctc-tab-<?php echo $pay_area_id; ?>" role="tab" aria-selected="false"><?php echo htmlspecialchars($pay_area_names[$pay_area_id] ?? ('PayArea-'.$pay_area_id)); ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php } ?>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="ctc-tab-all" role="tabpanel">
                                <?php render_employee_ctc_table($all_ctc_list, $unique, $banks, $teremp); ?>
                            </div>
                            <?php foreach((array)$pay_area_ids as $pay_area_id){ ?>
                                <div class="tab-pane fade" id="ctc-tab-<?php echo $pay_area_id; ?>" role="tabpanel">
                                    <?php render_employee_ctc_table((array)$pay_area_wise_list[$pay_area_id], $unique, $banks, $teremp); ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
