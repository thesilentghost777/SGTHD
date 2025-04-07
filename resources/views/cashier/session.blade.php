@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestion de Session #{{ $session->id }}</h1>
            <p class="text-gray-600">Démarrée le {{ $session->start_time->format('d/m/Y à H:i') }}</p>
        </div>
        <a href="{{ route('cashier.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="mdi mdi-arrow-left mr-2"></i>Retour
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <!-- Statut de la session -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Statut de la Session</h2>
                <span class="px-3 py-1 {{ $session->isActive() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }} rounded-full text-sm">
                    {{ $session->isActive() ? 'Active' : 'Clôturée' }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <p class="text-sm text-gray-500">Caisse initiale</p>
                        <p class="text-sm font-medium">{{ number_format($session->initial_cash, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @if($session->final_cash)
                    <div class="flex justify-between mt-2">
                        <p class="text-sm text-gray-500">Caisse finale</p>
                        <p class="text-sm font-medium">{{ number_format($session->final_cash, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endif
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <p class="text-sm text-gray-500">Monnaie initiale</p>
                        <p class="text-sm font-medium">{{ number_format($session->initial_change, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @if($session->final_change)
                    <div class="flex justify-between mt-2">
                        <p class="text-sm text-gray-500">Monnaie finale</p>
                        <p class="text-sm font-medium">{{ number_format($session->final_change, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endif
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between">
                        <p class="text-sm text-gray-500">Mobile initial</p>
                        <p class="text-sm font-medium">{{ number_format($session->initial_mobile_balance, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @if($session->final_mobile_balance)
                    <div class="flex justify-between mt-2">
                        <p class="text-sm text-gray-500">Mobile final</p>
                        <p class="text-sm font-medium">{{ number_format($session->final_mobile_balance, 0, ',', ' ') }} FCFA</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($session->notes)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Notes initiales:</p>
                <p class="text-sm">{{ $session->notes }}</p>
            </div>
            @endif

            @if($session->end_notes)
            <div class="mt-2 p-3 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Notes de clôture:</p>
                <p class="text-sm">{{ $session->end_notes }}</p>
            </div>
            @endif

            <!-- Statistiques de la session clôturée -->
            @if(!$session->isActive())
            <div class="mt-4 border-t pt-4">
                <h3 class="text-md font-semibold text-gray-700 mb-3">Résumé de la session</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-blue-600 mb-1">Durée de la session</p>
                        <p class="text-lg font-semibold">{{ $session->duration }}</p>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-blue-600 mb-1">Total retraits</p>
                        <p class="text-lg font-semibold">{{ number_format($session->total_withdrawals, 0, ',', ' ') }} FCFA</p>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-blue-600 mb-1">Montant versé</p>
                        <p class="text-lg font-semibold">{{ number_format($session->cash_remitted, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-gray-500">Écart de caisse:</p>
                        <p class="text-sm font-medium {{ $session->discrepancy > 0 ? 'text-green-600' : ($session->discrepancy < 0 ? 'text-red-600' : 'text-gray-600') }}">
                            {{ number_format($session->discrepancy, 0, ',', ' ') }} FCFA
                            ({{ $session->discrepancy > 0 ? 'Excédent' : ($session->discrepancy < 0 ? 'Déficit' : 'Aucun écart') }})
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if($session->isActive())
            <div class="mt-6 flex justify-end space-x-3">
                <button onclick="document.getElementById('withdrawalModal').classList.remove('hidden')" class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="mdi mdi-cash-remove mr-1"></i> Enregistrer un retrait
                </button>
                <button onclick="document.getElementById('endSessionModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="mdi mdi-cash-check mr-1"></i> Clôturer la session
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Retraits de caisse -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Retraits de caisse</h2>

            @if(count($withdrawals) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Responsable</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motif</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($withdrawals as $withdrawal)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $withdrawal->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $withdrawal->withdrawn_by }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $withdrawal->reason }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{ number_format($withdrawal->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right font-medium">Total:</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                    {{ number_format($withdrawals->sum('amount'), 0, ',', ' ') }} FCFA
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="bg-gray-50 p-4 rounded text-center">
                    <p class="text-gray-500">Aucun retrait enregistré pour cette session.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour enregistrer un retrait -->
<div id="withdrawalModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Enregistrer un retrait</h3>
                <button onclick="document.getElementById('withdrawalModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <form action="{{ route('cashier.withdraw', $session->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Montant du retrait</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="amount" id="amount" required
                                class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="withdrawn_by" class="block text-sm font-medium text-gray-700 mb-1">Prélevé par</label>
                        <select name="withdrawn_by" id="withdrawn_by" required
                            class="block w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                            <option value="">Sélectionner un responsable</option>
                            @foreach($adminEmployees as $employee)
                                <option value="{{ $employee->name }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Motif du retrait</label>
                        <textarea id="reason" name="reason" rows="2" required
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Explication du retrait..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        onclick="document.getElementById('withdrawalModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Enregistrer
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal pour clôturer la session -->
<div id="endSessionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Clôturer la session</h3>
                <button onclick="document.getElementById('endSessionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <form action="{{ route('cashier.end-session', $session->id) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="final_cash" class="block text-sm font-medium text-gray-700 mb-1">Montant final en caisse</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="final_cash" id="final_cash" required
                                   class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="final_change" class="block text-sm font-medium text-gray-700 mb-1">Monnaie restante</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="final_change" id="final_change" required
                                   class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nouveaux champs pour MOMO et OM -->
<div>
    <label for="momo_amount" class="block text-sm font-medium text-gray-700 mb-1">
    <div class="flex items-center">
    <div class="w-8 h-6 mr-2">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 60">
      <!-- Fond jaune hexagonal avec bordure blanche -->
      <path d="M10,10 L90,10 L90,50 L10,50 Z" fill="#FFCC00" stroke="white" stroke-width="2"/>
      <!-- Texte MTN -->
      <text x="25" y="25" font-family="Arial" font-size="14" font-weight="bold" fill="black">MTN</text>
      <!-- Texte Mobile Money -->
      <text x="15" y="40" font-family="Arial" font-size="10" font-weight="bold" fill="#FF0000">Mobile</text>
      <text x="15" y="48" font-family="Arial" font-size="10" font-weight="bold" fill="#FF0000">Money</text>
      <!-- Téléphone simplifié -->
      <rect x="65" y="20" width="20" height="30" rx="2" fill="white" stroke="black" stroke-width="1"/>
      <!-- Écran du téléphone -->
      <rect x="68" y="23" width="14" height="15" fill="#004466"/>
      <!-- Touches du téléphone -->
      <rect x="69" y="40" width="4" height="3" fill="black"/>
      <rect x="75" y="40" width="4" height="3" fill="black"/>
      <rect x="69" y="45" width="4" height="3" fill="black"/>
      <rect x="75" y="45" width="4" height="3" fill="black"/>
      <!-- Icône de paiement/carte -->
      <path d="M58,15 L70,10 L75,25 L63,30 Z" fill="#004466" stroke="white"/>
      <text x="65" y="22" font-family="Arial" font-size="7" font-weight="bold" fill="white">CFA</text>
      <!-- Triangle rouge -->
      <path d="M60,30 L70,30 L65,40 Z" fill="#FF0000"/>
    </svg>
    </div>
     Montant MOMO final (MTN Mobile Money)
    </div>
    </label>
    <div class="mt-1 relative rounded-md shadow-sm">
    <input type="number" id="momo_amount"
    class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
    placeholder="0" oninput="calculateTotal()">
    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
    <span class="text-gray-500 sm:text-sm">FCFA</span>
    </div>
    </div>
    </div>
    <div>
    <label for="om_amount" class="block text-sm font-medium text-gray-700 mb-1">
    <div class="flex items-center">
    <div class="w-8 h-6 mr-2">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 60">
      <!-- Fond noir -->
      <rect x="10" y="10" width="80" height="40" fill="black"/>
      <!-- Flèche blanche -->
      <path d="M25,20 L35,20 L35,25 L42,25 L42,30 L35,30 L35,35 L25,20" fill="white"/>
      <!-- Flèche orange -->
      <path d="M45,20 L55,35 L55,30 L62,30 L62,25 L55,25 L55,20 L45,20" fill="#FF6600"/>
      <!-- Texte Orange Money -->
      <text x="25" y="45" font-family="Arial" font-size="10" font-weight="bold" fill="#FF6600">Orange Money</text>
    </svg>
    </div>
     Montant OM final (Orange Money)
    </div>
    </label>
    <div class="mt-1 relative rounded-md shadow-sm">
    <input type="number" id="om_amount"
    class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
    placeholder="0" oninput="calculateTotal()">
    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
    <span class="text-gray-500 sm:text-sm">FCFA</span>
    </div>
    </div>
    </div>

                    <div>
                        <label for="final_mobile_balance" class="block text-sm font-medium text-gray-700 mb-1">Solde compte mobile final</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="final_mobile_balance" id="final_mobile_balance" required
                                   class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="0" readonly>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="cash_remitted" class="block text-sm font-medium text-gray-700 mb-1">Montant versé</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="cash_remitted" id="cash_remitted" required
                                   class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                   placeholder="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="end_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes de clôture (optionnel)</label>
                        <textarea id="end_notes" name="end_notes" rows="2"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                  placeholder="Notes supplémentaires..."></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                            onclick="document.getElementById('endSessionModal').classList.add('hidden')"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Clôturer la session
                    </button>
                </div>

                <script>
                    function calculateTotal() {
                        // Récupérer les valeurs des champs MOMO et OM
                        const momoAmount = parseFloat(document.getElementById('momo_amount').value) || 0;
                        const omAmount = parseFloat(document.getElementById('om_amount').value) || 0;

                        // Calculer le total
                        const total = momoAmount + omAmount;

                        // Mettre à jour le champ du solde mobile final
                        document.getElementById('final_mobile_balance').value = total;

                        // Mettre à jour le champ notes
                        const currentNotes = document.getElementById('end_notes').value;
                        if (!currentNotes.includes("MOMO:") && !currentNotes.includes("OM:")) {
                            document.getElementById('end_notes').value = `MOMO: ${momoAmount} FCFA | OM: ${omAmount} FCFA` +
                                (currentNotes ? "\n\n" + currentNotes : "");
                        } else {
                            // Mettre à jour seulement la partie concernant MOMO et OM
                            const noteLines = currentNotes.split("\n");
                            noteLines[0] = `MOMO: ${momoAmount} FCFA | OM: ${omAmount} FCFA`;
                            document.getElementById('end_notes').value = noteLines.join("\n");
                        }
                    }

                    // Initialiser les calculs au chargement de la page
                    document.addEventListener('DOMContentLoaded', function() {
                        calculateTotal();
                    });
                </script>
            </form>
        </div>
    </div>
</div>
@endsection

