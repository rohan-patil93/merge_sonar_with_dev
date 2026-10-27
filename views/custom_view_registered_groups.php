<?php

$url = API."?mode=getsubscriptionusers&admin=Y&user_email=".$_SESSION['user_email'];
$subscripusers = callAPI($url);
$subscriptusers = json_decode($subscripusers,1);
$applicationuser = $subscriptusers['application_user'];
$company_subscription_plans = $subscriptusers['company_subscription_plans'];
$csplres = $subscriptusers['company_subscription_plan_licenses'];
$subscription_plans = $subscriptusers['subscription_plans'];
$subscriptiontypeunit = $subscriptusers['subscriptiontypeunit'];
$module_setup = $subscriptusers['module_setup'];
$licensecode = $subscriptusers['licensecode'];
?>
<script type="text/javascript">
    function viewSubscriptonHistory(company_subscr_id){
    jQuery.ajax({
            url: "views/custom_view_subscription_plan_history.php",
            data: 'company_subscr_id='+company_subscr_id,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            }
        });
}
function AddUserPlans(company_subscr_id){
    jQuery.ajax({
        url: "views/custom_insert_assign_subscription_plan_popup.php",
        data: 'company_subscr_id='+company_subscr_id,
        type: "POST",
        success:function(data){
            $('#dynamic_popupxxl').html(data);
            $('#kt_modal_dynamic_popupxxl').modal("show");
        }
    });
}
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky">
            <div class="card-header">
                <h5>Subscription Plans</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-block">
                <div class="card-body">
                    <div class="card">
                      <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Billing Address</h5>

                                </div>
                                <div class="card-body mt-2">
                                    <div class="row">
                                        <div class="col-lg-12 col-xl-6">
                                            <div class="table-responsive">
                                                <table class="table m-0">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="col" >Bill to:</th>
                                                            <td><?php echo $applicationuser['user_email']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="col" >Customer Name:</th>
                                                            <td><?php echo $applicationuser['display_name']; ?></td>
                                                        </tr>
                                                        <?php
                                                    $custom_fields = json_decode($applicationuser['custom_fields'],1);
                                                    foreach($custom_fields as $kk=>$custoval){ ?>
                                                    <tr>
                                                            <th scope="col" ><?php echo $kk; ?></th>
                                                            <td><?php echo $custoval; ?></td>
                                                        </tr>

                                                   <?php }?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 col-xl-6">
                                            <div class="table-responsive">
                                                <table class="table m-0">
                                                    <tbody>
                                                        <tr>
                                                            <th scope="row">Subscription Fees:</th>
                                                            <td>Rs.0.00/Year</td>
                                                        </tr>
                                                        <tr>
                                                            <th scope="row">Billing method:</th>
                                                            <td>Manual</td>
                                                        </tr>

                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h5>Subscribed Products:</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <?php $tabletype = "client";
                                        include_once "views/custom_insert_subscription_product_table.php"; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h5>Recent Invoice</h5>

                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover m-b-0">
                                            <tbody>
                                                <?php
                                                foreach($csplres as $csplress){  //print_r($csplress);?>
                                                <tr>
                                                    <th scope="col" class="min-w-400px">Invoice No. <?php echo $licensecode[$csplress['csp_id']]; ?> User License :
                                                        <a href="#" class="fw-bolder text-gray-800 text-hover-primary me-1"><?php echo $csplress['added_license_users']; ?></a> Price : <?php echo $csplress['price']; ?>
                                                      <?php echo $csplress['payment_type']=="N"?'<span class="badge label-warning ms-2">Pendding</span>':'<span class="badge label-success ms-2">Done</span>'; ?>
                                                        </th>
                                                        <th scope="col" class="pe-0 text-gray-600 text-end min-w-200px">
                                                            <?php echo $csplress['request_date']; ?>
                                                        </th>
                                                    </tr>
                                                    <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-lg-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h5>Summary</h5>
                                                </div>
                                                <div class="card-body">
                                                    <h6 class="mt-5 f-w-600">Customer Details</h6>
                                                    <div class="d-block mt-3">
                                                        <a class="f-w-600" href=""><?php echo $applicationuser['display_name']; ?></a>
                                                        <div class="badge label-success ms-2">Active</div>
                                                    </div>
                                                    <div class="d-block border-bottom pb-4">
                                                        <a class="f-w-600" href=""><?php echo $applicationuser['user_email']; ?></a>
                                                    </div>
                                                    <div class="d-block border-bottom pb-4 mt-3">
                                                        <div class="badge bg-light text-primary ms-2">Basic Bundle</div>
                                                        <span class="d-inline-block mr-2 f-w-600" id="totalpricesubscription"><?php echo "Rs.".$totalactualprice; ?></span>
                                                    </div>
                                                    <div class="d-block border-bottom pb-4 mt-3">
                                                        <h6 class="f-w-600">Payment Details</h6>
                                                        <div class="d-block mt-2">
                                                            <span class="d-inline-block mr-2 f-w-600">Manual</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-block mt-3">
                                                        <h6 class="f-w-600">Subscription Details</h6>
                                                        <div class="table-responsive">
                                                            <table class="table m-0">
                                                                <tbody>
                                                                    <tr>
                                                                        <th scope="row">Subscription Group</th>
                                                                        <td><?php echo $applicationuser['group_name']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Started</th>
                                                                        <td>15 Apr 2021</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Status</th>
                                                                        <td >
                                                                            <div class="badge label-success ms-2">Active</div>
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th scope="row">Next Invoice</th>
                                                                        <td>15 Apr 2022</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
