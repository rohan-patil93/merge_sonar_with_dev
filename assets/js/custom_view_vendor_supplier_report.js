// custom_view_vendor_supplier_report.js - Dynamic Vendor & Supplier Reconciliation

(function waitForJQuery(callback) {
    if (window.jQuery) { callback(window.jQuery); }
    else { setTimeout(function() { waitForJQuery(callback); }, 30); }
})(function($) {

    function showModal(id) {
        var el = document.getElementById(id);
        if (!el) return;
        if (window.bootstrap && window.bootstrap.Modal) {
            var instance = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
            instance.show();
        } else if (window.jQuery && jQuery.fn.modal) {
            $(el).modal('show');
        }
    }

    $(function() {
        // Perform AJAX search when filters are submitted
        function applyFilters() {
            var searchQuery = $('#vendor_search_query').val();
            var targetBox = $('.VendorSupplierTable');

            var loader = '<div class="loader-center">' +
                '<img src="assets/images/spiner/spiner3.gif" class="loader-lg" style="width: 120px;">' +
                '<p class="text-muted mt-3 font-weight-bold">Querying Vendor &amp; Supplier logs...</p>' +
                '</div>';

            targetBox.html(loader);

            $.ajax({
                url: 'views/custom_view_vendor_supplier_report_table.php',
                type: 'GET',
                data: {
                    search: searchQuery,
                    page: 'AJAX_CALL'
                },
                success: function (response) {
                    targetBox.html(response);
                },
                error: function (xhr) {
                    targetBox.html('<div class="alert alert-danger m-3"><i class="fa fa-exclamation-triangle"></i> Error fetching report data. (Status: ' + xhr.status + ')</div>');
                }
            });
        }

        // Query trigger on button click
        $(document).on('click', '.js-vendor-query-btn', function() {
            applyFilters();
        });

        // Search trigger on enter key
        $(document).on('keypress', '#vendor_search_query', function(e) {
            if (e.which === 13) {
                applyFilters();
            }
        });

        // Trigger on click of details
        $(document).on('click', '.js-view-vendor-details', function() {
            var vendorId = $(this).data('vendor-id');
            if (!vendorId) return;

            $('#vendorDetailsBody').html(
                '<div class="text-center py-5">' +
                '<div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">' +
                '<span class="visually-hidden">Loading...</span></div>' +
                '<p class="text-muted mt-3 font-weight-bold">Compiling full reconciliation ledger and check-in timeline...</p></div>'
            );

            showModal('vendorSupplierDetailsModal');

            $.ajax({
                url: 'views/custom_view_vendor_supplier_details_ajax.php',
                type: 'GET',
                data: { vendor_id: vendorId },
                success: function(response) {
                    $('#vendorDetailsBody').html(response);
                },
                error: function() {
                    $('#vendorDetailsBody').html(
                        '<div class="alert alert-danger font-weight-bold">' +
                        '<i class="fa fa-exclamation-triangle"></i> Failed to retrieve detailed history. Please try again.</div>'
                    );
                }
            });
        });
    });

});
