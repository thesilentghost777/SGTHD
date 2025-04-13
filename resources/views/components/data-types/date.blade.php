@foreach($data as $item)
    <!-- Vérification si l'élément contient une date -->
    @if(isset($item->date))
        <!-- Formatage de la date en français -->
        <div>{{ \Carbon\Carbon::parse($item->date)->locale('fr')->translatedFormat('l j F Y') }}</div>
    @endif
@endforeach
