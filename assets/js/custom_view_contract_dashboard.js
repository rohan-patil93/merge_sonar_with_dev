"use strict";

$(document).ready(function () {
    if (!document.getElementById('morris-bar-chart')) {
        return;
    }

    var barChartData = (typeof BarData !== 'undefined' && BarData && BarData.length > 0) ? BarData : [{
        y: String(new Date().getFullYear()),
        vendor: 0,
        customer: 0,
        inhouse: 0
    }];

    window.contractDashboardBarChart = Morris.Bar({
        element: 'morris-bar-chart',
        data: barChartData,
        xkey: 'y',
        ykeys: ['vendor', 'customer', 'inhouse'],
        labels: ['Vendor', 'Customer', 'In-house'],
        barColors: ['#5FBEAA', '#5D9CEC', '#cCcCcC'],
        barSize: 22,
        barGap: 6,
        barSizeRatio: 0.55,
        hideHover: 'auto',
        gridLineColor: '#eef0f2',
        resize: true
    });

    $(window).on('resize', function () {
        if (window.contractDashboardBarChart) {
            window.contractDashboardBarChart.redraw();
        }
    });
});
