<?php
$pt=1;
if(isset($_POST['submit_pt'])){
    $data['company_id'] = $_SESSION['company_id'];
    $locatin = explode("||", $_POST['wrok_location_id']);
    $data['work_location_id'] = $locatin['0'];
    $data['pt_number'] = $_POST['pt_number'];
    $data['deduction_cycle'] = $_POST['deduction_cycle'];
    $data['state_id'] = $_POST['state'];
    foreach ((array)$_POST['start_range'] as $start_range_key => $value) {
        $monthvalue = array();
        $insert_start_range['start_range'] = $value;
        $insert_start_range['end_range'] = $_POST['end_range'][$start_range_key];
        $insert_start_range['tax_amount'] =  $_POST['tax_amount'][$start_range_key];
        foreach($_POST['monthvalue'] as $monthky=>$valuemonth){
            $monthvalue[$monthky] = $_POST['monthvalue'][$monthky][$start_range_key];
        }
        $insert_start_range['months'] =  $monthvalue;
        $insert_start_ranges[] =$insert_start_range;
    }
    if($_POST['sameasval']==1){
        foreach ((array)$_POST['fstart_range'] as $start_range_key => $value) {
            $fmonthvalue = array();
            $finsert_start_range['start_range'] = $value;
            $finsert_start_range['end_range'] = $_POST['fend_range'][$start_range_key];
            $finsert_start_range['tax_amount'] =  $_POST['ftax_amount'][$start_range_key];
            foreach($_POST['fmonthvalue'] as $monthky=>$valuemonth){
                $fmonthvalue[$monthky] = $_POST['fmonthvalue'][$monthky][$start_range_key];
            }
            $finsert_start_range['months'] =  $fmonthvalue;
            $finsert_start_ranges[] =$finsert_start_range;
        }
    }else{
        $finsert_start_ranges = $insert_start_ranges;
    }
    foreach((array)$_POST['ptexemption'] as $checkkey=>$checkid){
        $arpostemption[] = $_POST['ptexemptionmin'][$checkkey];
    }
    $data['slabs'] = json_encode($insert_start_ranges,1);
    $data['male_slabs'] = json_encode($insert_start_ranges,1);
    $data['female_slabs'] = json_encode($finsert_start_ranges,1);
    $data['personal_tax_exemptions'] =json_encode($arpostemption,1);
    $data['femaie_check'] = $_POST['sameasval'];
    $data['created_by'] = $_SESSION['userid'];
    $data['is_active'] = '0';
    $data['created_date'] = nowDate();
    if($_POST['submit_pt'] == 'submit'){
        $data['created_by'] = $_SESSION['userid'];
        $data['created_date'] = nowDate();
        $data['current_status'] = '0';
        $url = API."?mode=adddata&method=post&table=professional_tax";
         $response = file_post_contents($url, $data, 'insert');
    }else{
        $data['updated_by'] = $_SESSION['userid'];
        $data['updated_date'] = nowDate();
        $url = API."?mode=updaterowtabledata&method=post&table=professional_tax&rid=".$_POST['rid'];
        $response = file_post_contents($url, $data, 'update');
    }
    $pt = '0';
}else{
    include_once "new_from_submit.php";
}
?>
<link rel="stylesheet" href="assets/css/custom_insert_esi.css">

    <div class=" ">
        <!--begin::Content-->
        <div class="flex-row-fluid ml-lg-12 ">
            <!--begin::Row-->

                <div class="card card-custom p-3">
                    <div class="row">

                        <div class="form-group   col-lg-12">
                            <div class="tab-content p-6" id="myTabContent5">
                                <div class="tab-pane fade active show" id="home-5" role="tabpanel" aria-labelledby="home-tab-5">
                                    <ul class="nav infisuite_tab md-tabs mb-5">
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $pt!='0'?'active':'';?>" data-bs-toggle="tab" href="#kt_tab_pane_1" role="tab">

                                                <span class="nav-text">ESI</span>
                                            </a>
                                            <div class="slide"></div>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                                                <span class="nav-text">PF</span>
                                            </a>

                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link <?php echo $pt=='0'?'active':'';?>" data-bs-toggle="tab" href="#kt_tab_pane_4" role="tab">

                                                <span class="nav-text">Professional Tax</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">

                                                <span class="nav-text">Labour Welfare Fund</span>
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="tab-content mt-5" id="myTabContent">
                                        <div class="tab-pane fade <?php echo $pt!='0'?'show active':'';?>" id="kt_tab_pane_1" role="tabpanel" aria-labelledby="kt_tab_pane_1">
                                            <?php include_once 'employee_state_insaurance.php'; ?>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                            <?php include_once 'custom_insert_gratuty.php'; ?>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel" aria-labelledby="kt_tab_pane_3">
                                            <?php include_once 'employee_provident_fund.php'; ?>
                                        </div>

                                        <div class="tab-pane fade <?php echo $pt=='0'?'show active':'';?>" id="kt_tab_pane_4" role="tabpanel" aria-labelledby="kt_tab_pane_4">
                                           <?php include_once 'custom_professional_tax.php'; ?>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel" aria-labelledby="kt_tab_pane_5">
                                           <?php include_once 'labour_welfare_fund.php'; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="home-6" role="tabpanel" aria-labelledby="home-tab-6">
                                    <form class="form">
                                        <div class="card card-custom gutter-b">
                                            <!--begin::Header-->
                                            <div class="card-header border-0 py-5">
                                                <h3 class="card-title align-items-start flex-column">
                                                    <span class="card-label font-weight-bolder text-dark">Bank</span>
                                                    <span class="text-muted mt-3 font-weight-bold font-size-sm">Experience in different organizations</span>
                                                </h3>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-group row">
                                                    <div class="col-lg-4">
                                                        <span>Bank Name</span>
                                                        <input aria-label="Enter employee ID" type="text" class="form-control" placeholder="Enter employee ID">

                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span>IFSC Code</span>
                                                        <input aria-label="Enter email" type="email" class="form-control" placeholder="Enter email">

                                                    </div>
                                                    <div class="col-lg-4">
                                                        <span>Username:</span>
                                                        <div class="input-group">
                                                           <div class="input-group-prepend"><span class="input-group-text"><i class="la la-user"></i></span></div>
                                                           <input aria-label="Username:" type="text" class="form-control" placeholder="Username:">
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                            <!--end::Header-->
                                            <!--begin::Body-->
                                            <div class="card-body pt-0 pb-3">
                                                <!--begin::Table-->
                                                <div class="table-responsive">
                                                    <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                                        <thead>
                                                            <tr class="text-uppercase">
                                                                <th scope="col" class="table-column-40 pl-7">
                                                                    <span class="text-dark-75">Company</span>
                                                                </th>
                                                                <th scope="col" class="table-column-min-15">From</th>
                                                                <th scope="col" class="table-column-min-15">To</th>
                                                                <th scope="col" class="table-column-min-15">Experience</th>
                                                                <th scope="col" class="table-column-min-15">Remarks</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td class="pl-0 py-8">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                            <div class="symbol-label symbol-label-img-26"></div>
                                                                        </div>
                                                                        <div>
                                                                            <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Sant Extreanet Solution</a>
                                                                            <span class="text-muted font-weight-bold d-block">HTML, JS, ReactJS</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">9 Years</span>
                                                                    <span class="text-muted font-weight-bold">10 Months</span>
                                                                </td>
                                                                <td>

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pl-0 py-0">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                            <div class="symbol-label symbol-label-img-3"></div>
                                                                        </div>
                                                                        <div>
                                                                            <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Telegram Development</a>
                                                                            <span class="text-muted font-weight-bold d-block">C#, ASP.NET, MS SQL</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">5 Years</span>
                                                                    <span class="text-muted font-weight-bold">4 Months</span>
                                                                </td>
                                                                <td>

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pl-0 py-8">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                            <div class="symbol-label symbol-label-img-5"></div>
                                                                        </div>
                                                                        <div>
                                                                            <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Payroll Application</a>
                                                                            <span class="text-muted font-weight-bold d-block">PHP, Laravel, VueJS</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">6 Years</span>
                                                                    <span class="text-muted font-weight-bold">3 Months</span>
                                                                </td>
                                                                <td>

                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="pl-0 py-0">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                            <div class="symbol-label symbol-label-img-18"></div>
                                                                        </div>
                                                                        <div>
                                                                            <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">HR Management System</a>
                                                                            <span class="text-muted font-weight-bold d-block">Python, PostgreSQL, ReactJS</span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                </td>
                                                                <td>
                                                                    <span class="text-dark-75 font-weight-bolder d-block font-size-lg">7 Years</span>
                                                                    <span class="text-muted font-weight-bold">5 Months</span>
                                                                </td>
                                                                <td>

                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <!--end::Table-->
                                            </div>
                                            <!--end::Body-->
                                        </div>
                                        <div class="card-footer">
                                            <div class="row">
                                                <div class="col-lg-4"></div>
                                                <div class="col-lg-8">
                                                    <button type="reset" class="btn btn-primary mr-2">Submit</button>
                                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="tab-pane fade" id="home-7" role="tabpanel" aria-labelledby="home-tab-7">
                                    <ul class="nav nav-tabs nav-tabs-line mb-5">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#kt_tab_pane_1" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <span class="nav-icon"><i class="flaticon2-chat-1"></i></span>
                                                <span class="nav-text">ESI</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_2" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <span class="nav-icon"><i class="flaticon2-pie-chart-4"></i></span>
                                                <span class="nav-text">Gratuty</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_3" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <span class="nav-icon"><i class="flaticon2-pie-chart-4"></i></span>
                                                <span class="nav-text">PF</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#kt_tab_pane_4" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                                <span class="nav-icon"><i class="flaticon2-pie-chart-4"></i></span>
                                                <span class="nav-text">Experience</span>
                                            </a>
                                        </li>

                                    </ul>
                                    <div class="tab-content mt-5" id="myTabContent_2">
                                        <div class="tab-pane fade show active" id="kt_tab_pane_1_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                            <form class="form">
                                                <div class="card card-custom gutter-b">
                                                    <!--begin::Header-->
                                                    <div class="card-header border-0 py-0 pl-5">
                                                        <h3 class="card-title align-items-start flex-column">
                                                            <span class="card-label font-weight-bolder text-dark">Salary</span>
                                                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Experience in different organizations</span>
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group row">
                                                            <div class="col-lg-4">
                                                                <span>User Name</span>
                                                                <input aria-label="Enter employee ID" type="text" class="form-control" placeholder="Enter employee ID"/>

                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span>Description:</span>
                                                                <textarea aria-label="Kt Autosize 2" class="form-control textarea-description" id="kt_autosize_2" rows="3">Max 5000 characters</textarea>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <div class="card-body row pl-5 py-0">
                                                        <div class="col-md-2 mt-3">
                                                            <span>Annual CTC</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span class="input-group-text">$</span>
                                                                    </div>
                                                                    <input type="text" class="form-control" placeholder="0.00" aria-label="Amount (to the nearest dollar)">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-2 mt-3">
                                                            <span>per year</span>
                                                        </div>
                                                    </div>
                                                    <!--begin::Body-->
                                                    <div class="card-body pt-0 pb-3">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive">
                                                            <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                                                <thead>
                                                                    <tr class="text-uppercase">
                                                                        <th scope="col" class="table-column-40 pl-7">
                                                                            <span class="text-dark-75">Salary Component</span>
                                                                        </th>
                                                                        <th scope="col" class="table-column-min-20">Calculation Type</th>
                                                                        <th scope="col" class="table-column-min-20">Amount Monthly</th>
                                                                        <th scope="col" class="table-column-min-20">Amount Annually</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="pl-0 py-4">
                                                                            <b>Earning</b>
                                                                        </td>
                                                                        <td></td>
                                                                        <td></td>
                                                                        <td></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-0 py-2">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="form-group row" data-select2-id="38">
                                                                                    <div class="col-lg-4 col-md-9 col-sm-12">
                                                                                        <select aria-label="Param" class="form-control select2-hidden-accessible" id="kt_select2_1" name="param" data-select2-id="kt_select2_1_2" tabindex="-1">
                                                                                            <option value="AK" data-select2-id="2">Basic</option>
                                                                                            <option value="HI" data-select2-id="42">Hawaii</option>
                                                                                            <option value="CA" data-select2-id="43">California</option>
                                                                                            <option value="NV" data-select2-id="44">Nevada</option>
                                                                                            <option value="OR" data-select2-id="45">Oregon</option>
                                                                                            <option value="WA" data-select2-id="46">Washington</option>
                                                                                            <option value="AZ" data-select2-id="47">Arizona</option>
                                                                                            <option value="CO" data-select2-id="48">Colorado</option>
                                                                                            <option value="ID" data-select2-id="49">Idaho</option>

                                                                                        </select><span class="select2 select2-container select2-container--default select2-container--below select2-container--focus" dir="ltr" data-select2-id="1"><span class="selection"><span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-disabled="false" aria-labelledby="select2-kt_select2_1-container" aria-controls="select2-kt_select2_1-results"><span class="select2-selection__rendered" id="select2-kt_select2_1-container" role="textbox" aria-readonly="true" title="Alaska">Basic</span><span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span></span></span><span class="dropdown-wrapper"></span></span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-prepend">
                                                                                        <span class="input-group-text">$</span>
                                                                                    </div>
                                                                                    <input type="text" class="form-control" placeholder="30.00" aria-label="Amount (to the nearest dollar)">

                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td style="text-align: right;">
                                                                            <div class="form-group">
                                                                                <div class="input-group">
                                                                                    <input type="text" class="form-control text-align-right" placeholder="0.00" aria-label="Amount (to the nearest dollar)">

                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            0.00
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-0 py-4">
                                                                            <div class="align-items-center">
                                                                                <p>fixed Allowance</p>

                                                                                <span class="text-muted font-weight-bold">Monthly CTC - Sum of all other components</span>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">Fixed amount</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">0.00</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">0.00</span>

                                                                        </td>
                                                                    </tr>
                                                                    <tr class="row-bg-light-gray">
                                                                        <td class="pl-2 py-4">
                                                                            <span class="text-dark-75">Cost to Company</span>
                                                                        </td>
                                                                        <td></td>
                                                                        <td><span class="fas fa-rupee-sign"></span>
                                                                            <span class="text-dark-75 font-weight-bolder font-size-lg">0</span>
                                                                        </td>
                                                                        <td><span class="fas fa-rupee-sign"></span>
                                                                            <span class="text-dark-75 font-weight-bolder font-size-lg">0</span>
                                                                        </td>

                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                    <!--end::Body-->
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-lg-4"></div>
                                                        <div class="col-lg-8">
                                                            <button type="reset" class="btn btn-primary mr-2">Submit</button>
                                                            <button type="reset" class="btn btn-secondary">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_2_2" role="tabpanel" aria-labelledby="kt_tab_pane_2">
                                            <form class="form">
                                               <div class="card card-custom gutter-b">
                                                    <!--begin::Header-->
                                                    <div class="card-header border-0 py-5">
                                                        <h3 class="card-title align-items-start flex-column">
                                                            <span class="card-label font-weight-bolder text-dark">Bank</span>
                                                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Experience in different organizations</span>
                                                        </h3>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="form-group row">
                                                            <div class="col-lg-4">
                                                                <span>Bank Name</span>
                                                                <input aria-label="Enter employee ID" type="text" class="form-control" placeholder="Enter employee ID"/>

                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span>IFSC Code</span>
                                                                <input aria-label="Enter email" type="email" class="form-control" placeholder="Enter email"/>

                                                            </div>
                                                            <div class="col-lg-4">
                                                                <span>Username:</span>
                                                                <div class="input-group">
                                                                   <div class="input-group-prepend"><span class="input-group-text"><i class="la la-user"></i></span></div>
                                                                   <input aria-label="Username:" type="text" class="form-control" placeholder="Username:"/>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end::Header-->
                                                    <!--begin::Body-->
                                                    <div class="card-body pt-0 pb-3">
                                                        <!--begin::Table-->
                                                        <div class="table-responsive">
                                                            <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                                                <thead>
                                                                    <tr class="text-uppercase">
                                                                        <th scope="col" class="table-column-40 pl-7">
                                                                            <span class="text-dark-75">Company</span>
                                                                        </th>
                                                                        <th scope="col" class="table-column-min-15">From</th>
                                                                        <th scope="col" class="table-column-min-15">To</th>
                                                                        <th scope="col" class="table-column-min-15">Experience</th>
                                                                        <th scope="col" class="table-column-min-15">Remarks</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="pl-0 py-8">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                    <div class="symbol-label symbol-label-img-26"></div>
                                                                                </div>
                                                                                <div>
                                                                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Sant Extreanet Solution</a>
                                                                                    <span class="text-muted font-weight-bold d-block">HTML, JS, ReactJS</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">9 Years</span>
                                                                            <span class="text-muted font-weight-bold">10 Months</span>
                                                                        </td>
                                                                        <td>

                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-0 py-0">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                    <div class="symbol-label symbol-label-img-3"></div>
                                                                                </div>
                                                                                <div>
                                                                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Telegram Development</a>
                                                                                    <span class="text-muted font-weight-bold d-block">C#, ASP.NET, MS SQL</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">5 Years</span>
                                                                            <span class="text-muted font-weight-bold">4 Months</span>
                                                                        </td>
                                                                        <td>

                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-0 py-8">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                    <div class="symbol-label symbol-label-img-5"></div>
                                                                                </div>
                                                                                <div>
                                                                                    <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Payroll Application</a>
                                                                                    <span class="text-muted font-weight-bold d-block">PHP, Laravel, VueJS</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">6 Years</span>
                                                                            <span class="text-muted font-weight-bold">3 Months</span>
                                                                        </td>
                                                                        <td>

                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="pl-0 py-0">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                    <div class="symbol-label symbol-label-img-18"></div>
                                                                                </div>
                                                                                <div>
                                                                                    <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">HR Management System</a>
                                                                                    <span class="text-muted font-weight-bold d-block">Python, PostgreSQL, ReactJS</span>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                        </td>
                                                                        <td>
                                                                            <span class="text-dark-75 font-weight-bolder d-block font-size-lg">7 Years</span>
                                                                            <span class="text-muted font-weight-bold">5 Months</span>
                                                                        </td>
                                                                        <td>

                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <!--end::Table-->
                                                    </div>
                                                    <!--end::Body-->
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-lg-4"></div>
                                                        <div class="col-lg-8">
                                                            <button type="reset" class="btn btn-primary mr-2">Submit</button>
                                                            <button type="reset" class="btn btn-secondary">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_3_2" role="tabpanel" aria-labelledby="kt_tab_pane_3">
                                            <form class="form">
                                               <div class="card-body">
                                                    <div class="form-group row">
                                                        <div class="col-lg-4">
                                                            <span>Date of Birth</span>
                                                            <input aria-label="Date of Birth" type="text" class="form-control" placeholder="Date of Birth"/>

                                                        </div>
                                                        <div class="col-lg-4">
                                                            <span>Age</span>
                                                            <input aria-label="Age" type="text" class="form-control" placeholder="Age"/>

                                                        </div>
                                                        <div class="col-lg-4">
                                                            <span>Gender</span>
                                                            <div class="radio-inline">
                                                               <label class="radio radio-solid">
                                                               <input aria-label="Example 2" type="radio" name="example_2" checked="checked" value="2"/>
                                                               <span></span>
                                                               Male
                                                               </label>
                                                               <label class="radio radio-solid">
                                                               <input aria-label="Example 2" type="radio" name="example_2" value="2"/>
                                                               <span></span>
                                                               Female
                                                               </label>
                                                            </div>
                                                            <span class="form-text text-muted">Please select user group</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-lg-4">
                                                            <span>Merital Status</span>
                                                            <input aria-label="Merital Status" type="text" class="form-control" placeholder="Merital Status"/>

                                                        </div>
                                                        <div class="col-lg-4">
                                                            <span>Marrige Date</span>
                                                            <input aria-label="Marrige Date" type="text" class="form-control" placeholder="Marrige Date"/>

                                                        </div>
                                                        <div class="col-lg-4">
                                                            <span>Blood Group</span>
                                                            <input aria-label="Blood Group" type="text" class="form-control" placeholder="Blood Group"/>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-lg-4"></div>
                                                        <div class="col-lg-8">
                                                            <button type="reset" class="btn btn-primary mr-2">Submit</button>
                                                            <button type="reset" class="btn btn-secondary">Cancel</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane fade" id="kt_tab_pane_4_2" role="tabpanel" aria-labelledby="kt_tab_pane_4">
                                            <div class="card card-custom gutter-b">
                                                <!--begin::Header-->
                                                <div class="card-header border-0 py-5">
                                                    <h3 class="card-title align-items-start flex-column">
                                                        <span class="card-label font-weight-bolder text-dark">Add Experience</span>
                                                        <span class="text-muted mt-3 font-weight-bold font-size-sm">Experience in different organizations</span>
                                                    </h3>
                                                    <div class="card-toolbar">
                                                        <a href="#" class="btn btn-success font-weight-bolder font-size-sm">
                                                        <span class="svg-icon svg-icon-md svg-icon-white">

                                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                                    <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                                    <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                                                </g>
                                                            </svg>
                                                            <!--end::Svg Icon-->
                                                        </span>Add New Experience</a>
                                                    </div>
                                                </div>
                                                <!--end::Header-->
                                                <!--begin::Body-->
                                                <div class="card-body pt-0 pb-3">
                                                    <!--begin::Table-->
                                                    <div class="table-responsive">
                                                        <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                                            <thead>
                                                                <tr class="text-uppercase">
                                                                    <th scope="col" class="table-column-width-40 pl-7">
                                                                        <span class="text-dark-75">Company</span>
                                                                    </th>
                                                                    <th scope="col" class="table-column-width-15-min">From</th>
                                                                    <th scope="col" class="table-column-width-15-min">To</th>
                                                                    <th scope="col" class="table-column-width-15-min">Experience</th>
                                                                    <th scope="col" class="table-column-width-15-min">Remarks</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr>
                                                                    <td class="pl-0 py-8">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                <div class="symbol-label symbol-label-img-26"></div>
                                                                            </div>
                                                                            <div>
                                                                                <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Sant Extreanet Solution</a>
                                                                                <span class="text-muted font-weight-bold d-block">HTML, JS, ReactJS</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">9 Years</span>
                                                                        <span class="text-muted font-weight-bold">10 Months</span>
                                                                    </td>
                                                                    <td>

                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pl-0 py-0">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                <div class="symbol-label symbol-label-img-3"></div>
                                                                            </div>
                                                                            <div>
                                                                                <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Telegram Development</a>
                                                                                <span class="text-muted font-weight-bold d-block">C#, ASP.NET, MS SQL</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">5 Years</span>
                                                                        <span class="text-muted font-weight-bold">4 Months</span>
                                                                    </td>
                                                                    <td>

                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pl-0 py-8">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                <div class="symbol-label symbol-label-img-5"></div>
                                                                            </div>
                                                                            <div>
                                                                                <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Payroll Application</a>
                                                                                <span class="text-muted font-weight-bold d-block">PHP, Laravel, VueJS</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">6 Years</span>
                                                                        <span class="text-muted font-weight-bold">3 Months</span>
                                                                    </td>
                                                                    <td>

                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="pl-0 py-0">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                                <div class="symbol-label symbol-label-img-18"></div>
                                                                            </div>
                                                                            <div>
                                                                                <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">HR Management System</a>
                                                                                <span class="text-muted font-weight-bold d-block">Python, PostgreSQL, ReactJS</span>
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                                    </td>
                                                                    <td>
                                                                        <span class="text-dark-75 font-weight-bolder d-block font-size-lg">7 Years</span>
                                                                        <span class="text-muted font-weight-bold">5 Months</span>
                                                                    </td>
                                                                    <td>

                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!--end::Table-->
                                                </div>
                                                <!--end::Body-->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane fade" id="home-8" role="tabpanel" aria-labelledby="home-tab-8">
                                    Goals
                                </div>

                                <div class="tab-pane fade" id="home-9" role="tabpanel" aria-labelledby="home-tab-9">
                                    <div class="card card-custom gutter-b">
                                        <!--begin::Header-->
                                        <div class="card-header border-0 py-5">
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label font-weight-bolder text-dark">Add Assets</span>
                                                <span class="text-muted mt-3 font-weight-bold font-size-sm">Experience in different organizations</span>
                                            </h3>
                                            <div class="card-toolbar">
                                                <a href="#" class="btn btn-success font-weight-bolder font-size-sm">
                                                <span class="svg-icon svg-icon-md svg-icon-white">

                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                                            <path d="M18,8 L16,8 C15.4477153,8 15,7.55228475 15,7 C15,6.44771525 15.4477153,6 16,6 L18,6 L18,4 C18,3.44771525 18.4477153,3 19,3 C19.5522847,3 20,3.44771525 20,4 L20,6 L22,6 C22.5522847,6 23,6.44771525 23,7 C23,7.55228475 22.5522847,8 22,8 L20,8 L20,10 C20,10.5522847 19.5522847,11 19,11 C18.4477153,11 18,10.5522847 18,10 L18,8 Z M9,11 C6.790861,11 5,9.209139 5,7 C5,4.790861 6.790861,3 9,3 C11.209139,3 13,4.790861 13,7 C13,9.209139 11.209139,11 9,11 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                            <path d="M0.00065168429,20.1992055 C0.388258525,15.4265159 4.26191235,13 8.98334134,13 C13.7712164,13 17.7048837,15.2931929 17.9979143,20.2 C18.0095879,20.3954741 17.9979143,21 17.2466999,21 C13.541124,21 8.03472472,21 0.727502227,21 C0.476712155,21 -0.0204617505,20.45918 0.00065168429,20.1992055 Z" fill="#000000" fill-rule="nonzero"></path>
                                                        </g>
                                                    </svg>
                                                    <!--end::Svg Icon-->
                                                </span>Add New Assets</a>
                                            </div>
                                        </div>
                                        <!--end::Header-->
                                        <!--begin::Body-->
                                        <div class="card-body pt-0 pb-3">
                                            <!--begin::Table-->
                                            <div class="table-responsive">
                                                <table class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                                    <thead>
                                                        <tr class="text-uppercase">
                                                            <th scope="col" class="table-column-width-40 pl-7">
                                                                <span class="text-dark-75">Assets</span>
                                                            </th>
                                                            <th scope="col" class="table-column-width-15-min">From</th>
                                                            <th scope="col" class="table-column-width-15-min">To</th>

                                                            <th scope="col" class="table-column-width-15-min">Remarks</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td class="pl-0 py-8">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                        <div class="symbol-label symbol-label-img-26"></div>
                                                                    </div>
                                                                    <div>
                                                                        <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Sant Extreanet Solution</a>
                                                                        <span class="text-muted font-weight-bold d-block">HTML, JS, ReactJS</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                            </td>
                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                            </td>

                                                            <td>

                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td class="pl-0 py-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                        <div class="symbol-label symbol-label-img-3"></div>
                                                                    </div>
                                                                    <div>
                                                                        <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Telegram Development</a>
                                                                        <span class="text-muted font-weight-bold d-block">C#, ASP.NET, MS SQL</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                            </td>

                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">5 Years</span>
                                                                <span class="text-muted font-weight-bold">4 Months</span>
                                                            </td>
                                                            <td>

                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="pl-0 py-8">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                        <div class="symbol-label symbol-label-img-5"></div>
                                                                    </div>
                                                                    <div>
                                                                        <a href="#" class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Payroll Application</a>
                                                                        <span class="text-muted font-weight-bold d-block">PHP, Laravel, VueJS</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                            </td>

                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">6 Years</span>
                                                                <span class="text-muted font-weight-bold">3 Months</span>
                                                            </td>
                                                            <td>

                                                            </td>

                                                        </tr>
                                                        <tr>
                                                            <td class="pl-0 py-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="symbol symbol-50 flex-shrink-0 mr-4">
                                                                        <div class="symbol-label symbol-label-img-18"></div>
                                                                    </div>
                                                                    <div>
                                                                        <a href="#" class="text-dark font-weight-bolder text-hover-primary mb-1 font-size-lg">HR Management System</a>
                                                                        <span class="text-muted font-weight-bold d-block">Python, PostgreSQL, ReactJS</span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">22-Sep-20</span>

                                                            </td>

                                                            <td>
                                                                <span class="text-dark-75 font-weight-bolder d-block font-size-lg">7 Years</span>
                                                                <span class="text-muted font-weight-bold">5 Months</span>
                                                            </td>
                                                            <td>

                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--end::Table-->
                                        </div>
                                        <!--end::Body-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            </div>
        </div>
    </div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>
<script src="assets/js/custom_insert_esi.js"></script>
