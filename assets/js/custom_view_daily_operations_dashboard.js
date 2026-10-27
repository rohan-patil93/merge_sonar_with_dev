let dodRouteMap = null;
let dodRouteMarkers = [];
let dodRoutePolyline = null;
let dodInfoWindow = null;
let dodCurrentMode = '';
let dodSelectedNavPoint = null;

const dodDefaultCenter = { lat: 17.44, lng: 78.39 };
const dodDefaultZoom = 11.5;

const dodMarkerColors = {
  pending: '#f59e0b',
  overdue: '#ef4444',
  completed: '#10b981',
  extra: '#8b5cf6',
  last_visit: '#2563eb',
  check_in: '#2563eb',
  start: '#2563eb',
  end: '#2563eb'
};

const dodStatusLabels = {
  pending: 'Pending',
  overdue: 'Overdue',
  completed: 'Completed',
  extra: 'Extra',
  last_visit: 'Last visit',
  check_in: 'Check-in'
};

function dodEscapeHtml(value) {
  return String(value == null ? '' : value)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

function getDodMapModes() {
  if (!window.dodMapData || !window.dodMapData.modes) {
    return {};
  }
  return window.dodMapData.modes;
}

function getDodMapPoints(mode) {
  const modes = getDodMapModes();
  const points = modes[mode];
  if (!points || !points.length) {
    return [];
  }
  return points;
}

function getDodMapRole() {
  return (window.dodMapData && window.dodMapData.role) ? window.dodMapData.role : '';
}

function dodIsEmployeeNavMode(mode) {
  if (getDodMapRole() != 'employee') {
    return false;
  }
  return mode == 'planned' || mode == 'visits' || mode == 'next_planned' || mode == 'last_visit';
}

function dodBuildGoogleMapsNavUrl(lat, lng) {
  return 'https://www.google.com/maps/dir/?api=1&destination=' +
    encodeURIComponent(parseFloat(lat) + ',' + parseFloat(lng));
}

function dodSetNavigateTarget(lat, lng) {
  const $btn = $('#dodNavigateBtn');
  if ($btn.length == 0) {
    return;
  }
  if (lat == null || lng == null || isNaN(parseFloat(lat)) || isNaN(parseFloat(lng))) {
    $btn.addClass('d-none').attr('href', '#');
    return;
  }
  dodSelectedNavPoint = { lat: parseFloat(lat), lng: parseFloat(lng) };
  $btn.attr('href', dodBuildGoogleMapsNavUrl(dodSelectedNavPoint.lat, dodSelectedNavPoint.lng))
    .removeClass('d-none');
}

function dodUpdateNavigateButton(mode, points) {
  const $btn = $('#dodNavigateBtn');
  if ($btn.length == 0) {
    return;
  }
  if (!dodIsEmployeeNavMode(mode) || !points || points.length == 0) {
    dodSelectedNavPoint = null;
    $btn.addClass('d-none').attr('href', '#');
    return;
  }
  const first = points[0];
  dodSetNavigateTarget(first.lat, first.lng);
}

function getDodMarkerIcon(status) {
  const color = dodMarkerColors[status] || '#2563eb';
  const size = (status == 'overdue' || status == 'check_in') ? 18 : 14;
  const svg =
    '<svg xmlns="http://www.w3.org/2000/svg" width="' + size + '" height="' + size + '" viewBox="0 0 ' + size + ' ' + size + '">' +
    '<circle cx="' + (size / 2) + '" cy="' + (size / 2) + '" r="' + ((size / 2) - 1) + '" fill="' + color + '" stroke="#ffffff" stroke-width="2"/>' +
    '</svg>';
  return {
    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(svg),
    scaledSize: new google.maps.Size(size, size),
    anchor: new google.maps.Point(size / 2, size / 2)
  };
}

function createDodMapMarker(point) {
  const statusText = dodStatusLabels[point.status] || point.status || '';
  const showNav = dodIsEmployeeNavMode(dodCurrentMode);
  const navUrl = showNav ? dodBuildGoogleMapsNavUrl(point.lat, point.lng) : '';
  const popupHtml =
    '<div class="dod-map-popup-content">' +
    '<strong>' + dodEscapeHtml(point.label || '') + '</strong>' +
    (point.sub_label ? '<div>' + dodEscapeHtml(point.sub_label) + '</div>' : '') +
    (statusText ? '<div class="dod-map-popup-status">' + dodEscapeHtml(statusText) + '</div>' : '') +
    (showNav
      ? '<a class="dod-map-popup-nav" href="' + navUrl + '" target="_blank" rel="noopener noreferrer">Navigate</a>'
      : '') +
    '</div>';

  const marker = new google.maps.Marker({
    position: { lat: point.lat, lng: point.lng },
    map: dodRouteMap,
    icon: getDodMarkerIcon(point.status),
    title: point.label || ''
  });

  marker.addListener('click', function () {
    if (!dodInfoWindow) {
      dodInfoWindow = new google.maps.InfoWindow();
    }
    dodInfoWindow.setContent(popupHtml);
    dodInfoWindow.open(dodRouteMap, marker);
    if (showNav) {
      dodSetNavigateTarget(point.lat, point.lng);
    }
  });

  return marker;
}

function addDodRouteLayer(path) {
  clearDodRouteLayer();
  dodRoutePolyline = new google.maps.Polyline({
    path: path,
    geodesic: true,
    strokeColor: '#2563eb',
    strokeOpacity: 0.9,
    strokeWeight: 4,
    map: dodRouteMap
  });
}

function clearDodRouteLayer() {
  if (dodRoutePolyline) {
    dodRoutePolyline.setMap(null);
    dodRoutePolyline = null;
  }
}

function fitDodRouteBounds(latLngs) {
  if (!latLngs.length) {
    return;
  }
  if (latLngs.length == 1) {
    dodRouteMap.setCenter(latLngs[0]);
    dodRouteMap.setZoom(13);
    return;
  }
  const bounds = new google.maps.LatLngBounds();
  latLngs.forEach(function (latLng) {
    bounds.extend(latLng);
  });
  dodRouteMap.fitBounds(bounds, {
    top: 40,
    bottom: 80,
    left: 40,
    right: 40
  });
}

function clearDodRouteMarkers() {
  dodRouteMarkers.forEach(function (marker) {
    marker.setMap(null);
  });
  dodRouteMarkers = [];
  if (dodInfoWindow) {
    dodInfoWindow.close();
  }
}

function setDodMapEmpty(show, message) {
  const emptyEl = document.getElementById('dodMapEmpty');
  if (!emptyEl) {
    return;
  }
  if (message) {
    emptyEl.textContent = message;
  }
  emptyEl.style.display = show ? 'flex' : 'none';
}

function renderDodMapMode(mode) {
  if (!dodRouteMap) {
    return;
  }

  dodCurrentMode = mode;
  clearDodRouteMarkers();
  clearDodRouteLayer();
  dodSelectedNavPoint = null;

  const points = getDodMapPoints(mode);
  const latLngs = [];
  const validPoints = [];

  points.forEach(function (point) {
    const lat = parseFloat(point.lat);
    const lng = parseFloat(point.lng);
    if (isNaN(lat) || isNaN(lng)) {
      return;
    }
    const latLng = { lat: lat, lng: lng };
    latLngs.push(latLng);
    validPoints.push({
      lat: lat,
      lng: lng,
      label: point.label,
      sub_label: point.sub_label,
      status: point.status
    });
    dodRouteMarkers.push(createDodMapMarker({
      lat: lat,
      lng: lng,
      label: point.label,
      sub_label: point.sub_label,
      status: point.status
    }));
  });

  setDodMapEmpty(latLngs.length == 0, 'No map points for this view.');
  dodUpdateNavigateButton(mode, validPoints);

  const role = getDodMapRole();
  if (role == 'employee' && mode == 'planned' && latLngs.length > 1) {
    addDodRouteLayer(latLngs);
  }

  if (latLngs.length > 0) {
    fitDodRouteBounds(latLngs);
  } else {
    dodRouteMap.setCenter(dodDefaultCenter);
    dodRouteMap.setZoom(dodDefaultZoom);
  }
}

function initializeDodRouteMap() {
  const mapEl = document.getElementById('dodRouteMap');
  if (!mapEl) {
    return;
  }

  if (!window.dodGoogleMapsKeyConfigured) {
    setDodMapEmpty(true, 'Map key not configured.');
    return;
  }

  if (typeof google == 'undefined' || !google.maps) {
    setDodMapEmpty(true, 'Map key not configured.');
    return;
  }

  dodRouteMap = new google.maps.Map(mapEl, {
    center: dodDefaultCenter,
    zoom: dodDefaultZoom,
    mapTypeControl: false,
    streetViewControl: false,
    fullscreenControl: false,
    gestureHandling: 'cooperative'
  });

  const defaultMode = (window.dodMapData && window.dodMapData.default_mode)
    ? window.dodMapData.default_mode
    : 'last_visit';

  renderDodMapMode(defaultMode);
}

$(document).ready(function () {
  initializeDodRouteMap();

  $('.dod-map-mode-btn').on('click', function () {
    const mode = $(this).data('map-mode');
    $('.dod-map-mode-btn').removeClass('active');
    $(this).addClass('active');
    renderDodMapMode(mode);
  });

  $('#dodNavigateBtn').on('click', function (e) {
    const href = $(this).attr('href');
    if (!href || href == '#') {
      e.preventDefault();
      if (typeof tostarnotify == 'function') {
        tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger',
          'animated flipInX', 'animated flipOutX', 'No location available to navigate.', '');
      }
    }
  });

  $('.dod-tab').on('click', function () {
    const tabKey = $(this).data('visit-tab');
    $('.dod-tab').removeClass('active');
    $(this).addClass('active');
    $('.dod-visit-panel').removeClass('active');
    if (tabKey == 'planned') {
      $('#dodVisitPlanned').addClass('active');
    }
    if (tabKey == 'completed') {
      $('#dodVisitCompleted').addClass('active');
    }
    if (tabKey == 'extra') {
      $('#dodVisitExtra').addClass('active');
    }
  });
});
