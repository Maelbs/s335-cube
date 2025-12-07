document.addEventListener("DOMContentLoaded", function () {
  // 1. GESTION DE L'AFFICHAGE CONDITIONNEL
  const checkbox = document.getElementById("use_same_address");
  const billingWrapper = document.getElementById("billing-address-wrapper");
  // Sélectionner tous les inputs DANS le wrapper facturation
  const billingInputs = billingWrapper.querySelectorAll("input");

  function toggleBilling() {
    if (checkbox.checked) {
      // Si coché (Même adresse) -> On cache la facturation
      billingWrapper.classList.remove("visible");
      // On retire l'attribut required pour ne pas bloquer le formulaire
      billingInputs.forEach((input) => (input.required = false));
    } else {
      // Si décoché (Adresses différentes) -> On affiche
      billingWrapper.classList.add("visible");
      // On remet l'attribut required
      billingInputs.forEach((input) => (input.required = true));
    }
  }

  checkbox.addEventListener("change", toggleBilling);
  // Appel initial pour régler l'état au chargement de la page
  toggleBilling();

  // 2. AUTOCOMPLETION API GOUV (Fonction réutilisable)
  function setupAddressAutocomplete(rueId, zipId, cityId, countryId) {
    const rueInput = document.getElementById(rueId);
    const zipcodeInput = document.getElementById(zipId);
    const cityInput = document.getElementById(cityId);
    const countryInput = document.getElementById(countryId);

    if (!rueInput) return;

    const suggestionBox = document.createElement("div");
    suggestionBox.classList.add("suggestion-adresse");
    rueInput.parentNode.style.position = "relative";
    rueInput.parentNode.appendChild(suggestionBox);

    let timeout = null;

    rueInput.addEventListener("input", function () {
      const query = rueInput.value;
      clearTimeout(timeout);

      if (query.length < 3) {
        suggestionBox.style.display = "none";
        return;
      }

      timeout = setTimeout(async () => {
        try {
          const response = await fetch(
            `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(
              query
            )}&limit=5&autocomplete=1`
          );
          const data = await response.json();

          suggestionBox.innerHTML = "";

          if (data.features && data.features.length > 0) {
            data.features.forEach((item) => {
              const props = item.properties;
              const label = props.label;

              const div = document.createElement("div");
              div.textContent = label;
              div.style.padding = "10px";
              div.style.cursor = "pointer";
              div.style.borderBottom = "1px solid #eee";
              div.style.backgroundColor = "white"; // Fond blanc pour les suggestions

              div.addEventListener(
                "mouseenter",
                () => (div.style.backgroundColor = "#f0f0f0")
              );
              div.addEventListener(
                "mouseleave",
                () => (div.style.backgroundColor = "white")
              );

              div.addEventListener("click", function () {
                rueInput.value = props.name;
                zipcodeInput.value = props.postcode;
                cityInput.value = props.city;
                countryInput.value = "France";
                suggestionBox.style.display = "none";
              });

              suggestionBox.appendChild(div);
            });
            suggestionBox.style.display = "block";
          } else {
            suggestionBox.style.display = "none";
          }
        } catch (error) {
          console.error("Erreur API:", error);
        }
      }, 300);
    });

    document.addEventListener("click", function (e) {
      if (e.target !== rueInput) {
        suggestionBox.style.display = "none";
      }
    });
  }

  setupAddressAutocomplete("rue", "zipcode", "city", "country");
  setupAddressAutocomplete(
    "billing_rue",
    "billing_zipcode",
    "billing_city",
    "billing_country"
  );
});
