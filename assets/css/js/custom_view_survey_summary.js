(function() {
    'use strict';

    function getPhpData() {
        var dataContainer = document.querySelector('.survey-summary-php-data');
        if (!dataContainer) return {};
        return {
            employeeIdList: dataContainer.getAttribute('data-employee-idlist')
        };
    }

    function moreemployees() {
        var phpData = getPhpData();
        var employee_idlist = phpData.employeeIdList;
        
        jQuery.ajax({
            url: "views/custom_view_employees_popup.php",
            data: 'empids=' + employee_idlist,
            type: "GET",
            success: function(data) {
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true });
            },
            error: function() {}
        });
    }

    function bindEvents() {
        $(document).on('click', '.survey-summary-more-employees', function(e) {
            e.preventDefault();
            moreemployees();
        });
    }

    function init() {
        bindEvents();
    }

    $(document).ready(function() {
        init();
    });

})();
