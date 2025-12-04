<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification du code</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 320px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        input {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        button {
            padding: 10px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>

<section>
    @if (session('success'))
        <div style="
            background:#d4edda;
            color:#155724;
            padding:12px;
            border-radius:5px;
            margin-bottom:15px;
            border:1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.check') }}">
        @csrf
        <input type="hidden" name="email" value="{{ session('reg_data.email') }}">
        <input type="text" name="verification_code" required placeholder="Code de vérification" />
        <button type="submit">Vérifier</button>
    </form>

</section>

</body>
</html>