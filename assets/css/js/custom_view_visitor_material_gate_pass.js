// custom_view_visitor_material_gate_pass.js
$(document).on('click', '.js-print-gatepass', function () {
    const targetId = $(this).data('print-target');
    const el = document.getElementById(targetId);
    if (!el) return;

    const printContents = el.innerHTML;
    const originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload(); // ensure scripts/styles restore cleanly
});
