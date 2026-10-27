(function () {
  function bindWorkOrderSelect() {
    if (!window.jQuery) {
      return;
    }

    var selector = ".js-work-order-select[data-auto-submit='1']";
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

  function bindLogRowHandlers() {
    var addRowButton = document.getElementById("addRow");
    var logTableBody = document.querySelector("#logTable tbody");
    if (!addRowButton || !logTableBody) {
      return;
    }

    var rowCount = logTableBody.querySelectorAll("tr").length || 1;

    addRowButton.addEventListener("click", function () {
      if (this.disabled) {
        return;
      }

      rowCount += 1;
      var newRow = document.createElement("tr");
      newRow.innerHTML =
        '<td class="text-center">' +
        rowCount +
        "</td>" +
        '<input type="hidden" name="logs[' +
        rowCount +
        '][id]" value="">' +
        '<td><input type="date" name="logs[' +
        rowCount +
        '][log_date]" class="form-control" required></td>' +
        '<td><input type="number" name="logs[' +
        rowCount +
        '][log_qty]" class="form-control text-end" step="any" min="0" required></td>' +
        '<td><textarea name="logs[' +
        rowCount +
        '][remarks]" class="form-control" placeholder="Enter remarks..."></textarea></td>' +
        '<td class="text-center">' +
        '<button type="button" class="btn btn-outline-danger btn-sm remove-row">' +
        '<i class="ti ti-trash"></i>' +
        "</button>" +
        "</td>";

      logTableBody.appendChild(newRow);
    });

    document.addEventListener("click", function (event) {
      var removeButton = event.target.closest(".remove-row");
      if (removeButton) {
        var row = removeButton.closest("tr");
        if (row) {
          row.remove();
        }
      }
    });
  }

  function bindLogFormValidation() {
    var form = document.getElementById("workOrderLogsForm");
    if (!form) {
      return;
    }

    var plannedQty = parseFloat(form.dataset.plannedQty || "0") || 0;

    form.addEventListener("submit", function (event) {
      if (!event.submitter) {
        return;
      }

      var totalQty = 0;
      form
        .querySelectorAll('input[name^="logs"][name$="[log_qty]"]')
        .forEach(function (input) {
          totalQty += parseFloat(input.value) || 0;
        });

      if (totalQty > plannedQty && plannedQty > 0) {
        event.preventDefault();

        if (!window.Swal) {
          return;
        }

        Swal.fire({
          title: "Quantity Exceeds Planned Quantity",
          text:
            "Total entered: " +
            totalQty +
            " exceeds planned: " +
            plannedQty +
            ". Do you want to continue?",
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "Yes, Continue",
          cancelButtonText: "No, Cancel",
          allowOutsideClick: false,
          allowEscapeKey: false,
          reverseButtons: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33"
        }).then(function (result) {
          if (result.isConfirmed) {
            HTMLFormElement.prototype.submit.call(form);
          } else {
            Swal.fire({
              title: "Submission Cancelled",
              icon: "info",
              timer: 1500,
              showConfirmButton: false
            });
          }
        });
      }
    });
  }

  if (window.jQuery) {
    jQuery(function () {
      bindWorkOrderSelect();
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    bindLogRowHandlers();
    bindLogFormValidation();
  });
})();
