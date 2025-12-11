/* public/js/map.js */

let mapInitialized = false;
let map = null;
let userCoords = null; // Stockera [lat, lng] du client

// --- 1. FONCTIONS UTILITAIRES (Distance & Tri) ---

// Formule de Haversine pour calculer la distance en km
function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
    var R = 6371; // Rayon de la terre en km
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
        Math.sin(dLon / 2) * Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var d = R * c; // Distance en km
    return d;
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}

// Fonction pour trier la liste HTML
function sortStoreList() {
    const container = document.getElementById('view-list');
    const cards = Array.from(container.getElementsByClassName('sl-card'));

    cards.sort((a, b) => {
        const distA = parseFloat(a.getAttribute('data-distance')) || 99999;
        const distB = parseFloat(b.getAttribute('data-distance')) || 99999;
        return distA - distB;
    });

    // Réinsertion dans l'ordre
    cards.forEach(card => container.appendChild(card));
}

// --- 2. LOGIQUE DE CARTE ---

function initMap() {
    // Si déjà initialisé, on ne refait pas tout
    if (map) return;

    map = L.map('sl-map').setView([46.603354, 1.888334], 6); // Centre France
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    // A. Si le client a une adresse, on la géocode d'abord pour avoir le point de référence
    if (window.userAddress) {
        geocodeUserAndSort(window.userAddress);
    } else {
        // Sinon on charge juste les magasins sans tri
        loadStoresOnMap();
    }
}

// Géocodage de l'adresse client
function geocodeUserAndSort(address) {
    const query = encodeURIComponent(address);
    fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&limit=1`)
        .then(res => res.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                const coords = data.features[0].geometry.coordinates; // [Lon, Lat]
                userCoords = { lat: coords[1], lng: coords[0] };

                // On ajoute un marqueur bleu pour le client
                L.marker([userCoords.lat, userCoords.lng])
                 .addTo(map)
                 .bindPopup("<b>📍 Votre adresse</b><br>" + address)
                 .openPopup();

                // On charge les magasins maintenant qu'on a la ref client
                loadStoresOnMap();
            } else {
                loadStoresOnMap(); // Fallback si adresse client non trouvée
            }
        })
        .catch(() => loadStoresOnMap());
}

function loadStoresOnMap() {
    if (!window.magasinsData) return;

    window.magasinsData.forEach(mag => {
        if (mag.adresse) {
            const query = encodeURIComponent(mag.adresse);
            
            // Délai aléatoire très court pour éviter de spammer l'API brutalement
            setTimeout(() => {
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if (data.features && data.features.length > 0) {
                        const coords = data.features[0].geometry.coordinates; // [Lon, Lat]
                        const lat = coords[1];
                        const lng = coords[0];

                        // 1. Ajouter Marqueur
                        const marker = L.marker([lat, lng]).addTo(map);

                        // 2. Créer le contenu HTML du Popup avec le FORMULAIRE
                        // Note: On utilise window.csrfToken et window.routeDefinirMagasin injectés par Blade
                        const stockHtml = mag.stock 
                            ? '<b style="color:#00AEEF">✔ En stock</b>' 
                            : '<span style="color:#999">✖ Indisponible</span>';

                        const popupContent = `
                            <div style="text-align:center; min-width: 200px;">
                                <h3 style="margin:0 0 5px 0; font-size:14px; font-weight:800;">${mag.nom}</h3>
                                <div style="font-size:12px; margin-bottom:10px;">${mag.ville}</div>
                                <div style="margin-bottom:10px;">${stockHtml}</div>
                                
                                <form action="${window.routeDefinirMagasin}" method="POST">
                                    <input type="hidden" name="_token" value="${window.csrfToken}">
                                    <input type="hidden" name="id_magasin" value="${mag.id}">
                                    <button type="submit" class="btn-skew-black" style="transform: scale(0.8); width:100%;">
                                        <span class="btn-content" style="font-size:12px;">CHOISIR CE MAGASIN</span>
                                    </button>
                                </form>
                            </div>
                        `;

                        marker.bindPopup(popupContent);

                        // 3. Calcul de Distance & Mise à jour de la liste
                        if (userCoords) {
                            const distance = getDistanceFromLatLonInKm(userCoords.lat, userCoords.lng, lat, lng);
                            
                            // On cherche la carte correspondante dans la liste HTML
                            // On suppose que l'ordre du JSON correspond à l'ordre d'affichage initial ou on cherche par ID ?
                            // Mieux : Ajoutons un attribut data-id aux cartes Blade pour les retrouver
                            // *Astuce simple:* on cherche par le nom ou l'index si ça correspond. 
                            // Pour faire propre, il faudrait ajouter data-id="${mag.id}" dans le Blade.
                            
                            // Méthode robuste : Recherche par texte (nom) ou index
                            // Pour simplifier ici, on va chercher tous les éléments .sl-card 
                            // et trouver celui qui contient le nom du magasin.
                            const cards = document.querySelectorAll('.sl-card');
                            cards.forEach(card => {
                                if(card.querySelector('h3').innerText.includes(mag.nom)) {
                                    // On injecte la distance
                                    card.setAttribute('data-distance', distance);
                                    
                                    // On affiche la distance dans la carte (Optionnel mais cool)
                                    const infoDiv = card.querySelector('.sl-card-info');
                                    if(!infoDiv.querySelector('.dist-info')) {
                                        const distSpan = document.createElement('div');
                                        distSpan.className = 'dist-info';
                                        distSpan.style.color = '#666';
                                        distSpan.style.fontSize = '0.8rem';
                                        distSpan.style.marginTop = '5px';
                                        distSpan.innerHTML = `🏁 à <b>${distance.toFixed(1)} km</b> de chez vous`;
                                        infoDiv.appendChild(distSpan);
                                    }
                                }
                            });

                            // Une fois qu'on a mis à jour une distance, on relance le tri
                            sortStoreList();
                        }
                    }
                })
                .catch(e => console.error(e));
            }, Math.random() * 1000); // Petit délai pour lisser la charge
        }
    });
}

// Fonction switchView (déjà présente, assurez-vous d'appeler initMap si map view)
function switchView(viewName) {
    const tabs = document.querySelectorAll('.sl-tab');
    document.getElementById('view-list').style.display = (viewName === 'list') ? 'block' : 'none';
    
    const mapContainer = document.getElementById('view-map');
    mapContainer.style.display = (viewName === 'map') ? 'block' : 'none';

    if(tabs.length > 0) {
        tabs[0].classList.toggle('active', viewName === 'list');
        tabs[1].classList.toggle('active', viewName === 'map');
    }

    if (viewName === 'map') {
        if (!mapInitialized) {
            initMap();
            mapInitialized = true;
        } else {
            setTimeout(() => { if(map) map.invalidateSize(); }, 100);
        }
    }
}