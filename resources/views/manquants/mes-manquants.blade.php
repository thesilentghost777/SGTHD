@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Mes Manquants</h1>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if($manquant)
        <div class="bg-white overflow-hidden shadow-lg rounded-lg border border-gray-100">
            <div class="px-6 py-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <i class="mdi mdi-clipboard-text-outline mr-2 text-blue-500"></i>
                        État de votre manquant
                    </h2>
                    <span class="px-4 py-2 inline-flex items-center text-sm leading-5 font-semibold rounded-full
                        @if($manquant->statut == 'en_attente') bg-yellow-100 text-yellow-800
                        @elseif($manquant->statut == 'ajuste') bg-blue-100 text-blue-800
                        @elseif($manquant->statut == 'valide') bg-green-100 text-green-800
                        @endif">
                        <i class="mdi
                            @if($manquant->statut == 'en_attente') mdi-clock-outline
                            @elseif($manquant->statut == 'ajuste') mdi-adjust
                            @elseif($manquant->statut == 'valide') mdi-check-circle-outline
                            @endif mr-2"></i>
                        {{ ucfirst(str_replace('_', ' ', $manquant->statut)) }}
                    </span>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <dl class="grid grid-cols-1 gap-5 sm:gap-6">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-600 flex items-center">
                                <i class="mdi mdi-currency-usd mr-2 text-blue-500 text-xl"></i>
                                Montant
                            </dt>
                            <dd class="mt-1 text-xl font-bold text-gray-900 sm:mt-0 sm:col-span-2">
                                {{ number_format($manquant->montant, 0, ',', ' ') }} <span class="text-sm font-normal text-gray-500">FCFA</span>
                            </dd>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-600 flex items-center">
                                <i class="mdi mdi-text-box-outline mr-2 text-blue-500 text-xl"></i>
                                Explication
                            </dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                <div class="whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-100">{{ $manquant->explication }}</div>
                            </dd>
                        </div>

                        @if($manquant->commentaire_dg)
                            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-600 flex items-center">
                                    <i class="mdi mdi-comment-text-outline mr-2 text-blue-500 text-xl"></i>
                                    Commentaire du DG
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                    <div class="bg-white p-4 rounded-lg border border-blue-200 shadow-sm">{{ $manquant->commentaire_dg }}</div>
                                </dd>
                            </div>
                        @endif

                        <div class="bg-white rounded-lg border border-gray-200 px-6 py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                            <dt class="text-sm font-medium text-gray-600 flex items-center">
                                <i class="mdi mdi-calendar-clock mr-2 text-blue-500 text-xl"></i>
                                Dernière mise à jour
                            </dt>
                            <dd class="mt-1 text-sm text-gray-700 sm:mt-0 sm:col-span-2">
                                {{ $manquant->updated_at->format('d/m/Y à H:i') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    @else
        <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-6 rounded-lg border border-blue-200 shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="mdi mdi-information-outline text-blue-500 text-2xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-800">Information</h3>
                    <div class="mt-2 text-gray-700">
                        <p>Vous n'avez actuellement aucun manquant enregistré.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
