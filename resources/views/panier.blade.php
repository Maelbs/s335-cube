<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    {{-- Token CSRF pour que la sauvegarde AJAX fonctionne --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    @include('layouts.header')

    <div class="page-wrapper">
        
        @if(empty($cart))
            <div class="empty-cart">
                <p>Votre panier est vide.</p>
                <a href="/">Retour à la boutique</a>
            </div>
        @else
            <div class="cart-grid">
                
                <div class="cart-items-section">
                    <h2 class="section-title">PANIER (<span id="cart-count">{{ count($cart) }}</span>)</h2>

                    @if(session('success'))
                        <div class="alert-success">{{ session('success') }}</div>
                    @endif

                    @foreach($cart as $key => $item)
                    {{-- 
                        LIGNE PRODUIT : 
                        data-row-id : Identifiant pour sauvegarder en session
                        data-stock : Le stock MAX calculé par le controller (Vélo + Taille spécifique)
                    --}}
                    <div class="cart-card cart-item-row" 
                         data-row-id="{{ $key }}"
                         data-price="{{ $item['price'] }}" 
                         data-stock="{{ $item['max_stock'] }}"> 
                        
                        <div class="card-img">
                            @if(!empty($item['image']))
                                <img src="{{ filter_var($item['image'], FILTER_VALIDATE_URL) ? $item['image'] : asset('storage/'.$item['image']) }}" alt="{{ $item['name'] }}">
                            @endif
                        </div>

                        <div class="card-info">
                            <h3 class="product-name">{{ $item['name'] }}</h3>
                            <div class="product-price-unit">{{ number_format($item['price'], 2, ',', ' ') }} €</div>
                            <div class="product-meta">RÉFÉRENCE : {{ $item['reference'] }}</div>
                            <div class="product-meta">Taille : {{ $item['taille'] }}</div>
                        </div>

                        <div class="card-actions">
                            <div class="row-total-price">
                                <span class="item-total-display">{{ number_format($item['price'] * $item['quantity'], 2, ',', ' ') }}</span> € TTC
                            </div>

                            <div class="qty-container">
                                <div class="qty-selector">
                                    <button type="button" class="qty-btn btn-minus">-</button>
                                    <input type="text" value="{{ $item['quantity'] }}" class="qty-input" readonly>
                                    <button type="button" class="qty-btn btn-plus">+</button>
                                </div>
                                {{-- Message d'erreur (Caché par défaut) --}}
                                <div class="stock-error-msg">
                                    La quantité sélectionnée pour ce produit est supérieure au stock actuellement disponible
                                </div>
                            </div>

                            <form action="{{ route('cart.remove', $key) }}" method="POST" class="form-delete">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Supprimer</button>
                            </form>
                        </div>
                    </div>
                    @endforeach

                    <div class="cart-footer-actions">
                        <a href="/" class="back-link">
                           <span>&larr;</span> CONTINUER MES ACHATS
                        </a>
                        
                        <a href="#" class="btn-cube-red btn-validate">
                            <span>► Valider mon panier</span>
                        </a>
                    </div>
                </div>

                <div class="cart-summary-section">
                    <h2 class="section-title">RÉCAPITULATIF</h2>
                    
                    <div class="summary-card">
                        <div class="summary-row">
                            <span>Panier (<span id="summary-count-txt">{{ count($cart) }}</span>)</span>
                            <span><span id="summary-subtotal">{{ number_format($total, 2, ',', ' ') }}</span> €</span>
                        </div>
                        <div class="summary-row">
                            <span>Livraison</span>
                            <span>gratuit</span>
                        </div>
                        
                        <div class="summary-divider"></div>

                        <div class="summary-row total-row">
                            <span>Total TTC</span>
                            <span><span id="summary-total">{{ number_format($total, 2, ',', ' ') }}</span> €</span>
                        </div>
                        <div class="taxes-info">Taxes incluses : <span id="summary-taxes">{{ number_format($total * 0.2, 2, ',', ' ') }}</span> €</div>

                        <div class="summary-btn-container">
                            <a href="#" class="btn-cube-red btn-validate">
                                <span>► Valider mon panier</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>

    {{-- SCRIPT JAVASCRIPT GESTION STOCK --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. Formatage des prix
        const formatMoney = (amount) => {
            return new Intl.NumberFormat('fr-FR', {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(amount);
        };

        // 2. Sauvegarde AJAX (Pour éviter la réinitialisation lors d'une suppression)
        const saveQuantityToServer = (rowId, newQty) => {
            fetch('{{ route("cart.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    id: rowId,
                    quantity: newQty
                })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    // Met à jour le total global affiché à droite
                    const totalElement = document.getElementById('summary-subtotal');
                    const totalTTCElement = document.getElementById('summary-total');
                    
                    if(totalElement) totalElement.textContent = data.newTotal;
                    if(totalTTCElement) totalTTCElement.textContent = data.newTotal;
                    
                    // Mise à jour des taxes (20%)
                    const numTotal = parseFloat(data.newTotal.replace(/\s/g, '').replace(',', '.'));
                    const taxes = numTotal * 0.2;
                    if(document.getElementById('summary-taxes')) {
                        document.getElementById('summary-taxes').textContent = formatMoney(taxes);
                    }
                }
            })
            .catch(error => console.error('Erreur:', error));
        };

        // 3. Mise à jour de l'interface (Stock, Prix Ligne, Bouton Valider)
        const updateCartState = () => {
            let hasGlobalError = false;

            document.querySelectorAll('.cart-item-row').forEach(row => {
                const price = parseFloat(row.dataset.price);
                const maxStock = parseInt(row.dataset.stock); // Stock spécifique à ce vélo/taille
                const input = row.querySelector('.qty-input');
                let qty = parseInt(input.value);
                
                const errorMsg = row.querySelector('.stock-error-msg');
                const totalDisplay = row.querySelector('.item-total-display');

                // --- VÉRIFICATION DU STOCK ---
                if (qty > maxStock) {
                    hasGlobalError = true;
                    errorMsg.style.display = 'block'; // Affiche le message rouge
                    input.style.color = '#e02b2b';
                    input.style.fontWeight = '800';
                } else {
                    errorMsg.style.display = 'none';
                    input.style.color = '#333';
                    input.style.fontWeight = '700';
                }

                // --- CALCUL PRIX LIGNE ---
                const lineTotal = price * qty;
                totalDisplay.textContent = formatMoney(lineTotal);
            });

            // --- BLOCAGE BOUTON VALIDER ---
            const validationButtons = document.querySelectorAll('.btn-validate');
            validationButtons.forEach(btn => {
                if (hasGlobalError) {
                    btn.classList.add('disabled');
                    btn.onclick = (e) => e.preventDefault();
                    btn.href = "javascript:void(0)";
                } else {
                    btn.classList.remove('disabled');
                    btn.onclick = null;
                    btn.href = "/checkout"; // Ton lien vers la caisse
                }
            });
        };

        // 4. Écouteurs d'événements
        document.querySelectorAll('.cart-item-row').forEach(row => {
            const btnPlus = row.querySelector('.btn-plus');
            const btnMinus = row.querySelector('.btn-minus');
            const input = row.querySelector('.qty-input');
            const rowId = row.dataset.rowId; 

            btnPlus.addEventListener('click', () => {
                let currentQty = parseInt(input.value);
                let newQty = currentQty + 1;
                
                input.value = newQty;
                updateCartState(); // Mise à jour visuelle instantanée
                saveQuantityToServer(rowId, newQty); // Sauvegarde en BDD
            });

            btnMinus.addEventListener('click', () => {
                let currentQty = parseInt(input.value);
                if (currentQty > 1) {
                    let newQty = currentQty - 1;
                    
                    input.value = newQty;
                    updateCartState();
                    saveQuantityToServer(rowId, newQty);
                }
            });
        });

        // Lancement initial au chargement de la page
        updateCartState();
    });
    </script>

</body>
</html>