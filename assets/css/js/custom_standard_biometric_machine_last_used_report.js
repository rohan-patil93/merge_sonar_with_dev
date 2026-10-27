(function($) {
    $(function() {
        var $machineFilter = $('.js-biometric-machine-filter');
        if ($machineFilter.length > 0 && typeof $machineFilter.select2 === 'function') {
            $machineFilter.select2({
                width: '100%'
            });
        }
    });
})(jQuery);