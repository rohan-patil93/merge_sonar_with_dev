"use strict";

jQuery(document).ready(function () {
    initSurveyAdminDashboardCharts();
});

function parseSurveyDashboardJson(rawValue) {
    if (!rawValue) {
        return [];
    }
    try {
        var parsed = JSON.parse(rawValue);
        return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
        return [];
    }
}

function hasSurveyChartData(rows, key) {
    var total = 0;
    var i;
    for (i = 0; i < rows.length; i++) {
        total += parseInt(rows[i][key], 10) || 0;
    }
    return total > 0;
}

function initSurveyAdminDashboardCharts() {
    var departmentRaw = document.getElementById("survey_department_chart_data");
    var monthlyRaw = document.getElementById("survey_monthly_trend_data");
    var departmentData = parseSurveyDashboardJson(departmentRaw ? departmentRaw.value : "[]");
    var monthlyData = parseSurveyDashboardJson(monthlyRaw ? monthlyRaw.value : "[]");
    var barElement = document.getElementById("morris-bar-chart");
    var lineElement = document.getElementById("line-example");
    var barEmptyElement = document.getElementById("morris-bar-chart-empty");
    var lineEmptyElement = document.getElementById("line-example-empty");
    var morrisBarData = [];
    var morrisLineData = [];
    var i;

    for (i = 0; i < departmentData.length; i++) {
        morrisBarData.push({
            y: departmentData[i].department,
            a: parseInt(departmentData[i].count, 10) || 0
        });
    }

    for (i = 0; i < monthlyData.length; i++) {
        morrisLineData.push({
            y: monthlyData[i].month,
            a: parseInt(monthlyData[i].count, 10) || 0
        });
    }

    if (barElement && typeof Morris != "undefined") {
        if (hasSurveyChartData(departmentData, "count")) {
            Morris.Bar({
                element: "morris-bar-chart",
                data: morrisBarData,
                xkey: "y",
                ykeys: ["a"],
                labels: ["Responses"],
                hideHover: "auto",
                barColors: ["#4099ff"],
                resize: true
            });
        } else if (barEmptyElement) {
            barElement.style.display = "none";
            barEmptyElement.style.display = "block";
        }
    }

    if (lineElement && typeof Morris != "undefined") {
        if (hasSurveyChartData(monthlyData, "count")) {
            Morris.Line({
                element: "line-example",
                data: morrisLineData,
                xkey: "y",
                ykeys: ["a"],
                labels: ["Responses"],
                parseTime: false,   // ← this is the key fix
                hideHover: "auto",
                lineColors: ["#4099ff"],
                resize: true,
                xLabelAngle: 45,
                xLabels: 'auto'
            });
        } else if (lineEmptyElement) {
            lineElement.style.display = "none";
            lineEmptyElement.style.display = "block";
        }
    }
}
