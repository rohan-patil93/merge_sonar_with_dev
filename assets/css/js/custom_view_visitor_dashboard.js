$(document).ready(function () {

    /* ---------- Location filter change ---------- */
    $(document).on('change', '.js-location-select', function () {
        const locationId = $(this).val();
        ShowLocationBased(locationId);
    });

    /* ---------- Navigation buttons ---------- */
    $(document).on('click', '.js-nav-btn', function () {
        const url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });

});
    function ShowLocationBased(location_id) {
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $(".DashboardMain").html(loader);
        document.querySelector(".DashboardMain").scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        $.ajax({
            url: 'views/custom_view_visitor_dashboard_main.php',
            type: 'GET',
            data: {
                key: 'AJAX_CALL_MAIN',
                location_id: location_id
            },
            success: function(response) {
                $('.DashboardMain').html(response);
            },
            error: function() {
                alert('Error fetching visitor data.');
            }
        });
    }