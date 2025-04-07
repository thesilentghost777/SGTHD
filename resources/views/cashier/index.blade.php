@extends('layouts.app')

@section('content')

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Gestion de la caisse</h1>
        <div class="flex space-x-2">
            <a href="{{ route('cashier.reports') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="mdi mdi-file-chart mr-2"></i>Rapports
            </a>
        </div>
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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Carte d'information de session active -->
        <div class="col-span-1 md:col-span-2 bg-white rounded-lg shadow-md p-6">
            @if($openSession)
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-green-600">Session Active</h2>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                        Démarrée il y a {{ $openSession->start_time->diffForHumans() }}
                    </span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Caisse initiale</p>
                        <p class="text-xl font-bold">{{ number_format($openSession->initial_cash, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Monnaie reçue</p>
                        <p class="text-xl font-bold">{{ number_format($openSession->initial_change, 0, ',', ' ') }} FCFA</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1">Compte mobile initial (somme de MOMO et OM)</p>
                        <p class="text-xl font-bold">{{ number_format($openSession->initial_mobile_balance, 0, ',', ' ') }} FCFA</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <a href="{{ route('cashier.session', $openSession->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        Gérer la session
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-5xl text-gray-300 mb-4">
                        <i class="mdi mdi-cash-register"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-600 mb-2">Aucune session active</h2>
                    <p class="text-gray-500 mb-4">Pour commencer à travailler, démarrez une nouvelle session de caisse.</p>
                    <button onclick="document.getElementById('startSessionModal').classList.remove('hidden')" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">
                        <i class="mdi mdi-plus-circle mr-1"></i> Démarrer une nouvelle session
                    </button>
                </div>
            @endif
        </div>

        <!-- Carte statistiques -->
        <div class="col-span-1 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Statistiques</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Sessions récentes</p>
                    <p class="text-2xl font-bold">{{ $sessions->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Session aujourd'hui</p>
                    <p class="text-2xl font-bold">
                        {{ $sessions->where('start_time', '>=', \Carbon\Carbon::today())->count() }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Session ce mois</p>
                    <p class="text-2xl font-bold">
                        {{ $sessions->where('start_time', '>=', \Carbon\Carbon::now()->startOfMonth())->count() }}
                    </p>
                </div>
                <div class="pt-4">
                    <a href="{{ route('cashier.reports') }}" class="text-blue-500 hover:text-blue-700 text-sm">
                        <i class="mdi mdi-chart-bar mr-1"></i> Voir les rapports détaillés
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des sessions récentes -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Sessions récentes</h2>

        @if($sessions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 text-left border-b">ID</th>
                            <th class="py-3 px-4 text-left border-b">Date</th>
                            <th class="py-3 px-4 text-right border-b">Caisse Initiale</th>
                            <th class="py-3 px-4 text-right border-b">Caisse Finale</th>
                            <th class="py-3 px-4 text-center border-b">Durée</th>
                            <th class="py-3 px-4 text-center border-b">Statut</th>
                            <th class="py-3 px-4 text-center border-b">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border-b">{{ $session->id }}</td>
                                <td class="py-3 px-4 border-b">{{ $session->start_time->format('d/m/Y H:i') }}</td>
                                <td class="py-3 px-4 text-right border-b">{{ number_format($session->initial_cash, 0, ',', ' ') }} FCFA</td>
                                <td class="py-3 px-4 text-right border-b">
                                    @if($session->end_time)
                                        {{ number_format($session->final_cash, 0, ',', ' ') }} FCFA
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center border-b">{{ $session->duration }}</td>
                                <td class="py-3 px-4 text-center border-b">
                                    @if($session->end_time)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Clôturée</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center border-b">
                                    <a href="{{ route('cashier.session', $session->id) }}" class="text-blue-500 hover:text-blue-700 mr-2">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $sessions->links() }}
            </div>
        @else
            <div class="bg-gray-50 p-4 rounded text-center">
                <p class="text-gray-500">Aucune session de caisse trouvée.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal pour démarrer une session -->
<div id="startSessionModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Démarrer une nouvelle session</h3>
                <button onclick="document.getElementById('startSessionModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                    <i class="mdi mdi-close text-xl"></i>
                </button>
            </div>

            <form action="{{ route('cashier.start-session') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="initial_cash" class="block text-sm font-medium text-gray-700 mb-1">Montant d'argent reçu</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="initial_cash" id="initial_cash" required
                                class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="0">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="initial_change" class="block text-sm font-medium text-gray-700 mb-1">Montant de monnaie reçu</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="initial_change" id="initial_change" required
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
                           Montant MOMO (MTN Mobile Money)
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
                           Montant OM (Orange Money)
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
                        <label for="initial_mobile_balance" class="block text-sm font-medium text-gray-700 mb-1">Solde compte mobile initial(somme de MOMO et OM)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" name="initial_mobile_balance" id="initial_mobile_balance" required
                                class="block w-full rounded-md border-gray-300 pl-3 pr-12 focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                placeholder="0" readonly>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">FCFA</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Entrer la distribution MOMO|OM : exemple : MOMO : 100000 OM:225000)</label>
                        <textarea id="notes" name="notes" rows="2"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                            placeholder="Notes supplémentaires..." readonly></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button"
                        onclick="document.getElementById('startSessionModal').classList.add('hidden')"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Démarrer la session
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
<script>
    function calculateTotal() {
        // Récupérer les valeurs des champs MOMO et OM
        const momoAmount = parseFloat(document.getElementById('momo_amount').value) || 0;
        const omAmount = parseFloat(document.getElementById('om_amount').value) || 0;

        // Calculer le total
        const total = momoAmount + omAmount;

        // Mettre à jour le champ du solde mobile
        document.getElementById('initial_mobile_balance').value = total;

        // Mettre à jour le champ notes
        document.getElementById('notes').value = `MOMO: ${momoAmount} FCFA | OM: ${omAmount} FCFA`;
    }

    // Initialiser les calculs au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotal();
    });
</script>
