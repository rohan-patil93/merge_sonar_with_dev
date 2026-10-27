(function () {
  function getPageData() {
    var pageRoot = document.querySelector(".page-body");
    if (!pageRoot) {
      return { configName: "", error9059: "", toastMessage: "" };
    }

    return {
      configName: pageRoot.dataset.configName || "",
      error9059: pageRoot.dataset.error9059 || "",
      toastMessage: pageRoot.dataset.toastMessage || ""
    };
  }

  function showToastIfNeeded() {
    var pageData = getPageData();
    if (!pageData.toastMessage || typeof tostarnotify !== "function") {
      return;
    }

    tostarnotify(
      "top",
      "right",
      "fa fa-thumbs-up",
      "form-label label label-success",
      "animated flipInX",
      "animated flipOutX",
      pageData.toastMessage,
      ""
    );
  }

  function handleFilterChange(selectEl) {
    if (!selectEl) {
      return;
    }

    var form = selectEl.closest("form");
    if (!form) {
      return;
    }

    var yearSelect = form.querySelector("select[name='year']");
    var monthSelect = form.querySelector("select[name='month']");

    if (!yearSelect || !monthSelect) {
      return;
    }

    if (yearSelect.value && monthSelect.value) {
      form.submit();
    }
  }

  function updateCellValue(cell, newValue) {
    if (!cell) {
      return;
    }
    cell.textContent = parseFloat(newValue || 0).toFixed(2);
  }

  function updateFirmOrderTotals() {
    var table = document.querySelector("table.forecast-table");
    if (!table) {
      return;
    }

    var rows = table.querySelectorAll("tbody tr");
    var footer = table.querySelector("tfoot tr");
    if (!footer) {
      return;
    }

    var monthlyTotals = [0, 0, 0];
    var grandTotal = 0;

    rows.forEach(function (row) {
      var rowTotal = 0;
      var inputs = row.querySelectorAll(".firm-order-input");
      inputs.forEach(function (input, i) {
        var value = parseFloat(input.value) || 0;
        monthlyTotals[i] += value;
        rowTotal += value;
      });
      updateCellValue(row.querySelector("td.bg-secondary"), rowTotal);
      grandTotal += rowTotal;
    });

    var footerCells = footer.querySelectorAll("td");
    for (var i = 0; i < 3; i++) {
      updateCellValue(footerCells[i + 1], monthlyTotals[i]);
    }
    updateCellValue(footerCells[footerCells.length - 1], grandTotal);
  }

  window.submitDynamicWorkflowPopUp = function (
    tableName,
    transactionId,
    currentStatus,
    nextStatus
  ) {
    jQuery.ajax({
      url: "views/custom_insert_dynamic_status_popup.php",
      data:
        "table_name=" +
        tableName +
        "&current_status=" +
        currentStatus +
        "&transaction_id=" +
        transactionId +
        "&next_status=" +
        nextStatus,
      type: "POST",
      success: function (data) {
        jQuery("#popup1").html(data);
        jQuery("#kt_modal_KTDatatable_remote").modal("show");
      },
      error: function () {}
    });
  };

  window.submitDynamicWorkflow = function (
    tableName,
    transactionId,
    currentStatus,
    nextStatus,
    oldStatusName,
    newStatusName
  ) {
    var pageData = getPageData();
    swal(
      {
        title: pageData.configName,
        text: "Please note the status of the document will change to " + newStatusName,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
      },
      function () {
        jQuery.ajax({
          url: "ajax.php",
          data:
            "page=submitstatusdynamicworkflow&table_name=" +
            tableName +
            "&current_status=" +
            currentStatus +
            "&transaction_id=" +
            transactionId +
            "&next_status=" +
            nextStatus,
          type: "POST",
          success: function (response) {
            var res = JSON.parse(response);
            window.location.reload();
            if (res.transaction_update.status === "sucess") {
              window.location.reload();
              swal(
                {
                  title: pageData.configName,
                  text: pageData.error9059,
                  type: "success",
                  showCancelButton: false,
                  closeOnConfirm: false,
                  showLoaderOnConfirm: true
                },
                function () {
                  window.location.reload();
                }
              );
            } else {
              swal({
                title: "Oops...",
                text: res.transaction_update.message,
                type: "error",
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: "Ok",
                confirmButtonClass: "btn-danger",
                closeOnCancel: false
              });
            }
          },
          error: function () {}
        });
      }
    );
  };

  if (window.jQuery) {
    jQuery(function () {
      var selector = ".js-firm-orders-filter[data-auto-submit='1']";
      jQuery(document).off("change select2:select", selector).on(
        "change select2:select",
        selector,
        function () {
          handleFilterChange(this);
        }
      );

      jQuery(document).off("submit", "#manufacturingFirmOrderForm").on(
        "submit",
        "#manufacturingFirmOrderForm",
        function (event) {
          var valid = true;
          var hasProductRows = jQuery(this).find("tbody tr").length > 0;

          jQuery(this).find(".firm-order-input").each(function () {
            var value = jQuery(this).val();
            var numberValue = parseFloat(value);
            if (value === "" || isNaN(numberValue) || numberValue < 0) {
              valid = false;
              jQuery(this)
                .removeClass("border-primary border-warning")
                .addClass("border border-danger");
            }
          });

          if (!hasProductRows) {
            valid = false;
          }

          if (!valid) {
            event.preventDefault();
            if (typeof swal === "function") {
              swal("Validation", "Please enter valid firm order quantities for all products.", "warning");
            } else {
              alert("Please enter valid firm order quantities for all products.");
            }
          }
        }
      );

      jQuery(document).off("input", ".firm-order-input").on(
        "input",
        ".firm-order-input",
        updateFirmOrderTotals
      );
    });
  }

  window.addEventListener("load", showToastIfNeeded);
})();
