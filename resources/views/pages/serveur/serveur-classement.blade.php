<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classements des Serveurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #007bff;
            color: #f4f4f4;
        }
        .table-primary{
            background-color: #f4f4f4
        }
        .rank {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Classement des Serveurs</h1>
        <table>
            <thead class="table-primary">
                <tr>
                    <th>Rang</th>
                    <th>Nom du Serveur</th>
                    <th>Total des Ventes (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classements as $index => $classement)
                    <tr>
                        <td class="rank">N°{{ $index + 1 }}</td>
                        <td>{{ $classement->name }}</td>
                        <td>{{ number_format($classement->total_ventes, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
