"use strict";

jQuery(document).ready(function () {
    initEmployeeSurveyDashboardProgress();
    initEmployeeSurveyDashboardTable();
});

function initEmployeeSurveyDashboardProgress() {
    var circumference = 188.5;
    var circles = document.querySelectorAll(".progress-circle .progress-value");

    for (var i = 0; i < circles.length; i++) {
        var circle = circles[i];
        var percent = parseInt(circle.getAttribute("data-progress"), 10) || 0;
        if (percent < 0) {
            percent = 0;
        }
        if (percent > 100) {
            percent = 100;
        }
        var filled = (percent / 100) * circumference;
        circle.setAttribute("stroke-dasharray", filled + " " + circumference);
    }

    var rateCircle = document.querySelector(".progress-circle-rate");
    if (rateCircle) {
        var ratePercent = parseInt(rateCircle.getAttribute("data-percent"), 10) || 0;
        if (ratePercent < 0) {
            ratePercent = 0;
        }
        if (ratePercent > 100) {
            ratePercent = 100;
        }
        rateCircle.style.setProperty("--percent", ratePercent + "%");
    }
}

function initEmployeeSurveyDashboardTable() {
    jQuery(".emp-survey-kpi-card").on("click", function () {
        var filter = jQuery(this).attr("data-filter");
        loadEmpSurveyTable(filter, this);
    });

    loadEmpSurveyTable("assigned", document.getElementById("empSurveyKpiAssigned"));
}

function loadEmpSurveyTable(statusFilter, el) {
    var loader = '<div class="text-center py-5"><img src="assets/images/spiner/spiner3.gif" style="width:200px;" alt=""></div>';
    jQuery("#empSurveyTableContent").html(loader);

    if (el) {
        jQuery(".emp-survey-kpi-card").removeClass("emp-survey-kpi-active");
        jQuery(el).addClass("emp-survey-kpi-active");
    }

    jQuery.ajax({
        url: "views/custom_employee_survey_admin_dashboard_ajax.php",
        type: "POST",
        data: {
            status_filter: statusFilter,
            company_id: jQuery("#emp_survey_dash_company_id").val(),
            emp_id: jQuery("#emp_survey_dash_emp_id").val(),
            category: jQuery("#emp_survey_dash_category").val()
        },
        success: function (response) {
            jQuery("#empSurveyTableContent").html(response);
        },
        error: function () {
            jQuery("#empSurveyTableContent").html('<p class="text-danger p-3">Unable to load data. Please try again.</p>');
        }
    });
}
