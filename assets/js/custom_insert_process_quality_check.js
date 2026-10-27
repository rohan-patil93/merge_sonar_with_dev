(function () {
  var state = {
    pageNo: 1,
    selectedProgressId: "",
    rows: [],
    item: null,
    searchTimer: null
  };

  var statusLabels = {
    "0": "Pending",
    "1": "In Progress",
    "2": "Passed",
    "3": "Failed"
  };

  function escapeHtml(value) {
    return String(value || "").replace(/[&<>"']/g, function (char) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#039;" }[char];
    });
  }

  function selectedTransaction() {
    var option = jQuery("#process_qc_transaction option:selected");
    return {
      tranid: option.val() || "",
      itemId: option.data("item-id") || "",
      itemCatId: option.data("item-cat-id") || "",
      itemName: option.data("item-name") || ""
    };
  }

  function showStatus(html, type) {
    var className = type === "error" ? "alert-danger" : type === "warning" ? "alert-warning" : "alert-info";
    jQuery("#process_qc_status").html('<div class="alert ' + className + ' mb-0">' + html + '</div>');
  }

  function notifyOk(message) {
    if (typeof tostarnotify === "function") {
      tostarnotify("top", "right", "fa fa-thumbs-up", "form-label label label-success", "animated flipInX", "animated flipOutX", message, "");
    }
  }

  function notifyError(message) {
    if (typeof swal === "function") {
      swal("Error", message, "error");
    } else {
      alert(message);
    }
  }

  function loadTransactions() {
    var process = jQuery("#process_qc_process").val();
    state.pageNo = 1;
    state.selectedProgressId = "";
    jQuery("#process_qc_open_btn").prop("disabled", true);
    jQuery("#process_qc_transaction").html('<option value="">Loading...</option>');
    jQuery("#process_qc_status").html("");
    jQuery("#process_qc_workspace").hide();
    jQuery("#process_qc_form_panel").html('<div class="process-qc-empty">Select a serial/lot row to start QC.</div>');

    if (!process) {
      jQuery("#process_qc_transaction").html('<option value="">Select process first</option>');
      return;
    }

    jQuery.ajax({
      url: "ajax.php",
      type: "POST",
      dataType: "json",
      data: { page: "getprocessqctransactions", process: process },
      success: function (res) {
        var rows = res.data || [];
        var html = '<option value="">Select Any One</option>';
        if (res.status === "error") {
          showStatus(res.message || "Unable to load transactions.", "error");
        }
        jQuery.each(rows, function (_, row) {
          var qcText = row.summary_text ? " - " + row.summary_text : " - Pending QC";
          var label = (row.transaction_no || row.transaction_id) + " - " + (row.item_name || "") + qcText;
          html += '<option value="' + escapeHtml(row.transaction_id) + '" data-item-id="' + escapeHtml(row.item_id) + '" data-item-cat-id="' + escapeHtml(row.item_cat_id) + '" data-item-name="' + escapeHtml(row.item_name || "") + '">' + escapeHtml(label) + '</option>';
        });
        jQuery("#process_qc_transaction").html(html);
        if (!rows.length && res.status !== "error") {
          showStatus("No QC required transactions found for this process.", "warning");
        }
      },
      error: function () {
        jQuery("#process_qc_transaction").html('<option value="">Select Any One</option>');
        showStatus("Unable to load transactions.", "error");
      }
    });
  }

  function checkMapping() {
    var process = jQuery("#process_qc_process").val();
    var tx = selectedTransaction();
    state.pageNo = 1;
    state.selectedProgressId = "";
    jQuery("#process_qc_open_btn").prop("disabled", true);
    jQuery("#process_qc_workspace").hide();
    jQuery("#process_qc_status").html("");
    if (!process || !tx.tranid || !tx.itemId) {
      return;
    }

    jQuery.ajax({
      url: "ajax.php",
      type: "POST",
      dataType: "json",
      data: { page: "getprocessqcmapping", process: process, tranid: tx.tranid, item_id: tx.itemId },
      success: function (res) {
        if (res.status === "error") {
          showStatus(res.message || "Unable to check QC mapping.", "error");
          return;
        }
        if (!res.has_mapping) {
          var setupUrl = "index.php?option=COM_INSERT_ITEM_QUALITY_PARAMETERS_MAPPING&ITEMID=" + encodeURIComponent(tx.itemId) + "&CATID=" + encodeURIComponent(tx.itemCatId || res.item_cat_id || 0) + "&PROCESS=" + encodeURIComponent(process);
          showStatus('No QC mapping is available for this item/category/process. <a class="btn btn-sm btn-primary ms-2" href="' + setupUrl + '">Apply QC Check</a>', "warning");
          return;
        }
        var trackingCount = (res.serial_rows || []).length + (res.lot_rows || []).length;
        var trackingText = trackingCount > 0 ? trackingCount + " serial/lot rows found." : "No serial/lot rows found, QC will apply once for the item.";
        showStatus("QC mapping found. " + trackingText, "info");
        jQuery("#process_qc_open_btn").prop("disabled", false);
      },
      error: function () {
        showStatus("Unable to check QC mapping.", "error");
      }
    });
  }

  function workspaceRequest(extra) {
    var tx = selectedTransaction();
    return jQuery.extend({
      page: "getprocessqcworkspace",
      process: jQuery("#process_qc_process").val(),
      tranid: tx.tranid,
      item_id: tx.itemId,
      status: jQuery("#process_qc_status_filter").val() || "all",
      search: jQuery("#process_qc_search").val() || "",
      page_no: state.pageNo,
      limit: jQuery("#process_qc_limit").val() || 25
    }, extra || {});
  }

  function updateSummary(summary) {
    summary = summary || {};
    jQuery("#pqc_total").text(summary.total || 0);
    jQuery("#pqc_passed").text(summary.passed || 0);
    jQuery("#pqc_failed").text(summary.failed || 0);
    jQuery("#pqc_draft").text(summary.draft || 0);
    jQuery("#pqc_pending").text(summary.pending || 0);
  }

  function renderItems(res) {
    state.rows = res.rows || [];
    var html = "";
    if (!state.rows.length) {
      html = '<div class="process-qc-empty">No rows found for this filter.</div>';
    }
    var grouped = {};
    jQuery.each(state.rows, function (_, row) {
      var groupKey = String(row.item_id || "item");
      if (!grouped[groupKey]) {
        grouped[groupKey] = {
          itemName: row.item_name || (state.item ? state.item.item_name : "") || "Item",
          sku: row.sku || (state.item ? state.item.sku : "") || "",
          rows: [],
          counts: { total: 0, pending: 0, draft: 0, passed: 0, failed: 0 }
        };
      }
      grouped[groupKey].rows.push(row);
      grouped[groupKey].counts.total++;
      var st = String(row.qc_status || 0);
      if (st === "0") grouped[groupKey].counts.pending++;
      if (st === "1") grouped[groupKey].counts.draft++;
      if (st === "2") grouped[groupKey].counts.passed++;
      if (st === "3") grouped[groupKey].counts.failed++;
    });
    jQuery.each(grouped, function (_, group) {
      html += '<div class="process-qc-product">';
      html += '<div class="process-qc-product-head">';
      html += '<div><div class="process-qc-product-title">' + escapeHtml(group.itemName) + '</div>';
      html += '<div class="process-qc-item-meta">SKU: ' + escapeHtml(group.sku || "-") + '</div></div>';
      html += '<div class="process-qc-product-counts">';
      html += '<span class="process-qc-count-chip process-qc-count-total"><strong>' + escapeHtml(group.counts.total) + '</strong> Total</span>';
      html += '<span class="process-qc-count-chip process-qc-count-passed"><strong>' + escapeHtml(group.counts.passed) + '</strong> Passed</span>';
      html += '<span class="process-qc-count-chip process-qc-count-failed"><strong>' + escapeHtml(group.counts.failed) + '</strong> Failed</span>';
      html += '<span class="process-qc-count-chip process-qc-count-draft"><strong>' + escapeHtml(group.counts.draft) + '</strong> Draft</span>';
      html += '<span class="process-qc-count-chip process-qc-count-pending"><strong>' + escapeHtml(group.counts.pending) + '</strong> Pending</span>';
      html += '</div>';
      html += '</div>';
      jQuery.each(group.rows, function (_, row) {
      var status = String(row.qc_status || 0);
        var lockedText = (status === "2" || status === "3") ? " | Locked" : "";
      html += '<button type="button" class="process-qc-item process-qc-child-item ' + (String(row.id) === String(state.selectedProgressId) ? "active" : "") + '" data-progress-id="' + escapeHtml(row.id) + '">';
      html += '<div class="d-flex justify-content-between gap-2">';
      html += '<div class="process-qc-item-title">' + escapeHtml(row.serial_lot_no || "Item QC") + '</div>';
      html += '<span class="process-qc-status-pill process-qc-status-' + status + '">' + escapeHtml(statusLabels[status] || "Pending") + '</span>';
      html += '</div>';
      html += '<div class="process-qc-item-meta">' + escapeHtml(row.serial_lot_type || "ITEM") + " | Checked " + escapeHtml(row.checked_count || 0) + "/" + escapeHtml(row.required_count || 0) + " | Failed " + escapeHtml(row.failed_count || 0) + '</div>';
      html += '<div class="process-qc-item-meta">Last saved: ' + escapeHtml(row.last_saved || "Not saved yet") + escapeHtml(lockedText) + '</div>';
      html += '</button>';
      });
      html += '</div>';
    });
    jQuery("#process_qc_items").html(html);
    jQuery("#process_qc_page_text").text("Page " + (res.page_no || state.pageNo) + " of " + (res.total_pages || 1));
    jQuery("#process_qc_prev").prop("disabled", state.pageNo <= 1);
    jQuery("#process_qc_next").prop("disabled", state.pageNo >= parseInt(res.total_pages || 1, 10));
  }

  function loadWorkspace(autoSelect) {
    var tx = selectedTransaction();
    if (!jQuery("#process_qc_process").val() || !tx.tranid || !tx.itemId) {
      return;
    }
    jQuery("#process_qc_workspace").show();
    jQuery("#process_qc_items").html('<div class="process-qc-empty">Loading rows...</div>');
    jQuery.ajax({
      url: "ajax.php",
      type: "POST",
      dataType: "json",
      data: workspaceRequest(),
      success: function (res) {
        if (res.status === "error") {
          showStatus(res.message || "Unable to load QC workspace.", "error");
          return;
        }
        state.item = res.item || null;
        updateSummary(res.summary);
        renderItems(res);
        if (autoSelect && state.rows.length) {
          loadItemForm(state.rows[0].id);
        }
      },
      error: function () {
        showStatus("Unable to load QC workspace.", "error");
      }
    });
  }

  function loadItemForm(progressId) {
    var tx = selectedTransaction();
    state.selectedProgressId = progressId;
    jQuery(".process-qc-item").removeClass("active");
    jQuery('.process-qc-item[data-progress-id="' + progressId + '"]').addClass("active");
    jQuery("#process_qc_form_panel").html('<div class="process-qc-empty">Loading QC form...</div>');
    jQuery.ajax({
      url: "ajax.php",
      type: "POST",
      dataType: "json",
      data: {
        page: "getprocessqcitemform",
        process: jQuery("#process_qc_process").val(),
        tranid: tx.tranid,
        item_id: tx.itemId,
        progress_id: progressId
      },
      success: function (res) {
        if (res.status === "error") {
          jQuery("#process_qc_form_panel").html('<div class="process-qc-empty text-danger">' + escapeHtml(res.message || "Unable to load QC form.") + '</div>');
          return;
        }
        renderForm(res);
      },
      error: function () {
        jQuery("#process_qc_form_panel").html('<div class="process-qc-empty text-danger">Unable to load QC form.</div>');
      }
    });
  }

  function optionList(raw) {
    if (!raw) {
      return [];
    }
    if (jQuery.isArray(raw)) {
      return jQuery.map(raw, function (value, key) { return { key: String(key), label: String(value) }; });
    }
    if (typeof raw === "object") {
      return jQuery.map(raw, function (value, key) { return { key: String(key), label: String(value) }; });
    }
    try {
      var parsed = JSON.parse(raw);
      if (jQuery.isArray(parsed)) {
        return jQuery.map(parsed, function (value, key) { return { key: String(key), label: String(value) }; });
      }
      if (typeof parsed === "object") {
        return jQuery.map(parsed, function (value, key) { return { key: String(key), label: String(value) }; });
      }
    } catch (e) {}
    return [];
  }

  function renderInput(param, value) {
    var displayType = String(param.display_type || "");
    var mandatory = parseInt(param.mand || 0, 10) === 1 ? "1" : "0";
    if (displayType === "3" || displayType === "4") {
      var target = parseInt(param.res || 0, 10) || 1;
      var labels = optionList(param.quality_parameters);
      var htmlScale = '<div class="process-qc-choice">';
      for (var i = 1; i <= 5; i++) {
        var label = displayType === "3" && labels[i - 1] ? labels[i - 1].label : String(i);
        var checked = String(value || "") === String(i) ? " checked" : "";
        htmlScale += '<label title="' + escapeHtml(label) + '"><input type="radio" class="pqc-rule-input" data-display-type="' + displayType + '" data-mandatory="' + mandatory + '" data-rule-res="' + escapeHtml(target) + '" name="item_attribute_res[' + escapeHtml(param.id) + ']" value="' + i + '"' + checked + '> ' + i + '</label>';
      }
      htmlScale += '</div><div class="text-muted small mt-1">Target: ' + escapeHtml(target) + ' or above</div><div class="small pqc-rule-hint mt-1"></div>';
      return htmlScale;
    }
    if (displayType === "5") {
      var spec = "";
      var min = "";
      var max = "";
      var rangeMode = String(param.res || "0");
      try {
        var parsedSpec = JSON.parse(param.quality_parameters || "");
        if (parsedSpec && typeof parsedSpec === "object") {
          min = parsedSpec.min || "";
          max = parsedSpec.max || "";
          if (parsedSpec.min || parsedSpec.max) {
            spec = "Range: " + (parsedSpec.min || "-") + " to " + (parsedSpec.max || "-");
          }
        }
      } catch (e) {
        spec = param.quality_parameters ? "Range: " + param.quality_parameters : "";
      }
      return '<input type="number" step="any" class="form-control pqc-rule-input" data-display-type="5" data-mandatory="' + mandatory + '" data-rule-res="' + escapeHtml(rangeMode) + '" data-min="' + escapeHtml(min) + '" data-max="' + escapeHtml(max) + '" min="' + escapeHtml(min) + '" max="' + escapeHtml(max) + '" name="item_attribute_res[' + escapeHtml(param.id) + ']" value="' + escapeHtml(value || "") + '">' + (spec ? '<div class="text-muted small mt-1">' + escapeHtml(spec) + (rangeMode === "1" ? " | Outside range is expected" : " | Inside range is expected") + '</div>' : "") + '<div class="small pqc-rule-hint mt-1"></div>';
    }
    var options = optionList(param.quality_parameters);
    var html = "";
    if (options.length) {
      html += '<div class="process-qc-choice">';
      jQuery.each(options, function (_, option) {
        var checked = (String(value || "") === String(option.key) || String(value || "") === String(option.label)) ? " checked" : "";
        html += '<label><input type="radio" class="pqc-rule-input" data-display-type="' + escapeHtml(displayType) + '" data-mandatory="' + mandatory + '" data-rule-res="' + escapeHtml(param.res || "0") + '" name="item_attribute_res[' + escapeHtml(param.id) + ']" value="' + escapeHtml(option.key) + '"' + checked + '> ' + escapeHtml(option.label) + '</label>';
      });
      html += '</div><div class="small pqc-rule-hint mt-1"></div>';
    } else {
      html += '<input type="text" class="form-control" name="item_attribute_res[' + escapeHtml(param.id) + ']" value="' + escapeHtml(value || "") + '">';
    }
    return html;
  }

  function refreshRuleHint(input) {
    var el = jQuery(input);
    var wrapper = el.closest(".process-qc-param-row");
    var hint = wrapper.find(".pqc-rule-hint").first();
    var displayType = String(el.data("display-type") || "");
    var expected = String(el.data("rule-res") || "0");
    var mandatory = String(el.data("mandatory") || "0") === "1";
    var value = displayType === "5" ? el.val() : wrapper.find(".pqc-rule-input:checked").val();
    if (!hint.length || value === undefined || value === "") {
      hint.text("").removeClass("text-success text-danger");
      return;
    }
    var passed = true;
    if (displayType === "1" || displayType === "2") {
      passed = String(value) === expected;
    } else if (displayType === "3" || displayType === "4") {
      passed = !isNaN(parseFloat(value)) && parseFloat(value) >= parseFloat(expected) && parseFloat(value) >= 1 && parseFloat(value) <= 5;
    } else if (displayType === "5") {
      var num = parseFloat(value);
      var min = parseFloat(el.data("min"));
      var max = parseFloat(el.data("max"));
      var inside = !isNaN(num) && !isNaN(min) && !isNaN(max) && num >= min && num <= max;
      passed = expected === "1" ? !inside : inside;
    }
    var message = "";
    if (passed) {
      message = mandatory ? "Mandatory result will pass." : "Observation is within rule.";
    } else {
      message = mandatory ? "Mandatory result will fail this QC. Remarks are required before completion." : "Optional observation is outside rule. It will be saved but will not fail final QC.";
    }
    hint.text(message)
      .toggleClass("text-success", passed)
      .toggleClass("text-danger", !passed);
  }

  function renderForm(res) {
    var progress = res.progress || {};
    var item = res.item || {};
    var groups = res.groups || [];
    var values = res.values || {};
    var remarks = res.remarks || {};
    var locked = parseInt(progress.qc_status || 0, 10) === 2 || parseInt(progress.qc_status || 0, 10) === 3;
    var html = "";
    html += '<div class="process-qc-form-head">';
    html += '<div class="d-flex justify-content-between gap-3 flex-wrap">';
    html += '<div><h5 class="mb-1">' + escapeHtml(progress.serial_lot_no || "Item QC") + '</h5><div class="text-muted small">' + escapeHtml(progress.serial_lot_type || "ITEM") + ' | ' + escapeHtml(item.item_name || "") + ' | SKU: ' + escapeHtml(item.sku || "") + '</div></div>';
    html += '<div><span class="process-qc-status-pill process-qc-status-' + escapeHtml(progress.qc_status || 0) + '">' + escapeHtml(statusLabels[String(progress.qc_status || 0)] || "Pending") + '</span></div>';
    html += '</div></div>';
    html += '<form id="process_qc_item_form" data-locked="' + (locked ? "1" : "0") + '">';
    html += '<input type="hidden" name="process" value="' + escapeHtml(jQuery("#process_qc_process").val()) + '">';
    html += '<input type="hidden" name="tranid" value="' + escapeHtml(selectedTransaction().tranid) + '">';
    html += '<input type="hidden" name="item_id" value="' + escapeHtml(selectedTransaction().itemId) + '">';
    html += '<input type="hidden" name="progress_id" value="' + escapeHtml(progress.id || "") + '">';
    html += '<div class="process-qc-form-body custom-scroll">';
    html += '<div class="' + (locked ? "event_disabled" : "") + '">';
    if (!groups.length) {
      html += '<div class="process-qc-empty">No QC parameters found.</div>';
    }
    jQuery.each(groups, function (_, group) {
      html += '<div class="process-qc-param-group">';
      html += '<div class="process-qc-param-group-title">' + escapeHtml(group.group_name || "QC Parameters") + '</div>';
      jQuery.each(group.parameters || [], function (idx, param) {
        html += '<div class="process-qc-param-row">';
        html += '<div class="fw-bold">' + escapeHtml(idx + 1) + '</div>';
        html += '<div><div class="fw-bold">' + escapeHtml(param.name || "") + (parseInt(param.mand || 0, 10) === 1 ? ' <span class="text-danger">*</span>' : "") + '</div></div>';
        html += '<div>' + renderInput(param, values[param.id]) + '</div>';
        html += '<div><textarea class="form-control" rows="2" name="item_attribute_remarks[' + escapeHtml(param.id) + ']" placeholder="Remarks">' + escapeHtml(remarks[param.id] || "") + '</textarea></div>';
        html += '</div>';
      });
      html += '</div>';
    });
    html += '</div></div>';
    html += '<div class="process-qc-actions">';
    html += '<button type="button" class="btn btn-light" id="process_qc_refresh_form">Refresh</button>';
    if (locked) {
      html += '<span class="process-qc-lock-text">Completed and locked</span>';
    } else {
      html += '<button type="button" class="btn btn-outline-primary" id="process_qc_save_draft">Save Draft</button>';
      html += '<button type="button" class="btn btn-primary" id="process_qc_complete">Complete QC</button>';
    }
    html += '</div></form>';
    jQuery("#process_qc_form_panel").html(html);
    jQuery("#process_qc_form_panel .pqc-rule-input:checked,#process_qc_form_panel input.pqc-rule-input[type='number']").each(function () {
      refreshRuleHint(this);
    });
  }

  function saveItem(mode) {
    var form = jQuery("#process_qc_item_form");
    if (!form.length) {
      return;
    }
    if (String(form.data("locked") || "0") === "1") {
      notifyError("QC is already completed and locked.");
      return;
    }
    var page = mode === "complete" ? "completeprocessqcitem" : "saveprocessqcitemdraft";
    jQuery.ajax({
      url: "ajax.php",
      type: "POST",
      dataType: "json",
      data: "page=" + page + "&" + form.serialize(),
      success: function (res) {
        if (res.status === "error") {
          notifyError(res.message || "Unable to save QC.");
          return;
        }
        notifyOk(res.message || "QC saved successfully.");
        updateSummary(res.summary);
        loadWorkspace(false);
        if (mode === "complete" && res.next_progress_id) {
          loadItemForm(res.next_progress_id);
        } else {
          loadItemForm(state.selectedProgressId);
        }
      },
      error: function () {
        notifyError("Unable to save QC.");
      }
    });
  }

  function nextPending() {
    var next = null;
    jQuery.each(state.rows, function (_, row) {
      if (parseInt(row.qc_status || 0, 10) === 0 || parseInt(row.qc_status || 0, 10) === 1) {
        next = row.id;
        return false;
      }
    });
    if (next) {
      loadItemForm(next);
      return;
    }
    jQuery("#process_qc_status_filter").val("0");
    state.pageNo = 1;
    loadWorkspace(true);
  }

  jQuery(document).ready(function () {
    jQuery(document).on("change", "#process_qc_process", loadTransactions);
    jQuery(document).on("change", "#process_qc_transaction", checkMapping);
    jQuery(document).on("click", "#process_qc_open_btn", function () { loadWorkspace(true); });
    jQuery(document).on("click", ".process-qc-item", function () { loadItemForm(jQuery(this).data("progress-id")); });
    jQuery(document).on("change", "#process_qc_status_filter,#process_qc_limit", function () { state.pageNo = 1; loadWorkspace(false); });
    jQuery(document).on("keyup", "#process_qc_search", function () {
      clearTimeout(state.searchTimer);
      state.searchTimer = setTimeout(function () { state.pageNo = 1; loadWorkspace(false); }, 300);
    });
    jQuery(document).on("click", "#process_qc_prev", function () { if (state.pageNo > 1) { state.pageNo--; loadWorkspace(false); } });
    jQuery(document).on("click", "#process_qc_next", function () { state.pageNo++; loadWorkspace(false); });
    jQuery(document).on("click", "#process_qc_next_pending", nextPending);
    jQuery(document).on("click", "#process_qc_save_draft", function () { saveItem("draft"); });
    jQuery(document).on("click", "#process_qc_complete", function () { saveItem("complete"); });
    jQuery(document).on("click", "#process_qc_refresh_form", function () { loadItemForm(state.selectedProgressId); });
    jQuery(document).on("change keyup", ".pqc-rule-input", function () { refreshRuleHint(this); });
    if (jQuery("#process_qc_process").val()) {
      loadTransactions();
    }
  });
})();
