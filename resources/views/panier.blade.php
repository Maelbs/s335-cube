<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/panier.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
<<<<<<< HEAD
    
=======


>>>>>>> af1c590583c17add1f2ecbe99b6f243f3ef975be
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
                        <div class="cart-card cart-item-row" data-row-id="{{ $key }}" data-price="{{ $item['price'] }}"
                            data-stock="{{ $item['max_stock'] }}">

                            <div class="card-img">
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                                    onerror="this.src='https://placehold.co/150x150?text=Img+Error'">
                            </div>

                            <div class="card-info">
                                <h3 class="product-name">{{ $item['name'] }}</h3>
                                <div class="product-price-unit">{{ number_format($item['price'], 2, ',', ' ') }} €</div>
                                <div class="product-meta">RÉFÉRENCE : {{ $item['reference'] }}</div>
                                @if(isset($item['taille']) && $item['taille'] !== 'Unique')
                                    <div class="product-meta">Taille : {{ $item['taille'] }}</div>
                                @endif
                            </div>

                            <div class="card-actions">
                                <div class="row-total-price">
                                    <span class="item-total-display">{{ number_format($item['price'] * $item['quantity'], 2, ',', ' ') }}</span>
                                    € TTC
                                </div>

                                <div class="qty-container">
                                    <div class="qty-selector">
                                        <button type="button" class="qty-btn btn-minus">-</button>
                                        <input type="text" value="{{ $item['quantity'] }}" class="qty-input" readonly>
                                        <button type="button" class="qty-btn btn-plus">+</button>
                                    </div>
                                    <div class="stock-error-msg">
                                        Stock insuffisant.
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
                        <a href="{{ Auth::check() ? route('payment.show') : route('login') }}" class="btn-cube-red btn-validate">
                            <span>► Valider mon panier</span>
                        </a>
                    </div>
                </div>

                <div class="cart-summary-section">
                    <h2 class="section-title">RÉCAPITULATIF</h2>

                    <div class="summary-card">
                        <div class="summary-row">
                            <span>Panier (<span id="summary-count-txt">{{ count($cart) }}</span>)</span>
                            <span>{{ number_format($subTotal ?? 0, 2, ',', ' ') }} €</span>
                        </div>

                        @if(isset($discountAmount) && $discountAmount > 0)
                            <div class="summary-row promo-active-row">
                                    <div class="promo-label-group">
                                        <span>Réduction ({{ $promoCode }})</span>
                                        <button type="button" id="btn-remove-promo" class="btn-remove-promo" title="Retirer le code">
                                            &times;
                                        </button>
                                    </div>
                                <span class="promo-amount">- {{ number_format($discountAmount, 2, ',', ' ') }} €</span>
                            </div>
                        @endif

                        <div class="summary-row">
                            <span>Livraison</span>
                            <span>gratuit</span>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-row total-row">
                            <span>Total TTC</span>
                            <span>{{ number_format($total ?? 0, 2, ',', ' ') }} €</span>
                        </div>
                        
                        <div class="taxes-info">
                            Taxes incluses : {{ number_format(($total ?? 0) * 0.2, 2, ',', ' ') }} €
                            <span class="info-bubble" data-tooltip="Le montant de la TVA (20%) est déjà inclus dans le prix total affiché.">?</span>
                        </div>

                        <div class="summary-btn-container">
                            <a href="{{ Auth::check() ? route('payment.show') : route('login') }}" class="btn-cube-red btn-validate">
                                <span>► Valider mon panier</span>
                            </a>
                        </div>
                    </div>

                    <div class="promo-container">
                        <h3 class="promo-title">
                            CODE PROMO
                            <span class="info-bubble" data-tooltip="Si vous possédez un code de réduction Cube France, saisissez-le ici pour mettre à jour votre total.">?</span>
                        </h3>
                        
                        <div class="promo-box">
                            <input type="text" id="promo-input" class="promo-input" placeholder="Code promo" value="{{ $promoCode ?? '' }}">
                            <button type="button" id="btn-apply-promo" class="btn-cube-black">
                                <span>► Appliquer</span>
                            </button>
                        </div>
                        
                        <div id="promo-message" class="promo-msg"></div>
                    </div>

                </div>

            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const saveQuantityToServer = (rowId, newQty) => {
                fetch('{{ route("cart.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ id: rowId, quantity: newQty })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload(); 
                    }
                })
                .catch(error => console.error('Erreur:', error));
            };

            document.querySelectorAll('.cart-item-row').forEach(row => {
                const btnPlus = row.querySelector('.btn-plus');
                const btnMinus = row.querySelector('.btn-minus');
                const input = row.querySelector('.qty-input');
                const rowId = row.dataset.rowId;
                const maxStock = parseInt(row.dataset.stock);
                const errorMsg = row.querySelector('.stock-error-msg');

                btnPlus.addEventListener('click', () => {
                    let currentQty = parseInt(input.value);
                    if (currentQty < maxStock) {
                        let newQty = currentQty + 1;
                        input.value = newQty;
                        errorMsg.style.display = 'none';
                        saveQuantityToServer(rowId, newQty);
                    } else {
                        errorMsg.style.display = 'block';
                    }
                });

                btnMinus.addEventListener('click', () => {
                    let currentQty = parseInt(input.value);
                    if (currentQty > 1) {
                        let newQty = currentQty - 1;
                        input.value = newQty;
                        errorMsg.style.display = 'none';
                        saveQuantityToServer(rowId, newQty);
                    }
                });
            });

            const btnPromo = document.getElementById('btn-apply-promo');
            const inputPromo = document.getElementById('promo-input');
            const msgPromo = document.getElementById('promo-message');

            if (btnPromo) {
                btnPromo.addEventListener('click', function(e) {
                    e.preventDefault();
                    const code = inputPromo.value.trim();
                    if(!code) return;
                    fetch('{{ route("cart.applyPromo") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ code_promo: code })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            window.location.reload();
                        } else {
                            msgPromo.textContent = data.message;
                            msgPromo.className = 'promo-msg error';
                            msgPromo.style.color = '#e02b2b';
                        }
                    })
                    .catch(error => console.error(error));
                });
            }

            const btnRemovePromo = document.getElementById('btn-remove-promo');
            if (btnRemovePromo) {
                btnRemovePromo.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetch('{{ route("cart.removePromo") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        }
                    });
                });
            }
        });
    </script>
<<<<<<< HEAD
    
=======
>>>>>>> af1c590583c17add1f2ecbe99b6f243f3ef975be
</body>
</html>