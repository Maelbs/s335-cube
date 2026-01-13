<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DPO - Anonymisation</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f3f4f6;
            font-family: sans-serif;
        }
        .container {
            background-color: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        h1 {
            color: #333;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        input[type="email"], input[type="date"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 20px; /* Bords arrondis demandés */
            box-sizing: border-box; /* Pour que le padding ne dépasse pas */
            margin-top: 5px;
        }
        .btn-validate {
            background-color: #e32636; /* Rouge Cube Bikes */
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            margin-top: 10px;
        }
        .btn-validate:hover {
            background-color: #cc1f2d;
        }
        .btn-logout {
            display: inline-block;
            margin-top: 20px;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        .alert-success { background-color: #d1fae5; color: #065f46; }
        .alert-error { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Anonymiser</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('dpo.process') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" style="display:none;">Email du client</label>
                <input type="email" name="email" id="email" placeholder="Email du client" required>
            </div>

            <div class="form-group">
                <label for="date_limite" style="text-align:left; display:block; margin-left:10px; font-size:0.8rem; color:#666;">Date limite (inclus)</label>
                <input type="date" name="date_limite" id="date_limite" required>
            </div>

            <button type="submit" class="btn-validate">Valider</button>
        </form>

        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn-logout" style="background:none; border:none; cursor:pointer; text-decoration:underline;">Se déconnecter</button>
        </form>
    </div>

</body>
</html>