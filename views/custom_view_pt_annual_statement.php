<?php
$_SESSION['apply_js']['table']= 1;
 $company_start = date('Y', strtotime($_SESSION['config']['created_date']));
 $list_year =  date('Y')-1;
if(isset($_POST['get_ecr_summary'])) {
    $pay_year = $_POST['pay_year'];
} else {
    $pay_year = $list_year;
}

$start_month_year_ar = explode("-", $start_month_year);
$end_month_year_ar =explode("-", $end_month_year);
$company_name = $_SESSION['config']['name'];
$company_id = $_SESSION['company_id'];

$salary_component_id = GROSS_SALARY;
$start_year = $start_month_year_ar[0];
$end_year = $end_month_year_ar[0];
$start_month = $start_month_year_ar[1];
$end_month = $end_month_year_ar[1];


 $vurl1 = API."?mode=togetptanualstatment&company_id=".$company_id."&pay_year=".$pay_year."&component_code=".PT_CODE;
$configesponse = callAPI($vurl1);
$ecr_lists = json_decode($configesponse,1);
$ecr_list = !empty((array)$ecr_lists['data']) ? $ecr_lists['data'] : array();

$all_ecr_list = array();
$pay_area_wise_list = array();
$pay_area_ids = array();
foreach((array)$ecr_list as $period => $period_rows){
    $total_emp = 0;
    $total_sumpt = 0;
    foreach((array)$period_rows as $period_row){
        $row_data = (array)$period_row;
        $total_emp += (float)($row_data['emp'] ?? 0);
        $total_sumpt += (float)($row_data['sumpt'] ?? 0);
        $pay_area_id = trim((string)($row_data['pay_area_id'] ?? ''));
        if($pay_area_id !== ''){
            $pay_area_ids[$pay_area_id] = $pay_area_id;
            $pay_area_wise_list[$pay_area_id][$period] = array(array(
                'emp' => $row_data['emp'] ?? 0,
                'sumpt' => $row_data['sumpt'] ?? 0,
                'pay_area_id' => $pay_area_id,
            ));
        }
    }
    $all_ecr_list[$period] = array(array(
        'emp' => $total_emp,
        'sumpt' => $total_sumpt,
    ));
}
ksort($pay_area_ids);
$pay_area_names = array();
if(!empty((array)$ecr_lists['pay_areas'])){
    foreach((array)$ecr_lists['pay_areas'] as $pay_area){
        $map_id = trim((string)($pay_area['id'] ?? ''));
        $map_name = trim((string)($pay_area['name'] ?? ''));
        if($map_id != '' && $map_name != ''){
            $pay_area_names[$map_id] = $map_name;
        }
    }
}

function valueemtpydash($val){
    $val = $val ?? [];
    return $val=="" || $val=="0"?" - ":$val;
}
$now_year = date('Y');

$professional_tax_payments = $ecr_lists['professional_tax_payments'];
foreach($professional_tax_payments as $ptp){
    $ymdata[$ptp['pay_year']][$ptp['pay_month']] =  $ptp;
}

if(!function_exists('render_pt_annual_statement_table')){
    function render_pt_annual_statement_table($report_rows, $ymdata){
        ?>
        <div class="dt-responsive table-responsive">
            <table id="basic-btn" class=" table table-striped table-bordered nowrap">
                <thead>
                    <tr role="row" style="background-color: #f7f7f7;">
                        <th scope="col">#</th>
                        <th scope="col">Period</th>
                        <th scope="col" style="text-align:right">No. of Employee's</th>
                        <th scope="col" style="text-align:right">PT Amount</th>
                        <th scope="col" style="text-align:center;">PT Payment Number</th>
                        <th scope="col" style="text-align:right">PT Payment Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty((array)$report_rows)){
                        $m=1;
                        foreach($report_rows as $key=>$ecr_data){
                            $ecr_data = $ecr_data[0];
                            $yy = date('Y',strtotime($key));
                            $mm = date('n',strtotime($key));
                            ?>
                            <tr role="row" class="odd">
                                <td><?php echo $m; ?></td>
                                <td><?php echo $key; ?> - <?php echo date('t-m-Y',strtotime($key)); ?> </td>
                                <td style="text-align:right"><?php echo valueemtpydash($ecr_data['emp']); ?></td>
                                <td style="text-align:right"><?php echo valueemtpydash($ecr_data['sumpt']); ?></td>
                                <td style="text-align: center;"><?php echo valueemtpydash($ymdata[$yy][$mm]['ref_number'] ?? ''); ?></td>
                                <td style="text-align: right;"><?php echo valueemtpydash($ymdata[$yy][$mm]['amount'] ?? ''); ?></td>
                            </tr>
                        <?php $m++;
}
                    }else{ ?>
                        <tr role="row"><td colspan="6" class="text-center"><h4 class="mt-4"><i class="fa fa-warning"></i> No Professional Tax Annual Statement Reports</h4></td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}
?>
<div class="flex-column-fluid">
<div class="container-fluid ">
<div class=" flex-row ">
    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-4 pb-0 pl-5 ">
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
                        <h4 class="card-label text-center f-w-700">PT Annual Return Statement
                            <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small>
                        </h4>
                    </div>
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12">
                        <form method="post" action="#">
                            <div class="input-group input-group-button">

                                <select aria-label="Pay Year" class="js-example-basic-single form-control form-control-sm width1 table-card-header" style=" border: 1px solid #00bcd4;" name="pay_year" >

                                    <?php for($i=$list_year;$i<=$now_year;$i++){ ?>
                                    <option <?php if($pay_year == $i){ echo "selected";
} ?> value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php } ?>
                                </select>
                                 <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body ">
            <!--begin: Datatable-->
            <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <?php if(count((array)$pay_area_ids) > 0){ ?>
                    <ul class="nav nav-tabs mb-3">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#pt-annual-tab-all" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">All</a>
                        </li>
                        <?php foreach((array)$pay_area_ids as $pay_area_id){ ?>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#pt-annual-tab-<?php echo $pay_area_id; ?>" role="tab" aria-selected="false"><?php echo htmlspecialchars($pay_area_names[$pay_area_id] ?? ('PayArea-'.$pay_area_id)); ?></a>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pt-annual-tab-all" role="tabpanel">
                        <?php render_pt_annual_statement_table($all_ecr_list, $ymdata); ?>
                    </div>
                    <?php foreach((array)$pay_area_ids as $pay_area_id){ ?>
                        <div class="tab-pane fade" id="pt-annual-tab-<?php echo $pay_area_id; ?>" role="tabpanel">
                            <?php render_pt_annual_statement_table((array)$pay_area_wise_list[$pay_area_id], $ymdata); ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!--end: Datatable-->
        </div>
    </div>
</div></div></div>
