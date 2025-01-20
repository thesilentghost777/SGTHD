<html>
<head>
    <title>Classement</title>
<link rel="stylesheet" href="{{asset('css/pointeur/pointeur_classement.css')}}">
</head>
<body>
<div class="container">
    <h1 class="title">📊 Classement des Pointeurs</h1>

    <table class="ranking-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Pointeur</th>
                <th>Manquants XAF</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classement as $index => $item)
                <tr class="{{ $item->manquants == 0 ? 'first-place' : '' }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->pointeur }}</td>
                    <td>{{ number_format($item->manquants, 2, ',', ' ') }} XAF</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
