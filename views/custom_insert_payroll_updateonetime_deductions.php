<div class="card card-custom gutter-b">
    <div class="card-header border-0 ">
        <div class="card-header table-card-header">
            <h5>  Update Onetime Deductions</h5>
        </div>
    </div>
    <div class="card-body py-0">
        <div class="card-body">
            <form class="form" id="kt_form" method="post" action="" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-xl-2"></div>
                    <div class="col-xl-8">
                        <div class="form-group row">
                            <span class="col-4">Update Onetime</span>
                            <div class="col-8">
                                <div class="custom-file">
                                    <input aria-label="Name" type="file" class="form-control fill" id="customFile" name="name">
                                </div>
                            </div>
                        </div>
                        <div class="form-group row mt-2">
                            <span class="col-3">&nbsp;</span>
                            <div class="col-9">
                                <input type="submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" value="Submit" name="submit" />
                                <a target="_blank" href="csv_download.php?table=employee_other_earnings" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2">
                                    Sample    Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="pay_year" id="year" value="<?php echo $_GET['PAY_YEAR'] ?>">
                <input type="hidden" name="pay_month" id="month" value="<?php echo $_GET['PAY_MONTH'] ?>">
                <input type="hidden" name="type" id="type" value="deductions">
                <input type="hidden" name="submit_earning" value="onedeductions">
            </form>
        </div>
    </div>
</div>
