<?php
/** Conditions to list out data in table **/
if(isset($_POST['get_ept_summary'])) {

    $pay_date_from = $_POST['pay_date_from'];
    $pay_date_to = $_POST['pay_date_to'];

} else {
    $pay_date_from = date('Y-m-d');
    $pay_date_to = date('Y-m-d');

}

$company_name = "Infisuite";
$basic_pay_component_id =1;
$prof_tax_component_id =  '18';
$pay_year_from = date('Y',strtotime($pay_date_from));
$pay_month_from = date('m',strtotime($pay_date_from));
$pay_year_to = date('Y',strtotime($pay_date_to));
$pay_month_to = date('m',strtotime($pay_date_to));
$whrcon["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
$vdata = urlencode(json_encode($whrcon));
$url = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
$response = callAPI($url);
$empdata = json_decode($response,1);
foreach ($empdata as  $data) {

    $emp_code = $data['emp_code'];
    $employee_id = $data['id'];
    $emp_display_name = $data['display_name'];
    $emp_pf_no = $data['pf_no'];

    /**  get employee basic salary **/
    $vurl = API."?mode=togetsumofempsalaryoncomponentidbtwdates&company_id=".$_SESSION['company_id']."&employee_id=".$employee_id."&component_id=".$prof_tax_component_id."&pay_month_from=".$pay_month_from."&pay_year_from=".$pay_year_from."&pay_month_to=".$pay_month_to."&pay_year_to=".$pay_year_to;
    $configesponse = callAPI($vurl);
    $emp_salary_array = json_decode($configesponse,1);
    $emp_ptax = $emp_salary_array[0]['totalactual'];

    $emp_ptax_list[] = array('emp_code'=>$emp_code,'emp_display_name'=>$emp_display_name,'emp_ptax'=>$emp_ptax);
}

?>

<div class="row">
    <div class="col-md-8">
        <div class="col bg-light-primary px-6 py-8 rounded-xl mr-7 mb-7">
            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">

                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <polygon points="0 0 24 0 24 24 0 24" />
                        <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                        <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero" />
                    </g>
                </svg>
                <!--end::Svg Icon-->
            </span>
            <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>

        </div>

    </div>
    <div class="col-md-4">
        <form method="post" action="">
            <div class="form-group row">
                <label for="example-date-input-from" class="col-2 col-form-label">From</label>
                <div class="col-10">
                    <input aria-label="Pay Date From" class="form-control" type="date" name="pay_date_from" value="<?php echo $pay_year."-".$pay_month."-01";?>"  id="example-date-input-from" />
                </div>
            </div>
            <div class="form-group row">
                <label for="example-date-input-to" class="col-2 col-form-label">To</label>
                <div class="col-10">
                    <input aria-label="Pay Date To" class="form-control" type="date" name="pay_date_to" value="<?php echo $pay_year."-".$pay_month."-01";?>"  id="example-date-input-to" />
                </div>
            </div>


            <input type="submit" name="get_ept_summary" class="btn btn-success mr-2">
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
            <h3 class="card-label text-center">PT Summary
                <span class="d-block text-muted pt-2 font-size-sm">Contribution period from <?php echo $pay_date_from; ?> to <?php echo $pay_date_to; ?></span></h3>
            </div>
        </div>
        <div class="card-body">
            <!--begin: Datatable-->
            <div id="kt_datatable_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-hover table-checkable mt-10 dataTable no-footer dtr-inline" id="kt_datatable" aria-describedby="kt_datatable_info" style="width: 1236px;">
                            <thead>
                                <tr role="row" style="background-color: #f7f7f7;">
                                    <th scope="col">Employee ID</th>
                                    <th scope="col">Employee Name</th>
                                    <th scope="col">PT Amount</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                if(!empty($emp_ptax_list)) {
                                    foreach ($emp_ptax_list as $ptax) { ?>
                                        <tr role="row" class="even">
                                            <th scope="col"><?php echo $ptax['emp_code']; ?></th>
                                            <th scope="col"><?php echo $ptax['emp_display_name']; ?></th>
                                            <th scope="col"><?php echo 0; ?></th>

                                            <th scope="col"><?php echo round($ptax['emp_ptax'],2); ?></th>
                                        </tr>
                                    <?php }
                                }?>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!--end: Datatable-->
        </div>
    </div>
