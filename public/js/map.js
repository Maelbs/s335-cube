/* Fichier : public/js/store-locator.js */

var mapInitialized = false;
var map = null;

// --- Ouverture / Fermeture ---
function toggleStoreLocator() {
    const overlay = document.getElementById('store-locator-overlay');
    const body = document.body;
    
    if (overlay.classList.contains('visible')) {
        overlay.classList.remove('visible');
        setTimeout(() => { overlay.style.visibility = 'hidden'; }, 300);
        body.style.overflow = '';
    } else {
        overlay.style.visibility = 'visible';
        requestAnimationFrame(() => { overlay.classList.add('visible'); });
        body.style.overflow = 'hidden';
    }
}

// Fermeture au clic sur le fond
// On attend que le DOM soit chargé pour attacher cet événement
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('store-locator-overlay');
    if(overlay) {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) toggleStoreLocator();
        });
    }
    
    // Initialisation des filtres
    const stockToggle = document.getElementById('stockToggle');
    const searchInput = document.getElementById('storeSearchInput');
    
    if(stockToggle) stockToggle.addEventListener('change', filterMagasins);
    if(searchInput) searchInput.addEventListener('keyup', filterMagasins);
});

// --- Gestion Onglets ---
function switchView(viewName) {
    const tabs = document.querySelectorAll('.sl-tab');
    document.getElementById('view-list').style.display = (viewName === 'list') ? 'block' : 'none';
    
    const mapContainer = document.getElementById('view-map');
    mapContainer.style.display = (viewName === 'map') ? 'block' : 'none';

    // Gestion classe active
    // On suppose l'ordre : 0 = Liste, 1 = Carte
    if(tabs.length > 0) {
        tabs[0].classList.toggle('active', viewName === 'list');
        tabs[1].classList.toggle('active', viewName === 'map');
    }

    // Initialisation Carte
    if (viewName === 'map') {
        if (!mapInitialized) {
            initMap();
            mapInitialized = true;
        } else {
            setTimeout(() => { if(map) map.invalidateSize(); }, 100);
        }
    }
}

// --- Filtres ---
function filterMagasins() {
    const stockToggle = document.getElementById('stockToggle');
    const searchInput = document.getElementById('storeSearchInput');
    const cards = document.querySelectorAll('.sl-card');

    const showOnlyStock = stockToggle.checked;
    const searchText = searchInput.value.toLowerCase();

    cards.forEach(card => {
        const hasStock = card.getAttribute('data-has-stock') === 'true';
        const searchString = card.getAttribute('data-searchString');
        
        let visible = true;
        if (showOnlyStock && !hasStock) visible = false;
        if (searchText.length > 0 && !searchString.includes(searchText)) visible = false;

        if(visible) card.classList.remove('hidden-item');
        else card.classList.add('hidden-item');
    });
}

// --- Logique Carte ---
function initMap() {
    // Vérification de sécurité
    if(typeof L === 'undefined') {
        console.error("Leaflet n'est pas chargé.");
        return;
    }

    map = L.map('sl-map').setView([46.603354, 1.888334], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // ICI : On utilise la variable globale window.magasinsData définie dans le Blade
    if (typeof window.magasinsData !== 'undefined') {
        window.magasinsData.forEach(mag => {
            if(mag.adresse) {
                const query = encodeURIComponent(mag.adresse);
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&limit=1`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.features && data.features.length > 0) {
                            const coords = data.features[0].geometry.coordinates;
                            const marker = L.marker([coords[1], coords[0]]).addTo(map);
                            
                            const stockHtml = mag.stock 
                                ? '<b style="color:#00AEEF">✔ En stock</b>' 
                                : '<span style="color:red">✖ Indisponible</span>';
                            
                            marker.bindPopup(`
                                <div style="text-align:center; font-family:sans-serif;">
                                    <strong>${mag.nom}</strong><br>
                                    <span style="font-size:0.9em">${mag.ville}</span><br>
                                    ${stockHtml}
                                </div>
                            `);
                        }
                    })
                    .catch(e => console.error(e));
            }
        });
    }
}