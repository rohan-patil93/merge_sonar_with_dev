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

  function bindStockTransferSelect() {
    if (!window.jQuery) {
      return;
    }

    var selector = ".js-stock-transfer-select[data-auto-submit='1']";
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

  function bindReceiptQtyValidation() {
    if (!window.jQuery) {
      return;
    }

    jQuery(document).off("input", ".receipt-qty-input").on("input", ".receipt-qty-input", function () {
      var pendingQty = parseFloat(this.getAttribute("data-pending-qty") || "0");
      var receiptQty = parseFloat(this.value || "0");

      if (receiptQty < 0) {
        this.value = "0";
        return;
      }

      if (receiptQty > pendingQty) {
        this.value = pendingQty;
      }
    });

    jQuery(document).off("submit", "#stockTransferReceiptForm").on("submit", "#stockTransferReceiptForm", function (event) {
      var hasValidQty = false;
      jQuery(this).find(".receipt-qty-input").each(function () {
        var pendingQty = parseFloat(this.getAttribute("data-pending-qty") || "0");
        var receiptQty = parseFloat(this.value || "0");

        if (receiptQty > pendingQty) {
          this.value = pendingQty;
          receiptQty = pendingQty;
        }

        if (receiptQty > 0) {
          hasValidQty = true;
        }
      });

      if (!hasValidQty) {
        event.preventDefault();
        if (window.swal) {
          swal("Receipt quantity required", "Enter at least one receipt quantity greater than zero.", "warning");
        } else {
          alert("Enter at least one receipt quantity greater than zero.");
        }
      }
    });
  }

  if (window.jQuery) {
    jQuery(function () {
      bindStockRequestSelect();
      bindStockTransferSelect();
      bindReceiptQtyValidation();
    });
  }
})();
