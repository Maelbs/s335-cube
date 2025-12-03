<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier</title>
    {{-- Réutilise ton header CSS si besoin --}}
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">

</head>
<body>
    @include('layouts.header')

    <div class="cart-container">
        <h1>Votre Panier</h1>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @if(empty($cart))
            <p>Votre panier est vide.</p>
            <a href="/" style="text-decoration: underline;">Retour à la boutique</a>
        @else
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Taille</th>
                        <th>Prix</th>
                        <th>Qté</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cart as $key => $item)
                    <tr>
                        <td>
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" width="50" style="vertical-align: middle; margin-right: 10px;">
                            @endif
                            {{ $item['name'] }} <br>
                            <small style="color: #666;">Ref: {{ $item['reference'] }}</small>
                        </td>
                        <td>{{ $item['taille'] }}</td>
                        <td>{{ number_format($item['price'], 2) }} €</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>{{ number_format($item['price'] * $item['quantity'], 2) }} €</td>
                        <td>
                            <form action="{{ route('cart.remove', $key) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-box">
                Total : {{ number_format($total, 2) }} €
            </div>
            
            <div style="text-align: right;">
                <a href="#" class="btn-checkout">Valider la commande</a>
            </div>
        @endif
    </div>
</body>
</html>