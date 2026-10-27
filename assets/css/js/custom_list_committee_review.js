// Bootstrap tooltip init
document.addEventListener("DOMContentLoaded", function () {
    if (typeof bootstrap !== "undefined") {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
            new bootstrap.Tooltip(el);
        });
    }
});
// status label color binding
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-status-label').forEach(function (el) {
        const color = el.getAttribute('data-color');
        const bg = el.getAttribute('data-bg');

        if (color) el.style.color = color;
        if (bg) el.style.backgroundColor = bg;
    });
});
// CSR card click handler
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".csr-click").forEach(function (el) {
        el.addEventListener("click", function () {

            loadCSRList(
                this.dataset.title,
                this.dataset.count,
                this.dataset.status,
                this.dataset.userid,
                this.dataset.currentStatus
            );

        });
    });
});

// AJAX function (same logic, moved)
function loadCSRList(title, count, status, userid, current_status) {
    $.ajax({
        url: 'views/csr_list_popup.php',
        type: 'POST',
        data: {
            title: title,
            count: count,
            status: status,
            user_id: userid,
            current_status: current_status
        },
        success: function (response) {
            $('#dynamic_popup1').html(response);
            $('#kt_modal_dynamic_popup').modal("show");
        },
        error: function () {
            alert('Failed to load data');
        }
    });
}
