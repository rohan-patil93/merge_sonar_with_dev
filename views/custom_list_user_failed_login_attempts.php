<?php
$_SESSION['apply_js']['multiselect'] = 1;

$users["where"] = array('is_active' => '0', 'company_id' => $_SESSION['company_id']);
$users["field"] = "id,display_name,user_email";
$users['order'] = "display_name ASC";
$userdata = urlencode(json_encode($users));
$userurl = API . "?mode=gettabledetailswithconditions&table=application_users&data=" . $locdata;
$user_details = callAPI($userurl);
$user_data = json_decode($user_details, 1);

?>
<div class="page-body">
    <div class="card">
        <div class="card-header">
            <h5>Failed Login Attempts</h5>
            <div class="card-header-right">
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row">
                <div class="col-lg-3 col-md-6 form-group">
                    <span>From Date <span class="text-danger">*</span></span>
                    <input aria-label="From Date" class="form-control" type="date" name="from_date" id="from_date" value="">
                    <div style="display: none;color: red;" id="from_date_err"></div>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>To Date <span class="text-danger">*</span></span>
                    <input aria-label="To Date" class="form-control" type="date" name="to_date" id="to_date" value="">
                    <div style="display: none;color: red;" id="to_date_err"></div>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>Frequency</span>
                    <select aria-label="Frequency" class="form-control js-example-basic-single" id="frequency" name="frequency" style="width:100%">
                        <option value="">All</option>
                        <option value="once">Once</option>
                        <option value="more_than_1">More than once</option>
                        <option value="more_than_2">More than 2 times</option>
                        <option value="more_than_3">More than 3 times</option>
                        <option value="more_than_4">More than 4 times</option>
                        <option value="more_than_5">More than 5 times</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>Users</span>
                    <select aria-label="Email" class="form-control js-example-basic-single" id="email_id" name="email_id" style="width:100%">
                        <option value="">All Users</option>
                        <?php if(count((array)$user_data) > 0){
                            foreach($user_data as $row){ ?>
                                <option value="<?php echo $row['user_email']; ?>"><?php echo htmlspecialchars($row['display_name']); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>

                    <input class="form-control" type="hidden" name="domain_name" id="domain_name" value="" placeholder="Search domain">

                <div class="col-lg-3 col-md-6 form-group">
                    <span>Device Source</span>
                    <select aria-label="Device Source" class="form-control js-example-basic-single" id="device_source" name="device_source" style="width:100%">
                        <option value="">All</option>
                        <option value="Web">Web</option>
                        <option value="Mobile">Mobile</option>
                        <option value="App">App</option>
                    </select>
                </div>
                <div class="col-lg-3 col-md-6 form-group">
                    <span>&nbsp;</span>
                    <button type="button" class="btn btn-primary form-control" id="getFailedLoginAttemptsBtn">Search</button>
                </div>
            </div>
            <div class="row mt-3 custom-scroll" style="height:500px" id="TableContent">
                <div class="col-12">
                    <p class="text-muted mb-0">Select a date range and click Search to view failed login attempts.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="failedLoginDetailModal" tabindex="-1" role="dialog" aria-labelledby="failedLoginDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="failedLoginDetailModalLabel">Failed Login Attempt Details</h5>
                <button type="button" class="btn-close closeFailedLoginDetailModal" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="failedLoginDetailModalBody">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary closeFailedLoginDetailModal" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function loadFailedLoginAttempts(){
        $("#from_date_err").hide();
        $("#to_date_err").hide();

        var fromDate = $("#from_date").val();
        var toDate = $("#to_date").val();
        var isValid = true;

        if(fromDate == ""){
            $("#from_date_err").text("Please select From Date").show();
            isValid = false;
        }
        if(toDate == ""){
            $("#to_date_err").text("Please select To Date").show();
            isValid = false;
        }
        if(!isValid){
            return;
        }

        var loader = '<div class="col-12 text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#TableContent").html(loader);

        $.ajax({
            url: "views/custom_list_user_failed_login_attempts_ajax.php",
            type: "POST",
            data: {
                view_type: "summary",
                from_date: fromDate,
                to_date: toDate,
                frequency: $("#frequency").val(),
                email_id: $("#email_id").val(),
                domain_name: $("#domain_name").val(),
                device_source: $("#device_source").val()
            },
            success: function (response) {
                $("#TableContent").html(response);
            },
            error: function () {
                $("#TableContent").html('<div class="col-12"><p class="text-danger mb-0">Unable to load failed login attempts.</p></div>');
            }
        });
    }

    function loadFailedLoginAttemptDetails(emailId){
        var loader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 200px;" alt="Preview"></div>';
        $("#failedLoginDetailModalBody").html(loader);
        $("#failedLoginDetailModal").modal("show");

        $.ajax({
            url: "views/custom_list_user_failed_login_attempts_ajax.php",
            type: "POST",
            data: {
                view_type: "detail",
                from_date: $("#from_date").val(),
                to_date: $("#to_date").val(),
                email_id: emailId
            },
            success: function (response) {
                $("#failedLoginDetailModalBody").html(response);
            },
            error: function () {
                $("#failedLoginDetailModalBody").html('<p class="text-danger mb-0">Unable to load attempt details.</p>');
            }
        });
    }

    $(document).ready(function () {
        $(".js-example-basic-single").select2();

        $("#from_date").on("change", function () {
            var fromDate = $(this).val();
            $("#to_date").attr("min", fromDate);
            if($("#to_date").val() != "" && $("#to_date").val() < fromDate){
                $("#to_date").val(fromDate);
            }
        });

        $("#getFailedLoginAttemptsBtn").on("click", function () {
            loadFailedLoginAttempts();
        });

        $(document).on("click", ".failed-login-count-link", function () {
            var emailId = $(this).data("email");
            loadFailedLoginAttemptDetails(emailId);
        });

        $(document).on("click", ".closeFailedLoginDetailModal", function () {
            $("#failedLoginDetailModal").modal("hide");
        });
    });
</script>
