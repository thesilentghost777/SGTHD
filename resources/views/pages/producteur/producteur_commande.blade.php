@extends('pages.producteur.pdefault')

@section('page-content')
<div class="container mx-auto bg-white rounded-lg shadow-lg p-6 animate-fadeIn">
    <!-- User Info -->
    <div class="user-info bg-gradient-to-r from-blue-800 to-blue-600 text-white rounded-lg shadow-lg p-6 mb-6">
        <h4 class="text-xl font-bold uppercase tracking-wider">Informations producteur</h4>
        <p class="mt-2 text-sm">Nom: {{ $nom }}</p>
        <p class="mt-2 text-sm">Secteur: {{ $secteur }}</p>
    </div>

    <!-- Section Header -->
    <div class="section-header border-b-4 border-blue-800 pb-2 mb-6">
        <h2 class="text-2xl font-extrabold uppercase text-blue-800">Liste des Commandes</h2>
    </div>

    <!-- Commandes Table -->
    @if(count($commandes) > 0)
        <div class="overflow-x-auto">
            <table class="commandes-table w-full border-collapse">
                <thead>
                    <tr class="bg-blue-800 text-white">
                        <th class="p-4 text-left">ID</th>
                        <th class="p-4 text-left">Libellé</th>
                        <th class="p-4 text-left">Date de commande</th>
                        <th class="p-4 text-left">Produit</th>
                        <th class="p-4 text-left">Quantité</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commandes as $commande)
                        <tr class="hover:bg-blue-50 transition duration-300">
                            <td class="p-4 font-bold text-blue-800">{{ $commande->id }}</td>
                            <td class="p-4">{{ $commande->libelle }}</td>
                            <td class="p-4 text-gray-600">{{ \Carbon\Carbon::parse($commande->date_commande)->format('d/m/Y H:i') }}</td>
                            <td class="p-4">
                                @if($commande->produit)
                                    {{ \App\Models\Produit_fixes::where('code_produit', $commande->produit)->first()->nom ?? 'N/A' }}
                                @else
                                    Non spécifié
                                @endif
                            </td>
                            <td class="p-4">{{ $commande->quantite }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state border-dashed border-2 border-blue-800 rounded-lg p-10 text-center text-blue-800">
            <p class="text-lg">Aucune commande trouvée pour votre secteur.</p>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.commandes-table tbody tr').forEach(row => {
            row.addEventListener('click', () => {
                console.log('Commande sélectionnée:', row.querySelector('td').textContent);
            });
        });
    });
</script>
@endsection
