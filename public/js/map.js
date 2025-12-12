/* public/js/map.js */

if (typeof window.mapScriptLoaded === 'undefined') {
    window.mapScriptLoaded = true;

    var mapInitialized = false;
    var map = null;
    var markersLayer = null; // Nouveau : Pour gérer le groupe de marqueurs
    var userCoords = null;
    var storeLocatorTimeout = null;

    // --- 1. DÉFINITION DES ICÔNES ---
    // Icône Verte (Client)
    var greenIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    // Icône Rouge (Magasin Sélectionné)
    var redIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    // Icône Bleue (Magasins standards)
    var blueIcon = new L.Icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
        iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41]
    });

    // --- FONCTIONS UTILITAIRES ---
    window.getDistanceFromLatLonInKm = function (lat1, lon1, lat2, lon2) {
        var R = 6371;
        var dLat = deg2rad(lat2 - lat1);
        var dLon = deg2rad(lon2 - lon1);
        var a = Math.sin(dLat / 2) * Math.sin(dLat / 2) + Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * Math.sin(dLon / 2) * Math.sin(dLon / 2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    };
    window.deg2rad = function (deg) { return deg * (Math.PI / 180); };

    // --- GESTION DE L'AFFICHAGE ET DU FILTRE ---

    // Cette fonction gère tout : filtre la liste HTML ET recharge la carte
    window.refreshStoreDisplay = function () {
        var onlyStock = document.getElementById('stockToggle') ? document.getElementById('stockToggle').checked : false;

        // A. FILTRE LISTE HTML
        var cards = document.querySelectorAll('.sl-card');
        cards.forEach(function (card) {
            var hasStock = card.getAttribute('data-has-stock') === 'true';
            if (onlyStock && !hasStock) {
                card.style.display = 'none';
            } else {
                card.style.display = 'block';
            }
        });

        // B. FILTRE CARTE
        loadStoresOnMap();
    };

    window.initMap = function () {
        if (typeof L === 'undefined' || map) return;

        map = L.map('sl-map').setView([46.603354, 1.888334], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);

        // Création du groupe de calques pour pouvoir les supprimer facilement
        markersLayer = L.layerGroup().addTo(map);

        function setUserLocation(lat, lng) {
            userCoords = { lat: lat, lng: lng };
            L.marker([lat, lng], { icon: redIcon }).addTo(map).bindPopup("<b>Vous êtes ici</b>").openPopup();
            map.setView([lat, lng], 10);

            // Calcul des distances dans la liste
            var cards = document.querySelectorAll('.sl-card');
            // ... (Votre logique de calcul de distance existante pourrait aller ici si besoin de recalculer)

            refreshStoreDisplay(); // On lance l'affichage
        }

        function useDatabaseAddress() {
            if (window.userAddress && window.userAddress.trim() !== "") {
                var url = 'https://api-adresse.data.gouv.fr/search/?q=' + encodeURIComponent(window.userAddress) + '&limit=1';
                if (window.userAddress.includes("74")) url += '&deptCODE=74';

                fetch(url).then(r => r.json()).then(data => {
                    if (data.features && data.features.length > 0) {
                        var c = data.features[0].geometry.coordinates;
                        setUserLocation(c[1], c[0], "Adresse Profil");
                    } else {
                        refreshStoreDisplay();
                    }
                }).catch(() => refreshStoreDisplay());
            } else {
                refreshStoreDisplay();
            }
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function (p) { setUserLocation(p.coords.latitude, p.coords.longitude, "GPS"); },
                function () { useDatabaseAddress(); }
            );
        } else {
            useDatabaseAddress();
        }
    };
    if (userCoords) {
        var dist = getDistanceFromLatLonInKm(userCoords.lat, userCoords.lng, lat, lng);
        // Mise à jour du badge dans la liste HTML correspondante
        var card = Array.from(document.querySelectorAll('.sl-card')).find(c => c.innerText.includes(mag.nom));
        if (card) {
            card.setAttribute('data-distance', dist);
            var header = card.querySelector('.sl-card-header');
            if (header && !header.querySelector('.dist-badge')) {
                var b = document.createElement('span');
                b.className = 'dist-badge';
                b.style.cssText = "float:right; font-size:0.8rem; color:#666; font-weight:normal;";
                b.innerHTML = dist.toFixed(1) + ' km';
                header.appendChild(b);
            }
        }
    }
    window.loadStoresOnMap = function () {
        if (!window.magasinsData || !markersLayer) return;

        // 1. On nettoie les anciens marqueurs (indispensable pour le filtre)
        markersLayer.clearLayers();

        // 2. On récupère l'état du filtre stock
        var onlyStock = document.getElementById('stockToggle') ? document.getElementById('stockToggle').checked : false;

        window.magasinsData.forEach(function (mag) {

            // FILTRE : Si on veut que du stock et que le magasin n'en a pas, on passe au suivant
            if (onlyStock && !mag.stock) return;

            if (mag.adresse) {
                var query = encodeURIComponent(mag.adresse);

                // Petit délai aléatoire pour éviter de spammer l'API (Rate Limit)
                setTimeout(function () {
                    fetch('https://api-adresse.data.gouv.fr/search/?q=' + query + '&limit=1')
                        .then(r => r.json())
                        .then(data => {
                            if (data.features && data.features.length > 0) {
                                var coords = data.features[0].geometry.coordinates;
                                var lat = coords[1];
                                var lng = coords[0];

                                // CHOIX DE L'ICÔNE : Rouge si sélectionné, Bleu sinon
                                var iconToUse = mag.selected ? greenIcon : blueIcon;

                                var marker = L.marker([lat, lng], { icon: iconToUse });

                                // Contenu Popup
                                var stockHtml = mag.stock
                                    ? '<div style="font-size:11px; margin-bottom:10px;">🟢 En stock</div>'
                                    : '<div style="font-size:11px; margin-bottom:10px;">🔴 Indisponible</div>';

                                var btnHtml = mag.selected
                                    ? '<button class="btn-skew-black" style="background:#28a745; border-color:#28a745; width:100%; cursor:default;">DÉJÀ SÉLECTIONNÉ</button>'
                                    : '<form action="' + window.routeDefinirMagasin + '" method="POST">' +
                                    '<input type="hidden" name="_token" value="' + window.csrfToken + '">' +
                                    '<input type="hidden" name="id_magasin" value="' + mag.id + '">' +
                                    '<button type="submit" class="btn-skew-black" style="font-size:11px; padding:8px 15px; width:100%;">CHOISIR</button>' +
                                    '</form>';

                                marker.bindPopup(
                                    '<div style="text-align:center; min-width: 180px;">' +
                                    '<h3 style="margin:0 0 5px 0; font-size:14px;">' + mag.nom + '</h3>' +
                                    '<div style="font-size:12px; margin-bottom:5px;">' + mag.ville + '</div>' +
                                    stockHtml +
                                    btnHtml +
                                    '</div>'
                                );

                                // IMPORTANT : On ajoute au groupe markersLayer, pas directement à map
                                markersLayer.addLayer(marker);

                                // Calcul distance (si userCoords existe)
                                if (userCoords) {
                                    var dist = getDistanceFromLatLonInKm(userCoords.lat, userCoords.lng, lat, lng);
                                    // Mise à jour du badge dans la liste HTML correspondante
                                    var card = Array.from(document.querySelectorAll('.sl-card')).find(c => c.innerText.includes(mag.nom));
                                    if (card) {
                                        card.setAttribute('data-distance', dist);
                                        var header = card.querySelector('.sl-card-header');
                                        if (header && !header.querySelector('.dist-badge')) {
                                            var b = document.createElement('span');
                                            b.className = 'dist-badge';
                                            b.style.cssText = "float:right; font-size:0.8rem; color:#666; font-weight:normal;";
                                            b.innerHTML = dist.toFixed(1) + ' km';
                                            header.appendChild(b);
                                        }
                                    }
                                }
                            }
                        });
                }, Math.random() * 1000); // Délai pour l'API
            }
        });

        // On retrie la liste après un petit délai pour laisser le temps aux distances d'arriver
        setTimeout(window.sortStoreList, 1500);
    };

    // --- ÉCOUTEURS D'ÉVÉNEMENTS ---
    document.addEventListener("DOMContentLoaded", function () {
        // ... (gestion overlay existante) ...
        var overlay = document.getElementById("store-locator-overlay");
        if (overlay) overlay.addEventListener("click", function (e) { if (e.target.id === "store-locator-overlay") toggleStoreLocator(); });
        var closeBtn = document.querySelector(".sl-close-btn");
        if (closeBtn) closeBtn.addEventListener("click", toggleStoreLocator);

        // NOUVEAU : Écouteur sur le switch de stock
        var stockToggle = document.getElementById('stockToggle');
        if (stockToggle) {
            stockToggle.addEventListener('change', function () {
                // Quand on clique sur le switch, on relance l'affichage
                window.refreshStoreDisplay();
            });
        }
    });

    // ... (restes des fonctions toggleStoreLocator, switchView, sortStoreList...) ...
    window.toggleStoreLocator = function () {
        /* ... VOTRE CODE EXISTANT ... */
        var overlay = document.getElementById("store-locator-overlay");
        var header = document.querySelector("header");
        var body = document.body; if (!overlay) return;

        if (storeLocatorTimeout) {
            clearTimeout(storeLocatorTimeout);
            storeLocatorTimeout = null;
        }
        if (overlay.classList.contains("visible")) {
            overlay.classList.remove("visible");
            if (header) header.classList.remove("header-hidden");
            body.style.overflow = "";
            storeLocatorTimeout = setTimeout(function () { overlay.style.visibility = "hidden"; }, 300);
        }
        else {
            overlay.style.visibility = "visible";
            body.style.overflow = "hidden";
            if (header) header.classList.add("header-hidden"); requestAnimationFrame(function () {
                overlay.classList.add("visible");
            });
        }
        
    };


    window.switchView = function (viewName) {
        var tabs = document.querySelectorAll('.sl-tab');
        var list = document.getElementById('view-list');
        var mapDiv = document.getElementById('view-map');

        if (list && mapDiv) {
            list.style.display = (viewName === 'list') ? 'block' : 'none';
            mapDiv.style.display = (viewName === 'map') ? 'block' : 'none';
        }

        if (tabs.length > 0) {
            tabs[0].classList.toggle('active', viewName === 'list');
            tabs[1].classList.toggle('active', viewName === 'map');
        }

        // Gestion de la carte
        if (viewName === 'map') {
            // Si la carte n'est pas encore lancée, on la lance (cas rare maintenant)
            if (!mapInitialized) {
                initMap();
                mapInitialized = true;
            } else {
                // Si elle tourne déjà, on la redessine correctement
                setTimeout(function () {
                    if (map) {
                        map.invalidateSize();

                        // NOUVEAU : On recentre sur l'utilisateur si on a ses coordonnées
                        // (Car l'initialisation cachée peut avoir décalé le centre)
                        if (userCoords) {
                            map.setView([userCoords.lat, userCoords.lng], 10);
                        }
                    }
                }, 100);
            }
        }
    };


    window.sortStoreList = function () { var container = document.getElementById('view-list'); if (!container) return; var cards = Array.from(container.getElementsByClassName('sl-card')); cards.sort(function (a, b) { var distA = parseFloat(a.getAttribute('data-distance')) || 99999; var distB = parseFloat(b.getAttribute('data-distance')) || 99999; return distA - distB; }); cards.forEach(function (card) { container.appendChild(card); }); };
}