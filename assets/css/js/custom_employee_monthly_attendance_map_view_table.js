(function () {
    'use strict';

    function loadScriptOnce(src) {
        return new Promise(function (resolve, reject) {
            if (document.querySelector('script[src="' + src + '"]')) {
                resolve();
                return;
            }
            var script = document.createElement('script');
            script.src = src;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function loadStyleOnce(href) {
        if (document.querySelector('link[href="' + href + '"]')) {
            return;
        }
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        document.head.appendChild(link);
    }

    function ensureOpenLayers() {
        if (window.ol) {
            return Promise.resolve();
        }
        loadStyleOnce('https://cdn.jsdelivr.net/npm/ol@9.0.0/ol.css');
        return loadScriptOnce('https://cdn.jsdelivr.net/npm/ol@9.0.0/dist/ol.js');
    }

    function showModal($modal) {
        var modalEl = $modal && $modal[0];
        if (window.bootstrap && window.bootstrap.Modal && modalEl) {
            if (typeof window.bootstrap.Modal.getOrCreateInstance === 'function') {
                window.bootstrap.Modal.getOrCreateInstance(modalEl).show();
                return;
            }
            try {
                new window.bootstrap.Modal(modalEl).show();
                return;
            } catch (err) {
            }
        }
        if (typeof $modal.modal === 'function') {
            $modal.modal('show');
        }
    }

    document.addEventListener('click', function (event) {
        var target = event.target.closest('.view-attendance-location');
        if (!target) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        var empId = target.getAttribute('data-emp-id');
        var date = target.getAttribute('data-date');
        var empName = target.getAttribute('data-emp-name') || '';

        if (!empId || !date) {
            return;
        }

        var $modal = $('#attendanceLocationModal');
        var $modalBody = $('#attendanceLocationModalBody');
        $modalBody.html('<div class="text-center p-4">Loading...</div>');
        showModal($modal);

        ensureOpenLayers()
            .then(function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        url: 'views/custom_employee_attendance_location_map_view.php',
                        type: 'POST',
                        data: {
                            employee_id: empId,
                            attendance_date: date,
                            emp_name: empName
                        },
                        success: function (html) {
                            resolve(html);
                        },
                        error: function () {
                            reject(new Error('Failed to load attendance map.'));
                        }
                    });
                });
            })
            .then(function (html) {
                $modalBody.html(html);
                if (window.renderAttendanceLocationMap) {
                    window.renderAttendanceLocationMap($modalBody[0]);
                }

                setTimeout(function () {
                    var mapEl = $modalBody[0].querySelector('#attendanceLocationMap');
                    if (mapEl && mapEl._olMap) {
                        mapEl._olMap.updateSize();
                    }
                }, 250);
            })
            .catch(function () {
                $modalBody.html('<div class="text-danger text-center p-4">Unable to load map data.</div>');
            });
    });
})();
