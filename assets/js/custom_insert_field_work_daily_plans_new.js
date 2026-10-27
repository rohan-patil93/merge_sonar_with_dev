const fwdpDefaultCenter = { lat: 17.44, lng: 78.39 };
const fwdpDefaultZoom = 11;

let fwdpMap = null;
let fwdpMarker = null;
let fwdpGeocoder = null;
let fwdpAutocomplete = null;
let fwdpRoutePolyline = null;
let fwdpRouteMarkers = [];
let fwdpInfoWindow = null;
let fwdpDirectionsRenderer = null;
let fwdpDirectionsService = null;
let fwdpSegmentOverlays = [];
let fwdpConnectorPolylines = [];
let fwdpRouteRequestToken = 0;

let currentLatInput = null;
let currentLngInput = null;
let currentAddressInput = null;
let pendingLat = null;
let pendingLng = null;
let pendingAddress = '';
let fwdpMapViewAll = false;
let fwdpPendingMapInit = null;

function fwdpPageUrl(params) {
  const option = window.fwdpPageOption || 'COM_INSERT_FIELD_WORK_DAILY_PLANS_NEW';
  let url = 'index.php?option=' + option;
  if (params) {
    Object.keys(params).forEach(function (key) {
      url += '&' + key + '=' + encodeURIComponent(params[key]);
    });
  }
  return url;
}

function toast(msg) {
  tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
    'animated flipInX', 'animated flipOutX', msg, '');
}

function fwdpEscapeHtml(value) {
  return String(value == null ? '' : value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function fwdpUpdateCoordPreview($card) {
  const lat = $card.find('.lat').val();
  const lng = $card.find('.lng').val();
  const $preview = $card.find('.fwdp-coord-value');
  if (lat && lng) {
    $preview.text(parseFloat(lat).toFixed(6) + ', ' + parseFloat(lng).toFixed(6));
  } else {
    $preview.text('--');
  }
}

function fwdpRenumberLocationCards() {
  $('#locationCardsContainer .fwdp-location-card').each(function (index) {
    $(this).find('.fwdp-location-num').text(index + 1);
  });
}

function fwdpFormatPlanTime(value) {
  if (!value) {
    return '';
  }
  const text = String(value);
  if (text.length >= 5) {
    return text.substring(0, 5);
  }
  return text;
}

function fwdpValidateUniquePlanTime(changedInput) {
  if (!changedInput) {
    return true;
  }

  const normalizedTime = fwdpFormatPlanTime(changedInput.value);
  if (!normalizedTime) {
    return true;
  }

  let isDuplicate = false;
  $('#locationCardsContainer .fwdp-location-card').each(function () {
    const $timeInput = $(this).find("input[name='plan_time[]']");
    if ($timeInput.length && $timeInput[0] != changedInput) {
      const otherTime = fwdpFormatPlanTime($timeInput.val());
      if (otherTime && otherTime == normalizedTime) {
        isDuplicate = true;
        return false;
      }
    }
  });

  if (isDuplicate) {
    toast('This planned time is already used for another location.');
    changedInput.value = '';
    return false;
  }

  return true;
}

function fwdpHasDuplicatePlanTimes() {
  const times = [];
  $('#locationCardsContainer .fwdp-location-card').each(function () {
    const normalizedTime = fwdpFormatPlanTime($(this).find("input[name='plan_time[]']").val());
    if (normalizedTime) {
      times.push(normalizedTime);
    }
  });
  return times.length != new Set(times).size;
}

function fwdpBuildLocationCardHtml(row) {
  row = row || {};
  return (
    '<div class="fwdp-location-card">' +
      '<div class="fwdp-location-card-head">' +
        '<span class="fwdp-location-num">1</span>' +
        '<button type="button" class="fwdp-remove-row" title="Remove location"><i class="fas fa-trash-alt"></i></button>' +
      '</div>' +
      '<div class="row g-3">' +
        '<div class="col-md-3">' +
          '<label class="fwdp-label">Contact Person</label>' +
          '<input class="form-control" type="text" name="contact_person[]" value="' + fwdpEscapeHtml(row.contact_person || '') + '">' +
        '</div>' +
        '<div class="col-md-3">' +
          '<label class="fwdp-label">Phone No.</label>' +
          '<input class="form-control" type="text" name="mobile[]" value="' + fwdpEscapeHtml(row.mobile || '') + '">' +
        '</div>' +
        '<div class="col-md-3">' +
          '<label class="fwdp-label">Location Name</label>' +
          '<input class="form-control" type="text" name="plan_location[]" value="' + fwdpEscapeHtml(row.plan_location || '') + '">' +
        '</div>' +
        '<div class="col-md-3">' +
          '<label class="fwdp-label">Planned Time</label>' +
          '<input class="form-control" type="time" name="plan_time[]" value="' + fwdpEscapeHtml(fwdpFormatPlanTime(row.plan_time)) + '">' +
        '</div>' +
        '<div class="col-12">' +
          '<label class="fwdp-label">Address</label>' +
          '<textarea class="form-control fwdp-address-field" name="plan_address[]" rows="2" readonly>' + fwdpEscapeHtml(row.plan_address || '') + '</textarea>' +
        '</div>' +
        '<div class="col-md-6 d-none">' +
          '<input class="form-control lat" type="text" name="plan_latitude[]" value="' + fwdpEscapeHtml(row.plan_latitude || '') + '" readonly>' +
        '</div>' +
        '<div class="col-md-6 d-none">' +
          '<input class="form-control lng" type="text" name="plan_longitude[]" value="' + fwdpEscapeHtml(row.plan_longitude || '') + '" readonly>' +
        '</div>' +
      '</div>' +
      '<div class="fwdp-location-footer">' +
        '<div class="fwdp-coords-preview">' +
          '<span class="fwdp-coord-label">Lat / Lng</span>' +
          '<span class="fwdp-coord-value">--</span>' +
        '</div>' +
        '<button type="button" class="fwdp-btn fwdp-btn-map addMapBtn">' +
          '<i class="fas fa-map-marker-alt"></i> Pick on Map' +
        '</button>' +
      '</div>' +
    '</div>'
  );
}

function fwdpClearRouteMarkersAndConnectors() {
  fwdpRouteMarkers.forEach(function (marker) {
    marker.setMap(null);
  });
  fwdpRouteMarkers = [];
  fwdpConnectorPolylines.forEach(function (polyline) {
    polyline.setMap(null);
  });
  fwdpConnectorPolylines = [];
}

function fwdpClearRouteLayers() {
  fwdpRouteRequestToken++;
  fwdpClearRouteMarkersAndConnectors();
  if (fwdpRoutePolyline) {
    fwdpRoutePolyline.setMap(null);
    fwdpRoutePolyline = null;
  }
  fwdpSegmentOverlays.forEach(function (overlay) {
    overlay.setMap(null);
  });
  fwdpSegmentOverlays = [];
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
}

function fwdpToLatLngLiteral(point) {
  if (!point) {
    return null;
  }
  if (typeof point.lat === 'function') {
    return { lat: point.lat(), lng: point.lng() };
  }
  return { lat: parseFloat(point.lat), lng: parseFloat(point.lng) };
}

function fwdpMetersBetween(pointA, pointB) {
  const a = fwdpToLatLngLiteral(pointA);
  const b = fwdpToLatLngLiteral(pointB);
  if (!a || !b || isNaN(a.lat) || isNaN(a.lng) || isNaN(b.lat) || isNaN(b.lng)) {
    return 0;
  }
  if (!google.maps.geometry) {
    return 0;
  }
  return google.maps.geometry.spherical.computeDistanceBetween(
    new google.maps.LatLng(a.lat, a.lng),
    new google.maps.LatLng(b.lat, b.lng)
  );
}

function fwdpBuildPointInfoHtml(point, index) {
  const contact = (point && point.contact_person) ? point.contact_person : '';
  const place = (point && point.plan_location) ? point.plan_location : '';
  const time = (point && point.plan_time) ? fwdpFormatPlanTime(point.plan_time) : '';

  return (
    '<b>Contact Person:</b> ' + fwdpEscapeHtml(contact != '' ? contact : '—') +
    '<br><b>Place:</b> ' + fwdpEscapeHtml(place != '' ? place : 'Unassigned') +
    '<br><b>Time:</b> ' + fwdpEscapeHtml(time != '' ? time : 'No Time')
  );
}

function fwdpPlacePointMarkers(points) {
  fwdpClearRouteMarkersAndConnectors();

  if (!fwdpInfoWindow) {
    fwdpInfoWindow = new google.maps.InfoWindow();
  }

  points.forEach(function (point, index) {
    const literal = fwdpToLatLngLiteral(point);
    if (!literal || isNaN(literal.lat) || isNaN(literal.lng)) {
      return;
    }

    const placeTitle = (point && point.plan_location) ? point.plan_location : ('Location ' + (index + 1));
    const popupHtml = fwdpBuildPointInfoHtml(point, index);

    const marker = new google.maps.Marker({
      position: literal,
      map: fwdpMap,
      label: {
        text: String(index + 1),
        color: '#ffffff',
        fontWeight: 'bold'
      },
      title: placeTitle,
      zIndex: 2
    });

    marker.addListener('click', function () {
      fwdpInfoWindow.setContent(popupHtml);
      fwdpInfoWindow.open(fwdpMap, marker);
    });

    fwdpRouteMarkers.push(marker);
  });
}

function fwdpRenderPointRoute(points) {
  if (!points || points.length < 2) {
    return;
  }

  if (fwdpDirectionsRenderer) {
    try {
      fwdpDirectionsRenderer.set('directions', null);
    } catch (e) {
      // ignore
    }
    fwdpDirectionsRenderer.setMap(null);
  }

  if (fwdpRoutePolyline) {
    fwdpRoutePolyline.setMap(null);
    fwdpRoutePolyline = null;
  }

  fwdpSegmentOverlays.forEach(function (overlay) {
    overlay.setMap(null);
  });
  fwdpSegmentOverlays = [];

  fwdpRoutePolyline = new google.maps.Polyline({
    path: points.slice(),
    geodesic: true,
    strokeColor: '#2563eb',
    strokeOpacity: 0.9,
    strokeWeight: 4,
    map: fwdpMap,
    zIndex: 1
  });

  fwdpPlacePointMarkers(points);

  let totalDistance = 0;
  let summaryHtml = '<div class="fwdp-route-legs">';

  if (google.maps.geometry) {
    for (let i = 0; i < points.length - 1; i++) {
      const from = new google.maps.LatLng(points[i].lat, points[i].lng);
      const to = new google.maps.LatLng(points[i + 1].lat, points[i + 1].lng);
      const segmentMeters = google.maps.geometry.spherical.computeDistanceBetween(from, to);
      totalDistance += segmentMeters;
      const segmentKm = (segmentMeters / 1000).toFixed(2);

      summaryHtml +=
        '<div class="fwdp-route-leg">' +
          '<span class="fwdp-route-leg-label">' + (i + 1) + ' → ' + (i + 2) + '</span>' +
          '<span class="fwdp-route-leg-value">' + segmentKm + ' km</span>' +
        '</div>';
    }
  }

  summaryHtml += '</div>';
  $('#fwdpRouteSummary').html(summaryHtml).show();
  $('#fwdpMapDistance').text('Total (straight): ' + (totalDistance / 1000).toFixed(2) + ' km').show();
  $('#fwdpSelectedAddress').text(points.length + ' location(s) · straight-line route');
  fwdpFitBounds(points.map(function (point) {
    return fwdpToLatLngLiteral(point);
  }));
}

function fwdpPlaceRouteMarkers(originalPoints, displayPoints) {
  fwdpPlacePointMarkers(originalPoints);
}

function fwdpBuildGoogleMapsNavUrl(points) {
  if (!points.length) {
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

function fwdpCreateDistanceLabel(position, text) {
  const overlay = new google.maps.OverlayView();
  overlay.onAdd = function () {
    const div = document.createElement('div');
    div.className = 'fwdp-distance-label';
    div.textContent = text;
    this.div = div;
    this.getPanes().overlayMouseTarget.appendChild(div);
  };
  overlay.draw = function () {
    const projection = this.getProjection();
    if (!projection || !this.div) {
      return;
    }
    const point = projection.fromLatLngToDivPixel(position);
    if (!point) {
      return;
    }
    this.div.style.left = (point.x - 28) + 'px';
    this.div.style.top = (point.y - 12) + 'px';
  };
  overlay.onRemove = function () {
    if (this.div && this.div.parentNode) {
      this.div.parentNode.removeChild(this.div);
    }
  };
  overlay.setMap(fwdpMap);
  return overlay;
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
        strokeColor: '#2563eb',
        strokeOpacity: 0.95,
        strokeWeight: 5,
        zIndex: 1
      }
    });
  }
}

function fwdpRenderStraightRoute(points) {
  fwdpRenderPointRoute(points);
}

function fwdpRenderDrivingRoute(points) {
  if (!points || points.length < 2) {
    return;
  }

  if (typeof google == 'undefined' || !google.maps || !google.maps.DirectionsService) {
    toast('Could not load driving route. Showing straight-line path.');
    fwdpRenderPointRoute(points);
    return;
  }

  fwdpEnsureDirections();

  if (fwdpRoutePolyline) {
    fwdpRoutePolyline.setMap(null);
    fwdpRoutePolyline = null;
  }
  fwdpSegmentOverlays.forEach(function (overlay) {
    overlay.setMap(null);
  });
  fwdpSegmentOverlays = [];

  fwdpDirectionsRenderer.setMap(fwdpMap);
  fwdpPlacePointMarkers(points);

  const requestToken = fwdpRouteRequestToken;
  const origin = fwdpToLatLngLiteral(points[0]);
  const destination = fwdpToLatLngLiteral(points[points.length - 1]);
  const waypoints = [];
  for (let i = 1; i < points.length - 1; i++) {
    const literal = fwdpToLatLngLiteral(points[i]);
    if (!literal) {
      continue;
    }
    waypoints.push({
      location: literal,
      stopover: true
    });
  }

  $('#fwdpRouteSummary').empty().show();
  $('#fwdpMapDistance').hide();
  $('#fwdpSelectedAddress').text('Calculating driving route...');

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
      fwdpRenderPointRoute(points);
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
    $('#fwdpSelectedAddress').text(points.length + ' location(s) · driving route');
  });
}

function fwdpSetMapEmpty(show, message) {
  const emptyEl = document.getElementById('fwdpMapEmpty');
  if (!emptyEl) {
    return;
  }
  if (message) {
    emptyEl.textContent = message;
  }
  emptyEl.style.display = show ? 'flex' : 'none';
}

function fwdpReverseGeocode(lat, lng, callback) {
  if (!fwdpGeocoder) {
    callback('');
    return;
  }
  fwdpGeocoder.geocode({ location: { lat: lat, lng: lng } }, function (results, status) {
    if (status == 'OK' && results && results[0]) {
      callback(results[0].formatted_address);
    } else {
      callback('');
    }
  });
}

function fwdpSetPendingLocation(lat, lng, address) {
  pendingLat = lat;
  pendingLng = lng;
  pendingAddress = address || '';
  $('#fwdpSelectedAddress').text(pendingAddress || 'Location selected. Click Confirm to apply.');

  if (fwdpMarker) {
    fwdpMarker.setPosition({ lat: lat, lng: lng });
  } else if (fwdpMap) {
    fwdpMarker = new google.maps.Marker({
      position: { lat: lat, lng: lng },
      map: fwdpMap,
      draggable: !fwdpMapViewAll
    });
    if (!fwdpMapViewAll) {
      fwdpMarker.addListener('dragend', function (event) {
        const dragLat = event.latLng.lat();
        const dragLng = event.latLng.lng();
        fwdpReverseGeocode(dragLat, dragLng, function (addr) {
          fwdpSetPendingLocation(dragLat, dragLng, addr);
        });
      });
    }
  }

  if (fwdpMap) {
    fwdpMap.panTo({ lat: lat, lng: lng });
    if (fwdpMap.getZoom() < 14) {
      fwdpMap.setZoom(14);
    }
  }
}

function fwdpFixPacContainer() {
  const pacContainers = document.querySelectorAll('.pac-container');
  pacContainers.forEach(function (el) {
    el.style.zIndex = '10050';
    if (el.parentNode != document.body) {
      document.body.appendChild(el);
    }
  });
}

function fwdpGeocodeSearchQuery(query) {
  if (!query || !fwdpGeocoder) {
    return;
  }
  fwdpGeocoder.geocode({ address: query }, function (results, status) {
    if (status == 'OK' && results && results[0] && results[0].geometry) {
      const loc = results[0].geometry.location;
      fwdpSetPendingLocation(loc.lat(), loc.lng(), results[0].formatted_address || query);
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
    fwdpSetPendingLocation(lat, lng, address);
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

function fwdpFitBounds(latLngs) {
  if (!fwdpMap || !latLngs.length) {
    return;
  }
  if (latLngs.length == 1) {
    fwdpMap.setCenter(latLngs[0]);
    fwdpMap.setZoom(14);
    return;
  }
  const bounds = new google.maps.LatLngBounds();
  latLngs.forEach(function (latLng) {
    bounds.extend(latLng);
  });
  fwdpMap.fitBounds(bounds, { top: 40, bottom: 60, left: 40, right: 40 });
}

function fwdpRenderAllPoints() {
  fwdpClearRouteLayers();
  if (fwdpMarker) {
    fwdpMarker.setMap(null);
    fwdpMarker = null;
  }

  const points = [];
  $('#locationCardsContainer .fwdp-location-card').each(function () {
    const $card = $(this);
    const lat = parseFloat($card.find('.lat').val());
    const lng = parseFloat($card.find('.lng').val());
    if (!isNaN(lat) && !isNaN(lng)) {
      points.push({
        lat: lat,
        lng: lng,
        contact_person: $card.find('input[name="contact_person[]"]').val() || '',
        plan_location: $card.find('input[name="plan_location[]"]').val() || '',
        plan_time: $card.find('input[name="plan_time[]"]').val() || ''
      });
    }
  });

  $('#fwdpRouteSummary').empty();
  $('#fwdpOpenGoogleNav').attr('href', fwdpBuildGoogleMapsNavUrl(points));

  if (points.length == 0) {
    $('#fwdpSelectedAddress').text('No map points added yet.');
    $('#fwdpMapDistance').hide();
    fwdpMap.setCenter(fwdpDefaultCenter);
    fwdpMap.setZoom(fwdpDefaultZoom);
    return;
  }

  if (points.length > 1) {
    fwdpRenderDrivingRoute(points);
  } else {
    fwdpPlacePointMarkers(points);
    $('#fwdpMapDistance').hide();
    fwdpMap.setCenter(points[0]);
    fwdpMap.setZoom(14);
    $('#fwdpSelectedAddress').text('1 location on map.');
  }
}

function fwdpInitPickerMap() {
  fwdpClearRouteLayers();
  $('#fwdpMapDistance').hide();

  if (fwdpMarker) {
    fwdpMarker.setMap(null);
    fwdpMarker = null;
  }

  const latVal = currentLatInput ? parseFloat(currentLatInput.val()) : NaN;
  const lngVal = currentLngInput ? parseFloat(currentLngInput.val()) : NaN;

  if (!isNaN(latVal) && !isNaN(lngVal)) {
    const address = currentAddressInput ? currentAddressInput.val() : '';
    fwdpSetPendingLocation(latVal, lngVal, address);
  } else {
    pendingLat = null;
    pendingLng = null;
    pendingAddress = '';
    $('#fwdpSelectedAddress').text('Click on the map or search to select a location.');
    fwdpMap.setCenter(fwdpDefaultCenter);
    fwdpMap.setZoom(fwdpDefaultZoom);
  }

  google.maps.event.clearListeners(fwdpMap, 'click');
  fwdpMap.addListener('click', function (event) {
    const lat = event.latLng.lat();
    const lng = event.latLng.lng();
    fwdpReverseGeocode(lat, lng, function (address) {
      fwdpSetPendingLocation(lat, lng, address);
    });
  });
}

function fwdpInitializeMap(showAllPoints) {
  fwdpMapViewAll = showAllPoints;

  if (!window.fwdpGoogleMapsKeyConfigured || typeof google == 'undefined' || !google.maps) {
    fwdpSetMapEmpty(true, 'Map key not configured.');
    return;
  }

  fwdpSetMapEmpty(false);

  if (!fwdpMap) {
    fwdpMap = new google.maps.Map(document.getElementById('fwdpMap'), {
      center: fwdpDefaultCenter,
      zoom: fwdpDefaultZoom,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: true,
      gestureHandling: 'cooperative'
    });
    fwdpGeocoder = new google.maps.Geocoder();
    fwdpInfoWindow = new google.maps.InfoWindow();
  }

  if (!fwdpInfoWindow) {
    fwdpInfoWindow = new google.maps.InfoWindow();
  }

  if (showAllPoints) {
    fwdpRenderAllPoints();
  } else {
    fwdpInitPickerMap();
  }
}

function fwdpConfirmLocation() {
  if (pendingLat == null || pendingLng == null || !currentLatInput || !currentLngInput) {
    return;
  }

  const lat = pendingLat.toFixed(6);
  const lng = pendingLng.toFixed(6);

  currentLatInput.val(lat);
  currentLngInput.val(lng);

  if (currentAddressInput) {
    currentAddressInput.val(pendingAddress);
  }

  const $card = currentLatInput.closest('.fwdp-location-card');
  if ($card.length) {
    fwdpUpdateCoordPreview($card);
  }

  $('#mapModal').modal('hide');
}

function fwdpToggleMapModalMode(showAllPoints) {
  if (showAllPoints) {
    $('#mapModalLabel').text('All Map Points');
    $('#fwdpMapSearchWrap').hide();
    $('#fwdpMapSelectedWrap').show();
    $('#fwdpMapSelectedWrap .fwdp-label').first().text('Route Summary');
    $('#fwdpRouteSummary').show();
    $('#fwdpNavActions').show();
    $('#fwdpMapModalFooter').hide();
  } else {
    $('#mapModalLabel').text('Select Location on Map');
    $('#fwdpMapSearchWrap').show();
    $('#fwdpMapSelectedWrap').show();
    $('#fwdpMapSelectedWrap .fwdp-label').first().text('Selected Address');
    $('#fwdpRouteSummary').hide().empty();
    $('#fwdpNavActions').hide();
    $('#fwdpMapModalFooter').show();
  }
}

$(document).ready(function () {
  document.getElementById('fieldWorkDailyPlansForm').addEventListener('submit', function (e) {
    $('.error').hide();
    const workType = document.getElementById('user_field_work_type').value;
    let isValid = true;
    let mapMissing = false;

    const userId = $('#user_id').val();
    const userErr = document.getElementById('user_id_err');
    if (!userId || userId == '0') {
      if (userErr) {
        userErr.classList.remove('d-none');
        userErr.textContent = 'This field is required.';
      }
      toast('Please select a user.');
      isValid = false;
    } else if (userErr) {
      userErr.classList.add('d-none');
    }

    if (workType == '1' || workType == '2') {
      const fieldsToValidate = ['plan_date'];
      fieldsToValidate.forEach(function (id) {
        const field = document.getElementById(id);
        const errorDiv = document.getElementById(id + '_err');
        if (!field.value.trim()) {
          errorDiv.classList.remove('d-none');
          errorDiv.textContent = 'This field is required.';
          isValid = false;
        } else {
          errorDiv.classList.add('d-none');
        }
      });

      $('#locationCardsContainer .fwdp-location-card').each(function () {
        const latInput = $(this).find("input[name='plan_latitude[]']");
        const lngInput = $(this).find("input[name='plan_longitude[]']");

        if (!latInput.val() || isNaN(latInput.val()) || !lngInput.val() || isNaN(lngInput.val())) {
          mapMissing = true;
          isValid = false;
        }
      });

      if (mapMissing) {
        toast('Please pick a location on the map.');
      }

      if (fwdpHasDuplicatePlanTimes()) {
        toast('Duplicate planned times are not allowed.');
        isValid = false;
      }
    }

    if (workType == '3') {
      const fieldsToValidate = ['work_latitude', 'work_longitude', 'work_time', 'work_from_date', 'work_to_date'];
      fieldsToValidate.forEach(function (id) {
        const field = document.getElementById(id);
        const errorDiv = document.getElementById(id + '_err');
        if (!field.value.trim()) {
          errorDiv.classList.remove('d-none');
          errorDiv.textContent = 'This field is required.';
          isValid = false;
        } else {
          errorDiv.classList.add('d-none');
        }
      });

      const fromDate = document.getElementById('work_from_date');
      const toDate = document.getElementById('work_to_date');
      const toDateErr = document.getElementById('work_to_date_err');
      if (fromDate && toDate && fromDate.value.trim() && toDate.value.trim() && toDate.value < fromDate.value) {
        if (toDateErr) {
          toDateErr.classList.remove('d-none');
          toDateErr.textContent = 'To Date must be on or after From Date.';
        }
        toast('To Date must be on or after From Date.');
        isValid = false;
      }
    }

    if (!isValid) {
      e.preventDefault();
    }
  });

  $('#user_id').on('change', function () {
    handleUserSelection();
  });

  if ($('#user_id').val() && $('#user_id').val() != '0') {
    $('#user_id').trigger('change');
  }

  $('#plan_date').on('change', function () {
    checkData();
  });

  const workFromDateEl = document.getElementById('work_from_date');
  if (workFromDateEl) {
    workFromDateEl.addEventListener('change', function () {
      handleFromDateChange();
    });
  }

  $(document).on('click', '.fwdp-remove-row', function () {
    removeRow(this);
  });

  $(document).on('change', "input[name='plan_time[]']", function () {
    fwdpValidateUniquePlanTime(this);
  });

  $('#addMoreBtn').click(function () {
    $('#locationCardsContainer').append(fwdpBuildLocationCardHtml());
    fwdpRenumberLocationCards();
  });

  $(document).on('click', '.addMapBtn', function () {
    const $card = $(this).closest('.fwdp-location-card');
    if ($card.length > 0) {
      currentLatInput = $card.find('.lat').first();
      currentLngInput = $card.find('.lng').first();
      currentAddressInput = $card.find('.fwdp-address-field').first();
    } else {
      const $section = $(this).closest('#workType2');
      currentLatInput = $section.find('#work_latitude');
      currentLngInput = $section.find('#work_longitude');
      currentAddressInput = $section.find('#work_address');
    }

    fwdpPendingMapInit = false;
    fwdpToggleMapModalMode(false);
    $('#mapModal').modal('show');
  });

  $('#updateMapBtn').click(function () {
    currentLatInput = null;
    currentLngInput = null;
    currentAddressInput = null;
    fwdpPendingMapInit = true;
    fwdpToggleMapModalMode(true);
    $('#mapModal').modal('show');
  });

  $('#fwdpConfirmLocation').click(function () {
    fwdpConfirmLocation();
  });

  $('#mapModal').on('shown.bs.modal', function () {
    if (fwdpPendingMapInit !== null) {
      fwdpInitializeMap(fwdpPendingMapInit);
      fwdpPendingMapInit = null;
    }
    if (fwdpMap) {
      google.maps.event.trigger(fwdpMap, 'resize');
    }
    if (!fwdpMapViewAll) {
      fwdpInitAutocomplete();
      fwdpFixPacContainer();
    }
  });

  $('#mapModal').on('hidden.bs.modal', function () {
    if (fwdpMarker) {
      fwdpMarker.setMap(null);
      fwdpMarker = null;
    }
    fwdpClearRouteLayers();
    if (fwdpAutocomplete) {
      google.maps.event.clearInstanceListeners(fwdpAutocomplete);
      fwdpAutocomplete = null;
    }
    document.querySelectorAll('.pac-container').forEach(function (el) {
      el.parentNode.removeChild(el);
    });
    pendingLat = null;
    pendingLng = null;
    pendingAddress = '';
    $('#fwdpMapSearch').val('');
    fwdpPendingMapInit = null;
  });

  const userSelect = document.getElementById('user_id');
  if (userSelect) {
    userSelect.dispatchEvent(new Event('change'));
  }
});

function checkData() {
  const userId = $('#user_id').val();
  const planDate = $('#plan_date').val();

  if (userId && userId != '0' && planDate) {
    $.ajax({
      url: 'ajax.php',
      type: 'POST',
      data: {
        table: 'field_work_daily_plans',
        page: 'fetchFieldWorkDailyPlans',
        user_id: userId,
        date: planDate
      },
      success: function (response) {
        const data = JSON.parse(response);
        const qeury_reslut = data['qeury_reslut'];
        if (qeury_reslut) {
          window.location.href = fwdpPageUrl({ user_id: userId, date: planDate });
        }
      },
      error: function () {
        toast('An error occurred while processing the request.');
      }
    });
  } else {
    toast('Please select a user and date.');
  }
}

function handleFromDateChange() {
  const fromDateInput = document.getElementById('work_from_date');
  const toDateInput = document.getElementById('work_to_date');

  toDateInput.min = fromDateInput.value;
  if (toDateInput.value < fromDateInput.value) {
    toDateInput.value = fromDateInput.value;
  }

  const userId = $('#user_id').val();
  const planDate = $('#work_from_date').val();

  if (userId && userId != '0' && planDate) {
    $.ajax({
      url: 'ajax.php',
      type: 'POST',
      data: {
        table: 'user_field_work_details',
        page: 'fetchFieldWorkDailyPlans',
        user_id: userId,
        date: planDate
      },
      success: function (response) {
        const data = JSON.parse(response);
        const qeury_reslut = data['qeury_reslut'];
        if (qeury_reslut) {
          window.location.href = fwdpPageUrl({ user_id: userId, date: planDate });
        }
      },
      error: function () {
        toast('An error occurred while processing the request.');
      }
    });
  } else {
    toast('Please select a user and date.');
  }
}

function handleUserSelection() {
  $('#locationCardsContainer').empty();
  document.getElementById('work_latitude').value = '';
  document.getElementById('work_longitude').value = '';
  document.getElementById('location_name').value = '';
  document.getElementById('work_from_date').value = '';
  document.getElementById('work_to_date').value = '';
  document.getElementById('work_time').value = '';
  document.getElementById('work_radius').value = '';
  document.getElementById('contact_person').value = '';
  document.getElementById('mobile').value = '';
  document.getElementById('work_address').value = '';

  const userSelect = document.getElementById('user_id');
  const selectedOption = userSelect.options[userSelect.selectedIndex];
  const workType = selectedOption.getAttribute('data-work-type');

  document.getElementById('user_field_work_type').value = workType;
  document.getElementById('toDateDiv').classList.add('d-none');
  document.querySelectorAll('.fwdp-work-type').forEach(function (el) {
    el.classList.add('d-none');
  });

  if (workType == '1' || workType == '2') {
    document.getElementById('workType1').classList.remove('d-none');
    document.getElementById('toDateDiv').classList.remove('d-none');
    $('#locationCardsContainer').append(fwdpBuildLocationCardHtml());
    fwdpRenumberLocationCards();
  } else if (workType == '3') {
    document.getElementById('workType2').classList.remove('d-none');
  }

  if (workType == '1' || workType == '2' || workType == '3') {
    document.getElementById('saveButton').classList.remove('d-none');
  } else {
    document.getElementById('saveButton').classList.add('d-none');
  }

  const userId = document.getElementById('user_id').value;
  const date = new URLSearchParams(window.location.search).get('date');

  if (workType == '1' || workType == '2') {
    fetchFieldWorkDailyPlans(userId, date);
  } else if (workType == '3') {
    fetchUserFieldWorkDetails(userId, date);
  }

  document.querySelectorAll('.fwdp-work-type').forEach(function (section) {
    if (section.classList.contains('d-none')) {
      section.querySelectorAll('input, select, textarea').forEach(function (input) {
        input.disabled = true;
      });
    } else {
      section.querySelectorAll('input, select, textarea').forEach(function (input) {
        input.disabled = false;
      });
    }
  });
}

function fetchFieldWorkDailyPlans(userId, date) {
  $.ajax({
    url: 'ajax.php',
    type: 'POST',
    data: {
      table: 'field_work_daily_plans',
      page: 'fetchFieldWorkDailyPlans',
      user_id: userId,
      date: date
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        const qeury_reslut = data['qeury_reslut'];
        if (qeury_reslut) {
          populateLocationCards(qeury_reslut);
        }
      } catch (e) {
        console.error('Error parsing response:', e);
      }
    },
    error: function (xhr, status, error) {
      console.error('Error fetching Field Work Daily Plans:', error);
    }
  });
}

function fetchUserFieldWorkDetails(userId, date) {
  $.ajax({
    url: 'ajax.php',
    type: 'POST',
    data: {
      table: 'user_field_work_details',
      page: 'fetchFieldWorkDailyPlans',
      user_id: userId,
      date: date
    },
    success: function (response) {
      try {
        const data = JSON.parse(response);
        const qeury_reslut = data['qeury_reslut'];
        if (qeury_reslut) {
          populateWorkType2Fields(qeury_reslut);
        }
      } catch (e) {
        console.error('Error parsing response:', e);
      }
    },
    error: function (xhr, status, error) {
      console.error('Error fetching User Field Work Details:', error);
    }
  });
}

function populateLocationCards(data) {
  const container = $('#locationCardsContainer');
  container.empty();

  if (Array.isArray(data)) {
    data.forEach(function (row) {
      container.append(fwdpBuildLocationCardHtml(row));
    });
  } else {
    container.append(fwdpBuildLocationCardHtml(data));
  }

  container.find('.fwdp-location-card').each(function () {
    fwdpUpdateCoordPreview($(this));
  });
  fwdpRenumberLocationCards();
}

function populateWorkType2Fields(data) {
  if (!data) {
    return;
  }

  const existingHidden = document.querySelector('#workType2 input[name="user_field_work_details_id"]');
  if (existingHidden) {
    existingHidden.remove();
  }

  if (data.id) {
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'user_field_work_details_id';
    hiddenInput.value = data.id;
    document.getElementById('workType2').appendChild(hiddenInput);
  }

  document.getElementById('work_latitude').value = data.work_latitude || '';
  document.getElementById('work_longitude').value = data.work_longitude || '';
  document.getElementById('location_name').value = data.location_name || '';
  document.getElementById('work_from_date').value = data.work_from_date || '';
  document.getElementById('work_to_date').value = data.work_to_date || '';
  document.getElementById('work_time').value = data.work_time || '';
  document.getElementById('work_radius').value = data.work_radius || '';
  document.getElementById('contact_person').value = data.contact_person || '';
  document.getElementById('mobile').value = data.mobile || '';
  document.getElementById('work_address').value = data.work_address || '';
}

function removeRow(button) {
  const $cards = $('#locationCardsContainer .fwdp-location-card');
  if ($cards.length <= 1) {
    toast('At least one location is required.');
    return;
  }
  $(button).closest('.fwdp-location-card').remove();
  fwdpRenumberLocationCards();
}
