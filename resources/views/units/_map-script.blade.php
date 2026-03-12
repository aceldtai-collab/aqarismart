<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mapContainer = document.getElementById('unit-map');
        if (!mapContainer || typeof L === 'undefined') {
            return;
        }

        const latInput = document.getElementById('lat');
        const lngInput = document.getElementById('lng');

        const fallbackLat = 31.9539;
        const fallbackLng = 35.9106;

        const initialLat = latInput && latInput.value ? parseFloat(latInput.value) : fallbackLat;
        const initialLng = lngInput && lngInput.value ? parseFloat(lngInput.value) : fallbackLng;

        const map = L.map(mapContainer).setView([
            Number.isFinite(initialLat) ? initialLat : fallbackLat,
            Number.isFinite(initialLng) ? initialLng : fallbackLng,
        ], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);

        let marker = L.marker([
            Number.isFinite(initialLat) ? initialLat : fallbackLat,
            Number.isFinite(initialLng) ? initialLng : fallbackLng,
        ], { draggable: true }).addTo(map);

        const updateInputs = (lat, lng) => {
            if (latInput) {
                latInput.value = Number(lat).toFixed(7);
            }
            if (lngInput) {
                lngInput.value = Number(lng).toFixed(7);
            }
        };

        marker.on('dragend', (event) => {
            const { lat, lng } = event.target.getLatLng();
            updateInputs(lat, lng);
        });

        map.on('click', (event) => {
            const { lat, lng } = event.latlng;
            marker.setLatLng([lat, lng]);
            updateInputs(lat, lng);
        });

        if (latInput) {
            latInput.addEventListener('change', () => {
                const lat = parseFloat(latInput.value);
                const lng = lngInput ? parseFloat(lngInput.value) : NaN;
                if (Number.isFinite(lat) && Number.isFinite(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.panTo([lat, lng]);
                }
            });
        }

        if (lngInput) {
            lngInput.addEventListener('change', () => {
                const lat = latInput ? parseFloat(latInput.value) : NaN;
                const lng = parseFloat(lngInput.value);
                if (Number.isFinite(lat) && Number.isFinite(lng)) {
                    marker.setLatLng([lat, lng]);
                    map.panTo([lat, lng]);
                }
            });
        }
    });
</script>
