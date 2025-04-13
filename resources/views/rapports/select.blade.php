@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Sélection des Rapports</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Rapport Employés -->
        <a href="{{ route('rapports.index') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-purple-600">
                <i class="mdi mdi-account-group text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Rapport Employés</h3>
            <p class="text-sm text-gray-600 text-center mt-2">Informations détaillées sur les employés</p>
        </a>

        <!-- Rapport Salaire -->
        <a href="{{ route('rapport_salaire') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-blue-600">
                <i class="mdi mdi-cash-multiple text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Rapport Salaire</h3>
            <p class="text-sm text-gray-600 text-center mt-2">État des salaires versés</p>
        </a>

        <!-- Rapport Avance Salaire -->
        <a href="{{ route('avances_salaire') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-green-600">
                <i class="mdi mdi-cash-fast text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Avance Salaire</h3>
            <p class="text-sm text-gray-600 text-center mt-2">Suivi des avances sur salaire</p>
        </a>

        <!-- Rapport Versement CP -->
        <a href="{{ route('versements_chef') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-yellow-600">
                <i class="mdi mdi-calendar-check text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Versement </h3>
            <p class="text-sm text-gray-600 text-center mt-2">Suivi des Versements</p>
        </a>

        <!-- Rapport Transaction Monétaire -->
        <a href="{{ route('transactions') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-indigo-600">
                <i class="mdi mdi-cash-register text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Transactions</h3>
            <p class="text-sm text-gray-600 text-center mt-2">État des transactions monétaires</p>
        </a>

        <!-- Rapport Production (Global) -->
        <a href="{{ route('rapports.production.global') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-cyan-600">
                <i class="mdi mdi-factory text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Production Globale</h3>
            <p class="text-sm text-gray-600 text-center mt-2">Aperçu global de la production</p>
        </a>

        <!-- Rapport Vente -->
        <a href="{{ route('rapports.vente.global') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-emerald-600">
                <i class="mdi mdi-cart text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Rapport Vente</h3>
            <p class="text-sm text-gray-600 text-center mt-2">Analyse des ventes</p>
        </a>

        <!-- Rapports Manquants -->
        <a href="{{ route('deductions') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-orange-600">
                <i class="mdi mdi-alert-circle text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Rapports Deductions</h3>
            <p class="text-sm text-gray-600 text-center mt-2">Suivis des deductions</p>
        </a>

        <!-- Rapport Delis -->
        <a href="depenses" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-rose-600">
                <i class="mdi mdi-alert text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Rapport Depense</h3>
            <p class="text-sm text-gray-600 text-center mt-2">Suivi des incidents</p>
        </a>

        <!-- Rapports Commande -->
        <a href="commandes" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow transform hover:scale-105 transition-transform border border-gray-200">
            <div class="flex items-center justify-center mb-4 text-fuchsia-600">
                <i class="mdi mdi-clipboard-list text-4xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-center text-gray-800">Rapports Commande</h3>
            <p class="text-sm text-gray-600 text-center mt-2">Suivi des commandes clients</p>
        </a>



    </div>
</div>
@endsection
