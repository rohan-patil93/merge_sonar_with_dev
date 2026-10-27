document.addEventListener("DOMContentLoaded", function() {
	var tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
	tooltipTriggerList.forEach(function(tooltipTriggerEl) {
		new bootstrap.Tooltip(tooltipTriggerEl);
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
                this.dataset.userid
            );
        });
    });
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

// image fallback (replaces inline onerror)
document.addEventListener("error", function (e) {
    if (e.target.tagName === "IMG") {
        e.target.src = "uploads/all/no-image.jpg";
    }
}, true);

// AJAX function (same logic, moved)
function loadCSRList(title, count, status, userid) {
    $.ajax({
        url: 'views/csr_list_popup.php',
        type: 'POST',
        data: {
            title: title,
            count: count,
            status: status,
            user_id: userid
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
