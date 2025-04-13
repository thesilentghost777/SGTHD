@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mes Déductions Salariales</h1>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($deductions)
        <div class="bg-white overflow-hidden shadow rounded-lg divide-y divide-gray-200">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Informations de déduction sur votre salaire
                </h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Ces montants seront déduits lors du prochain paiement.
                </p>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="col-span-1 sm:col-span-2">
                        <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center">
                                <i class="mdi mdi-information-outline text-blue-500 text-xl mr-3"></i>
                                <p class="text-sm text-blue-700">
                                    La dernière mise à jour de ces informations a été effectuée le
                                    <span class="font-semibold">{{ $deductions->date->format('d/m/Y') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Manquant Effectif -->
                    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-base font-medium text-gray-900">Manquant Effectif</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="mdi mdi-alert-circle-outline mr-1"></i>
                                Déduit
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ number_format($deductions->manquants, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                        </p>
                    </div>

                    <!-- Remboursement -->
                    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-base font-medium text-gray-900">Remboursement d'emprunt</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="mdi mdi-cash-refund mr-1"></i>
                                Déduit
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ number_format($deductions->remboursement, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                        </p>
                    </div>

                    <!-- Caisse Sociale -->
                    <div class="bg-white p-4 border border-gray-200 rounded-lg shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <h4 class="text-base font-medium text-gray-900">Caisse Sociale</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="mdi mdi-medical-bag mr-1"></i>
                                Déduit
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900">
                            {{ number_format($deductions->caisse_sociale, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                        </p>
                    </div>
                </div>

                <!-- Total de déductions -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center">
                        <h4 class="text-lg font-semibold text-gray-900">Total des déductions</h4>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ number_format($deductions->manquants + $deductions->remboursement + $deductions->pret + $deductions->caisse_sociale, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-blue-50 p-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="mdi mdi-information-outline text-blue-400 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-blue-800">Information</h3>
                    <div class="mt-2 text-blue-700">
                        <p>Vous n'avez actuellement aucune déduction enregistrée.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

