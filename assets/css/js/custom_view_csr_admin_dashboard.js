    document.addEventListener("DOMContentLoaded", function() {
        var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".csr-click").forEach(function (el) {
        el.addEventListener("click", function () {

            var title = this.getAttribute("data-title");
            var count = this.getAttribute("data-count");
            var type  = this.getAttribute("data-type");
            var year  = this.getAttribute("data-year");

            if (year) {
                loadCSRList(title, count, type, year);
            } else {
                loadCSRList(title, count, type);
            }
        });
    });
});

    function loadCSRList(title, count, status, year = null) {

        $.ajax({
            url: 'views/csr_list_popup.php',
            type: 'POST',
            data: {
                title: title,
                count: count,
                status: status,
                year: year
            },
            success: function(response) {
                $('#dynamic_popup1').html(response);
                $('#kt_modal_dynamic_popup').modal("show");
            },
            error: function(xhr) {
                alert('Failed to load data');
            }
        });
    }


// It adds a search functionality to filter the CSR list based on user input.

function enableTableSearch(inputSelector, tableSelector) {
    const searchInput = document.querySelector(inputSelector);
    const table = document.querySelector(tableSelector);
    if (!searchInput || !table) return;

    searchInput.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

// Usage for Recent CSRs table
document.addEventListener("DOMContentLoaded", function() {
    enableTableSearch('#searcing_recent', '#recent_csr_list .table');
    // You can call enableTableSearch for other tables as needed
});

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-status-label').forEach(function (el) {
        const color = el.getAttribute('data-color');
        const bg = el.getAttribute('data-bg');

        if (color) el.style.color = color;
        if (bg) el.style.backgroundColor = bg;
    });
});
