<?php
if(isset($_POST['get_ecr_summary'])) {
    $pay_month_year = $_POST['pay_month_year'];
} else {
    $pay_month_year = date('F, Y');
}

$dataorg["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$datw = urlencode(json_encode($dataorg));
$curl1 = API."?mode=gettabledetailswithconditions&table=config&data=".$datw;
$cudata1 = callAPI($curl1);
$org_details = json_decode($cudata1,1);
$company_name = $org_details[0]['name'];
$epf_remitted_component_id = 2;
$eps_remitted_component_id = 3;
$company_id=1;
$pay_year = date('Y',strtotime($pay_month_year));
$pay_month = date('m',strtotime($pay_month_year));

 $vurl1 = API."?mode=togetepfecrsummary&company_id=".$company_id."&pay_year=".$pay_year."&pay_month=".$pay_month."&epf_remitted_component_id=".$epf_remitted_component_id."&eps_remitted_component_id=".$eps_remitted_component_id;
$configesponse = callAPI($vurl1);
$ecr_list = json_decode($configesponse,1);
?>
<div class="flex-column-fluid j-wrapper">
<div class="container-fluid j-pro">
<div class=" flex-row j-content">

<div class="row ">
        <div class="col-md-7">
            <div class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
            <i class="fa fa-user-plus text-info f-40"></i>
                                                            </span>
                                                        <span class="text-dark-75 font-weight-bolder  font-size-h2 "><?php echo $company_name; ?></span>


                                                        </div>

        </div>
        <div class="col-md-5">
            <form method="post" action="#">
                <div class="form-group row">
                    <label for="example-month-input" class="col-2 f-w-700 mt-3 form-label"><?= __('Month'); ?></label>
                    <div class="col-7">
                        <input class="form-control" type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input" />
                    </div>
                    <div class="col-3">
                    <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light"><?php echo __('Submit'); ?></button>
                </div>
                </div>

            </div>
        </form>
    </div>




    <div class="subheader py-2 py-lg-6 subheader-transparent" id="kt_subheader">
        <div class="container d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Mobile Toggle-->
                <button class="burger-icon burger-icon-left mr-4 d-inline-block d-lg-none" id="kt_subheader_mobile_toggle">
                    <span></span>
                </button>
                <!--end::Mobile Toggle-->
                <!--begin::Page Heading-->

                <!--end::Page Heading-->
            </div>
            <!--end::Info-->

        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header flex-wrap border-0 pt-4 pb-0 pl-5 mx-auto">
            <div class="card-title">
                <h3 class="card-label text-center f-w-700"><?= __('EPF-ECR Report for'); ?> <?php echo $pay_month_year; ?>
            </h3>
            </div>
        </div>
            <div class="card-body dt-responsive table-responsive">
            <!--begin: Datatable-->
            <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover table-checkable mt-10 dataTable no-footer dtr-inline" id="kt_datatable" aria-describedby="kt_datatable_info" style="width: 1236px;">
                            <thead>
                                <tr role="row" bgcolor="#f7f7f7">
                                    <th scope="col"><?= __('Employee ID'); ?></th>
                                    <th scope="col"><?= __('Uan'); ?></th>
                                    <th scope="col"><?= __('Member Name'); ?></th>
                                    <th scope="col"><?php echo __('Gross Wages'); ?></th>
                                    <th scope="col"><?php echo __('EPF Wages'); ?></th>
                                    <th scope="col"><?php echo __('EPS Wages'); ?></th>
                                    <th scope="col"><?php echo __('EDLI Wages'); ?></th>
                                    <th scope="col" style="text-align:right"><?= __('EPF Contribution Remitted'); ?></th>
                                    <th scope="col" style="text-align:right"><?= __('EPS Contribution Remitted'); ?></th>
                                    <th scope="col" style="text-align:right"><?= htmlspecialchars(__('EPF & EPS Diff Remitted'), ENT_QUOTES, 'UTF-8'); ?></th>
                                    <th scope="col"><?php echo __('NCP Days'); ?></th>
                                    <th scope="col"><?= __('Refund Of Advances'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($ecr_list)) {
                                        foreach ($ecr_list as $ecr_data) { ?>
                                <tr role="row" class="odd">
                                    <th scope="col"><?php echo $ecr_data['employee_id']; ?></th>
                                    <th scope="col">---<?= __('TBD'); ?>---</th>
                                    <th scope="col"><?php echo $ecr_data['employee_name']; ?></th>
                                    <th scope="col"><?php echo $ecr_data['emp_gross']; ?></th>
                                    <th scope="col"><?php echo $ecr_data['epf_wages']; ?></th>
                                    <th scope="col"><?php echo $ecr_data['eps_wages']; ?></th>
                                    <th scope="col"><?php echo $ecr_data['edli_wages']; ?></th>

                                    <th scope="col"><?php echo $ecr_data['epf_remitted']; ?></th>
                                    <th scope="col"><?php echo $ecr_data['eps_remitted']; ?></th>
                                    <th scope="col"><?php echo $ecr_data['epf_eps_diff']; ?></th>
                                    <th scope="col">---<?= __('TBD'); ?>---</th>
                                    <th scope="col">---<?= __('TBD'); ?>---</th>
                                </tr>

                                <?php
                            }
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end: Datatable-->
        </div>
    </div>    </div></div></div>



