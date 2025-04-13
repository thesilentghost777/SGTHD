@extends('rapports.layout.rapport')

@section('content')
    <!-- En-tête du rapport avec statistiques globales -->
    <div class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total des revenus -->
            <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                <h3 class="text-lg font-semibold text-green-800 mb-2">Total des revenus</h3>
                <p class="text-3xl font-bold text-green-700">{{ number_format($totalRevenus, 2, ',', ' ') }} XAF</p>
                <p class="text-sm text-green-600 mt-2">Pour le mois de {{ $currentMonthName }}</p>
            </div>

            <!-- Total des dépenses -->
            <div class="bg-red-50 p-6 rounded-lg border border-red-200">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Total des dépenses</h3>
                <p class="text-3xl font-bold text-red-700">{{ number_format($totalDepenses, 2, ',', ' ') }} XAF</p>
                <p class="text-sm text-red-600 mt-2">Pour le mois de {{ $currentMonthName }}</p>
            </div>

            <!-- Balance -->
            <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                <h3 class="text-lg font-semibold text-blue-800 mb-2">Balance</h3>
                <p class="text-3xl font-bold {{ $balance >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                    {{ number_format($balance, 2, ',', ' ') }} XAF
                </p>
                <div class="flex items-center mt-2">
                </div>
            </div>
        </div>
    </div>

    <!-- Résumé narratif -->
    <div class="mb-8 bg-white p-6 rounded-lg border border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Résumé des transactions</h3>
        <p class="text-gray-700 leading-relaxed">
            Au cours du mois de <strong>{{ $currentMonthName }}</strong>, l'entreprise a enregistré des revenus totalisant
            <strong>{{ number_format($totalRevenus, 2, ',', ' ') }} XAF</strong> et des dépenses s'élevant à
            <strong>{{ number_format($totalDepenses, 2, ',', ' ') }} XAF</strong>.

            La balance mensuelle est de <strong>{{ number_format($balance, 2, ',', ' ') }} XAF</strong>,
            ce qui représente une {{ $evolution >= 0 ? 'augmentation' : 'diminution' }} de
            <strong>{{ abs($evolution) }}%</strong> par rapport au mois précédent.

            @if($balance > 0)
                Ce résultat positif reflète une bonne gestion financière et une activité commerciale performante.
            @elseif($balance == 0)
                Ce résultat équilibré montre une gestion stable des finances de l'entreprise.
            @else
                Ce résultat négatif nécessite une attention particulière pour améliorer l'équilibre financier dans les mois à venir.
            @endif
        </p>
    </div>

    <!-- Répartition par catégorie -->
    @if(count($transactionsParCategorie) > 0)
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Répartition par catégorie</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Catégorie
                        </th>
                        <th class="py-3 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Type
                        </th>
                        <th class="py-3 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">
                            Montant
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactionsParCategorie as $transaction)
                    <tr class="{{ $transaction->type === 'income' ? 'bg-green-50' : 'bg-red-50' }}">
                        <td class="py-4 px-4 text-sm text-gray-900">
                            {{ $transaction->name }}
                        </td>
                        <td class="py-4 px-4 text-sm text-right {{ $transaction->type === 'income' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $transaction->type === 'income' ? 'Revenu' : 'Dépense' }}
                        </td>
                        <td class="py-4 px-4 text-sm font-medium text-right {{ $transaction->type === 'income' ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($transaction->total, 2, ',', ' ') }} XAF
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Liste des transactions -->
    <div>
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Détail des transactions</h3>
        <div class="overflow-x-auto print:text-xs">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr>
                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Date</th>
                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Description</th>
                        <th class="py-3 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Catégorie</th>
                        <th class="py-3 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Type</th>
                        <th class="py-3 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider border-b">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                    <tr>
                        <td class="py-2 px-4 text-sm text-gray-900 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') }}
                        </td>
                        <td class="py-2 px-4 text-sm text-gray-900">
                            {{ $transaction->description }}
                        </td>
                        <td class="py-2 px-4 text-sm text-gray-900">
                            {{ $transaction->category->name }}
                        </td>
                        <td class="py-2 px-4 text-sm text-right {{ $transaction->type === 'income' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $transaction->type === 'income' ? 'Revenu' : 'Dépense' }}
                        </td>
                        <td class="py-2 px-4 text-sm font-medium text-right {{ $transaction->type === 'income' ? 'text-green-700' : 'text-red-700' }}">
                            {{ number_format($transaction->amount, 2, ',', ' ') }} XAF
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
