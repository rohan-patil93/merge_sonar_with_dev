document.addEventListener("DOMContentLoaded", function () {
    const fromDate = document.querySelector('.csr-from-date');
    const toDate = document.querySelector('.csr-to-date');

    if (fromDate && toDate) {
        fromDate.addEventListener('change', function () {
            toDate.min = this.value;
        });
    }
});

document.addEventListener("error", function (e) {
    if (e.target.tagName === "IMG" && e.target.classList.contains('csr-user-img')) {
        e.target.src = "uploads/all/no-image.jpg";
    }
}, true);

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.csr-status-label').forEach(el => {
        const color = el.dataset.color;
        const bg = el.dataset.bg;

        if (color) el.style.color = color;
        if (bg) el.style.backgroundColor = bg;
    });
});
