<?php
$url = API . "?mode=getitadmindashboard&method=get";
$result = callAPI($url);
$data = json_decode($result, 1);

?>
<div class="card">
    <div class="card-body">
        <div class="row col-lg-12">
            <div class="col-lg-3">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-45">Total Employees</h6>
                                <h3 class="f-w-700 text-c-blue"><?php echo $data['total_emp_count']; ?></h3>
                                <p class="m-b-0">Total joined     employees till now</p>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users bg-c-blue"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card comp-card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-b-45">Resigned Employees</h6>
                                <h3 class="f-w-700 text-c-red"><?php echo $data['total_emp_ter_count']; ?></h3>
                                <p class="m-b-0">Total resigned employees till now</p>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users bg-c-red"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card o-hidden">
                    <div class="card-header">
                        <h5> Employees Asset Submission </h5>
                        <div class="card-header-right">
                            <a href="index.php?option=COM_VIEW_RESIGN_EMPLOYEE_ASSETS" class="btn btn-success mb-2 desktop-view"><i class="icofont icofont-plus icon-white "></i> Asset Submission </a>
                        </div>
                    </div>
                    <div class="card-block">
                        <div class="row">
                            <div class="col-4">
                                <p class="text-muted m-b-6 f-12 f-w-600">Resigned Employees  Assets Submitted </p>
                                <h6><?php echo $data['total_emp_uat_count']; ?></h6>
                            </div>
                            <div class="col-4">
                                <p class="text-muted m-b-6 f-12 f-w-600">Resigned Employees Pending Assets Submition </p>
                                <h6><?php echo $data['total_emp_ter_count']-$data['total_emp_uat_count']; ?></h6>
                            </div>
                            <div class="col-4">
                                <p class="text-muted m-b-6 f-12 f-w-600">Non-Resigned Employees Assets Submitted </p>
                                <h6><?php echo $data['total_emp_uant_count']; ?></h6>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-lg-6">
                <div class="card table-card">
                    <div class="card-header">
                        <h5>Assets</h5>
                        <div class="card-header-right">
                            <a href="index.php?option=COM_LIST_ASSETS" class="btn btn-info mb-2 desktop-view"><i class="icofont icofont-list icon-white "></i> Assets</a>
                            <a href="index.php?option=COM_INSERT_ASSETS" class="btn btn-success mb-2 desktop-view"><i class="icofont icofont-plus icon-white "></i> Asset </a>
                        </div>
                    </div>
                    <div class="card-block p-b-0 custom-scroll" style=" height: 325px;">
                        <div class="table-responsive">
                            <table class="table table-hover m-b-0">
                                <thead>
                                    <tr>
                                        <th scope="col">Sl No</th>
                                        <th scope="col">Asset Code</th>
                                        <th scope="col">Asset Name</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $k=1; foreach((array)$data['assets'] as $asset){ ?>
                                    <tr>
                                        <td><?php echo $k; ?></td>
                                        <td><?php echo $asset['asset_code'];  ?></td>
                                        <td><?php echo $asset['asset_description'];  ?></td>
                                        <td><span class="form-label label label-primary">Active</span></td>
                                    </tr>
                                    <?php $k++;
} ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card new-cust-card">
                    <div class="card-header">
                        <h5>New Resigned</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-block custom-scroll" style=" height: 325px;">
                        <?php foreach($data['emp_ter_data'] as $emp_ter_data){ ?>
                        <div class="align-middle m-b-35">
                            <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-12 text-center bg-white text-black"><?php echo initials($emp_ter_data['display_name']);  ?></span>
                            <div class="d-inline-block">
                                <h7><?php echo $emp_ter_data['display_name'];  ?></h7>
                                <p class="text-muted m-b-0"><?php echo $emp_ter_data['emp_code'];  ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card new-cust-card">
                    <div class="card-header">
                        <h5>Asset Submission</h5>
                        <div class="card-header-right">

                        </div>
                    </div>
                    <div class="card-block custom-scroll" style=" height: 325px;">
                        <?php foreach($data['emp_asset_data'] as $emp_ter_data){ ?>
                        <div class="align-middle m-b-35">
                            <span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-12 text-center bg-white text-black"><?php echo initials($emp_ter_data['display_name']);  ?></span>
                            <div class="d-inline-block">
                                <h7><?php echo $emp_ter_data['display_name'];  ?></h7>
                                <p class="text-muted m-b-0"><?php echo $emp_ter_data['emp_code'];  ?></p>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
