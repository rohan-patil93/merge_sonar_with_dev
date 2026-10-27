const fwdpDefaultCenter = { lat: 22.836946, lng: 78.354492 };
const fwdpDefaultZoom = 5;

let fwdpMap = null;
let fwdpGeocoder = null;
let fwdpInfoWindow = null;
let fwdpClickedMarker = null;
let fwdpUserMarkers = [];
let fwdpAutocomplete = null;

const pageEl = document.getElementById('viewMapPointsPage');
let usersLocations = {};
try {
    usersLocations = JSON.parse(pageEl.dataset.usersLocations || '{}');
} catch (e) {
    usersLocations = {};
}

function fwdpEscapeHtml(value) {
    return String(value == null ? '' : value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function fwdpMotorcycleIcon() {
    const svg =
        '<svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 44 44">' +
        '<circle cx="22" cy="22" r="20" fill="#3722a7" stroke="#ffffff" stroke-width="2"/>' +
        '<path fill="#ffffff" d="M12 27a4.5 4.5 0 1 0 0.1 0zm20 0a4.5 4.5 0 1 0 0.1 0zM14 18h8l3 5h5v2h-3.2a5.5 5.5 0 0 1-9.6 0H14v-7zm9 2h-5v3h3.5l1.5-3z"/>' +
        '</svg>';
    return {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
        scaledSize: new google.maps.Size(40, 40),
        anchor: new google.maps.Point(20, 20)
    };
}

function fwdpUpdateCoordPreview(lat, lng) {
    if (lat && lng) {
        $('#fwdpCoordPreview').text(parseFloat(lat).toFixed(6) + ', ' + parseFloat(lng).toFixed(6));
    } else {
        $('#fwdpCoordPreview').text('--');
    }
}

function fwdpFixPacContainer() {
    document.querySelectorAll('.pac-container').forEach(function (el) {
        el.style.zIndex = '10050';
    });
}

function fwdpSetSearchedLocation(lat, lng, address) {
    if (fwdpClickedMarker) {
        fwdpClickedMarker.setMap(null);
    }

    $("#plan_latitude").val(parseFloat(lat).toFixed(6));
    $("#plan_longitude").val(parseFloat(lng).toFixed(6));
    fwdpUpdateCoordPreview(lat, lng);
    if (address) {
        $('#plan_address').val(address);
    } else {
        fetchAddressFromCoordinates(lat, lng);
    }

    fwdpClickedMarker = new google.maps.Marker({
        position: { lat: parseFloat(lat), lng: parseFloat(lng) },
        map: fwdpMap,
        title: 'Selected Location'
    });

    fwdpInfoWindow.setContent(
        '<b>Selected Location</b><br>Lat: ' + parseFloat(lat).toFixed(6) + '<br>Lng: ' + parseFloat(lng).toFixed(6)
    );
    fwdpInfoWindow.open(fwdpMap, fwdpClickedMarker);

    if ($("#user_id").val()) {
        $("#infoContainer").removeClass('d-none');
    }
}

function fwdpGeocodeSearchQuery(query) {
    if (!fwdpGeocoder || !query) {
        return;
    }
    fwdpGeocoder.geocode({ address: query }, function (results, status) {
        if (status == 'OK' && results && results[0] && results[0].geometry) {
            const loc = results[0].geometry.location;
            const lat = loc.lat();
            const lng = loc.lng();
            fwdpMap.panTo(loc);
            fwdpMap.setZoom(15);
            fwdpSetSearchedLocation(lat, lng, results[0].formatted_address || query);
        }
    });
}

function fwdpInitAutocomplete() {
    const input = document.getElementById('fwdpMapSearch');
    if (!input || !fwdpMap || typeof google.maps.places == 'undefined') {
        return;
    }

    if (fwdpAutocomplete) {
        google.maps.event.clearInstanceListeners(fwdpAutocomplete);
        fwdpAutocomplete = null;
    }

    fwdpAutocomplete = new google.maps.places.Autocomplete(input, {
        fields: ['formatted_address', 'geometry', 'name'],
        types: ['geocode', 'establishment']
    });
    fwdpAutocomplete.bindTo('bounds', fwdpMap);

    fwdpAutocomplete.addListener('place_changed', function () {
        const place = fwdpAutocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) {
            return;
        }
        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();
        const address = place.formatted_address || place.name || '';

        if (place.geometry.viewport) {
            fwdpMap.fitBounds(place.geometry.viewport);
        } else {
            fwdpMap.panTo(place.geometry.location);
            fwdpMap.setZoom(15);
        }
        fwdpSetSearchedLocation(lat, lng, address);
    });

    $(input).off('focus.fwdp keydown.fwdp input.fwdp').on('focus.fwdp input.fwdp', function () {
        setTimeout(fwdpFixPacContainer, 100);
    }).on('keydown.fwdp', function (e) {
        if (e.key == 'Enter') {
            e.preventDefault();
            const query = $(this).val().trim();
            if (query) {
                fwdpGeocodeSearchQuery(query);
            }
        }
    });

    fwdpFixPacContainer();
}

function fwdpEnsureMap() {
    if (!window.fwdpGoogleMapsKeyConfigured || typeof google == 'undefined' || !google.maps) {
        $('#fwdpMapEmpty').show();
        $('#fwdpMapSearchWrap').hide();
        return null;
    }
    $('#fwdpMapEmpty').hide();
    $('#fwdpMapSearchWrap').show();

    if (!fwdpMap) {
        fwdpMap = new google.maps.Map(document.getElementById('fwdpMap'), {
            center: fwdpDefaultCenter,
            zoom: fwdpDefaultZoom,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: true
        });
        fwdpGeocoder = new google.maps.Geocoder();
        fwdpInfoWindow = new google.maps.InfoWindow();

        fwdpMap.addListener('click', function (e) {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();

            if (fwdpClickedMarker) {
                fwdpClickedMarker.setMap(null);
            }

            $("#plan_latitude").val(lat.toFixed(6));
            $("#plan_longitude").val(lng.toFixed(6));
            fwdpUpdateCoordPreview(lat, lng);
            fetchAddressFromCoordinates(lat, lng);

            fwdpClickedMarker = new google.maps.Marker({
                position: { lat: lat, lng: lng },
                map: fwdpMap,
                title: 'Clicked Location'
            });

            fwdpInfoWindow.setContent(
                '<b>Clicked Location</b><br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6)
            );
            fwdpInfoWindow.open(fwdpMap, fwdpClickedMarker);
        });

        fwdpInitAutocomplete();
    }
    return fwdpMap;
}

function fetchAddressFromCoordinates(lat, lng) {
    if (!fwdpGeocoder) {
        return;
    }
    fwdpGeocoder.geocode({ location: { lat: lat, lng: lng } }, function (results, status) {
        if (status == 'OK' && results && results[0]) {
            $('#plan_address').val(results[0].formatted_address);
        }
    });
}

function initializeVisitedMap(visitedLocationsObj) {
    const map = fwdpEnsureMap();
    if (!map) {
        return;
    }

    fwdpUserMarkers.forEach(function (marker) {
        marker.setMap(null);
    });
    fwdpUserMarkers = [];

    const visitedLocations = Object.values(visitedLocationsObj || {});
    const visitedPoints = [];
    const icon = fwdpMotorcycleIcon();

    visitedLocations.forEach(function (location) {
        const lat = parseFloat(location.lat);
        const lng = parseFloat(location.lng);
        if (isNaN(lat) || isNaN(lng)) {
            return;
        }

        visitedPoints.push({ lat: lat, lng: lng });

        const marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: fwdpMap,
            icon: icon,
            title: location.display_name || ''
        });

        const popupContent =
            '<b>Name:</b> ' + fwdpEscapeHtml(location.display_name || '') +
            '<br><b>Time:</b> ' + fwdpEscapeHtml(location.map_time || 'No Time') + '<br>';

        marker.addListener('mouseover', function () {
            fwdpInfoWindow.setContent(popupContent);
            fwdpInfoWindow.open(fwdpMap, marker);
        });
        marker.addListener('mouseout', function () {
            fwdpInfoWindow.close();
        });
        marker.addListener('click', function () {
            $("#display_name").val(location.display_name || '');
            $("#user_id").val(location.user_id || '');
            $("#plan_date").val(location.map_date || '');
            $("#infoContainer").removeClass('d-none');
        });

        fwdpUserMarkers.push(marker);
    });

    if (visitedPoints.length > 0) {
        const bounds = new google.maps.LatLngBounds();
        visitedPoints.forEach(function (point) {
            bounds.extend(point);
        });
        fwdpMap.fitBounds(bounds, 40);
        if (visitedPoints.length == 1) {
            fwdpMap.setZoom(14);
        }
    }
}

$(document).ready(function () {
    initializeVisitedMap(usersLocations);

    $("#closeContainer").on('click', function () {
        $("#infoContainer").addClass('d-none');
    });

    $("#plan_latitude, #plan_longitude").on('change input', function () {
        fwdpUpdateCoordPreview($("#plan_latitude").val(), $("#plan_longitude").val());
    });
});
