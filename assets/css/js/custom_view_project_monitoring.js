(function () {
  document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery) {
      $('#csrProjectMonitoringFySelect').on('change', function () {
        $('#csrProjectMonitoringYearForm').submit();
      });
    } else {
      var fySelect = document.getElementById('csrProjectMonitoringFySelect');
      var fyForm = document.getElementById('csrProjectMonitoringYearForm');
      if (fySelect && fyForm) {
        fySelect.addEventListener('change', function () {
          fyForm.submit();
        });
      }
    }
  });
}());
