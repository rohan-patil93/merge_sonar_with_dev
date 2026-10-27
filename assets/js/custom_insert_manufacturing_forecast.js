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

  function handleForecastInput(event) {
    var target = event.target;
    if (!target || !target.matches("input[type='number']")) {
      return;
    }

    var row = target.closest("tr");
    if (!row) {
      return;
    }

    var val = parseFloat(target.value) || 0;
    target.classList.remove("border-primary", "border-danger", "border-warning");

    if (val > 0) {
      target.classList.add("border", "border-warning");
    } else {
      target.classList.add("border", "border-warning");
    }

    var q1 = 0;
    var q2 = 0;
    var q3 = 0;
    var q4 = 0;
    var months = row.querySelectorAll("input[type='number']");

    months.forEach(function (input, i) {
      var monthValue = parseFloat(input.value) || 0;
      if (i < 3) {
        q1 += monthValue;
      } else if (i < 6) {
        q2 += monthValue;
      } else if (i < 9) {
        q3 += monthValue;
      } else if (i < 12) {
        q4 += monthValue;
      }
    });

    var quarterCells = row.querySelectorAll("td.bg-light");
    if (quarterCells.length === 4) {
      var qValues = [q1, q2, q3, q4];
      qValues.forEach(function (qValue, i) {
        updateCellValue(quarterCells[i], qValue);
      });
    }

    var total = q1 + q2 + q3 + q4;
    var totalCell = row.querySelector("td.bg-secondary");
    if (totalCell) {
      updateCellValue(totalCell, total);
    }

    updateFooterTotals();
  }

  function updateCellValue(cell, newValue) {
    if (!cell) {
      return;
    }

    var oldValue = parseFloat(cell.textContent) || 0;
    var roundedNew = parseFloat(newValue.toFixed(2));
    if (oldValue !== roundedNew) {
      cell.textContent = roundedNew.toFixed(2);
      cell.classList.remove("highlight");
      void cell.offsetWidth;
      cell.classList.add("highlight");
    }
  }

  function updateFooterTotals() {
    var table = document.querySelector("table.forecast-table");
    if (!table) {
      return;
    }

    var rows = table.querySelectorAll("tbody tr");
    var footer = table.querySelector("tfoot tr");
    if (!footer) {
      return;
    }

    var monthlyTotals = Array(12).fill(0);
    var grandTotal = 0;

    rows.forEach(function (row) {
      var inputs = row.querySelectorAll("input[type='number']");
      inputs.forEach(function (input, i) {
        monthlyTotals[i] += parseFloat(input.value) || 0;
      });
    });

    var footerCells = footer.querySelectorAll("td");
    var cellIndex = 1;

    for (var i = 0; i < 12; i++) {
      updateCellValue(footerCells[cellIndex++], monthlyTotals[i]);
      if ((i + 1) % 3 === 0) {
        var qTotal = monthlyTotals[i - 2] + monthlyTotals[i - 1] + monthlyTotals[i];
        updateCellValue(footerCells[cellIndex++], qTotal);
      }
      grandTotal += monthlyTotals[i];
    }

    updateCellValue(footerCells[footerCells.length - 1], grandTotal);
  }

  window.submitDynamicWorkflowPopUp = function (tableName, transactionId, currentStatus, nextStatus) {
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
      var selector = ".js-forecast-year-select[data-auto-submit='1']";
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

      jQuery(document).off("submit", "#manufacturingForecastForm").on(
        "submit",
        "#manufacturingForecastForm",
        function (event) {
          var valid = true;
          var hasProductRows = jQuery(this).find("tbody tr").length > 0;

          jQuery(this).find(".forecast-month-input").each(function () {
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
              swal("Validation", "Please enter valid forecast quantities for all products.", "warning");
            } else {
              alert("Please enter valid forecast quantities for all products.");
            }
          }
        }
      );
    });
  }
  document.addEventListener("input", handleForecastInput);
  window.addEventListener("load", showToastIfNeeded);
})();
