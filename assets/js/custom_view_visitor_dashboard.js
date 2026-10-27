(function waitForJQuery(callback) {
    if (window.jQuery) {
        callback(window.jQuery);
    } else {
        setTimeout(function () { waitForJQuery(callback); }, 30);
    }
})(function ($) {
    $(document).ready(function () {
        $(document).on('click change', '.getvisitorsdata', function (e) {
            //  Block incorrect event-type per element
            if (this.tagName === 'SELECT' && e.type !== 'change') return;
            if (this.tagName === 'BUTTON' && e.type !== 'click') return;
            const el = this;
            const $el = $(el);

            const period_key = $el.data('period_key');
            const location_id = $el.data('location_id');
            const offset = Number($el.data('offset')) || 0;
            const tab = $el.data('tab');

            let limit, q;
            // LIMIT
            const limitData = $el.data('limit');

            if (typeof limitData === 'number') {
                // Case 3: Prev / Next (direct value)
                limit = limitData;
            } else if (el.tagName === 'SELECT') {
                // Case 2: limit dropdown
                limit = el.value;
            } else if (typeof limitData === 'string') {
                // Case 1: Search (input ID)
                limit = document.getElementById(limitData)?.value || 0;
            }

            // QUERY
            const qData = $el.data('q');

            if (typeof qData === 'string' && document.getElementById(qData)) {
                // input ID
                q = document.getElementById(qData).value || '';
            } else {
                // direct value
                q = qData || '';
            }

            GetVisitorsData(period_key, location_id, offset, limit, q, tab);
        });


        $(document).on('click', '.openbucketmodal', function () {
            const period = $(this).data('period');
            const type = $(this).data('type');
            const bucket = $(this).data('bucket');
            const location_id = $(this).data('location_id');
            openBucketModal(period, type, bucket, location_id);
        });
        $(document).on('click', '.js-checkout', function (e) {
            e.preventDefault();

            const el = this;
            const $el = $(el);

            const url = $el.data('url');
            const target = $el.data('target') || '_self';

            if (!url) return;

            if (target === '_blank') {
                window.open(url, '_blank', 'noopener');
            } else {
                window.location.href = url;
            }
        });


    });

    function GetVisitorsData(day_type, location_id, offset, limit, q, tab = 'ALL') {
        // defaults to keep backwards compatibility (when only day_type is passed)
        if (typeof offset === 'undefined' || offset === null) offset = 0;
        if (typeof limit === 'undefined' || limit === null) limit = 10;
        if (typeof q === 'undefined' || q === null) q = '';

        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $(".DayWiseVisitors").html(loader);

        document.querySelector(".DayWiseVisitors").scrollIntoView({ behavior: 'smooth', block: 'start' });

        $.ajax({
            url: 'views/custom_view_visitor_dashboard_table.php',
            type: 'GET',
            data: {
                page: 'AJAX_CALL',
                day_type: day_type,
                location_id: location_id,
                offset: offset,
                limit: limit,
                q: q,
                tab: tab
            },
            success: function (response) {
                $('.DayWiseVisitors').html(response);
            },
            error: function () {
                alert('Error fetching visitor data.');
            }
        });
    }

    function openBucketModal(period, type, bucket, location_id) {
        var url = 'views/visitors_bucket_modal.php' +
            '?period=' + encodeURIComponent(period) +
            '&type=' + encodeURIComponent(type) +
            '&bucket=' + encodeURIComponent(bucket) +
            '&location_id=' + encodeURIComponent(location_id);

        // show a quick loading state
        var $content = $('#bucketModal .modal-content');
        $content.html(
            '<div class="modal-header py-2">' +
            '<h6 class="modal-title">Loading…</h6>' +
            '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>' +
            '</div>' +
            '<div class="modal-body py-4 text-center text-muted">Fetching records…</div>'
        );

        // load server-rendered HTML (Core PHP builds the table)
        $content.load(url, function () {
            // when the HTML is in, show the modal
            var modal = new bootstrap.Modal(document.getElementById('bucketModal'));
            modal.show();
        });
    }
});