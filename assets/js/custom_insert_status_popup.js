(function() {
  'use strict';

  // Get the root container with data attributes
  const root = document.getElementById('status-popup-root');
  if (!root) return;

  const msgSuccessTitle = root.dataset.msgSuccessTitle || 'Success';
  const msgSuccessText = root.dataset.msgSuccessText || 'Status updated successfully';

  // =============================================================================
  // Function: submitrom
  // =============================================================================
  function submitrom() {
    const status = jQuery('#status_value').val();
    const str = 'table=' + jQuery('#table').val() + 
                '&comment=' + jQuery('#comment').val() + 
                '&status=' + status + 
                '&hid=' + jQuery('#rid').val() + 
                '&extra=' + jQuery('#extra').val();
    
    console.log(str);
    
    jQuery.ajax({
      url: "ajax.php",
      type: "POST",
      data: 'page=dynamicchangestatus&' + str,
      success: function(response) {
        console.log(response);
        
        swal({
          title: msgSuccessTitle,
          text: msgSuccessText,
          type: "success",
          showCancelButton: false,
          closeOnConfirm: false,
          showLoaderOnConfirm: true
        }, function() {
          jQuery('#kt_modal_KTDatatable_remote').modal('hide');
          window.location.reload();
        });
      }
    });
  }

  // =============================================================================
  // Event listeners
  // =============================================================================

  // Submit button
  const submitBtn = document.querySelector('.js-submit-status');
  if (submitBtn) {
    submitBtn.addEventListener('click', submitrom);
  }

})();
