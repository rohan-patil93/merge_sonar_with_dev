window.addEventListener("load", function() {

    $(document).on("change", ".verificationFormSubmit", function () {
        document.getElementById('typeForm').submit();
    });
    


     $(document).on("click", ".verificationDetails", function () {
       var verification_type = $(this).data("verification_type");
       var record_id = $(this).data("record_id");
       var action_type = $(this).data("action_type");
       var table_name = $(this).data("table_name");
        verificationDetails(verification_type, record_id, action_type, table_name);
    });

    var verificationCompleted = false; 
    function setVerificationCompleted() {
      verificationCompleted = true;
    } 
    $(document).ready(function () {
      $('#kt_modal_dynamic_popup').on('hidden.bs.modal', function () {
        if (verificationCompleted) {
          location.reload();
          verificationCompleted = false; 
        }
      });
    });

});

  function verificationDetails(verification_type = "", record_id = "", action_type = "", table_name = "") {
    if (!verification_type || !record_id || !action_type || !table_name) {
      alert('Missing required parameters for verification');
      return;
    } 
    var currentRow = event.target.closest('tr');
    var fieldData = {}; 
    if (verification_type === 'pan') {
      fieldData.pan_no = currentRow.querySelector('td:nth-child(3)').textContent.trim();
      fieldData.company = currentRow.querySelector('.company-details .fw-bold').textContent.trim();
      fieldData.email = currentRow.querySelector('.company-details .text-muted').textContent.trim();
    } else if (verification_type === 'gst') {
      fieldData.gstin = currentRow.querySelector('td:nth-child(4)').textContent.trim();
    } else if (verification_type === 'bank') {
      var bankCell = currentRow.querySelector('.bank-details');
      fieldData.bank_account_no = bankCell.querySelector('.fw-bold').textContent.trim();
      var ifscElement = bankCell.querySelector('.text-muted');
      if (ifscElement) {
        fieldData.ifsc_code = ifscElement.textContent.replace('IFSC: ', '').trim();
      }
    } else if (verification_type === 'tan') {
      fieldData.tan_number = currentRow.querySelector('td:nth-child(6)').textContent.trim();
    } else if (verification_type === 'msme') {
      fieldData.msme_number = currentRow.querySelector('td:nth-child(7)').textContent.trim();
    } 
    Object.keys(fieldData).forEach(key => {
      if (fieldData[key] === 'N/A' || fieldData[key] === '') {
        fieldData[key] = '';
      }
    });
    jQuery.ajax({
      url: "views/custom_list_verification_vendors_and_vendor_registers_popup.php",
      data: {
        'verification_type': verification_type,
        'record_id': record_id,
        'action_type': action_type,
        'table_name': table_name,
        'field_data': JSON.stringify(fieldData)
      },
      type: "POST",
      success: function (data) {
        $('#dynamic_popup1').html(data);
        $('#kt_modal_dynamic_popup').modal("show");
      },
      error: function (xhr, status, error) {
        console.error('AJAX Error:', error);
        alert('Error loading verification popup. Please try again.');
      }
    });
  }