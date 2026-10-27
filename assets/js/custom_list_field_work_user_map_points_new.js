const fwdpDefaultCenter = { lat: 22.836946, lng: 78.354492 };
const fwdpDefaultZoom = 5;

let fwdpMap = null;
let fwdpMarkers = [];
let fwdpPolyline = null;
let fwdpInfoWindow = null;
let fwdpPendingInit = null;
let fwdpDirectionsService = null;
let fwdpDirectionsRenderer = null;
let fwdpRouteRequestToken = 0;

function toast(msg) {
    tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
        'animated flipInX', 'animated flipOutX', msg, '');
}

function fwdpShowErr(id, msg) {
    const $el = $('#' + id);
    if ($el.length == 0) {
        return;
    }
    $el.text(msg).removeClass('d-none');
}

function fwdpHideErr(id) {
    const $el = $('#' + id);
    if ($el.length == 0) {
        return;
    }
    $el.text('').addClass('d-none');
}

function fwdpFilterIds(val) {
    if (val == null || val == '') {
        return [];
    }
    if (!$.isArray(val)) {
        val = [val];
    }
    return val.filter(function (id) {
        return id != null && id != '' && id != '0';
    });
}

function handleFromDateChange() {
    var fromDateInput = document.getElementById("from_date");
    var toDateInput = document.getElementById("to_date");

    toDateInput.min = fromDateInput.value;
    if (toDateInput.value && fromDateInput.value && toDateInput.value < fromDateInput.value) {
        toDateInput.value = fromDateInput.value;
    }
}

function fwdpEscapeHtml(value) {
    return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function fwdpPinIcon(color) {
    const svg =
        '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="42" viewBox="0 0 32 42">' +
        '<path fill="' + color + '" stroke="#fff" stroke-width="2" d="M16 1C8.3 1 2 7.3 2 15c0 10.5 14 25 14 25s14-14.5 14-25C30 7.3 23.7 1 16 1z"/>' +
        '<circle cx="16" cy="15" r="5" fill="#fff"/>' +
        '</svg>';
    return {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
        scaledSize: new google.maps.Size(32, 42),
        anchor: new google.maps.Point(16, 42)
    };
}

function fwdpClearMapLayers() {
    fwdpRouteRequestToken++;
    fwdpMarkers.forEach(function (marker) {
        marker.setMap(null);
    });
    fwdpMarkers = [];
    if (fwdpPolyline) {
        fwdpPolyline.setMap(null);
        fwdpPolyline = null;
    }
    if (fwdpDirectionsRenderer) {
        try {
            fwdpDirectionsRenderer.set('directions', null);
        } catch (e) {
            // ignore clear errors on older Maps builds
        }
        fwdpDirectionsRenderer.setMap(null);
    }
    if (fwdpInfoWindow) {
        fwdpInfoWindow.close();
    }
    $('#fwdpMapDistance').hide().text('');
    $('#fwdpRouteSummary').hide().html('');
    $('#fwdpNavActions').hide();
    $('#fwdpSelectedAddress').text('Select a row and open the map to view visit points.');
}

function fwdpEnsureMap() {
    if (!window.fwdpGoogleMapsKeyConfigured || typeof google == 'undefined' || !google.maps) {
        $('#fwdpMapEmpty').text('Map key not configured.').show();
        toast('Map key not configured.');
        return null;
    }
    $('#fwdpMapEmpty').hide();

    if (!fwdpMap) {
        fwdpMap = new google.maps.Map(document.getElementById('fwdpMap'), {
            center: fwdpDefaultCenter,
            zoom: fwdpDefaultZoom,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });
        fwdpInfoWindow = new google.maps.InfoWindow();
    }
    return fwdpMap;
}

function fwdpFitBounds(points) {
    if (!fwdpMap || !points || points.length == 0) {
        return;
    }
    const bounds = new google.maps.LatLngBounds();
    points.forEach(function (point) {
        bounds.extend(point);
    });
    fwdpMap.fitBounds(bounds, 40);
    if (points.length == 1) {
        fwdpMap.setZoom(14);
    }
}

function fwdpBuildGoogleMapsNavUrl(points) {
    if (!points || points.length == 0) {
        return '#';
    }
    const origin = points[0].lat + ',' + points[0].lng;
    const destination = points[points.length - 1].lat + ',' + points[points.length - 1].lng;
    let url = 'https://www.google.com/maps/dir/?api=1&origin=' + encodeURIComponent(origin) +
        '&destination=' + encodeURIComponent(destination) + '&travelmode=driving';
    if (points.length > 2) {
        const waypoints = points.slice(1, points.length - 1).map(function (p) {
            return p.lat + ',' + p.lng;
        }).join('|');
        url += '&waypoints=' + encodeURIComponent(waypoints);
    }
    return url;
}

function fwdpEnsureDirections() {
    if (!fwdpDirectionsService) {
        fwdpDirectionsService = new google.maps.DirectionsService();
    }
    if (!fwdpDirectionsRenderer) {
        fwdpDirectionsRenderer = new google.maps.DirectionsRenderer({
            suppressMarkers: true,
            preserveViewport: false,
            polylineOptions: {
                strokeColor: '#10b981',
                strokeOpacity: 0.95,
                strokeWeight: 5
            }
        });
    }
}

function fwdpShowVisitedNavActions(visitedPoints) {
    $('#fwdpOpenGoogleNav').attr('href', fwdpBuildGoogleMapsNavUrl(visitedPoints));
    $('#fwdpNavActions').show();
}

function fwdpRenderStraightVisitedSummary(visitedPoints) {
    if (!visitedPoints || visitedPoints.length == 0) {
        $('#fwdpSelectedAddress').text('No visited points found.');
        return;
    }

    let totalDistance = 0;
    let summaryHtml = '<div class="fwdp-route-legs">';

    if (visitedPoints.length > 1 && google.maps.geometry) {
        for (let i = 0; i < visitedPoints.length - 1; i++) {
            const from = new google.maps.LatLng(visitedPoints[i].lat, visitedPoints[i].lng);
            const to = new google.maps.LatLng(visitedPoints[i + 1].lat, visitedPoints[i + 1].lng);
            const segmentMeters = google.maps.geometry.spherical.computeDistanceBetween(from, to);
            totalDistance += segmentMeters;
            const segmentKm = (segmentMeters / 1000).toFixed(2);
            summaryHtml +=
                '<div class="fwdp-route-leg">' +
                '<span class="fwdp-route-leg-label">' + (i + 1) + ' → ' + (i + 2) + '</span>' +
                '<span class="fwdp-route-leg-value">' + segmentKm + ' km</span>' +
                '</div>';
        }
        summaryHtml += '</div>';
        $('#fwdpRouteSummary').html(summaryHtml).show();
        $('#fwdpMapDistance').text('Total (straight): ' + (totalDistance / 1000).toFixed(2) + ' km').show();
        $('#fwdpSelectedAddress').text(visitedPoints.length + ' visited location(s) · straight-line route');
    } else {
        summaryHtml += '</div>';
        $('#fwdpRouteSummary').hide().html('');
        $('#fwdpMapDistance').hide();
        $('#fwdpSelectedAddress').text(visitedPoints.length + ' visited location(s)');
    }

    fwdpShowVisitedNavActions(visitedPoints);
}

function fwdpDrawStraightVisitedRoute(visitedPoints) {
    if (fwdpPolyline) {
        fwdpPolyline.setMap(null);
        fwdpPolyline = null;
    }
    if (fwdpDirectionsRenderer) {
        try {
            fwdpDirectionsRenderer.set('directions', null);
        } catch (e) {
            // ignore
        }
        fwdpDirectionsRenderer.setMap(null);
    }

    if (visitedPoints.length > 1) {
        fwdpPolyline = new google.maps.Polyline({
            path: visitedPoints,
            geodesic: true,
            strokeColor: '#10b981',
            strokeOpacity: 0.9,
            strokeWeight: 4,
            map: fwdpMap
        });
    }

    fwdpRenderStraightVisitedSummary(visitedPoints);
}

function fwdpRenderDrivingVisitedRoute(visitedPoints) {
    if (!visitedPoints || visitedPoints.length < 2) {
        fwdpRenderStraightVisitedSummary(visitedPoints);
        return;
    }

    if (typeof google == 'undefined' || !google.maps || !google.maps.DirectionsService) {
        toast('Could not load driving route. Showing straight-line path.');
        fwdpDrawStraightVisitedRoute(visitedPoints);
        return;
    }

    fwdpEnsureDirections();

    if (fwdpPolyline) {
        fwdpPolyline.setMap(null);
        fwdpPolyline = null;
    }

    fwdpDirectionsRenderer.setMap(fwdpMap);

    const requestToken = fwdpRouteRequestToken;
    const origin = visitedPoints[0];
    const destination = visitedPoints[visitedPoints.length - 1];
    const waypoints = [];
    for (let i = 1; i < visitedPoints.length - 1; i++) {
        waypoints.push({
            location: visitedPoints[i],
            stopover: true
        });
    }

    $('#fwdpRouteSummary').empty().show();
    $('#fwdpMapDistance').hide();
    $('#fwdpSelectedAddress').text('Calculating driving route...');
    fwdpShowVisitedNavActions(visitedPoints);

    fwdpDirectionsService.route({
        origin: origin,
        destination: destination,
        waypoints: waypoints,
        travelMode: google.maps.TravelMode.DRIVING,
        optimizeWaypoints: false
    }, function (result, status) {
        if (requestToken != fwdpRouteRequestToken) {
            return;
        }

        if (status != google.maps.DirectionsStatus.OK || !result || !result.routes || !result.routes.length) {
            toast('Could not load driving route. Showing straight-line path.');
            fwdpDrawStraightVisitedRoute(visitedPoints);
            return;
        }

        fwdpDirectionsRenderer.setDirections(result);

        const route = result.routes[0];
        const legs = route.legs || [];
        let totalMeters = 0;
        let summaryHtml = '<div class="fwdp-route-legs">';

        for (let i = 0; i < legs.length; i++) {
            const leg = legs[i];
            const meters = (leg.distance && leg.distance.value) ? leg.distance.value : 0;
            totalMeters += meters;
            const distText = (leg.distance && leg.distance.text) ? leg.distance.text : ((meters / 1000).toFixed(2) + ' km');
            const durText = (leg.duration && leg.duration.text) ? leg.duration.text : '';
            const valueText = durText != '' ? (distText + ' · ' + durText) : distText;

            summaryHtml +=
                '<div class="fwdp-route-leg">' +
                '<span class="fwdp-route-leg-label">' + (i + 1) + ' → ' + (i + 2) + '</span>' +
                '<span class="fwdp-route-leg-value">' + fwdpEscapeHtml(valueText) + '</span>' +
                '</div>';
        }

        summaryHtml += '</div>';
        $('#fwdpRouteSummary').html(summaryHtml).show();
        $('#fwdpMapDistance').text('Total (driving): ' + (totalMeters / 1000).toFixed(2) + ' km').show();
        $('#fwdpSelectedAddress').text(visitedPoints.length + ' visited location(s) · driving route');
        fwdpShowVisitedNavActions(visitedPoints);
    });
}

function fwdpRenderRouteSummary(visitedPoints) {
    fwdpRenderStraightVisitedSummary(visitedPoints);
}

function fwdpHasMatchedPlanId(planId) {
    return planId != null && planId != '' && planId != '0' && planId != 0;
}

function fwdpRenderMapCountsSummary(visitedCount, extraCount, pendingCount, navPoints) {
    const parts = [];
    parts.push(visitedCount + ' visited');
    if (extraCount > 0) {
        parts.push(extraCount + ' extra');
    }
    if (pendingCount > 0) {
        parts.push(pendingCount + ' not visited');
    }
    $('#fwdpSelectedAddress').text(parts.join(' · '));
    $('#fwdpRouteSummary').hide().html('');
    $('#fwdpMapDistance').hide().text('');

    if (navPoints && navPoints.length > 0) {
        fwdpShowVisitedNavActions(navPoints);
    } else {
        $('#fwdpNavActions').hide();
    }
}

function fwdpAddMarker(lat, lng, icon, popupHtml) {
    const marker = new google.maps.Marker({
        position: { lat: parseFloat(lat), lng: parseFloat(lng) },
        map: fwdpMap,
        icon: icon
    });
    marker.addListener('click', function () {
        fwdpInfoWindow.setContent(popupHtml);
        fwdpInfoWindow.open(fwdpMap, marker);
    });
    fwdpMarkers.push(marker);
    return marker;
}

function initializeMap(assignedLocationsObj, visitedLocationsObj) {
    const map = fwdpEnsureMap();
    if (!map) {
        return;
    }

    fwdpClearMapLayers();

    const assignedLocations = Object.values(assignedLocationsObj || {});
    const visitedLocations = Object.values(visitedLocationsObj || {});
    const visitedPoints = [];
    const allBounds = [];
    const matchedVisitedIds = [];
    let visitedCount = 0;
    let extraCount = 0;
    let pendingCount = 0;
    const greenIcon = fwdpPinIcon('#10b981');
    const blueIcon = fwdpPinIcon('#2563eb');
    const redIcon = fwdpPinIcon('#ef4444');

    visitedLocations.forEach(function (location) {
        const lat = parseFloat(location.lat);
        const lng = parseFloat(location.lng);
        if (isNaN(lat) || isNaN(lng)) {
            return;
        }
        allBounds.push({ lat: lat, lng: lng });

        if (fwdpHasMatchedPlanId(location.field_work_daily_plan_id)) {
            visitedCount++;
            matchedVisitedIds.push(String(location.field_work_daily_plan_id));
            visitedPoints.push({ lat: lat, lng: lng });
            fwdpAddMarker(
                lat,
                lng,
                greenIcon,
                '<b>Location:</b> ' + fwdpEscapeHtml(location.name || 'Unassigned') +
                '<br><b>Time:</b> ' + fwdpEscapeHtml(location.map_time || 'No Time') +
                '<br><b>Status:</b> Visited'
            );
        } else {
            extraCount++;
            fwdpAddMarker(
                lat,
                lng,
                blueIcon,
                '<b>Location:</b> ' + fwdpEscapeHtml(location.name || 'Unassigned') +
                '<br><b>Time:</b> ' + fwdpEscapeHtml(location.map_time || 'No Time') +
                '<br><b>Status:</b> Extra'
            );
        }
    });

    assignedLocations.forEach(function (location) {
        const lat = parseFloat(location.lat);
        const lng = parseFloat(location.lng);
        const asid = location.id;
        if (isNaN(lat) || isNaN(lng)) {
            return;
        }
        if (asid == null || asid == '') {
            return;
        }
        if (matchedVisitedIds.indexOf(String(asid)) == -1) {
            pendingCount++;
            allBounds.push({ lat: lat, lng: lng });
            fwdpAddMarker(
                lat,
                lng,
                redIcon,
                '<b>Location:</b> ' + fwdpEscapeHtml(location.name || '') +
                '<br><b>Status:</b> Not Visited'
            );
        }
    });

    if (visitedPoints.length > 1) {
        fwdpRenderDrivingVisitedRoute(visitedPoints);
    } else {
        fwdpRenderMapCountsSummary(visitedCount, extraCount, pendingCount, visitedPoints);
    }
    fwdpFitBounds(allBounds);

    if (allBounds.length == 0) {
        $('#fwdpSelectedAddress').text('No map points found for this user and date.');
        $('#fwdpMapEmpty').text('No map points found for this user and date.').show();
        toast('No map points found for this user and date.');
    } else {
        $('#fwdpMapEmpty').hide();
    }
}

function initializeVisitedMap(visitedLocationsObj) {
    const map = fwdpEnsureMap();
    if (!map) {
        return;
    }

    fwdpClearMapLayers();

    const visitedLocations = Object.values(visitedLocationsObj || {});
    const visitedPoints = [];
    const greenIcon = fwdpPinIcon('#10b981');

    visitedLocations.forEach(function (location) {
        const lat = parseFloat(location.lat);
        const lng = parseFloat(location.lng);
        if (isNaN(lat) || isNaN(lng)) {
            return;
        }
        visitedPoints.push({ lat: lat, lng: lng });
        fwdpAddMarker(
            lat,
            lng,
            greenIcon,
            '<b>Location:</b> ' + fwdpEscapeHtml(location.name || 'Unassigned') +
            '<br><b>Time:</b> ' + fwdpEscapeHtml(location.map_time || 'No Time') +
            '<br><b>Status:</b> Visited'
        );
    });

    if (visitedPoints.length > 1) {
        fwdpRenderDrivingVisitedRoute(visitedPoints);
    } else {
        fwdpRenderStraightVisitedSummary(visitedPoints);
    }
    fwdpFitBounds(visitedPoints);

    if (visitedPoints.length == 0) {
        $('#fwdpSelectedAddress').text('No visited points found for this user and date.');
        $('#fwdpMapEmpty').text('No visited points found for this user and date.').show();
        toast('No visited points found for this user and date.');
    } else {
        $('#fwdpMapEmpty').hide();
    }
}

function ViewMapPoint(id, user_field_work_type, map_date) {
    $.ajax({
        url: 'ajax.php',
        method: 'POST',
        data: {
            page: 'getMapPoints',
            user_id: id,
            map_date: map_date,
            user_field_work_type: user_field_work_type
        },
        success: function (response) {
            let data;
            try {
                data = JSON.parse(response);
            } catch (e) {
                toast('An error occurred while loading map points.');
                return;
            }
            const visitedLocations = data && data.visitedLocations ? data.visitedLocations : {};
            const assignedLocations = data && data.assignedLocations ? data.assignedLocations : {};

            fwdpPendingInit = function () {
                if (user_field_work_type == '2' || user_field_work_type == '3') {
                    initializeVisitedMap(visitedLocations);
                } else {
                    initializeMap(assignedLocations, visitedLocations);
                }
                if (fwdpMap) {
                    google.maps.event.trigger(fwdpMap, 'resize');
                }
            };

            $("#mapModal").modal("show");
        },
        error: function (xhr, status, error) {
            console.error(error);
            toast('An error occurred while loading map points.');
        }
    });
}

$(document).ready(function () {
    $('#from_date').on('change', function () {
        handleFromDateChange();
    });

    $(document).on('click', '.viewMapBtn', function () {
        const id = $(this).data('id');
        const user_field_work_type = $(this).data('work-type');
        const map_date = $(this).data('date');
        ViewMapPoint(id, user_field_work_type, map_date);
    });

    $('#getDataButton').on('click', function () {
        fwdpHideErr('user_id_err');
        fwdpHideErr('from_date_err');
        fwdpHideErr('to_date_err');

        let user_ids = fwdpFilterIds($('#user_id').val());
        let from_date = $('#from_date').val();
        let to_date = $('#to_date').val();
        let isValid = true;

        if (user_ids.length == 0) {
            fwdpShowErr('user_id_err', 'Please select at least one user');
            toast('Please select at least one user');
            isValid = false;
        }

        if (!from_date) {
            fwdpShowErr('from_date_err', 'Please select a from date');
            toast('Please select a from date');
            isValid = false;
        }

        if (!to_date) {
            fwdpShowErr('to_date_err', 'Please select a to date');
            toast('Please select a to date');
            isValid = false;
        }

        if (from_date && to_date && to_date < from_date) {
            fwdpShowErr('to_date_err', 'To Date must be on or after From Date');
            toast('To Date must be on or after From Date');
            isValid = false;
        }

        if (!isValid) {
            return;
        }

        const $btn = $('#getDataButton');
        $btn.prop('disabled', true).text('Searching...');

        $.ajax({
            url: 'ajax.php',
            method: 'POST',
            data: {
                page: 'getFieldWorkUserMapPointsData',
                user_id: user_ids,
                from_date: from_date,
                to_date: to_date,
            },
            success: function (response) {
                let data;
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    toast('An error occurred while processing the request.');
                    return;
                }
                if (!$.isArray(data)) {
                    data = [];
                }

                document.getElementById('workType1').classList.remove('d-none');

                const tableBody = document.querySelector('#fiedWorkTable tbody');
                tableBody.innerHTML = '';

                if (data.length == 0) {
                    tableBody.innerHTML =
                        '<tr>' +
                        '<td colspan="8">' +
                        '<div class="fwdp-empty-state">' +
                        '<p><i class="fa fa-warning"></i> No Records</p>' +
                        '<div class="text-center px-4">' +
                        '<img class="mw-100 mh-300px work-img" alt="" src="assets/images/work.png">' +
                        '</div>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                } else {
                    let i = 1;
                    data.forEach(function (item) {
                        const newRow =
                            '<tr>' +
                            '<td>' + i + '</td>' +
                            '<td>' + (item.display_name || item.id) + '</td>' +
                            '<td>' + (item.user_email || '') + '</td>' +
                            '<td>' + (item.map_date || '') + '</td>' +
                            '<td><span class="fwdp-badge fwdp-badge-visited">' + (item.visited || '0') + '</span></td>' +
                            '<td><span class="fwdp-badge fwdp-badge-extra">' + (item.extra || '0') + '</span></td>' +
                            '<td><span class="fwdp-badge fwdp-badge-pending">' + (item.pending || '0') + '</span></td>' +
                            '<td>' +
                            '<button type="button" class="fwdp-btn fwdp-btn-success viewMapBtn" ' +
                            'data-id="' + item.id + '" ' +
                            'data-work-type="' + item.user_field_work_type + '" ' +
                            'data-date="' + item.map_date + '">' +
                            '<i class="fas fa-map-marker-alt"></i> View Map Point' +
                            '</button>' +
                            '</td>' +
                            '</tr>';
                        tableBody.innerHTML += newRow;
                        i++;
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error(error);
                toast('An error occurred while processing the request.');
            },
            complete: function () {
                $btn.prop('disabled', false).text('Search');
            }
        });
    });

    $("#mapModal").on("shown.bs.modal", function () {
        if (typeof fwdpPendingInit == 'function') {
            fwdpPendingInit();
            fwdpPendingInit = null;
        } else if (fwdpMap) {
            google.maps.event.trigger(fwdpMap, 'resize');
        }
    });
});
