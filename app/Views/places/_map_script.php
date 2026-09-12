<script>
document.addEventListener('DOMContentLoaded', () => {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const mapLink = document.getElementById('openPlaceMap');
    const mapElement = document.getElementById('placePickerMap');
    if (!latInput || !lngInput || !mapElement) return;

    const readCoordinates = () => {
        const lat = Number(latInput.value);
        const lng = Number(lngInput.value);
        return Number.isFinite(lat) && Number.isFinite(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180
            ? {lat, lng}
            : null;
    };
    const syncLink = () => {
        const point = readCoordinates();
        if (!mapLink) return;
        mapLink.classList.toggle('disabled', !point);
        mapLink.href = point ? `https://www.openstreetmap.org/?mlat=${point.lat}&mlon=${point.lng}#map=11/${point.lat}/${point.lng}` : '#';
    };
    syncLink();

    if (typeof L === 'undefined') {
        mapElement.innerHTML = `<div class="h-100 d-flex align-items-center justify-content-center text-center p-4 muted"><div><i class="fa-solid fa-map-location-dot fa-2x mb-2"></i><div><?= esc(lang('App.map_unavailable_short')) ?></div></div></div>`;
        latInput.addEventListener('input', syncLink);
        lngInput.addEventListener('input', syncLink);
        return;
    }

    mapElement.innerHTML = '';
    const initial = readCoordinates();
    const map = L.map(mapElement, {scrollWheelZoom: false}).setView(initial ? [initial.lat, initial.lng] : [25, 15], initial ? 10 : 2);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker = null;
    const setPoint = (lat, lng, pan = false) => {
        lat = Number(lat.toFixed(6));
        lng = Number(lng.toFixed(6));
        latInput.value = lat;
        lngInput.value = lng;
        if (!marker) {
            marker = L.marker([lat, lng], {draggable: true}).addTo(map);
            marker.on('dragend', event => {
                const point = event.target.getLatLng();
                setPoint(point.lat, point.lng, false);
            });
        } else {
            marker.setLatLng([lat, lng]);
        }
        if (pan) map.setView([lat, lng], Math.max(map.getZoom(), 8));
        syncLink();
    };

    if (initial) setPoint(initial.lat, initial.lng, false);
    map.on('click', event => setPoint(event.latlng.lat, event.latlng.lng, true));
    [latInput, lngInput].forEach(input => input.addEventListener('change', () => {
        const point = readCoordinates();
        if (point) setPoint(point.lat, point.lng, true);
        else syncLink();
    }));
    window.setTimeout(() => map.invalidateSize(), 150);
});
</script>
