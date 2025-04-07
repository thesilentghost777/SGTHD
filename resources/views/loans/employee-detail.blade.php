@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Détail du prêt - {{ $employee->name }}</h2>
            <a href="{{ route('loans.employees-with-loans') }}" class="bg-white text-indigo-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-50 transition-colors">
                Retour à la liste
            </a>
        </div>

        <div class="p-6 space-y-8">
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Information sur le prêt</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Prêt restant:</span>
                            <span class="font-bold text-blue-600">{{ number_format($loanData->pret, 0, ',', ' ') }} XAF</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Remboursement mensuel actuel:</span>
                            <span class="font-bold text-red-600">{{ number_format($loanData->remboursement, 0, ',', ' ') }} XAF</span>
                        </div>

                        @php
                        $totalRepaid = DB::table('loan_repayments')
                                        ->where('user_id', $employee->id)
                                        ->sum('amount');
                        @endphp

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total déjà remboursé:</span>
                            <span class="font-bold text-green-600">{{ number_format($totalRepaid, 0, ',', ' ') }} XAF</span>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Définir le remboursement mensuel</h3>

                    <form action="{{ route('loans.set-monthly-repayment', $employee->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="remboursement" class="block text-sm font-medium text-gray-700 mb-2">Montant à rembourser ce mois</label>
                            <div class="relative mt-1 rounded-md shadow-sm">
                                <input type="number" name="remboursement" id="remboursement" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0" min="0" max="{{ $loanData->pret }}" value="{{ $loanData->remboursement }}" required>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">XAF</span>
                                </div>
                            </div>
                            @error('remboursement')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-300">
                            Enregistrer
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enregistrer un remboursement manuel</h3>

                <form action="{{ route('loans.record-repayment', $employee->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="montant" class="block text-sm font-medium text-gray-700 mb-2">Montant remboursé</label>
                        <div class="relative mt-1 rounded-md shadow-sm">
                            <input type="number" name="montant" id="montant" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="0" min="1" max="{{ $loanData->pret }}" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">XAF</span>
                            </div>
                        </div>
                        @error('montant')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md transition-colors duration-300">
                        Enregistrer le remboursement
                    </button>
                </form>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Historique des remboursements</h3>

                @if($repaymentHistory->isEmpty())
                    <p class="text-gray-500 italic">Aucun remboursement effectué.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($repaymentHistory as $repayment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ Carbon\Carbon::parse($repayment->created_at)->format('d/m/Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($repayment->amount, 0, ',', ' ') }} XAF</td>
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
