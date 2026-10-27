// custom_view_visitor_work_permit_pass.js
$(document).on('click', '.js-print-workpermit', function () {
    const targetId = $(this).data('print-target');
    const el = document.getElementById(targetId);
    if (!el) return;

    const printContents = el.innerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
});
