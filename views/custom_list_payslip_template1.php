<?php
$month =$_GET['month'];
$year = $_GET['year'];
if($_GET['emp_id']){
    $employe_id = $_GET['emp_id'];
}
else{
    $employe_id = $_SESSION['emp_id'];
}

$cusurl = API."?mode=togetpayslipdetails&month=".$month."&year=".$year."&employe_id=".$employe_id;
$cusresponse = callAPI($cusurl);
$payslip_data_list = json_decode($cusresponse,1);

$dataorg["where"] = array('company_id'=> $_SESSION['company_id'], 'is_active'=>'0');
$datw = urlencode(json_encode($dataorg));
$curl1 = API."?mode=gettabledetailswithconditions&table=organization_settings&data=".$datw;
$cudata1 = callAPI($curl1);
$org_details = json_decode($cudata1,1);
$organization_name = $org_details['organization_name'];
$organization_logo = $org_details['display_logo'];
$organization_address = $org_details['location_name'] . ", " .$org_details['location_address'];
$org_location =  $org_details['location_city'] . ", " .$org_details['location_post_code'];
?>
<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <!--begin::Subheader-->
    <div class="subheader py-2 py-lg-6 subheader-solid" id="kt_subheader">
        <div class="container-fluid d-flex align-items-center justify-content-between flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex align-items-center flex-wrap mr-1">
                <!--begin::Page Heading-->
                <div class="d-flex align-items-baseline flex-wrap mr-5">
                    <!--begin::Page Title-->
                    <h5 class="text-dark font-weight-bold my-1 mr-5">Payslip 1</h5>
                    <!--end::Page Title-->
                    <!--begin::Breadcrumb-->
                    <ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
                        <li class="breadcrumb-item">
                            <a href="" class="text-muted">Pages</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="" class="text-muted">Payslip 1</a>
                        </li>
                    </ul>
                    <!--end::Breadcrumb-->
                </div>
                <!--end::Page Heading-->
            </div>
            <!--end::Info-->
            <!--begin::Toolbar-->
            <div class="d-flex align-items-center">
                <!--begin::Actions-->
                <button type="button" class="btn btn-light-primary font-weight-bolder btn-sm">Actions</button>
                <!--end::Actions-->
                <!--begin::Dropdown-->
                <div class="dropdown dropdown-inline" data-toggle="tooltip" title="Quick actions" data-placement="left">
                    <a href="#" class="btn btn-icon" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <span class="svg-icon svg-icon-success svg-icon-2x">

                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M5.85714286,2 L13.7364114,2 C14.0910962,2 14.4343066,2.12568431 14.7051108,2.35473959 L19.4686994,6.3839416 C19.8056532,6.66894833 20,7.08787823 20,7.52920201 L20,20.0833333 C20,21.8738751 19.9795521,22 18.1428571,22 L5.85714286,22 C4.02044787,22 4,21.8738751 4,20.0833333 L4,3.91666667 C4,2.12612489 4.02044787,2 5.85714286,2 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
                                    <path d="M11,14 L9,14 C8.44771525,14 8,13.5522847 8,13 C8,12.4477153 8.44771525,12 9,12 L11,12 L11,10 C11,9.44771525 11.4477153,9 12,9 C12.5522847,9 13,9.44771525 13,10 L13,12 L15,12 C15.5522847,12 16,12.4477153 16,13 C16,13.5522847 15.5522847,14 15,14 L13,14 L13,16 C13,16.5522847 12.5522847,17 12,17 C11.4477153,17 11,16.5522847 11,16 L11,14 Z" fill="#000000" />
                                </g>
                            </svg>
                            <!--end::Svg Icon-->
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-md dropdown-menu-right p-0 m-0">
                        <!--begin::Navigation-->
                        <ul class="navi navi-hover">
                            <li class="navi-header font-weight-bold py-4">
                                <span class="font-size-lg">Choose Label:</span>
                                <i class="flaticon2-information icon-md text-muted" data-toggle="tooltip" data-placement="right" title="Click to learn more..."></i>
                            </li>
                            <li class="navi-separator mb-3 opacity-70"></li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-success">Customer</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-danger">Partner</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-warning">Suplier</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-primary">Member</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-item">
                                <a href="#" class="navi-link">
                                    <span class="navi-text">
                                        <span class="label label-xl label-inline label-light-dark">Staff</span>
                                    </span>
                                </a>
                            </li>
                            <li class="navi-separator mt-3 opacity-70"></li>
                            <li class="navi-footer py-4">
                                <a class="btn btn-clean font-weight-bold btn-sm" href="#">
                                    <i class="ki ki-plus icon-sm"></i>Add new</a>
                                </li>
                            </ul>
                            <!--end::Navigation-->
                        </div>
                    </div>
                    <!--end::Dropdown-->
                </div>
                <!--end::Toolbar-->
            </div>
        </div>
        <!--end::Subheader-->

        <div class="d-flex flex-column-fluid">
            <!--begin::Container-->
            <div class="container">
                <!-- begin::Card-->
                <div class="card card-custom overflow-hidden">
                    <div class="card-body p-0">
                        <!-- begin: Invoice-->
                        <!-- begin: Invoice header-->
                        <div class="row justify-content-center bgi-size-cover bgi-no-repeat py-8 px-8 py-md-27 px-md-0" style="background-image: url(assets/media/bg/bg-6.jpg);">
                            <div class="col-md-10">
                                <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                                    <h1 class="display-4 text-white font-weight-boldest mb-10">PAYSLIP FOR <?php echo $payslip_data_list['month']." ".$payslip_data_list['year'];?></h1>
                                    <div class="d-flex flex-column align-items-md-end px-0">
                                        <!--begin::Logo-->
                                        <a href="#" class="mb-5">
                                            <img src="uploads/all/<?php echo $organization_logo; ?>"     width="60" height="60" alt="" />
                                        </a>
                                        <!--end::Logo-->
                                        <span class="text-white d-flex flex-column align-items-md-end opacity-70">

                                            <span><?php echo $organization_name.", ".$organization_address; ?></span>
                                            <span><?php echo $org_location; ?></span>
                                        </span>
                                    </div>

                                </div>

                                <div class="border-bottom w-100 opacity-20"></div>
                                <div class="d-flex justify-content-between text-white pt-6">
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">STD DAYS</span>
                                        <span class="opacity-70"><?php echo $payslip_data_list['std']; ?>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">WORKED DAYS</span>
                                        <span class="opacity-70"><?php echo $payslip_data_list['work']; ?>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">LOP DAYS</span>
                                        <span class="opacity-70"><?php echo $payslip_data_list['lop']; ?></span>
                                    </div>

                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">DATE OF JOINING</span>
                                        <span class="opacity-70"><?php
                                        echo date('F d, Y',strtotime($payslip_data_list['doj'])); ?>
                                    </div>
                                    <div class="d-flex flex-column flex-root">
                                        <span class="font-weight-bolder mb-2">EMPLOYEE</span>
                                        <span><?php echo $payslip_data_list['emp_number']; ?> - <?php echo $payslip_data_list['emp_name']; ?></span>
                                        <span class="opacity-70"><?php echo $payslip_data_list['designation']; ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- end: Invoice header-->
                        <!-- begin: Invoice body-->
                        <div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
                            <div class="col-md-10">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th scope="col" class="pl-0 font-weight-boldest text-muted text-uppercase ">Earnings</th>
                                                <th scope="col" class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
                                                <th scope="col" class="pl-0 font-weight-boldest text-muted text-uppercase">Deductions</th>
                                                <th scope="col" class="text-right font-weight-boldest text-muted text-uppercase">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            $actual_count= count((array)$payslip_data_list['actual']);
                                            $dedaction_count= count((array)$payslip_data_list['dedaction']);
                                            $count = ($actual_count>$dedaction_count) ? $actual_count : $dedaction_count;
                                            for($i=0; $i<$count;$i++) {?>
                                                <tr class="font-weight-normal font-size-lg">
                                                    <td class="pl-0 pt-5">
                                                        <?php echo  $payslip_data_list['actual'][$i]['name']; ?>
                                                    </td>
                                                    <td class="text-right pt-5">
                                                        <?php
                                                        echo  $payslip_data_list['actual'][$i]['amount']; ?>
                                                    </td>
                                                    <td class="pl-0 pt-5">
                                                        <?php
                                                        echo  $payslip_data_list['dedaction'][$i]['name']; ?>
                                                    </td>
                                                    <td class="text-right pt-5">
                                                        <?php echo  $payslip_data_list['dedaction'][$i]['amount']; ?>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                <td class="pl-0 pt-5">GROSS EARNINGS</td>
                                                <td class="text-right pt-5 text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                                <td class="pl-0 pt-5">TOTAL DEDUCTIONS</td>
                                                <td class="text-right pt-5 text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                            </tr>

                                            <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                <td class="pl-0 pt-7 text-muted">NET PAY</td>
                                                <td class="text-right pt-5"></td>
                                                <td class="pl-0 pt-5"></td>
                                                <td class="text-right pt-5 text-muted">AMOUNT</td>
                                            </tr>
                                            <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                <td class="pl-0 pt-5">GROSS EARNINGS</td>
                                                <td class="text-right pt-5 text-primary"></td>
                                                <td class="pl-0 pt-5"></td>
                                                <td class="text-right pt-5 text-primary"><?php echo $payslip_data_list['actual_amount'];?></td>
                                            </tr>
                                            <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                <td class="pl-0 pt-5">TOTAL DEDUCTIONS</td>
                                                <td class="text-right pt-5 text-primary"></td>
                                                <td class="pl-0 pt-5"></td>
                                                <td class="text-right pt-5 text-danger"><?php echo $payslip_data_list['dedaction_amount'];?></td>
                                            </tr>

                                            <tr class="font-weight-boldest border-bottom-0 font-size-lg">
                                                <td class="pl-0 pt-5">TOTAL NET PAYABLE</td>
                                                <td class="text-right pt-5 text-primary"></td>
                                                <td class="pl-0 pt-5"></td>
                                                <td class="text-right pt-5 text-primary">
                                                    <?php echo $payslip_data_list['net'];?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row justify-content-center  py-8 px-8 py-md-10 px-md-0">
                                    <div class="col-md-10">
                                        <div class="d-flex justify-content-between flex-column flex-md-row font-size-lg">
                                            <div class="d-flex flex-column mb-10 mb-md-0">
                                                <div class="font-weight-bolder font-size-lg mb-3">Total Net Payable : ₹<?php echo $payslip_data_list['net'];?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end: Invoice body-->
                    <!-- begin: Invoice footer-->
                    <div class="row justify-content-center bg-gray-100 py-8 px-8 py-md-10 px-md-0">
                        <div class="col-md-9">
                            <div class="d-flex justify-content-between flex-column flex-md-row font-size-lg">
                                <div class="d-flex flex-column mb-10 mb-md-0">
                                    <div class="font-weight-bolder font-size-lg mb-3">BANK TRANSFER</div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="mr-15 font-weight-bold">Account Name:</span>
                                        <span class="text-right"><?php echo $payslip_data_list['bank']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="mr-15 font-weight-bold ">Account Number:</span>
                                        <span class="text-right"><?php echo $payslip_data_list['bank_account_no']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="mr-15 font-weight-bold">PF A/C:</span>
                                        <span class="text-right"><?php echo $payslip_data_list['pf']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="mr-15 font-weight-bold">UAN:</span>
                                        <span class="text-right"><?php echo $payslip_data_list['pf_uan']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span class="mr-15 font-weight-bold">PAN:</span>
                                        <span class="text-right"><?php echo $payslip_data_list['pan_number']; ?></span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column text-md-right">
                                    <span class="font-size-lg font-weight-bolder mb-1">NET SALARY</span>
                                    <span class="font-size-h2 font-weight-boldest text-primary mb-1">₹<?php echo $payslip_data_list['net'];?></span>
                                                    </div>
</div>
</div>
</div>
<!-- end: Invoice footer-->
<!-- begin: Invoice action-->
<div class="row justify-content-center py-8 px-8 py-md-10 px-md-0">
    <div class="col-md-9">
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-light-primary font-weight-bold" onclick="window.print();">Download Payslip</button>
            <button type="button" class="btn btn-primary font-weight-bold" onclick="window.print();">Print Payslip</button>
        </div>
    </div>
</div>
<!-- end: Invoice action-->
<!-- end: Invoice-->
</div>
</div>
<!-- end::Card-->
</div>
<!--end::Container-->
</div>

</div>
<!--end::Content-->
