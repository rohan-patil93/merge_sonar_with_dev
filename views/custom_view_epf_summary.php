<?php

if(isset($_POST['get_ecr_summary'])) {
    $pay_month_year = $_POST['pay_month_year'];
} else {
    $pay_month_year = date('F, Y');
}
$company_name = $_SESSION['config']['name'];
$company_id = $_SESSION['company_id'];
$pay_year = date('Y',strtotime($pay_month_year));
$pay_month = date('m',strtotime($pay_month_year));
 $vurl1 = API."?mode=togetepfecrsummary&company_id=".$company_id."&pay_year=".$pay_year."&pay_month=".$pay_month."&component_code=".PF_CODE;
$configesponse = callAPI($vurl1);
$ecr_list = json_decode($configesponse,1);
$unique = array();
if(!empty((array)$ecr_list['componant_code'])){
    $unique = $ecr_list['componant_code'];
}elseif(!empty((array)$ecr_list['componant'])){
    $unique = $ecr_list['componant'];
}
$unique = array_values(array_unique((array)$unique));

$all_ecr_list = !empty((array)$ecr_list['list']) ? $ecr_list['list'] : array();
$pay_area_wise_list = array();
$pay_area_ids = array();
foreach((array)$all_ecr_list as $emp_code => $emp_rows){
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
if(!empty((array)$ecr_list['pay_areas'])){
    foreach((array)$ecr_list['pay_areas'] as $pay_area){
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

if(!function_exists('render_epf_summary_table')){
    function render_epf_summary_table($report_rows, $unique){
        $total_columns = 6 + count((array)$unique);
        ?>
        <div class="dt-responsive table-responsive">
            <table id="basic-btn" class="table table-striped table-bordered nowrap">
                <thead>
                    <tr role="row" style="background-color: #f7f7f7;">
                        <th scope="col" rowspan="2">Employee Code</th>
                        <th scope="col" rowspan="2">Employee Name</th>
                        <th scope="col" rowspan="2">PF Account Number</th>
                        <th scope="col" rowspan="2" class="text-right">PF Wages</th>
                        <th scope="col" colspan="2">Employee's Contribution</th>
                        <th scope="col" colspan="<?php echo count((array)$unique); ?>">Employer's Contribution</th>
                    </tr>
                    <tr>
                        <th scope="col" class="text-right">PF Amount</th>
                        <th scope="col" class="text-right">VPF Amount</th>
                        <?php foreach((array)$unique as $names){ ?>
                            <th scope="col" class="text-right"><?php echo $names; ?></th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($report_rows)){
                        foreach($report_rows as $ecr_data){
                            $employee_rows = (array)$ecr_data;
                            $first_row = $employee_rows[0];
                            $employer_contributions = array();
                            foreach($employee_rows as $row){
                                $employer_contributions[$row['component_short_name']] = $row['esbi_employer_contributions'];
                            }
                            ?>
                            <tr role="row" class="odd">
                                <th scope="col"><?php echo valueemtpydash($first_row['emp_code']); ?></th>
                                <th scope="col"><?php echo valueemtpydash($first_row['Employee_Name']); ?></th>
                                <th scope="col"><?php echo valueemtpydash($first_row['pf_uan']); ?></th>
                                <th scope="col" class="text-right"><?php echo valueemtpydash(customeummarmonyformate($first_row['epf_base_amount'])); ?></th>
                                <th scope="col" class="text-right"><?php echo valueemtpydash(customeummarmonyformate($first_row['pf_salary_component_actual'])); ?></th>
                                <td class="">-</td>
                                <?php foreach((array)$unique as $names1){ ?>
                                    <td class="text-right">
                                        <?php
                                        $percentage = isset($employer_contributions[$names1]) ? $employer_contributions[$names1] : "";
                                        echo $percentage === "" ? "-" : valueemtpydash(customeummarmonyformate(round(($percentage * $first_row['epf_base_amount']) / 100)));
                                        ?>
                                    </td>
                                <?php } ?>
                            </tr>
                            <?php
                        }
                    }else{
                        echo "<tr role='row'><td colspan='".$total_columns."'><h4 class='text-center mt-4'><i class='fa fa-warning'></i>No EPF Summary Reports</h4></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

?>
<div class="flex-column-fluid">
    <div class="container-fluid">
        <div class=" flex-row ">
            <div class="row">

            </div>
            <div class="card card-custom ">
                <div class="card-header flex-wrap border-0 pt-4 pb-0 pl-5">
                    <div class="card-title  mb-4">
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
                                <h4 class="card-label text-center f-w-700">EPF Summary Report for <?php echo date("M Y",strtotime($pay_month_year)); ?>
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
                    <div class="card-body">
                        <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <?php if(count((array)$pay_area_ids) > 0){ ?>
                                    <ul class="nav nav-tabs mb-3">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#summary-tab-all" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">All</a>
                                        </li>
                                        <?php foreach((array)$pay_area_ids as $pay_area_id){ ?>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#summary-tab-<?php echo $pay_area_id; ?>" role="tab" aria-selected="false"><?php echo htmlspecialchars($pay_area_names[$pay_area_id] ?? ('PayArea-'.$pay_area_id)); ?></a>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                    <?php } ?>

                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="summary-tab-all" role="tabpanel">
                                            <?php render_epf_summary_table($all_ecr_list, $unique); ?>
                                        </div>
                                        <?php foreach((array)$pay_area_ids as $pay_area_id){ ?>
                                            <div class="tab-pane fade" id="summary-tab-<?php echo $pay_area_id; ?>" role="tabpanel">
                                                <?php render_epf_summary_table((array)$pay_area_wise_list[$pay_area_id], $unique); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div></div>

                            <!--end: Datatable-->
                        </div>
                    </div>
                </div></div></div>
