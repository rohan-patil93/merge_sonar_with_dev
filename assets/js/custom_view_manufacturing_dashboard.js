(function () {
  /**
   * Initialize page data from data attributes (CSP-compliant)
   */
  function initializePageData() {
    var pageRoot = document.querySelector(".page-body");
    if (!pageRoot) {
      console.warn("⚠️ Page root element not found");
      return;
    }

    // Parse chart data from data attribute
    var chartData = pageRoot.dataset.chartData
      ? JSON.parse(pageRoot.dataset.chartData)
      : [];

    // Populate window variables for use in external code
    window.lineChartData = chartData;
  }

  /**
   * Handle work order row clicks
   */
  function handleWorkOrderClick(event) {
    var row = event.currentTarget;
    var woData = row.dataset.workorder;

    if (!woData) {
      console.warn("⚠️ No work order data found");
      return;
    }

    try {
      var wo = JSON.parse(woData);

      // Update modal title
      var modalTitle = document.querySelector("#order-number .modal-title");
      if (modalTitle) {
        modalTitle.textContent = "Work Order Details";
      }

      // Update work order heading
      var woHeading = document.querySelector("#order-number h5.text-primary");
      if (woHeading) {
        woHeading.textContent = `${wo.transaction_no} - ${wo.product_name}`;
      }

      // Update info table
      var infoTable = document.querySelector(
        "#order-number .table.table-sm"
      );
      if (infoTable) {
        infoTable.innerHTML = `
          <tr>
            <th>Line:</th><td>${wo.work_order_id || "-"}</td>
            <th>Planned Qty:</th><td>${wo.planned_qty || 0}</td>
          </tr>
        `;
      }

      // Parse and display materials
      var materials = [];
      try {
        materials = JSON.parse(wo.materials || "[]");
      } catch (e) {
        materials = [];
      }

      var matList = document.querySelector(
        "#order-number .col-lg-6:first-child ul"
      );
      if (matList) {
        if (materials.length > 0) {
          matList.innerHTML = materials
            .map(function (m) {
              return `
            <li>
              <i class="icofont icofont-double-right text-success"></i>
              <strong>${m.item_name ? m.item_name : "Item #" + m.item_id}</strong>
              — Quantity ${m.request_qty || 0} ${m.uom || ""}
            </li>
          `;
            })
            .join("");
        } else {
          matList.innerHTML = "<li>No materials listed</li>";
        }
      }

      // Parse and display operations
      var operations = [];
      try {
        operations = JSON.parse(wo.operations || "[]");
      } catch (e) {
        operations = [];
      }

      var opList = document.querySelector(
        "#order-number .col-lg-6:last-child ul"
      );
      if (opList) {
        if (operations.length > 0) {
          opList.innerHTML = operations
            .map(function (o) {
              return `
            <li>
              <i class="icofont icofont-tools-alt-2 text-primary"></i>
              ${o.operation_name || "Operation #" + o.operation_id}
              — ${o.operation_time || 0} mins, manpower: ${o.operation_manpower || 0}
            </li>
          `;
            })
            .join("");
        } else {
          opList.innerHTML = "<li>No operations recorded</li>";
        }
      }
    } catch (error) {
      console.error("❌ Error parsing work order data:", error);
    }
  }

  /**
   * Initialize event handlers
   */
  function initializeHandlers() {
    // Work order row click handlers
    var rows = document.querySelectorAll(".work-order-row");
    rows.forEach(function (row) {
      row.removeEventListener("click", handleWorkOrderClick);
      row.addEventListener("click", handleWorkOrderClick);
    });
  }

  /**
   * Initialize on page load
   */
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", function () {
      initializePageData();
      initializeHandlers();
    });
  } else {
    // DOM is already loaded
    initializePageData();
    initializeHandlers();
  }

  // Export for backward compatibility
  window.handleWorkOrderClick = handleWorkOrderClick;
  window.initializePageData = initializePageData;
})();
