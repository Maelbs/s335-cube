<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Logs - CUBE</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: #f3f4f6; padding: 20px; color: #333; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; color: #111; border-bottom: 2px solid #eee; padding-bottom: 15px; }
        .summary { display: flex; gap: 20px; margin-bottom: 20px; }
        .card { background: #00bce4; color: white; padding: 15px 25px; border-radius: 6px; font-weight: bold; }
        .number { font-size: 24px; display: block; }
        
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead { background: #f9fafb; }
        th, td { text-align: left; padding: 12px; border-bottom: 1px solid #e5e7eb; }
        th { font-weight: 600; color: #6b7280; text-transform: uppercase; font-size: 11px; }
        tr:hover { background-color: #f9f9f9; }
        .ip { font-family: monospace; color: #666; }
        .date { white-space: nowrap; color: #888; }
        .url { color: #2563eb; font-weight: 500; }
    </style>
</head>
<body>

<div class="container">
    <h1>Journal des visites (Logs)</h1>
    
    <div class="summary">
        <div class="card">
            <span class="number">{{ $total }}</span>
            Visites Totales
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Page consultée</th>
                <th>Adresse IP</th>
                <th>Navigateur / OS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($visits as $visit)
            <tr>
                <td class="date">{{ $visit->visited_at }}</td>
                <td class="url">{{ $visit->url }}</td>
                <td class="ip">{{ $visit->ip_address }}</td>
                <td>{{ Str::limit($visit->user_agent, 60) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

</body>
</html>