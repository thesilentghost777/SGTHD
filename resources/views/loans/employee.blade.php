@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Gestion de vos prêts</h2>
        </div>

        <div class="p-6">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Situation actuelle</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prêt en cours:</span>
                            <span class="font-bold text-blue-600">{{ number_format($loanData->pret, 0, ',', ' ') }} XAF</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Remboursement du mois:</span>
                            <span class="font-bold text-red-600">{{ number_format($loanData->remboursement, 0, ',', ' ') }} XAF</span>
                        </div>

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                            <span class="text-gray-700 font-medium">Solde après remboursement:</span>
                            <span class="font-bold text-gray-800">{{ number_format($loanData->pret - $loanData->remboursement, 0, ',', ' ') }} XAF</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Demander un prêt</h3>

                    @if($loanData->pret > 0)
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Vous avez déjà un prêt en cours. Vous ne pouvez pas faire une nouvelle demande pour le moment.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <form action="{{ route('loans.request') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">Montant souhaité</label>
                                <div class="relative mt-1 rounded-md shadow-sm">
                                    <input type="number" name="montant" id="montant" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0" min="1000" required>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm">XAF</span>
                                    </div>
                                </div>
                                @error('montant')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-300">
                                Soumettre la demande
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique des demandes</h3>

                @php
                $loanRequests = DB::table('loan_requests')
                    ->where('user_id', auth()->id())
                    ->orderBy('created_at', 'desc')
                    ->get();
                @endphp

                @if($loanRequests->isEmpty())
                    <p class="text-gray-500 italic">Aucune demande de prêt effectuée.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($loanRequests as $request)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Carbon\Carbon::parse($request->created_at)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($request->amount, 0, ',', ' ') }} XAF</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $request->status == 'approved' ? 'bg-green-100 text-green-800' :
                                                  ($request->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $request->status == 'approved' ? 'Approuvé' :
                                                  ($request->status == 'rejected' ? 'Refusé' : 'En attente') }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
