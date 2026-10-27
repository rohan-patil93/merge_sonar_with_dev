(() => {
	'use strict';

	function renderAttendanceLocationMap(root) {
		const pageEl = root.querySelector('#attendanceLocationMapPage');
		if (!pageEl) {
			return;
		}

		const mapEl = pageEl.querySelector('#attendanceLocationMap');
		const emptyEl = pageEl.querySelector('#attendanceLocationEmpty');
		if (!mapEl) {
			return;
		}

		if (mapEl.dataset.mapReady === '1') {
			if (mapEl._olMap) {
				mapEl._olMap.updateSize();
			}
			return;
		}

		mapEl.dataset.mapReady = '1';
		mapEl.innerHTML = '';

		let data = {};
		try {
			data = JSON.parse(pageEl.dataset.attendance || '{}');
		} catch (err) {
			data = {};
		}

		const details = Array.isArray(data.details) ? data.details : [];

		const map = new ol.Map({
			target: mapEl,
			layers: [
				new ol.layer.Tile({
					source: new ol.source.OSM()
				})
			],
			view: new ol.View({
				center: ol.proj.fromLonLat([78.354492, 22.836946]),
				zoom: 5,
				minZoom: 2,
				maxZoom: 19
			})
		});

		const pinSvg = (color) => (
			"data:image/svg+xml;utf8," +
			encodeURIComponent(
				"<svg xmlns='http://www.w3.org/2000/svg' width='96' height='120' viewBox='0 0 96 120'>" +
				"<path d='M48 2C27 2 10 19 10 40c0 27 38 74 38 74s38-47 38-74C86 19 69 2 48 2z' fill='" + color + "'/>" +
				"<circle cx='48' cy='40' r='16' fill='%23ffffff'/>" +
				"<ellipse cx='48' cy='110' rx='26' ry='8' fill='" + color + "'/>" +
				"</svg>"
			)
		);

		const inStyle = new ol.style.Style({
			image: new ol.style.Icon({
				src: pinSvg('#22c55e'),
				scale: 0.35,
				anchor: [0.5, 1]
			})
		});

		const outStyle = new ol.style.Style({
			image: new ol.style.Icon({
				src: pinSvg('#ef4444'),
				scale: 0.35,
				anchor: [0.5, 1]
			})
		});

		const features = [];
		details.forEach((row, index) => {
			const seq = index + 1;
			const inLat = parseFloat(row.clock_in_lat);
			const inLng = parseFloat(row.clock_in_long);
			const outLat = parseFloat(row.clock_out_lat);
			const outLng = parseFloat(row.clock_out_long);

			if (!Number.isNaN(inLat) && !Number.isNaN(inLng)) {
				const feature = new ol.Feature({
					geometry: new ol.geom.Point(ol.proj.fromLonLat([inLng, inLat])),
					type: 'in',
					time: row.clock_in || '-',
					sequence: seq
				});
				feature.setStyle(inStyle);
				features.push(feature);
			}

			if (!Number.isNaN(outLat) && !Number.isNaN(outLng)) {
				const feature = new ol.Feature({
					geometry: new ol.geom.Point(ol.proj.fromLonLat([outLng, outLat])),
					type: 'out',
					time: row.clock_out || '-',
					sequence: seq
				});
				feature.setStyle(outStyle);
				features.push(feature);
			}
		});

		const vectorLayer = new ol.layer.Vector({
			source: new ol.source.Vector({
				features: features
			})
		});
		map.addLayer(vectorLayer);

		mapEl._olMap = map;

		if (features.length) {
			const extent = vectorLayer.getSource().getExtent();
			map.getView().fit(extent, { padding: [40, 40, 40, 40], maxZoom: 16 });
			if (emptyEl) {
				emptyEl.style.display = 'none';
			}
		} else if (emptyEl) {
			emptyEl.style.display = 'block';
		}

		const tooltip = document.createElement('div');
		tooltip.className = 'attendance-map-tooltip';
		tooltip.style.display = 'none';
		mapEl.appendChild(tooltip);

		const overlay = new ol.Overlay({
			element: tooltip,
			offset: [0, -15],
			positioning: 'bottom-center'
		});
		map.addOverlay(overlay);

		map.on('pointermove', (evt) => {
			const feature = map.forEachFeatureAtPixel(evt.pixel, (feat) => feat);
			if (!feature) {
				tooltip.style.display = 'none';
				map.getTargetElement().style.cursor = '';
				return;
			}

			const type = feature.get('type') === 'in' ? 'Clock In' : 'Clock Out';
			const time = feature.get('time') || '-';
			const sequence = feature.get('sequence');

			tooltip.innerHTML =
				'<div class="tooltip-title">' + type + '</div>' +
				'<div><strong>Time:</strong> ' + time + '</div>' +
				'<div><strong>Sequence:</strong> ' + sequence + '</div>';
			tooltip.style.display = 'block';
			overlay.setPosition(feature.getGeometry().getCoordinates());
			map.getTargetElement().style.cursor = 'pointer';
		});
	}

	window.renderAttendanceLocationMap = renderAttendanceLocationMap;

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => {
			renderAttendanceLocationMap(document);
		});
	} else {
		renderAttendanceLocationMap(document);
	}
})();
