(function ($) {
  function bindEvents() {
    $('#csrOblYearFrom, #csrOblYearTo, #csrOblLocation, #csrOblEntity').on('change', function () {
      $('#csrOblYearForm').trigger('submit');
    });
  }

  $(document).ready(function () {
    bindEvents();
  });
})(jQuery);
