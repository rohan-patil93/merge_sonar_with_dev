(function () {
  function bindStockRequestSelect() {
    if (!window.jQuery) {
      return;
    }

    var selector = ".js-stock-request-select[data-auto-submit='1']";
    jQuery(document).off("change select2:select", selector).on(
      "change select2:select",
      selector,
      function () {
        var form = this.closest("form");
        if (form) {
          form.submit();
        }
      }
    );
  }

  function bindQtyValidation() {
    var inputs = document.querySelectorAll('.stock-transfer-qty-input');
    
    inputs.forEach(function(input) {
      input.addEventListener('change', function() {
        var value = parseFloat(this.value);
        var maxQty = parseFloat(this.getAttribute('data-item-qty'));
        
        if (value > maxQty) {
          if (typeof swal === 'function') {
            swal('Validation Error', 'Stock transfer quantity cannot exceed item quantity (' + maxQty + ')', 'warning');
          } else {
            alert('Stock transfer quantity cannot exceed item quantity (' + maxQty + ')');
          }
          this.value = '';
        }
      });
    });
  }

  function bindFormValidation() {
    var form = document.getElementById('stockTransferForm');
    if (!form) return;

    function handleSubmit(e) {
      e.preventDefault();
      // Validate that at least one stock transfer quantity is entered
      var inputs = form.querySelectorAll('.stock-transfer-qty-input');
      var hasValue = false;

      inputs.forEach(function(input) {
        if (parseFloat(input.value) > 0) {
          hasValue = true;
        }
      });

      if (!hasValue) {
        if (typeof swal === 'function') {
          swal('Validation Error', 'Please enter at least one stock transfer quantity', 'warning');
        } else {
          alert('Please enter at least one stock transfer quantity');
        }
        return false;
      }

      if (typeof swal === 'function') {
        swal({
          title: 'Warning',
          text: 'This is not editable. Please make sure before submit',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'OK',
          cancelButtonText: 'Cancel',
          allowOutsideClick: false,
          closeOnConfirm: true,
          closeOnCancel: true
        }, function(isConfirm) {
          if (isConfirm) {
            form.removeEventListener('submit', handleSubmit);
            HTMLFormElement.prototype.submit.call(form);
          }
        });
      }
    }

    form.addEventListener('submit', handleSubmit);
  }

  if (window.jQuery) {
    jQuery(function () {
      bindStockRequestSelect();
      bindFormValidation();
      bindQtyValidation();
    });
  }
})();
