/* public/js/map.js */

// 1. PROTECTION CONTRE LE DOUBLE CHARGEMENT
// On utilise 'var' car 'let' plante si le fichier est chargé 2 fois.
if (typeof window.mapScriptLoaded === 'undefined') {
    window.mapScriptLoaded = true;

    // Déclaration des variables globales avec var (plus permissif)
    var mapInitialized = false;
    var map = null;
    var userCoords = null;
    var storeLocatorTimeout = null;

    // --- FONCTIONS UTILITAIRES ---

    window.getDistanceFromLatLonInKm = function(lat1, lon1, lat2, lon2) {
        var R = 6371; 
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    };

    window.deg2rad = function(deg) {
        return deg * (Math.PI / 180);
    };

    window.sortStoreList = function() {
        var container = document.getElementById('view-list');
        if(!container) return;
        
        var cards = Array.from(container.getElementsByClassName('sl-card'));

        cards.sort(function(a, b) {
            var distA = parseFloat(a.getAttribute('data-distance')) || 99999;
            var distB = parseFloat(b.getAttribute('data-distance')) || 99999;
            return distA - distB;
        });

        cards.forEach(function(card) { container.appendChild(card); });
    };

    // --- LOGIQUE D'OUVERTURE (Global) ---

    // On attache explicitement à window pour être sûr que le HTML le trouve
    window.toggleStoreLocator = function() {
        var overlay = document.getElementById("store-locator-overlay");
        var header = document.querySelector("header");
        var body = document.body;

        if (!overlay) {
            console.error("Overlay introuvable ! Vérifiez storeLocator.blade.php");
            return;
        }

        if (storeLocatorTimeout) {
            clearTimeout(storeLocatorTimeout);
            storeLocatorTimeout = null;
        }

        if (overlay.classList.contains("visible")) {
            overlay.classList.remove("visible");
            if (header) header.classList.remove("header-hidden");
            body.style.overflow = "";
            storeLocatorTimeout = setTimeout(function() { overlay.style.visibility = "hidden"; }, 300);
        } else {
            overlay.style.visibility = "visible";
            body.style.overflow = "hidden";
            if (header) header.classList.add("header-hidden");
            requestAnimationFrame(function() { overlay.classList.add("visible"); });
        }
    };

    window.switchView = function(viewName) {
        var tabs = document.querySelectorAll('.sl-tab');
        var list = document.getElementById('view-list');
        var mapDiv = document.getElementById('view-map');

        if(list && mapDiv) {
            list.style.display = (viewName === 'list') ? 'block' : 'none';
            mapDiv.style.display = (viewName === 'map') ? 'block' : 'none';
        }

        if(tabs.length > 0) {
            tabs[0].classList.toggle('active', viewName === 'list');
            tabs[1].classList.toggle('active', viewName === 'map');
        }

        if (viewName === 'map') {
            if (!mapInitialized) {
                initMap();
                mapInitialized = true;
            } else {
                setTimeout(function() { if(map) map.invalidateSize(); }, 100);
            }
        }
    };

    // --- CARTE & GÉOCODAGE ---

    window.initMap = function() {
        if (typeof L === 'undefined' || map) return;
    
        // 1. Initialisation carte
        map = L.map('sl-map').setView([46.603354, 1.888334], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);
    
        // Fonction utilitaire pour placer le marqueur
        function setUserLocation(lat, lng, sourceName) {
            // On sauvegarde la position pour le calcul des distances
            userCoords = { lat: lat, lng: lng };
            
            L.marker([lat, lng])
             .addTo(map)
             .bindPopup("<b>📍 Vous êtes ici (" + sourceName + ")</b>")
             .openPopup();
             
            map.setView([lat, lng], 10);
            loadStoresOnMap(); // On charge les magasins et on recalcule les distances
        }
    
        // Fonction de repli : Utiliser l'adresse BDD si le GPS échoue
        function useDatabaseAddress() {
            if (window.userAddress && window.userAddress.trim() !== "") {
                var query = encodeURIComponent(window.userAddress);
                fetch('https://api-adresse.data.gouv.fr/search/?q=' + query + '&limit=1')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.features && data.features.length > 0) {
                            var coords = data.features[0].geometry.coordinates;
                            // On utilise l'adresse du compte client
                            setUserLocation(coords[1], coords[0], "Adresse Profil");
                        } else {
                            loadStoresOnMap(); // Adresse introuvable
                        }
                    })
                    .catch(function() { loadStoresOnMap(); });
            } else {
                loadStoresOnMap(); // Pas d'adresse, pas de GPS -> Vue par défaut
            }
        }
    
        // --- LOGIQUE PRINCIPALE : ON TENTE D'ABORD LE GPS ---
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // SUCCÈS : On utilise le GPS (Annecy)
                    setUserLocation(position.coords.latitude, position.coords.longitude, "GPS");
                },
                function(error) {
                    // ÉCHEC (Refus ou Erreur) : On se rabat sur l'adresse du compte (Toulouse)
                    console.warn("Géolocalisation refusée ou impossible, utilisation de l'adresse du compte.");
                    useDatabaseAddress();
                }
            );
        } else {
            // Pas de support GPS
            useDatabaseAddress();
        }
    };

    window.loadStoresOnMap = function() {
        if (!window.magasinsData) return;

        window.magasinsData.forEach(function(mag) {
            if (mag.adresse) {
                var query = encodeURIComponent(mag.adresse);
                
                setTimeout(function() {
                    fetch('https://api-adresse.data.gouv.fr/search/?q=' + query + '&limit=1')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.features && data.features.length > 0) {
                            var coords = data.features[0].geometry.coordinates;
                            var lat = coords[1];
                            var lng = coords[0];

                            var marker = L.marker([lat, lng]).addTo(map);

                            var stockIcon = mag.stock ? '🟢' : '🔴';
                            var popupContent = 
                                '<div style="text-align:center; min-width: 180px;">' +
                                    '<h3 style="margin:0 0 5px 0; font-size:14px;">' + mag.nom + '</h3>' +
                                    '<div style="font-size:12px; margin-bottom:5px;">' + mag.ville + '</div>' +
                                    '<div style="font-size:11px; margin-bottom:10px;">' + stockIcon + (mag.stock ? ' En stock' : ' Indisponible') + '</div>' +
                                    '<form action="' + window.routeDefinirMagasin + '" method="POST">' +
                                        '<input type="hidden" name="_token" value="' + window.csrfToken + '">' +
                                        '<input type="hidden" name="id_magasin" value="' + mag.id + '">' +
                                        '<button type="submit" class="btn-skew-black" style="font-size:11px; padding:8px 15px; width:100%;">CHOISIR</button>' +
                                    '</form>' +
                                '</div>';

                            marker.bindPopup(popupContent);

                            if (userCoords) {
                                var distance = getDistanceFromLatLonInKm(userCoords.lat, userCoords.lng, lat, lng);
                                var cards = document.querySelectorAll('.sl-card');
                                cards.forEach(function(card) {
                                    if(card.innerText.includes(mag.nom)) {
                                        card.setAttribute('data-distance', distance);
                                        var header = card.querySelector('.sl-card-header');
                                        if(header && !header.querySelector('.dist-badge')) {
                                            var distBadge = document.createElement('span');
                                            distBadge.className = 'dist-badge';
                                            distBadge.style.cssText = "float:right; font-size:0.8rem; color:#666; font-weight:normal;";
                                            distBadge.innerHTML = '📍 ' + distance.toFixed(1) + ' km';
                                            header.appendChild(distBadge);
                                        }
                                    }
                                });
                                sortStoreList();
                            }
                        }
                    });
                }, Math.random() * 800);
            }
        });
    };

    // --- LISTENERS (Démarrage) ---
    document.addEventListener("DOMContentLoaded", function () {
        var overlay = document.getElementById("store-locator-overlay");
        if (overlay) {
            overlay.addEventListener("click", function(e) { 
                if(e.target.id === "store-locator-overlay") toggleStoreLocator(); 
            });
        }
        var closeBtn = document.querySelector(".sl-close-btn");
        if(closeBtn) {
            closeBtn.addEventListener("click", toggleStoreLocator);
        }
    });
}