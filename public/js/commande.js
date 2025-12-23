function toggleDeliveryMode() {
  const isMagasin = document.getElementById("mode_magasin")
    ? document.getElementById("mode_magasin").checked
    : false;
  const divDomicile = document.getElementById("domicile-options");

  if (divDomicile) {
    divDomicile.style.display = isMagasin ? "none" : "block";

    if (!isMagasin) {
      const checkedAddr = document.querySelector(
        'input[name="id_adresse"]:checked'
      );
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

function toggleNewAddressForm(show) {
  const form = document.getElementById("new-address-form");
  if (!form) return;

  const inputs = form.querySelectorAll("input");

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
        form.action = "{{ route('stripe.payment') }}";
      } else if (radio.value === "paypal") {
        form.action = "{{ route('paypal.payment') }}";
      }
    });
  });

  toggleDeliveryMode();

  const newAddrRadio = document.getElementById("adr_new");
  if (newAddrRadio && newAddrRadio.checked) {
    toggleNewAddressForm(true);
  } else {
    const formNewAddr = document.getElementById("new-address-form");
    if (formNewAddr && formNewAddr.style.display === "none") {
      toggleNewAddressForm(false);
    }
  }

  const rueInput = document.getElementById("rue");
  const suggestionsList = document.getElementById("adresse-suggestions");

  if (rueInput) {
    rueInput.addEventListener("input", function () {
      const query = this.value;
      if (query.length > 3) {
        fetch(`https://api-adresse.data.gouv.fr/search/?q=${query}&limit=5`)
          .then((response) => response.json())
          .then((data) => {
            suggestionsList.innerHTML = "";
            suggestionsList.style.display = "block";

            data.features.forEach((feature) => {
              const li = document.createElement("li");
              li.className = "list-group-item list-group-item-action";
              li.style.cursor = "pointer";
              li.textContent = feature.properties.label;

              li.addEventListener("click", function () {
                document.getElementById("rue").value = feature.properties.name;
                document.getElementById("zipcode").value =
                  feature.properties.postcode;
                document.getElementById("city").value = feature.properties.city;
                document.getElementById("country").value = "France";
                suggestionsList.style.display = "none";
              });
              suggestionsList.appendChild(li);
            });
          });
      } else {
        suggestionsList.style.display = "none";
      }
    });

    document.addEventListener("click", function (e) {
      if (e.target !== rueInput && e.target !== suggestionsList) {
        if (suggestionsList) suggestionsList.style.display = "none";
      }
    });
  }
});
