let leafletMap = null;
let clickedMarker = null;
const pageEl = document.getElementById('viewMapPointsPage');
const usersLocations = JSON.parse(pageEl.dataset.usersLocations);
// const greenIcon = L.ExtraMarkers.icon({
//     icon: 'fa-motorcycle', // Font Awesome icon
//     markerColor: 'blue',
//     shape: 'circle',
//     prefix: 'fas'
// });
const greenIcon = L.divIcon({
    html: '<i class="fas fa-motorcycle motorcycle-icon" ></i>',
    className: 'custom-div-icon', // No default styles
    // iconSize: [48, 48], // Set the size of the icon
    // iconAnchor: [24, 48] // Center the icon properly (adjust as needed)
});
console.log(usersLocations);
$(document).ready(function () {
    $("#mapModal").on("shown.bs.modal", function () {
        leafletMap.invalidateSize();
    });

    initializeVisitedMap(usersLocations);
    // Handle close button click
    $("#closeContainer").on('click', function () {
        $("#infoContainer").addClass('d-none');
    });

});


function initializeVisitedMap(visitedLocationsObj) {
    const visitedLocations = Object.values(visitedLocationsObj); // Convert object to array

    // Check if Leaflet map is already initialized
    if (leafletMap) {
        // Clear previous markers and layers, retain tile layer
        leafletMap.eachLayer(function (layer) {
            if (!layer._url) leafletMap.removeLayer(layer);
        });
    } else {
        // Initialize Leaflet map for the first time
        leafletMap = L.map("map").setView([22.836946, 78.354492], 5); // Default center
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors"
        }).addTo(leafletMap);
        // Attach the click event listener for "Add Map" mode
        leafletMap.on('click', function (e) {

            const { lat, lng } = e.latlng;
            if (clickedMarker) {
                clickedMarker.remove();
            }
            $("#plan_latitude").val(lat.toFixed(6)); // Limit to 6 decimal places
            $("#plan_longitude").val(lng.toFixed(6));
            fetchAddressFromCoordinates(lat, lng);
            clickedMarker = L.marker([lat, lng]).addTo(leafletMap);

            // Optionally bind a popup to the marker
            clickedMarker.bindPopup(`<b>Clicked Location</b><br>Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();


        });
    }

    const visitedPoints = []; // To store visited marker points for bounds and polyline

    // Add markers for visited locations
    visitedLocations.forEach(location => {
        const lat = location.lat;
        const lng = location.lng;

        visitedPoints.push([lat, lng]); // Add to bounds array

        // Add green marker
        const marker = L.marker([lat, lng], { icon: greenIcon }).addTo(leafletMap);
        // Bind popup
        const popupContent = `<b>Name:</b> ${location.display_name}<br><b>Time:</b> ${location.map_time || "No Time"}<br>`;
        marker.bindPopup(popupContent);

        // Show popup on hover
        marker.on('mouseover', function (e) {
            this.openPopup();
        });

        // Close popup on mouseout
        marker.on('mouseout', function (e) {
            this.closePopup();
        });


        marker.on('click', function (e) {
            // Populate container data
            $("#display_name").val(location.display_name);
            $("#user_id").val(location.user_id);
            $("#plan_date").val(location.map_date);
            // $("#plan_latitude").val(location.lat);
            // $("#plan_longitude").val(location.lng);

            $("#infoContainer").removeClass('d-none');

        });
    });


    // Automatically adjust the map to fit all markers
    if (visitedPoints.length > 0) {
        const bounds = L.latLngBounds(visitedPoints); // Create bounds from all points
        leafletMap.fitBounds(bounds, { padding: [5, 20] }); // Add padding for better visibility
    }
}


function fetchAddressFromCoordinates(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`;

    fetch(url)
        .then((response) => response.json())
        .then((data) => {
            if (data && data.display_name) {
                const address = data.display_name;
                $('#plan_address').val(address)
            }
        })
        .catch((error) => {
            console.error("Error fetching address:", error);
        });
}
