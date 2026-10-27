(function() {
    'use strict';

    var date_format = '';
    var backdate_limit = 0;
    var errorMessages = {};
    var sessionConfig = {};

    function loadGlobalConfig() {
        date_format = $('#hide_config_date_format').val() || 'yy-mm-dd';
        
        try {
            var configJson = $('#session_config_json').val();
            if (configJson) sessionConfig = JSON.parse(configJson);
            
            var errorJson = $('#error_message_json').val();
            if (errorJson) errorMessages = JSON.parse(errorJson);
            
            backdate_limit = sessionConfig.backdate_limit || 0;
        } catch(e) {
            console.log('Config parse error:', e);
        }
    }

    function init() {
        loadGlobalConfig();
        bindEvents();
        initDatePickers();
        initSelect2();
    }

    function bindEvents() {
        $(document).on('click', '.survey-delete-question', function(e) {
            e.preventDefault();
            var qid = $(this).data('qid');
            deletequestion(qid);
        });

        $(document).on('click', '.survey-add-option-btn', function(e) {
            e.preventDefault();
            var qid = $(this).data('qid');
            addnewoptionrows(qid);
        });

        $(document).on('click', '.survey-delete-option', function(e) {
            e.preventDefault();
            var rowid = $(this).data('rowid');
            var qid = $(this).data('qid');
            delnewoptionrows(rowid, qid);
        });

        $(document).on('click', '.survey-add-question-link', function(e) {
            e.preventDefault();
            var type = $(this).data('type');
            var title = $(this).data('title');
            dynamiceditpopup(type, title);
        });

        $(document).on('click', '.survey-status-popup', function(e) {
            e.preventDefault();
            var status = $(this).data('status');
            var id = $(this).data('id');
            statuspopup(status, id);
        });

        $(document).on('click', '.survey-show-popup', function(e) {
            e.preventDefault();
            showpopup();
        });

        $(document).on('change', '.survey-status-dropdown', function() {
            var id = $(this).data('id');
            var table = $(this).data('table');
            var status_id = $(this).val();
            displaystausdrop(id, table, status_id);
        });
    }

    function initDatePickers() {
        if (typeof $.fn.datepicker !== 'undefined') {
            $(".datepicker").datepicker({
                showOn: "both",
                buttonText: "Calendar",
                dateFormat: date_format,
                minDate: backdate_limit,
                setDate: new Date(),
                changeMonth: true,
                changeYear: true
            }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
        }
    }

    function initSelect2() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.js-example-basic-single').select2({ width: '100%' });
            $('.js-example-basic-multiple').select2({ width: '100%' });
        }
    }

    function deletequestion(qid) {
        $.ajax({
            url: "ajax.php",
            data: 'page=removeqidfromsession&qid=' + qid,
            type: "POST",
            success: function() {
                $('#' + qid).remove();
            },
            error: function() {}
        });
    }

    function addnewoptionrows(qid) {
        var op = document.getElementById('option_row').value;
        var op = parseInt(op) + 1;
        var optionlistid = "optionlist" + qid + op;
        $("#options" + qid).append('<div id="' + optionlistid + '"> <div class="form-group row" id="' + op + '"><div class="col-lg-1"></div><div class="col-lg-8"><div class="input-group input-icon-right"> <input type="text" class="form-control display-inline"  name="survey_question_option' + qid + '[]" placeholder="Option"> <div class="input-group-append"><span class="input-group-text bg-primary"><span><i class="fa fa-trash close text-white survey-delete-option" data-rowid="' + op + '" data-qid="' + qid + '" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove Option"></i></span></span></div></div></div></div></div>');
        document.getElementById('option_row').value = op;
    }

    function delnewoptionrows(rowid, qid) {
        var optionlistid = "optionlist" + qid + rowid;
        $("#" + optionlistid).remove();
    }

    function dynamiceditpopup(type, title) {
        jQuery.ajax({
            url: "views/custom_insert_survey_addquestions.php",
            data: 'type=' + type + "&question_title=" + title,
            type: "GET",
            success: function(data) {
                $('#popup2').html(data);
                $("#kt_modal_KTDatatable_setup").modal("show");
            },
            error: function() {}
        });
    }

    function statuspopup(status, id) {
        var table_name = document.getElementById('table_name').value;
        jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data: 'table=' + table_name + '&status=' + status + '&rid=' + id,
            type: "POST",
            success: function(data) {
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal('show');
            },
            error: function() {}
        });
    }

    function displaystausdrop(id, table, status_id) {
        $.ajax({
            url: "ajax.php",
            data: 'page=current_status_drop&id=' + id + '&current_status=' + status_id + '&table=' + table,
            type: "POST",
            success: function(data) {
                $("#dropdownlist_" + id).html(data);
            },
            error: function() {}
        });
    }

    function showpopup() {
        var filter_id = document.getElementById('filtertbl_rowid').value;
        jQuery.ajax({
            url: "views/custom_filter_bulktransfer.php",
            data: 'filter_id=' + filter_id,
            type: "GET",
            success: function(data) {
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal('show');
            },
            error: function() {}
        });
    }

    function getnamelist() {
        var selectednames = document.getElementById('filter').value;
        $.ajax({
            url: "ajax.php",
            data: 'page=selectednames&names=' + selectednames,
            type: "POST",
            success: function(response) {
                var division = document.getElementById('tsk_fortable');
                console.log("response =" + response);
                division.innerHTML = response;
            }
        });
    }

    window.getnamelist = getnamelist;

    $(document).ready(function() {
        init();
    });

})();
