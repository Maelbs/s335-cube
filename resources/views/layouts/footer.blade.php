<style>
    footer {
        background: #111;
        color: #fff;
        padding: 40px;
        text-align: center;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
    }

    a {
        color: white;
    }
</style>

<footer class="bg-gray-900 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="flex space-x-6 text-sm">

                <a href="{{ route('privacy.policy') }}"
                    class="hover:text-blue-400 transition underline decoration-blue-500"
                    style="text-decoration: underline;">
                    Politique de Protection de données et Cookies
                </a>
            </div>

            <div class="mt-4 md:mt-0 text-gray-500 text-xs">
                &copy; {{ date('Y') }} CUBE BIKES FRANCE Tous droits réservés.
            </div>
        </div>
    </div>

    <script>
        // 1. FORCAGE DU RECHARGEMENT SI RETOUR NAVIGATEUR (BFCache)
        window.onpageshow = function (event) {
            if (event.persisted) {
                console.log("Page chargée depuis le cache navigateur (BFCache) -> Rechargement forcé.");
                window.location.reload();
            }
        };

        document.addEventListener("DOMContentLoaded", function () {
            refreshCartGlobal();
        });

        function refreshCartGlobal() {
            // Timestamp pour être sûr que le serveur ne renvoie pas une vieille réponse
            const url = "{{ route('cart.count') }}" + "?t=" + new Date().getTime();

            fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'include'
            })
                .then(response => response.json())
                .then(data => {
                    // --- A. MISE À JOUR HEADER (Bulle rouge & Prix) ---
                    const counters = document.querySelectorAll('.cart-count-display');
                    counters.forEach(el => {
                        el.textContent = data.count;
                        el.style.display = (parseInt(data.count) > 0) ? 'flex' : 'none';
                    });

                    const headerTotal = document.querySelector('.cart-total-row .price');
                    if (headerTotal) headerTotal.textContent = data.total;

                    // --- B. MISE À JOUR PREVIEW MINI-PANIER ---
                    const cartList = document.querySelector('.cart-items-list');
                    if (cartList && data.items) {
                        if (data.count === 0) {
                            cartList.innerHTML = `<div style="padding:30px;text-align:center;color:#999;"><i>Votre panier est vide.</i></div>`;
                        } else {
                            let html = '';
                            data.items.forEach(item => {
                                let tailleHtml = (item.taille && item.taille !== 'Non renseigné') ? ` <b>(${item.taille})</b>` : '';
                                let qtyHtml = (item.quantity > 1) ? ` <small>x${item.quantity}</small>` : '';
                                html += `
                        <div class="cart-item">
                            <div class="cart-item-img"><img src="${item.image}" style="width:100%;" onerror="this.src='https://placehold.co/80'"></div>
                            <div class="cart-item-details">
                                <span class="item-name">${item.name}${tailleHtml}${qtyHtml}</span>
                                <span class="item-price">${item.price}</span>
                            </div>
                        </div>`;
                            });
                            cartList.innerHTML = html;
                        }
                    }

                    // --- C. DÉTECTION ET CORRECTION DE LA PAGE PANIER PRINCIPALE ---
                    // On vérifie si on est sur la page panier
                    if (document.querySelector('.page-wrapper .cart-items-section')) {

                        // 1. Comparaison par NOMBRE d'articles (Le plus fiable)
                        // Dans ton panier.blade.php, tu as : <h2 ...>PANIER (<span id="cart-count">...</span>)</h2>
                        const pageCountEl = document.getElementById('cart-count');

                        if (pageCountEl) {
                            const currentCount = parseInt(pageCountEl.textContent.trim());
                            const serverCount = parseInt(data.count);

                            if (currentCount !== serverCount) {
                                console.log(`Mismatch Compteur : Page(${currentCount}) vs Serveur(${serverCount}). Reloading...`);
                                window.location.reload();
                                return; // Stop ici
                            }
                        }

                        // 2. Comparaison par PRIX TOTAL (Sécurité supplémentaire)
                        const pageTotalEl = document.getElementById('cartTotal');
                        if (pageTotalEl) {
                            // Fonction pour nettoyer le prix (garder que les chiffres)
                            const clean = (str) => str.replace(/[^0-9]/g, '');

                            const htmlPrice = clean(pageTotalEl.textContent);
                            const serverPrice = clean(data.total);

                            if (htmlPrice !== serverPrice) {
                                console.log("Mismatch Prix. Reloading...");
                                // Optionnel : On peut essayer de mettre à jour le texte directement sans reload
                                // pageTotalEl.textContent = data.total; 
                                // Mais le reload est plus sûr pour recalculer les frais de port/promo
                                window.location.reload();
                            }
                        }
                    }
                })
                .catch(console.error);
        }
    </script>
</footer>