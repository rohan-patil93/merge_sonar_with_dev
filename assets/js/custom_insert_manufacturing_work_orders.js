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

  function selectManufacturingFirmOrder() {
    var manufacturingFirmOrderId = jQuery("#manufacturing_firm_order_id").val();
    jQuery.ajax({
      url: "ajax.php",
      data:
        "page=getmanufacturingworkorders&manufacturing_firm_order_id=" +
        manufacturingFirmOrderId +
        "&gettype=SELECTMANUFACTURINGFIRMORDER",
      type: "POST",
      success: function (data) {
        var res = JSON.parse(data);
        if (!res.SELECTMANUFACTURINGFIRMORDER || !res.SELECTMANUFACTURINGFIRMORDER.product_id) {
          jQuery("#work_order_item_id").val("").trigger("change");
          jQuery("#work_order_item_qty").val("");
          jQuery("#manufacturing_bom_id").html('<option value="">Select Any one</option>');
          if (typeof swal === "function") {
            swal("Firm Order", res.message || "This firm order cannot be used for a work order.", "warning");
          }
          return;
        }

        jQuery("#work_order_item_id").val(res.SELECTMANUFACTURINGFIRMORDER.product_id);
        jQuery("#work_order_item_id").select2("destroy").select2();
        jQuery("#work_order_item_qty").val(res.SELECTMANUFACTURINGFIRMORDER.firm_qty_1);
        updateWorkOrderUom(res.SELECTMANUFACTURINGFIRMORDER.uom_name || "");

        var boms = res.MANUFACTURINGBOMS;
        var bomOptions = '<option value="">Select Any one</option>';
        var seqVersion = 1;
        jQuery.each(boms, function (index, bom) {
          bomOptions +=
            "<option value=\"" +
            bom.id +
            "\">V" +
            seqVersion +
            " - " +
            bom.transaction_date +
            " - " +
            bom.bom_quantity +
            " " +
            (bom.uom_name || "") +
            "</option>";
          seqVersion += 1;
        });
        jQuery("#manufacturing_bom_id").html(bomOptions);
      }
    });
  }

  function selectManufacturingBOM() {
    var manufacturingBomId = jQuery("#manufacturing_bom_id").val();
    var workOrderItemQty = jQuery("#work_order_item_qty").val();
    jQuery.ajax({
      url: "views/custom_insert_manufacturing_work_orders_ajax_new.php",
      data:
        "manufacturing_bom_id=" +
        manufacturingBomId +
        "&work_order_item_qty=" +
        workOrderItemQty +
        "&gettype=SELECTMANUFACTURINGBOM",
      type: "POST",
      success: function (data) {
        jQuery("#Manufacturing_Work_Orders_ajax").html(data);
        jQuery(".js-example-basic-single").select2();
        jQuery(".js-example-disabled-results").select2();
        jQuery('[data-bs-toggle="tooltip"]').tooltip();
      }
    });
  }

  function renderTemplate(templateId, seq) {
    var template = document.getElementById(templateId);
    if (!template) {
      return "";
    }

    return template.innerHTML.replace(/__SEQ__/g, String(seq));
  }

  function addMoreBomChild() {
    bomItemSeq += 1;
    var htmlRows = renderTemplate("bom-item-row-template", bomItemSeq);
    if (!htmlRows) {
      return;
    }

    jQuery("#invoiceItem").append(htmlRows);
    jQuery(".js-example-basic-single").select2();
    jQuery(".js-example-disabled-results").select2();
    jQuery('[data-bs-toggle="tooltip"]').tooltip();
  }

  function addMoreBomOperations() {
    bomOperationSeq += 1;
    var htmlRows = renderTemplate("bom-operation-row-template", bomOperationSeq);
    if (!htmlRows) {
      return;
    }

    jQuery("#invoiceItemOperation").append(htmlRows);
    jQuery(".js-example-basic-single").select2();
    jQuery(".js-example-disabled-results").select2();
    jQuery('[data-bs-toggle="tooltip"]').tooltip();
  }

  function addMoreByItems() {
    byItemSeq += 1;
    var htmlRows = renderTemplate("byitem-row-template", byItemSeq);
    if (!htmlRows) {
      return;
    }

    jQuery("#byitem_invoiceitems").append(htmlRows);
    jQuery(".js-example-basic-single").select2();
    jQuery(".js-example-disabled-results").select2();
    jQuery('[data-bs-toggle="tooltip"]').tooltip();
  }

  function selectWorkOrderItemId() {
    var workOrderItemId = jQuery("#work_order_item_id").val();
    jQuery.ajax({
      url: "ajax.php",
      data:
        "page=getmanufacturingworkorders&work_order_item_id=" +
        workOrderItemId +
        "&gettype=SELECTWORKOODERITEMID",
      type: "POST",
      success: function (data) {
        var res = JSON.parse(data);
        updateWorkOrderUom(getSelectedOptionUom("#work_order_item_id"));
        var boms = res.SELECTWORKOODERITEMID;
        var bomOptions = '<option value="">Select Any one</option>';
        var seqVersion = 1;
        jQuery.each(boms, function (index, bom) {
          bomOptions +=
            "<option value=\"" +
            bom.id +
            "\">V" +
            seqVersion +
            " - " +
            bom.transaction_date +
            " - " +
            bom.bom_quantity +
            " " +
            (bom.uom_name || "") +
            "</option>";
          seqVersion += 1;
        });
        jQuery("#manufacturing_bom_id").html(bomOptions);
      }
    });
  }

  function selectManufacturingOperations(seq, type) {
    var getType = "";
    var manufacturingOperationId = "";

    if (type === "OPERATIONS") {
      manufacturingOperationId = jQuery("#manufacturing_operation_id_" + seq).val();
      getType = "SELECTMANUFACTURINGOPERATION";
    } else if (type === "MACHINE") {
      manufacturingOperationId = jQuery("#manufacturing_operation_machine_id_" + seq).val();
      getType = "SELECTMANUFACTURINGOPERATIONMACHINE";
    }

    if (!manufacturingOperationId) {
      return;
    }

    jQuery.ajax({
      url: "ajax.php",
      data:
        "page=getmanufacturingboms&manufacturing_operation_id=" +
        manufacturingOperationId +
        "&gettype=" +
        getType,
      type: "POST",
      success: function (data) {
        var res = JSON.parse(data);
        var operationData = res.SELECTMANUFACTURINGOPERATION || {};

        if (type === "OPERATIONS" || type === "MACHINE") {
          jQuery("#operation_time_" + seq).val(
            operationData.average_time_in_minutes || 0
          );
          jQuery("#operation_manpower_" + seq).val(
            operationData.manpower_required || 0
          );
          jQuery("#operation_cost_per_hours_" + seq).val(
            operationData.cost_per_hour || 0
          );
          jQuery("#operation_note_" + seq).val(operationData.description || "");

          jQuery("#operation_time_display_" + seq).text(
            operationData.average_time_in_minutes || 0
          );
          jQuery("#operation_manpower_display_" + seq).text(
            operationData.manpower_required || 0
          );
          jQuery("#operation_cost_per_hours_display_" + seq).text(
            operationData.cost_per_hour || 0
          );
        }

        if (res.SELECT_OPTIONS) {
          jQuery("#manufacturing_operation_machine_id_" + seq).html(res.SELECT_OPTIONS);
        }

        if (typeof window.manufacturingCalculation === "function") {
          window.manufacturingCalculation();
        }

        var totalVal = jQuery("#operation_total_" + seq).val();
        if (totalVal !== undefined) {
          jQuery("#operation_total_display_" + seq).text(totalVal);
        }
      }
    });
  }

  window.selectManufacturingOperations = selectManufacturingOperations;

  function getSelectedOptionUom(selector) {
    var selected = jQuery(selector).find("option:selected");
    return selected.data("uom") || "";
  }

  function updateWorkOrderUom(uomName) {
    jQuery("#work_order_item_uom").text(uomName || "UOM");
  }

  function updateManufacturingMethodFields(selectEl) {
    var $select = jQuery(selectEl);
    var seq = $select.data("seq");
    var isSubcontract = $select.val() === "SUBCONTRACT";
    var $fields = jQuery("#subcontract_fields_" + seq);
    var $note = jQuery("#inhouse_note_" + seq);

    $fields.toggleClass("d-none", !isSubcontract);
    $note.toggleClass("d-none", isSubcontract);

    if (!isSubcontract) {
      jQuery("#party_id_" + seq).val("").trigger("change.select2");
      jQuery("#warehouse_id_" + seq).val("").trigger("change.select2");
    }
  }

  window.removerows = function (id) {
    jQuery("#" + id).remove();
  };

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

  window.manufacturingCalculation = function () {
    jQuery(".manufacturing_bom_items_seq_no").each(function () {
      var seq = jQuery(this).val();
      var quantity = jQuery("#request_qty_" + seq).val();
      quantity = quantity !== "" ? quantity : 0;
      var rate = jQuery("#rate_" + seq).val();
      rate = rate !== "" ? rate : 0;
      var amount = quantity * rate;
      jQuery("#amount_" + seq).val(amount.toFixed(2));
      jQuery("#amount_text_" + seq).text(amount.toFixed(2));
    });

    jQuery(".manufacturing_bom_operation_seq_no").each(function () {
      var seq = jQuery(this).val();
      var operationTime = jQuery("#operation_time_" + seq).val();
      operationTime = operationTime !== "" ? operationTime : 0;
      var operationCost = jQuery("#operation_cost_per_hours_" + seq).val();
      operationCost = operationCost !== "" ? operationCost : 0;
      var perMinuteRate = operationCost !== 0 ? operationCost / 60 : 0;
      var amount = operationTime * perMinuteRate;
      jQuery("#operation_total_" + seq).val(amount.toFixed(2));
    });
  };

  var bomItemSeq = 10222;
  var bomOperationSeq = 20222;
  var byItemSeq = 30222;

  if (window.jQuery) {
    jQuery(function () {
      jQuery(document).off(
        "change select2:select",
        ".js-manufacturing-firm-order"
      );
      jQuery(document).on(
        "change select2:select",
        ".js-manufacturing-firm-order",
        selectManufacturingFirmOrder
      );

      jQuery(document).off("change select2:select", ".js-work-order-item");
      jQuery(document).on(
        "change select2:select",
        ".js-work-order-item",
        selectWorkOrderItemId
      );

      jQuery(document).off("change select2:select", ".js-manufacturing-bom");
      jQuery(document).on(
        "change select2:select",
        ".js-manufacturing-bom",
        selectManufacturingBOM
      );

      jQuery(document).off("click", ".js-remove-row");
      jQuery(document).on("click", ".js-remove-row", function () {
        var targetId = jQuery(this).data("target");
        if (targetId) {
          jQuery("#" + targetId).remove();
        }
      });

      jQuery(document).off("click", ".js-add-bom-item");
      jQuery(document).on("click", ".js-add-bom-item", function () {
        addMoreBomChild();
      });

      jQuery(document).off("click", ".js-add-bom-operation");
      jQuery(document).on("click", ".js-add-bom-operation", function () {
        addMoreBomOperations();
      });

      jQuery(document).off("click", ".js-add-byitem");
      jQuery(document).on("click", ".js-add-byitem", function () {
        addMoreByItems();
      });

      jQuery(document).off("change select2:select", ".js-product-name-select");
      jQuery(document).on(
        "change select2:select",
        ".js-product-name-select",
        function () {
          var seq = jQuery(this).data("seq");
          if (seq && typeof window.selectProductName === "function") {
            window.selectProductName(String(seq));
          }
        }
      );

      jQuery(document).off(
        "change select2:select",
        ".js-operation-select, .js-operation-machine-select"
      );
      jQuery(document).on(
        "change select2:select",
        ".js-operation-select, .js-operation-machine-select",
        function () {
          var $el = jQuery(this);
          var seq = $el.data("seq");
          var type = $el.data("type") || "OPERATIONS";
          if (seq && typeof window.selectManufacturingOperations === "function") {
            window.selectManufacturingOperations(String(seq), String(type));
          }
        }
      );

      jQuery(document).off("blur", ".js-manufacturing-calc");
      jQuery(document).on("blur", ".js-manufacturing-calc", function () {
        if (typeof window.manufacturingCalculation === "function") {
          window.manufacturingCalculation();
        }
      });

      jQuery(document).off("change", ".js-manufacturing-method");
      jQuery(document).on("change", ".js-manufacturing-method", function () {
        updateManufacturingMethodFields(this);
      });

      jQuery(document).off("change", "#work_order_item_qty");
      jQuery(document).on("change", "#work_order_item_qty", function () {
        var newWorkOrderQty = jQuery(this).val();
        newWorkOrderQty = newWorkOrderQty !== "" ? parseFloat(newWorkOrderQty) : 0;
        
        // Update request_qty[] - multiply original bom_qty by new work_order_item_qty
        jQuery('input[name="request_qty[]"]').each(function() {
          var originalQty = jQuery(this).data("original-qty");
          if (originalQty !== undefined) {
            var newVal = (parseFloat(originalQty) * newWorkOrderQty).toFixed(2);
            jQuery(this).val(newVal);
            // Update display span
            var inputId = jQuery(this).attr("id");
            var seq = inputId.replace("request_qty_", "");
            jQuery("#request_qty_display_" + seq).text(newVal);
          }
        });
        
        // Update byitem_quantity[] - multiply original by-product qty by new work_order_item_qty
        jQuery('input[name="byitem_quantity[]"]').each(function() {
          var originalQty = jQuery(this).data("original-qty");
          if (originalQty !== undefined) {
            var newVal = (parseFloat(originalQty) * newWorkOrderQty).toFixed(2);
            jQuery(this).val(newVal);
            // Update display span
            var inputId = jQuery(this).attr("id");
            var seq = inputId.replace("byitem_quantity_", "");
            jQuery("#byitem_quantity_display_" + seq).text(newVal);
          }
        });
        
        // Trigger calculation
        if (typeof window.manufacturingCalculation === "function") {
          window.manufacturingCalculation();
        }
      });

      // Bind production lifecycle buttons
      bindProductionStartButton();
      bindProductionHoldButton();
      bindProductionResumeButton();
      bindProductionCompleteButton();
    });
  }

  function bindProductionStartButton() {
    var btn = document.getElementById('production_start_btn');
    if (!btn) {
      return;
    }

    btn.addEventListener('click', function (e) {
      e.preventDefault();

      var workOrderId = btn.getAttribute('data-work-order-id');
      if (!workOrderId) {
        if (typeof swal === 'function') {
          swal('Error', 'Work Order ID not found', 'error');
        } else {
          alert('Work Order ID not found');
        }
        return;
      }

      if (typeof swal === 'function') {
        swal({
          title: 'Start Production',
          text: 'Are you sure you want to start production for this work order?',
          type: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, Start',
          cancelButtonText: 'Cancel',
          allowOutsideClick: false,
          closeOnConfirm: true,
          closeOnCancel: true
        }, function (isConfirm) {
          if (isConfirm) {
            startProduction(workOrderId);
          }
        });
      }
    });
  }

  function startProduction(workOrderId) {
    // Show Bootstrap modal for remarks
    var modalHtml = '<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-hidden="true">' +
      '<div class="modal-dialog modal-dialog-centered">' +
      '<div class="modal-content">' +
      '<div class="modal-header border-bottom">' +
      '<h5 class="modal-title">Production Remarks</h5>' +
      '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
      '</div>' +
      '<div class="modal-body">' +
      '<div class="form-group">' +
      '<label class="form-label">Remarks (Optional)</label>' +
      '<textarea id="remarks-input" class="form-control" rows="4" maxlength="255" placeholder="Enter any remarks for starting production..."></textarea>' +
      '<small class="text-muted d-block mt-2">Maximum 255 characters</small>' +
      '</div>' +
      '</div>' +
      '<div class="modal-footer border-top">' +
      '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>' +
      '<button type="button" class="btn btn-success" id="submitRemarksBtn"><i class="la la-check"></i> Start</button>' +
      '</div>' +
      '</div></div></div>';
    
    jQuery('body').append(modalHtml);
    var modal = new bootstrap.Modal(document.getElementById('remarksModal'));
    modal.show();
    
    // Focus on textarea
    setTimeout(function() {
      jQuery('#remarks-input').focus();
    }, 300);
    
    // Handle submit
    jQuery('#submitRemarksBtn').off('click').on('click', function() {
      var remarks = jQuery('#remarks-input').val().trim();
      modal.hide();
      jQuery('#remarksModal').remove();
      submitStartProduction(workOrderId, remarks);
    });
    
    // Cleanup on modal close
    jQuery('#remarksModal').on('hidden.bs.modal', function() {
      jQuery(this).remove();
    });
  }

  function submitStartProduction(workOrderId, remarks) {
    jQuery.ajax({
      url: 'ajax.php',
      data: {
        page: 'manufacturing_production_start',
        action: 'save',
        work_order_id: workOrderId,
        remarks: remarks
      },
      type: 'POST',
      success: function (response) {
        try {
          var parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;

          if (parsedResponse.status === 'success') {
            if (typeof swal === 'function') {
              swal(
                {
                  title: 'Success',
                  text: 'Production started successfully',
                  type: 'success',
                  confirmButtonText: 'OK'
                },
                function() {
                  window.location.reload();
                }
              );
            } else {
              alert('Production started successfully');
              window.location.reload();
            }
          } else {
            if (typeof swal === 'function') {
              swal('Error', parsedResponse.message || 'Failed to start production', 'error');
            } else {
              alert(parsedResponse.message || 'Failed to start production');
            }
          }
        } catch (e) {
          if (typeof swal === 'function') {
            swal('Error', 'Invalid response from server', 'error');
          } else {
            alert('Invalid response from server');
          }
        }
      },
      error: function () {
        if (typeof swal === 'function') {
          swal('Error', 'Network error or server error occurred', 'error');
        } else {
          alert('Network error or server error occurred');
        }
      }
    });
  }

  function bindProductionHoldButton() {
    var btn = document.getElementById('production_hold_btn');
    if (!btn) {
      return;
    }

    btn.addEventListener('click', function (e) {
      e.preventDefault();

      var workOrderId = btn.getAttribute('data-work-order-id');
      if (!workOrderId) {
        if (typeof swal === 'function') {
          swal('Error', 'Work Order ID not found', 'error');
        } else {
          alert('Work Order ID not found');
        }
        return;
      }

      loadProductionModal(workOrderId, 'HOLD');
    });
  }

  function bindProductionResumeButton() {
    var btn = document.getElementById('production_resume_btn');
    if (!btn) {
      return;
    }

    btn.addEventListener('click', function (e) {
      e.preventDefault();

      var workOrderId = btn.getAttribute('data-work-order-id');
      if (!workOrderId) {
        if (typeof swal === 'function') {
          swal('Error', 'Work Order ID not found', 'error');
        } else {
          alert('Work Order ID not found');
        }
        return;
      }

      if (typeof swal === 'function') {
        swal({
            title: 'Resume Production',
            text: 'Do you want to resume production for this work order?',
            type: 'info',
            showCancelButton: true,
            confirmButtonClass: 'btn-primary',
            confirmButtonText: 'Yes, Resume',
            cancelButtonText: 'No',
            closeOnConfirm: true,
            closeOnCancel: true
          }, function (isConfirm) {
            if (isConfirm) {
              resumeProduction(workOrderId);
            }
          });
      } else {
        if (confirm('Do you want to resume production for this work order?')) {
          resumeProduction(workOrderId);
        }
      }
    });
  }

  function resumeProduction(workOrderId) {
    // Show Bootstrap modal for remarks
    var modalHtml = '<div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-hidden="true">' +
      '<div class="modal-dialog modal-dialog-centered">' +
      '<div class="modal-content">' +
      '<div class="modal-header border-bottom">' +
      '<h5 class="modal-title">Production Remarks</h5>' +
      '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>' +
      '</div>' +
      '<div class="modal-body">' +
      '<div class="form-group">' +
      '<label class="form-label">Remarks (Optional)</label>' +
      '<textarea id="remarks-input" class="form-control" rows="4" maxlength="255" placeholder="Enter any remarks for resuming production..."></textarea>' +
      '<small class="text-muted d-block mt-2">Maximum 255 characters</small>' +
      '</div>' +
      '</div>' +
      '<div class="modal-footer border-top">' +
      '<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>' +
      '<button type="button" class="btn btn-primary" id="submitRemarksBtn"><i class="la la-play-circle"></i> Resume</button>' +
      '</div>' +
      '</div></div></div>';
    
    jQuery('body').append(modalHtml);
    var modal = new bootstrap.Modal(document.getElementById('remarksModal'));
    modal.show();
    
    // Focus on textarea
    setTimeout(function() {
      jQuery('#remarks-input').focus();
    }, 300);
    
    // Handle submit
    jQuery('#submitRemarksBtn').off('click').on('click', function() {
      var remarks = jQuery('#remarks-input').val().trim();
      modal.hide();
      jQuery('#remarksModal').remove();
      submitResumeProduction(workOrderId, remarks);
    });
    
    // Cleanup on modal close
    jQuery('#remarksModal').on('hidden.bs.modal', function() {
      jQuery(this).remove();
    });
  }

  function submitResumeProduction(workOrderId, remarks) {
    jQuery.ajax({
      url: 'ajax.php',
      data: {
        page: 'manufacturing_production_resume',
        work_order_id: workOrderId,
        remarks: remarks
      },
      type: 'POST',
      success: function (response) {
        try {
          var parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;

          if (parsedResponse.status === 'success') {
            if (typeof swal === 'function') {
              swal(
                {
                  title: 'Success',
                  text: 'Production resumed successfully',
                  type: 'success',
                  confirmButtonText: 'OK'
                },
                function() {
                  window.location.reload();
                }
              );
            } else {
              alert('Production resumed successfully');
              window.location.reload();
            }
          } else {
            if (typeof swal === 'function') {
              swal('Error', parsedResponse.message || 'Failed to resume production', 'error');
            } else {
              alert(parsedResponse.message || 'Failed to resume production');
            }
          }
        } catch (e) {
          if (typeof swal === 'function') {
            swal('Error', 'Invalid response from server', 'error');
          } else {
            alert('Invalid response from server');
          }
        }
      },
      error: function () {
        if (typeof swal === 'function') {
          swal('Error', 'Network error or server error occurred', 'error');
        } else {
          alert('Network error or server error occurred');
        }
      }
    });
  }

  function bindProductionCompleteButton() {
    var btn = document.getElementById('production_complete_btn');
    if (!btn) {
      return;
    }

    btn.addEventListener('click', function (e) {
      e.preventDefault();

      var workOrderId = btn.getAttribute('data-work-order-id');
      if (!workOrderId) {
        if (typeof swal === 'function') {
          swal('Error', 'Work Order ID not found', 'error');
        } else {
          alert('Work Order ID not found');
        }
        return;
      }

      loadProductionModal(workOrderId, 'COMPLETED');
    });
  }

  function loadProductionModal(workOrderId, productionType) {
    jQuery.ajax({
      url: 'ajax.php',
      data: {
        page: 'get_production_details',
        work_order_id: workOrderId,
        production_type: productionType
      },
      type: 'POST',
      success: function (response) {
        var modalContent = document.getElementById('productionModalContent');
        if (modalContent) {
          modalContent.innerHTML = response;
          
          // Bind modal events after content loaded
          bindProductionModalEvents();
          
          // Show the modal
          if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            var modal = new bootstrap.Modal(document.getElementById('productionModal'));
            modal.show();
          } else if (typeof jQuery !== 'undefined') {
            jQuery('#productionModal').modal('show');
          }
        }
        $('.js-example-basic-single').select2({
            dropdownParent: $('#productionModalForm')
        });

      },
      error: function () {
        if (typeof swal === 'function') {
          swal('Error', 'Failed to load production details', 'error');
        } else {
          alert('Failed to load production details');
        }
      }
    });
  }

  function bindProductionModalEvents() {
    // Validate consumed quantities
    function validateConsumedQuantities() {
      var inputs = document.querySelectorAll('.consumed-qty-input');
      var hasError = false;

      inputs.forEach(function(input) {
        var value = parseFloat(input.value) || 0;
        var availableQty = parseFloat(input.getAttribute('data-max')) || 0;

        if (value > availableQty) {
          hasError = true;
          input.classList.add('is-invalid');
          var inputUom = input.getAttribute('data-uom') || '';
          var availableText = availableQty + (inputUom ? ' ' + inputUom : '');
          if (typeof swal === 'function') {
            swal('Validation Error', 'Consumed quantity cannot exceed available quantity (' + availableText + ')', 'warning');
          } else {
            alert('Consumed quantity cannot exceed available quantity (' + availableText + ')');
          }
        } else {
          input.classList.remove('is-invalid');
        }
      });

      return !hasError;
    }

    function completedQtyValue() {
      var input = document.getElementById('session_completed_qty');
      return input ? (parseFloat(input.value) || 0) : 0;
    }

    function isQcRequired() {
      var selected = document.querySelector('input[name="qc_required"]:checked');
      return selected && selected.value === '1';
    }

    function trackingTypeValue() {
      var input = document.getElementById('production_tracking_type');
      return input ? String(input.value || '0') : '0';
    }

    function renderSerialRows() {
      var wrapper = document.getElementById('serialRows');
      if (!wrapper) return;

      var qty = completedQtyValue();
      wrapper.innerHTML = '';
      if (!isQcRequired() || trackingTypeValue() !== '1' || qty <= 0) {
        return;
      }
      if (Math.floor(qty) !== qty) {
        return;
      }

      for (var i = 1; i <= qty; i += 1) {
        wrapper.insertAdjacentHTML(
          'beforeend',
          '<div class="col-md-4 mb-2">' +
            '<label class="form-label">Serial No ' + i + ' <span class="text-danger">*</span></label>' +
            '<input type="text" class="form-control serial-no-input" name="serial_no[]" maxlength="255">' +
          '</div>'
        );
      }
    }

    function addLotRow(lotNo, lotQty) {
      var wrapper = document.getElementById('lotRows');
      if (!wrapper) return;
      var rowId = 'lotRow_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
      var productUomInput = document.getElementById('production_product_uom');
      var productUom = productUomInput ? productUomInput.value : '';
      wrapper.insertAdjacentHTML(
        'beforeend',
        '<div class="row align-items-end mb-2 lot-row" id="' + rowId + '">' +
          '<div class="col-md-5">' +
            '<label class="form-label">Lot No <span class="text-danger">*</span></label>' +
            '<input type="text" class="form-control lot-no-input" name="lot_no[]" maxlength="255" value="' + (lotNo || '') + '">' +
          '</div>' +
          '<div class="col-md-5">' +
            '<label class="form-label">Qty <span class="text-danger">*</span></label>' +
            '<div class="input-group">' +
              '<input type="number" class="form-control lot-qty-input text-end" name="lot_qty[]" min="0.01" step="0.01" value="' + (lotQty || '') + '">' +
              '<span class="input-group-text manufacturing-uom-addon">' + (productUom || 'UOM') + '</span>' +
            '</div>' +
          '</div>' +
          '<div class="col-md-2">' +
            '<button type="button" class="btn btn-outline-danger btn-sm js-remove-lot-row" data-target="' + rowId + '">Remove</button>' +
          '</div>' +
        '</div>'
      );
    }

    function refreshTrackingSections() {
      var serialSection = document.getElementById('serialTrackingSection');
      var lotSection = document.getElementById('lotTrackingSection');
      var required = isQcRequired();
      var trackingType = trackingTypeValue();

      if (serialSection) {
        serialSection.classList.toggle('d-none', !(required && trackingType === '1'));
      }
      if (lotSection) {
        lotSection.classList.toggle('d-none', !(required && trackingType === '2'));
      }

      if (required && trackingType === '1') {
        renderSerialRows();
      }
      if (required && trackingType === '2') {
        var lotRows = document.querySelectorAll('.lot-row');
        if (lotRows.length === 0) {
          addLotRow('', '');
        }
      }
    }

    function validateTrackingDetails() {
      if (!isQcRequired()) {
        return true;
      }

      var trackingType = trackingTypeValue();
      var completedQty = completedQtyValue();
      if (completedQty <= 0) {
        swalOrAlert('Validation Error', 'Completed quantity is required when QC check is yes', 'warning');
        return false;
      }

      if (trackingType === '1') {
        if (Math.floor(completedQty) !== completedQty) {
          swalOrAlert('Validation Error', 'Serial tracked completed quantity must be a whole number', 'warning');
          return false;
        }
        var serialInputs = document.querySelectorAll('.serial-no-input');
        var values = [];
        for (var i = 0; i < serialInputs.length; i += 1) {
          var val = serialInputs[i].value.trim();
          if (!val) {
            swalOrAlert('Validation Error', 'Please enter all serial numbers', 'warning');
            return false;
          }
          if (values.indexOf(val) !== -1) {
            swalOrAlert('Validation Error', 'Duplicate serial numbers are not allowed', 'warning');
            return false;
          }
          values.push(val);
        }
        if (values.length !== completedQty) {
          swalOrAlert('Validation Error', 'Serial number count must equal completed quantity', 'warning');
          return false;
        }
      }

      if (trackingType === '2') {
        var lotRows = document.querySelectorAll('.lot-row');
        var totalQty = 0;
        if (lotRows.length === 0) {
          swalOrAlert('Validation Error', 'Please add at least one lot number', 'warning');
          return false;
        }
        for (var j = 0; j < lotRows.length; j += 1) {
          var lotNo = lotRows[j].querySelector('.lot-no-input').value.trim();
          var lotQty = parseFloat(lotRows[j].querySelector('.lot-qty-input').value) || 0;
          if (!lotNo || lotQty <= 0) {
            swalOrAlert('Validation Error', 'Lot number and quantity are required', 'warning');
            return false;
          }
          totalQty += lotQty;
        }
        if (Math.abs(totalQty - completedQty) > 0.0001) {
          swalOrAlert('Validation Error', 'Total lot quantity must equal completed quantity', 'warning');
          return false;
        }
      }

      return true;
    }

    function swalOrAlert(title, message, type) {
      if (typeof swal === 'function') {
        swal(title, message, type || 'warning');
      } else {
        alert(message);
      }
    }

    // Submit form via AJAX
    function submitProductionForm() {
      var form = document.getElementById('productionModalForm');
      if (!form) return;

      var formData = new FormData(form);
      var productionType = formData.get('production_type');
      var page = (productionType === 'HOLD') ? 'manufacturing_production_hold' : 'manufacturing_production_complete';

      jQuery.ajax({
        url: 'ajax.php',
        data: {
          page: page,
          work_order_id: formData.get('work_order_id'),
          completed_qty: formData.get('completed_qty'),
          item_id: formData.getAll('item_id[]'),
          manufacturing_work_order_item_id: formData.getAll('manufacturing_work_order_item_id[]'),
          consumed_qty: formData.getAll('consumed_qty[]'),
          qc_required: formData.get('qc_required') || '0',
          tracking_type: formData.get('tracking_type') || '0',
          serial_no: formData.getAll('serial_no[]'),
          lot_no: formData.getAll('lot_no[]'),
          lot_qty: formData.getAll('lot_qty[]'),
          remarks: formData.get('remarks'),
          hold_reason: formData.get('hold_reason'),
          production_type: productionType
        },
        type: 'POST',
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            var message = 'Production ' + response.production_type.toLowerCase() + ' successfully';
            
            // Close modal
            jQuery('#productionModal').modal('hide');
            
            if (typeof swal === 'function') {
              swal('Success', message, 'success', {
                buttons: false,
                timer: 1500
              });
            } else {
              alert(message);
            }
            
            // Reload page after short delay
            setTimeout(function() {
              location.reload();
            }, 1500);
          } else {
            if (typeof swal === 'function') {
              swal('Error', response.message || 'Failed to update production', 'error');
            } else {
              alert(response.message || 'Failed to update production');
            }
          }
        },
        error: function() {
          if (typeof swal === 'function') {
            swal('Error', 'Network error or server error occurred', 'error');
          } else {
            alert('Network error or server error occurred');
          }
        }
      });
    }

    // Form submission handler
    function handleFormSubmit(e) {
      e.preventDefault();

      if (!validateConsumedQuantities()) {
        return false;
      }

        var productionType = (document.querySelector('input[name="production_type"]') || {}).value || '';
        var completedQty = completedQtyValue();
        if (productionType !== 'HOLD' && completedQty <= 0) {
        if (typeof swal === 'function') {
          swal('Validation Error', 'Please enter completed quantity', 'warning');
        } else {
          alert('Please enter completed quantity');
        }
          return false;
        }

      if (!validateTrackingDetails()) {
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
            submitProductionForm();
          }
        });
      } else {
        if (confirm('This is not editable. Please make sure before submit')) {
          submitProductionForm();
        }
      }
    }

    // Bind form submission event
    var form = document.getElementById('productionModalForm');
    if (form) {
      form.removeEventListener('submit', handleFormSubmit); // Remove previous if any
      form.addEventListener('submit', handleFormSubmit);
    }

    jQuery(document).off('change', 'input[name="qc_required"]');
    jQuery(document).on('change', 'input[name="qc_required"]', refreshTrackingSections);
    jQuery(document).off('input change', '#session_completed_qty');
    jQuery(document).on('input change', '#session_completed_qty', refreshTrackingSections);
    jQuery(document).off('click', '#addLotRowBtn');
    jQuery(document).on('click', '#addLotRowBtn', function () {
      addLotRow('', '');
    });
    jQuery(document).off('click', '.js-remove-lot-row');
    jQuery(document).on('click', '.js-remove-lot-row', function () {
      var target = jQuery(this).data('target');
      if (target) {
        jQuery('#' + target).remove();
      }
    });
    refreshTrackingSections();
  }

  window.addEventListener("load", showToastIfNeeded);
})();
