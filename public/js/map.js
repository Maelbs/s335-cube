/* public/js/map.js */

if (typeof window.mapScriptLoaded === "undefined") {
  window.mapScriptLoaded = true;

  // --- VARIABLES GLOBALES ---
  var mapInitialized = false;
  var map = null;
  var markersLayer = null;
  var userCoords = null; // Stocke {lat, lng} de l'utilisateur
  var storeLocatorTimeout = null;

  // Stocke l'ID de la taille sélectionnée
  window.currentTailleId = null;

  // --- 1. DÉFINITION DES ICÔNES ---
  var greenIcon = new L.Icon({
    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
  });

  var redIcon = new L.Icon({
    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png",
    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
  });

  var blueIcon = new L.Icon({
    iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png",
    shadowUrl: "https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png",
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41],
  });

  // --- FONCTIONS UTILITAIRES ---
  window.getDistanceFromLatLonInKm = function (lat1, lon1, lat2, lon2) {
    var R = 6371; // Rayon de la terre en km
    var dLat = deg2rad(lat2 - lat1);
    var dLon = deg2rad(lon2 - lon1);
    var a =
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(deg2rad(lat1)) *
        Math.cos(deg2rad(lat2)) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
  };

  window.deg2rad = function (deg) {
    return deg * (Math.PI / 180);
  };

  /**
   * Vérifie la disponibilité d'un magasin selon la taille sélectionnée
   */
  function checkAvailability(cardElement) {
    var rawJson = cardElement.getAttribute("data-stock-details");
    var stockGlobal = cardElement.getAttribute("data-stock-global") === "1";
    var stockDetails = {};
    try {
      if (rawJson) stockDetails = JSON.parse(rawJson);
    } catch (e) {
      console.error(e);
    }

    if (window.currentTailleId) {
      var qty = stockDetails[String(window.currentTailleId)] || 0;
      return qty > 0;
    } else {
      return stockGlobal;
    }
  }

  // --- GESTION DE L'AFFICHAGE LISTE ---
  window.refreshStoreDisplay = function () {
    var onlyStock = document.getElementById("stockToggle")
      ? document.getElementById("stockToggle").checked
      : false;
    var searchInput = document.getElementById("storeSearchInput");
    var searchText = searchInput ? searchInput.value.toLowerCase().trim() : "";

    var cards = document.querySelectorAll(".sl-card");

    cards.forEach(function (card) {
      var hasStock = checkAvailability(card);
      var searchString =
        card.getAttribute("data-search-string") ||
        card.getAttribute("data-searchString") ||
        "";
      var matchesSearch = searchString.indexOf(searchText) !== -1;

      var showCard = true;
      if (onlyStock && !hasStock) showCard = false;
      if (!matchesSearch) showCard = false;

      if (showCard) {
        card.style.display = "block";
        card.classList.remove("hidden-item");
      } else {
        card.style.display = "none";
        card.classList.add("hidden-item");
      }
    });

    // On recharge les marqueurs (et donc les distances)
    window.loadStoresOnMap();
  };

  // --- INITIALISATION CARTE ---
  window.initMap = function () {
    if (typeof L === "undefined" || map) return;

    map = L.map("sl-map").setView([46.603354, 1.888334], 6);
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: "&copy; OpenStreetMap",
    }).addTo(map);

    markersLayer = L.layerGroup().addTo(map);

    // Fonction interne pour définir la position et lancer le calcul
    function setUserLocation(lat, lng, label) {
      console.log("📍 Position utilisateur fixée :", lat, lng);
      userCoords = { lat: lat, lng: lng };

      L.marker([lat, lng], { icon: redIcon })
        .addTo(map)
        .bindPopup("<b>" + label + "</b>")
        .openPopup();

      map.setView([lat, lng], 10);

      // IMPORTANT : Une fois la position connue, on recalcule les distances
      window.loadStoresOnMap();
    }

    // Fonction pour demander la géolocalisation navigateur (GPS)
    function useBrowserGeolocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
          function (position) {
            setUserLocation(
              position.coords.latitude,
              position.coords.longitude,
              "Ma position (GPS)"
            );
          },
          function (error) {
            console.warn("⚠️ Géolocalisation refusée ou erreur.", error);
            // On affiche quand même la liste sans distance
            window.refreshStoreDisplay();
          }
        );
      } else {
        console.warn("⚠️ Navigateur non compatible GPS.");
        window.refreshStoreDisplay();
      }
    }

    // Fonction principale : Essai Adresse DB, sinon GPS
    function useDatabaseAddress() {
      if (window.userAddress && window.userAddress.trim() !== "") {
        console.log("📍 Adresse client trouvée en DB :", window.userAddress);

        var url =
          "https://api-adresse.data.gouv.fr/search/?q=" +
          encodeURIComponent(window.userAddress) +
          "&limit=1";
        if (window.userAddress.includes("74")) url += "&deptCODE=74";

        fetch(url)
          .then((r) => r.json())
          .then((data) => {
            if (data.features && data.features.length > 0) {
              var c = data.features[0].geometry.coordinates;
              // c[1] = lat, c[0] = lng
              setUserLocation(c[1], c[0], "Votre adresse");
            } else {
              console.warn("⚠️ Adresse DB non trouvée par l'API. Essai GPS...");
              useBrowserGeolocation();
            }
          })
          .catch((e) => {
            console.error("Erreur API Adresse", e);
            useBrowserGeolocation();
          });
      } else {
        console.log("ℹ️ Aucune adresse client (Invité). Essai GPS...");
        useBrowserGeolocation();
      }
    }

    // LANCEMENT
    useDatabaseAddress();
  };

  // --- CHARGEMENT DES MARQUEURS ET CALCUL DES DISTANCES ---
  window.loadStoresOnMap = function () {
    if (!window.magasinsData) return;
    if (markersLayer) markersLayer.clearLayers();

    var onlyStock = document.getElementById("stockToggle")
      ? document.getElementById("stockToggle").checked
      : false;
    var searchInput = document.getElementById("storeSearchInput");
    var searchText = searchInput ? searchInput.value.toLowerCase().trim() : "";

    window.magasinsData.forEach(function (mag) {
      // 1. Récup carte DOM pour vérifier dispo
      var domCard = document.querySelector(
        '.sl-card[data-id="' + mag.id + '"]'
      );
      
      // Note: mag.stock est un boolean PHP, checkAvailability recalcule selon la taille JS
      var isAvailable = domCard ? checkAvailability(domCard) : mag.stock;

      // 2. Filtres (Stock et Recherche)
      if (onlyStock && !isAvailable) return;
      
      var magSearchString = (
        mag.nom + " " + mag.ville + " " + mag.adresse
      ).toLowerCase();
      
      if (searchText !== "" && magSearchString.indexOf(searchText) === -1)
        return;

      // 3. Calcul coordonnées magasin (API Gouv)
      if (mag.adresse) {
        var query = encodeURIComponent(mag.adresse);
        // Timeout pour ne pas spammer l'API si beaucoup de magasins
        setTimeout(function () {
          fetch(
            "https://api-adresse.data.gouv.fr/search/?q=" + query + "&limit=1"
          )
            .then((r) => r.json())
            .then((data) => {
              if (data.features && data.features.length > 0) {
                var coords = data.features[0].geometry.coordinates;
                var lat = coords[1];
                var lng = coords[0];

                if (map && markersLayer) {
                  // --- GESTION MARQUEUR CARTE ---
                  var iconToUse = blueIcon;
                  if (mag.selected) {
                    iconToUse = greenIcon;
                  }

                  var marker = L.marker([lat, lng], { icon: iconToUse });

                  // --- MODIFICATION ICI ---
                  var stockHtml = "";
                  
                  // On affiche le statut SEULEMENT si on est sur une page produit (checkStock est true)
                  if (window.checkStock) {
                      stockHtml = isAvailable
                        ? '<div style="color:green;">🟢 Disponible</div>'
                        : '<div style="color:red;">🔴 Indisponible</div>';
                  }
                  // ------------------------
                    
                  var btnHtml = mag.selected
                    ? '<button class="btn-skew-black" style="background:#28a745; width:100%; cursor:default;">DÉJÀ SÉLECTIONNÉ</button>'
                    : '<form action="' + window.routeDefinirMagasin + '" method="POST"><input type="hidden" name="_token" value="' + window.csrfToken + '"><input type="hidden" name="id_magasin" value="' + mag.id + '"><button type="submit" class="btn-skew-black" style="font-size:10px; padding:5px; width:100%;">CHOISIR</button></form>';

                  marker.bindPopup(
                    '<div style="text-align:center;"><b>' + mag.nom + "</b><br>" + mag.ville + "<br>" + stockHtml + "<br>" + btnHtml + "</div>"
                  );
                  markersLayer.addLayer(marker);
                }

                // --- CALCUL ET AFFICHAGE DISTANCE DANS LA LISTE ---
                if (window.userCoords && domCard) {
                  // 1. Calcul mathématique
                  var dist = window.getDistanceFromLatLonInKm(
                    window.userCoords.lat,
                    window.userCoords.lng,
                    lat,
                    lng
                  );

                  // 2. Mise à jour attribut data (pour le tri)
                  domCard.setAttribute("data-distance", dist);

                  // 3. Mise à jour visuelle du <p class="sl-distance">
                  var distanceP = domCard.querySelector(".sl-distance");
                  if (distanceP) {
                    distanceP.innerText = dist.toFixed(1) + " km";
                    distanceP.style.display = "block";
                  }
                }
              }
            })
            .catch((err) => console.log("Erreur fetch adresse magasin", err));
        }, Math.random() * 500); // Petit délai aléatoire
      }
    });

    // On lance le tri après un délai pour laisser le temps aux fetchs de finir
    setTimeout(window.sortStoreList, 2500);
  };

  // --- ÉCOUTEURS DOM ET TOGGLE ---
  document.addEventListener("DOMContentLoaded", function () {
    var overlay = document.getElementById("store-locator-overlay");
    if (overlay)
      overlay.addEventListener("click", function (e) {
        if (e.target.id === "store-locator-overlay")
          window.toggleStoreLocator();
      });

    var stockToggle = document.getElementById("stockToggle");
    if (stockToggle)
      stockToggle.addEventListener("change", window.refreshStoreDisplay);

    var searchInput = document.getElementById("storeSearchInput");
    if (searchInput)
      searchInput.addEventListener("input", window.refreshStoreDisplay);
  });

  window.toggleStoreLocator = function () {
    var overlay = document.getElementById("store-locator-overlay");
    var header = document.querySelector("header");
    var body = document.body;
    if (!overlay) return;
    
    if (storeLocatorTimeout) {
      clearTimeout(storeLocatorTimeout);
      storeLocatorTimeout = null;
    }

    if (overlay.classList.contains("visible")) {
      overlay.classList.remove("visible");
      if (header) header.classList.remove("header-hidden");
      body.style.overflow = "";
      storeLocatorTimeout = setTimeout(function () {
        overlay.style.visibility = "hidden";
      }, 300);
    } else {
      overlay.style.visibility = "visible";
      body.style.overflow = "hidden";
      if (header) header.classList.add("header-hidden");
      requestAnimationFrame(function () {
        overlay.classList.add("visible");
      });
      
      // Si on ouvre directement, on peut initier la carte/GPS si pas encore fait
      if (!mapInitialized) {
         window.switchView("list"); // Par défaut on reste sur liste, mais on init la map en background
         window.initMap();
         mapInitialized = true;
      }
    }
  };

  window.switchView = function (viewName) {
    var tabs = document.querySelectorAll(".sl-tab");
    var list = document.getElementById("view-list");
    var mapDiv = document.getElementById("view-map");
    
    if (list && mapDiv) {
      list.style.display = viewName === "list" ? "block" : "none";
      mapDiv.style.display = viewName === "map" ? "block" : "none";
    }
    
    if (tabs.length > 0) {
      tabs[0].classList.toggle("active", viewName === "list");
      tabs[1].classList.toggle("active", viewName === "map");
    }
    
    // Initialisation au premier clic sur "Vue Carte" si pas fait avant
    if (viewName === "map") {
      if (!mapInitialized) {
        window.initMap();
        mapInitialized = true;
      } else {
        setTimeout(function () {
          if (map) {
            map.invalidateSize();
            if (userCoords) map.setView([userCoords.lat, userCoords.lng], 10);
          }
        }, 100);
      }
    }
  };

  window.sortStoreList = function () {
    var container = document.getElementById("view-list");
    if (!container) return;
    var cards = Array.from(container.getElementsByClassName("sl-card"));
    
    cards.sort(function (a, b) {
      var distA = parseFloat(a.getAttribute("data-distance")) || 99999;
      var distB = parseFloat(b.getAttribute("data-distance")) || 99999;
      return distA - distB;
    });
    
    cards.forEach(function (card) {
      container.appendChild(card);
    });
  };
}

// Fonction appelée par le clic bouton taille (Page produit)
window.updateStoreLocatorStocks = function (idInventaire) {
  window.currentTailleId = idInventaire;
  
  const cards = document.querySelectorAll(".sl-card");
  cards.forEach((card) => {
    const displayDiv = card.querySelector(".js-stock-display");
    if (!displayDiv) return;
    
    var isAvailable = checkAvailability(card);
    var message = isAvailable
      ? idInventaire
        ? "Disponible (Taille sélectionnée)"
        : "Disponible"
      : idInventaire
      ? "Indisponible (Taille sélectionnée)"
      : "Indisponible";

    // HTML simplifié
    displayDiv.innerHTML = isAvailable
      ? `<div class="sl-stock-status status-dispo"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#00AEEF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg> ${message}</div>`
      : `<div class="sl-stock-status status-indispo" style="color: #999;"><span style="font-size:12px;">✖</span> ${message}</div>`;
  });
  
  window.refreshStoreDisplay();
};