/* Unified Contract page (Standard + AMC wizard) */
var _amcContractPageInitialized = false;

function initAmcContractPage() {
    if (!document.getElementById('unified-contract-page')) {
        return;
    }
    if (_amcContractPageInitialized) {
        populateAmcEditFormIfNeeded();
        initializeAmcRenewalDates();
        bindAmcSubmitButtons();
        syncUnifiedLocationsMultiselectToForm();
        initRenewalAmountHistoryPanel();
        initAmcRenewalServiceDateSyncListeners();
        return;
    }
    initContractTypeToggle();
    applyContractPageModeUi();
    initAmcWizardNav();
    initAmcDatePickers();
    initAmcPartiesAjax();
    initPartyDetailsPreview();
    initAmcPaymentModeToggle();
    initAmcEscalations();
    initAmcContractUI();
    initStandardLocationsSelect();
    syncUnifiedLocationsMultiselectToForm();
    initContractServicesSelect();
    initContactPersonsUi();
    initOemToggle();
    initUnifiedCommercials();
    initEditTabs();
    initAddPartyModal();
    populateAmcEditFormIfNeeded();
    syncCommercialsBlockUi();
    initializeAmcRenewalDates();
    initRenewalAmountHistoryPanel();
    initAmcRenewalServiceDateSyncListeners();
    bindAmcSubmitButtons();
    _amcContractPageInitialized = true;
}

jQuery(function () {
    initAmcContractPage();
});

// function getContractPageMode() {
//     var isEditEl = document.getElementById('is-edit-mode');
//     var isAmcEl = document.getElementById('is-amc-mode');
//     if (isEditEl && isEditEl.textContent.trim() === 'true' && isAmcEl) {
//         return isAmcEl.textContent.trim() === 'true' ? 'amc' : 'standard';
//     }
//     var amcRadio = document.getElementById('page_contract_type_amc');
//     if (amcRadio && amcRadio.checked) {
//         return 'amc';
//     }
//     return 'standard';
// }
function getContractPageMode() {
    var isEditEl = document.getElementById('is-edit-mode');
    var isAmcEl = document.getElementById('is-amc-mode');
    if (isEditEl && isEditEl.textContent.trim() === 'true' && isAmcEl) {
        return isAmcEl.textContent.trim() === 'true' ? 'amc' : 'standard';
    }
    var amcRadio = document.getElementById('page_contract_type_amc');
    var standardRadio = document.getElementById('page_contract_type_standard');
    if (amcRadio && standardRadio) {
        return amcRadio.checked ? 'amc' : 'standard';
    }
    if (isAmcEl) {
        return isAmcEl.textContent.trim() === 'true' ? 'amc' : 'standard';
    }
    return 'standard';
}

function isUnifiedEditMode() {
    var el = document.getElementById('is-edit-mode');
    return !!(el && el.textContent.trim() === 'true');
}

function isUnifiedRenewMode() {
    var el = document.getElementById('is-renew-mode');
    return !!(el && el.textContent.trim() === 'true');
}

function getRenewalAmountHistoryData() {
    var el = document.getElementById('renewal-amount-history-data');
    if (!el || !el.textContent) {
        return null;
    }
    try {
        return JSON.parse(el.textContent);
    } catch (e) {
        return null;
    }
}

function escapeRenewalHistoryHtml(str) {
    if (!str) {
        return '';
    }
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatRenewalHistoryAmount(amount) {
    if (amount === null || amount === undefined || amount === '' || isNaN(Number(amount))) {
        return '—';
    }
    return '₹ ' + Number(amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function getRenewalHistoryYearLabel(index, total) {
    var yearNum = total - index;
    if (yearNum <= 1) {
        return '1st Year (Base)';
    }
    if (yearNum === 2) {
        return '2nd Year';
    }
    if (yearNum === 3) {
        return '3rd Year';
    }
    return yearNum + 'th Year';
}

function calcRenewalTrendBadge(currentAmt, previousAmt) {
    var current = parseFloat(currentAmt);
    var previous = parseFloat(previousAmt);
    if (isNaN(current) || isNaN(previous) || previous <= 0) {
        return '';
    }
    var diff = ((current - previous) / previous) * 100;
    var pct = Math.round(Math.abs(diff));
    if (diff > 0) {
        return '<span class="cc-trend-up" title="Increase vs previous year">&#9650; ' + pct + '%</span>';
    }
    if (diff < 0) {
        return '<span class="cc-trend-down" title="Decrease vs previous year">&#9660; ' + pct + '%</span>';
    }
    return '';
}

function getNewRenewalPeriodDates() {
    var data = getRenewalAmountHistoryData() || {};
    var start = data.new_period_start || '';
    var end = data.new_period_end || '';
    var startInput = document.querySelector('#unified-contract-page [name="start_date"]');
    var endInput = document.querySelector('#unified-contract-page [name="end_date"]');
    if (startInput && startInput.value) {
        start = startInput.value;
    }
    if (endInput && endInput.value) {
        end = endInput.value;
    }
    return { start: start, end: end };
}

function renderRenewalAmountHistoryPanel() {
    var panel = document.getElementById('renewal-amount-history-panel');
    var list = document.getElementById('renewal-amount-history-list');
    if (!panel || !list) {
        return;
    }

    var data = getRenewalAmountHistoryData();
    if (!data || !Array.isArray(data.periods)) {
        panel.style.display = 'none';
        return;
    }

    panel.style.display = '';
    var periods = data.periods;
    var amountInput = document.getElementById('amc_contract_amount');
    var currentAmount = amountInput ? amountInput.value : '';
    var newDates = getNewRenewalPeriodDates();
    var rows = [];
    var nextAmount = periods.length > 0 ? periods[0].amount : null;

    rows.push({
        label: 'Current Year (New)',
        start: newDates.start,
        end: newDates.end,
        amount: currentAmount,
        trendHtml: calcRenewalTrendBadge(currentAmount, nextAmount),
        isCurrent: true
    });

    for (var i = 0; i < periods.length; i++) {
        var period = periods[i];
        var previousAmount = (i < periods.length - 1) ? periods[i + 1].amount : null;
        rows.push({
            label: getRenewalHistoryYearLabel(i, periods.length),
            start: period.start_date || '',
            end: period.end_date || '',
            amount: period.amount,
            trendHtml: previousAmount !== null ? calcRenewalTrendBadge(period.amount, previousAmount) : '',
            isCurrent: false
        });
    }

    var html = '';
    rows.forEach(function (row) {
        var effective = '';
        if (row.start && row.end) {
            effective = 'Effective: ' + row.start + ' to ' + row.end;
        } else if (row.start) {
            effective = 'Effective: ' + row.start;
        }

        html += '<div class="cc-renewal-history-row' + (row.isCurrent ? ' cc-renewal-history-row-current' : '') + '">';
        html += '<div class="cc-renewal-history-row-main">';
        html += '<div class="cc-renewal-history-label">' + escapeRenewalHistoryHtml(row.label) + '</div>';
        if (effective) {
            html += '<div class="cc-renewal-history-effective">' + escapeRenewalHistoryHtml(effective) + '</div>';
        }
        html += '</div>';
        html += '<div class="cc-renewal-history-amount-wrap">';
        html += '<span class="cc-renewal-history-amount">' + formatRenewalHistoryAmount(row.amount) + '</span>';
        if (row.trendHtml) {
            html += row.trendHtml;
        }
        html += '</div>';
        html += '</div>';
    });

    list.innerHTML = html;
}

function initRenewalAmountHistoryPanel() {
    if (!isUnifiedRenewMode()) {
        return;
    }
    if (!document.getElementById('renewal-amount-history-panel')) {
        return;
    }

    renderRenewalAmountHistoryPanel();

    var $page = $('#unified-contract-page');
    $page.off(
        'input.renewalHistory change.renewalHistory',
        '#amc_contract_amount, [name="start_date"], [name="end_date"], .amc-start-datepicker, .amc-end-datepicker'
    ).on(
        'input.renewalHistory change.renewalHistory',
        '#amc_contract_amount, [name="start_date"], [name="end_date"], .amc-start-datepicker, .amc-end-datepicker',
        renderRenewalAmountHistoryPanel
    );
}

function getWizardStepTargets() {
    if (getContractPageMode() === 'standard') {
        return ['#cc-step-1', '#cc-step-3'];
    }
    return ['#cc-step-1', '#cc-step-2', '#cc-step-3', '#cc-step-4', '#cc-step-5', '#cc-step-6'];
}

function applyContractPageModeUi() {
    var mode = getContractPageMode();
    var isAmc = mode === 'amc';
    var $page = $('#unified-contract-page');
    if (!$page.length) {
        return;
    }
    $page.toggleClass('unified-mode-amc', isAmc);
    $page.toggleClass('unified-mode-standard', !isAmc);
    $page.find('.cc-amc-only').toggle(isAmc);
    $page.find('.cc-standard-only').toggle(!isAmc);
    if (!isAmc) {
        var connectBox = document.getElementById('amc_customer_connect_box');
        if (connectBox) {
            connectBox.style.display = 'none';
        }
        var connectSelect = document.getElementById('amc_customer_connect_id');
        if (connectSelect) {
            connectSelect.value = '';
            if (jQuery(connectSelect).hasClass('select2-hidden-accessible')) {
                jQuery(connectSelect).val('').trigger('change');
            }
        }
    } else {
        toggleAmcPartyTypeUi({ allowEmployeePreselect: false });
    }
    $page.find('#cc-step-3 .cc-section-num').text(isAmc ? '3' : '2');
    syncCommercialsBlockUi();
    var $visibleSteps = $page.find('.cc-wizard-steps-list .cc-wizard-step').filter(function () {
        return isAmc || !$(this).hasClass('cc-amc-only');
    });
    $visibleSteps.each(function (idx) {
        $(this).find('.cc-step-number').text(idx + 1);
        $(this).attr('data-step', idx);
    });
    var $action = $('#unified_contract_action');
    var $isAmcField = $('#unified_is_amc_contract');
    if ($action.length && !isUnifiedRenewMode()) {
        if (isAmc) {
            $action.val('submit_amc_contract');
        } else {
            $action.val('submit_contract');
        }
    }
    if ($isAmcField.length) {
        $isAmcField.val(isAmc ? 'Y' : 'N');
    }
    refreshAmcWizardSidebar(0);
}

function syncCommercialsBlockUi() {
    var $page = $('#unified-contract-page');
    if (!$page.length) {
        return;
    }
    var mode = $page.find('input[name="amc_payment_mode"]:checked').val() || 'billing_cycle';
    var isMilestones = mode === 'milestones';
    $page.find('.unified-billing-cycle-block, .amc-billing-cycle-block').toggleClass('d-none', isMilestones);
    $page.find('.unified-milestones-block, .amc-milestones-block').toggleClass('d-none', !isMilestones);
    $page.find('#unified-billing-schedule-block').toggleClass('d-none', isMilestones);
    $page.find('.unified-billing-cycle-block :input, .amc-billing-cycle-block :input').prop('disabled', isMilestones);
    $page.find('.unified-milestones-block :input, .amc-milestones-block :input').prop('disabled', !isMilestones);
    var $amount = $('#amc_contract_amount');
    if ($amount.length) {
        if (isMilestones) {
            $amount.prop('readonly', true).css('background-color', '#f8f9fa');
            updateUnifiedMilestonesTotal();
        } else {
            $amount.prop('readonly', false).css('background-color', '');
            $('#unified-billing-schedule-body').removeAttr('data-server-prefilled');
            buildBillingSchedulePreview();
        }
    }
}

function normalizeYearPart(yearPart) {
    var y = parseInt(yearPart, 10);
    if (isNaN(y)) {
        return '';
    }
    if (yearPart.length <= 2) {
        return y < 100 ? (2000 + y) : y;
    }
    return y;
}

function dateObjToYmd(dateObj) {
    if (!dateObj || isNaN(dateObj.getTime())) {
        return '';
    }
    var y = dateObj.getFullYear();
    var m = String(dateObj.getMonth() + 1).padStart(2, '0');
    var d = String(dateObj.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + d;
}

function getUnifiedDateFormat() {
    var $hideFmt = $('#hide_config_date_format');
    if ($hideFmt.length && $.trim($hideFmt.val() || '') !== '') {
        return $.trim($hideFmt.val());
    }
    if (typeof date_format !== 'undefined' && date_format) {
        return date_format;
    }
    if (typeof DateFormate !== 'undefined' && DateFormate) {
        return DateFormate;
    }
    return 'dd-mm-yy';
}

function toBootstrapDateFormat(fmt) {
    var normalized = String(fmt || 'dd-mm-yyyy').toLowerCase();
    if (normalized.indexOf('yyyy') === -1) {
        normalized = normalized.replace(/yy/g, 'yyyy');
    }
    return normalized;
}

function toJqueryUiDateFormat(fmt) {
    return String(fmt || 'dd-mm-yy').toLowerCase().replace(/yyyy/g, 'yy');
}

function isBootstrapDatepickerPlugin() {
    return typeof $.fn.datepicker === 'function' && $.fn.datepicker.DPGlobal;
}

function amcDatepickerDestroy($input) {
    if (!$input || !$input.length || typeof $.fn.datepicker !== 'function') {
        return;
    }
    if (isBootstrapDatepickerPlugin() && $input.data('datepicker')) {
        $input.datepicker('destroy');
        return;
    }
    if ($input.hasClass('hasDatepicker')) {
        $input.datepicker('destroy');
    }
}

function amcDatepickerHasInstance($input) {
    if (!$input || !$input.length) {
        return false;
    }
    if (isBootstrapDatepickerPlugin()) {
        return !!$input.data('datepicker');
    }
    return $input.hasClass('hasDatepicker');
}

function parseUnifiedDateToYmd(dateStr) {
    var raw = $.trim(dateStr || '');
    if (!raw || raw === '01-01-1970' || raw === '01/01/1970') {
        return '';
    }
    var sepMatch = raw.match(/[\/\-\.]/);
    if (!sepMatch) {
        return '';
    }
    var parts = raw.split(sepMatch[0]);
    if (parts.length !== 3) {
        return '';
    }
    if (parts[0].length === 4) {
        return parts[0] + '-' + parts[1] + '-' + parts[2];
    }
    var fmt = toBootstrapDateFormat(getUnifiedDateFormat());
    var dayIdx = 0;
    var monthIdx = 1;
    var yearIdx = 2;
    var ddPos = fmt.indexOf('dd');
    var mmPos = fmt.indexOf('mm');
    if (mmPos !== -1 && ddPos !== -1 && mmPos < ddPos) {
        monthIdx = 0;
        dayIdx = 1;
        yearIdx = 2;
    }
    var year = normalizeYearPart(parts[yearIdx]);
    if (!year) {
        return '';
    }
    var month = String(parseInt(parts[monthIdx], 10)).padStart(2, '0');
    var day = String(parseInt(parts[dayIdx], 10)).padStart(2, '0');
    if (month === 'NaN' || day === 'NaN') {
        return '';
    }
    return year + '-' + month + '-' + day;
}

function getUnifiedContractDateYmd(selector) {
    var $page = $('#unified-contract-page');
    var $input = $page.find(selector);
    if (!$input.length) {
        return '';
    }
    var inputVal = $.trim($input.val() || '');
    var fromInput = parseUnifiedDateToYmd(inputVal);
    var fromPicker = '';
    if (typeof $.fn.datepicker === 'function') {
        try {
            var picked = $input.datepicker('getDate');
            fromPicker = dateObjToYmd(picked);
        } catch (e) {
        }
    }
    if (fromInput && fromPicker && fromInput !== fromPicker) {
        return fromInput;
    }
    if (fromInput) {
        return fromInput;
    }
    if (fromPicker) {
        return fromPicker;
    }
    return '';
}

function toggleBillingScheduleHint(showHint) {
    var $hint = $('#unified-billing-schedule-hint');
    if ($hint.length) {
        $hint.toggleClass('d-none', !showHint);
    }
}

function formatYmdToDisplay(ymd) {
    if (!ymd) {
        return '—';
    }
    var parts = ymd.split('-');
    if (parts.length !== 3) {
        return ymd;
    }
    return parts[2] + '-' + parts[1] + '-' + parts[0];
}

function getBillingPeriodLabel(cycle, index) {
    var labels = {
        monthly: 'Month',
        quarterly: 'Quarter',
        half_yearly: 'Half-Year',
        yearly: 'Year'
    };
    var prefix = labels[cycle] || 'Period';
    return prefix + ' ' + (index + 1);
}

function syncBillingScheduleTotalFromAmount() {
    var $page = $('#unified-contract-page');
    var $totalCell = $('#unified-billing-schedule-total');
    if (!$totalCell.length) {
        return;
    }
    var totalAmount = parseFloat($page.find('#amc_contract_amount').val()) || 0;
    $totalCell.text(totalAmount.toFixed(2));
}

function buildBillingSchedulePreview() {
    var $page = $('#unified-contract-page');
    var $tbody = $('#unified-billing-schedule-body');
    var $totalCell = $('#unified-billing-schedule-total');
    if (!$tbody.length) {
        return;
    }
    var mode = $page.find('input[name="amc_payment_mode"]:checked').val() || 'billing_cycle';
    if (mode === 'milestones') {
        return;
    }
    if ($tbody.attr('data-server-prefilled') === '1') {
        return;
    }
    var startYmd = getUnifiedContractDateYmd('.amc-start-datepicker');
    var endYmd = getUnifiedContractDateYmd('.amc-end-datepicker');
    if (!startYmd) {
        startYmd = parseUnifiedDateToYmd($page.find('[name="start_date"]').val());
    }
    if (!endYmd) {
        endYmd = parseUnifiedDateToYmd($page.find('[name="end_date"]').val());
    }
    var cycle = $page.find('[name="amc_billing_cycle"]').val() || 'quarterly';
    var billingDay = parseInt($page.find('[name="amc_billing_day"]').val(), 10) || 1;
    var totalAmount = parseFloat($page.find('#amc_contract_amount').val()) || 0;
    var intervalMap = { monthly: 1, quarterly: 3, half_yearly: 6, yearly: 12 };
    var intervalMonths = intervalMap[cycle] || 3;
    billingDay = Math.max(1, Math.min(31, billingDay));
    $tbody.empty();
    if (!startYmd || !endYmd || startYmd > endYmd) {
        if ($totalCell.length) {
            $totalCell.text(totalAmount.toFixed(2));
        }
        toggleBillingScheduleHint(true);
        return;
    }
    toggleBillingScheduleHint(false);
    var dates = [];
    var start = new Date(startYmd + 'T00:00:00');
    var end = new Date(endYmd + 'T00:00:00');
    var cursor = new Date(start.getTime());
    var daysInMonth = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate();
    cursor.setDate(Math.min(billingDay, daysInMonth));
    if (cursor < start) {
        cursor.setMonth(cursor.getMonth() + intervalMonths);
        daysInMonth = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate();
        cursor.setDate(Math.min(billingDay, daysInMonth));
    }
    while (cursor <= end) {
        var y = cursor.getFullYear();
        var m = String(cursor.getMonth() + 1).padStart(2, '0');
        var d = String(cursor.getDate()).padStart(2, '0');
        dates.push(y + '-' + m + '-' + d);
        cursor.setMonth(cursor.getMonth() + intervalMonths);
        daysInMonth = new Date(cursor.getFullYear(), cursor.getMonth() + 1, 0).getDate();
        cursor.setDate(Math.min(billingDay, daysInMonth));
    }
    if (!dates.length) {
        dates.push(startYmd);
    }
    var count = dates.length;
    var priceEach = count > 0 ? Math.round((totalAmount / count) * 100) / 100 : 0;
    var runningTotal = 0;
    dates.forEach(function (dateYmd, i) {
        var amount = priceEach;
        if (i === count - 1) {
            amount = Math.round((totalAmount - runningTotal) * 100) / 100;
        } else {
            runningTotal += amount;
        }
        var label = getBillingPeriodLabel(cycle, i);
        $tbody.append(
            '<tr><td>' + label + '</td><td class="text-end">' + amount.toFixed(2) + '</td><td>' + formatYmdToDisplay(dateYmd) + '</td></tr>'
        );
    });
    if ($totalCell.length) {
        $totalCell.text(totalAmount.toFixed(2));
    }
}

function updateUnifiedMilestonesTotal() {
    var $page = $('#unified-contract-page');
    var mode = $page.find('input[name="amc_payment_mode"]:checked').val() || 'billing_cycle';
    var total = 0;
    $page.find('#unified-milestone-container .milestone-price').each(function () {
        total += parseFloat($(this).val()) || 0;
    });
    total = Math.round(total * 100) / 100;
    $('#unified-milestones-total').text(total.toFixed(2));
    if (mode === 'milestones') {
        $('#amc_contract_amount').val(total > 0 ? total.toFixed(2) : '');
        if (typeof renderRenewalAmountHistoryPanel === 'function') {
            renderRenewalAmountHistoryPanel();
        }
    }
}

function initUnifiedCommercials() {
    var $page = $('#unified-contract-page');
    if (!$page.length) {
        return;
    }
    var container = document.getElementById('unified-milestone-container');
    var addBtn = document.getElementById('unified-add-milestone');
    if (container) {
        bindUnifiedMilestoneContainer(container);
    }
    if (addBtn && container) {
        addBtn.addEventListener('click', function () {
            addUnifiedMilestoneRow(container);
            updateUnifiedMilestonesTotal();
        });
    }
    $page.off('input.unifiedCommercials change.unifiedCommercials', '#unified-milestone-container .milestone-price')
        .on('input.unifiedCommercials change.unifiedCommercials', '#unified-milestone-container .milestone-price', updateUnifiedMilestonesTotal);
    $page.off('input.unifiedBilling change.unifiedBilling', '#amc_contract_amount, [name="amc_billing_cycle"], [name="amc_billing_day"], [name="start_date"], [name="end_date"], .amc-start-datepicker, .amc-end-datepicker')
        .on('input.unifiedBilling change.unifiedBilling', '#amc_contract_amount, [name="amc_billing_cycle"], [name="amc_billing_day"], [name="start_date"], [name="end_date"], .amc-start-datepicker, .amc-end-datepicker', function () {
            if (($page.find('input[name="amc_payment_mode"]:checked').val() || 'billing_cycle') !== 'milestones') {
                $('#unified-billing-schedule-body').removeAttr('data-server-prefilled');
                buildBillingSchedulePreview();
            }
        });
    $page.off('select2:select.unifiedBilling change.unifiedBillingCycle', '[name="amc_billing_cycle"], #unified_billing_cycle')
        .on('select2:select.unifiedBilling change.unifiedBillingCycle', '[name="amc_billing_cycle"], #unified_billing_cycle', function () {
            if (($page.find('input[name="amc_payment_mode"]:checked').val() || 'billing_cycle') !== 'milestones') {
                $('#unified-billing-schedule-body').removeAttr('data-server-prefilled');
                buildBillingSchedulePreview();
            }
        });
    syncCommercialsBlockUi();
}

function initContractTypeToggle() {
    if (isUnifiedEditMode() || isUnifiedRenewMode()) {
        applyContractPageModeUi();
        return;
    }
    $('input[name="page_contract_type"]').off('change.unifiedToggle').on('change.unifiedToggle', function () {
        try {
            sessionStorage.setItem('unified_contract_page_type', $(this).val());
        } catch (e) { /* ignore */ }
        applyContractPageModeUi();
    });
    try {
        var saved = sessionStorage.getItem('unified_contract_page_type');
        if (saved === 'amc' || saved === 'standard') {
            var radio = document.getElementById(saved === 'amc' ? 'page_contract_type_amc' : 'page_contract_type_standard');
            if (radio) {
                radio.checked = true;
            }
        }
    } catch (e) { /* ignore */ }
    applyContractPageModeUi();
}

function initStandardLocationsSelect() {
    var $sel = $('#unified_locations_multiselect');
    if (!$sel.length || !$.fn.select2) {
        return;
    }
    if (!$sel.data('select2')) {
        $sel.select2({ width: '100%', placeholder: 'Select Locations' });
    }
    $sel.off('change.unifiedLocations select2:select.unifiedLocations select2:unselect.unifiedLocations')
        .on('change.unifiedLocations select2:select.unifiedLocations select2:unselect.unifiedLocations', syncUnifiedLocationsMultiselectToForm);
}

function isUnifiedLocationsRequired() {
    var flagEl = document.getElementById('unified-locations-required');
    if (flagEl) {
        return flagEl.textContent.trim() === 'true';
    }
    var locSelect = document.getElementById('unified_locations_multiselect');
    return !!(locSelect && locSelect.hasAttribute('required'));
}

function getUnifiedLocationsMultiselectValues() {
    var locSelect = document.getElementById('unified_locations_multiselect');
    if (!locSelect) {
        return [];
    }
    var $el = $(locSelect);
    if ($el.data('select2')) {
        var data = $el.select2('data');
        if (Array.isArray(data) && data.length) {
            var fromData = data.map(function (d) {
                return d.id != null ? String(d.id) : '';
            }).filter(function (v) {
                return v && v !== '0';
            });
            if (fromData.length) {
                return fromData;
            }
        }
    }
    var vals = $el.val();
    if (Array.isArray(vals) && vals.length) {
        return vals.map(String).filter(function (v) {
            return v && v !== '0';
        });
    }
    if (vals && !Array.isArray(vals)) {
        return String(vals) !== '0' ? [String(vals)] : [];
    }
    return Array.from(locSelect.selectedOptions || []).map(function (opt) {
        return String(opt.value);
    }).filter(function (v) {
        return v && v !== '0';
    });
}

function syncUnifiedLocationsMultiselectToForm() {
    var locSelect = document.getElementById('unified_locations_multiselect');
    if (!locSelect) {
        return;
    }
    var vals = getUnifiedLocationsMultiselectValues();
    Array.from(locSelect.options).forEach(function (opt) {
        opt.selected = vals.indexOf(String(opt.value)) !== -1;
    });
    var hidden = document.getElementById('hidden_locations');
    if (hidden) {
        hidden.value = JSON.stringify(vals);
    }
}

function initContractServicesSelect() {
    var $sel = $('#unified_contract_services_multiselect');
    if (!$sel.length || !$.fn.select2) {
        return;
    }
    if (!$sel.data('select2')) {
        $sel.select2({ width: '100%', placeholder: 'Select Services' });
    }
}

var unifiedContactPersonCounter = 100;

function initContactPersonsUi() {
    var $page = $('#unified-contract-page');
    if (!$page.length) {
        return;
    }
    $page.off('click.unifiedContactPersonAdd', '#unified-add-contact-person-btn')
        .on('click.unifiedContactPersonAdd', '#unified-add-contact-person-btn', function () {
            addMoreContactPerson();
        });
    $page.off('click.unifiedContactPersonRemove', '.unified-remove-contact-person')
        .on('click.unifiedContactPersonRemove', '.unified-remove-contact-person', function () {
            var targetId = $(this).data('target-id');
            if (targetId) {
                removeContactPersonsRemove(targetId);
            }
        });
}

function addMoreContactPerson() {
    unifiedContactPersonCounter = unifiedContactPersonCounter + 1;
    var idcnt = 'contact_persons_remove_' + unifiedContactPersonCounter;
    var innerHTML = '<div class="row g-2 unified-contact-person-row mb-0" id="' + idcnt + '">' +
        '<div class="col-lg-4">' +
        '<input type="text" name="contact_persons[name][]" value="" class="form-control" placeholder="Contact Person Name">' +
        '</div>' +
        '<div class="col-lg-4">' +
        '<input type="email" name="contact_persons[email][]" value="" class="form-control" placeholder="Contact Person Email">' +
        '</div>' +
        '<div class="col-lg-3">' +
        '<input type="tel" name="contact_persons[number][]" value="" class="form-control" placeholder="Contact Person Number">' +
        '</div>' +
        '<div class="col-lg-1 d-flex align-items-center">' +
        '<button type="button" class="btn btn-link p-0 unified-remove-contact-person text-danger" data-target-id="' + idcnt + '" title="Click to Remove">' +
        '<i class="ti-trash"></i>' +
        '</button>' +
        '</div>' +
        '</div>';
    $('#MoreContactPerson').append(innerHTML);
}

function removeContactPersonsRemove(id) {
    $('#' + id).remove();
}

function initOemToggle() {
    var oemYes = document.getElementById('unified_oem_yes');
    var oemNo = document.getElementById('unified_oem_no');
    var oemDetails = document.getElementById('unified_oem_details');
    if (!oemDetails) {
        return;
    }
    function toggleOemDetails() {
        if (oemYes && oemYes.checked) {
            oemDetails.classList.remove('d-none');
        } else {
            oemDetails.classList.add('d-none');
        }
    }
    if (oemYes) {
        oemYes.addEventListener('change', toggleOemDetails);
    }
    if (oemNo) {
        oemNo.addEventListener('change', toggleOemDetails);
    }
    toggleOemDetails();
}

function buildMilestoneRowHtml(index) {
    return '<input type="text" name="milestone_name_' + index + '" class="form-control milestone-name" placeholder="Milestone Name">' +
        '<input type="text" name="milestone_date_' + index + '" class="form-control datepicker milestone-date" placeholder="Date">' +
        '<input type="number" step="any" name="milestone_price_' + index + '" class="form-control milestone-price text-end" placeholder="Price">' +
        '<button type="button" class="btn btn-danger btn-sm remove-milestone" title="Remove milestone">&times;</button>';
}

function initMilestoneDatePicker(context) {
    if (!context || typeof $.fn.datepicker !== 'function') {
        return;
    }
    var fmt = (typeof date_format !== 'undefined') ? date_format : 'dd-mm-yy';
    $(context).find('.datepicker').each(function () {
        if (!$(this).hasClass('hasDatepicker')) {
            $(this).datepicker({
                showOn: 'both',
                buttonText: 'Calendar',
                dateFormat: fmt,
                changeMonth: true,
                changeYear: true
            }).next('button.ui-datepicker-trigger')
                .html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
        }
    });
}

function reindexMilestoneRows(container) {
    if (!container) {
        container = document.getElementById('unified-milestone-container');
    }
    if (!container) {
        return;
    }
    container.querySelectorAll('.milestone-row').forEach(function (row, i) {
        var idx = i + 1;
        var nameInput = row.querySelector('.milestone-name');
        var dateInput = row.querySelector('.milestone-date');
        var priceInput = row.querySelector('.milestone-price');
        if (nameInput) {
            nameInput.name = 'milestone_name_' + idx;
        }
        if (dateInput) {
            dateInput.name = 'milestone_date_' + idx;
        }
        if (priceInput) {
            priceInput.name = 'milestone_price_' + idx;
        }
    });
}

function isValidNumericAmount(val) {
    if (val == '' || val == null) {
        return false;
    }
    var num = parseFloat(val);
    return !isNaN(num) && isFinite(num);
}

function validateContractAmountAndBilling($form) {
    var amount = $.trim($form.find('[name="contract_amount"]').val() || '');
    if (!isValidNumericAmount(amount)) {
        amcNotifyWarning('Contract amount must be a valid number.');
        return false;
    }

    var paymentMode = $form.find('input[name="amc_payment_mode"]:checked').val() || 'billing_cycle';
    if (paymentMode == 'milestones') {
        var hasMilestone = false;
        $form.find('#unified-milestone-container .milestone-row').each(function () {
            var name = $.trim($(this).find('.milestone-name').val() || '');
            var date = $.trim($(this).find('.milestone-date').val() || '');
            var price = $.trim($(this).find('.milestone-price').val() || '');
            if (name && date && price) {
                hasMilestone = true;
            }
        });
        if (!hasMilestone) {
            amcNotifyWarning('Add at least one milestone with name, date, and price.');
            return false;
        }
    } else {
        var billingDay = parseInt($form.find('[name="amc_billing_day"]').val(), 10);
        if (isNaN(billingDay) || billingDay < 1 || billingDay > 31) {
            amcNotifyWarning('Billing date must be between 1 and 31.');
            return false;
        }
    }

    return true;
}

function getNextMilestoneIndex(container) {
    var max = 0;
    container.querySelectorAll('.milestone-row').forEach(function (row) {
        var nameInput = row.querySelector('.milestone-name');
        if (!nameInput || !nameInput.name) {
            return;
        }
        var match = nameInput.name.match(/milestone_name_(\d+)/);
        if (match) {
            max = Math.max(max, parseInt(match[1], 10) || 0);
        }
    });
    return max + 1;
}

function bindUnifiedMilestoneContainer(container) {
    if (!container || container.dataset.milestoneBound === '1') {
        return;
    }
    container.dataset.milestoneBound = '1';
    container.querySelectorAll('.milestone-row').forEach(function (row) {
        if (!row.querySelector('.remove-milestone')) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-danger btn-sm remove-milestone';
            btn.title = 'Remove milestone';
            btn.innerHTML = '&times;';
            row.appendChild(btn);
        }
    });
    initMilestoneDatePicker(container);
    container.addEventListener('click', function (e) {
        var btn = e.target.closest('.remove-milestone');
        if (!btn || !container.contains(btn)) {
            return;
        }
        var row = btn.closest('.milestone-row');
        if (!row) {
            return;
        }
        if (container.querySelectorAll('.milestone-row').length <= 1) {
            amcNotifyWarning('At least one milestone row is required.');
            return;
        }
        row.remove();
        reindexMilestoneRows(container);
        updateUnifiedMilestonesTotal();
    });
}

function addUnifiedMilestoneRow(container) {
    if (!container) {
        return null;
    }
    var idx = getNextMilestoneIndex(container);
    var row = document.createElement('div');
    row.className = 'milestone-row mb-2 d-flex align-items-center gap-2';
    row.innerHTML = buildMilestoneRowHtml(idx);
    container.appendChild(row);
    initMilestoneDatePicker(row);
    return row;
}

function initStandardMilestones() {
    initUnifiedCommercials();
}

function unifiedCopyFormFieldToHidden(fieldName) {
    var sourceField = document.querySelector('#amcContractForm [name="' + fieldName + '"]');
    var targetField = document.getElementById('hidden_' + fieldName);
    if (sourceField && targetField) {
        targetField.value = sourceField.value || '';
    }
}

function initEditTabs() {
    if (!isUnifiedEditMode()) {
        return;
    }
    var documentsForm = document.getElementById('documentsForm');
    if (documentsForm && !documentsForm.dataset.unifiedBound) {
        documentsForm.dataset.unifiedBound = '1';
        documentsForm.addEventListener('submit', function () {
            var fieldsToCopy = [
                'contract_name', 'transaction_no', 'invoice_number', 'rt_number',
                'business_code', 'parent_contract_id', 'contract_payment_terms',
                'typeofaggrements', 'contract_owner_name', 'contract_owner_email',
                'contract_owner_number', 'party_id', 'start_date', 'end_date',
                'renewal_frequency', 'contract_amount', 'description', 'additional_notes',
                'payment_term_id', 'termination_condition', 'contract_status',
                'renew_type', 'state', 'isp', 'paid_by', 'internet_charges',
                'hub_spoc_name', 'hub_spoc_number', 'wifi_provider', 'wifi_pass',
                'description_id', 'department_id', 'type_of_contract_id'
            ];
            fieldsToCopy.forEach(unifiedCopyFormFieldToHidden);
            var partyTypeRadio = document.querySelector('#amcContractForm input[name="party_type"]:checked');
            var hiddenPartyType = document.getElementById('hidden_party_type');
            if (partyTypeRadio && hiddenPartyType) {
                hiddenPartyType.value = partyTypeRadio.value;
            }
            var autoRenewCheckbox = document.querySelector('#amcContractForm input[name="auto_renew"]');
            var hiddenAutoRenew = document.getElementById('hidden_auto_renew');
            if (hiddenAutoRenew) {
                hiddenAutoRenew.value = (autoRenewCheckbox && autoRenewCheckbox.checked) ? '1' : '0';
            }
            var locationsSelect = document.getElementById('unified_locations_multiselect');
            var hiddenLocations = document.getElementById('hidden_locations');
            if (locationsSelect && hiddenLocations) {
                var selectedOptions = Array.from(locationsSelect.selectedOptions).map(function (opt) { return opt.value; });
                hiddenLocations.value = JSON.stringify(selectedOptions);
            }
            var oemYes = document.getElementById('unified_oem_yes');
            var hiddenIsOem = document.getElementById('hidden_is_oem');
            if (hiddenIsOem) {
                hiddenIsOem.value = (oemYes && oemYes.checked) ? 'Yes' : 'No';
            }
            ['oem_registration_number', 'oem_distributor_name', 'oem_product_category'].forEach(unifiedCopyFormFieldToHidden);
        });
    }
    var permissionsForm = document.getElementById('permissionsForm');
    if (permissionsForm && !permissionsForm.dataset.unifiedBound) {
        permissionsForm.dataset.unifiedBound = '1';
        permissionsForm.addEventListener('submit', function (e) {
            var userSelect = permissionsForm.querySelector('select[name="selected_user"]');
            var checkboxes = permissionsForm.querySelectorAll('input[name="permissions[]"]:checked');
            if (!userSelect || !userSelect.value) {
                e.preventDefault();
                amcNotifyWarning('Please select a user.');
                return false;
            }
            if (!checkboxes.length) {
                e.preventDefault();
                amcNotifyWarning('Please select at least one permission.');
                return false;
            }
        });
    }
}

function getUnifiedPartyType() {
    if (document.getElementById('amc_inhouse') && document.getElementById('amc_inhouse').checked) {
        return null;
    }
    if (document.getElementById('amc_customer') && document.getElementById('amc_customer').checked) {
        return 'customer';
    }
    if (document.getElementById('amc_vendor') && document.getElementById('amc_vendor').checked) {
        return 'vendor';
    }
    return null;
}

function showUnifiedAlert(message, type) {
    type = type || 'info';
    var alertContainer = document.querySelector('#unified-contract-page .col-lg-12') || document.querySelector('#unified-contract-page');
    if (!alertContainer) {
        amcNotifyWarning(message);
        return;
    }
    var alert = document.createElement('div');
    alert.className = 'alert alert-' + type + ' alert-dismissible fade show';
    alert.innerHTML = '<strong>' + message + '</strong><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    var existingAlerts = alertContainer.querySelectorAll('.alert');
    existingAlerts.forEach(function (existingAlert) {
        existingAlert.remove();
    });
    alertContainer.insertBefore(alert, alertContainer.firstChild);
    window.setTimeout(function () {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

function openAddPartyModal() {
    var partyType = getUnifiedPartyType();
    if (!partyType) {
        showUnifiedAlert('Please select Party Type (Customer or Vendor) first.', 'warning');
        return;
    }
    var modalPartyType = document.getElementById('modalPartyType');
    var text1 = document.getElementById('partyTypeText');
    var text2 = document.getElementById('partyTypeText2');
    var text3 = document.getElementById('partyTypeText3');
    var formattedPartyType = partyType.charAt(0).toUpperCase() + partyType.slice(1);
    if (modalPartyType) {
        modalPartyType.value = partyType;
    }
    if (text1) {
        text1.textContent = formattedPartyType;
    }
    if (text2) {
        text2.textContent = formattedPartyType;
    }
    if (text3) {
        text3.textContent = formattedPartyType.toLowerCase();
    }
    var modalEl = document.getElementById('addPartyModal');
    var modal = getBootstrapModal(modalEl);
    if (modal) {
        modal.show();
    } else if (modalEl && typeof jQuery !== 'undefined') {
        jQuery(modalEl).modal('show');
    }
}

function submitAddPartyForm(e) {
    if (e) {
        e.preventDefault();
    }
    var form = document.getElementById('addPartyForm');
    if (!form) {
        return;
    }
    var formData = new FormData(form);
    var partyName = formData.get('party_name');
    if (!partyName || !String(partyName).trim()) {
        showUnifiedAlert('Party name is required.', 'danger');
        return;
    }
    var submitBtn = form.querySelector('button[type="submit"]');
    var originalText = submitBtn ? submitBtn.textContent : '';
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Adding...';
    }
    var payload = new URLSearchParams({
        page: 'add_party',
        party_type: formData.get('party_type') || '',
        party_name: formData.get('party_name') || '',
        email: formData.get('email') || '',
        phone: formData.get('phone') || '',
        company: formData.get('company') || '',
        address: formData.get('address') || '',
        bp_code: formData.get('bp_code') || ''
    });
    fetch('ajax.php', {
        method: 'POST',
        body: payload
    })
        .then(function (res) {
            return res.text();
        })
        .then(function (text) {
            var data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                throw err;
            }
            if (data.success) {
                var addedPartyType = formData.get('party_type') || 'Party';
                var addedPartyName = formData.get('party_name') || '';
                var formattedType = addedPartyType.charAt(0).toUpperCase() + addedPartyType.slice(1);
                var message = formattedType + " '" + addedPartyName + "' has been added successfully. You can now select them from the list.";
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Party Added',
                        text: message,
                        confirmButtonText: 'Great!',
                        customClass: { confirmButton: 'btn btn-primary' },
                        buttonsStyling: false
                    });
                } else {
                    showUnifiedAlert(message, 'success');
                }
                var modalEl = document.getElementById('addPartyModal');
                var modal = getBootstrapModal(modalEl);
                if (modal) {
                    modal.hide();
                } else if (modalEl && typeof jQuery !== 'undefined') {
                    jQuery(modalEl).modal('hide');
                }
                form.reset();
                var newId = data.result && data.result.res ? data.result.res.LASTID : '';
                loadAmcPartiesAjax(newId, true);
            } else {
                showUnifiedAlert(data.message || 'Error adding party.', 'danger');
            }
        })
        .catch(function () {
            showUnifiedAlert('Error adding party. Please try again.', 'danger');
        })
        .finally(function () {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
}

function initAddPartyModal() {
    var addBtn = document.getElementById('unified-add-party-btn');
    var modalEl = document.getElementById('addPartyModal');
    var addPartyForm = document.getElementById('addPartyForm');
    if (addBtn) {
        addBtn.addEventListener('click', openAddPartyModal);
    }
    if (addPartyForm) {
        addPartyForm.addEventListener('submit', submitAddPartyForm);
    }
    if (modalEl) {
        modalEl.addEventListener('show.bs.modal', function () {
            var partyType = getUnifiedPartyType();
            if (!partyType) {
                return;
            }
            var formattedPartyType = partyType.charAt(0).toUpperCase() + partyType.slice(1);
            var modalPartyType = document.getElementById('modalPartyType');
            if (modalPartyType) {
                modalPartyType.value = partyType;
            }
            var text1 = document.getElementById('partyTypeText');
            var text2 = document.getElementById('partyTypeText2');
            var text3 = document.getElementById('partyTypeText3');
            if (text1) {
                text1.textContent = formattedPartyType;
            }
            if (text2) {
                text2.textContent = formattedPartyType;
            }
            if (text3) {
                text3.textContent = formattedPartyType.toLowerCase();
            }
        });
    }
}

function showUnifiedEditOptions() {
    var modalEl = document.getElementById('editConfirmationModal');
    var modal = getBootstrapModal(modalEl);
    if (modal) {
        modal.show();
    } else if (modalEl && typeof jQuery !== 'undefined') {
        jQuery(modalEl).modal('show');
    }
}

function submitUnifiedWithOption(option) {
    var editOptionInput = document.getElementById('unified_edit_option');
    if (editOptionInput) {
        editOptionInput.value = option;
    }
    var form = document.getElementById('amcContractForm');
    if (!form) {
        return;
    }
    if (getContractPageMode() === 'amc') {
        submitAmcContract(true);
    } else {
        submitStandardContract(true);
    }
}

window.showEditOptions = showUnifiedEditOptions;
window.submitWithOption = submitUnifiedWithOption;

function showCancelModal() {
    var modalEl = document.getElementById('cancelContractModal');
    var modal = getBootstrapModal(modalEl);
    if (modal) {
        modal.show();
    } else if (modalEl && typeof jQuery !== 'undefined') {
        jQuery(modalEl).modal('show');
    }
}

async function submitCancellation() {
    var reasonEl = document.getElementById('cancel_reason');
    var reason = reasonEl ? reasonEl.value : '';
    if (!reason || reason.trim() === '') {
        alert('Please enter a reason for cancellation.');
        return;
    }
    var ridEl = document.getElementById('contractId') || document.getElementById('contract-id');
    var rid = ridEl ? (ridEl.value || ridEl.textContent) : '';
    var company_id = document.getElementById('company-id').textContent;
    var user_id = document.getElementById('user-id').textContent;
    var data_name = document.getElementById('data-name').textContent;
    var submitBtn = document.querySelector('#cancelContractModal .btn-danger');
    var originalText = submitBtn ? submitBtn.textContent : '';
    if (submitBtn) {
        submitBtn.textContent = 'Processing...';
        submitBtn.disabled = true;
    }
    try {
        var response = await fetch('api.php?mode=cancelcontractreact&method=post&user_id=' + user_id + '&company_id=' + company_id + '&data_name=' + data_name + '&platform=react', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ Rid: rid, cancel_reason: reason })
        });
        var result = await response.json();
        if (result.status == 'success') {
            window.location.href = 'index.php?option=COM_LIST_CONTRACTS';
            return;
        }
        alert(result.message || 'Failed to cancel contract.');
    } catch (error) {
        console.error('Error during cancellation:', error);
        alert('Failed to cancel contract. Please try again.');
    } finally {
        if (submitBtn) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    }
}

window.showCancelModal = showCancelModal;
window.submitCancellation = submitCancellation;

function syncAmcDatepickerFromInputValue($input) {
    if (!$input || !$input.length || typeof $.fn.datepicker !== 'function') {
        return;
    }
    if (!amcDatepickerHasInstance($input)) {
        return;
    }
    var ymd = parseUnifiedDateToYmd($.trim($input.val() || ''));
    if (!ymd) {
        return;
    }
    var parts = ymd.split('-');
    var dateObj = new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
    if (isNaN(dateObj.getTime())) {
        return;
    }
    $input.datepicker('setDate', dateObj);
}

function initAmcDatePickers() {
    var $root = $('#unified-contract-page');
    if (!$root.length || typeof $.fn.datepicker !== 'function') {
        return;
    }

    var $start = $root.find('.amc-start-datepicker');
    var $end = $root.find('.amc-end-datepicker');
    if (!$start.length || !$end.length) {
        return;
    }

    function rebuildBillingSchedule() {
        $('#unified-billing-schedule-body').removeAttr('data-server-prefilled');
        buildBillingSchedulePreview();
    }

    amcDatepickerDestroy($start);
    amcDatepickerDestroy($end);

    if (isBootstrapDatepickerPlugin()) {
        var fmt = toBootstrapDateFormat(getUnifiedDateFormat());
        var pickerOpts = {
            format: fmt,
            autoclose: true,
            todayHighlight: true,
            clearBtn: false
        };
        $start.datepicker(pickerOpts).off('changeDate.amcBilling clearDate.amcBilling')
            .on('changeDate.amcBilling clearDate.amcBilling', function () {
                var start = $start.datepicker('getDate');
                if (start) {
                    $end.datepicker('setStartDate', start);
                    var endDate = $end.datepicker('getDate');
                    if (!endDate || endDate < start) {
                        $end.datepicker('setDate', start);
                    }
                }
                rebuildBillingSchedule();
            });
        $end.datepicker(pickerOpts).off('changeDate.amcBilling clearDate.amcBilling')
            .on('changeDate.amcBilling clearDate.amcBilling', rebuildBillingSchedule);
        syncAmcDatepickerFromInputValue($start);
        syncAmcDatepickerFromInputValue($end);
        var bootstrapStartDate = $start.datepicker('getDate');
        if (bootstrapStartDate) {
            $end.datepicker('setStartDate', bootstrapStartDate);
        }
        return;
    }

    var uiFmt = toJqueryUiDateFormat(getUnifiedDateFormat());

    $start.datepicker({
        dateFormat: uiFmt,
        changeMonth: true,
        changeYear: true,
        onSelect: function () {
            var start = $start.datepicker('getDate');
            if (start) {
                $end.datepicker('option', 'minDate', start);
                var endDate = $end.datepicker('getDate');
                if (!endDate || endDate < start) {
                    $end.datepicker('setDate', start);
                }
            }
            rebuildBillingSchedule();
        }
    });

    $end.datepicker({
        dateFormat: uiFmt,
        changeMonth: true,
        changeYear: true,
        onSelect: rebuildBillingSchedule,
        onClose: function () {
            var dt1 = $start.datepicker('getDate');
            var dt2 = $end.datepicker('getDate');
            if (dt1 && dt2 && dt2 < dt1) {
                $end.datepicker('setDate', dt1);
            }
            rebuildBillingSchedule();
        }
    });

    syncAmcDatepickerFromInputValue($start);
    syncAmcDatepickerFromInputValue($end);
    var uiStartDate = $start.datepicker('getDate');
    if (uiStartDate) {
        $end.datepicker('option', 'minDate', uiStartDate);
    }
}

var amcWizardCurrentStep = 0;

function getAmcStepDataFilled() {
    var $form = $('#unified-contract-page #amcContractForm');
    if (!$form.length) {
        return [false, false, false, false, false, false];
    }

    var startDate = $.trim($form.find('[name="start_date"]').val() || '');
    var endDate = $.trim($form.find('[name="end_date"]').val() || '');
    if (startDate === '01-01-1970') {
        startDate = '';
    }
    if (endDate === '01-01-1970') {
        endDate = '';
    }

    var partySelect = document.getElementById('amc_party_id');
    var partyId = partySelect ? partySelect.value : $form.find('[name="party_id"]').val();
    var isInhouse = document.getElementById('amc_inhouse') && document.getElementById('amc_inhouse').checked;

    var detailsFilled = !!$.trim($form.find('[name="contract_name"]').val() || '') && (isInhouse || !!partyId) && !!startDate && !!endDate;
    var commercialsFilled = !!$.trim($form.find('[name="contract_amount"]').val() || '');

    if (getContractPageMode() === 'standard') {
        var locationsOk = !isUnifiedLocationsRequired() || getUnifiedLocationsMultiselectValues().length > 0;
        return [detailsFilled && locationsOk, commercialsFilled];
    }

    var hasLocation = false;
    document.querySelectorAll('#amc_locations_container .amc-location-block').forEach(function (block) {
        var locSelect = block.querySelector('.amc-location-select');
        if (locSelect && locSelect.value) {
            hasLocation = true;
        }
    });
    var locationsStepOk = !isUnifiedLocationsRequired() || hasLocation;

    return [
        detailsFilled,
        locationsStepOk,
        commercialsFilled,
        $form.find('[name="reminder_days[]"]:checked').length > 0,
        document.querySelectorAll('#amc-escalation-container .cc-escalation-level').length > 0,
        !!$form.find('[name="gate_entry"]:checked').val()
    ];
}

function refreshAmcWizardSidebar(activeIndex) {
    if (typeof activeIndex === 'number' && activeIndex >= 0) {
        amcWizardCurrentStep = activeIndex;
    }
    var $page = $('#unified-contract-page .cc-wizard-page');
    var isAmc = getContractPageMode() === 'amc';
    var $steps = $page.find('.cc-wizard-steps-list .cc-wizard-step').filter(function () {
        return isAmc || !$(this).hasClass('cc-amc-only');
    });
    if (!$steps.length) {
        return;
    }
    var filled = getAmcStepDataFilled();
    $steps.each(function (i) {
        var $step = $(this);
        $step.toggleClass('active', i === amcWizardCurrentStep);
        $step.toggleClass('completed', !!filled[i]);
    });
}

function initAmcWizardNav() {
    var $page = $('#unified-contract-page .cc-wizard-page');
    if (!$page.length) {
        return;
    }

    var $steps = $page.find('.cc-wizard-steps-list .cc-wizard-step');
    var isScrolling = false;

    function getVisibleSteps() {
        var isAmc = getContractPageMode() === 'amc';
        return $steps.filter(function () {
            return isAmc || !$(this).hasClass('cc-amc-only');
        });
    }

    function getStepTargets() {
        return getWizardStepTargets();
    }

    function setActiveStep(index, doScroll) {
        var stepTargets = getStepTargets();
        if (index < 0 || index >= stepTargets.length) {
            return;
        }
        refreshAmcWizardSidebar(index);
        var commercialsTarget = '#cc-step-3';
        if (stepTargets[index] === commercialsTarget || $page.find(stepTargets[index]).attr('id') === 'cc-step-3') {
            buildBillingSchedulePreview();
        }
        if (doScroll) {
            var $target = $page.find(stepTargets[index]);
            var $main = $page.find('.cc-wizard-main');
            if ($target.length) {
                isScrolling = true;
                if ($main.length && $main[0].scrollHeight > $main[0].clientHeight + 5) {
                    var targetTop = $target.offset().top - $main.offset().top + $main.scrollTop();
                    $main.animate({
                        scrollTop: Math.max(0, targetTop - 20)
                    }, 400, function () {
                        window.setTimeout(function () {
                            isScrolling = false;
                        }, 100);
                    });
                } else {
                    $('html, body').animate({
                        scrollTop: $target.offset().top - 90
                    }, 400, function () {
                        window.setTimeout(function () {
                            isScrolling = false;
                        }, 100);
                    });
                }
            }
        }
    }

    function getScrollTop() {
        var $main = $page.find('.cc-wizard-main');
        if ($main.length && $main[0].scrollHeight > $main[0].clientHeight + 5) {
            return $main.scrollTop();
        }
        return $(window).scrollTop();
    }

    function getActiveStepFromScroll() {
        var active = 0;
        var stepTargets = getStepTargets();
        var $scrollRoot = $page.find('.cc-wizard-main');
        var useMain = $scrollRoot.length && $scrollRoot[0].scrollHeight > $scrollRoot[0].clientHeight + 5;
        var comparePos = useMain ? ($scrollRoot.scrollTop() + 120) : (getScrollTop() + 120);

        for (var i = 0; i < stepTargets.length; i++) {
            var $el = $page.find(stepTargets[i]);
            if (!$el.length) {
                continue;
            }
            var top = useMain ? $el.position().top : $el.offset().top;
            if (top <= comparePos) {
                active = i;
            }
        }
        return active;
    }

    getVisibleSteps().on('click', function (e) {
        e.preventDefault();
        var idx = getVisibleSteps().index(this);
        setActiveStep(idx, true);
    });

    function onWizardScroll() {
        if (isScrolling || !$page.length) {
            return;
        }
        var step = getActiveStepFromScroll();
        if (step !== amcWizardCurrentStep) {
            setActiveStep(step, false);
        } else {
            refreshAmcWizardSidebar();
        }
        var stepTargets = getStepTargets();
        if (stepTargets[step] === '#cc-step-3' || ($page.find(stepTargets[step]).attr('id') === 'cc-step-3')) {
            buildBillingSchedulePreview();
        }
    }

    $(window).on('scroll.ccWizard', onWizardScroll);
    $page.find('.cc-wizard-main').on('scroll.ccWizard', onWizardScroll);
    $page.on('input change', '#amcContractForm input, #amcContractForm textarea, #amcContractForm select', function () {
        refreshAmcWizardSidebar();
    });

    $page.find('.cc-upload-zone').on('click', function () {
        $page.find('#amc_contract_document').trigger('click');
    });
    $page.find('#amc_contract_document').on('change', function () {
        handleAmcContractDocumentSelect(this);
    });
    /* Remove disabled — re-enable when delete-on-save is implemented
    $page.on('click', '.amc-remove-additional-document-btn', function () {
        var fileName = $(this).data('filename');
        if (fileName) {
            removeAmcAdditionalDocument(fileName);
        }
    });
    */
    initAmcContractDocuments();
    bindAmcSubmitButtons();
    $page.find('#amcContractForm').on('submit', function (e) {
        e.preventDefault();
    });
    if ($.fn.select2) {
        $page.find('.js-example-basic-single').select2({ width: '100%' });
    }
    setActiveStep(0, false);
    window.setTimeout(function () {
        refreshAmcWizardSidebar(0);
    }, 300);
}

function initAmcPartiesAjax() {
    var $root = $('#unified-contract-page');
    $root.find('input[name="party_type"]').on('change', function () {
        toggleAmcPartyTypeUi({ allowEmployeePreselect: false });
        loadAmcPartiesAjax(null, true);
    });
    toggleAmcPartyTypeUi({ allowEmployeePreselect: true });
    loadAmcPartiesAjax(null, false);
    initAmcInhouseDepartmentEmployees();
}

function toggleAmcPartyTypeUi(options) {
    options = options || {};
    var allowEmployeePreselect = options.allowEmployeePreselect === true;
    var isInhouse = document.getElementById('amc_inhouse') && document.getElementById('amc_inhouse').checked;
    var isVendor = document.getElementById('amc_vendor') && document.getElementById('amc_vendor').checked;
    var partyBox = document.getElementById('amc_party_name_box');
    var empBox = document.getElementById('amc_default_employee_box');
    var connectBox = document.getElementById('amc_customer_connect_box');
    var serviceModeInput = document.getElementById('amc_service_mode');
    if (serviceModeInput) {
        serviceModeInput.value = isInhouse ? 'inhouse' : 'vendor';
    }
    if (partyBox) {
        partyBox.style.display = isInhouse ? 'none' : '';
    }
    if (empBox) {
        empBox.style.display = isInhouse ? '' : 'none';
    }
    if (connectBox) {
        var isAmcContract = getContractPageMode() === 'amc';
        connectBox.style.display = (isAmcContract && isVendor) ? '' : 'none';
        if (!isAmcContract || !isVendor) {
            var connectSelect = document.getElementById('amc_customer_connect_id');
            if (connectSelect) {
                connectSelect.value = '';
                if (jQuery(connectSelect).hasClass('select2-hidden-accessible')) {
                    jQuery(connectSelect).val('').trigger('change');
                }
            }
        }
    }
    if (isInhouse) {
        refreshAmcInhouseEmployees(allowEmployeePreselect);
    } else {
        clearAmcInhouseEmployeeSelect();
    }
}

function getAmcContractDepartmentId() {
    var deptSelect = document.getElementById('amc_contract_department_id');
    if (!deptSelect) {
        return '';
    }
    var $dept = jQuery(deptSelect);
    var val = $dept.val();
    if (Array.isArray(val)) {
        val = val[0] || '';
    }
    return $.trim(val || deptSelect.value || '');
}

function clearAmcInhouseEmployeeSelect() {
    var empSelect = document.getElementById('amc_default_employee_id');
    if (!empSelect) {
        return;
    }
    var $emp = jQuery(empSelect);
    empSelect.innerHTML = '<option value="">Select Employee (optional)</option>';
    if ($emp.hasClass('select2-hidden-accessible')) {
        $emp.select2('destroy');
    }
    if (jQuery.fn.select2) {
        $emp.select2({ width: '100%' });
    }
    $emp.val('').trigger('change');
}

function refreshAmcInhouseEmployees(allowPreselect) {
    var deptId = getAmcContractDepartmentId();
    var empSelect = document.getElementById('amc_default_employee_id');
    if (!empSelect) {
        return;
    }
    var preselect = null;
    if (allowPreselect && empSelect.dataset.selected) {
        preselect = empSelect.dataset.selected;
    }
    if (!deptId) {
        clearAmcInhouseEmployeeSelect();
        return;
    }
    loadAmcEmployeesForDepartment(deptId, preselect);
}

function initAmcInhouseDepartmentEmployees() {
    var $root = jQuery('#unified-contract-page');
    if (!$root.length) {
        return;
    }
    if ($root.data('amcInhouseDeptEmpBound')) {
        return;
    }
    $root.data('amcInhouseDeptEmpBound', true);
    $root.on('change.amcInhouseDept', '#amc_contract_department_id', function () {
        if (document.getElementById('amc_inhouse') && document.getElementById('amc_inhouse').checked) {
            refreshAmcInhouseEmployees(false);
        }
    });
}

function loadAmcEmployeesForDepartment(departmentId, preselectId) {
    var empSelect = document.getElementById('amc_default_employee_id');
    if (!empSelect) {
        return;
    }
    var $emp = jQuery(empSelect);
    if (!departmentId) {
        empSelect.innerHTML = '<option value="">Select Employee (optional)</option>';
        if ($emp.hasClass('select2-hidden-accessible')) {
            $emp.val('').trigger('change');
        }
        return;
    }
    empSelect.innerHTML = '<option value="">Loading...</option>';
    if ($emp.hasClass('select2-hidden-accessible')) {
        $emp.select2('destroy');
    }
    jQuery.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: { page: 'getselectedlist', id: departmentId, field: 'department_id', table_name: 'employees' },
        success: function (data) {
            var rows = [];
            try {
                if (typeof data === 'string' && data.trim() !== '') {
                    rows = JSON.parse(data);
                } else if (Array.isArray(data)) {
                    rows = data;
                } else if (data && Array.isArray(data.result)) {
                    rows = data.result;
                }
            } catch (e) {
                rows = [];
            }
            empSelect.innerHTML = '<option value="">Select Employee (optional)</option>';
            if (rows.length > 0) {
                rows.forEach(function (row) {
                    var option = document.createElement('option');
                    option.value = row.id;
                    var label = row.display_name || '';
                    if (row.emp_code) {
                        label = row.emp_code + ' - ' + label;
                    }
                    option.textContent = label || ('Employee ' + row.id);
                    empSelect.appendChild(option);
                });
            }
            if (jQuery.fn.select2) {
                $emp.select2({ width: '100%' });
            }
            if (preselectId) {
                $emp.val(String(preselectId)).trigger('change');
                delete empSelect.dataset.selected;
            }
        },
        error: function () {
            empSelect.innerHTML = '<option value="">Error loading employees</option>';
            if (jQuery.fn.select2) {
                $emp.select2({ width: '100%' });
            }
        }
    });
}

function initPartyDetailsPreview() {
    var partySelect = document.getElementById('amc_party_id');
    if (!partySelect) {
        return;
    }
    function updatePartyDetailsPreview() {
        var previewContainer = document.getElementById('party-details-preview');
        if (!previewContainer) {
            return;
        }
        var selectedOpt = partySelect.options[partySelect.selectedIndex];
        if (!selectedOpt || !selectedOpt.value) {
            previewContainer.classList.add('d-none');
            return;
        }
        var nameEl = document.getElementById('preview-party-name');
        var emailEl = document.getElementById('preview-party-email');
        var phoneEl = document.getElementById('preview-party-phone');
        var bpEl = document.getElementById('preview-bp-code');
        var billingEl = document.getElementById('preview-billing-address');
        var shippingEl = document.getElementById('preview-shipping-address');
        if (nameEl) {
            nameEl.textContent = selectedOpt.dataset.name || '—';
        }
        if (emailEl) {
            emailEl.textContent = selectedOpt.dataset.email || '—';
        }
        if (phoneEl) {
            phoneEl.textContent = selectedOpt.dataset.phone || '—';
        }
        if (bpEl) {
            bpEl.textContent = selectedOpt.dataset.bpCode || '—';
        }
        if (billingEl) {
            billingEl.textContent = selectedOpt.dataset.billingAddress || '—';
        }
        if (shippingEl) {
            shippingEl.textContent = selectedOpt.dataset.shippingAddress || '—';
        }
        previewContainer.classList.remove('d-none');
    }
    $(partySelect).off('change.partyPreview').on('change.partyPreview', updatePartyDetailsPreview);
    updatePartyDetailsPreview();
}

function loadAmcPartiesAjax(preselectId, forceReload) {
    var partyType = document.getElementById('amc_customer')?.checked ? 'customer' : (document.getElementById('amc_vendor')?.checked ? 'vendor' : null);
    if (document.getElementById('amc_inhouse')?.checked) {
        return;
    }
    if (!partyType) {
        return;
    }
    var partySelect = document.getElementById('amc_party_id');
    if (!partySelect) {
        return;
    }
    var preselectedId = preselectId ? String(preselectId) : (partySelect.dataset.selected ? String(partySelect.dataset.selected) : '');
    var existingCount = partySelect.options.length;
    if (!forceReload && existingCount > 1) {
        if (preselectedId) {
            partySelect.value = preselectedId;
            delete partySelect.dataset.selected;
            if ($.fn.select2) {
                $(partySelect).trigger('change.select2');
            }
        }
        return;
    }
    if ($.fn.select2 && $(partySelect).hasClass('select2-hidden-accessible')) {
        $(partySelect).select2('destroy');
    }
    partySelect.innerHTML = '<option value="">Loading...</option>';
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: { page: 'load_parties', party_type: partyType },
        success: function (data) {
            partySelect.innerHTML = '<option value="">Select a Party</option>';
            var parties = (data && data.result) ? data.result : data;
            if (!Array.isArray(parties)) {
                partySelect.innerHTML = '<option value="">No parties found</option>';
                return;
            }
            parties.forEach(function (party) {
                var option = document.createElement('option');
                option.value = party.id;
                var company = party.company || 'N/A';
                var displayName = party.display_name || 'N/A';
                var email = party.email || 'N/A';
                option.textContent = company + ' | ' + displayName + ' | ' + email;
                option.dataset.name = company;
                option.dataset.email = email;
                option.dataset.phone = party.phone || '—';
                option.dataset.bpCode = party.bp_code || '—';
                option.dataset.billingAddress = party.billing_address || '—';
                option.dataset.shippingAddress = party.shipping_address || '—';
                partySelect.appendChild(option);
            });
            if (preselectedId) {
                var optExists = Array.from(partySelect.options).some(function (opt) {
                    return String(opt.value) === String(preselectedId);
                });
                if (optExists) {
                    partySelect.value = preselectedId;
                }
                delete partySelect.dataset.selected;
            }
            initAmcSelect2(partySelect.parentNode || partySelect);
            if ($.fn.select2) {
                $(partySelect).val(preselectedId || partySelect.value || '').trigger('change');
            }
            $(partySelect).trigger('change.partyPreview');
        },
        error: function () {
            partySelect.innerHTML = '<option value="">Error loading parties</option>';
        }
    });
}

function bindAmcSubmitButtons() {
    var $page = $('#unified-contract-page');
    if (!$page.length) {
        return;
    }
    $page.off('click.amcSubmit', '.cc-btn-submit').on('click.amcSubmit', '.cc-btn-submit', function (e) {
        e.preventDefault();
        e.stopPropagation();
        if (isUnifiedRenewMode()) {
            if (getContractPageMode() === 'amc') {
                submitAmcContract();
            } else {
                submitStandardContract();
            }
            return;
        }
        // if (isUnifiedEditMode()) {
        //     showUnifiedEditOptions();
        //     return;
        // }
        if (getContractPageMode() === 'amc') {
            submitAmcContract();
        } else {
            submitStandardContract();
        }
    });
}

function validateStandardContractForm() {
    var $form = $('#unified-contract-page #amcContractForm');
    var contractName = $.trim($form.find('[name="contract_name"]').val() || '');
    if (!contractName) {
        amcNotifyWarning('Contract name is required.');
        return false;
    }

    var startDate = $.trim($form.find('[name="start_date"]').val() || '');
    var endDate = $.trim($form.find('[name="end_date"]').val() || '');
    if (startDate === '01-01-1970') {
        startDate = '';
    }
    if (endDate === '01-01-1970') {
        endDate = '';
    }
    if (!startDate || !endDate) {
        amcNotifyWarning('Effective and expiration dates are required.');
        return false;
    }

    var partyId = $.trim($form.find('input[name="party_id"]').val() || '');
    if (!partyId) {
        var partySelect = document.getElementById('amc_party_id');
        partyId = partySelect ? $.trim(partySelect.value || '') : '';
    }
    var isInhouse = document.getElementById('amc_inhouse') && document.getElementById('amc_inhouse').checked;
    if (!isInhouse && !partyId) {
        amcNotifyWarning('Party is required.');
        return false;
    }
    if (!getAmcContractDepartmentId()) {
        amcNotifyWarning('Department is required.');
        return false;
    }

    var amount = $.trim($form.find('[name="contract_amount"]').val() || '');
    if (!amount) {
        amcNotifyWarning('Contract amount is required.');
        return false;
    }
    if (!validateContractAmountAndBilling($form)) {
        return false;
    }

    if (isUnifiedLocationsRequired() && !getUnifiedLocationsMultiselectValues().length) {
        amcNotifyWarning('Please select at least one location.');
        return false;
    }

    return true;
}

function validateAmcContractForm() {
    var $form = $('#unified-contract-page #amcContractForm');
    var contractName = $.trim($form.find('[name="contract_name"]').val() || '');
    if (!contractName) {
        amcNotifyWarning('Contract name is required.');
        return false;
    }

    var startDate = $.trim($form.find('[name="start_date"]').val() || '');
    var endDate = $.trim($form.find('[name="end_date"]').val() || '');
    if (startDate === '01-01-1970') {
        startDate = '';
    }
    if (endDate === '01-01-1970') {
        endDate = '';
    }
    if (!startDate || !endDate) {
        amcNotifyWarning('Effective and expiration dates are required.');
        return false;
    }

    var partyId = $.trim($form.find('input[name="party_id"]').val() || '');
    if (!partyId) {
        var partySelect = document.getElementById('amc_party_id');
        partyId = partySelect ? $.trim(partySelect.value || '') : '';
    }
    var isInhouse = document.getElementById('amc_inhouse') && document.getElementById('amc_inhouse').checked;
    if (!isInhouse && !partyId) {
        amcNotifyWarning('Party is required.');
        return false;
    }
    var deptId = getAmcContractDepartmentId();
    if (!deptId) {
        amcNotifyWarning('Department is required.');
        return false;
    }

    var amount = $.trim($form.find('[name="contract_amount"]').val() || '');
    if (!amount) {
        amcNotifyWarning('Contract amount is required.');
        return false;
    }
    if (!validateContractAmountAndBilling($form)) {
        return false;
    }

    updateAmcLocationFieldNames();
    if (isUnifiedLocationsRequired()) {
        var locationBlocks = document.querySelectorAll('#amc_locations_container .amc-location-block');
        if (!locationBlocks.length) {
            amcNotifyWarning('At least one AMC location is required.');
            return false;
        }

        var hasValidLocation = false;
        locationBlocks.forEach(function (block) {
            var locSelect = block.querySelector('.amc-location-select');
            var locVal = '';
            if (locSelect) {
                locVal = $.trim(locSelect.value || '');
                if (!locVal && locSelect.dataset && locSelect.dataset.selected) {
                    locVal = $.trim(locSelect.dataset.selected || '');
                }
                if (!locVal && window.jQuery && jQuery(locSelect).data('select2')) {
                    locVal = $.trim(jQuery(locSelect).val() || '');
                }
            }
            if (locVal) {
                hasValidLocation = true;
            }
        });
        if (!hasValidLocation) {
            amcNotifyWarning('Please select at least one location.');
            return false;
        }
    }

    return true;
}

function syncAmcSelect2Values() {
    if (!$.fn.select2) {
        return;
    }
    $('#unified-contract-page .js-example-basic-single, #unified-contract-page .js-example-basic-multiple').each(function () {
        var $el = $(this);
        if (!$el.data('select2')) {
            return;
        }
        var val = $el.val();
        if (val === null || val === undefined) {
            return;
        }
        if (this.multiple && Array.isArray(val)) {
            Array.from(this.options).forEach(function (opt) {
                opt.selected = val.indexOf(opt.value) !== -1 || val.indexOf(String(opt.value)) !== -1;
            });
        } else {
            this.value = val;
        }
    });
    syncUnifiedLocationsMultiselectToForm();
}

function isAmcRenewMode() {
    var el = document.getElementById('is-renew-mode');
    return el && el.textContent.trim() === 'true';
}

function syncAmcRenewalServiceDatesFromContract() {
    if (!isAmcRenewMode() || getContractPageMode() !== 'amc') {
        return;
    }
    var $root = $('#unified-contract-page');
    var startIso = parseUnifiedDateToYmd($.trim($root.find('.amc-start-datepicker').first().val() || ''));
    var endIso = parseUnifiedDateToYmd($.trim($root.find('.amc-end-datepicker').first().val() || ''));
    if (!startIso || !endIso) {
        return;
    }
    $root.find('input[name^="amc_loc"][name$="[service_start_date]"]').val(startIso);
    $root.find('input[name^="amc_loc"][name$="[service_end_date]"]').val(endIso);
    $root.find('input[name^="amc_asset"][name$="[service_start_date]"]').val(startIso);
    $root.find('input[name^="amc_asset"][name$="[service_end_date]"]').val(endIso);
    updateAmcSummary();
}

function initAmcRenewalServiceDateSyncListeners() {
    if (!isAmcRenewMode() || getContractPageMode() !== 'amc') {
        return;
    }
    var $page = $('#unified-contract-page');
    $page.off(
        'change.renewalServiceDates input.renewalServiceDates changeDate.renewalServiceDates',
        '.amc-end-datepicker, [name="end_date"]'
    ).on(
        'change.renewalServiceDates input.renewalServiceDates changeDate.renewalServiceDates',
        '.amc-end-datepicker, [name="end_date"]',
        function () {
            syncAmcRenewalServiceDatesFromContract();
            if (typeof renderRenewalAmountHistoryPanel === 'function') {
                renderRenewalAmountHistoryPanel();
            }
        }
    );
}

function initializeAmcRenewalDates() {
    if (!isAmcRenewMode()) {
        return;
    }
    var $root = $('#unified-contract-page');
    var $startInput = $root.find('.amc-start-datepicker').first();
    var $endInput = $root.find('.amc-end-datepicker').first();

    if ($startInput.attr('readonly') && $.trim($startInput.val() || '') !== '') {
        syncAmcRenewalServiceDatesFromContract();
        if (typeof renderRenewalAmountHistoryPanel === 'function') {
            renderRenewalAmountHistoryPanel();
        }
        return;
    }

    var currentEnd = $.trim($endInput.val() || '');
    if (!currentEnd) {
        return;
    }
    var parts = currentEnd.split('-');
    if (parts.length !== 3) {
        return;
    }
    var endDate = new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10));
    if (isNaN(endDate.getTime())) {
        return;
    }
    var newStart = new Date(endDate);
    newStart.setDate(newStart.getDate() + 1);
    var newEnd = new Date(newStart);
    newEnd.setFullYear(newEnd.getFullYear() + 1);

    function formatDdMmYyyy(date) {
        var day = String(date.getDate()).padStart(2, '0');
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var year = date.getFullYear();
        return day + '-' + month + '-' + year;
    }
    function formatIsoDate(date) {
        var day = String(date.getDate()).padStart(2, '0');
        var month = String(date.getMonth() + 1).padStart(2, '0');
        return date.getFullYear() + '-' + month + '-' + day;
    }

    var startStr = formatDdMmYyyy(newStart);
    var endStr = formatDdMmYyyy(newEnd);
    var startIso = formatIsoDate(newStart);
    var endIso = formatIsoDate(newEnd);

    $root.find('.amc-start-datepicker').val(startStr);
    $endInput.val(endStr);
    $root.find('input[name^="amc_loc"][name$="[service_start_date]"]').val(startIso);
    $root.find('input[name^="amc_loc"][name$="[service_end_date]"]').val(endIso);
    $root.find('input[name^="amc_asset"][name$="[service_start_date]"]').val(startIso);
    $root.find('input[name^="amc_asset"][name$="[service_end_date]"]').val(endIso);
    updateAmcSummary();
    if (typeof renderRenewalAmountHistoryPanel === 'function') {
        renderRenewalAmountHistoryPanel();
    }
}

function submitStandardContract(skipModal) {
    syncAmcSelect2Values();
    syncUnifiedLocationsMultiselectToForm();
    reindexMilestoneRows();
    if (!skipModal && !validateStandardContractForm()) {
        return;
    }

    var form = document.getElementById('amcContractForm');
    if (!form) {
        return;
    }

    var actionField = form.querySelector('#unified_contract_action') || form.querySelector('[name="action"]');
    var isAmcField = form.querySelector('#unified_is_amc_contract') || form.querySelector('[name="is_amc_contract"]');
    if (actionField) {
        actionField.value = isUnifiedRenewMode() ? 'renew_contract' : 'submit_contract';
    }
    if (isAmcField) {
        isAmcField.value = 'N';
    }

    if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype.submit) {
        HTMLFormElement.prototype.submit.call(form);
    } else {
        form.submit();
    }
}

function syncAmcContractDatesBeforeSubmit() {
    var form = document.getElementById('amcContractForm');
    if (!form) {
        return;
    }
    var startYmd = parseUnifiedDateToYmd(form.querySelector('[name="start_date"]')?.value || '');
    var endYmd = parseUnifiedDateToYmd(form.querySelector('[name="end_date"]')?.value || '');
    if (!startYmd || !endYmd) {
        return;
    }
    document.querySelectorAll('#amc_locations_container .amc-location-block').forEach(function (block) {
        var locStart = block.querySelector('.amc-loc-service-start');
        var locEnd = block.querySelector('.amc-loc-service-end');
        if (locStart && !locStart.value) {
            locStart.value = startYmd;
        }
        if (locEnd && !locEnd.value) {
            locEnd.value = endYmd;
        }
        syncAmcLocationDatesToAssets(block);
    });
}

function submitAmcContract(skipModal) {
    syncAmcSelect2Values();
    reindexMilestoneRows();
    updateAmcLocationFieldNames();
    syncAmcContractDatesBeforeSubmit();
    if (!skipModal && !validateAmcContractForm()) {
        return;
    }
    updateAmcSummary();

    var form = document.getElementById('amcContractForm');
    if (!form) {
        return;
    }

    var isInhouse = document.getElementById('amc_inhouse') && document.getElementById('amc_inhouse').checked;
    if (isInhouse) {
        var partyIdField = form.querySelector('[name="party_id"]');
        if (partyIdField) {
            partyIdField.value = '0';
        }
        var inhouseRadio = document.getElementById('amc_inhouse');
        if (inhouseRadio) {
            inhouseRadio.checked = true;
        }
        var serviceModeInput = document.getElementById('amc_service_mode');
        if (serviceModeInput) {
            serviceModeInput.value = 'inhouse';
        }
    }

    var actionField = form.querySelector('#unified_contract_action') || form.querySelector('[name="action"]');
    var isAmcField = form.querySelector('#unified_is_amc_contract') || form.querySelector('[name="is_amc_contract"]');
    if (actionField) {
        actionField.value = isAmcRenewMode() ? 'renew_amc_contract' : 'submit_amc_contract';
    }
    if (isAmcField) {
        isAmcField.value = 'Y';
    }

    if (typeof fetch === 'function') {
        var formData = new FormData(form);
        fetch(window.location.href, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            redirect: 'follow'
        }).then(function (response) {
            return response.text().then(function (html) {
                return { html: html, url: response.url || '' };
            });
        }).then(function (result) {
            if (result.url.indexOf('COM_LIST_CONTRACTS') !== -1) {
                window.location.replace(result.url);
                return;
            }
            if (/Failed to save AMC contract|Unexpected API response|Empty API response|error occurred while renewing|Unexpected API response during renewal|Empty API response from server during renewal|Contract name is required|At least one AMC location|Party is required|Contract amount is required|Contract must be approved|Contract id is required for renewal|Department is required/i.test(result.html)) {
                document.open();
                document.write(result.html);
                document.close();
                return;
            }
            document.open();
            document.write(result.html);
            document.close();
        }).catch(function () {
            if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype.submit) {
                HTMLFormElement.prototype.submit.call(form);
            } else {
                form.submit();
            }
        });
        return;
    }

    if (typeof HTMLFormElement !== 'undefined' && HTMLFormElement.prototype.submit) {
        HTMLFormElement.prototype.submit.call(form);
    } else {
        form.submit();
    }
}

function updateAmcSummary() {
    var summaryEl = document.getElementById('amc-summary-content');
    if (!summaryEl) {
        return;
    }
    var $form = $('#unified-contract-page #amcContractForm');
    var contractName = $.trim($form.find('[name="contract_name"]').val() || '') || '—';
    var partySelect = document.getElementById('amc_party_id');
    var partyLabel = '—';
    if (partySelect && partySelect.selectedIndex > 0) {
        partyLabel = partySelect.options[partySelect.selectedIndex].text.trim();
    }
    var partyType = document.getElementById('amc_inhouse')?.checked ? 'In-house' : (document.getElementById('amc_customer')?.checked ? 'Customer' : 'Vendor');
    if (document.getElementById('amc_inhouse')?.checked) {
        var deptSelect = document.getElementById('amc_contract_department_id');
        if (deptSelect && deptSelect.selectedIndex > 0) {
            partyLabel = 'In-house — ' + deptSelect.options[deptSelect.selectedIndex].text.trim();
        } else {
            partyLabel = 'In-house';
        }
    }
    var startDate = $.trim($form.find('[name="start_date"]').val() || '') || '—';
    var endDate = $.trim($form.find('[name="end_date"]').val() || '') || '—';
    var amount = $.trim($form.find('[name="contract_amount"]').val() || '') || '—';
    var paymentMode = $form.find('input[name="amc_payment_mode"]:checked').val() || 'billing_cycle';
    var paymentLabel = paymentMode === 'milestones' ? 'Milestones' : 'Billing Cycle';

    var locationLines = [];
    document.querySelectorAll('#amc_locations_container .amc-location-block').forEach(function (block, idx) {
        var locSelect = block.querySelector('.amc-location-select');
        var locName = '—';
        if (locSelect && locSelect.selectedIndex > 0) {
            locName = locSelect.options[locSelect.selectedIndex].text.trim();
        }
        var locStart = block.querySelector('.amc-loc-service-start')?.value || '—';
        var locEnd = block.querySelector('.amc-loc-service-end')?.value || '—';
        var assetRows = block.querySelectorAll('.amc-asset-row');
        var assetParts = [];
        assetRows.forEach(function (row) {
            var assetSelect = row.querySelector('.amc-asset-select');
            var freqSelect = row.querySelector('.amc-service-frequency-select');
            var assetLabel = '—';
            if (assetSelect && assetSelect.selectedIndex > 0) {
                assetLabel = assetSelect.options[assetSelect.selectedIndex].text.trim();
            }
            var freqLabel = '';
            if (freqSelect && freqSelect.selectedIndex > 0) {
                freqLabel = ' (' + freqSelect.options[freqSelect.selectedIndex].text.trim() + ')';
            }
            assetParts.push(assetLabel + freqLabel);
        });
        locationLines.push(
            '<li><strong>Location ' + (idx + 1) + ':</strong> ' + locName +
            ' <span class="text-muted">(' + locStart + ' to ' + locEnd + ')</span>' +
            (assetParts.length ? '<br><span class="ms-3">Assets: ' + assetParts.join(', ') + '</span>' : '') +
            '</li>'
        );
    });

    var html = '<dl class="row mb-2 amc-summary-dl">' +
        '<dt class="col-sm-4">Contract Name</dt><dd class="col-sm-8">' + escapeHtml(contractName) + '</dd>' +
        '<dt class="col-sm-4">Party</dt><dd class="col-sm-8">' + escapeHtml(partyType + ' — ' + partyLabel) + '</dd>' +
        '<dt class="col-sm-4">Contract Period</dt><dd class="col-sm-8">' + escapeHtml(startDate + ' to ' + endDate) + '</dd>' +
        '<dt class="col-sm-4">Amount</dt><dd class="col-sm-8">' + escapeHtml(amount) + '</dd>' +
        '<dt class="col-sm-4">Payment Plan</dt><dd class="col-sm-8">' + escapeHtml(paymentLabel) + '</dd>' +
        '</dl>';
    if (locationLines.length) {
        html += '<p class="mb-1 fw-semibold">Locations &amp; Assets</p><ul class="mb-0 ps-3">' + locationLines.join('') + '</ul>';
    } else {
        html += '<p class="mb-0 text-muted">No locations added yet.</p>';
    }
    var escCount = document.querySelectorAll('#amc-escalation-container .cc-escalation-level').length;
    html += '<p class="mb-0 mt-2"><span class="fw-semibold">Escalation levels:</span> ' + escCount + '</p>';
    summaryEl.innerHTML = html;
}

function escapeHtml(text) {
    var div = document.createElement('div');
    div.textContent = text == null ? '' : String(text);
    return div.innerHTML;
}

function initAmcEscalations() {
    var $root = $('#unified-contract-page');
    var container = document.getElementById('amc-escalation-container');
    if (!container) {
        return;
    }
    $root.on('click', '#amc-add-escalation-btn', function () {
        addAmcEscalationLevel({
            condition: 'not_started',
            hours: 4,
            notify_role: 'service_manager'
        });
    });
    container.addEventListener('click', function (e) {
        var removeBtn = e.target.closest('.amc-remove-escalation');
        if (!removeBtn) {
            return;
        }
        var levels = container.querySelectorAll('.cc-escalation-level');
        if (levels.length <= 1) {
            amcNotifyWarning('At least one escalation level is required.');
            return;
        }
        removeBtn.closest('.cc-escalation-level').remove();
        updateAmcEscalationFieldNames();
        updateAmcSummary();
        refreshAmcWizardSidebar();
    });
    container.addEventListener('change', function (e) {
        if (e.target.classList.contains('amc-esc-condition') || e.target.classList.contains('amc-esc-hours') || e.target.classList.contains('amc-esc-notify')) {
            updateAmcSummary();
        }
    });
    updateAmcEscalationFieldNames();
}

function addAmcEscalationLevel(data) {
    var template = document.getElementById('amc-escalation-level-template');
    var container = document.getElementById('amc-escalation-container');
    if (!template || !container) {
        return;
    }
    var clone = template.content.cloneNode(true);
    container.appendChild(clone);
    var levelEl = container.lastElementChild;
    var conditionSelect = levelEl.querySelector('.amc-esc-condition');
    var hoursInput = levelEl.querySelector('.amc-esc-hours');
    var notifySelect = levelEl.querySelector('.amc-esc-notify');
    if (conditionSelect && data && data.condition) {
        conditionSelect.value = data.condition;
    }
    if (hoursInput && data && data.hours) {
        hoursInput.value = data.hours;
    }
    if (notifySelect && data && data.notify_role) {
        notifySelect.value = data.notify_role;
    }
    initAmcSelect2(levelEl);
    updateAmcEscalationFieldNames();
    updateAmcSummary();
    refreshAmcWizardSidebar();
}

function updateAmcEscalationFieldNames() {
    var container = document.getElementById('amc-escalation-container');
    if (!container) {
        return;
    }
    var levels = container.querySelectorAll('.cc-escalation-level');
    levels.forEach(function (levelEl, idx) {
        levelEl.setAttribute('data-esc-idx', idx);
        var badge = levelEl.querySelector('.level-badge');
        if (badge) {
            badge.textContent = 'Level ' + (idx + 1);
        }
        var conditionSelect = levelEl.querySelector('.amc-esc-condition');
        var hiddenCondition = levelEl.querySelector('input[type="hidden"][name*="[condition]"]');
        if (conditionSelect) {
            conditionSelect.name = 'amc_escalation_config[' + idx + '][condition]';
        } else if (hiddenCondition) {
            hiddenCondition.name = 'amc_escalation_config[' + idx + '][condition]';
        }
        var hoursInput = levelEl.querySelector('.amc-esc-hours');
        if (hoursInput) {
            hoursInput.name = 'amc_escalation_config[' + idx + '][hours]';
        }
        var notifySelect = levelEl.querySelector('.amc-esc-notify');
        if (notifySelect) {
            notifySelect.name = 'amc_escalation_config[' + idx + '][notify_role]';
        }
    });
}

function populateAmcEditFormIfNeeded() {
    var editFlag = document.getElementById('amc-edit-mode-flag');
    if (!editFlag || editFlag.textContent.trim() !== 'true') {
        return;
    }
    var dataEl = document.getElementById('amc-edit-data');
    if (!dataEl || !dataEl.textContent.trim()) {
        return;
    }
    try {
        populateAmcEditForm(JSON.parse(dataEl.textContent));
    } catch (e) {
        console.error('AMC edit data parse failed', e);
    }
}

function applyAmcEditAssetSchedule(row, assetRow) {
    if (!row || !assetRow) {
        return;
    }
    var visitsInput = row.querySelector('.amc-visits-per-interval');
    if (visitsInput && assetRow.visits_per_interval) {
        visitsInput.value = assetRow.visits_per_interval;
    }
    rebuildAmcSlotFields(row);
    if (Array.isArray(assetRow.service_slots) && assetRow.service_slots.length) {
        var slotsRow = getAmcAssetSlotsRow(row);
        if (slotsRow) {
            var slotFields = slotsRow.querySelectorAll('.amc-slot-field');
            assetRow.service_slots.forEach(function (slotVal, slotIdx) {
                if (slotFields[slotIdx]) {
                    slotFields[slotIdx].value = slotVal;
                }
            });
        }
    }
}

function populateAmcEditForm(editData) {
    if (!editData || !Array.isArray(editData.locations) || !editData.locations.length) {
        updateAmcSummary();
        refreshAmcWizardSidebar();
        return;
    }
    var container = document.getElementById('amc_locations_container');
    if (!container) {
        return;
    }
    var editAssetRows = [];
    container.innerHTML = '';
    editData.locations.forEach(function (locRow) {
        addAmcLocation();
        var block = container.lastElementChild;
        if (!block) {
            return;
        }
        var locSelect = block.querySelector('.amc-location-select');
        var locStart = block.querySelector('.amc-loc-service-start');
        var locEnd = block.querySelector('.amc-loc-service-end');
        if (locSelect && locRow.location_id) {
            locSelect.value = String(locRow.location_id);
        }
        if (locStart && locRow.service_start_date) {
            locStart.value = String(locRow.service_start_date).substring(0, 10);
        }
        if (locEnd && locRow.service_end_date) {
            locEnd.value = String(locRow.service_end_date).substring(0, 10);
        }
        if (Array.isArray(locRow.assets)) {
            locRow.assets.forEach(function (assetRow) {
                addAmcAssetRow(block);
                var tbody = block.querySelector('.amc-assets-tbody');
                var assetTr = tbody ? tbody.querySelectorAll('.amc-asset-row') : [];
                var row = assetTr.length ? assetTr[assetTr.length - 1] : null;
                if (!row) {
                    return;
                }
                var assetSelect = row.querySelector('.amc-asset-select');
                var assetName = row.querySelector('.amc-asset-name');
                var freqSelect = row.querySelector('.amc-service-frequency-select');
                var visitsInput = row.querySelector('.amc-visits-per-interval');
                var assetStart = row.querySelector('.amc-asset-service-start');
                var assetEnd = row.querySelector('.amc-asset-service-end');
                if (assetSelect && assetRow.asset_id) {
                    assetSelect.value = String(assetRow.asset_id);
                }
                if (assetName && assetRow.asset_name) {
                    assetName.value = assetRow.asset_name;
                }
                if (freqSelect && assetRow.service_frequency) {
                    freqSelect.value = assetRow.service_frequency;
                }
                if (visitsInput && assetRow.visits_per_interval) {
                    visitsInput.value = assetRow.visits_per_interval;
                }
                if (assetStart && assetRow.service_start_date) {
                    assetStart.value = String(assetRow.service_start_date).substring(0, 10);
                }
                if (assetEnd && assetRow.service_end_date) {
                    assetEnd.value = String(assetRow.service_end_date).substring(0, 10);
                }
                if (assetRow.id) {
                    row.setAttribute('data-contract-amc-asset-id', String(assetRow.id));
                }
                if (assetSelect) {
                    onAmcAssetChange(assetSelect);
                }
                editAssetRows.push({ row: row, assetRow: assetRow });
            });
        }
        initAmcSelect2(block);
    });
    updateAmcLocationFieldNames();
    if ($.fn.select2) {
        $('#unified-contract-page .js-example-basic-single').each(function () {
            $(this).trigger('change.select2');
        });
    }
    editAssetRows.forEach(function (item) {
        applyAmcEditAssetSchedule(item.row, item.assetRow);
    });
    container.querySelectorAll('.amc-location-block').forEach(function (block) {
        syncAmcLocationSelectTracking(block);
    });
    if (isAmcRenewMode()) {
        syncAmcRenewalServiceDatesFromContract();
    }
    updateAmcSummary();
    refreshAmcWizardSidebar();
    // var paymentMode = $('#unified-contract-page input[name="amc_payment_mode"]:checked').val() || 'billing_cycle';
    // if (paymentMode != 'milestones') {
    //     $('#unified-billing-schedule-body').removeAttr('data-server-prefilled');
    //     buildBillingSchedulePreview();
    // }
}

function initAmcPaymentModeToggle() {
    var $root = $('#unified-contract-page');
    function apply() {
        syncCommercialsBlockUi();
    }
    $root.find('input[name="amc_payment_mode"]').on('change', function () {
        $('#unified-billing-schedule-body').removeAttr('data-server-prefilled');
        apply();
    });
    apply();
}

/* ========== AMC Contract UI (frontend only) ========== */
let amcLocationCounter = 0;
let amcAssetsLookup = [];
let amcServiceCalendarInstance = null;

function getBootstrapModal(modalEl) {
    if (!modalEl || typeof bootstrap === 'undefined' || !bootstrap.Modal) {
        return null;
    }
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }
    return modal;
}

function addOneYearToDateValue(dateStr) {
    if (!dateStr) {
        return '';
    }
    const parts = dateStr.split('-');
    if (parts.length !== 3) {
        return '';
    }
    const d = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
    if (isNaN(d.getTime())) {
        return '';
    }
    d.setFullYear(d.getFullYear() + 1);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + day;
}

function applyAmcLocationStartDate(block) {
    const locStart = block.querySelector('.amc-loc-service-start');
    const locEnd = block.querySelector('.amc-loc-service-end');
    if (!locStart || !locEnd || !locStart.value) {
        return;
    }
    locEnd.value = addOneYearToDateValue(locStart.value);
    syncAmcLocationDatesToAssets(block);
}

function initAmcContractUI() {
    const addLocationBtn = document.getElementById('amc-add-location-btn');
    const locationsContainer = document.getElementById('amc_locations_container');
    const assetsDataEl = document.getElementById('amc-assets-data');

    if (!locationsContainer) {
        return;
    }

    if (assetsDataEl) {
        try {
            amcAssetsLookup = JSON.parse(assetsDataEl.textContent) || [];
        } catch (e) {
            amcAssetsLookup = [];
        }
    }

    var editDataEl = document.getElementById('amc-edit-data');
    var hasEditLocations = false;
    if (editDataEl && editDataEl.textContent.trim()) {
        try {
            var editPayload = JSON.parse(editDataEl.textContent);
            hasEditLocations = Array.isArray(editPayload.locations) && editPayload.locations.length > 0;
        } catch (e) {
            hasEditLocations = false;
        }
    }
    if (locationsContainer.querySelectorAll('.amc-location-block').length === 0 && !hasEditLocations) {
        addAmcLocation();
    }

    initAmcDatePickers();

    if (addLocationBtn) {
        addLocationBtn.addEventListener('click', addAmcLocation);
    }

    var $root = $('#unified-contract-page');
    $root.on('input change', '#amcContractForm input, #amcContractForm textarea, #amcContractForm select', updateAmcSummary);
    $root.on('change', '#amc_party_id, .amc-location-select, .amc-asset-select, .amc-service-frequency-select', updateAmcSummary);
    updateAmcSummary();

    locationsContainer.addEventListener('click', function (e) {
        const removeLocBtn = e.target.closest('.amc-remove-location');
        if (removeLocBtn) {
            removeAmcLocation(removeLocBtn);
            return;
        }
        const addAssetBtn = e.target.closest('.amc-add-asset-btn');
        if (addAssetBtn) {
            const block = addAssetBtn.closest('.amc-location-block');
            if (block) {
                addAmcAssetRow(block);
            }
            return;
        }
        const addByCategoryBtn = e.target.closest('.amc-add-by-category-btn');
        if (addByCategoryBtn) {
            const block = addByCategoryBtn.closest('.amc-location-block');
            if (block) {
                addAmcAssetsByCategory(block);
            }
            return;
        }
        const removeAssetBtn = e.target.closest('.amc-remove-asset');
        if (removeAssetBtn) {
            removeAmcAssetRow(removeAssetBtn);
            return;
        }
        const historyBtn = e.target.closest('.amc-view-history');
        if (historyBtn) {
            openAmcAssetHistory(historyBtn);
            return;
        }
        const calendarBtn = e.target.closest('.amc-service-calendar-btn');
        if (calendarBtn) {
            openAmcServiceCalendar(calendarBtn);
        }
    });

    locationsContainer.addEventListener('change', function (e) {
        if (e.target.classList.contains('amc-location-select')) {
            const block = e.target.closest('.amc-location-block');
            if (block) {
                handleAmcLocationSelectChange(block, e.target);
            }
        }
        if (e.target.classList.contains('amc-asset-select')) {
            onAmcAssetChange(e.target);
        }
        if (e.target.classList.contains('amc-loc-service-start')) {
            const block = e.target.closest('.amc-location-block');
            if (block) {
                applyAmcLocationStartDate(block);
            }
        }
        if (e.target.classList.contains('amc-loc-service-end')) {
            const block = e.target.closest('.amc-location-block');
            if (block) {
                syncAmcLocationDatesToAssets(block);
            }
        }
        if (e.target.classList.contains('amc-service-frequency-select')) {
            onAmcServiceFrequencyChange(e.target);
        }
        if (e.target.classList.contains('amc-visits-per-interval')) {
            const row = e.target.closest('.amc-asset-row');
            if (row) {
                rebuildAmcSlotFields(row);
            }
        }
    });

    locationsContainer.addEventListener('input', function (e) {
        if (e.target.classList.contains('amc-visits-per-interval')) {
            const row = e.target.closest('.amc-asset-row');
            if (row) {
                rebuildAmcSlotFields(row);
            }
        }
    });

    const calendarModal = document.getElementById('amcServiceCalendarModal');
    if (calendarModal) {
        calendarModal.addEventListener('hidden.bs.modal', destroyAmcServiceCalendar);
    }
}

function initAmcSelect2(context) {
    if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
        return;
    }
    jQuery(context).find('.js-example-basic-single').each(function () {
        const $el = jQuery(this);
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({ width: '100%' });
    });
    jQuery(context).find('.amc-asset-select').off('select2:select select2:clear change.amcAsset').on('select2:select select2:clear change.amcAsset', function () {
        onAmcAssetChange(this);
    });
    jQuery(context).find('.amc-service-frequency-select').off('select2:select.amcFreq select2:clear.amcFreq change.amcFreq').on('select2:select.amcFreq select2:clear.amcFreq change.amcFreq', function () {
        onAmcServiceFrequencyChange(this);
    });
}

function syncAmcLocationDatesToAssets(locationBlock) {
    if (!locationBlock) {
        return;
    }
    const locStart = locationBlock.querySelector('.amc-loc-service-start');
    const locEnd = locationBlock.querySelector('.amc-loc-service-end');
    const startVal = locStart ? locStart.value : '';
    const endVal = locEnd ? locEnd.value : '';
    locationBlock.querySelectorAll('.amc-asset-row').forEach(function (row) {
        const assetStart = row.querySelector('.amc-asset-service-start');
        const assetEnd = row.querySelector('.amc-asset-service-end');
        if (assetStart) {
            assetStart.value = startVal;
        }
        if (assetEnd) {
            assetEnd.value = endVal;
        }
        const freqSelect = row.querySelector('.amc-service-frequency-select');
        if (freqSelect && normalizeAmcServiceFrequency(freqSelect.value) === 'custom') {
            rebuildAmcSlotFields(row);
        }
    });
}

function updateAmcLocationFieldNames() {
    const blocks = document.querySelectorAll('#amc_locations_container .amc-location-block');
    blocks.forEach(function (block, locIdx) {
        block.setAttribute('data-loc-idx', locIdx);
        const title = block.querySelector('.amc-location-title');
        if (title) {
            title.textContent = 'Location ' + (locIdx + 1);
        }
        const locSelect = block.querySelector('.amc-location-select');
        const locStart = block.querySelector('.amc-loc-service-start');
        const locEnd = block.querySelector('.amc-loc-service-end');
        if (locSelect) locSelect.name = 'amc_loc[' + locIdx + '][location_id]';
        if (locStart) locStart.name = 'amc_loc[' + locIdx + '][service_start_date]';
        if (locEnd) locEnd.name = 'amc_loc[' + locIdx + '][service_end_date]';

        const assetRows = block.querySelectorAll('.amc-asset-row');
        assetRows.forEach(function (row, assetIdx) {
            const assetSelect = row.querySelector('.amc-asset-select');
            const assetName = row.querySelector('.amc-asset-name');
            const freqSelect = row.querySelector('.amc-service-frequency-select');
            const visitsInput = row.querySelector('.amc-visits-per-interval');
            const assetStart = row.querySelector('.amc-asset-service-start');
            const assetEnd = row.querySelector('.amc-asset-service-end');
            const prefix = 'amc_asset[' + locIdx + '][' + assetIdx + ']';
            if (assetSelect) assetSelect.name = prefix + '[asset_id]';
            if (assetName) assetName.name = prefix + '[asset_name]';
            if (freqSelect) freqSelect.name = prefix + '[service_frequency]';
            if (visitsInput) visitsInput.name = prefix + '[visits_per_interval]';
            if (assetStart) assetStart.name = prefix + '[service_start_date]';
            if (assetEnd) assetEnd.name = prefix + '[service_end_date]';

            const slotsRow = getAmcAssetSlotsRow(row);
            if (slotsRow) {
                slotsRow.querySelectorAll('.amc-slot-field').forEach(function (slotInput, slotIdx) {
                    slotInput.name = prefix + '[service_slots][' + slotIdx + ']';
                });
            }
        });
    });
}

function addAmcLocation() {
    const template = document.getElementById('amc-location-template');
    const container = document.getElementById('amc_locations_container');
    if (!template || !container) {
        return;
    }
    amcLocationCounter++;
    const clone = template.content.cloneNode(true);
    container.appendChild(clone);
    const newBlock = container.lastElementChild;
    initAmcSelect2(newBlock);
    bindAmcLocationDateListeners(newBlock);
    updateAmcLocationFieldNames();
    updateAmcSummary();
    refreshAmcWizardSidebar();
}

function bindAmcLocationDateListeners(block) {
    if (!block) {
        return;
    }
    block.querySelectorAll('.amc-location-select').forEach(function (select) {
        select.addEventListener('change', function () {
            handleAmcLocationSelectChange(block, select);
        });
        if (window.jQuery) {
            jQuery(select).off('select2:select.amcLoc select2:clear.amcLoc').on('select2:select.amcLoc select2:clear.amcLoc', function () {
                handleAmcLocationSelectChange(block, select);
            });
        }
    });
    block.querySelectorAll('.amc-loc-service-start').forEach(function (input) {
        input.addEventListener('change', function () {
            applyAmcLocationStartDate(block);
        });
    });
    block.querySelectorAll('.amc-loc-service-end').forEach(function (input) {
        input.addEventListener('change', function () {
            syncAmcLocationDatesToAssets(block);
        });
    });
}

function removeAmcLocation(btn) {
    const block = btn.closest('.amc-location-block');
    const container = document.getElementById('amc_locations_container');
    if (!block || !container) {
        return;
    }
    if (container.querySelectorAll('.amc-location-block').length <= 1) {
        if (typeof tostarnotify === 'function') {
            tostarnotify('top', 'right', 'fa fa-info', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', 'At least one contract location is required when AMC is enabled.', '');
        }
        return;
    }
    block.remove();
    updateAmcLocationFieldNames();
    updateAmcSummary();
    refreshAmcWizardSidebar();
}

function addAmcAssetRow(locationBlock) {
    const locationId = getAmcLocationIdFromBlock(locationBlock);
    if (!locationId) {
        amcNotifyWarning('Select a location before adding assets.');
        return;
    }
    const template = document.getElementById('amc-asset-row-template');
    const tbody = locationBlock.querySelector('.amc-assets-tbody');
    if (!template || !tbody) {
        return;
    }
    const clone = template.content.cloneNode(true);
    tbody.appendChild(clone);
    const assetRows = tbody.querySelectorAll('.amc-asset-row');
    const newRow = assetRows[assetRows.length - 1];
    const newSelect = newRow ? newRow.querySelector('.amc-asset-select') : null;
    if (newSelect) {
        rebuildAmcAssetSelectOptions(newSelect, locationId, '');
    }
    initAmcSelect2(tbody);
    syncAmcLocationDatesToAssets(locationBlock);
    updateAmcLocationFieldNames();
    updateAmcSummary();
}

function getAmcLocationIdFromBlock(locationBlock) {
    var locSelect = locationBlock ? locationBlock.querySelector('.amc-location-select') : null;
    if (!locSelect) {
        return '';
    }
    var locationId = $.trim(locSelect.value || '');
    if (!locationId && locSelect.dataset && locSelect.dataset.selected) {
        locationId = $.trim(locSelect.dataset.selected || '');
    }
    if (!locationId && window.jQuery && jQuery(locSelect).data('select2')) {
        locationId = $.trim(jQuery(locSelect).val() || '');
    }
    return locationId;
}

function getAmcAssetsForLocation(locationId) {
    if (!locationId) {
        return [];
    }
    var locKey = String(locationId);
    var source = Array.isArray(amcAssetsLookup) ? amcAssetsLookup : Object.values(amcAssetsLookup || {});
    var list = [];
    source.forEach(function (asset) {
        if (!asset) {
            return;
        }
        var assetLoc = String(asset.asset_location != null ? asset.asset_location : '');
        if (assetLoc === locKey) {
            list.push(asset);
        }
    });
    list.sort(function (a, b) {
        return String(a.asset_code || '').localeCompare(String(b.asset_code || ''));
    });
    return list;
}

function rebuildAmcAssetSelectOptions(selectEl, locationId, selectedId) {
    if (!selectEl) {
        return;
    }
    var preserveId = selectedId != null ? String(selectedId) : String(selectEl.value || '');
    var hadSelect2 = window.jQuery && jQuery(selectEl).hasClass('select2-hidden-accessible');
    if (hadSelect2) {
        jQuery(selectEl).select2('destroy');
    }
    selectEl.innerHTML = '';
    var placeholder = document.createElement('option');
    placeholder.value = '';
    if (!locationId) {
        placeholder.textContent = 'Select location first';
    } else {
        placeholder.textContent = 'Select Asset';
    }
    selectEl.appendChild(placeholder);

    if (locationId) {
        var assets = getAmcAssetsForLocation(locationId);
        if (assets.length === 0) {
            var emptyOpt = document.createElement('option');
            emptyOpt.value = '';
            emptyOpt.textContent = 'No assets at this location';
            emptyOpt.disabled = true;
            selectEl.appendChild(emptyOpt);
        } else {
            assets.forEach(function (asset) {
                var option = document.createElement('option');
                option.value = String(asset.id || '');
                option.textContent = asset.asset_code || ('Asset ' + asset.id);
                option.setAttribute('data-code', asset.asset_code || '');
                option.setAttribute('data-name', asset.asset_description || '');
                option.setAttribute('data-category-id', asset.category_id || '');
                option.setAttribute('data-category-name', asset.category_name || '');
                selectEl.appendChild(option);
            });
        }
    }

    var matched = false;
    if (preserveId) {
        Array.prototype.forEach.call(selectEl.options, function (opt) {
            if (opt.value === preserveId) {
                matched = true;
            }
        });
    }
    selectEl.value = matched ? preserveId : '';

    if (hadSelect2 && jQuery.fn.select2) {
        jQuery(selectEl).select2({ width: '100%' });
        jQuery(selectEl).off('select2:select select2:clear change.amcAsset').on('select2:select select2:clear change.amcAsset', function () {
            onAmcAssetChange(this);
        });
    }
    onAmcAssetChange(selectEl);
}

function refreshAmcLocationAssetSelects(locationBlock) {
    if (!locationBlock) {
        return;
    }
    var locationId = getAmcLocationIdFromBlock(locationBlock);
    locationBlock.querySelectorAll('.amc-asset-select').forEach(function (selectEl) {
        rebuildAmcAssetSelectOptions(selectEl, locationId, selectEl.value);
    });
}

function clearAmcLocationAssets(locationBlock) {
    if (!locationBlock) {
        return;
    }
    var tbody = locationBlock.querySelector('.amc-assets-tbody');
    if (!tbody) {
        return;
    }
    tbody.querySelectorAll('.amc-asset-row').forEach(function (row) {
        var slotsRow = getAmcAssetSlotsRow(row);
        if (slotsRow) {
            slotsRow.remove();
        }
        row.remove();
    });
    updateAmcLocationFieldNames();
    updateAmcSummary();
}

function syncAmcLocationSelectTracking(locationBlock) {
    var select = locationBlock ? locationBlock.querySelector('.amc-location-select') : null;
    if (select) {
        select.dataset.amcPrevLocationId = getAmcLocationIdFromBlock(locationBlock);
    }
}

function handleAmcLocationSelectChange(locationBlock, selectEl) {
    if (!locationBlock || !selectEl) {
        return;
    }
    var prev = selectEl.dataset.amcPrevLocationId || '';
    var next = getAmcLocationIdFromBlock(locationBlock);
    if (prev !== '' && prev !== next) {
        clearAmcLocationAssets(locationBlock);
        amcNotifyWarning('Location changed. Assets for this location were cleared — add assets again.');
    }
    selectEl.dataset.amcPrevLocationId = next;
}

function removeAmcAssetRow(btn) {
    const row = btn.closest('.amc-asset-row');
    if (row) {
        const slotsRow = getAmcAssetSlotsRow(row);
        if (slotsRow) {
            slotsRow.remove();
        }
        row.remove();
        updateAmcLocationFieldNames();
        updateAmcSummary();
    }
}

function onAmcAssetChange(selectEl) {
    const row = selectEl.closest('.amc-asset-row');
    if (!row) {
        return;
    }
    const nameInput = row.querySelector('.amc-asset-name');
    const categoryInput = row.querySelector('.amc-asset-category');
    if (!nameInput) {
        return;
    }
    if (!selectEl.value) {
        nameInput.value = '';
        if (categoryInput) {
            categoryInput.value = '';
        }
        return;
    }
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    if (selectedOption) {
        nameInput.value = selectedOption.getAttribute('data-name') || '';
        if (categoryInput) {
            categoryInput.value = selectedOption.getAttribute('data-category-name') || '';
        }
    }
}

function getAmcSelectedAssetIdsInBlock(locationBlock) {
    var ids = {};
    locationBlock.querySelectorAll('.amc-asset-select').forEach(function (sel) {
        if (sel.value) {
            ids[sel.value] = true;
        }
    });
    return ids;
}

function addAmcAssetsByCategory(locationBlock) {
    var locSelect = locationBlock.querySelector('.amc-location-select');
    var categorySelect = locationBlock.querySelector('.amc-bulk-category-select');
    var locationId = getAmcLocationIdFromBlock(locationBlock);
    var categoryId = categorySelect ? $.trim(categorySelect.value || '') : '';
    if (!locationId) {
        amcNotifyWarning('Select a location before adding assets by category.');
        return;
    }
    if (!categoryId) {
        amcNotifyWarning('Select a category to bulk-add assets.');
        return;
    }
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: { page: 'getamcassetsbylocationcategory', location_id: locationId, category_id: categoryId },
        success: function (resp) {
            var assets = (resp && resp.assets) ? resp.assets : [];
            if (!Array.isArray(assets) || assets.length === 0) {
                amcNotifyWarning('No assets found for this location and category.');
                return;
            }
            var existing = getAmcSelectedAssetIdsInBlock(locationBlock);
            var added = 0;
            assets.forEach(function (asset) {
                var assetId = String(asset.id || '');
                if (!assetId || existing[assetId]) {
                    return;
                }
                addAmcAssetRow(locationBlock);
                var tbody = locationBlock.querySelector('.amc-assets-tbody');
                var rows = tbody ? tbody.querySelectorAll('.amc-asset-row') : [];
                var row = rows.length ? rows[rows.length - 1] : null;
                if (!row) {
                    return;
                }
                var assetSelect = row.querySelector('.amc-asset-select');
                if (assetSelect) {
                    assetSelect.value = assetId;
                    onAmcAssetChange(assetSelect);
                    if ($.fn.select2) {
                        $(assetSelect).trigger('change.select2');
                    }
                }
                existing[assetId] = true;
                added++;
            });
            initAmcSelect2(locationBlock);
            updateAmcLocationFieldNames();
            updateAmcSummary();
            if (added === 0) {
                amcNotifyWarning('All matching assets are already added.');
            }
        },
        error: function () {
            amcNotifyWarning('Unable to load assets for category.');
        }
    });
}

const AMC_DEFAULT_VISITS_PER_INTERVAL = {
    daily: 1,
    weekly: 1,
    monthly: 1,
    monthly_twice: 2,
    quarterly: 1,
    halfyearly: 1,
    yearly: 1,
    custom: 1
};

const AMC_MAX_VISITS_PER_INTERVAL = {
    daily: 12,
    weekly: 7,
    monthly: 31,
    monthly_twice: 2,
    quarterly: 31,
    halfyearly: 31,
    yearly: 12,
    custom: 31
};

const AMC_WEEKDAY_OPTIONS = [
    { value: '0', label: 'Sun' },
    { value: '1', label: 'Mon' },
    { value: '2', label: 'Tue' },
    { value: '3', label: 'Wed' },
    { value: '4', label: 'Thu' },
    { value: '5', label: 'Fri' },
    { value: '6', label: 'Sat' }
];

function getAmcAssetSlotsRow(assetRow) {
    if (!assetRow) {
        return null;
    }
    const next = assetRow.nextElementSibling;
    return next && next.classList.contains('amc-asset-slots-row') ? next : null;
}

function getAmcMaxVisitsPerInterval(freq) {
    return AMC_MAX_VISITS_PER_INTERVAL[normalizeAmcServiceFrequency(freq)] || 31;
}

function getAmcDefaultVisitsPerInterval(freq) {
    return AMC_DEFAULT_VISITS_PER_INTERVAL[normalizeAmcServiceFrequency(freq)] || 1;
}

function getAmcSlotFieldType(freq) {
    const f = normalizeAmcServiceFrequency(freq);
    if (f === 'custom') {
        return 'date';
    }
    if (f === 'daily') {
        return 'time';
    }
    if (f === 'weekly') {
        return 'weekday';
    }
    if (f === 'monthly' || f === 'monthly_twice' || f === 'quarterly' || f === 'halfyearly' || f === 'yearly') {
        return 'day';
    }
    return '';
}

function getDefaultAmcSlotValue(freq, slotIndex, interval, startYmd) {
    const f = normalizeAmcServiceFrequency(freq);
    if (f === 'daily') {
        const hour = Math.min(9 + slotIndex * 3, 21);
        return String(hour).padStart(2, '0') + ':00';
    }
    if (f === 'weekly') {
        const startDay = startYmd ? new Date(startYmd + 'T00:00:00').getDay() : 1;
        return String((startDay + slotIndex) % 7);
    }
    const startDay = startYmd ? parseInt(startYmd.split('-')[2], 10) : 1;
    if (f === 'monthly_twice') {
        if (slotIndex === 0) {
            return String(startDay);
        }
        return String(Math.min(28, startDay + 15));
    }
    if (interval <= 1) {
        return String(startDay);
    }
    const step = Math.max(1, Math.floor(28 / interval));
    return String(Math.min(28, startDay + slotIndex * step));
}

function getAmcSlotValuesFromRow(assetRow) {
    const slotsRow = getAmcAssetSlotsRow(assetRow);
    if (!slotsRow) {
        return [];
    }
    return Array.from(slotsRow.querySelectorAll('.amc-slot-field')).map(function (el) {
        return el.value;
    });
}

function rebuildAmcSlotFields(assetRow) {
    const freqSelect = assetRow.querySelector('.amc-service-frequency-select');
    const visitsInput = assetRow.querySelector('.amc-visits-per-interval');
    const slotsRow = getAmcAssetSlotsRow(assetRow);
    if (!freqSelect || !visitsInput || !slotsRow) {
        return;
    }
    const freq = normalizeAmcServiceFrequency(freqSelect.value);
    const container = slotsRow.querySelector('.amc-slot-fields');
    const labelEl = slotsRow.querySelector('.amc-slot-config-label');
    if (!container) {
        return;
    }
    if (!freq) {
        slotsRow.classList.add('d-none');
        container.innerHTML = '';
        return;
    }
    slotsRow.classList.remove('d-none');

    const maxVisits = getAmcMaxVisitsPerInterval(freq);
    let interval = parseInt(visitsInput.value, 10);
    if (isNaN(interval) || interval < 1) {
        interval = 1;
    }
    if (interval > maxVisits) {
        interval = maxVisits;
        visitsInput.value = interval;
    }
    visitsInput.max = maxVisits;
    visitsInput.readOnly = freq === 'monthly_twice';

    const existing = getAmcSlotValuesFromRow(assetRow);
    const prevFreq = slotsRow.getAttribute('data-amc-slot-freq') || '';
    const reuseExisting = prevFreq === freq;
    slotsRow.setAttribute('data-amc-slot-freq', freq);
    const startYmd = assetRow.querySelector('.amc-asset-service-start')?.value || '';
    const endYmd = assetRow.querySelector('.amc-asset-service-end')?.value || '';
    const fieldType = getAmcSlotFieldType(freq);
    const typeLabels = { time: 'Time', weekday: 'Weekday', day: 'Day of month', date: 'Date' };
    if (labelEl) {
        labelEl.textContent = freq === 'custom'
            ? 'Visit dates (pick manually):'
            : 'Visit slots (' + (typeLabels[fieldType] || 'Slot') + '):';
    }

    container.innerHTML = '';
    for (let i = 0; i < interval; i++) {
        const wrap = document.createElement('div');
        wrap.className = 'amc-slot-item';
        const lbl = document.createElement('label');
        lbl.className = 'form-label mb-0 small text-muted';
        lbl.textContent = '#' + (i + 1);
        wrap.appendChild(lbl);

        let input;
        const defaultVal = reuseExisting && existing[i] !== undefined && existing[i] !== ''
            ? existing[i]
            : (fieldType === 'date' ? '' : getDefaultAmcSlotValue(freq, i, interval, startYmd));

        if (fieldType === 'time') {
            input = document.createElement('input');
            input.type = 'time';
            input.className = 'form-control form-control-sm amc-slot-field';
            input.value = defaultVal;
        } else if (fieldType === 'date') {
            input = document.createElement('input');
            input.type = 'date';
            input.className = 'form-control form-control-sm amc-slot-field';
            input.value = defaultVal;
            if (startYmd) {
                input.min = startYmd;
            }
            if (endYmd) {
                input.max = endYmd;
            }
            input.title = 'Visit date';
        } else if (fieldType === 'weekday') {
            input = document.createElement('select');
            input.className = 'form-select form-select-sm amc-slot-field';
            AMC_WEEKDAY_OPTIONS.forEach(function (opt) {
                const o = document.createElement('option');
                o.value = opt.value;
                o.textContent = opt.label;
                if (String(opt.value) === String(defaultVal)) {
                    o.selected = true;
                }
                input.appendChild(o);
            });
        } else {
            input = document.createElement('input');
            input.type = 'number';
            input.min = '1';
            input.max = '31';
            input.className = 'form-control form-control-sm amc-slot-field text-end';
            input.value = defaultVal;
        }
        wrap.appendChild(input);
        container.appendChild(wrap);
    }
    updateAmcLocationFieldNames();
}

function onAmcServiceFrequencyChange(selectEl) {
    const row = selectEl.closest('.amc-asset-row');
    if (!row) {
        return;
    }
    const freq = normalizeAmcServiceFrequency(selectEl.value);
    const visitsInput = row.querySelector('.amc-visits-per-interval');
    if (!visitsInput) {
        return;
    }
    const slotsRow = getAmcAssetSlotsRow(row);
    if (slotsRow) {
        const container = slotsRow.querySelector('.amc-slot-fields');
        if (container) {
            container.innerHTML = '';
        }
        slotsRow.setAttribute('data-amc-slot-freq', '');
    }
    if (!freq) {
        slotsRow?.classList.add('d-none');
        return;
    }
    if (freq === 'custom') {
        visitsInput.value = getAmcDefaultVisitsPerInterval(freq);
        visitsInput.readOnly = false;
        rebuildAmcSlotFields(row);
        return;
    }
    visitsInput.value = getAmcDefaultVisitsPerInterval(freq);
    visitsInput.readOnly = freq === 'monthly_twice';
    rebuildAmcSlotFields(row);
}

function getAmcAssetRowData(row) {
    const assetSelect = row.querySelector('.amc-asset-select');
    const nameInput = row.querySelector('.amc-asset-name');
    const freqSelect = row.querySelector('.amc-service-frequency-select');
    const visitsInput = row.querySelector('.amc-visits-per-interval');
    const startInput = row.querySelector('.amc-asset-service-start');
    const endInput = row.querySelector('.amc-asset-service-end');
    let assetCode = '';
    if (assetSelect && assetSelect.selectedIndex >= 0) {
        const opt = assetSelect.options[assetSelect.selectedIndex];
        assetCode = opt ? (opt.getAttribute('data-code') || opt.textContent.trim()) : '';
    }
    let frequencyLabel = '';
    if (freqSelect && freqSelect.selectedIndex >= 0) {
        const freqOpt = freqSelect.options[freqSelect.selectedIndex];
        frequencyLabel = freqOpt ? freqOpt.textContent.trim() : '';
    }
    const visitsPerInterval = visitsInput ? parseInt(visitsInput.value, 10) || 1 : 1;
    const serviceSlots = getAmcSlotValuesFromRow(row);
    return {
        code: assetCode || '—',
        name: nameInput ? nameInput.value : '—',
        serviceFrequency: freqSelect ? freqSelect.value : '',
        frequencyLabel: frequencyLabel,
        visitsPerInterval: visitsPerInterval,
        serviceSlots: serviceSlots,
        serviceStart: startInput ? startInput.value : '',
        serviceEnd: endInput ? endInput.value : ''
    };
}

function parseAmcDateForCalendar(dateStr) {
    if (!dateStr) {
        return null;
    }
    if (typeof moment !== 'undefined') {
        if (typeof date_format !== 'undefined') {
            const fmt = date_format.replace(/yy/g, 'YYYY').replace(/mm/g, 'MM').replace(/dd/g, 'DD');
            const m = moment(dateStr, fmt, true);
            if (m.isValid()) {
                return m.format('YYYY-MM-DD');
            }
        }
        const m2 = moment(dateStr);
        return m2.isValid() ? m2.format('YYYY-MM-DD') : null;
    }
    return dateStr;
}

function normalizeAmcServiceFrequency(code) {
    if (!code) {
        return '';
    }
    const c = String(code).trim().toLowerCase();
    const legacy = { '0': 'custom', '1': 'daily', '2': 'weekly', '3': 'monthly', '4': 'yearly' };
    if (legacy[c]) {
        return legacy[c];
    }
    return c;
}

function formatAmcDateYmd(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + day;
}

function addMonthsSafeYmd(ymd, months) {
    const d = new Date(ymd + 'T00:00:00');
    const day = d.getDate();
    d.setMonth(d.getMonth() + months);
    if (d.getDate() < day) {
        d.setDate(0);
    }
    return formatAmcDateYmd(d);
}

function addMonthsSafe(date, months) {
    const d = new Date(date);
    const day = d.getDate();
    d.setMonth(d.getMonth() + months);
    if (d.getDate() < day) {
        d.setDate(0);
    }
    return d;
}

function advanceAmcVisitDateYmd(currentYmd, frequency) {
    const freq = normalizeAmcServiceFrequency(frequency);
    const base = new Date(currentYmd + 'T00:00:00');
    switch (freq) {
        case 'daily':
            return formatAmcDateYmd(new Date(base.getTime() + 86400000));
        case 'weekly':
            return formatAmcDateYmd(new Date(base.getTime() + 7 * 86400000));
        case 'monthly':
            return addMonthsSafeYmd(currentYmd, 1);
        case 'monthly_twice':
            return formatAmcDateYmd(new Date(base.getTime() + 15 * 86400000));
        case 'quarterly':
            return addMonthsSafeYmd(currentYmd, 3);
        case 'halfyearly':
            return addMonthsSafeYmd(currentYmd, 6);
        case 'yearly': {
            const y = new Date(base);
            y.setFullYear(y.getFullYear() + 1);
            return formatAmcDateYmd(y);
        }
        default:
            return null;
    }
}

function buildAmcPlannedVisitDatesSequential(frequency, serviceStart, serviceEnd) {
    const freq = normalizeAmcServiceFrequency(frequency);
    if (freq === 'custom' || !freq) {
        return [];
    }
    const startYmd = parseAmcDateForCalendar(serviceStart);
    const endYmd = parseAmcDateForCalendar(serviceEnd);
    if (!startYmd || !endYmd || startYmd >= endYmd) {
        return [];
    }
    const dates = [];
    let current = startYmd;
    while (current < endYmd) {
        dates.push(current);
        const next = advanceAmcVisitDateYmd(current, freq);
        if (!next || next === current) {
            break;
        }
        current = next;
        if (dates.length > 10000) {
            break;
        }
    }
    return dates;
}

function getNextAmcPeriodAnchor(currentYmd, frequency) {
    const freq = normalizeAmcServiceFrequency(frequency);
    const base = new Date(currentYmd + 'T00:00:00');
    switch (freq) {
        case 'daily':
            return formatAmcDateYmd(new Date(base.getTime() + 86400000));
        case 'weekly':
            return formatAmcDateYmd(new Date(base.getTime() + 7 * 86400000));
        case 'monthly':
            return addMonthsSafeYmd(currentYmd, 1);
        case 'monthly_twice':
            return formatAmcDateYmd(new Date(base.getTime() + 15 * 86400000));
        case 'quarterly':
            return addMonthsSafeYmd(currentYmd, 3);
        case 'halfyearly':
            return addMonthsSafeYmd(currentYmd, 6);
        case 'yearly': {
            const y = new Date(base);
            y.setFullYear(y.getFullYear() + 1);
            return formatAmcDateYmd(y);
        }
        default:
            return null;
    }
}

function computeAmcSlotVisit(periodAnchor, frequency, slotValue, slotIndex, interval, contractStartYmd) {
    const freq = normalizeAmcServiceFrequency(frequency);
    const slot = (slotValue || '').toString().trim();
    if (freq === 'daily') {
        return { date: periodAnchor, time: slot || getDefaultAmcSlotValue(freq, slotIndex, interval, contractStartYmd) };
    }
    if (freq === 'weekly') {
        const weekStart = new Date(periodAnchor + 'T00:00:00');
        const startDow = weekStart.getDay();
        let targetDow = parseInt(slot, 10);
        if (isNaN(targetDow)) {
            targetDow = (startDow + slotIndex) % 7;
        }
        let delta = targetDow - startDow;
        if (delta < 0) {
            delta += 7;
        }
        const visitDate = new Date(weekStart.getTime() + delta * 86400000);
        return { date: formatAmcDateYmd(visitDate), time: '' };
    }
    let day = parseInt(slot, 10);
    if (isNaN(day) || day < 1) {
        day = parseInt(getDefaultAmcSlotValue(freq, slotIndex, interval, contractStartYmd), 10);
    }
    day = Math.min(31, Math.max(1, day));
    const parts = periodAnchor.split('-');
    const y = parseInt(parts[0], 10);
    const m = parseInt(parts[1], 10);
    const dim = new Date(y, m, 0).getDate();
    day = Math.min(day, dim);
    return {
        date: y + '-' + String(m).padStart(2, '0') + '-' + String(day).padStart(2, '0'),
        time: ''
    };
}

function buildAmcPlannedVisits(frequency, serviceStart, serviceEnd, visitsPerInterval, serviceSlots) {
    const freq = normalizeAmcServiceFrequency(frequency);
    if (!freq) {
        return [];
    }
    const startYmd = parseAmcDateForCalendar(serviceStart);
    const endYmd = parseAmcDateForCalendar(serviceEnd);
    if (freq === 'custom') {
        return (serviceSlots || [])
            .map(function (slot) { return parseAmcDateForCalendar(slot) || slot; })
            .filter(Boolean)
            .filter(function (d) {
                if (!startYmd || !endYmd) {
                    return true;
                }
                return d >= startYmd && d < endYmd;
            })
            .sort()
            .map(function (d) { return { date: d, time: '' }; });
    }
    if (!startYmd || !endYmd || startYmd >= endYmd) {
        return [];
    }
    const interval = Math.max(1, parseInt(visitsPerInterval, 10) || 1);
    if (interval <= 1 && freq !== 'daily') {
        return buildAmcPlannedVisitDatesSequential(frequency, serviceStart, serviceEnd).map(function (d) {
            return { date: d, time: '' };
        });
    }
    if (interval <= 1 && freq === 'daily') {
        const dailyTime = (serviceSlots && serviceSlots[0])
            ? serviceSlots[0]
            : getDefaultAmcSlotValue('daily', 0, 1, startYmd);
        return buildAmcPlannedVisitDatesSequential(frequency, serviceStart, serviceEnd).map(function (d) {
            return { date: d, time: dailyTime };
        });
    }
    const slots = (serviceSlots && serviceSlots.length)
        ? serviceSlots
        : Array.from({ length: interval }, function (_, i) {
            return getDefaultAmcSlotValue(freq, i, interval, startYmd);
        });

    const visits = [];
    let periodAnchor = startYmd;
    while (periodAnchor < endYmd) {
        for (let i = 0; i < interval; i++) {
            const visit = computeAmcSlotVisit(periodAnchor, freq, slots[i], i, interval, startYmd);
            if (visit.date >= startYmd && visit.date < endYmd) {
                visits.push(visit);
            }
        }
        const nextAnchor = getNextAmcPeriodAnchor(periodAnchor, freq);
        if (!nextAnchor || nextAnchor === periodAnchor) {
            break;
        }
        periodAnchor = nextAnchor;
        if (visits.length > 10000) {
            break;
        }
    }
    return visits;
}

function buildAmcPlannedVisitDates(frequency, serviceStart, serviceEnd, visitsPerInterval, serviceSlots) {
    const interval = visitsPerInterval != null ? parseInt(visitsPerInterval, 10) : 1;
    if ((!interval || interval <= 1) && (!serviceSlots || !serviceSlots.length)) {
        return buildAmcPlannedVisitDatesSequential(frequency, serviceStart, serviceEnd);
    }
    return buildAmcPlannedVisits(frequency, serviceStart, serviceEnd, visitsPerInterval, serviceSlots)
        .map(function (v) { return v.date; });
}

function formatAmcDisplayDate(ymd) {
    if (!ymd) {
        return '—';
    }
    if (typeof moment !== 'undefined') {
        if (typeof date_format !== 'undefined') {
            const fmt = date_format.replace(/yy/g, 'YYYY').replace(/mm/g, 'MM').replace(/dd/g, 'DD');
            const m = moment(ymd, 'YYYY-MM-DD', true);
            if (m.isValid()) {
                return m.format(fmt);
            }
        }
        const m2 = moment(ymd, 'YYYY-MM-DD', true);
        return m2.isValid() ? m2.format('YYYY-MM-DD') : ymd;
    }
    return ymd;
}

function amcNotifyWarning(message) {
    if (typeof tostarnotify === 'function') {
        tostarnotify('top', 'right', 'fa fa-info', 'form-label label label-warning', 'animated flipInX', 'animated flipOutX', message, '');
    }
}

function validateAmcSchedulePreview(data) {
    if (!data.serviceFrequency) {
        amcNotifyWarning('Please select a service frequency.');
        return false;
    }
    if (!data.serviceStart || !data.serviceEnd) {
        amcNotifyWarning('Please set service start and end dates.');
        return false;
    }
    const freq = normalizeAmcServiceFrequency(data.serviceFrequency);
    if (freq === 'custom') {
        const filled = (data.serviceSlots || []).filter(Boolean);
        if (!filled.length) {
            amcNotifyWarning('Please pick at least one visit date below the asset row.');
            return false;
        }
        return true;
    }
    if (!freq) {
        amcNotifyWarning('Unknown service frequency.');
        return false;
    }
    if (!data.visitsPerInterval || data.visitsPerInterval < 1) {
        amcNotifyWarning('Please set visits per interval (1 or more).');
        return false;
    }
    return true;
}

function formatAmcTimeDisplay(timeStr) {
    if (!timeStr) {
        return '';
    }
    if (typeof moment !== 'undefined') {
        const m = moment(timeStr, ['HH:mm', 'H:mm'], true);
        if (m.isValid()) {
            return m.format('h:mm A');
        }
    }
    return timeStr;
}

function buildAmcCalendarEventTitle(visit, frequency) {
    const freq = normalizeAmcServiceFrequency(frequency);
    if (freq === 'daily' && visit.time) {
        return 'Scheduled Visit · ' + formatAmcTimeDisplay(visit.time);
    }
    return 'Scheduled Visit';
}

function formatAmcVisitLabel(visit, frequency) {
    const datePart = formatAmcDisplayDate(visit.date);
    if (normalizeAmcServiceFrequency(frequency) === 'daily' && visit.time) {
        return datePart + ' · ' + formatAmcTimeDisplay(visit.time);
    }
    return datePart;
}

function buildAmcModalLabel(data) {
    let label = data.code + (data.name && data.name !== '—' ? ' — ' + data.name : '');
    if (data.frequencyLabel) {
        label += ' (' + data.frequencyLabel;
        if (data.visitsPerInterval > 1) {
            label += ' × ' + data.visitsPerInterval;
        }
        label += ')';
    }
    return label;
}

function escapeAmcHtml(str) {
    if (str == null) {
        return '';
    }
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function renderAmcAssetHistoryTable(tbody, data) {
    if (!tbody) {
        return;
    }
    const visits = buildAmcPlannedVisits(
        data.serviceFrequency,
        data.serviceStart,
        data.serviceEnd,
        data.visitsPerInterval,
        data.serviceSlots
    );
    if (!visits.length) {
        const emptyMsg = normalizeAmcServiceFrequency(data.serviceFrequency) === 'custom'
            ? 'Pick visit dates below the asset row to preview the schedule.'
            : 'No planned service visits for the selected dates and frequency.';
        tbody.innerHTML = '<tr class="amc-history-empty"><td colspan="6" class="text-center text-muted">' + emptyMsg + '</td></tr>';
        return;
    }
    const code = escapeAmcHtml(data.code);
    const name = escapeAmcHtml(data.name);
    let html = '';
    visits.forEach(function (visit) {
        const displayDate = formatAmcVisitLabel(visit, data.serviceFrequency);
        html += '<tr>' +
            '<td>' + code + '</td>' +
            '<td>' + name + '</td>' +
            '<td>' + escapeAmcHtml(displayDate) + '</td>' +
            '<td>' + escapeAmcHtml(displayDate) + '</td>' +
            '<td>—</td>' +
            '<td>—</td>' +
            '</tr>';
    });
    tbody.innerHTML = html;
}

function getAmcPageContext() {
    return {
        contractId: (document.getElementById('contract-id') || {}).textContent || '',
        companyId: (document.getElementById('company-id') || {}).textContent || '',
        userId: (document.getElementById('user-id') || {}).textContent || '',
        dataName: (document.getElementById('data-name') || {}).textContent || ''
    };
}

function getAmcAssetScheduleIds(row) {
    const ctx = getAmcPageContext();
    const contractAmcAssetId = row ? row.getAttribute('data-contract-amc-asset-id') : '';
    return {
        contractId: ctx.contractId.trim(),
        companyId: ctx.companyId.trim(),
        contractAmcAssetId: contractAmcAssetId ? contractAmcAssetId.trim() : ''
    };
}

function openAmcAssetHistory(btn) {
    const row = btn.closest('.amc-asset-row');
    if (!row) {
        return;
    }
    const ids = getAmcAssetScheduleIds(row);
    if (!ids.contractId || !ids.contractAmcAssetId) {
        amcNotifyWarning('Save the contract first to view the planned schedule.');
        return;
    }
    const data = getAmcAssetRowData(row);
    const labelEl = document.getElementById('amcHistoryAssetLabel');
    const bodyEl = document.getElementById('amcAssetHistoryBody');
    if (labelEl) {
        labelEl.textContent = buildAmcModalLabel(data);
    }
    if (bodyEl) {
        bodyEl.innerHTML = '<p class="text-center text-muted mb-0">Loading schedule…</p>';
    }
    const modalEl = document.getElementById('amcAssetHistoryModal');
    const modal = getBootstrapModal(modalEl);
    if (modal) {
        modal.show();
    }
    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        data: {
            page: 'amc_asset_schedule_history',
            contract_id: ids.contractId,
            contract_amc_asset_id: ids.contractAmcAssetId
        },
        success: function (html) {
            if (bodyEl) {
                bodyEl.innerHTML = html;
            }
        },
        error: function () {
            if (bodyEl) {
                bodyEl.innerHTML = '<p class="text-center text-danger mb-0">Failed to load schedule.</p>';
            }
        }
    });
}

function buildAmcCalendarEvents(serviceStart, serviceEnd, frequency, visitsPerInterval, serviceSlots) {
    const visits = buildAmcPlannedVisits(frequency, serviceStart, serviceEnd, visitsPerInterval, serviceSlots);
    const freq = normalizeAmcServiceFrequency(frequency);
    return visits.map(function (visit) {
        const event = {
            title: buildAmcCalendarEventTitle(visit, frequency),
            start: visit.date,
            allDay: !(freq === 'daily' && visit.time),
            color: '#17a2b8'
        };
        if (freq === 'daily' && visit.time) {
            event.start = visit.date + 'T' + visit.time;
            event.allDay = false;
        }
        return event;
    });
}

function destroyAmcServiceCalendar() {
    const calEl = jQuery('#amc-service-calendar');
    if (calEl.length && calEl.fullCalendar) {
        try {
            calEl.fullCalendar('destroy');
        } catch (e) {
            calEl.empty();
        }
    } else if (calEl.length) {
        calEl.empty();
    }
    amcServiceCalendarInstance = null;
}

function openAmcServiceCalendar(btn) {
    const row = btn.closest('.amc-asset-row');
    if (!row) {
        return;
    }
    const ids = getAmcAssetScheduleIds(row);
    if (!ids.contractId || !ids.contractAmcAssetId) {
        amcNotifyWarning('Save the contract first to view the service calendar.');
        return;
    }
    const data = getAmcAssetRowData(row);
    const labelEl = document.getElementById('amcCalendarAssetLabel');
    if (labelEl) {
        labelEl.textContent = buildAmcModalLabel(data);
    }

    const modalEl = document.getElementById('amcServiceCalendarModal');
    const modal = getBootstrapModal(modalEl);
    if (!modal) {
        return;
    }
    modal.show();

    modalEl.addEventListener('shown.bs.modal', function onShown() {
        modalEl.removeEventListener('shown.bs.modal', onShown);
        destroyAmcServiceCalendar();
        $.ajax({
            url: 'ajax.php',
            type: 'POST',
            dataType: 'json',
            data: {
                page: 'get_amc_contract_asset_calendar_events',
                contract_id: ids.contractId,
                contract_amc_asset_id: ids.contractAmcAssetId
            },
            success: function (payload) {
                const events = (payload && payload.events) ? payload.events : [];
                const defaultDate = parseAmcDateForCalendar(data.serviceStart) ||
                    (typeof moment !== 'undefined' ? moment().format('YYYY-MM-DD') : new Date().toISOString().slice(0, 10));
                const isDaily = normalizeAmcServiceFrequency(data.serviceFrequency) === 'daily';
                if (typeof jQuery !== 'undefined' && jQuery.fn.fullCalendar) {
                    jQuery('#amc-service-calendar').fullCalendar({
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'month,agendaWeek'
                        },
                        defaultView: isDaily ? 'agendaWeek' : 'month',
                        defaultDate: defaultDate,
                        navLinks: true,
                        editable: false,
                        eventLimit: true,
                        timeFormat: 'h:mm A',
                        events: events,
                        eventClick: function (calEvent) {
                            if (typeof tostarnotify === 'function') {
                                let when = calEvent.start.format('YYYY-MM-DD');
                                if (calEvent.start.hasTime && calEvent.start.hasTime()) {
                                    when += ' · ' + calEvent.start.format('h:mm A');
                                }
                                tostarnotify('top', 'right', 'fa fa-info', 'form-label label label-info', 'animated flipInX', 'animated flipOutX', calEvent.title + ' — ' + when, '');
                            }
                        }
                    });
                    amcServiceCalendarInstance = true;
                }
            },
            error: function () {
                amcNotifyWarning('Failed to load calendar events.');
            }
        });
    }, { once: true });
}

function initAmcContractDocuments() {
    var $container = $('#amc_additional_file_buttons');
    var savedCount = $container.find('.cc-uploaded-file-item[data-filename]').length;
    if (savedCount > 0) {
        $('#unified-contract-page .cc-upload-filename').text(savedCount + ' file(s) uploaded');
    }
}

function handleAmcContractDocumentSelect(input) {
    if (!input.files || input.files.length === 0) {
        return;
    }

    var $container = $('#amc_additional_file_buttons');
    if (!$container.length) {
        return;
    }

    for (var i = 0; i < input.files.length; i++) {
        var file = input.files[i];
        var tempId = 'pending_amc_' + Date.now() + '_' + i;
        var pendingHtml = ''
            + '<div class="cc-uploaded-file-item d-flex align-items-center justify-content-between gap-2" data-temp-id="' + tempId + '">'
            + '<span class="text-truncate" title="' + escapeAmcHtml(file.name) + '">'
            + '<i class="icofont icofont-clock-time"></i> ' + escapeAmcHtml(file.name) + ' <small class="text-muted">(pending)</small>'
            + '</span>'
            + '</div>';
        $container.append(pendingHtml);
    }

    var pendingCount = $container.find('[data-temp-id]').length;
    var savedCount = $container.find('.cc-uploaded-file-item[data-filename]').length;
    var totalCount = pendingCount + savedCount;
    $('#unified-contract-page .cc-upload-filename').text(totalCount + ' file(s) selected');

    /* Remove disabled — re-enable when delete-on-save is implemented
    $container.off('click.amcPendingRemove').on('click.amcPendingRemove', '.amc-remove-pending-document-btn', function () {
        var tempId = $(this).data('temp-id');
        $container.find('[data-temp-id="' + tempId + '"]').remove();
        refreshAmcContractDocumentHint();
        if ($container.find('[data-temp-id]').length === 0) {
            input.value = '';
        }
    });
    */
}

function refreshAmcContractDocumentHint() {
    var $container = $('#amc_additional_file_buttons');
    var savedCount = $container.find('.cc-uploaded-file-item[data-filename]').length;
    var pendingCount = $container.find('[data-temp-id]').length;
    var $hint = $('#unified-contract-page .cc-upload-filename');

    if (savedCount === 0 && pendingCount === 0) {
        $hint.text('');
        return;
    }
    if (pendingCount > 0) {
        $hint.text((savedCount + pendingCount) + ' file(s) selected');
        return;
    }
    $hint.text(savedCount + ' file(s) uploaded');
}

/* Remove disabled — re-enable when delete-on-save is implemented
function removeAmcAdditionalDocument(fileName) {
    var doRemove = function () {
        $('#amc_additional_file_buttons .cc-uploaded-file-item[data-filename]').filter(function () {
            return String($(this).data('filename')) === String(fileName);
        }).remove();

        var hiddenInput = document.getElementById('amc_all_additional_documents');
        if (hiddenInput) {
            var currentFiles = hiddenInput.value.split(',').filter(function (file) {
                return file.trim() !== '' && file.trim() !== fileName.trim();
            });
            hiddenInput.value = currentFiles.join(',');
        }

        refreshAmcContractDocumentHint();
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to remove this file?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!'
        }).then(function (result) {
            if (result.isConfirmed) {
                doRemove();
                Swal.fire('Removed!', 'File has been removed from the list.', 'success');
            }
        });
        return;
    }

    if (window.confirm('Do you want to remove this file?')) {
        doRemove();
    }
}
*/

function escapeAmcHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
function renewContractBTN(){
    updateUrlParameter('renew', 'Y');
    location.reload();
}
function updateUrlParameter(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    window.history.pushState({}, '', url);
}

