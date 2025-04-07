<div class="relative">
    <div id="map" class="h-96 rounded-lg shadow-lg"></div>
    <div class="absolute top-4 right-4 z-[1000] space-y-2">
        <button id="zoomIn" class="bg-white p-2 rounded shadow hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </button>
        <button id="zoomOut" class="bg-white p-2 rounded shadow hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
            </svg>
        </button>
        <button id="centerMap" class="bg-white p-2 rounded shadow hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
    </div>
</div>

<style>
    .leaflet-popup-content {
        font-family: system-ui, -apple-system, sans-serif;
    }

    .map-marker {
        background-color: #3B82F6;
        border: 2px solid white;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .cluster-marker {
        background-color: #2563EB;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.css" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de la carte
    const map = L.map('map', {
        center: [0, 0],
        zoom: 2,
        minZoom: 2,
        maxZoom: 18
    });

    // Ajout du fond de carte OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Récupération des données géographiques
    const geoData = {!! json_encode($data) !!};

    // Fonction pour créer une popup personnalisée
    function createPopup(properties) {
        let content = '<div class="p-2">';
        Object.entries(properties).forEach(([key, value]) => {
            content += `<p><strong>${key}:</strong> ${value}</p>`;
        });
        content += '</div>';
        return content;
    }

    // Création des markers pour le clustering
    const markers = L.markerClusterGroup();

    // Ajout des données à la carte
    if (Array.isArray(geoData)) {
        // Si les données sont des points
        geoData.forEach(point => {
            if (point.lat && point.lng) {
                const marker = L.marker([point.lat, point.lng])
                    .bindPopup(createPopup(point));
                markers.addLayer(marker);
            }
        });
        map.addLayer(markers);

        // Ajuster la carte pour centrer les points
        const bounds = markers.getBounds();
        if (bounds.isValid()) {
            map.fitBounds(bounds);
        }
    } else {
        // Si les données sont un GeoJSON
        const geoLayer = L.geoJSON(geoData, {
            onEachFeature: function(feature, layer) {
                layer.bindPopup(createPopup(feature.properties));
            }
        }).addTo(map);

        map.fitBounds(geoLayer.getBounds());
    }

    // Contrôles personnalisés pour zoom et recentrer
    document.getElementById('zoomIn').addEventListener('click', () => map.zoomIn());
    document.getElementById('zoomOut').addEventListener('click', () => map.zoomOut());
    document.getElementById('centerMap').addEventListener('click', () => map.setView([0, 0], 2));
});
</script>
