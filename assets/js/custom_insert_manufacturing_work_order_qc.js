$(document).ready(function () {
  $(document).off('click', '.js-open-manufacturing-qc');
  $(document).on('click', '.js-open-manufacturing-qc', function () {
    var button = $(this);
    $.ajax({
      url: 'views/custom_list_approve_quality_check_popup.php',
      type: 'POST',
      data: {
        table: button.data('table'),
        tranid: button.data('tranid'),
        item_id: button.data('item-id'),
        quality_type: button.data('quality-type')
      },
      success: function (html) {
        $('#manufacturingQcModalContent').html(html);
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
          var modal = new bootstrap.Modal(document.getElementById('manufacturingQcModal'));
          modal.show();
        } else {
          $('#manufacturingQcModal').modal('show');
        }
      },
      error: function () {
        if (typeof swal === 'function') {
          swal('Error', 'Unable to load QC details', 'error');
        } else {
          alert('Unable to load QC details');
        }
      }
    });
  });
});
