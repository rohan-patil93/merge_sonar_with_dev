<form method="post" action="">
    <div class="row col-lg-12 ">
        <?php
        $shortcode = $_POST['shortcode_date'] ?? "TM";
        $pick_date = $_POST['pick_date'] ?? "This Month";
        $from_date = $_POST['from'] ?? fromDate(date("Y-m-01"));
        $to_date   = $_POST['to']   ?? fromDate(date("Y-m-t")); ?>
        <div class="col-lg-4 mb-2">
            <input type="hidden" name="pick_date" id="pick_date" value="<?php echo $pick_date; ?>">
            <input type="hidden" name="shortcode_date" id="shortcode_date" value="<?php echo $shortcode; ?>">
            <div class="btn-group dropdown-split-primary" style="width: 100%;">
                <button type="button" class="btn btn-primary" style="width: 80%;height: 40px;">
                    <i class="ti-calendar"></i><span id="btn_date"><?php echo $pick_date; ?></span>
                </button>
                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                    style="width: 20%;height: 40px;">
                    <span class="visually-hidden">Toggle primary</span>
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TD" ? "active" : ""; ?> empty_active"
                        id="TD" href="#!"
                        onclick="dynamicSelectDateRange('TD','Today','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TW" ? "active" : ""; ?> empty_active"
                        id="TW" href="#!"
                        onclick="dynamicSelectDateRange('TW','This Week','<?php echo fromDate(date("Y-m-d", strtotime('monday this week'))); ?>','<?php echo fromDate(date("Y-m-d", strtotime('sunday this week'))); ?>')">This Week</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TM" ? "active" : ""; ?> empty_active"
                        id="TM" href="#!"
                        onclick="dynamicSelectDateRange('TM','This Month','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "TY" ? "active" : ""; ?> empty_active"
                        id="TY" href="#!"
                        onclick="dynamicSelectDateRange('TY','This Year','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "YD" ? "active" : ""; ?> empty_active"
                        id="YD" href="#!"
                        onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("-1 days"))); ?>')">Yesterday</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PW" ? "active" : ""; ?> empty_active"
                        id="PW" href="#!"
                        onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PM" ? "active" : ""; ?> empty_active"
                        id="PM" href="#!"
                        onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo fromDate(date("Y-m-01", strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t", strtotime('last month'))); ?>')">Previous Month</a>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "PY" ? "active" : ""; ?> empty_active"
                        id="PY" href="#!"
                        onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo fromDate(date("Y-01-01", strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31", strtotime('last year'))); ?>')">Previous Year</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode == "CD" ? "active" : ""; ?> empty_active"
                        id="CD" href="#!"
                        onclick="dynamicSelectDateRange('CD','Custom Date')">Custom Date</a>
                </div>
            </div>
            <div class="row mt-2" style="display: <?php echo $shortcode == 'CD' ? '' : 'none' ?>" id="custome_date">
                <div class="col-lg-6">
                    <input aria-label="From" type="text" class="form-control filterdynamic datepicker" name="from" id="from_date" value="<?php echo $from_date; ?>">
                </div>
                <div class="col-lg-6">
                    <input aria-label="To" type="text" class="form-control filterdynamic datepicker" name="to" id="to_date" value="<?php echo $to_date; ?>">
                </div>
            </div>
        </div>
        <div class="col-lg-2">
            <button type="submit" name="applyform" value="apply" class="btn btn-info p-2">Search</button>
        </div>
    </div>
</form>

<script>
    function dynamicSelectDateRange(type, name, start = "", end = "") {
        $("#btn_date").text(name);
        $("#pick_date").val(name);
        $("#shortcode_date").val(type);
        $(".empty_active").removeClass("active");
        $("#" + type).addClass("active");
        if (name == "Custom Date") {
            $("#custome_date").show('slow');
        } else {
            $("#custome_date").hide();
            $("#from_date").val(start);
            $("#to_date").val(end);
        }
    }
</script>
