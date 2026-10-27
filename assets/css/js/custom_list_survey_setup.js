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

    function getPhpData() {
        var surveyQtypes = $('#survey_qtypes').val();
        var surveyQtypeNames = $('#survey_qtype_names').val();
        
        return {
            surveyQtypes: surveyQtypes ? surveyQtypes.split('|') : [],
            surveyQtypeNames: surveyQtypeNames ? surveyQtypeNames.split('|') : []
        };
    }

    function initDataTable() {
        if (typeof $.fn.DataTable !== 'undefined') {
            $('#kt_datatable1').DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'asc']]
            });
        }
    }

    function bindEvents() {
        
    }

    function init() {
        loadGlobalConfig();
        bindEvents();
        initDataTable();
    }

    $(document).ready(function() {
        init();
    });

})();
