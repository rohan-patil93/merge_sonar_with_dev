"use strict";

function changeAmcLandingLocation(value) {
    var url = "index.php?option=COM_VIEW_AMC_ADMIN_LANDING_DASHBOARD";
    if (value !== undefined && value !== null && String(value) !== "") {
        url += "&LOCID=" + encodeURIComponent(value);
    }
    window.location.href = url;
}

$(document).ready(function () {
    $('#amc_landing_loc_id').on('change', function () {
        changeAmcLandingLocation($(this).val());
    });

    $('.amc-landing-kpi-click').on('click', function () {
        var type = $(this).data('type') || '';
        var title = $(this).data('title') || 'Contracts';
        var locId = $('#amc_landing_loc_id').val() || '';
        if (type == '') {
            return;
        }

        $.ajax({
            url: 'views/custom_view_amc_admin_landing_contracts_popup.php',
            type: 'GET',
            data: {
                purpose: 'edit',
                type: type,
                title: title,
                LOCID: locId
            },
            success: function (response) {
                $('#dynamic_popup1').html(response);
                $('#kt_modal_dynamic_popup').modal('show');
            },
            error: function () {
                alert('Failed to load contracts.');
            }
        });
    });

    $('.amc-landing-qa-contract-popup').on('click', function (e) {
        e.preventDefault();
        var purpose = $(this).data('purpose') || '';
        var title = $(this).data('title') || 'Contracts';
        var locId = $('#amc_landing_loc_id').val() || '';
        if (purpose == '') {
            return;
        }

        var ajaxData = {
            purpose: purpose,
            title: title,
            LOCID: locId
        };
        if (purpose == 'assign') {
            ajaxData.sort = 'assignment';
        }

        $.ajax({
            url: 'views/custom_view_amc_admin_landing_contracts_popup.php',
            type: 'GET',
            data: ajaxData,
            success: function (response) {
                $('#dynamic_popup1').html(response);
                $('#kt_modal_dynamic_popup').modal('show');
            },
            error: function () {
                alert('Failed to load contracts.');
            }
        });
    });

    $('.amc-landing-qa-reports-popup').on('click', function (e) {
        e.preventDefault();
        var title = $(this).data('title') || 'Reports';

        $.ajax({
            url: 'views/custom_view_amc_admin_landing_reports_popup.php',
            type: 'GET',
            data: {
                title: title
            },
            success: function (response) {
                $('#dynamic_popup1').html(response);
                $('#kt_modal_dynamic_popup').modal('show');
            },
            error: function () {
                alert('Failed to load reports.');
            }
        });
    });

    var chartEl = document.getElementById('amc-admin-landing-bar-chart');
    if (chartEl) {
        chartEl.innerHTML = '';

        var barChartData = (typeof AmcAdminLandingBarData !== 'undefined' && AmcAdminLandingBarData && AmcAdminLandingBarData.length > 0) ? AmcAdminLandingBarData : [{
            y: String(new Date().getFullYear()),
            vendor: 0,
            customer: 0,
            inhouse: 0
        }];

        window.amcAdminLandingBarChart = Morris.Bar({
            element: 'amc-admin-landing-bar-chart',
            data: barChartData,
            xkey: 'y',
            ykeys: ['vendor', 'customer', 'inhouse'],
            labels: ['Vendor', 'Customer', 'In-house'],
            barColors: ['#5FBEAA', '#5D9CEC', '#FFBC42'],
            barSize: 22,
            barGap: 6,
            barSizeRatio: 0.55,
            hideHover: 'auto',
            gridLineColor: '#eef0f2',
            resize: true
        });

        $(window).on('resize', function () {
            if (window.amcAdminLandingBarChart) {
                window.amcAdminLandingBarChart.redraw();
            }
        });
    }

    $('.amc-landing-contracts-card .nav-link[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
        var total = $(this).data('total');
        if (total !== undefined) {
            $('.amc-landing-contracts-total').text('Total: ' + total);
        }
    });
});
