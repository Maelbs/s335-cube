/**
 * Gère l'affichage des options de livraison (Domicile vs Magasin)
 * Appelé via onchange sur les boutons radio dans le HTML
 */
function toggleDeliveryMode() {
    const modeMagasin = document.getElementById("mode_magasin");
    const isMagasin = modeMagasin ? modeMagasin.checked : false;
    const divDomicile = document.getElementById("domicile-options");

    if (divDomicile) {
        // Affiche ou cache la section domicile
        divDomicile.style.display = isMagasin ? "none" : "block";

        if (!isMagasin) {
            // Si on repasse en mode domicile, on vérifie si une adresse est cochée
            const checkedAddr = document.querySelector('input[name="id_adresse"]:checked');
            
            if (!checkedAddr) {
                // Si aucune adresse n'est cochée, on coche "Nouvelle adresse" par défaut
                const newAddrRadio = document.getElementById("adr_new");
                if (newAddrRadio) {
                    newAddrRadio.checked = true;
                    toggleNewAddressForm(true);
                }
            } else {
                // Sinon on affiche/cache le formulaire selon l'adresse cochée
                toggleNewAddressForm(checkedAddr.value === "new");
            }
        }
    }
}

/**
 * Active ou désactive le formulaire de nouvelle adresse
 * @param {boolean} show - True pour afficher, False pour cacher
 */
function toggleNewAddressForm(show) {
    const form = document.getElementById("new-address-form");
    if (!form) return;

    // Sélectionne les inputs, selects et textareas à l'intérieur du formulaire
    const inputs = form.querySelectorAll("input, select, textarea");

    if (show) {
        form.style.display = "block";
        // On réactive les champs pour qu'ils soient envoyés
        inputs.forEach((input) => (input.disabled = false));
    } else {
        form.style.display = "none";
        // On désactive les champs pour qu'ils ne bloquent pas la validation HTML5
        inputs.forEach((input) => (input.disabled = true));
    }
}

/**
 * Logique exécutée une fois le DOM chargé
 */
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("payment-form");
    const radios = document.querySelectorAll('input[name="methode"]');

    // 1. GESTION DU CHANGEMENT D'ACTION DU FORMULAIRE (Stripe / Paypal)
    radios.forEach((radio) => {
        radio.addEventListener("change", () => {
            if (radio.value === "stripe") {
                // On utilise l'URL en dur car on est dans un fichier .js (Blade ne marche pas ici)
                form.action = "/stripe/payment";
            } else if (radio.value === "paypal") {
                form.action = "/paypal/payment";
            }
        });
    });

    // Initialisation de l'action correcte au chargement de la page
    const checkedRadio = document.querySelector('input[name="methode"]:checked');
    if (checkedRadio) {
        if (checkedRadio.value === "stripe") form.action = "/stripe/payment";
        else if (checkedRadio.value === "paypal") form.action = "/paypal/payment";
    }

    // 2. INITIALISATION DE L'ETAT VISUEL
    toggleDeliveryMode();

    const newAddrRadio = document.getElementById("adr_new");
    if (newAddrRadio && newAddrRadio.checked) {
        toggleNewAddressForm(true);
    } else {
        // Si "Nouvelle adresse" n'est pas coché, on s'assure que le formulaire est caché/désactivé
        const formNewAddr = document.getElementById("new-address-form");
        // On force à false si ce n'est pas coché pour bien désactiver les inputs
        if(newAddrRadio && !newAddrRadio.checked) {
             toggleNewAddressForm(false);
        }
    }

    // 3. AUTOCOMPLETION ADRESSE (API GOUV)
    const rueInput = document.getElementById("rue");
    const suggestionsList = document.getElementById("adresse-suggestions");

    if (rueInput && suggestionsList) {
        rueInput.addEventListener("input", function () {
            const query = this.value;
            
            if (query.length > 3) {
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&limit=5`)
                    .then((response) => response.json())
                    .then((data) => {
                        suggestionsList.innerHTML = "";
                        suggestionsList.style.display = "block";

                        if (data.features && data.features.length > 0) {
                            data.features.forEach((feature) => {
                                const li = document.createElement("li");
                                li.className = "list-group-item list-group-item-action";
                                li.style.cursor = "pointer";
                                li.textContent = feature.properties.label;

                                li.addEventListener("click", function () {
                                    // Remplissage des champs
                                    document.getElementById("rue").value = feature.properties.name;
                                    document.getElementById("zipcode").value = feature.properties.postcode;
                                    document.getElementById("city").value = feature.properties.city;
                                    
                                    // Gestion du pays (par défaut France pour l'API Gouv)
                                    const countryInput = document.getElementById("country");
                                    if(countryInput) countryInput.value = "France";

                                    suggestionsList.style.display = "none";
                                });
                                suggestionsList.appendChild(li);
                            });
                        } else {
                            suggestionsList.style.display = "none";
                        }
                    })
                    .catch(error => console.error("Erreur API Adresse:", error));
            } else {
                suggestionsList.style.display = "none";
            }
        });

        // Fermer la liste si on clique ailleurs
        document.addEventListener("click", function (e) {
            if (e.target !== rueInput && e.target !== suggestionsList) {
                suggestionsList.style.display = "none";
            }
        });
    }
});

// Fonction utilitaire pour le bouton "Choisir un magasin" (si besoin)
function toggleStoreLocation() {
    // Redirection vers la page ou ouverture de la modal des magasins
    // Remplacez par votre logique ou URL
    window.location.href = "/magasins"; 
}