(function() {
    'use strict';

    var errorMessages = {};

    function loadGlobalConfig() {
        try {
            var errorJson = $('#error_message_json').val();
            if (errorJson) errorMessages = JSON.parse(errorJson);
        } catch(e) {
            console.log('Config parse error:', e);
        }
    }

    function getPhpData() {
        var dataContainer = document.querySelector('.status-popup-php-data');
        if (!dataContainer) return {};
        return {
            successTitle: dataContainer.getAttribute('data-success-title') || 'Success',
            successText: dataContainer.getAttribute('data-success-text') || 'Status updated successfully'
        };
    }

    function submitrom() {
        var status = $('#status_value').val();
        var str = 'table=' + $('#table').val() + 
                  '&comment=' + $('#comment').val() + 
                  '&status=' + status + 
                  '&hid=' + $('#rid').val() + 
                  '&extra=' + $('#extra').val();
        
        console.log(str);

        var phpData = getPhpData();

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=dynamicchangestatus&' + str,
            success: function(response) {
                console.log(response);
                swal({
                    title: phpData.successTitle,
                    text: phpData.successText,
                    type: "success",
                    showCancelButton: false,
                    closeOnConfirm: false,
                    showLoaderOnConfirm: true
                }, function() {
                    $('#kt_modal_KTDatatable_remote').modal('hide');
                    window.location.reload();
                });
            }
        });
    }

    function bindEvents() {
        $(document).on('click', '.status-popup-submit-btn', function(e) {
            e.preventDefault();
            submitrom();
        });
    }

    function init() {
        loadGlobalConfig();
        bindEvents();
    }

    $(document).ready(function() {
        init();
    });

})();
