
function toggleDeliveryMode() {
    const modeMagasin = document.getElementById("mode_magasin");
    const isMagasin = modeMagasin ? modeMagasin.checked : false;
    const divDomicile = document.getElementById("domicile-options");

    if (divDomicile) {
      
        divDomicile.style.display = isMagasin ? "none" : "block";

        if (!isMagasin) {
    
            const checkedAddr = document.querySelector('input[name="id_adresse"]:checked');
            
            if (!checkedAddr) {
       
                const newAddrRadio = document.getElementById("adr_new");
                if (newAddrRadio) {
                    newAddrRadio.checked = true;
                    toggleNewAddressForm(true);
                }
            } else {
              
                toggleNewAddressForm(checkedAddr.value === "new");
            }
        }
    }
}

/**
 * 
 * @param {boolean} show - True pour afficher, False pour cacher
 */


function toggleNewAddressForm(show) {
    const form = document.getElementById("new-address-form");
    if (!form) return;


    const inputs = form.querySelectorAll("input, select, textarea");

    if (show) {
        form.style.display = "block";
      
        inputs.forEach((input) => (input.disabled = false));
    } else {
        form.style.display = "none";

        inputs.forEach((input) => (input.disabled = true));
    }
}


document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("payment-form");
    const radios = document.querySelectorAll('input[name="methode"]');

  
    radios.forEach((radio) => {
        radio.addEventListener("change", () => {
            if (radio.value === "stripe") {
              
                form.action = "/stripe/payment";
            } else if (radio.value === "paypal") {
                form.action = "/paypal/payment";
            }
        });
    });

  
    const checkedRadio = document.querySelector('input[name="methode"]:checked');
    if (checkedRadio) {
        if (checkedRadio.value === "stripe") form.action = "/stripe/payment";
        else if (checkedRadio.value === "paypal") form.action = "/paypal/payment";
    }

 
    toggleDeliveryMode();

    const newAddrRadio = document.getElementById("adr_new");
    if (newAddrRadio && newAddrRadio.checked) {
        toggleNewAddressForm(true);
    } else {
 
        const formNewAddr = document.getElementById("new-address-form");

        if(newAddrRadio && !newAddrRadio.checked) {
             toggleNewAddressForm(false);
        }
    }


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
                               
                                    document.getElementById("rue").value = feature.properties.name;
                                    document.getElementById("zipcode").value = feature.properties.postcode;
                                    document.getElementById("city").value = feature.properties.city;
                                    
                          
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

   
        document.addEventListener("click", function (e) {
            if (e.target !== rueInput && e.target !== suggestionsList) {
                suggestionsList.style.display = "none";
            }
        });
    }
});


function toggleStoreLocation() {
 
    window.location.href = "/magasins"; 
}