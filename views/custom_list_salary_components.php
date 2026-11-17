
<?php
$vurl = API."?mode=testingfunction";
$configesponse = callAPI($vurl);
$resulta = json_decode($configesponse,1);
$unique = $resulta['componant'];
function valueemtpydash($val){
    $val = $val ?? [];
    return $val==""?" - ":$val;
}

?>
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card j-pro">
                <div class="card-header">
                    <h5><?= __('Salary Components'); ?></h5>
                    <div class="card-header-right">
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            <?= __('Back'); ?>
                        </a>
                    </div>
                </div>
                <div class="dt-responsive table-responsive mt-2">
                    <table id="new-cons" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col"><?= __('Emp Code'); ?></th>
                                <th scope="col">display_name</th>
                                <th scope="col"><?= __('First Name'); ?></th>
                                <th scope="col"><?= __('Middle Name'); ?></th>
                                <th scope="col"><?= __('Last Name'); ?></th>
                                <th scope="col"><?= __('Joining Sate'); ?></th>
                                <th scope="col"><?= __('Designation'); ?></th>
                                <th scope="col"><?= __('Department Name'); ?></th>
                                <th scope="col"><?= __('Verticals Name'); ?></th>
                                <th scope="col"><?= __('band Name'); ?></th>
                                <th scope="col"><?= __('level Name'); ?></th>
                                <th scope="col"><?= __('location Name'); ?></th>
                                <th scope="col"><?= __('role Name'); ?></th>
                                <th scope="col"><?= __('job Title'); ?></th>
                                <th scope="col"><?= __('employment Category'); ?></th><th scope="col"><?= __('employee Type'); ?></th>
                                <th scope="col"><?= __('email Id'); ?></th>
                                <th scope="col"><?= __('Gender'); ?></th>
                                <th scope="col"><?= __('Marital Status'); ?></th>
                                <th scope="col"><?= __('Wedding Date'); ?></th>
                                <th scope="col"><?= __('Date Of Birth'); ?></th>
                                <th scope="col"><?= __('Role'); ?></th>
                                <th scope="col"><?= __('Father Name'); ?></th>
                                <th scope="col"><?= __('Spouse Name'); ?></th>
                                <th scope="col"><?= __('Blood Group'); ?></th>
                                <th scope="col"><?= __('Pan Number'); ?></th>
                                <th scope="col"><?= __('Pan Issue Date'); ?></th>
                                <th scope="col"><?= __('PF UAN'); ?></th>
                                <th scope="col"><?= __('P UAN Issue Date'); ?></th>
                                <th scope="col"><?= __('PF No.'); ?></th>
                                <th scope="col"><?= __('PF Issue Date'); ?></th>
                                <th scope="col"><?= __('Nationality'); ?></th>
                                <th scope="col"><?= __('Disability'); ?></th>
                                <th scope="col"><?= __('Company Cost Yearly'); ?></th>
                                <th scope="col"><?= __('Company Cost Monthly'); ?></th>
                                <?php foreach($unique as $names){ ?>
                                    <?php if($_GET['MONTH']==1){ ?><th scope="col"><?php echo $names."_Month"; ?></th> <?php
} ?>
                                    <th scope="col"><?php echo $names; ?></th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($resulta['list'] as $key=>$rows){   $rows = $rows[0];?>
                                <tr><td><?php echo valueemtpydash($rows['emp_code']); ?></td>
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo valueemtpydash($rows['first_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['middle_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['last_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['joining_date']); ?></td>
                                    <td><?php echo valueemtpydash($rows['designation_name']); ?></td>

                                    <td><?php echo valueemtpydash($rows['department_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['verticals_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['band_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['level_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['location_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['role_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['job_title']); ?></td>
                                    <td><?php echo valueemtpydash($rows['employment_category']); ?></td>
                                    <td><?php echo valueemtpydash($rows['employee_type']); ?></td>
                                    <td><?php echo valueemtpydash($rows['email_id']); ?></td>
                                    <td><?php echo valueemtpydash($rows['gender']); ?></td>
                                    <td><?php echo valueemtpydash($rows['marital_status']); ?></td>
                                    <td><?php echo valueemtpydash($rows['wedding_date']); ?></td>
                                    <td><?php echo valueemtpydash($rows['date_of_birth']); ?></td>
                                    <td><?php echo renderSensitiveDataWithToggle($rows['adhar_no'], 'aadhaar'); ?></td>
                                    <td><?php echo valueemtpydash($rows['father_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['spouse_name']); ?></td>
                                    <td><?php echo valueemtpydash($rows['blood_group']); ?></td>
                                    <td><?php echo renderSensitiveDataWithToggle($rows['pan_number'], 'pan'); ?></td>
                                    <td><?php echo valueemtpydash($rows['pan_issue_date']); ?></td>
                                    <td><?php echo renderSensitiveDataWithToggle($rows['pf_uan'], 'uan_number'); ?></td>
                                    <td><?php echo valueemtpydash($rows['pf_uan_issue_date']); ?></td>
                                    <td><?php echo renderSensitiveDataWithToggle($rows['pf_no'], 'pf_number'); ?></td>
                                    <td><?php echo valueemtpydash($rows['pf_issue_date']); ?></td>
                                    <td><?php echo valueemtpydash($rows['nationality']); ?></td>
                                    <td><?php echo valueemtpydash($rows['disability']); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash( number_format($rows['company_cost_yearly'])); ?></td>
                                    <td class="text-right"><?php echo valueemtpydash( number_format($rows['company_cost_monthly'])); ?></td>
                                    <?php foreach($unique as $names1){
                                        $listvalue = filterArrayValue($resulta['list'][$key],'name',$names1);
                                        if($listvalue[0]['yearly_amount']==""){
                                            $dis_value = "-";
                                            $dis_month_value = "-";
                                            $dis_css = "text-center";
                                        }else{
                                            $dis_value = number_format($listvalue[0]['yearly_amount']);
                                            $dis_month_value = number_format($listvalue[0]['monthly_amount']);
                                            $dis_css = "text-right";
                                        }
                                        ?>
                                        <?php if($_GET['MONTH']==1){ ?> <td class="<?php echo $dis_css; ?>"><?php echo $dis_month_value; ?></td> <?php
} ?>
                                        <td class="<?php echo $dis_css; ?>"><?php echo $dis_value; ?></td>
                                    <?php } ?>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
