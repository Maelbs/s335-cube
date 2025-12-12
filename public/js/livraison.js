document.addEventListener("DOMContentLoaded", function () {
    
  // Fonction d'autocomplétion API Gouv
  function setupAddressAutocomplete(rueId, zipId, cityId, countryId) {
    const rueInput = document.getElementById(rueId);
    const zipcodeInput = document.getElementById(zipId);
    const cityInput = document.getElementById(cityId);
    const countryInput = document.getElementById(countryId);

    if (!rueInput) return; // Si le champ n'existe pas sur la page, on arrête

    // Création de la liste de suggestions
    const suggestionBox = document.createElement("div");
    suggestionBox.classList.add("suggestion-adresse");
    
    // Style inline pour que la liste s'affiche correctement sous le champ
    suggestionBox.style.position = "absolute";
    suggestionBox.style.zIndex = "1000";
    suggestionBox.style.width = "100%";
    suggestionBox.style.backgroundColor = "#fff";
    suggestionBox.style.border = "1px solid #ddd";
    suggestionBox.style.borderRadius = "0 0 4px 4px";
    suggestionBox.style.boxShadow = "0 4px 6px rgba(0,0,0,0.1)";
    
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
            `https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5&autocomplete=1`
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
              div.style.fontSize = "14px";
              div.style.color = "#333";

              div.addEventListener("mouseenter", () => (div.style.backgroundColor = "#f0f0f0"));
              div.addEventListener("mouseleave", () => (div.style.backgroundColor = "white"));

              div.addEventListener("click", function () {
                rueInput.value = props.name;       // Rue
                zipcodeInput.value = props.postcode; // Code Postal
                cityInput.value = props.city;      // Ville
                if(countryInput) countryInput.value = "France"; // Pays
                
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

    // Fermeture au clic extérieur
    document.addEventListener("click", function (e) {
      if (e.target !== rueInput) {
        suggestionBox.style.display = "none";
      }
    });
  }

  setupAddressAutocomplete("rueId", "zipId", "cityId", "countryId");
});