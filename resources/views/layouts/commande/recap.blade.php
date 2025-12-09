<h3>Récapitulatif de la commande</h3>
<table class="table">
    <thead>
        <tr>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Prix Unitaire</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($cart as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ number_format($item['price'], 2) }} €</td>
                <td>{{ number_format($item['total'], 2) }} €</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h4>Total à payer : <strong>{{ number_format($total, 2) }} €</strong></h4>
