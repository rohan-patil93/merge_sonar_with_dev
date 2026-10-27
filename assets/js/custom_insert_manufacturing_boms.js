(function () {
  var bomItemSeq = 10222;
  var bomOperationSeq = 20222;
  var alternateItemSeq = 30222;
  var byItemSeq = 69222;
  var alternateOperationSeq = 79222;

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

  function renderTemplate(templateId, replacements) {
    var template = document.getElementById(templateId);
    if (!template) {
      return "";
    }

    var html = template.innerHTML;
    Object.keys(replacements).forEach(function (key) {
      var value = replacements[key];
      var token = new RegExp("__" + key + "__", "g");
      html = html.replace(token, value);
    });
    return html;
  }

  function reinitWidgets() {
    jQuery(".js-example-basic-single").select2();
    jQuery(".js-example-disabled-results").select2();
    jQuery('[data-bs-toggle="tooltip"]').tooltip();
  }

  function selectProductName(seq, type) {
    var bomItemId = "";
    if (!seq) {
      bomItemId = jQuery("#bom_item_id").val();
    } else if (type === "BOMITEM") {
      bomItemId = jQuery("#item_id_" + seq).val();
    } else if (type === "ALTERNATE_BOMITEM") {
      bomItemId = jQuery("#alternate_item_id_" + seq).val();
    } else if (type === "BYITEM") {
      bomItemId = jQuery("#byitem_item_id_" + seq).val();
    }

    jQuery.ajax({
      url: "ajax.php",
      data:
        "page=getmanufacturingboms&item_id=" + bomItemId + "&gettype=SELECTBOMITEMID",
      type: "POST",
      success: function (data) {
        var res = JSON.parse(data);
        if (!seq) {
          jQuery("#bom_uom").select2();
          jQuery("#bom_uom").val(res.SELECTBOMITEMID.purchase_uom).trigger("change.select2");
          return;
        }

        if (type === "BOMITEM") {
          jQuery("#description_" + seq).val(res.SELECTBOMITEMID.item_description);
          jQuery("#uom_" + seq).select2();
          jQuery("#uom_" + seq).val(res.SELECTBOMITEMID.purchase_uom).trigger("change.select2");
          jQuery("#rate_" + seq).val(res.SELECTBOMITEMID.purchase_price);
          jQuery("#rate_text_" + seq).text(res.SELECTBOMITEMID.purchase_price);
          jQuery("#warehouse_id_" + seq).select2();
          jQuery("#warehouse_id_" + seq)
            .val(res.SELECTBOMITEMID.warehouse_id)
            .trigger("change.select2");

          jQuery("#alternate_invoiceItemalternateitems_" + seq).html(" ");
          addAlternateMoreBomChild(seq, res.alternate_items_options || "");
        } else if (type === "ALTERNATE_BOMITEM") {
          jQuery("#alternate_rate_" + seq).val(res.SELECTBOMITEMID.purchase_price);
          jQuery("#alternate_rate_text_" + seq).text(res.SELECTBOMITEMID.purchase_price);
          jQuery("#alternate_description_" + seq).val(res.SELECTBOMITEMID.item_description);
          jQuery("#alternate_uom_" + seq).select2();
          jQuery("#alternate_uom_" + seq)
            .val(res.SELECTBOMITEMID.purchase_uom)
            .trigger("change.select2");
        } else if (type === "BYITEM") {
          jQuery("#byitem_description_" + seq).val(res.SELECTBOMITEMID.item_description);
          jQuery("#byitem_uom_" + seq).select2();
          jQuery("#byitem_uom_" + seq)
            .val(res.SELECTBOMITEMID.purchase_uom)
            .trigger("change.select2");
        }

        manufacturingCalculation();
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
    } else if (type === "ALTERNATE_MACHINE") {
      manufacturingOperationId = jQuery("#alternate_manufacturing_operation_machine_id_" + seq).val();
      getType = "SELECTMANUFACTURINGOPERATIONMACHINE";
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

        if (type === "OPERATIONS" || type === "MACHINE") {
          jQuery("#operation_time_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.average_time_in_minutes
          );
          jQuery("#operation_manpower_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.manpower_required
          );
          jQuery("#operation_cost_per_hours_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.cost_per_hour
          );
          jQuery("#operation_note_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.description
          );
          jQuery("#manufacturing_operation_machine_id_" + seq).html(res.SELECT_OPTIONS);

          jQuery("#alternate_invoiceItemOperation_" + seq).html(" ");
          addAlternateMoreBomOperations(seq, res.SELECT_OPTIONS || "");
        } else if (type === "ALTERNATE_MACHINE") {
          jQuery("#alternate_operation_time_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.average_time_in_minutes
          );
          jQuery("#alternate_operation_manpower_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.manpower_required
          );
          jQuery("#alternate_operation_cost_per_hours_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.cost_per_hour
          );
          jQuery("#alternate_operation_note_" + seq).val(
            res.SELECTMANUFACTURINGOPERATION.description
          );
        }

        manufacturingCalculation();
      }
    });
  }

  function manufacturingCalculation() {
    var formatter = new Intl.NumberFormat("en-IN", {
      minimumFractionDigits: 2
    });

    var totalBom = 0;
    jQuery(".manufacturing_bom_items_seq_no").each(function () {
      var seq = jQuery(this).val();
      var quantity = jQuery("#quantity_" + seq).val();
      quantity = quantity !== "" ? quantity : 0;
      var rate = jQuery("#rate_" + seq).val();
      rate = rate !== "" ? rate : 0;
      var amount = quantity * rate;
      jQuery("#amount_" + seq).val(amount.toFixed(2));
      jQuery("#amount_text_" + seq).text(amount.toFixed(2));
      totalBom = parseFloat(totalBom) + parseFloat(amount);
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
      totalBom = parseFloat(totalBom) + parseFloat(amount);
    });

    jQuery(".alternate_manufacturing_bom_operation_seq_no").each(function () {
      var seq = jQuery(this).val();
      var operationTime = jQuery("#alternate_operation_time_" + seq).val();
      operationTime = operationTime !== "" ? operationTime : 0;
      var operationCost = jQuery("#alternate_operation_cost_per_hours_" + seq).val();
      operationCost = operationCost !== "" ? operationCost : 0;
      var perMinuteRate = operationCost !== 0 ? operationCost / 60 : 0;
      var amount = operationTime * perMinuteRate;
      jQuery("#alternate_operation_total_" + seq).val(amount.toFixed(2));
    });

    jQuery(".alternate_manufacturing_bom_items_seq_no").each(function () {
      var seq = jQuery(this).val();
      var quantity = jQuery("#alternate_quantity_" + seq).val();
      quantity = quantity !== "" ? quantity : 0;
      var rate = jQuery("#alternate_rate_" + seq).val();
      rate = rate !== "" ? rate : 0;
      var amount = quantity * rate;
      jQuery("#alternate_amount_" + seq).val(amount.toFixed(2));
      jQuery("#alternate_amount_text_" + seq).text(amount.toFixed(2));
    });

    jQuery("#bom_amount").val(totalBom.toFixed(2));
    jQuery("#bom_amount_span").text(formatter.format(totalBom));
  }

  function selectionValidations() {
    jQuery(".item-selection").each(function () {
      var currentSelect = jQuery(this);
      var currentValue = currentSelect.val();
      currentSelect.find("option").prop("disabled", false);
      jQuery(".item-selection")
        .not(currentSelect)
        .each(function () {
          var otherValue = jQuery(this).val();
          if (otherValue && otherValue !== currentValue) {
            currentSelect.find('option[value="' + otherValue + '"]').prop("disabled", true);
          }
        });
      currentSelect.trigger("change.select2");
    });
  }

  function openAlternateItemsPopUp(seq) {
    closeAlternateItemsPopUp();
    jQuery('.alternate-items-popup[data-seq="' + seq + '"]').show("slow");
  }

  function closeAlternateItemsPopUp() {
    jQuery(".custom-popup-open").hide();
  }

  function openAlternateMachinePopUp(seq) {
    closeAlternateItemsPopUp();
    jQuery('.alternate-machine-popup[data-seq="' + seq + '"]').show("slow");
  }

  function addMoreBomChild() {
    bomItemSeq += 1;
    var html = renderTemplate("bom-item-row-template", { SEQ: bomItemSeq });
    if (!html) {
      return;
    }

    jQuery("#invoiceItem").append(html);
    reinitWidgets();
    addAlternateMoreBomChild(bomItemSeq);
  }

  function addMoreBomOperations() {
    bomOperationSeq += 1;
    var html = renderTemplate("bom-operation-row-template", { SEQ: bomOperationSeq });
    if (!html) {
      return;
    }

    jQuery("#invoiceItemOperation").append(html);
    reinitWidgets();
    addAlternateMoreBomOperations(bomOperationSeq);
  }

  function addByItemMoreBomChild() {
    byItemSeq += 1;
    var html = renderTemplate("byitem-row-template", { SEQ: byItemSeq });
    if (!html) {
      return;
    }

    jQuery("#byitem_invoiceitems").append(html);
    reinitWidgets();
  }

  function addAlternateMoreBomChild(parentSeq, optionHtml) {
    alternateItemSeq += 1;

    if (!optionHtml) {
      var existing = jQuery("#alternate_invoiceItemalternateitems_" + parentSeq)
        .find("select")
        .first();
      if (existing.length) {
        optionHtml = existing.html();
      }
    }

    var html = renderTemplate("alternate-item-row-template", {
      SEQ: alternateItemSeq,
      PARENT: parentSeq,
      OPTIONS: optionHtml || ""
    });
    if (!html) {
      return;
    }

    jQuery("#alternate_invoiceItemalternateitems_" + parentSeq).append(html);
    reinitWidgets();
  }

  function addAlternateMoreBomOperations(parentSeq, optionHtml) {
    alternateOperationSeq += 1;

    if (!optionHtml) {
      var optionSource = jQuery("#manufacturing_operation_machine_id_" + parentSeq)
        .clone()
        .find("option")
        .removeAttr("selected")
        .end();
      optionHtml = optionSource.html();
    }

    var html = renderTemplate("alternate-operation-row-template", {
      SEQ: alternateOperationSeq,
      PARENT: parentSeq,
      OPTIONS: optionHtml || ""
    });
    if (!html) {
      return;
    }

    jQuery("#alternate_invoiceItemOperation_" + parentSeq).append(html);
    reinitWidgets();
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
      var $doc = jQuery(document);

      $doc.off("change select2:select", ".js-bom-item-root");
      $doc.on("change select2:select", ".js-bom-item-root", function () {
        selectProductName("", "ROOT");
      });

      $doc.off("change select2:select", ".js-bom-item-select, .js-alternate-item-select, .js-byitem-select");
      $doc.on(
        "change select2:select",
        ".js-bom-item-select, .js-alternate-item-select, .js-byitem-select",
        function () {
          var $el = jQuery(this);
          var seq = $el.data("seq");
          var type = $el.data("type");
          if (seq && type) {
            selectProductName(String(seq), String(type));
          }
        }
      );

      $doc.off("change select2:select", ".js-operation-select, .js-operation-machine-select, .js-alternate-machine-select");
      $doc.on(
        "change select2:select",
        ".js-operation-select, .js-operation-machine-select, .js-alternate-machine-select",
        function () {
          var $el = jQuery(this);
          var seq = $el.data("seq");
          var type = $el.data("type");
          if (seq && type) {
            selectManufacturingOperations(String(seq), String(type));
          }
        }
      );

      $doc.off("blur", ".js-manufacturing-calc");
      $doc.on("blur", ".js-manufacturing-calc", function () {
        manufacturingCalculation();
      });

      $doc.off("blur", ".js-update-calculation");
      $doc.on("blur", ".js-update-calculation", function () {
        if (typeof window.updateCalculation === "function") {
          window.updateCalculation();
        }
      });

      $doc.off("click", ".js-remove-row");
      $doc.on("click", ".js-remove-row", function () {
        var targetId = jQuery(this).data("target");
        if (targetId) {
          jQuery("#" + targetId).remove();
          manufacturingCalculation();
          jQuery('[data-bs-toggle="tooltip"]').tooltip();
        }
      });

      $doc.off("click", ".js-add-bom-item");
      $doc.on("click", ".js-add-bom-item", function () {
        addMoreBomChild();
      });

      $doc.off("click", ".js-add-byitem");
      $doc.on("click", ".js-add-byitem", function () {
        addByItemMoreBomChild();
      });

      $doc.off("click", ".js-add-bom-operations");
      $doc.on("click", ".js-add-bom-operations", function () {
        addMoreBomOperations();
      });

      $doc.off("click", ".js-add-alternate-items");
      $doc.on("click", ".js-add-alternate-items", function () {
        var seq = jQuery(this).data("seq");
        if (seq) {
          addAlternateMoreBomChild(String(seq));
        }
      });

      $doc.off("click", ".js-add-alternate-operations");
      $doc.on("click", ".js-add-alternate-operations", function () {
        var seq = jQuery(this).data("seq");
        if (seq) {
          addAlternateMoreBomOperations(String(seq));
        }
      });

      $doc.off("click", ".js-open-alternate-items");
      $doc.on("click", ".js-open-alternate-items", function () {
        var seq = jQuery(this).data("seq");
        if (seq) {
          openAlternateItemsPopUp(String(seq));
        }
      });

      $doc.off("click", ".js-open-alternate-machine");
      $doc.on("click", ".js-open-alternate-machine", function () {
        var seq = jQuery(this).data("seq");
        if (seq) {
          openAlternateMachinePopUp(String(seq));
        }
      });

      $doc.off("click", ".js-close-popup");
      $doc.on("click", ".js-close-popup", function () {
        closeAlternateItemsPopUp();
      });
    });
  }

  window.addEventListener("load", showToastIfNeeded);
})();
